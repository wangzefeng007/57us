$(function() {
    'use strict';
    //所有页面
    $(document).on("pageInit", function(e, id, page) {
        $(".headMenu").on("click", function() {
            $(".popmask").show();
            $(".secondMenu").addClass("on");
        });
        $(".popmask").on("click", function() {
            $(".popmask").hide();
            $(".secondMenu").removeClass("on");
        });
        //默认开关设置
        $(".label-switch").on("click",function(){
            if($(this).find("input").is(':checked')){
                $(this).find("input").removeAttr("checked");
            }else{
                $(this).find("input").attr("checked",true);
            }
        });
    });
    //全部订单页面
    $(document).on("pageInit", "#orderAll", function(e, id, page) {
        //点击订单分类
        $(".mangeList .btn").on("click", function() {
            if($(".mangeList").hasClass("on")) {
                $(this).parent().removeClass("on");
                $("#orderSty").hide();
                $(".collectMask").hide();
            } else {
                $(this).parent().addClass("on");
                $("#orderSty").show();
                $(".collectMask").show();
            }
        });
    });

    //点击在线咨询后注入百度商桥代码
    $(".chat").on('click',function () {
        var html = '<iframe src="http://p.qiao.baidu.com/cps/chat?siteId=9980989&userId=21983137" frameborder="0"  width="100%" height="100%"></iframe>';
        $("#chat .content").empty();
        $("#chat .content").append(html);
        $("#chat").show();
    })
    //百度商桥关闭
    $("#chat .close").on('click',function () {
        $("#chat").hide();
        $("#chat .content").empty();
    })
})