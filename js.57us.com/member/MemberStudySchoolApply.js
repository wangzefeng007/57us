/**
 * Created by Foliage on 2017/2/15.
 */
$(function () {
    //学样申请，选择录取学校
    $('.applySchool').on('click',function () {
        var ajaxData = {
            'Intention': 'StudentApplySchool', //方法
            'OrderID':$(this).parents('.content').find('.orderId').val(), //对应的订单id
            'ID':$(this).parents('tr').attr('data-id'), //此录取表格当前行对应的id
        }

        //选择学校的名称
        var $title = $(this).parents('tr').find('td').eq(0).text();

        layer.confirm('您确认要选择<span style="color: red">'+$title+'</span>这所学校吗?', {
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