$(function () {
    //相关问题最后一条去掉border
    $(".relateList li").last().addClass("noBor");
    //显示隐藏悬浮问题
    $(window).scroll(function() {
        var listOff = $(".proDetailBoxM").offset().top;
        var windowOff = $(window).scrollTop();
        if(windowOff > listOff) {
            $(".fixedFun").addClass("on");
        } else {
            $(".fixedFun").removeClass("on");
        }
    });
})

