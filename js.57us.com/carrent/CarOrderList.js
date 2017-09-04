require(['laytpl', '57common', "inputbox"], function(laytpl) {
    InicoHeight();
    //显示门店信息
    $(".GetCarListIns .ShowShop").click(function() {
        $(this).hide();
        $(".GetCarListInsLi").find("table").removeClass("hidden");
        InicoHeight();
    });
    var statusBox = $('.CarOrderCont .nr').find('span');
    var Status = statusBox.last().html().split('：')[1];
    var OrderNum = statusBox.first().html().split('：')[1];
    //console.log(Status, OrderNum);
    //取消订单
    $(".FunBtnBox .CanceBtn").click(function() {
        var CancePop = $(".GetCancePop").html();
        layer.confirm(CancePop, {
            skin: 'GetCancePop',
            title: "订单取消",
            btn: ['取消订单', '点错了'],
            success: function(layero, index) {
                $('[name="rbt"]').inputbox();
            },
            yes: function() {
                var text = $('label.rb_active').text();
                var ajaxData = {
                    'Status': Status,
                    'OrderNum': OrderNum,
                    'text': text
                };
                $.post(_HrefHead + '/ajaxorderedit.html', ajaxData, function(json) {
                    var icon;
                    if (json.ResultCode === 200) {
                        iconNum = 1;
                    } else {
                        iconNum = 2;
                    }
                    layer.msg(json.Message, {
                        skin: 'GetCancePopMsg',
                        icon: iconNum,
                        time: 1000,
                    });
                    setTimeout(window.location.reload(), 1000);
                }, 'json');
            }
        });
    });
});

function InicoHeight() {
    var InicoHeight1 = $(".GetCarListInsLi").eq(0).offset().top;
    var InicoHeight2 = $(".GetCarListInsLi").eq(1).offset().top;
    $(".GetCarDetailIns .Inico").height(InicoHeight2 - InicoHeight1 + 20);
}
