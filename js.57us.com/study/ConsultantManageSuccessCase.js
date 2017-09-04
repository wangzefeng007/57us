/**
 * Created by Foliage on 2016/10/11.
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
        var Status = '1';
        var CaseID = $(this).parent().attr('data-id');
        CaseUnshelve(CaseID,Status);
    })

    //左侧列表点击上架
    $(document).on('click','.CaseAdded',function () {
        $(this).addClass("on").siblings().removeClass("on");
        var Status = '2';
        var CaseID = $(this).parent().attr('data-id');
        CaseUnshelve(CaseID,Status);
    })
})

function CaseLeftAjax() {
    ajaxData ={
        'Intention': 'SuccessCaseList',
        'Status':'2',
        'CaseColumn':$("#CaseColumn .on").attr('data-type'),
    };
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/ajaxstudyconmanage.html",  //提交地址/consultantmanageajax/
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
    $("#CaseView").show();
    $("#CaseLeft").empty();
    var item;
    $.each(data.CaseListData, function(i, list) {
        item = '<li class="transition aaa">' +
            '<div class="CaseListHeader">' +
            '<img src="'+list.StudentImage+'" width="96" height="96" />' +
            '<p class="name">'+list.StudentName+'</p>' +
            '<p class="">'+list.ApplySeason+'</p>' +
            '</div>' +
            '<div class="CaseListIns">' +
            '<p class="InsLis"><b>录取院校：</b>'+list.AdmissionSchool+'</p>' +
            '<p class="InsLis mt10"><b>申请院校：</b>'+list.ApplySchool+'</p>' +
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
    $("#NoServiceBox").show();
    $("#NoServiceBox").empty();
    html = '<div class="NoService mt50">' +
        '<i class="noIco"></i>' +
        '<p class="tit">您还没有已展示的案例</p>' +
        '<a href="/consultantmanage/addsuccesscase/" class="Nbtn mt15">立即新建案例<i></i></a>' +
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
        url: "/ajaxstudyconmanage.html",  //提交地址/consultantmanageajax/
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
        '<img src="'+data.StudentImage+'" width="72" height="72">' +
        '<p>'+data.StudentName+'</p>' +
        '<p>'+data.ApplySeason+'</p>' +
        '</div>' +
        '<div class="SecondCont cf">' +
        '<div class="caseOtherBox cf">' +
        '<div class="caseOtherBoxT"><img src="http://images.57us.com/img/study/caseT1.png"></div>' +
        '<div class="caseOtherM mt15">' +
        '<table border="0" cellspacing="0" cellpadding="0" width="100%" class="insTab"><tbody>'+
        '<tr>'+
        '<td width="89">学生姓名：</td>'+
        '<td width="360">'+data.StudentName+'</td>'+
        '<td width="89">申请季：</td>'+
        '<td width="360">'+data.ApplySeason+'</td>'+
        '</tr>'+
        '<tr>'+
        '<td>录取院校：</td>'+
        '<td>'+data.AdmissionSchool+'</td>'+
        '<td>申请院校：</td>'+
        '<td>'+data.ApplySchool+'</td>'+
        '</tr>'+
        '<tr>'+
        '<td>入读院校：</td>'+
        '<td>'+data.AttendSchool+'</td>'+
        '<td>奖学金：</td>'+
        '<td>'+data.Scholarship+'</td>'+
        '</tr>'+
        '<tr>'+
        '<td>录取专业：</td>'+
        '<td>'+data.AdmissionSpecialty+'</td>'+
        '<td>背景院校：</td>'+
        '<td>'+data.OnSchool+'</td>'+
        '</tr>'+
        '<td>背景专业：</td>'+
        '<td>'+data.OnSpecialty+'</td>'+
        '<td></td>'+
        '<td></td>'+
        '</tr></tbody></table>'+
        '</div>' +
        '<table border="0" cellspacing="1" cellpadding="0" width="100%" class="langeScrol">' +
        '<tbody>' +
        '<tr height="60">' +
        '<th>GPA</th>' +
        '<th>托福</th>' +
        '<th>雅思</th>' +
        '<th>GRE</th>' +
        '<th>GMAT</th>' +
        '<th>SAT</th>' +
        '<th>SSAT</th>' +
        '<th>ACT</th>' +
        '</tr>' +
        '<tr height="50">' +
        '<td>'+data.GPA+'</td>' +
        '<td>'+data.TOEFL+'</td>' +
        '<td>'+data.IELTS+'</td>' +
        '<td>'+data.GRE+'</td>' +
        '<td>'+data.GMAT+'</td>' +
        '<td>'+data.SAT+'</td>' +
        '<td>'+data.SSAT+'</td>' +
        '<td>'+data.ACT+'</td>' +
        '</tr>' +
        '</tbody>' +
        '</table>' +
        '</div>' +
        '<div class="caseOtherBox">' +
        '<div class="caseOtherBoxT"><img src="http://images.57us.com/img/study/caseT2.png" /></div>' +
        '<div class="OfferScroll">' +
        '<a href="JavaScript:void(0)" class="prev"></a>' +
        '<a href="JavaScript:void(0)" class="next"></a>' +
        '<div class="ScrollMain">' +
        '<ul class="pic" id="OfferPic">' +
        '</ul>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="SecondLine"></div>' +
        '<div class="caseOtherBox">' +
        '<div class="caseOtherBoxT"><img src="http://images.57us.com/img/study/caseT3.png"></div>' +
        '<div class="cf Casedescr">' +
        '<div class="mt25 cf">' +
        '<p class="f18">优势分析</p>' +
        '<p class="summary mt10">'+data.Advantage+'</p>' +
        '</div>' +
        '<div class="mt25 cf">' +
        '<p class="f18">劣势分析</p>' +
        '<p class="summary mt10">'+data.Disadvantage+'</p>' +
        '</div>' +
        '<div class="mt25 cf">' +
        '<p class="f18">申请总结</p>' +
        '<p class="summary mt10">'+data.ApplySummary+'</p>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';
    $("#CaseRight").append(html);
    if(data.OfferImage){
        var item;
        $.each(data.OfferImage, function(){
            item = '<li><img src="'+this+'" width="320" height="225" /></li>';
            $("#OfferPic").append(item)
        });
    }

    //右侧成功案例
    jQuery(".OfferScroll").slide({titCell:"",mainCell:".ScrollMain .pic",autoPage:true,effect:"leftLoop",autoPlay:true,vis:2});
}

//左侧点击编辑时方法
function CaseEdit() {
    ajaxData = {
        'Intention': 'CaseDetails',
        'CaseID':$(".CaseListBtn .on").parent().attr('data-id'),
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/ajaxstudyconmanage.html",  //提交地址/consultantmanageajax/
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
        '<div class="AddServiceBoxT">' +
        '<span class="name fl">编辑案例</span>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">学生姓名：</p>' +
        '<div class="inputBox">' +
        '<input type="text" name="StudentName" id="StudentName" value="'+data.StudentName+'" placeholder="不方便留全名，可：刘同学或L同学" class="InsInput">' +
        '<div class="erroText"></div>' +
        '<input type="hidden" name="CaseID" id="CaseID" value="'+data.CaseID+'">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">申请季：</p>' +
        '<div class="inputBox">' +
        '<input type="text" name="ApplySeason" id="ApplySeason" value="'+data.ApplySeason+'" placeholder="" class="InsInput">' +
        '<div class="erroText"></div>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList full">' +
        '<p class="left fl">录取院校：</p>' +
        '<div class="inputBox">' +
        '<input type="text" name="AdmissionSchool" id="AdmissionSchool" value="'+data.AdmissionSchool+'" placeholder="不同的学校用逗号隔开" class="InsInput">' +
        '<div class="erroText"></div>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList full">' +
        '<p class="left fl">申请学校：</p>' +
        '<div class="inputBox">' +
        '<input type="text" name="ApplySchool" id="ApplySchool" value="'+data.ApplySchool+'" placeholder="不同的学校用逗号隔开" class="InsInput">' +
        '<div class="erroText"></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">入读院校：<span class="c9 chos">（选填）</span></p>' +
        '<div class="inputBox">' +
        '<input type="text" name="AttendSchool" id="AttendSchool" value="'+data.AttendSchool+'" placeholder="" class="InsInput">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">奖学金：<span class="c9 chos">（选填）</span></p>' +
        '<div class="inputBox">' +
        '<input type="text" name="Scholarship" id="Scholarship" value="'+data.Scholarship+'" placeholder="" class="InsInput">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList full">' +
        '<p class="left fl">录取专业：<span class="c9 chos">（选填）</span></p>' +
        '<div class="inputBox">' +
        '<input type="text" name="AdmissionSpecialty" id="AdmissionSpecialty" value="'+data.AdmissionSpecialty+'" placeholder="不同的学校用逗号隔开" class="InsInput">' +
        '</div>' +
        '</div>' +
        '<table border="0" cellspacing="1" cellpadding="0" width="100%" class="langeScrol">' +
        '<tbody>' +
        '<tr height="60">' +
        '<th>GPA</th>' +
        '<th>托福</th>' +
        '<th>雅思</th>' +
        '<th>GRE</th>' +
        '<th>GMAT</th>' +
        '<th>SAT</th>' +
        '<th>SSAT</th>' +
        '<th>ACT</th>' +
        '</tr>' +
        '<tr height="50">' +
        '<td><input type="text" name="GPA" id="GPA" value="'+data.GPA+'"  class="studentScrol"/></td>' +
        '<td><input type="text" name="TOEFL" id="TOEFL" value="'+data.TOEFL+'"  class="studentScrol"/></td>' +
        '<td><input type="text" name="IELTS" id="IELTS" value="'+data.IELTS+'"  class="studentScrol"/></td>' +
        '<td><input type="text" name="GRE" id="GRE" value="'+data.GRE+'"  class="studentScrol"/></td>' +
        '<td><input type="text" name="GMAT" id="GMAT" value="'+data.GMAT+'"  class="studentScrol"/></td>' +
        '<td><input type="text" name="SAT" id="SAT" value="'+data.SAT+'"  class="studentScrol"/></td>' +
        '<td><input type="text" name="SSAT" id="SSAT" value="'+data.SSAT+'"  class="studentScrol"/></td>' +
        '<td><input type="text" name="ACT" id="ACT" value="'+data.ACT+'"  class="studentScrol"/></td>' +
        '</tr>' +
        '</tbody>' +
        '</table>' +
        '<div class="cf mt15 full fl">' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">就读院校：<span class="c9 chos">（选填）</span></p>' +
        '<div class="inputBox currentBox">' +
        '<input type="text" name="OnSchool" id="OnSchool" value="'+data.OnSchool+'" placeholder="" class="InsInput">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList">' +
        '<p class="left fl">就读专业：<span class="c9 chos">（选填）</span></p>' +
        '<div class="inputBox">' +
        '<input type="text" name="OnSpecialty" id="OnSpecialty" value="'+data.OnSpecialty+'" placeholder="" class="InsInput">' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList cf" style="width: 100%;">' +
        '<p class="left fl" style="width: 112px;">添加学生头像：<span class="c9 chos">（选填）</span></p>' +
        '<div class="right fl">' +
        '<p class="mt10 red">如果没有学生照片，可以添加录取学校的校徽，请尽量清晰，不能超过 5M</p>' +
        '<ul class="addPic addPic1 mt15" id="PicPortraits">' +
        '<li>' +
        '<div class="OtherFun"><a href="javascript:void(0)" class="DelPortraits">删除</a>|<a href="javascript:void(0)" class="EditPortraits">替换</a></div>' +
        '<img src="'+data.StudentImage+'" width="150" height="150"><i class="upSucess"></i>' +
        '</li>' +
        '<span class="AddBtn" id="AddPortraits" style="display:none"><i></i></span>' +
        '<input type="file" id="addpreview" style="display: none"/>' +
        '</ul>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList cf" style="width: 100%;">' +
        '<p class="left fl" style="width: 120px;">添加学生Offer：<span class="c9 chos">（选填）</span></p>' +
        '<div class="right fl">' +
        '<p class="mt10 red"></p>' +
        '<ul class="addPic mt15" id="PicOffer">' +
        '<span class="AddBtn" id="AddOfferImg"><i></i></span>'+
        '<input type="file" id="addOfferpreview" style="display: none"/>'+
        '</ul>' +
        '</div>' +
        '</div>' +
        '<div class="AddServiceBoxT">' +
        '<span class="name green f20 fl">案例分析</span>' +
        '</div>' +
        '<div class="AddCaseList mt20 cf" style="width: 100%;">' +
        '<p class="left fl">优势分析：<span class="c9 chos">（选填）<b class="pl20"></b></span></p>' +
        '<div class="right fl">' +
        '<textarea class="textare" name="Advantage" id="Advantage" rows="" cols="" placeholder="总体介绍服务该学生的过程或者遇到的问题并如何解决。">'+data.Advantage+'</textarea>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList cf" style="width: 100%;">' +
        '<p class="left fl">劣势分析：<span class="c9 chos">（选填）<b class="pl20"></b></span></p>' +
        '<div class="right fl">' +
        '<textarea class="textare" name="Disadvantage" id="Disadvantage" rows="" cols="" placeholder="简单介绍学生的劣势，全面分析学生。">'+data.Disadvantage+'</textarea>' +
        '</div>' +
        '</div>' +
        '<div class="AddCaseList cf" style="width: 100%;">' +
        '<p class="left fl">申请总结：<span class="c9 chos">（选填）<b class="pl20"></b></span></p>' +
        '<div class="right fl">' +
        '<div class="inputBox">' +
        '<textarea class="textare" name="ApplySummary" id="ApplySummary" rows="" cols="" placeholder="简单介绍学生的优势，让有同样优势的学生看到。">'+data.ApplySummary+'</textarea>' +
        '<div class="erroText"></div>' +
        '</div>' +
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
    if(data.OfferImage != null){
        var item;
        $.each(data.OfferImage, function(){
            item = '<li>' +
                '<div class="OtherFun"><a href="javascript:void(0)" class="DelOffer">删除</a>|<a href="javascript:void(0)" class="EditOffer">替换</a></div>' +
                '<img src="' + this + '" width="200" height="150" />' +
                '</li>';
            $("#AddOfferImg").before(item);
        });
        if(data.OfferImage.length >= 8){
            $("#AddOfferImg").hide();
        }
    }
    if($("#CaseColumn .on").attr('id') == 'Draft'){
        $("#CaseSaveBtn").html('<a href="javascript:void(0)" class="submitChange pushBtn publicBtn1 fr" id="DraftRelease">立即发布</a><a href="javascript:void(0)" class="submitChange saveBtn publicBtn0 fr" id="DraftSave">保存</a>');
    }
    CaseImgEdit();

    //鼠标离开验证
    BlurVerify();
}

function CaseImgEdit() {
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
//offer替换时定位li第几个
var OffetImgWave;

function SubmitAjax(SubmitType) {
    //肖像参数
    var PicPortraits = [];
    $('#PicPortraits li').each(function () {
        PicPortraits.push({'Img':$(this).find('img').attr('src')});
    });
    //offer参数
    var PicOffer = [];
    $('#PicOffer li').each(function () {
        PicOffer.push({'Img':$(this).find('img').attr('src')});
    });

    //ajax提交参数
    ajaxData = {
        'Intention': 'SaveSuccessCase',
        'SubmitType':SubmitType,
        'CaseID':$("#CaseID").val(),
        'StudentName':$("#StudentName").val(),
        'ApplySeason':$("#ApplySeason").val(),
        'AdmissionSchool':$("#AdmissionSchool").val(),
        'ApplySchool':$("#ApplySchool").val(),
        'AttendSchool':$("#AttendSchool").val(),
        'Scholarship':$("#Scholarship").val(),
        'AdmissionSpecialty':$("#AdmissionSpecialty").val(),
        'GPA':$("#GPA").val(),
        'TOEFL':$("#TOEFL").val(),
        'IELTS':$("#IELTS").val(),
        'GRE':$("#GRE").val(),
        'GMAT':$("#GMAT").val(),
        'SAT':$("#SAT").val(),
        'SSAT':$("#SSAT").val(),
        'ACT':$("#ACT").val(),
        'OnSchool':$("#OnSchool").val(),
        'OnSpecialty':$("#OnSpecialty").val(),
        'PicPortraits':PicPortraits,
        'PicOffer':PicOffer,
        'Advantage':$("#Advantage").val(),
        'Disadvantage':$("#Disadvantage").val(),
        'ApplySummary':$("#ApplySummary").val(),
    }
    //提交验证
    if(ajaxData.StudentName.length<1){
        $("#StudentName").parent().addClass('ErroBox');
        $("#StudentName").next().text('您还没有填写学生姓名');
        W_ScrollTo($("#StudentName"),+100);
        return;
    }else if(ajaxData.ApplySeason<1){
        $("#ApplySeason").parent().addClass('ErroBox');
        $("#ApplySeason").next().text('您还没有填申请季');
        W_ScrollTo($("#ApplySeason"),+100);
        return;
    }else if(ajaxData.AdmissionSchool<1){
        $("#AdmissionSchool").parent().addClass('ErroBox');
        $("#AdmissionSchool").next().text('您还没有填写录取院校');
        W_ScrollTo($("#AdmissionSchool"),+100);
        return;
    }else if(ajaxData.ApplySchool<1){
        $("#ApplySchool").parent().addClass('ErroBox');
        $("#ApplySchool").next().text('您还没有填申请读院校');
        W_ScrollTo($("#ApplySchool"),+100);
        return;
    }else if(ajaxData.ApplySummary<1){
        $("#ApplySummary").parent().addClass('ErroBox');
        $("#ApplySummary").next().text('您还没有填写申请总结');
        W_ScrollTo($("#ApplySummary"),+100);
        return;
    }

    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "/ajaxstudyconmanage.html",  //提交地址
        data: ajaxData,
        beforeSend: function () { //加载过程效果
            // $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                if(SubmitType == 'DraftRelease'){
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                }
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
        }else if(file.files[0].size > 512 * 10){  //判断图片是否大于5Mb
            layer.alert('请不要上传大于512KB的图片');
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
        }else if(file.files[0].size > 512 * 10){  //判断图片是否大于5Mb
            layer.alert('请不要上传大于512KB的图片');
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
function CaseUnshelve(CaseID,Status) {
    ajaxData = {
        'Intention': 'UpdateCaseStatus',
        'Status':Status,
        'CaseID':CaseID,
    }
    $.post("/ajaxstudyconmanage.html",ajaxData,function(data){
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

//鼠标离开验证方法
function BlurVerify() {
    $(".AddServiceBox input,.AddServiceBox textarea").mouseup(function () {
        $(this).parent().removeClass('ErroBox');
    })

    $("#StudentName").blur(function () {
        if($(this).val().length < 1){
            $(this).parent().addClass('ErroBox');
            $(this).next().html('您还没有填写学生姓名');
        }else{
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#ApplySeason").blur(function () {
        if($(this).val().length < 1){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('您还没有填申请季');
        }else{
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#AdmissionSchool").blur(function () {
        if($(this).val().length < 1){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('您还没有填写录取院校');
        }else{
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#ApplySchool").blur(function () {
        if($(this).val().length < 1){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('您还没有填写申请学校');
        }else{
            $(this).parent().removeClass('ErroBox');
        }
    })

    $("#ApplySummary").blur(function () {
        if($(this).val().length < 1){
            $(this).parent().addClass('ErroBox');
            $(this).next().text('您还没有填写申请总结');
        }else{
            $(this).parent().removeClass('ErroBox');
        }
    })
}