/**
 * Created by Foliage on 2017/2/13.
 */
$(function () {
    //设为默认地址
    $(document).on('click','.setDef',function (e) {
        e.stopPropagation();
        var _thisDom = $(this).parents('tr');
        var ajaxData = {
            'Intention': 'AddressSetDef', //邮寄列表，设为默认
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

    //删除地址
    $(document).on('click','.delAddress',function (e) {
        e.stopPropagation();
        var _thisDom = $(this).parents('tr');
        var ajaxData = {
            'Intention': 'DelAddress', //邮寄列表，删除地址
            'ID':_thisDom.attr('data-id'), //此条记录的id
        }
        //判断删除的记录是否为默认地址
        if(_thisDom.is('.on')){
            layer.confirm('确定要删除此默认邮寄地址？', {
                title:'删除提示',
                btn: ['确定','取消'] //按钮
            }, function(){
                deleteAddress(_thisDom,ajaxData);
            }, function(index){
                layer.close(index);
            });
        }else {
            deleteAddress(_thisDom,ajaxData);
        }
    })

})

/**
 * 删除邮寄方法
 *
 * @param _thisDom 为当前操作的邮寄地址记录的dom
 * @param ajaxData 删除邮寄地址所需要提示的ajax
 */
function deleteAddress(_thisDom,ajaxData) {
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
                if($(".addressLine").length < 1){
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