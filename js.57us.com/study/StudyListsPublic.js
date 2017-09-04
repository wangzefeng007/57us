/**
 * Created by Foliage on 2016/9/23.
 */

$(function(){

    //自定义复选框
    $('.cbt').inputbox();
    $(".ChoseMore").each(function(){
        $(this).click(function(){
            $(this).parents(".selectList").toggleClass("on")
        })
    })
    var loca = decodeURIComponent(window.location).split('/');
    var domain = loca[3];
    var Type = GetQueryString('t');

    //初始化加载
    var Keyword = GetQueryString("K");
    $("#AjaxKeyword").append(Keyword);
    $("#keyword").val(Keyword);
    $("#keyword").attr('value',Keyword);

    //顾问服务列表
    if(domain == 'consultant_service'){
        if(Type == '1'){
            $("#AjaxServiceType").html('<span data-id="1">1</span>');
        }else if(Type == '2'){
            $("#AjaxServiceType").html('<span data-id="2">2</span>');
        }else if(Type == '3'){
            $("#AjaxServiceType").html('<span data-id="3">3</span>');
        }else if(Type == '4'){
            $("#AjaxServiceType").html('<span data-id="4">4</span>');
        }else if(Type == '5'){
            $("#AjaxServiceType").html('<span data-id="5">5</span>');
        }else if(Type == '6'){
            $("#AjaxServiceType").html('<span data-id="6">6</span>');
        }else if(Type == '7'){
            $("#AjaxServiceType").html('<span data-id="7">7</span>');
        }
        if(Type != null){
            Ajax();
        }
    }else if(domain == 'teacher_course'){
        if(Type == '1'){
            $("#AjaxTrainSubject").html('<span data-id="1">1</span>');
        }else if(Type == '2'){
            $("#AjaxTrainSubject").html('<span data-id="2">2</span>');
        }else if(Type == '3'){
            $("#AjaxTrainSubject").html('<span data-id="3">3</span>');
        }else if(Type == '4'){
            $("#AjaxTrainSubject").html('<span data-id="4">4</span>');
        }else if(Type == '5'){
            $("#AjaxTrainSubject").html('<span data-id="5">5</span>');
        }else if(Type == '6'){
            $("#AjaxTrainSubject").html('<span data-id="6">6</span>');
        }else if(Type == '7'){
            $("#AjaxTrainSubject").html('<span data-id="7">7</span>');
        }
        if(Type != null){
            Ajax();
        }
    }
})

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
            $("#ExperienceSort").html('工作经验排序<i></i>');
            $("#APSort").html('AP排序<i></i>');
            $("#ExpensesSort").html('费用排序<i></i>');
            $("#SuccessRateSort").html('成功率排序<i></i>');
            $("#RankingSort").html('排名<i></i>');
        }else if(text == '经验从低到高' || text == '经验从高到低'){
            $("#ExperienceSort").html('工作经验排序<i></i>');
        }else if(text == 'AP从低到高' || text == 'AP从高到低'){
            $("#APSort").html('AP排序<i></i>');
        }else if(text == '费用上升' || text == '费用下降'){
            $("#ExpensesSort").html('费用排序<i></i>');
        }else if(text == '成功率从低到高' || text == '成功率从高到低'){
            $("#SuccessRateSort").html('成功率排序<i></i>');
        }else if(text == '排名从低到高' || text == '排名从高到低'){
            $("#RankingSort").html('排名<i></i>');
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


//单选操作方法
function TourRadio(Filter) {
    //表单美化注入
    $("#" + Filter.idname + " .cbt").inputbox();
    //出发城市选择时，移除不限
    $("#" + Filter.idname + " label").click(function() {
        $("#" + Filter.nini + "").parent().removeClass('on');
        //注入ajax提交位置
        html2 = '<span data-id="' + Filter.dataidname + '" id="' + $(this).text() + '">' + $(this).attr('val') + '</span>';
        $("#" + Filter.ajaxname + " span").attr('data-id', Filter.dataidname).remove();
        $("#" + Filter.ajaxname + "").append(html2);
        Ajax();
    })

    //点击不限时清除已选内容
    $("#" + Filter.nini + "").click(function() {
        $("#" + Filter.nini + "").parent().addClass('on');
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
        //执行ajax事件
        Ajax();
    })
}

//多选操作方法
function TourCheckbox(Filter) {
    //表单美化注入
    $("#" + Filter.idname + " .cbt").inputbox();
    //lable点击
    $("#" + Filter.idname + " label").on('click',function() {
        //清除不限按钮class
        $("#" + Filter.nini + "").parent().removeClass('on');
        //取选中按钮值
        var TextName = $(this).attr('val');
        //判断当前按钮是否已经选中
        if($(this).is('.checked')) { //未选中操作
            //拼接ajax参数
            html = '<span data-id="' + TextName + '">' + TextName + '</span>';
            //注入ajax参数
            if($('#'+Filter.ajaxname+' span[data-id="' + TextName + '"]').length === 0) {
                $("#" + Filter.ajaxname + "").append(html);
            }
            //移除不限ajax参数
            $('#'+Filter.ajaxname+' span[data-id="All"]').remove();
            //执行ajax事件
            Ajax();
        } else { //选中操作
            //移除ajax参数
            $('#'+Filter.ajaxname+' span[data-id="' + TextName + '"]').remove();
            //判断当前有几个按钮选中，如果条件无选中 执行当前条件的不限参数
            var _num = $('#'+Filter.ajaxname+' span').length;
            if(_num == '0') {
                $("#" + Filter.nini + "").parent().addClass('on');
                $("#" + Filter.ajaxname+"").html('<span data-id="All">All</span>');
            }
            //执行ajax事件
            Ajax();
        }
    })

    //不限点击
    $("#" + Filter.nini + "").on('click',function() {
        //不限添加class
        $("#" + Filter.nini + "").parent().addClass('on');
        //重置ajax提交码数为不限
        $("#" + Filter.ajaxname + "").html('<span data-id="All">All</span>');
        //清空已选中的所有状态
        $("#" + Filter.idname + " label").each(function() {
            $(this).attr('class','cbt cb');
            $(this).find('input').attr("checked", true);
            $(this).find('i').attr("checked", true);
        })
        //执行ajax事件
        Ajax();
    })
}