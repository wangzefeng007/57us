/**
 * Created by Foliage on 2017/1/16.
 */
$(function () {
    //我要提问代码
    if($(".DiySelect").length){
        $('.DiySelect[name="areaSelect"]').inputbox({
            height:34,
            width:130
        });
    }
    
    //及时提问
    $(".lineAskBtn").on('click',function () {
        $(".NfixMenu .i1").trigger('click');
    });
    //分享
	$(".askshare").on('click',function(){
		$(".sharePop").addClass("on");
	});
	$(".sharePop .close").on('click',function(){
		$(".sharePop").removeClass("on");
	})

    //我要提问
    $(".MyQuestions").on('click',function () {
        $("#askPop").show();
        getHeight();
    })

    //自动生成相关的标签
    $("#QuestionText").blur(function () {
        var ajaxData = {
            'Intention':'OsTags', //系统标签
            'AskInfo':html_encode($(this).val()), //提问内容
        }
        $.post('/askajax/',ajaxData,function (data) {
            if(data.ResultCode == "200"){
                $(".tipGet .OsTags").remove();
                $.each(data.Tags,function(n,list){
                    $(".ostag").before('<span class="OsTags">'+list+'<em class="tipDete"><i class="icon iconfont icon-guanbifuzhi"></i></em></span>');
                });
            }
        },'json');
    })

    //删除自动生成的标签
    $(document).on('click','.OsTags i',function () {
        $(this).parents('.OsTags').remove();
        tagsLength();
    })

    //自定义标签html
    var TagHtml = '<span class="autoSpan"><input type="text" name="tag" value="" placeholder="自定义" maxlength="8"/><em class="tipDete"><i class="icon iconfont icon-guanbifuzhi"></i></em></span>';
    //增加自定义标签
    $("#addTag").on('click',function () {
        $(this).before(TagHtml);
        tagsLength();
    })

    //删除自定义标签
    $(document).on('click','.autoSpan i',function () {
        $(this).parents('.autoSpan').remove();
        tagsLength();
    })

    //我要提问问题提交
    $("#AskSubmit").on('click',function () {
        var OsTags = [];
        $(".tipGet .OsTags").each(function () {
            OsTags.push($(this).text());
        })

        var CMTags = [];
        $(".tipGet .autoSpan").each(function () {
            CMTags.push($(this).find('input').val());
        })

        //标签，合并去重
        var Tags = unique(OsTags.concat(CMTags));

        var ajaxData = {
            'Intention': 'AddQuestion', //方法
            'AskCategoryID':$("#QuestionClass input").val(), //分区ID 站队传0
            'AskInfo':html_encode($("#QuestionText").val()), //提问内容
            'Tag':Tags, //系统生成标签数组
        }

        if($("#QuestionText").val().length < 1){
            layer.msg('问题详情不能为空');
            return
        }

        //执行ajax提交
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/askajax/",
            data: ajaxData,
            beforeSend: function () {
                //提交加载效果
                public_loading();
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location = data.Url;
                    },500);
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $("#public_loading").remove();
            }
        });
    })


    //我来回答
    $(".myReply").on('click',function() {
        $("#comeanswer").show();
        getHeight();
    });

    //我来回答提交
    $("#myReplySubmit").on('click',function () {
        var ajaxData ={
            'Intention': 'AddAnswer', //方法
            'ID':$("#AskId").val(), //话题ID
            'AnswerInfo':html_encode($("#myReplyText").val()), //回答内容
        }

        if($("#myReplyText").val().length < 1){
            layer.msg('回答内容不能为空');
            return
        }

        //执行ajax提交
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/askajax/",
            data: ajaxData,
            beforeSend: function () {
                //提交加载效果
                public_loading();
            },
            success: function(data) {
                if(data.ResultCode == "200"){
                    layer.msg(data.Message);
                    setTimeout(function(){
                        window.location.reload();
                    },500);
                }else {
                    layer.msg(data.Message);
                }
            },
            complete: function () { //加载完成提示
                $("#public_loading").remove();
            }
        });
    })

    //所有模态窗口关闭事件
    $(".pop .popClose").click(function() {
        $(".popmask").fadeOut();
        $(".pop").css({
            "margin-top": -1500 + 'px'
        });
    });

    //关注,取消关注提交
    $(".AskAttention").on('click',function () {
        var ajaxData = {
            'Intention': 'AttentionAsk', //方法
            'AskID':$("#AskId").val(),
        }
        $.post('/askajax/',ajaxData,function (data) {
            //200代表关注成功  201代表取消关注
            if(data.ResultCode == "200"){
                layer.msg(data.Message);
                $(".AskAttentionNum").text(data.Num);
                $(".AskAttention").html('<i class="icon iconfont icon-ok"></i>已关注');
                $(".AskAttention").addClass('on');
            }else if(data.ResultCode == '201'){
                layer.msg(data.Message);
                $(".AskAttentionNum").text(data.Num);
                $(".AskAttention").html('<i class="icon iconfont icon-start"></i>关注');
                $(".AskAttention").removeClass('on');
            }else {
                layer.msg(data.Message);
            }
        },'json')
    })

    //点赞提交
    $('.AskZan').on('click',function () {
        var ajaxData = {
            'Intention': 'AskThumbup', //方法
            'AnswerID':$(this).attr('data-id'),
        }
        var _this = $(this);
        $.post('/askajax/',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                _this.find('.AskZanNum').text(data.Num);
                _this.addClass('on');
            }else {
                layer.msg(data.Message);
            }
        },'json')
    })

    //更换人气话题tags
    $("#changeTags").on('click',function () {
        var ajaxData = {
            Intention:'TopicHot',
            Type:$(this).attr('data-type'),
        }
        $.post('/askajax/',ajaxData,function (data) {
            if(data.ResultCode == '200'){
                $("#changeChanHtml").empty();
                var item;
                $.each(data.Data, function(i, list) {
                    item = '<a href="'+list.Url+'" data-id="'+list.Id+'" class="bradius" target="_blank" title="'+list.Name+'">'+list.Name+'</a>';
                    $('#changeChanHtml').append(item);
                });
            }else {
                layer.msg(data.Message);
            }
        },'json');
    })

    //查看更多
    $(".moreArea").click(function(){
        $(this).parents(".otherAreaM").toggleClass("auto")
    })

    $(".askshare").on('click',function () {
        $('#bdshare .bds_more').click();
    })
})

//判断是否还可以增加自定义标签
function tagsLength() {
    var _num = $(".tipGet span").length;
    //当标签大于10时，隐藏增加按钮
    if(_num >= 10){
        $("#addTag").hide();
    }else {
        $("#addTag").show();
    }
}

//获取弹窗高度以及居中
function getHeight() {
    $(".pop").each(function(){
        var boxHight = $(this).outerHeight();
        var marTop = boxHight / 2;
        $(this).css({
            "margin-top": -marTop + 'px'
        });
    })
}