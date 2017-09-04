$(function() {
    //修改密码start
    $('.ChangePassWord .CanceBtn').click(function() {
        window.location = "/member/securitycenter/";
    })

    $('.ChangePassWord .EditeBtn').click(function() {
        if(!verifyCheck._click()) return;
        var oldpwd = $('#oldpwd').val();
        var newpwd = $('input[name="pass"]').val();
        ajaxJson = {
            'Intention': 'ModifyPass',
            'Pass': oldpwd,
            'NewPass': newpwd
        }
        $.post('/userajax.html', ajaxJson, function(data) {
            if(data.ResultCode == '200') {
                var Url = data.Url;
                layer.msg(data.Message);
                setTimeout(function() {
                    window.location = Url
                }, 1000);
            } else if(data.ResultCode == '101') {
                $('.oldpw-sucessfill').hide();
                $('#oldpwd').addClass('v_error');
                $('.oldpwdError').append('密码错误');
            } else {
                layer.msg(data.Message);
            }
        }, 'json');
    })
    //修改密码end

    //修改手机start
    $('div.mobile .GetEms').click(function() {
        var o = this;
        $.post('/userajax.html', {
            Intention: 'SendMobileCode'
        }, function(json) {
            if(json.ResultCode == '200') {
                get_code_time(o);
            } else {
                layer.msg(json.Message);
            }
        }, 'json');
    })

    $('div.mobile .EditeBtn').click(function() {
        var ajaxJson = {
            'Intention': 'MobileVerifyCode',
            'VerifyCode': $('div.mobile .input').val()
        };
        if(ajaxJson.VerifyCode == '') {
            $('div.mobile .input').focus();
            layer.msg('请填写验证码');
        } else {
            $.post('/userajax.html', ajaxJson, function(json) {
                if(json.ResultCode == '200') {
                    layer.msg('验证通过');
                    setTimeout(function() {
                        window.location = json.Url
                    }, 1000);
                } else {
                    $('div.mobile .input').focus();
                    layer.msg(json.Message);
                }
            }, 'json');
        }
    })
    //修改手机end

    //修改邮箱start
    $('div.email .GetEms').click(function() {
        var o = this;
        $.post('/userajax.html', {
            Intention: 'SendMailCode'
        }, function(json) {
            if(json.ResultCode == '200') {
                get_code_time(o);
            } else {
                layer.msg(json.Message);
            }
        }, 'json');
    })

    $('div.email .EditeBtn').click(function() {
        var ajaxJson = {
            'Intention': 'MailVerifyCode',
            'VerifyCode': $('div.email .input').val()
        };
        if(ajaxJson.VerifyCode == '') {
            $('div.email .input').focus();
            layer.msg('请填写验证码');
        } else {
            $.post('/userajax.html', ajaxJson, function(json) {
                if(json.ResultCode == '200') {
                    layer.msg('验证通过');
                    setTimeout(function() {
                        window.location = json.Url
                    }, 1000);
                } else {
                    $('div.mobile .input').focus();
                    layer.msg(json.Message);
                }
            }, 'json');
        }
    })
    //修改邮箱end

    //绑定手机或邮箱start
    $("#Account").on('input change', function() {
        var a = $(this).val();
        $(this).attr('value', a);
    })

    $('div.m_em_both .GetEms').click(function() {
        var o = this;
        var Account = $(".m_em_both #Account").val();
        if($("#Account").val() == '') {
            if($('div.m_em_both #Account').attr('name') == 'phone') {
                $('.SafeListRight b').remove();
                $('div.m_em_both #Account').after('<b class="red pl10">*手机号码不能为空</b>');
            } else {
                $('.SafeListRight b').remove();
                $('div.m_em_both #Account').after('<b class="red pl10">*邮箱不能为空</b>');
            }
            return
        } else {
            if($('div.m_em_both #Account').attr('name') == 'phone') {
                if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(Account)) {
                    $('.SafeListRight b').remove();
                    $('div.m_em_both #Account').after('<b class="red pl10">*手机号码格式不正确</b>');
                    return
                }
            } else {
                if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(Account)) {
                    $('.SafeListRight b').remove();
                    $('div.m_em_both #Account').after('<b class="red pl10">*邮箱格式不正确</b>');
                    return
                }
            }
        }
        $.post('/userajax.html', {
            Intention: 'SendVerifyCode',
            User: Account,
        }, function(json) {
            if(json.ResultCode == '200') {
                get_code_time(o);
            } else if(json.ResultCode == '102') {
                $('div.m_em_both #Account').focus();
                $('div.m_em_both #Account').siblings('b.red').remove();
                if($('div.m_em_both #Account').attr('name') == 'phone') {
                    $('div.m_em_both #Account').after('<b class="red pl10">*该手机已绑定过账号</b>');
                } else {
                    $('div.m_em_both #Account').after('<b class="red pl10">*该邮箱已绑定过账号</b>');
                }
            } else {
                layer.msg(json.Message);
            }
        }, 'json');
    })

    $('div.m_em_both .EditeBtn').click(function() {
        var ajaxJson = {
            'Intention': 'BindingAccount',
            'User': $('div.m_em_both #Account').val(),
            'VerifyCode': $('div.m_em_both #verifycode').val()
        };
        if(ajaxJson.VerifyCode == '') {
            $('div.m_em_both #verifycode').focus();
            layer.msg('请填写验证码');
        } else {
            $.post('/userajax.html', ajaxJson, function(json) {
                if(json.ResultCode == '200') {
                    var Url = json.Url;
                    layer.msg('绑定成功');
                    setTimeout(function() {
                        window.location = Url
                    }, 1000);
                } else if(json.ResultCode == '102') {
                    $('div.m_em_both #Account').focus();
                    $('div.m_em_both #Account').siblings('b.red').remove();
                    if($('div.m_em_both #Account').attr('name') == 'phone') {
                        $('div.m_em_both #Account').after('<b class="red pl10">*该手机已绑定过账号</b>');
                    } else {
                        $('div.m_em_both #Account').after('<b class="red pl10">*该邮箱已绑定过账号</b>');
                    }
                } else {
                    $('div.m_em_both #verifycode').focus();
                    layer.msg(json.Message);
                }
            }, 'json');
        }
    })
    //绑定手机或邮箱end

    function ErrTips(Dom, isTrue, Msg) {
        $(Dom).siblings('b.red').remove();
        if(!isTrue) {
            $(Dom).after('<b class="red pl10">*' + Msg + '</b>');
        }
    }
});