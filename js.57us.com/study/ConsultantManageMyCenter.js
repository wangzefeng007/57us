/**
 * Created by Foliage on 2016/9/29.
 */
$(function () {
    //点击查看消息
    $(".chatNewsMore").on('click',function () {
        var _type = $(this).attr('data-type')
        var _thidid = $(this).parents('li').attr("data-id");
        if(_type == '0'){
            systemInfo(_thidid);
        }else if(_type == '1'){
            InteractionInfo(_thidid);
        }
    })
})

html = '<div class="newsPop">' +
    '<p class="cf"><span class="fr f12 time"></span><span class="fl">您好，<b class="name"></b>：</span></p>' +
    '<p class="newsCont"><span class="info"></span><a href="" class="red url">去验证</a></p>' +
    '</div>';

html2 = '<div class="newsPop">' +
    '<p class="cf"><span class="fr f12 time"></span><span class="fl">您好，<b class="name"></b>：</span></p>' +
    '<p class="info"></p>' +
    '<p class="newsCont1 mt10"><span class="green">他的留言：</span><span class="message"></span></p>' +
    '</div>';

/*function systemInfo(_thidid) {
    ajaxData = {
        'ID':_thidid,
    }
    $.post("/commoncontroller/messageinfo/",ajaxData,function(data){
        if(data.ResultCode == "200"){
            layer.open({
                type: 1,
                title:"消息详情",
                closeBtn: 1,
                shadeClose: true,
                area:['360px','auto'],
                skin: 'newsPopBox',
                content: html,
                end: function () {
                    location.reload();
                }
            });
            $(".newsPopBox .time").text(data.Date);
            $(".newsPopBox .name").text(data.Name);
            $(".newsPopBox .info").text(data.Info);
            $(".newsPopBox .url").attr('href',data.Url);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}*/

/**
 *
 * @param _thidid 提交的id
 * @param data.Date 时间
 * @param data.Name 姓名
 * @param data.Title 标题
 * @param data.Message 留言
 */
function InteractionInfo(_thidid) {
    var ajaxData = {
        'ID':_thidid,
    }
    $.post("/commoncontroller/getmessagecontent/",ajaxData,function(data){
        if(data.ResultCode == "200"){
            layer.open({
                type: 1,
                title:"消息详情",
                closeBtn: 1,
                shadeClose: true,
                skin: 'newsPopBox',
                content: html2,
                end: function () {
                    location.reload();
                }
            });
            $(".newsPopBox .time").text(data.Date);
            $(".newsPopBox .name").text(data.Name);
            $(".newsPopBox .info").text(data.Title);
            $(".newsPopBox .message").text(data.Message);
        }else {
            layer.msg(data.Message);
        }
    },'json');
}