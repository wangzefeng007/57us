/**
 * Created by Foliage on 2016/11/24.
 */
$(function () {
     //所在城市
    $.post('/study/getcity/',function (data) {
        if(data.ResultCode == '200'){
            var _City = data.DataCity;
            $("#city-picker").picker({
                toolbarTemplate: '<header class="bar bar-nav diyCity">\<button class="button button-link pull-right close-picker">确定</button>\<h1 class="title">所在地区</h1>\</header>',
                cols: [
                    {
                        textAlign: 'center',
                        values: _City,
                        cssClass: 'picker-items-col-normal'
                    }
                ]
            });
        }else {
            $.toast(data.Message);
        }
    },'json');

    //申请层次
    $.get('/study/gettargetlevel/',function (data) {
        $("#Level").empty();
        var item;
        $.each(data, function(i, list) {
            item = '<option value="'+list.ID+'">'+list.Name+'</option>';
            $("#Level").append(item);
        });
    },'json');
    //意向服务
    $.get('/study/getservicetype/',function (data) {
        $("#ServiceType").empty();
        var item;
        $.each(data, function(i, list) {
            item = '<option value="'+list.ID+'">'+list.Name+'</option>';
            $("#ServiceType").append(item);
        });
    },'json');

    $(".sexyChose label").on('click',function () {
        $(".sexyChose label").removeClass('on');
        $(this).addClass('on');
    })

    $(".getSolution").on('click',function () {
        var ajaxData = {
            'Intention':'MarryInfoSave', //方法
            'MarryName':$("#name").val(), //姓名
            'MarrySex': $(".sexyChose .on").attr('data-type'), //姓名
            'GoAbroadTime':$("#GoAbroadTime").val(), //预计出国时间
            'ContactWay':$("#ContactWay").val(), //联系方式
            'MarryCity':$("#city-picker").val(),
            'MarryTargetLevel':$('#Level').find('option').not(function() {return !this.selected}).val(), //申请层次
            'MarryServiceType':$('#ServiceType').find('option').not(function() {return !this.selected}).val(), //意向服务
            'MarryGrade':$("#grade").find('option').not(function() {return !this.selected}).val(), //所在年级
        }
        if(ajaxData.MarryName == ''){
            $.toast('请输入姓名');
            return
        }else if(ajaxData.MarryCity == ''){
            $.toast('请选择所在城市');
            return
        }else if(ajaxData.GoAbroadTime == ''){
            $.toast('请输入预计出国时间');
            return
        }else if(rule.Num.test(ajaxData.ContactWay) != true){
            $.toast('请输入正确的手机号码');
            return
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajaxstudy/",
            data: ajaxData,
            success: function(data) {
                if(data.ResultCode == '200'){
                    var Url = data.Url;
                    setTimeout(function(){
                        window.location=Url;
                    },200);
                }else{
                    $.toast(data.Message)
                }
            }
        });
    })
})