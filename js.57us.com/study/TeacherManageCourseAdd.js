/**
 * Created by Foliage on 2016/10/14.
 */
$(function () {
    //自定义下拉
    $('.DiySelect').inputbox({
        height:41,
        width:217
    });

    //鼠标离开验证方法
    BlurVerify();
    //弹窗推荐图片
    jQuery(".tjUppicList").slide({titCell:".hd ul",mainCell:".ulWrap",effect:"left",pnLoop:false,vis:1});
    //弹窗显示
    $(document).on('click','.AddBtn',function(){
        $(".mask").show();
        $(".AddPicPop").addClass("on");
        var num = $("#addPic li").length;
        if(num > 0){
            AddPicPopLoading();
        }else {
            $("#addPicList li").remove();
            $("#AddImgBox").removeClass('has');
            $("#addPicList").hide();
        }
    })

    //弹窗关闭
    $(".AddPicPop .close,.popBtn a.canceBtn").click(function(){
        $(".mask").hide();
        $(".AddPicPop").removeClass("on")
    })

    //课程课时
    $(document).on('click','#CoursePeriod li',function () {
        if ($(this).is('.on')) {
            $(this).removeClass('on');
        }else {
            $(this).addClass('on');
        }
    })
    //自定义课时
    $("#CustomPackage").click(function () {
        html = '<li><i></i><input type="number" value="" placeholder="自定义套餐"/><span class="closeIco"></span></li>';
        $("#CustomPackage").before(html);
        if($("#Custom li").length >= 4){
            $("#CustomPackage").hide();
        }
    })
    //删除自定义课时
    $(document).on('click','#Custom .closeIco',function () {
        $(this).parent().remove();
        if($("#Custom li").length < 4){
            $("#CustomPackage").show();
        }
    })

    //是否允许用户自定义课时
    $('.labelList label').inputbox();
    
    //添加课时标签
    $("#AddTag").click(function () {
        html = '<div class="inputBox">' +
            '<span class="closeIco"></span>' +
            '<input type="text" name="" id="" value="" placeholder="99.9%通过率" class="InsInput TipInput" />' +
            '</div>';
        $("#AddTag").before(html);
        if($("#CourseTag .inputBox").length >= 4){
            $("#AddTag").hide();
        }
    })
    //编辑时使用
    if($("#CourseTag .inputBox").length >= 4){
        $("#AddTag").hide();
    }
    //删除课时标签
    $(document).on('click','#CourseTag .closeIco',function () {
        $(this).parent().remove();
        if($("#CourseTag .inputBox").length < 4){
            $("#AddTag").show();
        }
    })

    //服务条款
    $('.tkchose label').inputbox();
    //服务标签提示
    $(".tipIco").hover(function(){
        var content=$(this).attr("data-text")
        layer.tips(content,$(this), {
            skin: 'OrderTip',
            tips: [1, '#fff'],
            time: 400000
        });
    },function(){
        layer.closeAll();
    })

    //上传图片，继续添加图片事件
    $(document).on('click','#UpPicBtn,#ContinueAddImg',function () {
        var num = Number($('#addPicList li').length) + Number($("#tjbox .on").length);
        if(num >=8){
            layer.msg('选择的图片与上传的图片不能超过8张');
        }else {
            UpImg();
            $("#head_photo").trigger('click');
        }
    })

    //选择模板图片事件
    $("#tjbox li").on('click',function () {
        var num = Number($('#addPicList li').length) + Number($("#tjbox .on").length);
        var Avatar = $('#DefaultAvatar').text();
        if ($(this).is('.on')) {
            $(this).removeClass('on');
            var a = $(this).find('img').attr('src');
            if(a == Avatar){
                $('#DefaultAvatar').empty();
            }
        }else {
            if(num >=8){
                layer.msg('选择的图片与上传的图片不能超过8张');
            }else {
                $(this).addClass('on');
            }
        }
    })

    //上传图片，点击确定事件
    $('#sureBtn').on('click',function () {
        $(".mask").hide();
        $(".AddPicPop").removeClass("on");
        // $("#addPic").empty();

        //本地图片
        var LocalImg = [];
        $('#addPicList li').each(function () {
            LocalImg.push({'img':$(this).find('img').attr('src')});
        })
        $.each(LocalImg, function(i, list) {
            html = '<li>' +
                '<div class="OtherFun">' +
                '<a href="javascript:void(0)" class="AddPicDelImg">删除</a>|' +
                '<a href="javascript:void(0)" class="ChangeImg">替换</a>|' +
                '<a href="javascript:void(0)" class="Cover">设置封面</a>' +
                '</div>' +
                '<i class="choseIco"></i>' +
                '<img src="' + list.img + '" width="200" height="150" class="localimg"/><i class="upSucess"></i>' +
                '</li>';
            $('#AddBtn').before(html);
        });

        //模板图片
        var WebImg = [];
        $('#tjbox .on').each(function () {
            WebImg.push({'img':$(this).find('img').attr('src')});
        })
        $.each(WebImg, function(i, list) {
            html = '<li>' +
                '<div class="OtherFun">' +
                '<a href="javascript:void(0)" class="AddPicDelImg">删除</a>|' +
                '<a href="javascript:void(0)" class="ChangeImg">替换</a>|' +
                '<a href="javascript:void(0)" class="Cover">设置封面</a>' +
                '</div>' +
                '<i class="choseIco"></i>' +
                '<img src="' + list.img + '" width="200" height="150" class="webimg"/><i class="upSucess"></i>' +
                '</li>';
            $('#AddBtn').before(html);
        });
        var Avatar = $('#DefaultAvatar').text();
        if(Avatar == ''){
            $("#addPic li").first().addClass('on');
        }else {
            $("#addPic li").each(function () {
                var a = $(this).find('img').attr('src');
                if(a == Avatar){
                    $(this).addClass('on');
                }
            })
        }

        $("#addPic").append('<span class="AddBtn" id="AddBtn"><i></i></span>');
        var num = $("#addPic li").length;
        if(num >=8){
            $('#AddBtn').hide();
        }
    })

    //删除图片
    $(document).on('click','.AddPicDelImg',function () {
        $(this).parent().parent().remove();
        $('#AddBtn').show();
    })

    //替换上传的图片
    $(document).on('click','.ChangeImg',function () {
        ChangeImgWave = $(this).parent().parent().index();
        UpImgB();
        $("#head_photob").trigger('click');
    })

    //设置封面
    $(document).on('click','.Cover',function () {
        $("#addPic").each(function () {
            $(this).find('.on').removeClass();
        })
        $(this).parent().parent().addClass('on');
        var Avatar =$(this).parent().parent().find('img').attr('src');
        $('#DefaultAvatar').text(Avatar);
    });

    //本地上传图片，删除方法
    $(document).on('click','.deteBtn',function () {
        $(this).parent().parent().remove();
        var num = $('#addPicList li').length;
        if(num <= 0){
            $("#AddImgBox").removeClass('has');
            $("#addPicList").hide();
        }
    })

    //提交并审核
    $("#SubmitAudit").click(function () {
        var SubmitType = $(this).attr('id');
        AjaxSubmit(SubmitType);
    })

    //保存预览
    $("#SaveView").click(function () {
        var SubmitType = $(this).attr('id');
        AjaxSubmit(SubmitType);
    })
})

var ChangeImgWave; //替换图片时定位第几个li

//有添加图片时,重新赋值
function AddPicPopLoading() {
    $("#addPicList li").remove();
    $("#tjbox li").removeAttr("class");
    var localimgnum = $("#addPic .localimg").length;
    if(localimgnum >0){
        $("#AddImgBox").addClass('has');
        $("#addPicList").show();
    }
    var localimg = [];
    $('#addPic .localimg').each(function () {
        localimg.push({'img':$(this).attr('src')});
    })
    var item;
    $.each(localimg, function(i, list) {
        item = '<li>' +
            '<div class="OtherFun"><i class="deteBtn"></i></div>' +
            '<img src="' + list.img + '" width="120" height="90" />' +
            '</li>';
        $("#ContinueAddImg").parent().before(item);
    });

    $('#addPic .webimg').each(function () {
        var a = $(this).attr('src');
        $("#tjbox img").each(function () {
            var SelectedImg = $(this).attr('src');
            if(a == SelectedImg){
                $(this).parent().addClass('on');
            }
        })
    });
}

//本地上传图片裁剪功能方法
function UpImg() {
    //上传头像
    var URL = window.URL || window.webkitURL;
    var blobURL;
    if(URL){
        var $inputImage=$("#head_photo");
        var $image;
        $inputImage.change(function () {
            var files = this.files;
            var file;
            if (files && files.length) {
                file = files[0];
                if (/^image\/\w+$/.test(file.type)) {
                    blobURL = URL.createObjectURL(file);
                    layer.open({
                        type: 1,
                        skin: 'UpAvatar',
                        area: ['486px','495px'], //宽高
                        closeBtn:0,
                        title:'图片裁剪',
                        content:"<div style=\"max-height:380px;max-width:480px;z-index: 2000\"><img src=\"\" id=\"AvatarFile\"/></div>",
                        //scrollbar:false,
                        btn: ['保存', '关闭'],
                        yes: function(index, layero){
                            //图片BASE64处理
                            var ImgBaseData = $image.cropper("getCroppedCanvas").toDataURL('image/jpeg');
                            AddImgBox(ImgBaseData);
                            layer.close(index);
                        },
                        success:function(index,layero){
                            $image=$(".UpAvatar #AvatarFile");
                            $image.one('built.cropper', function () {
                                // Revoke when load complete
                                URL.revokeObjectURL(blobURL);
                            }).cropper({
                                aspectRatio: 4 / 3,
                                minContainerHeight:380,
                                minContainerWidth:480,
                            }).cropper('replace', blobURL);
                            $inputImage.val('');
                        },
                        end:function(index,layero){
                            layer.close(index);
                        }
                    });
                }else{
                    layer.msg('请上传正确的图片');
                }
            }
        });
    }else{
        layer.msg("图片创建失败");
    }
}

//本地上传图片，后执行方法
function AddImgBox(dataimg) {
    $("#AddImgBox").addClass('has');
    $("#addPicList").show();

    html = '<li>' +
        '<div class="OtherFun"><i class="deteBtn"></i></div>' +
        '<img src="' + dataimg + '" width="120" height="90" />' +
        '</li>';
    $("#ContinueAddImg").parent().before(html);
}

//替换图片裁剪功能方法
function UpImgB() {
    //上传头像
    var URL = window.URL || window.webkitURL;
    var blobURL;
    if(URL){
        var $inputImage=$("#head_photob");
        var $image;
        $inputImage.change(function () {
            var files = this.files;
            var file;
            if (files && files.length) {
                file = files[0];
                if (/^image\/\w+$/.test(file.type)) {
                    blobURL = URL.createObjectURL(file);
                    layer.open({
                        type: 1,
                        skin: 'UpAvatar',
                        area: ['486px','495px'], //宽高
                        closeBtn:0,
                        title:'图片裁剪',
                        content:"<div style=\"max-height:380px;max-width:480px;z-index: 2000\"><img src=\"\" id=\"AvatarFile\"/></div>",
                        //scrollbar:false,
                        btn: ['保存', '关闭'],
                        yes: function(index, layero){
                            //图片BASE64处理
                            var ImgBaseData = $image.cropper("getCroppedCanvas").toDataURL('image/jpeg');
                            ChangeImg(ImgBaseData);
                            layer.close(index);
                        },
                        success:function(index,layero){
                            $image=$(".UpAvatar #AvatarFile");
                            $image.one('built.cropper', function () {
                                // Revoke when load complete
                                URL.revokeObjectURL(blobURL);
                            }).cropper({
                                aspectRatio: 4 / 3,
                                minContainerHeight:380,
                                minContainerWidth:480,
                            }).cropper('replace', blobURL);
                            $inputImage.val('');
                        },
                        end:function(index,layero){
                            layer.close(index);
                        }
                    });
                }else{
                    layer.msg('请上传正确的图片');
                }
            }
        });
    }else{
        layer.msg("图片创建失败");
    }
}

//替换图片方法
function ChangeImg(dataimg) {
    $("#addPic li").eq(ChangeImgWave).find('img').remove();
    $("#addPic li").eq(ChangeImgWave).find('.upSucess').before('<img src="' + dataimg + '" width="200" height="150" class="localimg"/>');
    if ($("#addPic li").eq(ChangeImgWave).is('.on')) {
        $("#DefaultAvatar").text(dataimg);
    }
}

//ajax提交
function AjaxSubmit(SubmitType) {
    //自定义套餐参数
    var CoursePeriod = [];
    $('#CoursePeriod .on').each(function () {
        CoursePeriod.push({'Course':$(this).find('input').val()});
    })

    //课程标签参数
    var CourseTag = [];
    $('#CourseTag input').each(function () {
        CourseTag.push({'CourseTag':$(this).val()});
    })

    //课程图片参数
    var CourseImg = [];
    $('#addPic li').each(function () {
        CourseImg.push({'Img':$(this).find('img').attr('src')});
    })

    ajaxData = {
        'ID':$('.AddServiceBoxT .name').data('id'), //编辑时的id
        'Intention': 'AddCourse', //方法
        'SubmitType':SubmitType, //提交类型 SubmitAudit提交审核 SaveView保存并预览
        'CourseName':$("#CourseName").val(), //课程名称
        'TrainSubject':$("#TrainSubject input").val(), //培训科目 TOEFL IELTS SAT ACT GRE GMAT PTE
        'FormClass':$("#FormClass input").val(), //上课方式 1线上 2线下
        'ClassSize':$("#ClassSize input").val(), //班级规模 1小班 2大班 3一对一
        'CoursePrice':$("#CoursePrice").val(),  //课程价格
        'CoursePeriod':CoursePeriod,    //课程课时
        'WhetherCourse':$("#WhetherCourse .rb_active").attr('val'), //允许用户自定义课时 1表示允许 2表示不允许
        'CourseTag':CourseTag, //课程标签
        'CourseIntroduction':$("#CourseIntroduction").val(), //课程简介
        'CourseImg':CourseImg, //课程图片
        'CourseDefaultImg':$("#addPic .on").index(), //封面图片是第几个
        'CourseDetails':ue.getContent(), //服务详情
    }

    if(ajaxData.CourseName == ''){
        $("#CourseName").parent().addClass('ErroBox');
        $("#CourseName").next().text('您还没有填写课程名称');
        W_ScrollTo($("#CourseName"),+100);
        return
    }else if(ajaxData.CoursePrice == ''){
        $("#CoursePrice").parent().addClass('ErroBox');
        $("#CoursePrice").next().text('您还没有填写课程价格');
        W_ScrollTo($("#CoursePrice"),+100);
        return
    }else if(rule.Num2.test(ajaxData.CoursePrice) != true){
        $("#CoursePrice").parent().addClass('ErroBox');
        $("#CoursePrice").next().text('课程价格为正整数');
        W_ScrollTo($("#CoursePrice"),+100);
        return;
    }else if(ajaxData.CourseIntroduction.length <1){
        $("#CourseIntroduction").parent().addClass('ErrBox');
        $("#CourseIntroduction").next().text('您还没有填写课程简介');
        W_ScrollTo($("#CourseIntroduction"),+100);
        return;
    }else if(ajaxData.CourseIntroduction.length >90){
        $("#CourseIntroduction").parent().addClass('ErrBox');
        $("#CourseIntroduction").next().text('课程简介字数不能超过90个字(包括符号)');
        W_ScrollTo($("#CourseIntroduction"),+100);
        return;
    }else if(ajaxData.CourseImg.length <= 0){
        layer.msg("课程图片至少要添加一张");
        W_ScrollTo($("#addPic"),+100);
        return
    }else if(ajaxData.CourseDetails == ''){
        layer.msg("您还没有填写课程详情");
        W_ScrollTo($("#addPic"),+100);
        return
    }

    $.ajax({
        type: "post",
        dataType: "json",
        url: "/teachermanageajax/",
        data: ajaxData,
        beforeSend: function () {
            //提交加载效果
            public_loading();
        },
        success: function(data) {
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location=data.Url
                },1500);
            }else{
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            $("#public_loading").remove();
        }
    });
}

//鼠标离开验证方法
function BlurVerify() {
    $(".AddServiceBoxM input,.AddServiceBoxM textarea").mouseup(function () {
        $(this).parent().removeClass('ErroBox');
    })

    $("#CourseName").blur(function () {
        if($(this).val().length < 1){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('您还没有填写课程名称');
        }else{
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#CoursePrice").blur(function () {
        if($(this).val().length < 1){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('您还没有填写课程价格');
        }else if(rule.Num.test($(this).val()) != true){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('课程价格为正整数');
        }else {
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#CourseIntroduction").blur(function () {
        if($(this).val().length < 1){
            $(this).parent().addClass('ErrBox');
            $(this).next().text('您还没有填写课程简介');
        }
    })
}