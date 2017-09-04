/**
 * Created by Foliage on 2016/11/8.
 */
$(function () {
    $(".Accepted").on('click',function () {
        ajaxData = {
            'Intention': 'StudentVisa', //方法
            'OrderID':$(this).attr('data-id'), //对应的订单id
        }
        layer.confirm('<span style="color: red">您确定签证信息无误？</span>', {
            btn: ['确认','取消'] //按钮
        }, function(index){
            $.post("/studentmanageajax/",ajaxData,function(data){
                if(data.ResultCode == "200"){
                    layer.close(index);
                    layer.msg(data.Message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                }else {
                    layer.msg(data.Message);
                }
            },'json');
        },function (index) {
            layer.close(index);
        });
    })
})
