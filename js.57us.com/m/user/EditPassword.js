/**
 * Created by Foliage on 2016/11/23.
 */
$(function () {
    //当用户名，手机号码不为空时登录按钮添加active
    $("#oldpass,#pass,#pass2").on('input',function () {
        if($("#oldpass").val() != '' && $("#pass").val() != '' && $("#pass2").val() != ''){
            $(".LoginBtn").addClass('active');
        }else {
            $(".LoginBtn").removeClass('active');
        }
    })

    //设置密码，点击提交
    $(".LoginBtn").on('click',function () {
        ajaxData = {
            'Intention':'MpLoginSetPass', //设置密码方法名
            'OldPassWord':$("#oldpass").val(),//旧的密码
            'PassWord':$("#pass").val(), //第一次输入的密码
            'PassWord2':$("#pass2").val(), //第二次输入的密码
        }
        if(ajaxData.OldPassWord == ''){
            $.toast('当前密码不能为空');
            return
        }else if(!/^(?=.*?[a-zA-Z])(?=.*?[0-6])[!"#$%&'()*+,\-./:;<=>?@\[\\\]^_`{|}~A-Za-z0-9]{6,20}$/i.test(ajaxData.PassWord)){
            $.toast('请输入6-20位数字、字母或者符号组合');
            return
        }else if(ajaxData.PassWord != ajaxData.PassWord2){
            $.toast('两次密码不一致');
            return
        }
        $.post("/ajax/",ajaxData,function(data){
            //200=密码设置成功，返回个人中心链接并登录  其它都是=异常
            if(data.ResultCode == "200"){
                $.toast(data.Message);
                var Url = data.Url;
                setTimeout(function(){window.location=Url;},600);
            }else {
                $.toast(data.Message);
            }
        },'json');
    })
})