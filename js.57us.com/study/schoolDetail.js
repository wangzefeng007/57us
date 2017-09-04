//图片展示
if($(".schoolScroll").length){
	jQuery(".schoolScroll").slide({mainCell:".scrollPic ul",titCell:".hd span",interTime:4000,autoPlay:true});
}
//固定右侧
if($(".autofix_sb").length>0){
	$(".autofix_sb").autofix_anything();
}
ScrollFixed();
//锚点滚动
function ScrollFixed() {
	var naviTop = jQuery(".SchoolDetailLeft").offset().top-170;
	jQuery('.slideFixMenu li').click(function() {
		var $dayLi = jQuery(this).index();
		var dInfor = jQuery(".contBox").eq($dayLi).offset().top;
		jQuery('html, body').animate({
			scrollTop: dInfor
		}, 5);
	});

	function checkScroll(forcon, forli, wtop) {
		var next = forcon.size() - 1;
		while(next > -1) {
			var itemTop = forcon.eq(next).offset().top;
			if(wtop >= itemTop) {
				forli.eq(next).addClass("on").siblings().removeClass("on");
				return false;
			}
			next--;
		};
	}
	jQuery(window).scroll(function() {

		var wintop = jQuery(window).scrollTop();
		if(naviTop >= wintop) {
			$(".slideFixMenu").removeClass("fixnav");
		} else {
			$(".slideFixMenu").addClass("fixnav");
		}
		checkScroll(jQuery('.contBox'), jQuery('.slideFixMenu li'), wintop);

	});

}