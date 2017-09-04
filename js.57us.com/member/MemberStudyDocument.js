/**
 * Created by Foliage on 2017/2/14.
 */
$(function () {
    //根据当前类型设置模态窗口的标题
    var $type = $(".content .orderId").attr('data-type');
    if($type == '1'){
        var title = '上传简历';
    }else if($type == '2'){
        var title = '上传RL';
    }else if($type == '3'){
        var title = '上传PS';
    }else if($type == '4'){
        var title = '上传ESSAY';
    }

    //模态窗口注入
    uploadModal(title);

    //模态窗口，点击确定
    $(document).on('click','#upModal .submit',function () {
        var ajaxData = {
            'Intention': 'StudentInstruments', //方法
            'ID':orderId, //对应的id
            'Type':$type, //对应的是第几个 1代表简历 2代表RL 3代表PS 4代表ESSAY
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

    //文书管理，确认定稿
    $('.finaLize').on('click',function () {
        //ajax提交参数
        var ajaxData = {
            'Intention': 'StudentSureInstruments', //方法
            'ID':$(this).parents('.content').find('.orderId').val(), //对应的id
            'Type':$type, //对应的是第几个 1代表简历 2代表RL 3代表PS 4代表ESSAY
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