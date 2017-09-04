/**
 * Created by Foliage on 2016/11/23.
 */

//定义变量手机号码
var Tel = GetQueryString('Tel');
//定义变量，发送验证按钮
var o = document.getElementById("btnsms");

$(function () {

    //根据情况添加下一步class
    $("#Code").on('input',function () {
        if($("#Code").val()){
            $("#LoginBtn").addClass('active');
        }else {
            $("#LoginBtn").removeClass('active');
        }
    })

    //如果上个页面带过来的手机号码存在执行获取验证码操作
    if(Tel != null){
        var tel = Tel;
        var reg = /^(\d{3})\d{4}(\d{4})$/;
        tel = tel.replace(reg, "$1****$2");
        $(".tel").text(tel);
        RegisterVerify();
    }

    //重新获取手机验证码
    $("#btnsms").on('click',function () {
        RegisterVerify();
    });

    //提交短信验证码
    $("#LoginBtn").on('click',function () {
        ajaxData = {
            'Intention':'RegisterVerify',
            'User':Tel,
            'Code':$("#Code").val(),
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
                var Url = data.Url; // 返回这个url  /member/registersetpassword/
                window.location=Url;
            }else {
                $.toast(data.Message);
            }
        },'json');
    })

    $.init();
})

//获取验证码方法
function RegisterVerify() {
    ajaxData = {
        'Intention':'RegisterSend',
        'User':Tel,
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
}
