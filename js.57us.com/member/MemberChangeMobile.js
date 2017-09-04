/**
 * Created by Foliage on 2017/2/10.
 */
$(function () {
    //验证手机号码是否绑定过其它账号
    $("#binding .newMobile,#amend .newMobile").blur(function () {
        var _this = $(this).parent();
        var ajaxData = {
            'Intention': 'MobileExists', //验证手机号码是否存在
            'Mobile':$(this).val(), //手机号码
        }
        //验证手机号码是否正确，正确则执行，不正确退出
        if(rule.phone.test(ajaxData.Mobile) != true){
            _this.removeClass('current');
            return
        }
        $.post('/userajax.html',ajaxData,function (data) {
            //200代表不存在 100代表存在
            if(data.ResultCode == '200'){
                _this.addClass('current');
                _this.find('.currtip').html('恭喜您，该手机可以使用<i class="icon iconfont icon-ok"></i>');
            }else if(data.ResultCode == '100'){
                _this.addClass('current');
                _this.find('.currtip').html('该手机号码已被绑定<i class="icon iconfont icon-warning"></i>')
            }
        },'json');
    })

    //绑定手机获取验证码
    $("#binding .newCodeBtn,#amend .newCodeBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention': 'VerificationCode', //获取手机验证码
            'Mobile':$(".newMobile").val(), //手机号码
        }
        if(ajaxData.Mobile == ''){
            layer.msg('手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            layer.msg('手机号码格式不正确');
            return
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

    //绑定手机第一步,点击下一步
    $("#binding .nextBtn1").on('click',function () {
        var ajaxData = {
            'Intention': 'BindingMobile', //绑定手机号码
            'Mobile':$("#binding .newMobile").val(), //手机号码
            'Code':$("#binding .newCode").val(), //短信验证码
        }

        if(ajaxData.Mobile == ''){
            layer.msg('手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            layer.msg('手机号码格式不正确');
            return
        }else if(ajaxData.Code == ''){
            layer.msg('短信验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            layer.msg('请输入6位纯数字短信验证码');
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
                    $("#binding #schedule li").eq(1).addClass('on');
                    $("#binding .mobileShow").text(ajaxData.Mobile);
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

    //更换手机号码第一步，获取短信验证码
    $("#amend .oldCodeBtn").on('click',function () {
        var o = this;
        var ajaxData = {
            'Intention': 'VerificationCode', //获取手机验证码
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

    //更换手机号码第一步
    $("#amend .nextBtn1").on('click',function () {
        var ajaxData = {
            'Intention': 'VerifyMobile', //验证旧的手机号码是否正确
            'Code':$("#amend .oldCode").val(), //短信验证码
        }
        if(ajaxData.Code == ''){
            layer.msg('短信验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            layer.msg('请输入6位纯数字短信验证码');
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
                    $("#amend #schedule li").eq(1).addClass('on');
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    //更换手机号码第二步
    $("#amend .nextBtn2").on('click',function () {
        var ajaxData = {
            'Intention': 'BindingMobile', //更换新的手机号码
            'Mobile':$("#amend .newMobile").val(), //手机号码
            'Code':$("#amend .newCode").val(), //短信验证码
        }

        if(ajaxData.Mobile == ''){
            layer.msg('手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            layer.msg('手机号码格式不正确');
            return
        }else if(ajaxData.Code == ''){
            layer.msg('短信验证码不能为空');
            return
        }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
            layer.msg('请输入6位纯数字短信验证码');
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
                    $("#amend #schedule li").eq(2).addClass('on');
                    $("#amend .mobileShow").text(ajaxData.Mobile);
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