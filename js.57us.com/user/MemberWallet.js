$(function(){
    //表格效果
    $(".TransactionTab tbody tr").mousemove(function(){
        if($(this).hasClass("default")){
        }else{$(this).addClass("TrOn");}

    }).mouseout(function(){
        $(this).removeClass("TrOn");;
    })
});