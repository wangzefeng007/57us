/**
 * Created by Foliage on 2017/3/2.
 */
$(function () {
    $("body").addClass('index');
    //搜索回车提交
    $("#indexSearch").submit(function () {
        $("#searchBtn").trigger('click');
        return false
    });

    //搜索点击提交
    $("#searchBtn").on('click',function () {
        var $val = $("#searchVal").val();
        location.href="/house/lists/?s="+$val;
    });

});