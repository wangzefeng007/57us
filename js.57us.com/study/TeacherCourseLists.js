/**
 * Created by Foliage on 2016/10/20.
 */
$(function () {
    var Type = GetQueryString('t');
    //培训科目
    $('#TrainSubject').empty();
    $.get('/Templates/Study/data/Teacher/TrainSubject.json', function(data) {
        var item;
        $.each(data, function(i, TrainSubject) {
            item = '<li>' +
                '<label name="cbt" type="checkbox" val="' + TrainSubject.id + '" class="cbt"><i></i>' + TrainSubject.name + '</label>' +
                '</li>';
            $('#TrainSubject').append(item);
        });
        var Filter = {
            'idname': 'TrainSubject',
            'nini': 'TrainSubjectAll',
            'dataidname': 'TrainSubjectName',
            'ajaxname': 'AjaxTrainSubject',
        }
        TourCheckbox(Filter);
        //从nav进来，根据内容进去订位
        if(Type == '1'){
            $("#TrainSubjectAll").parent().removeClass('on');
            $("#TrainSubject li").eq(0).find('label').addClass('checked');
        }else if(Type == '2'){
            $("#TrainSubjectAll").parent().removeClass('on');
            $("#TrainSubject li").eq(1).find('label').addClass('checked');
        }else if(Type == '3'){
            $("#TrainSubjectAll").parent().removeClass('on');
            $("#TrainSubject li").eq(2).find('label').addClass('checked');
        }else if(Type == '4'){
            $("#TrainSubjectAll").parent().removeClass('on');
            $("#TrainSubject li").eq(3).find('label').addClass('checked');
        }else if(Type == '5'){
            $("#TrainSubjectAll").parent().removeClass('on');
            $("#TrainSubject li").eq(4).find('label').addClass('checked');
        }else if(Type == '6'){
            $("#TrainSubjectAll").parent().removeClass('on');
            $("#TrainSubject li").eq(5).find('label').addClass('checked');
        }else if(Type == '7'){
            $("#TrainSubjectAll").parent().removeClass('on');
            $("#TrainSubject li").eq(6).find('label').addClass('checked');
        }
    }, 'json')

    //上课方式
    $('#FormClass').empty();
    $.get('/Templates/Study/data/Teacher/FormClass.json', function(data) {
        var item;
        $.each(data, function(i, FormClass) {
            item = '<li>' +
                '<label name="cbt1" type="checkbox" val="' + FormClass.id + '" class="cbt"><i></i>' + FormClass.name + '</label>' +
                '</li>';
            $('#FormClass').append(item);
        });
        var Filter = {
            'idname': 'FormClass',
            'nini': 'FormClassAll',
            'dataidname': 'FormClassName',
            'ajaxname': 'AjaxFormClass',
        }
        TourCheckbox(Filter);
    }, 'json')

    //选择区域
    $('#Region').empty();
    $.get('/consultant/getcity', function(data) {
        var item;
        $.each(data, function(i, Region) {
            item = '<li>' +
                '<label name="rbt2" type="checkbox" val="' + Region.AeraID + '" class="cbt"><i></i>' + Region.name + '</label>' +
                '</li>';
            $('#Region').append(item);
        });
        var Filter = {
            'idname': 'Region',
            'nini': 'RegionAll',
            'dataidname': 'RegionName',
            'ajaxname': 'AjaxRegion',
        }
        TourCheckbox(Filter);
    }, 'json')
})

//ajax提交的对应的参数
function Ajax(Page) {
    //培训科目
    var TrainSubject = [];
    $('#AjaxTrainSubject span').each(function () {
        TrainSubject.push($(this).attr("data-id"));
    })
    //上课方式
    var FormClass = [];
    $('#AjaxFormClass span').each(function () {
        FormClass.push($(this).attr("data-id"));
    })
    //选择区域
    var Region = [];
    $('#AjaxRegion span').each(function () {
        Region.push($(this).attr("data-id"));
    })
    var Sort = $('#AjaxSort').text();
    var Keyword = $('#AjaxKeyword').text();
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/teacherajax/",
        data: {
            'Intention': 'CourseLists', //方法
            'TrainSubject': TrainSubject, //培训科目 TOEFL IELTS SAT ACT GRE GMAT PTE
            'FormClass':FormClass, //上课方式 1线上 2线下
            'Region':Region,
            'Sort':Sort,
            'Page':Page,
            'Keyword':Keyword,
        },
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
                $("#Nosearch").append('sorry，没有找到“ <span>'+Keyword+'</span>”相关的课程！');
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
            '<div class="ListLeft">' +
            '<p class="tit">' +
            '<a href=" '+list.StudyUrl + '" title="' + list.Study_name + '" target="_blank">' + list.Study_name + '</a>' +
            '</p>' +
            '<div class="img">' +
            '<a href="' + list.StudyUrl + '" title="' + list.Study_name + '" target="_blank">' +
            '<img src="' + list.StudyImg + '" class="transition" width="200" height="150" alt="'+list.Study_name+'"/>' +
            '</a>' +
            '</div>' +
            '<p class="where">' +
            '<span>科目：' + list.StudyTrainSubject + '</span>'+
            '<span>上课方式：' + list.StudyFormClass + '</span>'+
            '<span>地区：' + list.StudyServiceRegion + '</span>'+
            '</p>' +
            '<p class="ListService mt15">' + list.StudyService + '</p>' +
            '<p class="ListDescion mt10"><b>课程简介</b>：' + list.StudyDepict + '</p>' +
            '</div>' +
            '<div class="ListRight">' +
            '<span class="price">&yen;<em>' + list.StudyPicre + '</em>'+'<i>/课时</i></span>' +
            '<a href="' + list.StudyUrl +'" class="CheckMore transition mt15" target="_blank">查看详情</a>'+
            '<a href="" class="Collect" style="display: none">收藏</a>'+
            '</div>' +
            '</li>';
        $('#StudyLineList').append(item);
    });
    //分页机制
    if($("#Page").attr('data-type') == 0){
        $("#Page").attr('data-type','1');
        $("#Page2").remove();
    }
    if(data.PageCount > 1){
        diffPage(data,1);
        $("#Page").show();
    }else{
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
            '<div class="ListLeft">' +
            '<p class="tit">' +
            '<a href=" '+list.StudyUrl + '" title="' + list.Study_name + '" target="_blank">' + list.Study_name + '</a>' +
            '</p>' +
            '<div class="img">' +
            '<a href="' + list.StudyUrl + '" title="' + list.Study_name + '" target="_blank">' +
            '<img src="' + list.StudyImg + '" class="transition" width="200" height="150" alt="'+list.Study_name+'"/>' +
            '</a>' +
            '</div>' +
            '<p class="where">' +
            '<span>科目：' + list.StudyTrainSubject + '</span>'+
            '<span>上课方式：' + list.StudyFormClass + '</span>'+
            '<span>地区：' + list.StudyServiceRegion + '</span>'+
            '</p>' +
            '<p class="ListService mt15">' + list.StudyService + '</p>' +
            '<p class="ListDescion mt10"><b>课程简介</b>：' + list.StudyDepict + '</p>' +
            '</div>' +
            '<div class="ListRight">' +
            '<span class="price">&yen;<em>' + list.StudyPicre + '</em></span>' +
            '<a href="' + list.StudyUrl +'" class="CheckMore transition mt15" target="_blank">查看详情</a>'+
            '<a href="" class="Collect" style="display: none">收藏</a>'+
            '</div>' +
            '</li>';
        $('#StudyLineList').append(item);
    });
}