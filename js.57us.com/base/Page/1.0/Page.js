/**
 * Created by Foliage on 2016/9/30.
 */

//分页机制
function diffPage(pageNumData,type) {
    var pageDemo = $('#pageDemo').html(); //获取模版
    var pageDemoParent = $('#Page');
    var showWhichPage = 1;
    $('#Page').empty();
    if(pageNumData.Page) {
        laytpl(pageDemo).render(pageNumData, function(html) {
            var $html = $(html);
            $html.appendTo('#Page').on('click', function() {
            });
            //页面点击事件
            $("#Page a").click(function () {
                var Page = $(this).attr('data-id');
                if (Page == '0'){
                    return
                }else if(Page == 'undefined'){
                    return
                }
                if(type == 1){
                    W_ScrollTo($('.selectBox').eq(0));
                }

                Ajax(Page);
            })
            //页码输入事件
            $("#pagebtn").click(function () {
                var Page = $("#pagenum").val();
                var pagemax = pageNumData.PageCount+1;
                if(Page == ''){
                    layer.msg('输入的页码不能为空');
                    return
                }else if(!/^\+?[1-9]\d*$/i.test(Page)){
                    layer.msg('请输入大于0的整数页码');
                    return
                }else if(Page >= pagemax){
                    layer.msg('输入的页码不能大于'+pageNumData.PageCount);
                    return
                }
                if(type == 1){
                    W_ScrollTo($('.selectBox').eq(0));
                }
                Ajax(Page);
            })

            var allPage = $html.parent().find('a');
            for(var i = 0; i < allPage.length; i++) {
                if(Number($(allPage[i]).html()) == pageNumData.Page) {
                    $(allPage[i]).addClass('on');
                }
            }
            if(pageNumData.Page === 1){
                $('.prve').addClass('prvestop')
            }
            if(pageNumData.Page === 1) {
                $(allPage[0]).addClass('no');
                pageDemoParent.find('.firstEllipsis').remove();
            }
            if(pageNumData.Page < 5) {
                pageDemoParent.find('.first').remove();
            }
            if(pageNumData.Page > 1) {
                $(".prev").removeClass("no");
            }
            if(pageNumData.Page > 5) {
                $(".first").after('<span class="firstEllipsis">...</span>');
            }

            if(pageNumData.PageCount < 7) {
                pageDemoParent.find('.lastEllipsis').remove();
                pageDemoParent.find('.PageCount').remove();
            }

            if(pageNumData.Page == pageNumData.PageCount) {
                $(allPage[allPage.length - 1]).addClass('no');
                pageDemoParent.find('.lastEllipsis').remove();
            }
            if(pageNumData.Page == pageNumData.PageCount) {
                $(allPage[allPage.length - 1]).addClass('no');
                $(".next").addClass('nextstop');
                pageDemoParent.find('.PageCount').remove();
            }
            if(pageNumData.Page == pageNumData.PageCount - 1) {
                pageDemoParent.find('.PageCount').remove();
                pageDemoParent.find('.lastEllipsis').remove();
            } else if(pageNumData.Page === pageNumData.PageCount - 2) {
                pageDemoParent.find('.lastEllipsis').remove();
                pageDemoParent.find('.PageCount').remove()
            }
            if(pageNumData.Page === pageNumData.PageCount - 3) {
                pageDemoParent.find('.lastEllipsis').remove();
            }
        });
    }
}