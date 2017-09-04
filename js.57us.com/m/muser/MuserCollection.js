/**
 * Created by Foliage on 2017/2/24.
 */
$(function () {
    //点击收藏下拉打开
    // $(".mangeList .btn").on("click", function() {
    //     if($(".mangeList").hasClass("on")) {
    //         $(this).parents().removeClass("on");
    //         $(".collectMask").hide();
    //     } else {
    //         $(this).parents().addClass("on");
    //         $(".collectMask").show();
    //     }
    // })
    //点击收藏下拉关闭
    // $(".collectMask").on("click", function() {
    //     $(".mangeList").removeClass("on");
    //     $(this).hide();
    // })

    loadPage('1');
    //点击管理
    $(document).on("click",'.mangeBtn', function() {
        if($(".collectList").hasClass("mangeOn")) {
            $(".collectList").removeClass("mangeOn");
            $(this).html("管理");
            $(".deteBox").addClass("hidden");
            $(".content").removeClass("pdb");
        } else {
            $(".collectList").addClass("mangeOn");
            $(this).html("取消");
            $(".deteBox").removeClass("hidden");
            $(".content").addClass("pdb");
        }
    });

    //收藏删除事件
    $(document).on('click','.delBtn',function () {
        var idS = [];
        $("#list li input:checked").each(function () {
            idS.push($(this).parents('li').attr('data-id'));
        })
        var ajaxData ={
            'Intention':'DelCollection', //方法
            'IDS':idS, //删除ID 数组
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('删除中');
            },
            success: function(data) {
                if(data.ResultCode == '200'){
                    $.toast(data.Message);
                    setTimeout(function() {
                        window.location.reload();
                    },500);
                }else{
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
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
});

//定义页码变量
var j = 2;
function loadPage(page) {
    var ajaxData = {
        'Intention': 'GetCollection', //方法
        't': $("#collectionType").val(), //模块 news 资讯 tour 旅游
        'Page':page//页码
    }

    $.ajax({
        type: "post",
        dataType: "json",
        url: "/userajax.html",
        data: ajaxData,
        beforeSend: function () {
            $.showPreloader();
        },
        success: function(data) {
            if(data.ResultCode == '200'){
                loadPageSucceed(data);
                maxItems = data.RecordCount;
            }else if(data.ResultCode == '102'){
                loadPageError(data);
            }else{
                $.toast(data.Message);
            }
        },
        complete: function () { //加载完成提示
            $.hidePreloader();
        }
    });

    //计算全部可以加载的条数
    var maxItems = '';
    $(document).on("pageInit", function() {
        var loading = false;
        $("#page").on('infinite', function() {
            // 如果正在加载，则退出
            if (loading) return;
            // 设置flag
            loading = true;
            ajaxData = {
                'Intention': 'GetCollection', //方法
                't': $("#collectionType").val(), //模块 news 资讯 tour 旅游
                'Page':j//页码
            }

            $.post('/userajax.html',ajaxData, function(data) {
                // 重置加载flag
                loading = false;
                if(data.ResultCode == '200') {
                    maxItems = data.RecordCount;
                    if(data.Data.length) {
                        //循环输出列表数据
                        var item = '';
                        $.each(data.Data, function(i, list) {
                            item +='<li data-id="'+list.ID+'">';
                            item +='<label class="label-checkbox">';
                            item +='<input type="checkbox" name="checkbox">';
                            item +='<div class="item-media"><i class="icon icon-form-checkbox"></i></div>';
                            item +='</label>';
                            item +='<a href="'+list.Url+'" class="img">';
                            item +='<span class="tip">'+list.Type+'</span>';
                            item +='<img src="'+list.ImageUrl+'"/>';
                            item +='</a>';
                            item +='<div class="listCon">'+list.ProductName+'</div>';
                            item +='</li>';
                        });
                        $('#list').append(item);
                        //计算目前所有li的条数
                        var lastIndex = $('#list li').length;
                        //重新计算页码
                        j = Number(j) + Number('1');
                        //当加载完所有内容，注销无限加载事件
                        if (lastIndex >= maxItems) {
                            // 加载完毕，则注销无限加载事件，以防不必要的加载
                            $.detachInfiniteScroll($('.infinite-scroll'));
                            // 删除加载提示符
                            $('.infinite-scroll-preloader').hide();
                            $(".pullUpLabel").show();
                            return;
                        }
                    } else {
                        // 加载完毕，则注销无限加载事件，以防不必要的加载
                        $.detachInfiniteScroll($('.infinite-scroll'));
                        // 删除加载提示符
                        $('.infinite-scroll-preloader').hide();
                        $(".pullUpLabel").show();
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
 * 加载成功并且有数据
 *
 * @param data 返回的json数据
 * @param data.ID ID
 * @param data.Url url
 * @param data.Type 属于什么类型
 * @param data.ProductName 名称
 * @param data.ImageUrl 图片url
 */
function loadPageSucceed(data) {
    $("#list").empty();
    if(data.RecordCount > 8){
        $.attachInfiniteScroll($('.infinite-scroll'));
        $(".pullUpLabel").hide();
        $(".infinite-scroll-preloader").show();
    }else {
        $.detachInfiniteScroll($('.infinite-scroll'));
        $(".infinite-scroll-preloader").hide();
        $(".pullUpLabel").show();
    }
    //循环输出列表数据
    var item = '';
    $.each(data.Data, function(i, list) {
        item +='<li data-id="'+list.ID+'">';
        item +='<label class="label-checkbox">';
        item +='<input type="checkbox" name="checkbox">';
        item +='<div class="item-media"><i class="icon icon-form-checkbox"></i></div>';
        item +='</label>';
        item +='<a href="'+list.Url+'" class="img">';
        item +='<span class="tip">'+list.Type+'</span>';
        item +='<img src="'+list.ImageUrl+'"/>';
        item +='</a>';
        item +='<div class="listCon">'+list.ProductName+'</div>';
        item +='</li>';
    });
    $('#list').append(item);
}

/**
 * 加载成功，无数据
 */
function loadPageError() {
    $(".infinite-scroll-preloader").hide();
    $(".infinite-scroll-preloader").hide();
    // 加载完毕，则注销无限加载事件，以防不必要的加载
    $.detachInfiniteScroll($('.infinite-scroll'));
    $(".pullUpLabel").hide();
    //模块 news 资讯 tour 旅游
    if($("#collectionType").val() == 'news'){
        var url = 'http://m.57us.com/';
    }else if($("#collectionType").val() == 'tour'){
        var url = 'http://m.57us.com/tour/'
    }
    var errHtml = '';
    errHtml +='<div class="noOrder">';
    errHtml +='<div class="noOrderCont mt10">';
    errHtml +='<div class="noBox">';
    errHtml +='<i class="icon iconfont icon-smile"></i>';
    errHtml +='<p class="tit">您暂时没有收藏内容</p>';
    errHtml +='<a href="'+url+'" class="button backHome">去首页看看</a>';
    errHtml +='</div>';
    errHtml +='</div>';
    errHtml +='</div>';
    $('#list').append(errHtml);
}