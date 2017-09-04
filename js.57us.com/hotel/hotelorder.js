$('input').W_Format();
var layerIndex;

function laoding(open) {
	if(open == 1) {
		var load = layer.open({
			type: 1,
			title: false,
			closeBtn: 0,
			shadeClose: false,
			// shade: [0.0001, '#fff'],
			content: '<div">正在查询当前房型库存信息，请稍等！</div>',
			success: function(k, j) {
				layerIndex = j;
			}
		});
		layer.style(load, {
			background: 'rgba(0,0,0,0.8)',
			color: '#fff',
			border: '0px',
			padding: '20px 50px',
			textAlign: 'center'
		});
	} else if(open == 0) {
		layer.msg('很抱歉,该酒店当前房型已满,您可以更换条件重新尝试！');
		layer.close(layerIndex);
	} else if(open == 2) {
		//layer.msg('该房型可预定,为确保顺利,请尽快完成订单！');
		$(".layui-layer-shade").css('display', 'none');
		layer.close(layerIndex);
	} else if(open == 3) {
		var load = layer.open({
			type: 1,
			title: false,
			closeBtn: 0,
			shadeClose: false,
			// shade: [0.0001, '#fff'],
			content: '<div">订单生成中,请稍等！</div>',
			success: function(k, j) {
				layerIndex = j;
			}
		});
		layer.style(load, {
			background: 'rgba(0,0,0,0.8)',
			color: '#fff',
			border: '0px',
			padding: '20px 50px',
			textAlign: 'center'
		});
	} else if(open == 4) {
		layer.msg('超过该房间入住的成人数量！');
		layer.close(layerIndex);
	} else if(open == 5) {
		layer.msg('请填写完整的订单信息！');
		layer.close(layerIndex);
	}

}
//获取初始数据
var initData = new Object;
initData = GetRequest();
$('.cbt,.cbt1').inputbox();

$(".sidebar").autofix_anything();

//修改房间数量
$('.RoomNum .num_btn').each(function(index) {
	$(this).click(function() {
		var min = 1;
		var max = 4;
		var n = parseInt($('input[name="RoomNum"]').val());
		if(index == 0) {
			if(n > 1) {
				n = n - 1;
				OrderNum(function(data) {
					if(data == 1) {
						$('input[name="RoomNum"]').attr('value', n);
					}
				}, n);
			}
		} else {
			if(n < 4) {
				n = n + 1;
				OrderNum(function(data) {
					if(data == 1) {
						$('input[name="RoomNum"]').attr('value', n);
					}
				}, n);
			}
		}
	})
})

//添加联系人
$(document).on('click', '.AddPerson', function() {
	if($(this).parents('.CustomInstr').find('.CustomMan').size() < $(this).parents('.CustomInstr').find('.OccupancyDetails').data('adultcount')) {
		var aa = $(this).parents('.CustomInstr').attr('data-id');
		$(this).parents('.CustomInstr').append($('.PersonBlock').html());
		$(this).parents('.CustomInstr').find('.CustomMan:last').addClass('aa');
		$('input').W_Format();
	} else {
		laoding(4);
	}
});
//删除联系人
$(document).on("click", ".DelPerson", function() {
	$(this).parents('.CustomMan').remove();
});

//修改住处人信息
$(document).on('click', '.RoomNumAdd .ChangeNum', function() {
	var thisDom = $(this).parents('.CustomInstr');
	var adultNum = thisDom.find('.OccupancyDetails').data('adultcount');
	var childrenNum = thisDom.find('.OccupancyDetails').data('childcount');
	var childAge = thisDom.find('.OccupancyDetails').data('childage');
	layer.open({
		type: 1,
		title: false,
		area: '400px',
		skin: 'HotelPopBox', //样式类名
		closeBtn: 0, //不显示关闭按钮
		btn: ['确定', '取消'],
		yes: function(index, layero) {
			thisDom.find('.OccupancyDetails').data('adultcount', adultNum);
			thisDom.find('.OccupancyDetails').data('childcount', childrenNum);

			var AgeArr = [];
			for(var i = 0; i < childrenNum; i++) {
				if(typeof($('.ChildAge').eq(i + 3).find('a.selected').attr('value')) == 'undefined') {
					AgeArr.push(1);
				} else {
					AgeArr.push($('.ChildAge').eq(i + 3).find('a.selected').attr('value'));
				}
			}
			// console.log(AgeArr);
			thisDom.find('.OccupancyDetails').data('childage', AgeArr);
			thisDom.find('.CustomInstrTop').find("p").html('入住人：<span id="adultNum">' + adultNum + '</span>成人，<span id="childrenNum">' + childrenNum + '</span>儿童');
			var bbb = adultNum + childrenNum - 1;
			thisDom.each(function() {
				var aaaa = $(this).find('.aa').length;
				if(bbb < aaaa) {
					thisDom.find('.aa:last').remove();
					thisDom.each(function() {
						var bbbb = $(this).find('.aa').length;
						if(bbb < bbbb) {
							thisDom.find('.aa:last').remove();
						}
					});
					thisDom.each(function() {
						var cccc = $(this).find('.aa').length;
						if(bbb < cccc) {
							thisDom.find('.aa').remove();
						}
					});
				} else if(bbb >= aaaa) {
					$(this).find('.aa').length;
				}
			})
			layer.close(index);
		},
		btn2: function(index, layero) {

		},
		shift: 2,
		shadeClose: true, //开启遮罩关闭
		content: $(".HotelPop[name='PopNum']").html(),
		success: function(layero, index) {
			$('.HotelPopBox input[name="adultNum"]').val(adultNum);
			$('.HotelPopBox input[name="childrenNum"]').val(childrenNum);
			$(".ChildAge").addClass("hidden");
			$(".ChildAge").find('a').removeClass('selected');
			for(var i = 0; i < childrenNum; i++) {
				$(".ChildAge").eq((3 + i)).removeClass("hidden");
				$(".ChildAge").eq((3 + i)).find('.opts a').eq(childAge[i] - 1).addClass('selected');
			}
			$('.diyselect').inputbox({
				height: 24,
				width: 90
			});
			$('.adultNum').W_NumberBox({
				'max': 4,
				'min': 1
			}, function(n) {
				if(n + childrenNum <= 4) {
					adultNum = n;
					return true;
				} else {
					layer.msg('成人加儿童最多只能入住4人');
				}
			});
			$('.childrenNum').W_NumberBox({
				'max': 3,
				'min': 0
			}, function(n) {
				if(adultNum + n <= 4) {
					childrenNum = n;
					$(".ChildAge").addClass("hidden");
					$(".ChildAge:lt(" + (n + 3) + ")").removeClass("hidden");
					return true;
				} else {
					layer.msg('成人加儿童最多只能入住4人');
				}
			});
		},
		end: function() {
			$(".ChildAge input[name='age']").remove();
			$(".ChildAge .sb_icon").remove();
			$(".ChildAge .selected").remove();

		}
	});
})

//创建订单
$('.NextOrderBtn').click(function() {
	$('.RoomNumDe input').blur();
	$('.ContactIns input').blur();
	if($('.erro_tip').size() < 1) {
		var n = parseInt($('input[name="RoomNum"]').val());
		var OccupancyDetails = [];
		var GuestList = [];
		for(var i = 0; i < n; i++) {
			var RoomObj = $(".RoomNumDe .CustomInstr").eq(i);
			OccupancyDetails.push({
				AdultCount: RoomObj.find('.OccupancyDetails').data('adultcount'),
				ChildCount: RoomObj.find('.OccupancyDetails').data('childcount'),
				ChildAge: RoomObj.find('.OccupancyDetails').data('childage')
			});
			var RoomGuest = [];
			RoomObj.find('.CustomMan').each(function() {
				RoomGuest.push({
					First: $(this).find('.FirstName').val().toUpperCase(),
					Last: $(this).find('.LastName').val().toUpperCase()
				});
			})
			GuestList.push(RoomGuest);
		}
		var ContactObj = $('.ContactIns');
		var ContactInfo = {
			First: ContactObj.find('.FirstName').val().toUpperCase(),
			Last: ContactObj.find('.LastName').val().toUpperCase(),
			Phone: ContactObj.find('.Phone').val(),
			EMail: ContactObj.find('.EMail').val()
		};
		var CustomerRequest = [];
		$('.RoomOtherLi').find('label').each(function() {
			if($(this).hasClass('checked')) {
				CustomerRequest.push($(this).attr('val'));
			}
		})
		laoding(3);
		$.post('/ajaxhotel.html', {
			Intention: 'CreateOrder',
			HotelID: initData.HotelID,
			RatePlanID: initData.RatePlanID,
			CheckInDate: initData.CheckInDate,
			CheckOutDate: initData.CheckOutDate,
			RoomNums: n,
			OccupancyDetails: OccupancyDetails,
			GuestList: GuestList,
			ContactInfo: ContactInfo,
			CustomerRequest: CustomerRequest,
			Remark: ''
		}, function(data) {
			var json = eval('(' + data + ')');
			if(json.ResultCode == '200') {
				window.location = json.Url;
			} else {
				laoding(0);
			}
		})
	} else {
		laoding(5);
	}
})

function GetRequest() {
	var url = location.search; //获取url中"?"符后的字串 
	var theRequest = new Object();
	if(url.indexOf("?") != -1) {
		var str = url.substr(1);
		strs = str.split("&");
		for(var i = 0; i < strs.length; i++) {
			theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
		}
	}
	return theRequest;
}

function OrderNum(callback, n) {
	//var result=true;
	//房间数量
	var OccupancyDetails = [];
	for(var i = 0; i < n; i++) {
		OccupancyDetails.push({
			AdultCount: '2',
			ChildCount: '0'
		});
	}
	laoding(1);
	$.ajax({
		url: "/ajaxhotel.html",
		data: {
			Intention: 'CheckRoomPrice',
			HotelID: initData.HotelID,
			RatePlanID: initData.RatePlanID,
			CheckInDate: initData.CheckInDate,
			CheckOutDate: initData.CheckOutDate,
			RoomNums: n,
			OccupancyDetails: OccupancyDetails
		},
		type: "post",
		async: true,
		dataType: 'json',
		success: function(json) {
			if(json.ResultCode == '200') {
				if($(".RoomNumDe .CustomInstr").size() < n) {
					$(".RoomNumDe").append($(".RoomAddTpl").html());
					$('.RoomNumDe .CustomInstr:last').attr('data-id', 'a' + n);
					$(".RoomNumDe .CustomInstr:last").find('.RoomI').text(n);
				} else {
					$(".RoomNumDe .CustomInstr:last").remove();
				}
				//添加房间数量后价格
				var OrderInfo = eval('(' + json.OrderInfo + ')');
				var AllPrice = OrderInfo.Price;
				$("input#Allprice").val(AllPrice)
				$(".AllPrice").html(AllPrice);
				$(".HotelOrderPrice .price").find("i").html(AllPrice);
				//取消政策
				$('div.CancellationPolicy p:first').siblings('p').remove();
				$('div.CancellationPolicy p:first').after(OrderInfo.CancellationPolicy);
				//添加后的验证
				$('input').W_Format();
				laoding(2);
				callback(1);
			} else {
				laoding(0);
				callback(0);
			}
		}
	});
	//return result;
}

function FixRight() {
	var naviTop = jQuery(".SlideRight ").offset().top;
	var wintop = jQuery(window).scrollTop();
	var naviHeight = jQuery(".HotelOrderIns").outerHeight();
	var num = $(".ContactIns").offset().top;
	var num1 = $(".SlideLeft").offset().top;
	var chargNum = num - num1
	if(naviTop >= wintop) {
		$(".HotelOrderIns").removeClass("FixRight");
	} else if(wintop > num) {
		$(".HotelOrderIns").removeClass("FixRight").css({
			"marginTop": chargNum + 'px'
		});
	} else {
		$(".HotelOrderIns").removeClass("FixRight").css({
			"marginTop": 0
		});
		if(naviHeight > $(window).height()) {
			$(".HotelOrderIns").removeClass("FixRight");
		} else {
			$(".HotelOrderIns").addClass("FixRight");
		}
	}
}