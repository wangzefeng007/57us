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
        $json_data=curl_postsend_usersession('http://member.57us.com/userajax.html',array('Intention'=>'GetSession','ID'=>$_SESSION['UserID'],'Account'=>$_SESSION['Account']));
        $_SESSION=json_decode($json_data,true);
    }
}


if ($_POST)
{
    include "../../Include/Class.Common.php";
    $InsertInfo['Name'] = trim($_POST['Name']);
    $InsertInfo['Email'] = trim($_POST['Email']);
    $InsertInfo['Tel'] = trim($_POST['Tel']);
    $InsertInfo['UserID'] = $_SESSION['UserID'];
    $InsertInfo['Type'] = 1;
    $InsertInfo['FromIP'] = GetIP();
    $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
    if ($InsertInfo['Name'] =='' || strlen($InsertInfo['Name'])>30 || $InsertInfo['Email']=='' || $InsertInfo['Tel']=='')
    {
        $JsonResult = array(
            'ResultCode' => 101,
            'Message' => '信息填写错误！'
        );
    }else{
        include '../../Include/Class.Databasedriver.Mysql.php';
        include '../../Config.php';
                //初始化数据库连接类
        $DB = new DatabaseDriver_MySql ($NewsDbConfig);
        $IsOk = $DB->insertArray('tbl_eb5_message', $InsertInfo,true);
        if ($IsOk)
        {
            $JsonResult = array(
                'ResultCode' => 200,
                'Message' => '提交成功'
            );
        }else{
            $JsonResult = array(
                'ResultCode' => 102,
                'Message' => '提交失败，请重新提交!'
            );
        }
    }
    echo json_encode ( $JsonResult );
    exit ();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>50万美金成就EB-5移民梦想，绿卡末班车截止至12月11日 - 57美国网</title>
    <meta name="keywords" content="EB-5移民,IIUSA美国移民协会,eb5投资移民,YK 集团,eb5投资移民流程,申请投资移民,eb-5投资移民,eb5投资移民成功率">
    <meta name="description" content="57美国网联合yk集团推出“EB-5移民项目”，只需要50万美金就能成就EB-5移民梦想。该EB-5移民项目拥有21年商业不动产开发经验、7年区域中心操盘经验、更有多重资源及优秀的专业团队。">
    <link href="http://css.57us.com/common/reset.css" rel="stylesheet" type="text/css" />
    <link href="http://css.57us.com/www/newspublic.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <script src="http://js.57us.com/base/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://js.57us.com/base/base.js"></script>
    <script src="http://js.57us.com/base/jquery.SuperSlide/2.1/jquery.SuperSlide.2.1.js"></script>
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
                    <div class="SeconMenuM"> <a href="/studytopic_uscolege/" class="transition" target="_blank" title="院校">院校</a> <span class="line"></span> <a href="/studytopic_news/" class="transition" target="_blank" title="留学">留学</a> <span class="line"></span> <a href="/studytopic_exam/" class="transition" target="_blank" title="考试">考试</a> <span class="line"></span> <a href="/studytopic_learning/" class="transition" target="_blank" title="游学">游学</a> <span class="line"></span> <a href="/studytopic_guide/" class="transition" target="_blank" title="签证">签证</a> <span class="line"></span> <a href="/topic/lecture/" target="_blank" title="留学讲堂">留学讲堂</a><span class="line"></span> </div>
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
            <form target="_blank" id="index_text" action="/search.html" method="get">
                <input type="text" name="keyword" id="keyword" value="" class="input" placeholder="旅游 留学 移民" autocomplete="off"/>
                <input name="" type="submit" value="" class="SearchBtn">
            </form>
        </div>
        <div class="ban">
            <ul class="pic">
                <li><img src="images/ban2.jpg" width="1920" height="600" /></li>
                <li><img src="images/ban3.jpg" width="1920" height="600" /></li>
            </ul>
            <ul class="hd">
                <li></li>
                <li></li>
            </ul>
        </div>
    </div>
    <div class="cf">
        <div class="w1000 cf">
            <div class="tit1 mt15">为什么选择我们？</div>
            <div class="Platform mt20"> <img src="images/Platform.png" width="138" height="134" title=""/>
                <p> <span class="red">21</span>年的商业不动产开发经验，从事投资移民<span class="red">7</span>年时间<br>
                    临时绿卡申请通过率高达<span class="red">75%</span>以上，<span class="red">37</span>（48）位I-829成功转换正式绿卡<br>
                    <span class="red">20</span>（35）位投资5年期满取得投资额，100%的项目成功率<br>
                    2013-2015连续<span class="red">3年</span>被IIUSA美国移民协会评为全美十大区域中心！ </p>
            </div>
            <div class="cf mt35"><img src="images/about.png"/></div>
            <div class="cf mt30 EB5Ins"> <img src="images/EB5.png" width="138" height="134" title=""/>
                <p class="fr EB5InsCont"> EB-5是美国的一个签证类别代码，意思是“第五类有限就业型移民签证”<br>
                    EB-5项目于1990年立法设定，是美国针对海外移民类别中，申请核准时间最短、资格条件最少的一个便捷渠道。<br>
                    投资人可以自由进出美国，无需放弃原居住国事业<br>
                    可享受与当地居民相同的福利，五年后可申请转换为美国公民<br>
                    投资人可以自行选择美国任何地区定居，不限于投资项目所在地
                    配偶与21岁以下的子女均可同时获得绿卡 </p>
            </div>
        </div>
    </div>
    <div class="item">
        <div class="w1000 cf">
            <div class="ItemLeft fl">
                <div class="ItemTit">EB-5项目介绍<br>
                    <span class="en">PROJECT INTRODUTION</span></div>
                <div class="ItemTit ItemTit1 mt15">项目摘要<br>
                    <span class="en">Project Summary</span></div>
                <ul class="ItemList mt10">
                    <li class="m">项目名称：波莫那凯悦酒店</li>
                    <li><b>项目位置：</b>南加州洛杉矶郡波莫那市</li>
                    <li><b>土地总面积：</b>77亩</li>
                    <li><b>建设面积：</b>23 .2万 2英呎</li>
                    <li><b>建设容积率：</b>1: 0.5</li>
                    <li><b>建设总投资：</b>$7，997万 美元</li>
                    <li><b>EB-5 投资额：</b>$4,000万 美元</li>
                    <li><b>EB-5 投资人：</b>开放 80 位投资人</li>
                    <li><b>报酬率：</b>投资人每年约享 1% 的回报</li>
                </ul>
            </div>
            <div class="fr"><img src="images/itemPic.jpg" width="582" height="464"/></div>
        </div>
    </div>
    <div class="cf">
        <div class="w1000 Advantage cf">
            <div class="tit2 mt15">我们有哪些优势？</div>
            <div class="AdvantageList fl mt10">
                <p class="mt10"><b>区域优势：</b>临近高速公路、国际机场、活动展馆、高级住宅区，交通便捷、人潮流动量高。</p>
                <p class="mt30"> <b>地段优势：</b><br>
                    • 波莫那生活广场为波莫那市最大商业中心；<br>
                    • 位于60号及71号高速公路的波莫那零售广场享有交通便利及地理位置明显的优势；<br>
                    • 距离Ontario国际机场，San Bernardino国际机场及LAX国际机场车程皆在一个小时内；<br>
                    • 紧邻波莫那市高级社区Philips Ranch，社区内家庭平均年收入为$86,000美元 </p>
                <p class="mt35"> <b>客流优势：</b><br>
                    Fairplex洛杉矶永久博物会馆距离波莫那零售广场约5英里，每年平均可吸引167万人次。 </p>
            </div>
        </div>
    </div>
    <div class="Ghbox">项目规划</div>
    <div class="cf">
        <div class="w1000 cf">
            <div class="tit3 mt25">项目安全</div>
            <div class="ItemSafe mt20">
                <p class="mt35"><b>股权回报：</b><b class="red">50万美元投资额+借款利息</b></p>
                <p>&nbsp;</p>
                <p class="mt30"><b>投资报酬率：</b>投资人每年约享<b class="red">1% </b>的回报</p>
                <p>&nbsp;</p>
                <p class="mt30"><b>股权退出：</b>自收到有限合伙股权证之日起，<b class="red">届满5周年并取得永久绿卡后即可提出退股</b></p>
                <p class="mt50"><b>投资者保障：</b><b class="red">投资人将拥有双重保障，降低投资风险：</b></p>
                <p>1.投资人拥有加入的合伙公司股权(投资股权证)的法律保障。<br>
                    2.加上借贷予控股公司(项目及土地拥有方)，控股公司给予合伙公司的债权(借贷债权证)的法律保障。</p>
                <p class="mt20"><b>计算模式：</b>Loan Model借贷方式。以建设总投资金额计算就业，无租约模式问题，安全可靠。</p>
            </div>
        </div>
    </div>
    <div class="cf">
        <div class="w1000 cf">
            <div class="tit4 mt25">投资架构</div>
            <p class="mt10"><img src="images/mechanism1.jpg"/></p>
            <p class="mt10"><img src="images/mechanism2.jpg"/></p>
            <p class="mt10"><img src="images/mechanism3.jpg"/></p>
        </div>
    </div>
    <div class="AdvantTwo cf">
        <div class="w1000 cf">
            <div class="AdvantTwoT mt10"> <span class="china">美国投资移民两大优势</span><br>
                <span class="en">Application condition of loose</span> </div>
            <div class="AdvantTwoLeft mt20 fl">
                <div class="AdvantTwoLefTit"> <span class="china">1.申请条件宽松</span><br>
                    <span class="en">INVESTMENT IMMIGRANT THREE BIG ADVANTAGE</span> </div>
                <p class="AdvantTwoLeftM mt10">如对申请人的个人资格或商业背景，均无限制条件。因此，只要申请人有足够经济能力，投资50万美元，即可申请。<br>
                    ● 申请人需年满18周岁；<br>
                    ● 证明申请人有足够资金 (可包括配偶的资产，继承、赠与、博彩也被认可)；<br>
                    ● 通过官方文件，证明资金合法来源；<br>
                    ● 应有相关部门的健康和无犯罪记录证明；</p>
            </div>
            <div class="AdvantTwoRight mt20 fr">
                <div class="AdvantTwoLefTit"> <span class="china">2.没有移民监</span><br>
                    <span class="en">No immigration prison</span> </div>
                <p class="AdvantTwoLeftM mt10">美国投资移民一旦核发，投资人在报到后，即可自由进出美国。<br>
                    ● 申请条件宽松：无年龄、经商、学历、英语能力等要求；<br>
                    ● 快捷：约16-20个月办成，是获得美国永久移民身份的最快方式；<br>
                    ● 无移民监：申请人只需每半年入境美国一次，便可维持绿卡的有效性；<br>
                    ● 材料准备简单、快捷；<br>
                    ● 子女免费就读美国公立名校；<br>
                    ● 可自由选择居住地；<br>
                    ● 投资50万美元，5年期满返还本金；</p>
            </div>
        </div>
    </div>
    <div class="w1000 mt30 cf">
        <div class="AdvantTwoT"> <span class="china">丰富的成功案例</span><br>
            <span class="en">RICH SUCCESSFUL CASES</span> </div>
        <div class="cf mt20"><img src="images/case.jpg"/></div>
    </div>
    <div class="w1000 mt40 cf">
        <div class="AdvantTwoT"> <span class="china">成功的项目投资</span><br>
            <span class="en">A SUCCESSFUL PROJECT INVESTMENT</span> </div>
        <p class="success">57美国网的开发项目均已通过美国移民局的审核批准。凭借多年的成功经验，与沃尔玛等美国各大零售商紧密合作，取得不斐的骄人战绩。</p>
        <div class="cf mt20"><img src="images/case1.jpg"/></div>
    </div>
    <div class="wrapBottom mt30">
        <div class="wrapBottomBg"></div>
        <div class="w1000 wrapBottomBox cf">
            <div class="AdvantTwoT mt30"> <span class="china">政府的高度支持</span><br>
                <span class="en">A SUCCESSFUL PROJECT INVESTMENT</span> </div>
            <div class="gov"> ● 2014年11月，加州帝王郡和埃尔森特罗市多名官员参与YK埃尔蒙特罗市中心别墅公寓项目的破土典礼<br>
                ● 加州帝王郡长Michael Kelley在埃尔森特罗中心别墅项目破土仪式上向美国YK集团颁发奖状<br>
                ● 美国联邦众议员诺玛托尔斯颁赠“卓越开发奖“予美国YK集团<br>
                ● 美国联邦众议员Juan Vargas的代表ReneFelix接受美国YK集团破土纪念奖牌 </div>
            <div class="mt10"><img src="images/success.jpg"/></div>
            <div class="mt20"><img src="images/success1.jpg"/></div>
        </div>
        <div class="BottomContact">
            <div class="w1000 ContactFeek mt25">
                <ul class="ContactList">
                    <li><span>姓名：</span>
                        <input type="text" name="username" id="username" value="" />
                    </li>
                    <li><span>邮箱：</span>
                        <input type="text" name="mail" id="mail" value="" />
                    </li>
                    <li><span>手机：</span>
                        <input type="text" name="phone" id="phone" value="" />
                    </li>
                </ul>
                <a class="ContactBtn">点击提交</a> </div>
        </div>
    </div>
    <!--
          作者：xiao15980751809@126.com
          时间：2016-07-19
          描述：固定底部窗口
      -->
    <div class="FixContact transition"> <a href="JavaScript:void(0)" class="close"></a>
        <div class="FixContactM">
            <ul class="ContactList">
                <li><span>姓名：</span>
                    <input type="text" name="username" id="username" value="" />
                </li>
                <li><span>邮箱：</span>
                    <input type="text" name="mail" id="mail" value="" />
                </li>
                <li><span>手机：</span>
                    <input type="text" name="phone" id="phone" value="" />
                </li>
            </ul>
            <a class="ContactBtn">点击提交</a> </div>
    </div>
</div>
<script type="text/javascript" src="js/index.js"></script>
<script src="http://js.57us.com/base/layer/2.4/layer.js"></script>
</div>
</body>
</html>