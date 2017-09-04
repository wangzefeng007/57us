/**
 * Created by Foliage on 2017/2/28.
 */
$(function () {
    //旅客设为默认
    $(".passengerSetDef").on('click',function () {
        var $thisDom = $(this);
        var $thisId = $(this).attr('data-id');
        var $thisVal = $(this).find('input').attr('checked');
        if($thisVal != 'true'){
            var ajaxData = {
                'Intention': 'PassengerSetDef', //设置默认
                'ID': $thisId, //旅客信息ID
            };
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/userajax.html",
                data: ajaxData,
                beforeSend: function () {
                    $.showPreloader('提交中');
                },
                success: function(data) {
                    if(data.ResultCode=='200'){
                        $.toast('设置成功');
                        $(".passengerSetDef input").removeAttr('checked');
                        $thisDom.find('input').attr('checked','true');
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
    //地址设为默认
    $(".addressSetDef").on('click',function () {
        var $thisDom = $(this);
        var $thisId = $(this).attr('data-id');
        var $thisVal = $(this).find('input').attr('checked');
        if($thisVal != 'true'){
            var ajaxData = {
                'Intention': 'AddressSetDef', //设置默认
                'ID': $thisId, //旅客信息ID
            };
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/userajax.html",
                data: ajaxData,
                beforeSend: function () {
                    $.showPreloader('提交中');
                },
                success: function(data) {
                    if(data.ResultCode=='200'){
                        $.toast('设置成功');
                        $(".addressSetDef input").removeAttr('checked');
                        $thisDom.find('input').attr('checked','true');
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
})