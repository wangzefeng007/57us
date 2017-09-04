/**
 * Created by Foliage on 2016/9/29.
 */
var NowTime,i=0;
var EndTime= new Date(ExpirationTime).getTime(); //截止时间 加载页面从服务获取
function GetRTime(){
    i++;
    var nMS =EndTime - NowTime.getTime();
    if(nMS<0){
        window.location.reload();
    }
    var nD =Math.floor(nMS/(1000 * 60 * 60 * 24));
    var nH=Math.floor(nMS/(1000*60*60)) % 24;
    var nM=Math.floor(nMS/(1000*60)) % 60;
    var nS=Math.floor(nMS/1000) % 60;
    var nU=Math.floor(nMS/100)%10;
    if(nD>= 0){
        document.getElementById("RemainD").innerHTML=nD;
        document.getElementById("RemainH").innerHTML=nH;
        document.getElementById("RemainM").innerHTML=nM;
        document.getElementById("RemainS").innerHTML=nS;
        document.getElementById("RemainU").innerHTML=nU;
        document.getElementById("numb").innerHTML=i;
    }
    NowTime = new Date(NowTime.valueOf() + 100);
    if(nMS<0){
        window.location.reload();
    }else{
        setTimeout("GetRTime()",100);
    }
}
window.onload=function(){
    NowTime=new Date(ServerTime);// 服务器当前时间加载页面从服务器获取
    GetRTime();
}

//倒计时html页面代码
// <span id="RemainD" class="hidden">天</span><span class="hours" id="RemainH"></span>小时:<span class="minutes" id="RemainM"></span>分:<span class="seconds" id="RemainS"></span>秒<span id="RemainU" class="hidden"></span><span id="numb" class="hidden"></span>
