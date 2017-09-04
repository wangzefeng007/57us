/**
 * Created by Foliage on 2017/2/9.
 */
(function($){
    var ms = {
        init:function(obj,args){
            return (function(){
                ms.fillHtml(obj,args);
                ms.bindEvent(obj,args);
            })();
        },
        //填充html
        fillHtml:function(obj,args){
            if(args.pageCount <= 1){
                return
            }
            return (function(){
                obj.empty();
                //上一页
                if(args.current > 1){
                    obj.append('<a href="javascript:;" class="prevPage">上一页</a>');
                }else{
                    obj.remove('.prevPage');
                    obj.append('<a href="javascript:void(0)" class="prvestop">上一页</a>');
                }
                //中间页码
                if(args.current != 1 && args.current >= 4 && args.pageCount != 4){
                    obj.append('<a href="javascript:void(0);" class="tcdNumber">'+1+'</a>');
                }
                if(args.current-2 > 2 && args.current <= args.pageCount && args.pageCount > 5){
                    obj.append('<span>...</span>');
                }
                var start = args.current -2,end = args.current+2;
                if((start > 1 && args.current < 4)||args.current == 1){
                    end++;
                }
                if(args.current > args.pageCount-4 && args.current >= args.pageCount){
                    start--;
                }
                for (;start <= end; start++) {
                    if(start <= args.pageCount && start >= 1){
                        if(start != args.current){
                            obj.append('<a href="javascript:void(0);" class="tcdNumber">'+ start +'</a>');
                        }else{
                            obj.append('<a href="javascript:void(0)" class="current on">'+ start +'</a>');
                        }
                    }
                }
                if(args.current + 2 < args.pageCount - 1 && args.current >= 1 && args.pageCount > 5){
                    obj.append('<span>...</span>');
                }
                if(args.current != args.pageCount && args.current < args.pageCount -2  && args.pageCount != 4){
                    obj.append('<a href="javascript:void(0);" class="tcdNumber">'+args.pageCount+'</a>');
                }
                //下一页
                if(args.current < args.pageCount){
                    obj.append('<a href="javascript:void(0);" class="nextPage">下一页</a>');
                }else{
                    obj.remove('.nextPage');
                    obj.append('<a href="javascript:void(0)" class="nextstop">下一页</a>');
                }
                //跳转页面
                if(args.pageCount > 1){
                    obj.append('<div class="GoPage">到 <input type="text" id="pageNum" value="" class="input" maxlength="4"/> 页 <input type="button" name="" id="pageBtn" value="确定" class="PageBtn"/></div>');
                }
            })();
        },
        //绑定事件
        bindEvent:function(obj,args){
            return (function(){
                $('.tcdPageCode a.tcdNumber').on("click",function(){
                    var current = parseInt($(this).text());
                    ms.fillHtml(obj,{"current":current,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current);
                    }
                });
                //上一页
                $('.tcdPageCode a.prevPage').on("click",function(){
                    var current = parseInt(obj.children("a.current").text());
                    ms.fillHtml(obj,{"current":current-1,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current-1);
                    }
                });
                //下一页
                $('.tcdPageCode a.nextPage').on("click",function(){
                    var current = parseInt(obj.children("a.current").text());
                    ms.fillHtml(obj,{"current":current+1,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current+1);
                    }
                });
                //跳转页面
                $("#pageBtn").on('click',function () {
                    var pageNum = parseInt($("#pageNum").val());
                    if(pageNum == ''){
                        layer.msg('输入页码不能为空');
                        return
                    }else if(pageNum > args.pageCount){
                        layer.msg('输入页码不能大于'+args.pageCount+'');
                        return
                    }else{
                        args.backFn(pageNum);
                    }
                })
            })();
        }
    }
    $.fn.createPage = function(options){
        var args = $.extend({
            pageCount : 15,
            current : 1,
            backFn : function(){}
        },options);
        ms.init(this,args);
    }
})(jQuery);