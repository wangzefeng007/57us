/**
 * Created by Foliage on 2016/12/29.
 */
$(function(){
    //top幻灯片滚动
    jQuery(".studytBan").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:600, trigger:"click"});
    //获取遮罩的高度
    $(".DayBoxNr .line").each(function(){
        var allHeight = $(this).siblings("ul").height()
        var zzheight = $(this).siblings("ul").find("li").last().find(".suNr").height()
        $(this).height(allHeight-zzheight-35)
    });

    //游学主题
    $('#Theme').empty();
    $.get('/Templates/Study/data/StudyTour/Theme.json', function(data) {
        var item;
        $.each(data, function(i, Level) {
            item = '<li>' +
                '<label name="cbt" type="checkbox" val="' + Level.id + '" class="cbt"><i></i>' + Level.name + '</label>' +
                '</li>';
            $('#Theme').append(item);
        });

        var Filter = {
            'idname': 'Theme',
            'nini': 'ThemeAll',
            'dataidname': 'ThemeName',
            'ajaxname': 'AjaxTheme',
        }
        TourCheckbox(Filter);
    }, 'json')

    //适合人群
    $('#Crowd').empty();
    $.get('/Templates/Study/data/StudyTour/Crowd.json', function(data) {
        var item;
        $.each(data, function(i, Level) {
            item = '<li>' +
                '<label name="cbt" type="checkbox" val="' + Level.id + '" class="cbt"><i></i>' + Level.name + '</label>' +
                '</li>';
            $('#Crowd').append(item);
        });

        var Filter = {
            'idname': 'Crowd',
            'nini': 'CrowdAll',
            'dataidname': 'CrowdName',
            'ajaxname': 'AjaxCrowd',
        }
        TourCheckbox(Filter);
    }, 'json')

    //出行时间
    $('#Date').empty();
    $.get('/Templates/Study/data/StudyTour/Date.json', function(data) {
        var item;
        $.each(data, function(i, Level) {
            item = '<li>' +
                '<label name="cbt" type="checkbox" val="' + Level.id + '" class="cbt"><i></i>' + Level.name + '</label>' +
                '</li>';
            $('#Date').append(item);
        });

        var Filter = {
            'idname': 'Date',
            'nini': 'DateAll',
            'dataidname': 'DateName',
            'ajaxname': 'AjaxDate',
        }
        TourCheckbox(Filter);
    }, 'json')

    //出行地
    $('#StartCity').empty();
    $.get('/Templates/Study/data/StudyTour/StartCity.json', function(data) {
        var item;
        $.each(data, function(i, Level) {
            item = '<li>' +
                '<label name="cbt" type="checkbox" val="' + Level.id + '" class="cbt"><i></i>' + Level.name + '</label>' +
                '</li>';
            $('#StartCity').append(item);
        });

        var Filter = {
            'idname': 'StartCity',
            'nini': 'StartCityAll',
            'dataidname': 'StartCityName',
            'ajaxname': 'AjaxStartCity',
        }
        TourCheckbox(Filter);
    }, 'json')
})


//ajax提交的对应的参数
function Ajax(Page) {
    //游学主题
    var Theme = [];
    $('#AjaxTheme span').each(function () {
        Theme.push($(this).attr("data-id"));
    })
    //适合人群
    var Crowd = [];
    $('#AjaxCrowd span').each(function () {
        Crowd.push($(this).attr("data-id"));
    })
    //出行天数
    var Date = [];
    $('#AjaxDate span').each(function () {
        Date.push($(this).attr("data-id"));
    })
    //出行地
    var StartCity = [];
    $('#AjaxStartCity span').each(function () {
        StartCity.push($(this).attr("data-id"));
    })
    var Sort = $('#AjaxSort').text();
    var Keyword = $('#AjaxKeyword').text();

    var ajaxData = {
        'Intention': 'StudyTour', //方法
        'Theme': Theme, //游学主题
        'Crowd':Crowd, //适合人群
        'Date':Date, //出行天数
        'StartCity':StartCity, //出行地
        'Sort':Sort, //排序
        'Page':Page, //分页
        'Keyword':Keyword,  //关键字
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/studytourajax",
        data: ajaxData,
        beforeSend: function () { //加载过程效果
            $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                DataSuccess(data)
            }else if(data.ResultCode == "100"){
                layer.msg('加载出错，请刷新页面重新选择!');
            }else if(data.ResultCode == "101"){
                DataFailure(data);
            }else if(data.ResultCode == "102"){     //搜索有内容
                $("#Position").empty();
                $("#Position").append(' > 搜索“'+Keyword+'”结果');
                DataSuccess(data);
            }else if(data.ResultCode == "103"){ //搜索无内容
                $("#Position").empty();
                $("#Position").append(' > 搜索“'+Keyword+'”结果');
                $("#Nosearch").empty();
                $("#Nosearch").append('sorry，没有找到“ <span>'+Keyword+'</span>”相关的线路！');
                // $("#Filter").hide();
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
    $('#StudyLineList').empty();
    var item;
    $.each(data.Data, function(i, list) {
        item =  '<li>' +
            '<span class="Recommend " data-id="'+list.Study_Recommend+'"></span>' +
            '<img src="'+list.Study_Img+'" width="286" height="181" title="'+list.Study_Name+'" alt="'+list.Study_Name+'"/>' +
            '<div class="listM">' +
            '<p class="tit">'+list.Study_Name+'</p>' +
            '<p class="time mt10">报名截止时间：'+list.Study_Date+'</p>' +
            '<p class="price">' +
            '<span class="fl oldPrice">原价：&yen;'+list.Study_OriginalPrice+'</span>' +
            '<span class="fr nowPrice">现价：<em class="red f18">￥<i>'+list.Study_Picre+'</i></em></span>' +
            '</p>' +
            '</div>' +
            '<div class="listMask">' +
            '<a href="'+list.Study_Url+'" title="'+list.Study_Name+'" target="_blank">查看详情</a>' +
            '</div>' +
            '</li>';
        $('#StudyLineList').append(item);
    });
    //如果推荐增加class
    $(".Recommend").each(function () {
        var a = $(this).attr('data-id');
        if(a == '1'){
            $(this).addClass('HotTj1');
        }
    })
    //分页机制
    if($("#Page").attr('data-type') == 0){
        $("#Page").attr('data-type','1');
        $("#Page2").remove();
    }
    if(data.PageCount > 1){
        diffPage(data,1);
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
    $('#Page').empty();
    //产品列表注入
    $('#StudyLineList').empty();
    var item;
    $.each(data.Data, function(i, list) {
        item =  '<li>' +
            '<span class="Recommend " data-id="'+list.Study_Recommend+'"></span>' +
            '<img src="'+list.Study_Img+'" width="286" height="181" title="'+list.Study_Name+'" alt="'+list.Study_Name+'"/>' +
            '<div class="listM">' +
            '<p class="tit">'+list.Study_Name+'</p>' +
            '<p class="time mt10">报名截止时间：'+list.Study_Date+'</p>' +
            '<p class="price">' +
            '<span class="fl oldPrice">原价：&yen;'+list.Study_OriginalPrice+'</span>' +
            '<span class="fr nowPrice">现价：<em class="red f18">￥<i>'+list.Study_Picre+'</i></em></span>' +
            '</p>' +
            '</div>' +
            '<div class="listMask">' +
            '<a href="'+list.Study_Url+'" title="'+list.Study_Name+'" target="_blank">查看详情</a>' +
            '</div>' +
            '</li>';
        $('#StudyLineList').append(item);
    });
    //如果推荐增加class
    $(".Recommend").each(function () {
        var a = $(this).attr('data-id');
        if(a == '1'){
            $(this).addClass('HotTj1');
        }
    })
}