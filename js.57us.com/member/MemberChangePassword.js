/**
 * Created by Foliage on 2017/2/9.
 */
$(function () {
    //新输入的密码验证强度
    $("#newPassword").bind('input propertychange',function () {
        var _thisVal = $(this).val();
        var _num = pwdStrong(_thisVal);
        if(_num == '1'){
            $("#strength li").removeClass('on');
            $("#strength li").eq(0).addClass('on');
        }else if(_num == '2' || _num == '3'){
            $("#strength li").removeClass('on');
            $("#strength li").eq(0).addClass('on');
            $("#strength li").eq(1).addClass('on');
        }else if(_num >= '4'){
            $("#strength li").removeClass('on');
            $("#strength li").eq(0).addClass('on');
            $("#strength li").eq(1).addClass('on');
            $("#strength li").eq(2).addClass('on');
        }else {
            $("#strength li").removeClass('on');
        }
    })

    //保存修改密码
    $("#save").on('click',function () {
        //ajax提交参数
        var ajaxData = {
            'Intention': 'ModifyPass', //方法名
            'Pass': $("#oldPassword").val(), //旧的密码
            'NewPass': $("#newPassword").val(), //新的密码
        }
        //执行验证
        if(ajaxData.Pass == ''){
            layer.msg('当前密码不能为空');
            return
        }else if(ajaxData.NewPass == ''){
            layer.msg('请输入新的密码');
            return
        }else if(rule.PassWord.test(ajaxData.NewPass) != true){
            layer.msg('密码格式为6-20位数字、字母或者符号组合');
            return
        }else if(ajaxData.NewPass != $("#confirmPassword").val()){
            layer.msg('两次新密码不一致');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location = data.Url;
                    },500);
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })
})

//验证密码强度
function pwdStrong(val) {
    var lv=0;
    if(val.match(/[a-z]/g)){lv++;}
    if(val.match(/[A-Z]/g)){lv++;}
    if(val.match(/[0-9]/g)){lv++;}
    if(val.match(/(.[^a-z0-9A-Z])/g)){lv++;}
    if(lv > 4){lv=4;}
    if(lv===0) return false;
    return lv;
}