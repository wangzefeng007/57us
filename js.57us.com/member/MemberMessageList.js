/**
 * Created by Foliage on 2017/2/9.
 */
$(function () {
    //表单美化
    $('.newsTab .cbt').inputbox();

    //单条删除
    $(".oneDel").on('click',function () {
        var _thisDom = $(this).parents('tr');
        var ajaxData = {
            'Intention':'OneMessageDel', //方法名
            'ID':$(this).parents('tr').attr('data-id'), //对应的单个ID
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    layer.msg(data.Message);
                    _thisDom.remove();
                    if($("#messagesList tr").length < 2){
                        setTimeout(function(){
                            window.location.reload();
                        },300);
                    }
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });

    })

    //多条删除
    $("#multiDel").on('click',function (){
        var _messagesList = [];
        $(".allMessage .tac .checked").each(function () {
            _messagesList.push($(this).parents('tr').attr('data-id'));
        })

        var ajaxData = {
            'Intention':'MessageMultiOperate', //方法名
            'IDs':_messagesList, //多个对应Id，一维数组
            'Status':3,
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },500);
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    //多条标识已读
    $("#multiRead").on('click',function () {
        var _messagesList = [];
        $(".noRead .tac .checked").each(function () {
            _messagesList.push($(this).parents('tr').attr('data-id'));
        })

        var ajaxData = {
            'Intention':'MessageMultiOperate', //方法名
            'IDs':_messagesList, //多个对应Id，一维数组
            'Status':2,
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },500);
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    });
    //获取消息内容
    $(".smstitle").on('click',function(){
        var $this = $(this);
        var $thisId = $this.parents('tr').attr('data-id');
        var $superiorDom = $this.parents('tr');
        var ajaxData = {
            'Intention':'ReadMessageContent', //方法名
            'ID':$thisId, //多个对应Id，一维数组
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/userajax.html",
            data: ajaxData,
            beforeSend: function () {
                layer.load(2);
            },
            success: function(data) {
                if(data.ResultCode=='200'){
                    $superiorDom.find('.smstext').empty();
                    $superiorDom.find('.smstext').text(data.text);
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })
})