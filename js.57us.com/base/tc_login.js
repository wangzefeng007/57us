/**
 * Created by Foliage on 2017/2/20.
 * 弹窗，登录
 */
var tc_login_dom;
$("._MustLogin").on("click ", function() {
    tc_login_dom = {
        'ID': $(this).attr('data-id'),
        'Type': $(this).attr('data-type'),
        'Val': $(this).attr('data-val'),
    }
    $.ajax({
        type: "get",
        url: member + 'loginajax.html',
        data: {
            'Intention': 'LoginStatus',
        },
        dataType: "jsonp",
        jsonp: "LoginStatus",
        success: function() {}
    });
});

//弹窗切换验证码
$(document).on('click','#tc_html .imgCode',function () {
    this.src = '/code/pic.jpg?' + Math.random();
});

/**
 * 判断是否登录返回参数
 * @param data 0代表未登录 1代表登录
 */
function LoginStatus(data) {
    if(data.ResultCode == '0') {
        if(tc_login_dom.Type == 'collection'|| tc_login_dom.Type == 'order' || tc_login_dom.Type == 'tc_login') {
            LoginPop();
        }else if(tc_login_dom.Type == 'phone'){
            IdYesNo();
        }
    } else if(data.ResultCode == '1') {
        if(tc_login_dom.Type == 'collection') {
            tc_collection();
        }
    }
}

/**
 * 弹窗登录窗口相关操作
 */
var tc_index;
var tc_o;
function LoginPop() {
    layer.open({
        type: 1,
        area: ['413px', '420px'],
        skin: 'layui-layer-demo', //样式类名
        closeBtn: 1, //不显示关闭按钮
        shift: 2,
        title: false,
        shadeClose: true, //开启遮罩关闭
        content: tc_html,
        success: function(layero, index) {
            tc_index = index;
            //表单美化
            $('#tc_html .cbt').inputbox();
            //登录切换
            $("#tc_html .LoginPop .hd a").on('click',function() {
                var $num = $(this).index();
                $(this).addClass("on").siblings().removeClass("on");
                $(this).parent().siblings(".bd").find("table").addClass("hidden").eq($num).removeClass("hidden");
            })
            //普通登录事件
            $('#tc_html .index1 .loginBtn').on('click',function() {
                if($("#tc_html .index1 .autoLogin label").hasClass('checked')){
                    var autoLogin = '1';
                }else {
                    var autoLogin = '0';
                }
                var ajaxData = {
                    'Intention': 'Tc_Login',
                    'User': $('#tc_html .index1 .user').val(),
                    'Password': $('#tc_html .index1 .password').val(),
                    'AutoLogin': autoLogin,
                };

                if((rule.phone.test(ajaxData.User) != true && rule.Mail.test(ajaxData.User) != true)) {
                    layer.msg('请输入正确的手机号码或邮箱');
                    return
                } else if(ajaxData.Password == '') {
                    layer.msg('密码不能为空');
                    return
                }
                $.ajax({
                    type: "get",
                    url: member + 'userajax.html',
                    data: ajaxData,
                    dataType: "jsonp",
                    jsonp: "Tc_Login",
                    success: function() {}
                });
            })

            //获取验证码
            $('#tc_html .index2 .codeBtn').on('click',function() {
                tc_o = this;
                var ajaxData = {
                    'Intention': 'Tc_Sms_Code',
                    'User': $('#tc_html .index2 .user').val(),
                    'ImgCode': $('#tc_html .index2 .imgCode').val(),
                };
                if(ajaxData.User == ''){
                    layer.msg('手机号码不能为空');
                    return
                }else if(rule.phone.test(ajaxData.User) != true) {
                    layer.msg('手机号码格式不正确');
                    return
                } else if(ajaxData.ImgCode == "") {
                    layer.msg('图形验证码不能为空');
                    return
                }
                $.ajax({
                    type: "get",
                    url: member + 'loginajax.html',
                    data: ajaxData,
                    dataType: "jsonp",
                    jsonp: "Tc_Sms_Code",
                    success: function() {}
                });
            })

            //手机登录事件
            $('#tc_html .index2 .loginBtn').on('click',function() {
                var ajaxData = {
                    'Intention': 'Tc_Mobile_Login',
                    'User': $('#tc_html .index2 .user').val(),
                    'Code': $('#tc_html .index2 .code').val(),
                };
                if(ajaxData.User == ''){
                    layer.msg('手机号码不能为空');
                    return
                }else if(rule.phone.test(ajaxData.User) != true) {
                    layer.msg('手机号码格式不正确');
                    return
                }else if(ajaxData.Code == ''){
                    layer.msg('短信验证码不为空');
                    return
                }else if(!/^\d{6}$/i.test(ajaxData.Code)) {
                    layer.msg('请输入6位纯数字短信验证码');
                    return
                }
                $.ajax({
                    type: "get",
                    url: member + 'loginajax.html',
                    data: ajaxData,
                    dataType: "jsonp",
                    jsonp: "Tc_Mobile_Login",
                    success: function() {}
                });
            })
        }
    });
}

//普通弹窗回调
function Tc_Login(data) {
    if(data.ResultCode == '200') {
        layer.close(tc_index);
        if(tc_login_dom.Type == 'collection') {
            tc_collection();
        }else if(tc_login_dom.Type == 'order'){
            tc_order(data);
        }else{
            layer.msg(data.Message);
            setTimeout(function () {
                window.location.reload();
            }, 500);
        }
    }else {
        layer.msg(data.Message);
    }
}

//弹窗口获取短信验证码回调
function Tc_Sms_Code(data) {
    if(data.ResultCode == '200') {
        get_code_time(tc_o);
        layer.msg(data.Message);
    }else {
        layer.msg(data.Message);
    }
}

//弹窗手机登录回调
function Tc_Mobile_Login(data) {
    if(data.ResultCode == '200') {
        if(data.ResultCode == '200') {
            layer.close(tc_index);
            if(tc_login_dom.Type == 'collection') {
                tc_collection();
            }else if(tc_login_dom.Type == 'order'){
                tc_order(data);
            }else{
                layer.msg(data.Message);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }
        }
    }else {
        layer.msg(data.Message);
    }
}

//收藏方法
function tc_collection() {
    var ajaxData = {
        'Intention':'Cross_Domain_Collection',
        'ID':tc_login_dom.Val,
        'Type':tc_login_dom.ID,
    }
    $.ajax({
        type: "get",
        url: member + 'userajax.html',
        data: ajaxData,
        dataType: "jsonp",
        jsonp: "Cross_Domain_Collection",
        success: function() {}
    });
}

//收藏提交成功后执行
function Cross_Domain_Collection(data) {
    if(data.ResultCode == '200'){
        layer.msg(data.Message);
        setTimeout(function () {
            window.location.reload();
        }, 500);
    }else {
        layer.msg(data.Message);
    }
}

//订单弹窗登录成功后执行方法
function tc_order(data) {
    $("#yesphone").hide();
    setTimeout(function () {
        layer.msg(data.Message);
    }, 400);
}

// 弹窗登录html
var tc_html = '';
tc_html +='<div class="HiddenLoginBox" id="tc_html">';
tc_html +='<div class="LoginPop">';
tc_html +='<div class="hd"><a href="JavaScript:void(0)" class="on">普通登录</a><a href="JavaScript:void(0)" class="">快速登录</a></div>';
tc_html +='<div class="LoginBoxTit mt50">欢迎登录57美国网</div>';
tc_html +='<div class="bd">';
tc_html +='<table border="0" cellspacing="0" cellpadding="0" width="100%" class="mt20 index1">';
tc_html +='<tbody><tr height="65">';
tc_html +='<td>';
tc_html +='<div class="Loinput UserInput">';
tc_html +='<i></i>';
tc_html +='<input type="text" name="user" value="" placeholder="请输入手机号码或者邮箱" class="UserInput user">';
tc_html +='</div>';
tc_html +='</td>';
tc_html +='</tr>';
tc_html +='<tr height="65">';
tc_html +='<td>';
tc_html +='<div class="Loinput WordInput">';
tc_html +='<i></i>';
tc_html +='<input type="password" name="password" value="" placeholder="请输入密码" class="UserInput password">';
tc_html +='</div>';
tc_html +='</td>';
tc_html +='</tr>';
tc_html +='<tr height="40">';
tc_html +='<td>';
tc_html +='<div class="fl autoLogin">';
tc_html +='<label class="cbt cb" name="rbt" id="auto" type="checkbox" val="3"><i></i>两周内自动登录<input type="hidden" name="check" value="3"></label>';
tc_html +='</div>';
tc_html +='<div class="fr">';
tc_html +='<a href="'+member+'/member/findpassword/">忘记密码？</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'+member+'/member/register/" class="LoginBlueBtn ">立即注册</a>';
tc_html +='</div>';
tc_html +='</td>';
tc_html +='</tr>';
tc_html +='<tr height="55">';
tc_html +='<td><a href="JavaScript:void(0)" class="LoginBtn loginBtn">登录</a></td>';
tc_html +='</tr>';
tc_html +='</tbody></table>';
tc_html +='<table border="0" cellspacing="0" cellpadding="0" width="100%" class="mt20 hidden index2">';
tc_html +='<tbody><tr height="71">';
tc_html +='<td>';
tc_html +='<div class="Loinput UserInput">';
tc_html +='<i></i>';
tc_html +='<input type="text" name="user" value="" placeholder="请输入手机号码" class="UserInput user">';
tc_html +='</div>';
tc_html +='</td>';
tc_html +='</tr>';
tc_html +='<tr height="65">';
tc_html +='<td>';
tc_html +='<div class="Loinput CodesInput fl">';
tc_html +='<input type="text" name="imgCode" value="" placeholder="请输入图形验证码" class="UserInput imgCode">';
tc_html +='</div>';
tc_html +='<div class="fr CodesImg"><img src="/code/pic.jpg" class="imgCode"></div>';
tc_html +='</td>';
tc_html +='</tr>';
tc_html +='<tr height="71">';
tc_html +='<td>';
tc_html +='<div class="Loinput DynamicCodes fl">';
tc_html +='<input type="text" name="code" value="" placeholder="请输入短信验证码" class="UserInput code">';
tc_html +='</div>';
tc_html +='<div class="SendSms fl">';
tc_html +='<input type="button" name="codeBtn" class="codeBtn" value="获取动态密码" />';
tc_html +='</div>';
tc_html +='</td>';
tc_html +='</tr>';
tc_html +='<tr height="55">';
tc_html +='<td><a href="JavaScript:void(0)" class="LoginBtn mt5 loginBtn">登录</a></td>';
tc_html +='</tr>';
tc_html +='</tbody></table>';
tc_html +='</div>';
tc_html +='</div>';
tc_html +='</div>';