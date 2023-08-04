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
    <title>testtest</title>
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/common.css">
    <link rel="stylesheet" href="CSS/newcomer.css">
    <link rel="stylesheet" href="CSS/expert.css">
    <style>
        .ranking {
            margin: 60px 10px;
        }
        .vote{
            margin: 60px 10px; 
        }
        .bar-graph {
            height: 20px;
            position: relative;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .sport-button {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
            padding: 10px;
            border: none;
            background-color: #f2f2f2;
            color: #333;
            font-size: 16px;
            cursor: pointer;
        }
        .sport-button:hover {
            background-color: #e0e0e0;
        }
        .sportName {
            border: 1.5px solid #BBBBBB;
            width: 400px;
            height: 25px;
            border-radius: 100vh;
            padding-top: 10px;
        }
        .rank {
            width: 50px;
            font-size: 2rem;
        }
        .rank-1 .sportName {
            border-color: #CAA846; /* 1位の場合の枠の色を黄緑 (#CAA846) に変更 */
            border-width: 3px;
        }

        .rank-2 .sportName {
            border-color: #B2BABA; /* 2位の場合の枠の色を灰色 (#B2BABA) に変更 */
            border-width: 3px;
        }

        .rank-3 .sportName {
            border-color: #8B4513; /* 3位の場合の枠の色を茶色 (#8B4513) に変更 */
            border-width: 3px;
        }
        .rank span {
            font-size: 3rem;
            line-height: 1;
        }
        .title-ranking {
            font-size: 1.7rem;
            line-height: 140%;
            margin-top: 10px;
            font-weight: bold;
        }
        .asterisk {
            font-size: 1.7rem;
            font-weight: bold;
            color: #8B2022;
            margin: auto;
        }
        .rank-img{
            width: 15px;
            height: auto;
        }

        .v-text{
            font-family: Helvetica,"游ゴシック体", 'YuGothic', "游ゴシック", "Yu Gothic", sans-serif;
            text-align: left;
            line-height: 2rem;
        }
        .ranking-section {
            display: none;
            width: 100%;
            padding: 20px; /* 必要な場合はランキングセクションの内容との間に適切な余白を設定 */
            box-sizing: border-box; 
            background-color: #f2f2f2; /* ランキングセクションの背景色をグレー (#f2f2f2) に変更 */
        }

        .radio-buttons {
            display: flex;
            flex-wrap: wrap;
            text-align: left;
            margin-left: 30px;
            gap: 20px; /* 選択肢の間隔調整 */
        }
        .radio-column {
            flex-basis: 30%; /* 各列の幅を調整 */
        }
        .arrow-container {
            position: relative;
            display: flex;
            justify-content: center;
            }
            .arrow-bottom {
            position: absolute;
            border-bottom: solid 2px #8B2022;
            border-right: solid 2px #8B2022;
            width: 24px;
            height: 24px;
            transform: rotate(45deg);
            margin: auto;
            }
            .arrow-bottom-Shifted {
            top: 15px;
            }
    </style>
</head>
<body>
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

        <!-- 投票機能 -->
    <div class="wrapper">
        <p class="font-style-title">質　問</p>
        <hr class="border-line">
        <p class="font-style-words2">「あなたの好きなスポーツは何ですか？」</p>
            <div class="load">
                <div id="loadingText">上位5つを更新中<span class="dots">...</span></div>
            </div>
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

        <button class="cercle" id="rankingButton" onclick="toggleRanking()">ランキングに参加する</button>
       
        <div class="arrow-container">
            <div class="arrow-bottom"></div>
            <div class="arrow-bottom arrow-bottom-Shifted"></div>
        </div>
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
