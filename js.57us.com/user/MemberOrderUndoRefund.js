/**
 * Created by Foliage on 2016/8/4.
 */
$(function() {
    //订单超时自动取消订单
    updateEndTime();
    //申请退款
    $(".OrderUndoRefund").click(function() {
        var Operation = $(this).text();
        var OrderID = $(this).attr('data-id');
        var Type = $(this).attr('data-type');
        var Status = $(this).attr('data-status');
        if(Type == 'tour') {
            var RefundUrl = '/ajaxorder.html';
            var MethodName = 'CancelTourOrder';
            $('.GetCancePop .erratum').html('<i></i>订错出游日期');
            $('.GetCancePop .erratum').attr('val','订错出游日期');
        } else if(Type == 'zuche') {
            var RefundUrl = '/ajaxorder.html';
            var MethodName = 'CarRentOrderEdit';
            $('.GetCancePop .erratum').html('<i></i>订错租车日期/车型');
            $('.GetCancePop .erratum').attr('val','订错租车日期/车型');
        } else if(Type == 'hotel') {
            var RefundUrl = '/ajaxorder.html';
            var MethodName = 'CancelHotelOrder';
        } else if(Type == 'dingzhi') {
            var RefundUrl = '/ajaxorder.html';
            var MethodName = 'DingZhiOrderEdit';
            $('.GetCancePop .erratum').html('<i></i>订错出游日期');
            $('.GetCancePop .erratum').attr('val','订错出游日期');
        } else if(Type = 'visa'){
            var RefundUrl = '/ajaxorder.html';
            var MethodName = 'CancelVisaOrder';
            $('.GetCancePop .erratum').html('<i></i>订错签证日期');
            $('.GetCancePop .erratum').attr('val','订错签证日期');
        }
        if(Operation == '申请退款') {
            var CancePop = $(".GetCancePop[name='Refund']").html();
            layer.confirm(CancePop, {
                skin: 'Refund',
                title: "申请退款",
                btn: ['申请退款', '点错了'],
                success: function(layero, index) {
                    $('[name="rbt"]').inputbox();
                },
                yes: function(){
                    var text = $('.Refund label.rb_active').text();
                    var ajaxData = {
                        'Intention': MethodName,
                        'OrderNum': OrderID,
                        'Status':Status,
                        'text': text
                    };
                    $.post(_HrefHead + RefundUrl, ajaxData, function(json) {
                        if(json.ResultCode === 200) {
                            layer.msg("申请退款成功！"),
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                        } else {
                            layer.msg(json.Message),
                                setTimeout(function(){
                                    window.location.reload();
                                }, 1000);
                        }

                    }, 'json');
                }
            });
        } else if(Operation == '取消订单') {
            var CancePop = $(".GetCancePop[name='Cance']").html();
            layer.confirm(CancePop, {
                skin: 'Undo',
                title: "订单取消",
                btn: ['取消订单', '点错了'],
                success: function(layero, index) {
                    $('[name="rbt"]').inputbox();
                },
                yes: function() {
                    var text = $('.Undo label.rb_active').text();
                    var ajaxData = {
                        'Intention': MethodName,
                        'Status':Status,
                        'OrderNum': OrderID,
                        'text': text
                    };
                    $.post(_HrefHead + RefundUrl, ajaxData, function(json) {
                        if(json.ResultCode === 200) {
                            layer.msg("订单取消成功"),
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                        } else {
                            layer.msg(json.Message),
                                setTimeout(function(){
                                    window.location.reload();
                                },1000);
                        }

                    }, 'json');
                }
            });
        } else if(Operation == '取消申请退款'|| Operation == '取消退款') {
            var ajaxData = {
                'Intention': MethodName,
                'OrderNum': OrderID
            };
            $.post(_HrefHead + RefundUrl, ajaxData, function(json) {
                if(json.ResultCode === 200) {
                    layer.msg("取消申请退款成功"),
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                } else {
                    layer.msg(json.Message),
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                }

            }, 'json');
        }
    })
})

//订单超时自动取消订单方法
function updateEndTime(){
    var date = new Date();
    var time = date.getTime(); //当前时间距1970年1月1日之间的毫秒数
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
            $(this).find('.remain').html(day+"天"+hour+"小时"+minite+"分"+second+"秒");
        }
        else{
            $(this).removeClass("settime");
            var thisdom = $(this);
            var typeid = $(this).attr('data-id');
            var Type = $(this).attr('data-type');
            if(Type == 'tour') {
                var RefundUrl = '/ajaxorder.html';
                var MethodName = 'CancelTourOrder';
            }else if(Type == 'zuche') {
                var RefundUrl = '/ajaxorder.html';
                var MethodName = 'CarRentOrderEdit';
            } else if(Type == 'hotel') {
                var RefundUrl = '/ajaxorder.html';
                var MethodName = 'CancelHotelOrder';
            } else if(Type == 'dingzhi') {
                var RefundUrl = '/ajaxorder.html';
                var MethodName = 'DingZhiOrderEdit';
            } else if(Type = 'visa'){
                var RefundUrl = '/ajaxorder.html';
                var MethodName = 'CancelVisaOrder';
            }
            var ajaxData = {
                'Intention': MethodName,
                'OrderNum': $(this).find('.OrderNumber').text(),
                'Status':'10',
            };
            if(typeid == '1'){
                $.post(RefundUrl, ajaxData, function(json) {
                    if(json.ResultCode === 200) {
                        thisdom.find('.orderStatus').text('交易关闭（超时）');
                        thisdom.find('.GoPay').parent().remove();
                    }
                }, 'json');
            }
        }
    });
    setTimeout("updateEndTime()",1000);
}