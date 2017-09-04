/**
 * Created by Foliage on 2016/11/14.
 */
//上传模态窗口html
var UpHtml = '<div class="mask">' +
    '<div class="UpfilePop">' +
    '<a href="JavaScript:void(0)" class="close"></a>' +
    '<p class="tit">上传简历</p>' +
    '<div class="UpfilePopM">' +
    '<table border="0" cellspacing="0" cellpadding="0" width="100%">' +
    '<tr>' +
    '<th width="48" valign="top">备注</th>' +
    '<td width="284">' +
    '<textarea name="Message" class="Message" id="Message"></textarea>' +
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

function UpModal(_title) {
    $("#CustomerRight").append(UpHtml);
    $(".UpfilePop").find(".tit").text(_title);
    //上传弹窗显示
    $('.upbtn,.upbtn2').on('click',function() {
        $(".mask").show();
        popcenter();
    })
    //弹窗关闭
    $('.UpfilePop .close,.canceBtn').on('click',function() {
        $(".mask").hide(100);
    })

    function popcenter() {
        var popHeight = 　$(".UpfilePop").height();
        var marginTop = popHeight / 2;
        $(".UpfilePop").css({
            "margin-top": -marginTop + 'px'
        })
    }
    //弹出文件选择框
    $('#AddWork,.aginUp').on('click',function () {
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