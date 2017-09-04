/**
 * Created by Foliage on 2017/2/13.
 */
$(function () {
    //表单相关美化
    $('.sexChose .cbt').inputbox();
    $('.diyselect').inputbox({
        height:40,
        width:156
    });

    //焦点移入后，隐藏错误提示
    $("#rightContent input").mouseup(function () {
        $(this).parents('tr').find('.posDiv').removeClass('erro');
    })
    $("#cardType,#nationality").mouseup(function () {
        $(this).css('border','1px solid #c3c3c3');
    })

    //保存提交
    $("#save").on('click',function () {
        var ajaxData = {
            'Intention': 'PassengerAdd', //新增旅客
            'ZhName':$("#zhName").val(), //中文姓名
            'ZhXinPin':$("#zhXinPin").val(), //中文姓拼音
            'ZhMingPin':$("#zhMingPin").val(), //中文名拼音
            "Sex":$("#sex .rb_active").attr('val'), //性别  0为女 1为男
            'BirthDay':$("#birthDay").val(), //出生日期
            'Mobile':$("#mobile").val(), //手机号码
            'Mail':$("#mail").val(), //电子邮箱
            'IdCard':$("#idCard").val(), //证件号码
            'CardEndDate':$("#cardEndDate").val(), //证件有效截止日期
            'Nationality':$("#nationality input").val(), //国籍
            'PassengerID':$("#PassengerID").val(), //旅客ID
        }
        if(ajaxData.ZhName == ''){
            errorHint('zhName','姓名不能为空');
            return
        }else if(rule.Name.test(ajaxData.ZhName) != true){
            errorHint('zhName','只允许使用 中文、26个英文字母的组合形式');
            return
        }
        if(ajaxData.ZhXinPin != ''){
            if(!/^[a-zA-Z|\s]{1,40}$/i.test(ajaxData.ZhXinPin)){
                errorHint('zhXinPin','姓名拼音，姓只能输入拼音');
                return
            }
        }
        if(ajaxData.ZhMingPin != ''){
            if(!/^[a-zA-Z|\s]{1,40}$/i.test(ajaxData.ZhMingPin)){
                errorHint('zhMingPin','姓名拼音，名只能输入拼音');
                return
            }
        }
        if(ajaxData.BirthDay == ''){
            errorHint('birthDay','出生日期不能为空');
            return
        }else if(ajaxData.Mobile == ''){
            errorHint('mobile','手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            errorHint('mobile','手机号码格式不正确');
            return
        }else if(ajaxData.Mail!=''){
            if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9])$/i.test(ajaxData.Mail)){
                errorHint('mail', '邮箱格式不正确');
                return
            }
        }else if(ajaxData.IdCard == ''){
            errorHint('idCard','护照号不能为空');
            return
        }else if(ajaxData.CardEndDate == ''){
            errorHint('cardEndDate','证件有效日期不能为空');
            return
        }else if(ajaxData.Nationality == '请选择国籍'){
            $("#nationality").css('border','1px solid #ff6767');
            layer.msg('请选择国籍');
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
                if(data.ResultCode == '200'){
                    layer.msg('保存成功');
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

/**
 * 错误提示方法
 *
 * @param id 对应显示错误位置
 * @param errorText 错误提示文字
 */
function errorHint(id,errorText) {
    $("#"+id+"").parents('tr').find('.posDiv').addClass('erro');
    W_ScrollTo($("#"+id+""),+100);
    layer.msg(errorText);
}