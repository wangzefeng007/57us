$(function(){
	//大图
	jQuery(".ban").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:600, trigger:"click"});
	//
	scrollFix();
	$(".FixContact .close").click(function(){
		$(this).parent().removeClass("on");
	})
})
function scrollFix(){
	$(window).scroll(function(){
		if($(window).scrollTop()>2900&&$(window).scrollTop()<5700){
			$(".FixContact").addClass("on");
		}
		else{
			$(".FixContact").removeClass("on");
		}
	})
	
}
