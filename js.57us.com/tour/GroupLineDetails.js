/**
 * Created by Foliage on 2016/8/22.
 */
$(function(){
    //底部同城推荐
    jQuery(".ScrollPic").slide({mainCell:".ScrollMain ul",autoPage:true,effect:"left",vis:4,pnLoop:'false'});
    //时间滚动轴
    jQuery(".DayScroll").slide({mainCell:".DayScrollMain ul",autoPage:true,effect:"left",vis:5,pnLoop:'false'});
    //获取遮罩的高度
    $(".DayBoxNr .line").each(function(){
        var allHeight = $(this).siblings("ul").height()
        var zzheight = $(this).siblings("ul").find("li").last().find(".suNr").height()
        $(this).height(allHeight-zzheight-35)
    });

    //计算有几个房间类型 注释于2016.08.30 20:28
    // roomprice();

    //底部去除虚线
    addClass();

    //滚动定位导航
    scroll_fix();
    //自定义下拉
    $('.RoomAdd .diyselect').inputbox({
        height:35,
        width:100
    });
    //自定义下拉
    $('.dateIns .diyselect').inputbox({
        height:33,
        width:400
    });
    //特色体验下拉菜单
    $('.procont1 .diyselect').inputbox({
        height:35,
        width:412
    });

    //获取当前产品是否下架  0为未下架  1为下架
    if($(".noProIco").is(':hidden')){
        var noProIco = '0';
    }else {
        $("#oncebtn").text('已售馨');
        $("#oncebtn").addClass('course');
        return
    }

    //填写订单页面上一步至此页面
    Backoperate();

    //开始预定日期插件调用
    $("#startbtn").click(function (e) {
        $("#calendar").addClass('erro');
        calendar(e);
    })

    //日期插件调用
    $('#calendar').click(function(e) {
        calendar(e);
    });

    //隐藏区域日期插件调用
    $('#calendarb').click(function(e) {
        var sjData = $("#DatePriceJson").text();
        pickerEvent.setPriceArr(eval("("+sjData+")"));
        pickerEvent.Init("calendarb");
        e.stopPropagation();
    });

    //滚动导航预定
    $("#oncebtn").click(function (e) {
        var date = $("#calendar").val();
        if(date == ''){
            calendar(e);
            $("#calendar").addClass('erro');
            $('body').animate({scrollTop: $(".TourMenu").offset().top}, 400);
        }else{
            $("#oncebtn").text('预定中...');
            $("#oncebtn").addClass('course');
            $("#NowOrderBtn").trigger('click');
        }
    })
    
    //添加房间
    $("#addroom").click(function () {
        var n = $("#roomnum li").length;
        AddRoomLoad(n);
    })

    //立即预定
    $("#NowOrderBtn").click(function () {
        var Skuid=[];
        $("#roomprice li").each(function () {
            Skuid.push({'sku':$(this).find('.skuid').text(),'sort':$(this).find('.priceroom').text()});
        })
        ajaxData ={
            'ProductId':ProductId,
            'Date':GroupDate,
            'Skuid':Skuid,
        }
        $.ajax({
            type: "get",	//提交类型
            dataType: "json",	//提交数据类型
            url: "/group/grouporder/",  //提交地址
            data: ajaxData,
            beforeSend: function () { //加载过程效果
                $("#NowOrderBtn").text('预定中...');
                $("#NowOrderBtn").addClass('course');
                $("#NowOrderBtn").attr('id','');
            },
            success: function (data) {	//函数回调
                if(data.ResultCode == '200'){
                    var Url = data.Url;
                    window.location.href = Url;
                }else {
                    layer.msg(data.Message);
                }
            }
        })
    })
    //样式重定义
    $('.FreeContNr p').each(function () {
        $(this).css('font-family','微软雅黑');
        $(this).css('color','#999999');
    })
    $('.FreeContNr span').each(function () {
        $(this).css('font-family','微软雅黑');
        $(this).css('color','#999999');
    })
    
    $('.DayBoxNr li p span').each(function () {
        $(this).css('font-family','微软雅黑');
        $(this).css('color','#999999');
    })

    //评价点赞
    $(".TourZan").on('click',function () {
        var _this = $(this);
        var ajaxData = {
            'Intention': 'AddPraise', //方法
            'EvaluateID':$(this).attr('data-id'), //提交的id
        }
        $.post('/ajax.html',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                layer.msg(data.Message);
                _this.find('.zanNum').text(data.Num);
            }else {
                layer.msg(data.Message);
            }
        },'json')
    })

    //点评nav切换
    $("#CommentsNav a").on('click',function () {
        $("#CommentsNav a").removeClass('on');
        $(this).addClass('on');
        Ajax('1');
    })

    //点评事件
    if($(".Comments").length > 0){
        Ajax('1');
    }

})

//点评方法
function Ajax(Page) {
    var ajaxData = {
        'Intention': 'TourComments', //方法
        'TourProductID':$("#ProductId").val(), //产品编号
        'Type':$("#CommentsNav .on").attr('data-type'), //类型 0代表全部 1代表有图
        'Page':Page, //页码
    }
    $.post('/ajax.html',ajaxData,function (data) {
        if(data.ResultCode == '200'){
            $("#pjlist").empty();
            $("#pjlist").append(data.Data);
            if(data.PageCount > 1){
                diffPage(data);
                $("#Page").show();
            }else {
                $("#Page").hide();
            }
        }else{
            layer.msg(data.Message);
        }
    },'json')
}

$(document).ready(function(){
    //头部幻灯片
    $('.PicScroll').banqh({
        box:".PicScroll",//总框架
        pic:"#imgRolling",//大图框架
        pnum:".tra_small",//小图框架
        prev_btn:"#left_btn",//小图左箭头
        next_btn:"#right_btn",//小图右箭头
        pop_prev:"#prev2",//弹出框左箭头
        pop_next:"#next2",//弹出框右箭头
        prev:"#prev1",//大图左箭头
        next:"#next1",//大图右箭头
        pop_div:"#demo2",//弹出框框架
        pop_pic:"#ban_pic2",//弹出框图片框架
        pop_xx:".pop_up_xx",//关闭弹出框按钮
        mhc:".mhc",//朦灰层
        autoplay:true,//是否自动播放
        interTime:4000,//图片自动切换间隔
        delayTime:400,//切换一张图片时间
        pop_delayTime:400,//弹出框切换一张图片时间
        order:0,//当前显示的图片（从0开始）
        picdire:true,//大图滚动方向（true为水平方向滚动）
        mindire:true,//小图滚动方向（true为水平方向滚动）
        min_picnum:5,//小图显示数量
        pop_up:false//大图是否有弹出框
    })
});

//计算房间数据类型   注释于2016.08.30 20:28
// function roomprice() {
//     if($('.styleList span').length == '1'){
//         $(".RoomStyle").css("background-image","url(http://images.57us.com/img/tour/roombox_1.jpg)");
//     }else if($('.styleList span').length == '2'){
//         $(".RoomStyle").css("background-image","url(http://images.57us.com/img/tour/roombox_2.jpg)");
//     }else if($('.styleList span').length == '3'){
//         $(".RoomStyle").css("background-image","url(http://images.57us.com/img/tour/roombox_3.jpg)");
//     }else if($('.styleList span').length == '4'){
//         $(".RoomStyle").css("background-image","url(http://images.57us.com/img/tour/roombox_4.jpg)");
//     }
// }

//计算房间数据类型
function RoomPrice(RoomPriceList) {
    $("#styleList").empty();
    $("#styleList").append(RoomPriceList);
    var roomprice1 = $("#roomprice1").text()
    var roomprice2 = $("#roomprice2").text()
    var roomprice3 = $("#roomprice3").text()
    var roomprice4 = $("#roomprice4").text()
    if(roomprice1 == '暂无房间'){
        $("[data-id='roompimg1']").empty();
        $("[data-id='roompimg1']").append('<img src="http://images.57us.com/img/tour/room/roombox_1_2.jpg">');
    }
    if(roomprice2 == '暂无房间'){
        $("[data-id='roompimg2']").empty();
        $("[data-id='roompimg2']").append('<img src="http://images.57us.com/img/tour/room/roombox_2_2.jpg">');
    }
    if(roomprice3 == '暂无房间'){
        $("[data-id='roompimg3']").empty();
        $("[data-id='roompimg3']").append('<img src="http://images.57us.com/img/tour/room/roombox_3_2.jpg">');
    }
    if(roomprice4 == '暂无房间'){
        $("[data-id='roompimg4']").empty();
        $("[data-id='roompimg4']").append('<img src="http://images.57us.com/img/tour/room/roombox_4_2.jpg">');
    }
}

//直接执行房间数量加载
function Backoperate() {
    var Date = GetQueryString('Date');
    var Back = GetQueryString('Back');
    $("#Back").val(Back);
    if(Back == '1'){
        $("#calendar").val(Date);
        $("#calendar").attr('value',Date);
        $("#calendarb").val(Date);
        $("#calendar").attr('value',Date);
        $(".RoomBox").removeClass('hidden');
        $("#RoomBox").removeClass('hidden');
        $("#RoomBox").show();
        $('body').animate({scrollTop: $(".RoomBox").offset().top - 100}, 250);
        DateLoad(Date);
    }
}

//日期插件调用方法
function calendar(e) {
    var sjData = $("#DatePriceJson").text();
    pickerEvent.setPriceArr(eval("("+sjData+")"));
    pickerEvent.Init("calendar");
    e.stopPropagation();
}

var ProductId = $("#ProductId").val();
var GroupDate;
//日期加载Data
function DateLoad(date) {
    GroupDate = date;
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/group/searchbydate/",  //提交地址
        data: {	//提交数据
            'date': date,
            'ID':ProductId,
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == '200'){
                var roomtext = '房间1';
                if(data.Type == '0'){
                    html = '<li data-id="1">'+
                            '<span style="padding: 5px" class="roomnumx">房间1</span>'+
                            '<div name="room" type="selectbox" class="diyselect AdultClick">'+
                            '<div class="opts roomopts">'+data.AdultData+
                            '</div>'+
                            '</div>'+
                            '<div name="room" type="selectbox" class="diyselect ChildClick">'+
                            '<div class="opts roomopts">'+data.ChildData+
                            '</div>'+
                            '</div>'+
                            '</li>';
                    html2 = '<li><span class="skuid hidden">'+data.PriceData.skuid+'</span><span class="fr">费用：￥<span class="cost">'+data.PriceData.cost+'</span></span><span class="fl"><span class="roomnum">房间<span class="priceroom">1</span></span>：<span class="checknum">'+data.PriceData.checknum+'</span></span></li>';
                    $("#roomnum li").remove();
                    $("#roomprice li").remove();
                    $(".clear").hide();
                    $("#addroom").show();
                    $("#operate").before(html);
                    $("#roomprice").append(html2);
                    diyselect();
                    TotalPrice();
                    AdultLoad(roomtext);
                    ChildLoad(roomtext);
                    RoomPrice(data.RoomPriceList);
                }else if(data.Type = '1'){
                    html = '<li data-id="1">'+
                        '<span style="padding: 5px" class="roomnumx">房间1</span>'+
                        '<div name="room" type="selectbox" class="diyselect AdultClick">'+
                        '<div class="opts roomopts">'+data.AdultData+
                        '</div>'+
                        '</div>'+
                        '</li>';
                    html2 = '<li><span class="skuid hidden">'+data.PriceData.skuid+'</span><span class="fr">费用：￥<span class="cost">'+data.PriceData.cost+'</span></span><span class="fl"><span class="roomnum">房间<span class="priceroom">1</span></span>：<span class="checknum">'+data.PriceData.checknum+'</span></span></li>';
                    $("#roomnum li").remove();
                    $("#roomprice li").remove();
                    $(".clear").hide();
                    $("#addroom").show();
                    $("#operate").before(html);
                    $("#roomprice").append(html2);
                    diyselect();
                    TotalPrice();
                    AdultLoad(roomtext);
                    ChildLoad(roomtext);
                    RoomPrice(data.RoomPriceList);
                }
            }else{
                layer.msg(data.Message)
            }
        }
    });
}
//成人加载Dada
function AdultLoad(roomtext,num) {
    //成人点击执行
    $(".AdultClick a").click(function () {
        if(roomtext == '房间1'){
            var skuid = $(this).parent().parent().parent().find('input').val();
            var Adult = $(this).parent().parent().parent();
            var roomnumx = $(this).parent().parent().parent().find('.roomnumx').text();
            var Chinld = $(this).parent().parent().parent();
            $.ajax({
                type: "post",	//提交类型
                dataType: "json",	//提交数据类型
                url: "/group/searchbyadult/",  //提交地址
                data: {	//提交数据
                    'date': GroupDate,
                    'skuid':skuid,
                    'ID':ProductId,
                },
                success: function(data) {	//函数回调
                    if(data.Type == '0'){
                        html = '<div name="room" type="selectbox" class="diyselect ChildClick">'+
                            '<div class="opts roomopts">'+data.ChildData+
                            '</div>'+
                            '</div>';
                        Chinld.each(function () {
                            $(this).find('.ChildClick').remove();
                        })
                        Adult.append(html);
                        Adult.find('.ChildClick').inputbox({
                            height:35,
                            width:100
                        });
                        $("#roomprice li").each(function () {
                            var roomnum = $(this).find('.roomnum').text();
                            var thisDom = $(this);
                            if(roomnum == roomnumx){
                                thisDom.find('.cost').text(data.PriceData.cost);
                                thisDom.find('.skuid').text(data.PriceData.skuid);
                                thisDom.find('.checknum').text(data.PriceData.checknum);
                            }
                        })
                        ChildLoad(roomtext);
                        TotalPrice();
                    }else if(data.Type = '1'){
                        $("#roomprice li").each(function () {
                            var roomnum = $(this).find('.roomnum').text();
                            var thisDom = $(this);
                            if(roomnum == roomnumx){
                                thisDom.find('.cost').text(data.PriceData.cost);
                                thisDom.find('.skuid').text(data.PriceData.skuid);
                                thisDom.find('.checknum').text(data.PriceData.checknum);
                            }
                        })
                        ChildLoad(roomtext);
                        TotalPrice();
                    }
                }
            });
        }else {
            var skuid = $(this).parent().parent().parent().find('input').val();
            var Adult = $(this).parent().parent().parent();
            var roomnumx = $(this).parent().parent().parent().find('.roomnumx').text();
            var Chinld = $(this).parent().parent().parent();
            $.ajax({
                type: "post",	//提交类型
                dataType: "json",	//提交数据类型
                url: "/group/searchbyadult/",  //提交地址
                data: {	//提交数据
                    'date': GroupDate,
                    'skuid':skuid,
                    'ID':ProductId,
                },
                success: function(data) {	//函数回调
                    if(data.Type == '0'){
                        html = '<div name="room" type="selectbox" class="ChildClick diyselect diyselectx'+num+'">'+
                            '<div class="opts roomopts">'+data.ChildData+
                            '</div>'+
                            '</div>';
                        Chinld.each(function () {
                            $(this).find('.ChildClick').remove();
                        })
                        Adult.append(html);
                        Adult.find('.ChildClick').inputbox({
                            height:35,
                            width:100
                        });
                        $("#roomprice li").each(function () {
                            var roomnum = $(this).find('.roomnum').text();
                            var thisDom = $(this);
                            if(roomnum == roomnumx){
                                thisDom.find('.cost').text(data.PriceData.cost);
                                thisDom.find('.skuid').text(data.PriceData.skuid);
                                thisDom.find('.checknum').text(data.PriceData.checknum);
                            }
                        })
                        ChildLoad(roomtext,num)
                        TotalPrice();
                        RemoveRoom(num);
                        if(num >= '4'){
                            $("#addroom").hide();
                        }
                    }else if(data.Type = '1'){
                        $("#roomprice li").each(function () {
                            var roomnum = $(this).find('.roomnum').text();
                            var thisDom = $(this);
                            if(roomnum == roomnumx){
                                thisDom.find('.cost').text(data.PriceData.cost);
                                thisDom.find('.skuid').text(data.PriceData.skuid);
                                thisDom.find('.checknum').text(data.PriceData.checknum);
                            }
                        })
                        ChildLoad(roomtext);
                        TotalPrice();
                    }
                },
            });
        }
    })
}

//儿童加载Data
function ChildLoad(roomtext,num) {
        $(".ChildClick a").click(function () {
            if(roomtext == '房间1'){
                var skuid = $(this).attr('value');
                var roomnumx = $(this).parent().parent().parent().find('.roomnumx').text();
                $.ajax({
                    type: "post",	//提交类型
                    dataType: "json",	//提交数据类型
                    url: "/group/searchbychild/",  //提交地址
                    data: {	//提交数据
                        'date': GroupDate,
                        'skuid':skuid,
                        'ID':ProductId,
                    },
                    success: function(data) {	//函数回调
                        if(data.ResultCode == '200'){
                            $("#roomprice li").each(function () {
                                var roomnum = $(this).find('.roomnum').text();
                                var thisDom = $(this);
                                if(roomnum == roomnumx){
                                    thisDom.find('.cost').text(data.PriceData.cost);
                                    thisDom.find('.skuid').text(data.PriceData.skuid);
                                    thisDom.find('.checknum').text(data.PriceData.checknum);
                                }
                            })
                            TotalPrice();
                        }else{
                            layer.msg(data.Message)
                        }
                    }
                });
            }else {
                var skuid = $(this).attr('value');
                var roomnumx = $(this).parent().parent().parent().find('.roomnumx').text();
                $.ajax({
                    type: "post",	//提交类型
                    dataType: "json",	//提交数据类型
                    url: "/group/searchbychild/",  //提交地址
                    data: {	//提交数据
                        'date': GroupDate,
                        'skuid':skuid,
                        'ID':ProductId,
                    },
                    success: function(data) {	//函数回调
                        if(data.ResultCode == '200'){
                            $("#roomprice li").each(function () {
                                var roomnum = $(this).find('.roomnum').text();
                                var thisDom = $(this);
                                if(roomnum == roomnumx){
                                    thisDom.find('.cost').text(data.PriceData.cost);
                                    thisDom.find('.skuid').text(data.PriceData.skuid);
                                    thisDom.find('.checknum').text(data.PriceData.checknum);
                                }
                            })
                            TotalPrice();
                        }else{
                            layer.msg(data.Message)
                        }
                    }
                });
            }
        })

}
//添加房间加载Data
function AddRoomLoad(n) {
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/group/searchbydate/",  //提交地址
        data: {	//提交数据
            'date': GroupDate,
            'ID':ProductId,
        },
        beforeSend: function () { //加载过程效果
            $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == '200'){
                var roomtext = '房间x';
                var num = n+1;
                if(data.Type == '0'){
                    html = '<li data-id="'+ num +'">'+
                        '<span style="padding: 5px" class="roomnumx">房间'+ num +'</span>'+
                        '<div name="room" type="selectbox" class="AdultClick diyselect diyselect'+num+'">'+
                        '<div class="opts roomopts">'+data.AdultData+
                        '</div>'+
                        '</div>'+
                        '<div name="room" type="selectbox" class="ChildClick diyselect diyselect'+num+'">'+
                        '<div class="opts roomopts">'+data.ChildData+
                        '</div>'+
                        '</div>'+
                        '</li>';
                    html2 = '<li><span class="skuid hidden">'+data.PriceData.skuid+'</span><span class="fr">费用：￥<span class="cost">'+data.PriceData.cost+'</span></span><span class="fl"><span class="roomnum">房间<span class="priceroom">1</span></span>：<span class="checknum">'+data.PriceData.checknum+'</span></span></li>';
                    $("#operate").before(html);
                    $("#roomprice").append(html2);
                    diyselectx(num);
                    TotalPrice();
                    RemoveRoom(num);
                    if(num >= '4'){
                        $("#addroom").hide();
                    }
                    $("#roomprice li:last").find('.priceroom').empty();
                    $("#roomprice li:last").find('.priceroom').append(num);
                    AdultLoad(roomtext,num);
                    ChildLoad(roomtext,num);
                }else if(data.Type = '1'){
                    html = '<li data-id="'+ num +'">'+
                        '<span style="padding: 5px" class="roomnumx">房间'+ num +'</span>'+
                        '<div name="room" type="selectbox" class="AdultClick diyselect diyselect'+num+'">'+
                        '<div class="opts roomopts">'+data.AdultData+
                        '</div>'+
                        '</div>'+
                        '</li>';
                    html2 = '<li><span class="skuid hidden">'+data.PriceData.skuid+'</span><span class="fr">费用：￥<span class="cost">'+data.PriceData.cost+'</span></span><span class="fl"><span class="roomnum">房间<span class="priceroom">1</span></span>：<span class="checknum">'+data.PriceData.checknum+'</span></span></li>';
                    $("#operate").before(html);
                    $("#roomprice").append(html2);
                    diyselectx(num);
                    TotalPrice();
                    RemoveRoom(num);
                    if(num >= '4'){
                        $("#addroom").hide();
                    }
                    $("#roomprice li:last").find('.priceroom').empty();
                    $("#roomprice li:last").find('.priceroom').append(num);
                    AdultLoad(roomtext,num);
                    ChildLoad(roomtext,num);
                }
            }else{
                layer.msg(data.Message)
            }
        },
        complete: function () { //加载完成提示
            $("#loading").hide();
        }
    });
}

//移除房间方法
var numx;
function RemoveRoom(num) {
    numx = num;
    if(num > '1'){
        $(".clear").show();
    }
}

//移除房间
$(".RemoveRoom").click(function () {
    if($("#roomnum li").length > '1'){
        $('#roomnum li:last').remove();
        $("#roomprice li:last").remove();
        TotalPrice();
    }
    if(numx >= '4'){
        $("#addroom").show();
    }
    if($("#roomnum li").length == '1'){
        $(".clear").hide();
    }
})

//计算总价方法
function TotalPrice() {
    var sum = 0;
    $("#roomprice li").each(function () {
        sum += $(this).find('.cost').text() * 1;
    })
    $("#TotalPrice").text(sum);
}

//下拉框加载方法
function diyselect() {
    $('.RoomAdd .diyselect').inputbox({
        height:35,
        width:100
    });
}

//下拉框加载方法
function diyselectx(num) {
    $(".RoomAdd .diyselect"+num+"").inputbox({
        height:35,
        width:100
    });
}

//滚动定位导航方法
function scroll_fix(){
    if (jQuery(".DayBox ").text() != '')
    {
        var naviTop = jQuery(".contMenu").offset().top;
        var RoomBox = jQuery(".RoomBox").offset().top;
        var jumpEnd = jQuery(".DayBoxM").offset().top+jQuery(".DayBoxM").height()-jQuery(".DateMenu").height()-380;

        jQuery('.contMenu li').click(function(){
            var $dayLi = jQuery(this).index();
            var dInfor = jQuery(".contBox").eq($dayLi).offset().top - 50;
            jQuery('html, body').animate({ scrollTop: dInfor }, 500);
        });

        jQuery('.DateMenu li').click(function(){
            var $dayLi = jQuery(this).index();
            var dInfor = jQuery(".DayBox").eq($dayLi).offset().top - 50;
            jQuery('html, body').animate({ scrollTop: dInfor }, 500);
        });
        function checkScroll(forcon, forli, wtop)
        {
            var next = forcon.size()-1;
            while (next > -1)
            {
                var itemTop = forcon.eq(next).offset().top-70;
                if (wtop >= itemTop)
                {
                    forli.eq(next).addClass("on").siblings().removeClass("on");
                    return false;
                }
                next--;
            };
        }
        jQuery(window).scroll(function(){
            var wintop = jQuery(window).scrollTop();
            if($("#RoomBox").is('.hidden')){
                var charge =wintop-280
            }else {
                var charge =wintop-780
            }
            var chargeb =wintop-500
            if($("#RoomBox").is('.hidden')){
                if(naviTop >= wintop){
                    $(".contMenu ul").removeClass("fix_xc");
                    $("#oncebtn").hide();
                }
                else
                {
                    $(".contMenu ul").addClass("fix_xc");
                    $("#oncebtn").show();
                }
            }else {
                if(naviTop >= chargeb){
                    $(".contMenu ul").removeClass("fix_xc");
                    $("#oncebtn").hide();
                }
                else
                {
                    $(".contMenu ul").addClass("fix_xc");
                    $("#oncebtn").show();
                }
            }
            if (naviTop >= charge || jumpEnd <= charge ){
                $(".DateMenu ul").removeClass("FixDateMenu");
            }else{
                $(".DateMenu ul").addClass("FixDateMenu");
            }
            checkScroll(jQuery('.contBox'), jQuery('.contMenu li'), wintop);
            checkScroll(jQuery('.DayBox'), jQuery('.DateMenu li'), wintop);
        });
    }
}
//底部去除虚线方法
function addClass(){
	$(".DetailContM .contBox").last().addClass("last");
	$(".contBox").each(function(){
		$(this).find(".FreeCont").last().addClass("last");
	})
}

