/**
 * Created by Foliage on 2016/12/19.
 */
$(function () {
    //旅游首页右侧广告,根据情况滚动条显示隐藏
    var _host = window.location.pathname.split('/');
    var _thisDom = $("#smallAd");
    return
    if(_host[1] == ''){
        var _top = $(".TourMain").offset().top +100;
        $(window).scroll(function() {
            if($(this).scrollTop() < _top) {
                _thisDom.hide();
            }
            if($(this).scrollTop() > _top) {
                _thisDom.show();
            }
        });
    }else {
        $("body").append(righthtml);
    }
    //旅游右侧广告，点击显示显示二维码
    $(document).on('click','.smallAd',function () {
        $(".wrap").append(codehtml);
        $("#adEwmPop").show();
    })
    $(document).on('click','.jgClose',function () {
        $("#adEwmPop").remove();
    });
    $(document).on('click','.smallClose',function () {
        $(".smallAdBbox").remove();
    })
});

var righthtml = '<div class="smallAdBbox" id="smallAd"><div class="smallAd"></div><span class="smallClose">×</span></div>';

var codehtml = '<div class="jgMask" id="adEwmPop" style="display: none">' +
    '<div class="adEwmPop cf">' +
    '<span class="jgClose"></span>' +
    '<img src="http://images.57us.com/img/active/activeEwm.png"/>' +
    '<p class="mt10">请使用微信扫描二维码参与极光砍价活动</p>' +
    '</div>' +
    '</div>';