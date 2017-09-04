/**
 * Created by Foliage on 2016/11/4.
 */
$(function () {
    //手机号码事件
    blurverify();
    //明细弹出
    $(".freeDetails").on("click",function(){
        $(".freeDetaBox").addClass("on");
        $(".mask").show();
    })
    $(".freeDetaBox .close,.mask").on("click",function(){
        $(".freeDetaBox").removeClass("on");
        $(".mask").hide();
    })

    //取上个页面带过来的url参数
    var Num = GetQueryString("Num");
    var Price = GetQueryString("Price");
    var Date = GetQueryString("Date");
    var TourProductID = GetQueryString("TourProductID");
    var ProductSkuId = GetQueryString("ProductSkuId");
    var DayPriceId = GetQueryString("DayPriceId");
    var Val = GetQueryString("Val");

    //页面上相关数据注入
    var TotalPrice = Number(Price) * Number(Num);
    $(".OrderBottom .Pricenum").text(TotalPrice);
    $(".freeDetailList div").eq(0).find('i').text(Val);
    $(".freeDetailList .num").text(Num);
    $(".freeDetailList .red i").text(Price);

    //旅客数量循环入住
    var str = '<div class="everyOne"><p class="everyOneT">旅客1：</p><div class="row"><div class="col-50"><input type="text" name="name" class="last" value="" placeholder="与护照姓名一致（姓）" maxlength="10" /></div><div class="col-50"><input type="text" name="name" class="name" value="" placeholder="与护照姓名一致（名）" maxlength="10"/></div></div></div>';
    for(var i = 2; i <= Num; i++) {0
        str = str + '<div class="everyOne"><p class="everyOneT">旅客'+i+'：</p><div class="row"><div class="col-50"><input type="text" name="name" class="last" value="" placeholder="与护照姓名一致（姓）" maxlength="10"/></div><div class="col-50"><input type="text" name="name" class="name" value="" placeholder="与护照姓名一致（名）" maxlength="10"/></div></div></div>';
    }
    $("#TravellerInfo").append(str);

    $("#paybtn").on('click',function () {
        //旅客信息
        var roomnum=[];
        $("#TravellerInfo .everyOne").each(function () {
            roomnum.push({'name':$(this).find('.name').val(),'last':$(this).find('.last').val()});
        })
        var ajaxData = {
            'TourProductID':TourProductID, //TourProductID
            'ProductSkuID':ProductSkuId, //ProductSkuID
            'Number':Num, //购买数量
            'Travellers':roomnum, //旅客信息
            'Date':Date, //出行时间
            'Contacts':$("#ZhName").val(), //联系人姓名
            'Email':$("#mail").val(), //邮箱
            'Mobile':$("#phone").val(), //手机号
            'VerifyCode':$("#code").val(), //验证码
            'Message':$("#Message").val(), //留言
        }

        if($(".last").val() == ''){
            $.toast('请填写完整的旅客信息');
            return
        }else if(!/^[a-zA-Z|\s]{1,10}$/i.test($(".last").val())){
            $.toast('旅客姓只能输入长度1-10位英文或者拼音');
            return
        }else if($(".name").val() == ''){
            $.toast('请填写完整的旅客信息');
            return
        }else if(!/^[a-zA-Z|\s]{1,10}$/i.test($(".name").val())){
            $.toast('旅客姓只能输入长度1-10位英文或者拼音');
            return
        }else if(ajaxData.Contacts == ''){
            $.toast('姓名不能为空');
            return
        }else if(!/^[\u4e00-\u9fa5|^\\s]{2,20}$/i.test(ajaxData.Contacts)){
            $.toast('姓名只能输入纯中文长度为2-20位,不能有空格');
            return
        }else if(ajaxData.Email == ''){
            $.toast('邮箱不能为空');
            return
        }else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(ajaxData.Email)){
            $.toast('邮箱格式不正确');
            return
        }else if(ajaxData.Mobile == ''){
            $.toast('手机号码不能为空');
            return
        }else if(!/^1(3\d|5[0-35-9]|8[025-9]|47)\d{8}$/i.test(ajaxData.Mobile)){
            $.toast('手机号码格式不正确');
            return
        }

        $.ajax({
            type: "post",	//提交类型
            dataType: "json",	//提交数据类型
            url: "/play/confirmorder/",  //提交地址
            data: ajaxData,
            beforeSend: function () { //加载过程效果
                // $("#paybtn").text('提交中...');
                // $("#paybtn").addClass('course');
                // $("#paybtn").attr('id','');
            },
            success: function (data) {	//函数回调
                if(data.ResultCode == '200'){
                    var Url = data.Url;
                    window.location.href = Url;
                }else if(data.ResultCode == '100'){
                    $.toast(data.Message);
                }else{
                    $.toast(data.Message);
                }
            }
        })
    })
})