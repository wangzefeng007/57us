/**
 * Created by Foliage on 2016/10/20.
 */
$(function () {
    //学校类型
    $('#SchoolType').empty();
    $.get('/Templates/Study/data/School/College/SchoolType.json', function(data) {
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label name="cbt" type="checkbox" val="' + list.id + '" class="cbt"><i></i>' + list.name + '</label>' +
                '</li>';
            $('#SchoolType').append(item);
        });
        var Filter = {
            'idname': 'SchoolType',
            'nini': 'SchoolTypeAll',
            'dataidname': 'SchoolTypeeName',
            'ajaxname': 'AjaxSchoolType',
        }
        TourCheckbox(Filter);
    }, 'json')

    //学校排名
    $('#SchooRanking').empty();
    $.get('/Templates/Study/data/School/College/SchooRanking.json', function(data) {
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label name="cbt1" type="checkbox" val="' + list.id + '" class="cbt"><i></i>' + list.name + '</label>' +
                '</li>';
            $('#SchooRanking').append(item);
        });
        var Filter = {
            'idname': 'SchooRanking',
            'nini': 'SchooRankingAll',
            'dataidname': 'SchooRankingName',
            'ajaxname': 'AjaxSchooRanking',
        }
        TourCheckbox(Filter);
    }, 'json')

    //年总费用
    $('#AnnualCost').empty();
    $.get('/Templates/Study/data/School/College/AnnualCost.json', function(data) {
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label name="cbt2" type="checkbox" val="' + list.id + '" class="cbt"><i></i>' + list.name + '</label>' +
                '</li>';
            $('#AnnualCost').append(item);
        });
        var Filter = {
            'idname': 'AnnualCost',
            'nini': 'AnnualCostAll',
            'dataidname': 'AnnualCostName',
            'ajaxname': 'AjaxAnnualCost',
        }
        TourCheckbox(Filter);
    }, 'json')

    //托福
    $('#TOEFL').empty();
    $.get('/Templates/Study/data/School/College/TOEFL.json', function(data) {
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label name="cbt3" type="checkbox" val="' + list.id + '" class="cbt"><i></i>' + list.name + '</label>' +
                '</li>';
            $('#TOEFL').append(item);
        });
        var Filter = {
            'idname': 'TOEFL',
            'nini': 'TOEFLAll',
            'dataidname': 'TOEFLName',
            'ajaxname': 'AjaxTOEFL',
        }
        TourCheckbox(Filter);
    }, 'json')

    //SAT
    $('#SAT').empty();
    $.get('/Templates/Study/data/School/College/SAT.json', function(data) {
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label name="cbt4" type="checkbox" val="' + list.id + '" class="cbt"><i></i>' + list.name + '</label>' +
                '</li>';
            $('#SAT').append(item);
        });
        var Filter = {
            'idname': 'SAT',
            'nini': 'SATAll',
            'dataidname': 'SATName',
            'ajaxname': 'AjaxSAT',
        }
        TourCheckbox(Filter);
    }, 'json')

    //地理位置
    $('#Location').empty();
    $.get('/Templates/Study/data/School/College/Location.json', function(data) {
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label name="cbt5" type="checkbox" val="' + list.AeraID + '" class="cbt"><i></i>' + list.name + '</label>' +
                '</li>';
            $('#Location').append(item);
        });
        var Filter = {
            'idname': 'Location',
            'nini': 'LocationAll',
            'dataidname': 'LocationName',
            'ajaxname': 'AjaxLocation',
        }
        TourCheckbox(Filter);
    }, 'json')
})

//ajax提交的对应的参数
function Ajax(Page) {
    var SchoolType = [];
    $('#AjaxSchoolType span').each(function () {
        SchoolType.push($(this).attr("data-id"));
    })
    var Major = [];
    $('#AjaxMajor span').each(function () {
        Major.push($(this).attr("data-id"));
    })
    var SchooRanking = [];
    $('#AjaxSchooRanking span').each(function () {
        SchooRanking.push($(this).attr("data-id"));
    })
    var AnnualCost = [];
    $('#AjaxAnnualCost span').each(function () {
        AnnualCost.push($(this).attr("data-id"));
    })
    var TOEFL = [];
    $('#AjaxTOEFL span').each(function () {
        TOEFL.push($(this).attr("data-id"));
    })
    var SAT = [];
    $('#AjaxSAT span').each(function () {
        SAT.push($(this).attr("data-id"));
    })
    var Location = [];
    $('#AjaxLocation span').each(function () {
        Location.push($(this).attr("data-id"));
    })
    var Sort = $('#AjaxSort').text();
    var Keyword = $('#AjaxKeyword').text();
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "",
        data: {
            'Intention': 'CourseLists', //方法
            'SchoolType':SchoolType, //学校类型
            'Major': Major, //专业
            'SchooRanking':SchooRanking, //学校排名
            'AnnualCost':AnnualCost, //年总费用
            'TOEFL':TOEFL, //托福
            'SAT':SAT, //SAT
            'Location':Location, //地理位置
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
                $("#Nosearch").append('sorry，没有找到“ <span>'+Keyword+'</span>”相关的院校！');
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
        item =  '<li data-id="'+list.StudyID+'">' +
            '<div class="ListRight">' +
            '<a href="'+list.StudyUrl+'" class="CheckMore transition mt50" target="_blank" title="'+list.Study_name+'">查看详情</a>' +
            '<a href="" class="Collect mt20" style="display: none">收藏</a>' +
            '</div>' +
            '<a href="'+list.StudyUrl+'" target="_blank"><img class="transition" width="100%" src="'+list.StudyImg+'" width="200" height="150" alt="'+list.Study_name+'"></a>' +
            '<div class="ListCont">' +
            '<p class="tit"><a href="'+list.StudyUrl+'" target="_blank">'+list.Study_name+'</a></p>' +
            '<table border="0" cellspacing="0" cellpadding="0" width="100%" class="mt10">' +
            '<tr>' +
            '<td colspan="2">学校排名：'+list.StudySchooRanking+'</td>' +
            '</tr>' +
            '<tr>' +
            '<td colspan="2">学校地点：'+list.StudyLocation+'</td>' +
            '</tr>' +
            '<tr>' +
            '<td width="50%">录取率：'+list.StudyAcceptanceRate+'</td>' +
            '<td width="50%">总费用：'+list.StudyAnnualCost+'</td>' +
            '</tr>' +
            '<tr>' +
            '<td>托福成绩：'+list.StudyTOEFL+'</td>' +
            '<td>SAT II 成绩：'+list.StudySAT+'</td>' +
            '</tr>' +
            '</table>' +
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
        item =  '<li data-id="'+list.StudyID+'">' +
            '<div class="ListRight">' +
            '<a href="'+list.StudyUrl+'" class="CheckMore transition mt50">查看详情</a>' +
            '<a href="" class="Collect mt20" style="display: none">收藏</a>' +
            '</div>' +
            '<img class="transition" width="100%" src="'+list.StudyImg+'" width="200" height="150" alt="'+list.Study_name+'">' +
            '<div class="ListCont">' +
            '<p class="tit">'+list.Study_name+'</p>' +
            '<table border="0" cellspacing="0" cellpadding="0" width="100%" class="mt10">' +
            '<tr>' +
            '<td colspan="2">学校排名：'+list.StudySchooRanking+'</td>' +
            '</tr>' +
            '<tr>' +
            '<td colspan="2">学校地点：'+list.StudyLocation+'</td>' +
            '</tr>' +
            '<tr>' +
            '<td>录取率：'+list.StudyAcceptanceRate+'</td>' +
            '<td>总费用：'+list.StudyAnnualCost+'</td>' +
            '</tr>' +
            '<tr>' +
            '<td>托福成绩：'+list.StudyTOEFL+'</td>' +
            '<td>SAT II 成绩：'+list.StudySAT+'</td>' +
            '</tr>' +
            '</table>' +
            '</div>' +
            '</li>';
        $('#StudyLineList').append(item);
    });
}