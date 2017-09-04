/**
 * Created by Foliage on 2016/11/4.
 */
//页面登录逻辑判断
var _thisDom;
$(function() {
    $("._MustLogin").on("click ", function() {
        _thisDom = {
            'id': $(this).attr('id'),
            'dataType': $(this).attr('data-type'),
            'dataId': $(this).attr('data-id'),
        }
        $.ajax({
            //请求方式为get
            type: "post",
            //json文件位置
            url: member + '/usertcajax.html',
            data: {
                'Intention': 'LoginStatus',
            },
            //返回数据格式为json
            dataType: "jsonp",
            jsonp: "LoginStatus",
            //请求成功完成后要执行的方法
            success: function() {}
        });
    });
});


//页面登录逻辑判断回调
function LoginStatus(data) {
    if(data.ResultCode == '0') {
        if(_thisDom.id == 'collection'|| _thisDom.id == 'TourOrder' || _thisDom.id == 'M_Study') {
            LoginPop();
        }else if(_thisDom.id == 'phone'){
            IdYesNo();
        }
    } else if(data.ResultCode == '1') {
        if(_thisDom.id == 'collection') {
            collection(_thisDom);
        }
    }
}

//收藏方法
function collection() {
    $.ajax({
        type: "post",
        url: '/ajaxwww.html',
        data: {
            'Intention': 'OperateCollection',
            'id': _thisDom.dataType,
            'type': _thisDom.dataId,
        },
        dataType: "json",
        error: function() {
            layer.msg('网络出错！');
        },
        success: function(data) {
            if(data.ResultCode == '200') {
                $.toast(data.Message);
            } else if(data.ResultCode == '100') {
                $.toast(data.Message);
            } else {
                $.toast(data.Message);
            }
        }
    });
}
//弹出登录窗口方法
var sms_btn;
function LoginPop() {
    //点击切换验证码
    $("#codeb").click(function() {
        this.src = '/code/pic.jpg?' + Math.random();
    })
    var _phone = $("#phone").val();
    $("#user").val(_phone);
    $("#mobile").val(_phone);

    $("#LoginBtn").on('click',function () {
        var _thisId = $('.tabs .active').attr('id');
        if(_thisId == 'tab1'){
            ajaxData = {
                'user': $('#user').val(),
                'password': $('#password').val(),
            }
            if((!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.user)) && (!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(ajaxData.user))) {
                $.toast('请输入正确的手机号码或邮箱');
                return
            } else if(ajaxData.password == '') {
                $.toast('密码不能为空');
                return
            }
            $.ajax({
                type: "post",
                //json文件位置
                url: member + '/usertcajax.html',
                data: {
                    'Intention': 'TCLogin',
                    'User': ajaxData.user,
                    'Pass': ajaxData.password,
                },
                //返回数据格式为json
                dataType: "jsonp",
                jsonp: "jsonpCallback",
                success: function() {}
            });
        }else if(_thisId == 'tab2'){
            ajaxData = {
                'phone': $('#mobile').val(),
                'sms': $('#sms').val(),
            }
            if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.phone)) {
                $.toast('请输入正确的手机号码');
                return
            } else if(ajaxData.sms == "") {
                $.toast('短信验证码不能为空');
                return
            } else if(!/^\d{6}$/i.test(ajaxData.sms)) {
                $.toast('请输入6位纯数字短信验证码');
                return
            }
            if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxData.phone)) {
                $.toast('请输入正确的手机号码');
                return
            } else if(ajaxData.sms == "") {
                $.toast('短信验证码不能为空');
                return
            } else if(!/^\d{6}$/i.test(ajaxData.sms)) {
                $.toast('请输入6位纯数字短信验证码');
                return
            }
            $.ajax({
                type: "post",
                url: member + '/usertcajax.html',
                data: {
                    'Intention': 'MpLoginVerify',
                    'User': ajaxData.phone,
                    'Code': ajaxData.sms,
                },
                dataType: "jsonp",
                jsonp: "jsonpCallback",
                success: function() {}
            });
        }
    })

    //获取验证码事件
    $('#sms_btn').on('click',function() {
        sms_btn = this;
        ajaxJson = {
            'phone': $('#mobile').val(),
            'codes': $('#Codes').val(),
        };
        if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(ajaxJson.phone)) {
            $.toast('请输入正确的手机号码');
            $('#codeb').trigger("click");
            return
        } else if(ajaxJson.codes == "") {
            $.toast('验证码不正确');
            $('#codeb').trigger("click");
            return
        }
        $.ajax({
            //请求方式为get
            type: "post",
            url: member + '/usertcajax.html',
            data: {
                'Intention': 'MpLogin',
                'User': ajaxJson.phone,
                'Code': ajaxJson.codes,
            },
            dataType: "jsonp",
            jsonp: "jsonpCallback",
            success: function() {}
        });
    })
}
//弹窗回调
function jsonpCallback(data) {
    //普通登录事件回调执行方法
    if(data.Intention == 'TCLogin') {
        if(data.ResultCode == '200') {
            if(_thisDom.id == 'collection') {
                $(".layui-layer-shade").hide();
                $(".layui-layer-demo").hide();
                collection(_thisDom);
                setTimeout(function () {
                    window.location.reload();
                }, 400);
            }else if(_thisDom.id == 'TourOrder'){
                history.go(-1);//返回上一页面
                $("#yesphone").hide(); //隐藏登录div
                setTimeout(function () {
                    $.toast(data.Message);
                }, 400);
            }else if(_thisDom.id == 'M_Study'){
                history.go(-1);//返回上一页面
                setTimeout(function () {
                    $.toast(data.Message);
                    setTimeout(function () {
                        window.location.reload();
                    },200);
                }, 400);
            }
        } else if(data.ResultCode == '100') {
            $.toast(data.Message);
            return
        }else if(data.ResultCode == '106') {
            $.toast('未注册的手机号码或邮箱!');
            return
        }
        //获取验证码事件回调执行方法
    } else if(data.Intention == 'MpLogin') {
        var o = sms_btn;
        if(data.ResultCode == '200') {
            get_code_time(o);
            $.toast('验证码发送成功');
        } else if(data.ResultCode == '100') {
            $.toast('请求异常，请稍后再试！');
            $('.layui-layer-demo #codeb').trigger("click");
            return
        } else if(data.ResultCode == '101') {
            $.toast('图形验证码错误');
            $('.layui-layer-demo #codeb').trigger("click");
            return
        }
        //手机登录事件回调执行方法
    } else if(data.Intention == 'MpLoginVerify') {
        if(data.ResultCode == '200') {
            if(_thisDom.id == 'collection') {
                $(".layui-layer-shade").hide();
                $(".layui-layer-demo").hide();
                collection(_thisDom);
                setTimeout(function () {
                    window.location.reload();
                }, 400);
            }else if(_thisDom.id == 'TourOrder'){
                history.go(-1);//返回上一页面
                $("#yesphone").hide(); //隐藏登录div
                setTimeout(function () {
                    $.toast(data.Message);
                }, 400);
            }else if(_thisDom.id == 'M_Study'){
                history.go(-1);//返回上一页面
                setTimeout(function () {
                    $.toast(data.Message);
                    setTimeout(function () {
                        window.location.reload();
                    },200);
                }, 400);
            }
        } else if(data.ResultCode == '102') {
            $.toast('短信验证码错误');
            return
        } else if(data.ResultCode == '103') {
            $.toast('短信验证码过期');
            return
        }
    }
}