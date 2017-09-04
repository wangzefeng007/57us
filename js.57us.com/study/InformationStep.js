//自定义下拉
$('.DiySelect').inputbox({
        height:41,
        width:122
  });
//城市联动
comSelect();
selectCity();

 //服务标签提示
 $(".tipIco").hover(function(){
 	var content=$(this).attr("data-text")
 	layer.tips(content,$(this), {
 	  skin: 'OrderTip',
	  tips: [1, '#fff'],
	  time: 400000
	});
 },function(){
	layer.closeAll();
 })
  //性别选择
$('.labelList label').inputbox();
