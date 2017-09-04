$(function(){
	remfix();
	
	//safari浏览器可以通过此代码来隐藏地址栏
	window.addEventListener('load', function(){
   		setTimeout(function(){ window.scrollTo(0, 1); }, 100);
	});
	//大图切换
	TouchSlide({ 
		slideCell:"#focus",
		titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
		mainCell:".bd ul", 
		effect:"left", 
		autoPlay:true,//自动播放
		autoPage:true, //自动分页
		switchLoad:"_src" //切换加载，真实图片路径为"_src" 
	});
	
//	//导航弹出
//	$(".menu i").bind('touchend',function(){
//		$(this).parent().addClass("on");
//		$(".op").show();
//	})
//	$(".sunav li").bind("touchend",function(){
//		$(this).parents(".menu").removeClass("on");
//		$(".op").hide();
//	})
})

//自适应字体
function remfix (minwidth,size) {
	if (!minwidth) { minwidth = 320; };
	if (!size) { size = 20; };
	var width = $(window).width();
	width = minwidth>width?minwidth:width;
	width = 640<width?640:width;
	//计算目标值大小,并赋给html的字体
	var tosize =  size*width/minwidth;
	tosize = tosize<40?tosize:40;
	$("html").css("font-size",tosize);
	window.onresize = function(){
		rem = remfix(minwidth,size);
	}
	return tosize;
}

