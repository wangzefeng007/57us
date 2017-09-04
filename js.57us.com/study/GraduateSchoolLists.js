/**
 * Created by Foliage on 2016/10/20.
 */
$(function () {
    //专业方向
    $('#ProfessionalEmphasis').empty();
    $.get('/Templates/Study/data/School/Graduate/ProfessionalEmphasis.json', function(data) {
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label name="rbt" type="radiobox" val="' + list.id + '" class="cbt"><i></i>' + list.name + '</label>' +
                '</li>';
            $('#ProfessionalEmphasis').append(item);
        });
        var Filter = {
            'idname': 'ProfessionalEmphasis',
            'nini': 'ProfessionalEmphasisAll',
            'dataidname': 'ProfessionalEmphasisName',
            'ajaxname': 'AjaxProfessionalEmphasis',
        }
        TourRadio(Filter);
        $("#ProfessionalEmphasis label,#ProfessionalEmphasisAll").on('click',function() {
            var Specific = $("#AjaxProfessionalEmphasis span").text();
            if(Specific == 'All'){
                $("#Specific").hide();
                $("#AjaxSpecificDirection span").text('All');
                $("#SpecificDirectionAll").parent().addClass('on');
            }else{
                $("#Specific").show();
                $("#AjaxSpecificDirection span").text('All');
                $("#SpecificDirectionAll").parent().addClass('on');
                SpecificDirection(Specific);
            }
        })
    }, 'json')

    //具体专业
    function SpecificDirection(Specific) {
        $('#SpecificDirection').empty();
        $.get('/Templates/Study/data/School/Graduate/'+Specific+'.json', function(data) {
            var item;
            $.each(data, function(i, list) {
                item = '<li>' +
                    '<label name="cbt" type="checkbox" val="' + list.id + '" class="cbt"><i></i>' + list.name + '</label>' +
                    '</li>';
                $('#SpecificDirection').append(item);
            });
            var Filter = {
                'idname': 'SpecificDirection',
                'nini': 'SpecificDirectionAll',
                'dataidname': 'SpecificDirectionName',
                'ajaxname': 'AjaxSpecificDirection',
            }
            TourCheckbox(Filter);
        }, 'json')
    }

})

//ajax提交的对应的参数
function Ajax(Page) {
    var ProfessionalEmphasis = $('#AjaxProfessionalEmphasis').text();
    var SpecificDirection = [];
    $('#AjaxSpecificDirection span').each(function () {
        SpecificDirection.push($(this).attr("data-id"));
    })
    var Sort = $('#AjaxSort').text();
    var Keyword = $('#AjaxKeyword').text();
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "",
        data: {
            'Intention': 'CourseLists', //方法
            'ProfessionalEmphasis':ProfessionalEmphasis, //专业方向
            'SpecificDirection': SpecificDirection, //具体专业
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
        item = '<li data-id="'+list.StudyID+'">' +
            '<div class="ListRight">' +
            '<a href="'+list.StudyUrl+'" class="CheckMore transition mt25" target="_blank" title="'+list.Study_name+'">查看详情</a>' +
            '<a href="" class="Collect mt20" style="display: none">收藏</a>' +
            '</div>' +
            '<a href="'+list.StudyUrl+'" target="_blank"><img class="transition" width="100%" src="'+list.StudyImg+'" width="200" height="150" alt="'+list.Study_name+'"></a>' +
            '<div class="ListCont">' +
            '<p class="tit"><a href="'+list.StudyUrl+'" target="_blank">'+list.Study_name+'</a></p>' +
            '<p class="tit"><span class="f16">'+list.Study_Englishname+'</span></p>' +
            '<table border="0" cellspacing="0" cellpadding="0" width="100%" class="mt15">' +
            '<tr>' +
            '<td colspan="2">学校地点：'+list.StudyLocation+'</td>' +
            '</tr>' +
            '<tr>' +
            '<td colspan="2">'+list.StudyMajor+'</td>' +
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
        item = '<li data-id="'+list.StudyID+'">' +
            '<div class="ListRight">' +
            '<a href="'+list.StudyUrl+'" class="CheckMore transition mt25" title="'+list.Study_name+'">查看详情</a>' +
            '<a href="" class="Collect mt20" style="display: none">收藏</a>' +
            '</div>' +
            '<img class="transition" width="100%" src="'+list.StudyImg+'" width="200" height="150" alt="'+list.Study_name+'">' +
            '<div class="ListCont">' +
            '<p class="tit">'+list.Study_name+'</p>' +
            '<p class="tit"><span class="f16">'+list.Study_Englishname+'</span></p>' +
            '<table border="0" cellspacing="0" cellpadding="0" width="100%" class="mt15">' +
            '<tr>' +
            '<td colspan="2">学校地点：'+list.StudyLocation+'</td>' +
            '</tr>' +
            '<tr>' +
            '<td colspan="2">'+list.StudyMajor+'</td>' +
            '</tr>' +
            '</table>' +
            '</div>' +
            '</li>';
        $('#StudyLineList').append(item);
    });
}