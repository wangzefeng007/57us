/**
 * Created by Foliage on 2016/8/8.
 */
$(function () {
    $('.vote').click(function () {
        var id = $(this).attr('data-id');
        var ballot = $(this).prevAll('.ballot').text();
        $.ajax({
            type: "post",
            url: '/ajaxwww.html',
            data: {
                'Intention': 'OperateCollection',
                'id': id,
            },
            dataType: "json",
            error: function() {
                layer.msg('网络出错！');
            },
            success: function(data) {
                if(data.ResultCode == '200') {
                    ballot.html('<b>'+ data.ballot+ '票<b/>');
                }else {
                    layer.msg(data.Message);
                }
            }
        });
    })
})