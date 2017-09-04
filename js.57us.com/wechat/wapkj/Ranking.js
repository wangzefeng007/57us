/**
 * Created by Foliage on 2016/12/13.
 */
$(function () {
    //初始化加载
    ajaxLoad();
})

//定义ajax提交变量
var ajaxData = {
    'Page': '1',
}

//定义排名1-3 class方法只执行一次
var valid = true;

//定义排名序号
var j = '0';

//初始化加载方法
function ajaxLoad() {
    var dropload = $('.inner').dropload({
        domUp: {
            domClass: 'dropload-up',
            domRefresh: '<div class="dropload-refresh">↓下拉刷新</div>',
            domUpdate: '<div class="dropload-update">↑释放更新</div>',
            domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>'
        },
        domDown: {
            domClass: 'dropload-down',
            domRefresh: '<div class="dropload-refresh">↑上拉加载更多</div>',
            domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>',
            domNoData: '<div class="dropload-noData">暂无数据</div>'
        },
        loadUpFn: function (me) {
            window.location.reload();
        },
        loadDownFn: function (me) {
            $.ajax({
                type: "GET",	//提交类型
                dataType: 'json',
                url: '/bargain/getranking/',
                data: ajaxData,
                success: function (data) {
                    ajaxData.Page = Number(ajaxData.Page) + Number('1');
                    if (data.ResultCode == 200) {
                        if (data.Data.length > 0) {
                            var item = '';
                            $.each(data.Data, function (i, list) {
                                item += '<li>'+
                                    '<a href="javascript:void(0)">'+
                                    '<div class="Lnum">'+
                                    '<span class="Pnum">'+ ++j +'</span>'+
                                    '</div>'+
                                    '<div class="rankM">'+
                                    '<span class="img"><img src="'+list.HeadImgUrl+'"></span>'+
                                    '<span class="name">'+list.Nickname+'</span>'+
                                    '</div>'+
                                    '<div class="rankRight">&yen;'+list.Amount+'</div>'+
                                    '</a>'+
                                    '</li>';
                            });
                            $('.lists').append(item);
                            onceMethod();
                            // 每次数据加载完，必须重置
                            dropload.resetload();
                        } else {
                            dropload.lock();
                            $('.dropload-down').hide();
                            $('.lists').append('<div class="dropload-noData">暂无数据</div>');
                        }
                    } else {
                        alert('加载出错，请刷新页面！');
                        // 即使加载出错，也得重置
                        dropload.resetload();
                    }
                }
            });
        }
    });
}

//排名1-3名增class
function onceMethod() {
    if(valid){
        $(".lists .Lnum").eq(0).addClass('pmNum');
        $(".lists .Lnum").eq(0).find('span').attr('class','num');
        $(".lists .Lnum").eq(1).addClass('pmNum');
        $(".lists .Lnum").eq(1).find('span').attr('class','num');
        $(".lists .Lnum").eq(2).addClass('pmNum');
        $(".lists .Lnum").eq(2).find('span').attr('class','num');
        valid = false;
    }else return;
}