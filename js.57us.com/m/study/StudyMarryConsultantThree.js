/**
 * Created by Foliage on 2016/11/25.
 */
$(function () {
    $(".howContact li").on('click',function () {
        $(".howContact li").removeClass('on');
        $(this).addClass('on');
    })

    $(".getSolution").on('click',function () {
        var ajaxData = {
            'Intention':'MarryChoose',
            'MarryID':GetQueryString('MarryID'),
            'ConsultantID':GetQueryString('ConsultantID'),
            'ContactTimes':$(".howContact .on").attr('data-type'),
        }
        $.post('/ajaxstudy/',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                var Url = data.Url;
                window.location = Url;
            }else {
                $.toast(data.Message);
            }
        },'json')
    })

})