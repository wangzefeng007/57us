/**
 * Created by Foliage on 2016/12/1.
 */
$(function () {
    $("#Region li").on('click',function () {
        $("#Region li").removeClass('on');
        $("#RegionAll").removeClass('on');
        $(this).addClass('on');
        var _type = $(this).attr('data-type');
        if(_type == '1'){
            $(".city").hide();
            $(".city").eq(0).show();
        }else if(_type == '2'){
            $(".city").hide();
            $(".city").eq(1).show();
        }else if(_type == '3'){
            $(".city").hide();
            $(".city").eq(2).show();
        }else if(_type == '4'){
            $(".city").hide();
            $(".city").eq(3).show();
        }else if(_type == '5'){
            $(".city").hide();
            $(".city").eq(4).show();
        }
    })
})