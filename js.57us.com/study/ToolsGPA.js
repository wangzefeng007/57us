/**
 * Created by Foliage on 2016/10/21.
 */
$(function () {
    //增加科目
    $('#AddSubject').click(function () {
        var Num = Number($('.num:last').text()) + Number('1');
        html = '<tr>' +
            '<td align="center">科目<span class="num">'+Num+'</span></td>' +
            '<td><input type="text" name="Score" value="" class="input Score" /></td>' +
            '<td><input type="text" name="Credit" value="" class="input Credit" /></td>' +
            '</tr>';
        $('#AddTr').before(html);
    })
    //点击计算结果
    $("#Calculation").click(function () {
        if(rule.Num.test($('.Credit:first').val()) != true){
            layer.msg('科目一学分必须填写');
            return;
        }
        var sum=0;
        var sum2=0;
        $("table tr").each(function (i) {
            //取百分制成绩
            var Score = $(this).find('.Score').val();
            //取学分
            var Credit = $(this).find(".Credit").val();
            //正整数化百分制成绩，学分
            Score = parseInt(Score);
            Credit = parseInt(Credit);
            //判断是百分制成绩，学分是否正确
            if(!(isNaN(Score) || isNaN(Credit))) {
                //取学分X成绩的和
                sum += (Score * Credit);
                //取学分和
                sum2+= Credit;
            }
        })
        //计算GPA
        var GPA = (Number(sum) * Number('4')) / (Number(sum2) * Number('100'));
        //四舍五入取小数点两位数
        var GRANum = GPA.toFixed(2);
        //判断计算结果，是否是整数不是整数，输出提示
        if(GRANum == 'NaN'){
            layer.msg('请在同一行内输入整数');
        }else {
            $("#Result").val(GRANum);
        }
    })
})