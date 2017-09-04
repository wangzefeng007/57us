/**
 * Created by Foliage on 2017/1/20.
 */
$(function () {
    //在线交流
    $("#Advisory").on('click',function () {
        $(".NfixMenu .i1").trigger('click');
    }); 
    $(".faceBtn").on('click',function () {
        $(".ewmPop").show();
    });
    $(".ewmPop .close").on('click',function () {
        $(".ewmPop").hide();
    });

    //获取url参数
    var data0 = GetQueryString('data0');
    var data1 = GetQueryString('data1');
    var data2 = GetQueryString('data2');
    var data3 = GetQueryString('data3');
    var data4 = GetQueryString('data4');
    var data5 = GetQueryString('data5');
    var data6 = GetQueryString('data6');
    var data7 = GetQueryString('data7');
    var data8 = GetQueryString('data8');
    var data9 = GetQueryString('data9');
    var data10 = GetQueryString('data10');
    var data11 = GetQueryString('data11');
    var data12 = GetQueryString('data12');
    var data13 = GetQueryString('data13');
    var data14 = GetQueryString('data14');
    var data15 = GetQueryString('data15');
    var data16 = GetQueryString('data16');
    var data17 = GetQueryString('data17');

    //相关数据注入表格
    $("#data0").text(data0);
    $("#data1").text(data1);
    $("#data2").text(data2);
    $("#data3").text(data3);
    $("#data4").text(data4);
    $("#data5").text(data5);
    $("#data6").text(data6);
    $("#data7").text(data7);
    $("#data8").text(data8);
    $("#data9").text(data9);
    $("#data10").text(data10);
    $("#data11").text(data11);
    $("#data12").text(data12);
    $("#data13").text(data13);
    $("#data14").text(data14);
    $("#data15").text(data15);
    $("#data16").text(data16);
    $("#data17").text(data17);

    //总分计算并注入
    //R总分'
    var RScore = Number(data0) + Number(data6) + Number(data12);
    $("#Total0").text(RScore);

    //I总分
    var IScore = Number(data1) + Number(data7) + Number(data13);
    $("#Total1").text(IScore);

    //A总分
    var AScore = Number(data2) + Number(data8) + Number(data14);
    $("#Total2").text(AScore);

    //S总分
    var SScore = Number(data3) + Number(data9) + Number(data15);
    $("#Total3").text(SScore);

    //E总分
    var EScore = Number(data4) + Number(data10) + Number(data16);
    $("#Total4").text(EScore);

    //C总分
    var CScore = Number(data5) + Number(data11) + Number(data17);
    $("#Total5").text(CScore);

    //ajax提交
    $("#submit").on('click',function () {
        //获取url各科成绩
        var _Url = window.location.search.split('?');
        var Fraction = _Url[1];
        var TotalScore = 'RScore='+RScore+'&IScore='+IScore+'&AScore='+AScore+'&SScore='+SScore+'&EScore='+EScore+'&CScore='+CScore;
        var ajaxData = {
            'Intention':'SaveTest', //方法名
            'Fraction':Fraction, //各科成绩
            'TotalScore':TotalScore, //各部分总成绩
            'Tel':$("#tel").val(), //手机号码
        }
        if(ajaxData.Tel == ''){
            layer.msg('请输入手机号码');
            return
        }else if(rule.phone.test(ajaxData.Tel) != true){
            layer.msg('手机号码格式不正确');
            return
        }
        $.post('./ajax.php',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                layer.msg(data.Message);
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })

})