jQuery(".baner").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:600,interTime:4000, trigger:"click"});
jQuery(".FaqMain").slide({ mainCell:".pic",effect:"left", autoPlay:true, delayTime:800,interTime:7000, trigger:"click",endFun:function(i,c){
	var _this = $(".FaqMain li").eq(i).find(".faqList");
	var _thisChang =$(".FaqMain li").eq(i-1).find(".faqList");
	_this.each(function(){
		var marginL =$(this).attr("data-left");
		var trans=$(this).attr("data-load")
		$(this).css({"margin-left":-marginL+'px',"transition-delay":trans+"s"});
	});
	if(i==0){
		$(".FaqMain li").eq(1).find(".faqList").css({"margin-left":1000+'px'});
		$(".FaqMain li").eq(2).find(".faqList").css({"margin-left":1000+'px'});
	}else if(i == 1){
		$(".FaqMain li").eq(0).find(".faqList").css({"margin-left":1000+'px'});
		$(".FaqMain li").eq(2).find(".faqList").css({"margin-left":1000+'px'});
	}else if(i == 2){
		$(".FaqMain li").eq(0).find(".faqList").css({"margin-left":1000+'px'});
		$(".FaqMain li").eq(1).find(".faqList").css({"margin-left":1000+'px'});
	}else{
		$(".FaqMain li").eq(0).find(".faqList").css({"margin-left":1000+'px'});
		$(".FaqMain li").eq(1).find(".faqList").css({"margin-left":1000+'px'});
		$(".FaqMain li").eq(2).find(".faqList").css({"margin-left":1000+'px'});
	}
}});

$(function() {

	$(".onlineChat").click(function () {
		$(".NfixMenu .i1").trigger("click");
	});

	$("#username").on("input change", function() {
		var a = $(this).val();
		$(this).attr('value', a);
	});
	$("#mail").on('input change', function() {
		var b = $(this).val();
		$(this).attr('value', b);
	})
	$("#phone").on('input change', function() {
		var c = $(this).val();
		$(this).attr('value', c);
	})

	$(".ContactBtn").on('click', function() {
		var username = $("#username").val();
		var mail = $("#mail").val();
		var phone = $("#phone").val();
		if(username == "") {
			layer.msg("请输入姓名(10位以内)");
			return
		} else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,8})+$/i.test(mail)) {
			layer.msg("邮箱格式不正确");
			return
		} else if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(phone)) {
			layer.msg("请输入正确手机号码");
			return
		}
		host = window.location;
		$.ajax({
			//请求方式为get
			type: "post",
			//json文件位置
			url: '/topic/lecture/',
			data: {
				'Name': username,
				'Email': mail,
				'Tel': phone,
			},
			//返回数据格式为json
			dataType: "json",
			//请求成功完成后要执行的方法
			success: function(data) {
				if(data.ResultCode == 200) {
					layer.msg('提交成功'),
						setTimeout(function(){window.location=host;},1200);
				} else if(data.ResultCode == 101) {
					layer.msg('信息填写错误！');
				}
			}
		});
	});

})
