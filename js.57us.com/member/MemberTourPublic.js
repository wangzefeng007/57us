/**
 * Created by Foliage on 2017/2/13.
 */
$(function () {
    //订单超时自动取消订单
    updateEndTime();

    //取消订单
    $(".cancelRefundOrderBtn").on('click',function () {
        var $thisDom = $(this).parents('tr');
        var $thisType = $thisDom.attr('data-type');
        var $thisId = $thisDom.attr('data-id');
        var $thisVal = $(this).attr('data-val');
        //0代表取消订单 1代表申请退款 2代表取消申请退款 3取消退款
        if($thisVal == '0'){
            var _title = '取消订单';
            var _subhead = '取消';
        }else if($thisVal == '1'){
            var _title = '申请退款';
            var _subhead = '退款';
        }
        if($thisType == 'tour'){
            var _text = '订错出游日期';
            var _IntentionName = 'CancelTourOrder';
        }else if($thisType == 'zuche'){
            var _text = '订错租车日期/车型';
            var _IntentionName = 'CarRentOrderEdit';
        }else if($thisType == 'hotel'){
            var _text = '订错入住日期/酒店房型';
            var _IntentionName = 'CancelHotelOrder';
        }else if($thisType == 'dingzhi'){
            var _text = '订错出游日期';
            var _IntentionName = 'DingZhiOrderEdit';
        }else if($thisType == 'visa'){
            var _text = '订错签证日期';
            var _IntentionName = 'CancelVisaOrder';
        }
        if($thisVal == '0' || $thisVal == '1'){
            cancel_refund_order($thisId,_title,_subhead,_text,_IntentionName);
        }
    });
    //关闭模态窗口
    $(document).on('click','#cancel_refund .close,#cancel_refund .canceBtn',function () {
        $('#cancel_refund').remove();
    });
})

/**
 * 取消订单，申请退款方法
 *
 * @param id 订单id
 * @param title 窗口标题
 * @param subhead 窗口类型
 * @param text 窗口类型文件
 * @param IntentionName 提交方法名
 */
function cancel_refund_order(id,title,subhead,text,IntentionName) {
    var html = '';
    html +='<div class="mpopMask mpoptk" id="cancel_refund">';
    html +='<div class="mpop">';
    html +='<div class="mpopBox">';
    html +='<span class="close"><i class="icon iconfont icon-guanbifuzhi"></i></span>';
    html +='<p class="mpoptransT">'+title+'</p>';
    html +='<table border="0" cellspacing="0" cellpadding="0" width="100%" class="transTab mt10">';
    html +='<tr>';
    html +='<th colspan="2">'+subhead+'原因：</th>';
    html +='</tr>';
    html +='<tr>';
    html +='<td colspan="2">';
    html +='<div name="style" type="selectbox" class="diyselect cause">';
    html +='<div class="opts">';
    html +='<a href="javascript:void(0);" value="1">行程改变</a>';
    html +='<a href="javascript:void(0);" value="2">'+text+'</a>';
    html +='<a href="javascript:void(0);" value="3">信息填写错误</a>';
    html +='<a href="javascript:void(0);" value="4">其他</a>';
    html +='<a href="javascript:void(0);" value="0" class="selected">请先择'+subhead+'原因</a>';
    html +='</div>';
    html +='</div>';
    html +='</td>';
    html +='</tr>';
    html +='<tr>';
    html +='<th colspan="2">退款说明</th>';
    html +='</tr>';
    html +='<tr>';
    html +='<td colspan="2">';
    html +='<textarea name="message" rows="" cols="" id="message" class="tktextarea" placeholder="请输入'+subhead+'说明"></textarea>';
    html +='</td>';
    html +='</tr>';
    html +='<tr>';
    html +='<th></th>';
    html +='<td>';
    html +='<a href="JavaScript:void(0)" class="publicBtn2 sureBtn submitBtn">提交申请</a>';
    html +='<a href="JavaScript:void(0)" class="publicBtn2 canceBtn">取消</a>';
    html +='</td>';
    html +='</tr>';
    html +='</table>';
    html +='</div>';
    html +='</div>';
    html +='</div>';
    $("body").append(html);
    popCenterWindow('mpop','3.3','2');
    $("#cancel_refund .cause").inputbox({
        height:40,
        width:392
    });
    $("#cancel_refund").show();
    $('#cancel_refund .submitBtn').on('click',function () {
        var ajaxData = {
            'Intention': IntentionName,
            'OrderID': id,
            'Type':$("#cancel_refund").find('.cause input').val(),
            'Text': html_encode($("#cancel_refund").find('#message').val()),
        };
        if(ajaxData.Type == '0'){
            layer.msg('请先择'+subhead+'原因');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajaxorder.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode == '200'){
                    $('#cancel_refund').remove();
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },600);
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () {
                layer.closeAll('loading');
            }
        });
    })
}

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
        }else{
            $(this).removeClass("settime");
            var $thisdom = $(this);
            var $thisOrderId = $(this).attr('data-id');
            var $thisType = $(this).attr('data-type');
            if($thisType == 'tour'){
                var _IntentionName = 'CancelTourOrder';
            }else if($thisType == 'zuche'){
                var _IntentionName = 'CarRentOrderEdit';
            }else if($thisType == 'hotel'){
                var _IntentionName = 'CancelHotelOrder';
            }else if($thisType == 'dingzhi'){
                var _IntentionName = 'DingZhiOrderEdit';
            }else if($thisType == 'visa'){
                var _IntentionName = 'CancelVisaOrder';
            }
            var ajaxData = {
                'Intention': _IntentionName,
                'OrderId': $thisOrderId,
            };
            $.post('/ajaxorder.html', ajaxData, function(data) {
                if(data.ResultCode === 200) {
                    $thisdom.find('.orderStatus').text('交易关闭（超时）');
                    $thisdom.find('.cancelRefundOrderBtn').remove();
                }
            }, 'json');
        }
    });
    setTimeout("updateEndTime()",1000);
}

/**
 * 函数描述
 *
 * @param id 参数 模态窗口的id
 * @param w 参数 根据模态窗口大小调整距离left
 * @param h 参数 根据模态窗口大小调整距离top
 */
function popCenterWindow(id,w,h) {
    //获取窗口的高度
    var windowHeight = $(window).height();
    //获取窗口的宽度
    var windowWidth = $(window).width();
    //计算弹出窗口的左上角Y的偏移量
    var popY = windowHeight / w;
    var popX = windowWidth / h;
    //设定窗口的位置
    $("."+id+"").css("top", popY).css("left", popX);
}