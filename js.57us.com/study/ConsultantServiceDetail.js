/**
 * Created by Foliage on 2016/9/23.
 */
$(function () {
	//详细也大图滚动
	jQuery(".DePicScroll").slide({ mainCell:".pic",effect:"fold", delayTime:600,pnLoop:false});

	//固定右侧
	$(".autofix_sb").autofix_anything();

	//锚点滚动
	ScrollFixed();

	//服务列表
	$("#servic li").last().addClass('last'); //服务最后一条添加last
	$("#servic li:eq(2)").addClass('last'); //初始化第三条添加last
	var serviclen = $("#servic li").length;
	if(serviclen >3){
		$("#servicemore").show();
	}else {
		$("#servicemore").hide();
	}
	$("#servicemore").on('click',function () {
		$("#servic li").slideDown(500); //点击更多，从上到下显示剩余的条数
		$("#servicemore").hide(); //点击更多，隐藏更div
		$("#servic li:eq(2)").removeClass('last'); //点击更多时第三条移除last
	})
	
	$(".NowBuyBtn").click(function () {
		var ProductId = $(this).attr('data-id');
		$("#OrdersBtn").text('下单中...');
		$("#OrdersBtn").addClass('course');
		$("#OrdersBtn").attr('id','');
		location.href="/order/service/?ID="+ProductId;
	})
	
})

//锚点滚动方法
function ScrollFixed() {
	var naviTop = jQuery(".contMenu ul").offset().top;
	jQuery('.contMenu li').click(function() {
		var $dayLi = jQuery(this).index();
		var dInfor = jQuery(".contBox").eq($dayLi).offset().top - 70;
		jQuery('html, body').animate({
			scrollTop: dInfor
		}, 5);
	});

	function checkScroll(forcon, forli, wtop) {
		var next = forcon.size() - 1;
		while(next > -1) {
			var itemTop = forcon.eq(next).offset().top-70;
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
			$(".contMenu ul").removeClass("fixnav");
		} else {
			$(".contMenu ul").addClass("fixnav");
		}
		checkScroll(jQuery('.contBox'), jQuery('.contMenu li'), wintop);

	});

}