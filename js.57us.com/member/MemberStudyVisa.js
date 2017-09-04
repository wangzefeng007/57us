/**
 * Created by Foliage on 2017/2/15.
 */
$(function () {
    //确定签证
    $(".confirmVisa").on('click',function () {
        var ajaxData = {
            'Intention': 'StudentVisa', //方法
            'OrderID':$(this).parents('.content').find('.orderId').val(), //对应的订单id
        }
        layer.confirm('<span style="color: red">您确定签证信息无误？</span>', {
            btn: ['确认','取消'] //按钮
        }, function(index){
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/ajaxstudy/",
                data: ajaxData,
                beforeSend: function () {
                    layer.load(2);
                },
                success: function(data) {
                    if(data.ResultCode=='200'){
                        layer.msg(data.Message);
                        layer.close(index);
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    }else{
                        layer.msg(data.Message);
                    }
                },
                complete: function () { //加载完成提示
                    layer.closeAll('loading');
                }
            });
        },function (index) {
            layer.close(index);
        });
    })
})