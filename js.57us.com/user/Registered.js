/**
 * Created by Foliage on 2016/7/5.
 */
$(function () {

    //鼠标离开验证
    blurVerify();

    $(".click_code").on('click',function () {
        this.src='/code/pic.jpg?'+Math.random();
    })

    //获取短信验证码
    $("#btnsms").click(function () {
        var o = this;
        //ajax提交参数
        var ajaxData = {
            'Intention':'SendVerifyCode',
            'User':$("#user").val(),
            'ImageCode':$("#code").val(),
        }

        //执行验证
        if(ajaxData.User == '') {
            errorHint('m_user', '请输入手机号码或邮箱');
            return
        } else if(rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true) {
            errorHint('m_user', '手机号码或邮箱格式不正确');
            return
        } else if(ajaxData.ImageCode == '') {
            errorHint('m_verify_img', '图形验证码不为空');
            return
        }

        //ajax提交
        $.ajax({
            type:"post",
            url:'/userajax.html',
            data:ajaxData,
            dataType: "json",
            success:function(data){
                //200=成功 100=异常 101=图形验证码错误 102=账号已存在 103=账号错误
                if(data.ResultCode == "200"){
                    get_code_time(o);
                    var _thisDom = $("#m_code").parents('td');
                    _thisDom.find('.Loinput').addClass('Erro');
                    _thisDom.find('.ErroTip').show();
                    _thisDom.find('.ErroTip').html('<i class="YesIco"></i>验证码发送成功');
                }else if(data.ResultCode == "100"){
                    layer.msg('请求异常，请稍后再试！');
                    $('.click_code').trigger("click");
                }else if(data.ResultCode == "101"){
                    errorHint('m_verify_img', '图形验证码不正确');
                    $('.click_code').trigger("click");
                }else if(data.ResultCode == "102"){
                    errorHint('user', '该帐号已注册，请更换，或<a href="/user/login/" class="LoginBlueBtn">立即登录</a>');
                    $('.click_code').trigger("click");
                }else if(data.ResultCode == "103"){
                    errorHint('user', '请输入正确的手机号码或者邮箱');
                    $('.click_code').trigger("click");
                }
            }
        });
    })

    //执行注册事件
    $(".NextBtn").click(function () {

        //ajax提交参数
        var ajaxData = {
            'Intention':'RegisterVerify',
            'User':$("#user").val(),
            'Code':$("#m_code").val(),
        }

        //执行验证
        if(ajaxData.User == '') {
            errorHint('user', '请输入手机号码');
            return
        } else if(rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true) {
            errorHint('user', '手机号码或邮箱格式不正确');
            return
        } else if(ajaxData.Code == '') {
            errorHint('m_code', '验证码不为空');
            return
        } else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            errorHint('m_code', '请输入6位纯数字验证码');
            return
        }

        //ajax提交
        $.ajax({
            type:"post",
            url:'/userajax.html',
            data:ajaxData,
            dataType: "json",
            success:function(data){
                //200=成功 102=短信验证码错误 103=短信验证码过期 100=异常
                if(data.ResultCode == "200"){
                    $("#page2").show();
                    $("#page1").hide();
                }else if(data.ResultCode == "100"){
                    layer.msg('请求异常，请稍后再试！');
                }else if(data.ResultCode == "102"){
                    var _thisDom = $("#m_code").parents('td');
                    _thisDom.find('.Loinput').addClass('Erro');
                    _thisDom.find('.ErroTip').show();
                    _thisDom.find('.ErroTip').html('<i class="NoIco"></i>验证码错误');
                    $('#code').trigger("click");
                }else if(data.ResultCode == "103"){
                    var _thisDom = $("#m_code").parents('td');
                    _thisDom.find('.Loinput').addClass('Erro');
                    _thisDom.find('.ErroTip').show();
                    _thisDom.find('.ErroTip').html('<i class="NoIco"></i>验证码过期');
                    $('#code').trigger("click");
                    return
                }
            }
        });
    })

    //设置用户密码
    $("#RegistBtn").click(function () {
        //ajax提交参数
        var ajaxData = {
            'Intention':'RegisterUser',
            'User':$("#user").val(),
            'PassWord':$("#password").val(),
            'PassWordConfirm':$("#rePassword").val(),
        }

        //执行验证
        if(ajaxData.PassWord == '') {
            errorHint('password', '密码不能为空');
            return
        } else if(!/^(?=.*?[a-zA-Z])(?=.*?[0-6])[!"#$%&'()*+,\-./:;<=>?@\[\\\]^_`{|}~A-Za-z0-9]{6,20}$/i.test(ajaxData.PassWord)) {
            errorHint('password', '6-20位数字、字母和符号');
            return
        }else if(ajaxData.PassWord != ajaxData.PassWordConfirm){
            errorHint('rePassword', '两密码不一致');
            return
        }

        //ajax提交
        $.ajax({
            type:"post",
            url:member + '/userajax.html',
            data:ajaxData,
            dataType: "json",
            success:function(data){
                //200 成功 100=保存失败 101=密码格式错误 103=账号错误 102=异常请求 104=两次密码不一致
                if(data.ResultCode == "200"){
                    layer.msg("注册成功！57美国网欢迎您！");
                    setTimeout(function(){
                        window.location=data.Url;
                    },5000);
                }else if(data.ResultCode == "100"){
                    layer.msg('保存失败,请重新提交！');
                }else if(data.ResultCode == "101"){
                    errorHint('password', '密码格式不正确');
                }else if(data.ResultCode == "103"){
                    layer.msg('异常的请求,请重新注册！');
                }else if(data.ResultCode == "102"){
                    layer.msg('请求异常，请稍后再试！');
                    setTimeout(function(){
                        window.location=data.Url;
                    },1000);
                }else if(data.ResultCode == "104"){
                    errorHint('rePassword', '两密码不一致');
                }
            }
        });
    })
});

//错误提示方法
function errorHint(id, message) {
    var _thisDom = $("#" + id + "").parents('td');
    _thisDom.find('.Loinput').addClass('Erro');
    _thisDom.find('.ErroTip').show();
    _thisDom.find('.ErroTip').html('<i></i>' + message);
}

//鼠标离开验证方法
function blurVerify() {

    //焦点移入，移除错误提示
    $('input').mouseup(function() {
        $(this).parent().removeClass('Erro');
        $(this).parents('td').find('.ErroTip').hide();
    })

    //手机号码验证
    $("#user").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '请输入手机号码或邮箱');
        } else if(rule.phone.test(_this) != true && rule.Mail.test(_this) != true) {
            errorHint(this.id, '手机号码或邮箱格式不正确');
        } else {
            $(this).parent().removeClass('Erro');
            $(this).parent().next().hide();
        }
    })

    //图形验证码验证
    $("#code").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '验证码不能为空');
        } else {
            $(this).parent().removeClass('Erro');
            $(this).parent().next().next().hide();
        }
    })

    //手机登录短信验证码
    $("#m_code").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '证码不为空');
        } else if(!/^\d{6}$/i.test(_this)) {
            errorHint('m_code', '请输入6位纯数字验证码');
        } else {
            $(this).parent().removeClass('Erro');
            $(this).parents('td').find('.ErroTip').hide();
        }
    })

    //设置密码
    $("#password").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '密码不能为空');
        }else if(!/^(?=.*?[a-zA-Z])(?=.*?[0-6])[!"#$%&'()*+,\-./:;<=>?@\[\\\]^_`{|}~A-Za-z0-9]{6,20}$/i.test(_this)) {
            errorHint(this.id, '6-20位数字、字母和符号');
        }else {
            $(this).parent().removeClass('Erro');
            $(this).parents('td').find('.ErroTip').hide();
        }
    })
}