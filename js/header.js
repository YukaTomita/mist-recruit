$(function () {
    // ハンバーガーメニュー
    $(".burger-btn").on('click' ,function(){
        $('.bar').toggleClass('cross'); //バーをクロス
        $('.header-nav').toggleClass('open'); //ナビが開く
        $('.burger-musk').fadeToggle(300); //背景を暗く
        $('body').toggleClass('noscroll'); //スクロールできなく
    });
});


