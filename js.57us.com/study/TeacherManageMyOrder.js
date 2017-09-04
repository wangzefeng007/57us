/**
 * Created by Foliage on 2016/10/8.
 */

$(function () {
    //初始化加载
    OrderList();

    //菜单导航点击
    $("#OrderNav li").click(function () {
        $(this).addClass("on").siblings().removeClass("on");
        OrderList();
    })
})

//列表加载方法
function OrderList(Page) {
    ajaxData = {
        'Intention': 'OrderList',
        'Type':$("#OrderNav .on").attr('data-type'),
        'Page':Page,
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/teachermanageajax/",
        data: ajaxData,
        beforeSend: function () { //加载过程效果
            // $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                SuccessOrderList(data);
            }else if(data.ResultCode == "100"){
                FailureOrderList(ajaxData)
            }else {
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            // $("#loading").hide();
        }
    });
}

//列表加载成功，有数据
function SuccessOrderList(data) {
    $("#OrderList").empty();
    var item;
    $.each(data.OrderList, function(i, list) {
        item ='<li data-id="'+list.Id+'">' +
            '<div class="MyOrderHeader">' +
            '<p>订单号：'+list.OrderId+'</p>' +
            '<p>订单日期：'+list.OrderDate+'</p>' +
            '</div>' +
            '<div class="MyOrderBody">' +
            '<img src="'+list.OrderNameImg+'" width="60" height="60">' +
            '<p class="fl StudentName">'+list.OrderName+'</p>' +
            '<p class="MyOrderSe fl">培训科目：'+list.OrderTrainSubject+'</p>' +
            '</div>' +
            '<div class="MyOrderTab">' +
            '<table border="0" cellspacing="0" cellpadding="0" width="100%">' +
            '<tr>' +
            '<td width="130"><img src="'+list.OrderImg+'" width="130" height="97"/></td>' +
            '<td width="234" class="borderR"><p class="ServiceName">'+list.OrderServiceName+'</p></td>' +
            '<td width="237" class="tac borderR">' +
            '<div class="ClassPrice"><span>¥ '+list.OrderCoursePrice+'/课时</span><span>X '+list.OrderCourseNum+'课时</span></div>' +
            '</td>' +
            '<td width="270" class="borderR">' +
            '<div class="ServicePrice">' +
            '<p>总额</p>' +
            '<p class="price mt10">¥<em>'+list.OrderPrice+'</em></p>' +
            '</div>' +
            '</td>' +
            '<td width="249" class="tac">' +
            '<div class="ServicePay">' +
            '<p>'+list.OrderWhetherPay+'</p>' +
            '<p class="borderT">'+list.OrderPayment+'</p>' +
            '</div>' +
            '</td>' +
            '</tr>' +
            '</table>' +
            '</div>' +
            '</li>' ;
        $('#OrderList').append(item);
    });

    // //分页机制
    if(data.PageCount >1){
        diffPage(data);
    }

}
//列表加载成功，没有数据
function FailureOrderList(ajaxData) {
    $("#OrderList").empty();
    html = '<div class="NoServiceBox">' +
        '<div class="NoService mt50">' +
        '<i class="noIco"></i>' +
        '<p class="tit mt35">还没有用户下单哦~</p>' +
        '</div>' +
        '</div>';
    $('#OrderList').append(html);
    if(ajaxData.Type == '3'){
        $(".NoServiceBox").find('.tit').text('还没有已完成的订单哦~');
    }else if(ajaxData.Type == '4'){
        $(".NoServiceBox").find('.tit').text('还没有未支付的订单哦~');
    }
}

//分页
function diffPage(pageNumData) {
    var pageDemo = $('#pageDemo').html(); //获取模版
    var pageDemoParent = $('#Page');
    var showWhichPage = 1;
    $('#Page').empty();
    if(pageNumData.Page) {
        laytpl(pageDemo).render(pageNumData, function(html) {
            var $html = $(html);
            $html.appendTo('#Page').on('click', function() {
            });
            //页面点击事件
            $("#Page a").click(function () {
                var Page = $(this).attr('data-id');
                if (Page == '0'){
                    return
                }else if(Page == 'undefined'){
                    return
                }
                OrderList(Page)
            })

            var allPage = $html.parent().find('a');
            for(var i = 0; i < allPage.length; i++) {
                if(Number($(allPage[i]).html()) == pageNumData.Page) {
                    $(allPage[i]).addClass('on');
                }
            }
            if(pageNumData.Page === 1){
                $('.prve').addClass('prvestop')
            }
            if(pageNumData.Page === 1) {
                $(allPage[0]).addClass('no');
                pageDemoParent.find('.firstEllipsis').remove();
            }
            if(pageNumData.Page < 5) {
                pageDemoParent.find('.first').remove();
            }
            if(pageNumData.Page > 1) {
                $(".prev").removeClass("no");
            }
            if(pageNumData.Page > 5) {
                $(".first").after('<span class="firstEllipsis">...</span>');
            }

            if(pageNumData.PageCount < 7) {
                pageDemoParent.find('.lastEllipsis').remove();
                pageDemoParent.find('.PageCount').remove();
            }

            if(pageNumData.Page == pageNumData.PageCount) {
                $(allPage[allPage.length - 1]).addClass('no');
                pageDemoParent.find('.lastEllipsis').remove();
            }
            if(pageNumData.Page == pageNumData.PageCount) {
                $(allPage[allPage.length - 1]).addClass('no');
                $(".next").addClass('nextstop');
                pageDemoParent.find('.PageCount').remove();
            }
            if(pageNumData.Page == pageNumData.PageCount - 1) {
                pageDemoParent.find('.PageCount').remove();
                pageDemoParent.find('.lastEllipsis').remove();
            } else if(pageNumData.Page === pageNumData.PageCount - 2) {
                pageDemoParent.find('.lastEllipsis').remove();
                pageDemoParent.find('.PageCount').remove()
            }
            if(pageNumData.Page === pageNumData.PageCount - 3) {
                pageDemoParent.find('.lastEllipsis').remove();
            }
        });
    }
}