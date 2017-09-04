/**
 * Created by Foliage on 2017/2/27.
 */
$(function () {
    //头像裁剪
    $("#portrait").on('click',function () {
        $("#information").append(portraitHtml);
        var pc = new PhotoClip('#clipArea', {
            size: 260,
            outputSize: 640,
            //adaptive: ['60%', '80%'],
            file: '#file',
            // view: '#view',
            ok: '#clipBtn',
            //img: 'img/mm.jpg',
            loadStart: function() {
                // console.log('开始读取照片');
            },
            loadComplete: function() {
                // console.log('照片读取完成');
            },
            done: function(dataURL) {
                portraitSubmit(dataURL);
            },
            fail: function(msg) {
                $.toast(msg);
            }
        });
    });

    //头像裁剪取消
    $(document).on('click','#cancelPortrait',function () {
        $("#portraitHtml").remove();
    })

    //出生日期打开
    $("#birthDay").on('click',function () {
        $oldBirthDay = $(this).val();
    });

    //出生日期点击确定
    $(document).on('click','.picker-calendar-months .picker-calendar-day',function () {
        var newBirthDay = $('#birthDay').val();
        var ajaxData = {
            'Intention':"SaveInformation",
            'Type':'birthday',
            'BirthDay':newBirthDay,
        }
        if($oldBirthDay != newBirthDay){
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/userajax.html",
                data: ajaxData,
                beforeSend: function () {
                    $.showPreloader('提交中');
                },
                success: function(data) {
                    if(data.ResultCode == '200'){
                        $.toast('修改成功');
                    }else{
                        $.toast(data.Message);
                    }
                },
                complete: function () { //加载完成提示
                    $.hidePreloader();
                }
            });
        }
    });

    $("#city").cityPicker({
        toolbarTemplate: '<header class="bar bar-nav">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">选择所在城市</h1>\</header>'
    });

    //详细地址点击打开
    $("#city").on('click',function () {
        $oldCity = $(this).val();
    })

    //详细地址点击确定
    $(document).on('click','.picker-modal .close-picker',function () {
        var $newCity = $("#city").val();
        var ajaxData = {
            'Intention':"SaveInformation",
            'Type':'city',
            'City':$newCity,
        }
        if($newCity != $oldCity){
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/userajax.html",
                data: ajaxData,
                beforeSend: function () {
                    $.showPreloader('提交中');
                },
                success: function(data) {
                    if(data.ResultCode == '200'){
                        $.toast('修改成功');
                    }else{
                        $.toast(data.Message);
                    }
                },
                complete: function () { //加载完成提示
                    $.hidePreloader();
                }
            });
        }
    })
})
var $oldBirthDay;
var $oldCity;

/**
 * 头像裁剪ajax提交
 * @param dataURL base64位头像编码
 */
function portraitSubmit(dataURL) {
    var ajaxData ={
        'Intention': 'SaveAvatar', //头像提交方法
        'Img':dataURL, //base64位编码
    }
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/userajax.html",
        data: ajaxData,
        beforeSend: function () {
            $.showPreloader('提交中');
        },
        success: function(data) {
            if(data.ResultCode == '200'){
                $("#portraitHtml").remove();
                $.toast('修改成功');
                $("#portrait").find('img').attr('src',dataURL);
            }else{
                $.toast(data.Message);
            }
        },
        complete: function () { //加载完成提示
            $.hidePreloader();
        }
    });
}

//头像裁剪插件
var portraitHtml = '';
portraitHtml +='<div class="clipMask" id="portraitHtml" style="display: block">';
portraitHtml +='<div id="clipArea"></div>';
portraitHtml +='<div class="clipBtn mt20">';
portraitHtml +='<a href="javascript:void(0)" class="button button-fill canceClip" id="cancelPortrait">取消</a>';
portraitHtml +='<a href="javascript:void(0)" class="button button-fill sureClip" id="clipBtn">裁剪</a>';
portraitHtml +='</div>';
portraitHtml +='</div>';