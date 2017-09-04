/**
 * Created by Foliage on 2016/11/24.
 */
$(function () {
    //初始化加载
    ajaxLoad();

    //弹出筛选项目
    $(document).on('click','.checkBtn',function(){
        $(".contanCheckBox").addClass("active");
        var _type = $(".contanCheckBox").attr('data-type');
        //获取城市列表
        if(_type == '0'){
            $.post('/study/getcity/',function (data) {
                if(data.ResultCode == '200'){
                    var _City = data.DataCity;
                    $("#city-picker").picker({
                        toolbarTemplate: '<header class="bar bar-nav diyCity">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">所在地区</h1>\</header>',
                        cols: [
                            {
                                textAlign: 'center',
                                values: _City,
                                cssClass: 'picker-items-col-normal'
                            }
                        ]
                    });
                    $(".contanCheckBox").attr('data-type','1');
                }else {
                    $.toast(data.Message);
                }
            },'json');
        }
    });
    //城市选择关闭，ajax提交数据注入
    $(document).on('click','.close-picker',function () {
        ajaxData.City = $('#city-picker').val();
    })

    //关掉选项
    $(".contanCheckBox .close,#cancel").click(function(){
        $(".contanCheckBox").removeClass("active");
    });
    //工作年限筛选
    $(".checkBox a").click(function(){
        $(this).addClass("on").siblings().removeClass("on");
        ajaxData.Term = $(this).attr('data-id');
    });

    //搜索
    $(document).on('click','.searchBtn',function(){
        $(".diySearch").addClass("active");
        document.getElementById("search").focus();
    });
    $(".diySearch .close").click(function(){
        $(".diySearch").removeClass("active")
    })

    //点击筛选确定
    $(document).on('click','#sure',function () {
        $(".contanCheckBox").removeClass("active");
        j = 2;
        ajaxData.Page = '1';
        $(".content").scrollTop(0);
        ajaxLoad();
    })

    //搜索
    $(".search-input form").submit(function() {
        ajaxData.Keyword = $('#search').val();
        j = 2;
        ajaxData.Page = '1';
        $(".content").scrollTop(0);
        ajaxLoad();
        return false;
    })

    $.init();
})

//定义页码变量
var j = 2;

//计算全部可以加载的条数
var maxItems = '';

var ajaxData = {
    'Intention':'GetConsultantList', //方法
    'City':'All', //城市
    'Term':'All', //从业年限
    'Page':'1', //页码
    'Keyword':'', //关键字
}

function ajaxLoad() {
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/ajaxstudy/",
        data: ajaxData,
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                if(data.RecordCount > 6){
                    $(".infinite-scroll-preloader").show();
                    $.attachInfiniteScroll($('.infinite-scroll'));
                }else {
                    $(".infinite-scroll-preloader").hide();
                }
                DataSuccess(data);
                maxItems = data.RecordCount;
            }else if(data.ResultCode == "100"){
                layer.msg('加载出错，请刷新页面重新选择!');
            }else if(data.ResultCode == "101"){
                DataFailure(0);
            }else if(data.ResultCode == "102"){     //搜索有内容
                if(data.RecordCount > 6){
                    $(".infinite-scroll-preloader").show();
                    $.attachInfiniteScroll($('.infinite-scroll'));
                }else {
                    $(".infinite-scroll-preloader").hide();
                    $(".pullUpLabel").show();
                }
                DataSuccess(data);
                maxItems = data.RecordCount;
            }else if(data.ResultCode == "103"){ //搜索无内容
                DataFailure(1);
            }
        }
    });

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
                            item =  '<li data-id="'+list.StudyId+'" >'+
                                '<a href="'+list.StudyUrl+'" class="external">'+
                                '<img src="'+list.StudyImg+'" width="96" height="96">'+
                                '<div class="listCont">'+
                                '<div class="tit cf">'+
                                '<p class="name pull-left">'+list.StudyName+'<span class="TopTip hidden"><i class="icon iconfont">&#xe60e;</i></span></p>'+
                                '</div>'+
                                '<div class="fun cf">'+
                                '<span><i class="icon iconfont">&#xe647;</i>'+list.StudyPosition+'</span>'+
                                '<span><i class="icon iconfont">&#xe62f;</i>从业'+list.StudyTerm+'年</span>'+
                                '</div>'+
                                '<div class="tip cf">'+list.StudyTag+'</div>'+
                                '</div>'+
                                '</a>'+
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

    //下拉刷新页面
    $(".page").on("pageInit", function(e, id, page) {
        var $content = $(page).find(".content").on('refresh', function(e) {
            window.location.reload();
            $.pullToRefreshDone($content);
        });
    });
}

//页面初始化，无搜索有数据方法
function DataSuccess(data) {
    $(".Nosearch").remove();
    $(".contantList").empty();
    var item;
    $.each(data.Data, function(i, list) {
        item =  '<li data-id="'+list.StudyId+'" >'+
            '<a href="'+list.StudyUrl+'" class="external">'+
            '<img src="'+list.StudyImg+'" width="96" height="96">'+
            '<div class="listCont">'+
            '<div class="tit cf">'+
            '<p class="name pull-left">'+list.StudyName+'<span class="TopTip hidden"><i class="icon iconfont">&#xe60e;</i></span></p>'+
            '</div>'+
            '<div class="fun cf">'+
            '<span><i class="icon iconfont">&#xe647;</i>'+list.StudyPosition+'</span>'+
            '<span><i class="icon iconfont">&#xe62f;</i>从业'+list.StudyTerm+'年</span>'+
            '</div>'+
            '<div class="tip cf">'+list.StudyTag+'</div>'+
            '</div>'+
            '</a>'+
            '</li>';
        $('.contantList').append(item);
    });
}

//页面，无内容
function DataFailure(type) {
    $(".Nosearch").remove();
    $(".contantList").empty();
    $(".pullUpLabel").hide();
    $(".infinite-scroll-preloader").hide();
    if(type == 0){
        html = '<div class="Nosearch tac"><p><i class="icon iconfont sadIcon">&#xe615;</i></p><p class="text">筛选无内容：暂时没有找到符合条件的顾问</p>' +
            '<a href="javascript:void(0)" class="button button-big external checkBtn">重新筛选</a></div>';
        $('.ConsultantList').append(html);
    }else if(type == 1){
        html = '<div class="Nosearch tac"><p><i class="icon iconfont sadIcon">&#xe615;</i></p><p class="text">搜索无内容：暂时没有找到“'+ajaxData.Keyword+'”的相关的顾问</p>' +
            '<a href="javascript:void(0)" class="button button-big external searchBtn">重新搜索</a></div>';
        $('.ConsultantList').append(html);
    }
}