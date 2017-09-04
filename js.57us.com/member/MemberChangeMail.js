/**
 * Created by Foliage on 2017/2/10.
 */
$(function () {
    //验证手机号码是否绑定过其它账号
    $("#binding .newMail,#amend .newMail").blur(function () {
        var _this = $(this).parent();
        var ajaxData = {
            'Intention': 'MailExists', //验证邮箱是否存在
            'Mail':$(this).val(), //电子邮箱
        }
        //验证手机号码是否正确，正确则执行，不正确退出
        if(rule.Mail.test(ajaxData.Mail) != true){
            _this.removeClass('current');
            return
        }
        $.post('/userajax.html',ajaxData,function (data) {
            //200代表不存在 100代表存在
            if(data.ResultCode == '200'){
                _this.addClass('current');
                _this.find('.currtip').html('恭喜您，该邮箱可以使用<i class="icon iconfont icon-ok"></i>');
            }else if(data.ResultCode == '100'){
                _this.addClass('current');
                _this.find('.currtip').html('该邮箱已被绑定<i class="icon iconfont icon-warning"></i>')
            }
        },'json');
    })

    //绑定邮箱获取验证码
    $("#binding .newCodeBtn,#amend .newCodeBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention': 'VerificationMailCode', //获取邮箱验证码
            'Mail':$(this).parents('.yzCont').find('.newMail').val(), //电子邮箱
        }
        if(ajaxData.Mail == ''){
            layer.msg('邮箱不能为空');
            return
        }else if(rule.Mail.test(ajaxData.Mail) != true){
            layer.msg('邮箱格式不正确');
            return
        }
        $.post('/userajax.html',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                get_code_time(o);
                layer.msg('邮箱验证码发送成功');
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })

    //绑定邮箱第一步,点击下一步
    $("#binding .nextBtn1").on('click',function () {
        var ajaxData = {
            'Intention': 'BindingMail', //绑定电子邮箱
            'Mail':$("#binding .newMail").val(), //电子邮箱
            'Code':$("#binding .newCode").val(), //邮箱验证码
        }

        if(ajaxData.Mail == ''){
            layer.msg('邮箱不能为空');
            return
        }else if(rule.Mail.test(ajaxData.Mail) != true){
            layer.msg('邮箱格式不正确');
            return
        }else if(ajaxData.Code == ''){
            layer.msg('邮箱验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            layer.msg('请输入6位纯数字邮箱验证码');
            return
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    $('#binding .index1').hide();
                    $('#binding .index2').show();
                    $("#binding .schedule li").eq(1).addClass('on');
                    $("#binding .mailShow").text(ajaxData.Mail);
                    setTimeout(function(){
                        window.location = '/member/';
                    },5000);
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    //更换邮箱第一步，获取邮箱验证码
    $("#amend .oldCodeBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention': 'VerificationMailCode', //获取邮箱验证码
        }
        $.post('/userajax.html',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                get_code_time(o);
                layer.msg('验证码发送成功');
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })

    //更换邮箱第一步
    $("#amend .nextBtn1").on('click',function () {
        var ajaxData = {
            'Intention': 'VerifyMail', //验证旧的邮箱是否正确
            'Code':$("#amend .oldCode").val(), //邮箱验证码
        }
        if(ajaxData.Code == ''){
            layer.msg('邮箱验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            layer.msg('请输入6位纯数字邮箱验证码');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    $('#amend .index1').hide();
                    $('#amend .index2').show();
                    $("#amend .schedule li").eq(1).addClass('on');
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    //更换邮箱第二步
    $("#amend .nextBtn2").on('click',function () {
        var ajaxData = {
            'Intention': 'BindingMail', //更换新的邮箱
            'Mail':$("#amend .newMail").val(), //邮箱
            'Code':$("#amend .newCode").val(), //邮箱验证码
        }

        if(ajaxData.Mail == ''){
            layer.msg('邮箱不能为空');
            return
        }else if(rule.Mail.test(ajaxData.Mail) != true){
            layer.msg('邮箱格式不正确');
            return
        }else if(ajaxData.Code == ''){
            layer.msg('邮箱验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            layer.msg('请输入6位纯数字邮箱验证码');
            return
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    $('#amend .index2').hide();
                    $('#amend .index3').show();
                    $("#amend .schedule li").eq(2).addClass('on');
                    $("#amend .mailShow").text(ajaxData.Mail);
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