$(function(){
	PublicPay()
	
})
function PublicPay(){
	$(".PayStyleBox .hd a").click(function(){
		var num = $(this).index();
		$(this).addClass("on").siblings().removeClass("on");
		$(this).parent().siblings(".bd").find(".BdTab").hide().eq(num).show()
	})
}
