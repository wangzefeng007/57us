/**
 * Created by Foliage on 2017/2/27.
 */
$(function () {
    //性别保存
    var $oldSex = $("#sex label input:checked").attr('data-val');
    $("#saveBtn").on('click',function () {
        var ajaxData = {
            'Intention':"SaveInformation",
            'Type':'sex',
            'Sex':$("#sex label input:checked").attr('data-val'),
        }
        if($oldSex != ajaxData.Sex){
            $.ajax({
                type: "post",
                dataType: "json",
                url: "/userajax.html",
                data: ajaxData,
                beforeSend: function () {
                    $.showPreloader('提交中');
                },
                success: function(data) {
                    if(data.ResultCode == '200'){
                        $.toast('修改成功');
                        setTimeout(function() {
                            history.go(-1);
                        },500);
                    }else{
                        $.toast(data.Message);
                    }
                },
                complete: function () { //加载完成提示
                    $.hidePreloader();
                }
            });
        }else {
            $.toast('没做任何修改');
        }
    })
})