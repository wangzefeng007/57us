$(function(){
	//tab切换
	jQuery(".ImmBox").slide({ titCell:".hd a",mainCell:".bd"});
	//pic滚动
	jQuery(".focusBox").slide({ titCell:".num li", mainCell:".pic",prevCell:".sprev",nextCell:".snext",effect:"fold", autoPlay:true,trigger:"click"});
})
