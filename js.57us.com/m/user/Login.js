/**
 * Created by Foliage on 2016/11/12.
 */
$(function () {
    //当用户名，手机号码不为空时登录按钮添加active
    $("#user,#pass").on('input',function () {
        if($("#user").val() != '' && $("#pass").val() != ''){
            $(".LoginBtn").addClass('active');
        }else {
            $(".LoginBtn").removeClass('active');
        }
    })

    //鼠标移入后移除边框样式
    $(".loginMain input").focus(function () {
        $(this).parent().parent().attr('style','');
    })

    //点击登录事件
    $(".LoginBtn").on('click',function () {
        ajaxData = {
            'Intention':'Login', //方法
            'User':$("#user").val(), //账号
            'Pass':$("#pass").val(), //密码
            'ImageCode':$("#verify").val(), //图形验证码
            'Type':$(".study").val(),
        };
        if((!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.User)) && (!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(ajaxData.User))){
            $(".loginuser").css('border','1px solid red');
            $.toast('请输入正确的手机号码');
            return
        }else if(ajaxData.Pass == ''){
            $(".loginpass").css('border','1px solid red');
            $.toast('请输入密码');
            return
        }

        $.post("/ajax/",ajaxData,function(data){
            //200=成功 100=用户名密码错误 101=验证码错误 102=异常请求 105=错误超过3次要验证码 106=账号未注册
            if(data.ResultCode == "200"){
                var Url = data.Url;
                $.toast("登录成功");
                setTimeout(function(){window.location=Url;},600);
            }else if(data.ResultCode == '100'){
                $.toast("用户名或密码错误");
            }else if(data.ResultCode == '101'){
                $.toast("验证码错误");
            }else if(data.ResultCode == '105'){
                $(".piccode").show();
                $('#code').trigger("click");
            }else if(data.ResultCode == '106'){
                $.toast("账号未注册");
            }
        },'json');
    })
})