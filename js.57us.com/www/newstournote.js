$(function(){
    //tab切换
    jQuery(".Examination").slide({ titCell:".hd a",mainCell:".bd"});
})
scroll_fix();
function scroll_fix(){
    var naviTop = jQuery(".TourNoteBox").offset().top;
    jQuery('.TourNoteMenu li').click(function() {
        var $dayLi = jQuery(this).index();
        var dInfor = jQuery(".TourNoteL").eq($dayLi).offset().top
        jQuery('html, body').animate({
            scrollTop: dInfor
        }, 500);
    });
    function checkScroll(forcon, forli, wtop) {
        var next = forcon.size() - 1;
        while (next > -1) {
            var itemTop = forcon.eq(next).offset().top - 70;
            if (wtop >= itemTop) {
                forli.eq(next).addClass("on").siblings().removeClass("on");
                return false;
            }
            next--;
        };
    }
    jQuery(window).scroll(function() {
        var wintop = jQuery(window).scrollTop();
        var othertop = $(".statement").offset().top;
        var menuhright = $(".TourNoteMenu").height();
        if (wintop >= naviTop) {
            if(wintop >= othertop){
                $(".TourNoteMenu").hide()
            }else{
                $(".TourNoteMenu").fadeIn();
            }
        }else {
            $(".TourNoteMenu").hide()
        }
        checkScroll(jQuery('.TourNoteL'), jQuery('.TourNoteMenu li'), wintop);
    });
}