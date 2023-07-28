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
    <h1>好きなスポーツランキング</h1>
    <form method="post">
        <label for="sport1">サッカー</label>
        <input type="radio" name="sport" value="サッカー" id="sport1"><br>

        <!-- 他の投票項目も同様に10個繰り返す -->
        <label for="sport2">野球</label>
        <input type="radio" name="sport" value="野球" id="sport2"><br>

        <label for="sport3">バスケットボール</label>
        <input type="radio" name="sport" value="バスケットボール" id="sport3"><br>

        <!-- 他の投票項目も同様に10個繰り返す -->

        <input type="submit" value="投票">
    </form>

    <h2>投票結果</h2>
    <?php
    // データベース接続情報
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "rank";
    
    // データベースからトップ3のランキングを取得
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("接続エラー: " . $conn->connect_error);
    }

    $sql = "SELECT sport_name, votes FROM sports_ranking ORDER BY votes DESC LIMIT 3";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<div class='ranking'>";
            echo "<img src='" . $rank . "th_place_icon.png' alt='" . $rank . "位'>";
            echo "<strong>" . $rank . "位</strong> " . $row["sport_name"] . " - " . $row["votes"] . "票";
            echo "</div>";
            $rank++;
        }
    } else {
        echo "<p>データがありません。</p>";
    }

    $conn->close();
    ?>
</body>
</html>

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
    <title>好きなスポーツランキング</title>
</head>
<body>
    <h2>投票結果</h2>
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

    <h1>好きなスポーツランキング</h1>
    <form method="post">
        <input type="radio" name="sport" value="サッカー" id="sport1">
        <label for="sport1">サッカー</label>
        <input type="radio" name="sport" value="野球" id="sport2">
        <label for="sport2">野球</label>
        <input type="radio" name="sport" value="バスケットボール" id="sport3">
        <label for="sport3">バスケットボール</label>

        <input type="submit" value="投票">
    </form>

</body>
</html>

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
    <h1>好きなスポーツランキング</h1>
    <form method="post">
        <label for="sport1">サッカー</label>
        <input type="radio" name="sport" value="サッカー" id="sport1"><br>

        <!-- 他の投票項目も同様に10個繰り返す -->
        <label for="sport2">野球</label>
        <input type="radio" name="sport" value="野球" id="sport2"><br>

        <label for="sport3">バスケットボール</label>
        <input type="radio" name="sport" value="バスケットボール" id="sport3"><br>

        <!-- 他の投票項目も同様に10個繰り返す -->

        <input type="submit" value="投票">
    </form>

    <h2>投票結果</h2>
    <div class='ranking'>
        <img src='1st_place_icon.png' alt='1位'>
        <strong>1位</strong> <!-- 1位のスポーツ名はPHPで表示する -->
    </div>
    <div class='ranking'>
        <img src='2nd_place_icon.png' alt='2位'>
        <strong>2位</strong> <!-- 2位のスポーツ名はPHPで表示する -->
    </div>
    <div class='ranking'>
        <img src='3rd_place_icon.png' alt='3位'>
        <strong>3位</strong> <!-- 3位のスポーツ名はPHPで表示する -->
    </div>
</body>
</html>
