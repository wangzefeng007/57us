/**
 * Created by Foliage on 2017/1/20.
 */
$(function () {
    $.get("/topic/study2017/data/data.json",function (json) {
        Data = json;
        listWrite(0);
    },'json');

    //选择是否按钮
    $(document).on('click','.choseBtn a',function () {
        $(this).parent().find('a').removeClass('on');
        $(this).addClass('on');
        //写入提交到下个页面数据
        var fraction = $(".fraction.on").length;
        $("#data" + Num + "").val(fraction);
    })

    //点击下一步
    $('#nextBtn').on('click',function () {
        //设置目前第几步
        var _num = $('#indexId').val();
        $('#indexId').val(Number(_num) + Number('1'));

        //定义目前几步
        Num = $('#indexId').val();

        //计算进度百分比
        var _schedule = 100 / 18 * (Number(_num) + Number('1'));
        $("#schedule").text(parseInt(_schedule));

        if(Num >= 18){
            var _thisDom = $('#listData').serialize();
            window.location = '/topic/study2017/pcr.html?'+_thisDom;
            return
        }
        //列表数据写入
        listWrite(Num);
    })
})
var Data;
var Num = '0';
//列表数据写入方法
function listWrite(num) {
    //定义第几步使用的数组
    var list = Data[num];

    //标题的注入
    $("#title").text(list.title);
    $("#subtitle").text(list.subtitle);
    $("#description").text(list.description);

    //列表数据注入
    $("#list").empty();
    var html = '';
    $.each(list.data,function(n,list){
        html+= '<li>' +
            '<p class="tit">'+list+'</p>' +
            '<div class="choseBtn">' +
            '<a href="JavaScript:void(0)" class="fraction">是</a>' +
            '<a href="JavaScript:void(0)">否</a>' +
            '</div>' +
            '</li>';
    });
    $("#list").append(html);
}
