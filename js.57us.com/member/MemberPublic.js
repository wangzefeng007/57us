/**
 * Created by Foliage on 2017/2/8.
 */
$(function () {
    //左侧菜单点击事件
    $("#leftmenu  li .firstA").on('click',function () {
        var _thisDom = $(this).parents('li');
        if(_thisDom.is(".on")){
            _thisDom.removeClass('on');
        }else {
            _thisDom.addClass('on');
        }
    })
})