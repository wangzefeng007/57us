$(function(){
    //申请退款
    $(".ReRefund").click(function() {
        var CancePop = "<div id='refundWin'>" + $(".GetCancePop[name='Refund']").html() + "</div>";
        layer.confirm(CancePop,{
            skin: 'GetCancePop',
            title: "申请退款",
            btn: ['申请退款', '点错了'],
            success: function(layero, index) {
                $('[name="rbt"]').inputbox();
            },
            yes: function() {

            }
        });
    });

    //取消订单
    $(".CancelOrder").click(function() {
        var CancePop = $(".GetCancePop[name='Cance']").html();
        layer.confirm(CancePop, {
            skin: 'GetCancePop',
            title: "订单取消",
            btn: ['取消订单', '点错了'],
            success: function(layero, index) {
                $('[name="rbt"]').inputbox();
            },
            yes: function() {

            }
        });
    });
})