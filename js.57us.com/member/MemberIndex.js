/**
 * Created by Foliage on 2017/2/8.
 */
$(function () {
    //取消收藏
    $(".cancelColl").on('click',function () {
        var ajaxData = {
            'Intention': 'CancelColl', //取消收藏方法
            'ID':$(this).attr('data-id'), //对应的id
        }
        $.post('/userajax.html',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                layer.msg('取消成功收藏');
                setTimeout(function(){
                    window.location.reload();
                },1000);
            }else {
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location.reload();
                },1000);
            }
        },'json');
    })
})