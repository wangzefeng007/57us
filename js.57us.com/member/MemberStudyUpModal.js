/**
 * Created by Foliage on 2017/2/14.
 */
$(document).ready(function(){
    //查看全部
    $(".more").click(function(){
        if($(this).is('.on')){
            $(this).removeClass('on')
            $(this).parents('.content').find('.qhide').slideUp(200);
            $(this).html('查看全部<i class="icon iconfont icon-up-copy"></i>')
        }else{
            $(this).addClass('on');
            $(this).parents('.content').find('.qhide').slideDown(200);
            $(this).html('关闭全部<i class="icon iconfont icon-up"></i>')
        }
    });
})

// 上传模态窗口
var upModalHtml = '';
upModalHtml = '<div class="mask" id="upModal">' +
    '<div class="UpfilePop" id="UpfilePop">' +
    '<a href="JavaScript:void(0)" class="close closeUploadModal"><i class="icon iconfont icon-guanbifuzhi"></i></a>' +
    '<p class="tit title">上传简历</p>' +
    '<div class="UpfilePopM">' +
    '<table border="0" cellspacing="0" cellpadding="0" width="100%">' +
    '<tbody>' +
    '<tr>' +
    '<th width="48" valign="top">备注</th>' +
    '<td width="284"><textarea name="Message" class="Message" id="Message" rows="" cols="" placeholder=""></textarea></td>' +
    '</tr>' +
    '<tr>' +
    '<td colspan="2" id="WorkFile">上传附件:' +
    '<p class="upInput"><span class="file uploadFile"><i class="icon iconfont icon-48copy18"></i></span></p>' +
    '<input type="file" id="avatarInput" style="display: none"/>' +
    '<a href="JavaScript:void(0)" class="anewUploadFile" style="display: none;">重新上传</a>' +
    '</td>' +
    '</tr>' +
    '</tbody>' +
    '</table>' +
    '</div>' +
    '<div class="btnBox">' +
    '<a href="javascript:void(0);" class="canceBtn closeUploadModal">取消</a>' +
    '<a href="javascript:void(0);" class="sureBtn submit">确定</a>' +
    '</div>' +
    '</div>' +
    '</div>';

/**
 *
 * @param title 模态窗口标题
 */
function uploadModal(title) {
    //模态窗口html页面写入页面
    $("body").append(upModalHtml);

    //模态窗口标题写入
    $("#upModal .title").text(title);

    //打开模态窗口事件
    $('.uploadModal').on('click',function () {
        $("#upModal").show();
        popCenterWindow('UpfilePop','3.3','2');

        //此前操作的订单号
        orderId = $(this).parents('.content').find('.orderId').val();

        //此前订单操作的是第几次操作
        orderLength = $(this).parents('.dialogueList').find('li').length;
    })

    //关闭模态窗口事件
    $('.closeUploadModal').on('click',function () {
        $("#upModal").hide();
    })

    //弹出文件选择框
    $('#upModal .uploadFile,#upModal .anewUploadFile').on('click',function () {
        $("#avatarInput").trigger('click');
    })
    $("#upModal #avatarInput").change(function() {
        uploadFile(this);
    })
}

var orderId;
var orderLength;

/**
 * 函数描述
 *
 * @param file 当前上传的文件相关数据
 */
function uploadFile(file) {
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
        //按下系统窗口，打开事件
        reader.onload = function(evt) {
            if($("#upModal .hasFile").html() == undefined){
                html = '<p class="hasFile"><span class="fileName" val="'+ evt.target.result +'">'+file.files[0].name+'</span><span class="detefile"></span></p>';
                $("#upModal .uploadFile").hide();
                $("#upModal .anewUploadFile").before(html);
                $("#upModal .anewUploadFile").show();
            }else {
                $("#upModal .hasFile").find('.fileName').attr('val',evt.target.result);
                $("#upModal .hasFile").find('.fileName').text(file.files[0].name);
            }
        }
        reader.readAsDataURL(file.files[0]);
    }
}

/**
 * 函数描述
 *
 * @param id 参数 模态窗口的id
 * @param w 参数 根据模态窗口大小调整距离left
 * @param h 参数 根据模态窗口大小调整距离top
 */
function popCenterWindow(id,w,h) {
    //获取窗口的高度
    var windowHeight = $(window).height();
    //获取窗口的宽度
    var windowWidth = $(window).width();
    //计算弹出窗口的左上角Y的偏移量
    var popY = windowHeight / w;
    var popX = windowWidth / h;
    //设定窗口的位置
    $("."+id+"").css("top", popY).css("left", popX);
}