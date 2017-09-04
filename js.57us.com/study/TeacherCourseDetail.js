/**
 * Created by Foliage on 2016/9/23.
 */
$(function () {
    //详细也大图滚动
    jQuery(".DePicScroll").slide({ mainCell:".pic",effect:"fold", delayTime:600,pnLoop:false});

    //固定右侧
    $(".autofix_sb").autofix_anything();

    //锚点滚动
    ScrollFixed();

    //定义变量，当前产品的价格
    var price = $("#Price").text();

    //立即购买点击显示模态窗口
    $(".NowBuyBtn").click(function(){
        if($('.sureClassBuy').is(':hidden')){
        	$("body").css({'height':100+"%",'overflow':'hidden'});
        	$(".mask").show();
            $('.sureClassBuy').show();
            //点击时初始化价格
            $("#TotalPrice").text($(".classMont .on").find('a').attr('val') * price);
        }
    })

    //立即购买模态窗口关闭
    $(".sureClassBuy .close").click(function(){
        $('.sureClassBuy').hide();
        $(".mask").hide();
        $("body").css({'height':"auto",'overflow':'auto'});
    })

    //模态窗口内，点击切换事件
    $(".classMont li").click(function () {
        $('.classMont li').removeClass('on');
        $(this).addClass('on');
        //点击切换时计算价格
        if($(this).attr('class') != 'Custom on'){
            var num = $(this).find('a').attr('val');
            $("#TotalPrice").text(num * price);
        }
        if($(this).attr('class') == 'Custom on'){
            $('.CustomContent').show();
            var num = $(".num_box").find('.num_input').val();
            $("#TotalPrice").text(num * price);
        }else {
            $('.CustomContent').hide();
        }
    })

    //自定义套餐点击增加减少事件
    $('.num_box').W_NumberBox({
        "min": 1,
        "max": 999,
        "maxlength": 3,
        'readonly':false,
    }, function() {
        $(".num_box").on('click',function () {
            var num = $(".num_box").find('input').val();
            $("#TotalPrice").text(num * price);
        })
        return true;
    });

    //手动输入数量时，计算总价格
    $(".num_box input").on('input change',function () {
        $("#TotalPrice").text($(this).val() * price);
    })

    //点击确定下单
    $("#OrdersBtn").click(function () {
        var ProductId = $("#ProductId").attr('data-id');
        if($('.classMont li').attr('class') == 'Custom on'){
            var ProductNum = $(".num_box").find('.num_input').val();
        }else {
            var ProductNum = $('.classMont .on').find('a').attr('val');
        }
        $("#OrdersBtn").text('下单中...');
        $("#OrdersBtn").addClass('course');
        $("#OrdersBtn").attr('id','');
        location.href="/order/course/?ID="+ProductId+'&ProductNum='+ProductNum;
    })

    //服务列表
    $("#servic li").last().addClass('last'); //服务最后一条添加last
    $("#servic li:eq(2)").addClass('last'); //初始化第三条添加last
    var serviclen = $("#servic li").length;
    if(serviclen >3){
        $("#servicemore").show();
    }else {
        $("#servicemore").hide();
    }
    $("#servicemore").on('click',function () {
        $("#servic li").slideDown(500); //点击更多，从上到下显示剩余的条数
        $("#servicemore").hide(); //点击更多，隐藏更div
        $("#servic li:eq(2)").removeClass('last'); //点击更多时第三条移除last
    })
})

//锚点滚动方法
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