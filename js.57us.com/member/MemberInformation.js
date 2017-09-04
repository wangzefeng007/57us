/**
 * Created by Foliage on 2017/2/8.
 */
$(function () {
    //表单美化
    $('.sexChose .cbt').inputbox();
    $('.diyselect').inputbox({
        height:40,
        width:156
    });

    //城市选择
    $("#CitySet").click(function (e) {
        SelCity(this,e);
    });

    //编辑修改事件
    $("#modification").on('click',function () {
        $("#index1").hide();
        $("#index2").show();
    })

    //编辑取消事件
    $("#cancel").on('click',function () {
        $("#index1").show();
        $("#index2").hide();
    })

    //编辑保存事件
    $("#save").on('click',function () {
        //ajax提交参数
        var _region = $("#CitySet").val().split('-');
        var ajaxData = {
            'Intention':'ModifyUserInfo', //方法名
            'NickName':$('#NickName').val(), //昵称
            'RealName':$('#RealName').val(), //真实姓名
            'Sex':$('#Sex .rb_active').attr('val'), //性别
            'Occupation':$("#Occupation input").val(),//职业
            'BirthDay':$('#BirthDay').val(), //生日
            'Province':_region[0], //省份
            'City':_region[1], //城市
            'Area':_region[2], //县城
            'Address':$('#Address').val(), //详细地址
        };

        //提交前的验证
        if(ajaxData.NickName == ''){
            layer.msg('昵称不能为空');
            return
        }else if(ajaxData.NickName.length<3 || ajaxData.NickName.length>20){
            layer.msg("昵称长度3-20位");
            return
        }else if(ajaxData.RealName == ''){
            layer.msg("姓名不能为空");
            return
        }else if(ajaxData.RealName.length<2 || ajaxData.RealName.length >8){
            layer.msg("姓名长度2-8位");
            return
        }else if(rule.NameZH.test(ajaxData.RealName) != true){
            layer.msg("姓名只能输入中文");
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
                        window.location.reload();
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

//图片上传裁剪方法
function imagesInput(ImgBaseData,index) {
    var ajaxData={
        'Intention':'SaveAvatar',
        'Img':ImgBaseData,
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