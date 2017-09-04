/**
 * Created by Foliage on 2016/11/12.
 */
$(function () {
    //当用户名，手机号码不为空时登录按钮添加active
    $("#user").on('input',function () {
        if($("#user").val()){
            $("#LoginBtn").addClass('active');
        }else {
            $("#LoginBtn").removeClass('active');
        }
        $("#LoginBtn").find('a').attr('href','javascript:void(0)');
    })
    
    $(".study").val(GetQueryString('Type'));

    $("#Code").on('input',function () {
        if($("#Code").val()){
            $("#LoginBtn2").addClass('active');
        }else {
            $("#LoginBtn2").removeClass('active');
        }
    })

    //当移入手机号码框，去掉边框
    $(".loginMain input").focus(function () {
        $(this).parent().parent().attr('style','');
    })

    //获取短信验证码
    $("#LoginBtn").on('click',function () {
        var o = document.getElementById("btnsms");
        ajaxData = {
            'Intention':'MpLogin',
            'User':$("#user").val(),
        }
        if((!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.User))){
            $.toast('请输入正确的手机号码');
            $(".loginuser").css('border','1px solid red');
            return
        }else {
            $('#LoginBtn').find('a').attr('href','#verify');
        }
        $.post("/ajax/",ajaxData,function(data){
            //200=成功 其它都是=异常
            if(data.ResultCode == "200"){
                get_code_time(o);
                $.toast(data.Message);
                var tel = ajaxData.User;
                var reg = /^(\d{3})\d{4}(\d{4})$/;
                tel = tel.replace(reg, "$1****$2");
                $(".tel").text(tel);
            }else{
                $.toast(data.Message);
            }
        },'json');

    })

    //重新发送验证码事件
    $("#btnsms").on('click',function () {
        var o = this;
        ajaxData = {
            'Intention':'MpLogin',
            'User':$("#user").val(),
        }
        $.post("/ajax/",ajaxData,function(data){
            //200=成功 其它都是=异常
            if(data.ResultCode == "200"){
                get_code_time(o);
                $.toast(data.Message);
            }else{
                $.toast(data.Message);
            }
        },'json');
    });

    //点击登录事件
    $("#LoginBtn2").on('click',function () {
        ajaxData = {
            'Intention':'MpLoginVerify',
            'User':$("#user").val(),
            'Code':$("#Code").val(),
            'Type':$(".study").val(),
        }
        if(ajaxData.Code == ""){
            $.toast('验证码不能为空');
            $(".longincode").css('border','1px solid red');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)){
            $.toast('请输入6位纯数字短信验证码');
            $(".longincode").css('border','1px solid red');
            return
        }
        $.post("/ajax/",ajaxData,function(data){
            //200=成功 其它都是=异常
            if(data.ResultCode == "200"){
                var Url = data.Url;
                $.toast("登录成功");
                setTimeout(function(){window.location=Url;},600);
            }else {
                $.toast(data.Message);
            }
        },'json');
    })
})
