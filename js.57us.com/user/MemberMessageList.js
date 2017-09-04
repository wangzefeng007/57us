$(function(){
    $(".NewsList li a").each(function(){
        $(this).click(function(){
            var thisDom=$(this);
            var ID=thisDom.siblings('label[name="check"]').attr('val');
            if(thisDom.parents('li').hasClass('NoRead')){
                var isNoRead=true;
            }else{
                var isNoRead=false;
            }
            $.post('/usermessage/getmessage/',{ID:ID},function(json){
                if(json.ResultCode=='200'){
                    var NewsPopHtml = $(".NewsPopMain")
                    layer.open({
                        type: 1,
                        area: ['620px', '470px'],
                        skin: 'DomeNews',
                        shift: 2,
                        title:0,
                        btn: ['确定']
                        ,yes: function(index, layero){
                            layer.close(index);
                        },
                        content: NewsPopHtml.html(),
                        success: function(layero, index){
                            $(".NewsPop .NewsPopTit").text(json.Data.Title);
                            $(".NewsPop .NewsPopTime").text(json.Data.AddTime);
                            $(".NewsPop .NewsPopCont").text(json.Data.Content);
                            if(isNoRead){
                                thisDom.parents('li').removeClass('NoRead');
                                $('span#noread').text(parseInt($('span#noread').text())-1);
                            }
                        }
                    });
                }else if(json.ResultCode=='100'){
                    layer.msg(json.Message);
                    setTimeout(function(){
                        window.location=json.Url;
                    },800);
                }else{
                    layer.msg(json.Message);
                }
            },'json');
        })
    })

    //删除
    $("#delBtn").click(function(){
        var IDS=[];
        $("ul.NewsList li label[name='check']").each(function(){
            if($(this).hasClass('cb_active')){
                IDS.push($(this).attr('val'));
            }
        })
        if(IDS.length<1){
            layer.msg('请选择要删除的信息!');
        }else{
            $.post('/usermessage/delmessage/',{IDS:IDS},function(json){
                if(json.ResultCode=='200' || json.ResultCode=='101'){
                    layer.msg(json.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },800);
                }else if(json.ResultCode=='100'){
                    layer.msg(json.Message);
                    setTimeout(function(){
                        window.location=json.Url;
                    },800);
                }
            },'json');
        }
    })
});