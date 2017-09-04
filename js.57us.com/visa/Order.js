/**
 * Created by Foliage on 2016/12/1.
 */

//定义提交变量
var id = GetQueryString('id');
var startDate = GetQueryString('d');
var n = GetQueryString('n');
var price = GetQueryString('price');

$(function () {
    //同意按钮
    $('.protocol .cbt').inputbox();
    //出行时间注入
    $(".startDate").text(startDate);
    //数量注入
    $(".num").text(n);
    //单价注入
    $(".price").text(price);
    //总价注入
    $(".TotalPrice").text(price * n);
    //鼠标离开验证方法
    blurverify();

    //城市选择
    $("#ordercity").click(function (e) {
        SelCity(this,e);
    });

    //去支付
    $("#GoPayBtn").on('click',function () {
        //定义提交变量
        var ajaxData = {
            'Intention':'VisaOrder', //方法
            'VisaID':id, //产品id
            'Time':startDate, //出行时间
            'Number':n , //出行人数
            'Contacts':$("#zhname").val(), //中文姓名
            'Mobile':$("#phone").val(), //手机号
            'VerifyCode':$("#code").val(), //短信验证码,如果提交空不做判断  如果提交不为空做判断
            'Email':$("#mail").val(), //邮箱地址
            'City':$("#ordercity").val(), //所在城市
            'Address':$("#address").val(), //详细地址
        }
        //所在城市定义，下面验证使用
        var _city = $("#ordercity").val().split('-');

        //验证判断
        if(ajaxData.Contacts == ''){
            $("#zhname").parent().addClass('ErroBox');
            $("#zhname").next().text('姓名不能为空');
            W_ScrollTo($("#zhname"),+50);
            return
        }else if(rule.Name.test(ajaxData.Contacts) != true){
            $("#zhname").parent().addClass('ErroBox');
            $("#zhname").next().text('请输入2-20位的中英文姓名');
            W_ScrollTo($("#zhname"),+50);
            return
        }else if(ajaxData.Mobile == ''){
            $("#phone").parent().addClass('ErroBox');
            $("#phone").next().text('手机号码不能为空');
            W_ScrollTo($("#phone"),+50);
            return
        }else if(!/^1(3\d|5[0-35-9]|8[025-9]|47)\d{8}$/i.test(ajaxData.Mobile)){
            $("#phone").parent().addClass('ErroBox');
            $("#phone").next().text('手机号码格式不正确');
            W_ScrollTo($("#phone"),+50);
            return
        }else if(ajaxData.Email == ''){
            $("#mail").parent().addClass('ErroBox');
            $("#mail").next().text('邮箱不能为空');
            W_ScrollTo($("#mail"),+50);
            return
        }else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(ajaxData.Email)){
            $("#mail").parent().addClass('ErroBox');
            $("#mail").next().text('邮箱格式不正确');
            W_ScrollTo($("#mail"),+50);
            return
        }else  if(_city[0] == undefined || _city[1] == undefined || _city[2] == undefined){
            $('#ordercity').parent().addClass('ErroBox');
            $('#ordercity').parent().find('.erroText').text('所在城市不正确');
            return
        }else if(ajaxData.Address == ''){
            $('#address').parent().addClass('ErroBox');
            $('#address').parent().find('.erroText').text('所在城市不正确');
            return
        }else if($(".protocol label").attr('class') != 'cbt cb checked'){
            layer.msg('请先同意服务条款');
            return
        }
        if($("#piccode").is(':visible')){
            if($("#code").val() == ''){
                layer.msg('请输入短信验证码');
                return
            }
        }

        //ajax提交
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajaxvisa.html",  //提交地址
            data: ajaxData,
            beforeSend: function () { //加载过程效果
                $("#GoPayBtn").text('提交中...');
                $("#GoPayBtn").addClass('course');
                $("#GoPayBtn").attr('id','');
            },
            success: function (data) {	//函数回调
                if(data.ResultCode == '200'){
                    var Url = data.Url;
                    window.location.href = Url;
                }else if(data.ResultCode == '100'){
                    layer.msg(data.Message);
                    return
                }else{
                    layer.msg(data.Message);
                    return
                }
            },complete: function () {  //提交结束
                $(".GoPayBtn").text('去支付');
                $(".GoPayBtn").removeClass('course');
                $(".GoPayBtn").attr('id','GoPayBtn');
            }
        })
    });
    
})

//所在城市验证
function OrderVerify() {
    var _city = $("#ordercity").val().split('-');
    if(_city[0] == undefined || _city[1] == undefined || _city[2] == undefined){
        $('#ordercity').parent().addClass('ErroBox');
        $('#ordercity').parent().find('.erroText').text('所在城市不正确');
    }
}

//鼠标离开验证方法
function blurverify() {
    $(".VisaOrderLeft input").mouseup(function () {
        $(this).parent().removeClass('ErroBox');
    })

    $("#zhname").blur(function () {
        var zhname = this.value;
        if(zhname == ''){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('姓名不能为空');
        }else if(rule.Name.test(zhname) != true){
            $(this).parent().addClass('ErroBox')
            $(this).next().text('请输入2-20位的中英文姓名')
        }else {
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#mail").blur(function () {
        var mail = this.value;
        if(mail == ''){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('邮箱不能为空');
        }else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(mail)){
            $(this).parent().addClass('ErroBox')
            $(this).next().text('邮箱格式不正确')
        }else {
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#address").blur(function () {
        var address = this.value;
        if(address == ''){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('详细地址不能为空');
        }
    })

    $("#phone").on('input change',function () {
        $("#piccode").hide();
        $("#yesphone").hide();
    })

    $("#phone").blur(function () {
        var phone = $(this).val();
        if(phone == ''){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('手机号码不能为空');
            return
        }else if(!/^1(3\d|5[0-35-9]|8[025-9]|47)\d{8}$/i.test(phone)){
            $(this).parent().addClass('ErroBox')
            $(this).next().text('手机号码格式不正确')
            return
        }else {
            $(this).parent().removeClass('ErroBox');
        }
        _thisDom = {
            'id': $(this).attr('id'),
        }
        tc_login_dom = {
            'Type': $(this).attr('data-type'),
        }
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
    })
}
var _thisDom;
//判断账号是否存在
function IdYesNo() {
    var phone = $("#phone").val();
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/ajaxvisa.html",
        data: {
            'Intention':'JudgeIsRegister',
            'Mobile': phone,
        },
        success: function(data) {
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
        type: "post",
        dataType: "json",
        url: "/ajaxvisa.html",
        data: {
            'Intention':'ValidateMobileCode',
            'Mobile': phone,
        },
        success: function(data) {
            if(data.ResultCode == '200'){
                get_code_time(o);
                layer.msg(data.Message);
            }else if(data.ResultCode == '100'){
                layer.msg(data.Message);
            }
        }
    });
}
