/**
 * Created by Foliage on 2016/10/14.
 */
$(function () {
    //服务列表初始化加载
    CaseLeftAjax();

    //展示，草稿箱点击
    $("#CaseColumn li").click(function () {
        $("#CaseColumn li").removeClass();
        $(this).addClass('on');
        CaseLeftAjax();
    })

    //左侧学生切换
    $(document).on('click','#CaseLeft .CaseListHeader',function () {
        $(this).parent().addClass("on").siblings().removeClass("on");
        $(".CaseListBtn a").removeClass('on');
        $(this).parent().find('.CaseListBtn a:eq(0)').addClass('on');
        CaseSeeDetails();
    })

    //点击左侧查看详情
    $(document).on('click','.CaseListBtn .SeeDetails',function () {
        $(this).addClass("on").siblings().removeClass("on");
        CaseSeeDetails();
    })

    //点击左侧编辑
    $(document).on('click','.CaseListBtn .Edit',function () {
        $(this).addClass("on").siblings().removeClass("on");
        CaseEdit();
    })


    //左侧列表点击下架
    $(document).on('click','.Unshelve',function () {
        $(this).addClass("on").siblings().removeClass("on");
        var CaseID = $(this).parent().attr('data-id');
        CaseUnshelve(CaseID);
    })

    //左侧列表点击上架
    $(document).on('click','.CaseAdded',function () {
        $(this).addClass("on").siblings().removeClass("on");
        var CaseID = $(this).parent().attr('data-id');
        CaseAdded(CaseID);
    })
})

function CaseLeftAjax() {
    ajaxData ={
        'Intention': 'SuccessCaseList',
        'CaseColumn':$("#CaseColumn .on").attr('data-type'), //2代表已展示的 1代表草稿箱
    };
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/teachermanageajax/",  //提交地址/consultantmanageajax/
        data: ajaxData,
        beforeSend: function () { //加载过程效果
            // $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                SuccessCaseList(data);
            }else if(data.ResultCode == "100"){
                FailureCaseList()
            }else {
                layer.msg(data.Message);

            }
        },
        complete: function () { //加载完成提示
            // $("#loading").hide();
        }
    });
}

//左侧列表有数据时显示
function SuccessCaseList(data) {
    $("#NoServiceBox").hide();
    $("#addCase").show();
    $("#CaseView").show();
    $("#CaseLeft").empty();
    var item;
    $.each(data.CaseListData, function(i, list) {
        item = '<li class="transition">' +
            '<div class="CaseListHeader">' +
            '<img src="'+list.PicPortraits+'" width="96" height="96" />' +
            '<p class="name">'+list.StudentName+'</p>' +
            '<p class="">科目：'+list.TrainSubject+'</p>' +
            '</div>' +
            '<div class="CaseListIns">' +
            '<p class="InsLis">培训类别：'+list.TrainCategory+'</p>' +
            '<p class="InsLis">培训后成绩：'+list.TrainPreScore+'</p>' +
            '<p class="InsLis">培训前成绩：'+list.TrainHouScore+'</p>' +
            '</div>' +
            '<div class="CaseListBtn" data-id="'+list.CaseID+'">' +
            '<a href="javascript:void(0)" class="i1 transition SeeDetails">查看详情</a>' +
            '<a href="javascript:void(0)" class="transition Edit">编辑</a>' +
            '<a href="javascript:void(0)" class="transition Unshelve">下架</a>' +
            '</div>' +
            '</li>';
        $('#CaseLeft').append(item);
    });
    $("#CaseLeft li:eq(0)").addClass('on');
    $("#CaseLeft li:eq(0)").find('.SeeDetails').addClass('on');
    if($("#CaseColumn .on").attr('id') == "Draft"){
        $("#CaseLeft .CaseListBtn a:eq(2)").text('上架');
        $("#CaseLeft .CaseListBtn a:eq(2)").attr('class','transition CaseAdded');
    }
    CaseSeeDetails();
}

//左侧列表无数据时显示
function FailureCaseList() {
    $("#CaseView").hide();
    $("#addCase").hide();
    $("#NoServiceBox").show();
    $("#NoServiceBox").empty();
    html = '<div class="NoService mt50">' +
        '<i class="noIco"></i>' +
        '<p class="tit">您还没有已展示的案例</p>' +
        '<a href="/teachermanage/successcaseadd/" class="Nbtn mt15">立即新建案例<i></i></a>' +
        '</div>';
    $("#NoServiceBox").append(html);
    if($("#CaseColumn .on").attr('id') == "Draft"){
        $("#NoServiceBox").find('.tit').text('草稿箱空空如也');
    }
}

//左侧案例列表，查看详情
function CaseSeeDetails() {
    ajaxData = {
        'Intention': 'CaseDetails',
        'CaseID':$(".CaseListBtn .on").parent().attr('data-id'),
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/teachermanageajax/",  //提交地址/teachermanageajax/
        data: ajaxData,
        beforeSend: function () { //加载过程效果
            // $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                SuccessCaseSeeDetails(data);
            }else {
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            // $("#loading").hide();
        }
    });
}

//查看详情请求成功
function SuccessCaseSeeDetails(data) {
    $("#CaseRight").empty();
    html = '<div class="cf caseShow">' +
        '<div class="FirstCont cf">' +
        '<img src="'+data.PicPortraits+'" width="72" height="72">' +
        '<p>'+data.StudentName+'</p>' +
        '<p>科目：'+data.TrainSubject+'</p>' +
        '</div>' +
        '<div class="SecondCont cf">' +
        '<div class="caseOtherBox cf">' +
        '<div class="caseOtherBoxT"><img src="http://images.57us.com/img/study/caseT1.png"></div>' +
        '<div class="caseOtherM mt15">' +
        '<ul class="studentCaseList">' +
        '<li>学生姓名：'+data.StudentName+'</li>' +
        '<li>培训科目：'+data.TrainSubject+'</li>' +
        '<li>培训类别：'+data.TrainCategory+'</li>' +
        '<li>培训后成绩：'+data.TrainPreScore+'</li>' +
        '<li>培训前成绩：'+data.TrainHouScore+'</li>' +
        '<li>就读学校：'+data.AttendSchool+'</li>' +
        '</ul>' +
        '</div>' +
        '</div>' +
        '<div class="caseOtherBox">' +
        '<div class="caseOtherBoxT"><img src="http://images.57us.com/img/study/caseT4.png" /></div>' +
        '<div class="OfferScroll">' +
        '<a href="JavaScript:void(0)" class="prev"></a>' +
        '<a href="JavaScript:void(0)" class="next"></a>' +
        '<div class="ScrollMain">' +
        '<ul class="pic" id="PicScore">' +
        '</ul>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="SecondLine"></div>' +
        '<div class="caseOtherBox">' +
        '<div class="caseOtherBoxT"><img src="http://images.57us.com/img/study/caseT5.png"></div>' +
        '<div class="cf Casedescr">' +
        '<div class="mt25 cf">' +
        '<p class="summary mt10">'+data.StudentSurvey+'</p>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="caseOtherBox">' +
        '<div class="caseOtherBoxT"><img src="http://images.57us.com/img/study/caseT3.png"></div>' +
        '<div class="cf Casedescr">' +
        '<div class="mt25 cf">' +
        '<p class="summary mt10">'+data.CaseDescription+'</p>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';
    $("#CaseRight").append(html);
    if(data.PicScore != null){
        var item;
        $.each(data.PicScore, function(){
            item = '<li><img src="'+this+'" width="320" height="225" /></li>';
            $("#PicScore").append(item)
        });
    }
    //右侧成功案例
    jQuery(".OfferScroll").slide({titCell:"",mainCell:".ScrollMain .pic",autoPage:true,effect:"leftLoop",autoPlay:true,vis:2});
}

// 左侧点击编辑时方法
function CaseEdit() {
    ajaxData = {
        'Intention': 'CaseEdit',
        'CaseID':$(".CaseListBtn .on").parent().attr('data-id'),
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/teachermanageajax/",  //提交地址/teachermanageajax/
        data: ajaxData,
        beforeSend: function () { //加载过程效果
            // $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                SuccessCaseEdit(data)
            }else {
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            // $("#loading").hide();
        }
    });
}

function SuccessCaseEdit(data) {
    $("#CaseRight").empty();
    html = '<div class="consCaseEdite">' +
        '<div class="AddServiceBoxT"><span class="name fl">编辑案例</span></div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">学生姓名：</p>' +
        '<input type="hidden" name="CaseID" id="CaseID" value="'+data.CaseID+'"/>' +
        '<div class="inputBox">' +
        '<input type="text" name="StudentName" id="StudentName" value="'+data.StudentName+'" placeholder="不方便留全名，可：刘同学或L同学" class="InsInput">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">培训科目：</p>' +
        '<div type="selectbox" class="DiySelect diytearch otherDiy fl" id="TrainSubject">' +
        '<div class="opts">' +
        '<a href="javascript:void(0);" value="托福">托福</a>' +
        '<a href="javascript:void(0);" value="雅思">雅思</a>' +
        '<a href="javascript:void(0);" value="SAT">SAT</a>' +
        '<a href="javascript:void(0);" value="ACT">ACT</a>' +
        '<a href="javascript:void(0);" value="GRE">GRE</a>' +
        '<a href="javascript:void(0);" value="GMAT">GMAT</a>' +
        '<a href="javascript:void(0);" value="PTE">PTE</a>' +
        '<a href="javascript:void(0);" value="其它">其它</a>' +
        '</div>' +
        '</div>' +
        '<div class="inputBox pl10 hidden">' +
        '<input type="text" name="TrainSubjectCustom" id="TrainSubjectCustom" value="" placeholder="" class="InsInput" style="width: 85px;">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">就读学校：<span class="c9 chos">（选填）</span></p>' +
        '<div class="inputBox">' +
        '<input type="text" name="AttendSchool" id="AttendSchool" value="'+data.AttendSchool+'" placeholder="" class="InsInput">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">培训类别：</p>' +
        '<div type="selectbox" class="DiySelect diytearch fl otherDiy" id="TrainCategory">' +
        '<div class="opts">' +
        '<a href="javascript:void(0);" value="阅读">阅读</a>' +
        '<a href="javascript:void(0);" value="口语">口语</a>' +
        '<a href="javascript:void(0);" value="写作">写作</a>' +
        '<a href="javascript:void(0);" value="听力">听力</a>' +
        '<a href="javascript:void(0);" value="其它">其它</a>' +
        '</div>' +
        '</div>' +
        '<div class="inputBox pl10 hidden">' +
        '<input type="text" name="TrainCategoryCustom" id="TrainCategoryCustom" value="" placeholder="" class="InsInput" style="width: 85px;">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl" style="width: 96px;">培训前成绩：<span class="c9 chos">（选填）</span></p>' +
        '<div class="inputBox">' +
        '<input type="text" name="TrainPreScore" id="TrainPreScore" value="'+data.TrainPreScore+'" placeholder="不同的学校用逗号隔开" class="InsInput" style="width: 230px">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl" style="width: 96px;">培训后成绩：</p>' +
        '<div class="inputBox">' +
        '<input type="text" name="TrainHouScore" id="TrainHouScore" value="'+data.TrainHouScore+'" placeholder="不方便留全名，可：刘同学或L同学" class="InsInput" style="width: 230px;">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList cf" style="width: 100%;">' +
        '<p class="left fl" style="width: 112px;">添加学生头像：<span class="c9 chos">（选填）</span></p>' +
        '<div class="right fl">' +
        '<p class="mt10 red">如果没有学生照片，可以添加录取学校的校徽，请尽量清晰，不能超过 5M</p>' +
        '<ul class="addPic mt15" id="PicPortraits">' +
        '<li>' +
        '<div class="OtherFun"><a href="javascript:void(0)" class="DelPortraits">删除</a>|<a href="javascript:void(0)" class="EditPortraits">替换</a></div>' +
        '<img src="'+data.PicPortraits+'" width="150" height="150"><i class="upSucess"></i>' +
        '</li>' +
        '<span class="AddBtn" id="AddPortraits" style="display:none"><i></i></span>' +
        '<input type="file" id="addpreview" style="display: none"/>' +
        '</ul>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList cf" style="width: 100%;">' +
        '<p class="left fl" style="width: 120px;">添加学生成绩：<span class="c9 chos">（选填）</span></p>' +
        '<div class="right fl">' +
        '<p class="mt10 red"></p>' +
        '<ul class="addPic mt15" id="PicOffer">' +
        '<span class="AddBtn" id="AddOfferImg"><i></i></span>'+
        '<input type="file" id="addOfferpreview" style="display: none"/>'+
        '</ul>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList mt20 cf" style="width: 100%;">' +
        '<p class="left fl">学生反馈：<span class="c9 chos">（选填）<b class="pl20"></b></span></p>' +
        '<div class="right fl">' +
        '<textarea class="textare" name="StudentSurvey" id="StudentSurvey" rows="" cols="" placeholder="总体介绍服务该学生的过程或者遇到的问题并如何解决。">'+data.StudentSurvey+'</textarea>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList cf" style="width: 100%;">' +
        '<p class="left fl">案例描述：</p>' +
        '<div class="right fl">' +
        '<textarea class="textare" name="CaseDescription" id="CaseDescription" rows="" cols="" placeholder="简单介绍学生的优势，让有同样优势的学生看到。">'+data.CaseDescription+'</textarea>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList cf" style="width: 100%;">' +
        '<p class="left fl"></p>' +
        '<div class="right fl" id="CaseSaveBtn">' +
        '<a href="javascript:void(0)" class="submitChange pushBtn publicBtn1 fr" id="ImmediatelyShow">立即展示</a>' +
        '</div>' +
        '</div>' +
        '</div>';
    $("#CaseRight").append(html);
    //自定义下拉
    $('.DiySelect').inputbox({
        height:41,
        width:150
    });
    //培训科目赋值
    $("#TrainSubject .selected").text(data.TrainSubject);
    $("#TrainSubject input").val(data.TrainSubject);
    if(data.TrainSubject != 'TOEFL' || data.TrainSubject != 'IELTS' || data.TrainSubject != 'SAT' || data.TrainSubject != 'ACT' || data.TrainSubject != 'GRE' || data.TrainSubject != 'GMAT' || data.TrainSubject != 'PTE'){
        $("#TrainSubjectCustom").parent().removeClass('hidden');
        $("#TrainSubject .selected").text('其它');
        $("#TrainSubject input").val('其它');
        $("#TrainSubjectCustom").val(data.TrainSubject);
    }
    //培训类别赋值
    $("#TrainCategory .selected").text(data.TrainCategory);
    $("#TrainCategory input").val(data.TrainCategory);
    if(data.TrainSubject != '阅读' || data.TrainSubject != '口语' || data.TrainSubject != '写作' || data.TrainSubject != '听力'){
        $("#TrainCategoryCustom").parent().removeClass('hidden');
        $("#TrainCategory .selected").text('其它');
        $("#TrainCategory input").val('其它');
        $("#TrainCategoryCustom").val(data.TrainCategory);
    }
    //当学生成绩有图片时，赋值
    if(data.PicScore != null){
        var item;
        $.each(data.PicScore, function(){
            item = '<li>' +
                '<div class="OtherFun"><a href="javascript:void(0)" class="DelOffer">删除</a>|<a href="javascript:void(0)" class="EditOffer">替换</a></div>' +
                '<img src="' + this + '" width="200" height="150" />' +
                '</li>';
            $("#AddOfferImg").before(item);
        });
    }
    //当图片大于等于8张时隐藏新增图片按钮
    if(data.PicScore != null){
        if(data.PicScore.length >= 8){
            $("#AddOfferImg").hide();
        }
    }
    if($("#CaseColumn .on").attr('id') == 'Draft'){
        $("#CaseSaveBtn").html('<a href="javascript:void(0)" class="submitChange publicBtn0 fr" id="DraftRelease">立即发布</a><a href="javascript:void(0)" class="submitChange publicBtn0 fr" id="DraftSave">保存</a>');
    }
    CaseImgEdit();
}

function CaseImgEdit() {
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
    AddOfferImg();
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

    //立即展示
    $("#ImmediatelyShow").click(function () {
        var SubmitType = $(this).attr('id');
        SubmitAjax(SubmitType);
    })

    //草稿箱立即发布
    $("#DraftRelease").click(function () {
        var SubmitType = $(this).attr('id');
        SubmitAjax(SubmitType);
    })

    //草稿箱保存
    $("#DraftSave").click(function () {
        var SubmitType = $(this).attr('id');
        SubmitAjax(SubmitType);
    })
}
// offer替换时定位li第几个
var OffetImgWave;

function SubmitAjax(SubmitType) {
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
        'SubmitType':SubmitType, //提交类型  ImmediatelyShow 立即展示  DraftRelease 草稿箱立即发布  DraftSave 草稿箱保存
        'CaseID':$("#CaseID").val(), //当前编辑案例的ID
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
            // $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
            }else{
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            // $("#loading").hide();
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
        }else if(file.files[0].size > 512 * 10){  //判断图片是否大于512kb
            layer.alert('请不要上传大于512kb的图片');
            $("#AddPortraits").show();
            return;
        }
        reader.onload = function(evt) {
            var _data = evt.target.result.split(';')[1];
            var _img = 'data:image/jpeg;'+_data;
            html = '<li>' +
                ' <div class="OtherFun"><a href="javascript:void(0)" class="DelPortraits">删除</a>|<a href="javascript:void(0)" class="EditPortraits">替换</a></div>' +
                '<img src="'+ _img +'" width="200" height="150" />' +
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
        }else if(file.files[0].size > 512 * 10){  //判断图片是否大于5kb
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
function AddOfferImg() {
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
}

//左侧案例列表下架
function CaseUnshelve(CaseID) {
    ajaxData = {
        'Intention': 'CaseUnshelve',
        'CaseID':CaseID,
    }
    $.post("/teachermanageajax/",ajaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
            setTimeout(function () {
                window.location.reload();
            }, 600);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}

//左侧案例列表上架
function CaseAdded(CaseID) {
    ajaxData = {
        'Intention': 'CaseAdded',
        'CaseID':CaseID,
    }
    $.post("/teachermanageajax/",ajaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
            setTimeout(function () {
                window.location.reload();
            }, 600);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}