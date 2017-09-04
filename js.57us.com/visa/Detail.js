/**
 * Created by Foliage on 2016/12/1.
 */
$(function() {
    //出行日期初始化
    function Dayb() {
        var mydate = new Date();
        var mydate = new Date(mydate.valueOf() + 1 * 24 * 60 * 60 * 1000);
        if(mydate.getMonth() < 9) {
            var bb = '0' + (mydate.getMonth() + 1);
        } else {
            var bb = (mydate.getMonth() + 1);
        }
        if(mydate.getDate() >= 0 && mydate.getDate() <= 9) {
            var cc = '0' + (mydate.getDate());
        } else {
            var cc = (mydate.getDate());
        }
        var str = "" + mydate.getFullYear() + "-";
        str += bb + "-";
        str += cc;
        return str;
    }

    //出行日期初始化注入
    $("#startDate").val(Dayb());

    //购买数量写入
    $('.num_box').W_NumberBox({
        "min": 1,
        "max": 99,
        "maxlength": 2
    }, function() {
        return true;
    });

    //点击立即预定
    $("#OrderBtn").on('click',function () {
        var startDate = $('#startDate').val();
        var numbers = $('#num').val();
        var url = window.location.href.split('/');
        var id = url[url.length - 1].split('.')[0];
        var price = $(".Price").text();
        window.location = '/visaorder/?id=' + id + '&d=' + startDate + '&n=' + numbers + '&price=' + price;
    })

    //获取虚线高度
    $(".visaPro li").each(function(){
        $(this).find(".sline").height($(this).outerHeight()-128)
    })
    //受理范围
    $(".Accepted .ico").hover(function(){
        layer.tips($(".ReceiveArea").html(), $(this), {
            tips: [4, '#fff'],
            skin: 'diyPop',
            area: ['528px', 'auto'],
            offset: ['100px', '50px']
        });
    },function(){
        layer.closeAll();
    })
    //所需材料切换
    InvTab();
    function InvTab() {
        $(".VisaInv .hd li").click(function() {
            $(this).addClass("on").siblings().removeClass("on");
            var num = $(this).index();
            $(this).parents(".VisaInv").find(".bdList").hide().eq(num).show();
        });
    }
    //定位锚点
    scroll_fix();
    function scroll_fix(){
        var naviTop = jQuery(".VisaDetailMenu").offset().top;
        jQuery('.VisaDetailMenu li').click(function() {
            var $dayLi = jQuery(this).index();
            var dInfor = jQuery(".VisaDetailList").eq($dayLi).offset().top - 80;
            jQuery('html, body').animate({
                scrollTop: dInfor
            }, 5);
        });

        function checkScroll(forcon, forli, wtop) {
            var next = forcon.size() - 1;
            while(next > -1) {
                var itemTop = forcon.eq(next).offset().top - 80;
                if(wtop >= itemTop) {
                    forli.eq(next).addClass("on").siblings().removeClass("on");
                    return false;
                }
                next--;
            }
        }
        jQuery(window).scroll(function() {
            var wintop = jQuery(window).scrollTop();
            if(naviTop >= wintop) {
                $(".VisaDetailMenuLi").removeClass("FixMenu");
            } else {
                $(".VisaDetailMenuLi").addClass("FixMenu");
            }
            checkScroll(jQuery('.VisaDetailList'), jQuery('.VisaDetailMenuLi li'), wintop);
        });

    }
});