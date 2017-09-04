/**
 * Created by Foliage on 2017/2/23.
 */
$(function () {
    'use strict';
    //时间数据注入
    var ajaxData = {
        "Intention":'GetStudyTourDate',
        'id':GetQueryString('id'),
    }
    $.post('/ajaxstudy/',ajaxData, function (data) {
        $("#departDate").picker({
            toolbarTemplate: '<header class="bar bar-nav">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">请选择时间</h1>\</header>',
            cols: [
                {
                    textAlign: 'center',
                    values: data.Data,
                }
            ],
            onClose:function () {
                var _n;
                for(var i = 0; i < data.Data.length; i++) {
                    if(data.Data[i] == $("#departDate").val()) {
                        _n = [i];
                    }
                }
                $("#ednDate").text('截止报名时间：'+data.Data2[_n]);
            }
        });
    },'json');

    //数量增加减少，并计算价格
    $(".num_box a").on('click',function() {
        var $count=$(this).attr("data-type");
        var $thisDom = $(this).parents('.num_box');
        $thisDom.find('.num_input').val(function() {
            var value = $(this).val();
            $count=="-"?value--:value++;
            if ( value>1 ) {
                return value;
            }
            else if ( value=1 ){
                var value = 1;
                return value;
            }
        })
        var $num = $thisDom.find('.num_input').val();
        var $price = $("#price").val();
        var totalPrice = Number($num) * Number($price);
        $("#totalPrice").html('&yen;'+totalPrice+'.00');
    });

    //提交按钮
    $(".submitBtn").on('click',function () {
        var id = GetQueryString('id');
        var $num = $('.num_input').val();
        var $price = $('#price').val();
        var $date = $("#departDate").val();
        if($date == ''){
            $.toast('请选择出发时间');
            return
        }
        location.href='/study/placeorder/?id='+id+'&d='+$date+'&p='+$price+'&n='+$num;
    })
    $.init();
})