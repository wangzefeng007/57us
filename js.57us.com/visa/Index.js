/**
 * Created by Foliage on 2016/12/1.
 */
$(function(){
    //轮播图滚动
    jQuery(".visaBan").hover(function(){ jQuery(this).find(".prev,.next").stop(true,true).fadeTo("show",0.8) },function(){ 	jQuery(this).find(".prev,.next").fadeOut() });
    jQuery(".visaBan").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:600, trigger:"click"});
    $(".areaList li .tit").click(function(){
        $(this).parents("li").addClass("on").siblings().removeClass("on")
    })
    jQuery(".hasVisaScrol").slide({titCell:".hd ul",mainCell:"ul",autoPage:true,effect:"topLoop",autoPlay:true,vis:3});
})
