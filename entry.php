<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "yuka_tomita@mistnet.co.jp"; // 宛先のメールアドレス
    $subject = "お問い合わせフォームからのメッセージ";

    // フォームデータの取得
    $name = $_POST["last-name"] . " " . $_POST["first-name"];
    $furigana = $_POST["last-name-furigana"] . " " . $_POST["first-name-furigana"];
    $experience = $_POST["experience"];
    $email = $_POST["email"];
    $interview = $_POST["interview"];
    $role = $_POST["role"];
    $remarks = $_POST["remarks"];

    // メール本文の作成
    $message = "お名前: $name\n";
    $message .= "フリガナ: $furigana\n";
    $message .= "経験年数: $experience\n";
    $message .= "メールアドレス: $email\n";
    $message .= "希望面談形式: $interview\n";
    $message .= "希望種別: $role\n";
    $message .= "備考: $remarks\n";

    // メール送信
    mail($to, $subject, $message);

    echo "お問い合わせありがとうございます。";
}
?>