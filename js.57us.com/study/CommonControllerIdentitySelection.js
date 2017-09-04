/**
 * Created by Foliage on 2016/10/26.
 */
$(function () {
    var type;
    $(".identityList li").click(function(){
        var _text = $(this).attr("data-text");
        type = $(this).attr('data-type');
        $(".mask").fadeIn();
        $(".identityChoseSure").fadeIn();
        $(".identityChoseSure").find(".identChose").text(_text);

    })
    $(".identityChoseSure .close,#close").click(function(){
        $(".mask").fadeOut();
        $(".identityChoseSure").fadeOut();
    })

    $("#SureBtn").click(function () {
        ajaxData = {
            'Intention': 'IdentitySelection', //方法名
            'Type':type, //选择的身份类型 1代表学生 2代表顾问 3代表教师
        }
        $.post("/commonajax/",ajaxData,function(data){
            if(data.ResultCode == "200"){
                $(".mask").fadeOut();
                $(".identityChoseSure").fadeOut();
                var Url = data.Url;
                layer.msg(data.Message);
                setTimeout(function () {
                    location.href=Url;
                }, 600);
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })
})