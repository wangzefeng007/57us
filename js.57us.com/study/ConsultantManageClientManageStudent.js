/**
 * Created by Foliage on 2016/12/29.
 */

$(function () {
    //添加留学备注方法
    $(document).on('click','.BeizhuAdd',function(){
        layer.open({
            type: 1,
            title:"添加留学备注",
            skin: 'BeizhuAddPop', //样式类名
            btn: ['确定', '取消'],
            area: ['377px'],
            closeBtn: 0, //不显示关闭按钮
            shift: 2,
            shadeClose: true, //开启遮罩关闭
            content:$(".PopTextarea").html(),
            yes: function(index, layero){
                html = '<li>' +
                    '<p class="nr">' + $('.BeizhuAddPop #RemarksInfo').val() + '</p>' +
                    '<p class="time mt10"><span class="date">'+ CurentDate()+'</span><span class="pl20 date-time">'+CurentTime()+'</span><span class="StudyRemarksEdit pl20">编辑</span> <span class="StudyRemarksDel pl20">删除</span></p>' +
                    '</li>';
                $("#StudyRemarks").append(html);
                layer.close(index);
            },cancel: function(){
                //右上角关闭回调
            }
        });
    });

    //留学备注删除
    $(document).on('click','.StudyRemarksDel',function () {
        $(this).parent().parent().remove();
    });

    //留学备注编辑
    $(document).on('click','.StudyRemarksEdit',function () {
        var EditIndex = $(this).parent().parent().index();
        var EditText = html_decode($(this).parent().parent().find('.nr').html());
        var EditHtml = '<div class="PopTextarea">' +
            '<textarea name="RemarksInfo" id="RemarksInfo" rows="" class="InforTextare" placeholder="">'+ EditText +'</textarea>' +
            '</div>';
        layer.open({
            type: 1,
            title:"编辑留学备注",
            skin: 'BeizhuEditPop', //样式类名
            btn: ['确定', '取消'],
            area: ['477px'],
            closeBtn: 0, //不显示关闭按钮
            shift: 2,
            shadeClose: true, //开启遮罩关闭
            content:EditHtml,
            yes: function(index, layero){
                html = '<p class="nr">' + $('.BeizhuEditPop #RemarksInfo').val() + '</p>' +
                    '<p class="time mt10"><span class="date">'+ CurentDate()+'</span><span class="pl20 date-time">'+CurentTime()+'</span><span class="StudyRemarksEdit pl20">编辑</span> <span class="StudyRemarksDel pl20">删除</span></p>';
                $("#StudyRemarks li").eq(EditIndex).html(html);
                layer.close(index);
            },cancel: function(){
                //右上角关闭回调
            }
        });
    })

    //点击保存,ajax提交
    $(document).on('click','#studentInforBtn',function () {
        var StudyRemarks = [];
        $('#StudyRemarks li').each(function () {
            StudyRemarks.push({'text':$(this).find('.nr').text(),'date':$(this).find('.date').text(),'time':$(this).find('.date-time').text()});
        })
        var ajaxData = {
            'Intention':'CustomerDataSave',
            'ID': $("#InfoID").val(),
            'StudentName':$("#StudentName").val(),
            'Tel':$("#StudentTel").val(),
            'QQ':$("#StudentQQ").val(),
            'Mail':$("#StudentMail").val(),
            'Nationality':$("#StudentNationality").val(),
            'Birthday':$("#StudentBirthday").val(),
            'PatriarchName':$("#StudentParentsName").val(),
            'PatriarchTel':$("#StudentParentsTel").val(),
            'OtherTel':$("#StudentParentsOtherTel").val(),
            'PassportNum':$("#StudentPassport").val(),
            'NowGrade':$("#CurrentGrade").val(),
            'ApplyCountry':$("#ApplyCountry").val(),
            'StudyAbroadTime':$("#StudyDate").val(),
            'School':$("#School").val(),
            'ApplyProject':$("#ApplyItems").val(),
            'GRA':$("#GRA").val(),
            'TOEFL':$("#TOEFL").val(),
            'IELTS':$("#IELTS").val(),
            'GRE':$("#GRE").val(),
            'GMAT':$("#GMAT").val(),
            'SAT':$("#SAT").val(),
            'SSAT':$("#SSAT").val(),
            'ACT':$("#ACT").val(),
            'Remarks':StudyRemarks,
        };
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
                    },500);
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