$(function(){
    //按下回车键时消除浏览器差异
    var event=arguments.callee.caller.arguments[0]||window.event;
    //未设置密码用户，弹出模态窗口
    var pass = GetQueryString("pass");
    if( pass == "null"){
        var StepPop = "<div id='pass'>" + $(".StepPop").html() + "</div>";
        layer.confirm(StepPop,{
            type: 1,
            title:0,
            skin: 'StepPop',
            area: ['440px','395px'], //宽高
            closeBtn:0,
            // content:StepPop,
            btn: ['确认', '暂不设置'],
            yes: function(index, layero){
                var Password = $("#pass input[name='password']").val();
                var RePassword = $("#pass input[name='rePassword']").val();

                $("#pass #rePassword").keyup(function () {
                    if (event.keyCode == "13") {
                        condition=1;
                    }
                });
                var ajaxData = {
                    'Intention':'MpLoginSetPass',
                    'PassWord':Password,
                    'PassWordConfirm':RePassword,
                };
                if(!/^(?=.*?[a-zA-Z])(?=.*?[0-6])[!"#$%&'()*+,\-./:;<=>?@\[\\\]^_`{|}~A-Za-z0-9]{6,20}$/i.test(Password)){
                    layer.msg('请输入6-20位数字、字母或者符号组合');
                    return
                }else if(Password !== RePassword){
                    layer.msg('两次密码不一致');
                    return
                }
                $.post('/userajax.html', ajaxData, function(data) {
                    //200 成功 100=保存失败 101=密码格式错误 102=异常请求 103=账号错误 104=两次密码不一致
                    if(data.ResultCode == "200"){
                        var MemberUrl = data.Url;
                        layer.msg("密码设置成功"),
                            setTimeout(function(){window.location=MemberUrl;},600);
                    }else if(data.ResultCode == "100"){
                        layer.msg('保存失败,请重新提交！');
                        return
                    }else if(data.ResultCode == "101"){
                        layer.msg('密码格式错误！');
                        return
                    }else if(data.ResultCode == "102"){
                        layer.msg('请求异常，请稍后再试！');
                        return
                    }else if(data.ResultCode == "103"){
                        layer.msg('账号错误！');
                        return
                    }else if(data.ResultCode == "104"){
                        layer.msg('两次密码不一致！');
                        return
                    }
                }, 'json');
            }
        });
    }

    $("#birthday").on('focus',function () {
        var BirthDate = $(this).val();
        $(this).attr('value',BirthDate);
    });

    //城市选择
    $("#CitySet").click(function (e) {
        SelCity(this,e);
    });

    //编辑按钮
    $('.im_s .EditeBtn').click(function(){
        $('.im_s').addClass('hidden');
        $('.im_e').removeClass('hidden');
        // $('.im_e input').W_Format(ErrTips);
    })

    $('#CitySet').on('blur',function () {
        $(this).removeClass('v_error');
        $(this).nextAll('.valid').addClass('hide');
    })


    //保存按钮
    $('.im_e .EditeBtn').click(function(){
        if(!verifyCheck._click()) return;
        // $('.im_e input').W_Format(ErrTips);
        // $('.im_e input').blur();
        if($('.im_e b.red').size()<1){
            var AddressArr=$('.im_e input[name="address"]').val().split('-');
            var Province,City,Area;
            if(AddressArr.length==1){
                Province=AddressArr[0];
                City='';
                Area='';
            }else if(AddressArr.length==2){
                Province=AddressArr[0];
                City=AddressArr[1];
                Area='';
            }else if(AddressArr.length==3){
                Province=AddressArr[0];
                City=AddressArr[1];
                Area=AddressArr[2];
            }
            ajaxJson={
                'Intention':'ModifyUserInfo',
                'NickName':$('.im_e input[name="nick"]').val(),
                'RealName':$('.im_e input[name="name"]').val(),
                'Sex':$('.im_e label[name="SexChose"].rb_active').attr('val'),
                'BirthDay':$('.im_e input[name="birthday"]').val(),
                'Province':Province,
                'City':City,
                'Area':Area,
                'Address':$('.im_e input[name="particularaddress"]').val()
            };
            $.post('/userajax.html',ajaxJson,function(data){
                if(data.ResultCode=='200'){
                    $('.im_s dd').eq(0).find('em').text(ajaxJson.NickName);
                    $('.im_s dd').eq(1).find('em').text(ajaxJson.RealName);
                    if(ajaxJson.Sex==1){
                        $('.im_s dd').eq(2).find('em').text("男");
                    }else if(ajaxJson.Sex==0){
                        $('.im_s dd').eq(2).find('em').text("女");
                    }else{
                        $('.im_s dd').eq(2).find('em').text("保密");
                    }
                    $('.im_s dd').eq(3).find('em').text(ajaxJson.BirthDay);
                    $('.im_s dd').eq(4).find('em').text(ajaxJson.Province+ajaxJson.City+ajaxJson.Area+ajaxJson.Address);
                    $('.im_e').addClass('hidden');
                    $('.im_s').removeClass('hidden');
                    layer.msg('保存成功');
                }else{
                    layer.msg(data.Message);
                }
            },'json');
        }
    })

    function ErrTips(Dom,isTrue,Msg){
        $(Dom).siblings('b.red').remove();
        if(!isTrue){
            $(Dom).after('<b class="red pl10">*'+Msg+'</b>');
        }
    }
});

//图片上传裁剪方法
function imagesInput(ImgBaseData,index) {
    var ajaxData={
        'Intention':'SaveAvatar',
        'Img':ImgBaseData
    };
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
                $(".MuserLogin span.img img").attr('src',ImgBaseData);
                setTimeout(function(){
                    layer.close(index);
                },200);
                $("#show_photo img").attr('src',ImgBaseData);
            }else{
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            layer.closeAll('loading');
        }
    });
}