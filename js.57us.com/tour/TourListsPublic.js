/**
 * Created by Foliage on 2016/8/15.
 */
$(function(){
    //自定义复选框
    $('.cbt').inputbox();
    $(".ChoseMore").each(function(){
        $(this).click(function(){
            $(this).parents(".selectList").toggleClass("on")
        })
    })

    //初始化加载
    var Keyword = GetQueryString("K");
    $("#AjaxKeyword").append(Keyword);
    $("#keyword").val(Keyword);
    $("#keyword").attr('value',Keyword);
    // Ajax();
})

//获取当前时间
function CurentTime() {
    var now = new Date();
    var year = now.getFullYear(); //年
    var month = now.getMonth() + 1; //月
    var day = now.getDate(); //日
    var clock = year + "-";
    if(month < 10)
        clock += "0";
    clock += month + "-";
    if(day < 10)
        clock += "0";
    clock += day + " ";
    return(clock);
}
//增加月份 格式：yyyy-mm
function gettMonth(date, n) {
    var arr = date.split('-');
    var year = arr[0]; //获取当前日期的年份
    var month = arr[1]; //获取当前日期的月份
    var day = arr[2]; //获取当前日期的日
    var days = new Date(year, month, 0);
    days = days.getDate(); //获取当前日期中的月的天数
    var year2 = year;
    var month2 = parseInt(month) + n;
    if(month2 == 13) {
        year2 = parseInt(year2) + 1;
        month2 = 1;
    } else if(month2 == 14) {
        year2 = parseInt(year2) + 1;
        month2 = 2;
    } else if(month2 == 15) {
        year2 = parseInt(year2) + 1;
        month2 = 3;
    } else if(month2 == 16) {
        year2 = parseInt(year2) + 1;
        month2 = 4;
    } else if(month2 == 17) {
        year2 = parseInt(year2) + 1;
        month2 = 5;
    } else if(month2 == 18) {
        year2 = parseInt(year2) + 1;
        month2 = 6;
    } else if(month2 == 19) {
        year2 = parseInt(year2) + 1;
        month2 = 7;
    } else if(month2 == 20) {
        year2 = parseInt(year2) + 1;
        month2 = 8;
    } else if(month2 == 21) {
        year2 = parseInt(year2) + 1;
        month2 = 9;
    } else if(month2 == 22) {
        year2 = parseInt(year2) + 1;
        month2 = 10;
    } else if(month2 == 23) {
        year2 = parseInt(year2) + 1;
        month2 = 11;
    } else if(month2 == 24) {
        year2 = parseInt(year2) + 1;
        month2 = 12;
    }
    var day2 = day;
    var days2 = new Date(year2, month2, 0);
    days2 = days2.getDate();
    if(day2 > days2) {
        day2 = days2;
    }
    if(month2 < 10) {
        month2 = '0' + month2;
    }
    var t2 = year2 + '' + month2;
    return t2;
}
//增加月份 格式：yyyy年mm月
function gettMonthb(date, n) {
    var arr = date.split('-');
    var year = arr[0]; //获取当前日期的年份
    var month = arr[1]; //获取当前日期的月份
    var day = arr[2]; //获取当前日期的日
    var days = new Date(year, month, 0);
    days = days.getDate(); //获取当前日期中的月的天数
    var year2 = year;
    var month2 = parseInt(month) + n;
    if(month2 == 13) {
        year2 = parseInt(year2) + 1;
        month2 = 1;
    } else if(month2 == 14) {
        year2 = parseInt(year2) + 1;
        month2 = 2;
    } else if(month2 == 15) {
        year2 = parseInt(year2) + 1;
        month2 = 3;
    } else if(month2 == 16) {
        year2 = parseInt(year2) + 1;
        month2 = 4;
    } else if(month2 == 17) {
        year2 = parseInt(year2) + 1;
        month2 = 5;
    } else if(month2 == 18) {
        year2 = parseInt(year2) + 1;
        month2 = 6;
    } else if(month2 == 19) {
        year2 = parseInt(year2) + 1;
        month2 = 7;
    } else if(month2 == 20) {
        year2 = parseInt(year2) + 1;
        month2 = 8;
    } else if(month2 == 21) {
        year2 = parseInt(year2) + 1;
        month2 = 9;
    } else if(month2 == 22) {
        year2 = parseInt(year2) + 1;
        month2 = 10;
    } else if(month2 == 23) {
        year2 = parseInt(year2) + 1;
        month2 = 11;
    } else if(month2 == 24) {
        year2 = parseInt(year2) + 1;
        month2 = 12;
    }
    var day2 = day;
    var days2 = new Date(year2, month2, 0);
    days2 = days2.getDate();
    if(day2 > days2) {
        day2 = days2;
    }
    if(month2 < 10) {
        month2 = '0' + month2;
    }
    var t2 = year2 + '年' + month2 + '月';
    return t2;
}

//出发时间
$(document).ready(function(){
    //出发时间页面代码
    html =  '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 0) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 0) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 1) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 1) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 2) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 2) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 3) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 3) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 4) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 4) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 5) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 5) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 6) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 6) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 7) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 7) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 8) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 8) + '</label></li>' +
        '<li><label name="cbt" type="checkbox" val="' + gettMonth(CurentTime(), 9) + '" class="cbt"><i></i>' + gettMonthb(CurentTime(), 9) + '</label></li>';
    $("#StartDate").empty();
    $("#StartDate").append(html);
    $("#StartDate .cbt").inputbox();
    //出发时间选择时，移除不限
    $('#StartDate label').click(function() {
        $('#StartDateAll').parent().removeClass('on');
        //点击时计算长度，为0时监听
        var StartDate = $(this).text();
        var StartDateNum = $(this).attr('val');
        if($(this).is('.checked')) {
            html = '<p data-id="StartDate">出发时间：<span data-id="' + StartDate + '">' + StartDate + '</span><em></em></p>';
            html2 = '<span data-id="' + StartDate + '">' + StartDateNum + ',</span>';
            //注入筛选位置
            if($('#condition span[data-id="' + StartDate + '"]').length === 0) {
                $('#clearAll').before(html);
                $("#AjaxStartDate span[data-id='StartDateName']").remove();
                $('#AjaxStartDate').append(html2);
            }
            Ajax();
        } else {
            $("#condition span[data-id=" + StartDate + "]").parent().remove();
            $("#AjaxStartDate span[data-id=" + StartDate + "]").remove();
            var b = $("#condition p[data-id='StartDate']").length;
            if(b == '0') {
                $('#StartDateAll').parent().addClass('on');
                $('#AjaxStartDate').append('<span data-id="StartDateName">All</span>');
            }
            Ajax();
        }
    })

    //条件框内点击
    $(document).on("click","#condition p[data-id='StartDate']",function(){
        var id = $(this).find('span').text();
        $(this).remove();
        $('#StartDate label').each(function() {
            var a = $(this).text();
            if(id == a) {
                $(this).removeClass('checked');
                $(this).find('input').attr("checked", true);
                $(this).find('i').attr("checked", true);
                var b = $("#condition p[data-id='StartDate']").length;
                if(b == '0') {
                    $('#StartDateAll').parent().addClass('on');
                }
            }
        })
        $('#AjaxStartDate span').each(function() {
            var a = $(this).attr('data-id');
            if(id == a) {
                $(this).remove();
                var b = $("#condition p[data-id='StartDate']").length;
                if(b == '0') {
                    $('#AjaxStartDate').append('<span data-id="StartDateName">All</span>');
                }
            }
        })
        Ajax();
    })

    $('#StartDateAll').click(function() {
        $('#StartDateAll').parent().addClass('on');
        $('#condition p').each(function() {
            var a = $(this).attr('data-id');
            if(a == 'StartDate') {
                $(this).remove();
            }
        })
        $('#AjaxStartDate span').attr('data-id', 'StartDateName').remove();
        $('#AjaxStartDate').append('<span data-id="StartDateName">All</span>');
        $('#StartDate label').each(function() {
            var StartCityClass = $(this).attr('class');
            if(StartCityClass == 'cbt cb checked') {
                $(this).removeClass('checked');
                $(this).find('input').attr("checked", true);
                $(this).find('i').attr("checked", true);
            }
        })
        Ajax();
    })
});

$(function () {
    //排序事件
    $('#Ranking a').click(function () {
        var text = $(this).text();
        if(text == '销量上升' || text == '销量下降'){
            $("#PicerSort").html('价格排序<i></i>');
        }else if(text == '价格从低到高' || text == '价格从高到低'){
            $("#SalesSort").html('销量排序<i></i>');
        }else if(text == '综合排序'){
            $("#PicerSort").html('价格排序<i></i>');
            $("#SalesSort").html('销量排序<i></i>');
        }
        $(this).parent().prev().html(text+'<i></i>');
        var Sort = $(this).attr('id');
        RankingClass(Sort);
        ranking(Sort);
        Ajax();
    })
    //排序点击时增加class
    function RankingClass(Sort) {
        $('#Ranking li').removeClass('on');
        $("#"+Sort+"").parents('li').addClass('on');
    }
    //排序ajax提交数据注入
    function ranking(Sort) {
        $('#AjaxSort').empty();
        $('#AjaxSort').append(Sort);
    }
})

//点击条件搜索ajax加载方法
function ClickLoad(Filter) {
    $("#" + Filter.nini + "").on('click',function () {
        Ajax();
    })
}

//单选操作方法
function TourRadio(Filter,type) {
    //表单美化注入
    $("#" + Filter.idname + " .cbt").inputbox();
    //出发城市选择时，移除不限
    $("#" + Filter.idname + " label").click(function() {
        $("#" + Filter.nini + "").parent().removeClass('on');
        html = '<p data-id="' + Filter.dataidname + '">' + Filter.textname + '<span>' + $(this).text() + '</span><em></em></p>';
        //注入筛选位置
        $('#condition p').each(function() {
            if($(this).attr('data-id') == Filter.dataidname) {
                $(this).remove();
            }
        })
        if(type == 'Ticket'){
            $("#TicketPriceCustom input").attr("value","");
            $("#TicketPriceCustom input").val("");
            $('#condition p').each(function() {
                if($(this).attr('data-id') == 'TicketPriceCustomName') {
                    $(this).remove();
                }
            })
        }
        $('#clearAll').before(html);
        //注入ajax提交位置
        html2 = '<span data-id="' + Filter.dataidname + '" id="' + $(this).text() + '">' + $(this).attr('val') + '</span>';
        $("#" + Filter.ajaxname + " span").attr('data-id', Filter.dataidname).remove();
        $("#" + Filter.ajaxname + "").append(html2);
        Ajax();
    })
    //条件框内点击
    $(document).on("click","#condition p[data-id='" + Filter.dataidname + "']",function(){
        var id = $(this).find('span').text();
        $(this).remove();
        $("#" + Filter.idname + " label").each(function() {
            var a = $(this).text();
            if(id == a) {
                $(this).removeClass('rb_active');
                $(this).find('input').attr("checked", false);
                $(this).find('i').attr("checked", false);
                $("#" + Filter.nini + "").parent().addClass('on');
            }
        })
        $("#" + Filter.ajaxname + " span").each(function() {
            var a = $(this).attr('id');
            if(id == a) {
                $(this).remove();
                $("#" + Filter.ajaxname + "").append('<span data-id="' + Filter.idname + '">All</span>');
            }
        })
        Ajax();
    })

    //点击不限时清除已选内容
    $("#" + Filter.nini + "").click(function() {
        $("#" + Filter.nini + "").parent().addClass('on');
        //点击不限时清除已选内容
        $('#condition p').each(function() {
            var a = $(this).attr('data-id');
            if(a == Filter.dataidname) {
                $(this).remove();
            }
        })
        if(type == 'Ticket'){
            $("#TicketPriceCustom input").attr("value","");
            $("#TicketPriceCustom input").val("");
            $('#condition p').each(function() {
                if($(this).attr('data-id') == 'TicketPriceCustomName') {
                    $(this).remove();
                }
            })
        }
        $("#" + Filter.ajaxname + " span").attr('data-id', Filter.dataidname).remove();
        $("#" + Filter.ajaxname + "").append('<span data-id="' + Filter.idname + '">All</span>');
        $("#" + Filter.idname + " label").each(function() {
            var ThisClass = $(this).attr('class');
            if(ThisClass == 'cbt rb rb_active') {
                $(this).removeClass('rb_active');
                $(this).find('input').attr("checked", false);
                $(this).find('i').attr("checked", false);
            }
        })
    })
}

//多选操作方法
function TourCheckbox(Filter) {
    //表单美化注入
    $("#" + Filter.idname + " .cbt").inputbox();
    //lable点击
    $("#" + Filter.idname + " label").click(function() {
        $("#" + Filter.nini + "").parent().removeClass('on');
        var TextName = $(this).text();
        var TexteNum = $(this).attr('val');
        if($(this).is('.checked')) {
            html = '<p data-id="' + Filter.dataidname + '">' + Filter.textname + '<span data-id="' + TextName + '">' + TextName + '</span><em></em></p>';
            html2 = '<span data-id="' + TextName + '">' + TexteNum + ',</span>';
            //注入筛选位置
            if($('#condition span[data-id="' + TextName + '"]').length === 0) {
                $('#clearAll').before(html);
                $("#AjaxTheme span[data-id='" + Filter.dataidname + "']").remove();
                $("#" + Filter.ajaxname + "").append(html2);
            }
            Ajax();
        } else {
            $("#condition span[data-id=" + TextName + "]").parent().remove();
            $("#AjaxTheme span[data-id=" + TextName + "]").remove();
            var b = $("#condition p[data-id='" + Filter.dataidname + "']").length;
            if(b == '0') {
                $("#" + Filter.nini + "").parent().addClass('on');
                $("#" + Filter.ajaxname + "").append('<span data-id="' + Filter.dataidname + '">All</span>');
            }
            Ajax();
        }
    })

    //条件框内点击
    $(document).on("click","#condition p[data-id='" + Filter.dataidname + "']",function(){
        var id = $(this).find('span').text();
        $(this).remove();
        $("#" + Filter.idname + " label").each(function() {
            var a = $(this).text();
            if(id == a) {
                $(this).removeClass('checked');
                $(this).find('input').attr("checked", true);
                $(this).find('i').attr("checked", true);
                var b = $("#condition p[data-id='" + Filter.dataidname + "']").length;
                if(b == '0') {
                    $("#" + Filter.nini + "").parent().addClass('on');
                }
            }
        })
        $("#" + Filter.ajaxname + " span").each(function() {
            var a = $(this).attr('data-id');
            if(id == a) {
                $(this).remove();
                var b = $("#condition p[data-id='" + Filter.dataidname + "']").length;
                if(b == '0') {
                    $("#" + Filter.ajaxname + "").append('<span data-id="' + Filter.dataidname + '">All</span>');
                }
            }
        })
        Ajax();
    });

    //不限点击
    $("#" + Filter.nini + "").click(function() {
        $("#" + Filter.nini + "").parent().addClass('on');
        $('#condition p').each(function() {
            var a = $(this).attr('data-id');
            if(a == Filter.dataidname) {
                $(this).remove();
            }
        })
        $("#" + Filter.ajaxname + " span").attr('data-id',Filter.dataidname).remove();
        $("#" + Filter.ajaxname + "").append('<span data-id="' + Filter.dataidname + '">All</span>');
        //移除选择条件
        $("#condition p[data-id='" + Filter.dataidname + "']").remove();
        $("#" + Filter.ajaxname + " span").remove();
        $("#" + Filter.ajaxname + "").append('<span data-id="' + Filter.dataidname +'">All</span>');
        $("#" + Filter.idname + " label").each(function() {
            var ThisClass = $(this).attr('class');
            if(ThisClass == 'cbt cb checked') {
                $(this).removeClass('checked');
                $(this).find('input').attr("checked", true);
                $(this).find('i').attr("checked", true);
            }
        })
    })
}

//清空筛选条件
$("#clearAll").click(function() {
    window.location.reload();
})

//筛选条件显示隐藏
$(document).on('click','#selectBox', function() {
    var a = $("#condition p").length;
    if(a > '0') {
        $("#conditionpanel table").show();
    } else {
        $("#conditionpanel table").hide();
    }
})

//分页机制
function diffPage(pageNumData) {
    var pageDemo = $('#pageDemo').html(); //获取模版
    var pageDemoParent = $('#Page');
    var showWhichPage = 1;
    $('#Page').empty();
    if(pageNumData.Page) {
        laytpl(pageDemo).render(pageNumData, function(html) {
            var $html = $(html);
            $html.appendTo('#Page').on('click', function() {
            });
            //页面点击事件
            $("#Page a").click(function () {
                var Page = $(this).attr('data-id');
                if (Page == '0'){
                    return
                }else if(Page == 'undefined'){
                    return
                }
                W_ScrollTo($('.selectBox').eq(0));
                Ajax(Page);
            })
            //页码输入事件
            $("#pagebtn").click(function () {
                var Page = $("#pagenum").val();
                var pagemax = pageNumData.PageCount+1;
                if(Page == ''){
                    layer.msg('输入的页码不能为空');
                    return
                }else if(!/^\+?[1-9]\d*$/i.test(Page)){
                    layer.msg('请输入大于0的整数页码');
                    return
                }else if(Page >= pagemax){
                    layer.msg('输入的页码不能大于'+pageNumData.PageCount);
                    return
                }
                W_ScrollTo($('.selectBox').eq(0));
                Ajax(Page);
            })

            var allPage = $html.parent().find('a');
            for(var i = 0; i < allPage.length; i++) {
                if(Number($(allPage[i]).html()) == pageNumData.Page) {
                    $(allPage[i]).addClass('on');
                }
            }
            if(pageNumData.Page === 1){
                $('.prve').addClass('prvestop')
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
                pageDemoParent.find('.lastEllipsis').remove();
                pageDemoParent.find('.PageCount').remove();
            }

            if(pageNumData.Page == pageNumData.PageCount) {
                $(allPage[allPage.length - 1]).addClass('no');
                pageDemoParent.find('.lastEllipsis').remove();
            }
            if(pageNumData.Page == pageNumData.PageCount) {
                $(allPage[allPage.length - 1]).addClass('no');
                $(".next").addClass('nextstop');
                pageDemoParent.find('.PageCount').remove();
            }
            if(pageNumData.Page == pageNumData.PageCount - 1) {
                pageDemoParent.find('.PageCount').remove();
                pageDemoParent.find('.lastEllipsis').remove();
            } else if(pageNumData.Page === pageNumData.PageCount - 2) {
                pageDemoParent.find('.lastEllipsis').remove();
                pageDemoParent.find('.PageCount').remove()
            }
            if(pageNumData.Page === pageNumData.PageCount - 3) {
                pageDemoParent.find('.lastEllipsis').remove();
            }
        });
    }
}
