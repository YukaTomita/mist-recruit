<!DOCTYPE html>
<html>
<head>
    <title>投票ページ</title>
    <style>
        .container {
            display: flex;
            writing-mode: vertical-rl; /* カテゴリ名を縦書きにする */
        }
        .item {
            flex: 1;
            margin: 5px;
            padding: 10px;
            text-align: center;
        }
        .bar-container {
            display: flex;
            flex-direction: column-reverse;
            height: 200px;
            width: 20px;
            background-color: #ccc;
        }
        .bar {
            background-color: lightblue;
            height: 0;
            transition: height 0.3s ease; /* アニメーションを追加 */
        }
        .img-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 5px;
        }
        .img-container img {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <?php
    // データベースへの接続情報
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "selection";

    // データベースへの接続
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 接続エラーの確認
    if ($conn->connect_error) {
        die("接続エラー: " . $conn->connect_error);
    }

    // 投票フォームが送信された場合
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["items"])) {
        $selectedItems = $_POST["items"];

        foreach ($selectedItems as $itemId) {
            // 選択された項目の投票数を1つ増やす
            $sql = "UPDATE items SET votes = votes + 1 WHERE id = " . (int)$itemId;
            $conn->query($sql);
        }
    }
    // 項目のリストと順位情報を取得
    $sql = "SELECT id, name, votes FROM items ORDER BY votes DESC";
    $result = $conn->query($sql);

    $items = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }

    // データベース接続を閉じる
    $conn->close();

    // ランキングの投票が押された最新の日付を取得
    $voteDate = ""; // デフォルトは空文字列

    // データベースへの接続
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 接続エラーの確認
    if ($conn->connect_error) {
        die("接続エラー: " . $conn->connect_error);
    }

    // ランキングの投票の最新日を取得するクエリ
    $sql = "SELECT MAX(vote_date) AS latest_date FROM votes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $voteDate = $row["latest_date"];
    }

    // データベース接続を閉じる
    $conn->close();
    ?>

    <h1>項目ランキング</h1>
    <div class="container">
        <?php foreach ($items as $item): ?>
            <div class="item">
                <div class="img-container">
                    <?php if ($item['votes'] > 0 && $item['votes'] <= 3): ?>
                        <img src="rank_<?php echo $item['votes']; ?>.png" alt="Rank <?php echo $item['votes']; ?>">
                    <?php endif; ?>
                </div>
                <div class="bar-container">
                    <div class="bar" style="height: <?php echo $item['votes'] * 10; ?>px;"></div>
                </div>
                <?php echo $item['name']; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>最新の投票日：<?php echo $voteDate; ?></h2>

    <h2>投票する項目</h2>
    <form method="post" action="">
        <div class="container">
            <?php foreach ($items as $item): ?>
                <label>
                    <input type="checkbox" name="items[]" value="<?php echo $item['id']; ?>">
                    <?php echo $item['name']; ?>
                </label>
            <?php endforeach; ?>
        </div>
        <br>
        <input type="submit" value="投票">
    </form>
</body>
</html>
