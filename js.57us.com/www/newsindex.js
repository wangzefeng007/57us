$(function(){
    //幻灯片
    jQuery(".IndexBan").hover(function(){ jQuery(this).find(".prev,.next").stop(true,true).fadeTo("show",0.7) },function(){ jQuery(this).find(".prev,.next").fadeOut() });
    jQuery(".IndexBan").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:600, trigger:"click"});
    //移民左边切换
    $(".IndexImmgClass li").hover(function(){
        $(this).addClass("on").siblings().removeClass("on")
    })
    //移民右边幻灯片
    jQuery(".ImmgScroll").slide({ mainCell:".ImmgScrollMain ul",titCell:'.Immghd li', effect:"fold", delayTime:300, autoPlay:false });
    //移民大切换
    jQuery(".tabBox").slide({ titCell:".IndexStudyHd a", mainCell:".bd",delayTime:0 });
    //考试切换
    jQuery(".ExamTab").slide({ titCell:".TabHd a", mainCell:".TabBd",delayTime:0 });
    //留学生活切换
    jQuery(".IndexStudyLife").slide({ titCell:".LifeHd a", mainCell:".LifeBd",delayTime:0 });
    //专业解析
    jQuery(".Analytical").slide({ titCell:".TabHd a", mainCell:".TabBd",delayTime:0 });
    //去除导航的border
    $(".SeconMenuM").each(function(){
        $(this).find("a").last().next().hide()
    })

    // var _thisDom = $("#smallAd");
    // var _top = $(".IndexTopMain").offset().top +480;
    //
    // $(window).scroll(function() {
    //     if($(this).scrollTop() < _top) {
    //         _thisDom.hide();
    //     }
    //     if($(this).scrollTop() > _top) {
    //         _thisDom.show();
    //     }
    // });
    //
    // //资讯右侧广告，点击显示显示二维码
    // $(document).on('click','.smallAd',function () {
    //     $(".wrap").append(codehtml);
    //     $("#adEwmPop").show();
    // });
    // $(document).on('click','.jgClose',function () {
    //     $("#adEwmPop").remove();
    // });
    // $(document).on('click','.smallClose',function () {
    //     $(".smallAdBbox").remove();
    // })

})

var codehtml = '<div class="jgMask" id="adEwmPop" style="display: none">' +
    '<div class="adEwmPop cf">' +
    '<span class="jgClose"></span>' +
    '<img src="http://images.57us.com/img/active/activeEwm.png"/>' +
    '<p class="mt10">请使用微信扫描二维码参与极光砍价活动</p>' +
    '</div>' +
    '</div>';
