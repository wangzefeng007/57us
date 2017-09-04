$(function(){
    //所有产品滚动
    jQuery(".ScrollPic").slide({mainCell:".ScrollMain ul",autoPage:true,effect:"left",vis:4,pnLoop:'false'});
    //所有留学滚动
    jQuery(".TearchScroll").slide({mainCell:".TearchScrMain ul",scroll:8,autoPage:true,autoPlay:true,delayTime:1500,effect:"left",pnLoop:'true',vis:8});
    //右侧标题切换
    $(".HotNewsList li").hover(function(){
        $(this).addClass("on").siblings().removeClass("on")
    })
})