/**
 * Created by Foliage on 2017/2/14.
 */
$(function () {
    //模态窗口注入
    uploadModal('上传简历');

    //模态窗口，点击确定
    $(document).on('click','#upModal .submit',function () {
        var ajaxData = {
            'Intention': 'StudentQuestion', //方法
            'ID':orderId, //对应的id
            'FileName':$("#upModal .hasFile").find('.fileName').text(), //文件名
            'FileData':$("#upModal .hasFile").find('.fileName').attr('val'), //文件数据 base64位
            'Message':html_encode($("#upModal .Message").val()), //留言
        }

        //执行验证是第一次上传，还是第二次上传
        if(orderLength <= '1'){
            if(ajaxData.Message == ''){
                layer.msg('请输入留言');
                return
            }else if(ajaxData.FileName == ''){
                layer.msg('请上传附件');
                return
            }
        }else {
            if(ajaxData.Message == '' && ajaxData.FileName == ''){
                layer.msg('请上传附件或者留言给顾问');
                return
            }
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
})