/**
 * Created by Foliage on 2017/2/15.
 */
$(function () {
    //切换登录方式
    $(".loginBox .hd a").click(function(){
        $(this).addClass("on").siblings().removeClass("on");
        var num =$(this).index();
        $(this).parents(".loginBox").find(".loginCont").hide().eq(num).show();
    })
    //两周内的自动登录表单美化
    $('.cbt').inputbox();

    //验证码切换
    $(".click_code").on('click',function() {
        this.src = '/code/pic.jpg?' + Math.random();
    })

    //焦点移入，移除错误提示
    $("input[type='text'],input[type='password']").mouseup(function() {
        $(this).parents('.loginCont').find('.erroTip').fadeOut(200);
        $(this).parents('.inputBox').removeClass('erro');
    })

    $("#userlogin").submit(function() {
        $("#index1 .loginBtn").trigger('click');
        return false;
    })

    //普通方式登录
    $("#index1 .loginBtn").on('click',function () {
        //定义是否自动登录变量
        if($("#autoLogin").is('.checked')){
            var _autoLogin = '1';
        }else {
            var _autoLogin = '0';
        }

        //ajax提交参数
        var ajaxData = {
            'Intention': 'Login', //方法
            'User': $("#index1 .user").val(), //账号
            'Pass': $("#index1 .password").val(), //密码
            'ImageCode': $("#index1 .imgCode").val(), //图形验证码
            'AutoLogin': _autoLogin, //是否自动登录 0代表不是 1代表是
            'ComeFrom': $(".ComeFrom").val(), //来自哪一模块（资讯，留学，旅游）
        }

        //执行验证
        if(ajaxData.User == ''){
            errorHint('#index1 .user','账号不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true) {
            errorHint('#index1 .user', '手机号码或邮箱格式不正确');
            return
        }else if(ajaxData.Pass == '') {
            errorHint('#index1 .password', '密码不能为空');
            return
        }
        if($("#index1 .yzmBox").is(':visible')){
            if(ajaxData.ImageCode == ''){
                errorHint('#index1 .imgCode','图形验证码不能为空');
                return
            }
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: "/loginajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                //200=成功 100=用户名密码错误 101=验证码错误 102=异常请求 105=错误超过3次要验证码 106=账号未注册
                if(data.ResultCode == "200") {
                    layer.msg("登录成功");
                    setTimeout(function() {
                        window.location = data.Url;
                    }, 600);
                } else if(data.ResultCode == "100") {
                    errorHint('#index1 .password', '用户名密码错误');
                } else if(data.ResultCode == "101") {
                    errorHint('#index1 .imgCode', '图形验证码错误');
                } else if(data.ResultCode == "102") {
                    layer.msg('请求异常，请稍后再试！');
                } else if(data.ResultCode == "105") {
                    $("#index1 #yzmBox").show();
                } else if(data.ResultCode == "106") {
                    errorHint('#index1 .user', '未注册的手机号码或邮箱');
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    //获取短信验证码
    $("#index2 #smsBtn").on('click',function () {
        //定义当前操作的对象，验证码倒计时使用
        var o = this;
        //ajax提示参数
        var ajaxData = {
            'Intention': 'MpLogin', //方法
            'User': $("#index2 .user").val(),
            'ImageCode': $("#index2 .imgCode").val(),
        }

        //执行验证
        if(ajaxData.User == ''){
            errorHint('#index2 .user','手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true) {
            errorHint('#index2 .user', '手机号码格式不正确');
            return
        }else if(ajaxData.ImageCode == '') {
            errorHint('#index2 .imgCode', '图形验证码不为空');
            return
        }

        //执行ajax提交操作
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/loginajax.html", ///loginajax.html
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                //200=成功 100=异常 101=图形验证码错误
                if(data.ResultCode == "200") {
                    get_code_time(o);
                    layer.msg('验证码发送成功');
                    $("#index2 #smsBtn").addClass('on');
                    setTimeout(function(){
                        $("#index2 #smsBtn").removeClass('on');
                    },60000);
                } else if(data.ResultCode == "100") {
                    layer.msg('请求异常，请稍后再试！');
                } else if(data.ResultCode == "101") {
                    errorHint('#index2 .imgCode', '图形验证码不正确');
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    $("#mobileLogin").submit(function() {
        $("#index2 .loginBtn").trigger('click');
        return false;
    })

    //手机登录方式
    $("#index2 .loginBtn").on('click',function () {
        var ajaxData = {
            'Intention': 'MpLoginVerify', //方法
            'User': $("#index2 .user").val(), //账号
            'Code': $("#index2 .code").val(), //短信验证码
        }

        if(ajaxData.User == ''){
            errorHint('#index2 .user','账号不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true) {
            errorHint('#index2 .user', '手机号码格式不正确');
            return
        }else if(ajaxData.Code == ''){
            errorHint('#index2 .code','短信验证码不为空')
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            errorHint('#index2 .code', '请输入6位纯数字短信验证码');
            return
        }

        //执行ajax提交操作
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/loginajax.html", ///loginajax.html
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                //200=成功 102=短信验证码错误 103=短信验证码过期
                if(data.ResultCode == "200") {
                    layer.msg("登录成功");
                    setTimeout(function() {
                        window.location = data.Url;
                    }, 600);
                } else if(data.ResultCode == "102") {
                    errorHint('#index2 .code', '短信验证码错误');
                } else if(data.ResultCode == "103") {
                    errorHint('#index2 .code', '短信验证码过期');
                    $('#index2 .click_code').trigger("click");
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })
})

/**
 * 错误提示方法
 *
 * @param id 操作对应的dom
 * @param message 错误操作文字
 */
function errorHint(id, message) {
    var $thisDom = $(""+id+"");
    $thisDom.parents('.loginCont').find('.erroTip').fadeIn(500);
    $thisDom.parents('.loginCont').find('.erroIfno').text(message);
    $thisDom.parents('.inputBox').addClass('erro');
    setTimeout(function(){
        $thisDom.parents('.loginCont').find('.erroTip').fadeOut(1000);
        setTimeout(function(){
            $thisDom.parents('.inputBox').removeClass('erro');
        },400);
    },5000);
}