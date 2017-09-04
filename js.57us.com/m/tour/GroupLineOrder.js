/**
 * Created by Foliage on 2016/11/11.
 */

var GroupDate;
var TourProductID;
var Num;
$(function () {
    //初始化页面加载
    ajaxLoad();

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

    //点击提交去支付
    $("#paybtn").on('click',function () {
        var SkuID =[];
        //skuid数据遍历成数组
        $('.freeDetailList').each(function () {
            SkuID.push($(this).attr('data-id'));
        })
        //旅客信息
        var roomnum=[];
        $("#TravellerInfo .everyOne").each(function () {
            roomnum.push({'name':$(this).find('.name').val(),'last':$(this).find('.last').val()});
        })
        var ajaxData = {
            'Intention':'GroupLineOrder', //方法
            'TourProductID':TourProductID, //TourProductID
            'SkuID':SkuID, //SkuID
            'Travellers':roomnum, //旅客信息
            'Date':GroupDate, //出行时间
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
            type: "post",
            dataType: "json",
            url: "",
            data: ajaxData,
            success: function(data) {
                if(data.ResultCode == '200'){
                    var Url = data.Url;
                    window.location.href = Url;
                }else{
                    $.toast(data.Message);
                }
            }
        });
    })

})

//初始化加载方法
function ajaxLoad() {
	var ajaxData = {
            'Load':1, //TourProductID
        }
    $.ajax({
        type: "get",
        dataType: "json",
        url: "",
        data: ajaxData,
        success: function(data) {
            if(data.ResultCode == '200'){
                GroupDate = data.Date;
                TourProductID = data.TourProductID;
                Num = data.Num;
                //旅客数量循环入住
                var str = '<div class="everyOne"><p class="everyOneT">旅客1：</p><div class="row"><div class="col-50"><input type="text" name="name" class="last" value="" placeholder="与护照姓名一致（姓）" /></div><div class="col-50"><input type="text" name="name" class="name" value="" placeholder="与护照姓名一致（名）" /></div></div></div>';
                for(var i = 2; i <= data.Num; i++) {
                    str = str + '<div class="everyOne"><p class="everyOneT">旅客'+i+'：</p><div class="row"><div class="col-50"><input type="text" name="name" class="last" value="" placeholder="与护照姓名一致（姓）" /></div><div class="col-50"><input type="text" name="name" class="name" value="" placeholder="与护照姓名一致（名）" /></div></div></div>';
                }
                $("#TravellerInfo").append(str);
                //费用明细注入
                $("#costlist").empty();
                $("#costlist").append(data.CostList);
                //重新计算总价
                var sum = 0;
                $(".freeDetailList").each(function () {
                    sum += $(this).attr('data-value') * 1;
                })
                $("#TotalPrice").text(sum);
            }else{
                $.toast(data.Message);
            }
        }
    });
}