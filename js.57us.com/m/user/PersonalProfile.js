$(function(){
    
    $(".sexyChose label").on('click',function () {
        $(".sexyChose label").removeClass('on');
        $(this).addClass('on');
    })

    $(".getSolution").click(function(){
        var ajaxData = {
            'Intention':'PersonalProfile',
            'NickName':$("#NickName").val(),
            'Sex':$('.sexyChose .on').attr('data-type'),
            'City':$('#city-picker').val(),
            'Mobile':$("#Mobile").val(),
            'Email':$("#Email").val(),
        }

        if(ajaxData.NickName == ''){
            $.toast('请输入昵称');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            $.toast('请输入正确的手机号码');
            return
        }
        $.post("/ajax/",ajaxData,function(data){
            //200=成功 其它都是=异常
            if(data.ResultCode == "200"){
                $.toast(data.Message);
                setTimeout(function(){
                    window.location.reload();
                },300);
            }else{
                $.toast(data.Message);
            }
        },'json');
    });
})