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
    $lastVotingDate = $stmt->fetchColumn();

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

// 投票日取得：更新
$userIp = $_SERVER['REMOTE_ADDR'];
$query = "SELECT updated_at FROM votes_history WHERE user_ip = :user_ip ORDER BY updated_at DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_ip', $userIp);
$stmt->execute();
$lastVotingDateTime = $stmt->fetchColumn();

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
    <!--css-->
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

    <!-- 隙間 -->
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!-- 投票機能 -->
<div class="wrapper">
        <p class="font-style-title">質　問</p>
        <hr class="border-line">
        <p class="font-style-words2">「あなたの好きなスポーツは何ですか？」</p>
            <div class="load">
                <div id="loadingText">上位5つを更新中<span class="dots">...</span></div>
            </div>
            <div class="gap-control-probram"></div>
            <div class="gap-control-probram"></div>

        <?php
            if ($lastVotingDateTime) {
                // 最終投票日時を指定のフォーマットに変換して表示
                $formattedLastVotingDate = date('更新：Y.m.d', strtotime($lastVotingDateTime));
                echo $formattedLastVotingDate;
            } else {
                // 投票履歴がない場合の処理
                echo "まだ投票がありません。";
            }            
            ?>

        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <p class="font-style-words text-center">現在のランキング</p>
        <?php if (!empty($ranking) && $voteHistory) : ?>

        <div class="ranking">
        <?php $count = 0; ?>
        <?php foreach ($ranking as $rankData) : ?>
            <?php if ($count >= 5) break; ?> <!-- 5つ以上の要素は表示しない -->
            <?php
                $rank = $rankData['rank'];
                $sportName = $rankData['sportName'];
                $imagePath = ''; // 画像のパスを指定する変数

            // 1位から3位までの場合に画像のパスを設定
            if ($rank === 1) {
                $imagePath = 'img/new1.png';
                $rankClass = 'rank-1'; /* 1位の場合のクラスを追加 */
            } elseif ($rank === 2) {
                $imagePath = 'img/new2.png';
                $rankClass = 'rank-2'; /* 2位の場合のクラスを追加 */
            } elseif ($rank === 3) {
                $imagePath = 'img/new3.png';
                $rankClass = 'rank-3'; /* 3位の場合のクラスを追加 */
            } else {
                $rankClass = ''; /* 1位から3位以外はクラスを空にする */
            }
            ?>

            <div class="bar-graph text-align <?php echo $rankClass; ?>">
                <!-- 画像を挿入 -->
                <?php if (!empty($imagePath)) : ?>
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $rank; ?>位の画像" style="width: 40px; height: 30px;">
                <?php else : ?>
                    <div style="width: 40px; height: 30px;"></div>
                <?php endif; ?>

                <p class="rank"><span><?php echo $rank; ?></span>位</p>
                <p class="sportName"><?php echo $sportName; ?></p>
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

        <button class="cercle" id="rankingButton" onclick="toggleRanking()">ランキングに参加する</button>
        <div class="arrow-container">
            <div class="arrow-bottom"></div>
            <div class="arrow-bottom arrow-bottom-Shifted"></div>
        </div>

        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>
</div>
    <!-- 投票欄 -->
    <div class="ranking-section" id="rankingSection">
        <div class="wrapper">
            <div class="font-style-comments2 line-height">
                <p class="v-text">「学生時代していた。」もしくは、「個人でしていた。」など、該当するスポーツを下記からお選びください。（※複数されていた方は、一番長く在籍していたスポーツをお選びください。）</p>
                <div class="vote">
                    <?php if (!$voteHistory) : ?>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="radio-buttons">
                                <?php foreach ($sports as $sport) : ?>
                                    <label class="radio-column">
                                        <input type="radio" name="sport" value="<?php echo $sport['id']; ?>" onchange="this.form.submit()">
                                        <?php echo $sport['name']; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    <?php else : ?>
                        <p class="asterisk">※すでに投票済みです。</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
<div class="wrapper">
        <P class="font-style-comments2">
        エンジニアに何故スポーツ？と思う方もいるかもしませんが、今回入社した若手エンジニアたちは、皆スポーツ経験者です。
        彼らの仕事に取り組む姿勢にスポーツでの経験が活かされています。
        エンジニアとしての実務経験がなかったり、短かったりしても、取り組む姿勢は強い武器になっています。
        </p>

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
    <!--<div class="contents__ict container" id="target2"></div>-->
    <div class="sport-container">
        <div class="sport-image-container">
            <img src="img/グループ 1098.jpg" alt="画像１" class="sport-main-image">
            <p class="sport-word">僕はラグビー<br>をしてました。</p>
            <p class="more-link" onclick="togglePopup('popup1')">▶ もっと見る</p>
            <!-- ポップアップ -->
            <div class="sport-popup popup1">
                <div class="sport-popup-content">
                    <div class="sport-popup-title">ポップアップのタイトル</div>
                    <div class="sport-popup-info">
                        <img src="img/rugby.png" alt="ポップアップ画像" class="sport-popup-image">
                        <p class="sport-popup-comment">ここにコメントを記入</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <!--<div class="contents__ict container" id="target2"></div>-->
    <div class="sport-container">
        <div class="sport-image-container">
            <img src="img/グループ 1100.jpg" alt="画像１" class="sport-main-image">
            <p class="sport-word">僕はサッカー<br>をしてました。</p>
            <p class="more-link" onclick="togglePopup('popup2')">▶ もっと見る</p>
            <!-- ポップアップ -->
            <div class="sport-popup popup2">
                <div class="sport-popup-content">
                    <div class="sport-popup-title">ポップアップのタイトル</div>
                    <div class="sport-popup-info">
                        <img src="img/soccor.png" alt="ポップアップ画像" class="sport-popup-image">
                        <p class="sport-popup-comment">ここにコメントを記入</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <!--<div class="contents__ict container" id="target2"></div>-->
    <div class="sport-container">
        <div class="sport-image-container">
            <img src="img/グループ 1101.jpg" alt="画像１" class="sport-main-image">
            <p class="sport-word">僕はバスケ<br>をしてました。</p>
            <p class="more-link" onclick="togglePopup('popup3')">▶ もっと見る</p>
            <!-- ポップアップ -->
            <div class="sport-popup popup3">
                <div class="sport-popup-content">
                    <div class="sport-popup-title">ポップアップのタイトル</div>
                    <div class="sport-popup-info">
                        <img src="img/basketball.png" alt="ポップアップ画像" class="sport-popup-image">
                        <p class="sport-popup-comment">ここにコメントを記入</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--        </div>
        </section>  -->

    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <div class="flex-space">
        <div>2022年入社　新卒入社</div>
        <div><a href="#!">先輩たちをもっと見る →</a></div>
    </div>

    <!-- フレックス群 -->
    <div class="flex-img">
        <ul class="flex-ul">
            <li>
                <img class="size-img" src="img/グループ 11.png" alt="画像">
                <div class="text-small">アプリ開発エンジニア</div>
                <div class="text-small2">猪瀬</div>
            </li>
            <li>
                <img class="size-img" src="img/okazaki.png" alt="画像">
                <div class="text-small">アプリ開発エンジニア</div>
                <div class="text-small2">岡崎</div>
            </li>
            <li>
                <img class="size-img" src="img/watanabe.png" alt="画像">
                <div class="text-small">アプリ開発エンジニア</div>
                <div class="text-small2">渡辺陸</div>
            </li>
        </ul>
    </div>

<!--エントリー-->
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
    <script>
        // 例: ポップアップを表示する関数
        function showPopup(popupClass) {
            var popup = document.querySelector("." + popupClass);
            if (popup) {
                popup.style.display = "block";
            }
        }

        // 例: ポップアップを非表示にする関数
        function hidePopup(popupClass) {
            var popup = document.querySelector("." + popupClass);
            if (popup) {
                popup.style.display = "none";
            }
        }
        function togglePopup(popupClass) {
            var popup = document.querySelector("." + popupClass);
            if (popup) {
                if (popup.style.display === "block") {
                    popup.style.display = "none";
                } else {
                    popup.style.display = "block";
                }
            }
        }
    </script>
</body>
</html>
