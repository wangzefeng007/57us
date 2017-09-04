$(function() {
	$('input').W_Format();
	//多图滚动
	Focus();
	$("#tFocusBtn").mouseenter(function() {
			$(this).find("a").fadeIn()
		}).mouseleave(function() {
			$(this).find("a").fadeOut()
		})
		//地图切换
	MapTab();
	//锚点定位
	ScrollFixed();
	//政策
	TabTip();
	//显示年龄
	//		ShowChildAge()
	//弹窗修改
	PopChange();
	initialize();

	//固定右边栏目
	$(".sidebar").autofix_anything();

	var GetHref = window.location.search.split('&');
	var oldSD, oldED, hotel_id;
	for(var i = 0; i < GetHref.length; i++) {
		if(GetHref[i].indexOf('HotelID') > -1) {
			hotel_id = GetHref[i].split('=')[1];
		}
		if(GetHref[i].indexOf('sd=') > -1) {
			bb = GetHref[i].split('=')[1];
			var ss = /[^%]*/;
			oldSD = ss.exec(bb);
		}
		if(GetHref[i].indexOf('ed=') > -1) {
			oldED = GetHref[i].split('=')[1];
		}
	}

	//获取当前日期+15
	function Day() {
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
	function Dayb() {
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

	function get_unix_time(dateStr) {
		var newstr = dateStr.replace(/-/g, '/');
		var date = new Date(newstr);
		var time_str = date.getTime().toString();
		return time_str;
	}

	function diffDays(startDate, endDate) {
		if(startDate.indexOf('-') > -1) {
			var st = get_unix_time(startDate);
			var ed = get_unix_time(endDate);
			return(Number(ed) - Number(st)) / (1000 * 60 * 60 * 24) + '天';
		} else {
			return(Number(endDate) - Number(startDate)) / (1000 * 60 * 60 * 24) + '天';
		}
	}

	$('#startDate').on('focus', function() {
		var a = $(this).val();
		$(this).attr('value', a);
	})

	$('#endDate').on('focus', function() {
		var daysElem = $('.days');
		daysElem.html(diffDays(_DomJson.startDate.value, _DomJson.endDate.value));
		var b = $(this).val();
		$(this).attr('value', b);
	})

	//判断是否有从上面带日期过来，如果无  获取当前日期
	if(oldSD == undefined || oldED == undefined) {
		_DomJson.startDate.value = Day();
		_DomJson.endDate.value = Dayb();
		var daysElem = $('.days');
		daysElem.html(diffDays(_DomJson.startDate.value, _DomJson.endDate.value));
	} else {
		_DomJson.startDate.value = oldSD;
		_DomJson.endDate.value = oldED;
		var daysElem = $('.days');
		daysElem.html(diffDays(_DomJson.startDate.value, _DomJson.endDate.value));
	}

	var StartDateDom = $('[name="startDate"]');
	var EndDateDom = $('[name="endDate"]');
	change();

	$('.ReSearchBtn').on('click', function() {
		var data = {
			'adultNum': '2',
			'childrenNum': '0',
			'startDate': _DomJson.startDate.value,
			'endDate': _DomJson.endDate.value
		};
		var text = $('.personNum').html();
		data.adultNum = text.split('，')[0].split('成人')[0];
		data.childrenNum = text.split('，')[1].split('儿童')[0];
		//console.log(data);
	});
});

//产品加载开始

$('.ReSearchBtn').on('click', function() {
	change();
});

$('#shouchang').on('click', function() {
	var url = window.location;
	var title = document.title;
	var ua = navigator.userAgent.toLowerCase();
	if(ua.indexOf("360se") > -1) {
		alert("由于360浏览器功能限制，请按 Ctrl+D 手动收藏！");
	} else if(ua.indexOf("msie 8") > -1) {
		window.external.AddToFavoritesBar(url, title); //IE8
	} else if(document.all) {
		try {
			window.external.addFavorite(url, title);
		} catch(e) {
			alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
		}
	} else if(window.sidebar) {
		window.sidebar.addPanel(title, url, "");
	} else {
		alert('您的浏览器不支持,请按 Ctrl+D 手动收藏!');
	}
});

//前往预定页面
$('.GoBooking').click(function() {
		W_ScrollTo($('.HotelDetailMenuBox li').eq(1));
	})
	//产品加载结束

function get_unix_time(dateStr) {
	var newstr = dateStr.replace(/-/g, '/');
	var date = new Date(newstr);
	var time_str = date.getTime().toString();
	return time_str;
}

function change() {
	var startDate = $('#startDate').val();
	var endDate = $('#endDate').val();
	var Times = get_unix_time(endDate) - get_unix_time(startDate);
	var days = parseInt(Times / (1000 * 60 * 60 * 24));
	var adultNum = $('.personNum').data('adultcount');
	var childrenNum = $('.personNum').data('childcount');
	var childAge = $('.personNum').data('childage');
	var hotel_id = $('#btn').attr('data-id');
	var datas = 'start_time=' + startDate + '&end_time=' + endDate + '&adult_count=' + adultNum + '&child_count=' + childrenNum + '&HotelID=' + hotel_id;
	if(days > 30) {
		layer.msg('您入住酒店时间超过30天，请分订单提交预订');
		return;
	}
	if(childAge != '') {
		datas += '&child_age=' + childAge;
	}
	$.ajax({
		url: '/ajaxgetroom.html',
		data: datas,
		dataType: 'json',
		beforeSend: function() {
			$('#loading_bg').removeClass('hidden');
			$('.NoHotel').addClass('hidden');
			$('#changes').html('');
		},
		error: function() {
			layer.close('网络出错！');
		},
		success: function(data) {
			if(data.status) {
				$('#loading_bg').addClass('hideen');
				var html = '<table border="0" cellspacing="0" cellpadding="0" class="HotelTab mt30" width="100%">' +
					'<tr height="47">' +
					'<th width="160">客房类型</th>' +
					'<th width="110">床型</th>' +
					'<th width="90">入住人数</th>' +
					'<th width="140">取消政策</th>' +
					'<th width="110">早餐</th>' +
					'<th width="116">日均价</th>' +
					'<th width="111"></th>' +
					'</tr>';
				for(var i = 0; i < data.data.length; i++) {
					var val = data.data[i];
					// console.log(val);
					html += '<tr height="56" class="roomlist">' +
						'<td>' + val.room_type + '</td>' +
						'<td>' + val.bed_name + '</td>' +
						'<td>' + val.num + '</td>' +
						'<td>' + val.rate + '</td>' +
						'<td>' + val.Breakfast + '</td>' +
						'<td><span class="TabPrice">￥<i>' + Math.ceil(val.price / days) + '</i></span></td>' +
						'<td><a href="' + val.url + '" class="OrderBtn">预定</a></td>' +
						'</tr>';
				}
				var showNum = resetNum = 5;

				if(data.data.length > showNum) {
					html += '<tr height="35">' +
						'<td colspan="7"><a href="javascript:;" class="MorePluss">查看更多价格<i class="dropdown"></i></a></td>' +
						'</tr>';
					$('#loading_bg').addClass('hidden');
					$('.NoHotel').addClass('hidden');
					$('#changes').html(html);
					$(".roomlist:gt(" + (showNum - 1) + ")").addClass('hidden');
					$(".MorePluss").click(function() {
						if($('.roomlist').hasClass('hidden')) {
							showNum += resetNum;
							$(".roomlist:lt(" + showNum + ")").removeClass('hidden');
							//$('body,html').animate({scrollTop:$('.roomlist').eq((showNum-resetNum-1)).offset().top},600);
							if(showNum >= data.data.length) {
								$(this).html('<span style="color:#999999">收起列表</span><i class="retract"></i>');
							}
						} else {
							showNum = resetNum;
							$(".roomlist:gt(" + (resetNum - 1) + ")").addClass('hidden');
							$('body,html').animate({
								scrollTop: 523
							}, 600);
							$(this).html('查看更多价格<i class="dropdown">');
						}
					});
				} else {
					$('#loading_bg').addClass('hidden');
					$('.NoHotel').addClass('hidden');
					$('#changes').html(html);
				}
			} else {
				$('#loading_bg').addClass('hidden');
				$('.NoHotel').removeClass('hidden');
			}
		}
	})
}

function MapTab() {
	$(".MapBox .hd a").click(function() {
		var num = $(this).index();
		$(this).addClass("on").siblings().removeClass("on");
		$(this).parent().siblings().find("ul").hide().eq(num).show();
	});
}
//定位锚点
function ScrollFixed() {
	var naviTop = jQuery(".HotelDetailMenu").offset().top;
	jQuery('.HotelDetailMenuBox li').click(function() {
		var $dayLi = jQuery(this).index();
		var dInfor = jQuery(".DetailBox").eq($dayLi).offset().top - 50;
		jQuery('html, body').animate({
			scrollTop: dInfor
		}, 500);
	});

	function checkScroll(forcon, forli, wtop) {
		var next = forcon.size() - 1;
		while(next > -1) {
			var itemTop = forcon.eq(next).offset().top - 70;
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
			$(".HotelDetailMenuBox").removeClass("FixedMenu");
		} else {
			$(".HotelDetailMenuBox").addClass("FixedMenu");
		}
		checkScroll(jQuery('.DetailBox'), jQuery('.HotelDetailMenuBox li'), wintop);

	});

}

function TabTip() {
	$(".HotelTab i.question").each(function() {
		$(this).hover(function() {
			var TipHtml = '<div class="TipBox"><h3 class="TipBoxTit">' + $(this).parent().attr("data-tit") + '</h3><div class="TipBoxCont">' + $(this).parent().attr("data-content") + '</div></div>'
			layer.tips(TipHtml, this, {
				tips: [1, '#3595CC'],
				time: 400000000,
				skin: 'TipDemo',
				area: ['340px']
			});
		}, function() {
			layer.closeAll();
		});
	});
}

function PopChange() {
	$(".ChangeBtn").click(function() {
		var adultNum = $('.personNum').data('adultcount');
		var childrenNum = $('.personNum').data('childcount');
		var childAge = $('.personNum').data('childage');
		layer.open({
			type: 1,
			title: false,
			area: '400px',
			skin: 'HotelPopBox', //样式类名
			closeBtn: 0, //不显示关闭按钮
			shift: 2,
			shadeClose: true, //开启遮罩关闭
			content: $(".HotelPop").html(),
			btn: ['确定', '取消'],
			yes: function(index, layero) {
				$('.personNum').data('adultcount', adultNum);
				$('.personNum').data('childcount', childrenNum);
				var AgeArr = [];
				for(var i = 0; i < childrenNum; i++) {
					if(typeof($('.ChildAge').eq(i + 3).find('a.selected').attr('value')) == 'undefined') {
						AgeArr.push(1);
					} else {
						AgeArr.push($('.ChildAge').eq(i + 3).find('a.selected').attr('value'));
					}
				}
				$('.personNum').data('childage', AgeArr);
				$('.personNum').html(adultNum + '成人，' + childrenNum + '儿童');
				layer.close(index);
			},
			btn2: function(index, layero) {

			},
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
	});
	$(".CanceBtn").on('click', function() {
		layer.closeAll();
	});
}

function initialize() {
	var lat = $('.MapMain').attr('data-lat');
	var log = $('.MapMain').attr('data-log');
	var name = $('.tit').attr('data-tit');
	var latlng = new google.maps.LatLng(lat, log);
	var myOptions = {
		zoom: 16,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		streetViewControl: false
	};
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(lat, log), //此参数为地图上要显示点的坐标
		animation: google.maps.Animation.DROP,
		map: map,
		title: name
	});
}

function FixRight() {
	var naviTop = jQuery(".SlideRight").offset().top;
	var wintop = jQuery(window).scrollTop();
	if(naviTop >= wintop) {
		$(".MapBox").removeClass("FixMap")
	} else {
		$(".MapBox").addClass("FixMap")
	}

}