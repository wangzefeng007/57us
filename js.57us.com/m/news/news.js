$(function(){
	var swiper = new Swiper('.ban', {
		pagination: '.swiper-pagination',
		paginationClickable: true,
		autoHeight: true, //enable auto height
	});
	//导航下拉
	$(".downBox").click(function(){
		$(this).parent().toggleClass("on");
	})
 
	//显示返回顶部
	$(".detailCont").on('scroll',function(){
		if($(".detailCont").scrollTop()>$(".detailBody").offset().top){
	        $(".fixBackTop").show();
	    }
	    else{
	        $(".fixBackTop").hide();
	    };
	})
	$(".fixBackTop").on("click",function(){
		$(".detailCont").scrollTop(0,500)
	})
    

})
