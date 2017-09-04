/**
 * Created by Foliage on 2017/2/28.
 */
$(document).ready(function () {
    //选择性别
    $("#sex").picker({
        toolbarTemplate: '<header class="bar bar-nav">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">请选择性别</h1>\</header>',
        cols: [
            {
                textAlign: 'center',
                values: ['男', '女']
            }
        ]
    });
    //选择性别
    $("#nationality").picker({
        toolbarTemplate: '<header class="bar bar-nav">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">请选择国籍</h1>\</header>',
        cols: [
            {
                textAlign: 'center',
                values: ['中国', '美国']
            }
        ]
    });
    //提交保存
    $("#saveBtn").on('click',function () {
        if($("#isDefault input").attr('checked') == 'true'){
            var isDefault = '1';
        }else {
            var isDefault = '0';
        }
        var ajaxData = {
            'Intention': 'PassengerAdd', //新增旅客
            'ID':GetQueryString('ID'), //旅客信息ID
            'ZhName':$("#zhName").val(), //中文姓名
            "Sex":$("#sex").val(), //性别  女男
            'BirthDay':$("#birthDay").val(), //出生日期
            'Mobile':$("#mobile").val(), //手机号码
            'IdCard':$("#idCard").val(), //护照号
            'CardEndDate':$("#cardEndDate").val(), //证件有效截止日期
            'Nationality':$("#nationality").val(), //国籍
            'IsDefault':isDefault, //是否默认 0或1
        };
        if(rule.NameZH.test(ajaxData.ZhName) != true){
            $.toast('请输入中文姓名');
            return
        }else if(ajaxData.Sex == ''){
            $.toast('请选择性别');
            return
        }else if(ajaxData.BirthDay == ''){
            $.toast('请选择出生日期');
            return
        }else if(ajaxData.Mobile == ''){
            $.toast('手机号码不能为空');
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            $.toast('手机号码格式不正确');
            return
        }else if(ajaxData.IdCard == ''){
            $.toast('请输入护照号');
            return
        }else if(ajaxData.CardEndDate == ''){
            $.toast('请输入护照有效日期');
            return
        }else if(ajaxData.Nationality == ''){
            $.toast('请选择国籍');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('保存中');
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    $.toast('保存成功');
                    setTimeout(function(){
                        history.go(-1);
                    },1000);
                }else{
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });

    //删除旅客信息
    $(".deteTrave").on('click',function () {
        $.confirm("是否要删除当前旅客信息？", function() {
            var ajaxData = {
                'Intention':'DelPassenger', //删除旅客
                'ID':GetQueryString('ID'), //旅客信息ID
            };
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/userajax.html",
                data: ajaxData,
                beforeSend: function () {
                    $.showPreloader('删除中');
                },
                success: function(data) {
                    if(data.ResultCode=='200'){
                        $.toast('删除成功');
                        setTimeout(function(){
                            history.go(-1);
                        },1000);
                    }else{
                        $.toast(data.Message);
                    }
                },
                complete: function () { //加载完成提示
                    $.hidePreloader();
                }
            });
        });
    });
});