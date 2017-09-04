/**
 * Created by Foliage on 2016/11/14.
 */
$(function () {
    //当用户名，短信验证码不为空时登录按钮添加active
    $("#user,#SmsCode").on('input',function () {
        if($("#user").val() && $("#SmsCode").val()){
            $("#LoginBtn").addClass('active');
        }else {
            $("#LoginBtn").removeClass('active');
        }
    })

    //当移入手机号码框，去掉边框
    $(".loginMain input").focus(function () {
        $(this).parent().parent().attr('style','');
    })

    //获取短信验证码
    $("#btnsms").on('click',function () {
        var o = this;
        ajaxData = {
            'Intention':'FindPwdVerifyCode',
            'User':$("#user").val(),
            'ImageCode':$("#verify").val(),
        }
        if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.User)){
            $.toast('请输入正确的手机号码');
            $(".loginuser").css('border','1px solid red');
            return
        }else if(ajaxData.ImageCode == ''){
            $.toast('请输入正确的四位图形验证码');
            $(".loginverify").css('border','1px solid red');
            return
        }
        $.post("/ajax/",ajaxData,function(data){
            //200=成功 其它都是=异常
            if(data.ResultCode == "200"){
                get_code_time(o);
                $.toast(data.Message);
                $("#btnsms").attr('data-type','1');
            }else{
                $.toast(data.Message);
                $(".content #code").trigger('click');
            }
        },'json');
    })

    //点击下一步
    $("#LoginBtn").on('click',function () {
        ajaxData = {
            'Intention':'FindPwdVerify',
            'User':$("#user").val(),
            'Code':$("#SmsCode").val(),
        }

        if($("#btnsms").attr('data-type') == '0'){
            $.toast('请先获取短信验证码');
            return
        }else if((!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.User))){
            $.toast('请输入正确的手机号码');
            $(".loginuser").css('border','1px solid red');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)){
            $.toast('请输入6位纯数字短信验证码');
            $(".logincode").css('border','1px solid red');
            return
        }
        $.post("/ajax/",ajaxData,function(data){
            //200=成功 其它都是=异常
            if(data.ResultCode == "200"){
                var Url = data.Url; // 返回这个url  /member/getsetpassword/
                window.location=Url;
            }else{
                $.toast(data.Message);
                $(".content #code").trigger('click');
            }
        },'json');
    })
})