/**
 * Created by Foliage on 2017/3/1.
 */
$(document).ready(function () {
    //城市
    $("#city").cityPicker({
        toolbarTemplate: '<header class="bar bar-nav">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">选择所在城市</h1>\</header>'
    });

    //保存事件
    $("#saveBtn").on('click',function () {
        if($("#isDefault input").attr('checked') == 'true'){
            var isDefault = '1';
        }else {
            var isDefault = '0';
        }
        var ajaxData = {
            'Intention': 'AddressAdd', //新增旅客
            'Recipients':$("#recipients").val(), //收件人
            'Mobile':$("#mobile").val(),  //手机号码
            'City':$("#city").val(), //城市
            'Address':$('#address').val(), //详细地址
            'Postcode':$("#postcode").val(), //邮编
            'IsDefault': isDefault, // 0代表不是默认 1代表默认
        }
        if(ajaxData.Recipients == ''){
            $.toast('请输入收件人');
            return
        }else if(ajaxData.Mobile == ''){
            $.toast('手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            $.toast('手机号码格式不正确');
            return
        }else if(ajaxData.City == ''){
            $.toast('请选择所在地区');
            return
        }else if(ajaxData.Address == ''){
            $.toast('请输入详细地址');
            return
        }else if(ajaxData.Postcode == ''){
            $.toast('请输入邮编');
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

    //删除地址
    $(".delAddress").on('click',function () {
        $.confirm("是否要删除当前地址？", function() {
            var ajaxData = {
                'Intention':'DelAddress', //删除旅客
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