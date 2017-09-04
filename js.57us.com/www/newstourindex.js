$(function(){
//	$(".B_Demo").PicCarousel({
//		"width":980,		//幻灯片的宽度
//		"height":426,		//幻灯片的高度
//		"posterWidth":566,	//幻灯片第一帧的宽度
//		"posterHeight":426, //幻灯片第一张的高度
//		"scale":0.7,		//记录显示比例关系
//		"speed":1500,		//记录幻灯片滚动速度
//		"autoPlay":true,	//是否开启自动播放
//		"delay":10000,		//自动播放间隔
//		"verticalAlign":"middle"	//图片对齐位置
//	});
// //
    pic3D();
})
function pic3D(){
    window.onload = function(){
        var oBut = document.getElementById('list');
        var aLi = oBut.getElementsByTagName('li');
        var aA = oBut.getElementsByTagName('b');
        var i = iNow = 0;
        var timer = null;
        var aSort = [];
        var aPosition = [
            {width:566,height:426,top:0,left:205,zIndex:10},
            {width:400,height:300,top:56,left:80,zIndex:8},
            {width:300,height:225,top:92,left:0,zIndex:6},
            {width:300,height:225,top:92,left:680,zIndex:6},
            {width:400,height:300,top:56,left:500,zIndex:8}
        ]

        for(i=0;i<aLi.length;i++){
            aLi[i].index = i;
            aLi[i].style.width = aPosition[i].width +'px';
            aLi[i].style.height = aPosition[i].height +'px';
            aLi[i].style.top = aPosition[i].top +'px';
            aLi[i].style.left = aPosition[i].left +'px';
            aLi[i].style.zIndex = aPosition[i].zIndex;
            aSort[i] = aPosition[i];
            myAddEvent(aLi[i], 'click', function(){
                var iSort = this.index;
                iNow = this.index;
                Sort();
                for(i=0;i<iSort;i++){
                    aSort.unshift(aSort.pop());
                }
                sMove();

            });
        }
        myAddEvent(aA[0], 'click', function(){
            aSort.unshift(aSort.pop());
            sMove();
            setInter();
        });
        myAddEvent(aA[1], 'click', function(){
            aSort.push(aSort.shift());
            sMove();
            iNow--;
            if(iNow<0)iNow = aLi.length - 1;
            tab();
        });
        timer = setInterval(setInter,9000);
        function setInter(){
            iNow++;
            if(iNow>aLi.length-1)iNow = 0;
            tab();
        }
        function tab(){
            var iSort = iNow;
            Sort();
            for(i=0;i<iSort;i++){
                aSort.unshift(aSort.pop());
            }
            sMove();
        }
        function Sort(){
            for(i=0;i<aLi.length;i++){
                aSort[i] = aPosition[i];
            }
        }
        function sMove(){
            for(i=0;i<aLi.length;i++){
                startMove(aLi[i], aSort[i], function(){});
                aLi[i].className = '';
            }
            aLi[iNow].className = 'hove';
        }
    };
    function getClass(oParent, sClass){
        var aElem = document.getElementsByTagName('*');
        var aClass = [];
        var i = 0;
        for(i=0;i<aElem.length;i++)if(aElem[i].className == sClass)aClass.push(aElem[i]);
        return aClass;
    }
    function myAddEvent(obj, sEvent, fn){
        if(obj.attachEvent){
            obj.attachEvent('on' + sEvent, function(){
                fn.call(obj);
            });
        }else{
            obj.addEventListener(sEvent, fn, false);
        }
    }
    function startMove(obj, json, fnEnd){
        if(obj.timer)clearInterval(obj.timer);
        obj.timer = setInterval(function (){
            doMove(obj, json, fnEnd);
        }, 30);
    }
    function getStyle(obj, attr){
        return obj.currentStyle ? obj.currentStyle[attr] : getComputedStyle(obj, false)[attr];
    }
    function doMove(obj, json, fnEnd){
        var iCur = 0;
        var attr = '';
        var bStop = true;
        for(attr in json){
            attr == 'opacity' ? iCur = parseInt(100*parseFloat(getStyle(obj, 'opacity'))) : iCur = parseInt(getStyle(obj, attr));
            if(isNaN(iCur))iCur = 0;
            if(navigator.userAgent.indexOf("MSIE 8.0") > 0){
                var iSpeed = (json[attr]-iCur) / 3;
            }else{
                var iSpeed = (json[attr]-iCur) / 5;
            }
            iSpeed = iSpeed > 0 ? Math.ceil(iSpeed) : Math.floor(iSpeed);
            if(parseInt(json[attr])!=iCur)bStop = false;
            if(attr=='opacity'){
                obj.style.filter = "alpha(opacity:"+(iCur+iSpeed)+")";
                obj.style.opacity = (iCur + iSpeed) / 100;
            }else{
                attr == 'zIndex' ? obj.style[attr] = iCur + iSpeed : obj.style[attr] = iCur + iSpeed +'px';
            }
        }
        if(bStop){
            clearInterval(obj.timer);
            obj.timer = null;
            if(fnEnd)fnEnd();
        }
    }
    $(".findPic li").first().addClass("hove")

}
