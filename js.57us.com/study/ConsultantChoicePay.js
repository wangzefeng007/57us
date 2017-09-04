/**
 * Created by Foliage on 2016/9/29.
 */
$(function () {
    //自定义复选框
    $('.cbt').inputbox();
    //订单详情
    $(".payMoreBtn").click(function(){
        $(this).toggleClass("on");
        $(".payIns").toggleClass("ovh");
    })
    //导航添加on
    $(".OrderProcess li:eq(0),.OrderProcess li:eq(1)").addClass('on');
    //选择支付方式后跳转
    $(".PayBtn").click(function () {
        var Payment = $(".payStyle .rb_active").attr('val');
        window.location.href="/order/pay/?Type="+Payment+'&ID='+$("#OrderNum").text();
    })
})