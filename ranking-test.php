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
        $stmt->bindParam(':option_id', $option);
        $stmt->execute();
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
    <title>ベテranking-test</title>
    <!-- css,js -->
    <link rel="stylesheet" href="CSS/reset.css" type="text/css">
    <link rel="stylesheet" href="CSS/common.css" type="text/css">
    <link rel="stylesheet" href="CSS/expert.css" type="text/css">
    <script type="text/javascript" src="js/header.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <!-- Chart.jsを読み込む -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>    <!-- favicon -->
    <link rel="icon" href="img/favicon.ico">
    <style>
        /* 縦書きのスタイル */
        .vertical-labels {
            writing-mode: vertical-rl;
            text-orientation: upright;
            text-align: center;
        }

        /* 項目名の間隔を設定 */
        .vertical-labels p {
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <!-- header -->
    <!-- トップ画像 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- Question -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="font-style-title text-center">質問</p>
                <hr class="border-line">
            </div>
        </div>
    </div>
    <!--  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="font-style-words2 text-center">「キャリアアップで選ぶポイントは何ですか？」</p>
                <div class="load">
                    <div id="loadingText">エンジニアが選ぶ企業のポイントを更新中<span class="dots">...</span></div>
                </div>

                <div class="gap-control-probram"></div>
                <div class="gap-control-probram"></div>

                <div class="gap-control-probram"></div>
                <div class="gap-control-probram"></div>

                <p class="font-style-words text-center">現在のランキング</p>
            </div>
        </div>
    </div>

    <!-- エンジニアが選ぶ企業のポイント　ランキング -->
    <div class="wrapper">
        <canvas id="barChart"></canvas>
    <!-- 項目名を縦書きで表示 -->
    <div class="vertical-labels">
            <?php foreach ($results as $result) : ?>
                <p><?php echo $result['name']; ?></p>
            <?php endforeach; ?>
    </div>
    <!-- ...（その他のコンテンツ）... -->
    <?php
    // 1位から3位までのランキングの画像ファイル名を配列で定義する
    $rankingImages = array(
        'img/ex1.png',
        'img/ex2.png',
        'img/ex3.png'
    );
    ?>
        <script>
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
                labels: labels,
                datasets: [{
                    label: '投票数',
                    data: data,
                    backgroundColor: '#8B2022', // 全ての棒グラフに#8B2022カラーを使用
                    borderWidth: 0 // 棒グラフの枠線を削除
                }]
            },
            options: {
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1, // X軸に整数のみ表示
                            callback: function (value, index, values) {
                                // 1位、2位、3位の画像をラベルの下に表示
                                return (index === 0 || index === 1 || index === 2) ? 
                                    `<img src="images/ranking_${index + 1}.png" alt="${value}" width="20" height="20" style="vertical-align: middle;">` :
                                    '';
                            }
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            display: false // Y軸のグリッド線を非表示
                        }
                    }]
                },
                legend: {
                    display: false // 凡例を非表示
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
        </script>

        <!-- 隙間 -->
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <div class="cercle">ランキングに参加する</div>
        <div class="Arrow-Bottom"></div>
        <div class="Arrow-Bottom"></div>

        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <p class="font-style-comments2 txt line-height">キャリアアップで転職される際に、重要視されるポイントを下記よりお選びください。<br>※複数選択可能</p>
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
    <!-- コメント -->
    <div class="container-fluid">
        <div class="row">
            <div class="wrapper">
                <p class="font-style-comments2 txt line-height">皆さんは、転職先を選ぶ時に何を最も重視しますか？
                    たとえば…職場環境、雰囲気、年収、<br>業務内容、技術力、ネームバリューなど、
                    エンジニアに転職をするなら実際自分が使ったことのあ<br>るサービスを
                    開発している企業や、なじみのあるサービスに少しでも携われるのは魅力的だと
                    思わ<br>れたりもします。しかし、就活時と実際にエンジニアとして
                    働いたあとでは企業選びは変わります。<br>ステージでごとに柔軟な対応で
                    エンジニアとの相乗効果を図ります。</p>
            </div>
        </div>
    </div>

