/**
 * Created by Foliage on 2016/10/17.
 */

$(function () {
    //头像上传
    $("#InforImg img").click(function () {
        $("#UpAvatar").trigger('click');
    })
    $("#UpAvatar").click(function () {
        UpImg();
        $("#head_photo").trigger('click');
    })

    //自定义下拉
    $('.DiySelect').inputbox({
        height:41,
        width:122
    });
    //单选
    $('.ItemLabelList label').inputbox();
    //城市联动
    comSelect();
    selectCity();
    //选择国家切换时
    $("#Country a").click(function () {
        var Country = $("#Country input").val();
        if(Country == 'cn'){
            $("#usopts").hide();
            $("#cnopts").show();
        }else if(Country == 'us'){
            $("#cnopts").hide();
            $("#usopts").show();
        }
    })
    //左侧菜单点击对应显示隐藏
    $("#LeftMenu li").click(function () {
        $(this).addClass("on").siblings().removeClass("on");
        if($(this).attr('id') == 'BasicInfo'){
            $("#Index1").show();
            $("#Index2,#Index3").hide();
        }else if($(this).attr('id') == 'Resume'){
            $("#Index2").show();
            $("#Index1,#Index3").hide();
        }else if($(this).attr('id') == 'IdVerifiCation'){
            $("#Index3").show();
            $("#Index1,#Index2").hide();
        }
    })

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

    //标签初始化时如果大于等于4隐藏增加标签按钮
    if($("#MyTag .inputBox").length >=4){
        $("#AddTag").hide();
    }
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

//基本信息提交审核
function Index1Ajax() {
    //城市
    var Country = $("#Country input").val();
    if(Country == 'cn'){
        var City = {
            'Country':$("#Country input").val(),
            'Province':$("#province span em").text(),
            'City':$("#city span em").text(),
        }
    }else if(Country == 'us'){
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
        'Intention': 'ConsultantManageApproveIndex1',
        'Name':$("#Name").val(),
        'Nickname':$("#Nickname").val(),
        'City':City,
        'Sex':$("#Sex .rb_active").attr('val'),
        'ServiceManifesto':$("#ServiceManifesto").val(),
        'MyTag':MyTag,
    }
    $.post("/consultantmanageajax/",ajaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
            setTimeout(function () {
                window.location = data.Url;
            }, 400);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}

//图片上传方法
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
    $.post("/ajaxstudyconmanage.html",AJaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}

//背景资料
$(document).ready(function(){
    //从业经历点击增加
    $("#AddExpr").click(function () {
        var numIco = Number($(".numIco:last").text()) + Number('1');
        html ='<div class="rightBox cf">' +
            '<i class="numIco">'+numIco+'</i>' +
            '<div class="InforListLi cf">' +
            '<p class="left">时间：</p>' +
            '<div class="right">' +
            '<div class="inputBox"><input placeholder="起始时间" type="text" name="StartDate" value="" class="InsInput tac StartDate" style="width: 116px;" onfocus="WdatePicker({maxDate:\'%y-%M-%d\'})" readonly/></div>' +
            '<span class="fl pl10 f18 c9">—</span>' +
            '<div class="inputBox pl10"><input placeholder="结束时间" type="text" name="EndDate" value="" class="InsInput tac EndDate" style="width: 116px;" onfocus="WdatePicker({maxDate:\'%y-%M-%d\'})" readonly/></div>' +
            '</div>' +
            '</div>' +
            '<div class="InforListLi cf">' +
            '<p class="left">就职公司：</p>' +
            '<div class="inputBox"><input placeholder="" type="text" name="Company" value="" class="InsInput Company" style="width:493px;" /></div>' +
            '</div>' +
            '<div class="InforListLi cf">' +
            '<p class="left">从业经历：<span class="c9 chos">（300字以内）</span></p>' +
            '<div class="inputBox"><textarea class="textare Undergo" name="Undergo" rows="" cols="" placeholder="简要描述您在该公司的工作经历等，有助于学生更好的了解你！" style="height:151px;width:478px;"></textarea></div>' +
            '</div>' +
            '</div>' ;
        $("#AddExpr").before(html);
        $("#DelExpr").show();
    })

    //从业经历点击删除
    $("#DelExpr").click(function () {
        $("#WorkExperience .rightBox:last").remove();
        if($("#WorkExperience .rightBox").length <=1){
            $("#DelExpr").hide();
        }
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
        'Intention': 'ConsultantManageApproveIndex2',
        'Experience':$("#Experience").val(),
        'Introduction':$("#Introduction").val(),
        'ServiceProject':serviceProject,
        'WorkExperience':WorkExperience,
    }

    $.post("/ajaxstudyconmanage.html",ajaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
            setTimeout(function () {
                window.location = data.Url;
            }, 400);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}