<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
if($_COOKIE['session_id']!=''){
    session_id($_COOKIE['session_id']);
}
session_start();
//post函数
function curl_postsend_usersession($url, $data = array()) {
    $ch = curl_init ();
    //设置选项，包括URL
    curl_setopt ( $ch, CURLOPT_URL, "$url" );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 5 ); //定义超时3秒钟
    // POST数据
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    // POST参数
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $data ) );
    //执行并获取url地址的内容
    $output = curl_exec ( $ch );
    $errorCode = curl_errno ( $ch );
    //释放curl句柄
    curl_close ( $ch );
    if (0 !== $errorCode) {
        return false;
    }
    return $output;
}

//获取登录信息
if($_SESSION['UserID'] && $_SESSION['Account']){
    if(!isset($_SESSION['Level'])){
        $json_data=curl_postsend_usersession(WEB_MEMBER_URL.'/userajax.html',array('Intention'=>'GetSession','ID'=>$_SESSION['UserID'],'Account'=>$_SESSION['Account']));
        $_SESSION=json_decode($json_data,true);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>城市代理人招募 - 57美国网</title>
    <meta name="keywords" content="旅游城市代理人,留学城市代理人,区域代理人,城市代理人,授权代理认,指定代理认,独立代理人">
    <meta name="description" content="57美国网招募城市代理人，零成本高收益、全职兼职皆可、多渠道资源、结合线上线下推广资源，只要你满足条件,即可申请成为57美国的旅游城市代理人留学城市代理人或。">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <script src="http://js.57us.com/base/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://js.57us.com/base/base.js"></script>
    <script src="http://js.57us.com/base/jquery.SuperSlide/2.1/jquery.SuperSlide.2.1.js"></script>
    <link href="http://css.57us.com/common/reset.css" rel="stylesheet" type="text/css" />
</head>
<body class="index">
<?php include  '../../Templates/Common/NewRightNav.php';?>
<div class="wrap">
    <div class="w1200 cf NewsTop">
        <div class="fl">欢迎来到57美国网旅游平台!</div>
        <div class="fr">
            <div class="UserStatus fr">
                <?php if($_SESSION[UserID] && !empty($_SESSION[UserID])){?>
                    <div class="UserLogin">
                        <div class="UserMain"> <a href="http://member.57us.com/" target="_blank">
                                <!--<img src="https://ss0.baidu.com/6ONWsjip0QIZ8tyhnq/it/u=2069873511,207811596&amp;fm=80">-->
                                <span id="" class="UserName"><?php echo $_SESSION[NickName];?></span> </a>
                            <div class="UserList"> <b class="ico"></b>
                                <div class="UserListM">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                        <!--<tr>
                                                      <th width="80">全部订单</th>
                                                      <td><a href="">旅游订单</a><a href="">租车订单</a><a href="">酒店订单</a><a href="">签证订单</a></td>
                                                  </tr>-->
                                        <tr>
                                            <th width="80">账户管理</th>
                                            <td><a href="http://member.57us.com" target="_blank">个人中心</a> <a href="http://member.57us.com/member/securitycenter/" target="_blank">安全中心</a> <a href="http://member.57us.com/member/address/" target="_blank">收货地址</a> <a href="http://member.57us.com/member/signout/" target="_blank">安全退出</a> </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <a id="" class="letter" href=""></a><span id="">|</span> <a href="http://member.57us.com/member/signout/" target="_blank">退出</a> </div>
                <?php }else{?>
                    <div class="UserLogin"> <a href="http://member.57us.com/member/login/" target="_blank">登录</a><span id="">|</span> <a href="http://member.57us.com/member/register/" target="_blank">免费注册</a> </div>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="IndexTopMain">
        <div class="MaskUp"></div>
        <a href="http://www.57us.com" class="NewsLogo" title="57美国网" target="_blank">57美国网</a>
        <ul class="IndexMenu">
            <li> <a href="http://www.57us.com/tour/" class="a" target="_blank">旅游</a>
                <div class="SeconMenu"> <em></em>
                    <div class="SeconMenuM"> <a href="/travels/" class="transition" target="_blank" title="美国游记">美国游记</a> <span class="line"></span> <a href="/tour_tournews/" class="transition" target="_blank" title="旅游新发现">旅游新发现</a> <span class="line"></span> <a href="/tour_meishi/" class="transition" target="_blank" title="美食购物">美食购物</a> <span class="line"></span> <a href="/tour_fengjing/" class="transition" target="_blank" title="当季推荐">当季推荐</a> <span class="line"></span> </div>
                </div>
            </li>
            <li> <a href="http://www.57us.com/study/" class="a" target="_blank">留学</a>
                <div class="SeconMenu"> <em></em>
                    <div class="SeconMenuM"> <a href="/studytopic_uscolege/" class="transition" target="_blank" title="院校">院校</a> <span class="line"></span> <a href="/studytopic_news/" class="transition" target="_blank" title="留学">留学</a> <span class="line"></span> <a href="/studytopic_exam/" class="transition" target="_blank" title="考试">考试</a> <span class="line"></span> <a href="/studytopic_learning/" class="transition" target="_blank" title="游学">游学</a> <span class="line"></span> <a href="/studytopic_guide/" class="transition" target="_blank" title="签证">签证</a> <span class="line"></span><a href="/topic/lecture/" target="_blank" title="留学讲堂">留学讲堂</a><span class="line"></span></div>
                </div>
            </li>
            <li> <a href="http://www.57us.com/immigrant/" class="a" target="_blank">移民</a>
                <div class="SeconMenu"> <em></em>
                    <div class="SeconMenuM"> <a href="/immigtopic_genre/" target="_blank" class="transition" title="移民类别">移民类别</a> <span class="line"></span> <a href="/immigtopic_guide/" target="_blank" class="transition" title="生活指南">生活指南</a> <span class="line"></span> <a href="/immigtopic_way/" target="_blank" class="transition" title="移民攻略">移民攻略</a> <span class="line"></span> <a href="/immigtopic_house/" target="_blank" class="transition" title="投资房产">投资房产</a> <span class="line"></span> <a href="/topic/eb5/" target="_blank" title="美国EB-5移民">美国EB-5移民</a><span class="line"></span> <a href="/topic/eb5_2/" target="_blank" title="汤姆森教育EB-5">汤姆森教育EB-5</a><span class="line"></span> </div>
                </div>
            </li>
            <li> <a href="http://www.57us.com/topic/cityagent1/" class="a" target="_blank">城市代理人</a> </li>
        </ul>
        <div class="NewsSearchBox">
            <input type="text" name="" id="" value="" class="input" placeholder="" />
            <input type="button" id="" value="" class="SearchBtn" />
        </div>
        <div class="IndexBan">
            <ul class="pic">
                <li><img src="images/ban1.jpg" width="1920" height="630" /></li>
                <li><img src="images/ban2.jpg" width="1920" height="630" /></li>
                <li><img src="images/ban3.jpg" width="1920" height="630" /></li>
            </ul>
            <ul class="hd">
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
    </div>
    <div class="main">
        <div class="w1200 allMenu"></div>
        <div class="w1100 about">
            <p class="Ptit tit1">57美国网简介</p>
            <div class="aboutNr"> 57美国网总部位于美国洛杉矶，是集美国旅游、留学、移民、投资、商贸及代购为一体的国内首家专注美国分类信息的门户网。我们致力为全球华人整合最具时效性的新闻聚合及社交串联，并提供一站式服务，直通美国。 </div>
        </div>
        <div class="w1100 advantage">
            <p class="Ptit tit2">我们的优势</p>
            <div class="advantageNr"></div>
        </div>
        <div class="w1100 reason">
            <p class="Ptit tit3">给你6个不得不加入我们的理由！</p>
            <table border="0" cellspacing="2" cellpadding="0" width="696" class="fl tab1 mt35">
                <tr height="53">
                    <th width="207" class="tac">各项</th>
                    <th width="257" class="t0">传统中介</th>
                    <th width="225" class="t1">自营工作室</th>
                </tr>
                <tr height="122">
                    <td class="tac">收益</td>
                    <td class="t1">底薪＋抽成<br>
                        收入水平普遍不高</td>
                    <td class="t1">成本高昂，收入<br>
                        无保障</td>
                </tr>
                <tr height="112">
                    <td class="tac">工作方式</td>
                    <td class="t1">超长工作时间，易<br>
                        疲劳</td>
                    <td class="t1">灵活多变</td>
                </tr>
                <tr height="147">
                    <td class="tac">工作环境</td>
                    <td class="t1">商务园区写字楼，<br>
                        承担交通成本和时<br>
                        间成本</td>
                    <td class="t1">办公环境自选<br>
                        承担运营成本</td>
                </tr>
                <tr height="76">
                    <td class="tac">工作团队</td>
                    <td class="t1">层级分明的管理制度</td>
                    <td class="t1">扁平化管理模式</td>
                </tr>
                <tr height="172">
                    <td class="tac">线上资源</td>
                    <td class="t1">总部集中投放分配</td>
                    <td class="t1">网络推广成本高</td>
                </tr>
                <tr height="133">
                    <td class="tac">线下资源</td>
                    <td class="t1">院校来访，各类教育<br>
                        展览等</td>
                    <td class="t1">资源匮乏，难以<br>
                        阻止中大型活动</td>
                </tr>
            </table>
            <table border="0" cellspacing="2" cellpadding="0" width="404" class="fl tab2 mt35">
                <tr height="53">
                    <th width="207" class="t0">57美国网</th>
                </tr>
                <tr height="122">
                    <td class="t2">零成本高收入；<br>
                        拥有出国实地考察的机会；<br>
                        具体收益欢迎来电详询或关注微信公众号</td>
                </tr>
                <tr height="112">
                    <td class="t2 t3">全职或兼职，根据自身情况灵活选择</td>
                </tr>
                <tr height="148">
                    <td class="t2 t3">自由办公，降低了交通和时间成本</td>
                </tr>
                <tr height="76">
                    <td class="t2 t3">公司提供多渠道资源；成就自我事业高峰；</td>
                </tr>
                <tr height="172">
                    <td class="t2">互联网+留学新模式；<br>
                        搜索引擎推广、DSP、网盟推广、新媒体<br>
                        推广、新闻媒体平台推广、视频广告推广等；</td>
                </tr>
                <tr height="133">
                    <td class="t2">不定期提供大型专题讲座、周末社区活动、高档写字楼地推、院校来访、大型教育展等；</td>
                </tr>
            </table>
        </div>
        <div class="w1100 join">
            <p class="Ptit tit4">若你满足以下条件，你就是我们正在寻找的千里马！</p>
            <div class="joinM mt40"></div>
        </div>
        <div class="ad mt50"></div>
        <div class="w1200 contact mt15">
            <p class="Ptit tit5">我们在这里，等你来</p>
            <div class="contactm mt35"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/index.js"></script>
<script src="http://js.57us.com/base/layer/2.4/layer.js"></script>
</body>
</html>