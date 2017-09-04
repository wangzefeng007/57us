/**
 * Created by Foliage on 2017/3/1.
 */
$(document).ready(function () {
    //初始化加载
    loadPage('1');
    //点击订单筛选
    $(".sxbtn").on("click", function() {
        if($(this).hasClass("on")) {
            $(this).removeClass("on");
            $("#shaixuan").hide();
            $(".collectMask").hide();
        } else {
            $(this).addClass("on");
            $("#shaixuan").show();
            $(".collectMask").show();
        }
    })
    $(".collectMask").on("click", function() {
        $(".mangeList,.sxbtn").removeClass("on");
        $("#orderSty,#shaixuan").hide();
        $(this).hide();
    })

    //筛选选择点击
    $("#shaixuan a").on('click',function () {
        $("#shaixuan").hide();
        $(".collectMask").hide();
        $("#shaixuan a").removeClass('on');
        $(this).addClass('on');
        $("#list").empty();
        loadPage('1');
    })

    //下拉刷新页面
    $(document).one("pageInit", ".page", function(e, id, page) {
        var $content = $(page).find(".content").on('refresh', function(e) {
            // 模拟2s的加载过程
            window.location.reload();
            $.pullToRefreshDone($content);
        });
    });

    $.init();
});

/**
 * 页面数据加载
 *
 * @param Intention 加载
 * @param Status '0'全部 '1'//待支付 '2'//已支付 '3'//取消 '4'//退款 5 未评价
 * @param Page 页码
 */
//定义页码变量
var j = 2;
function loadPage(page) {
    var ajaxData = {
        'Intention': 'MyOrder', //订单列表
        'Status': $("#shaixuan .on").attr('data-id'), // '0'全部 '1'//待支付 '2'//已支付 '3'//取消 '4'//退款 5 未评价
        'Page':page, //页码
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/ajaxtourorder/",
        data: ajaxData,
        beforeSend: function () {
            $.showPreloader();
        },
        success: function(data) {
            if(data.ResultCode == '200'){
                loadPageSucceed(data);
                maxItems = data.RecordCount;
            }else if(data.ResultCode == '102'){
                loadPageError(data);
            }else{
                $.toast(data.Message);
            }
        },
        complete: function () { //加载完成提示
            $.hidePreloader();
        }
    });

    //计算全部可以加载的条数
    var maxItems = '';
    $(document).on("pageInit", function() {
        var loading = false;
        $("#orderAll").on('infinite', function() {
            // 如果正在加载，则退出
            if (loading) return;
            // 设置flag
            loading = true;

            ajaxData = {
                'Intention': 'MyOrder', //订单列表
                'Status': $("#shaixuan .on").attr('data-id'),
                'Page':j, //页码
            }

            $.post('/ajaxtourorder/',ajaxData, function(data) {
                // 重置加载flag
                loading = false;
                if(data.ResultCode == '200') {
                    maxItems = data.RecordCount;
                    if(data.Data.length) {
                        //循环输出列表数据
                        var item = '';
                        $.each(data.Data, function(i, list) {
                            item +='<li class="card">';
                            item +='<div class="card-header">';
                            item +='<p>'+list.Category+'</p>';
                            item +='<p class="pull-right cardRigh">';
                            item +='<span>订单号：'+list.OrderNum+'</span>';
                            item +='</p>';
                            item +='</div>';
                            item +='<div class="card-content">';
                            item +='<a href="'+list.ProductUrl+'">';
                            item +='<div class="card-content-inner cf">';
                            item +='<p class="tit">'+list.OrderName+'</p>';
                            item +='<p class="contB c9">';
                            item +='<span class="pull-left">出行日期：'+list.Depart+'</span>';
                            item +='<span class="pull-right">结束日期：'+list.DepartEnd+'</span>';
                            item +='</p>';
                            item +='</div>';
                            item +='</a>';
                            item +='</div>';
                            item +='<div class="card-footer c9">';
                            item +='<span>'+get_status_text(list.PayType)+'</span>';
                            item +='<p class="cardBtn">';
                            item +='<a href="'+list.OrderUrl+'" class="button button-fill button-orange">'+get_status_urltext(list.PayType)+'</a>';
                            item +='</p>';
                            item +='</div>';
                            item +='</li>';
                        });
                        $('#list').append(item);
                        //计算目前所有li的条数
                        var lastIndex = $('#list li').length;
                        //重新计算页码
                        j = Number(j) + Number('1');

                        //当加载完所有内容，注销无限加载事件
                        if (lastIndex >= maxItems) {
                            // 加载完毕，则注销无限加载事件，以防不必要的加载
                            $.detachInfiniteScroll($('.infinite-scroll'));
                            // 删除加载提示符
                            $('.infinite-scroll-preloader').hide();
                            $(".pullUpLabel").show();
                            return;
                        }
                    } else {
                        // 加载完毕，则注销无限加载事件，以防不必要的加载
                        $.detachInfiniteScroll($('.infinite-scroll'));
                        // 删除加载提示符
                        $('.infinite-scroll-preloader').hide();
                        $(".pullUpLabel").show();
                        return;
                    }
                }
                //sui上拉刷新重置
                $.refreshScroller();
            }, 'json')
        });
    });
}

/**
 * 加载成功并且有数据
 *
 * @param data 返回的json数据
 * @param data.Category 模块
 * @param data.OrderNum 订单编号
 * @param data.Depart 出发时间
 * @param data.DepartEnd 结束时间
 * @param data.TourProductID 产品ID
 * @param data.OrderName 订单名字
 * @param data.PayType 支付状态
 * @param data.OrderUrl 订单详情
 * @param data.ProductUrl 产品对应的url
 * @param data.PayUrl 支付地址
 */
function loadPageSucceed(data) {
    $("#list").empty();
    $("#noOrder").remove();
    if(data.RecordCount > 6){
        $.attachInfiniteScroll($('.infinite-scroll'));
        $(".pullUpLabel").hide();
        $(".infinite-scroll-preloader").show();
    }else {
        $.detachInfiniteScroll($('.infinite-scroll'));
        $(".infinite-scroll-preloader").hide();
        $(".pullUpLabel").show();
    }
    //循环输出列表数据
    var item = '';
    $.each(data.Data, function(i, list) {
        item +='<li class="card">';
        item +='<div class="card-header">';
        item +='<p>'+list.Category+'</p>';
        item +='<p class="pull-right cardRigh">';
        item +='<span>订单号：'+list.OrderNum+'</span>';
        item +='</p>';
        item +='</div>';
        item +='<div class="card-content">';
        item +='<a href="'+list.ProductUrl+'">';
        item +='<div class="card-content-inner cf">';
        item +='<p class="tit">'+list.OrderName+'</p>';
        item +='<p class="contB c9">';
        item +='<span class="pull-left">出行日期：'+list.Depart+'</span>';
        item +='<span class="pull-right">结束日期：'+list.DepartEnd+'</span>';
        item +='</p>';
        item +='</div>';
        item +='</a>';
        item +='</div>';
        item +='<div class="card-footer c9">';
        item +='<span>'+get_status_text(list.PayType)+'</span>';
        item +='<p class="cardBtn">';
        item +='<a href="'+list.OrderUrl+'" class="button button-fill button-orange">'+get_status_urltext(list.PayType)+'</a>';
        item +='</p>';
        item +='</div>';
        item +='</li>';
    });
    $('#list').append(item);
}

/**
 * 加载成功，无数据
 */
function loadPageError() {
    $("#list").empty();
    $(".infinite-scroll-preloader").hide();
    $(".infinite-scroll-preloader").hide();
    // 加载完毕，则注销无限加载事件，以防不必要的加载
    $.detachInfiniteScroll($('.infinite-scroll'));
    $(".pullUpLabel").hide();
    $('.content').append(errorHtml);
}

//没有订单情况下html
var errorHtml = '';
errorHtml +='<div class="noOrder" id="noOrder">';
errorHtml +='<div class="noOrderCont mt10">';
errorHtml +='<div class="noBox">';
errorHtml +='<i class="icon iconfont icon-smile"></i>';
errorHtml +='<p class="tit">您暂时没有美国旅游订单</p>';
errorHtml +='<a href="http://m.57us.com/tour/" class="button backHome">去首页看看</a>';
errorHtml +='</div>';
errorHtml +='</div>';
errorHtml +='</div>';

/**
 * 获取支付状态文本
 *
 * @param 1 待付款
 * @param 2 已付款
 * @param 3 已取消
 * @param 4 待评价
 * @param 5 已完成
 * @param 6 退款中
 * @param 7 已退款
 *
 */
function get_status_text(type) {
    switch(type){
        case 1:
            return "待付款";
            break;
        case 2:
            return "已付款";
            break;
        case 3:
            return "已取消";
            break;
        case 4:
            return "待评价";
            break;
        case 5:
            return "已完成";
            break;
        case 6:
            return "退款中";
            break;
        case 7:
            return "已退款";
            break;
    }
}

/**
 * 获取支付状态url文本
 *
 * @param 1 待付款
 * @param 2 已付款
 * @param 3 已取消
 * @param 4 待评价
 * @param 5 已完成
 * @param 6 退款中
 * @param 7 已退款
 *
 */
function get_status_urltext(type) {
    switch(type){
        case 1:
        return "去支付";
        break;
        case 2:
            return "去评价";
            break;
        case 3:
            return "已关闭";
            break;
        case 4:
            return "去评价";
            break;
        case 5:
            return "已完成";
            break;
        case 6:
            return "退款中";
            break;
        case 7:
            return "已退款";
            break;
    }
}