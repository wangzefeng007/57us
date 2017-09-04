/**
 * Created by Foliage on 2016/11/3.
 */
$(function() {
    $('.buttons-tab').fixedTab({});
    //显示头部
    $(".detailCont").on('scroll',function(){
        //显示头部
        if($(".detailCont").scrollTop()>$(".ProdetailBox").offset().top){
            $("#diybar").removeClass("detailBar");
        }
        else{
            $("#diybar").addClass("detailBar");
        };
        if($(".content").scrollTop()>$(".proTab").offset().top){
            $(".BackTop").show();
            $(document).on('click','.proTab .buttons-tab',function(){
                if($(".proTab .buttons-tab .fitst").is(".active")){
                    $("#PhotoBtn").show();
                }else {
                    $("#PhotoBtn").hide();
                }
            });
            if($(".proTab .buttons-tab .fitst").is(".active")){
                $("#PhotoBtn").show();
            };
        }
        else{
            $(".PhotoBtn,.BackTop").hide();
        };
    });

    //图片详情
    $(document).on('click','.proTab .buttons-tab',function(){
        if($(".proTab .buttons-tab .fitst").is(".active")){
            $("#PhotoBtn").show();
        }else {
            $("#PhotoBtn").hide();
        }
    })

    //图文详情
    $("#PhotoBtn").on('click',function () {
        var _thisID = $(this).attr('data-id');
        var _type = $(this).attr('data-type');
        if(_thisID == '0'){
            ajaxData = {
                'Intention': 'DetailsAndPic',
            }
            $.post("",ajaxData,function(data){
                if(data.ResultCode == "200"){
                    $("#DetailsText").hide();
                    $("#PhotoBtn").attr('data-id','1');
                    $("#DetailsPic").empty();
                    $("#DetailsPic").append(data.Content);
                    $("#DetailsPic").show()
                    $("#PhotoBtn").html('文本<br>详情');
                    $("#PhotoBtn").attr('data-type','1');
                    //产品介绍图片点击放大事件
                    var _Img = [];
                    $('#DetailsPic img').each(function () {
                        _Img.push($(this).attr('src'));
                    })
                    //执行图片浏览器
                    var myPhotoBrowserStandalone = $.photoBrowser({
                        photos : _Img
                    });
                    //点击时打开图片浏览器
                    $(document).on('click','#DetailsPic img',function () {
                        myPhotoBrowserStandalone.open();
                        $(".photo-browser .photo-browser-close-link .icon").html("&#xe604;")
                    });
                }else {
                    $.toast(data.Message);
                }
            },'json');
        }else {
            if(_type == '1'){
                $("#DetailsText").show();
                $("#DetailsPic").hide();
                $(this).attr('data-type','0');
                $("#PhotoBtn").html('图文<br>详情');
            }else {
                $("#DetailsPic").show();
                $("#DetailsText").hide();
                $(this).attr('data-type','1');
                $("#PhotoBtn").html('文本<br>详情');
            }
        }
    })
    //点击图片详情图片列表数据注入
    $(".pagePhoto").on('click',function () {
        ajaxData = {
            'Intention': 'DetailsPic', //方法
            'ID':$(this).attr('data-id'), //对应的产品id
        }
        $.post("/play/getlists/",ajaxData,function(data){
            if(data.ResultCode == "200"){
                $("#pagePhoto .picList").empty();
                $.each(data.DataPic,function(n,list){
                    $("#pagePhoto .picList").append('<li><a class="pb-standalone"><img src="'+list+'" /></a></li>');
                });

                //执行图片浏览器
                var myPhotoBrowserStandalone = $.photoBrowser({
                    photos : data.DataPic
                });
                //点击时打开图片浏览器
                $(document).on('click','.pb-standalone',function () {
                    myPhotoBrowserStandalone.open();
                    $(".photo-browser-close-link .icon").html("&#xe604;")
                });
            }else {
                $.toast(data.Message);
            }
        },'json');
    })

    //点击在线咨询后注入百度商桥代码
    $(".chat").on('click',function () {
        html = '<iframe src="http://p.qiao.baidu.com/cps/chat?siteId=9980989&userId=21983137" frameborder="0"  width="100%" height="100%"></iframe>';
        $("#chat .content").empty();
        $("#chat .content").append(html);
    })

    //点击返回顶部
    $(document).on("click",".BackTop", function() {
        $(".detailCont").scrollTo({durTime:150});
    });

    $(".moreBox").on('click',function () {
        if($(".sutithide").css("display") == "block"){
            $(".sutithide").hide();
            $(this).find('i').html("&#xe7f5;");
        }else {
            $(".sutithide").show();
            $(this).find('i').html("&#xe7f5;");
        }
    })

});