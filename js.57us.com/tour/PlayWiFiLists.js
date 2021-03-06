/**
 * Created by Foliage on 2016/8/15.
 */
$(function () {
    //目的地
    $('#EndCity').empty();
    $.get('/Templates/Tour/data/WiFi/AreaEnter.json', function(data) {
        var item;
        $.each(data, function(i, EndCity) {
            item = '<li>' +
                '<label name="rbt" type="radiobox" val="' + EndCity.AeraID + '" class="cbt"><i></i>' + EndCity.name + '</label>' +
                '</li>';
            $('#EndCity').append(item);
        });
        var Filter = {
            'idname': 'EndCity',
            'nini': 'EndCityAll',
            'dataidname': 'EndCityName',
            'ajaxname': 'AjaxEndCity',
            'textname':'目的地：',
        }
        TourRadio(Filter);
        ClickLoad(Filter);
    }, 'json');
    //目的地
    $('#Type').empty();
    $.get('/Templates/Tour/data/WiFi/Types.json', function(data) {
        var item;
        $.each(data, function(i, Type) {
            item = '<li>' +
                '<label name="rbt" type="radiobox" val="' + Type.name + '" class="cbt"><i></i>' + Type.name + '</label>' +
                '</li>';
            $('#Type').append(item);
        });
        var Filter = {
            'idname': 'Type',
            'nini': 'TypeAll',
            'dataidname': 'TypeName',
            'ajaxname': 'AjaxType',
            'textname':'服务类型：',
        }
        TourRadio(Filter);
        ClickLoad(Filter);
    }, 'json');
})

//ajax提交的对应的参数
function Ajax(Page) {
    var EndCity = $('#AjaxEndCity').text();
    var Type = $('#AjaxType').text();
    var Sort = $('#AjaxSort').text();
    var Keyword = $('#AjaxKeyword').text();
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "",  //提交地址
        data: {	//提交数据
            'Intention': 'WiFi',
            'EndCity':EndCity,
            'Type':Type,
            'Sort':Sort,
            'Page':Page,
            'Keyword':Keyword,
        },
        beforeSend: function () { //加载过程效果
            $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                DataSuccess(data);
            }else if(data.ResultCode == "100"){
                layer.msg('加载出错，请刷新页面重新选择!');
            }else if(data.ResultCode == "101"){
                DataFailure(data);
            }else if(data.ResultCode == "102"){     //搜索有内容
                $("#Position").empty();
                $("#Search").hide();
                $("#Position").append('> 搜索<span  style="color:red">'+'“'+Keyword+'”'+'</span>结果');
                DataSuccess(data);
            }else if(data.ResultCode == "103"){ //搜索无内容
                $("#Position").empty();
                $("#Search").hide();
                $("#Position").append('> 搜索<span  style="color:red">'+'“'+Keyword+'”'+'</span>结果');
                $("#Nosearch").empty();
                $("#Nosearch").append('很抱歉，暂时无法找到符合您要求的产品。');
                $("#Filter").hide();
                $("#conditionpanel").hide();
                $(".Sequence").hide();
                DataFailure(data);
            }
        },
        complete: function () { //加载完成提示
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
    $('#CharacList').empty();
    var item;
    $.each(data.Data, function (i, list) {
        item =  '<li class="transition">' +
            '<span data-id="'+list.TourRecommend+'" id="Recommend"></span>'+
            '<a href="'+list.TourUrl+'" target="_blank">'+
            '<p class="img"><img src="'+list.TourImg+'" class="transition" width="370" height="277" title="'+list.Tour_name+'"/></p>'+
            '<p class="destination f16"><span class="endcitytext">目的地：'+list.TourEndCity+'</span></p>'+
            '<p class="tit" title="'+list.Tour_name+'">'+list.Tour_name+'</p>'+
            '</a>'+
            '<div class="CharacListB">'+
            '<p class="fr">'+
            '<span class="fr oldPrice">￥'+list.TourCostPrice+'</span>'+
            '<span class="fl nowPrice"><em>￥</em><i>'+list.TourPicre+'</i> 起</span>'+
            '</p>'+
            '<span class="fl playDate"></span>'+
            '</div>'+
            '</li>';
        $('#CharacList').append(item);
    });
    //出发城市结束文本为null清空
    $(".destination").each(function() {
        var startcitytext = $(this).find('.startcitytext').text();
        var endcitytext = $(this).find('.endcitytext').text();
        if(startcitytext == '出发：null') {
            $(this).find('.startcitytext').text('');
        }
        if(endcitytext == '目的地：null') {
            $(this).find('.endcitytext').text('');
        }
    })
    //如果推荐增加class
    $("#Recommend").each(function () {
        var a = $(this).attr('data-id');
        if(a == '1'){
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
    //注入前清空
    $('#CharacList').empty();
    var item;
    $.each(data.Data, function (i, list) {
        item =  '<li class="transition">' +
            '<span data-id="'+list.TourRecommend+'" id="Recommend"></span>'+
            '<a href="'+list.TourUrl+'" target="_blank">'+
            '<p class="img">' +
            '<img src="'+list.TourImg+'" class="transition" width="370" height="277" title="'+list.Tour_name+'"/>' +
            '</p>'+
            '<p class="destination f16"><span class="endcitytext">目的地：'+list.TourEndCity+'</span></p>'+
            '<p class="tit">'+list.Tour_name+'</p>'+
            '</a>'+
            '<div class="CharacListB">'+
            '<p class="fr">'+
            '<span class="fr oldPrice">￥'+list.TourCostPrice+'</span>'+
            '<span class="fl nowPrice"><em>￥</em><i>'+list.TourPicre+'</i> 起</span>'+
            '</p>'+
            '<span class="fl">游玩时间：'+list.TouDate+'</span>'+
            '</div>'+
            '</li>';
        $('#CharacList').append(item);
    });
    //出发城市结束文本为null清空
    $(".destination").each(function() {
        var startcitytext = $(this).find('.startcitytext').text();
        var endcitytext = $(this).find('.endcitytext').text();
        if(startcitytext == '出发：null') {
            $(this).find('.startcitytext').text('');
        }
        if(endcitytext == '目的地：null') {
            $(this).find('.endcitytext').text('');
        }
    })
    //如果推荐增加class
    $("#Recommend").each(function () {
        var a = $(this).attr('data-id');
        if(a == '1'){
            $(this).addClass('HotTj1');
        }
    })
}
