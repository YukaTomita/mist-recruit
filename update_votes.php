<?php
$selectedOption = $_POST['option'];

// データベースに接続します
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "company_votes";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // オプションテーブルへの投票を追加します
    $stmt = $conn->prepare("INSERT INTO votes (option_id) VALUES (:option_id)");
    $stmt->bindParam(':option_id', $selectedOption);
    $stmt->execute();

    echo "投票が完了しました。";

} catch(PDOException $e) {
    echo "エラー: " . $e->getMessage();
}

// データベース接続を閉じます
$conn = null;

// 投票が完了したら、投票結果ページにリダイレクトします
header("Location: index.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>企業ランキング投票</title>
</head>
<body>
    <h1>企業ランキング投票</h1>
    <form action="vote.php" method="post">
        <label for="option">項目選択：</label>
        <select name="option" id="option">
            <?php
            $host = 'localhost';
            $db = 'enterprise';
            $user = 'root';
            $password = 'root';            

            // データベースに接続します
            $conn = new mysqli($servername, $username, $password, $dbname);

            // 接続エラーのチェック
            if ($conn->connect_error) {
                die("接続エラー: " . $conn->connect_error);
            }

            // オプションテーブルから項目を取得します
            $sql = "SELECT * FROM options";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row["id"] . '">' . $row["name"] . '</option>';
                }
            }

            // データベース接続を閉じます
            $conn->close();
            ?>
        </select>
        <button type="submit">投票する</button>
    </form>
    
    <!-- 投票結果表示部分 -->
    <h2>投票結果</h2>
    <canvas id="voteChart"></canvas>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
    <script>
        // データベースから投票結果を取得し、グラフを表示します
        // 以下はサンプルデータです
        var ctx = document.getElementById('voteChart').getContext('2d');
        var voteChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['事業内容', '技術力', 'ネームバリュー', '職場環境', '年収', '勤務地', '会社の成長', '福利厚生', '雰囲気', 'その他'],
                datasets: [{
                    label: '投票数',
                    data: [10, 5, 8, 12, 6, 9, 15, 7, 11, 3], // ここにデータベースから取得した投票数を挿入します
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
