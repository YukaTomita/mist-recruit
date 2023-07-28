<!DOCTYPE html>
<html>
<head>
    <title>好きなスポーツランキング</title>
    <style>
        .ranking {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .ranking img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<h2>投票結果</h2>
<?php
// データベース接続情報
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "rank";

// データベースに接続
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("接続エラー: " . $conn->connect_error);
}

// 上位5位のスポーツを取得するSQLクエリ
$sql = "SELECT sport_name FROM sports_ranking ORDER BY votes DESC LIMIT 5";
$result = $conn->query($sql);

// 上位5位のスポーツ名を取得
$top_five_sports = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $top_five_sports[] = $row["sport_name"];
    }
} else {
    // データがない場合の処理
    $top_five_sports = array("データがありません");
}

$conn->close();
?>

    <div class='ranking'>
        <img src='new1.png' alt='1位'>
        <strong>1位</strong> <?php echo isset($top_five_sports[0]) ? $top_five_sports[0] : ""; ?>
    </div>
    <div class='ranking'>
        <img src='new2.png' alt='2位'>
        <strong>2位</strong> <?php echo isset($top_five_sports[1]) ? $top_five_sports[1] : ""; ?>
    </div>
    <div class='ranking'>
        <img src='new3.png' alt='3位'>
        <strong>3位</strong> <?php echo isset($top_five_sports[2]) ? $top_five_sports[2] : ""; ?>
    </div>
    <div class='ranking'>
        <strong>4位</strong> <?php echo isset($top_five_sports[3]) ? $top_five_sports[3] : ""; ?>
    </div>
    <div class='ranking'>
        <strong>5位</strong> <?php echo isset($top_five_sports[4]) ? $top_five_sports[4] : ""; ?>
    </div>

    <h2>好きなスポーツランキング</h2>
<form method="post">
    <input type="radio" name="sport" value="サッカー" id="sport1">
    <label for="sport1">サッカー</label>
    <input type="radio" name="sport" value="野球" id="sport1">
    <label for="sport2">野球</label>
    <input type="radio" name="sport" value="バスケットボール" id="sport1">
    <label for="sport3">バスケットボール</label>
    <input type="radio" name="sport" value="卓球" id="sport1">
    <label for="sport4">卓球</label>
    <input type="radio" name="sport" value="水泳" id="sport1">
    <label for="sport5">水泳</label>
    <input type="radio" name="sport" value="テニス" id="sport1">
    <label for="sport6">テニス</label>
    <input type="radio" name="sport" value="ラグビー" id="sport1">
    <label for="sport7">ラグビー</label>
    <input type="radio" name="sport" value="陸上" id="sport1">
    <label for="sport8">陸上</label>
    <input type="radio" name="sport" value="格闘" id="sport1">
    <label for="sport9">格闘</label>
    <input type="radio" name="sport" value="その他" id="sport1">
    <label for="sport10">その他</label>


    
    <input type="submit" value="投票">
</form>

</body>
</html>
