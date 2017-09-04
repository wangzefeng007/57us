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
	
	//点击喜欢按钮
	// $(".listRight .icon").click(function(){
	// 	$(this).toggleClass("on")
	// });
	//点击更多标签
	$(document).on('click','.listCont .userFace .more',function(){
		$(this).parent().toggleClass("auto");
	});
	//弹出二维码
	$("#appendActive").click(function(){
		$(".popMask").show()
	});
	$(".popclose").click(function(){
		$(".popMask").hide()
	});
	//分享给好朋友
	$("#invite").click(function(){
		$(".shareBox").show()
	});
	$(".shareBox").click(function(){
		$(this).hide()
	});
	//点赞ajax提交
	$(".listRight .icon").on('click',function () {
		//是否点过赞
		var _type = $(this).attr('data-type');
		//点赞id
		var _id = $(this).attr('data-id');
		var _this = $(this);
		var ajaxData = {
			'TagID':_id,
		}
		//如果等于1表示未点赞，提交ajax点赞   否则弹出提示
		if(_type == '1'){
			$.post('/tag/likeoperate/',ajaxData,function (data) {
                if (data.ResultCode == "200") {
                    _this.addClass('on');
                    _this.attr('data-type','2');
					_this.parent().find('em').text(data.Count);
					_this.parent().prev().find('p').eq(1).attr('class','userFace');
					_this.parent().prev().find('.userFace').empty();
                    var item;
                    $.each(data.ImgArray, function(){
                        item = '<i><img src="'+this+'"></i>';
                        _this.parent().prev().find('.userFace').append(item)
                    });
                    if(data.Count > 5){
                        _this.parent().prev().find('.userFace').append('<a href="JavaScript:void(0)" class="more morem"></a>');
					}
                    layer.open({
                        content: data.Message,
						skin: 'msg',
						time: 1 //2秒后自动关闭
                    });
                }else {
                    layer.open({
                        content: data.Message,
                        skin: 'msg',
                        time: 1 //2秒后自动关闭
                    });
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
				}
            },'json')
		}else {
            layer.open({
                content: '您已经赞同过此标签',
				skin: 'msg',
				time: 1 //2秒后自动关闭
            });
		}
    })
})
