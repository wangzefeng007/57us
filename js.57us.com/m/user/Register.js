/**
 * Created by Foliage on 2016/11/12.
 */
$(function () {
    //当用户名，手机号码不为空时登录按钮添加active
    $("#user").on('input',function () {
        if($("#user").val() != ''){
            $(".LoginBtn").addClass('active');
        }else {
            $(".LoginBtn").removeClass('active');
        }
    })

    //鼠标移入后移除边框样式
    $(".loginMain input").focus(function () {
        $(this).parent().parent().attr('style','');
    })

    //点击注册事件
    $(".confirm-ok").on('click',function () {
        ajaxData = {
            'Intention':'Register', //方法
            'User':$("#user").val(), //账号
        }
        if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.User)){
            $(".loginuser").css('border','1px solid red');
            $.toast('请输入正确的手机号码');
            return
        }
        $.post('/ajax/',ajaxData,function (data) {
            console.log(data);
            //200代表未成功，101账户已注册，其它状态返回状态码与Message
            if(data.ResultCode == '200'){
                window.location='/member/registerverify/?Tel='+ajaxData.User;
            }else if(data.ResultCode == '101'){
                $.confirm('该手机号已注册，是否直接登录？', function () {
                    window.location='/';
                });
            }else {
                $.toast(data.Message);
            }
        },'json')
    })

    $.init();
})
