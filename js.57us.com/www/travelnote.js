$(function(){
	
	$("img.lazy").each(function(){
		$(this).delayLoading({
			defaultImg: "http://images.57us.com/img/common/loadpic.jpg",           // 预加载前显示的图片
			errorImg: "http://images.57us.com/img/common/loadpic.jpg",                        // 读取图片错误时替换图片(默认：与defaultImg一样)
			imgSrcAttr: "data-original",           // 记录图片路径的属性(默认：originalSrc，页面img的src属性也要替换为originalSrc)
			beforehand:100,                       // 预先提前多少像素加载图片(默认：0)
			event: "scroll",                     // 触发加载图片事件(默认：scroll)
			duration: "normal",                  // 三种预定淡出(入)速度之一的字符串("slow", "normal", or "fast")或表示动画时长的毫秒数值(如：1000),默认:"normal"
			container: window,                   // 对象加载的位置容器(默认：window)
			success: function (imgObj) { 
				$('.WaterFall').BlocksIt({
					numOfCol:4,
					offsetX:10,
					offsetY: 10,
					blockElement: "li"
				});
				setTimeout(function(){imgObj.parent().addClass("ImgBlackBg")},1000)
			},      // 加载图片成功后的回调函数(默认：不执行任何操作)
			error: function (imgObj) { }         // 加载图片失败后的回调函数(默认：不执行任何操作)
	});
	
	$(window).scroll(function(){
			// 当滚动到最底部以上50像素时， 加载新内容
			if ($(document).height() - $(this).scrollTop() - $(this).height()<250){
				appendTo();
				$('.WaterFall').BlocksIt({
					numOfCol:4,
					offsetX:10,
					offsetY: 10,
					blockElement: "li"
				});
				
		}
	});
function appendTo(){
	var data = {
      	       "data": [{ "src": "http://www.jq22.com/demo/jQuery-pbl20160309/images/1.jpg","title":"我是在测试1","cont":"秋色和行程路原来的帖里介绍过" },{ "src": "http://www.jq22.com/demo/jQuery-pbl20160309/images/2.jpg","title":"我是在测试2","cont":"秋色和行程路原来的帖里介绍过绍过2" },{ "src": "http://www.jq22.com/demo/jQuery-pbl20160309/images/3.jpg","title":"我是在测试3","cont":"秋色和行程路原来的帖里介绍过3" },{ "src": "http://www.jq22.com/demo/jQuery-pbl20160309/images/4.jpg","title":"我是在测试4","cont":"秋色和行程路原来的帖里介绍过4秋色和行程路原来的帖里介绍过4" },{ "src": "http://www.jq22.com/demo/jQuery-pbl20160309/images/5.jpg","title":"我是在测试5","cont":"秋色和行程路原来的帖里介绍过5" },{ "src": "http://www.jq22.com/demo/jQuery-pbl20160309/images/6.jpg","title":"我是在测试6","cont":"秋色和行程路原来的帖里介绍过5秋色和行程路原来的帖里介绍过6" }],
     	       };
    var str = "";
    var templ = '<li><p class=" ImgBlackBg"><a href=""><img src="http://images.57us.com/img/common/loadpic.jpg" data-original="{{src}}" width="100%" class="transition lazy"/></a></p><div class="WaterFallM"><p class="tit"><a href="">{{title}}</a></p><p class="nr">{{cont}}</p><p class="fun mt10"><span class="map fl"><i></i>纽约</span><span class="view fr"><i></i>593</span></p></div><div class="WaterFallB cf"><span class="fl">by:Karen</span><span class="fr">2016-05-02</span></div></li>'
    for(var i = 0; i < data.data.length; i++) {
         str += templ.replace("{{src}}", data.data[i].src).replace("{{title}}", data.data[i].title).replace("{{cont}}", data.data[i].cont);
    }
    $(str).appendTo($(".WaterFall"));
}
});