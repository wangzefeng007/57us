/**
 * Created by Foliage on 2016/7/5.
 */
$(function () {
    //ajax提交地址前缀
    var member = 'http://'+window.location.host;

    var event=arguments.callee.caller.arguments[0]||window.event;//按下回车键时消除浏览器差异
    //获取取用户名
    $("#user").on('input change',function () {
        var user = $(this).val();
        $(this).attr('value',user);
        $(".UserInput").removeClass('Erro');
        $(".UserErroTip").hide();
        $(".CodesInput").removeClass('Erro');
        $(".VerifyErroTip").hide();
        $(".DynamicCodes").removeClass('Erro');
        $(".SmsErroTip").hide();
    })
    //获取图形验证码
    $("#verify").on('input change keyup',function () {
        var verify = $(this).val();
        $(this).attr('value',verify);
        if (event.keyCode == "13") {
            $("#btnsms").click();
        }
        $(".CodesInput").removeClass('Erro');
        $(".VerifyErroTip").hide();
        $(".DynamicCodes").removeClass('Erro');
        $(".SmsErroTip").hide();
    })
    //获取短信验证码
    $("#sms").on('input change',function () {
        var sms = $(this).val();
        $(this).attr('value',sms);
        $(".DynamicCodes").removeClass('Erro');
        $(".SmsErroTip").hide();
    });

    $("#sms").on('keyup',function () {
        if (event.keyCode == "13") {
            $(".NextBtn").click();
        }
    });

    $("#code").click(function () {
        this.src='/code/pic.jpg?'+Math.random();
    })

    //获取短信验证码
    $("#btnsms").click(function () {
        var o = this;
        var User = $("#user").val();
        var Verify = $("#verify").val();
        if(rule.phone.test(User) != true && rule.Mail.test(User) != true){
            $(".UserInput").addClass('Erro');
            $(".UserErroTip").show();
            $('#code').trigger("click");
            $(".DynamicCodes").removeClass('Erro');
            $(".SmsErroTip").hide();
            return
        }else if(Verify == ""){
            $(".CodesInput").addClass('Erro');
            $(".VerifyErroTip").show();
            $('#code').trigger("click");
            $(".DynamicCodes").removeClass('Erro');
            $(".SmsErroTip").hide();
            return
        }
        $.ajax({
            //请求方式为get
            type:"post",
            //json文件位置
            url:member + '/userajax.html',
            data:{
                'Intention':'FindPwdVerifyCode',
                'User':User,
                'ImageCode':Verify,
            },
            //返回数据格式为json
            dataType: "json",
            error: function(){
                layer.msg("网络出错，请稍后再试！");
            },
            //请求成功完成后要执行的方法
            success:function(data){
                //200 成功 100=发送失败 异常 101=图形验证码错误 102=账号不存在
                if(data.ResultCode == "200"){
                    get_code_time(o);
                    $(".SmsErroTip").show();
                    $(".SmsErroTip").html();
                    $(".SmsErroTip").html('<i class="YesIco"></i>验证码发送成功');
                    //当输入图形验证码正确不刷新验证码  修改于2016.07.22
                    // $('#code').trigger("click");
                }else if(data.ResultCode == "100"){
                    layer.msg('请求异常，请稍后再试！');
                    $('#code').trigger("click");
                    return
                }else if(data.ResultCode == "101"){
                    $(".CodesInput").addClass('Erro');
                    $('#code').trigger("click");
                    $(".VerifyErroTip").show();
                    return
                }else if(data.ResultCode == "102"){
                    $(".UserInput").addClass('Erro');
                    $(".UserErroTip").show();
                    $(".UserErroTip").html();
                    $('#code').trigger("click");
                    $(".UserErroTip").html('<i></i>账号不存在');
                    return
                }
            }
        });
    })
    $(".NextBtn").click(function () {
        var User = $("#user").val();
        var Sms = $("#sms").val();
        if($(".UserErroTip").text() == "账号不存在"){
            $(".DynamicCodes").removeClass('Erro');
            $(".SmsErroTip").hide();
        }else if(rule.phone.test(User) != true && rule.Mail.test(User) != true){
            $(".UserInput").addClass('Erro');
            $(".UserErroTip").show();
            $('#code').trigger("click");
            $(".CodesInput").removeClass('Erro');
            $(".VerifyErroTip").hide();
            $(".DynamicCodes").removeClass('Erro');
            $(".SmsErroTip").hide();
            return
        }else if(Sms == ""){
            $(".DynamicCodes").addClass('Erro');
            $(".SmsErroTip").show();
            $('#code').trigger("click");
            $(".CodesInput").removeClass('Erro');
            $(".VerifyErroTip").hide();
            return
        }else if(!/^\d{6}$/i.test(Sms)){
            $(".DynamicCodes").addClass('Erro');
            $(".SmsErroTip").show();
            $(".SmsErroTip").html();
            $(".SmsErroTip").html('<i class="NoIco"></i>请输入6位纯数字验证码');
            $(".CodesInput").removeClass('Erro');
            $(".VerifyErroTip").hide();
            return
        }
        $.ajax({
            //请求方式为get
            type:"post",
            //json文件位置
            url:member + '/userajax.html',
            data:{
                'Intention':'FindPwdVerify',
                'User':User,
                'Code':Sms,
            },
            //返回数据格式为json
            dataType: "json",
            error: function(){
                layer.msg("网络出错，请稍后再试！");
            },
            //请求成功完成后要执行的方法
            success:function(data){
                //200=成功 102=短信验证码错误 103=短信验证码过期
                if(data.ResultCode == "200"){
                    $("#page2").show();
                    $("#page1").hide();
                }else if(data.ResultCode == "102"){
                    $(".DynamicCodes").addClass('Erro');
                    $(".SmsErroTip").show();
                    $(".SmsErroTip").html();
                    $(".SmsErroTip").html('<i class="NoIco"></i>验证码错误');
                    $(".CodesInput").removeClass('Erro');
                    $(".VerifyErroTip").hide();
                    $(".UserInput").removeClass('Erro');
                    $(".UserErroTip").hide();
                    return
                }else if(data.ResultCode == "103"){
                    $(".DynamicCodes").addClass('Erro');
                    $(".SmsErroTip").show();
                    $(".SmsErroTip").html();
                    $(".SmsErroTip").html('<i class="NoIco"></i>验证码过期');
                    $(".CodesInput").removeClass('Erro');
                    $(".VerifyErroTip").hide();
                    $(".UserInput").removeClass('Erro');
                    $(".UserErroTip").hide();
                    return
                }else if(data.ResultCode == "106"){
                    $(".UserInput").addClass('Erro');
                    $(".UserErroTip").show();
                    $(".CodesInput").removeClass('Erro');
                    $(".VerifyErroTip").hide();
                    $(".DynamicCodes").removeClass('Erro');
                    $(".SmsErroTip").hide();
                    $(".UserErroTip").html();
                    $(".UserErroTip").html('<i></i>账号不存在');
                    $('#code').trigger("click");
                    return
                }
            }
        });
    })

    //重置密码

    //获取第一次输入的密码
    $("#password").on('input change',function () {
        var password = $(this).val();
        $(this).attr('value',password);
        $(".PasswordInput").removeClass('Erro');
        $(".PasswordErroTip").hide();
    })
    //获取第二次输入的密码
    $("#rePassword").on('input change',function () {
        var rePassword = $(this).val();
        $(this).attr('value',rePassword);
        $(".RePasswordInput").removeClass('Erro');
        $(".RePasswordErroTip").hide();
    });

    $("#rePassword").on('keyup',function () {
        if (event.keyCode == "13") {
            $(".GetPassWordBtn").click();
        }
    });
    $(".GetPassWordBtn").click(function () {
        var User = $("#user").val();
        var Password = $("#password").val();
        var RePassword = $("#rePassword").val();
        if(!/^(?=.*?[a-zA-Z])(?=.*?[0-6])[!"#$%&'()*+,\-./:;<=>?@\[\\\]^_`{|}~A-Za-z0-9]{6,20}$/i.test(Password)){
            $(".PasswordInput").addClass('Erro');
            $(".PasswordErroTip").show();
            return
        }else if(Password !== RePassword){
            $(".RePasswordInput").addClass('Erro');
            $(".RePasswordErroTip").show();
            return
        }
        $.ajax({
            //请求方式为get
            type:"post",
            //json文件位置
            url:member + '/userajax.html',
            data:{
                'Intention':'ResetPass',
                'User':User,
                'PassWord':Password,
                'PassWordConfirm':RePassword,
            },
            //返回数据格式为json
            dataType: "json",
            error: function(){
                layer.msg("网络出错，请稍后再试！");
            },
            //请求成功完成后要执行的方法
            success:function(data){
                //200 成功 100=保存失败 101=账号错误 102=异常请求 103=密码格式错误 104=两次密码不一致
                if(data.ResultCode == "200"){
                    var MemberUrl = data.Url;
                    layer.msg("重置成功"),
                        setTimeout(function(){window.location=MemberUrl;},600);
                }else if(data.ResultCode == "100"){
                    layer.msg('保存失败,请重新提交！');
                    return
                }else if(data.ResultCode == "101"){
                    layer.msg('账号错误！');
                    return
                }else if(data.ResultCode == "102"){
                    layer.msg('请求异常，请稍后再试！');
                    return
                }else if(data.ResultCode == "103"){
                    $(".PasswordInput").addClass('Erro');
                    $(".PasswordErroTip").show();
                    return
                }else if(data.ResultCode == "104"){
                    $(".RePasswordInput").addClass('Erro');
                    $(".RePasswordErroTip").show()
                    return
                }
            }
        });
    })
});