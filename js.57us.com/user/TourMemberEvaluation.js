/**
 * Created by Foliage on 2017/1/17.
 */
$(function () {
    //打星评分
    $(".star").raty({
        starOn:'http://images.57us.com/img/user/star-on-big.png',
        starOff:'http://images.57us.com/img/user/star-off-big.png',
        readOnly:false,
        halfShow:true,
        size:34
    })

    //删除图片
    $(document).on('click','.pjclose',function () {
        $(this).parent().remove();
    })

    //提交评价
    $("#evaluationSubmit").on('click',function () {
        var _type = $(this).attr('data-type');
        var Pics = [];
        $('#pjPicAdd .Pic').each(function () {
            Pics.push($(this).find('img').attr('src'));
        });

        var ajaxData = {
            'Intention': 'AddEvaluate', //方法
            'TourProductID':$(this).attr('data-id'), //产品id
            'OrderNumber':$(this).attr('data-order'), //订单id
            'ServerFraction':$("#ServerFraction input").val(), //星星第一个
            'ConvenientFraction':$("#ConvenientFraction input").val(), //星星第二个
            'ExperienceFraction':$("#ExperienceFraction input").val(), //星星第三个
            'PerformanceFraction':$("#PerformanceFraction input").val(), //星星第四个
            'Content':html_encode($("#evaluationText").val()),//评价内容
            'Pics':Pics, //图片数组
        }

        if(ajaxData.ServerFraction == ''){
            if(_type == '0'){
                layer.msg('请给"57美国服务"打分');
                return
            }else {
                layer.msg('请给"导游服务"打分');
                return
            }
        }else if(ajaxData.ConvenientFraction == ''){
            if(_type == '0'){
                layer.msg('请给"产品便捷"打分');
                return
            }else {
                layer.msg('请给"行程安排"打分');
                return
            }
        }else if(ajaxData.ExperienceFraction == ''){
            if(_type == '0'){
                layer.msg('请给"出游体验"打分');
                return
            }else {
                layer.msg('请给"餐饮住宿"打分');
                return
            }
        }else if(ajaxData.PerformanceFraction == ''){
            if(_type == '0'){
                layer.msg('请给"性价比"打分');
                return
            }else {
                layer.msg('请给"出行交通"打分');
                return
            }
        }else if(ajaxData.Content == ''){
            layer.msg('请填写评价内容');
            return
        }else if($("#evaluationText").val().length <20){
            layer.msg('内容不能少于20个字');
            return
        }

        //执行ajax提交
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                //提交加载效果
                public_loading();
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location = data.Url;
                    },500);
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $("#public_loading").remove();
            }
        });
    })
})

//多张图片上传方法
var addImg = document.getElementById("AddOfferImg");
var PapersUp=new plupload.Uploader({
    browse_button: addImg, //触发文件选择对话框的按钮，为那个元素id
    url: '/Controller/ZuFang/upload.php',//ajaxUrl + "?Intention=ReleaseInfo", //服务器端的上传页面地址
    flash_swf_url: 'Moxie.swf', //swf文件，当需要使用swf方式进行上传时需要配置该参数
    // silverlight_xap_url: 'Moxie.xap', //silverlight文件，当需要使用silverlight方式进行上传时需要配置该参数
    // multi_selection:false,
    filters: {
        mime_types: [ //只允许上传图片文件
            {
                title: "图片文件",
                extensions: "jpg,gif,png,bmp"
            }
        ]
    },
    max_file_size : '2560kb', //最大只能上传400kb的文件
    prevent_duplicates : true,
});
//在实例对象上调用init()方法进行初始化
PapersUp.init();
//绑定各种事件，并在事件监听函数中做你想做的事
PapersUp.bind('FilesAdded', function(uploader, files) {
    var num = Number($("#pjPicAdd li").length) - Number('2') + files.length;
    var max_size = 512 * 5 * 1024;
    if(num > 5){
        layer.alert('评价图片数量最多为5张,您可以精心挑选后再上传');
        return;
    }
    var sum = 0;
    for (var i = 0 ; i < files.length; i++){
        sum+= files[i].size;
    }
    if(sum > max_size){
        layer.alert('评价图片不能大于2.5MB');
        return;
    }
    for (var i = 0; i < files.length; i++) {
        ImgTo64(files[i],function(imgsrc){
            var _data = imgsrc.split(';')[1];
            var _img = 'data:image/jpeg;'+_data;
            html = '<li class="Pic"><span class="pjclose"></span><img src="' + _img + '" width="80" height="60"></li>';
            $("#AddOfferImg").before(html);
        });
    };
    if(num >= 5){
        $("#AddOfferImg").hide();
    }
    picNum(num);
});

function picNum(num) {
    $("#zNum").text(num);
    $("#sNum").text(Number(5) - Number(num));
}