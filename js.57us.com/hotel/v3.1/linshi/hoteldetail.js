$(function() {
	//自定义下拉
	$('div[name="city"]').inputbox({
        height:30,
        width:70
  });
  //定位滚动
	ScrollFixed();
  //弹出筛选
  $(".posTit").click(function(){
  	$(this).parent().toggleClass("on")
  });
  //图文相册
  jQuery(".hotelPic").slide({ mainCell:".pic",titCell:".hdList li",effect:"fold", autoPlay:true, delayTime:600, trigger:"click"});
});
//定位锚点
function ScrollFixed() {
    var naviTop = jQuery(".contMenu").offset().top;
    jQuery('.contMenu ul li').click(function() {
        var $dayLi = jQuery(this).index();
        var dInfor = jQuery(".contBox").eq($dayLi).offset().top - 50;
        jQuery('html, body').animate({
            scrollTop: dInfor
        }, 5);
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
        if (naviTop >= wintop) {
            $(".contMenu ul").removeClass("fix_xc");
            $("#oncebtn").hide();
        } else {
            $(".contMenu ul").addClass("fix_xc");
            $("#oncebtn").show();
        }
        checkScroll(jQuery('.contBox'), jQuery('.contMenu ul li'), wintop);

    });

}