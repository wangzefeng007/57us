/**
 * Created by Foliage on 2016/11/4.
 */

//鼠标离开验证方法
function blurverify() {
    $("#phone").blur(function () {
        var phone = $(this).val();
        // if(phone == ''){
        //     $.toast("手机号码不能为空");
        //     return
        // }else if(!/^1(3\d|5[0-35-9]|8[025-9]|47)\d{8}$/i.test(phone)){
        //     $.toast("手机号码格式不正确");
        //     return
        // }
        _thisDom = {
            'id': $(this).attr('id'),
        }
        $.ajax({
            //请求方式为get
            type: "post",
            //json文件位置
            url: member + '/usertcajax.html',
            data: {
                'Intention': 'LoginStatus',
            },
            //返回数据格式为json
            dataType: "jsonp",
            jsonp: "LoginStatus",
            //请求成功完成后要执行的方法
            success: function() {}
        });
    })
}
var _thisDom;
//判断账号是否存在
function IdYesNo() {
    var phone = $("#phone").val();
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/ajax/judgeisregister/",  //提交地址
        data: {	//提交数据
            'Mobile': phone,
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == '100'){
                $("#piccode").removeClass("hidden")
                $("#codebtn").click(function () {
                    var smsbtn = this;
                    smscode(phone,smsbtn);
                })
            }else if(data.ResultCode == '200'){
                $("#yesphone").removeClass("hidden")
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
    $(".content").scrollTo({durTime:100});
}
