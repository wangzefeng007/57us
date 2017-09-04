/**
 * Created by Foliage on 2016/11/7.
 */
$(function () {
    UpModal();
    //上传模态窗口，点击确实
    $(document).on('click','#sureBtn',function () {
        var FileName = $(".hasFile").find('.fileName').text();
        var FileVal = $(".hasFile").find('.fileName').attr('val');
        var Message = html_encode($(".Message").val());
        if(OrderLength <= '1'){
            if(Message == ''){
                layer.msg('请输入留言');
                return
            }else if(FileName == ''){
                layer.msg('请上传附件');
                return
            }
        }else {
            if(Message == '' && FileName == ''){
                layer.msg('请上传附件或者留言给顾问');
                return
            }
        }
        $(".mask").fadeOut(100);
        var ajaxData = {
            'Intention': 'StudentQuestion', //方法
            'ID':OrderID, //对应的id
            'FileName':FileName, //文件名
            'FileData':FileVal, //文件数据 base64位
            'Message':Message, //留言
        }
        //执行ajax提交
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/studentmanageajax/",
            data: ajaxData,
            beforeSend: function () {
                //提交加载效果
                public_loading();
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $("#public_loading").remove();
            }
        });
    })
})
