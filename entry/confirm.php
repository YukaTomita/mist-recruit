<?php session_start(); ?>
<?php 
//添付ファイルアップロード
$filename = $_FILES['input_file']['name'];
$uploaded_path = 'attachment/'.$filename;
move_uploaded_file($_FILES['input_file']['tmp_name'],$uploaded_path);

$_SESSION['input_file'] = $filename;

        // フォームから送信されたデータを各変数に格納
        $lastNameKanji = $_POST["last-name"];
        $firstNameKanji = $_POST["first-name"];
        $lastNameKana = $_POST["klast-name"];
        $firstNameKana = $_POST["kfirst-name"];
        $experience = $_POST["experience"];
        $email = $_POST["email"];
        $interviewType = $_POST["interview"];
        $role = $_POST["role"];
        $skillData = $_POST["skillData"];
        $notes  = $_POST["textarea"];
        $content  = $_POST["content"];
        $agree  = $_POST["agree"];
        
    // 送信ボタンが押されたら
    if (isset($_POST["submit"])) {
        // 送信ボタンが押された時に動作する処理をここに記述する

        // 日本語をメールで送る場合のおまじない
mb_language("ja");
mb_internal_encoding("UTF-8");

// --ヘッダー情報を設定--
//メール形式
$header = "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\n";
//メールの返信先のアドレス
$header .= "Return-Path:MISTsolution採用担当 <r_pr@mistnet.co.jp>\n";
//送信者の名前（または組織名）とメールアドレス
$header .= "From:MISTsolution採用担当 <r_pr@mistnet.co.jp>\n";
// $header .= "\r\n";//消すやつ
//送信者の名前（または組織名）とメールアドレス
$header .= "Sender:MISTsolution採用担当 <r_pr@mistnet.co.jp>\n";
//受け取った人に表示される返信の宛先
$header .= "Reply-To:MISTsolution採用担当 <r_pr@mistnet.co.jp>\n";

//応募者用自動返信メール件名
$auto_reply_subject = "ご応募ありがとうございます";

//自動返信メール本文
$auto_reply_text = "この度は、ご応募頂き誠にありがとうございます。\n下記の内容でご応募を受け付けました。\n採用担当より5営業日以内に折り返しご連絡させていただきます。\n\n";
$auto_reply_text .= "ご応募日時：" .date_default_timezone_set('Asia/Tokyo'). date("Y-m-d H:i") . "\n\n";
$auto_reply_text .= "お名前：" . $lastNameKanji + $firstNameKanji . "\n";
$auto_reply_text .= "フリガナ：" . $lastNameKana + $firstNameKana . "\n";
$auto_reply_text .= "経験年数：" . $experience . "\n";
$auto_reply_text .= "メールアドレス：" . $email . "\n";
$auto_reply_text .= "希望面談形式：" . $interviewType . "\n";
$auto_reply_text .= "希望種別：" . $role . "\n";
$auto_reply_text .= "保有スキル：" . $skillData . "\n";
$auto_reply_text .= "履歴書・職務経歴書：" . $filename . "\n";
$auto_reply_text .= "備考：" . $content . "\n\n";
$auto_reply_text .= "個人情報の取り扱いについて：" . $agree . "\n\n";
$auto_reply_text .= "MISTsolution 採用担当";

// 応募者用自動返信メール用テキストメッセージを記述
$body = "--__BOUNDARY__\n";
$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
$body .= $auto_reply_text . "\n";
$body .= "--__BOUNDARY__\n";

// 応募者用自動返信メール用メール送信
mb_send_mail($email, $auto_reply_subject, $body, $header);

// 管理者確認用メールの件名
$admin_reply_subject = "リクルートサイトより応募を受け付けました";

// 管理者確認用メール本文
$admin_reply_text = "下記内容で応募がありました。\n\n";
$admin_reply_text .= "応募日時：" . date_default_timezone_set('Asia/Tokyo'). date("Y-m-d H:i") . "\n\n";
$admin_reply_text .= "お名前：" . $lastNameKanji + $firstNameKanji . "\n";
$admin_reply_text .= "フリガナ：" . $lastNameKana + $firstNameKana . "\n";
$admin_reply_text .= "経験年数：" . $experience . "\n";
$admin_reply_text .= "メールアドレス：" . $email . "\n";
$admin_reply_text .= "希望面談形式：" . $interviewType . "\n";
$admin_reply_text .= "希望種別：" . $role . "\n";
$admin_reply_text .= "保有スキル：" . $skillData . "\n";
$admin_reply_text .= "履歴書・職務経歴書：" . $filename . "\n";
$admin_reply_text .= "備考：" . $content . "\n\n";
$admin_reply_text .= "個人情報の取り扱いについて：" . $agree . "\n\n";

// 管理者確認用テキストメッセージを記述
$body = "--__BOUNDARY__\n";
$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
$body .= $admin_reply_text . "\n";
$body .= "--__BOUNDARY__\n";

// ファイルを添付
$filename = $_POST['input_file'];
// if(mb_detect_encoding($filename,"UTF-8",true) === false){
//     $filename = mb_convert_encoding($filename,"UTF-8","SJIS");
// }
// $filename = basename( mb_encode_mimeheader(  $filename ));
$body .= "Content-Type: application/octet-stream; name= \"recruit-".date('Y-m-d').".pdf\"\n";  
$body .= "Content-Disposition: attachment; filename= \"recruit-".date('Y-m-d').".pdf\"\n";
$body .= "Content-Transfer-Encoding: base64\n";
$body .= "\n";
$body .= chunk_split(base64_encode(file_get_contents('./attachment/'.$_POST['input_file'])))."\n";
$body .= "--__BOUNDARY__--";

// 管理者確認用メール送信
if(mb_send_mail( 'r_pr@mistnet.co.jp', $admin_reply_subject, $body, $header)){
    header("Location: thanks.php");
} else {
    header("Location: notsend.php");
}
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 shrink-to-fit=no">
    <meta name="description" content="株式会社MIST solution - トップページ 株式会社ミストソリューションは、異なった業界との接点を持つことで化学反応を起こし、
    幅広いニーズにより的確にお応えできる、常に進化しているIT企業です。">
    <meta name="keywords" content="株式会社ミストソリューション,ミストソリューション,MISTsolution,ミスト" />
    <meta name="copyright" content="© 1997, 2023 mistsolution. All Rights Reserved.">
    <meta name="format-detection" content="telephone=no">
    <!-- OGP -->
    <meta property="og:url" content="https://www.mistnet.co.jp">
    <meta property="og:title" content="株式会社MIST solution | WEBサイト" />
    <meta property="og:site_name" content="株式会社MIST solution | WEBサイト">
    <meta name="og:description" content="株式会社MIST solution - トップページ 株式会社ミストソリューションは、異なった業界との接点を持つことで化学反応を起こし、
    幅広いニーズにより的確にお応えできる、常に進化しているIT企業です。">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ja-JP">
    <meta property="og:image" content="assets/images/mist-ogp.jpg">
    <meta name="twitter:card" content="summary" />
    <!-- 各々変更 -->
    <title>Mistsolution recruit | 入力確認</title>
    <!-- css,js -->
    <link rel="stylesheet" href="../CSS/reset.css" type="text/css">
    <link rel="stylesheet" href="../CSS/common.css" type="text/css">
    <link rel="stylesheet" href="../CSS/entry.css" type="text/css">
    <script type="text/javascript" src="../js/common.js"></script>
    <!-- favicon -->
    <link rel="icon" href="../img/favicon.ico">    
</head>
<body>
  <!-- header -->
  <header class="header flex-box">
    <h1 class="site-title">
        <a href="#!">
            <img src="../img/グループ 1315.png" alt="ロゴ">
        </a>
    </h1>
    <a href="#!"><img class="header-twitter" src="../img/white background.png" alt="Twitter"></a>
    <a href="#!" class="header-entry">エントリー</a>
    <nav class="header-nav">
        <ul class="nav-list">
            <li class="nav-item"><a href="#!">Service</a></li>
            <li class="nav-item"><a href="#!">About</a></li>
            <li class="nav-item"><a href="#!">News</a></li>
            <li class="nav-item"><a href="#!">Conetact</a></li>
            <li class="nav-item"><a href="#!">Recruit</a></li>
        </ul>
    </nav>
    <button class="burger-btn">
        <span class="bars">
            <span class="bar bar_top"></span>
            <span class="bar bar_mid"></span>
            <span class="bar bar_bottom"></span>
        </span>
    </button>
    <span class="burger-musk"></span>
  </header>

  <div class="wrapper">
  <section>
    <div class="container text size-L px-sm-5">   
        <form action="confirm.php" method="post">
            <input type="hidden" name="occupation" value="<?php echo $occupation; ?>">
            <input type="hidden" name="name" value="<?php echo $name; ?>">
            <input type="hidden" name="furigana" value="<?php echo $furigana; ?>">
            <input type="hidden" name="sex" value="<?php echo $sex; ?>">
            <input type="hidden" name="zip11" value="<?php echo $zip11; ?>">
            <input type="hidden" name="addr11" value="<?php echo $addr11; ?>">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <input type="hidden" name="tel" value="<?php echo $tel; ?>">
            <input type="hidden" name="portfolio" value="<?php echo $portfolio; ?>">
            <input type="hidden" name="input_file" value="<?php echo $_FILES["input_file"]["name"]; ?>">
            <input type="hidden" name="content" value="<?php echo $content; ?>">
            <input type="hidden" name="agree" value="<?php echo $agree; ?>">

            <div class="font-style-words2">ENTRY</div>
            <div class="font-style-title">入力内容の確認</div>
            <div class="gap-control-probram"></div>
            <div class="gap-control-probram"></div>
            <div class="font-style-title">入力内容をご確認いただき、<br>よろしければ「送信する」ボタンを押して下さい</div>
            <div class="gap-control-probram"></div>
            <div class="gap-control-probram"></div>

            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>お名前</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo $lastNameKanji; + $firstNameKanji; ?></p>
                </div>
            </div>
            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>フリガナ</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo $lastNameKana; + $firstNameKana; ?></p>
                </div>
            </div>
            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>経験年数</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo $experience; ?></p>
                </div>
            </div>
            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>メールアドレス</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo $email; ?></p>
                </div>
            </div>
            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>希望面談形式</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo $interviewType; ?></p>
                </div>
            </div>
            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>希望職種</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo $role; ?></p>
                </div>
            </div>
            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>保有スキル</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo $skillData; ?></p>
                </div>
            </div>
            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>備考</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo nl2br($notes); ?></p>
                </div>
            </div>                
            <div class="row row-margin-none">
                <div class="col-sm-3 col-4">
                    <label>添付ファイル</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo $filename ?></p>
                </div>
            </div>
            <div class="row row-margin-none mt-2">
                <div class="col-sm-3 col-4">
                    <label>個人情報の取り扱いについて</label>
                </div>
                <div class="col-sm-6 col-8">
                    <p><?php echo nl2br($agree); ?></p>
                </div>
            </div>
            <div class="row row-margin-none">
                <div class="col-sm-12 p-md-2 text-center mt-5 mb-5">
                    <button type="submitButton" class="btn btn-blue" name="submit">送信する</button>
                    <input type="button" class="btn btn-gray" value="内容を修正する" onclick=history.back()>
                </div>
            </div>
        </form>
    </div>              
  </section>

  </div>


<!-- footer -->
  <footer class="footer">
    <small>&copy; 1997,2023 mistsolution.All Rights Reserved.</small>
  </footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="../js/header.js"></script>
<script src="../js/entry.js"></script>
<script>
  // URLパラメータからJSONデータを取得
  const urlParams = new URLSearchParams(window.location.search);
  const jsonData = urlParams.get("data");
  
  // JSONデータをオブジェクトに戻す
  const formData = JSON.parse(jsonData);
  
  // オブジェクトの内容を表示する処理を実装（例えば、テーブルやリストで表示）
</script>

<script>
  document.getElementById("submitButton").addEventListener("click", function() {
    // サーバーサイドへデータを送信する処理を実装（fetchを使用）
    fetch('process_form.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: jsonData
    })
    .then(response => response.json())
    .then(data => {
      window.location.href = 'thanks.html'; // 遷移先ページを指定
    })
    .catch(error => {
      window.location.href = 'failure.html'; // 遷移先ページを指定
    });
  });
</script>
</body>
</html>