/**
 * Created by Foliage on 2017/2/15.
 */
$(function () {
    //模态窗口注入
    uploadModal('上传翻译材料');

    //材料翻译，上传操作
    $(document).on('click','#upModal .submit',function () {
        var ajaxData = {
            'Intention': 'StudentTranslation', //方法
            'OrderID':orderId, //订单id
            'FileName':$("#upModal .hasFile").find('.fileName').text(), //文件名
            'FileData':$("#upModal .hasFile").find('.fileName').attr('val'), //文件数据 base64位
            'Message':html_encode($("#upModal .Message").val()), //留言
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
                    $("#upModal").hide();
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

    //材料翻译，确认定稿
    $('.finaLize').on('click',function () {
        //ajax提交参数
        var ajaxData = {
            'Intention': 'StudentSureTranslation', //方法
            'ID':$(this).parents('.content').find('.orderId').val(), //对应的id
        }
        layer.confirm('您确定定稿吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
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
    })
})