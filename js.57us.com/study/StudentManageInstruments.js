/**
 * Created by Foliage on 2016/11/7.
 */
$(function () {
    UpModal();

    if($(".dialogueList li") > 0){
        var _num = $(".dialogueList li").length();
        if(_num > 1){
            $(".sureBtn2").hide();
        }else {
            $(".sureBtn2").show();
        }
    }
    //上传模态窗口，点击确实
    $('#sureBtn').on('click',function () {
        console.log(OrderLength);
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
            'Intention': 'StudentInstruments', //方法
            'ID':OrderID, //对应的id
            'Type':$('.sureBtn2').attr('data-type'), //对应的是第几个 1代表简历 2代表RL 3代表PS 4代表ESSAY
            'FileName':FileName, //文件名
            'FileData':FileVal, //文件数据 base64位
            'Message':Message, //留言
        }
        //提交ajax提交
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
            complete: function () {
                $("#public_loading").remove();
            }
        });
    })

    //文书管理，确认定稿
    $('.sureBtn2').click(function () {
        ajaxData = {
            'Intention': 'StudentSureInstruments', //方法
            'ID':$(this).attr('data-id'), //对应的id
            'Type':$(this).attr('data-type'), //对应的是第几个 1代表简历 2代表RL 3代表PS 4代表ESSAY
        }
        $.post("/studentmanageajax/",ajaxData,function(data){
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })
})

