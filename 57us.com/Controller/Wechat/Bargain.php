<?php

/**
 * @desc  砍价活动
 * Class Bargain
 */
class Bargain extends WechatCommon {

    public $config;
    public function __construct()
    {
        //正式57us旅游
        $this->config = array(
            'appId'=>'wx9f862a9f9b6b28eb',
            'appSecret'=>'91f72a91b306b118f221e9f741f30d27',
            'token'=>'57uslvyouToken',
            'type'=>2  //旅游微信公众号
        );
        //正式57us留学
        /*$this->config = array(
            'appId'=>'wx7d65f983bc0fe512',
            'appSecret'=>'3e9d1cf13c3c3fef1eea13b16978112d',
            'token'=>'57uslvyouToken',
            'type'=>1  //留学
        );*/
        //测试公众号
        /*$this->appId = 'wx67074e58e8354f7d';
        $this->appSecret = '3e9d1cf13c3c3fef1eea13b16978112d';*/

        /*$GO = $_SERVER['REQUEST_URI'];
            $this->GoToUrl = WEB_WECHAT_URL.$GO;*/
        //$this->GoToUrl = WEB_WECHAT_URL . '/bargain/index/';
        //授权后跳转页
        $this->RedirectUrl = WEB_WECHAT_URL.'/bargain/setuserinfosession/';
        include SYSTEM_ROOTPATH.'/Controller/Wechat/Class.ApiWechat.php';
    }

    /**
     * @desc  微信验证
     */
    public function Validate(){
        $WechatObj = new ApiWechat($this->config);
        $WechatObj->Valid();
    }

    /**
     * @desc 保存微信投票会员SESSION
     */
    public function SetUserInfoSession()
    {
        $ApiWechatModule = new ApiWechat($this->config);
        $UserInfo = $ApiWechatModule->GetWechatUserInfo($_GET['code']);
        if ($UserInfo) {
            $WechatUserModule = new WechatUserModule();
            $CheckWechat = $WechatUserModule->GetInfoByWhere(" and ForeignKey = '{$UserInfo['openid']}'");
            if ($CheckWechat) {
                $_SESSION['UserInfo'] = $CheckWechat;
            } else {
                //开启事务
                $Data = array(
                    'NickName' => $this->NameFilter($UserInfo['nickname']),
                    'Sex' => $UserInfo['sex'],
                    'Country' => $UserInfo['country'],
                    'Province' => $UserInfo['province'],
                    'City' => $UserInfo['city'],
                    'HeadImgUrl' => $UserInfo['headimgurl'],
                    'ForeignKey'=>$UserInfo['openid'],
                    'AddTime'=>time(),
                );
                $Data['UserID'] = $WechatUserModule->InsertInfo($Data);
                $_SESSION['UserInfo'] = $Data;
            }
            if($_SESSION["GoUrl"]){
                $Url = WEB_WECHAT_URL.$_SESSION["GoUrl"];
            }
            else{
                $Url = WEB_WECHAT_URL.'/bargain/index/';
            }
            header("Location:" . $Url);
        }
    }


    /**
     * @desc 首页
     */
    public function Index()
    {
        $_SESSION['GoUrl'] = '/bargain/index/';
        //判断是否授权
        $this->JudgeAuthorize($this->RedirectUrl);

        //微信SignPackage
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();
        //分享Url
        $Url = WEB_WECHAT_URL.'/bargain/index/';

        $Nav = 'index';
        $Title = 'FM107北极圈极光之旅';
        include template('Bargain/Index');
    }

    /**
     * @desc  砍价活动
     */
    public function Bargain(){
        $WechatUserModule = new WechatUserModule();
        $MyUserID = $_SESSION['UserInfo']['UserID'];
        if(!$MyUserID){
            $_SESSION['GoUrl'] = '/bargain/bargain/';
            //判断是否授权
            $this->JudgeAuthorize($this->RedirectUrl);
        }

        //判断是否关注
        $MyUserInfo = $WechatUserModule->GetInfoByKeyID($MyUserID);
        $IsJudge = $this->JudgeFollow($MyUserInfo['ForeignKey']);

        //微信SignPackage
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();


        $BargainModule = new WechatBargainModule();
        $CheckInfo = $BargainModule->GetInfoByWhere(' and UserID = '.$MyUserID);
        if($CheckInfo){
            $BargainInfo = $CheckInfo;
        }
        else{
            $Data = array(
                'UserID'=>$MyUserID,
                'Amount'=>29998,
                'BargainAmount'=>0,
                'Type'=>1,
                'IsBargain'=>0,
                'TimeEnd'=>time()+172800
            );
            $Data['BargainID'] = $BargainModule->InsertInfo($Data);
            $BargainInfo = $Data;
        }
        $Title = 'FM107北极圈极光之旅';

        //分享Url
        $Url = WEB_WECHAT_URL.'/bargain/helpbargain/?ID='.$MyUserID;

        if($BargainInfo['TimeEnd']>time()){
            include template('Bargain/Bargain');
        }
        else{
            include template('Bargain/TimeEnd');
        }
    }

    /**
     * @desc  砍价操作
     */
    public function BargainOperate(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/

        $WechatBargainModule = new WechatBargainModule();
        $WechatBargainLogModule = new WechatBargainLogModule();
        $WechatUserModule = new WechatUserModule();
        $UserID = $_SESSION['UserInfo']['UserID'];
        $BargainUserID = $_POST['UserID'];
        $BargainUserInfo = $WechatUserModule->GetInfoByKeyID($BargainUserID);
        //砍价表信息
        $BargainInfo = $WechatBargainModule->GetInfoByWhere(' and UserID='.$BargainUserID);
        $BargainAmount = $this->GetAmountArithmetic($BargainInfo['Amount']);
        //判断是否砍过价
        $IsBargain = $WechatBargainLogModule->GetInfoByWhere(' and BargainUserID = '.$UserID.' and ToBargainUserID = '.$BargainUserID);
        if($IsBargain){
            $json_result = array('ResultCode' => 102, 'Message' => '您已经帮'.$BargainUserInfo['NickName'].'砍过价了!');
        }
        else{
            //开启事务
            global $DB;
            $DB->query("BEGIN");//
            if($_POST['Type'] == 1) {  //自己砍价
                $Result1 = $WechatBargainModule->UpdateAmount($BargainUserID, $BargainAmount, 1, 1);
                $GoUrl = '';
            }
            else { //帮别人砍价
                $Result1 = $WechatBargainModule->UpdateAmount($BargainUserID, $BargainAmount, 1);
                $GoUrl = WEB_WECHAT_URL.'/bargain/helpbargainresult/?ID='.$BargainUserID;
            }
            if($Result1){
                $LogData = array('BargainUserID'=>$UserID,'ToBargainUserID'=>$BargainUserID,'BargainAmount'=>$BargainAmount,'BargainTime'=>time(),'BargainType'=>1,'ClientIP'=>GetIP());
                $Result2 = $WechatBargainLogModule->InsertInfo($LogData);
                if($Result2){
                    $DB->query("COMMIT");//执行事务
                    $json_result = array('ResultCode' => 200, 'Message' => '砍价成功','Url'=>$GoUrl);
                }
                else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $json_result = array('ResultCode' => 101, 'Message' => '砍价失败','OtherMessage'=>'更新WechatBargainLogModule失败');

                }
            }else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $json_result = array('ResultCode' => 100, 'Message' => '砍价失败','OtherMessage'=>'更新WechatBargainModule失败');
            }
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  获取砍价算法
     */
    private function GetAmountArithmetic($Amount){
        if($Amount <= 29998 && $Amount > 29600){ //最少10次
            $Result = rand(1000,2000)/100;
        }
        elseif($Amount <= 29600 && $Amount > 29200 ){ //最少20次
            $Result = rand(100,1000)/100;
        }
        /*elseif($Amount <= 29000 || $Amount > 28500){ //最少50次
            $Result = rand(500,1000)/100;
        }
        elseif($Amount <= 28500 || $Amount > 28000){ //最少100次
            $Result = rand(300,500)/100;
        }
        elseif($Amount <= 28000 || $Amount > 27700){ //最少100次
            $Result = rand(100,300)/100;
        }
        elseif($Amount <= 27700 || $Amount > 27500){ //最少200次
            $Result = rand(50,100)/100;
        }
        elseif($Amount <= 27500 || $Amount > 27300){ //最少400次
            $Result = rand(10,50)/100;
        }*/
        else{
            $Result = rand(10,50)/100;
        }
        return $Result;
    }

    /**
     * @desc  好友帮忙砍价页面
     */
    public function HelpBargain(){
        $MyUserID = $_SESSION['UserInfo']['UserID']; //当前会员ID
        $UserID = $_GET['ID'];  //购买者ID

        if(!$MyUserID){
            $_SESSION['GoUrl'] = '/bargain/helpbargain/?ID='.$_GET['ID'];
            //判断是否授权
            $this->JudgeAuthorize($this->RedirectUrl);
            $MyUserID = $_SESSION['UserInfo']['UserID']; //当前会员ID
        }
        if($MyUserID == $UserID){
            header("Location:" . WEB_WECHAT_URL .'/bargain/bargain/');
        }

        //砍价信息
        $BargainModule = new WechatBargainModule();
        $BargainInfo = $BargainModule->GetInfoByWhere(' and UserID = '.$UserID);

        if($BargainInfo['TimeEnd']<time()) {
            header("Location:" . WEB_WECHAT_URL .'/bargain/helptimeend/?ID='.$UserID);
        }

        $WechatUserModule = new WechatUserModule();
        $WechatBargainLogModule = new WechatBargainLogModule();

        //判断是否关注
        $MyUserInfo = $WechatUserModule->GetInfoByKeyID($MyUserID);
        $IsJudge = $this->JudgeFollow($MyUserInfo['ForeignKey']);

        //微信SignPackage
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();

        //砍价对象会员信息
        $UserInfo = $WechatUserModule->GetInfoByKeyID($UserID);

        $IsBargain = $WechatBargainLogModule->GetInfoByWhere(' and BargainUserID = '.$MyUserID.' and ToBargainUserID = '.$UserID);
        if($IsBargain){
            $Message = '我为'.$UserInfo['NickName'].'的极光之旅砍了'.$IsBargain['BargainAmount'].'元,快来帮忙!';
        }
        else{
            $Message = '我要去看极光!据说朋友越多省越多,快来帮忙!';
        }

        //分享Url
        $Url = WEB_WECHAT_URL.'/bargain/helpbargain/?ID='.$UserID;

        $Title = 'FM107北极圈极光之旅';

        include template('Bargain/HelpBargain');
    }

    /**
     * @desc  获取砍价高手列表（个人）
     */
    public function BargainSuperior(){
        $WechatBargainLogModule = new WechatBargainLogModule();

        $UserID = $_GET['UserID'];
        $PageSize = 10;

        $Page = intval($_GET['Page']) ? intval($_GET['Page']) : '1';
        $Rscount = $WechatBargainLogModule->GetListsNum(' and ToBargainUserID = '.$UserID);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data = $WechatBargainLogModule->GetBargainSuperior( $UserID, $Offset, $Data ['PageSize']);
            foreach($Data as $key => $val){
                $Data[$key]['BargainTime'] = date("Y-m-d H:i:s",$val['BargainTime']);
            }
        }
        if ($Data) {
            $result = array('ResultCode' => 200, 'Data' => $Data );
        } else {
            $result = array('ResultCode' => 200, 'Data' => array() , 'Message' => '亲，没有更多内容了！');
        }
        echo json_encode($result);
    }

    /**
     * @desc  好友帮忙砍价结果页面
     */
    public function HelpBargainResult(){
        $WechatUserModule = new WechatUserModule();
        $WechatBargainModule = new WechatBargainModule();
        $WechatBargainLogModule = new WechatBargainLogModule();
        $MyUserID = $_SESSION['UserInfo']['UserID'];
        $UserID = $_GET['ID'];
        $UserInfo = $WechatUserModule->GetInfoByKeyID($UserID);
        $BargainInfo = $WechatBargainModule->GetInfoByWhere(' and UserID = '.$UserID);

        //微信SignPackage
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();
        //分享Url
        $Url = WEB_WECHAT_URL.'/bargain/helpbargain/?ID='.$UserID;


        $IsBargain = $WechatBargainLogModule->GetInfoByWhere(' and BargainUserID = '.$MyUserID.' and ToBargainUserID = '.$UserID);
        if($IsBargain){
            $Message = '我为'.$UserInfo['NickName'].'的极光之旅砍了'.$IsBargain['BargainAmount'].'元,快来帮忙!';
        }
        else{
            $Message = '我朋友'.$UserInfo['NickName'].'想要去看极光!大家快来帮忙!';
        }
        $Url = WEB_WECHAT_URL.'/bargain/helpbargain/?ID='.$UserID;
        $Title = 'FM107北极圈极光之旅';
        include template('Bargain/HelpBargainResult');
    }

    /**
     * @desc  砍价排行
     */
    public function Ranking(){
        $Title = 'FM107北极圈极光之旅';

        include template('Bargain/Ranking');
    }

    /**
     * @desc 帮忙砍价者跳转到购买者砍价页面，但是购买者活动时间结束
     */
    public function HelpTimeEnd(){
        $UserID = $_GET['ID'];

        $BargainModule = new WechatBargainModule();
        $BargainInfo = $BargainModule->GetInfoByWhere(' and UserID = '.$UserID);

        $WechatUserModule = new WechatUserModule();
        $UserInfo = $WechatUserModule->GetInfoByKeyID($UserID);


        //微信SignPackage
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();

        $Url = WEB_WECHAT_URL.'/bargain/index/';

        include template('Bargain/HelpTimeEnd');

    }

    /**
     * @desc  获取砍价排行榜
     */
    public function GetRanking(){
        $WechatBargainModule = new WechatBargainModule();
        $PageSize = 10;
        $Page = intval($_GET['Page']) ? intval($_GET['Page']) : '1';
        $Rscount = $WechatBargainModule->GetListsNum('');
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data = $WechatBargainModule->GetBargainRanking( $Offset, $Data ['PageSize']);
            foreach($Data as $key => $val){
                $Data[$key]['BargainTime'] = date("Y-m-d H:i:s",$val['BargainTime']);
            }
        }
        if ($Data) {
            $result = array('ResultCode' => 200, 'Data' => $Data );
        } else {
            $result = array('ResultCode' => 200, 'Data' => array() , 'Message' => '亲，没有更多内容了！');
        }
        echo json_encode($result);
    }

    /**
     * @desc  产品活动详情
     */
    public function Detail(){
        $Title = 'FM107北极圈极光之旅';

        //微信SignPackage
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();

        $Message = '我要去看极光!据说朋友越多省越多,快来帮忙!';
        $Url = WEB_WECHAT_URL.'/bargain/index/';

        include template('Bargain/Detail');
    }

    /**
     * @desc  判断是否授权,并跳转
     * @param $RedirectUrl 跳转页面
     */
    public function JudgeAuthorize($RedirectUrl)
    {
        if (!$_SESSION['UserInfo']['UserID']) {
            $ApiWechatModule = new ApiWechat($this->config);
            $AuthorizeUrl = $ApiWechatModule->GetAuthorizeUrl($RedirectUrl);
            header("Location:" . $AuthorizeUrl);
        }
    }

    /**
     * @desc  判断用户是否关注公众号
     */
    public function JudgeFollow($ForeignKey)
    {
        $ApiWechatModule = new ApiWechat($this->config);
        return $ApiWechatModule->JudgeFollow($ForeignKey);
    }


}