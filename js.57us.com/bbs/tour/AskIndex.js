$(function() {
    btnShow();
    //固定右侧
    $(".sidebar").autofix_anything();
    $(window).resize(function() {
        $(".sidebar").autofix_anything();
    });

    //页码相关跳转
    $(".PageBtn").on('click',function(){
        var gopage = $("#gopage").val();
        var lastpage = $("#AskPageCount").val();
        if(!gopage){
            layer.msg("请输入页码");
        }else if(lastpage < gopage ){
            layer.msg("您输入的页码超过最大页数");
        }else {
            var gourl = $("#AskGoPageUrl").val();
            window.location.href=gourl+'&p='+gopage;
        }
    });
})

//判断搜索框里面的值是否为空
function btnShow() {
    var inputDemo = $(".bbsSeach .input");
    $(inputDemo).focus(function() {
        $(this).parent().addClass("on");
    });
    $(inputDemo).blur(function() {
        if($(this).val() != "") {
            $(this).parent().addClass("on");
        } else {
            $(this).parent().removeClass("on");
        }
    })
}