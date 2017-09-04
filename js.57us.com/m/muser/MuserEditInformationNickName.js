/**
 * Created by Foliage on 2017/2/27.
 */
$(function () {
    //昵称有值添加清空按钮
    $("#nickName").on('input',function () {
        var $thisDom = $(this);
        var $superiorDom = $(this).parents('.UserName');
        if($thisDom.val() != ''){
            $superiorDom.find('.clear').show();
        }else {
            $superiorDom.find('.clear').hide();
        }
    });

    //昵称清空文本事件
    $(".clear").on('click',function () {
        $(this).parents('.UserName').find('#nickName').val('');
    });

    //昵称修改回车事件
    $("#editNick").submit(function () {
        $("#saveBtn").trigger('click');
        return false;
    });

    //昵称修改点击事件
    $('#saveBtn').on('click',function () {
        var ajaxData = {
            'Intention':"SaveInformation",
            'Type':'nickname',
            'NickName':$("#nickName").val(),
        }
        if(ajaxData.NickName == ''){
            $.toast('昵称不能为空');
            return
        }else if(rule.Nick.test(ajaxData.NickName) != true){
            $.toast('昵称格式3-15位中英文');
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