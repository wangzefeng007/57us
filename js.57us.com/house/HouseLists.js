/**
 * Created by Foliage on 2017/3/2.
 */
$(document).ready(function () {
    //url关键字读取
    $("#keyword").val(GetQueryString('s'));
    //多选
    $('.cbt').inputbox();

    //价格滑块
    $("#range_1").ionRangeSlider({
        min: 500,
        max: 15000,
        from: 500,
        to: 3000,
        type: 'double',
        step: 500,
        postfix: "$",
        prettify: false,
    });

    //月份滑块
    $("#range_2").ionRangeSlider({
        min: 0,
        max: 12,
        from: 3,
        step: 1,
        postfix: "月",
        prettify: false,
    });

    $("#formList").submit(function () {
        loadPage('1');
        return false
    });

});

function loadPage(page) {
    console.log(page);
}