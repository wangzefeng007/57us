/**
 * Created by Foliage on 2016/8/15.
 */
$(function () {
    //目的地
    $('#EndCity').empty();
    $.get('/Templates/Tour/data/Ticket/AreaEnter.json', function(data) {
        var item;
        $.each(data, function(i, EndCity) {
            item = '<li>' +
                '<label name="rbt" type="radiobox" val="' + EndCity.AeraID + '" class="cbt"><i></i>' + EndCity.name + '</label>' +
                '</li>';
            $('#EndCity').append(item);
        });
        var Filter = {
            'idname': 'EndCity',
            'nini': 'EndCityAll',
            'dataidname': 'EndCityName',
            'ajaxname': 'AjaxEndCity',
            'textname':'目的地：',
        }
        TourRadio(Filter);
        ClickLoad(Filter);
    }, 'json');

    //票务类型
    $('#TicketType').empty();
    $.get('/Templates/Tour/data/Ticket/Types.json', function(data) {
        var item;
        $.each(data, function(i, TicketType) {
            item = '<li>' +
                '<label name="rbt1" type="radiobox" val="' + TicketType.name + '" class="cbt"><i></i>' + TicketType.name + '</label>' +
                '</li>';
            $('#TicketType').append(item);
        });
        var Filter = {
            'idname': 'TicketType',
            'nini': 'TicketTypeAll',
            'dataidname': 'TicketTypeName',
            'ajaxname': 'AjaxTicketType',
            'textname':'票务类型：',
        }
        TourRadio(Filter);
        ClickLoad(Filter);
    }, 'json');

    //特色主题
    $('#Theme').empty();
    $.get('/Templates/Tour/data/Ticket/Subject.json', function(data) {
        var item;
        $.each(data, function(i, Theme) {
            item = '<li>' +
                '<label name="cbt" type="checkbox" val="' + Theme.id + '" class="cbt"><i></i>' + Theme.name + '</label>' +
                '</li>';
            $('#Theme').append(item);
        });
        var Filter = {
            'idname': 'Theme',
            'nini': 'ThemeAll',
            'dataidname': 'ThemeName',
            'ajaxname': 'AjaxTheme',
            'textname':'特色主题：',
        }
        TourCheckbox(Filter);
        ClickLoad(Filter);
    }, 'json');

    //票务价格
    $('#TicketPrice').empty();
    $.get('/Templates/Tour/data/Ticket/Prices.json', function(data) {
        var item;
        $.each(data, function(i, TicketPrice) {
            item = '<li>' +
                '<label name="rbt2" type="radiobox" val="' + TicketPrice.date + '" class="cbt"><i></i>' + TicketPrice.name + '</label>' +
                '</li>';
            $('#TicketPrice').append(item);
        });
        var Filter = {
            'idname': 'TicketPrice',
            'nini': 'TicketPriceAll',
            'dataidname': 'TicketPriceName',
            'ajaxname': 'AjaxTicketPrice',
            'textname':'票务价格：',
        }
        TourRadio(Filter,'Ticket');
        ClickLoad(Filter);
        //自定义价格回调
        $('#TicketPricemin').on('click change',function () {
            var Pricemin = $(this).val();
            $(this).attr('value',Pricemin);
        })
        $('#TicketPricemax').on('click change',function () {
            var Pricemax = $(this).val();
            $(this).attr('value',Pricemax);
        })
        $("#TicketPriceCustomSave").click(function () {
            var Pricemin = $('#TicketPricemin').val();
            var Pricemax = $('#TicketPricemax').val();
            if(Pricemin == '' && Pricemax == '') {
                layer.msg('请输入完整');
                return;
            }else if(!Pricemin == "" && !/^[1-9]\d*$/i.test(Pricemin) || !Pricemax == "" && !/^[1-9]\d*$/i.test(Pricemax)){
                layer.msg('请输入大于0的整数');
                return
            }else if(!isNaN(parseInt(Pricemax)) && Number(Pricemin) > Number(Pricemax)) {
                layer.msg('最小值不可以大于最大值');
                return;
            }
            $('#TicketPriceAll').parent().removeClass('on');
            PriceCustom = Pricemin + '-' + Pricemax;
            //价格注入大于小于号筛选
            if(PriceCustom == Pricemin + '-') {
                html = '<p data-id="TicketPriceCustomName">票务价格：<span>' + Pricemin + '以上</span><em></em></p>';
                html2 = '<span data-id="TicketPriceName">' + Pricemin +'-'+'All'+'</span>';
                PriceCustomName();
            } else if(PriceCustom == '-' + Pricemax) {
                html = '<p data-id="TicketPriceCustomName">票务价格：<span>' + Pricemax + '以下</span><em></em></p>';
                html2 = '<span data-id="TicketPriceName">' + '0'+ '-'+ Pricemax + '</span>';
                PriceCustomName();
            } else {
                html = '<p data-id="TicketPriceCustomName">票务价格：<span>' + PriceCustom + '</span><em></em></p>';
                html2 = '<span data-id="TicketPriceName">' + Pricemin + '-'+ Pricemax + '</span>';
                PriceCustomName();
            }
            function PriceCustomName() {
                $('#condition p').each(function() {
                    if($(this).attr('data-id') == 'TicketPriceCustomName') {
                        $(this).remove();
                    }
                })
                $('#condition p').each(function() {
                    if($(this).attr('data-id') == 'TicketPriceName') {
                        $(this).remove();
                    }
                })
                $('#TicketPrice label').each(function() {
                    var TicketPriceClass = $(this).attr('class');
                    if(TicketPriceClass == 'cbt rb rb_active') {
                        $(this).removeClass('rb_active');
                        $(this).find('input').attr("checked", false);
                        $(this).find('i').attr("checked", false);
                    }
                })
                $('#clearAll').before(html);
                $('#AjaxTicketPrice span').attr('data-id', 'TicketPriceName').remove();
                $('#AjaxTicketPrice').append(html2);
            }
            //筛选自定义价格点击时清空相关内容操作
            $("#condition p[data-id='TicketPriceCustomName']").click(function () {
                $(this).remove();
                $('#TicketPriceAll').parent().addClass('on');
                $("#TicketPriceCustom input").attr("value","");
                $("#TicketPriceCustom input").val("");
                $('#AjaxTicketPrice span').attr('data-id', 'TicketPriceCustomName').remove();
                $('#AjaxTicketPrice').append('<span data-id="TicketPriceName">All</span>');
                Ajax();
            })
            Ajax();
        })
    }, 'json');

})
//ajax提交的对应的参数
function Ajax(Page) {
    var EndCity = $('#AjaxEndCity').text();
    var TicketType = $('#AjaxTicketType').text();
    var Theme = $('#AjaxTheme').text();
    var TicketPrice = $('#AjaxTicketPrice').text();
    var Sort = $('#AjaxSort').text();
    var Keyword = $('#AjaxKeyword').text();
    $.ajax({
        type: "post",	//提交类型
        dataType: "json",	//提交数据类型
        url: "",  //提交地址
        data: {	//提交数据
            'Intention': 'Ticket',
            'EndCity': EndCity,
            'TicketType':TicketType,
            'Theme':Theme,
            'TicketPrice':TicketPrice,
            'Sort':Sort,
            'Page':Page,
            'Keyword':Keyword,
        },
        beforeSend: function () { //加载过程效果
            $("#loading").show();
        },
        success: function(data) {	//函数回调
            if(data.ResultCode == "200"){
                DataSuccess(data);
            }else if(data.ResultCode == "100"){
                layer.msg('加载出错，请刷新页面重新选择!');
            }else if(data.ResultCode == "101"){
                DataFailure(data);
            }else if(data.ResultCode == "102"){     //搜索有内容
                $("#Position").empty();
                $("#Search").hide();
                $("#Position").append('> 搜索<span  style="color:red">'+'“'+Keyword+'”'+'</span>结果');
                DataSuccess(data);
            }else if(data.ResultCode == "103"){ //搜索无内容
                $("#Position").empty();
                $("#Search").hide();
                $("#Position").append('> 搜索<span  style="color:red">'+'“'+Keyword+'”'+'</span>结果');
                $("#Nosearch").empty();
                $("#Nosearch").append('很抱歉，暂时无法找到符合您要求的产品。');
                $("#Filter").hide();
                $("#conditionpanel").hide();
                $(".Sequence").hide();
                DataFailure(data);
            }
        },
        complete: function () { //加载完成提示
            $("#loading").hide();
        }
    });
}
//ajax 200 102状态时执行的方法
function DataSuccess(data) {
    //产品条数注入
    $("#ProductNum").empty();
    $("#ProductNum").append(data.RecordCount);
    //hide没有找到产品div
    $("#NoProduct").hide();
    //产品列表注入
    $('#CharacList').empty();
    var item;
    $.each(data.Data, function (i, list) {
        item =  '<li class="transition">' +
                    '<span data-id="'+list.TourRecommend+'" id="Recommend"></span>'+
                    '<a href="'+list.TourUrl+'" target="_blank">'+
                        '<p class="img"><img src="'+list.TourImg+'" class="transition" width="370" height="277" title="'+list.Tour_name+'"/></p>'+
                        '<p class="destination f16"><span class="endcitytext">目的地：'+list.TourEndCity+'</span></p>'+
                        '<p class="tit" title="'+list.Tour_name+'">'+list.Tour_name+'</p>'+
                    '</a>'+
                    '<div class="CharacListB">'+
                        '<p class="fr">'+
                            '<span class="fr oldPrice">￥'+list.TourCostPrice+'</span>'+
                            '<span class="fl nowPrice"><em>￥</em><i>'+list.TourPicre+'</i> 起</span>'+
                        '</p>'+
                        '<span class="fl playDate">游玩时间：'+list.TouDate+'</span>'+
                    '</div>'+
                '</li>';
        $('#CharacList').append(item);
    });
    //出发城市结束文本为null清空
    $(".destination").each(function() {
        var startcitytext = $(this).find('.startcitytext').text();
        var endcitytext = $(this).find('.endcitytext').text();
        if(startcitytext == '出发：null') {
            $(this).find('.startcitytext').text('');
        }
        if(endcitytext == '目的地：null') {
            $(this).find('.endcitytext').text('');
        }
    })
    //如果推荐增加class
    $("#Recommend").each(function () {
        var a = $(this).attr('data-id');
        if(a == '1'){
            $(this).addClass('HotTj1');
        }
    })
    //分页机制
    if($("#Page").attr('data-type') == '0'){
        $("#Page2").hide();
        $("#Page").attr('data-type','1');
    }
    if(data.PageCount > 1){
        diffPage(data);
        $("#Page").show();
    }else {
        $("#Page").hide();
    }
}

//ajax 101 103状态时执行的方法
function DataFailure(data) {
    $("#NoProduct").show(); //show没有找到产品div
    //产品条数注入
    $("#ProductNum").empty();
    $("#ProductNum").append('0');
    $('#TourLineList').empty();
    $('#Page').empty();
    //注入前清空
    $('#CharacList').empty();
    var item;
    $.each(data.Data, function (i, list) {
        item =  '<li class="transition">' +
            '<span data-id="'+list.TourRecommend+'" id="Recommend"></span>'+
            '<a href="'+list.TourUrl+'" target="_blank">'+
            '<p class="img">' +
            '<img src="'+list.TourImg+'" class="transition" width="370" height="277" title="'+list.Tour_name+'"/>' +
            '</p>'+
            '<p class="destination f16"><span class="endcitytext">目的地：'+list.TourEndCity+'</span></p>'+
            '<p class="tit">'+list.Tour_name+'</p>'+
            '</a>'+
            '<div class="CharacListB">'+
            '<p class="fr">'+
            '<span class="fr oldPrice">￥'+list.TourCostPrice+'</span>'+
            '<span class="fl nowPrice"><em>￥</em><i>'+list.TourPicre+'</i> 起</span>'+
            '</p>'+
            '<span class="fl playDate">游玩时间：'+list.TouDate+'</span>'+
            '</div>'+
            '</li>';
        $('#CharacList').append(item);
    });
    //出发城市结束文本为null清空
    $(".destination").each(function() {
        var startcitytext = $(this).find('.startcitytext').text();
        var endcitytext = $(this).find('.endcitytext').text();
        if(startcitytext == '出发：null') {
            $(this).find('.startcitytext').text('');
        }
        if(endcitytext == '目的地：null') {
            $(this).find('.endcitytext').text('');
        }
    })
    //如果推荐增加class
    $("#Recommend").each(function () {
        var a = $(this).attr('data-id');
        if(a == '1'){
            $(this).addClass('HotTj1');
        }
    })
}

