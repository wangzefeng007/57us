/**
 * Created by Foliage on 2016/10/28.
 */
$(function () {
    //个人介绍的图片展示
    jQuery(".PicTab").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:300,interTime:5000,titCell:".hd a"});
    //案例展示
    jQuery(".OfferScroll").slide({titCell:"",mainCell:".ScrollMain .pic",autoPage:true,effect:"leftLoop",autoPlay:true,vis:2});
    //固定右侧
    $(".autofix_sb").autofix_anything();
    //成功案例展示
    $(".FirstCont").click(function(){
        $(this).parent().addClass("on").siblings().removeClass("on")
        W_ScrollTo($(this).parent(),+62);
    })
    //锚点滚动
    ScrollFixed();

    //首页点击成功案例，跳至对应的案例
    var CID = GetQueryString('CID');
    if(CID !=  'null'){
        $(".suCase li").removeClass('on');
        $(".suCase li").each(function () {
            var _thisId = $(this).attr('data-id');
            if(CID == _thisId){
                $(this).addClass('on');
                $(this).addClass('_this');
                $('body').animate({scrollTop: $('.suCase .on').offset().top -62}, 200);
            }
        })
    }
})


function ScrollFixed() {
    var naviTop = jQuery(".contMenu ul").offset().top;
    jQuery('.contMenu li').click(function() {
        var $dayLi = jQuery(this).index();
        var dInfor = jQuery(".contBox").eq($dayLi).offset().top - 70;
        jQuery('html, body').animate({
            scrollTop: dInfor
        }, 5);
    });

    function checkScroll(forcon, forli, wtop) {
        var next = forcon.size() - 1;
        while(next > -1) {
            var itemTop = forcon.eq(next).offset().top-70;
            if(wtop >= itemTop) {
                forli.eq(next).addClass("on").siblings().removeClass("on");
                return false;
            }
            next--;
        };
    }
    jQuery(window).scroll(function() {

        var wintop = jQuery(window).scrollTop();
        if(naviTop >= wintop) {
            $(".contMenu ul").removeClass("fixnav");
        } else {
            $(".contMenu ul").addClass("fixnav");
        }
        checkScroll(jQuery('.contBox'), jQuery('.contMenu li'), wintop);

    });

}