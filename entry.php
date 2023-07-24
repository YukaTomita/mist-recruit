 <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    if (empty($name) || empty($email) || empty($message)) {
        echo "全てのフィールドを入力してください。";
    } else {
        
        // ここで受信したデータを処理するための任意のコードを追加できます

        // 例えば、データベースに保存する場合は以下のようになります
        // $servername = "localhost";
        // $username = "your_username";
        // $password = "your_password";
        // $dbname = "your_database";
        // $conn = new mysqli($servername, $username, $password, $dbname);
        // $sql = "INSERT INTO contacts (name, email, message) VALUES ('$name', '$email', '$message')";
        // $conn->query($sql);
        // $conn->close();

        // データの処理が完了したら、ユーザーに感謝メッセージを表示する例を以下に示します
        echo "エントリーいただき、ありがとうございます。<br/>後日担当者よりご連絡いたします。";
    }
}
?>
