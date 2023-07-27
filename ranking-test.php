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
$sql = "SELECT id, item_name, votes FROM ranking";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>リアルタイム投票</title>
</head>
<body>
    <h1>リアルタイム投票</h1>
    <form id="votingForm">
        <?php
        // ランキングデータを投票フォームとして表示
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<label>';
                echo '<input type="checkbox" name="items[]" value="' . $row["id"] . '">';
                echo $row["item_name"];
                echo '</label><br>';
            }
        } else {
            echo "データがありません。";
        }
        ?>

        <input type="submit" value="投票">
    </form>

    <div id="resultContainer">
        <!-- 投票結果がここに表示されます -->
    </div>

    <script>
        // フォームの送信を非同期で処理
        const votingForm = document.getElementById("votingForm");
        votingForm.addEventListener("submit", function(event) {
            event.preventDefault(); // フォームの通常の送信を防止

            // フォームのデータを取得
            const formData = new FormData(votingForm);

            // 非同期でPHPファイルにデータを送信して投票結果を更新
            fetch("update_votes.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // 投票結果を表示
                document.getElementById("resultContainer").innerHTML = data;
            });
        });
    </script>
</body>
</html>
