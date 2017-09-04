$(function(){
    //切换效果
    $(".AddpassengHd a").click(function(){
        var num = $(this).index();
        $(this).addClass("on").siblings().removeClass("on");
        $(this).parents(".AddpassengBox").find("table").addClass("hidden").eq(num).removeClass("hidden")
    })
    function ErrTips(Dom,isTrue,Msg){
        $(Dom).siblings('b.red').remove();
        if(!isTrue){
            $(Dom).after('<b class="red pl10">*'+Msg+'</b>');
        }
    }

    $("#BirthDate").on('focus',function () {
        var BirthDate = $(this).val();
        $(this).attr('value',BirthDate);
        $(this).blur(function(){
            $(this).siblings('b.red').remove();
        });
    });

    $("#ValidityStart").on('focus',function () {
        var BirthDate = $(this).val();
        $(this).attr('value',BirthDate);
        $(this).blur(function(){
            $(this).siblings('b.red').remove();
        });
    });

    $("#ValidityEnd").on('focus',function () {
        var BirthDate = $(this).val();
        $(this).attr('value',BirthDate);
        $(this).blur(function(){
            $(this).siblings('b.red').remove();
        });
    });

    $('#SaveBtna').click(function () {
        if(!verifyCheck._click()){
            if($('#PingX').is('.v_error')){
                // $(this).next('.valid')
                $('#PingX').nextAll('.icon-sucessfill').addClass('hide');
                $('#PingX').nextAll('.valid').addClass('error');
                $('#PingX').nextAll('.valid').append('姓不能为空');
            }
            if($('#PingM').is('.v_error')){
                $('#PingM').nextAll('.icon-sucessfill').addClass('hide');
            }
            if($('#EnNameX').is('.v_error')){
                // $(this).next('.valid')
                $('#EnNameX').nextAll('.icon-sucessfill').addClass('hide');
                $('#EnNameX').nextAll('.valid').addClass('error');
                $('#EnNameX').nextAll('.valid').append('姓不能为空');
            }
            if($('#EnNameM').is('.v_error')){
                $('#EnNameM').nextAll('.icon-sucessfill').addClass('hide');
            }
            return;
        }
        $('#index1').addClass('hidden');
        $('#page1').removeClass('on');
        $('#index2').removeClass('hidden');
        $('#page2').addClass('on');
    })

    //保存按钮
    $('#SaveBtnb').click(function(){
        if(!verifyCheck._click()) return;
        if($('.mt20 b.red').size()<1){
            ajaxJson={
                'Intention':'PassengerSave',
                'ID' : $('#ID').val(),
                'CnName':$('#CnName').val(),
                'PingX':$('#PingX').val(),
                'PingM':$('#PingM').val(),
                'EnNameX':$('#EnNameX').val(),
                'EnNameM':$('#EnNameM').val(),
                'Sex':$('.mt20 label[name="check"].rb_active').attr('val'),
                'BirthDate':$('#BirthDate').val(),
                'Age':$('#Age').val(),
                'Tel':$('#Tel').val(),
                'CredentialsNO':$('#CredentialsNO').val(),
                'ValidityStart':$('#ValidityStart').val(),
                'ValidityEnd':$('#ValidityEnd').val(),
                'Issue':$('#Issue').val(),
            };
            $.post('/userajax.html',ajaxJson,function(data){
                layer.msg(data.Message);
                location.href = data.Url;
            },'json');
        }
        else {
            return false;
        }
    })
});