/**
 * Created by Foliage on 2016/10/25.
 */

$(function () {
    //input美化
    $('.DiySelect').inputbox({
        height:36,
        width:205
    });
    //协议选择
    $('.protocol .cbt').inputbox();
    //固定右边
    if($(".sidebar").length){$(".sidebar").autofix_anything();}

    //导航添加on
    $(".OrderProcess li:eq(0)").addClass('on');

    //解析url参数
    var ProductId = GetQueryString('ID');
    $("#ProductId").val(ProductId);

    //鼠标离开验证方法
    blurverify();

    //点击支付事件
    $("#paybtn").click(function () {
        //执行验证
        var zhname = $("#zhname").val();
        var phone = $("#phone").val();
        var StudyTime = $("#StudyTime").val();
        var protocol = $("#protocol").attr('class');
        if(zhname == ''){
            $("#zhname").parent().addClass('ErroBox');
            $("#zhname").next().text('姓名不能为空');
            W_ScrollTo($("#zhname"),+50);
            return
        }else if(!/^[\u4e00-\u9fa5|^\\s]{2,20}$/i.test(zhname)){
            $("#zhname").parent().addClass('ErroBox');
            $("#zhname").next().text('只能输入纯中文长度为2-20位,不能有空格');
            W_ScrollTo($("#zhname"),+50);
            return
        }else if(phone == ''){
            $("#phone").parent().addClass('ErroBox');
            $("#phone").next().text('手机号码不能为空');
            W_ScrollTo($("#phone"),+50);
            return
        }else if(!/^1(3\d|5[0-35-9]|8[025-9]|47)\d{8}$/i.test(phone)){
            $("#phone").parent().addClass('ErroBox');
            $("#phone").next().text('手机号码格式不正确');
            W_ScrollTo($("#phone"),+50);
            return
        }else if(StudyTime == ''){
            $("#StudyTime").parent().addClass('ErroBox');
            $("#StudyTime").next().text('计划留学时间不能为空');
            W_ScrollTo($("#phone"),+50);
            return
        }else if(protocol != 'cbt cb checked'){
            layer.msg('请先同意条款');
            return
        }
        var ajaxData = {
            'Intention':'OrdreConsultant', //方法名
            'ProductId':$("#ProductId").val(),  //服务id
            'Contacts':$("#zhname").val(), //联系人中文姓名
            'Mobile':$("#phone").val(), //手机号
            'Goal':$("#Goal input").val(), //留学目标
            'StudyTime':$("#StudyTime").val(), //计划留学时间
            'VerifyCode':$("#code").val(), //验证码，可能为空
            'Message':$("#message").val(), //留言
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/order/createorder/",
            data: ajaxData,
            beforeSend: function () {
                $("#paybtn").text('提交中...');
                $("#paybtn").addClass('course');
                $("#paybtn").attr('id','');
            },
            success: function (data) {
                if(data.ResultCode == '200'){
                    var Url = data.Url;
                    window.location.href = Url;
                }else if(data.ResultCode == '100'){
                    layer.msg(data.Message);
                    return
                }else{
                    layer.msg(data.Message);
                    return
                }
            },complete: function () {
                $(".nextPay").text('去支付');
                $(".nextPay").removeClass('course');
                $(".nextPay").attr('id','paybtn');
            }
        })
    })

})
