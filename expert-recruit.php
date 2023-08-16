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

// 最終投票日を取得するクエリ実行
$query = "SELECT MAX(vote_date) AS last_vote_date FROM votes_history WHERE user_ip = :user_ip";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_ip', $userIp);
$stmt->execute();
$lastVoteDateResult = $stmt->fetch(PDO::FETCH_ASSOC);
$lastVoteDate = $lastVoteDateResult['last_vote_date'];

// データベース接続のクローズ
$conn = null;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 shrink-to-fit=no">
    <meta name="description" content="株式会社MIST solution - トップページ 株式会社ミストソリューションは、異なった業界との接点を持つことで化学反応を起こし、
    幅広いニーズにより的確にお応えできる、常に進化しているIT企業です。">
    <meta name="keywords" content="株式会社ミストソリューション,ミストソリューション,MISTsolution,ミスト" />
    <meta name="copyright" content="© 1997, 2023 mistsolution. All Rights Reserved.">
    <meta name="format-detection" content="telephone=no">
    <!-- OGP -->
    <meta property="og:url" content="https://www.mistnet.co.jp">
    <meta property="og:title" content="株式会社MIST solution | WEBサイト" />
    <meta property="og:site_name" content="株式会社MIST solution | WEBサイト">
    <meta name="og:description" content="株式会社MIST solution - トップページ 株式会社ミストソリューションは、異なった業界との接点を持つことで化学反応を起こし、
    幅広いニーズにより的確にお応えできる、常に進化しているIT企業です。">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ja-JP">
    <meta property="og:image" content="assets/images/mist-ogp.jpg">
    <meta name="twitter:card" content="summary" />
    <!-- 各々変更 -->
    <title>ベテラン向け</title>
    <!-- css,js -->
    <link rel="stylesheet" href="CSS/reset.css" type="text/css">
    <link rel="stylesheet" href="CSS/common.css" type="text/css">
    <link rel="stylesheet" href="CSS/expert.css" type="text/css">
    <script type="text/javascript" src="js/header.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- favicon -->
    <link rel="icon" href="img/favicon.ico">

</head>
<body>
    <!-- header -->
    <header class="header flex-box">
        <h1 class="site-title">
            <a href="#!">
                <img src="img/グループ 1315.png" alt="ロゴ">
            </a>
        </h1>
        <a href="#!"><img class="header-twitter" src="img/white background.png" alt="Twitter"></a>
        <a href="#!" class="header-entry">エントリー</a>
        <nav class="header-nav">
            <ul class="nav-list">
                <li class="nav-item"><a href="#!">Service</a></li>
                <li class="nav-item"><a href="#!">About</a></li>
                <li class="nav-item"><a href="#!">News</a></li>
                <li class="nav-item"><a href="#!">Conetact</a></li>
                <li class="nav-item"><a href="#!">Recruit</a></li>
            </ul>
        </nav>
        <button class="burger-btn">
            <span class="bars">
                <span class="bar bar_top"></span>
                <span class="bar bar_mid"></span>
                <span class="bar bar_bottom"></span>
            </span>
        </button>
        <span class="burger-musk"></span>
    </header>

    <!-- トップ画像 -->
    <div><img src="img/expert-TOP.png" class="top-img" alt=""></div>

    <!-- 隙間 -->
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

                <div class="load">
                    <?php if ($lastVoteDate) : ?>
                        <p>最終投票日: <?php echo date('Y.m.d', strtotime($lastVoteDate)); ?></p>
                     <?php else : ?>
                        <p>まだ投票がありません。</p>
                    <?php endif; ?>
                </div>                
                <div class="gap-control-probram"></div>
                <div class="gap-control-probram"></div>

                <p class="font-style-words text-center">現在のランキング</p>
            </div>
        </div>
    </div>
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
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
    <div class="wrapper">
        <button class="cercle" id="rankingButton" onclick="toggleRanking()">ランキングに参加する</button>
        <div class="arrow-container">
            <div class="arrow-bottom"></div>
            <div class="arrow-bottom arrow-bottom-Shifted"></div>
        </div>
    </div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
    <!-- 投票 -->
    <div class="ranking-section" id="rankingSection" style="display: none;">
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

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
                    <button class="post-btn" type="submit"　onclick="showChart()">投票する</button>
                </form>
            <?php else : ?>
                <p class="vote-message asterisk">※すでに投票済みです。</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="gap-control-probram"></div>
<div class="gap-control-probram"></div>

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


    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- タイトル -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="font-style-title text-center">重要な役割</p>
                <hr class="border-line">
            </div>
        </div>
    </div>

    <!-- 括弧ワード（ボルドー） -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="font-style-words text-center">「熟練エンジニアの需要」</p>
            </div>
        </div>
    </div>

    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- コメント -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="font-style-comments text-center">豊富な経験が求められる時代に</p>
            </div>
        </div>
    </div>

    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- 写真・コメント -->
    <div class="role-img-left">
        <img src="img/sample1.png">
        <p class="role-txt">私たちは、技術者派遣に受託及びチーム派遣も含め、企業の社員負担の大幅削減させ、
                一丸となってソフトウェア開発業務に専念できるような環境をつくり社会に貢献し続けることを志し、
                その同志と共に歩んできました。
        </p>
    </div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="role-img-right">
        <p class="role-txt">労働力人口の減少の中で技術者不足は市場全体の課題です。エンジニアが働き続けられる環境の現実を
                重要ミッションの一つと捉えています。熟練を求められる技術職種において、20年以上の経験を有するエンジニアの最重要課題。
                特にハードウェア分野における豊富な経験を持つエンジニアの活用度が高く、時には技術伝承における重要な役割を担っています。<br>
                ライフイベントを機に職を離れざるを得なかったエンジニアの、ブランクからの復帰や
                時短勤務のニーズに応えることで、貴重なスキルを活かしながら生産性高く活躍されています。
        </p>
        <img src="img/sample2.png">
    </div>


    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- タイトル -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="font-style-title text-center">職場の事例</p>
                <hr class="border-line">
            </div>
        </div>
    </div>

    <!--  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="font-style-words text-center">「実際の業務内容」</p>
            </div>
        </div>
    </div>

    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- コメント -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="font-style-comments text-center">探していた環境と案件があります</p>
            </div>
        </div>
    </div>


    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- コメント -->
    <div class="container-fluid">
        <div class="row">
            <div class="wrapper">
                <p class="font-style-comments2 txt">エンジニアのスキルにあった現場や受託業務で活躍しています。
                    相談も連携も取り易い環境に加え、<br>業務後に帰社しコミュニケーションやPJに携わっている
                    社員もおります。自分の求める環境で仕<br>事とプライベートと両立できます。
                    先ずは、実際の業務内容をご紹介いたします。
                </p>
            </div>
        </div>
    </div>

    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>


    <!-- 業務内容（アプリ開発） -->
    <div>
        <p class="font-bordeaux text-center">分野</p>
    </div>
    <div class="cercle">アプリ開発</div>
    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <div class="flex-title">
        <div>案件概要</div>
        <div>技術要素</div>
    </div>

    <div class="flex">
        <div style="justify-content: center;">リスク管理システムパッケージ新規開発</div>
        <div style="justify-content: center;">Java<br>GWT<br>Hibernate<br>Jasper Studio<br>JP1<br>SQL Server<br><br></div>
    </div>

    <div class="flex">
        <div style="justify-content: center;">与信管理システム保守開発（クレジット会社向け）</div>
        <div style="justify-content: center;">Java<br>SQL<br>JP1<br>Oracle<br><br></div>
    </div>

    <div class="flex">
        <div style="justify-content: center;">給与計算システム（メーカー向け）</div>
        <div style="justify-content: center;">C<br>SHELL<br>PL<br>SQL<br>Oracle<br><br></div>
    </div>


    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- 業務内容（インフラエンジニア） -->
    <div>
        <p class="font-bordeaux text-center">分野</p>
    </div>
    <div class="cercle">インフラエンジニア</div>
    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <div class="flex-title">
        <div>案件概要</div>
        <div>技術要素</div>
    </div>

    <div class="flex">
        <div style="justify-content: center;">オンプレLinuxサーバ（RHEL）からクラウド移行に伴う基盤移行<br>及びOS、ミドルウェアバージョンアップ</div>
        <div style="justify-content: center;">IBM MQ<br>IBM Tivoli Monitoring<br>Netbackup<br>NetWorker<br>VMware vSphere<br><br></div>
    </div>

    <div class="flex">
        <div style="justify-content: center;">物理SolarisサーバからLinuxサーバ（RHEL）への移行<br>及びミドルウェアバージョンアップ</div>
        <div style="justify-content: center;">NetWorker<br>IBM MQ<br>IBM Tivoli Monitoring<br>Oracle<br>Systemwalker<br>Storabe Cruiser<br>ServerView<br><br></div>
    </div>

    <div class="flex">
        <div style="justify-content: center;">保険システムにおける基盤構築支援</div>
        <div style="justify-content: center;">Lotus Notes<br>TeraTerm<br>Db2V10.1<br>WebSphereApplicationServerV8.5<br>SVF for PD<br><br></div>
    </div>

    <!-- 隙間 -->
    <div class="gap-control-probram"></div>

<!--エントリー-->
<div class="wrapper">
    <div class="entry">
        <P class="font-style-comments entry-space">まずはあなたのキャリアプランを聞かせてください。</P>
        <button onclick="location.href='#!'" class="entry-button">　エントリー</button>
        <p class="entry-red">入社からプロジェクト着任までのフローが知りたい方はこちら ></p>
    </div>
    <div class="flex-link">
        <img class="link-img" src="img/グループ 1824.png">
        <img class="link-img" src="img/グループ 1826.png">
    </div>
    <div class="flex-link">
        <img class="link-img" src="img/グループ 1827.png">
        <img class="link-img" src="img/グループ 1828.png">
    </div>
</div>

<div class="gap-control-probram"></div>
<div class="gap-control-probram"></div>
<div class="gap-control-probram"></div>
<div class="gap-control-probram"></div>
<div class="gap-control-probram"></div>

<!-- footer -->
<footer class="footer">
<small>&copy; 1997,2023 mistsolution.All Rights Reserved.</small>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="js\header.js"></script>
<script src="js/upperclassman.js"></script>
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
