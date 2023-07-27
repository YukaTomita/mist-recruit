<!DOCTYPE html>
<html>
<head>
    <title>ランキング縦棒グラフ</title>
    <style>
        .graph-container {
            display: flex;
            flex-direction: column-reverse;
            width: 150px;
            height: 300px;
            border: 1px solid #000;
        }

        .graph-bar {
            flex: 1;
            background-color: blue;
            margin: 1px;
        }

        .item-name {
            writing-mode: vertical-rl;
            text-orientation: upright;
            text-align: center;
            margin: 1px;
        }
    </style>
</head>
<body>
    <h1>ランキング縦棒グラフ</h1>
    <div class="graph-container">
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

        // ランキングデータの取得
        $sql = "SELECT item_name FROM ranking ORDER BY rank ASC";
        $result = $conn->query($sql);

        // ランキングデータを縦棒グラフとして表示
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="graph-bar" style="height: ' . ($row["rank"] * 30) . 'px;"></div>';
                echo '<div class="item-name">' . $row["item_name"] . '</div>';
            }
        } else {
            echo "データがありません。";
        }

        // データベース接続を閉じる
        $conn->close();
        ?>
    </div>
</body>
</html>
