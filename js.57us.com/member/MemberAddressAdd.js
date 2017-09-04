/**
 * Created by Foliage on 2017/2/13.
 */
$(function () {
    //城市选择
    $("#CitySet").click(function (e) {
        SelCity(this,e);
    });

    //焦点移入后，隐藏错误提示
    $("#rightContent input").mouseup(function () {
        $(this).parents('tr').find('.posDiv').removeClass('erro');
    })
    $("#CitySet").mouseup(function () {
        $(this).css('border','1px solid #c3c3c3');
    })

    //保存提交
    $("#save").on('click',function () {
        var _citySet = $("#CitySet").val().split('-');
        var ajaxData = {
            'Intention': 'AddressAdd', //新增旅客
            'Province':_citySet[0], //省份
            'City':_citySet[1], //城市
            'Area':_citySet[2], //县城
            'Address':$('#address').val(), //详细地址
            'Postcode':$("#postcode").val(), //邮编
            'Recipients':$("#recipients").val(), //收件人
            'Mobile':$("#mobile").val(),  //手机号码
            'TelArea':$("#telArea").val(), //电话区号
            'Tel':$("#tel").val(), //电话号码
            'TelExtension':$("#telExtension").val(), //分机号
            'ID':$("#ShippingAddressID").val(), //ID
        }
        if($("#CitySet").val() == ''){
            $("#CitySet").css('border','1px solid #ff6767');
            W_ScrollTo($("#CitySet"),+100);
            layer.msg('请选择所在地区');
            return
        }else if(ajaxData.Address == ''){
            errorHint('address','详细地址不能为空');
            return
        }else if(ajaxData.Postcode == ''){
            errorHint('postcode','邮编不能为空');
            return
        }else if(ajaxData.Recipients == ''){
            errorHint('recipients','收件人不能为空');
            return
        }else if(ajaxData.Mobile == ''){
            errorHint('mobile','手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            errorHint('mobile','手机号码格式不正确');
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