$(function () {
    //页码相关跳转
    $(".PageBtn").click(function(){
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

	//相关问题最后一条去掉border
    $(".relateList li").last().addClass("noBor");
	//显示隐藏悬浮问题
    $(window).scroll(function() {
        var listOff = $(".appendManList").offset().top;
        var windowOff = $(window).scrollTop();
        if(windowOff > listOff) {
            $(".fixedFun").addClass("on");
        } else {
            $(".fixedFun").removeClass("on");
        }
    });
})
