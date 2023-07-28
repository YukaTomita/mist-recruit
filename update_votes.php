<?php
// データベース接続情報
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "rank";

// フォームからの投票データを取得
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["sport"])) {
        $selected_sport = $_POST["sport"];

        // データベースに接続
        $conn = new mysqli($servername, $username, $password, $dbname);

        // 接続エラーの確認
        if ($conn->connect_error) {
            die("接続エラー: " . $conn->connect_error);
        }

        // 選択されたスポーツの投票数を更新
        $sql = "UPDATE sports_ranking SET votes = votes + 1 WHERE sport_name = '$selected_sport'";
        $conn->query($sql);

        $conn->close();
    }
}

// データベースからトップ3のランキングを取得
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("接続エラー: " . $conn->connect_error);
}

$sql = "SELECT sport_name, votes FROM sports_ranking ORDER BY votes DESC LIMIT 3";
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>好きなスポーツランキング結果</title>
</head>
<body>
    <h1>好きなスポーツランキング結果</h1>
    <ol>
        <?php
        // ランキング結果の表示
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . $row["sport_name"] . " - " . $row["votes"] . "票</li>";
            }
        } else {
            echo "<li>データがありません。</li>";
        }
        ?>
    </ol>
</body>
</html>
