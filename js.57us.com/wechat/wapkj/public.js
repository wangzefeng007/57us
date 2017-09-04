$(function(){
	//字体
	var rem = remfix();
	function remfix (minwidth,size) {
		if (!minwidth) { minwidth = 320; };
		if (!size) { size = 20; };
		var width = $(window).width();
		width = minwidth>width?minwidth:width;
		width = 640<width?640:width;
		var tosize =  size*width/minwidth;
		tosize = tosize<30?tosize:30;
		$("html").css("font-size",tosize);
		window.onresize = function(){
			rem = remfix(minwidth,size);
		}
		return tosize;
	}
	//活动详情
	$(".ruleBtn").click(function(){
		$(".Popmask").show();
	});
	$(".PopClose").click(function(){
		$(".Popmask").hide();
	});
	//邀请好友
	$(".inviteBtn").click(function(){
		$(".shareBox").show();
	});
	$(".shareBox").click(function(){
		$(this).hide()
	})
	//返回顶部

    $('.conter').scroll(function() {
        if($(this).scrollTop() < 150) {
            $(".backTop").removeClass("on");
        }else {
            $(".backTop").addClass("on");
		}
    });

	$(".backTop").click(function(){
		$(".conter").scrollTop(0);
	})
	
})
