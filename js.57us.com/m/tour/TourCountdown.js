/**
 * Created by Foliage on 2016/11/16.
 */
$(function () {
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
                            $(this).html(hour+"时"+minite+"分"+second+"秒");
                        }
                        else{
                            var _this = $(this);
                            var ajaxData = {
                                'Intention':'Expiration',
                                'OrderID':$(this).attr('data-order'),
                                'type':$(this).attr('data-type'),
                            }
                            $.post('/ajax/',ajaxData,function (data) {
                                if(data.ResultCode == '200') {
                                    window.location.reload();
                                }else {
                                    $.toast(data.Message);
                                }
                            },'json')
                        }
                    });
                },
                1000
            );
        }
    );
}
