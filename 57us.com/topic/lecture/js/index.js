jQuery(".baner").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:600,interTime:4000, trigger:"click"});
jQuery(".FaqMain").slide({ mainCell:".pic",effect:"left", autoPlay:true, delayTime:600,interTime:4000, trigger:"click",endFun:function(i,c){
//	var this=$(".FaqMain li").eq(i).find(".faqList");
//	this.each(function(){
//		$(this).addClass("hi")
//	})
//	$(".FaqMain li").eq(i).find(".faqList").css({"left":$(this).attr("data-left")+'px'})
}});

