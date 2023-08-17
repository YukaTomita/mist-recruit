<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $lastName = $_POST["last-name"];
  $firstName = $_POST["first-name"];
  $email = $_POST["email"];
  $confirmEmail = $_POST["confirm-email"];
  $inquiryType = $_POST["inquiry-type"];
  $message = $_POST["message"];
  $experience = $_POST["experience"];
  $role = $_POST["role"];

  // メールアドレスの一致を確認
  if ($email !== $confirmEmail) {
    echo "エラー：メールアドレスが一致しません";
    exit; // エラーメッセージを表示してスクリプトの実行を終了する
  }

  // データの処理や保存などを行う

  // 処理が完了したら、別のページにリダイレクトするなどの操作を行う
}
?>
