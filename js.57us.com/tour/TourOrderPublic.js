/**
 * Created by Foliage on 2016/8/22.
 */

//鼠标离开验证方法
function blurverify() {

    //鼠标移入，移除错误提示
    $('input').mouseup(function () {
        $(this).parent().removeClass('erro');
    })

    //验证旅客姓
    $(".tourManList .last").each(function () {
        $(this).blur(function () {
            var last = $(this).val();
            if(last == ''){
                $(this).parent().addClass('erro');
                $(this).next().text('姓不能为空');
            }else if(!/^[a-zA-Z|\s]{1,20}$/i.test(last)){
                $(this).parent().addClass('erro')
                $(this).next().text('长度1-20位英文或者拼音')
            }else {
                $(this).parent().removeClass('erro');
            }
        })
    })

    //验证旅客名
    $(".tourManList .name").each(function () {
        $(this).blur(function () {
            var name = $(this).val();
            if(name == ''){
                $(this).parent().addClass('erro');
                $(this).next().text('名不能为空');
            }else if(!/^[a-zA-Z|\s]{1,20}$/i.test(name)){
                $(this).parent().addClass('erro')
                $(this).next().text('长度1-20位英文或者拼音')
            }else {
                $(this).parent().removeClass('erro');
            }
        })
    })

    //验证护照
    $(".tourManList .hz").each(function () {
        $(this).blur(function () {
            var hz = $(this).val();
            if(hz == ''){
                $(this).parent().addClass('erro');
                $(this).next().text('护照不能为空');
            }else if(rule.HZ.test(hz) != true){
                $(this).parent().addClass('erro')
                $(this).next().text('护照格式不正确')
            }else {
                $(this).parent().removeClass('erro');
            }
        })
    })

    //验证旅客手机号
    $(".tourManList .Tel").blur(function () {
        var Tel = $(this).val();
        if(Tel == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('联系电话不能为空');
        }else if(rule.phone.test(Tel) != true){
            $(this).parent().addClass('erro')
            $(this).next().text('联系电话格式不正确')
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证旅客微信号
    $(".tourManList .weixin").blur(function () {
        var weixin = $(this).val();
        if(weixin == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('微信号不能为空');
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证酒店名称
    $(".HotelInfo .hotelname").blur(function () {
        var hotelname = $(this).val();
        if(hotelname == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('酒店名称不能为空');
        }else if(!/^[a-zA-Z|\s]{1,50}$/i.test(hotelname)){
            $(this).parent().addClass('erro')
            $(this).next().text('请输入英文，不要超过50个字')
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证酒店地址
    $(".HotelInfo .hoteladdress").blur(function () {
        var hoteladdress = $(this).val();
        if(hoteladdress == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('酒店地址不能为空');
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证酒店电话
    $(".HotelInfo .hoteltel").blur(function () {
        var hoteltel = $(this).val();
        if(hoteltel == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('酒店电话不能为空');
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证接机航班号
    $(".FlightJoinCourse").blur(function () {
        var FlightJoinCourse = $(this).val();
        if(FlightJoinCourse == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('接机航班号不能为空');
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证送机航班号
    $(".FlightDeliverCourse").blur(function () {
        var FlightDeliverCourse = $(this).val();
        if(FlightDeliverCourse == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('出发航班号不能为空');
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证联系人姓名
    $("#zhname").blur(function () {
        var zhname = $(this).val();
        if(zhname == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('姓名不能为空');
        }else if(rule.NameZH.test(zhname) != true){
            $(this).parent().addClass('erro')
            $(this).next().text('只能输入纯中文长度为2-10位,不能有空格')
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证电子邮箱
    $("#mail").blur(function () {
        var mail = $(this).val();
        if(mail == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('邮箱不能为空');
        }else if(rule.Mail.test(mail) != true){
            $(this).parent().addClass('erro')
            $(this).next().text('邮箱格式不正确')
        }else {
            $(this).parent().removeClass('erro');
        }
    })

    //验证手机号码，及相关操作
    $("#phone").on('input change',function () {
        $("#piccode").hide();
        $("#yesphone").hide();
    })
    $("#phone").blur(function () {
        var phone = $(this).val();
        if(phone == ''){
            $(this).parent().addClass('erro');
            $(this).next().text('手机号码不能为空');
            return
        }else if(rule.phone.test(phone) != true){
            $(this).parent().addClass('erro')
            $(this).next().text('手机号码格式不正确')
            return
        }else {
            $(this).parent().removeClass('erro');
        }
        _thisDom = {
            'id': $(this).attr('id'),
        }

        tc_login_dom = {
            'Type': $(this).attr('data-type'),
        }
        if($('#country input').val() == 'cn'){
            $.ajax({
                type: "post",
                url: member + 'userajax.html',
                data: {
                    'Intention': 'LoginStatus',
                },
                dataType: "jsonp",
                jsonp: "LoginStatus",
                success: function() {}
            });
        }
    })
}
var _thisDom;
//判断账号是否存在
function IdYesNo() {
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/ajaxtour/judgeisregister/",  //提交地址
        data: {	//提交数据
            'Mobile': $("#phone").val(),
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == '100'){
                $("#piccode").show();
                $("#codebtn").click(function () {
                    var smsbtn = this;
                    smscode(phone,smsbtn);
                })
            }else if(data.ResultCode == '200'){
                $("#yesphone").show();
            }
        }
    });
}

//获取短位验证码
function smscode(phone,smsbtn) {
    var o = smsbtn;
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/ajax/validatemobilecode/",  //提交地址
        data: {	//提交数据
            'Mobile': phone,
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == '200'){
                get_code_time(o);
                layer.msg(data.Message);
            }else if(data.ResultCode == '100'){
                layer.msg(data.Message);
            }
        }
    });
}

//验证失败滚动失败位置
function roll() {
    W_ScrollTo($('.lastname').eq(0));
    // $('body').animate({scrollTop: $(".lastname").offset().top}, 300);
}