<?php
// データベース接続情報
$host = 'localhost';
$db = 'enterprise';
$user = 'root';
$password = 'root';

// データベースに接続
$conn = new PDO("mysql:host=$host;dbname=$db", $user, $password);

// オプションの一覧を取得
$query = "SELECT * FROM options";
$stmt = $conn->prepare($query);
$stmt->execute();
$options = $stmt->fetchAll(PDO::FETCH_ASSOC);

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedOptions = isset($_POST['vote']) ? $_POST['vote'] : [];

    // 投票履歴をチェック
    $userIp = $_SERVER['REMOTE_ADDR'];
    $query = "SELECT * FROM votes_history WHERE user_ip = :user_ip";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_ip', $userIp);
    $stmt->execute();
    $voteHistory = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voteHistory && count($selectedOptions) > 0) {
        // 投票結果をデータベースに保存
        $query = "INSERT INTO votes (option_id) VALUES (:option_id)";
        $stmt = $conn->prepare($query);

        foreach ($selectedOptions as $option) {
            $stmt->bindParam(':option_id', $option);
            $stmt->execute();
        }

        // 投票履歴を保存
        $query = "INSERT INTO votes_history (user_ip, option_id) VALUES (:user_ip, :option_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_ip', $userIp);

        foreach ($selectedOptions as $option) {
            $stmt->bindParam(':option_id', $option);
            $stmt->execute();
        }
    }

    // ページをリロードして再投稿を防止するためのリダイレクト
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 集計クエリ実行
$query = "SELECT o.name, COUNT(*) AS count
FROM options o
JOIN votes v ON o.id = v.option_id
GROUP BY o.name
ORDER BY count DESC;
";
$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 投票済みかどうかをチェック
$userIp = $_SERVER['REMOTE_ADDR'];
$query = "SELECT * FROM votes_history WHERE user_ip = :user_ip";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_ip', $userIp);
$stmt->execute();
$voteHistory = $stmt->fetch(PDO::FETCH_ASSOC);

// データベース接続のクローズ
$conn = null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <title>ranking-test</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>    <!-- favicon -->
    <link rel="icon" href="img/favicon.ico">
    <style>
  .vertical-label {
    writing-mode: vertical-rl; /* 縦書きスタイルを設定 */
    text-orientation: mixed; /* 必要に応じて調整 */
    transform: rotate(180deg); /* 必要に応じて調整 */
  }
</style>

</head>
<body>
    <div class="wrapper">
        <canvas id="barChart" style="max-width: 100%;"></canvas>
    <script>
        // データの準備
        const labels = [];
        const data = [];

        <?php foreach ($results as $result) : ?>
            labels.push('<?php echo $result['name']; ?>');
            data.push(<?php echo $result['count']; ?>);
        <?php endforeach; ?>

        // チャートの描画
        const ctx = document.getElementById('barChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar', // 縦棒グラフに変更
            data: {
                labels: ['事業内容', '技術力', 'ネームバリュー', '職場環境', '年収', '勤務地', '会社の成長', '福利厚生', '雰囲気', 'その他'],
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        <?php foreach ($results as $index => $result) : ?>
                            <?php echo ($index >= 3) ? "'rgba(139, 32, 34, 0.5)'" : "'#8B2022'"; ?>,
                        <?php endforeach; ?>
                    ],
                }]
            },
            options: {
                responsive: true,
                scales: {
                    yAxes: [
                        {
                        ticks: {
                            beginAtZero: true //0から始まる
                        },
                        gridLines: {
                            display: false 
                        },  
                        max: Math.max(...data) + 2,
                        min: 0,
                        title: {
                            display: false,
                            text: '投票数'
                        }
                    },],
                    xAxes: {
                        ticks: {
                            callback: function(value) {
                                return value.split("").join("\n"); // 項目名を改行して縦書きにする
                            },
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    afterDraw: (chart) => {
                        const { ctx, scales: { x }, width, height } = chart;

                        // 1位から3位までの画像を表示
                        for (let i = 0; i < 3; i++) {
                            const image = new Image();
                            image.src = images[i];
                            const xPosition = x.getPixelForValue(i);
                            const yPosition = height - 30;
                            ctx.drawImage(image, xPosition - 15, yPosition, 30, 30);
                        }
                    }
                },
                responsive: true,
            }
        });
        <label for="option<?php echo $option['id']; ?>" class="vertical-label"><?php echo $option['name']; ?></label>
    </script>
<!--votes-->        
        <?php if (!$voteHistory) : ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <?php foreach ($options as $option) : ?>
                    <div class="option">
                        <input type="checkbox" id="option<?php echo $option['id']; ?>" name="vote[]" value="<?php echo $option['id']; ?>">
                        <label for="option<?php echo $option['id']; ?>"><?php echo $option['name']; ?></label>
                    </div>
                <?php endforeach; ?>
                <button class="post-btn" type="submit">投票する</button>
            </form>
        <?php else : ?>
            <p class="vote-message asterisk">※すでに投票済みです。</p>
        <?php endif; ?>
    </div>
</body>
</html>
