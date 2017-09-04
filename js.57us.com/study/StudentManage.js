/**
 * Created by Foliage on 2016/10/16.
 */
$(function () {
    //左侧点击时增加on
    $("#LeftMenu .a").click(function () {
        $("#LeftMenu li").removeClass('on');
        if($(this).attr('id') ==  'MyService'){
            if(($(".SecondtMenu").is(":hidden"))){
                $(".SecondtMenu").slideDown(400);
            }else {
                $(".SecondtMenu").slideUp(400);
            }
        }
        if($(this).attr('id') !=  'MyService'){
            $(".SecondtMenu").slideUp(100);
        }
        $(this).parent().addClass('on');
    })

    //我的订单列表菜单导航点击
    $(document).on('click','#OrderNav li',function () {
        $(this).addClass("on").siblings().removeClass("on");
    })

    $("#InforImg img").click(function () {
        $("#UpAvatar").trigger('click');
    })

    $("#UpAvatar").on('click',function () {
        UpImg();
        $("#head_photo").trigger('click');
    })

})

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
        'Intention': 'StudentUpImg',
        'Img': ImgBaseData,
    }
    $("#InforImg").find('img').attr('src',ImgBaseData);
    $.post("/studentmanageajax/",AJaxData,function(data){
        if(data.ResultCode == "200"){
            layer.msg(data.Message);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}

//上传文件,留言模态窗口html
var UpHtml;
UpHtml = '<div class="mask">' +
    '<div class="UpfilePop">' +
    '<a href="JavaScript:void(0)" class="close"></a>' +
    '<p class="tit">上传简历</p>' +
    '<div class="UpfilePopM">' +
    '<table border="0" cellspacing="0" cellpadding="0" width="100%">' +
    '<tr>' +
    '<th width="48" valign="top">备注</th>' +
    '<td width="284">' +
    '<textarea name="Message" class="Message" id="Message" rows="" cols="" placeholder=""></textarea>' +
    '</td>' +
    '</tr>' +
    '<td colspan="2" id="WorkFile">上传附件: ' +
    '<p class="upInput"><span class="file" id="AddWork"><i></i></span></p>' +
    '<input type="file" id="addpreview" style="display: none"/>' +
    '<a href="JavaScript:void(0)" class="aginUp" style="display: none;">重新上传</a>' +
    '</td>' +
    '</table>' +
    '</div>' +
    '<div class="btnBox">' +
    '<a href="javascript:void(0);" class="canceBtn">取消</a>' +
    '<a href="javascript:void(0);" class="sureBtn" id="sureBtn">确定</a>' +
    '</div>' +
    '</div>' +
    '</div>';

var OrderID;
var OrderLength;
//上传模态窗口方法
function UpModal() {
    $("#Upload").append(UpHtml);
    //上传弹窗显示
    $(document).on('click','.upbtn',function() {
        OrderID = $(this).attr('data-order');
        OrderLength = $(this).parents('.dialogueList').find('li').length;
        $(".mask").fadeIn();
        popcenter();
    })
    //弹窗关闭
    $(document).on('click','.UpfilePop .close,.canceBtn',function() {
        $(".mask").fadeOut(100);
    })

    function popcenter() {
        var popHeight = 　$(".UpfilePop").height();
        var marginTop = popHeight / 2;
        $(".UpfilePop").css({
            "margin-top": -marginTop + 'px'
        })
    }
    //弹出文件选择框
    $(document).on('click','#AddWork,.aginUp',function () {
        $("#addpreview").trigger('click');
    })
    $("#addpreview").change(function() {
        preview(this);
    })
    //文件上传，替换方法
    function preview(file) {
        if(file.files && file.files[0]) {
            var reader = new FileReader();
            //判断上传枨是否正确
            if(file.files[0].type != "application/msword" && file.files[0].type != "application/vnd.openxmlformats-officedocument.wordprocessingml.document" && file.files[0].type != "application/x-zip-compressed"){
                layer.alert('选择文件错误,图片类型必须是<span style="color: red">doc,docx,zip压缩包中的一种</span>');
                return;
            }else if(file.files[0].size > 1024 * 1024 * 5){  //判断图片是否大于5Mb
                layer.alert('请不要上传大于5M的文件');
                return;
            }
            reader.onload = function(evt) {
                if($(".hasFile").html() == undefined){
                    html = '<p class="hasFile"><span class="fileName" val="'+ evt.target.result +'">'+file.files[0].name+'</span><span class="detefile"></span></p>';
                    $(".upInput").hide();
                    $(".aginUp").before(html);
                    $(".aginUp").show();
                }else {
                    $(".hasFile").find('.fileName').attr('val',evt.target.result);
                    $(".hasFile").find('.fileName').text(file.files[0].name);
                }
            }
            reader.readAsDataURL(file.files[0]);
        }
    }
    //上传文件模态窗口,点击删除
    $(document).on('click','.detefile',function () {
        $(this).parent().remove();
        $(".aginUp").hide();
        $(".upInput").show();
    })
}

//提现现弹窗
$(".reflect a").click(function(){
    html = '<div class="reflectBox ReflectBox">' +
        '<div class="reflecTab">' +
        '<table border="0" cellspacing="0" cellpadding="0" width="100%">' +
        '<tr>' +
        '<th>提现到：</th>' +
        '<td>' +
        '<div class="labelList">' +
        '<label name="studyStyle" type="radiobox" val="支付宝" class="cbt checked rb" spellcheck="true"><i></i>支付宝</label>' +
        '</div>' +
        '</td>' +
        '</tr>' +
        '<tr>' +
        '<th>提现帐号：</th>' +
        '<td>' +
        '<div class="inputBox">' +
        '<input type="text" name="alipaymember" id="alipaymember" value="" class="InsInput alipaymember" />' +
        '</div>' +
        '</td>' +
        '</tr>' +
        '<tr>' +
        '<th>提现金额：</th>' +
        '<td>' +
        '<div class="inputBox">' +
        '<input type="text" name="money" id="money" value="" class="InsInput" placeholder="请输入提现金额" />' +
        '</div>' +
        '<p class="c9 mt5">预计1-3个工作日到帐</p>' +
        '</td>' +
        '</tr>' +
        '</table>' +
        '</div>' +
        '</div>';
    layer.open({
        type: 1,
        title:"余额提现",
        skin: 'reflectBox', //样式类名
        closeBtn: 0, //不显示关闭按钮
        shift: 2,
        area: ['400px', '370px'],
        btn: ['取消', '提现'],
        shadeClose: true, //开启遮罩关闭
        content:html,
        btn1: function(index, layero){
            layer.close(index);
        },
        btn2: function(index, layero){
            var AjaxData = {
                'Intention': 'CustomerManageAssets',
                'alipaymember':$(".reflectBox #alipaymember").val(),
                'money':$(".reflectBox #money").val(),
            }
            $.ajax({
                type: "post",	//提交类型
                dataType: "json",	//提交数据类型
                url: "/commonajax/",  //提交地址
                data: AjaxData,
                success: function(data) {	//函数回调
                    if(data.ResultCode == "200"){
                        layer.msg(data.Message);
                    }else if(data.ResultCode == "201"){
                        layer.msg(data.Message);
                    }else if(data.ResultCode == "202"){
                        layer.msg(data.Message);
                    }else if(data.ResultCode == "101"){
                        layer.msg(data.Message);
                    }else if(data.ResultCode == "102"){
                        layer.msg(data.Message);
                    }
                },
            });
        },
        success: function(layero){
            $(".labelList label").inputbox()
        }
    });
})

//点击查看全部
$(".More").click(function(){
    if($(this).is('.on')){
        $(this).removeClass('on')
        $(this).parent().parent().find('.qhide').slideUp(200);
        $(this).html('查看全部<i></i>')
    }else{
        $(this).addClass('on');
        $(this).parent().parent().find('.qhide').slideDown(200);
        $(this).html('关闭全部<i></i>')
    }
});