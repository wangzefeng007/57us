/**
 * Created by Foliage on 2016/12/30.
 */
$(function () {

    //定义变量此步骤是否已经完成
    var ChooseSchoolStatus = $("#ChooseSchoolStatus").val();

    //判断是否有推送的历史记录,有的话显示 无则隐藏
    if($(".History table tr").length >1){
        $(".History").show();
    }else {
        $(".History").hide();
    }

    //点击编辑事件
    $(document).on('click','.editeBtn',function(){
        if(ChooseSchoolStatus == '3'){
            layer.msg('此流程已经走完，无法再次编辑');
            return
        }
        if($(".new").is('.edite')){
            var NewSchoolTable = $(".SchoolTable").html();
            if(NewSchoolTable == OldSchoolTable){
                $(".new").removeClass("edite");
                $(".editeBtn").text('编辑');
            }else {
                layer.confirm('您还没有保存添加相关记录确认要退出吗?', {
                    btn: ['确认','取消'] //按钮
                }, function(index){
                    $(".SchoolTable").empty();
                    $(".SchoolTable").append(OldSchoolTable);
                    $(".new").removeClass("edite");
                    $(".editeBtn").text('编辑');
                    layer.close(index);
                },function (index) {
                    layer.close(index);
                });
            }
        }else {
            if($(".SchoolTable tr").length < 2){
                $('.SchoolTable').append(AddTrHtml);
            }
            $(".new").addClass("edite");
            $(".editeBtn").text('取消');
            $(document).on('change','.NewData input',function () {
                $(this).prev().text($(this).val());
                $(this).attr('value',$(this).val());
            })
            OldSchoolTable = $(".SchoolTable").html();
        }
    })

    //增加行
    $(document).on('click','#AddNewTr',function () {
        $('.SchoolTable').append(AddTrHtml);
    })

    //删除行
    $(document).on('click','.delete',function () {
        var location = $(this).parent().parent().index();
        layer.confirm('您确认要删除此条记录吗?', {
            btn: ['确认','取消'] //按钮
        }, function(index){
            layer.msg('删除成功');
            $(".SchoolTable tr").eq(location).remove();
            layer.close(index);
        },function (index) {
            layer.close(index);
        });
    })

    //保存
    $(document).on('click','#SchoolSelectionSave',function () {
        var SchoolData = [];
        $('.SchoolTable .NewData').each(function () {
            SchoolData.push({'SchoolName':$(this).find('.SchoolName').val(),'SchoolTime':$(this).find('.SchoolTime').val(),'SchoolSystem':$(this).find('.SchoolSystem').val(),'LanguageRequirement':$(this).find('.LanguageRequirement').val(),'SchoolUrl':$(this).find('.SchoolUrl').val(),'Remark':$(this).find('.Remark').val()});
        })
        var ajaxData = {
            'Intention': 'SchoolSelectionSave',
            'ID':$(".serviceProcess").attr('data-id'),
            'Type':1,
            'SchoolData':SchoolData,
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

    //发送
    $(document).on('click','#sendBtn',function () {
        var SchoolData = [];
        $('.SchoolTable .NewData').each(function () {
            SchoolData.push({'SchoolName':$(this).find('.SchoolName').val(),'SchoolTime':$(this).find('.SchoolTime').val(),'SchoolSystem':$(this).find('.SchoolSystem').val(),'LanguageRequirement':$(this).find('.LanguageRequirement').val(),'SchoolUrl':$(this).find('.SchoolUrl').val(),'Remark':$(this).find('.Remark').val()});
        })
        var ajaxData = {
            'Intention': 'SchoolSelectionSave',
            'ID':$(".serviceProcess").attr('data-id'),
            'Type':2,
            'SchoolData':SchoolData,
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

//添加行html代码
var AddTrHtml = '<tr class="NewData">' +
    '<td height="30px"><p class="resultTex"></p><input type="text" name="SchoolName" class="SchoolName" value="" /></td>' +
    '<td><p class="resultTex"></p><input type="text" name="SchoolTime" class="SchoolTime" value="" /></td>' +
    '<td><p class="resultTex"></p><input type="text" name="SchoolSystem" class="SchoolSystem" value="" /></td>' +
    '<td><p class="resultTex"></p><input type="text" name="LanguageRequirement" class="LanguageRequirement" value="" /></td>' +
    '<td><p class="resultTex"></p><input type="text" name="SchoolUrl" class="SchoolUrl" id="" value="" /></td>' +
    '<td><p class="resultTex"></p><input type="text" name="Remark" class="Remark" value="" /></td>' +
    '<td class="white"><a href="javascript:void(0)" class="delete">删除</a></td>' +
    '</tr>';