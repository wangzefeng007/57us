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
        url: "/ajaxstudyconmanage.html",
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

//列表加载成功
function SuccessOrderList(data) {
    $("#OrderList").empty();
    var item;
    $.each(data.OrderList, function(i, list) {
        item = '<li data-id="'+list.Id+'" data-type="'+list.OrderWhetherEnd+'">' +
            '<div class="MyOrderHeader">' +
            '<p>订单号：'+list.OrderId+'</p>' +
            '<p>订单日期：'+list.OrderDate+'</p>' +
            '</div>' +
            '<div class="MyOrderBody">' +
            '<img src="'+list.OrderNameImg+'" width="60" height="60">' +
            '<p class="fl StudentName">'+list.OrderName+'</p>' +
            '<span class="MyOrderStyle fl"><em></em>'+list.OrderServiceStatus+'</span>' +
            '<p class="MyOrderSe fl">服务类型：'+list.OrderServiceType+'</p>';
        if(list.ServiceType==1 || list.ServiceType==2){
            item=item+'<div class="fr MyOrderDown">' +
                '<i class="ico"></i>' +
                '<p class="timeDown settime" endTime="'+list.OrderEndDate+'"></p>' +
                '<span class="helpIco" data-text="学生还在三天犹豫期，如有疑问，请联系57US。"></span>' +
                '</div>';
        }
        item=item+'</div>' +
            '<div class="MyOrderTab">' +
            '<table border="0" cellspacing="0" cellpadding="0" width="100%">' +
            '<tr>' +
            '<td width="130"><img src="'+list.OrderImg+'" width="130" height="97"/></td>' +
            '<td width="234" class="borderR"><p class="ServiceName">'+list.OrderServiceName+'</p></td>' +
            '<td width="270" class="borderR">' +
            '<div class="ServicePrice">' +
            '<p>总额</p>' +
            '<p class="price mt10">¥<em>'+list.OrderPrice+'</em></p>' +
            '</div>' +
            '</td>' +
            '<td width="249" class="borderR tac">' +
            '<div class="ServicePay">' +
            '<p>'+list.OrderWhetherPay+'</p>' +
            '<p class="borderT">'+list.OrderPayment+'</p>' +
            '</div>' +
            '</td>' +
            '<td width="237" class="tac">' +
            '<a href="'+list.OrderUrl+'" class="CheckMore transition">查看详情</a>' +
            '</td>' +
            '</tr>' +
            '</table>' +
            '</div>' +
            '</li>';
        $('#OrderList').append(item);
    });

    //订单倒计时
    updateEndTime();

    // //分页机制
    if(data.PageCount >1){
        diffPage(data);
        $("#Page").show();
    }else {
        $("#Page").hide();
    }

    //分析意向,犹豫期内倒计时显示
    $(".MyOrderDown .helpIco").each(function(){
        $(this).hover(function(){
            var content=$(this).attr("data-text")
            layer.tips(content, $(this), {
                tips: [2, '#eee'],
                skin: 'OrderTip',
                time: 400000
            });
        },function(){
            layer.closeAll();
        })
    })
}

function FailureOrderList(ajaxData) {
    $("#OrderList").empty();
    html = '<div class="NoServiceBox">' +
        '<div class="NoService mt50">' +
        '<i class="noIco"></i>' +
        '<p class="tit mt35">还没有用户下单哦~</p>' +
        '</div>' +
        '</div>';
    $('#OrderList').append(html);
    if(ajaxData.Type == '2'){
        $(".NoServiceBox").find('.tit').text('还没有服务中的订单哦~');
    }else if(ajaxData.Type == '3'){
        $(".NoServiceBox").find('.tit').text('还没有已完成的订单哦~');
    }else if(ajaxData.Type == '4'){
        $(".NoServiceBox").find('.tit').text('还没有未支付的订单哦~');
    }else if(ajaxData.Type == '5'){
        $(".NoServiceBox").find('.tit').text('还没有已终止的订单哦~');
    }
}

//获取服务器时间
function ajaxDate(callback){
    if(typeof callback!='function') return;
    var ajaxObject;
    try{
        ajaxObject=new XMLHttpRequest();
    }catch(e){
        try{
            ajaxObject=new ActiveXObject('Microsoft.XMLHTTP');
        }catch(e){
        }
    }
    if(!ajaxObject) return;
    if(ajaxObject.overrideMimeType){
        ajaxObject.overrideMimeType('text/html');
    }
    //location.href可以换成其他url，但必须是同一个站点的链接，并且文件存在
    ajaxObject.open('get',location.href);
    ajaxObject.send(null);
    ajaxObject.onreadystatechange=function(){
        if(ajaxObject.readyState==4){
            if(ajaxObject.status==200){
                callback(ajaxObject);
            }
        }
    };
}

//倒计时插件
function updateEndTime(){
    ajaxDate(
        function(ao){
            //只需要AJAX一次，将服务器时间获取后以毫米为单位保存到一个变量中
            time=Date.parse(ao.getResponseHeader('Date'));
            //设置定时器每过一秒动态刷新一次时间
            setInterval(
                function(){
                    time+=1000;
                    $(".settime").each(function(i){
                        var endDate =this.getAttribute("endTime"); //结束时间字符串
                        var endDate1 = eval('new Date(' + endDate.replace(/\d+(?=-[^-]+$)/, function (a) { return parseInt(a, 10) - 1; }).match(/\d+/g) +')'); //转换为时间日期类型
                        var endTime = endDate1.getTime(); //结束时间毫秒数
                        var lag = (endTime - time) / 1000; //当前时间和结束时间之间的秒数
                        if(lag > 0)
                        {
                            var second = Math.floor(lag % 60);
                            var minite = Math.floor((lag / 60) % 60);
                            var hour = Math.floor((lag / 3600) % 24);
                            var day = Math.floor((lag / 3600) / 24);
                            $(this).html(day+"天"+hour+"时"+minite+"分"+second+"秒");
                            $(this).parent().show();
                        }
                        else{
                            $(this).parent().hide();
                        }
                    });
                },
                1000
            );
        }
    );
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