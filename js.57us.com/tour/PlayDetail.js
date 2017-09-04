$(function(){
    //底部同城推荐
    jQuery(".ScrollPic").slide({mainCell:".ScrollMain ul",autoPage:true,effect:"left",vis:4,pnLoop:'false'});
    ScrollFixed();
    //
    addClass()
    //获取当前产品是否下架  0为未下架  1为下架 如果产品已下架则不执行
    if($(".noProIco").is(':hidden')){
        var noProIco = '0';
    }else {
        var noProIco = '1';
        $("#NowOrderBtn,#oncebtn").text('已售馨');
        $("#NowOrderBtn,#oncebtn").addClass('course');
        return
    }

    //特色体验下拉菜单
    $('.procont1 .diyselect').inputbox({
        height:35,
        width:412
    });

    //日期插件调用
    $('#calendar').on('click', function(e) {
        calendar(e);
    });
    //点击递增递减
    $('.num_box').W_NumberBox({
        "min": 1,
        "max": 99,
        "maxlength": 2,
    }, function() {
        var date = $("#calendar").val();
        if(date == ''){
            layer.msg('请选择出行日期');
            $("#calendar").addClass('erro');
            return
        }
        //计算总价
        $(".num_box").on('click',function () {
            numclick();
        })
        return true;
    });

    $(".num_box a").on('click',function (e) {
        var date = $("#calendar").val();
        if(date == ''){
            calendar(e);
        }
    })

    $(".num_box input").on('change input',function () {
        numclick();
    })

    //计算总价
    function numclick() {
        var num = $(".num_box").find('input').val();
        var productprice = $("#productprice").text();
        var totalprice = num * productprice;
        $("#Number").val(num);
        $("#totalprice").empty();
        $("#totalprice").append(totalprice+'.00');
    }

    //切换套餐
    $("#combo a").on('click',function () {
        $("#calendar").val('');
        $("#calendar").attr('value','');
        $(".num_box input").val('1');
        $(".num_box input").attr('value','1');
        var initialprice =$(this).attr('data-price');
        var initialid = $(this).attr('value');
        $("#ProductSkuID").val(initialid);
        $("#totalprice").empty();
        $("#totalprice").append(initialprice);
        // $(".num_box input").attr('readonly',true);
    })

    //滚动导航预定
    $("#oncebtn").click(function (e) {
        var date = $("#calendar").val();
        if(date == ''){
            $('body').animate({scrollTop: $(".TourMenu").offset().top}, 400);
            layer.msg('请选择出行日期');
            $("#calendar").addClass('erro');
            calendar(e);
        }else{
            $("#oncebtn").text('预定中...');
            $("#oncebtn").addClass('course');
            $("#NowOrderBtn").trigger('click');
        }
    })

    //点评事件
    if($(".Comments").length > 0){
        Ajax('1');
    }

    //点击预定跳转至一个页面
    $("#NowOrderBtn").click(function (e) {
        var date = $("#calendar").val();
        var TotalPrice = $("#totalprice").text();
        var ProductPrice = $("#productprice").text()
        if(date == ''){
            layer.msg('请选择出行日期');
            $("#calendar").addClass('erro');
            calendar(e);
            return
        }
        ajaxdata = {
            'TourProductID':$("#TourProductID").val(),
            'ProductSkuID':$("#ProductSkuID").val(),
            'DayPriceID':$("#DayPriceID").val(),
            'Number':$(".num_input").val(),
        }
        /*$.post('/play/playplaceorder/', ajaxdata, function(data) {
         // console.log(data);
         }, 'json');*/
        $("#NowOrderBtn").text('预定中...');
        $("#NowOrderBtn").addClass('course');
        $("#NowOrderBtn").attr('id','');
        location.href="/play/playplaceorder/?TourProductID="+ajaxdata.TourProductID+'&ProductSkuID='+ajaxdata.ProductSkuID+'&DayPriceID='+ajaxdata.DayPriceID+'&Number='+ajaxdata.Number+'&TotalPrice='+TotalPrice+'&Date='+date+'&ProductPrice='+ProductPrice;
    })

    //填写订单页面返回
    var ProductSkuID = GetQueryString('ProductSkuID');
    var DayPriceID = GetQueryString('DayPriceID');
    var Date = GetQueryString('Date')
    var Number = GetQueryString('Number');
    var TotalPrice = GetQueryString('TotalPrice');
    var ProductPrice = GetQueryString('ProductPrice');
    $("#NoPrice").val(TotalPrice);
    var NoPrice = $("#NoPrice").val();
    if(NoPrice != ''){
        $("#combo a").each(function () {
            var DataId = $(this).attr('data-id');
            if(DataId == ProductSkuID){
                $("#combo a").removeClass('selected');
                $(this).addClass('selected');
                var text = $(this).text();
                $("div .selected").text(text);
            }
            $("#combo input").attr('value',ProductSkuID);
        })
        $("#ProductSkuID").val(ProductSkuID);
        $("#DayPriceID").val(DayPriceID);
        $("#calendar").val(Date);
        $("#calendar").attr('value',Date);
        $(".num_input").val(Number),
            // $("#Number").val(Number);
            $(".num_box input").val(Number);
        $(".num_box input").attr('value',Number);
        $("#totalprice").empty();
        $("#totalprice").append(TotalPrice);
        $("#productprice").empty();
        $("#productprice").append(ProductPrice);
        // $(".num_box input").attr('readonly',false);
    }

    //评价点赞
    $(document).on('click','.TourZan',function () {
        var _this = $(this);
        var ajaxData = {
            'Intention': 'AddPraise', //方法
            'EvaluateID':$(this).attr('data-id'), //提交的id
        }
        $.post('/ajax.html',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                layer.msg(data.Message);
                _this.find('.zanNum').text(data.Num);
            }else{
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
})

//点评方法
function Ajax(Page) {
    var ajaxData = {
        'Intention': 'TourComments', //方法
        'TourProductID':$("#TourProductID").val(), //产品编号
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

//幻灯片切换
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

//日期插件使用方法
function calendar(e) {
    var comboid = $("#combo").find('input').attr('value');
    var sjData = $("#"+comboid+"").text();
    pickerEvent.setPriceArr(eval("("+sjData+")"));
    pickerEvent.setIdArr(eval("("+sjData+")"));
    pickerEvent.Init("calendar");
    e.stopPropagation();
}

//定位锚点
function ScrollFixed() {
    var naviTop = jQuery(".contMenu").offset().top;
    jQuery('.contMenu ul li').click(function() {
        var $dayLi = jQuery(this).index();
        var dInfor = jQuery(".contBox").eq($dayLi).offset().top - 50;
        jQuery('html, body').animate({
            scrollTop: dInfor
        }, 5);
    });
    function checkScroll(forcon, forli, wtop) {
        var next = forcon.size() - 1;
        while (next > -1) {
            var itemTop = forcon.eq(next).offset().top - 70;
            if (wtop >= itemTop) {
                forli.eq(next).addClass("on").siblings().removeClass("on");
                return false;
            }
            next--;
        };
    }
    jQuery(window).scroll(function() {

        var wintop = jQuery(window).scrollTop();
        if (naviTop >= wintop) {
            $(".contMenu ul").removeClass("fix_xc");
            $("#oncebtn").hide();
        } else {
            $(".contMenu ul").addClass("fix_xc");
            $("#oncebtn").show();
        }
        checkScroll(jQuery('.contBox'), jQuery('.contMenu ul li'), wintop);

    });

}
//添加样式last
function addClass(){
    $(".DetailContM .contBox").last().addClass("last");
    $(".contBox").each(function(){
        $(this).find(".FreeCont").last().addClass("last");
    })
}
