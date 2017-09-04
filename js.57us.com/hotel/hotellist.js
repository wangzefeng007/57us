$(function() {
	//加载等待
	var layerIndex;

	function laoding(open) {
		if(open) {
			var load = layer.open({
				type: 1,
				title: false,
				closeBtn: 0,
				shadeClose: false,
				// shade: [0.0001, '#fff'],
				content: '<div">正在加载中，请稍后。。。</div>',
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
		} else {
			layer.close(layerIndex);
			// layer.msg('数据载入成功！');
			$(".layui-layer-shade").css('display', 'none');
		}
	}
	laoding(1);
	$('.cbt').inputbox();
	$('[name="rbt"], [name="rbt1"]').inputbox();
	//ie8支持placeholder属性
	$(function(){ $('input, textarea').placeholder(); });
	$('input').W_Format();
	var request = W_GetRequest();
	var fromFirstPageData = {
		'CityId': request.ct,
		'StartDate': request.sd,
		'EndDate': request.ed
	};

	var StartDateDom = $('[name="startDate"]');
	var EndDateDom = $('[name="endDate"]');

	function unixDate(value) {
		var d = new Date(value * 1000); //根据时间戳生成的时间对象
		if(d.getMonth() < 9) {
			var bb = '0' + (d.getMonth() + 1);
		} else {
			var bb = (d.getMonth() + 1);
		}
		if(d.getDate() >= 0 && d.getDate() <= 9) {
			var cc = '0' + (d.getDate());
		} else {
			var cc = (d.getDate());
		}
		var date = (d.getFullYear()) + "-" +
			bb + "-" +
			cc + " ";
		return date;
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
	var allData;
	var pageData;
	var initData = {
		'CityCode': '213',
		'StartTime': '',
		'EndTime': '',
		'Keyword':"",
	};
	//获取数据
	if(fromFirstPageData.StartDate === undefined || fromFirstPageData.EndDate === undefined) {
		//console.log((new Date()).getTime());
		initData.StartTime = unixDate(((new Date()).getTime()));
		initData.EndTime = unixDate(((new Date()).getTime()) + (1000 * 60 * 60 * 24));
	} else {
		initData.StartTime = unixDate(fromFirstPageData.StartDate);
		initData.EndTime = unixDate(fromFirstPageData.EndDate);
	}
	if(fromFirstPageData.CityId != undefined) {
		initData.CityCode = fromFirstPageData.CityId;
	}

	//设置初始值
	_DomJson.cityid.value = fromFirstPageData.CityId;
	_DomJson.startDate.value = initData.StartTime;
	_DomJson.endDate.value = initData.EndTime;
	var daysElem = $('.days');
	daysElem.html(diffDays(_DomJson.startDate.value, _DomJson.endDate.value));
	var nowSD = _DomJson.startDate.value,
		nowED = _DomJson.endDate.value;

	//点击选择日期

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

	//計算入住幾天
	$('#endDate').on('focus', function() {
		var daysElem = $('.days');
		daysElem.html(diffDays(_DomJson.startDate.value, _DomJson.endDate.value));
	})

	$("#keyword").on("blur change", function() {
		var a = $(this).val();
		$(this).attr('value', a);
	});

	//设置初始城市
	var cityDataArr = $$.module.address.source.hotel.split('@');
	for(var i = 0; i < cityDataArr.length; i++) {
		var everycity = cityDataArr[i].split('|');
		for(j = 0; j < everycity.length; j++) {
			if(everycity[j] == fromFirstPageData.CityId) {
				_DomJson.StartCity.value = everycity[1];
			}

		}
	}
	//初始化
	var selectItems = {
		'price': '0',
		'starts': '0',
		'facilities': ['不限'],
		'selectPage': '1',
		'sort': {
			'type': '',
			'value': ''
		}
	};

	// if(selectItems.facilities == "不限"){
	//     $(".selectedInfor").addClass("limithide");
	// }
	function get_unix_time(dateStr) {
		var newstr = dateStr.replace(/-/g, '/');
		var date = new Date(newstr);
		var time_str = date.getTime().toString();
		return time_str;
	}

	//console.log(initData);
	$.get(_HrefHead + '/ajaxhotellist.html', initData, function(json) {
		//产品条数注入
		$("#productnum").empty();
		if(json.RecordCount == undefined){
			$("#productnum").append('0');
		}else {
			$("#productnum").append(json.RecordCount);
		}
		//console.log(json);
		hotelListDemo(json.Data);
		diffPage(json);
		pageData = json.Data;
		allData = json;
		laoding(0);
	}, 'json');
	$('.ChoseBoxBtn').on('click', function() {
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var Times = get_unix_time(endDate) - get_unix_time(startDate);
		var days = parseInt(Times / (1000 * 60 * 60 * 24));
		if(days > 30) {
			layer.msg('您入住酒店时间超过30天，请分订单提交预订');
			return;
		}

		laoding(1);
		var citycode = $('input[name=cityid]').val();
		initData.CityCode = citycode;
		initData.EndTime = _DomJson.endDate.value;
		initData.StartTime = _DomJson.startDate.value;
		initData.Keyword = $(".keyword").val();
		ResetOptions();
		$.get(_HrefHead + '/ajaxhotellist.html', initData, function(json) {
			if(json != '') {
				//产品条数注入
                $("#productnum").text(json.RecordCount);
				hotelListDemo(json.Data);
				diffPage(json);
				pageData = json.Data;
				allData = json;
				nowSD = _DomJson.startDate.value,
					nowED = _DomJson.endDate.value;
				W_ScrollTo($('.ChoseBoxFirst').eq(0));
				laoding(0);
			} else {
                $("#productnum").text('0');
				W_ScrollTo($('.ChoseBoxFirst').eq(0));
				laoding(0);
				noDataTpl()
			}
		}, 'json');
	});

	//重置筛选项
	function ResetOptions() {
		addSelected('starts', '不限', true);
		addSelected('price', '不限', true);
		addSelected('facilities', '不限', true);
		selectItems = {
			'price': '0',
			'starts': '0',
			'facilities': ['不限'],
			'selectPage': '1',
			'sort': {
				'type': '',
				'value': ''
			}
		};
		$('dl[type="starts"] dd label').removeClass('rb_active');
		$('dl[type="price"] dd label').removeClass('rb_active');
		$('dl[type="facilities"] dd label').removeClass('checked');
		$('dl[type="starts"] dd label').eq(0).addClass('rb_active');
		$('dl[type="price"] dd label').eq(0).addClass('rb_active');
		$('dl[type="facilities"] dd label').eq(0).addClass('checked');
		$('ul.fl li').eq(1).find('span').html('价格排序<i></i>');
		$('ul.fl li').eq(2).find('span').html('酒店星级<i></i>');
		$('ul.fl li').removeClass('on');
		$('ul.fl li').eq(0).addClass('on');
	}

	//点击获取数据
	function getEachPageData(data) {
		laoding(1);
		$.get(_HrefHead + '/ajaxhotellist.html', $.extend({}, data, initData), function(json) {
			if(json != '') {
				//产品条数注入
                $("#productnum").text(json.RecordCount);
				setTimeout(function() {
					hotelListDemo(json.Data);
					diffPage(json);
					pageData = json.Data;
					allData = json;
					laoding(0)
					W_ScrollTo($('.ChoseBoxFirst').eq(0));
				}, 1200);
			} else {
                $("#productnum").text('0');
;				W_ScrollTo($('.ChoseBoxFirst').eq(0));
				setTimeout(function() {
					laoding(0)
				}, 1000);
				noDataTpl()
			}
		}, 'json');
	}
	//改变选择项目
	function changeSeleced(selectType, data) {
		$('.clearList').find("[type=" + selectType + "]").find('label').html(data);
	}

	//加入选择项目
	var selectedDemo = $('#selectedDemo').html();

	function addSelected(type, data, ifNeedClear) {
		var injectdata = {
			'type': type,
			'data': data,
			'chType': '',

		};
		if(type == 'price') {
			injectdata.chType = "价格：";
		} else if(type == 'starts') {
			injectdata.chType = "星级：";

		} else if(type == 'facilities') {
			injectdata.chType = "设施：";
		}
		if(ifNeedClear) {
			$('.clearList').find("[type=" + type + "]").remove();
		}
		var selectedItems = $('.clearList').find("[type=" + type + "]");
		if(data != '不限') {
			for(var i = 0; i < selectedItems.length; i++) {
				if($(selectedItems[i]).find('label').text() == '不限') {
					$(selectedItems[i]).remove();
				}
			}
		}
		laytpl(selectedDemo).render(injectdata, function(html) {
			var _html = $(html);
			_html.appendTo('.clearList');
			_html.find('b').on('click', function() {
				clickDelete($(this));
			});
		});
	}
	//删除选择的项目
	function deleteItems(_this) {
		var items = $('.clearList').find('div');
		//console.log(_this.attr('value'));
		if(_this.attr('value') !== '不限') {
			for(var i = 0; i < items.length; i++) {
				var _item = $(items[i]);
				if(_item.find('label').text() == _this.attr('value')) {
					_item.remove();
				}
			}
		}
	}
	$('.clearList').find('b').on('click', function() {
		clickDelete($(this));
	});

	function clickDelete(_this) {
		var thisItemBox = _this.parents('.selectedInfor');
		var selectType = thisItemBox.attr('type');
		var thisItem = thisItemBox.find('label').text();
		if(thisItem !== '不限' && selectType !== 'facilities') {
			selectItems[selectType] = '0';
			$('.listIndex[type=' + selectType + ']').find('label.rb_active').removeClass('rb_active');
			$('.listIndex[type=' + selectType + ']').find('label').eq(0).addClass('rb_active');
			thisItemBox.find('label').text('不限');
		} else if(thisItem !== '不限' && selectType == 'facilities') {
			thisItemBox.remove();
			if($('.selectedInfor[type="facilities"]').length === 0) {
				addSelected('facilities', '不限', false);
				$('.listIndex[type="facilities"]').find('label').eq(0).addClass('checked');
				selectItems.facilities = ['不限'];
			}
			var checkedItems = $('.listIndex[type="' + selectType + '"]').find('label.checked');
			for(var i = 0; i < checkedItems.length; i++) {
				if($(checkedItems[i]).text() == thisItem) {
					$(checkedItems[i]).removeClass('checked');
				}
				if(selectItems.facilities[i] == thisItem) {
					selectItems.facilities.splice(i, 1);
				}
			}
		}
		selectItems.selectPage = 1;
		getEachPageData(selectItems);
	}
	//去选择项目
	$('.listIndex').delegate('label', 'click', function() {
		var _this = $(this);
		var _thisValue = _this.attr('value');
		var selectType = _this.parents('.listIndex').attr('type');
		var chSelectType = _this.parents('.listIndex').find('dt').text();
		setValue(_this, _this.attr('type'));
		// laoding(1);
		if(_this.attr('type') === "radiobox") {
			changeSeleced(selectType, _this.text());
		} else if(_this.attr('type') === "checkbox") {
			//此处只是样式
			var _thisParent = _this.parents('.listIndex');
			var thisItems = _thisParent.find('label');
			// console.log(_this.hasClass('checked'));
			if(_this.hasClass('checked')) {
				if(_thisValue == '不限') {
					for(var i = 1; i < thisItems.length; i++) {
						if($(thisItems[i]).hasClass('checked')) {
							$(thisItems[i]).removeClass('checked');
						}
					}
				} else {
					if($(thisItems[0]).hasClass('checked')) {
						$(thisItems[0]).removeClass('checked');
					}
				}
			} else {
				if(_thisParent.find('label.checked').length === 0 && _thisValue !== '不限') {
					$(thisItems[0]).addClass('checked');
					addSelected(selectType, '不限', true);
				}
				if(_thisParent.find('label.checked').length === 0 && _thisValue == '不限') {
					$(thisItems[0]).addClass('checked');
				}

			}
		}
	});
	//搜索值设置
	function setValue(_this, clickType) {
		var selectType = _this.parents('.listIndex').attr('type');
		if(clickType == 'radiobox') {
			selectItems[selectType] = _this.attr('value');
		} else {
			if(_this.hasClass('checked')) { //no checked
				if(_this.attr('value') == '不限') {
					selectItems.facilities = [];
					selectItems.facilities[0] = '不限';
					$('.clearList').find("[type=" + selectType + "]").remove();
				} else {
					for(var i = 0; i < selectItems.facilities.length; i++) {
						if(selectItems.facilities[i] === '不限') {
							selectItems.facilities.splice(i, 1);
							break;
						}
					}
					selectItems.facilities.push(_this.attr('value'));
				}
				addSelected(selectType, _this.text(), false);
			} else { //checked
				var _thisParent = _this.parents('.listIndex');
				if(_this.attr('value') !== '不限') {
					if(_thisParent.find('label.checked').length !== 0) {
						for(var n = 0; n < selectItems.facilities.length; n++) {
							if(selectItems.facilities[n] === _this.attr('value')) {
								selectItems.facilities.splice(n, 1);
							}
						}
					} else {
						selectItems.facilities = [];
						selectItems.facilities[0] = '不限';
					}
				}
				deleteItems(_this);
			}
		}
		selectItems.selectPage = 1;
		getEachPageData(selectItems);

	}
	//最大最小设置
	var minInput = $('#custext1');
	var maxInput = $('#custext2');
	$('.custom').delegate('.SureBtn', 'click', function() {
		var allInput = $('.custom').find('input[type="text"]');
		if(minInput.val() === '' && maxInput.val() === '') {
			layer.msg('请输入完整');
			return;
		}
		if(!minInput.val() == "" && !/^[1-9]\d*$/i.test(minInput.val()) || !maxInput.val() == "" && !/^[1-9]\d*$/i.test(maxInput.val())) {
			layer.msg('请输入大于0的正整数');
			return;
		}
		if(!isNaN(parseInt(maxInput.val())) && Number(minInput.val()) > Number(maxInput.val())) {
			layer.msg('最小值不可以大于最大值');
			return;
		}
		selectItems.price = minInput.val() + '-' + maxInput.val();
		//价格注入大于小于号筛选
		if(selectItems.price == minInput.val() + '-') {
			changeSeleced('price', minInput.val() + '以上');
		} else if(selectItems.price == '-' + maxInput.val()) {
			changeSeleced('price', maxInput.val() + '以下');
		} else {
			changeSeleced('price', minInput.val() + '-' + maxInput.val());
		}
		$('.listIndex[type="price"]').find('label.rb_active').removeClass('rb_active');
		selectItems.selectPage = 1;
		getEachPageData(selectItems);
	});
	//酒店模板
	var hotelDemo = $('#hotelDemo').html();

	function hotelListDemo(injectdata) {
		$('.noDataBox').empty();
		$('.HotelList').empty();
		// laoding(0);
		if(injectdata !== undefined && injectdata !== null && injectdata.length > 0) {
			//console.log(injectdata);
			laytpl(hotelDemo).render(injectdata, function(html) {
				var _html = $(html);
				_html.appendTo('ul.HotelList').find('a').on('click', function() {
					var _this = $(this);
					var hotel_id = _this.attr('data-hotelid');
					var location = _HrefHead + '/hotel/' + hotel_id + '.html?sd=' + nowSD + '&ed=' + nowED;
					window.open(location);
				});
			});
		} else {
			noDataTpl();
		}
	}
	//无数据模板
	var noDataDemo = $('#noDataDemo').html();

	function noDataTpl() {
		$('.HotelList').empty();
		$('.noDataBox').empty();
		$('.HotePage').empty();
		laytpl(noDataDemo).render({}, function(html) {
			var _html = $(html);
			_html.appendTo('.noDataBox');
		});
	}
	//分页机制
	function diffPage(pageNumData) {
		//清空分页数据
		$('.HotePage').empty();
		//获取分页dom
		var pageDemoParent = $('.HotePage');

		//遍历分页html
		var pageHtml = '';
		$.each(pageNumData.PageNums,function(n,value){
			pageHtml+='<a class="cupo" value="'+value+'">'+value+'</a>';
		});

		//拼装分页html
		var html = '';
		html+= '<a class="cupo prev no" value="">上一页</a>';
		html+= '<a class="cupo first" value="1">1</a>';
		html+= pageHtml;
		html+= '<span class="lastEllipsis" value="" href="javascript:void(0)">...</span>';
		html+= '<a class="cupo PageCount" value="'+pageNumData.PageCount+'">'+pageNumData.PageCount+'</a>';
		html+= '<a class="cupo next" value="">下一页</a>';

		//注入分页html后处理相关事件
		$(html).appendTo('.HotePage').on('click', function() {
			var _this = $(this);
			var allPage = $('.HotePage').find('a');
			var _thisValue = Number(_this.attr('value'));
			if(_this.text() !== '上一页' && _this.text() !== '下一页') {
				selectItems.selectPage = Number(_this.attr('value'));
			} else if(_this.text() == '上一页' && pageNumData.Page !== 1) {
				selectItems.selectPage = parseInt(pageNumData.Page) - 1;
			} else if(_this.text() == '上一页' && pageNumData.Page === 1) {
				return;
			} else if(_this.text() == '下一页' && pageNumData.Page !== pageNumData.PageCount) {
				selectItems.selectPage = parseInt(pageNumData.Page) + 1;
			} else if(_this.text() == '下一页' && pageNumData.Page === pageNumData.PageCount) {
				return;
			}
			getEachPageData(selectItems);
		});

		var allPage = $('.HotePage').find('a');
		for(var i = 0; i < allPage.length; i++) {
			if(Number($(allPage[i]).html()) == pageNumData.Page) {
				$(allPage[i]).addClass('on');
			}
		}
		if(pageNumData.Page === 1) {
			$(allPage[0]).addClass('no');
			pageDemoParent.find('.firstEllipsis').remove();
		}
		if(pageNumData.Page < 5) {
			pageDemoParent.find('.first').remove();
		}
		if(pageNumData.Page > 1) {
			$(".prev").removeClass("no");
		}
		if(pageNumData.Page > 5) {
			$(".first").after('<span class="firstEllipsis">...</span>');
		}

		if(pageNumData.PageCount < 7) {
			// $(allPage[allPage.length - 1]).addClass('no');
			pageDemoParent.find('.lastEllipsis').remove();
			pageDemoParent.find('.PageCount').remove();
		}

		if(pageNumData.Page === pageNumData.PageCount) {
			$(allPage[allPage.length - 1]).addClass('no');
			pageDemoParent.find('.lastEllipsis').remove();
		}
		if(pageNumData.Page === pageNumData.PageCount) {
			$(allPage[allPage.length - 1]).addClass('no');
			pageDemoParent.find('.PageCount').remove();
		}
		if(pageNumData.Page === pageNumData.PageCount - 1) {
			pageDemoParent.find('.PageCount').remove();
			pageDemoParent.find('.lastEllipsis').remove();
		} else if(pageNumData.Page === pageNumData.PageCount - 2) {
			pageDemoParent.find('.lastEllipsis').remove();
			pageDemoParent.find('.PageCount').remove()
		}
		if(pageNumData.Page === pageNumData.PageCount - 3) {
			pageDemoParent.find('.lastEllipsis').remove();
		}
	}
	//判断是否是数组
	function isArray(obj) {
		return Object.prototype.toString.call(obj) === '[object Array]';
	}

	$('.px_box li').delegate('a', 'click', function() {
		$('ul.fl li').eq(1).find('span').html('价格排序<i></i>');
		$('ul.fl li').eq(2).find('span').html('酒店星级<i></i>');
		$(this).parents('li').siblings('li').removeClass('on');
		$(this).parents('li').addClass('on');
		var _this = $(this);
		if(_this.text() !== '综合排序') {
			var sortType = _this.attr('data-va');
			var type = _this.parents('.suv').attr('type');
			_this.parents('li').find('span').html(_this.html() + '<i></i>');
			if(sortType == "asc") {
				_this.parents('li').find('span').find('i').css('background-position', '-682px -21px');
			}
			selectItems.sort.type = type;
			selectItems.sort.value = sortType;
		} else {
			selectItems.sort.type = 'random';
			selectItems.sort.value = '0';
		}
		//console.log(selectItems);
		selectItems.selectPage = 1;
		getEachPageData(selectItems);
	});
});