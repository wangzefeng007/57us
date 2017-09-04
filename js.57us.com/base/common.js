var _DomJson={},
  _HrefLocation=window.location.href,
  _HrefHead=window.location.protocol+"//"+window.location.hostname,
  _AjaxUrl=_HrefHead+"/ajax.html",
  _UserAjaxUrl=_HrefHead+"/userajax.html",
  _RegNotNull=new RegExp(/^\S/),
  _RegPhone=new RegExp(/^1[3|4|5|7|8]\d{9}$/),
  _RegMail=new RegExp(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/),
  _RegUser=new RegExp(/^1[3|4|5|7|8]\d{9}$|^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/),
  _RegPassWord=new RegExp(/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&*]+$)[a-zA-Z\d!@#$%^&*.]{8,16}$/),
  // _RegPassWord=new RegExp(/^(?![0-9]*$)(?![a-zA-Z]*$)[a-zA-Z0-9]{8,16}$/),
  _RegNameZH=new RegExp(/^[\u4e00-\u9fa5 ]{2,10}$/),//中文姓名
  _RegNameEN=new RegExp(/^[a-zA-Z|\s]{2,20}$/),//英文姓名
  _RegName=new RegExp(/^[\u4e00-\u9fa5 ]{2,10}$|^[a-zA-Z|\s]{2,20}$/),//中英文姓名
  _RegNick=new RegExp(/^[\w|\d|\u4e00-\u9fa5]{4,15}$/),//昵称
  _RegSFZ=new RegExp(/^[11|12|13|14|15|21|22|23|31|32|33|34|35|36|37|41|42|43|44|45|46|50|51|52|53|54|61|62|63|64|65|71|81|82|91]\d{16}[0-9|x|X]$/),//身份证
  _RegHZ=new RegExp(/^1[45][0-9]{7}|G[0-9]{8}|P[0-9]{7}|S[0-9]{7,8}|D[0-9]+$/),//护照
  _Reg6Number=new RegExp(/^\d{6}$/),//短信或邮箱6位验证码
  _RegNum=new RegExp(/^\d+$/),//纯数字>0
  _RegPrice=new RegExp(/^\d{0,}(\.\d{1,2})?$/),//货币
  _RegYZM=new RegExp(/^[0-9a-zA-Z]{4}$/),//图形验证码
  _RegHBH=new RegExp(/^[A-Z]{2}\d{3,4}$/);//航班号
  _RegYZBM = new RegExp(/^[0-9]\d{5}$/);//邮政编码

   $(function() {
     var fix_menu = $(".fix_menu");
     if (fix_menu.length) {
       fix_menu.find(".i5").on('click',function(){
         var title = title ? title : $("title").text(),
           url = url ? url : _HrefLocation;
         try {
           window.external.addFavorite(url, title);
         } catch (e) {
           try {
             window.sidebar.addPanel(url, title, "");
           } catch (e) {
             layer.msg("抱歉，您所使用的浏览器无法完成此操作。\n\n加入收藏失败，请使用Ctrl+D进行添加");
           }
         }
     });
       fix_menu.find(".i7").on('click',function(){
         W_ScrollTo();
     });
       /*百度分享star*/
       $(".share").append('<div class="baidushare"><div class="bdsharebuttonbox"><a title="分享到微信" href="#" class="bds_weixin" data-cmd="weixin"></a><a title="分享到新浪微博" href="#" class="bds_tsina" data-cmd="tsina"></a><a title="分享到百度贴吧" href="#" class="bds_tieba" data-cmd="tieba"></a><a title="分享到QQ好友" href="#" class="bds_sqq" data-cmd="sqq"></a><a title="分享到QQ空间" href="#" class="bds_qzone" data-cmd="qzone"></a><a title="分享到腾讯微博" href="#" class="bds_tqq" data-cmd="tqq"></a><a title="分享到人人网" href="#" class="bds_renren" data-cmd="renren"></a></div></div>');
       var share = {bdTitle:$("h1").text(), bdPic:"http://ceshi.57us.com/Images/common/erweima.png", bdUrl:_HrefLocation};
       if($("#shareMSG").length){
         try{
           share = $.extend({},share,$.parseJSON($("#shareMSG").text()));
         }catch(e){
           //console.log(e);
         }
       }
       window._bd_share_config = {
         "common": {
           "bdSnsKey": {
             "tsina": "505682229"
           },
           "bdText": "#我去美国#" + share.bdTitle,
           'bdDes': share.bdDes, //'请参考自定义分享摘要'
           "bdMini": "2",
           'wbUid': '505682229', //'请参考自定义微博 id'
           "bdUrl": share.bdUrl,
           "bdMiniList": false,
           "bdPic": share.bdPic,
           'searchPic': 0,
           "bdStyle": "0",
           "bdSize": $(".share").attr("data") ? $(".share").attr("data") : "24"
         },
         "share": {},
         /*"image": {
           "viewList": ["tieba", "sqq", "weixin", "qzone", "tsina", "tqq", "renren"],
           "viewText": "分享到：",
           "viewType": "list",
           "viewSize": "16"
         },*/
//       "selectShare": {
//         "bdContainerClass": null,
//         "bdSelectMiniList": ["weixin", "tsina", "tieba", "sqq", "qzone", "tqq", "renren"]
//       }
       };
       with(document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];
       /*百度分享end*/
     }
   });
//获取url中"?"符后的字串
function W_GetRequest(url) {
  url=url||location.search;
  var theRequest=new Object();
  if (url.indexOf("?") !=-1) {
    var str=url.substr(1);
    strs=str.split("&");
    for (var i=0; i < strs.length; i++) {
      theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
    }
  }
  return theRequest;
}



//input正则验证结果提示 以及一些常用验证工具
function W_formatTips(dom,isCur,msg){
  $(dom).addClass(isCur?'curr_input':'erro_input').removeClass(isCur?'erro_input':'curr_input').parent().find("span").remove();
  if(!isCur){
    $(dom).parent().append('<span class="erro_tip">'+msg+'</span>');
  }
}
function W_formatLayerTips(dom,isCur,msg){
  $(dom).parent().find("span").remove();
  if(isCur){
    $(dom).removeClass("errorInput");
  }else{
    $(dom).addClass("errorInput");
    layer.tips(msg, dom, {
        tips: 1
    });
  }
}
$.fn.W_BlurFomat=function(thisReg,msg,fun){
  var _canNull=this[0].className?(this[0].className.search("_CanNull")+1):0;
  this.on("blur",function(){
    var testBoolearn,v=this.value.replace(/(^\s*)|(\s*$)/g,"");
    if(!v){
      if(fun){
        fun(this,_canNull,'不能为空');
      }else{
        W_formatTips(this,_canNull,'不能为空');
      }
    }else{
      testBoolearn=thisReg.test(v);
      if(fun){
        fun(this,testBoolearn,msg);
      }else{
        W_formatTips(this,testBoolearn,msg);
      }
    }
});
  // console.log(this[0].value);
  if (this[0].value) {
    $(this).trigger('blur');
  }
};
$.fn.W_Format=function(fun){
  $(this).each(function(){
    var msg,thisReg,thisName=this.name||'';
    if(thisName!==''){
      if (_DomJson[thisName]) {
        if(_DomJson[thisName][1]){
          _DomJson[thisName].push(this);
        }else{
          _DomJson[thisName] = [_DomJson[thisName],this];
        }
      }else{
        _DomJson[thisName]=this;
      }
    }
    thisName=thisName.toLowerCase();
    if (this.pattern) {
      thisReg=eval(this.pattern);
      if (thisReg) {
        msg=this.getAttribute('data-msg')||"信息有误";
      }
    }else if(thisName.search('nameen')>=0){
      thisReg=_RegNameEN;
      msg='请输入真实的英文名字';
    }else if(thisName.search('pinyin')>=0){
        thisReg=_RegNameEN;
        msg='请输入正确的拼音';
    }else if(thisName.search('pyname')>=0){
        thisReg=_RegNameEN;
        msg='请输入正确的拼音姓名';
    } else if(thisName.search('namezh')>=0){
      thisReg=_RegNameZH;
      msg='请输入真实的中文名字';
    }else if(thisName.search('name')>=0){
      thisReg=_RegName;
      msg='请输入真实的名字';
  }else if(thisName.search('phone')>=0||thisName.search('tel')>=0){
      thisReg=_RegPhone;
      msg='手机号码格式错误';
  }else if(thisName.search('mail')>=0){
      thisReg=_RegMail;
      msg='邮箱格式错误';
    }else if(thisName.search('image')>=0){
      thisReg=_RegYZM;
      msg='图形验证码格式错误';
  }else if(thisName.search('postcode')>=0||thisName.search('zipcode')>=0){
      thisReg=_RegYZBM;
      msg='邮政编码格式错误';
  }else if(thisName.search('pass')>=0){
      thisReg=_RegPassWord;
      msg='密码长度为8-16位,且不能为纯数字或字母';
    }else if(this.className.search("_CantNull")+1){
      thisReg=new RegExp(/\S/);
    }else if(thisName.search('price')>=0){
      thisReg=_RegPrice;
      msg='价格格式不正确';
    }else if(thisName.search('nick')>=0){
      thisReg=_RegNick;
      msg='4-15位的中文,字母,数字或下划线';
    }else{
      return;
    }
    $(this).W_BlurFomat(thisReg,msg,fun);
});
};
//修改验证码
function W_modifyImageCode(fun){
  $(".img_yzm").attr('src','/code/pic.jpg?'+Math.random())
  _DomJson.ImageCode.value=null;
  fun = fun||W_formatTips;
  fun(_DomJson.ImageCod,0);
}
//将form中的值转换为键值对。
function W_getFormJson(frm) {
    var o={};
    var a=$(frm).serializeArray();
    $.each(a, function () {
        if (o[this.name] !==undefined) {
            if (!o[this.name].push) {
                o[this.name]=[o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name]=this.value || '';
        }
    });
    return o;
}

// 常用工具
$.fn.W_MidImg=function(options) {
    var opts=$.extend({},{
        "width":null,
        "height":null
    },options);
    return $(this).each(function() {
        var $this=$(this);
        var objHeight=$this.height(); //图片高度
        var objWidth=$this.width(); //图片宽度
        var parentHeight=opts.height||$this.parent().height(); //图片父容器高度
        var parentWidth=opts.width||$this.parent().width(); //图片父容器宽度
        if (objHeight/objWidth>parentHeight/parentWidth) {
            $this.width(parentWidth);
            $this.height(parentWidth*objHeight/objWidth);
            $this.css("margin-top", (parentHeight - $this.height()) / 2);
        }else{
            $this.height(parentHeight);
            $this.width(parentHeight*objWidth/objHeight);
            $this.css("margin-left", (parentWidth - $this.width()) / 2);
        }
    });
};
$.fn.W_NumberBox=function(options,fun,errFun) {
    var opts=$.extend({},{
        "readonly":true,
        "min":0,
        "max":99
    },options);
    return $(this).each(function() {
        var _this=this;
        var ipt=this.getElementsByTagName('input')[0],
            btn=this.getElementsByTagName('a');
        var n=Number(ipt.value);
        if(opts.readonly){
            $(ipt).attr("readonly","readonly");
        }
        if (n<opts.min||n>opts.max) {
            n=opts.min;
            ipt.value=n;
        }
        if($(ipt).attr("initnum")){
            ipt.value = Number($(ipt).attr("initnum"));
            n = Number($(ipt).attr("initnum"));
        }
        //双击A标签不选定
        var _btn = $(btn);
        _btn.attr('unselectable','on');
        _btn.attr('onselectstart','return false;');
        _btn.css('-moz-user-select','none');
        _btn.css('-webkit-user-select','none');
        _btn.css('-khtml-user-select: none;','none');
        _btn.css('-ms-user-select: none; ','none');
        _btn.each(function(ii,tt){
            tt.onclick=function(){
                var newN=n;
                ii?(newN++):(newN--);
                if (!(newN<opts.min||newN>opts.max)) {
                    if(fun(newN,_this)){
                      n=newN;
                      ipt.value=n;
                    }else if(errFun){
                      errFun(newN,this);
                    }
                }
            };
        });
    });
};
// 页面滚动到顶部或某个部件
function W_ScrollTo(dom, headspace) {
  var speed=50,
    finishAbs=speed/2+1,
    ScrollToTop=dom ? $(dom).offset().top : 0,
    ScrollTop=document.body.scrollTop;
  if (headspace) {
    ScrollToTop -=headspace;
}
  $(window).scrollTop(ScrollToTop);
  /*if (ScrollToTop ==ScrollTop) {
    return
  }
  if (ScrollToTop < ScrollTop) {
    speed=-speed
  }
  var scrollTime=setInterval(function() {
    ScrollTop +=speed;
    if (Math.abs(ScrollTop - ScrollToTop) < finishAbs) {
      document.body.scrollTop=ScrollToTop
      clearInterval(scrollTime)
    } else {
      document.body.scrollTop=ScrollTop;
    }
  }, 20)*/
}
// 上传图片功能 一张
$.fn.W_UploadImg1=function(fun){
  $(this).each(function(ii,tt){
    var PapersUp=new plupload.Uploader({
      browse_button: tt, //触发文件选择对话框的按钮，为那个元素id
      url: '/Controller/ZuFang/upload.php',//ajaxUrl + "?Intention=ReleaseInfo", //服务器端的上传页面地址
      flash_swf_url: 'Moxie.swf', //swf文件，当需要使用swf方式进行上传时需要配置该参数
      silverlight_xap_url: 'Moxie.xap', //silverlight文件，当需要使用silverlight方式进行上传时需要配置该参数
      multi_selection:false,
      filters: {
        mime_types: [ //只允许上传图片文件
          {
            title: "图片文件",
            extensions: "jpg,gif,png"
          }
        ]
      },
      max_file_size : '5000kb', //最大只能上传400kb的文件
      prevent_duplicates : true,
    });
    PapersUp.init();
    PapersUp.bind('FilesAdded', function(uploader, files) {
      ImgTo64(files[0],function(imgsrc){
        if (fun) {
          fun(imgsrc,tt);
        }
    });
      if (uploader.files.length>1) {
        PapersUp.removeFile(uploader.files[0].id);
      }
  });
    /*var uplDom=$("<input type='file' style='display:none' accept>").insertAfter(this).on("change",function(){
      // console.log(this.value)
      // console.log(this.value.match(new RegExp(/[jpe|jpeg|jpg|png]/)))
      if (this.value.search(new RegExp(/[jpe|jpeg|jpg|png]$/))+1) {
        //console.log(this.value)
        //console.log(tt.getElementsByTagName("img"))
        tt.getElementsByTagName("img")[0].src=this.value;
        tt.style.backgroundImg=this.value;
      }else{
        //console.log("cuo")
      }
    })
    $(this).on('click',function(){
      uplDom.trigger("click");
      //console.log('shangchuan')
    })*/
});
};
function W_DomJsonBlur(domObj){
  domObj = domObj||_DomJson;
  $.each(domObj,function(ii,tt){
    $(tt).trigger('blur');
});
}
/*降频方法,如scroll, resize等方法需要使用*/
function W_debounce(func, wait, immediate) {
  var timeout;
  return function() {
    var context = this, args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}
/** * 对Date的扩展，将 Date 转化为指定格式的String * 月(M)、日(d)、12小时(h)、24小时(H)、分(m)、秒(s)、周(E)、季度(q)
    可以用 1-2 个占位符 * 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字) * eg: * (new
    Date()).H_format("yyyy-MM-dd hh:mm:ss.S")==> 2006-07-02 08:09:04.423
 * (new Date()).H_format("yyyy-MM-dd E HH:mm:ss") ==> 2009-03-10 二 20:09:04
 * (new Date()).H_format("yyyy-MM-dd EE hh:mm:ss") ==> 2009-03-10 周二 08:09:04
 * (new Date()).H_format("yyyy-MM-dd EEE hh:mm:ss") ==> 2009-03-10 星期二 08:09:04
 * (new Date()).H_format("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18
 */
Date.prototype.H_format=function(fmt) {
    var o = {
    "M+" : this.getMonth()+1, //月份
    "d+" : this.getDate(), //日
    "h+" : this.getHours()%12 === 0 ? 12 : this.getHours()%12, //小时
    "H+" : this.getHours(), //小时
    "m+" : this.getMinutes(), //分
    "s+" : this.getSeconds(), //秒
    "q+" : Math.floor((this.getMonth()+3)/3), //季度
    "S" : this.getMilliseconds() //毫秒
    };
    var week = {
    "0" : "/u65e5",
    "1" : "/u4e00",
    "2" : "/u4e8c",
    "3" : "/u4e09",
    "4" : "/u56db",
    "5" : "/u4e94",
    "6" : "/u516d"
    };
    if(/(y+)/.test(fmt)){
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
    if(/(E+)/.test(fmt)){
        fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "/u661f/u671f" : "/u5468") : "")+week[this.getDay()+""]);
    }
    for(var k in o){
        if(new RegExp("("+ k +")").test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
    return fmt;
};

//临时借用
if($(".mobile-num").length){HoverTip()}
function HoverTip(){
	$(".mobile-num").each(function(){
		$(this).hover(function(){
			  var MobileHtml = $(this).attr("data-num")
				layer.tips(MobileHtml, $(this), {
				  tips: [1, '#3595CC'],
				  time: 400000,
				  skin: 'HoverTip'
				});
		},function(){
			layer.closeAll();
		})
	})

}
