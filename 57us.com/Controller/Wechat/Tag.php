<?php

/**
 * @desc  专属标签活动
 * Class Tag
 */
class Tag extends WechatCommon {

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
        //授权后跳转页
        $this->RedirectUrl = WEB_WECHAT_URL.'/tag/setuserinfosession/';
        include SYSTEM_ROOTPATH.'/Controller/Wechat/Class.ApiWechat.php';
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
                $TagModule = new WechatTagModule();
                $HaveTag = $TagModule->GetInfoByWhere(' and UserID = '.$CheckWechat['UserID']);
                if(!$HaveTag){
                    $this->AwardTag($CheckWechat['Sex']);
                }
            } else {
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
                $this->AwardTag($Data['Sex']);
            }
            if($_SESSION["GoUrl"]){
                $Url = WEB_WECHAT_URL.$_SESSION["GoUrl"];
            }
            else{
                $Url = WEB_WECHAT_URL.'/tag/index/';
            }
            header("Location:" . $Url);
        }
    }

    /**
     * @desc  根据性别授予标签
     * @param $Sex
     */
    public function AwardTag($Sex){
        $TagModule = new WechatTagModule();
        if($Sex == 2){ //女
            $TagArray = array_flip($TagModule->MadamTag);
        }
        elseif($Sex == 1){ //男
            $TagArray = array_flip($TagModule->ManTag);
        }
        else{
            $TagArray = array_flip(array_merge($TagModule->ManTag,$TagModule->MadamTag));
        }
        $TagArray = array_rand($TagArray,5);
        $UserID = $_SESSION['UserInfo']['UserID'];
        foreach($TagArray as $val){
            $Data = array('UserID'=>$UserID,'TagName'=>$val);
            $TagModule->InsertInfo($Data);
        }
    }

    /**
     * @desc 首页
     */
    public function Index()
    {
        $MyUserID = $_SESSION['UserInfo']['UserID'];
        //判断是否授权
        if(!$MyUserID){
            $_SESSION['GoUrl'] = '/tag/index/';
            //判断是否授权
            $this->JudgeAuthorize($this->RedirectUrl);
        }
        $UserModule = new WechatUserModule();
        $TagModule = new WechatTagModule();
        $TagLogModule = new WechatTagLogModule();

        $UserInfo = $UserModule->GetInfoByKeyID($MyUserID);
        $TagInfo = $TagModule->GetInfoByWhere(' and UserID = '.$MyUserID,true);

        foreach($TagInfo as $key => $val){
            $TagInfo[$key]['IsLike'] = $TagLogModule->GetInfoByWhere(' and OperateUserID = '.$MyUserID.' and TagID = '.$val['TagID']);
            $TagInfo[$key]['LikeUser'] = $TagLogModule->GetInfoByWhere(' and TagID = '.$val['TagID'],true);
        }

        //微信SignPackage
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();

        //分享信息
        $ShareUrl = WEB_WECHAT_URL.'/tag/other/?UserID='.$MyUserID; //分享Url
        $ShareTitle = '【'.$UserInfo['NickName'].'】的专属标签，我有这么好你知道吗？'; //分享标题
        $ShareDesc = '真羡慕你们，这么早就认识了才华横溢美丽动人的我'; //分享描述
        $ShareImg = $UserInfo['HeadImgUrl']; //分享图片

        $Title = '看看我究竟哪里好';
        include template('Tag/Index');
    }

    /**
     * @desc  其他用户参与页面（可点赞）
     */
    public function Other(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        $ToUserID = $_GET['UserID'];
        $MyUserID = $_SESSION['UserInfo']['UserID'];

        if(!$MyUserID){
            $_SESSION['GoUrl'] = '/tag/other/?UserID='.$ToUserID;
            //判断是否授权
            $this->JudgeAuthorize($this->RedirectUrl);
            $MyUserID = $_SESSION['UserInfo']['UserID'];
        }
        if($MyUserID == $ToUserID){
            header("Location:" . WEB_WECHAT_URL .'/tag/index/');
        }
        $WechatUserModule = new WechatUserModule();
        $TagLogModule = new WechatTagLogModule();
        $TagModule = new WechatTagModule();

        $ToUserInfo = $WechatUserModule->GetInfoByKeyID($ToUserID);
        $TagInfo = $TagModule->GetInfoByWhere(' and UserID = '.$ToUserID,true);

        foreach($TagInfo as $key => $val){
            $TagInfo[$key]['IsLike'] = $TagLogModule->GetInfoByWhere(' and OperateUserID = '.$MyUserID.' and TagID = '.$val['TagID']);
            $TagInfo[$key]['LikeUser'] = $TagLogModule->GetInfoByWhere(' and TagID = '.$val['TagID'],true);
        }

        //判断是否关注
        $MyUserInfo = $WechatUserModule->GetInfoByKeyID($MyUserID);
        $IsJudge = $this->JudgeFollow($MyUserInfo['ForeignKey']);

        //微信SignPackage
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();

        //分享信息
        $ShareUrl = WEB_WECHAT_URL.'/tag/other/?UserID='.$ToUserID; //分享Url
        $ShareTitle = '【'.$ToUserInfo['NickName'].'】的专属标签，我有这么好你知道吗？'; //分享标题
        $ShareDesc = '真羡慕你们，这么早就认识了才华横溢美丽动人的我'; //分享描述
        $ShareImg = $ToUserInfo['HeadImgUrl']; //分享图片$

        $Title = '看看我究竟哪里好';
        include template('Tag/Other');
    }

    /**
     * @desc  点赞操作
     */
    public function LikeOperate(){
        $TagLogModule = new WechatTagLogModule();
        $WechatUserModule = new WechatUserModule();

        $TagID = $_POST['TagID'];
        $MyUserID = $_SESSION['UserInfo']['UserID'];
        $MyUserInfo = $WechatUserModule->GetInfoByKeyID($MyUserID);

        $IsTag = $TagLogModule->GetInfoByWhere(' and OperateUserID = '.$MyUserID.' and TagID = '.$TagID);
        if($IsTag){
            $json_result = array('ResultCode' => 100, 'Message' => '您已经赞同过此标签');
        }
        else{
            $Data = array(
                'TagID'=>$TagID,
                'OperateUserID'=>$MyUserID,
                'OperateUserNickName'=>$MyUserInfo['NickName'],
                'OperateUserHeadImgUrl'=>$MyUserInfo['HeadImgUrl'],
                'OperateTime'=>time()
            );
            $Result = $TagLogModule->InsertInfo($Data);
            if($Result){
                $TagLog = $TagLogModule->GetInfoByWhere(' and TagID = '.$TagID,true);
                $Count = count($TagLog);
                if($TagLog){
                    $ImgArray = array();
                    foreach($TagLog as $val){
                        $ImgArray[] = $val['OperateUserHeadImgUrl'];
                    }
                }
                else{
                    $ImgArray = array();
                }
                $json_result = array('ResultCode' => 200, 'Message' => '点赞成功','Count'=>$Count,'ImgArray'=>$ImgArray);
            }
            else{
                $json_result = array('ResultCode' => 101, 'Message' => '点赞失败');
            }
        }
        echo json_encode($json_result);
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

    public function Test(){
        $WetchatTag = new WechatTagModule();
        $Info = $WetchatTag->GetInfoer();
        $WechatUserModule = new WechatUserModule();
        $i = 0;
        $j = 0;
        foreach($Info as $val){
            $UserInfo = $WechatUserModule->GetInfoByWhere(' and UserID = '.$val['UserID']);
            if($UserInfo['Sex'] == 1){
                $i++;
            }
            elseif($UserInfo['Sex'] == 2){
                $j++;
            }
        }
        echo "<pre>";print_r($i);echo "<br>";
        echo $j;exit;
    }


}