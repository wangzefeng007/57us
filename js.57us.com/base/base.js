//根据域名后缀判断引用的通用的js
host = window.location.host.split('.');
var suffix = host[2];
if(suffix == 'cn') {
	var member = "http://member.57us.cn";
	var js_url = 'http://js.57us.cn';
} else if(suffix == 'com') {
	var member = "http://member.57us.com/";
	var js_url = 'http://js.57us.com';
}else if(suffix == 'net'){
	var member = "http://member.57us.net/";
	var js_url = 'http://js.57us.net';
}

/**
 * js插件直接引入对应的css文件
 *
 * @param cssUrl 对应css文件地址
 */
function W_creatLink(cssUrl) {
	var link = document.createElement("link");
	link.type = "text/css";
	link.rel = "stylesheet";
	link.href = js_url + cssUrl;
	document.getElementsByTagName("head")[0].appendChild(link);
}

/**
 * 获取url中"?"符后的字串
 *
 * @param name url地址对应取的名称 使用方法 var xx = GetQueryString('xx');
 * @returns {*}
 */
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var result = window.location.search.substr(1).match(reg);
    return result ? decodeURIComponent(result[2]) : null;
}

/**
 * 获取url中"?"符后的字串
 *
 * @param url
 * @returns {Object}
 * @constructor  返回对象方式
 */
function W_GetRequest(url) {
    url = url || location.search;
    var theRequest = new Object();
    if(url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for(var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0]] = (strs[i].split("=")[1]);
        }
    }
    return theRequest;
}

/**
 * 获取短信验证点击倒计时 ipnut按钮
 *
 * @param o 当前操作的this
 * @param _wait 倒计时秒数
 */
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

/**
 * 页面滚动到顶部或某个位置
 *
 * @param dom 带入id或者class 需要带入#或者. 选择器
 * @param headspace 滚动位置往上 或者往少 使用+ -表示
 */
function W_ScrollTo(dom, headspace) {
    var speed = 50,
        finishAbs = speed / 2 + 1,
        ScrollToTop = dom ? $(dom).offset().top : 0,
        ScrollTop = document.body.scrollTop;
    if(headspace) {
        ScrollToTop -= headspace;
    }
    $(window).scrollTop(ScrollToTop);
}


/**
 * 获取当前日期方法
 *
 * @returns {clock} 返回当前日期 格式 yyyy-mm-dd
 */
function CurentDate(){
    var now = new Date();
    var year = now.getFullYear();       //年
    var month = now.getMonth() + 1;     //月
    var day = now.getDate();            //日
    var hh = now.getHours();            //时
    var mm = now.getMinutes();          //分
    var clock = year + "-";
    if(month < 10)
        clock += "0";
    clock += month + "-";
    if(day < 10)
        clock += "0";
    clock += day;
    return(clock);
}

/**
 * 获取当前时间方法
 *
 * @returns {clock} 返回当前时间 格式 hh:mm
 */
function CurentTime(){
    var now = new Date();
    var year = now.getFullYear();       //年
    var month = now.getMonth() + 1;     //月
    var day = now.getDate();            //日
    var hh = now.getHours();            //时
    var mm = now.getMinutes();          //分
    var clock = '';
    if(hh < 10)
        clock += "0";
    clock += hh + ":";
    if (mm < 10) clock += '0';
    clock += mm;
    return(clock);
}

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

/**
 * 一维数组去重方法
 *
 * @param arr 需要去重数组
 * @returns {Array} 返回已经去重数组
 */
function unique(arr) {
    var ret = []
    var hash = {}
    for (var i = 0; i < arr.length; i++) {
        var item = arr[i]
        var key = typeof(item) + item
        if (hash[key] !== 1) {
            ret.push(item)
            hash[key] = 1
        }
    }
    return ret
}

/**
 * 文字限制输入
 *
 * @param _this 当前操作的dom
 * @param Num 可以输入几个字
 * @returns {boolean} 返回剩余可输入的字数
 */
function checkLength(_this,Num) {
    var maxChars = Num; //取字数总数
    if(_this.val().length > maxChars) {
        _this.value = _this.val().substring(0, maxChars);
        return false;
    } else {
        var curr = maxChars - _this.val().length; //250 减去 当前输入的
        _this.prev().text(curr.toString() + '/'+Num+'');
        return true;
    }
};

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
    Num2:/^[+-]?\d+(\.\d+)?$/, //数字和小数点
    YZM:/^[0-9a-zA-Z]{4}$/, //图形验证码
    Postcode:/^[0-9]\d{5}$/,//邮政编码
    Mail:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/, //邮箱
    PassWord:/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&*]+$)[a-zA-Z\d!@#$%^&*.]{6,20}$/, //密码
    HZ:/^[a-zA-Z0-9]{3,21}$/, //护照
};

//加载中效果
function public_loading() {
	$('.wrap').append('<div id="public_loading"></div>');
	var opts = {
		lines: 9, // The number of lines to draw
		length: 0, // The length of each line
		width: 13, // The line thickness
		radius: 20, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		color: '#000', // #rgb or #rrggbb
		speed: 1, // Rounds per second
		trail: 60, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
		top: 'auto', // Top position relative to parent in px
		left: 'auto' // Left position relative to parent in px
	};
	var target = document.getElementById('public_loading');
	var spinner = new Spinner(opts).spin(target);
}

//点击增加，减少
$.fn.W_NumberBox = function(options, fun, errFun) {
    var opts = $.extend({}, {
        "readonly": true,
        "min": 1,
        "max": 99,
        "maxlength": 5
    }, options);
    return $(this).each(function() {
        var _this = this;
        var ipt = this.getElementsByTagName('input')[0],
            btn = this.getElementsByTagName('a');
        var n = Number(ipt.value);
        if(opts.readonly) {
            $(ipt).attr("readonly", "readonly");
        }
        if(opts.maxlength) {
            $(ipt).attr("maxlength", opts.maxlength);
        }
        if(n < opts.min || n > opts.max) {
            n = opts.min;
            ipt.value = n;
        }
        if($(ipt).attr("initnum")) {
            ipt.value = Number($(ipt).attr("initnum"));
            n = Number($(ipt).attr("initnum"));
        }
        $(ipt).on('change input', function() {
            var a = $(this).val();
            if(a == '') {
                layer.msg('只能输入整数');
                ipt.value = a;
                $(ipt).attr('value', a);
                $(ipt).blur(function() {
                    if(ipt.value == '') {
                        ipt.value = opts.min;
                        $(ipt).attr('value', opts.min);
                    }
                })
            } else if(a > opts.max) {
                layer.msg('不能输入大于' + opts.max + '整数');
                ipt.value = opts.max;
                $(ipt).attr('value', opts.max);
            } else if(a < opts.min) {
                layer.msg('不能输入小于' + opts.min + '整数');
                ipt.value = opts.min;
                $(ipt).attr('value', opts.min);
            } else if(!/^[0-9]*[0-9][0-9]*$/i.test(a)) {
                layer.msg('只能输入整数');
                ipt.value = opts.min;
                $(ipt).attr('value', opts.min);
            } else {
                $(ipt).attr('value', a);
            }
            if(a == opts.max) {
                $(ipt).next().click(function() {
                    layer.msg('数量不能大于' + opts.max);
                })
            }
        })
        $(ipt).prev().click(function() {
            if(n == opts.min) {
                layer.msg('数量不能小于' + opts.min);
            }
        })
        $(ipt).next().click(function() {
            if(n == opts.max) {
                layer.msg('数量不能大于' + opts.max);
            }
        })
        //双击A标签不选定
        var _btn = $(btn);
        _btn.attr('unselectable', 'on');
        _btn.attr('onselectstart', 'return false;');
        _btn.css('-moz-user-select', 'none');
        _btn.css('-webkit-user-select', 'none');
        _btn.css('-khtml-user-select: none;', 'none');
        _btn.css('-ms-user-select: none; ', 'none');
        _btn.each(function(ii, tt) {
            tt.onclick = function() {
                var newN = $(ipt).attr('value');
                ii ? (newN++) : (newN--);
                if(!(newN < opts.min || newN > opts.max)) {
                    if(fun(newN, _this)) {
                        n = newN;
                        ipt.value = n;
                        $(ipt).attr('value', n);
                    } else if(errFun) {
                        errFun(newN, this);
                    }
                }
            };
        });
    });
};

//根据url，执行对应的方法
$(document).ready(function(){
    //右侧导航事件
    var NfixMenu = $(".NfixMenu");
    var DnPrefix = host[0];
    //在线咨询url
    var _www = 'http://p.qiao.baidu.com/cps/chat?siteId=9748292&userId=21983137';	//主站咨询
    var _tour = 'http://p.qiao.baidu.com/cps/chat?siteId=9846405&userId=21983137'; 	//旅游咨询
    var _study = 'http://p.qiao.baidu.com/cps/chat?siteId=9846413&userId=21983137';	//留学咨询
    //百度商桥点击弹出事件
    NfixMenu.find(".i1").on('click', function () {
        if(DnPrefix == 'zuche'){  //租车
            $("#onlineBox").show(100);
            $("#onlineBoxM").empty();
            $("#onlineBoxM").append('<iframe scrolling="auto" allowtransparency="true"  class="fl" frameborder="0" src="'+ _tour +'" width="600" height="557"></iframe>');
            $("#onlineBox").find('.close').on('click',function () {
                $("#onlineBox").hide(100);
                $("#onlineBoxM").empty();
            });
        }else if(DnPrefix == 'study'){ //留学
            online_consultant(_study);
        }else if(DnPrefix == 'www'){ //资讯主站
            var DnSuffix = window.location.href.split('/');
            if(DnSuffix[3] == 'tour' || DnSuffix[3] == 'travels' || DnSuffix[3] == 'tour_tournews' || DnSuffix[3] == 'tour_meishi' || DnSuffix[3] == 'tour_fengjing'){  //旅游资讯
                online_consultant(_tour);
            }else if(DnSuffix[3] == 'study' || DnSuffix[3] == 'studytopic_uscolege' || DnSuffix[3] == 'studytopic_news' || DnSuffix[3] == 'studytopic_exam' || DnSuffix[3] == 'studytopic_learning' || DnSuffix[3] == 'studytopic_guide'){ //留学资讯
                online_consultant(_study);
            }else if(DnSuffix[3] == '' || DnSuffix[3] == 'immigrant' || DnSuffix[3] == 'immigtopic_genre' || DnSuffix[3] == 'immigtopic_way' || DnSuffix[3] == 'immigtopic_house' || DnSuffix[3] == 'immigtopic_guide'){ //移民资讯,三个专题页面,资讯主页
                online_consultant(_www);
            }else if(DnSuffix[3] == 'topic'){
                if(DnSuffix[4] == 'study2017'){
                    online_consultant(_study);
                }else {
                    online_consultant(_www);
                }
            }
        }else if(DnPrefix == 'hotel'){ //酒店
            online_consultant(_tour);
        }else if(DnPrefix == 'visa'){ //签证
            online_consultant(_tour);
        }else if(DnPrefix == 'tour'){ //旅游
            online_consultant(_tour);
        }else { //未定义的
            online_consultant(_tour);
        }
    })
    //二维码
    var $thisDom = NfixMenu.find(".i3").find(".FixSecondM");
    $thisDom.find('p').empty();
    $thisDom.find('img').remove();
    //二维码标题
    var _codetitle_public = '57US公众号';
    var _codetitle_study = '57US留学微信';
    var _codetitle_tour = '57US旅游微信';
    //二维码图处url
    var _codeimgurl_public = 'http://images.57us.com/img/common/wx.png'; //公用二维码
    var _codeimgurl_study = 'http://images.57us.com//img/common/wxstudy.jpg';	//留学二维码
    var _codeimgurl_tour = 'http://images.57us.com//img/common/wxtravel.jpg';	//旅游二维码
    //根据url判断使用对应二维码
    if(DnPrefix == 'www'){
        code_content($thisDom,_codetitle_public,_codeimgurl_public);
    }else if(DnPrefix == 'study'){
        code_content($thisDom,_codetitle_study,_codeimgurl_study);
    }else if(DnPrefix == 'zuche'){
        code_content($thisDom,_codetitle_tour,_codeimgurl_tour);
    }else if(DnPrefix == 'tour'){
        code_content($thisDom,_codetitle_tour,_codeimgurl_tour);
    }else if(DnPrefix == 'hotel'){
        code_content($thisDom,_codetitle_tour,_codeimgurl_tour);
    }else if(DnPrefix == 'visa'){
        code_content($thisDom,_codetitle_tour,_codeimgurl_tour);
    }else{
        code_content($thisDom,_codetitle_tour,_codeimgurl_tour);
    }
    //返回顶部事件
    NfixMenu.find(".i5").on('click', function() {
        $('body,html').animate({
            scrollTop: 0
        }, 300);
    });
    if(NfixMenu.length) {
        //当滚动条距离顶部大于200px时显示
        var thisDom = NfixMenu.find(".i5");
        if($(this).scrollTop() < 200) {
            thisDom.hide();
        }
        $(window).scroll(function() {
            if($(this).scrollTop() < 200) {
                thisDom.hide();
            }
            if($(this).scrollTop() > 200) {
                thisDom.show();
            }
        });
    }

    //百度
    // var _hmt = _hmt || [];
    // (function() {
    //     var hm = document.createElement("script");
    //     hm.src = "//hm.baidu.com/hm.js?21f9def31aa89e3cd98d7b6b95068701";
    //     var s = document.getElementsByTagName("script")[0];
    //     s.parentNode.insertBefore(hm, s);
    // })();
    //Cnzz
    // var cnzz = 'http://s4.cnzz.com/z_stat.php?id=1256811158&web_id=1256811158';
    // $("html body").append('<script src=' + cnzz + ' language="JavaScript"></script>');

    //分享事件
    //如果不需要注分享图标直接退出
    if($('#ShareBoxList').length > 1 || $('#ShareBoxList').length > 1){
        return
    }
    //资讯站的分享图标
    var html = '<div class="bdsharebuttonbox" data-tag="share_1">' +
        '<div class="bdsharebuttonbox">' +
        '<a title="分享到微信" class="bds_weixin" data-cmd="weixin"></a>' +
        '<a title="分享到新浪微博" href="#" class="bds_tsina" data-cmd="tsina"></a>' +
        '<a title="分享到QQ空间" href="#" class="bds_qzone" data-cmd="qzone"></a>' +
        '<a title="分享到QQ好友" href="#" class="bds_sqq" data-cmd="sqq"></a>' +
        '<a title="分享到人人网" href="#" class="bds_renren" data-cmd="renren"></a>' +
        '<a title="分享到百度贴吧" href="#" class="bds_tieba" data-cmd="tieba"></a>' +
        '<a title="分享到天涯" href="#" class="bds_ty" data-cmd="ty"></a>' +
        '<a title="分享到豆瓣" href="#" class="bds_douban" data-cmd="douban"></a>' +
        '<a title="分享到花瓣" href="#" class="bds_huaban" data-cmd="huaban"></a>' +
        '<a title="分享到开心" href="#" class="bds_kaixin001" data-cmd="kaixin001"></a>' +
        '</div>' +
        '</div>';
    //问答站的分享图标
    var html2 = '<div class="bdsharebuttonbox" data-tag="share_1">' +
        '<div class="bdsharebuttonbox">' +
        '<a title="分享到微信" class="bds_weixin" data-cmd="weixin"></a>' +
        '<a title="分享到新浪微博" href="#" class="bds_tsina" data-cmd="tsina"></a>' +
        '<a title="分享到QQ空间" href="#" class="bds_qzone" data-cmd="qzone"></a>' +
        '<a title="分享到QQ好友" href="#" class="bds_sqq" data-cmd="sqq"></a>' +
        '<a title="分享到人人网" href="#" class="bds_renren" data-cmd="renren"></a>' +
        '<a title="分享到百度贴吧" href="#" class="bds_tieba" data-cmd="tieba"></a>' +
        '<a title="分享到天涯" href="#" class="bds_ty" data-cmd="ty"></a>' +
        '<a title="分享到豆瓣" href="#" class="bds_douban" data-cmd="douban"></a>' +
        '<a title="分享到花瓣" href="#" class="bds_huaban" data-cmd="huaban"></a>' +
        '</div>' +
        '</div>';
    //分享图标html代码
    if($('#ShareBoxList').length > 0){
        $('#ShareBoxList').append(html);
    }
    //问答分享注入
    if($("#sharePop").length > 0){
        $("#sharePop").find('.shareList').append(html2);
    }
    //分享地址
    var share_url = window.location.href;
    //分享标题
    var share_title = $("head title").text();
    //分享描述
    var share_abstract = $("[data-cmd='depict']").text();
    //分享图片
    var share_pic = "http://images.57us.com/img/common/logo.png";
    window._bd_share_config = {
        common: {
            bdText: "#我去美国#" + share_title,
            bdDesc: share_abstract,
            bdUrl: share_url,
            bdPic: share_pic
        },
        //分享图标大小尺寸
        share: [{
            "bdSize": 24
        }]
    }
    with(document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion=' + ~(-new Date() / 36e5)];
});

/**
 * 在线咨询方法
 *
 * @param url 对应在线咨询方法
 */
function online_consultant(url) {
    layer.open({
        type: 2,
        skin: 'demoChat',
        title: '在线咨询',
        shadeClose: true,
        shade: 0,
        area: ['600px','600px'],
        content: url //iframe的url
    });
}

/**
 * 右侧二维码方法
 *
 * @param id 对应的id位置
 * @param title 标题
 * @param url 对应的图片地址
 */
function code_content($thisDom,title,url) {
    $thisDom.find('p').append(title);
    $thisDom.append('<img src="'+ url +'" width="134"/>');
}

//未重写的通用配置
var _DomJson = {},
    _HrefLocation = window.location.href,
    _HrefHead = window.location.protocol + "//" + window.location.hostname,
    _AjaxUrl = _HrefHead + "/ajax.html",
    _UserAjaxUrl = _HrefHead + "/userajax.html",
    _RegNotNull = new RegExp(/^\S/),
    _RegPhone = new RegExp(/^1[3|4|5|7|8]\d{9}$/),
    _RegMail = new RegExp(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/),
    _RegUser = new RegExp(/^1[3|4|5|7|8]\d{9}$|^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/),
    _RegPassWord = new RegExp(/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&*]+$)[a-zA-Z\d!@#$%^&*.]{8,16}$/),
    _RegNameZH = new RegExp(/^[\u4e00-\u9fa5 ]{2,10}$/), //中文姓名
    _RegNameEN = new RegExp(/^[a-zA-Z|\s]{2,20}$/), //英文姓名
    _RegName = new RegExp(/^[\u4e00-\u9fa5 ]{2,10}$|^[a-zA-Z|\s]{2,20}$/), //中英文姓名
    _RegNick = new RegExp(/^[\w|\d|\u4e00-\u9fa5]{4,15}$/), //昵称
    _RegSFZ = new RegExp(/^[11|12|13|14|15|21|22|23|31|32|33|34|35|36|37|41|42|43|44|45|46|50|51|52|53|54|61|62|63|64|65|71|81|82|91]\d{16}[0-9|x|X]$/), //身份证
    _RegHZ = new RegExp(/^1[45][0-9]{7}|G[0-9]{8}|P[0-9]{7}|S[0-9]{7,8}|D[0-9]+$/), //护照
    _Reg6Number = new RegExp(/^\d{6}$/), //短信或邮箱6位验证码
    _RegNum = new RegExp(/^\d+$/), //纯数字>0
    _RegPrice = new RegExp(/^\d{0,}(\.\d{1,2})?$/), //货币
    _RegYZM = new RegExp(/^[0-9a-zA-Z]{4}$/), //图形验证码
    _RegHBH = new RegExp(/^[A-Z]{2}\d{3,4}$/), //航班号
    _RegYZBM = new RegExp(/^[0-9]\d{5}$/); //邮政编码

//input正则验证结果提示 以及一些常用验证工具
function W_formatTips(dom, isCur, msg) {
    $(dom).addClass(isCur ? 'curr_input' : 'erro_input').removeClass(isCur ? 'erro_input' : 'curr_input').parent().find("span").remove();
    if(!isCur) {
        $(dom).parent().append('<span class="erro_tip">' + msg + '</span>');
    }
}

$.fn.W_BlurFomat = function(thisReg, msg, fun) {
    var _canNull = this[0].className ? (this[0].className.search("_CanNull") + 1) : 0;
    this.on("blur", function() {
        var testBoolearn, v = this.value.replace(/(^\s*)|(\s*$)/g, "");
        if(!v) {
            if(fun) {
                fun(this, _canNull, '不能为空');
            } else {
                W_formatTips(this, _canNull, '不能为空');
            }
        } else {
            testBoolearn = thisReg.test(v);
            if(fun) {
                fun(this, testBoolearn, msg);
            } else {
                W_formatTips(this, testBoolearn, msg);
            }
        }
    });
    // console.log(this[0].value);
    if(this[0].value) {
        $(this).trigger('blur');
    }
};
$.fn.W_Format = function(fun) {
    $(this).each(function() {
        var msg, thisReg, thisName = this.name || '';
        if(thisName !== '') {
            if(_DomJson[thisName]) {
                if(_DomJson[thisName][1]) {
                    _DomJson[thisName].push(this);
                } else {
                    _DomJson[thisName] = [_DomJson[thisName], this];
                }
            } else {
                _DomJson[thisName] = this;
            }
        }
        thisName = thisName.toLowerCase();
        if(this.pattern) {
            thisReg = eval(this.pattern);
            if(thisReg) {
                msg = this.getAttribute('data-msg') || "信息有误";
            }
        } else if(thisName.search('nameen') >= 0) {
            thisReg = _RegNameEN;
            msg = '请输入真实的英文名字';
        } else if(thisName.search('pinyin') >= 0) {
            thisReg = _RegNameEN;
            msg = '请输入正确的拼音';
        } else if(thisName.search('pyname') >= 0) {
            thisReg = _RegNameEN;
            msg = '请输入正确的拼音姓名';
        } else if(thisName.search('namezh') >= 0) {
            thisReg = _RegNameZH;
            msg = '请输入真实的中文名字';
        } else if(thisName.search('name') >= 0) {
            thisReg = _RegName;
            msg = '请输入真实的名字';
        } else if(thisName.search('phone') >= 0 || thisName.search('tel') >= 0) {
            thisReg = _RegPhone;
            msg = '手机号码格式错误';
        } else if(thisName.search('mail') >= 0) {
            thisReg = _RegMail;
            msg = '邮箱格式错误';
        } else if(thisName.search('image') >= 0) {
            thisReg = _RegYZM;
            msg = '图形验证码格式错误';
        } else if(thisName.search('postcode') >= 0 || thisName.search('zipcode') >= 0) {
            thisReg = _RegYZBM;
            msg = '邮政编码格式错误';
        } else if(thisName.search('pass') >= 0) {
            thisReg = _RegPassWord;
            msg = '密码长度为8-16位,且不能为纯数字或字母';
        } else if(this.className.search("_CantNull") + 1) {
            thisReg = new RegExp(/\S/);
        } else if(thisName.search('price') >= 0) {
            thisReg = _RegPrice;
            msg = '价格格式不正确';
        } else if(thisName.search('nick') >= 0) {
            thisReg = _RegNick;
            msg = '4-15位的中文,字母,数字或下划线';
        } else {
            return;
        }
        $(this).W_BlurFomat(thisReg, msg, fun);
    });
};