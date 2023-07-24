$(function () {
    /* ページ切り替え（ICT） */
    $("#ict").click(function (event) {
        event.preventDefault();
        $("#ict, #lbd, .contents__ict, .contents__lbd").removeClass("lbd-show");
        $(".contents__ict").addClass("show");
    });

    /* ページ切り替え（LBD） */
    $("#lbd").click(function (event) {
        event.preventDefault();
        $("#ict, #lbd, .contents__ict, .contents__lbd").addClass("lbd-show");
        $(".contents__lbd").addClass("show");
    });

    $(".newcomer-btn1").on('click', function() {
        $(this).siblings('.detail1').toggleClass('open');
    });
    $(".newcomer-btn2").on('click', function() {
        $(this).siblings('.detail2').toggleClass('open');
    });
    $(".newcomer-btn3").on('click', function() {
        $(this).siblings('.detail3').toggleClass('open');
    });  
});



