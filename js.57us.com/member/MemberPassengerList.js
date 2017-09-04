/**
 * Created by Foliage on 2017/2/13.
 */
$(function () {
    //设为默认旅客
    $(document).on('click','.setDef',function (e) {
        e.stopPropagation();
        var _thisDom = $(this).parents('tr');
        var ajaxData = {
            'Intention': 'PassengerSetDef', //旅客列表，设为默认
            'ID':_thisDom.attr('data-id'), //此条记录的id
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
                if(data.ResultCode == '200'){
                    $("#list tr").removeClass('on');
                    _thisDom.addClass('on');
                    layer.msg(data.Message);
                }else{
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

    //删除旅客
    $(document).on('click','.delPassenger',function (e) {
        e.stopPropagation();
        var _thisDom = $(this).parents('tr');
        var ajaxData = {
            'Intention': 'DelPassenger', //旅客列表，删除旅客
            'ID':_thisDom.attr('data-id'), //此条记录的id
        }
        //判断删除的记录是否为默认旅客
        if(_thisDom.is('.on')){
            layer.confirm('确定要删除此默认旅客？', {
                title:'删除提示',
                btn: ['确定','取消'] //按钮
            }, function(){
                deletePassenger(_thisDom,ajaxData);
            }, function(index){
                layer.close(index);
            });
        }else {
            deletePassenger(_thisDom,ajaxData);
        }
    })

    //搜索旅客回车事件
    $("#passengerSearch").submit(function() {
        $("#searchBtn").trigger('click');
        return false;
    })

    //搜索旅客点击事件
    $("#searchBtn").on('click',function () {
        var ajaxData = {
            'Intention': 'PassengerSearch', //旅客列表，搜索
            'SearchVal':$("#searchVal").val(), //搜索内容
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
                if(data.ResultCode == '200'){
                    $(".PassengerLine").remove();
                    //搜索到相关数据注入
                    var item;
                    $.each(data.Data, function(i, list) {
                        item = '<tr class="PassengerLine" data-id="'+list.List_ID+'" data-type="'+list.List_Default+'">' +
                            '<td>'+list.List_Name+'</td>' +
                            '<td>'+list.List_Sex+'</td>' +
                            '<td>'+list.List_Mobile+'</td>' +
                            '<td>护照</td>' +
                            '<td>'+list.List_Card+'</td>' +
                            '<td>' +
                            '<a href="JavaScript:void(0)" class="hasDef">默认旅客</a>' +
                            '<a href="JavaScript:void(0)" class="stepDef setDef">设为默认</a>' +
                            '<a href="'+list.List_Url+'" class="stepchange">修改</a>' +
                            '<a href="JavaScript:void(0)" class="stepdete delPassenger">删除</a>' +
                            '</td>' +
                            '</tr>';
                        $('#list').append(item);
                    });
                    //查看搜索出来的相关旅客信息是否有默认旅客，如果有则执行添加on class
                    $("#list .PassengerLine").each(function () {
                        var _type = $(this).attr('data-type');
                        if(_type == '0'){
                            $(this).addClass('on');
                        }
                    })
                }else if(data.ResultCode == '101'){
                    layer.msg(data.Message);
                }else{
                    $(".PassengerLine").remove();
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                layer.closeAll('loading');
            }
        });
    })

})

/**
 * 删除旅客方法
 *
 * @param _thisDom 为当前操作的旅客记录的dom
 * @param ajaxData 删除旅客所需要提示的ajax
 */
function deletePassenger(_thisDom,ajaxData) {
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/userajax.html",
        data: ajaxData,
        beforeSend: function () {
            layer.load(2);
        },
        success: function(data) {
            if(data.ResultCode == '200'){
                _thisDom.remove();
                layer.msg(data.Message);
                //当删除掉所有旅客时，刷新页面
                if($(".PassengerLine").length < 1){
                    setTimeout(function(){
                        window.location.reload();
                    },500);
                }
            }else{
                layer.msg(data.Message);
            }
        },
        complete: function () { //加载完成提示
            layer.closeAll('loading');
        }
    });
}