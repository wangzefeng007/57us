/**
 * Created by Foliage on 2016/10/25.
 */
//鼠标离开验证方法
function blurverify() {
    $(".sureOrderBoxM input").mouseup(function () {
        $(this).parent().removeClass('ErroBox');
    })

    $("#zhname").blur(function () {
        var zhname = this.value;
        if(zhname == ''){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('姓名不能为空');
        }else if(!/^[\u4e00-\u9fa5|^\\s]{2,20}$/i.test(zhname)){
            $(this).parent().addClass('ErroBox')
            $(this).next().text('只能输入纯中文长度为2-20位,不能有空格')
        }else {
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#StudyTime").blur(function () {
        var StudyTime = this.value;
        if(StudyTime == ''){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('计划留学时间不能为空');
        }else {
            $(this).parent().removeClass('ErroBox');
        }
    })


    //游学使用的离开验证
    //验证姓名
    $(".lvname").each(function () {
        $(this).blur(function () {
            var name = $(this).val();
            if(name == ''){
                $(this).parent().addClass('ErroBox');
                $(this).next().text('姓名不能为空');
            }else {
                $(this).parent().removeClass('ErroBox');
            }
        })
    })

    //验证护照
    $(".hz").each(function () {
        $(this).blur(function () {
            var hz = $(this).val();
            if(hz == ''){
                $(this).parent().addClass('ErroBox');
                $(this).next().text('护照不能为空');
            }else {
                $(this).parent().removeClass('ErroBox');
            }
        })
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
        url: "/commonajax/",
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
        url: "/commonajax/",
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

//验证失败滚动失败位置
function roll() {
    W_ScrollTo($('#tourManList'),+20);
    // $('body').animate({scrollTop: $(".lastname").offset().top}, 300);
}