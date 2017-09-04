$(function(){
	jQuery(".TourBan").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:600, trigger:"click"});
	jQuery(".IndexBox").slide({ titCell:".hd a",delayTime:0 });
	$(".ClassShowLi").hover(function(){
		$(this).siblings().addClass("no");
	},function(){
		$(this).siblings().removeClass("no");
	})
	$(".IndexBox .more").hover(function(){
		$(this).find(".MoreTitle").addClass("slideInRight");
		$(this).find(".MoreIcon").addClass("slideInLeft")
	},function(){
		$(this).find(".MoreTitle").removeClass("slideInRight");
		$(this).find(".MoreIcon").removeClass("slideInLeft")
	})
	
	//显示快速导航
	speedMenu();

})

function speedMenu() {
	var thisDom = $(".SpeedMenu");
	var num1 = $(".IndexBox").first().offset().top;
	var num2 = $("#num2").offset().top - 250;
	if($(this).scrollTop() < num1) {
		thisDom.hide();
	}
	if($(this).scrollTop() < num2) {
		thisDom.hide();
	}
	$(window).scroll(function() {
		if($(this).scrollTop() < num1) {
			thisDom.hide();
		}
		if($(this).scrollTop() > num1) {
			thisDom.show();
		}
		if($(this).scrollTop() > num2) {
			thisDom.hide();
		}
	});
}