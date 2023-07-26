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
                <p id="loadingText">エンジニアが選ぶ企業のポイントを更新中<span id="dots"></span></p>

                <div class="gap-control-probram"></div>
                <div class="gap-control-probram"></div>

                <p id="currentDate"></p>

                <div class="gap-control-probram"></div>
                <div class="gap-control-probram"></div>

                <p class="font-style-words text-center">現在のランキング</p>
            </div>
        </div>
    </div>
    <!-- エンジニアが選ぶ企業のポイント　ランキング -->
    <div class="wrapper">
        <canvas id="barChart"></canvas>

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
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '投票数',
                        data: data,
                        backgroundColor: 'transparent', // 棒グラフの背景色
                        borderColor: '#FF2D2D', // 棒グラフの枠線の色
                        borderWidth: 2 // 棒グラフの枠線の太さ
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: Math.max(...data) + 2, // 最大値 + 2 を設定
                            title: {
                                display: false,
                                text: '投票数'
                            }
                        }
                    }
                }
            });
        </script>

        <!-- 隙間 -->
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <div class="cercle">ランキングに参加する</div>
        <div class="Arrow-Bottom"></div>
        <div class="Arrow-Bottom"></div>

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
    <div class="wrapper">
        <section class="case">
            <article class="case__list">
                <div class="case__item">
                    <p class="case__item__text">私たちは、技術者派遣に受託及びチーム派遣も含め、<br>企業の社員負担の大幅削減させ、
                    一丸となってソフト<br>ウェア開発業務に専念できるような環境をつくり社会<br>に貢献し続けることを志し、
                    その同志と共に歩んできま<br>した。</p>
                    <img class="case__item__img" src="img/sample1.png" alt="画像1">
                </div>
                <div class="case__item">
                    <p class="case__item__text">労働力人口の減少の中で技術者不足は市場全体の課題<br>です。エンジニアが働き続けられる環境の現実を
                    重要<br>ミッションの一つと捉えています。熟練を求められる<br>技術職種において、20年以上の経験を有するエンジニ<br>アの最重要課題。
                    特にハードウェア分野における豊富<br>な経験を持つエンジニアの活用度が高く、時には技術<br>伝承における重要な役割を担っています。<br><br>
                    ライフイベントを機に職を離れざるを得なかったエン<br>ジニアの、ブランクからの復帰や
                    時短勤務のニーズに<br>応えることで、貴重なスキルを活かしながら生産性高<br>く活躍されています。</p>
                    <img class="case__item__img" src="img/sample2.png" alt="画像2">
                </div>
            </article>
        </section>
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
        <div class="flex-txt">リスク管理システムパッケージ新規開発</div>
        <div class="flex-txt">Java<br>GWT<br>Hibernate<br>Jasper Studio<br>JP1<br>SQL Server<br><br></div>
    </div>

    <div class="flex">
        <div class="flex-txt">与信管理システム保守開発（クレジット会社向け）</div>
        <div class="flex-txt">Java<br>SQL<br>JP1<br>Oracle<br><br></div>
    </div>

    <div class="flex">
        <div class="flex-txt">給与計算システム（メーカー向け）</div>
        <div class="flex-txt">C<br>SHELL<br>PL<br>SQL<br>Oracle<br><br></div>
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
        <div class="flex-txt">オンプレLinuxサーバ（RHEL）からクラウド移行に伴う基盤移行<br>及びOS、ミドルウェアバージョンアップ</div>
        <div class="flex-txt">IBM MQ<br>IBM Tivoli Monitoring<br>Netbackup<br>NetWorker<br>VMware vSphere<br><br></div>
    </div>

    <div class="flex">
        <div class="flex-txt">物理SolarisサーバからLinuxサーバ（RHEL）への移行<br>及びミドルウェアバージョンアップ</div>
        <div class="flex-txt">NetWorker<br>IBM MQ<br>IBM Tivoli Monitoring<br>Oracle<br>Systemwalker<br>Storabe Cruiser<br>ServerView<br><br></div>
    </div>

    <div class="flex">
        <div class="flex-txt">保険システムにおける基盤構築支援</div>
        <div class="flex-txt">Lotus Notes<br>TeraTerm<br>Db2V10.1<br>WebSphereApplicationServerV8.5<br>SVF for PD<br><br></div>
    </div>

    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
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

</body>
</html>
