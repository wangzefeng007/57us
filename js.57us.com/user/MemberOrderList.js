$(function(){
    //表格效果
    $(".AdressTab tbody tr").mousemove(function(){
        if($(this).hasClass("default")){
        }else{$(this).addClass("TrOn");}

    }).mouseout(function(){
        $(this).removeClass("TrOn");;
    })
});

function Deletes(type){
    var data = new Array();
    $("input:hidden[name='check']:checked").each(function() {
        data.push($(this).val());
    });
    if(data.length<1){
        layer.msg('至少选一个选项');
        return false;
    }
    ajaxJson = {'data': data};
    var url = '/tourmember/'+type+'/';
    layer.confirm('确定删除此记录？', {
        title:false,
        closeBtn: 0,
        btn: ['确定','取消'] ,
        yes: function(index, layero){
            layer.close(index);
            $.post(url, ajaxJson, function (data) {
                layer.msg(data.Message);
                window.location.reload();
            }, 'json');
        }});
}