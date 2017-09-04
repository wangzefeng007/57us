/**
 * Created by Foliage on 2017/2/15.
 */
$(function () {
    //验证码切换
    $(".click_code").on('click',function() {
        this.src = '/code/pic.jpg?' + Math.random();
    })

    //焦点移入，移除错误提示
    $("input[type='text'],input[type='password']").mouseup(function() {
        $(this).parents('.loginCont').find('.erroTip').fadeOut(200);
        $(this).parents('.inputBox').removeClass('erro');
    })

    //判断输入的用户名是手机还是邮箱，修改验证码提示
    $("#index1 .user").blur(function () {
        var $thisVal = $(this).val();
        if(rule.phone.test($thisVal) == true){
            $("#index1 .code").attr('placeholder','请输入短信验证码');
            _code = '短信';
        }else if(rule.Mail.test($thisVal) == true){
            $("#index1 .code").attr('placeholder','请输入邮箱验证码')
            _code = '邮箱';
        }else {
            $("#index1 .code").attr('placeholder','请输入验证码')
            _code = '';
        }
    })

    //获取验证码
    $("#index1 #codeBtn").on('click',function () {
        //定义当前操作的对象，验证码倒计时使用
        var o = this;

        var ajaxData = {
            'Intention':'RetrievePasswordSendCode',
            'User':$("#index1 .user").val(),
            'ImageCode':$("#index1 .imgCode").val(),
        }

        if(ajaxData.User == ''){
            errorHint('#index1 .user','账号不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true) {
            errorHint('#index1 .user', '手机号码或邮箱格式不正确');
            return
        }else if(ajaxData.ImageCode == ''){
            errorHint('#index1 .imgCode','图形验证码不能为空');
            return
        }

        //执行ajax提交操作
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/loginajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                //200=成功 100=异常 101=图形验证码错误
                if(data.ResultCode == "200") {
                    get_code_time(o);
                    layer.msg('验证码发送成功');
                    $("#index1 #codeBtn").addClass('on');
                    setTimeout(function(){
                        $("#index1 #codeBtn").removeClass('on');
                    },60000);
                } else if(data.ResultCode == "100") {
                    layer.msg('请求异常，请稍后再试！');
                } else if(data.ResultCode == "101") {
                    errorHint('#index1 .imgCode', '图形验证码不正确');
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    //找回密码事件
    $("#index1 .nextBtn").on('click',function () {
        var ajaxData = {
            'Intention': 'RegisterVerifyCode', //方法
            'User': $("#index1 .user").val(), //账号
            'Code': $("#index1 .code").val(), //验证码
        }

        if(ajaxData.User == ''){
            errorHint('#index1 .user','账号不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true) {
            errorHint('#index1 .user', '手机号码或邮箱格式不正确');
            return
        }else if(ajaxData.Code == ''){
            errorHint('#index1 .code',''+_code+'验证码不为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            errorHint('#index1 .code', '请输入6位纯数字'+_code+'验证码');
            return
        }

        //执行ajax提交操作
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/loginajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                //200=成功 102=短信验证码错误 103=短信验证码过期 100=异常
                if(data.ResultCode == "200"){
                    $("#index1").hide();
                    $("#index2").show();
                }else if(data.ResultCode == "100"){
                    layer.msg('请求异常，请稍后再试！');
                }else if(data.ResultCode == "102"){
                    errorHint('#index1 .code', ''+_code+'验证码错误');
                }else if(data.ResultCode == "103"){
                    errorHint('#index1 .code', ''+_code+'验证码过期');
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    //设置新的密码
    $("#index2 .regitBtn").on('click',function () {
        //ajax提交参数
        var ajaxData = {
            'Intention':'RetrievePassword',
            'PassWord':$("#index2 .password").val(),
            'PassWordConfirm':$("#index2 .rePassword").val(),
        }

        //执行验证
        if(ajaxData.PassWord == '') {
            errorHint('#index2 .password', '密码不能为空');
            return
        } else if(rule.PassWord.test(ajaxData.PassWord) != true) {
            errorHint('#index2 .password', '6-20位数字或字母');
            return
        }else if(ajaxData.PassWordConfirm == ''){
            errorHint('#index2 .rePassword', '请再次输入密码');
            return
        }else if(ajaxData.PassWord != ajaxData.PassWordConfirm){
            errorHint('#index2 .rePassword', '两密码不一致');
            return
        }

        //执行ajax提交操作
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/loginajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                //200 成功 100=保存失败 101=密码格式错误 103=账号错误 102=异常请求 104=两次密码不一致
                if(data.ResultCode == "200"){
                    layer.msg("注册成功！57美国网欢迎您！");
                    setTimeout(function(){
                        window.location=data.Url;
                    },2000);
                }else if(data.ResultCode == "100"){
                    layer.msg('保存失败,请重新提交！');
                }else if(data.ResultCode == "101"){
                    errorHint('#index2 .password', '密码格式不正确');
                }else if(data.ResultCode == "104"){
                    errorHint('#index2 .rePassword', '两密码不一致');
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

var _code;

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