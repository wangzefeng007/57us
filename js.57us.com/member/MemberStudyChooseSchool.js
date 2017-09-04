/**
 * Created by Foliage on 2017/2/14.
 */
$(function () {
    //确定选校点击
    $('.finaLize').on("click",function(){
        var $orderID = $(this).parents('.content').find('.orderId').val();
        layer.confirm('您确定定校吗', {
            btn: ['确定','取消'] //按钮
        }, function(){
            // //确定选校
            if($(this).attr('data-type') == '0'){
                layer.msg('请先操作未确认的学校，再确认定校');
                return
            }
            ajaxData = {
                'Intention': 'StudentSureSchool', //方法
                'ID':$orderID, //对应的id
            }

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
        });
    });

    //确定选择点击事件
    $(".Accepted").on('click',function () {
        var ajaxData = {
            'Intention': 'StudentChoseSchool', //方法
            'ID':$(this).parents('tr').attr('data-id'), //对应的id
            'Type':'1', // 1代表已确定，
        }
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
    })

    //驳回选校点击
    $(".Reject").on('click',function () {
        var ajaxData = {
            'Intention': 'StudentChoseSchool', //方法
            'ID':$(this).parents('tr').attr('data-id'), //对应的id
            'Type':'0', // 0代表驳回，
        }
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
    })
})
