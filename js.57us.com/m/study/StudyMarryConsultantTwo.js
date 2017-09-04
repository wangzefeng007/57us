/**
 * Created by Foliage on 2016/11/24.
 */
$(function () {
    //初始化加载
    ajaxLoad();

    //选择顾问
    $(document).on('click','.Selection',function () {
        var MarryID = GetQueryString('MarryID');
        var ConsultantID = $(this).parent().attr('data-id');
        if(Times > 0){
            window.location = '/study/marryconsultantthree/?MarryID='+MarryID+'&ConsultantID='+ConsultantID+'';
        }else {
            $.toast('您选择的顾问已经超过五个');
        }
    })

    $.init();
})

//ajax提交参数
var ajaxData = {
    'Intention':'MarrySelect', //方法
    'MarryID':GetQueryString('MarryID'), //对应id
    'Page':'1', //页码
}

//定义页码变量
var j = 2;

//定义选择次数
var Times;

//初始化加载方法
function ajaxLoad() {
    $.post('/ajaxstudy/',ajaxData,function (data) {
        if(data.ResultCode == '200'){
            if(data.Count > 6){
                $(".infinite-scroll-preloader").show();
                $.attachInfiniteScroll($('.infinite-scroll'));
            }else {
                $(".infinite-scroll-preloader").hide();
                $(".pullUpLabel").show();
            }
            $(".Count").text(data.Count);
            DataSuccess(data);
            Times = data.Times;
            maxItems = data.Count;
        }else if(data.RecordCount == '101'){
            DataFailure();
        }else {
            $.toast(data.Message);
        }
    },'json')

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
                            item =  '<li>' +
                                '<a href="'+list.Url+'" class="cf">' +
                                '<img src="'+list.Avatar+'" width="96" height="96">' +
                                '<div class="listCont">' +
                                '<div class="tit cf">' +
                                '<p class="name pull-left">'+list.NickName+'<span class="TopTip hidden"><i class="icon iconfont">&#xe60e;</i></span></p>' +
                                '</div>' +
                                '<div class="fun cf">' +
                                '<span><i class="icon iconfont">&#xe647;</i>'+list.City+'</span>' +
                                '<span><i class="icon iconfont">&#xe62f;</i>从业'+list.WorkingAge+'年</span>' +
                                '</div>' +
                                '<div class="tip cf">'+list.Tags+'</div>' +
                                '</div>' +
                                '<div class="ListRight">' +
                                '<span class="loadBar"><em style="width: '+list.scale+'%;"></em></span>' +
                                '<p class="tac">匹配值'+list.scale+'%</p>' +
                                '</div>' +
                                '</a>' +
                                '<div class="Bottomchose cf mt10" data-id="'+list.UserID+'" data-type="'+list.IsChoose+'">' +
                                '<a href="javascript:void(0)" class="button button-fill pull-right Selection">选择该顾问</a>' +
                                '<span class="pull-left">'+list.Choosed+'个人选择了该顾问</span>' +
                                '</div>' +
                                '</li>';
                            $('.contantList').append(item);
                        });
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
        item =  '<li>' +
            '<a href="'+list.Url+'" class="cf">' +
            '<img src="'+list.Avatar+'" width="96" height="96">' +
            '<div class="listCont">' +
            '<div class="tit cf">' +
            '<p class="name pull-left">'+list.NickName+'<span class="TopTip hidden"><i class="icon iconfont">&#xe60e;</i></span></p>' +
            '</div>' +
            '<div class="fun cf">' +
            '<span><i class="icon iconfont">&#xe647;</i>'+list.City+'</span>' +
            '<span><i class="icon iconfont">&#xe62f;</i>从业'+list.WorkingAge+'年</span>' +
            '</div>' +
            '<div class="tip cf">'+list.Tags+'</div>' +
            '</div>' +
            '<div class="ListRight">' +
            '<span class="loadBar"><em style="width: '+list.scale+'%;"></em></span>' +
            '<p class="tac">匹配值'+list.scale+'%</p>' +
            '</div>' +
            '</a>' +
            '<div class="Bottomchose cf mt10" data-id="'+list.UserID+'" data-type="'+list.IsChoose+'">' +
            '<a href="javascript:void(0)" class="button button-fill pull-right Selection">选择该顾问</a>' +
            '<span class="pull-left">'+list.Choosed+'个人选择了该顾问</span>' +
            '</div>' +
            '</li>';
        $('.contantList').append(item);
    });
}

//没有匹配到相关顾问
function DataFailure() {
    $(".contantList").empty();
    $(".Nosearch").remove();
    html = '<div class="Nosearch">暂无帮您匹配到的顾问</div>';
    $(".ConsultantList").append(html);
}