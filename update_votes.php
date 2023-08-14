１．ランキングの結果ははじめ非表示にして「※投票するとランキングが表示されます」と表示。
２．「post-btn」が押下されたら、ランキング結果を表示させ「※投票すると～」は非表示にする。
３．rankingSectionははじめ非表示に。
４．rankingButtonが押下したらrankingSectionを開く
５．rankingSectionの背景をグレーにする。

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 shrink-to-fit=no">
    <!-- 各々変更 -->
    <title>ベテラン向け</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- エンジニアが選ぶ企業のポイント　ランキング -->
    <div class="wrapper">
            <div style="position: relative; margin: auto;">
                <canvas id="voteChart" height="500px" width="100%"></canvas>
                <div id="imageContainer" style="position: absolute; bottom: 0; left: 0;"></div>
            </div>

            <script>
                // データの取得
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "root";
                $dbname = "enterprise";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "
                    SELECT o.name AS option_name, COUNT(v.id) AS vote_count
                    FROM options o
                    LEFT JOIN votes v ON o.id = v.option_id
                    GROUP BY o.id
                    ORDER BY vote_count DESC;
                ";

                $result = $conn->query($sql);
                $data = array();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $data[] = array(
                            "option_name" => $row["option_name"],
                            "vote_count" => $row["vote_count"]
                        );
                    }
                }

                $conn->close();
                ?>

                // データの設定
                var data = {
                    labels: <?php echo json_encode(array_column($data, "option_name")); ?>.map((v) => v.replace(/ー/g, '丨').split("")),
                    datasets: [{
                        data: <?php echo json_encode(array_column($data, "vote_count")); ?>,
                        backgroundColor: [
                            <?php
                            for ($i = 0; $i < count($data); $i++) {
                                if ($i < 3) {
                                    echo "'#8B2022',";
                                } else {
                                    echo "'rgba(139, 32, 34, 0.5)',";
                                }
                            }
                            ?>
                        ],
                        borderWidth: 0 // 区切り線を非表示
                    }]
                };

                // グラフ作成
                var ctx = document.getElementById('voteChart').getContext('2d');
                var voteChart = new Chart(ctx, {
                    type: 'bar', // 縦棒グラフ
                    data: data,
                    options: {
                        scales: {
                            x: {
                                display: true, // X軸目盛り表示
                                ticks: {
                                    color: 'black', // 項目名の色
                                    weight: 'bold' // 項目名の太さ
                                }
                            },
                            y: {
                                display: false, // Y軸目盛り非表示
                            }
                        },
                        plugins: {
                            legend: {
                                display: false, // 凡例非表示
                            },
                            tooltip: {
                                enabled: false
                            },
                            
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: 20,
                                bottom: 20
                            }
                        },
                        indexAxis: 'x', // 横軸に表示
                    },
                    plugins: [{
                        afterDraw: function(chart) {
                            displayImagesBelowBars(chart);
                        }
                    }]
                });

                // 画像を表示する関数
                function displayImagesBelowBars(chart) {
                    var imageContainer = document.getElementById('imageContainer');
                    var imageUrls = [
                        'img/ex-1.png', // 1位の画像URL
                        'img/ex-2.png', // 2位の画像URL
                        'img/ex-3.png'  // 3位の画像URL
                    ];

                    var xAxis = chart.scales.x;
                    var barWidth = xAxis.width / chart.data.labels.length;

                    chart.data.datasets[0].data.forEach(function(dataValue, index) {
                        if (index < imageUrls.length) {
                            var img = new Image();
                            img.src = imageUrls[index];
                            img.width = 45
                            img.height = 70;

                            var position = xAxis.getPixelForValue(index);
                            var imgContainer = document.createElement('div');
                            imgContainer.style.position = 'absolute';
                            imgContainer.style.left = (position - img.width / 2) + 'px';
                            imgContainer.style.bottom = '-60px'; // 画像をさらに下に移動
                            imgContainer.appendChild(img);

                            imageContainer.appendChild(imgContainer);
                        }
                    });
                }
            </script>
        </div>
        <!-- 隙間 -->
    <div class="wrapper">
        <button class="cercle" id="rankingButton" onclick="toggleRanking()">ランキングに参加する</button>
        <div class="arrow-container">
            <div class="arrow-bottom"></div>
            <div class="arrow-bottom arrow-bottom-Shifted"></div>
        </div>
    </div>
<!-- 投票 -->
    <div class="ranking-section" id="rankingSection">
        <div class="wrapper">
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
    </div>
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

<script>
    function toggleRanking() {
        var rankingSection = document.getElementById("rankingSection");
        var rankingButton = document.getElementById("rankingButton");
        if (rankingSection.style.display === "none") {
            rankingSection.style.display = "block";
            rankingButton.textContent = "× 閉じる";
            rankingButton.style.backgroundColor = "#f0f0f0";
        } else {
            rankingSection.style.display = "none";
            rankingButton.textContent = "ランキングに参加する";
            rankingButton.style.backgroundColor = "#8B2022";
        }
        return false; 
    }    
</script>
</body>
</html>
