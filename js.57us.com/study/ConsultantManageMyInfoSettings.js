/**
 * Created by Foliage on 2016/10/12.
 */

//个人设置第一个页面
$(function () {
    //自定义下拉
    $('.DiySelect').inputbox({
        height:41,
        width:122
    });
    //单选
    $('.ItemLabelList label').inputbox();

    $("#UpAvatar").click(function () {
        UpImg();
        $("#head_photo").trigger('click');
    })

    //城市联动
    comSelect();
    selectCity();
    //选择国家切换时
    $("#Country a").click(function () {
        var Country = $("#Country input").val();
        if(Country == '中国'){
            $("#usopts").hide();
            $("#cnopts").show();
        }else if(Country == '美国'){
            $("#cnopts").hide();
            $("#usopts").show();
        }
    })

    if($("#Country input").val() == '美国'){
        $("#cnopts").hide();
        $("#usopts").show();
    }

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
    //我的标签增加
    $("#AddTag").click(function () {
        html = '<div class="inputBox">'+
            '<span class="closeIco"></span>'+
            '<input type="text" name="" value="" placeholder="99.9%通过率" class="InsInput TipInput Tag" maxlength="6">'+
            '</div>';
        $("#AddTag").before(html);
        if($("#MyTag .inputBox").length >=4){
            $("#AddTag").hide();
        }
    })
    //我的标签删除
    $(document).on('click','.closeIco',function () {
        $(this).parent().remove();
        if($("#MyTag .inputBox").length <4){
            $("#AddTag").show();
        }
    });
    //性别选择
    $('.labelList label').inputbox();

    //基本信息ajax提交
    $("#Index1Sava").click(function () {
        Index1Ajax();
    })
})

function Index1Ajax() {
    //城市
    var Country = $("#Country input").val();
    if(Country == '中国'){
        var City = {
            'Country':$("#Country input").val(),
            'Province':$("#province span em").text(),
            'City':$("#city span em").text(),
        }
    }else if(Country == '美国'){
        var City = {
            'Country':$("#Country input").val(),
            'City':$("#usinput").val(),
        }
    }
    //我的标签
    var MyTag = [];
    $('#MyTag .Tag').each(function () {
        MyTag.push({'tag':$(this).val()});
    })
    //ajax提交数据
    ajaxData ={
        'Intention': 'ConsultantMyInfoIndex1',
        'Name':$("#Name").val(),
        'Nickname':$("#Nickname").val(),
        'City':City,
        'Sex':$("#Sex .rb_active").attr('val'),
        'ServiceManifesto':$("#ServiceManifesto").val(),
        'MyTag':MyTag,
    }
    if(ajaxData.Name == ''){
        layer.msg('姓名不能为空');
        return
    }else if(ajaxData.Name == ''){
        layer.msg('昵称不能为空');
        return
    }else if(ajaxData.City.Country == '中国'){
        if(ajaxData.City.Province == '请选择省份'){
            layer.msg('请选择所在省份');
            return
        }else if(ajaxData.City.City == '请选择城市'){
            layer.msg('请选择所在城市');
            return
        }
    }else if(ajaxData.City.Country == '美国'){
        if(ajaxData.City.City == ''){
            layer.msg('请输入所在地区');
            return
        }
    }
    //下一步页面显示、隐藏操作
    $.post("/consultantmanageajax/",ajaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
            $("#Index1").hide();
            $("#Index2").show();
            $("#InforProcess li:eq(0)").addClass('has');
            $("#InforProcess li:eq(1)").addClass('on');
        }else {
            layer.msg(data.Message);
        }
    },'json');
}

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
                            UpAvatar(ImgBaseData);
                            layer.close(index);
                        },
                        success:function(index,layero){
                            $image=$(".UpAvatar #AvatarFile");
                            $image.one('built.cropper', function () {
                                // Revoke when load complete
                                URL.revokeObjectURL(blobURL);
                            }).cropper({
                                aspectRatio:1,
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

function UpAvatar(ImgBaseData) {
    var AJaxData = {
        'Intention': 'ConsultantMyInfoUpImg',
        'Img': ImgBaseData,
    }
    $("#InforImg").find('img').attr('src',ImgBaseData);
    $.post("/consultantmanageajax/",AJaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}

//个人设置第二个页面
$(document).ready(function(){
    //从业经历点击增加
    $("#AddExpr").click(function () {
        var numIco = Number($(".numIco:last").text()) + Number('1');
        html ='<div class="rightBox cf">' +
            '<i class="numIco">'+numIco+'</i>' +
            '<div class="InforListLi cf">' +
            '<p class="left"><span class="red">*</span>时间：</p>' +
            '<div class="right">' +
            '<div class="inputBox"><input placeholder="起始时间" type="text" name="StartDate" value="" class="InsInput tac StartDate" style="width: 116px;" onfocus="WdatePicker({skin:\'whyGreen\',maxDate:\'%y-%M-%d\'})" readonly/></div>' +
            '<span class="fl pl10 f18 c9">—</span>' +
            '<div class="inputBox pl10"><input placeholder="结束时间" type="text" name="EndDate" value="" class="InsInput tac EndDate" style="width: 116px;" onfocus="WdatePicker({skin:\'whyGreen\',maxDate:\'%y-%M-%d\'})" readonly/></div>' +
            '</div>' +
            '</div>' +
            '<div class="InforListLi cf">' +
            '<p class="left"><span class="red">*</span>就职公司：</p>' +
            '<div class="inputBox"><input placeholder="" type="text" name="Company" value="" class="InsInput Company" style="width:493px;" /></div>' +
            '</div>' +
            '<div class="InforListLi cf">' +
            '<p class="left"><span class="red">*</span>从业经历：<span class="c9 chos"></span></p>' +
            '<div class="inputBox"><textarea class="textare Undergo" name="Undergo" rows="" cols="" placeholder="简要描述您在该公司的工作经历等，有助于学生更好的了解你！" style="height:151px;width:478px;"></textarea></div>' +
            '</div>' +
            '</div>' ;
        $("#AddExpr").parent().before(html);
        $("#DelExpr").show();
    })

    //从业经历点击删除
    $("#DelExpr").click(function () {
        $("#WorkExperience .rightBox:last").remove();
        if($("#WorkExperience .rightBox").length <=1){
            $("#DelExpr").hide();
        }
    })

    //返回第一个页面
    $("#PrevIndex1").click(function () {
        $("#Index2").hide();
        $("#Index1").show();
        $("#InforProcess li:eq(0)").attr('class','on');
        $("#InforProcess li:eq(1)").removeClass('on');
    })
    //第二个页面ajax提交保存
    $("#Index2Sava").click(function () {
        Index2Ajax();
    })
});

function Index2Ajax() {
    var WorkExperience =[];
    $("#WorkExperience .rightBox").each(function () {
        WorkExperience.push({'startdate':$(this).find('.StartDate').val(),'enddate':$(this).find('.EndDate').val(),'company':$(this).find('.Company').val(),'undergo':$(this).find('.Undergo').val()});
    })
    var serviceProject = [];
    $("#ItemChose .checked").each(function () {
        serviceProject.push($(this).attr('val'));
    })
    var ajaxData ={
        'Intention': 'ConsultantMyInfoIndex2',
        'Experience':$("#Experience").val(),
        'Introduction':$("#Introduction").val(),
        'ServiceProject':serviceProject,
        'WorkExperience':WorkExperience,
    }
    if(ajaxData.Experience == ''){
        console.log(ajaxData.Experience);
        layer.msg('请输入从业经验');
        W_ScrollTo($("#Experience"),+60);
        return
    }else if(ajaxData.Introduction == ''){
        layer.msg('请输入自我介绍');
        W_ScrollTo($("#Introduction"),+60);
        return
    }
    $(".StartDate").each(function () {
        var StartDate = $(this).val();
        if(StartDate == ''){
            layer.msg('请选择起始时间');
            W_ScrollTo($(this),+100);
            return
        }
    })

    $(".EndDate").each(function () {
        var EndDate = $(this).val();
        if(EndDate == ''){
            layer.msg('请选择结束时间');
            W_ScrollTo($(this),+100);
            return
        }
    })

    $(".Company").each(function () {
        var Company = $(this).val();
        if(Company == ''){
            layer.msg('请输入就职公司');
            W_ScrollTo($(this),+100);
            return
        }
    })

    $(".Undergo").each(function () {
        var Undergo = $(this).val();
        if(Undergo == ''){
            layer.msg('请输入就业经验');
            W_ScrollTo($(this),+60);
            return
        }
    })

    $.post("/consultantmanageajax/",ajaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
            $("#Index2").hide();
            $("#Index3").show();
            $("#InforProcess li:eq(1)").attr('class','has');
            $("#InforProcess li:eq(2)").addClass('on');
        }else {
            layer.msg(data.Message);
        }
    },'json');
}

//个人设置第三个页面
$(document).ready(function(){
    //身份证图片上传
    $("#AddPortraits").click(function () {
        $("#addpreview").trigger('click');
    })
    $("#addpreview").change(function() {
        preview(this);
    })
    if($("#PicPortraits li").length > 0){
        $("#AddPortraits").hide();
    }

    //身份证删除
    $(document).on('click','.DelPortraits',function () {
        $(this).parent().parent().remove();
        $("#AddPortraits").show();
    })

    //身份证替换
    $(document).on('click','.EditPortraits',function () {
        $("#addpreview").trigger('click');
        $(this).parent().parent().remove();
    })

    //返回第二个页面
    $("#PrevIndex2").click(function () {
        $("#Index3").hide();
        $("#Index2").show();
        $("#InforProcess li:eq(1)").attr('class','on');
        $("#InforProcess li:eq(2)").removeClass('on');
    })

    //第三页面ajax提交
    $("#Index3Sava").click(function () {
        Index3Ajax();
    })
})

function Index3Ajax() {
    ajaxData ={
        'Intention': 'ConsultantMyInfoIndex3',
        'IdCard':$("#IdCard").val(),
        'CardImg':$("#PicPortraits li").find('img').attr('src'),
    }
    if(ajaxData.IdCard == ''){
        layer.msg('请输入身份证号码');
        return
    }else if(rule.card.test(ajaxData.IdCard) != true){
        layer.msg('身份证号码格式不正确');
        return
    }else if(ajaxData.CardImg == undefined){
        layer.msg('请上传身份证图片');
        return
    }

    $.ajax({
        type: "post",
        dataType: "json",
        url: "/consultantmanageajax/",
        data: ajaxData,
        beforeSend: function () {
            //提交加载效果
            public_loading();
        },
        success: function(data) {
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                $("#Index3").hide();
                $("#Index4").show();
                $("#UpAvatar").hide();
                $("#InforProcess li:eq(2)").attr('class','has');
                $("#InforProcess li:eq(3)").addClass('on');
            }else {
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            $("#public_loading").remove();
        }
    });
}

//肖像单张图片上传方法
function preview(file) {
    if(file.files && file.files[0]) {
        var reader = new FileReader();
        //判断上传枨是否正确
        if(file.files[0].type != "image/jpeg" && file.files[0].type != "image/png" && file.files[0].type != "image/gif" && file.files[0].type != "image/bmp"){
            layer.alert('选择文件错误,图片类型必须是<span style="color: red">jpeg,jpg,png,gif,bmp中的一种</span>');
            $("#AddPortraits").show();
            return;
        }else if(file.files[0].size > 512 * 10240){  //判断图片是否大于512kb
            layer.alert('请不要上传大于512kb的图片');
            $("#AddPortraits").show();
            return;
        }
        reader.onload = function(evt) {
            html = '<li>' +
                ' <div class="OtherFun"><a href="javascript:void(0)" class="DelPortraits">删除</a>|<a href="javascript:void(0)" class="EditPortraits">替换</a></div>' +
                '<img src="'+ evt.target.result +'" width="200" height="150" />' +
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

//第四个页面
$(document).ready(function() {
    if(suffix == 'cn') {
        var InforUrl = "http://study.57us.cn/consultantmanage/myinfoauditview/";
    } else if(suffix == 'com') {
        var InforUrl = "http://study.57us.com/consultantmanage/myinfoauditview/";
    }else if(suffix == 'net'){
        var InforUrl = "http://study.57us.net/consultantmanage/myinfoauditview/";
    }
    $("#checkInfor").click(function(){
        layer.open({
            type: 2,
            title: '个人资料',
            shadeClose: true,
            shade: 0.8,
            area: ['980px', '90%'],
            content: InforUrl
        });
    })
});
