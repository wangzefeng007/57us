/**
 * Created by Foliage on 2017/1/3.
 */
$(function () {

    //获取签证当前状态，定义变量以下使用
    var TransactVisaStatus = $("#TransactVisaStatus").val();
    //点击编辑事件
    $('.editeBtn').on('click',function(){
        if(TransactVisaStatus == '3'){
            layer.msg('学生已经确认此签证，无法再进行相关信息的编辑');
            return;
        }
        if($(".visa").is('.edite')){
            var NewSchoolTable = $(".SchoolTable").html();
            if(NewSchoolTable == OldSchoolTable){
                $(".visa").removeClass("edite");
                $(".editeBtn").text('编辑');
                $("#visaType").hide();
            }else {
                layer.confirm('您还没有保存添加相关记录确认要退出吗?', {
                    btn: ['确认','取消'] //按钮
                }, function(index){
                    $(".SchoolTable").empty();
                    $(".SchoolTable").append(OldSchoolTable);
                    $(".visa").removeClass("edite");
                    $(".editeBtn").text('编辑');
                    layer.close(index);
                },function (index) {
                    layer.close(index);
                });
            }
        }else {
            $(".visa").addClass("edite");
            $(".editeBtn").text('取消');
            $("#visaType").inputbox({height:30,width:122});
            $("#visaType").show();
            $(document).on('change','.visa input',function () {
                $(this).prev().text($(this).val());
                $(this).attr('value',$(this).val());
            })
            OldSchoolTable = $(".SchoolTable").html();
        }
    })

    //时间回调到前面的p标签框内
    $(document).on('focus','.SubmitTime,.ResultTime', function() {
        $(this).prev().text($(this).val());
        $(this).attr('value',$(this).val());
    })

    //保存并发送
    $('#VisaSave').on('click',function () {
        var ajaxData = {
            'Intention': 'ApplyVisaSave', //方法
            'ID':$(".serviceProcess").attr('data-id'), //对应的id
            'SubmitTime':$(".SubmitTime").val(), //递交签证日期
            'ResultTime':$(".ResultTime").val(), //签证结果日期
            'VisaState':$(".VisaState").val(), //签证国家
            'AttendSchool':$(".AttendSchool").val(), //入读学校
            'EntranceTime':$(".EntranceTime").val(), //入学时间
            'Status':$("#visaType input").val(), //状态
            'Remark':$(".Remark").val(),  //备注
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
    var OldSchoolTable;
})
