$(function(){
    //协议选择
    $('.protocol .cbt').inputbox();
   //固定右边
   if($(".sidebar").length){$(".sidebar").autofix_anything();}

    //解析url参数
    var TourProductID = GetQueryString('TourProductID');
    var ProductSkuID = GetQueryString('ProductSkuID');
    var DayPriceID = GetQueryString('DayPriceID');
    var Number = GetQueryString('Number');
    var TotalPrice = GetQueryString('TotalPrice');
    var Date = GetQueryString('Date')
    var ProductPrice = GetQueryString('ProductPrice');

    //返回上一个页面
    $("#BackBtn").click(function () {
        window.location.href = '/play/'+TourProductID+'.html?ProductSkuID='+ProductSkuID+'&DayPriceID='+DayPriceID+'&Number='+Number+'&TotalPrice='+TotalPrice+'&Date='+Date+'&ProductPrice='+ProductPrice;
    })

    //旅客数量循环入住
    Nnt();

    //鼠标离开验证
    blurverify();

    //点击支付事件
    $("#paybtn").click(function () {
        //执行验证
        var zhname = $("#zhname").val();
        var mail = $("#mail").val();
        var phone = $("#phone").val();
        var code = $("#code").val();
        var protocol = $("#protocol").attr('class');
        var country = $("#country input").val();
        //验证旅姓
        $(".tourManList .last").each(function () {
            var last = $(this).val();
            if(last == ''){
                $(this).parent().addClass('erro');
                $(this).next().text('姓不能为空');
                roll();
                return
            }else if(!/^[a-zA-Z|\s]{1,20}$/i.test(last)){
                $(this).parent().addClass('erro')
                $(this).next().text('只能输入长度1-20位英文或者拼音')
                roll();
                return
            }
        })
        //验证旅名
        $(".tourManList .name").each(function () {
            var name = $(this).val();
            if(name == ''){
                $(this).parent().addClass('erro');
                $(this).next().text('姓名不能为空');
                roll();
                return
            }else if(!/^[a-zA-Z|\s]{1,20}$/i.test(name)){
                $(this).parent().addClass('erro')
                $(this).next().text('只能输入长度1-20位英文或者拼音')
                roll();
                return
            }
        })
        //验证护照
        $(".tourManList .hz").each(function () {
            var hz = $(this).val();
            if(hz == ''){
                $(this).parent().addClass('erro');
                $(this).next().text('护照不能为空');
                roll();
                return
            }else if(rule.HZ.test(hz) != true){
                $(this).parent().addClass('erro')
                $(this).next().text('护照格式不正确')
                roll();
                return
            }
        })
        //出生日期
        $(".tourManList .BirthTime").each(function () {
            var BirthTime = $(this).val();
            if(BirthTime == ''){
                $(this).parent().addClass('erro');
                $(this).next().text('请选择该旅客出生日期');
                roll();
                return
            }
        })
        //验证旅客1的手机号码
        if($(".Tel").val() == ''){
            $(".Tel").parent().addClass('erro');
            $(".Tel").next().text('请填写旅客1的联系电话');
            roll();
            return
        }else if(rule.phone.test($(".Tel").val()) != true){
            $(".Tel").parents().addClass('erro');
            $(".Tel").next().text('联系电话格式不正确');
            roll()
            return
        }
        //验证旅客1的微信
        if($(".weixin").val() == ''){
            $(".weixin").parent().addClass('erro');
            $(".weixin").next().text('请填写旅客1的微信号');
            roll();
            return
        }
        //验证酒店信息
        if($(".HotelInfo").is(':visible')){
            //验证酒店名称
            if($(".hotelname").val() == ''){
                $(".hotelname").parent().addClass('erro');
                $(".hotelname").next().text('酒店名称不能为空');
                W_ScrollTo($(".hotelname"),+100);
                return
            }else if(!/^[a-zA-Z|\s]{1,50}$/i.test($(".hotelname").val())){
                $(this).parent().addClass('erro')
                $(this).next().text('请输入英文，不要超过50个字');
                W_ScrollTo($(".HotelInfo"));
                return
            }
            //验证酒店地址
            if($(".hoteladdress").val() == ''){
                $(".hoteladdress").parent().addClass('erro');
                $(".hoteladdress").next().text('酒店地址不能为空');
                W_ScrollTo($(".HotelInfo"));
                return
            }
            //验证酒店电话
            if($(".hoteltel").val() == ''){
                $(".hoteltel").parent().addClass('erro');
                $(".hoteltel").next().text('酒店电话不能为空');
                W_ScrollTo($(".HotelInfo"));
                return
            }
        }
        //验证航班信息
        if($(".FlightInfo").is(':visible')){
            //验证接航班
            if($(".FlightJoin").is(':visible')){
                if($(".FlightJoinDate").val() == ''){
                    $(".FlightJoinDate").parent().addClass('erro');
                    $(".FlightJoinDate").next().text('请选择航班抵达日期');
                    W_ScrollTo($(".FlightInfo"));
                    return
                }else if($(".FlightJoinCourse").val() == ''){
                    $(".FlightJoinCourse").parent().addClass('erro');
                    $(".FlightJoinCourse").next().text('请填写接机航班号');
                    W_ScrollTo($(".FlightInfo"));
                    return
                }else if($(".FlightJoinTime").val() == ''){
                    $(".FlightJoinTime").parent().addClass('erro');
                    $(".FlightJoinTime").next().text('请选择航班抵达时间');
                    W_ScrollTo($(".FlightInfo"));
                    return
                }
            }
            //验证送航班
            if($(".FlightDeliver").is(':visible')){
                if($(".FlightDeliverDate").val() == ''){
                    $(".FlightDeliverDate").parent().addClass('erro');
                    $(".FlightDeliverDate").next().text('请选择航班出发日期');
                    W_ScrollTo($(".FlightInfo"));
                    return
                }else if($(".FlightDeliverCourse").val() == ''){
                    $(".FlightDeliverCourse").parent().addClass('erro');
                    $(".FlightDeliverCourse").next().text('请填写出发航班号');
                    W_ScrollTo($(".FlightInfo"));
                    return
                }else if($(".FlightDeliverTime").val() == ''){
                    $(".FlightDeliverTime").parent().addClass('erro');
                    $(".FlightDeliverTime").next().text('请选择航班出发时间');
                    W_ScrollTo($(".FlightInfo"));
                    return
                }
            }
        }
        if(zhname == ''){ //验证联系人姓名
            $("#zhname").parent().addClass('erro');
            $("#zhname").next().text('姓名不能为空');
            W_ScrollTo($(".Linkman"));
            return
        }else if(!/^[\u4e00-\u9fa5|^\\s]{2,20}$/i.test(zhname)){
            $("#zhname").parent().addClass('erro');
            $("#zhname").next().text('只能输入纯中文长度为2-20位,不能有空格');
            W_ScrollTo($(".Linkman"));
            return
        }else if(mail == ''){ //验证联系人邮箱
            $("#mail").parent().addClass('erro');
            $("#mail").next().text('邮箱不能为空');
            W_ScrollTo($(".Linkman"));
            return
        }else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(mail)){
            $("#mail").parent().addClass('erro');
            $("#mail").next().text('邮箱格式不正确');
            W_ScrollTo($(".Linkman"));
            return
        }
        if(country == 'cn'){
            if(phone == ''){ //验证联系人手机号码
                $("#phone").parent().addClass('erro');
                $("#phone").next().text('手机号码不能为空');
                W_ScrollTo($(".Linkman"));
                return
            }else if(!/^1(3\d|5[0-35-9]|8[025-9]|47)\d{8}$/i.test(phone)){
                $("#phone").parent().addClass('erro');
                $("#phone").next().text('手机号码格式不正确');
                W_ScrollTo($(".Linkman"));
                return
            }
        }else {
            if(phone == ''){ //验证联系人手机号码
                $("#phone").parent().addClass('erro');
                $("#phone").next().text('手机号码不能为空');
                W_ScrollTo($(".Linkman"));
                return
            }
        }
        if(protocol != 'cbt cb checked'){ //验证是否同意条款
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
            roomnum.push({'name':$(this).find('.name').val(),'last':$(this).find('.last').val(),'hz':$(this).find('.hz').val(),'sex':$(this).find(".sexChose").find('.rb_active').attr('val'),'BirthTime':$(this).find('.BirthTime').val(),'room':$(this).attr('data-id')});
        })

        var ajaxData = {
            'TourProductID':$("#TourProductID").val(), //产品id
            'ProductSkuID':$("#ProductSkuID").val(), //产品skuid
            'DayPriceID':$("#DayPriceID").val(), //出行日期价格id
            'Number':$("#Number").val(), //出行人数
            'Travellers':roomnum, //出行旅客参数 数组
            'TravellersTel':$(".Tel").val(), //旅客电话
            'TravellersWeixin':$(".weixin").val(), //旅客微信
            'HotelName':$(".hotelname").val(), //酒店名称
            'HotelAddress':$(".hoteladdress").val(), //酒店地址
            'HotelTel':$(".hoteltel").val(), //酒店电话
            'FlightJoinDate':$(".FlightJoinDate").val(), //航班抵达日期
            'FlightJoinCourse':$(".FlightJoinCourse").val(), //接机航班号
            'FlightJoinTime':$(".FlightJoinTime").val(), //航班抵达时间
            'FlightDeliverDate':$('.FlightDeliverDate').val(), //出发航班日期
            'FlightDeliverCourse':$(".FlightDeliverCourse").val(), //出发航班号
            'FlightDeliverTime':$(".FlightDeliverTime").val(), //出发航班时间
            'Contacts':$("#zhname").val(), //联系人姓名
            'Email':$("#mail").val(), //联系人邮箱
            'Mobile':$("#phone").val(), //联系人手机号码
            'VerifyCode':$("#code").val(), //短信验证码
            'Message':$("#message").val(), //留言
        }
        $.ajax({
            type: "post",	//提交类型
            dataType: "json",	//提交数据类型
            url: "/play/confirmorder/",  //提交地址
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

//旅客数量循环入住
function Nnt() {
    var nnt = $("#Number").val();
    var str = '';
    for(var i = 1; i <= nnt; i++) {
        str = str + '<table border="0" cellspacing="0" cellpadding="0" class="lvkeTab" data-id="'+i+'">'+
            '<thead>'+
            '<tr>'+
            '<td colspan="4" class="f18">旅客'+i+'</td>'+
            '</tr>'+
            '</thead>'+
            '<tbody>'+
            '<tr>'+
            '<th valign="top"><span class="red">*</span>姓（拼音/英文）:</th>'+
            '<td>'+
            '<div class="inputbox">'+
            '<input type="text" name="last" maxlength="27" value="" class="OrderInput last" placeholder="与护照一致"/>'+
            '<p class="errotip">只能输入中文</p>'+
            '</div>'+
            '</td>'+
            '<th valign="top"><span class="red">*</span>名（拼音/英文）:</th>'+
            '<td>'+
            '<div class="inputbox fl">'+
            '<input type="text" name="name" maxlength="27" value="" class="OrderInput name" placeholder="与护照一致"/>'+
            '<p class="errotip">只能输入中文</p>'+
            '</div>'+
            '</td>'+
            '</tr>'+
            '<tr>'+
            '<th valign="top"><span class="red">*</span>证件类型:</th>'+
            '<td colspan="3">'+
            '<div class="inputbox">'+
            '<div name="phone" type="selectbox" class="diyselect">'+
            '<div class="opts">'+
            '<a href="javascript:void(0);" val="护照" class="selected">护照</a>'+
            '<!--<a href="javascript:void(0);" val="+00(美国国)">+00(美国国)</a>-->'+
            '</div>'+
            '</div>'+
            '<input type="text" name="hz" value="" class="OrderInput hz" placeholder="请输入您的护照编号" autocomplete="off" style="width: 187px;margin-right: 10px"/>'+
            '<p class="errotip">只能输入中文</p>'+
            '</div>'+
            '</td>'+
            '</tr>'+
            '<tr>'+
            '<th valign="top"><span class="red">*</span>性别:</th>'+
            '<td colspan="3">'+
            '<div class="sexChose">'+
            '<label name="rbt'+i+'" type="radiobox" val="1" class="cbt cb rb_active"><i></i>男</label>'+
            '<label name="rbt'+i+'" type="radiobox" val="0" class="cbt cb"><i></i>女</label>'+
            '</div>'+
            '</td>'+
            '</tr>'+
            '<tr>'+
            '<th valign="top"><span class="red">*</span>出生日期:</th>'+
            '<td colspan="3">'+
            '<div class="inputbox fl">'+
            '<input type="text" name="BirthTime" value="" class="OrderInput BirthTime"  placeholder="请输入您的出生日期" onfocus="WdatePicker({maxDate:\'%y-%M-%d\'})" style="width: 200px;" readonly />'+
            '<p class="errotip"></p>'+
            '</div>'+
            '</td>'+
            '</tr>'+
            '</tbody>'+
            '</table>'+
            '<div class="lvkLine cf"></div>';
    }
    $("#tourManList").append(str);

    //联系电话，微信号html代码
    var html = '<tr>'+
        '<th valign="top"><span class="red">*</span>联系电话:</th>'+
        '<td colspan="3">'+
        '<div class="inputbox fl">'+
        '<input type="text" name="Tel" value="" class="OrderInput Tel" placeholder="联系电话" style="width: 200px;"/>'+
        '<p class="errotip"></p>'+
        '</div>'+
        '</td>'+
        '</tr>'+
        '<tr>'+
        '<th valign="top"><span class="red">*</span>微信号:</th>'+
        '<td colspan="3">'+
        '<div class="inputbox fl">'+
        '<input type="text" name="weixin" value="" class="OrderInput weixin" placeholder="微信号" style="width: 200px;"/>'+
        '<p class="errotip"></p>'+
        '</div>'+
        '</td>'+
        '</tr>';

    //每一个旅客信息添加联系联系电话，微信号
    $("#tourManList .lvkeTab:first").append(html);

    //移除最后一个旅行信息框下的虚线
    $("#tourManList .lvkLine:last").remove();

    //男女选择表单美化
    $(".sexChose .cbt*").inputbox();
    //下拉框美化
    $('.inputbox .diyselect').inputbox({
        height:36,
        width:103
    });
}

