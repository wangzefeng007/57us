$(function () {
        var ID = $(this).attr('data-id');
        ajaxJson = {
            'ID': ID,
            'Intention': 'PassengerSave',
            'CnName': $('.EditeUlR input[name="CnName"]').val(),
            'EnNameX': $('.EditeUlR input[name="EnNameX"]').val(),
            'EnNameM': $('.EditeUlR input[name="EnNameM"]').val(),
            'Tel': $('.EditeUlR input[name="Tel"]').val(),
        };
    $(".pull-right").click(function () {
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/ajax/",
            data: ajaxData,
            beforeSend: function () { //加载过程效果
                // $("#loading").show();
            },
            success: function (data) {	//函数回调
                if (data.ResultCode == "200") {
                    DataSuccess(data)
                } else if (data.ResultCode == "100") {
                    layer.msg('加载出错，请刷新页面重新选择!');
                } else if (data.ResultCode == "101") {
                    DataFailure(data);
                } else if (data.ResultCode == "102") {     //搜索有内容
                    DataSuccess(data);
                } else if (data.ResultCode == "103") { //搜索无内容
                    DataFailure(data);
                }
            },
            complete: function () { //加载完成提示
                // $("#loading").hide();
            }
        })
    })
});

