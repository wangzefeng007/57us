/**
 * Created by Foliage on 2017/3/2.
 */
$(document).ready(function () {
    //初始化加载
    loadPage('1');
    //点击管理
    $(".mangeBtn").on("click", function() {
        if($(".newsList").hasClass("mangeOn")) {
            $(".newsList").removeClass("mangeOn");
            $(this).html("管理");
            $(".deteBox").addClass("hidden");
            $(".content").removeClass("pdb");
        } else {
            $(".newsList").addClass("mangeOn");
            $(this).html("取消");
            $(".deteBox").removeClass("hidden");
            $(".content").addClass("pdb");
        }
    });

    //点击展开,发送此消息已读给后端
    $(document).on("click",'.newsList .card', function() {
        var $this = $(this);
        var $thisId = $(this).parents('li').attr('data-id');
        var $thisType = $(this).parents('li').attr('data-type');
        var ajaxData = {
            'Intention': 'DisposeMemberMessage', //留言读
            'ID':$thisId, //ID
        };
        if($thisType == '1'){
            $.post('/ajax/',ajaxData,function (data) {
                if(data.ResultCode == '200'){
                    if($this.is('.has')){
                    }else {
                        $this.addClass("on").parent("li").siblings().find(".card").removeClass("on");
                        $this.addClass('has');
                        $this.parent("li").attr('data-type','2');
                    }
                }
            },'json');
        }else {
            $this.addClass("on").parent("li").siblings().find(".card").removeClass("on");
        }
    });

    //删除事件
    $(document).on('click','.delBtn',function () {
        var idS = [];
        $("#list li input:checked").each(function () {
            idS.push($(this).parents('li').attr('data-id'));
        })
        var ajaxData ={
            'Intention':'DelMemberMessage', //方法
            'IDS':idS, //删除ID 数组
        };

        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajax/",
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

/**
 * 页面数据加载
 *
 * @param Intention 加载方法
 * @param Page 页码
 */
//定义页码变量
var j = 2;
function loadPage(page) {
    var ajaxData = {
        'Intention': 'GetMemberMessage', //订单列表
        'Page':page, //页码
    };
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/ajax/",
        data: ajaxData,
        beforeSend: function () {
            $.showPreloader();
        },
        success: function(data) {
            if(data.ResultCode == '200'){
                loadPageSucceed(data);
                maxItems = data.RecordCount;
            }else if(data.ResultCode == '101'){
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
        $("#newsList").on('infinite', function() {
            // 如果正在加载，则退出
            if (loading) return;
            // 设置flag
            loading = true;

            var ajaxData = {
                'Intention': 'GetMemberMessage', //订单列表
                'Page':j, //页码
            };

            $.post('/ajax/',ajaxData, function(data) {
                // 重置加载flag
                loading = false;
                if(data.ResultCode == '200') {
                    maxItems = data.RecordCount;
                    if(data.Data.length) {
                        //循环输出列表数据
                        var item = '';
                        $.each(data.Data, function(i, list) {
                            item +='<li data-id="'+list.ID+'" data-type="'+list.Type+'">';
                            item +='<label class="label-checkbox">';
                            item +='<input type="checkbox" name="checkbox">';
                            item +='<div class="item-media"><i class="icon icon-form-checkbox"></i></div>';
                            item +='</label>';
                            item +='<div class="card '+get_haveread_message(list.Type)+'">';
                            item +='<span class="cirle"></span>';
                            item +='<div class="card-header">'+list.Name+'</div>';
                            item +='<div class="card-content">';
                            item +='<div class="card-content-inner">'+list.Message+'</div>';
                            item +='</div>';
                            item +='<div class="card-footer">'+list.Date+'</div>';
                            item +='</div>';
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
 * @param data ajax返回json数据
 * @param ID 记录id
 * @param Name 名称
 * @param Date 时间
 * @param Message 消息
 * @param Type 1代表未读 2代表已读
 */
function loadPageSucceed(data) {
    $("#list").empty();
    $("#noOrder").remove();
    if(data.RecordCount > 6){
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
        item +='<li data-id="'+list.ID+'" data-type="'+list.Type+'">';
        item +='<label class="label-checkbox">';
        item +='<input type="checkbox" name="checkbox">';
        item +='<div class="item-media"><i class="icon icon-form-checkbox"></i></div>';
        item +='</label>';
        item +='<div class="'+get_haveread_message(list.Type)+'">';
        item +='<span class="cirle"></span>';
        item +='<div class="card-header">'+list.Name+'</div>';
        item +='<div class="card-content">';
        item +='<div class="card-content-inner">'+list.Message+'</div>';
        item +='</div>';
        item +='<div class="card-footer">'+list.Date+'</div>';
        item +='</div>';
        item +='</li>';
    });
    $('#list').append(item);
}

/**
 * 加载成功，无数据
 */
function loadPageError() {
    $("#list").empty();
    $(".infinite-scroll-preloader").hide();
    $(".infinite-scroll-preloader").hide();
    // 加载完毕，则注销无限加载事件，以防不必要的加载
    $.detachInfiniteScroll($('.infinite-scroll'));
    $(".pullUpLabel").hide();
    var errHtml = '';
    errHtml +='<div class="noOrder">';
    errHtml +='<div class="noOrderCont mt10">';
    errHtml +='<div class="noBox">';
    errHtml +='<i class="icon iconfont icon-smile"></i>';
    errHtml +='<p class="tit">您暂时没有相关消息</p>';
    errHtml +='<a href="http://m.57us.com" class="button backHome">去首页看看</a>';
    errHtml +='</div>';
    errHtml +='</div>';
    errHtml +='</div>';
    $('.content').append(errHtml);
}

/**
 * 留言信息是否已经读过
 * @param type
 */
function get_haveread_message(type) {
    switch(type){
        case "1":
            return "card ";
            break;
        case "2":
            return "card has";
            break;
    }
}