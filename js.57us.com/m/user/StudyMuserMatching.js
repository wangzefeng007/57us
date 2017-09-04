/**
 * Created by Foliage on 2016/11/25.
 */
$(function () {
    //初始化加载
    ajaxLoad();

    $(document).on('click','.contantList li',function () {
        var _matchSu = $(this).find(".matchSu").attr('data-id');
        var _thisId = $(this).attr('data-id');
        if(_matchSu != '0'){
            window.location='/muserstudy/matchingdetail/?MarryID='+_thisId;
        }else {
            $.toast('此条记录匹配失败，无详情');
        }
    })

    $.init();
})

//ajax提交参数
var ajaxData = {
    'Intention':'ConsultantMatching', //方法
    'Page':'1', //页码
}

//定义页码变量
var j = 2;

//初始化加载方法
function ajaxLoad() {
    $.post('/ajaxstudy/',ajaxData,function (data) {
        //console.log(data);
        if(data.ResultCode == '200'){
            if(data.Count > 6){
                $(".infinite-scroll-preloader").show();
                $.attachInfiniteScroll($('.infinite-scroll'));
            }else {
                $(".infinite-scroll-preloader").hide();
            }
            $(".Count").text(data.Count);
            DataSuccess(data);
            Times = data.Times;
            maxItems = data.Count;
        }else if(data.ResultCode == '101'){
            DataFailure();
        }else if(data.ResultCode == '500'){
            $.toast(data.Message);
            if(data.Url!='undefined'){
                setTimeout(function(){window.location=data.Url;},800);
            }  
        }else{
            $.toast(data.Message);
        }
    },'json');

    //计算全部可以加载的条数
    var maxItems = '';

    $(document).on("pageInit", function() {
        var loading = false;
        $(".page").on('infinite', function() {
            // $(document).on('infinite','#page',function() {
            // 如果正在加载，则退出
            if (loading) return;
            // 设置flag
            loading = true;
            ajaxData.Page = j;
            $.post('/ajaxstudy/',ajaxData, function(data) {
                // 重置加载flag
                loading = false;
                if(data.ResultCode == '200' || data.ResultCode == '102') {
                    if(data.Data.length) {
                        var item;
                        $.each(data.Data, function(i, list) {
                            item =  '<li data-id="'+list.MarryID+'">' +
                                '<a href="javascript:void(0)" class="external">' +
                                '<span class="left">'+list.MarryName.substr(0, 1)+'</span>' +
                                '<div class="cont">' +
                                '<p class="tit">'+list.MarryName+'</p>' +
                                '<div class="row no-gutter">' +
                                '<div class="col-50">'+list.MarryCity+'</div>' +
                                '<div class="col-50">目前：'+list.MarryGrade+'</div>' +
                                '</div>' +
                                '<div class="row no-gutter">' +
                                '<div class="col-50">'+list.GoAbroadTime+'</div>' +
                                '<div class="col-50">目标：'+list.MarryTargetLevel+'</div>' +
                                '</div>' +
                                '</div>' +
                                '<div class="right">' +
                                '<p class="red howNum">'+list.Consultants+'位顾问</p>' +
                                '<p class="matchSu" data-id="'+list.Consultants+'">匹配成功</p>' +
                                '</div>' +
                                '</a>' +
                                '</li>';
                            $('.matchList').append(item);
                        })
                        $(".matchSu").each(function () {
                            var _this = $(this).attr('data-id');
                            if(_this == '0'){
                                $(this).text('匹配失败');
                            }
                        })
                        //计算目前所有li的条数
                        var lastIndex = $('.contantList li').length;
                        //重新计算页码
                        j = parseInt(lastIndex / 6 + 1);

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
                        $('.infinite-scroll-preloader').remove();
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

//有匹配到顾问
function DataSuccess(data) {
    $(".contantList").empty();
    $(".Nosearch").remove();
    var item;
    $.each(data.Data, function(i, list) {
        item =  '<li data-id="'+list.MarryID+'">' +
            '<a href="javascript:void(0)" class="external">' +
            '<span class="left">'+list.MarryName.substr(0, 1)+'</span>' +
            '<div class="cont">' +
            '<p class="tit">'+list.MarryName+'</p>' +
            '<div class="row no-gutter">' +
            '<div class="col-50">'+list.MarryCity+'</div>' +
            '<div class="col-50">目前：'+list.MarryGrade+'</div>' +
            '</div>' +
            '<div class="row no-gutter">' +
            '<div class="col-50">'+list.GoAbroadTime+'</div>' +
            '<div class="col-50">目标：'+list.MarryTargetLevel+'</div>' +
            '</div>' +
            '</div>' +
            '<div class="right">' +
            '<p class="red howNum">'+list.Consultants+'位顾问</p>' +
            '<p class="matchSu" data-id="'+list.Consultants+'">匹配成功</p>' +
            '</div>' +
            '</a>' +
            '</li>';
        $('.matchList').append(item);
    })
    $(".matchSu").each(function () {
        var _this = $(this).attr('data-id');
        if(_this == '0'){
            $(this).text('匹配失败');
        }
    })
}

//没有匹配到相关顾问
function DataFailure() {
    $(".contantList").empty();
    $(".Nosearch").remove();
    html = '<div class="Nosearch tac">您暂无匹配顾问<a href="'+m_url+'/study/marryconsultantone/" class="button button-fill nowMatch external">马上匹配</a></div>';
    $(".matchList").append(html);
}