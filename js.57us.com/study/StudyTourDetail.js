/**
 * Created by Foliage on 2016/12/30.
 */
$(function() {

    jQuery(".stutoDePic").hover(function() {
        jQuery(this).find(".prev,.next").stop(true, true).fadeTo("show", 0.9)
    }, function() {
        jQuery(this).find(".prev,.next").fadeOut()
    });
    jQuery(".stutoDePic").slide({
        mainCell: ".pic",
        effect: "fold",
        autoPlay: true,
        delayTime: 600,
        trigger: "click"
    });
    //相关推荐
    jQuery(".ScrollPic").slide({
        mainCell: ".pic",
        effect: "left",
        delayTime: 600,
        autoPage: true,
        effect: "left",
        autoPlay: true,
        vis: 4
    });

    //获取遮罩的高度
    $(".DayBoxNr .line").each(function() {
        var allHeight = $(this).siblings("ul").height()
        var zzheight = $(this).siblings("ul").find("li").last().find(".suNr").height()
        $(this).height(allHeight - zzheight - 35)
    });
    //选择出发时间美化
    $('div[name="timeItem"]').inputbox({
        height: 33,
        width: 200
    });

    //选择出发时间对应截止时间
    $("#Date a").on('click',function () {
        var _end = $(this).attr('data-end');
        if(_end != '请选择出发时间'){
            $(".endDate").show();
            $(".endDate>span").text(_end);
        }else {
            $(".endDate").hide();
        }
    })
    //执行天数滚动方法
    scroll_fix();

    //数量递增递减
    $('.num_box').W_NumberBox({
        "min": 1,
        "max": 99,
        "maxlength": 2
    }, function() {
        return true;
    });

    //计算总价
    $(".num_box").on('click',function () {
        var total = Number($("#num").val()) * Number($("#price").attr('data-price'));
        $("#price").text(total);
    })

    //出发时间input去除边框
    $("#Date").on('click',function () {
        $(this).css('border','');
    })

    //点击立即预定
    $("#nowBuy,#oncebtn").on('click', function() {
        var _id = $(this).attr('data-id');
        var _date = $('#Date input').val();
        var _price = $("#price").text();
        var _num = $("#num").val();
        if (_date == '请选择出发时间') {
            $("#Date").css('border','1px solid #ff6767');
            W_ScrollTo('.wrap');
            layer.msg('请选择出发时间');
            return
        }
        window.location = '/studytour/placeorder/?id=' + _id + '&d=' + _date + '&p=' + _price + '&n=' + _num;
    })
})

function scroll_fix() {
    if ($(".DayBox").length > 0) {
        var naviTop = jQuery(".contMenu").offset().top;
        var jumpEnd = $(".stDetailContM .contBox").eq(2).offset().top +30;
        jQuery('.contMenu li').click(function() {
            var $dayLi = jQuery(this).index();
            var dInfor = jQuery(".contBox").eq($dayLi).offset().top - 50;
            jQuery('html, body').animate({
                scrollTop: dInfor
            }, 500);
        });
        jQuery('.DateMenu li').click(function() {
            var $dayLi = jQuery(this).index();
            var dInfor = jQuery(".DayBox").eq($dayLi).offset().top - 50;
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
            var charge = wintop - 280;
            if (naviTop >= wintop) {
                $(".contMenu ul").removeClass("fix_xc");
                $(".contMenu #oncebtn").hide();
            } else {
                $(".contMenu ul").addClass("fix_xc");
                $(".contMenu #oncebtn").show()
            }

            if (naviTop >= charge) {
                $(".DateMenu ul").removeClass("FixDateMenu");
            }else if($(".stDetailContM .contBox").eq(2).offset().top +30 <= charge){
                $(".DateMenu ul").removeClass("FixDateMenu");
            }else {
                $(".DateMenu ul").addClass("FixDateMenu");
            }
            checkScroll(jQuery('.contBox'), jQuery('.contMenu li'), wintop);
            checkScroll(jQuery('.DayBox'), jQuery('.DateMenu li'), wintop);
        });
    }


}