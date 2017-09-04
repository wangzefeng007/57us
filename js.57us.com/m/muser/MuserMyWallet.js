/**
 * Created by Foliage on 2017/2/21.
 */
$(function () {
    loadPage('1');

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

//定义页码变量
var j = 2;

function loadPage(page) {
    var ajaxData = {
        'Page':page, //分页
    }

    $.post('/member/mywallet/',ajaxData,function (data) {
        if(data.ResultCode == '200'){
            loadPageSucceed(data);
            maxItems = data.RecordCount;
        }else if(data.ResultCode == '102'){
            loadPageError(data);
        }else{
            $.toast(data.Message);
        }
    },'json');

    //计算全部可以加载的条数
    var maxItems = '';
    $(document).on("pageInit", function() {
        var loading = false;
        $("#page").on('infinite', function() {
            // 如果正在加载，则退出
            if (loading) return;
            // 设置flag
            loading = true;

            var ajaxData = {
                'Page':j, //分页
            }

            $.post('/member/mywallet/',ajaxData, function(data) {
                // 重置加载flag
                loading = false;
                if(data.ResultCode == '200') {
                    if(data.Data.length) {
                        //循环输出列表数据
                        var item = '';
                        $.each(data.Data, function(i, list) {
                            item +='<li class="row no-gutter"><div class="col-50"><p>'+list.Title+'</p><p>'+list.AddTime+'</p></div><div class="col-25 tac moneyNum">'+list.Money+'</div></li>';
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

function loadPageSucceed(data) {
    $("#list").empty();
    if(data.RecordCount > 6){
        $(".infinite-scroll-preloader").show();
    }else {
        $(".infinite-scroll-preloader").hide();
    }
    //循环输出列表数据
    var item = '';
    $.each(data.Data, function(i, list) {
        item +='<li class="row no-gutter"><div class="col-50"><p>'+list.Title+'</p><p>'+list.AddTime+'</p></div><div class="col-25 tac moneyNum">'+list.Money+'</div></li>';
    });
    $('#list').append(item);
}

function loadPageError(data) {
    $(".noList").show();
}