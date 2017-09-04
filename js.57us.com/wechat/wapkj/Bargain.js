/**
 * Created by Foliage on 2016/12/9.
 */
$(function () {
    $('#selfKanjia').on('click', function () {
        var ajaxData = {
            'UserID': $(this).attr('data-id'),
            'Type': $(this).attr('data-type'),
        }
        $.post('/bargain/bargainoperate/', ajaxData, function (data) {
            if (data.ResultCode == "200") {
                layer.open({
                    content: data.Message
                    , skin: 'msg'
                    , time: 1 //2秒后自动关闭
                });
                setTimeout(function () {
                    if(data.Url){
                        window.location = data.Url;
                    }
                    else {
                        window.location.reload();
                    }
                }, 500);
            } else {
                layer.open({
                    content: data.Message
                    , skin: 'msg'
                    , time: 2 //2秒后自动关闭
                });
            }
        }, 'json');
    })

    var ajaxData2 = {
        'UserID': $("#UserID").val(),
        'Page': '1',
    }

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
                url: '/bargain/bargainsuperior/',
                data: ajaxData2,
                success: function (data) {
                    ajaxData2.Page = Number(ajaxData2.Page) + Number('1');
                    if (data.ResultCode == 200) {
                        if (data.Data.length > 0) {
                            var item = '';
                            $.each(data.Data, function (i, list) {
                                item += '<li><img src="' + list.HeadImgUrl + '"/>' +
                                    '<div class="cont">' + list.Nickname + '帮忙砍了<br>' + list.BargainTime + '</div>' +
                                    '<span class="price">' + list.BargainAmount + '元</span>' +
                                    '</li>';
                            });
                            $('.lists').append(item);
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
})