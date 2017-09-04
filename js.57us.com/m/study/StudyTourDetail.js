/**
 * Created by Foliage on 2017/3/2.
 */
$(document).ready(function () {
    $('.buttons-tab').fixedTab({});
    $(".dayBox .dayBoxT").on("click",function(){
        if($(this).parent().hasClass("on")){
            $(this).parent().removeClass("on");
        }else{
            $(this).parent().addClass("on");
        }
    });
    //点击显示大图
    $('.pagePhoto').on('click',function (){
        var ajaxData = {
            'Intention': 'DetailsPic', //方法
            'ID':$(this).attr('data-id'), //对应的产品id
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajaxstudy/",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader();
            },
            success: function(data) {
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
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    });
});