/**
 * Created by Foliage on 2017/2/28.
 */
$(document).ready(function () {
    //绑定邮箱获取验证码
    $("#binding .newCodeBtn,#amend .newCodeBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention': 'GetVerifyCode', //获取邮箱验证码
            'User':$(this).parents('.content').find('.newMail').val(), //电子邮箱
        }
        if(ajaxData.User == ''){
            $.toast('邮箱不能为空');
            return
        }else if(rule.Mail.test(ajaxData.User) != true){
            $.toast('邮箱格式不正确');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('获取中');
            },
            success: function(data) {
                if(data.ResultCode == '200'){
                    get_code_time(o);
                    $.toast('邮箱验证码发送成功');
                }else {
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });

    //绑定邮箱第一步,点击下一步
    $("#binding .saveBtn").on('click',function () {
        var ajaxData = {
            'Intention': 'BindingMail', //绑定电子邮箱
            'Mail':$("#binding .newMail").val(), //电子邮箱
            'Code':$("#binding .newCode").val(), //邮箱验证码
        }
        if(ajaxData.Mail == ''){
            $.toast('邮箱不能为空');
            return
        }else if(rule.Mail.test(ajaxData.Mail) != true){
            $.toast('邮箱格式不正确');
            return
        }else if(ajaxData.Code == ''){
            $.toast('邮箱验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            $.toast('请输入6位纯数字邮箱验证码');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('提交中');
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    $.toast('绑定成功');
                    setTimeout(function(){
                        history.go(-1);
                    },1000);
                }else{
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });

    //更换邮箱第一步，获取邮箱验证码
    $("#amend .oldCodeBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention': 'GetVerifyCode', //获取邮箱验证码
            'Type':'0'
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('获取中');
            },
            success: function(data) {
                if(data.ResultCode == '200'){
                    get_code_time(o);
                    $.toast('邮箱验证码发送成功');
                }else {
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });

    //更换邮箱第一步
    $("#amend .nextBtn1").on('click',function () {
        var ajaxData = {
            'Intention': 'DoVerify', //验证旧的邮箱是否正确
            'Code':$("#amend .oldCode").val(), //邮箱验证码
        }
        if(ajaxData.Code == ''){
            $.toast('邮箱验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            $.toast('请输入6位纯数字邮箱验证码');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('提交中');
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    $('#amend .index1').hide();
                    $("#amend header h2").text('绑定新的邮箱');
                    $('#amend .index2').show();
                }else{
                    $.toast(data.Message);
                }
            },
            complete: function () {
                $.hidePreloader();
            }
        });
    });

    //更换邮箱第二步
    $("#amend .nextBtn2").on('click',function () {
        var ajaxData = {
            'Intention': 'BindingMail', //更换新的邮箱
            'Mail':$("#amend .newMail").val(), //邮箱
            'Code':$("#amend .newCode").val(), //邮箱验证码
        }

        if(ajaxData.Mail == ''){
            $.toast('邮箱不能为空');
            return
        }else if(rule.Mail.test(ajaxData.Mail) != true){
            $.toast('邮箱格式不正确');
            return
        }else if(ajaxData.Code == ''){
            $.toast('邮箱验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            $.toast('请输入6位纯数字邮箱验证码');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('提交中');
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    $.toast('绑定成功');
                    setTimeout(function(){
                        history.go(-1);
                    },1000);
                }else{
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });
});