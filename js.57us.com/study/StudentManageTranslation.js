/**
 * Created by Foliage on 2016/11/10.
 */
$(function () {
    UpModal();
    //上传模态窗口，点击确实
    $('#sureBtn').on('click',function () {
        var FileName = $(".hasFile").find('.fileName').text();
        var FileVal = $(".hasFile").find('.fileName').attr('val');
        var Message = html_encode($(".Message").val());
        $(".mask").fadeOut(100);
        ajaxData = {
            'Intention': 'StudentTranslation', //方法
            'OrderID':OrderID, //订单id
            'FileName':FileName, //文件名
            'FileData':FileVal, //文件数据 base64位
            'Message':Message, //留言
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

    //材料翻译，确认定稿
    $('.sureBtn2').click(function () {
        ajaxData = {
            'Intention': 'StudentSureTranslation', //方法
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
    //查看全部
    if($(".dialogueList .qhide").length >=1){
        $(".QuestionMore").show();
    }else {
        $(".QuestionMore").hide();
    }
})