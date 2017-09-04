$(function(){
//表格效果
    $(".AdressTab tbody tr").mousemove(function(){
        if($(this).hasClass("default")){
        }else{$(this).addClass("TrOn");}

    }).mouseout(function(){
        $(this).removeClass("TrOn");;
    })

    $(".Deletess").click(function(){
        var ID = $(this).attr('data-id');
        ajaxJson = {'Intention':'PassengerDel','ID': ID};
        layer.confirm('确定删除此地址？', {
            title:false,
            closeBtn: 0,
            btn: ['确定','取消'] ,
            yes: function(index, layero){
                layer.close(index);
                $.post('/userajax.html', ajaxJson, function (data) {
                    layer.msg(data.Message);
                    window.location.reload();
                }, 'json');
            }});
    })
});