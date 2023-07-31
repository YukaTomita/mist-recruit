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
    <title>testtest</title>
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/common.css">
    <link rel="stylesheet" href="CSS/newcomer.css">
    <link rel="stylesheet" href="CSS/expert.css">
</head>

<body>
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

        <!-- 隙間 -->
        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

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
                } elseif ($rank === 2) {
                    $imagePath = 'img/new2.png';
                } elseif ($rank === 3) {
                    $imagePath = 'img/new3.png';
                }
            ?>

            <div class="bar-graph text-align">
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

        <div class="cercle">ランキングに参加する</div>
        <div class="Arrow-Bottom"></div>
        <div class="Arrow-Bottom"></div>

        <div class="gap-control-probram"></div>
        <div class="gap-control-probram"></div>

        <!-- 投票欄 -->
        <div class="font-style-comments2 line-height">
            <p>「学生時代していた。」もしくは、「個人でしていた。」など、該当するスポーツを下記からお選びください。（※複数されていた方は、一番長く在籍していたスポーツをお選びください。）
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
                エンジニアに何故スポーツ？と思う方もいるかもしれませんが、エンジニアはスポーツで培った個々のポジションの役割、チームワークなど、今回社員になったSESのルーキーたちは、
                皆スポーツをしていて、現在の業務や仕事に取り組む際の姿勢のベースになっています。エンジニアの現場経験がなかったり、経験が短期だったとしても、実際の現場では人間力も
                強い武器になってきます。
            </p>
        </div>

