/**
 * Created by Foliage on 2016/12/30.
 */
$(function () {
    //获取url参数
    var _id = GetQueryString('id');
    var _date = GetQueryString('d');
    var _price = GetQueryString('p');
    var _num = GetQueryString('n');

    //页面相关位置赋值
    $(".productid").text(_id);
    $(".date").text(_date);
    $(".price").text(_price);
    $(".num").text(_num);

    //导航添加on
    $(".OrderProcess li:eq(0)").addClass('on');

    //循环出行旅客数量
    Nnt(_num);

    //协议选择
    $('.protocol .cbt').inputbox();

    //鼠标离开验证方法
    blurverify();

    //是否有护照显示隐藏
    $(".sexChose label").click(function(){
        var num =$(this).index();
        $(this).parent(".sexChose").siblings(".otherT").find(".otherLi").hide().eq(num).show()
    })

    //点击去支付
    $(document).on('click','#paybtn',function () {
        var zhname = $("#zhname").val();
        var phone = $("#phone").val();
        var email = $("#email").val();
        var protocol = $("#protocol").attr('class');

        //验证旅客姓名
        $(".tourManList .lvname").each(function () {
            var lvname = $(this).val();
            if(lvname == ''){
                $(this).parent().addClass('ErroBox');
                $(this).next().text('姓名不能为空');
                roll();
                return
            }
        })

        //验证护照
        $(".tourManList .sexChose").each(function () {
            var hz = $(this).find('.rb_active').attr('val');
            if(hz == '0'){
                var a = $(this).next().find('.hz').val();
                var _thisDom = $(this).next().find('.hz');
                if(a == ''){
                    _thisDom.parent().addClass('ErroBox');
                    _thisDom.next().text('护照不能为空');
                    roll();
                    return
                }
            }
        })

        //验证姓名
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
        }else if(phone == ''){ //验证手机号码是否为空
            $("#phone").parent().addClass('ErroBox');
            $("#phone").next().text('手机号码不能为空');
            W_ScrollTo($("#phone"),+50);
            return
        }else if(!/^1(3\d|5[0-35-9]|8[025-9]|47)\d{8}$/i.test(phone)){ //验证手机号码格式
            $("#phone").parent().addClass('ErroBox');
            $("#phone").next().text('手机号码格式不正确');
            W_ScrollTo($("#phone"),+50);
            return
        }else if(email == ''){
            $("#email").parent().addClass('ErroBox');
            $("#email").next().text('邮箱不能为空');
            W_ScrollTo($("#email"),+50);
            return
        }else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(email)){
            //验证邮箱
            $("#email").parent().addClass('ErroBox');
            $("#email").next().text('邮箱格式不正确');
            W_ScrollTo($("#email"),+50);
            return
        }else if(protocol != 'cbt cb checked'){ //是否同意协议
            layer.msg('请先同意条款');
            return
        }

        //验证短信验证码是否存在，如存在则验证
        if($("#piccode").is(':visible')){
            if($("#code").val() == ''){
                layer.msg('请输入短信验证码');
                return
            }
        }

        //旅客信息数组
        var roomnum=[];
        $("#tourManList table").each(function () {
            roomnum.push({'lvname':$(this).find('.lvname').val(),'yesnohz':$(this).find(".sexChose").find('.rb_active').attr('val'),'hz':$(this).find('.hz').val()});
        })
        var ajaxData = {
            'YoosureID':_id, //产品id
            'Date':_date, //出行日期
            'Num':_num, //出行人数
            'Travellers':roomnum, //旅客数组，lvname 代表姓名  yesnohz代表护照 0代表有护照 1代表无护照  hz护照编号
            'Contacts':zhname, //联系人中文姓名
            'Mobile':$("#phone").val(), //联系人手机号码
            'Email':$("#email").val(),//联系人邮箱
            'VerifyCode':$("#code").val(), //验证码，可能为空
            'Message':$("#message").val(), //留言
        }

        $.ajax({
            type: "post",	//提交类型
            dataType: "json",	//提交数据类型
            url: "/order/studytourorder/",  //提交地址
            data: ajaxData,
            beforeSend: function () { //加载过程效果
                $("#paybtn").text('提交中...');
                $("#paybtn").addClass('course');
                $("#paybtn").attr('id','');
            },
            success: function (data) {	//函数回调
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

//循环出行旅客数量
function Nnt(n) {
    var str = '';
    for(var i = 1; i <= n; i++) {
        str = str +'<table border="0" cellspacing="0" cellpadding="0" class="lvkeTab">'+
            '<thead>'+
            '<tr>'+
            '<td colspan="2" class="f18">旅客'+i+'</td>'+
            '</tr>'+
            '</thead>'+
            '<tbody>'+
            '<tr>'+
            '<th valign="top">姓名 ：</th>'+
            '<td>'+
            '<div class="inputBox"><input type="text" name="lvname" value="" class="InsInput lvname"><div class="erroText"></div></div>'+
            '</td>'+
            '</tr>'+
            '<tr>'+
            '<th valign="top">护照：</th>'+
            '<td>'+
            '<div class="sexChose cf">'+
            '<label name="rbt'+i+'" type="radiobox" val="0" class="cbt cb rb_active"><i></i>有护照<input style="display:none" type="radio" name="rbt1" value="1"></label>'+
            '<label name="rbt'+i+'" type="radiobox" val="1" class="cbt cb rb"><i></i>还没有办理<input style="display:none" type="radio" name="rbt1" value="0"></label>'+
            '</div>'+
            '<div class="otherT mt10">'+
            '<div class="otherLi" style="display: block;">'+
            '<div class="inputBox"><input type="text" name="hz" value="" class="InsInput hz" placeholder="请填写护照号码"><div class="erroText"></div></div>'+
            '</div>'+
            '<div class="otherLi">'+
            '<p class="f16 red">为了顺利出行，我们的工作人员会尽快与你取得联系。</p>'+
            '</div>'+
            '</div>'+
            '</td>'+
            '</tr>'+
            '</tbody>'+
            '</table>'+
            '<div class="lvkLine cf"></div>';
    }
    $("#tourManList").append(str);

    //移除最后一个旅行信息框下的虚线
    $("#tourManList .lvkLine:last").remove();

    //男女选择表单美化
    $(".sexChose .cbt*").inputbox();
}