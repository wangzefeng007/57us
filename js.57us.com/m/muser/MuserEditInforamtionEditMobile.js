/**
 * Created by Foliage on 2017/2/28.
 */
$(document).ready(function () {
    //绑定手机获取验证码
    $("#binding .newCodeBtn,#amend .newCodeBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention': 'GetVerifyCode', //获取手机验证码
            'User':$(".newMobile").val(), //手机号码
        }
        if(ajaxData.User == ''){
            $.toast('手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.User) != true){
            $.toast('手机号码格式不正确');
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
                    $.toast('验证码发送成功');
                }else {
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });

    //绑定手机第一步,点击下一步
    $("#binding .nextBtn1").on('click',function () {
        var ajaxData = {
            'Intention': 'BindingMobile', //绑定手机号码
            'Mobile':$("#binding .newMobile").val(), //手机号码
            'Code':$("#binding .newCode").val(), //短信验证码
        }
        if(ajaxData.Mobile == ''){
            $.toast('手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            $.toast('手机号码格式不正确');
            return
        }else if(ajaxData.Code == ''){
            $.toast('短信验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            $.toast('请输入6位纯数字短信验证码');
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

    //更换手机号码第一步，获取短信验证码
    $("#amend .oldCodeBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention': 'GetVerifyCode', //获取手机验证码
            'Type':'1'
        };
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
                    $.toast('验证码发送成功');
                }else {
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });

    //更换手机号码第一步
    $("#amend .nextBtn1").on('click',function () {
        var ajaxData = {
            'Intention': 'DoVerify', //验证旧的手机号码是否正确
            'Code':$("#amend .oldCode").val(), //短信验证码
        }
        if(ajaxData.Code == ''){
            $.toast('短信验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            $.toast('请输入6位纯数字短信验证码');
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
                    $("#amend header h2").text('绑定新的号码');
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

    //更换手机号码第二步
    $("#amend .nextBtn2").on('click',function () {
        var ajaxData = {
            'Intention': 'BindingMobile', //更换新的手机号码
            'Mobile':$("#amend .newMobile").val(), //手机号码
            'Code':$("#amend .newCode").val(), //短信验证码
        }

        if(ajaxData.Mobile == ''){
            $.toast('手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            $.toast('手机号码格式不正确');
            return
        }else if(ajaxData.Code == ''){
            $.toast('短信验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            $.toast('请输入6位纯数字短信验证码');
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