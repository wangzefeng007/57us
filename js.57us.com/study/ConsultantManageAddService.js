/**
 * Created by Foliage on 2016/9/29.
 */

$(function () {
	//自定义下拉
	$('.DiySelect').inputbox({
		height:41,
		width:217
	});
	//弹窗推荐图片
	jQuery(".tjUppicList").slide({titCell:".hd ul",mainCell:".ulWrap",effect:"left",pnLoop:false,vis:1});

	//服务条款
	$('.tkchose label').inputbox();

	//焦点离开验证
	BlurVerify();

	//服务类型对应的参数
	if($("#ServiceType input").val() == 'WholeService'){
		$("#AddInsList table").hide();
		$("#WholeService").show();
	}
	//服务标签提示
	$(".tipIco").hover(function(){
		var content=$(this).attr("data-text")
		layer.tips(content,$(this), {
			skin: 'tipIcoclass',
			tips: [1, '#ff6767'],
			time: 400000
		});
	},function(){
		layer.closeAll();
	})

	//服务类型对应服务参数显示隐藏
	$("#ServiceType a").on('click',function () {
		var type = $('#ServiceType').find('input').val();
		if(type == 'WholeService'){
			$("#AddInsList table").hide();
			$("#WholeService").show();
		}else if(type == 'ApplySchool'){
			$("#AddInsList table").hide();
			$("#ApplySchool").show();
		}else if(type == 'DocumentManage'){
			$("#AddInsList table").hide();
			$("#DocumentManage").show();
		}else if(type == 'ChooseSchoolsModify'){
			$("#AddInsList table").hide();
			$("#ChooseSchoolsModify").show();
		}else if(type == 'DataTranslation'){
			$("#AddInsList table").hide();
			$("#DataTranslation").show();
		}else if(type == 'BackgroundPromotion'){
			$("#AddInsList table").hide();
			$("#BackgroundPromotion").show();
		}else if(type == 'VisaDirect'){
			$("#AddInsList table").hide();
			$("#VisaDirect").show();
		}
	})


	//服务标签add
	$("#AddServiceTag").click(function () {
		html = '<div class="inputBox ServiceTagInput">' +
			'<span class="closeIco"></span>' +
			'<input type="text" name="" value="" placeholder="99.9%通过率" class="InsInput TipInput" maxlength="6"/>' +
			'</div>';
		$("#AddServiceTag").before(html);
		var tagnum = $(".ServiceTagInput").length;
		if(tagnum >= 4){
			$("#AddServiceTag").hide();
		}
	})
	//服务标签del
	$(document).on('click','.closeIco',function () {
		$(this).parent().remove();
		var tagnum = $(".ServiceTagInput").length;
		if(tagnum <= 4){
			$("#AddServiceTag").show();
		}
	})

	//弹窗显示
	$('#AddBtn').on('click','',function(){
		$(".mask").show();
		$(".AddPicPop").addClass("on");
	})

	//弹窗关闭
	$(".AddPicPop .close,.popBtn a.canceBtn").click(function(){
		$(".mask").hide();
		$(".AddPicPop").removeClass("on")
	})

	//上传图片，继续添加图片事件
	$('.uploadPic').on('click',function () {
		var num = Number($('#addPicList li').length) + Number($("#tjbox .on").length) + Number($("#addPic li").length);
		if(num >=8){
			layer.msg('选择的图片与上传的图片不能超过8张');
		}else {
            $('input[id=avatarInput]').click();
		}
	})

	//选择模板图片事件
	$(document).on('click','#tjbox li',function () {
		var num = Number($('#addPicList li').length) + Number($("#tjbox .on").length) + Number($("#addPic li").length);
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

		//关闭模态窗口
		$(".mask").hide();
        $(".AddPicPop").removeClass("on");

		//本地图片
		var localImg = [];
		$('#addPicList li').each(function () {
			localImg.push($(this).find('img').attr('src'));
		})

		//模板图片
        var webImg = [];
        $('#tjbox .on').each(function () {
            webImg.push($(this).find('img').attr('src'));
        })

		//新增图片数组合并
		var uploadImg = localImg.concat(webImg);

        //已有图片
        var existImg = [];
        $("#addPic li").each(function () {
            existImg.push($(this).find('img').attr('src'));
        })

		var newImg = unique(existImg.concat(uploadImg));

		var html = '';
        $.each(newImg,function(n,list){
        	html+= '<li>' +
                '<div class="OtherFun">' +
                '<a href="javascript:void(0)" class="AddPicDelImg">删除</a>|' +
                '<a href="javascript:void(0)" class="ChangeImg">替换</a>|' +
                '<a href="javascript:void(0)" class="Cover">设置封面</a>' +
                '</div>' +
                '<i class="choseIco"></i>' +
                '<img src="' + list + '" width="200" height="150" class="webimg"/><i class="upSucess"></i>' +
                '</li>';
        });
        $("#addPic li").remove();
        $("#AddBtn").before(html);

        //移除所有本地上传的图片
		$("#addPicList li").remove();
        $("#AddImgBox").removeClass('has');
        $("#addPicList").hide();

        //移除所有选中图片
        $("#tjbox li").removeClass('on');

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
		var num = $("#addPic li").length;
		if(num >=8){
			$('#AddBtn').hide();
		}else {
            $('#AddBtn').show();
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

//本地上传图片，后执行方法
function imagesInput(ImgBaseData,index) {
    $("#AddImgBox").addClass('has');
    $("#addPicList").show();
    html = '<li>' +
        '<div class="OtherFun"><i class="deteBtn"></i></div>' +
        '<img src="' + ImgBaseData + '" width="120" height="90" />' +
        '</li>';
    $("#addImg").parent().before(html);
    layer.close(index);
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

function AjaxSubmit(SubmitType) {
	//服务参数
	var type = $('#ServiceType').find('input').val();
	if(type == 'WholeService'){
		var ServiceParameter = {};
		$("#WholeService form").serializeArray().map(function(x){ServiceParameter[x.name] = x.value;});
	}else if(type == 'ApplySchool'){
		var ServiceParameter = {};
		$("#ApplySchool form").serializeArray().map(function(x){ServiceParameter[x.name] = x.value;});
	}else if(type == 'DocumentManage'){
		var ServiceParameter = {};
		$("#DocumentManage form").serializeArray().map(function(x){ServiceParameter[x.name] = x.value;});
	}else if(type == 'ChooseSchoolsModify'){
		var ServiceParameter = {};
		$("#ChooseSchoolsModify form").serializeArray().map(function(x){ServiceParameter[x.name] = x.value;});
	}else if(type == 'DataTranslation'){
		var ServiceParameter = {};
		$("#DataTranslation form").serializeArray().map(function(x){ServiceParameter[x.name] = x.value;});
	}else if(type == 'BackgroundPromotion'){
		var ServiceParameter = {};
		$("#BackgroundPromotion form").serializeArray().map(function(x){ServiceParameter[x.name] = x.value;});
	}else if(type == 'VisaDirect'){
		var ServiceParameter = {};
		$("#VisaDirect form").serializeArray().map(function(x){ServiceParameter[x.name] = x.value;});
	}

	//服务标签
	var ServiceTag = [];
	$('#ServiceTag input').each(function () {
		ServiceTag.push({'ServiceTag':$(this).val()});
	})

	//服务头像
	var ServiceImg = [];
	$('#addPic li').each(function () {
		ServiceImg.push({'Img':$(this).find('img').attr('src')});
	})

	//ajax提交的参数
	ajaxData ={
		'ID':$('.AddServiceBoxT .name').data('id'),
		'Intention': 'AddService',
		'SubmitType':SubmitType,
		'ServiceName':$("#servicename").val(),
		'ServicePrice':$("#serviceprice").val(),
		'ServiceType':$("#ServiceType").find('input').val(),
		'ApplyLevel':$("#ApplyLevel").find('input').val(),
		'ServiceParameter':ServiceParameter,
		'ServiceTag':ServiceTag,
		'ServiceDescription':$("#ServiceDescription").val(),
		'ServiceImg':ServiceImg,
		'ServiceDefaultImg':$("#addPic .on").index(),
		'ServiceDetails':ue.getContent()
	}

	//提交验证
	if(ajaxData.ServiceName.length < 1){
		$("#servicename").parent().addClass('ErroBox');
		$("#servicename").next().text('您还没有填写服务名称');
		W_ScrollTo($("#servicename"),+100);
		return;
	}else if(ajaxData.ServicePrice.length < 1){
		$("#serviceprice").parent().addClass('ErroBox');
		$("#serviceprice").next().next().text('您还没有填写服务价格');
		W_ScrollTo($("#serviceprice"),+100);
		return;
	}else if(rule.Num2.test(ajaxData.ServicePrice) != true){
		$("#serviceprice").parent().addClass('ErroBox');
		$("#serviceprice").next().next().text('服务价格为正整数');
		W_ScrollTo($("#serviceprice"),+100);
		return;
	}else if(ajaxData.ServiceDescription.length <1){
		$("#ServiceDescription").parent().addClass('ErrBox');
		$("#ServiceDescription").next().text('您还没有填写服务简介');
		W_ScrollTo($("#ServiceDescription"),+100);
		return;
	}else if(ajaxData.ServiceDescription.length >90){
		$("#ServiceDescription").parent().addClass('ErrBox');
		$("#ServiceDescription").next().text('服务简介字数不能超过90个字(包括符号)');
		W_ScrollTo($("#ServiceDescription"),+100);
		return;
	}else if(ajaxData.ServiceImg.length <= 0){
		layer.msg("服务头像至少要添加一张");
		W_ScrollTo($("#addPic"),+100);
		return
	}else if(ajaxData.ServiceDetails == ''){
		layer.msg("您还没有填写服务详情");
		W_ScrollTo($("#addPic"),+100);
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
                setTimeout(function(){
                	window.location=data.Url
                },2000);
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

	$("#servicename").blur(function () {
		if($(this).val().length < 1){
			$(this).parent().addClass('ErroBox');
			$(this).next().text('您还没有填写服务名称');
		}else{
			$(this).parent().removeClass('ErroBox');
		}
	})

	$("#serviceprice").blur(function () {
		if($(this).val().length < 1){
			$(this).parent().addClass('ErroBox');
			$(this).next().next().text('您还没有填写服务价格');
		}else if(rule.Num.test($(this).val()) != true){
			$(this).parent().addClass('ErroBox');
			$(this).next().next().text('服务价格为正整数');
		}else {
			$(this).parent().removeClass('ErroBox');
		}
	})

	$("#ServiceDescription").blur(function () {
		if($(this).val().length < 1){
			$(this).parent().addClass('ErroBox');
			$(this).next().text('您还没有填写服务简介');
		}
	})
}