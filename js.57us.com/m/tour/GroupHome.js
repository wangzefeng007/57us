/**
 * Created by Foliage on 2016/10/27.
 */
$(function() {
    //初始化加载数据
    ajaxSubmit();

    //弹出切换
    $(".diyBottom .tab-item").each(function() {
        $(this).on('click', function() {
            var _thisId = $(this).attr('date-id');
            var num = $(this).index();
            $(this).addClass("active").siblings().removeClass("active");
            if(_thisId == 'PlayFoth'){
                PlayFoth(num);
            }else if(_thisId == 'Howday'){
                Howday(num);
            }else if(_thisId == 'FeaPlay'){
                FeaPlay(num);
            }else if(_thisId == 'Ranking'){
                Ranking(num);
            }
        })
    })

    //弹出关闭
    $(".close").on('click',function () {
        $(this).parents('.tabBox').removeClass('on');
        var _thisId = $(this).parents('.tabBox').attr('id');
        if(_thisId == 'PlayFoth'){
            $("#StartCity,#EndCity,#WayCity").text('');
        }
    })

    //筛选点击确实
    $(document).on('click','.SureBtn',function () {
        j = 2;
        ajaxSubmit();
    })

    $.init();
})

//线路玩法
function PlayFoth(num) {
    var tabBox = $(".tabMain .tabBox").eq(num);
    if(tabBox.is('.on')){
        tabBox.removeClass('on');
        $("#StartCity,#EndCity,#WayCity").text('');
    }else {
        $(".tabMain .tabBox").removeClass('on');
        tabBox.addClass('on');
        var tabBoxCityl = $("#PlayFoth .tabBoxCityl");
        var PlayFoth = $("#PlayFoth .swiper-city ul");
        var Letter = $("#PlayFoth .swiper-letter li");
        //当点击退出后，重新点击进来初始化
        tabBoxCityl.find('a').removeClass('on');
        tabBoxCityl.find('a').eq(0).addClass('on');
        $("#PlayFoth .tabBoxCityM").hide();
        PlayFoth.scrollTop(0);
        StartCity(PlayFoth);
        //出发城市，结束城市，途径城市热门点击
        tabBoxCityl.find('a').on('click',function () {
            var _thidId = $(this).attr('data-id');
            tabBoxCityl.find('a').removeClass('on');
            $(this).addClass('on');

            if(_thidId == 'StartCity'){
                $("#PlayFoth .tabBoxCityM").hide();
                StartCity(PlayFoth);
            }else if(_thidId == 'EndCity'){
                $("#PlayFoth .tabBoxCityM").hide();
                EndCity(PlayFoth);
            }else if(_thidId == 'WayCity'){
                $("#PlayFoth .tabBoxCityM").show();
                WayCity(PlayFoth);
            }
        })
        //途径城市A-Z点击
        Letter.on('click',function () {
            Letter.removeClass('on');
            $(this).addClass('on');
            WayCity(PlayFoth);
        })

        //清空筛选
        $(".Clear").on('click',function () {
            PlayFoth.find('label').removeClass('on');
            $("#StartCity,#EndCity,#WayCity").text('');
        })

        //点击确实
        $('#PlayFoth .SureBtn').on('click',function () {
            //出发城市注入
            if($('#StartCity').text() != ''){
                $("#ajaxStartCity").text($('#StartCity').text());
            }else {
                $("#ajaxStartCity").text('All');
            }
            //结束城市注入
            if($('#EndCity').text() != ''){
                $("#ajaxEndCity").text($('#EndCity').text());
            }else {
                $("#ajaxEndCity").text('All');
            }
            //途径城市注入
            if($('#WayCity').text() != ''){
                $("#ajaxWayCity").html($('#WayCity').html());
            }else {
                $("#ajaxWayCity").html('<span data-id="All">All</span>');
            }
            //关闭当前弹出层
            $(this).parents('.tabBox').removeClass('on');
        })

    }
}

//出发城市方法
function StartCity(PlayFoth) {
    $.get('/Templates/M/data/Home/AreaOut.json', function(data) {
        PlayFoth.empty();
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label class="choseList" data-value="'+list.AeraID+'">' +
                '<p class="pull-left">'+list.name+'</p>' +
                '<p class="pull-right checkBox"><i class="icon iconfont">&#xe63d;</i></p>' +
                '</label>' +
                '</li>';
            PlayFoth.append(item);
        });

        //缓存区注入
        var _StartCity = $("#StartCity").text();
        PlayFoth.find('label').each(function () {
            var a = $(this).attr('data-value');
            if(a == _StartCity){
                $(this).addClass('on');
            }
        })

        var StartCity = $("#ajaxStartCity").text();
        //根据之前选择的条件进行标签重新赋值
        PlayFoth.find('label').each(function () {
            var a = $(this).attr('data-value');
            if(a == StartCity){
                $(this).addClass('on');
            }
        })

        //内容点击增加class事件
        PlayFoth.find('label').on('click',function () {
            if($(this).is('.on')){
                $(this).removeClass('on');
                $("#StartCity").text('');
            }else {
                PlayFoth.find('label').removeClass('on');
                $(this).addClass('on');
                $("#StartCity").text($(this).attr('data-value'));
            }
        })
    }, 'json');
}

//结束城市
function EndCity(PlayFoth) {
    $.get('/Templates/M/data/Home/AreaEnter.json', function(data) {
        PlayFoth.empty();
        var item;
        $.each(data, function(i, list) {
            item = '<li>' +
                '<label class="choseList" data-value="'+list.AeraID+'">' +
                '<p class="pull-left">'+list.name+'</p>' +
                '<p class="pull-right checkBox"><i class="icon iconfont">&#xe63d;</i></p>' +
                '</label>' +
                '</li>';
            PlayFoth.append(item);
        });

        //缓存区注入
        var _EndCity = $("#EndCity").text();
        PlayFoth.find('label').each(function () {
            var a = $(this).attr('data-value');
            if(a == _EndCity){
                $(this).addClass('on');
            }
        })

        var EndCity = $("#ajaxEndCity").text();
        //根据之前选择的条件进行标签重新赋值
        PlayFoth.find('label').each(function () {
            var a = $(this).attr('data-value');
            if(a == EndCity){
                $(this).addClass('on');
            }
        })

        //内容点击增加class事件
        PlayFoth.find('label').on('click',function () {
            if($(this).is('.on')){
                $(this).removeClass('on');
                $("#EndCity").text('');
            }else {
                PlayFoth.find('label').removeClass('on');
                $(this).addClass('on');
                $("#EndCity").text($(this).attr('data-value'));
            }
        })
    }, 'json');
}

//途径城市
function WayCity(PlayFoth) {
    var _thisId = $("#PlayFoth .swiper-letter .on").attr('data-value');
    $.get('/Templates/M/data/Home/Citys/'+_thisId+'.json', function(data) {
        if(data != null){
            PlayFoth.empty();
            var item;
            $.each(data, function(i, list) {
                item = '<li>' +
                    '<label class="choseList" data-value="'+list.name+'">' +
                    '<p class="pull-left">'+list.name+'</p>' +
                    '<p class="pull-right checkBox"><i class="icon iconfont">&#xe63d;</i></p>' +
                    '</label>' +
                    '</li>';
                PlayFoth.append(item);
            });

            //缓存区数据注入
            $("#WayCity span").each(function () {
                var _WayCity = $(this).text();
                PlayFoth.find('label').each(function () {
                    var a = $(this).attr('data-value');
                    if(a == _WayCity){
                        $(this).addClass('on');
                    }
                })
            })

            //根据之前选择的条件进行标签重新赋值
            $("#ajaxWayCity span").each(function () {
                var _WayCity = $(this).text();
                PlayFoth.find('label').each(function () {
                    var a = $(this).attr('data-value');
                    if(a == _WayCity){
                        $(this).addClass('on');
                    }
                })
            })

            //内容点击增加class事件
            PlayFoth.find('label').on('click',function () {
                if($(this).is('.on')){
                    $(this).removeClass('on');
                    var b = $(this).attr('data-value');
                    $('#WayCity span[data-id="' + b + '"]').remove();
                }else {
                    $(this).addClass('on');
                    var a = $(this).attr('data-value');
                    if($('#WayCity span[data-id="' + a + '"]').length === 0) {
                        $("#WayCity").append('<span data-id="'+a+'">'+a+'</span>');
                    }
                }
            })
        }
    }, 'json');
}

//时间天数
function Howday(num) {
    var tabBox = $(".tabMain .tabBox").eq(num);
    if(tabBox.is('.on')){
        tabBox.removeClass('on');
    }else {
        $(".tabMain .tabBox").removeClass('on');
        tabBox.addClass('on');
        var HowdayA = $("#Howday .tabBoxBodyM").eq(0);
        var HowdayB = $("#Howday .tabBoxBodyM").eq(1);
        //行程天数
        $.get('/Templates/M/data/Home/Days.json', function(data) {
            HowdayA.empty();
            HowdayA.append('<span class="All" data-value="All">不限</span>');
            var item;
            $.each(data, function(i, list) {
                item = '<span data-value="'+list.date+'">'+list.name+'</span>';
                HowdayA.append(item);
            });
            var Stroke = $("#ajaxStroke").text();
            //根据之前选择的条件进行标签重新赋值
            HowdayA.find('span').each(function () {
                var a = $(this).attr('data-value');
                if(a == Stroke){
                    $(this).addClass('on');
                }
            })
            //内容点击增加class事件
            HowdayA.find('span').on('click',function () {
                if($(this).is('.on')){
                    $(this).removeClass('on');
                    HowdayA.find('span').eq(0).addClass('on');
                }else {
                    HowdayA.find('span').removeClass('on');
                    $(this).addClass('on');
                }
            })
        }, 'json');

        //出发时间
        HowdayB.empty();
        HowdayB.append(StartDate());

        //根据之前选择的条件进行标签重新赋值
        $("#ajaxaStartDate span").each(function () {
            var a = $(this).text();
            HowdayB.find('span').each(function () {
                var b = $(this).attr('data-value');
                if(b == a){
                    $(this).addClass('on');
                }
            })
        })

        //点击不限
        HowdayB.find("[data-value='All']").on('click',function () {
            HowdayB.find('span').removeClass('on');
            $(this).addClass('on');
        })

        //点击其它出发时间
        HowdayB.find('span').on('click',function () {
            if($(this).is('.on')){
                $(this).removeClass('on')
                var num =  HowdayB.find('.on').length;
                if(num == 0){
                    HowdayB.find('span').eq(0).addClass('on');
                }
            }else {
                $(this).addClass('on');
                if($(this).attr('data-value') != 'All'){
                    HowdayB.find('span').eq(0).removeClass('on');
                }
            }
        })

        //点击清除，清空所有选择
        tabBox.find('.Clear').on('click',function () {
            HowdayA.find('span').removeClass('on');
            HowdayA.find('span').eq(0).addClass('on');
            HowdayB.find('span').removeClass('on');
            HowdayB.find('span').eq(0).addClass('on');
        })

        //点击确定，提交数据注入后ajax加载页面
        tabBox.find('.SureBtn').on('click',function () {
            //行程天数ajax数据注入
            var Stroke = HowdayA.find('.on').attr('data-value');
            $("#ajaxStroke").text(Stroke);

            //出发时间ajax数据注入
            var StartDate = [];
            HowdayB.find('.on').each(function () {
                StartDate.push($(this).attr("data-value"));
            })
            $("#ajaxaStartDate").empty();
            $.each(StartDate,function(n,list){
                    $("#ajaxaStartDate").append('<span>'+list+'</span>');
                }
            );
            $(this).parents('.tabBox').removeClass('on');
        })
    }
}

//特色筛选方法
function FeaPlay(num) {
    var tabBox = $(".tabMain .tabBox").eq(num);
    //显示出特色筛选
    if(tabBox.is('.on')){
        tabBox.removeClass('on');
    }else {
        $(".tabMain .tabBox").removeClass('on');
        tabBox.addClass('on');
        $.get('/Templates/M/data/Home/Subject.json', function(data) {
            $('#FeaPlay .tabBoxBodyM').empty();
            $("#FeaPlay .tabBoxBodyM").append('<span class="All" data-value="All">不限</span>');
            var item;
            $.each(data, function(i, list) {
                item = '<span data-value="'+list.id+'">'+list.name+'</span>';
                $("#FeaPlay .tabBoxBodyM").append(item);
            });

            //根据之前选择的条件进行标签重新赋值
            $("#ajaxTheme span").each(function () {
                var a = $(this).text();
                $("#FeaPlay span").each(function () {
                    var b = $(this).attr('data-value');
                    if(b == a){
                        $(this).addClass('on');
                    }
                })
            })

            //点击不限时，清空已选条件
            $("#FeaPlay [data-value='All']").on('click',function () {
                $('#FeaPlay span').removeClass('on');
                $(this).addClass('on');
            })

            //内容点击增加class事件
            $('#FeaPlay span').on('click',function () {
                if($(this).is('.on')){
                    $(this).removeClass('on')
                    var num = $("#FeaPlay .on").length;
                    if(num == 0){
                        $('#FeaPlay span').eq(0).addClass('on');
                    }
                }else {
                    $(this).addClass('on');
                    if($(this).attr('data-value') != 'All'){
                        $('#FeaPlay span').eq(0).removeClass('on');
                    }
                }
            })
        }, 'json');

        //点击清除，清空所有选择
        tabBox.find('.Clear').on('click',function () {
            $("#FeaPlay span").removeClass('on');
            $("#FeaPlay span").eq(0).addClass('on');
        })

        //点击确定，提交数据注入后ajax加载页面
        tabBox.find('.SureBtn').on('click',function () {
            var FeaPlay = []
            $('#FeaPlay .tabBoxBodyM .on').each(function () {
                FeaPlay.push($(this).attr("data-value"));
            })
            $("#ajaxTheme").empty();
            $.each(FeaPlay,function(n,list){
                    $("#ajaxTheme").append('<span>'+list+'</span>');
                }
            );
            $(this).parents('.tabBox').removeClass('on');
        })
    }
}

//综合排序方法
function Ranking(num) {
    var tabBox = $(".tabMain .tabBox").eq(num);
    if(tabBox.is('.on')){
        tabBox.removeClass('on');
    }else {
        $(".tabMain .tabBox").removeClass('on');
        tabBox.addClass('on');
        //排序条件注入
        tabBox.find('.tabBoxPx').empty();
        html = '<li data-value="Default">综合排序<i class="icon iconfont">&#xe63d;</i></li>' +
            '<li data-value="PicerAsce">价格从低到高<i class="icon iconfont">&#xe63d;</i></li>' +
            '<li data-value="PicerDown">价格从高到低<i class="icon iconfont">&#xe63d;</i></li>' +
            '<li data-value="SalesAsce">销量从低到高<i class="icon iconfont">&#xe63d;</i></li>' +
            '<li data-value="SalesDown">销量从高到低<i class="icon iconfont">&#xe63d;</i></li>' ;

        tabBox.find('.tabBoxPx').append(html);

        //排序条件，如果已经存在选择，重新赋值
        var Sort = $("#ajaxSort").text();
        $("#Ranking li").each(function () {
            var a = $(this).attr('data-value');
            if(a == Sort){
                $(this).addClass('on');
            }
        })

        //综合排序选择
        var Ranking = $("#Ranking");
        Ranking.find('.tabBoxPx li').on('click',function(){
            $(this).addClass("on").siblings().removeClass("on");
        })

        //综合排序清空选择
        Ranking.find('.Clear').on('click',function () {
            Ranking.find('.tabBoxPx li').removeClass('on');
            Ranking.find('.tabBoxPx li').eq(0).addClass('on');
        })

        //综合排序确定
        Ranking.find('.SureBtn').on('click',function () {
            var Sort = Ranking.find('.tabBoxPx .on').attr('data-value');
            $("#ajaxSort").text(Sort);
            $(this).parents('.tabBox').removeClass('on');
        })
    }
}

//定义页码变量
var j = 2;

//页面初始化的使用的ajax
function ajaxSubmit(Page) {
    var WayCity = [];
    $('#ajaxWayCity span').each(function () {
        WayCity.push($(this).attr("data-id"));
    })
    var StartDate = [];
    $('#ajaxaStartDate span').each(function () {
        StartDate.push($(this).text());
    })
    var Theme = [];
    $('#ajaxTheme span').each(function () {
        Theme.push($(this).text());
    })
    var Keyword = GetQueryString('K');
    ajaxData = {
        'Intention': 'GroupHome',
        'StartCity': $("#ajaxStartCity").text(),	//出发城市
        'WayCity':WayCity,	//途径城市 数组
        'Stroke':$("#ajaxStroke").text(),	//行程天数  如果提交是15-All 表示15天以上
        'StartDate':StartDate,	//出发时间
        'Theme':Theme,	//特色主题 数组
        'Sort':$("#ajaxSort").text(),	//排序
        'Page':Page,
        'Keyword':Keyword,	//关键字 可能是空
    }

    $.ajax({
        type: "post",
        dataType: "json",
        url: "",
        data: ajaxData,
        beforeSend: function () { //加载过程效果
            // $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                if(data.RecordCount > 6){
                    $(".infinite-scroll-preloader").show();
                }else {
                    $(".infinite-scroll-preloader").hide();
                }
                DataSuccess(data)
                maxItems = data.RecordCount;
            }else if(data.ResultCode == "100"){
                layer.msg('加载出错，请刷新页面重新选择!');
            }else if(data.ResultCode == "101"){
                $(".infinite-scroll-preloader").hide();
                DataFailure(0);
            }else if(data.ResultCode == "102"){     //搜索有内容
                if(data.RecordCount > 6){
                    $(".infinite-scroll-preloader").show();
                }else {
                    $(".infinite-scroll-preloader").show();
                }
                DataSuccess(data);
            }else if(data.ResultCode == "103"){ //搜索无内容
                $(".infinite-scroll-preloader").hide();
                DataFailure(1);
            }
        },
        complete: function () { //加载完成提示
            // $("#loading").hide();
        }
    });

    //计算全部可以加载的条数
    var maxItems = '';
    $(document).on("pageInit", function() {
        var loading = false;
        $("#page").on('infinite', function() {
            // $(document).on('infinite','#page',function() {
            // 如果正在加载，则退出
            if (loading) return;
            // 设置flag
            loading = true;
            ajaxData = {
                'Intention': 'GroupHome',
                'StartCity': $("#ajaxStartCity").text(),	//出发城市
                'WayCity':WayCity,	//途径城市 数组
                'Stroke':$("#ajaxStroke").text(),	//行程天数  如果提交是15-All 表示15天以上
                'StartDate':StartDate,	//出发时间
                'Theme':Theme,	//特色主题 数组
                'Sort':$("#ajaxSort").text(),	//排序
                'Page':j,
                'Keyword':Keyword,	//关键字 可能是空
            }
            $.post('',ajaxData, function(data) {
                // 重置加载flag
                loading = false;
                if(data.ResultCode == '200' || data.ResultCode == '102') {
                    if(data.Data.length) {
                        var item;
                        $.each(data.Data, function(i, list) {
                            item = '<li data-id="'+list.TourId+'">' +
                                '<a href="'+list.TourUrl+'">' +
                                '<p class="img"><img src="'+list.TourImg+'" /></p>' +
                                '<div class="ListRight">' +
                                '<p class="tit">'+list.TourName+'</p>' +
                                '<p class="tipspan">'+list.TourTag+'</p>' +
                                '<p class="ListBottom">' +
                                '<span class="pull-left">行程天数：<i class="green">'+list.TourStroke+'天</i></span>' +
                                '<span class="pull-right price"><i>¥'+list.TourPicre+'</i>/人起</span>' +
                                '</p>' +
                                '</div>' +
                                '</a>' +
                                '</li>';
                            $('.PlayList').append(item);
                        });
                        //计算目前所有li的条数
                        var lastIndex = $('.PlayList li').length;
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
                        $('.infinite-scroll-preloader').hide();
                        $(".pullUpLabel").show();
                        return;
                    }
                }
                //sui上拉刷新重置
                $.refreshScroller();
            }, 'json')
        });
    })

    //下拉刷新页面
    $(document).one("pageInit", ".page", function(e, id, page) {
        var $content = $(page).find(".content").on('refresh', function(e) {
            // 模拟2s的加载过程
            window.location.reload();
            $.pullToRefreshDone($content);
        });
    });
}

//页面初始化，无搜索有数据方法
function DataSuccess(data) {
    $(".payTipBox").remove();
    $(".pullUpLabel").hide();
    $(".PlayList").empty();
    $.attachInfiniteScroll($('.infinite-scroll'));
    $('.infinite-scroll-preloader').show();
    $.initPullToRefresh('.content');
    var item;
    $.each(data.Data, function(i, list) {
        item = '<li data-id="'+list.TourId+'">' +
            '<a href="'+list.TourUrl+'">' +
            '<p class="img"><img src="'+list.TourImg+'" /></p>' +
            '<div class="ListRight">' +
            '<p class="tit">'+list.TourName+'</p>' +
            '<p class="tipspan">'+list.TourTag+'</p>' +
            '<p class="ListBottom">' +
            '<span class="pull-left">行程天数：<i class="green">'+list.TourStroke+'天</i></span>' +
            '<span class="pull-right price"><i>¥'+list.TourPicre+'</i>/人起</span>' +
            '</p>' +
            '</div>' +
            '</a>' +
            '</li>';
        $('.PlayList').append(item);
    });
}

// 页面，无内容
function DataFailure(type) {
    $(".PlayList").empty();
    $(".payTipBox").remove();
    $(".pullUpLabel").hide();
    $.destroyPullToRefresh('.content');
    if(type == 0){
        html = '<div class="payTipBox"  style="margin-top:4.2rem">' +
            '<p class="tac cf"><i class="icon iconfont sadIcon">&#xe615;</i></p>' +
            '<div class="payContText tac mt10">' +
            '<p class="tit">暂时没有符合条件的产品</p>' +
            '<p class="mt10 c9">删除筛选条件或扩大搜索范围，您一<br>定会找到合适您的产品。</p>' +
            '</div>' +
            '<div class="payResultBtn tac mt10">' +
            '<a href="/group/home/" class="button button-big external">重新筛选</a>' +
            '</div>' +
            '</div>';
        $('.list-block').parent().append(html);
    }else if(type == 1){
        html = '<div class="payTipBox" style="margin-top:4.2rem">' +
            '<p class="tac cf"><i class="icon iconfont sadIcon">&#xe615;</i></p>' +
            '<div class="payContText tac mt10">' +
            '<p class="tit">暂时没有搜索结果</p>' +
            '<p class="mt10 c9">删除筛选条件或扩大搜索范围，您一<br>定会找到合适您的产品。</p>' +
            '</div>' +
            '<div class="payResultBtn tac mt10">' +
            '<a href="/tour/search" class="button button-big external">重新搜索</a>' +
            '</div>' +
            '</div>';
        $('.list-block').parent().append(html);
    }
}