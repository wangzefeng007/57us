/**
 * Created by Foliage on 2016/10/28.
 */
$(function () {
    //层次
    $('#Experience').empty();
    $.get('/Templates/Study/data/Consultant/Experience.json', function(data) {
        var item;
        $.each(data, function(i, Level) {
            item = '<li>' +
                '<label name="cbt" type="checkbox" val="' + Level.id + '" class="cbt"><i></i>' + Level.name + '</label>' +
                '</li>';
            $('#Experience').append(item);
        });

        var Filter = {
            'idname': 'Experience',
            'nini': 'ExperienceAll',
            'dataidname': 'ExperienceName',
            'ajaxname': 'AjaxExperience',
        }
        TourCheckbox(Filter);
    }, 'json')

    //选择区域
    $('#Region').empty();
    $.get('/teacher/getcity', function(data) {
        var item;
        $.each(data, function(i, Region) {
            item = '<li>' +
                '<label name="cbt2" type="checkbox" val="' + Region.AeraID + '" class="cbt"><i></i>' + Region.name + '</label>' +
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
    //申请层次
    var Experience = [];
    $('#AjaxExperience span').each(function () {
        Experience.push($(this).attr("data-id"));
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
        url: "/teacherajax/",  //提交地址
        data: {	//提交数据
            'Intention': 'TeacherLists',
            'Experience': Experience,
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
                $("#Nosearch").append('sorry，没有找到“ <span>'+Keyword+'</span>”相关的教师！');
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
    $("#Page").hide();
    //产品列表注入
    $('#StudyLineList').empty();
    var item;
    $.each(data.Data, function(i, list) {
        item =  '<li data-id="'+list.StudyID+'">' +
            '<a href="'+list.StudyUrl+'" title="'+list.Study_name+'" target="_blank">' +
            '<p class="img">' +
            '<i class="sex" data-type="'+list.StudySex+'"></i>' +
            '<img src="'+list.StudyImg+'" width="96" height="96" alt="'+list.Study_name+'"></p>' +
            '<div class="contRight">' +
            '<p class="f22 mt10">'+list.Study_name+'</p>' +
            '<p class="mt10">从业'+list.StudyExperience+'年<span class="pl10"><i class="mapIco"></i>'+list.StudyServiceRegion+'</span></p>' +
            '</div>' +
            '<p class="counTip mt15">'+list.StudyTag+'</p>' +
            '<p class="counIns mt20"><span class="green">教师简介：</span>'+list.StudyDepict+'</p>' +
            '</a>' +
            '</li>';
        $('#StudyLineList').append(item);
    });
    $("#StudyLineList .sex").each(function () {
        var sex = $(this).attr('data-type');
        if(sex == '0'){
            $(this).addClass('woman');
        }else {
            $(this).addClass('man');
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
        item =  '<li data-id="'+list.StudyID+'">' +
            '<a href="'+list.StudyUrl+'" title="'+list.Study_name+'" target="_blank">' +
            '<p class="img">' +
            '<i class="sex" data-type="'+list.StudySex+'"></i>' +
            '<img src="'+list.StudyImg+'" width="96" height="96" alt="'+list.Study_name+'"></p>' +
            '<div class="contRight">' +
            '<p class="f22 mt10">'+list.Study_name+'</p>' +
            '<p class="mt10">从业'+list.StudyExperience+'年<span class="pl10"><i class="mapIco"></i>'+list.StudyServiceRegion+'</span></p>' +
            '</div>' +
            '<p class="counTip mt15">'+list.StudyTag+'</p>' +
            '<p class="counIns mt20"><span class="green">教师简介：</span>'+list.StudyDepict+'</p>' +
            '</a>' +
            '</li>';
        $('#StudyLineList').append(item);
    });
    $("#StudyLineList .sex").each(function () {
        var sex = $(this).attr('data-type');
        if(sex == '0'){
            $(this).addClass('woman');
        }else {
            $(this).addClass('man');
        }
    })
}