/**
 * Created by Foliage on 2016/11/7.
 */
var showNum = resetNum = 8;
$(function() {
    $('.ListCont').scroll(function(){
        var $this =$(this),
            viewH =$(this).height(),//可见高度
            contentH =$(this).get(0).scrollHeight,//内容高度
            scrollTop =$(this).scrollTop();//滚动高度
        if(scrollTop/(contentH -viewH)>=0.99){ //到达底部100px时,加载新内容
            // 这里加载数据..
            showNum += resetNum;
            $(".gun").slice(0,showNum).removeClass('hidden');
            load(showNum);
        }
    });
})

function load(num) {
    num += resetNum;
    $(".gun").slice(0,num).removeClass('hidden');
    if(num == '32'){
        $(".infinite-scroll-preloader").hide();
        $('#nextpage').show();
    }
}