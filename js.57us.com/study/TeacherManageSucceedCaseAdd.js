/**
 * Created by Foliage on 2016/10/14.
 */
$(function () {
    //自定义下拉
    $('.DiySelect').inputbox({
        height:41,
        width:150
    });

    //培训类别,等于其它时显示自定义输入
    $(".otherDiy a").click(function(){
        if($(this).attr('value') == '其它'){
            $(this).parents(".otherDiy").siblings(".inputBox").removeClass("hidden");
        }else{
            $(this).parents(".otherDiy").siblings(".inputBox").addClass("hidden");
        }
    })

    //学生肖像上传
    $("#AddPortraits").click(function () {
        $("#addpreview").trigger('click');
    })
    $("#addpreview").change(function() {
        preview(this);
    })

    //学生肖像删除
    $(document).on('click','.DelPortraits',function () {
        $(this).parent().parent().remove();
        $("#AddPortraits").show();
    })

    //学生肖像替换
    $(document).on('click','.EditPortraits',function () {
        $("#addpreview").trigger('click');
        $(this).parent().parent().remove();
    })

    //Offer删除
    $(document).on('click','.DelOffer',function () {
        $(this).parent().parent().remove();
        if($("#PicOffer li").length < 8){
            $('#AddOfferImg').show();
        }
    })

    //Offer替换
    $(document).on('click','.EditOffer',function () {
        $("#addOfferpreview").trigger('click');
        OffetImgWave = $(this).parent().parent().index();
    })
    $("#addOfferpreview").change(function() {
        offerpreview(this);
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

//offer替换时定位li第几个
var OffetImgWave;

//肖像单张图片上传方法
function preview(file) {
    if(file.files && file.files[0]) {
        var reader = new FileReader();
        //判断上传枨是否正确
        if(file.files[0].type != "image/jpeg" && file.files[0].type != "image/png" && file.files[0].type != "image/gif" && file.files[0].type != "image/bmp"){
            layer.alert('选择文件错误,图片类型必须是<span style="color: red">jpeg,jpg,png,gif,bmp中的一种</span>');
            $("#AddPortraits").show();
            return;
        }else if(file.files[0].size > 512 *10){  //判断图片是否大于512kb
            layer.alert('请不要上传大于512kb的图片');
            $("#AddPortraits").show();
            return;
        }
        reader.onload = function(evt) {
            var _data = evt.target.result.split(';')[1];
            var _img = 'data:image/jpeg;'+_data;
            html = '<li>' +
                ' <div class="OtherFun"><a href="javascript:void(0)" class="DelPortraits">删除</a>|<a href="javascript:void(0)" class="EditPortraits">替换</a></div>' +
                '<img src="'+ _img +'" width="150" height="150" />' +
                '</li>';
            $("#AddPortraits").before(html);
            $("#AddPortraits").hide();
        }
        reader.readAsDataURL(file.files[0]);
    } else { //ie6-8时使用滤镜方式显示
        html = '<li>' +
            ' <div class="OtherFun"><a href="javascript:void(0)" class="DelPortraits">删除</a>|<a href="javascript:void(0)" class="EditPortraits">替换</a></div>' +
            '<div class="img" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></div>' +
            '</li>';
        $("#AddPortraits").before(html);
    }
}

//Offer替换方法
function offerpreview(file) {
    if(file.files && file.files[0]) {
        var reader = new FileReader();
        //判断上传枨是否正确
        if(file.files[0].type != "image/jpeg" && file.files[0].type != "image/png" && file.files[0].type != "image/gif" && file.files[0].type != "image/bmp"){
            layer.alert('选择文件错误,图片类型必须是<span style="color: red">jpeg,jpg,png,gif,bmp中的一种</span>');
            return;
        }else if(file.files[0].size > 512 * 10){  //判断图片是否大于512kb
            layer.alert('请不要上传大于512kb的图片');
            return;
        }
        reader.onload = function(evt) {
            var _data = evt.target.result.split(';')[1];
            var _img = 'data:image/jpeg;'+_data;
            $("#PicOffer li").eq(OffetImgWave).find('img').attr('src',_img);
        }
        reader.readAsDataURL(file.files[0]);
    } else { //ie6-8时使用滤镜方式显示
        $("#PicOffer li").eq(OffetImgWave).find('img').attr('src','');
        $("#PicOffer li").eq(OffetImgWave).find('img').attr('style','filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'');
    }
}

//多张图片上传方法
var addImg = document.getElementById("AddOfferImg");
var PapersUp=new plupload.Uploader({
    browse_button: addImg, //触发文件选择对话框的按钮，为那个元素id
    url: '/Controller/ZuFang/upload.php',//ajaxUrl + "?Intention=ReleaseInfo", //服务器端的上传页面地址
    flash_swf_url: 'Moxie.swf', //swf文件，当需要使用swf方式进行上传时需要配置该参数
    // silverlight_xap_url: 'Moxie.xap', //silverlight文件，当需要使用silverlight方式进行上传时需要配置该参数
    // multi_selection:false,
    filters: {
        mime_types: [ //只允许上传图片文件
            {
                title: "图片文件",
                extensions: "jpg,gif,png,bmp"
            }
        ]
    },
    max_file_size : '5000kb', //最大只能上传400kb的文件
    prevent_duplicates : true,
});
//在实例对象上调用init()方法进行初始化
PapersUp.init();
//绑定各种事件，并在事件监听函数中做你想做的事
PapersUp.bind('FilesAdded', function(uploader, files) {
    var num = $("#PicOffer li").length + files.length;
    if(num > 8){
        layer.alert('学生Offer展示图片数量最多为8张,您可以精心挑选后再上传');
        return;
    }
    for (var i = 0; i < files.length; i++) {
        ImgTo64(files[i],function(imgsrc){
            var _data = imgsrc.split(';')[1];
            var _img = 'data:image/jpeg;'+_data;
            html = '<li>' +
                '<div class="OtherFun"><a href="javascript:void(0)" class="DelOffer">删除</a>|<a href="javascript:void(0)" class="EditOffer">替换</a></div>' +
                '<img src="' + _img + '" width="200" height="150" />' +
                '</li>';
            $("#AddOfferImg").before(html);
            if($("#PicOffer li").length >= 8){
                $('#AddOfferImg').hide();
            }
        });
    };
});

function AjaxSubmit(SubmitType) {
    //肖像参数
    var PicPortraits = [];
    $('#PicPortraits li').each(function () {
        PicPortraits.push({'Img':$(this).find('img').attr('src')});
    });

    //培训科目参数
    if($("#TrainSubject input").val() == '其它'){
        var TrainSubject = $("#TrainSubjectCustom").val();
    }else {
        var TrainSubject = $("#TrainSubject input").val();
    }

    //培训类型参数
    if($("#TrainCategory input").val() == '其它'){
        var TrainCategory = $("#TrainCategoryCustom").val();
    }else {
        var TrainCategory = $("#TrainCategory input").val();
    }

    //offer参数
    var PicScore = [];
    $('#PicOffer li').each(function () {
        PicScore.push($(this).find('img').attr('src'));
    });

    // ajax提交参数
    ajaxData = {
        'Intention': 'SuccessCaseAdd', //方法
        'SubmitType':SubmitType, //提交类型  SubmitAudit立即发布 SaveView保存并预览
        'StudentName':$("#StudentName").val(), //学生姓名
        'TrainSubject':TrainSubject, //培训科目 TOEFL IELTS SAT ACT GRE GMAT PTE 其他时用户自定义参数
        'AttendSchool':$("#AttendSchool").val(), //就读学校
        'TrainCategory':TrainCategory, //培训类型 阅读 口语 写作 听力 其它时用户自定义参数
        'TrainPreScore':$("#TrainPreScore").val(), //培训前成绩
        'TrainHouScore':$("#TrainHouScore").val(), //培训后成绩
        'PicPortraits':PicPortraits, //学生头像
        'PicScore':PicScore, //学生成绩
        'StudentSurvey':$("#StudentSurvey").val(), //学生反馈
        'CaseDescription':$("#CaseDescription").val() //案例描述
    }

    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/teachermanageajax/",  //提交地址
        data: ajaxData,
        beforeSend: function () { //加载过程效果
            //提交加载效果
            public_loading();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                setTimeout(function(){
                    window.location=data.Url;
                },3000);
            }else{
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            $("#public_loading").remove();
        }
    });
};