/**
 * Created by Foliage on 2016/11/7.
 */
$(function () {
    //学样申请，选择录取学校
    $('.applyschool').on('click',function () {
        ajaxData = {
            'Intention': 'StudentApplySchool', //方法
            'OrderID':$(this).parents('table').attr('data-id'), //对应的订单id
            'ID':$(this).attr('data-id'), //此录取表格当前行对应的id
        }
        var _title = $(this).attr('data-title');

        layer.confirm('您确认要选择<span style="color: red">'+_title+'</span>这所学校吗?', {
            btn: ['确认','取消'] //按钮
        }, function(index){
            $.post("/studentmanageajax/",ajaxData,function(data){
                if(data.ResultCode == "200"){
                    layer.close(index);
                    layer.msg(data.Message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                }else {
                    layer.msg(data.Message);
                }
            },'json');
        },function (index) {
            layer.close(index);
        });
    })
})