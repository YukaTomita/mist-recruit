<?php
    // データベースへの接続情報
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "ranking";

// データベースに接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーの確認
if ($conn->connect_error) {
    die("接続に失敗しました: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // フォームから送信されたデータを取得
    if (isset($_POST["items"]) && is_array($_POST["items"])) {
        $items = $_POST["items"];
        $itemIds = implode(",", $items);

        // 投票数を更新
        $sql = "UPDATE ranking SET votes = votes + 1 WHERE id IN ($itemIds)";
        $conn->query($sql);
    }
}

// 更新された投票結果を取得
$sql = "SELECT item_name, votes FROM ranking ORDER BY votes DESC";
$result = $conn->query($sql);

// 投票結果をHTMLとして返す
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<p>' . $row["item_name"] . ': ' . $row["votes"] . '票</p>';
    }
} else {
    echo "データがありません。";
}

// データベース接続を閉じる
$conn->close();
?>
