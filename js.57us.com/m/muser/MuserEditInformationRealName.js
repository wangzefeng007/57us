/**
 * Created by Foliage on 2017/2/27.
 */
$(function () {
    //姓名有值添加清空按钮
    $("#realNmae").on('input',function () {
        var $thisDom = $(this);
        var $superiorDom = $(this).parents('.UserName');
        if($thisDom.val() != ''){
            $superiorDom.find('.clear').show();
        }else {
            $superiorDom.find('.clear').hide();
        }
    });

    //姓名清空文本事件
    $(".clear").on('click',function () {
        $(this).parents('.UserName').find('#realNmae').val('');
    });

    //姓名修改回车事件
    $("#editRealName").submit(function () {
        $("#saveBtn").trigger('click');
        return false;
    });

    //姓名修改点击事件
    $('#saveBtn').on('click',function () {
        var ajaxData = {
            'Intention':"SaveInformation",
            'Type':'realname',
            'RealName':$("#realNmae").val(),
        }
        if(ajaxData.realNmae == ''){
            $.toast('姓名不能为空');
            return
        }else if(rule.NameZH.test(ajaxData.RealName) != true){
            $.toast('姓名为2-8位纯中文');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('提交中');
            },
            success: function(data) {
                if(data.ResultCode == '200'){
                    $.toast('修改成功');
                    setTimeout(function() {
                        history.go(-1);
                    },500);
                }else{
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });
});
