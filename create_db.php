<?php
// データベース接続情報
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "rank";

// データベースに接続
$conn = new mysqli($servername, $username, $password);

// 接続エラーの確認
if ($conn->connect_error) {
    die("接続エラー: " . $conn->connect_error);
}

// データベースが存在しない場合は作成
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);

$conn->close();

// データベースに接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーの確認
if ($conn->connect_error) {
    die("接続エラー: " . $conn->connect_error);
}

// テーブルが存在しない場合は作成
$sql = "CREATE TABLE IF NOT EXISTS sports_ranking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sport_name VARCHAR(50) NOT NULL,
    votes INT DEFAULT 0
)";

$conn->query($sql);

// 初期データの挿入
$sql = "INSERT INTO sports_ranking (sport_name, votes) VALUES ('サッカー', 0), ('野球', 0), ('バスケットボール', 0)";
$conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>データベース初期設定とデータ挿入</title>
</head>
<body>
    <h1>データベース初期設定とデータ挿入</h1>
    <p>データベースの初期設定とデータの挿入が完了しました。</p>
</body>
</html>
