/**
 * Created by Foliage on 2016/12/29.
 */
$(function () {
    //上传模态窗口注入
    var _title = '上传调查表';
    UpModal(_title);

    //定义学生姓名变量
    var ClienteleName = $("#ClienteleName").val();
    //调查表初始化使用的html代码
    var status1html = '<a href="javascript:void(0)" class="delivery">发送</a>';

    //上传模态窗口，点击确实
    $('.sureBtn').on('click',function () {
        var FileName = $(".hasFile").find('.fileName').text();
        var FileVal = $(".hasFile").find('.fileName').attr('val');
        var Message = html_encode($("#Message").val());
        var status = $('.upbtn').attr('data-type');
        //判断是否第一次上传调查表，第一次则验证留言和附件
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

        //调查表，初始化执行此方法
        if(status == '1'){
            $('.QuestionRr a').eq(0).text('重新上传');
            $(".QuestionRr a").eq(1).remove();
            $(".QuestionRr").append(status1html);
            $(".QuestionRl").empty();
            $(".QuestionRl").append('<p class="FileName">已选中<span style="margin-left: 10px" val=""></span></p><p class="MyMessage">我的留言：<span style="margin-left: 10px"></span></p>');
            $(".QuestionRl").find('.FileName span').text(FileName);
            $(".QuestionRl").find('.FileName span').attr('val',FileVal);
            $(".QuestionRl").find(".MyMessage span").html(Message);
            $(".mask").fadeOut(100);
        }else { //调查表，处理中执行此方法
            $(".CounselorCB .time").html(CurentDate()+'&nbsp;'+CurentTime());
            $(".CounselorCB .green").text(FileName);
            $(".CounselorCB .green").attr('val',FileVal);
            $(".CounselorCB .Message span").html(Message);
            $(".CoupleBack").hide();
            $(".DeadCopy").hide();
            $(".CounselorCB").show();
            $(".mask").fadeOut(100);
        }

    })

    //上传调查表，点击发送
    $(document).on('click','.delivery',function () {
        //判断目前调查表目前是什么状态
        var status = $('.upbtn').attr('data-type');

        //初始化状态提交以下参数
        if(status == '1'){
            var ajaxData = {
                'Intention': 'QuestionDelivery',
                'ID':$(".serviceProcess").attr('data-id'),
                'FileName':$(".QuestionRl").find('.FileName span').text(),
                'FileData':$(".QuestionRl").find('.FileName span').attr('val'),
                'Message':$(".QuestionRl").find(".MyMessage span").html(),
                'ProgressStatus':'1',
            }
        }else { //处理中状态提交此参数
            var ajaxData = {
                'Intention': 'QuestionDelivery',
                'ID':$(".serviceProcess").attr('data-id'),
                'FileName':$(".CounselorCB").find('.green').text(),
                'FileData':$(".CounselorCB").find('.green').attr('val'),
                'Message':$(".CounselorCB").find('.Message span').html(),
                'ProgressStatus':'2',
                'OperateID':$("#OperateID").val(),
            }
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

    //确定定稿
    $('.DeadCopy').on('click',function () {
        var ajaxData = {
            'Intention': 'QuestionDeadCopy',
            'ID':$(".serviceProcess").attr('data-id'),
            'QuestionnaireID':$(this).attr('data-id'),
        }

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