/**
 * Created by Foliage on 2016/10/25.
 */
$(function () {
    //获取方案
    $('div[name="class"],div[name="scrol"]').inputbox({
        height:46,
        width:199
    });
    $('div[name="applyItem"]').inputbox({
        height:46,
        width:147
    });
//  $("#Project,#Grade,#Results").hover(function () {
//      $(this).trigger('click');
//  })
    //点击显示模态窗口
    $('#application').click(function () {
        if($("#Project input").val() == '申请项目'){
            layer.msg('请选择申请项目');
            return
        }else if($("#Grade input").val() == '目前就读年级'){
            layer.msg('请选择目前就读年级');
            return
        }else if($("#Results input").val() == '国内平均成绩'){
            layer.msg('请选择目前成绩');
            return
        }
        $(".mask").fadeIn();
        $(".getProgram").fadeIn();
    })
    //点击关闭模态窗口
    $(".getProgram .close").click(function () {
        $(".mask").fadeOut();
        $(".getProgram").fadeOut();
    })
    //点击模态窗口确定按钮，ajax提交数据
    $(".surebtn").click(function () {
        if(rule.phone.test($('#tel').val()) != true){
            layer.msg('请输入正确的手机号码');
            return
        }
        ajaxData = {
            'Intention': 'IndexApply', //方法
            'Project':$("#Project input").val(), //申请项目
            'Grade':$("#Grade input").val(),  //目前就读年级
            'Results':$("#Results input").val(), //国内平均成绩
            'Tel':$('#tel').val(),
        }
        $.post("/commonajax/",ajaxData,function(data){
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                $('#tel').val('');
                $(".mask").fadeOut();
                $(".getProgram").fadeOut();
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })
    //大图切换
    jQuery(".IndexBanScr").hover(function(){ jQuery(this).find(".prev,.next").stop(true,true).fadeTo("show",0.7) },function(){ jQuery(this).find(".prev,.next").fadeOut() });
    jQuery(".IndexBanScr").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:600, trigger:"click"});
    //成功案例
    jQuery(".IndexCaseScrol").slide({titCell:".hd ul",mainCell:".scrolMain ul",autoPage:true,effect:"left",autoPlay:true,vis:4,interTime:8000});
    //资讯
    $(".indexNewsList li").hover(function(){
        $(this).addClass("on").siblings().removeClass("on");
    })
})
