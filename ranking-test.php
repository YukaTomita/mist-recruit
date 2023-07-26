<!DOCTYPE html>
<html>
<head>
    <title>投票ページ</title>
</head>
<body>
    <?php
    // データベースへの接続情報
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "voting_db";

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
    $sql = "SELECT id, name, FIND_IN_SET( votes, ( SELECT GROUP_CONCAT( votes ORDER BY votes DESC ) FROM items ) ) as 'rank' FROM items ORDER BY votes DESC";
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
    <form method="post" action="">
        <?php foreach ($items as $item): ?>
            <label>
                <input type="checkbox" name="items[]" value="<?php echo $item['id']; ?>">
                <?php echo $item['name']; ?>
            </label><br>
        <?php endforeach; ?>
        <br>
        <input type="submit" value="投票">
    </form>
    <h2>最新の投票日：<?php echo $voteDate; ?></h2>
    <h2>投票結果</h2>
    <ol>
        <?php foreach ($items as $item): ?>
            <li>
                <?php if ($item['rank'] === "1"): ?>
                    <img src="img/ex-1.png" alt="Rank 1">
                <?php elseif ($item['rank'] === "2"): ?>
                    <img src="img/ex-2.png" alt="Rank 2">
                <?php elseif ($item['rank'] === "3"): ?>
                    <img src="img/ex-3.png" alt="Rank 3">
                <?php endif; ?>
                <?php echo $item['name']; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</body>
</html>
