/**
 * Created by Foliage on 2016/10/24.
 */
//根据域名后缀判断引用的通用的js

host = window.location.host.split('.');
var suffix = host[2];

if(suffix == 'cn') {
    var muser = 'http://muser.57us.cn';
    var m_url = 'http://m.57us.cn';
    var js_url = 'http://js.57us.cn';
    var member = "http://member.57us.cn";
} else if(suffix == 'com') {
    var muser = 'http://muser.57us.com';
    var m_url = 'http://m.57us.com';
    var js_url = 'http://js.57us.com';
    var member = "http://member.57us.com";
}else if(suffix == 'net'){
    var muser = 'http://muser.57us.net';
    var m_url = 'http://m.57us.net';
    var js_url = 'http://js.57us.net';
    var member = "http://member.57us.net";
}

//js直接引对应的css
function W_creatLink(cssUrl) {
    var link = document.createElement("link");
    link.type = "text/css";
    link.rel = "stylesheet";
    link.href = js_url + cssUrl;
    document.getElementsByTagName("head")[0].appendChild(link);
}

//图形验证码，点击切换
$(document).on('click','#code',function () {
    this.src='/code/pic.jpg?'+Math.random();
})

//获取短信验证点击倒计时 ipnut按钮
var _wait = 60;
get_code_time = function(o) {
    if(_wait == 0) {
        o.removeAttribute("disabled");
        o.value = "获取动态密码";
        _wait = 60;
    } else {
        o.setAttribute("disabled", true);
        o.value = "(" + _wait + ")秒后重新获取";
        _wait--;
        setTimeout(function() {
            get_code_time(o)
        }, 1000)
    }
}

//url参数截取
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var result = window.location.search.substr(1).match(reg);
    return result ? decodeURIComponent(result[2]) : null;
}

//验证规则
var rule={
    phone:/^1(3\d|5[0-35-9]|8[025-9]|47)\d{8}$/, //手机号
    company:/^[\u4E00-\u9FA5a-zA-Z][\u4E00-\u9FA5a-zA-Z0-9\s-,-.]*$/,
    uname:/^[\u4E00-\u9FA5a-zA-Z][\u4E00-\u9FA5a-zA-Z0-9_]*$/,
    zh:/^[\u4e00-\u9fa5]+$/,//纯中文
    card:/^((1[1-5])|(2[1-3])|(3[1-7])|(4[1-6])|(5[0-4])|(6[1-5])|71|(8[12])|91)\d{4}(((((19|20)((\d{2}(0[13-9]|1[012])(0[1-9]|[12]\d|30))|(\d{2}(0[13578]|1[02])31)|(\d{2}02(0[1-9]|1\d|2[0-8]))|(([13579][26]|[2468][048]|0[48])0229)))|20000229)\d{3}(\d|X|x))|(((\d{2}(0[13-9]|1[012])(0[1-9]|[12]\d|30))|(\d{2}(0[13578]|1[02])31)|(\d{2}02(0[1-9]|1\d|2[0-8]))|(([13579][26]|[2468][048]|0[48])0229))\d{3}))$/, //身份证号
    int:/^[0-9]*$/, //整数
    s:'',
    NameEN:/^[a-zA-Z|\s]{2,20}$/, //英文姓名
    NameZH:/^[\u4e00-\u9fa5 ]{2,10}$/, //中文姓名
    Name:/^[\u4e00-\u9fa5 ]{2,10}$|^[a-zA-Z|\s]{2,20}$/, //中英姓名
    Nick:/^[\w|\d|\u4e00-\u9fa5]{3,15}$/, //昵称
    Num:/^\d+$/, //纯数字>0
    YZM:/^[0-9a-zA-Z]{4}$/, //图形验证码
    Postcode:/^[0-9]\d{5}$/,//邮政编码
    Mail:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/, //邮箱
    PassWord:/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&*]+$)[a-zA-Z\d!@#$%^&*.]{6,20}$/, //密码
    HZ:/^1[45][0-9]{7}|G[0-9]{8}|P[0-9]{7}|S[0-9]{7,8}|D[0-9]+$/, //护照
};

// 页面滚动到顶部或某个部件
$.fn.scrollTo = function(options) {
    var defaults = {
        toT: 90, //滚动目标位置
        durTime: 500, //过渡动画时间
        delay: 30, //定时器时间
        callback: null //回调函数
    };
    var opts = $.extend({},defaults, options),
        timer = null,
        _this = this,
        curTop = _this.scrollTop(), //滚动条当前的位置
        subTop = opts.toT - curTop, //滚动条目标位置和当前位置的差值
        index = 0,
        dur = Math.round(opts.durTime / opts.delay),
        smoothScroll = function(t) {
            index++;
            var per = Math.round(subTop / dur);
            if (index >= dur) {
                _this.scrollTop(t);
                window.clearInterval(timer);
                if (opts.callback && typeof opts.callback == 'function') {
                    opts.callback();
                }
                return;
            } else {
                _this.scrollTop(curTop + index * per);
            }
        };
    timer = window.setInterval(function() {
        smoothScroll(opts.toT);
    }, opts.delay);
    return _this;
};

/**
 * HTML转义方法方法,使用于textarea 提交给后端 增加\n \a
 *
 * @param str 带入的需要转义的文本
 * @returns {s} 返回转义完的html代码
 */
function html_encode(str){
    var s = "";
    if (str.length == 0) return "";
    s = str.replace(/&/g, "&gt;");
    s = s.replace(/</g, "&lt;");
    s = s.replace(/>/g, "&gt;");
    s = s.replace(/ /g, "&nbsp;");
    s = s.replace(/\'/g, "&#39;");
    s = s.replace(/\"/g, "&quot;");
    s = s.replace(/\n/g, "<br>");
    return s;
}

/**
 * HTML转义方法方法,使用于textarea 后端返回 删除\n \a
 *
 * @param str 带入的需要转义的文本
 * @returns {s} 返回转义完的html代码
 */
function html_decode(str){
    var s = "";
    if (str.length == 0) return "";
    s = str.replace(/&gt;/g, "&");
    s = s.replace(/&lt;/g, "<");
    s = s.replace(/&gt;/g, ">");
    s = s.replace(/&nbsp;/g, " ");
    s = s.replace(/&#39;/g, "\'");
    s = s.replace(/&quot;/g, "\"");
    s = s.replace(/<br>/g, "\n");
    return s;
}