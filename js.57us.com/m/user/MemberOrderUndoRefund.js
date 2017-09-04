/**
 * Created by Foliage on 2016/11/25.
 */
$(function () {
    $(document).on("click",".pxBtn",function(){
        $(".pxBox").addClass("on");
        $(".mask").show();
    });
    $(document).on("click",".mask",function(){
        $(".pxBox").removeClass("on");
        $(".mask").hide();
    })
    updateEndTime();
})

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
                            $(this).find('.remain').html(hour+"时"+minite+"分"+second+"秒");
                        }
                        else{
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
                                        thisdom.find('.orderStatus').text('支付超时(订单关闭)');
                                        thisdom.find('.GoPay').parent().remove();
                                        $(this).removeClass("settime");
                                    }
                                }, 'json');
                            }
                        }
                    });
                },
                1000
            );
        }
    );
}