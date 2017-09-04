/**
 * Created by Foliage on 2017/2/21.
 */
$(function () {
    'use strict';
    //游学主题写入
    $.get('/Templates/M/data/StudyTour/Theme.json',function (data) {
        filtrateLoad('#theme',data);
    },'json');

    //适合人群
    $.get('/Templates/M/data/StudyTour/Crowd.json',function (data) {
        filtrateLoad('#crowd',data);
    },'json');

    //出行天数
    $.get('/Templates/M/data/StudyTour/TripDate.json',function (data) {
        filtrateLoad('#tripDate',data);
    },'json');

    //出发地
    $.get('/Templates/M/data/StudyTour/TripPlace.json',function (data) {
        filtrateLoad('#tripPlace',data);
    },'json');

    //初始化加载
    loadPage();

    //点击打开筛选条件
    $(".yxshaixuan li").on("click",function(){
        var $num = $(this).index();
        if($(this).hasClass("on")){
            $(this).removeClass("on");
            $(".sxbox").hide();
            $(".sxMask").hide();
        }else{
            $(this).addClass("on");
            $(this).siblings().removeClass("on");
            $(".sxbox").hide().eq($num).show();
            $(".sxMask").show();
            if($(".diySearch").hasClass('active')){
                $(".diySearch ").removeClass('active');
            }
        }
    });

    //重新晒选
    $(document).on('click','.checkBtn',function () {
        $('.yxshaixuan li').eq(0).addClass("on");
        $(".sxbox").hide().eq(0).show();
        $(".sxMask").show();
    })

    //点击模态窗口关闭筛选
    $(".sxMask").on('click',function () {
        $(this).hide();
        $(".sxbox").hide();
        $(".yxshaixuan li").removeClass('on');
    });

    //筛选条件点击
    $(document).on('click','#filtrate .hd a',function () {
        var $thisDom = $(this);
        var $thisSuperiorDom = $(this).parents('.hd');
        if($thisDom.attr('data-value') != '0'){
            $thisSuperiorDom.find("[data-value='0']").removeClass('on');
            if($thisDom.is('.on')){
                $thisDom.removeClass('on');
                if($thisSuperiorDom.find('.on').length < 1){
                    $thisSuperiorDom.find("[data-value='0']").addClass('on');
                }
            }else {
                $thisDom.addClass('on');
            }
        }else {
            $thisSuperiorDom.find('a').removeClass('on');
            $thisSuperiorDom.find("[data-value='0']").addClass('on');
        }
    });

    //筛选点击确定
    $(document).on('click','#filtrate .confirmBtn',function () {
        $(".sxMask").hide();
        $(".sxbox").hide();
        $(".yxshaixuan li").removeClass('on');
        j = '2';
        $("#search").val('');
        loadPage();
    });

    //搜索打开
    $(document).on('click','.searchBtn',function(){
        if($(this).hasClass('active')){
            $(".diySearch").removeClass("active");
        }else {
            $(".diySearch").addClass("active");
            if($('.sxMask').css('display') == 'block'){
                $(".sxMask").hide();
                $(".sxbox").hide();
                $(".yxshaixuan li").removeClass('on');
            }
        }
    });
    //搜索关闭
    $(".diySearch .close").on('click',function(){
        $(".diySearch").removeClass("active");
    });

    //搜索
    $("#search-input").submit(function() {
        j = '2';
        $('.diySearch').removeClass("active");
        $("#filtrate .hd a").removeClass('on');
        $("#theme .hd a").eq(0).addClass('on');
        $("#crowd .hd a").eq(0).addClass('on');
        $("#tripDate .hd a").eq(0).addClass('on');
        $("#tripPlace .hd a").eq(0).addClass('on');
        loadPage();
        return false;
    });

    //下拉刷新页面
    $(document).one("pageInit", ".page", function(e, id, page) {
        var $content = $(page).find(".content").on('refresh', function(e) {
            // 模拟2s的加载过程
            window.location.reload();
            $.pullToRefreshDone($content);
        });
    });

    $.init();
})

/**
 * 筛选条件数据注入
 *
 * @param id 对应操作id
 * @param data get到的数据
 */
function filtrateLoad(id,data) {
    $(""+id+"").find('.hd').eq(0).empty();
    var item ='';
    $.each(data, function(i, list) {
        item +='<a href="javascript:void(0)" data-value="'+list.id+'">'+list.name+'</a>'
    });
    var html = '';
    html += '<a href="javascript:void(0)" data-value="0" class="on">不限</a>';
    html +=item;
    $(""+id+"").find('.hd').eq(0).append(html);
}


//定义页码变量
var j = 2;
/**
 * 加载方法
 */
function loadPage() {
    if($('#theme .hd .on').length >0){
        var theme = [];
        $("#theme .hd .on").each(function () {
            theme.push($(this).attr('data-value'));
        })
        var crowd = [];
        $("#crowd .hd .on").each(function () {
            crowd.push($(this).attr('data-value'));
        })
        var tripDate = [];
        $("#tripDate .hd .on").each(function () {
            tripDate.push($(this).attr('data-value'));
        })
        var tripPlace = [];
        $("#tripPlace .hd .on").each(function () {
            tripPlace.push($(this).attr('data-value'));
        })
    }else {
        var theme = ['0'];
        var crowd = ['0']
        var tripDate = ['0'];
        var tripPlace = ['0'];
    }
    var ajaxData = {
        'Intention':'StudyTourList', //方法
        'Theme':theme, //出游主题
        'Crowd':crowd, //适合人群
        'TripDate':tripDate, //出行天数
        'TripPlace':tripPlace, //出发地
        'Page':'1', //页码
        'Keyword':$("#search").val(), //关键字
    }

    $.ajax({
        type: "post",
        dataType: "json",
        url: "/ajaxstudy/",
        data: ajaxData,
        beforeSend: function () {
            $.showPreloader();
        },
        success: function (data) {
            if(data.ResultCode == '200'){
                loadPageSucceed(data);
                maxItems = data.RecordCount;
            }else if(data.ResultCode == '101'){
                loadPageSucceed(data);
            }else{
                $.toast(data.Message);
            }
        },complete: function () {
            $.hidePreloader();
        }
    })

    //计算全部可以加载的条数
    var maxItems = '';
    $(document).on("pageInit", function() {
        var loading = false;
        var $page = $('#page');
        $page.on('infinite', function() {
            // 如果正在加载，则退出
            if (loading) return;
            // 设置flag
            loading = true;
            ajaxData = {
                'Intention':'StudyTourList', //方法
                'Theme':theme, //出游主题
                'Crowd':crowd, //适合人群
                'TripDate':tripDate, //出行天数
                'TripPlace':tripPlace, //出发地
                'Page':j, //页码
                'Keyword':$("#search").val(), //关键字
            }

            $.post('/ajaxstudy/',ajaxData, function(data) {
                // 重置加载flag
                loading = false;
                if(data.ResultCode == '200') {
                    if(data.Data.length) {
                        //循环输出列表数据
                        //循环输出列表数据
                        var item = '';
                        $.each(data.Data, function(i, list) {
                            item +='<li data-id="'+list.StudyId+'" data-type="'+list.StudyWTRecommend+'">';
                            item +='<a href="/study/studytourdetail/?ID='+list.StudyId+'" class="external">';
                            item +='<span class="tj"></span>';
                            item +='<img src="'+list.StudyImg+'"/>';
                            item +='<p class="tit">'+list.StudyTitle+'</p>';
                            item +='<p class="endTime">报名截止日期：'+list.StudyEndDate+'</p>';
                            item +='<p class="price">';
                            item +='<span class="nPrice"><em>￥</em>'+list.StudyPrice+'/人</span>';
                            item +='<span class="odPrice">￥'+list.StudyOriginalPrice+'/人</span>';
                            item +='</p>';
                            item +='</a>';
                            item +='</li>';
                        });
                        $page.find('#list').append(item);
                        $('#list li').each(function () {
                            var $type = $(this).attr('data-type');
                            if($type == '1'){
                                $(this).addClass('on');
                            }
                        });
                        //计算目前所有li的条数
                        var lastIndex = $page.find('#list li').length;
                        //重新计算页码
                        j = Number(j) + Number('1');

                        //当加载完所有内容，注销无限加载事件
                        if (lastIndex >= maxItems) {
                            // 加载完毕，则注销无限加载事件，以防不必要的加载
                            $.detachInfiniteScroll($page.find('.infinite-scroll'));
                            // 删除加载提示符
                            $page.find('.infinite-scroll-preloader').hide();
                            $page.find('.pullUpLabel').show();
                            return;
                        }
                    } else {
                        // 加载完毕，则注销无限加载事件，以防不必要的加载
                        $.detachInfiniteScroll($page.find('.infinite-scroll'));
                        // 删除加载提示符
                        $page.find('.infinite-scroll-preloader').hide();
                        $page.find('.pullUpLabel').show();
                        return;
                    }
                }

                //sui上拉刷新重置
                $.refreshScroller();
            }, 'json')
        });
    });
}

/**
 * 加载成功，并且有数据
 *
 * @param data post得到的json数据
 * @param data.StudyId 产品id
 * @param data.StudyTitle 产品标题
 * @param data.StudyImg 产品图片url
 * @param data.StudyEndDate 报名截止时间
 * @param data.StudyPrice 价格
 * @param data.StudyOriginalPrice 原价
 * @param data.StudyWTRecommend 是否推荐 0不代表推荐 1代表推荐
 */
function loadPageSucceed(data) {
    var $page = $("#page");
    var $list = $page.find('#list');
    $list.empty();
    if(data.RecordCount > 6){
        $page.find('.infinite-scroll-preloader').show();
        $.attachInfiniteScroll($page.find('.infinite-scroll'));
    }else {
        $page.find('.infinite-scroll-preloader').hide();
        $.detachInfiniteScroll($page.find('.infinite-scroll'));
    }
    $page.scrollTop(0);
    //循环输出列表数据
    var item = '';
    $.each(data.Data, function(i, list) {
        item +='<li data-id="'+list.StudyId+'" data-type="'+list.StudyWTRecommend+'">';
        item +='<a href="/study/studytourdetail/?ID='+list.StudyId+'" class="external">';
        item +='<span class="tj"></span>';
        item +='<img src="'+list.StudyImg+'"/>';
        item +='<p class="tit">'+list.StudyTitle+'</p>';
        item +='<p class="endTime">报名截止日期：'+list.StudyEndDate+'</p>';
        item +='<p class="price">';
        item +='<span class="nPrice"><em>￥</em>'+list.StudyPrice+'/人</span>';
        item +='<span class="odPrice">￥'+list.StudyOriginalPrice+'/人</span>';
        item +='</p>';
        item +='</a>';
        item +='</li>';
    });
    $list.append(item);
    $('#list li').each(function () {
        var $type = $(this).attr('data-type');
        if($type == '1'){
            $(this).addClass('on');
        }
    });
}

/**
 * 加载成功，但无数据
 */
function loadPageError() {
    var $page = $("#page");
    var $list = $page.find('#list');
    $list.empty();
    var $searchVal = $("#search").val();
    if($searchVal == ''){
        var html = '';
        html +='<div class="Nosearch tac">';
        html +='<p><i class="icon iconfont sadIcon">&#xe615;</i></p>';
        html +='<p class="text">筛选无内容：暂时没有找到符合条件的顾问</p>';
        html +='<a href="javascript:void(0)" class="button button-big external checkBtn">重新筛选</a>';
        html +='</div>';
    }else {
        var html = '';
        html +='<div class="Nosearch tac"><p><i class="icon iconfont sadIcon">&#xe615;</i></p>';
        html +='<p class="text">搜索无内容：暂时没有找到“'+$searchVal+'”的相关的产品</p>';
        html +='<a href="javascript:void(0)" class="button button-big external searchBtn">重新搜索</a>';
        html +='</div>';
    }
    $("#list").append(html);
    $.detachInfiniteScroll($page.find('.infinite-scroll'));
}