/**
 * Created by Foliage on 2016/11/3.
 */
var TourProductID = GetQueryString("ID");
$(function() {
    //选择日期，提交数据查询
    $("#choseDate").on('click',function () {
        if($(".ComboList .on").attr('data-id') != null){
            $.ajax({
                type: "get",	//提交类型
                dataType: "json",	//提交数据类型
                url: "/play/getdate/",  //提交地址
                data: {
                    'ProductSkuId':$(".ComboList .on").attr('data-id'),
                },
                success: function(data) {	//函数回调
                    if(data.ResultCode == '200'){
                        $("#pageDate").empty();
                        pickerEvent.setPriceArr(data);
                        pickerEvent.setMonthArr(data);
                        pickerEvent.Init("calendar");
                    }else{
                        $.toast(data.Message)
                    }
                }
            });
        }else {
            $.toast("请先选择套餐");
        }
    })

    //明细弹出
    $(".freeDetails").on("click", function() {
        $(".freeDetaBox").addClass("on");
        $(".mask").show();
    })
    $(".freeDetaBox .close,.mask").on("click", function() {
        $(".freeDetaBox").removeClass("on");
        $(".mask").hide();
    })

    //选择套餐点击
    $('.ComboList li').on('click',function () {
        $('.ComboList li').removeClass('on');
        $(this).addClass('on');
        var _text = $(this).find('span').text();
        var _price = $(this).attr('data-price');
        $(".freeDetailList div").eq(0).text(_text);
        $(".OrderBottom .Pricenum").text(_price);
        $(".freeDetailList div").eq(2).find('i').text(_price);
        $(".OrderBottom").show();
        $(".num_input").val('1');
        $(".num_input").attr('value','1');
        $(".freeDetailList .num").text('1');
        var _date = $(this).attr('data-date');
        $(".dateChose").text(_date);
    })

    //按钮点击增加，减少
    $(".num_box a").on('click',function() {
        if($(".OrderBottom").css('display') == "none"){
            $.toast('请先选择套餐');
            return
        }
        var count=$(this).attr("data-type");
        $(".num_input").val(function() {
            var value = $(this).val();
            count=="-"?value--:value++;
            if ( value>1 ) {
                return value;
            }
            else if ( value=1 ){
                var value = 1;
                return value;
            }
        })
        if($("#dateprice").val() != ''){
            var price = $("#dateprice").val();
        }else {
            var price = $(".ComboList .on").attr('data-price');
        }
        var num = $(".num_input").val();
        $(".num_input").attr('value',num);
        var totalprice = Number(price) * Number(num);
        $(".freeDetailList .num").text(num);
        $(".OrderBottom .Pricenum").text(totalprice + '.00');
    });
    //点击填写旅客信息
    $(".nextBtn").on('click',function () {
        var Num = $('.freeDetailList .num').text()
        var Price = $(".ComboList .on").attr('data-price');
        var Date = $(".dateChose").text();
        var ProductSkuID = $(".ComboList .on").attr('data-id');
        var Val = $(".ComboList .on").attr('data-value');
        location.href="/play/playplaceorder/?Num="+Num+'&Price='+Price+'&Date='+Date+'&TourProductID='+TourProductID+'&ProductSkuId='+ProductSkuID+'&Val='+Val;
    })
})
