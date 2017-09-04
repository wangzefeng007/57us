/**
 * Created by Foliage on 2017/2/28.
 */
$(document).ready(function () {
    //手机登录获取短信验证码回车事件
    $("#registerIndex1").submit(function () {
        $("#index1 .smsBtn").trigger('click');
        return false;
    })

    //手机登录获取短信验证码点击事件
    $("#index1 .smsBtn").on('click',function () {
        var o = document.getElementById("smsBtn");
        var ajaxData = {
            'Intention':'GetVerifyCode',
            'User':$("#index1 .user").val(),
        }
        if(ajaxData.User == ''){
            errorHint('#index1 .user','手机号码或者邮箱不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true){
            errorHint('#index1 .user', '手机号码或者邮箱式不正确');
            return
        }
        //console.log(ajaxData.User);
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('获取中');
            },
            success: function(data) {
                //200代表成功，101账户已注册，其它状态返回状态码与Message
                if(data.ResultCode == "200"){
                    get_code_time(o);
                    $.toast(data.Message);
                    var reg = /^(\d{3})\d{4}(\d{4})$/;
                    var tel = ajaxData.User.replace(reg, "$1****$2");
                    $(".tel").text(tel);
                    $("#index1").hide();
                    $("#index2").show();
                }else if(data.ResultCode == "101") {
                    $.confirm('该账号已注册，是否直接登录？', function () {
                        window.location='/muser/login/';
                    });
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
            'Intention':'GetVerifyCode',
            'User':$("#index1 .user").val(),
        };
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

    //点击下一步至设置密码
    $("#index2 .nextBtn").on('click',function () {
        var ajaxData = {
            'Intention':'DoVerify', //方法
            'User':$("#index1 .user").val(), //账号
            'Code':$("#index2 .code").val(), //短信验证码或邮箱验证码
        };
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
                $.showPreloader('提交中');
            },
            success: function(data) {
                //200=成功 100=用户名密码错误 101=验证码错误 102=异常请求 105=错误超过3次要验证码 106=账号未注册
                if(data.ResultCode == '200'){
                    $.toast("验证成功");
                    $("#index2").hide();
                    $("#index3").show();
                }else{
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });

    //设置密码
    $("#index3 .registerBtn").on('click',function () {
        //ajax提交参数
        var ajaxData = {
            'Intention':'RegisterUser',
            'User':$("#index1 .user").val(),
            'PassWord':$("#index3 .password").val(),
            'PassWordConfirm':$("#index3 .rePassword").val(),
        }
        //执行验证
        if(ajaxData.PassWord == '') {
            errorHint('#index3 .password', '密码不能为空');
            return
        } else if(!/^(?=.*?[a-zA-Z])(?=.*?[0-6])[!"#$%&'()*+,\-./:;<=>?@\[\\\]^_`{|}~A-Za-z0-9]{6,20}$/i.test(ajaxData.PassWord)) {
            errorHint('#index3 .password', '密码为6-20位数字、字母和符号');
            return
        }else if(ajaxData.PassWordConfirm == ''){
            errorHint('#index3 .rePassword', '请再次输入密码');
            return
        }else if(ajaxData.PassWord != ajaxData.PassWordConfirm){
            errorHint('#index3 .rePassword', '两密码不一致');
            return
        }
        //执行ajax提交操作
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('设置中');
            },
            success: function(data) {
                //200 成功 100=保存失败 101=密码格式错误 103=账号错误 102=异常请求 104=两次密码不一致
                if(data.ResultCode == "200"){
                    $.toast("注册成功！57美国网欢迎您！");
                    setTimeout(function(){
                        window.location=data.Url;
                    },1000);
                }else if(data.ResultCode == "101"){
                    errorHint('#index3 .password', '密码格式不正确');
                }else if(data.ResultCode == "104"){
                    errorHint('#index3 .rePassword', '两密码不一致');
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