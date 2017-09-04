/**
 * Created by Foliage on 2017/2/28.
 */
$(document).ready(function () {
    //登录回车事件
    $('#loginForm').submit(function () {
        $(".loginBtn").trigger('click');
        return false;
    })

    //登录点击事件
    $(".loginBtn").on('click',function () {
        var ajaxData = {
            'Intention':'MuserLogin', //方法
            'User':$(".user").val(), //账号
            'Pass':$(".password").val(), //密码
            'ImageCode':$(".imgCode").val(), //图形验证码
            'Type':$(".study").val(), //是否为留学过来
        };
        if(ajaxData.User == ''){
            errorHint('.user','账号不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true) {
            errorHint('.user', '手机号码或邮箱格式不正确');
            return
        }else if(ajaxData.Pass == '') {
            errorHint('.password', '密码不能为空');
            return
        }
        // if(ajaxData.ImageCode == ''){
        //     errorHint('.imgCode','图形验证码不能为空');
        //     return
        // }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('登录中');
            },
            success: function(data) {
                //200=成功 100=用户名密码错误 101=验证码错误 102=异常请求 105=错误超过3次要验证码 106=账号未注册
                if(data.ResultCode == '200'){
                    $.toast("登录成功");
                    setTimeout(function(){
                        window.location=data.Url;
                    },600);
                }else if(data.ResultCode == '105'){
                    $(".yzm").show();
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