$(function(){
    'use strict';
    //所有页面样式
    var rem = remfix();
    function remfix (minwidth,size) {
        if (!minwidth) { minwidth = 320; };
        if (!size) { size = 20; };
        var width = $(window).width();
        width = minwidth>width?minwidth:width;
        width = 640<width?640:width;
        var tosize =  size*width/minwidth;
        tosize = tosize<30?tosize:30;
        $("html").css("font-size",tosize);
        window.onresize = function(){
            rem = remfix(minwidth,size);
        }
        return tosize;
    };

    $("#popLogin .diyBar").css('background','#28beb5');

    //留学站，在线咨询
    $(document).on('click','.Consulting',function () {
        var html = '<iframe id="mstudyifram" src="http://p.qiao.baidu.com/cps/chat?siteId=9980989&userId=21983137" frameborder="0"  width="100%" height="100%"></iframe>';
        $("#Consulting .content").empty();
        $("#Consulting .content").append(html);
    })

    //判断是否登录
    $(document).on('click','#MyInfo',function () {
        var _type = $(this).attr('data-type');
        if(_type == '0'){
            window.location=muser;
        }else {
            window.location=muser + '/member/mycenter/';
        }
    })

    //首页ban
    $(document).on("pageInit", "#studyIndex", function(e, id, page) {
        var swiper = new Swiper('.swiper-container', {
            autoHeight: true, //enable auto height
        });
        //返回顶部按钮显示隐藏
        $(".content").on('scroll',function(){
            //显示头部
            if($(".content").scrollTop()>$(".playMenu").offset().top){
                $(".fixBackTop").show();
            }else{
                $(".fixBackTop").hide();
            };
        });

        //点击返回顶部
        $(".fixBackTop").on('click',function () {
            $(".content").scrollTo({durTime:150});
        });
    });

    //顾问详情页
    $(document).on("pageInit", "#ConsultantDetail", function(e, id, page) {
        $(".content").scrollTop(0);
        $(".moreBox").click(function(){
            var par =$(this).parents(".detailBox")
            par.toggleClass("active");
            if(par.hasClass("active")){
                $(this).find(".icon").html("&#xe7f5;")
            }else{
                $(this).find(".icon").html("&#xe61e;")
            }
        })
    });
    //评估页面JS
    $(document).on("pageInit", "#evaluation", function(e, id, page) {
        var $content = $(page).find('.content');
        //申请项目
        $content.find("#Project").picker({
            toolbarTemplate: '<header class="bar bar-nav diyCity"><button class="button button-link pull-right close-picker">确定</button><h1 class="title">请选择申请项目</h1></header>',
            cols: [
                {
                    textAlign: 'center',
                    values: ['小学', '高中', '本科', '硕士', '博士', '语言课程', '单签证'],
                    cssClass: 'picker-items-col-normal',
                }
            ]
        });

        //目前就读
        $content.find("#Grade").picker({
            toolbarTemplate: '<header class="bar bar-nav diyCity">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">请选择目前就读</h1>\</header>',
            cols: [
                {
                    textAlign: 'center',
                    values: ['初二及以前', '初三', '高一', '高二', '高三', '高中毕业已工作', '大一','大二','大三','大四','本科毕业及以后'],
                    cssClass: 'picker-items-col-normal'
                }
            ]
        });
        //国内平均成绩
        $content.find("#Results").picker({
            toolbarTemplate: '<header class="bar bar-nav diyCity">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">请选择国内平均成绩</h1>\</header>',
            cols: [
                {
                    textAlign: 'center',
                    values: ['68及以下', '68 - 71', '75 - 77', '78 - 81', '82 - 84', '90 - 94', '95 - 100'],
                    cssClass: 'picker-items-col-normal'
                }
            ]
        });

        $content.on('click','.getSolution', function () {
            var ajaxData = {
                'Intention':'IndexApply', //方法名
                'Project':$("#Project").val(), //申请项目
                'Grade':$("#Grade").val(), //目前就读
                'Results':$('#Results').val(), //国内平均成绩
                'phone':$("#phone").val(), //手机号码
            }
            if(rule.phone.test(ajaxData.phone) != true){
                $.toast('请输入正确的手机号码');
                return
            }

            $.post('/ajaxstudy/',ajaxData,function (data) {
                if(data.ResultCode == '200'){
                    $.toast(data.Message);
                    setTimeout(function(){
                        window.location=data.Url;
                    },2000);
                }else {
                    $.toast(data.Message);
                }
            },'json');
        });
    });
    $.init();
})

