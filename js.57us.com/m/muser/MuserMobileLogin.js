/**
 * Created by Foliage on 2017/2/28.
 */
$(document).ready(function () {
    //手机登录获取短信验证码回车事件
    $("#mobileIndex1").submit(function () {
        $("#index1 .smsBtn").trigger('click');
        return false;
    })

    //手机登录获取短信验证码点击事件
    $("#index1 .smsBtn").on('click',function () {
        var o = document.getElementById("smsBtn");
        var ajaxData = {
            'Intention':'MpLogin',
            'User':$("#index1 .user").val(),
        }
        if(ajaxData.User == ''){
            errorHint('#index1 .user','手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true){
            errorHint('#index1 .user', '手机号码式不正确');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('获取中');
            },
            success: function(data) {
                //200=成功 其它都是=异常
                if(data.ResultCode == "200"){
                    get_code_time(o);
                    $.toast(data.Message);
                    var reg = /^(\d{3})\d{4}(\d{4})$/;
                    var tel = ajaxData.User.replace(reg, "$1****$2");
                    $(".tel").text(tel);
                    $("#index1").hide();
                    $("#index2").show();
                }else{
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });

    //重新发送验证码事件
    $("#index2 .smsBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention':'MpLogin',
            'User':$("#index1 .user").val(),
        }
        $.post("/userajax.html",ajaxData,function(data){
            //200=成功 其它都是=异常
            if(data.ResultCode == "200"){
                get_code_time(o);
                $.toast(data.Message);
            }else{
                $.toast(data.Message);
            }
        },'json');
    });

    //回车登录事件
    $("#mobileIndex2").submit(function () {
        $("#index2 .loginBtn").trigger('click');
        return false;
    })

    //点击登录事件
    $("#index2 .loginBtn").on('click',function () {
        var ajaxData = {
            'Intention':'MpLoginVerify',
            'User':$("#index1 .user").val(),
            'Code':$("#index2 .code").val(),
            'Type':$(".study").val(),
        }
        if(ajaxData.Code == ""){
            errorHint('#index2 .code','短信验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)){
            errorHint('#index2 .code','请输入6位纯数字短信验证码');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('登录中');
            },
            success: function(data) {
                //200=成功 100=用户名密码错误 101=验证码错误 102=异常请求 106=账号未注册
                if(data.ResultCode == '200'){
                    $.toast("登录成功");
                    setTimeout(function(){
                        window.location=data.Url;
                    },600);
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