/**
 * Created by Foliage on 2017/2/27.
 */
$(function () {
    //焦点移入，移除错误提示
    $("input[type='text'],input[type='password']").mouseup(function() {
        $(this).parents('.loginCont').find('.erroTip').fadeOut(200);
        $(this).parents('.inputBox').removeClass('erro');
    })

    $(".saveBtn").on('click',function () {
        //ajax提交参数
        var ajaxData = {
            'Intention':'TransitionIdentity', //转为教师
            'ZhName':$(".zhName").val(), //姓名
            'Mobile':$(".mobile").val(), //手机号码
            'Identity':$("#Identity").val()
        }

        //执行验证
        if(ajaxData.ZhName == '') {
            errorHint('.zhName', '姓名不能为空');
            return
        } else if(rule.Name.test(ajaxData.ZhName) != true) {
            errorHint('.zhName', '请输入大于2位小于20位中英姓名');
            return
        }else if(ajaxData.Mobile == ''){
            errorHint('.mobile', '手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            errorHint('.mobile', '手机号码格式不正确');
            return
        }

        //执行ajax提交操作
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/loginajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function() {
                        window.location = data.Url;
                    }, 600);
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

/**
 * 错误提示方法
 *
 * @param id 操作对应的dom
 * @param message 错误操作文字
 */
function errorHint(id, message) {
    var $thisDom = $(""+id+"");
    $thisDom.parents('.loginCont').find('.erroTip').fadeIn(500);
    $thisDom.parents('.loginCont').find('.erroIfno').text(message);
    $thisDom.parents('.inputBox').addClass('erro');
    setTimeout(function(){
        $thisDom.parents('.loginCont').find('.erroTip').fadeOut(1000);
        setTimeout(function(){
            $thisDom.parents('.inputBox').removeClass('erro');
        },400);
    },5000);
}