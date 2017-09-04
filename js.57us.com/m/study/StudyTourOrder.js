/**
 * Created by Foliage on 2017/2/23.
 */
$(function () {
    'use strict';

    //初始化加载
    loadPage();

    $(document).on('click','.submitBtn',function () {
        var travellers = [];
        $('.manIns').each(function () {
            travellers.push({'zhname':$(this).find('.zhNmae').val(),'type':$(this).find('.hd .on').attr('data-type'),'zhCard':$(this).find('.zhCard').val()});
        });
        var ajaxData = {
            'Intention':'StudyTourOrder', //方法
            'Travellers':travellers, //出行人信息  数组 zhname 出行人姓名 type代表0代表有护照 1代表没有护照  zhCard号
            'Contacts':$("#name").val(), //姓名
            'Mobile':$(".mobile").val(), //手机号码
            'Email':$(".mail").val(), //邮箱
            'Message':$(".message").val(), //留言
            'YoosureID':GetQueryString('id'),//产品ID
            'Date':GetQueryString('d'),//出发时间
            'Num':GetQueryString('n'),//人数
        };
        //执行验证
        // var flag = false;
        // var v_index = '';
        // for(var i=0;i<num;i++){
        //     if(travellers[i].zhname == ''){
        //         flag = true;
        //         v_index = Number([i])+Number(1);
        //         break;
        //     }else if(travellers[i].zhname != ''){
        //         flag = true;
        //         if(travellers[i].type == '0'){
        //             if(travellers[i].zhCard == ''){
        //                 flag = true;
        //                 v_index = Number([i])+Number(1);
        //                 break;
        //             }
        //         }else{
        //             flag = false;
        //         }
        //     }
        // }
        // if(flag){
        //     $.toast('请填写第'+v_index+'个出行人相关信息');
        //     $(".content").scrollTop(0);
        //     return
        // }
        if(ajaxData.Contacts == ''){
            $.toast('联系人姓名不能为空');
            return
        }else if(ajaxData.Mobile == ''){
            $.toast('联系人手机号码不能为空');
            return
        }else if(rule.phone.test(ajaxData.Mobile) != true){
            $.toast('手机号码格式不正确');
            return
        }else if(ajaxData.Email == ''){
            $.toast('联系人邮箱不能为空');
            return
        }else if(rule.Mail.test(ajaxData.Email) != true){
            $.toast('邮箱格式不正确');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajaxstudy/",
            data: ajaxData,
            beforeSend: function () {
                $.showPreloader('提交中');
            },
            success: function (data) {
                $.toast(data.Message);
                var Url = data.Url;
                window.location.href = Url;
            },complete: function () {
                $.hidePreloader();
            }
        })
    })
    $.init();
})

//获取url参数,并定义变量
var id = GetQueryString('id');
var date = GetQueryString('d');
var price = GetQueryString('p');
var num = GetQueryString('n');

/**
 * 初始化加载方法
 */
function loadPage() {
    //相关数据注入页面
    var $page = $('.page');
    $page.find('.date').text(date);
    $page.find('.num').text(num);
    var totalPrice = Number(num) * Number(price);
    $page.find('.totalPrice').html('&yen;'+totalPrice+'.00');

    var html = '';
    for(var i = 1; i <= num; i++){
        html +='<div class="cf manIns">';
        html +='<p class="sutit"><i class="icon iconfont icon-weibiaoti5"></i>出行人'+i+'</p>';
        html +='<ul class="tabList">';
        html +='<li>';
        html +='<div class="tabL">姓名：</div>';
        html +='<div class="input">';
        html +='<input type="text" name="zhName" class="zhNmae" value="" placeholder="请输入出行人姓名"/>';
        html +='</div>';
        html +='</li>';
        html +='<li>';
        html +='<div class="tabL">护照：</div>';
        html +='<div class="input">';
        html +='<div class="tabChose">';
        html +='<div class="hd cf">';
        html +='<a href="JavaScript:void(0)" class="on" data-type="0"><em></em>有护照</a>';
        html +='<a href="JavaScript:void(0)" data-type="1"><em></em>未办理</a>';
        html +='</div>';
        html +='<div class="bd">';
        html +='<p style="display: block;"><input type="text" name="zhCard" class="zhCard"  value="" placeholder="请输入出行人护照"/></p>';
        html +='<p class="red">为了顺利出行，我们的工作人员会尽快与你取得联系。</p>';
        html +='</div>';
        html +='</div>';
        html +='</div>';
        html +='</li>';
        html +='</ul>';
        html +='</div>';
    }
    $page.find('#list').append(html);

    //出行人是否有护照切换事件
    $page.find(".manIns .hd a").on('click',function () {
        var $thisDom = $(this);
        var $index = $(this).index();
        var $superiorDom = $(this).parents('.tabChose');
        $superiorDom.find('.hd a').removeClass('on');
        $thisDom.addClass('on');
        $superiorDom.find('.bd p').hide();
        $superiorDom.find('.bd p input').val('');
        $superiorDom.find('.bd p').eq($index).show();
    });
}