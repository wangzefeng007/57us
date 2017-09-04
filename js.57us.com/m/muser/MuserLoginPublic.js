/**
 * Created by Foliage on 2017/2/28.
 */
$(document).ready(function () {
    //焦点移入，移除错误提示
    $("input[type='text'],input[type='password']").mouseup(function() {
        $(this).parents('.loginCont').find('.erroTip').hide();
    });

    //验证码切换
    $(".click_code").on('click',function() {
        this.src = '/code/pic.jpg?' + Math.random();
    });
});

/**
 * 错误提示方法
 *
 * @param id 操作对应的dom
 * @param message 错误操作文字
 */
function errorHint(id, message) {
    var $errorDom = $(""+id+"").parents('.loginCont');
    $errorDom.find('.erroTip').show();
    $errorDom.find('.erroIfno').text(message);
    setTimeout(function(){
        $errorDom.find('.erroTip').hide();
    },6000);
}