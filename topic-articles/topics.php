<?php
  $articlesData = file_get_contents('topic-articles.json');
  $articles = json_decode($articlesData)->articles;

  $perPage = 20; // Number of articles per page
  $totalPages = ceil(count($articles) / $perPage); // Calculate total pages

  // Get page number from query string, default to 1
  $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

  // Calculate the starting and ending index for the current page
  $startIndex = ($currentPage - 1) * $perPage;
  $endIndex = min($startIndex + $perPage - 1, count($articles) - 1);
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
    <meta property="og:title" content="株式会社MIST solution | WEBサイト"/>
    <meta property="og:site_name" content="株式会社MIST solution | WEBサイト">
    <meta name="og:description" content="株式会社MIST solution - トップページ 株式会社ミストソリューションは、異なった業界との接点を持つことで化学反応を起こし、
    幅広いニーズにより的確にお応えできる、常に進化しているIT企業です。">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ja-JP">
    <meta property="og:image" content="assets/images/mist-ogp.jpg">
    <meta name="twitter:card" content="summary"/>
    <!-- 各々変更 -->
    <title>日々の出来事</title>
    <!-- css,js -->
    <link rel="stylesheet" href="CSS/reset.css" type="text/css">
    <link rel="stylesheet" href="CSS/common.css" type="text/css">
    <link rel="stylesheet" href="CSS/article.css" type="text/css">
    <!-- favicon -->
    <link rel="icon" href="img/favicon.ico">
</head>
<body>
<!-- header -->
<header class="header flex-box">
<h1 class="site-title">
    <a href="#!">
        <img src="img/グループ 1315.png" alt="ロゴ">
    </a>
</h1>
<a href="#!"><img class="header-twitter" src="img/white background.png" alt="Twitter"></a>
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
<!--Title-->
<div class="title-band">
    <div class="topic-title text-center">
        私たちの日々の出来事<span class="font-style-comments2">　／　トピックス</span>
    </div>
</div>
<div class="wrapper">
    <!--パンくず-->
    <nav>
        <ul class="breadcrumbs">
            <li class="breadcrumbs-li"><a href="index.html">TOP</a></li>
            <li class="breadcrumbs-li">お知らせ</li>
            <li class="breadcrumbs-li">履歴</li>
        </ul>
    </nav>

    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

    <!--コンテンツ内容-->
    <div class="text-left txt2">過去一覧</div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    
    <!--記事一覧-->
    <ul id="article-list" class="article-list">
        <?php
            for ($i = $startIndex; $i <= $endIndex; $i++) {
                $article = $articles[$i];
                echo '<li class="article">';
                echo '<div class="article-image">';
                echo '<img src="images/' . $article->image . '" alt="Article Image">';
                echo '</div>';
                echo '<div class="article-content">';
                echo '<div class="article-date">' . date('Y.m.d', strtotime($article->date)) . '</div>';
                echo '<h2 class="article-title">' . $article->title . '</h2>';
                echo '</div>';
                echo '<a href="articles/' . $article->url . '" class="read-more">もっと見る &gt;</a>';
                echo '</li>';
            }
        ?>
    </ul>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>

<!-- Pager -->
<div class="pager">
    <?php
      echo '<a href="?page=' . max($currentPage - 1, 1) . '">＜</a> '; // Previous page link

      for ($page = 1; $page <= $totalPages; $page++) {
        echo '<a href="?page=' . $page . '" class="' . ($page === $currentPage ? 'current' : '') . '">' . $page . '</a> ';
      }

      echo '<a href="?page=' . min($currentPage + 1, $totalPages) . '">＞</a>'; // Next page link
    ?>
  </div>
</div>
<!--エントリー以下-->
<div class="wrapper">
    <div class="entry">
        <P class="font-style-comments entry-space">まずはあなたのキャリアプランを聞かせてください。</P>
        <button onclick="location.href='#!'" class="entry-button">　エントリー</button>
        <p class="entry-red">入社からプロジェクト着任までのフローが知りたい方はこちら ></p>
    </div>
    <div class="flex-link">
        <img class="link-img" src="img/グループ 1824.png">
        <img class="link-img" src="img/グループ 1826.png">
    </div>
    <div class="flex-link">
        <img class="link-img" src="img/グループ 1827.png">
        <img class="link-img" src="img/グループ 1828.png">
    </div>
</div>
    
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
    <div class="gap-control-probram"></div>
            
    <!-- footer -->
    <footer class="footer">
    <small>&copy; 1997,2023 mistsolution.All Rights Reserved.</small>
    </footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="js\header.js"></script>
</body>
</html>