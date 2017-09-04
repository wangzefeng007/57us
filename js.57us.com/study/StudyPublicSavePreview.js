/**
 * Created by Foliage on 2016/11/14.
 */
$(function () {
    var _type = $("#RedirectUrl").attr('data-type');
    var _url = $("#RedirectUrl").val();
    if(_type == '0'){
        setTimeout(function(){
            window.open(_url);
            $("#RedirectUrl").attr('data-type','1');
        },1000);
    }
})