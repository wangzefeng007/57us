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
    $InsertInfo['Type'] = 2;
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
    <title>投资高端教育项目，美国绿卡全家共享_汤姆森教育中心EB-5移民 - 57美国网</title>
    <meta name="keywords" content="汤姆森教育EB-5移民,美国中国之城,EB-5美国移民,eb5投资移民项目,怎样才能移民美国,移民美国需要什么条件,投资移民美国需要多少钱,eb-5美国投资移民">
    <meta name="description" content="57美国网联合美国中国之城，推出汤姆森教育“EB-5移民项目”，120户黄金移民席位，仅余10席。该EB-5移民项目拥有优越的地理位置、充足的就业机会、项目资金到位、项目风险保障、与美国名校直接合作、并有政府大力支持。">
    <script src="http://js.57us.com/base/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://js.57us.com/base/jquery.SuperSlide/2.1/jquery.SuperSlide.2.1.js"></script>
    <script src="http://js.57us.com/base/base.js"></script>
    <link href="http://css.57us.com/common/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
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
                        <div class="UserMain"> <a href="http://member.57us.com/">
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
                    <div class="SeconMenuM"> <a href="/studytopic_uscolege/" class="transition" target="_blank" title="院校">院校</a> <span class="line"></span> <a href="/studytopic_news/" class="transition" target="_blank" title="留学">留学</a> <span class="line"></span> <a href="/studytopic_exam/" class="transition" target="_blank" title="考试">考试</a> <span class="line"></span> <a href="/studytopic_learning/" class="transition" target="_blank" title="游学">游学</a> <span class="line"></span> <a href="/studytopic_guide/" class="transition" target="_blank" title="签证">签证</a> <span class="line"></span><a href="/topic/lecture/" target="_blank" title="留学讲堂">留学讲堂</a><span class="line"></span> </div>
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
        <div class="IndexBan">
            <ul class="pic">
                <li><img src="images/ban.jpg" width="1920" height="574" /></li>
            </ul>
        </div>
    </div>
    <div class="menu">
        <ul class="nav cf">
            <li><a href="#ItemInstr"><span class="ch">项目介绍</span><span class="en">Project introduction</span></a></li>
            <li><a href="#Advantage"><span class="ch">项目优势</span><span class="en">advantage of project</span></a></li>
            <li><a href="#TeamIns"><span class="ch">项目团队</span><span class="en">project team</span></a></li>
            <li class="last"><a href="#Process"><span class="ch">申办程序</span><span class="en">The application</span></a></li>
        </ul>
    </div>
    <div class="ItemInstr" id="ItemInstr">
        <div class="w1113 cf">
            <div class="ItemTitle">团队介绍Team to introduce</div>
            <div class="ItemIns mt15"> 汤普森教育中心是美国中国之城集团的首期开发项目，占地575英亩，位于美国纽约上州萨拉文郡汤普森镇。<br>
                汤普森教育中心与美国著名大学合作、开发综合性大学，一举打造涵盖：商学院、电影艺术学院及其附属设施的高端大学社区。<br>
                项目前期的各项投入、资金和准备工作已经完成，该项目将于2017年夏天完工。 </div>
            <div class="cf mt15">
                <div class="ItemLeft fl">
                    <div class="ItemTit mt15">项目摘要<br>
                        <span class="en">Project Summary</span></div>
                    <ul class="ItemList mt10">
                        <li class="m">项目名称: 汤普森B-1教育中心大学城</li>
                        <li><b>项目地点：</b>萨拉文郡(距纽约曼哈顿72英里)</li>
                        <li><b>单位投资额：</b>50万美元</li>
                        <li><b>项目风控：</b>5年期贷款</li>
                        <li><b>投资类型：</b>第一顺位抵押权贷款</li>
                        <li><b>抵押物：</b>所有土地、建筑物以及收入</li>
                    </ul>
                </div>
                <div class="fr"><img src="images/itemPic.jpg" width="586" height="361"></div>
            </div>
            <div class="DetailIns mt35">
                <div class="gaiyao">
                    <p class="DetailInsTit">发行概要</p>
                    <div class="DetailInsNr mt5"> <b>总投资1.5亿美元</b><br>
                        <b>项目方自筹：</b>9000万美元<br>
                        <b>EB-5筹资：</b>6000万美元<br>
                        <b>筹资人数：</b>120位EB-5投资人<br>
                        <b>退出方式：</b>5年后一次性归还投资 </div>
                </div>
                <div class="schoolInsr">
                    <p class="DetailInsTit">校区介绍</p>
                    <div class="DetailInsNr mt5"> 汤普森教育中心将成为中西文化融合的国际化平台，项目将打造一个高端智能化的大学城社区，囊括教学设施、附属设施和学区房，并引入中、美名牌大学的一流师资，学生将按学校规定入住学区房，而学区房将给投资者带来稳定的租金收入。 </div>
                </div>
            </div>
        </div>
    </div>
    <div class="w1113 Advantage cf" id="Advantage">
        <div class="ItemTitle1">项目优势The advantage of project</div>
        <div class="cf mt15"><img src="images/advan.jpg"/></div>
        <ul class="AdvantageUl mt10 cf">
            <li>
                <p class="tit">地理位置优越</p>
                <p class="nr">项目距纽约曼哈顿仅1小时车程，周边有2座国际机场和8所国籍知名院校。</p>
            </li>
            <li>
                <p class="tit">就业机会充足</p>
                <p class="nr">项目预计创造3588个就业，而一期仅需1200个就业，就业盈余率高达299%</p>
            </li>
            <li>
                <p class="tit">项目资金到位</p>
                <p class="nr">项目已于2015年3月顺利开工，预计2017年初完工，建筑周期为2年。</p>
            </li>
            <li>
                <p class="tit">项目风险保障</p>
                <p class="nr">项目拥有完工保障，并拥有开发商、施工方和监理方的三重保险作为保障。其中开发商的保额为1.5亿美元。</p>
            </li>
            <li>
                <p class="tit">美国名校合作</p>
                <p class="nr">项目将作为其的新校区使用 </p>
            </li>
            <li>
                <p class="tit">政府大力支持</p>
                <p class="nr">政府给予每期20%的部分现金和税收优惠，汤普森B-1可获得3000万的退税。 </p>
            </li>
            <li>
                <p class="tit">多项退出选择</p>
                <p class="nr">（1）再融资（2）通过政府支持寻求传统商业贷款（3）出售房产等 </p>
            </li>
            <li>
                <p class="tit">第一顺位抵押权</p>
                <p class="nr">EB-5投资人将享有大学城新校区项目的第一顺位抵押权。</p>
            </li>
        </ul>
    </div>
    <div class="cf w1113 mt40">
        <div class="ItemTitle3">投资保障 Investment guarantee</div>
        <ul class="guaranteeList mt20">
            <li>
                <p class="tit">绿卡保证</p>
                <p class="nr">共完成3588个就业机会<br>
                    项目可创造3588个就业，1200个就业就完全满足美国移民局要求，就业盈余299%，每个投资人将分配到29.9个就业。 </p>
            </li>
            <li class="i2">
                <p class="tit">风险控制</p>
                <p class="nr">共完成3588个就业机会<br>
                    拥有双重保险，施工方、监理方以及开发商分别为项目投保，并有相关完工保障。 </p>
            </li>
            <li>
                <p class="tit">政府支持</p>
                <p class="nr">•荣获当地政府鼎力支持，包括每期15%-20%的部分现金和税收优惠<br>
                    • 州政府大力支持该区域发展旅游和娱乐。 </p>
            </li>
        </ul>
    </div>
    <div class="cf TeamIns" id="TeamIns">
        <div class="w1113 cf">
            <div class="ItemTitle4">团队介绍 Team to introduce</div>
            <div class="cf mt15"><img src="images/team1.jpg"/></div>
            <div class="cf mt10"><img src="images/team2.jpg"/></div>
            <div class="TeamBox mt10">
                <div class="TeamBoxTit">施工团队</div>
                <div class="TeamBoxNr">
                    <p class="title">STV集团——美国首屈一指的大型施工、监理公司</p>
                    <p class="nr mt10">全球的全方位大型施工和监理公司，美国建筑界的翘楚；成立与1912年的纽约，是美国历史悠久的建造公司，拥有百年历史；<br>
                        北美地区拥有40多家分公司，1700多民员工；数千项成功案例，包括：西点军校、林肯中心、肯尼迪航天中心等的地标性建筑；<br>
                        获得多项殊荣，美国建筑界的项目成就奖、钻石奖、白金奖、国家荣誉奖等众多奖项；<br>
                        目前STV集团已签署相关的完工保障，并为项目的施工和监理投保；</p>
                </div>
            </div>
        </div>
    </div>
    <div class="w1113 Process cf" id="Process">
        <div class="ItemTitle5">申办流程 Application process</div>
        <div class="tac"> <img src="images/process.png"/> </div>
    </div>
    <div class="BottomContact">
        <div class="w1113 ContactFeek mt25">
            <ul class="ContactList">
                <li><span>姓名：</span>
                    <input type="text" name="username" id="username" value="">
                </li>
                <li><span>邮箱：</span>
                    <input type="text" name="mail" id="mail" value="">
                </li>
                <li><span>电话：</span>
                    <input type="text" name="phone" id="phone" value="">
                </li>
            </ul>
            <a class="ContactBtn">点击提交</a> </div>
    </div>
</div>
</div>
<script type="text/javascript" src="js/index.js"></script>
<script src="http://js.57us.com/base/layer/2.4/layer.js"></script>
</div>
</body>
</html>