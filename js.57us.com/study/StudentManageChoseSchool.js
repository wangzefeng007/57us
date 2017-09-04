/**
 * Created by Foliage on 2016/11/7.
 */
$(function () {
    //确定选校点击
    $(document).on("click",".sureBtn",function(){
        var _thisID = $(this).attr('data-id');
        layer.confirm('您确定定稿吗', {
            btn: ['确定','取消'] //按钮
        }, function(){
            // //确定选校
            if($(this).attr('data-type') == '0'){
                layer.msg('请先操作未确认的学校，再确认定校');
                return
            }
            ajaxData = {
                'Intention': 'StudentSureSchool', //方法
                'ID':_thisID, //对应的id
            }
            $.post("/studentmanageajax/",ajaxData,function(data){
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                }else {
                    layer.msg(data.Message);
                }
            },'json');

        });
    });


    $(".Accepted").on('click',function () {
        ajaxData = {
            'Intention': 'StudentChoseSchool', //方法
            'ID':$(this).parent().parent('tr').attr('data-id'), //对应的id
            'Type':'1', // 1代表已确定，
        }
        $.post("/studentmanageajax/",ajaxData,function(data){
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })
    //驳回选校点击
    $(".Reject").on('click',function () {
        ajaxData = {
            'Intention': 'StudentChoseSchool', //方法
            'ID':$(this).parent().parent('tr').attr('data-id'), //对应的id
            'Type':'0', // 0代表驳回，
        }
        $.post("/studentmanageajax/",ajaxData,function(data){
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })

})