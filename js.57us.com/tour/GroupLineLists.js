/**
 * Created by Foliage on 2016/8/10.
 */

//判断是国内参团，还是当地参团
var url = decodeURIComponent(window.location.href).split("/");
if(url[4] == 'home') {
	var Group = '/Templates/Tour/data/Home/';
} else if(url[4] == 'local') {
	var Group = '/Templates/Tour/data/Local/';
}

$(function() {
	//出发城市
	$('#StartCity').empty();
	$.get(Group + 'AreaOut.json', function(data) {
		var item;
		$.each(data, function(i, StartCity) {
			item = '<li>' +
				'<label name="rbt" type="radiobox" val="' + StartCity.AeraID + '" class="cbt"><i></i>' + StartCity.name + '</label>' +
				'</li>';
			$('#StartCity').append(item);
		});
		var Filter = {
			'idname': 'StartCity',
			'nini': 'StartCityAll',
			'dataidname': 'StartCityName',
			'ajaxname': 'AjaxStartCity',
			'textname':'出发城市：',
		}
		TourRadio(Filter);
		ClickLoad(Filter);
	}, 'json');

	//当地参团执行结束城市
	if(url[4] == 'local'){
		//结束城市
		$('#EndCity').empty();
		$.get(Group + 'AreaEnter.json', function(data) {
			var item;
			$.each(data, function(i, EndCity) {
				item = '<li>' +
					'<label name="rbt1" type="radiobox" val="' + EndCity.AeraID + '" class="cbt"><i></i>' + EndCity.name + '</label>' +
					'</li>';
				$('#EndCity').append(item);
			});
			var Filter = {
				'idname': 'EndCity',
				'nini': 'EndCityAll',
				'dataidname': 'EndCityName',
				'ajaxname': 'AjaxEndCity',
				'textname':'结束城市：',
			}
			TourRadio(Filter);
			ClickLoad(Filter);
		}, 'json');
	}

	//行程天数
	$('#Stroke').empty();
	$.get(Group + 'Days.json', function(data) {
		var item;
		$.each(data, function(i, Stroke) {
			item = '<li>' +
				'<label name="rbt2" type="radiobox" val="' + Stroke.date + '" class="cbt"><i></i>' + Stroke.name + '</label>' +
				'</li>';
			$('#Stroke').append(item);
		});

		var Filter = {
			'idname': 'Stroke',
			'nini': 'StrokeAll',
			'dataidname': 'StrokeName',
			'ajaxname': 'AjaxStroke',
			'textname':'行程天数：',
		}
		TourRadio(Filter);
		ClickLoad(Filter);
	}, 'json');

	//特色主题
	$('#Theme').empty();
	$.get(Group + 'Subject.json', function(data) {
		var item;
		$.each(data, function(i, Theme) {
			item = '<li>' +
				'<label name="cbt" type="checkbox" val="' + Theme.id + '" class="cbt"><i></i>' + Theme.name + '</label>' +
				'</li>';
			$('#Theme').append(item);
		});
		var Filter = {
			'idname': 'Theme',
			'nini': 'ThemeAll',
			'dataidname': 'ThemeName',
			'ajaxname': 'AjaxTheme',
			'textname':'特色主题：',
		}
		TourCheckbox(Filter);
		ClickLoad(Filter);
	}, 'json');

	//途径城市
	//途径城市热门
	$('#WayCityHot').empty();
	$.ajax({
		type: "get",
		url: Group + 'HotCitys.json',
		dataType: "json",
		success: function(data) {
			var item;
			$.each(data, function(i, WayCityHot) {
				item = '<li>' +
					'<label name="cbt" type="checkbox" val="' + WayCityHot.name + '" class="cbt"><i></i>' + WayCityHot.name + '</label>' +
					'</li>';
				$('#WayCityHot').append(item);
			});
			$('#WayCityHot .cbt').inputbox();
			//途径城市热门选择时，移除不限
			$('#WayCityHot label').click(function() {
				$('#WayCityAll').parent().removeClass('on');
				var WayCityName = $(this).text();
				if($(this).is('.checked')) {
					html = '<p data-id="WayCityName">途径城市：<span data-id="' + WayCityName + '">' + WayCityName + '</span><em></em></p>';
					html2 = '<span data-id="' + WayCityName + '">' + WayCityName + ',</span>';
					//注入筛选位置
					if($('#condition span[data-id="' + WayCityName + '"]').length === 0) {
						$('#clearAll').before(html);
						$("#AjaxWayCity span[data-id='WayCityName']").remove();
						$('#AjaxWayCity').append(html2);
					}
					addlabel(WayCityName,0);
				} else {
					$("#condition span[data-id=" + WayCityName + "]").parent().remove();
					$("#AjaxWayCity span[data-id=" + WayCityName + "]").remove();
					var b = $("#condition p[data-id='WayCityName']").length;
					if(b == '0') {
						$('#WayCityAll').parent().addClass('on');
						$('#AjaxWayCity').append('<span data-id="WayCityName">All</span>');
					}
					removelabel(WayCityName,0);
				}
			})
			//筛选出来条件点击
			$(document).on("click","#condition p[data-id='WayCityName']",function(){
				var id = $(this).find('span').text();
				$(this).remove();
				$('#WayCityHot label').each(function() {
					var a = $(this).text();
					if(id == a) {
						$(this).removeClass('checked');
						$(this).find('input').attr("checked", true);
						$(this).find('i').attr("checked", true);
						var b = $("#condition p[data-id='WayCityName']").length;
						if(b == '0') {
							$('#WayCityAll').parent().addClass('on');
						}
					}
				})
				$('#AjaxWayCity span').each(function() {
					var a = $(this).attr('data-id');
					if(id == a) {
						$(this).remove();
						var b = $("#condition p[data-id='WayCityName']").length;
						if(b == '0') {
							$('#AjaxWayCity').append('<span data-id="WayCityName">All</span>');
						}
					}
				})
				removelabel(id,0);
			})
			//途径不限点击
			$('#WayCityAll').click(function() {
				removeAttrlabel();
			})
		}
	});

	//点击更多载入A
	$("#WayCityMore").click(function() {
			$('#WayCity span').removeClass('on');
			$('#WayCity span:first').addClass('on');
			$.ajax({
				type: "get",
				url: Group +'Citys/'+ 'A.json',
				dataType: "json",
				success: function(data) {
					if(data != null){
						$('#A').remove();
						html = '<ul class="choseBox ScrollChose" id="A"></ul>';
						$('.moreList').append(html);
						var item;
						$.each(data, function(i, A) {
							item = '<li>' +
								'<label name="cbt" type="checkbox" val="' + A.name + '" class="cbt"><i></i>' + A.name + '</label>' +
								'</li>';
							$('#A').append(item);
						});
						$('.moreList ul').hide();
						$("#A").show();
						$('#A .cbt').inputbox();
						//途径城市热门选择时，移除不限
						$("#condition [data-id='WayCityName']").each(function() {
							var a = $(this).find('span').text();
							$("#A li").each(function() {
								var b = $(this).find('label').attr('val');
								if(a == b) {
									$(this).find('label').addClass('checked');
								}
							})
						});

						$('#A label').click(function() {
							$('#WayCityAll').parent().removeClass('on');
							var WayCityName = $(this).text();
							if($(this).is('.checked')) {
								html = '<p data-id="WayCityName">途径城市：<span data-id="' + WayCityName + '">' + WayCityName + '</span><em></em></p>';
								html2 = '<span data-id="' + WayCityName + '">' + WayCityName + ',</span>';
								//注入筛选位置
								if($('#condition span[data-id="' + WayCityName + '"]').length === 0) {
									$('#clearAll').before(html);
									$("#AjaxWayCity span[data-id='WayCityName']").remove();
									$('#AjaxWayCity').append(html2);
								}
								addlabel(WayCityName,1);
							} else {
								$("#condition span[data-id=" + WayCityName + "]").parent().remove();
								$("#AjaxWayCity span[data-id=" + WayCityName + "]").remove();
								var b = $("#condition p[data-id='WayCityName']").length;
								if(b == '0') {
									$('#WayCityAll').parent().addClass('on');
									$('#AjaxWayCity').append('<span data-id="WayCityName">All</span>');
								}
								removelabel(WayCityName,1);
							}
							//筛选出来条件点击
							$("#condition p[data-id='WayCityName']").click(function() {
								var id = $(this).find('span').text();
								$(this).remove();
								$('#A label').each(function() {
									var a = $(this).text();
									if(id == a) {
										$(this).removeClass('checked');
										$(this).find('input').attr("checked", true);
										$(this).find('i').attr("checked", true);
										var b = $("#condition p[data-id='WayCityName']").length;
										if(b == '0') {
											$('#WayCityAll').parent().addClass('on');
										}
									}
								})
								$('#AjaxWayCity span').each(function() {
									var a = $(this).attr('data-id');
									if(id == a) {
										$(this).remove();
										var b = $("#condition p[data-id='WayCityName']").length;
										if(b == '0') {
											$('#AjaxWayCity').append('<span data-id="WayCityName">All</span>');
										}
									}
								})
								removelabel(id,1);
							})
						})
						$('#WayCityAll').click(function() {
							removeAttrlabel();
						})
					}else{
						$(".moreList ul").remove();
					}
				}
			});
		})
		//点击A-Z时载入
	$("#WayCity span").click(function() {
			$('#WayCity span').removeClass('on');
			$(this).addClass('on');
			var letter = $(this).text();
			$.ajax({
				type: "get",
				url: Group + 'Citys/' + letter + '.json',
				dataType: "json",
				success: function(data) {
					if(data != null){
						$('#' + letter + '').remove();
						html = '<ul class="choseBox ScrollChose" id="' + letter + '"></ul>';
						$('.moreList').append(html);
						var item;
						$.each(data, function(i, letterx) {
							item = '<li>' +
								'<label name="cbt" type="checkbox" val="' + letterx.name + '" class="cbt"><i></i>' + letterx.name + '</label>' +
								'</li>';
							$("#" + letter + "").append(item);
						});
						$('.moreList ul').hide();
						$("#" + letter + "").show();
						$('.moreList ul .cbt').inputbox();
						//途径城市A-Z选择时，移除不限

						$("#condition [data-id='WayCityName']").each(function() {
							var a = $(this).find('span').text();
							$("#" + letter + " li").each(function() {
								var b = $(this).find('label').attr('val');
								if(a == b) {
									$(this).find('label').addClass('checked');
									$(this).find('input').attr("checked", false);
									$(this).find('i').attr("checked", false);
								}
							})
						});

						$("#" + letter + " label").click(function() {
							$('#WayCityAll').parent().removeClass('on');
							var WayCityName = $(this).text();
							if($(this).is('.checked')) {
								html = '<p data-id="WayCityName">途径城市：<span data-id="' + WayCityName + '">' + WayCityName + '</span><em></em></p>';
								html2 = '<span data-id="' + WayCityName + '">' + WayCityName + ',</span>';
								//注入筛选位置
								if($('#condition span[data-id="' + WayCityName + '"]').length === 0) {
									$('#clearAll').before(html);
									$("#AjaxWayCity span[data-id='WayCityName']").remove();
									$('#AjaxWayCity').append(html2);
								}
								addlabel(WayCityName,1);
							} else {
								$("#condition span[data-id=" + WayCityName + "]").parent().remove();
								$("#AjaxWayCity span[data-id=" + WayCityName + "]").remove();
								var b = $("#condition p[data-id='WayCityName']").length;
								if(b == '0') {
									$('#WayCityAll').parent().addClass('on');
									$('#AjaxWayCity').append('<span data-id="WayCityName">All</span>');
								}
								removelabel(WayCityName,1);
							}
							$("#condition p[data-id='WayCityName']").click(function() {
								var id = $(this).find('span').text();
								$(this).remove();
								$("#" + letter + " label").each(function() {
									var a = $(this).text();
									if(id == a) {
										$(this).removeClass('checked');
										$(this).find('input').attr("checked", true);
										$(this).find('i').attr("checked", true);
										var b = $("#condition p[data-id='WayCityName']").length;
										if(b == '0') {
											$('#WayCityAll').parent().addClass('on');
										}
									}
								})
								$('#AjaxWayCity span').each(function() {
									var a = $(this).attr('data-id');
									if(id == a) {
										$(this).remove();
										var b = $("#condition p[data-id='WayCityName']").length;
										if(b == '0') {
											$('#AjaxWayCity').append('<span data-id="WayCityName">All</span>');
										}
									}
								})
								removelabel(id,1);
							})
						})
						$('#WayCityAll').click(function() {
							removeAttrlabel();
						})
					}else {
						$(".moreList ul").remove();
					}
				}
			});
		})

	//途径城市点击热门,a-z点击增加相应选中状态
	function addlabel(name,type) {
		if(type == 0){
			$(".moreList ul li label").each(function () {
				var c = $(this).text();
				if(name == c){
					$(this).addClass('checked');
					$(this).find('input').attr("checked", false);
					$(this).find('i').attr("checked", false);
				}
			})
		}else if(type == 1){
			$("#WayCityHot label").each(function () {
				var c = $(this).text();
				if(name == c){
					$(this).addClass('checked');
					$(this).find('input').attr("checked", false);
					$(this).find('i').attr("checked", false);
				}
			})
		}
		//执行加载
		Ajax();
	}
	//途径城市点击热门,a-z点击移除相应选中状态
	function removelabel(id,type) {
		if(type == 0){
			$(".moreList ul li label").each(function () {
				var c = $(this).text();
				if(id == c){
					$(this).removeClass('checked');
					$(this).find('input').attr("checked", true);
					$(this).find('i').attr("checked", true);
				}
			})
		}else if(type == 1){
			$("#WayCityHot label").each(function () {
				var c = $(this).text();
				if(id == c){
					$(this).removeClass('checked');
					$(this).find('input').attr("checked", true);
					$(this).find('i').attr("checked", true);
				}
			})
		}
		//执行加载
		Ajax();
	}
	//途径城市点击不限热门,a-z所有选中状态
	function removeAttrlabel() {
		//不限增加class,筛选出来条件移除
		$('#WayCityAll').parent().addClass('on');
		$('#condition p').each(function() {
			var a = $(this).attr('data-id');
			if(a == 'WayCityName') {
				$(this).remove();
			}
		})
		//ajax提交条件移除
		$('#AjaxWayCity span').attr('data-id', 'WayCityName').remove();
		$('#AjaxWayCity').append('<span data-id="WayCityName">All</span>');
		//途径热门
		$("#condition p[data-id='WayCityName']").remove();
		$('#AjaxWayCity span').remove();
		$('#AjaxWayCity').append('<span data-id="WayCityName">All</span>');
		$('#WayCityHot label').each(function() {
			var ThisClass = $(this).attr('class');
			if(ThisClass == 'cbt cb checked') {
				$(this).removeClass('checked');
				$(this).find('input').attr("checked", true);
				$(this).find('i').attr("checked", true);
			}
		})
		//途径a-z
		$(".moreList ul li label").each(function () {
			var ThisClass = $(this).attr('class');
			if(ThisClass == 'cbt cb checked') {
				$(this).removeClass('checked');
				$(this).find('input').attr("checked", true);
				$(this).find('i').attr("checked", true);
			}
		})
		//执行加载
		Ajax();
	}
})

//ajax提交的对应的参数
function Ajax(Page) {
	if(url[4] == 'home') {
		var ajaxData ={
			'Intention': 'TourLocalDeparture',
			'StartCity': $('#AjaxStartCity').text(),
			'WayCity': $('#AjaxWayCity').text(),
			'Theme': $('#AjaxTheme').text(),
			'Stroke': $('#AjaxStroke').text(),
			'StartDate': $('#AjaxStartDate').text(),
			'Sort': $('#AjaxSort').text(),
			'Page': Page,
			'Keyword': $('#AjaxKeyword').text(),
		}
	} else if(url[4] == 'local') {
		var ajaxData ={
			'Intention': 'TourLocalDeparture',
			'StartCity': $('#AjaxStartCity').text(),
			'EndCity':$('#AjaxEndCity').text(),
			'WayCity': $('#AjaxWayCity').text(),
			'Theme': $('#AjaxTheme').text(),
			'Stroke': $('#AjaxStroke').text(),
			'StartDate': $('#AjaxStartDate').text(),
			'Sort': $('#AjaxSort').text(),
			'Page': Page,
			'Keyword': $('#AjaxKeyword').text(),
		}
	}
	$.ajax({
		type: "post", //提交类型
		dataType: "json", //提交数据类型
		url: "", //提交地址
		data: ajaxData,
		beforeSend: function() { //加载过程效果
			$("#loading").show();
		},
		success: function(data) { //函数回调
			if(data.ResultCode == "200") {
				DataSuccess(data);
			} else if(data.ResultCode == "100") {
				layer.msg('加载出错，请刷新页面重新选择!');
			} else if(data.ResultCode == "101") {
				DataFailure(data);
			} else if(data.ResultCode == "102") { //搜索有内容
				$("#Position").empty();
				$("#Search").hide();
				$("#Position").append('> 搜索<span  style="color:red">'+'“'+ajaxData.Keyword +'”'+'</span>结果');
				DataSuccess(data);
			} else if(data.ResultCode == "103") { //搜索无内容
				$("#Position").empty();
				$("#Position").append('> 搜索<span  style="color:red">'+'“'+ajaxData.Keyword +'”'+'</span>结果');
				$("#Search").hide();
				$("#Nosearch").empty();
				$("#Nosearch").append('很抱歉，暂时无法找到符合您要求的产品。');
				$("#Filter").hide();
				$("#conditionpanel").hide();
				$(".Sequence").hide();
				DataFailure(data);
			}
		},
		complete: function() { //加载完成提示
			$("#loading").hide();
		}
	});
}

//ajax 200 102状态时执行的方法
function DataSuccess(data) {
	//产品条数注入
	$("#ProductNum").empty();
	$("#ProductNum").append(data.RecordCount);
	//hide没有找到产品div
	$("#NoProduct").hide();
	//产品列表注入
	$('#TourLineList').empty();
	if(url[4] == 'home') {
		var item;
		$.each(data.Data, function(i, list) {
			item = '<li>' +
				'<div class="ListLeft">' +
				'<p class="tit"><a href="' + list.TourUrl + '" title="' + list.Tour_name + '" target="_blank">' + list.Tour_name + '</a></p>' +
				'<div class="img">' +
				'<span data-id="' + list.TourRecommend + '" id="Recommend"></span>' +
				'<a href="' + list.TourUrl + '" title="' + list.Tour_name + '" target="_blank">' +
				'<img src="' + list.TourImg + '" class="transition" width="200" height="150"/>' +
				'</a>' +
				'<div class="TourLineFun">' +
				'<a href="javascript:void(0)" class="_MustLogin" id="collection" data-type="4" data-id="' + list.TourID + '"><i class="i1"></i>收藏</a>|<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=280612744&amp;site=qq&amp;menu=yes" target="_blank"><i class="i2"></i>在线咨询</a>' +
				'</div>' +
				'</div>' +
				'<p class="where"><span class="startcitytext">出发：' + list.TourStartCity + '</span></p>' +
				'<p class="ListService mt15">' + list.TourService + '</p>' +
				'<div class="ListIns mt15">' + list.TourDepict + '</div>' +
				'</div>' +
				'<div class="ListRight tac">' +
				'<p class="f16">行程天数：<span>' + list.TouStroke + '天</span></p>' +
				'<p class="nprcie f18 mt5">￥<span>' + list.TourPicre + '</span><i class="f14">/人起</i></p>' +
				'<p class="oldprice f12 ">原价：￥' + list.TourCostPrice + '</p>' +
				'<a href="' + list.TourUrl + '" class="CheckMore transition mt5" target="_blank">查看详情</a>' ;
                                if(list.Sales>0){
                                    item+='<p class="buynum">已有'+list.Sales+'人出游</p>';
                                }
                                item+='</div>'+
				'</li>';
			$('#TourLineList').append(item);
		});
		//出发城市结束文本为null清空
		$(".where").each(function() {
			var startcitytext = $(this).find('.startcitytext').text();
			var endcitytext = $(this).find('.endcitytext').text();
			if(startcitytext == '出发：null') {
				$(this).find('.startcitytext').text('');
			}
			if(endcitytext == '结束：null') {
				$(this).find('.endcitytext').text('');
			}
		})
	} else if(url[4] == 'local') {
		var item;
		$.each(data.Data, function (i, list) {
			item = '<li>' +
				'<div class="ListLeft">'+
				'<p class="tit"><a href="'+list.TourUrl+'" title="'+list.Tour_name+'" target="_blank">'+list.Tour_name+'</a></p>'+
				'<div class="img">'+
				'<span data-id="'+list.TourRecommend+'" id="Recommend"></span>'+
				'<a href="'+list.TourUrl+'" title="'+list.Tour_name+'" target="_blank">'+
				'<img src="'+list.TourImg+'" class="transition" width="200" height="150"/>'+
				'</a>'+
				'<div class="TourLineFun">'+
				'<a href="javascript:void(0)" class="_MustLogin" id="collection" data-type="4" data-id="'+list.TourID+'"><i class="i1"></i>收藏</a>|<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=280612744&amp;site=qq&amp;menu=yes" target="_blank"><i class="i2"></i>在线咨询</a>'+
				'</div>'+
				'</div>'+
				'<p class="where"><span class="startcitytext">出发：'+list.TourStartCity+'</span><span class="endcitytext">结束：'+list.TourEndCity+'</span></p>'+
				'<p class="ListService mt15">' +list.TourService+ '</p>'+
				'<div class="ListIns mt15">'+list.TourDepict+'</div>'+
				'</div>'+
				'<div class="ListRight tac">'+
				'<p class="f16">行程天数：<span class="c6">'+list.TouStroke+'天</span></p>'+
				'<p class="nprcie f18 mt5">￥<span>' + list.TourPicre + '</span><i class="f14">/人起</i></p>' +
				'<p class="oldprice f12 ">原价：￥' + list.TourCostPrice + '</p>' +
				'<a href="'+list.TourUrl+'" class="CheckMore transition mt5" target="_blank">查看详情</a>';
                                if(list.Sales>0){
                                    item+='<p class="buynum">已有'+list.Sales+'人出游</p>';
                                }
                                item+='</div>'+
				'</li>';
			$('#TourLineList').append(item);
		});
		//出发城市结束文本为null清空
		$(".where").each(function () {
			var startcitytext = $(this).find('.startcitytext').text();
			var endcitytext = $(this).find('.endcitytext').text();
			if(startcitytext == '出发：null'){
				$(this).find('.startcitytext').text('');
			}
			if(endcitytext == '结束：null'){
				$(this).find('.endcitytext').text('');
			}
		})
	}

		//如果推荐增加class
	$("#Recommend").each(function() {
			var a = $(this).attr('data-id');
			if(a == '1') {
				$(this).addClass('HotTj1');
			}
		})
		//分页机制
	if($("#Page").attr('data-type') == '0'){
		$("#Page2").hide();
		$("#Page").attr('data-type','1');
	}
	if(data.PageCount > 1){
		diffPage(data);
		$("#Page").show();
	}else {
		$("#Page").hide();
	}

}

//ajax 101 103状态时执行的方法
function DataFailure(data) {
	$("#NoProduct").show(); //show没有找到产品div
	//产品条数注入
	$("#ProductNum").empty();
	$("#ProductNum").append('0');
	$('#TourLineList').empty();
	$('#Page').empty();
	//产品列表注入
	$('#TourLineList').empty();
	if(url[4] == 'home') {
		var item;
		$.each(data.Data, function(i, list) {
			item = '<li>' +
				'<div class="ListLeft">' +
				'<p class="tit"><a href="' + list.TourUrl + '" title="' + list.Tour_name + '" target="_blank">' + list.Tour_name + '</a></p>' +
				'<div class="img">' +
				'<span data-id="' + list.TourRecommend + '" id="Recommend"></span>' +
				'<a href="' + list.TourUrl + '" title="' + list.Tour_name + '" target="_blank">' +
				'<img src="' + list.TourImg + '" class="transition" width="200" height="150"/>' +
				'</a>' +
				'<div class="TourLineFun">' +
				'<a href="javascript:void(0)" class="_MustLogin" id="collection" data-type="4" data-id="' + list.TourID + '"><i class="i1"></i>收藏</a>|<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=280612744&amp;site=qq&amp;menu=yes" target="_blank"><i class="i2"></i>在线咨询</a>' +
				'</div>' +
				'</div>' +
				'<p class="where"><span class="startcitytext">出发：' + list.TourStartCity + '</span></p>' +
				'<p class="ListService mt15">' + list.TourService + '</p>' +
				'<div class="ListIns mt15">' + list.TourDepict + '</div>' +
				'</div>' +
				'<div class="ListRight tac">' +
				'<p class="f16">行程天数：<span>' + list.TouStroke + '天</span></p>' +
				'<p class="nprcie f18 mt5">￥<span>' + list.TourPicre + '</span><i class="f14">/人起</i></p>' +
				'<p class="oldprice f12 ">原价：￥' + list.TourCostPrice + '</p>' +
				'<a href="' + list.TourUrl + '" class="CheckMore transition mt5" target="_blank">查看详情</a>' ;
                                if(list.Sales>0){
                                    item+='<p class="buynum">已有'+list.Sales+'人出游</p>';
                                }
                                item+='</div>'+
				'</li>';
			$('#TourLineList').append(item);
		});
		//出发城市结束文本为null清空
		$(".where").each(function() {
			var startcitytext = $(this).find('.startcitytext').text();
			var endcitytext = $(this).find('.endcitytext').text();
			if(startcitytext == '出发：null') {
				$(this).find('.startcitytext').text('');
			}
			if(endcitytext == '结束：null') {
				$(this).find('.endcitytext').text('');
			}
		})
	} else if(url[4] == 'local') {
		var item;
		$.each(data.Data, function (i, list) {
			item = '<li>' +
				'<div class="ListLeft">'+
				'<p class="tit"><a href="'+list.TourUrl+'" title="'+list.Tour_name+'" target="_blank">'+list.Tour_name+'</a></p>'+
				'<div class="img">'+
				'<span data-id="'+list.TourRecommend+'" id="Recommend"></span>'+
				'<a href="'+list.TourUrl+'" title="'+list.Tour_name+'" target="_blank">'+
				'<img src="'+list.TourImg+'" class="transition" width="200" height="150"/>'+
				'</a>'+
				'<div class="TourLineFun">'+
				'<a href="javascript:void(0)" class="_MustLogin" id="collection" data-type="4" data-id="'+list.TourID+'"><i class="i1"></i>收藏</a>|<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=280612744&amp;site=qq&amp;menu=yes" target="_blank"><i class="i2"></i>在线咨询</a>'+
				'</div>'+
				'</div>'+
				'<p class="where"><span class="startcitytext">出发：'+list.TourStartCity+'</span><span class="endcitytext">结束：'+list.TourEndCity+'</span></p>'+
				'<p class="ListService mt15">' +list.TourService+ '</p>'+
				'<div class="ListIns mt15">'+list.TourDepict+'</div>'+
				'</div>'+
				'<div class="ListRight tac">'+
				'<p class="f16">行程天数：<span class="c6">'+list.TouStroke+'天</span></p>'+
				'<p class="nprcie f18 mt5">￥<span>' + list.TourPicre + '</span><i class="f14">/人起</i></p>' +
				'<p class="oldprice f12 ">原价：￥' + list.TourCostPrice + '</p>' +
				'<a href="'+list.TourUrl+'" class="CheckMore transition mt5" target="_blank">查看详情</a>';
                                if(list.Sales>0){
                                    item+='<p class="buynum">已有'+list.Sales+'人出游</p>';
                                }
                                item+='</div>'+
				'</li>';
			$('#TourLineList').append(item);
		});
		//出发城市结束文本为null清空
		$(".where").each(function () {
			var startcitytext = $(this).find('.startcitytext').text();
			var endcitytext = $(this).find('.endcitytext').text();
			if(startcitytext == '出发：null'){
				$(this).find('.startcitytext').text('');
			}
			if(endcitytext == '结束：null'){
				$(this).find('.endcitytext').text('');
			}
		})
	}
	//如果推荐增加class
	$("#Recommend").each(function() {
		var a = $(this).attr('data-id');
		if(a == '1') {
			$(this).addClass('HotTj1');
		}
	})
}