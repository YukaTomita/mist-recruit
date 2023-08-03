<?php
// データベース接続情報
$host = 'localhost';
$db = 'sport';
$user = 'root';
$password = 'root';

// データベースに接続
$conn = new PDO("mysql:host=$host;dbname=$db", $user, $password);

// タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

// 投票結果のリセット処理、1分間ボタンが押せなくなる、指定した時間に開いていないとリセットされない
if (date('H:i') === '03:00') {
    // 投票数をゼロにリセットするクエリを実行
    $resetQuery = "TRUNCATE TABLE votes";
    $resetStmt = $conn->prepare($resetQuery);
    $resetStmt->execute();

    // 投票履歴を削除するクエリを実行
    $deleteQuery = "TRUNCATE TABLE votes_history";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->execute();
}

// スポーツの一覧を取得
$query = "SELECT * FROM sports";
$stmt = $conn->prepare($query);
$stmt->execute();
$sports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSport = $_POST['sport'];

    // 投票履歴をチェック
    $userIp = $_SERVER['REMOTE_ADDR'];
    $query = "SELECT * FROM votes_history WHERE user_ip = :user_ip";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_ip', $userIp);
    $stmt->execute();
    $voteHistory = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voteHistory) {
        // 投票結果をデータベースに保存
        $query = "INSERT INTO votes (sport_id) VALUES (:sport_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':sport_id', $selectedSport);
        $stmt->execute();

        // 投票履歴を保存
        $query = "INSERT INTO votes_history (user_ip, sport_id) VALUES (:user_ip, :sport_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_ip', $userIp);
        $stmt->bindParam(':sport_id', $selectedSport);
        $stmt->execute();
    }

    // ページをリロードして再投稿を防止するためのリダイレクト
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 投票結果の取得とランキングの作成
$query = "SELECT sport_id, COUNT(*) AS count FROM votes GROUP BY sport_id ORDER BY count DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$voteResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ランキングデータの生成
$ranking = [];
$rank = 1;
$prevCount = null;
foreach ($voteResults as $result) {
    $sportId = $result['sport_id'];
    $count = $result['count'];

    $sportName = "";
    foreach ($sports as $sport) {
        if ($sport['id'] == $sportId) {
            $sportName = $sport['name'];
            break;
        }
    }

    // 同率順位の場合、前の順位と投票数を比較して順位を設定
    if ($prevCount !== null && $prevCount !== $count) {
        $rank++;
    }

    $ranking[] = [
        'rank' => $rank,
        'sportName' => $sportName,
        'count' => $count
    ];

    $prevCount = $count;
}

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
    <meta property="og:image" content="assets/img/mist-ogp.jpg">
    <meta name="twitter:card" content="summary" />
    <!-- favicon -->
    <link rel="icon" href="img/favicon.ico">
    <title>新人の声</title>
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/common.css">
    <link rel="stylesheet" href="CSS/newcomer.css">
    <link rel="stylesheet" href="CSS/expert.css">
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

    <!-- トップ画像と中央配置 -->
    <img class="top-img" src="img\newcomer.jpg" alt="画像">
    <div class="wrapper">

        <!-- 隙間 -->
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>


        <!-- 投票機能 -->
        <p class="font-style-title">質　問</p>
        <hr class="border-line">
        <p class="font-style-words2">「あなたの好きなスポーツは何ですか？」</p>
            <div class="load">
                <div id="loadingText">上位5つを更新中<span class="dots">...</span></div>
            </div>

            <div class="gap-control-probram"></div>
            <div class="gap-control-probram"></div>

            <div class="load">
                <p id="currentDate"></p>
            </div>

            <div class="gap-control-probram"></div>
            <div class="gap-control-probram"></div>

            <p class="font-style-words text-center">現在のランキング</p>

        <!-- 隙間 -->
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <?php if (!empty($ranking) && $voteHistory) : ?>
            <div class="ranking">
                <?php $count = 0; ?>
                <?php foreach ($ranking as $rankData) : ?>
                    <?php if ($count >= 5) break; ?> <!-- 5つ以上の要素は表示しない -->
                    <div class="bar-graph text-align">
                        <p class="rank"><span><?php echo $rankData['rank']; ?></span>位</p>
                        <p class="sportName"><?php echo $rankData['sportName']; ?></p>
                    </div>
                    <?php $count++; ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="ranking asterisk">※投票するとランキングが表示されます。</p>
        <?php endif; ?>

        <!-- 隙間 -->
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <div class="cercle">ランキングに参加する</div>
        <div class="Arrow-Bottom"></div>
        <div class="Arrow-Bottom"></div>

        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <!-- 投票欄 -->
        <div class="font-style-comments2 line-height">
            <p>経験したことのあるスポーツを下記から一つお選びください</p>
            <div class="vote">
                <?php if ($voteHistory) : ?>
                    <p class="asterisk">※既に投票済みです。</p>
                <?php else : ?>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <?php foreach ($sports as $sport) : ?>
                            <button class="sport-button" type="submit" name="sport" value="<?php echo $sport['id']; ?>">
                                <?php echo $sport['name']; ?>
                            </button>
                        <?php endforeach; ?>
                    </form>
                <?php endif; ?>
            </div>
            <P class="font-style-comments2">
            エンジニアに何故スポーツ？と思う方もいるかもしませんが、今回入社した若手エンジニアたちは、皆スポーツ経験者です。
            彼らの仕事に取り組む姿勢にスポーツでの経験が活かされています。
            エンジニアとしての実務経験がなかったり、短かったりしても、取り組む姿勢は強い武器になっています。
            </p>
        </div>

        <!-- 隙間 -->
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <div class="newcomer__title">
            <h2 class="font-style-words">
                「求められているから頑張れる！」
            </h2>            
        </div>

        <!-- 隙間 -->
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <!-- タブ切り替え -->
        <!--
        <section id="ict-link" class="section__contents anchor center">
            <div class="contents__bg">
                <div class="contents__change-button">
                    <a id="ict" class="change-button__ict" href="#">
                        <p class="tab__title"><span>東京</span>所属<br>(神田本社)</p>
                        <p class="tab__subtitle">着任：関東エリア</p>
                    </a>
                    <a id="lbd" class="change-button__lbd" href="#">
                        <p class="tab__title"><span>高知</span>所属<br>(高松支店)</p>
                        <p class="tab__subtitle">着任：四国エリア</p>
                    </a>
                </div>
                        -->
                        <div class="contents__ict container" id="target2">
                            <div class="image-container">
                                <img class="first-img" src="img/グループ 1098.jpg" alt="サンプル画像">
                                <div class="spopup">
                                    <div class="spopup-content">
                                        <h2 class="stitle">タイトル</h2>
                                        <img class="sphoto" src="img/写真のファイル名.jpg" alt="写真の説明">
                                        <p class="scomment">コメントをここに入力してください。</p>
                                    </div>
                                </div>
                            </div>
                            <div class="detail1">
                                <p class="newcomer-text">僕はラグビー<br>をしてました。</p>
                                <button class="newcomer-btn1">もっと見る</button>
                            </div>
                        </div>
                    <img class="first-img" src="img/グループ 1100.jpg" alt="サンプル画像">
                    <div class="detail2">
                        <img class="open-img" src="img/グループ 1819.png" alt="サンプル画像">
                    </div>
                    <p class="newcomer-text">僕はサッカー<br>をしてました。</p>
                    <button class="newcomer-btn2">もっと見る</button>
                    <img class="first-img" src="img/グループ 1101.jpg" alt="サンプル画像">
                    <div class="detail3">
                        <img class="open-img" src="img/グループ 1821.png" alt="サンプル画像">
                    </div>
                    <p class="newcomer-text">僕はバスケ<br>をしてました。</p>
                    <button class="newcomer-btn3">もっと見る</button>
                </div>
                <div class="contents__lbd" id="target">
                    <div class="contents">
                    </div>
                </div>
<!--        </div>
        </section>  -->
        <div class="flex">
            <p>2022年入社　新卒入社</p>
            <p>先輩たちをもっと見る→</p>
        </div>

        <!-- フレックス群 -->
        <div class="flex-img">
            <ul class="flex-ul">
                <li>
                    <img class="size-img" src="img/グループ 11.png" alt="画像">
                    <p>猪瀬</p>
                </li>
                <li>
                    <img class="size-img" src="img/グループ 3.png" alt="画像">
                    <p>岡崎</p>
                </li>
                <li>
                    <img class="size-img" src="img/グループ 3.png" alt="画像">
                    <P>渡辺陸</P>
                </li>
            </ul>
        </div>
    </div>

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
<script src="js/newcomer.js"></script>

</body>
</html>
