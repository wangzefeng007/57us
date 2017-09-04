$(function() {
	jQuery(".ban").slide({
		mainCell: ".pic",
		effect: "fade",
		autoPlay: true,
		delayTime: 600,
		interTime: 4000,
		trigger: "click"
	});
	$('input').W_Format();
	_DomJson.cityid.value = '213';

	function getNowFormatDate() {
		var date = new Date();
		var seperator1 = "-";
		var seperator2 = ":";
		var month = date.getMonth() + 1;
		var strDate = date.getDate();
		if(month >= 1 && month <= 9) {
			month = "0" + month;
		}
		if(strDate >= 0 && strDate <= 9) {
			strDate = "0" + strDate;
		}
		var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate +
			" " + date.getHours() + seperator2 + date.getMinutes() +
			seperator2 + date.getSeconds();
		return currentdate;
	}

	//获取当前日期
	function Daya() {
		var mydate = new Date();
		if(mydate.getMonth() < 9) {
			var bb = '0' + (mydate.getMonth() + 1);
		} else {
			var bb = (mydate.getMonth() + 1);
		}
		if(mydate.getDate() >= 0 && mydate.getDate() <= 9) {
			var cc = '0' + (mydate.getDate());
		} else {
			var cc = (mydate.getDate());
		}
		var str = "" + mydate.getFullYear() + "-";
		str += bb + "-";
		str += cc;
		return str;
	}
	//获取当前日期+1
	function Dayb() {
		var mydate = new Date();
		var mydate = new Date(mydate.valueOf() + 1 * 24 * 60 * 60 * 1000);
		if(mydate.getMonth() < 9) {
			var bb = '0' + (mydate.getMonth() + 1);
		} else {
			var bb = (mydate.getMonth() + 1);
		}
		if(mydate.getDate() >= 0 && mydate.getDate() <= 9) {
			var cc = '0' + (mydate.getDate());
		} else {
			var cc = (mydate.getDate());
		}
		var str = "" + mydate.getFullYear() + "-";
		str += bb + "-";
		str += cc;
		return str;
	}

	//获取当前日期+15
	function Dayc() {
		var mydate = new Date();
		var mydate = new Date(mydate.valueOf() + 15 * 24 * 60 * 60 * 1000);
		if(mydate.getMonth() < 9) {
			var bb = '0' + (mydate.getMonth() + 1);
		} else {
			var bb = (mydate.getMonth() + 1);
		}
		if(mydate.getDate() >= 0 && mydate.getDate() <= 9) {
			var cc = '0' + (mydate.getDate());
		} else {
			var cc = (mydate.getDate());
		}
		var str = "" + mydate.getFullYear() + "-";
		str += bb + "-";
		str += cc;
		return str;
	}
	//获取当前日期+16
	function Dayd() {
		var mydate = new Date();
		var mydate = new Date(mydate.valueOf() + 16 * 24 * 60 * 60 * 1000);
		if(mydate.getMonth() < 9) {
			var bb = '0' + (mydate.getMonth() + 1);
		} else {
			var bb = (mydate.getMonth() + 1);
		}
		if(mydate.getDate() >= 0 && mydate.getDate() <= 9) {
			var cc = '0' + (mydate.getDate());
		} else {
			var cc = (mydate.getDate());
		}
		var str = "" + mydate.getFullYear() + "-";
		str += bb + "-";
		str += cc;
		return str;
	}
	//初始化时间赋值
	$("input[name='sdDtae']").val(Dayc());
	$("input[name='edDate']").val(Dayd());
	$("input[name='startDate']").val(Daya());
	$("input[name='endDate']").val(Dayb());

	//入住日期回调
	$("#startDate").on('focus', function() {
		var a = $(this).val();
		$(this).attr('value', a);
	})

	//离开日期加调
	$("#endDate").on('focus', function() {
		var b = $(this).val();
		$(this).attr('value', b);
	})

	//选择日期后，搜索计算时间戳
	function getUrl(cityid, startDate, endDate) {
		//入住时间转时间戳
		strStartDate = _DomJson.startDate.value.replace(/-/g, '/');
		var unixStartDate = parseInt(new Date(strStartDate).getTime() / 1000);
		//离开时间转时间戳
		strEndDate = _DomJson.endDate.value.replace(/-/g, '/');
		var unixEndDate = parseInt(new Date(strEndDate).getTime() / 1000);
		var location = 'ct=' + cityid + '&sd=' + unixStartDate + '&ed=' + unixEndDate;
		return location;
	}

	//计算入住天数
	function get_unix_time(dateStr) {
		var newstr = dateStr.replace(/-/g, '/');
		var date = new Date(newstr);
		var time_str = date.getTime().toString();
		return time_str;
	}

	//点击搜索时验证判断
	$('.SearchBtn').on('click', function() {
		if(_DomJson.cityid.value === '') {
			layer.msg('请选择城市');
			return;
		}
		if(_DomJson.startDate.value == _DomJson.endDate.value) {
			layer.msg('请选择不同的日期');
			return;
		}
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var Times = get_unix_time(endDate) - get_unix_time(startDate);
		var days = parseInt(Times / (1000 * 60 * 60 * 24));
		if(days > 30) {
			layer.msg('您入住酒店时间超过30天，请分订单提交预订');
			return;
		}
		window.open(_HrefHead + '/hotel/hotellist?' + getUrl(_DomJson.cityid.value, _DomJson.startDate.value, _DomJson.endDate.value));
	});

	//当选择框没有选择日期时引用计算时间戳
	function getUrlb(cityid, sdDtae, edDate) {
		//入住时间转时间戳
		sdDtae = _DomJson.sdDtae.value.replace(/-/g, '/');
		var unixStartDate = parseInt(new Date(sdDtae).getTime() / 1000);
		//离开时间转时间戳
		edDate = _DomJson.edDate.value.replace(/-/g, '/');
		var unixEndDate = parseInt(new Date(edDate).getTime() / 1000);
		var location = 'ct=' + cityid + '&sd=' + unixStartDate + '&ed=' + unixEndDate;
		return location;
	}

	//热门推荐
	$('.HotCityList li').find('a').on('click', function() {
		//目前热门推荐读取时间为+15  修改于2016.7.21
		window.open(_HrefHead + '/hotel/hotellist?' + getUrlb($(this).attr('data-citycode'), _DomJson.sdDtae.value, _DomJson.edDate.value));

		//判断用户是否有选择入住日期与离开日期
		// if(_DomJson.startDate.value != '' || _DomJson.endDate.value != ''){
		//     window.open( _HrefHead + '/hotel/hotellist?' + getUrl($(this).attr('data-citycode'),_DomJson.startDate.value,_DomJson.endDate.value));
		// }else {
		//     window.open( _HrefHead + '/hotel/hotellist?' + getUrlb($(this).attr('data-citycode'),_DomJson.sdDtae.value,_DomJson.edDate.value));
		// }
	});

	//城市列表
	var data = {
		'keyword': ''
	};
	$.get(_HrefHead + '/ajaxhotelcity.html', data, function(json) {
		// console.log(json);
	}, 'json');
});