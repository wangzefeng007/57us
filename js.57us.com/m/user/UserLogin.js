/**
 * Created by Foliage on 2016/11/12.
 */
$(function () {
    //当用户名，手机号码不为空时登录按钮添加active
    $("#user").on('input',function () {
        if($("#user").val()){
            $(".LoginBtn").addClass('active');
        }
        $(".LoginBtn").find('a').attr('href','javascript:void(0)');
    })

    $(".loginMain input").focus(function () {
        $(this).parent().parent().attr('style','');
    })

    $(".LoginBtn").on('click',function () {
        ajaxData = {
            'Intention':'MpLogin',
            'User':$("#user").val(),
        }
        if((!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.User))){
            $.toast('请输入正确的手机号码');
            $(".loginuser").css('border','1px solid red');
            return
        }else {
        }

        $.post("/ajax/",ajaxData,function(data){
            //200=成功 100=异常 101=图形验证码错误
            if(data.ResultCode == "200"){
                $(this).find('a').attr('href','#verify');
                var Url = data.Url;
                $.toast("输入正确");
                setTimeout(function(){window.location=Url;},600);
            }else{
                $.toast("账号未注册");
            }
        },'json');

        var tel = _user;
        var reg = /^(\d{3})\d{4}(\d{4})$/;
        tel = tel.replace(reg, "$1****$2");
        $(".tel").text(tel);
    })
})

