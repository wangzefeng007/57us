$(function(){
	//safari浏览器可以通过此代码来隐藏地址栏
	window.addEventListener('load', function(){
   		setTimeout(function(){ window.scrollTo(0, 1); }, 100);
	});
	PopEwm();
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
remfix();


//弹出投票关注二维码
function PopEwm(){
	$("#vote").click(function(){
		$(".attentionPop").show();
		$(".op").show()
	});
	$(".op").click(function(){
		$(this).hide();
		$(".attentionPop").hide();
	});
}

