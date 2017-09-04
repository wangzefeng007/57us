/**
 * Created by Foliage on 2016/7/4.
 */
$(function() {
    //选框
    $('.cbt').inputbox();

    //登录切换
    $(".LoginBox .hd a").click(function() {
        var num = $(this).index();
        $(this).addClass("on").siblings().removeClass("on");
        $(this).parent().siblings(".bd").find("table").addClass("hidden").eq(num).removeClass("hidden");
    })

    //鼠标离开验证
    blurverify();

    //验证码切换
    $(".click_code").click(function() {
        this.src = '/code/pic.jpg?' + Math.random();
    })

    //普通登录取是否自动登录状态
    $("#auto").on('click', function() {
        var autologin = $('#autologin input').attr('checked');
        if(autologin == undefined) {
            $("#AutoLogin").val('1');
        } else {
            $("#AutoLogin").val('0');
        }
    })

    //普通登录方式submit提交方法
    $("#userlogin").submit(function() {
        $("#LoginBtn").trigger('click');
        return false;
    })

    //普通登录事件
    $("#LoginBtn").on('click', function() {
        //执行验证码更换
        $('#codeb').trigger("click");

        //ajax提交参数
        var ajaxData = {
            'Intention': 'Login',
            'User': $("#user").val(),
            'Pass': $("#password").val(),
            'Type': $(".study").val(),
            'ImageCode': $("#code").val(),
            'AutoLogin': $("#AutoLogin").val(),
        }

        //验证登录信息
        if(ajaxData.User == '') {
            errorHint('user', '请输入手机号码或邮箱');
            return
        } else if(rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true) {
            errorHint('user', '手机号码或邮箱格式不正确');
            return
        } else if(ajaxData.Pass == '') {
            errorHint('password', '密码不能为空');
            return
        }

        //验证图形验证码
        if($("#VerifyTh").is(':visible')) {
            if($("#code").val() == '') {
                errorHint('code', '验证码不能为空');
                return
            }
        }

        //执行ajax提交
        $.ajax({
            type: "post",
            url: '/userajax.html',
            data: ajaxData,
            dataType: "json",
            success: function(data) {
                //200=成功 100=用户名密码错误 101=验证码错误 102=异常请求 105=错误超过3次要验证码 106=账号未注册
                if(data.ResultCode == "200") {
                    layer.msg("登录成功");
                    setTimeout(function() {
                        window.location = data.Url;
                    }, 600);
                } else if(data.ResultCode == "100") {
                    errorHint('password', '用户名密码错误');
                } else if(data.ResultCode == "101") {
                    errorHint('code', '验证码错误');
                } else if(data.ResultCode == "102") {
                    layer.msg('请求异常，请稍后再试！');
                } else if(data.ResultCode == "105") {
                    $("#VerifyTh").show();
                } else if(data.ResultCode == "106") {
                    errorHint('user', '未注册的手机号码或邮箱&nbsp;<a href="member/register/" class="LoginBlueBtn">免费注册</a>');
                }
            }
        });
    })

    //获取短信验证码
    $("#btnsms").on('click', function() {
        var o = this;
        var ajaxData = {
            'Intention': 'MpLogin',
            'User': $("#m_user").val(),
            'ImageCode': $("#m_verify_img").val(),
        }

        //执行验证
        if(ajaxData.User == '') {
            errorHint('m_user', '请输入手机号码');
            return
        } else if(rule.phone.test(ajaxData.User) != true) {
            errorHint('m_user', '手机号码格式不正确');
            return
        } else if(ajaxData.ImageCode == '') {
            errorHint('m_verify_img', '图形验证码不为空');
            return
        }

        //执行ajax提交
        $.ajax({
            type: "post",
            url: '/userajax.html',
            data: ajaxData,
            dataType: "json",
            success: function(data) {
                //200=成功 100=异常 101=图形验证码错误
                if(data.ResultCode == "200") {
                    get_code_time(o);
                    var _thisDom = $("#m_verify_img").parents('td');
                    _thisDom.find('.Loinput').addClass('Erro');
                    _thisDom.find('.ErroTip').show();
                    _thisDom.find('.ErroTip').html('<i class="YesIco"></i>验证码发送成功');
                } else if(data.ResultCode == "100") {
                    layer.msg('请求异常，请稍后再试！');
                } else if(data.ResultCode == "101") {
                    errorHint('m_verify_img', '图形验证码不正确');
                }
            }
        });
    })

    $("#mlogin").submit(function() {
        $("#PhoneLogin").trigger('click');
        return false;
    })

    //手机登录事件
    $("#PhoneLogin").on('click', function() {
        var ajaxData = {
            'Intention': 'MpLoginVerify',
            'User': $("#m_user").val(),
            'Code': $("#m_code").val(),
            'Type': $(".study").val(),
        }

        //执行验证
        if(ajaxData.User == '') {
            errorHint('m_user', '请输入手机号码');
            return
        } else if(rule.phone.test(ajaxData.User) != true) {
            errorHint('m_user', '手机号码格式不正确');
            return
        } else if(ajaxData.Code == '') {
            errorHint('m_verify_img', '短信验证码不为空');
            return
        } else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            errorHint('m_code', '请输入6位纯数字短信验证码');
            return
        }

        $.ajax({
            type: "post",
            url: member + '/userajax.html',
            data: ajaxData,
            dataType: "json",
            success: function(data) {
                //200=成功 102=短信验证码错误 103=短信验证码过期
                if(data.ResultCode == "200") {
                    layer.msg("登录成功");
                    setTimeout(function() {
                        window.location = data.Url;
                    }, 600);
                } else if(data.ResultCode == "102") {
                    errorHint('m_code', '短信验证码错误');
                    $('#code').trigger("click");
                } else if(data.ResultCode == "103") {
                    errorHint('m_code', '短信验证码过期');
                    $('#code').trigger("click");
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
function blurverify() {

    //焦点移入，移除错误提示
    $('input').mouseup(function() {
        $(this).parent().removeClass('Erro');
        $(this).parents('tr').find('.ErroTip').hide();
    })

    //普通登录手机号码
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

    //普通登录密码验证
    $("#password").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '密码不能为空');
        } else {
            $(this).parent().removeClass('Erro');
            $(this).parent().next().hide();
        }
    })

    //普通登录图形验证码验证
    $("#code").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '验证码不能为空');
        } else {
            $(this).parent().removeClass('Erro');
            $(this).parent().next().next().hide();
        }
    })

    //手机登录手机号码验证
    $("#m_user").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '请输入手机号码');
        } else if(rule.phone.test(_this) != true) {
            errorHint(this.id, '手机号码格式不正确');
        } else {
            $(this).parent().removeClass('Erro');
            $(this).parent().next().hide();
        }
    })

    //手机登录图形验证码
    $("#m_verify_img").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '图形验证码不能为空');
        } else {
            $(this).parent().removeClass('Erro');
            $(this).parent().next().next().hide();
        }
    })

    //手机登录短信验证码
    $("#m_code").blur(function() {
        var _this = $(this).val();
        if(_this == '') {
            errorHint(this.id, '短信验证码不为空');
        } else if(!/^\d{6}$/i.test(_this)) {
            errorHint('m_code', '请输入6位纯数字短信验证码');
        } else {
            $(this).parent().removeClass('Erro');
            $(this).parent().next().next().hide();
        }
    })
}