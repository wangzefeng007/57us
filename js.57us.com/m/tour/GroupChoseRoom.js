/**
 * Created by Foliage on 2016/11/10
 */
//产品id
var TourProductID = GetQueryString("TourProductID");
$(function() {
    var Date = GetQueryString('Date');
    $(".dateChose").text(Date);
    //页面初始化加载
    ajaxLoad(Date);
    //选择日期，提交数据查询
    $("#choseDate").on('click',function () {
        $.ajax({
            type: "get",	//提交类型
            dataType: "json",	//提交数据类型
            url: "/group/getdate/",  //提交地址
            data: {
                'TourProductID':TourProductID,
            },
            success: function(data) {	//函数回调
                if(data.ResultCode == '200'){
                    $("#pageDate").empty();
                    pickerEvent.setPriceArr(data);
                    pickerEvent.setMonthArr(data);
                    pickerEvent.Init("calendar");
                }else{
                    $.toast(data.Message)
                }
            }
        });
    })

    //明细弹出
    $(".freeDetails").on("click", function() {
        $(".freeDetaBox").addClass("on");
        $(".mask").show();
    })
    $(".freeDetaBox .close,.mask").on("click", function() {
        $(".freeDetaBox").removeClass("on");
        $(".mask").hide();
    })

    //添加房间事件
    $('#addLi').on('click',function () {
        addLoad();
    })

    //选择成人数量事件
    $(document).bind("change",'.dult',function () {
        var _thidId = $(this).find('option').not(function() {return !this.selected}).attr('data-id') * 1;
        var _num = $(this).parent().find('.num').text();
        dultLoad(_thidId,_num);
    })

    //选择儿童数量事件
    $(document).bind('change','.child',function () {
        var _thidId = $(this).find('option').not(function() {return !this.selected}).attr('data-id') * 1;
        var _num = $(this).parent().find('.num').text();
        childLoad(_thidId,_num);
    })

    //选择人数量事件
    $(document).bind('change','.adult',function () {
        var _thidId = $(this).find('option').not(function() {return !this.selected}).attr('data-id') * 1;
        var _num = $(this).parent().find('.num').text();
        adultLoad(_thidId,_num);
    })

    //填写旅客信息
    $("#nextBtn").on('click',function () {
        var SkuID =[];
        //skuid数据遍历成数组
        $('.freeDetailList').each(function () {
            SkuID.push($(this).attr('data-id'));
        })
        ajaxData = {
            'Intention':'GroupRoomSubmit', //方法名
            'Date':GroupDate, //日期
            'TourProductID':TourProductID, //产品id
            'SkuID':SkuID, //skuid 数组
            'CostList':$("#costlist").html(), //费用明细，下个页面  直接扔给我使用
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "",
            data: ajaxData,
            success: function(data) {
                if(data.ResultCode == '200'){
                    var Url = data.Url;
                    window.location.href = Url;
                }else{
                    $.toast(data.Message);
                }
            }
        });
    })

})

var GroupDate; //点击日期的出行日期
//初始化加载
function ajaxLoad(date) {
    GroupDate = date;
    ajaxData = {
        'Intention':'GroupRoomInit', //方法名
        'Date': date,  //日期
        'TourProductID': TourProductID, //产品ID
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "",
        data: ajaxData,
        success: function(data) {
            if(data.ResultCode == '200'){
                $(".roomlist").remove(); //清空房间html
                $("#costlist").empty(); //清空费用明细
                if(data.Type == 0){
                    var dult = '';
                    var child = '';
                    //遍历成人数据
                    $.each(data.DultData, function(i, list) {
                        dult += '<option data-id="'+list.skuid+'" data-value="'+list.num+'">'+list.num+'成人</option>';
                    });
                    //遍历儿间数据
                    $.each(data.ChildData, function(i, list) {
                        child += '<option data-id="'+list.skuid+'" data-value="'+list.num+'">'+list.num+'儿童</option>';
                    });
                    //拼接房间注入数据
                    var html = '<li class="roomlist"><span class="fl">房间<span class="num">1</span>：</span>'
                    html += '<div class="selectBox dult"><select>';
                    html += dult;
                    html += '</select></div>';
                    html += '<div class="selectBox child"><select>';
                    html += child;
                    html += '</select></div>';
                    html += "</li>";
                    $("#addLi").before(html);
                    //计算房间数据类型
                    RoomPrice(data.RoomPriceList);
                    //拼接费用明细
                    var PriceData = data.PriceData;
                    var html2 = '<div class="row freeDetailList" data-id="'+PriceData.skuid+'" data-value="'+PriceData.cost+'"><div class="col-50">房间1：'+PriceData.dultnum+'成人，'+PriceData.childnum+'儿童</div><div class="col-50">费用：<span class="red">￥<i>'+PriceData.cost+'</i></span></div></div></div>';
                    $("#costlist").append(html2);
                    //总价注入
                    $("#TotalPrice").text(PriceData.cost);
                }else if(data.Type == '1'){
                    var adult = '';
                    //遍历人数
                    $.each(data.AdultData, function(i, list) {
                        adult += '<option data-id="'+list.skuid+'" data-value="'+list.num+'">'+list.num+'人</option>';
                    });
                    //拼接房间注入数据
                    var html = '<li class="roomlist c50"><span class="fl">房间<span class="num">1</span>：</span>'
                    html += '<div class="selectBox adult"><select>';
                    html += adult;
                    html += '</select></div>';
                    html += "</li>";
                    $("#addLi").before(html);
                    //计算房间数据类型
                    RoomPrice(data.RoomPriceList);
                    //拼接费用明细
                    var PriceData = data.PriceData;
                    var html2 = '<div class="row freeDetailList" data-id="'+PriceData.skuid+'" data-value="'+PriceData.cost+'"><div class="col-50">房间1：'+PriceData.checknum+'人</div><div class="col-50">费用：<span class="red">￥<i>'+PriceData.cost+'</i></span></div></div></div>';
                    $("#costlist").append(html2);
                    //总价注入
                    $("#TotalPrice").text(PriceData.cost);
                }
                OperateRoom(1);
            }else{
                $.toast(data.Message)
            }
        }
    });
}

//添房间加载数据
function addLoad() {
    ajaxData = {
        'Intention':'GroupRoomInit', //方法名
        'Date': GroupDate,  //日期
        'TourProductID': TourProductID, //产品ID
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "",
        data: ajaxData,
        success: function(data) {
            var num =  Number($('.num').last().text()) + Number('1');//计算当前房间数值
            if(data.ResultCode == '200'){
                if(data.Type == 0){
                    var dult = '';
                    var child = '';
                    //遍历成人数据
                    $.each(data.DultData, function(i, list) {
                        dult += '<option data-id="'+list.skuid+'" data-value="'+list.num+'">'+list.num+'成人</option>';
                    });
                    //遍历儿间数据
                    $.each(data.ChildData, function(i, list) {
                        child += '<option data-id="'+list.skuid+'" data-value="'+list.num+'">'+list.num+'儿童</option>';
                    });
                    //拼接房间注入数据
                    var html = '<li class="roomlist"><span class="fl">房间<span class="num">'+num+'</span>：</span>'
                    html += '<div class="selectBox dult"><select>';
                    html += dult;
                    html += '</select></div>';
                    html += '<div class="selectBox child"><select>';
                    html += child;
                    html += '</select></div>';
                    html += '<a href="javascript:void(0)" class="changeMan delroom"><i class="icon iconfont">&#xe69e;</i></a>';
                    html += "</li>";
                    $("#addLi").before(html);
                    //计算房间数据类型
                    RoomPrice(data.RoomPriceList);
                    //拼接费用明细
                    var PriceData = data.PriceData;
                    var html2 = '<div class="row freeDetailList" data-id="'+PriceData.skuid+'" data-value="'+PriceData.cost+'"><div class="col-50">房间'+num+'：'+PriceData.dultnum+'成人，'+PriceData.childnum+'儿童</div><div class="col-50">费用：<span class="red">￥<i>'+PriceData.cost+'</i></span></div></div></div>';
                    $("#costlist").append(html2);
                    //总价注入
                    $("#TotalPrice").text(PriceData.cost);
                }else if(data.Type == '1'){
                    var adult = '';
                    //遍历人数
                    $.each(data.AdultData, function(i, list) {
                        adult += '<option data-id="'+list.skuid+'" data-value="'+list.num+'">'+list.num+'人</option>';
                    });
                    //拼接房间注入数据
                    var html = '<li class="roomlist c50"><span class="fl">房间<span class="num">'+num+'</span>：</span>'
                    html += '<div class="selectBox adult"><select>';
                    html += adult;
                    html += '</select></div>';
                    html += '<a href="javascript:void(0)" class="changeMan delroom"><i class="icon iconfont">&#xe69e;</i></a>';
                    html += "</li>";
                    $("#addLi").before(html);
                    //计算房间数据类型
                    RoomPrice(data.RoomPriceList);
                    //拼接费用明细
                    var PriceData = data.PriceData;
                    var html2 = '<div class="row freeDetailList" data-id="'+PriceData.skuid+'" data-value="'+PriceData.cost+'"><div class="col-50">房间'+num+'：'+PriceData.checknum+'人</div><div class="col-50">费用：<span class="red">￥<i>'+PriceData.cost+'</i></span></div></div></div>';
                    $("#costlist").append(html2);
                    //总价注入
                    $("#TotalPrice").text(PriceData.cost);
                }
                //添加房间，删除房间对应显示隐藏
                OperateRoom(num);
            }else{
                $.toast(data.Message)
            }
        }
    });
}

//成人点击加载方法
function dultLoad(_thidId,_num) {
    ajaxData = {
        'Intention':'GroupRoomDult', //方法名
        'Date': GroupDate,  //日期
        'TourProductID': TourProductID, //产品ID
        "SkuID":_thidId, //skuid
    }
    var _index = _num -1;
    $.ajax({
        type: "post",
        dataType: "json",
        url: "",
        data: ajaxData,
        success: function(data) {
            if(data.ResultCode == '200'){
                $(".roomlist").eq(_index).find('.child').empty();
                var child = '';
                //遍历儿间数据
                $.each(data.ChildData, function(i, list) {
                    child += '<option data-id="'+list.skuid+'" data-value="'+list.num+'">'+list.num+'儿童</option>';
                });
                //拼接儿童注入数据
                var html = '<select>'
                html += child;
                html += '</select>';
                $(".roomlist").eq(_index).find('.child').append(html);
                //拼接费用明细
                var PriceData = data.PriceData;
                var html2 = '<div class="col-50">房间'+_num+'：'+PriceData.dultnum+'成人，'+PriceData.childnum+'儿童</div><div class="col-50">费用：<span class="red">￥<i>'+PriceData.cost+'</i></span></div></div>';
                //清空对应的费用明细
                $(".freeDetailList").eq(_index).empty();
                $(".freeDetailList").eq(_index).append(html2);
                //替换对应的费用明细skuid、价格
                $(".freeDetailList").eq(_index).attr('data-id',PriceData.skuid);
                $(".freeDetailList").eq(_index).attr('data-value',PriceData.cost);
                TotalPrice();
            }else{
                $.toast(data.Message)
            }
        }
    });
}

//儿童点击加载方法
function childLoad(_thidId,_num) {
    ajaxData = {
        'Intention':'GroupRoomChild', //方法名
        'Date': GroupDate,  //日期
        'TourProductID': TourProductID, //产品ID
        "SkuID":_thidId, //skuid
    }
    var _index = _num -1;
    $.ajax({
        type: "post",
        dataType: "json",
        url: "",
        data: ajaxData,
        success: function(data) {
            if(data.ResultCode == '200'){
                //拼接费用明细
                var PriceData = data.PriceData;
                var html2 = '<div class="col-50">房间'+_num+'：'+PriceData.dultnum+'成人，'+PriceData.childnum+'儿童</div><div class="col-50">费用：<span class="red">￥<i>'+PriceData.cost+'</i></span></div></div>';
                //清空对应的费用明细
                $(".freeDetailList").eq(_index).empty();
                $(".freeDetailList").eq(_index).append(html2);
                //替换对应的费用明细skuid、价格
                $(".freeDetailList").eq(_index).attr('data-id',PriceData.skuid);
                $(".freeDetailList").eq(_index).attr('data-value',PriceData.cost);
                TotalPrice();
            }else{
                $.toast(data.Message)
            }
        }
    });
}

//人点击加载方法
function adultLoad(_thidId,_num) {
    ajaxData = {
        'Intention':'GroupRoomAdult', //方法名
        'Date': GroupDate,  //日期
        'TourProductID': TourProductID, //产品ID
        "SkuID":_thidId, //skuid
    }
    var _index = _num -1;
    $.ajax({
        type: "post",
        dataType: "json",
        url: "",
        data: ajaxData,
        success: function(data) {
            if(data.ResultCode == '200'){
                if(data.Type == 1){
                    //拼接费用明细
                    var PriceData = data.PriceData;
                    var html2 = '<div class="col-50">房间'+_num+'：'+PriceData.checknum+'人</div><div class="col-50">费用：<span class="red">￥<i>'+PriceData.cost+'</i></span></div></div>';
                    //清空对应的费用明细
                    $(".freeDetailList").eq(_index).empty();
                    $(".freeDetailList").eq(_index).append(html2);
                    //替换对应的费用明细skuid、价格
                    $(".freeDetailList").eq(_index).attr('data-id',PriceData.skuid);
                    $(".freeDetailList").eq(_index).attr('data-value',PriceData.cost);
                }
                TotalPrice();
            }else{
                $.toast(data.Message)
            }
        }
    });
}

//添加房间，移除房间对应操作
function OperateRoom(num) {
    //添加房间事件
    if(num >= '4'){
        $("#addLi").hide();
    }else {
        $("#addLi").show();
    }
    TotalPrice();
    //删除房间，先全部隐藏后显示后最一个
    $(".delroom").hide();
    $(".delroom").last().css({"display":"inline-block"})
    //删除房间事件
    $(".delroom").on('click',function () {
        var _num = $(this).parent().find('.num').text();
        $(".freeDetailList").eq(_num -1).remove();
        $(this).parent().remove();
        $(".delroom").last().css({"display":"inline-block"})
        $("#addLi").show();
        TotalPrice();
    })
}

//计总价方法
function TotalPrice() {
    var sum = 0;
    $(".freeDetailList").each(function () {
        sum += $(this).attr('data-value') * 1;
    })
    $("#TotalPrice").text(sum);
}

//计算房间数据类型
function RoomPrice(RoomPriceList) {
    $("#RoomPriceList").empty();
    $.each(RoomPriceList,function(i,list){
        $("#RoomPriceList").append('<td><span class="red">&yen;'+list+'</span>/人起</td>');
    });
    if(RoomPriceList[0] == '0'){
        $("#RoomPricePic td").eq(0).find('i').css('color','#d9d7d7');
        $("#RoomPriceList td").eq(0).html('<span>暂无房间</span>');
    }
    if(RoomPriceList[1] == '0'){
        $("#RoomPricePic td").eq(1).find('i').css('color','#d9d7d7');
        $("#RoomPriceList td").eq(1).html('<span>暂无房间</span>');
    }
    if(RoomPriceList[2] == '0'){
        $("#RoomPricePic td").eq(2).find('i').css('color','#d9d7d7');
        $("#RoomPriceList td").eq(2).html('<span>暂无房间</span>');
    }
    if(RoomPriceList[3] == '0'){
        $("#RoomPricePic td").eq(3).find('i').css('color','#d9d7d7');
        $("#RoomPriceList td").eq(3).html('<span>暂无房间</span>');
    }
}