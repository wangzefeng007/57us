/**
 * Created by Foliage on 2017/3/2.
 */
$(document).ready(function () {
    $(document).on('click','.delPic',function () {
        var $this = $(this);
        $.confirm('确定要删除这张图片吗？', function () {
            $this.remove();
            if($(".delPic").length < 5){
                $(".addLi").show();
            }
        });
    })

    $("#pjstart .startB span").on('click',function () {
        var $index = Number($(this).index()) + Number(1);
        var $superiorDom = $(this).parents('.startB');
        $superiorDom.find('span').removeClass('on');
        $superiorDom.find('span').slice(0,$index).addClass('on');
    })

    $('#saveBtn').on('click',function () {
        var _type = $(this).attr('data-type');
        var Pics = [];
        $('#picList .delPic').each(function () {
            Pics.push($(this).find('img').attr('src'));
        });
        var ajaxData = {
            'Intention': 'AddEvaluate', //方法
            'TourProductID':$(this).attr('data-id'), //产品id
            'OrderNumber':$(this).attr('data-order'), //订单id
            'ServerFraction':$("#ServerFraction .on").length, //星星第一个
            'ConvenientFraction':$("#ConvenientFraction .on").length, //星星第二个
            'ExperienceFraction':$("#ExperienceFraction .on").length, //星星第三个
            'PerformanceFraction':$("#PerformanceFraction .on").length, //星星第四个
            'Content':html_encode($("#evaluationText").val()),//评价内容
            'Pics':Pics, //图片数组
        }

        if(ajaxData.ServerFraction == ''){
            if(_type == '0'){
                $.toast('请给"57美国服务"打分');
                return
            }else {
                $.toast('请给"导游服务"打分');
                return
            }
        }else if(ajaxData.ConvenientFraction == ''){
            if(_type == '0'){
                $.toast('请给"产品便捷"打分');
                return
            }else {
                $.toast('请给"行程安排"打分');
                return
            }
        }else if(ajaxData.ExperienceFraction == ''){
            if(_type == '0'){
                $.toast('请给"出游体验"打分');
                return
            }else {
                $.toast('请给"餐饮住宿"打分');
                return
            }
        }else if(ajaxData.PerformanceFraction == ''){
            if(_type == '0'){
                $.toast('请给"性价比"打分');
                return
            }else {
                $.toast('请给"出行交通"打分');
                return
            }
        }else if(ajaxData.Content == ''){
            $.toast('请填写评价内容');
            return
        }else if($("#evaluationText").val().length <20){
            $.toast('内容不能少于20个字');
            return
        }

        //执行ajax提交
        $.ajax({
            type: "post",
            dataType: "json",
            url: " /ajaxtour/",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('提交中');
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    $.toast(data.Message);
                    setTimeout(function(){
                        window.location = data.Url;
                    },500);
                }else {
                    $.toast(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $.hidePreloader();
            }
        });
    })
})

function imgChange() {
    //获取点击的文本框
    var file = document.getElementById("imgBtn");
    //获取当前url
    var URL = window.URL || window.webkitURL;

    var fileList = file.files;

    var imgArr = [];
    //遍历获取到得图片文件
    for (var i = 0; i < fileList.length; i++) {
        var imgUrl = URL.createObjectURL(file.files[i]);
        imgArr.push(imgUrl);
    };
    var num = Number(imgArr.length) + Number($(".delPic").length);
    if(num > 5){
        $.alert('请不要上传超过5张图片');
        return
    }
    var item = '';
    $.each(imgArr,function(n,list){
        item +='<li class="delPic"><img src="'+list+'"/></li>';
    });
    $('.addLi').before(item);
    if($(".delPic").length >= 5){
        $(".addLi").hide();
    }
};
