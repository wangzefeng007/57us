/**
 * Created by Foliage on 2017/2/27.
 */
$(function () {

    //修改密码密码回车事件
    $("#editPassword").submit(function () {
        $("#saveBtn").trigger('click');
        return false;
    })

    //修改密码按钮点击事件
    $("#saveBtn").on('click',function () {
        var ajaxData = {
            'Intention':'ModifyPass',//手机修改密码方法
            'Pass':$("#oldPassword").val(), //旧的密码
            'NewPass':$("#newPassword").val(), //新的密码
            'RePass':$("#rePassword").val(), //再次输入新的密码
        }
        if(ajaxData.NewPass == ''){
            $.toast('新的密码不能为空');
            return
        }else if(!/^(?=.*?[a-zA-Z])(?=.*?[0-6])[!"#$%&'()*+,\-./:;<=>?@\[\\\]^_`{|}~A-Za-z0-9]{6,20}$/i.test(ajaxData.NewPass)){
            $.toast('密码为6-20位数字、字母和符号');
            return
        }else if(ajaxData.RePass == ''){
            $.toast('请再次输入新的密码');
            return
        }else if(ajaxData.NewPass != ajaxData.RePass){
            $.toast('两次新密码不一致');
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
    })
})