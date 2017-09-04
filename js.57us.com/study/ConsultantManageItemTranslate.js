/**
 * Created by Foliage on 2017/1/5.
 */
$(function () {
    //上传模态窗口注入
    var _title = '上传翻译材料';
    UpModal(_title);

    //上传模态窗口，点击确实
    $('.sureBtn').on('click',function () {
        var FileName = $(".hasFile").find('.fileName').text();
        var FileVal = $(".hasFile").find('.fileName').attr('val');
        var Message = html_encode($("#Message").val());
        var status = $('.upbtn').attr('data-type');
        //判断是否第一次上传材料翻译，第一次则验证留言和附件
        if(status == '1'){
            if(Message == ''){
                layer.msg('请输入留言');
                return
            }else if(FileName == ''){
                layer.msg('请上传附件');
                return
            }
        }else {
            if(Message == '' && FileName == ''){
                layer.msg('请上传附件或者留言给学生');
                return
            }
        }
        $(".CounselorCB .time").html(CurentDate()+'&nbsp;'+CurentTime());
        $(".CounselorCB .green").text(FileName);
        $(".CounselorCB .green").attr('val',FileVal);
        $(".CounselorCB .Message span").html(Message);
        $(".CoupleBack").hide();
        $(".DeadCopy").hide();
        $(".CounselorCB").show();
        $(".mask").fadeOut(100);
    })

    //顾问发送翻译材料
    $('.delivery').on('click',function () {
         var ajaxData = {
            'Intention': 'DataTranslationDelivery',
            'ID':$(".serviceProcess").attr('data-id'),
            'FileName':$(".CounselorCB").find('.green').text(),
            'FileData':$(".CounselorCB").find('.green').attr('val'),
            'Message':$(".CounselorCB").find('.Message span').html(),
            'ProgressStatus':'2',
        }
        //执行ajax提交
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajaxstudyconmanage.html",
            data: ajaxData,
            beforeSend: function () {
                //提交加载效果
                public_loading();
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },400);
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $("#public_loading").remove();
            }
        });
    })

    //查看全部
    $('.ExamineAll').on('click',function () {
        if($(".HistoryData").is(":hidden")){
            $(".HistoryData").slideDown(400);
            $(this).addClass('on');
            $(this).html('关闭全部<i></i>');
        }else {
            $(".HistoryData").hide();
            $('.ExamineAll').removeClass('on');
            $(this).html('查看全部<i></i>');
        }
    })

})