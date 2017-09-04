<?php

class Ajax
{
    public function __construct()
    {
    }

    public function Index()
    {
        $Intention = trim($_POST ['Intention']);
        if ($Intention == '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            echo json_encode($json_result);
            exit;
        }
        $this->$Intention ();
    }
    
    //绑定邮箱或手机
    private function BindingAccount()
    {
        $Account = trim($_POST ['User']);
        if (is_numeric($Account)) {
            $Type = 0;
            $Data ['Mobile'] = $Account;
        } elseif (strpos($Account, '@')) {
            $Type = 1;
            $Data ['E-Mail'] = $Account;
        }
        $User = new MemberUserModule ();
        if ($User->AccountExists($Account)) {
            $json_result = array('ResultCode' => 102, 'Message' => '保存失败,已经存在');
        } else {
            $VerifyCode = $_POST ['VerifyCode'];
            $Authentication = new MemberAuthenticationModule ();
            $TempUserInfo = $Authentication->GetAccountInfo($Account, $VerifyCode, $Type);
            $ID = $Authentication->ValidateAccount($Account, $VerifyCode, $Type);
            if ($ID) {
                $CurrentTime = time();
                if ($CurrentTime > $TempUserInfo ['XpirationDate']) {
                    $json_result = array('ResultCode' => 104, 'Message' => '短信验证码过期');
                } else {
                    if ($User->UpdateUser($Data, $_SESSION ['UserID'])) {
                        $Authentication->DeleteUser($ID);
                        $json_result = array('ResultCode' => 200, 'Message' => '保存成功', 'Url' => WEB_MUSER_URL . '/member/securitycenter/');
                    } else {
                        $json_result = array('ResultCode' => 100, 'Message' => '保存失败,请重试');
                    }
                }
            } else {
                $json_result = array('ResultCode' => 101, 'Message' => '短信验证码错误');
            }
        }
        echo json_encode($json_result);
    }


    
    //获取客户SESSION信息
    private function GetSession()
    {
        $MemberUserInfoModule = new MemberUserInfoModule ();
        $Data ['UserID'] = $_POST ['ID'];
        $Data ['Account'] = $_POST ['Account'];
        $UserInfo = $MemberUserInfoModule->GetUserInfo($Data ['UserID']);
        $Data ['Identity'] = $UserInfo['Identity'];
        $Data ['NickName'] = $UserInfo ['NickName'];
        $Data ['Level'] = $UserInfo ['Level'];
        $Data ['CountIntegral'] = $UserInfo ['CountIntegral'];
        $Data ['Integral'] = $UserInfo ['Integral'];
        $Data ['Avatar'] = $UserInfo ['Avatar'];
        echo json_encode($Data);
    }
    
    /**
     * @desc  订单超时关闭订单
     */
    private function Expiration(){
        if($_POST){
            if (trim($_POST['type'])=='studytour'){
                $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
                $OrderID = $_POST['OrderID'];
                $Data['Status'] =10;
                $Result = $StudyYoosureOrderModule->UpdateInfoByKeyID($Data,$OrderID);
                if ($Result){
                    $res = array('ResultCode' => '200', 'Message' => '订单超时(关闭订单)');
                }else{
                    $res = array('ResultCode' => '100', 'Message' => '订单状态更新失败');
                }
                EchoResult($res);
            }else{
                include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderModule.php';
                $TourProductOrderModule = new TourProductOrderModule();
                $OrderID = $_POST['OrderID'];
                $Data['Status'] =10;
                $Result = $TourProductOrderModule->UpdateInfoByKeyID($Data,$OrderID);
                if ($Result){
                    $res = array('ResultCode' => '200', 'Message' => '订单超时(关闭订单)');
                }else{
                    $res = array('ResultCode' => '100', 'Message' => '订单状态更新失败');
                }
                EchoResult($res);
            }
        }
    }
    
    //重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构--重构
    /**
     * 需要登录
     * by Leo
     */
    private function NeedLogin(){
        if(!$_SESSION['UserID']){
            $json_result = array(
                'ResultCode' => 102,
                'Message' => '请先登录',
                'Url'=>WEB_MUSER_URL
            );
            echo json_encode($json_result);
            exit;
        }
    }

    /**
     * 检测邮箱或手机号是否可用
     */
    private function AccountExists($Account)
    {
        if (trim($Account)) {
            $User = new MemberUserModule ();
            $UserInfo = $User->AccountExists($Account);
            if ($UserInfo) {
                if (is_numeric($Account)) {
                    $result = array('ResultCode' => 101,'Message'=>'该手机号码已绑定过账号');
                } elseif (strpos($Account, '@')) {
                    $result = array('ResultCode' => 101,'Message'=>'该邮箱已绑定过账号');
                }
                echo json_encode($result);
                exit;
            }
        } else {
            $result = array('ResultCode' => 100,'Message'=>'请填写正确的账号');
            echo json_encode($result);
            exit;
        }
    }    
    
    /**
     * 获取验证码
     */
    private function GetVerifyCode(){
        $Account=trim($_POST['User']);
        if($Account){
            if(isset($_POST['ImageCode'])){
                //存在验证码为密码找回，无须验证是否存在
                    $ImageCode = strtolower(trim($_POST['ImageCode']));
                    if($ImageCode != $_SESSION['authnum_session']){
                        $result = array('ResultCode' => 101,'Message'=>'图形验证码错误');
                        echo json_encode($result);
                        exit;
                    }
            }else{
                //验证账号可用性
                $this->AccountExists($Account);
            }
        }else{
            //修改手机或邮箱时获取，需登录状态
            $this->NeedLogin();
            $Type=intval($_POST['Type']);
            $UserModule = new MemberUserModule();
            $User = $UserModule->GetInfoByKeyID($_SESSION['UserID']);
            if($Type==1){
                $Account=$User['Mobile'];
            }else{
                $Account=$User['E-Mail'];
            }
        }
        if (is_numeric($Account)) {
            //获取手机验证码
            $result = MemberService::SendMobileVerificationCode($Account);
        }elseif (strpos($Account, '@')) {
            //获取邮箱验证码
            //调用短信验证码发送接口
            $result = MemberService::SendMailVerificationCode($Account);
        }
        echo json_encode($result);
    }

    /**
     * 进行验证码确认
     */
    private function DoVerify(){
        $Account=trim($_POST['User']);
        $VerifyCode = intval(trim($_POST['Code']));
        if(!$Account){
            //没账号需要登录验证
            $this->NeedLogin();
            $UserModule = new MemberUserModule();
            $User = $UserModule->GetInfoByKeyID($_SESSION['UserID']);
            $Type=intval($_POST['Type']);
            if($Type==1){
                $Account=$User['Mobile'];
            }else{
                $Account=$User['E-Mail'];
            }
        }
        $result = MemberService::VerifySendCode($VerifyCode,$Account);
        echo json_encode($result);
    }
   
    /**
     * @desc  注册用户
     */
    private function RegisterUser()
    {
        $Account=trim($_POST['User']);
        if ($Account) {
            $User = new MemberUserModule();
            if ($User->AccountExists($Account)) {
                $result = array(
                    'ResultCode' => 103,
                    'Message' => '该账号已被注册,请更换账号重试',
                    'Url' => WEB_MUSER_URL.'/member/register/'
                );
            } elseif (trim($_POST['PassWord']) != trim($_POST['PassWordConfirm'])) {
                $result = array(
                    'ResultCode' => 104,
                    'Message' => '两次密码不一致,请重试！',
                    'Url' => ''
                );
            } else {
                if (is_numeric($Account)) {
                    $Data['Mobile'] = $Account;
                } elseif (strpos($Account, '@')) {
                    $Data['E-Mail'] = $Account;
                }
                $Data['AddTime'] = Time();
                $Data['State'] = 1;
                $Data['PassWord'] = md5(trim($_POST['PassWord']));
                $insert_result = $User->InsertUser($Data);
                if ($insert_result) {
                    $AccountInfo = $User->AccountExists($Account);
                    $UserInfo = new MemberUserInfoModule();
                    $InfoData['UserID'] = $AccountInfo['UserID'];
                    $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                    $InfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
                    $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                    $InfoData['Sex'] = 1;
                    $InfoData['Avatar']='/img/man3.0.png';
                    $UserInfo->InsertData($InfoData);
                    // 同步SESSIONID
                    setcookie("session_id", session_id(), time() + 3600 * 24, "/", WEB_HOST_URL);
                    $_SESSION['UserID'] = $InfoData['UserID'];
                    $_SESSION['NiceName'] = $InfoData['NickName'];
                    $_SESSION['Account'] = $Account;
                    setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
                    $result = array(
                        'ResultCode' => 200,
                        'Message' => '注册成功',
                        'Url' => WEB_MUSER_URL.'/member/login/'
                    );
                } else {
                    $result = array(
                        'ResultCode' => 100,
                        'Message' => '发生异常,注册失败'
                    );
                }
            }
        } else {
            $result = array(
                'ResultCode' => 102,
                'Message' => '异常的请求,请重新注册',
                'Url' => WEB_MUSER_URL.'/member/register/'
            );
        }
        echo json_encode($result);
        exit();
    }    
 
    // 手机号码直接登录（获取短信验证码）
    private function MpLogin()
    {
        if ($_POST){
            $Data['Account'] = ($_POST['User']);
            $Data['VerifyCode'] = mt_rand(100000, 999999);
            $Data['XpirationDate'] = Time() + 60 * 30;
            if (is_numeric($Data['Account'])) {
                $Data['Type'] = 0;
            } elseif (strpos($Data['Account'], '@')) {
                $Data['Type'] = 1;
            }
            $result = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
            if ($result) {
                // 成功添加到本地表
                $Authentication = new MemberAuthenticationModule();
                $Authentication->InsertUser($Data);
                $json_result = array(
                    'ResultCode' => 200,
                    'Message' => '发送成功',
                    'Url' => ''
                );
            } else {
                $json_result = array(
                    'ResultCode' => 100,
                    'Message' => '验证码发送失败,请重试'
                );
            }
        }
        echo json_encode($json_result);
        exit();
    }
  
    // 手机号码直接登录（短信验证）
    private function MpLoginVerify()
    {
        $VerifyCode = intval(trim($_POST['Code']));
        $Account = trim($_POST['User']);
        if (is_numeric($Account)) {
            $Type = 0;
        } elseif (strpos($Account, '@')) {
            $Type = 1;
        }
        $Authentication = new MemberAuthenticationModule();
        $TempUserInfo = $Authentication->GetAccountInfo($Account, $VerifyCode, $Type);
        if ($TempUserInfo) {
            $CurrentTime = time();
            if ($CurrentTime > $TempUserInfo['XpirationDate']) {
                $json_result = array(
                    'ResultCode' => 103,
                    'Message' => '短信验证码过期'
                );
            } else {
                $User = new MemberUserModule();
                $UserInfo = $User->GetUserIDbyMobile($Account);
                if (empty($UserInfo)) {
                    $InsertUserInfo['Mobile'] = $Account;
                    $InsertUserInfo['AddTime'] = time();
                    $UserID = $User->InsertInfo($InsertUserInfo);
                    $Url = WEB_MUSER_URL . '?pass=null';
                } else {
                    $UserID = $UserInfo['UserID'];
                    if ($UserInfo['PassWord'] == '') {
                        $Url = WEB_MUSER_URL . '?pass=null';
                    } else {
                        $Url = WEB_MUSER_URL;
                    }
                }
                $FromType=intval($_POST['Type']);
                if($FromType){
                    $Url=WEB_MUSER_URL.'/muserstudy/';
                }else{
                    $Url=WEB_MUSER_URL.'/member/mycenter/';
                }
                $XpirationDate = time() + 3600 * 24;
                // 同步SESSIONID
                setcookie("session_id", session_id(), $XpirationDate, "/", WEB_HOST_URL);
                $_SESSION['UserID'] = $UserID;
                $_SESSION['Account'] = $Account;
                setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
                $UserInfo = new MemberUserInfoModule();
                $Data['LastLogin'] = date('Y-m-d H:i:s', time());
                $UserInfo->UpdateData($Data, $UserID);
                $json_result = array(
                    'ResultCode' => 200,
                    'Message' => '登录成功',
                    'Url' => $Url
                );
            }
        } else {
            $json_result = array(
                'ResultCode' => 102,
                'Message' => '短信验证码错误'
            );
        }
        echo json_encode($json_result);
        exit();
    }
    
    /**
     * 重新设置密码
     */
    private function ResetNewPassWord(){
        $Account=trim($_POST['User']);
        if (isset($Account) && !empty($Account)) {
            $MemberUserModule = new MemberUserModule();
            $UserInfo = $MemberUserModule->AccountExists($Account);
            if (trim($_POST['PassWord']) != trim($_POST['PassWordConfirm'])) {
                $result = array(
                    'ResultCode' => 104,
                    'Message' => '两次密码不一样，请重试！',
                    'Url' => WEB_MUSER_URL .'/member/resetpassword/'
                );
            } elseif ($UserInfo) {
                $Data['PassWord'] = md5(trim($_POST['PassWord']));
                $update_result = $MemberUserModule->UpdateInfoByKeyID($Data, $UserInfo['UserID']);
                if ($update_result || $update_result === 0) {
                    $MemberUserInfoModule = new MemberUserInfoModule();
                    $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                    $MemberUserInfoModule->UpdateData($InfoData, $UserInfo['UserID']);
                    $UserInfo = $MemberUserInfoModule->GetUserInfo($UserInfo['UserID']);
                    // 同步SESSIONID
                    setcookie("session_id", session_id(), time() + 3600 * 24, "/", WEB_HOST_URL);
                    $_SESSION['UserID'] = $UserInfo['UserID'];
                    $_SESSION['NiceName'] = $UserInfo['NickName'];
                    $_SESSION['Account'] = $Account;
                    $result = array(
                        'ResultCode' => 200,
                        'Message' => '修改成功',
                        'Url' => WEB_MUSER_URL. '/member/mycenter/'
                    );
                } else {
                    $result = array(
                        'ResultCode' => 100,
                        'Message' => '修改失败,发生异常'
                    );
                }
            } else {
                $result = array(
                    'ResultCode' => 101,
                    'Message' => '修改的账号不存在',
                    'Url' => WEB_MUSER_URL . '/member/resetpassword/'
                );
            }
        } else {
            $result = array(
                'ResultCode' => 102,
                'Message' => '异常的请求,请先进行身份验证',
                'Url' => WEB_MUSER_URL . '/member/resetpassword/'
            );
        }
        echo json_encode($result);
        exit();
    }
    
    
    /**
     * @desc 手机
     * @desc 绑定手机号码
     */
    private function BindingMobile(){
        $this->NeedLogin();
        $VerifyCode = intval(trim($_POST['Code']));
        $Mobile = $_POST['Mobile'];
        //验证短信验证码
        $Result = MemberService::VerifySendCode($VerifyCode,$Mobile);
        if($Result['ResultCode'] == 200){ //验证码验证通过
            $UserModule = new MemberUserModule();
            $Result1 = $UserModule->UpdateInfoByKeyID(array('Mobile'=>$Mobile),$_SESSION['UserID']);
            if($Result1){
                $result_josn = array('ResultCode' => 200, 'Message' => '手机号码更新成功');
            }
            else{
                $result_josn = array('ResultCode' => 101, 'Message' => '手机号码更新失败');
            }
        }
        else{
            $result_josn = $Result;
        }
        echo json_encode($result_josn);
    }

    /**
     * @desc 邮箱
     * @desc 绑定邮箱号码
     */
    private function BindingMail(){
        //判断是否登陆
        $this->NeedLogin();
        $VerifyCode = intval(trim($_POST['Code']));
        $Email = $_POST['Mail'];
        //验证短信验证码
        $Result = MemberService::VerifySendCode($VerifyCode,$Email);
        if($Result['ResultCode'] == 200){ //验证码验证通过
            $UserModule = new MemberUserModule();
            $Result1 = $UserModule->UpdateInfoByKeyID(array('E-Mail'=>$Email),$_SESSION['UserID']);
            if($Result1){
                $result_josn = array('ResultCode' => 200, 'Message' => '邮箱更新成功');
            }
            else{
                $result_josn = array('ResultCode' => 101, 'Message' => '邮箱更新失败');
            }
        }
        else{
            $result_josn = $Result;
        }
        echo json_encode($result_josn);
    }   
    
    /**
     * 会员普通登录
     */
    private function MuserLogin()
    {
        $ImageCode = strtolower($_POST['ImageCode']);
        if ($_COOKIE['PasswordErrTimes'] < 3) {
            $_SESSION['authnum_session'] = $ImageCode;
        }
        if ($ImageCode == $_SESSION['authnum_session']) {
            $Account = ($_POST['User']);
            $User = new MemberUserModule();
            //判断账号是否注册
            $UserInfo = $User->AccountExists($Account);
            if (empty($UserInfo)) {
                $json_result = array(
                    'ResultCode' => 106,
                    'Message' => '未注册的账号！',
                    'Url' => ''
                );
                echo json_encode($json_result);
                exit;
            }
            $PassWord = md5($_POST['Pass']);
            $UserID = $User->CheckUser($Account, $PassWord);
            MemberService::SendSystemMessage($UserID);
            if ($UserID) {
                $XpirationDate = time() + 3600 * 24;
                if ($_POST['AutoLogin'] == 1) {
                    setcookie("UserID", $UserID, $XpirationDate, "/");
                    setcookie("Account", $Account, $XpirationDate, "/");
                }
                // 同步SESSIONID
                setcookie("session_id", session_id(), $XpirationDate, "/");
                $_SESSION['UserID'] = $UserID;
                $_SESSION['Account'] = $Account;
                $UserInfoModule = new MemberUserInfoModule();
                $Data['LastLogin'] = date('Y-m-d H:i:s', time());
                $UserInfoModule->UpdateData($Data, $UserID);
                $FromType=intval($_POST['Type']);
                if($FromType){
                    $Url=WEB_MUSER_URL.'/muserstudy/';
                }else{
                    $Url=WEB_MUSER_URL.'/member/mycenter/';
                }
                $json_result = array(
                    'ResultCode' => 200,
                    'Message' => '登录成功',
                    'Url' => $Url
                );
            } else {
                // 设置密码超过三次
                $PasswordErrTimes = intval($_COOKIE['PasswordErrTimes']) + 1;
                setcookie("PasswordErrTimes", $PasswordErrTimes, time() + 3600);
                if ($PasswordErrTimes > 2) {
                    $json_result = array(
                        'ResultCode' => 105,
                        'Message' => '您输入的密码错误，请重新输入!'
                    );
                } else {
                    $json_result = array(
                        'ResultCode' => 100,
                        'Message' => '您输入的密码错误，请重新输入!'
                    );
                }
            }
        } else {
            $json_result = array(
                'ResultCode' => 101,
                'Message' => '验证码错误'
            );
        }
        echo json_encode($json_result);
        exit;
    }
    
    //上传头像
    private function SaveAvatar()
    {
        $this->NeedLogin();
        $Img = trim($_POST['Img']);
        $Img = preg_replace('/^data\:image\/jpeg\;base64\,/iU', '', $Img);
        $ImgUrl = '/up/' . date('Y') . '/' . date('md') . '/' . date('YmdHis') . mt_rand(100, 999) . '.jpg';
        if ($Img) {
            $MemberUserInfoModule = new MemberUserInfoModule();
            $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
            if ($UserInfo['Avatar'] != '') {
                if (!strpos($UserInfo['Avatar'], 'http://')) {
                    //删除图片
                    DelFromImgServ($UserInfo['Avatar']);
                }
            }
            if ($MemberUserInfoModule->UpdateData(array('Avatar' => $ImgUrl), $_SESSION['UserID'])) {
                //上传图片服务器
                if (SendToImgServ($ImgUrl, $Img) == 'true') {
                    $_SESSION['Avatar'] = ImageURLP2 . $ImgUrl;
                    $json_result = array('ResultCode' => 200, 'Message' => '保存成功', 'ImgUrl' => $_SESSION['Avatar']);
                } else {
                    $json_result = array('ResultCode' => 102, 'Message' => '上传失败');
                }
            } else {
                $json_result = array('ResultCode' => 101, 'Message' => '保存失败');
            }
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '保存失败');
        }
        echo json_encode($json_result);
    }    
    
    
    /**
     * 个人资料编辑
     */
    private function SaveInformation(){
        $this->NeedLogin();
        $UserID=$_SESSION['UserID'];
        $Type=$_POST['Type'];
        $MemberUserInfoModule =new MemberUserInfoModule();
        switch($Type){
            case 'nickname':
                $Data['NickName']=trim($_POST['NickName']);
                $UserInfo = $MemberUserInfoModule->CheckNickName($Data ['NickName']);
                if ($UserInfo && $UserInfo['UserID'] != $_SESSION['UserID']) {
                    $json_result = array('ResultCode' => 100, 'Message' => '保存失败,昵称已经存在');
                    echo json_encode($json_result);
                    exit ();
                }
                break;
            case 'realname':
                $Data['RealName']=trim($_POST['RealName']);
                break;
            case 'sex':
                $Data['Sex']=intval(trim($_POST['Sex']));
                break;
            case 'birthday':
                $Data['BirthDay']=trim($_POST['BirthDay']);
                break;
            case 'city':
                $Address = explode(' ',trim($_POST['City']));
                $Data['Province'] = $Address[0];
                $Data['City'] = $Address[1];
                $Data['Area'] = $Address[2]; 
                break;
        }
        $Result=$MemberUserInfoModule->UpdateInfoByWhere($Data,' UserID = '.$UserID);
        if($Result!==false){
            $json_result=array('ResultCode'=>200,'Message'=>'保存成功');
        }else{
            $json_result=array('ResultCode'=>101,'Message'=>'保存失败');
        }
        echo json_encode($json_result);
    }
    
    //检测昵称是否可用
    private function CheckNickName()
    {
        $NickName = trim($_POST ['NickName']);
        if ($NickName) {
            $UserInfo = new MemberUserInfoModule ();
            $SearchResult = $UserInfo->CheckNickName($NickName);
            if ($SearchResult) {
                $result = array('ResultCode' => 100);
            } else {
                $result = array('ResultCode' => 200);
            }
        } else {
            $result = array('ResultCode' => 100);
        }
        echo json_encode($result);
    }
    
    /**
     * @desc  密码修改
     */
    private function ModifyPass()
    {
        $this->NeedLogin();
        $PassWord = md5($_POST ['Pass']);
        $MemberUserModule = new MemberUserModule ();
        $User = $MemberUserModule->GetUserByID($_SESSION ['UserID']);
        if ($User['PassWord'] == '' || $PassWord == $User ['PassWord']) {
            $Data ['PassWord'] = md5($_POST ['NewPass']);
            $Result = $MemberUserModule->UpdateUser($Data, $_SESSION ['UserID']);
            if ($Result || $Result === 0) {
                $json_result = array('ResultCode' => 200, 'Message' => '修改成功', 'Url' => WEB_MUSER_URL);
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '修改失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 101, 'Message' => '原密码输入错误');
        }
        echo json_encode($json_result);
    }
    
    //收藏模块--------------------------------------------------------------------------------
    /**
     * 获取收藏列表
     */
    private function GetCollection(){
        $this->NeedLogin();
        $UserID=$_SESSION['UserID'];
        $Type=$_POST['t'];
        switch($Type){
            case 'news':
                $Category='10,11,12,13';
                break;
            case 'tour':
                $Category='4,5,6,7,14';
                break;
        }
        $MysqlWhere=" and UserID=$UserID and `Category` in ($Category)";
        $MemberCollectionModule=new MemberCollectionModule();
        $Rscount = $MemberCollectionModule->GetListsNum($MysqlWhere);
        $Page=intval($_POST['Page'])?intval($_POST['Page']):0;
        if ($Page < 1) {
            $Page = 1;
        }
        if ($Rscount['Num']) {
            $PageSize=8;
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($Page > $Data['PageCount']){
                $CollectionList=array();
            }else{
                $Data['Page'] = min($Page, $Data['PageCount']);
                $Offset = ($Page - 1) * $Data['PageSize'];
                $MysqlWhere .=' order by CollectionID desc';
                $CollectionList = $MemberCollectionModule->GetLists($MysqlWhere,$Offset,$Data['PageSize']);
            }        
            $JsonData=array();
            foreach($CollectionList as $Key=>$CollectionInfo){
                $JsonData[$Key]=$this->GetCollect($CollectionInfo['Category'],$CollectionInfo['RelevanceID']);
                $JsonData[$Key]['ID']=$CollectionInfo['CollectionID'];
            }
            if(count($JsonData)){
                $ResultCode=200;
                $Message='';
            }else{
                //$ResultCode=101;
                $ResultCode=200;
                $Message='没有更多数据了';
            }
        }else{
            $ResultCode=102;
            $Message='没有数据';
        }
        $json_result=array('ResultCode'=>$ResultCode,'Data'=>$JsonData,'RecordCount'=>$Rscount['Num'],'Message'=>$Message);
        echo json_encode($json_result);
    }
    
    /**
     * @dese 获取收藏信息
     */
    private function  GetCollect($Category,$RelevanceID)
    {
        switch ($Category) {
            case '1'://服务
                $StudyConsultantServiceModule = new StudyConsultantServiceModule();
                $StudyConsultantService = $StudyConsultantServiceModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='服务';
                $Collection['ProductName'] =  $StudyConsultantService['ServiceName'];
                $ImagesJson = json_decode($StudyConsultantService['ImagesJson'],true);
                $Collection['ImageUrl']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($StudyConsultantService['ImagesJson'],true)[$StudyConsultantService['CoverImageKey']]):(ImageURL.'/img/study/defaultService3.0.jpg');
                $Collection['Url'] =  WEB_STUDY_URL.'/consultant_service/'. $StudyConsultantService['ServiceID'].'.html';
                break;
            case '2'://课程
                $StudyTeacherCourseModule = new StudyTeacherCourseModule();
                $StudyTeacherCourse = $StudyTeacherCourseModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='课程';
                $Collection['ProductName'] =  $StudyTeacherCourse['CourseName'];
                $ImagesJson = json_decode($StudyTeacherCourse['ImagesJson'],true);
                $Collection['ImageUrl']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($StudyTeacherCourse['ImagesJson'],true)[$StudyTeacherCourse['CoverImageKey']]):(ImageURL.'/img/study/defaultClass3.0.jpg');
                $Collection['Url'] =  WEB_STUDY_URL.'/teacher_course/'. $StudyTeacherCourse['CourseID'].'.html';
                break;
            case '3'://游学产品
                $StudyYoosureModule = new StudyYoosureModule();
                $StudyYoosure = $StudyYoosureModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='游学';
                $Collection['ProductName'] = $StudyYoosure['Title'];
                $StudyYoosureImageModule = new StudyYoosureImageModule();
                $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$RelevanceID.' and IsDefault = 1');
                if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                    $Collection['ImageUrl'] = LImageURL.$YoosureImage['Image'];
                }else{
                    $Collection['ImageUrl'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
                $Collection['Url'] =  WEB_STUDY_URL.'/studytour/'.$StudyYoosure['YoosureID'].'.html';
                break;
            case '4'://出游 （当地参团、国内跟团、特色体验、一日游）
                $TourProductModule = new TourProductModule();
                $TourProductImageModule = new TourProductImageModule();
                $TourProduct = $TourProductModule->GetInfoByKeyID($RelevanceID);
                $TourProductImage = $TourProductImageModule->GetInfoByTourProductID($RelevanceID);
                $Collection['Type'] ='出游';
                $Collection['ProductName'] = $TourProduct['ProductName'];
                $Collection['ImageUrl'] =  ImageURLP2.$TourProductImage['ImageUrl'];
                if ($TourProduct['Category']=='4'||$TourProduct['Category']=='12'){
                    $Collection['Url'] =  WEB_TOUR_URL.'/group/'.$TourProduct['TourProductID'].'.html';
                }elseif($TourProduct['Category']=='6'||$TourProduct['Category']=='9'){
                    $Collection['Url'] =  WEB_TOUR_URL.'/play/'.$TourProduct['TourProductID'].'.html';
                }
                break;

            case '5'://酒店
                $HotelBaseInfoModule = new HotelBaseInfoModule();
                $HotelBaseInfo = $HotelBaseInfoModule->GetInfoByWhere(' and HotelID ='.$RelevanceID);
                $Collection['Type'] ='酒店';
                $Collection['ProductName'] = $HotelBaseInfo['Name_Cn'];
                $Collection['ImageUrl'] =  ImageURLP2.$HotelBaseInfo['Image'];
                $Collection['Url'] =  WEB_HOTEL_URL.'/hotel/'.$HotelBaseInfo['HotelID'].'.html';
                break;
            case '6'://租车,目前没有
                break;
            case '7'://签证
                $VisaProducModule = new VisaProducModule();
                $VisaInfo = $VisaProducModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='签证';
                $Collection['ProductName'] = $VisaInfo['Title'];
                $Collection['ImageUrl'] =  ImageURLP2.$VisaInfo['Image'];
                $Collection['Url'] =  WEB_VISA_URL.'/visadetail/'.$VisaInfo['VisaID'].'.html';
                break;
            case '8'://高中院校
                $StudyHighSchoolModule = new StudyHighSchoolModule();
                $StudyHighSchool = $StudyHighSchoolModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='学校';
                $Collection['ProductName'] = $StudyHighSchool['HighSchoolName'];
                $Collection['ImageUrl'] =  $StudyHighSchool['Icon'];
                $Collection['Url'] =  WEB_STUDY_URL.'/highschool/'.$StudyHighSchool['HighSchoolID'].'.html';
                break;
            case '9'://大学院校
                $StudyCollegeModule = new StudyCollegeModule();
                $StudyCollege = $StudyCollegeModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='学校';
                $Collection['ProductName'] = $StudyCollege['CollegeName'];
                $Collection['ImageUrl'] =  $StudyCollege['LogoUrl'];
                $Collection['Url'] =  WEB_STUDY_URL.'/college/'.$StudyCollege['CollegeID'].'.html';
                break;
            case '10'://旅游资讯
                $TblTourModule = new TblTourModule();
                $TblTour = $TblTourModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='资讯';
                $Collection['ProductName'] = $TblTour['Title'];
                $Collection['ImageUrl'] =  LImageURL.$TblTour['Image'];
                $Collection['Url'] =  WEB_MAIN_URL.'/tour/'.$TblTour['TourID'].'.html';
                break;
            case '11'://留学资讯
                $TblStudyAbroadModule = new TblStudyAbroadModule();
                $TblStudyAbroad = $TblStudyAbroadModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='资讯';
                $Collection['ProductName'] = $TblStudyAbroad['Title'];
                $Collection['ImageUrl'] =  LImageURL.$TblStudyAbroad['Image'];
                $Collection['Url'] =  WEB_MAIN_URL.'/study/'.$TblStudyAbroad['StudyID'].'.html';
                break;
            case '12'://移民资讯
                $TblImmigrationModule = new TblImmigrationModule();
                $TblImmigration = $TblImmigrationModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='资讯';
                $Collection['ProductName'] = $TblImmigration['Title'];
                $Collection['ImageUrl'] =  LImageURL.$TblImmigration['Image'];
                $Collection['Url'] =  WEB_MAIN_URL.'/immigrant/'.$TblImmigration['ImmigrationID'].'.html';
                break;
            case '13'://游记资讯
                $TblTravelsModule = new TblTravelsModule();
                $TblTravels = $TblTravelsModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='资讯';
                $Collection['ProductName'] = $TblTravels['Title'];
                $Collection['ImageUrl'] =  LImageURL.$TblTravels['Image'];
                $Collection['Url'] =  WEB_MAIN_URL.'/travels/'.$TblTravels['TravelsID'].'.html';
                break;
            case '14':// 门票
                $TourProductModule = new TourProductModule();
                $TourProductImageModule = new TourProductImageModule();
                $TourProduct = $TourProductModule->GetInfoByKeyID($RelevanceID);
                $TourProductImage = $TourProductImageModule->GetInfoByTourProductID($RelevanceID);
                $Collection['Type'] ='门票';
                $Collection['ProductName'] = $TourProduct['ProductName'];
                $Collection['ImageUrl'] =  ImageURLP2.$TourProductImage['ImageUrl'];
                $Collection['Url'] =  WEB_TOUR_URL.'/play/'.$TourProduct['TourProductID'].'.html';
                break;
            default:
                break;
        }
        return $Collection;
    }
    
     /**
     * @desc  删除收藏记录
     */
    private function DelCollection()
    {
        $this->NeedLogin();
        $CollectionModule = new MemberCollectionModule();
        $IDs = $_POST['IDS'];
        foreach ($IDs as $val) {
            $result = $CollectionModule->DeleteByWhere(' and CollectionID = '.$val.' and UserID = '.$_SESSION['UserID']);
        }
        $array = array('ResultCode' => '200', 'Message' => '删除成功');
        echo json_encode($array);
        exit;
    }
    
    //---------------------------------------常用信息编辑---------------------------------------------
    
    /**
     * 保存旅客信息
     */
    private function PassengerAdd(){
        $this->NeedLogin();
        if ($_POST){
            $Data =array();
            $Data ['UserID'] = $_SESSION['UserID'];
            $Data ['ZhName'] = $_POST ['ZhName'];
            //$Data ['ZhXinPin'] = trim($_POST ['ZhXinPin']);
            //$Data ['ZhMingPin'] = trim($_POST ['ZhMingPin']);
            $Data ['Sex'] = $_POST['Sex'];
            $Data ['BirthDay'] = $_POST ['BirthDay'];
            $Data ['Mobile'] = $_POST ['Mobile'];
            //$Data ['Mail'] = $_POST ['Mail'];
            $Data ['IdCard'] = $_POST ['IdCard'];
            $Data ['CardEndDate'] = $_POST ['CardEndDate'];
            $Data ['CardType'] = $_POST ['CardType'];
            $Data ['Nationality'] = $_POST ['Nationality'];
            $Data ['IsDefault'] = intval($_POST['IsDefault']);
            $MemberPassengerModule = new MemberPassengerModule();
            if($Data['IsDefault']==1){
                $MemberPassengerModule->UpdateInfoByWhere(array('IsDefault'=>0),' UserID ='.$_SESSION['UserID']); 
            }
            $PassengerID = intval($_POST ['PassengerID']);
            if ($PassengerID>0){
                $UpdateInfo = $MemberPassengerModule->UpdateInfoByWhere($Data,' PassengerID = '.$PassengerID.' and UserID = '.$_SESSION['UserID']);
              if ($UpdateInfo!==false){
                  $Result = array('ResultCode' => '200', 'Message' => '修改成功','Url' => WEB_MUSER_URL.'/musertour/commoninfo/');
              }
              else{
                  $Result = array('ResultCode' => '100', 'Message' => '未操作修改');
              }
            }else{
                $InsertInfo = $MemberPassengerModule->InsertInfo($Data);
                if ($InsertInfo){
                    $Result = array('ResultCode' => '200', 'Message' => '新增成功','Url' => WEB_MUSER_URL.'/musertour/commoninfo/');
                }
                else{
                    $Result = array('ResultCode' => '100', 'Message' => '新增失败');
                }
            }
        }
        else{
            $Result = array('ResultCode' => '102', 'Message' => '新增失败');
        }
        echo json_encode($Result);
        exit;
    }
    
    /**
     * 设置默认旅客
     */
    private function PassengerSetDef(){
        $this->NeedLogin();
       if ($_POST['ID']){
           $ID = intval($_POST['ID']);
           $MemberPassengerModule = new MemberPassengerModule();
           $MemberPassengerModule->UpdateInfoByWhere(array('IsDefault'=>0),' UserID ='.$_SESSION['UserID']);
           $UpdateInfo = $MemberPassengerModule->UpdateInfoByKeyID(array('IsDefault'=>1),$ID);
           if ($UpdateInfo){
               $Result = array('ResultCode' => '200', 'Message' => '设置成功');
           }else{
               $Result = array('ResultCode' => '100', 'Message' => '设置失败');
           }
           echo json_encode($Result);
           exit;
       }
    }
    
    /**
     *  删除旅客信息
     */
    private function DelPassenger(){
        $this->NeedLogin();
        $UserID=$_SESSION['UserID'];
        $MemberPassengerModule = new MemberPassengerModule();
        if (isset($_POST['ID'])) {
            $PassengerID = intval($_POST['ID']);
            $DeletePassenger = $MemberPassengerModule->DeleteByWhere(" and UserID=$UserID and PassengerID=$PassengerID");
            if ($DeletePassenger){
                $Result = array('ResultCode' => '200', 'Message' => '删除成功');
            }else{
                $Result = array('ResultCode' => '100', 'Message' => '删除失败');
            }
            echo json_encode($Result);
            exit;
        }
    }
    
    /**
     * @desc 新增修改收货地址
     */
    private function AddressAdd()
    {
        $this->NeedLogin();
        $ID = intval($_POST['ID']);
        $Data['UserID'] = $_SESSION['UserID'];
        $Data['Recipients'] = trim($_POST['Recipients']);
        $Data['Address'] = trim($_POST['Address']);
        $Data['Mobile'] = trim($_POST['Mobile']);
        $Data['Postcode'] = trim($_POST['Postcode']);
        $Data['Province'] = trim($_POST['Province']);
        $Data['City'] = trim($_POST['City']);
        $Data['Area'] = trim($_POST['Area']);
        $Data['IsDefault']=intval($_POST['IsDefault']);
        $ShippingAddressModule = new MemberShippingAddressModule();
        if($Data['IsDefault']==1){
            $ShippingAddressModule->UpdateInfoByWhere(array('IsDefault'=>0),' UserID ='.$_SESSION['UserID']);
        }
        if ($ID>0) {
            $Result = $ShippingAddressModule->UpdateInfoByWhere($Data,' ShippingAddressID = '.$ID.' and UserID = '.$_SESSION['UserID']);
            if ($Result){
                $res = array('ResultCode' => '200', 'Message' => '修改成功','Url' => WEB_MEMBER_URL.'/member/addressadd/?ID='.$ID);
            }else{
                $res = array('ResultCode' => '100', 'Message' => '未操作修改');
            }
        } else {
            $Result = $ShippingAddressModule->InsertInfo($Data);
            if ($Result) {
                $res = array('ResultCode' => '200', 'Message' => '新增成功','Url' => WEB_MEMBER_URL.'/member/addresslist/');
            } else {
                $res = array('ResultCode' => '101', 'Message' => '新增失败');
            }
        }
        echo json_encode($res);
        exit;
    }
    /**
     * @desc 设置默认收货地址
     */
    private function AddressSetDef(){
        $this->NeedLogin();
        if ($_POST['ID']){
            $ID = intval($_POST['ID']);
            $ShippingAddressModule = new MemberShippingAddressModule();
            $ShippingAddressModule->UpdateInfoByWhere(array('IsDefault'=>0),' UserID ='.$_SESSION['UserID']);
            $UpdateInfo = $ShippingAddressModule->UpdateInfoByKeyID(array('IsDefault'=>1),$ID);
            if ($UpdateInfo){
                $Result = array('ResultCode' => '200', 'Message' => '设置成功');
            }else{
                $Result = array('ResultCode' => '100', 'Message' => '设置失败');
            }
            echo json_encode($Result);
            exit;
        }
    }

    /**
     * @desc 删除收货地址
     */
    private function DelAddress()
    {
        $this->NeedLogin();
        $ShippingAddressModule = new MemberShippingAddressModule();
        if (isset($_POST['ID'])) {
            $ShippingAddressID = intval($_POST['ID']);
            $result = $ShippingAddressModule->DeleteByWhere(' and ShippingAddressID = '.$ShippingAddressID. ' and UserID = '.$_SESSION['UserID']);
        }
        if ($result) {
            $res = array('ResultCode' => '200', 'Message' => '删除成功');
        } else {
            $res = array('ResultCode' => '100', 'Message' => '删除失败');
        }
        echo json_encode($res);
        exit;
    }

    /**
     * @desc  获取用户消息
     */
    public function GetMemberMessage(){
        unset($_SESSION['IsHaveMessage']);
        $MemberMessageInfoModule = new MemberMessageInfoModule();
        $MemberMessageSendModule = new MemberMessageSendModule();

        $MysqlWhere = ' and Status in (1,2) and UserID = '.$_SESSION['UserID'].' order by SendID desc';
        $Page = intval($_POST['Page']) < 1 ? 1 : intval($_POST['Page']); // 页码 可能是空
        $PageSize = 6;
        $Rscount = $MemberMessageSendModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount']){
                $Page = $Data ['PageCount'];
            }
            $Data ['Data'] = $MemberMessageSendModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            $Result = array();
            foreach ($Data ['Data'] as $key=>$val){
                $MessageInfo = $MemberMessageInfoModule->GetInfoByKeyID($val['MessageID']);
                $Result [$key]['ID'] = $val['SendID'];
                $Result [$key]['Name'] = $MessageInfo['Title'];
                $Result [$key]['Date'] = date('Y-m-d H:i:s',$MessageInfo['SendTime']);
                $Result [$key]['Message'] = $MessageInfo['Content'];
                $Result [$key]['Type'] = $val['Status'];
            }
        }
        if($Data){
            $json_result = array('ResultCode'=>200,'Data'=>$Result,'RecordCount'=>$Data ['RecordCount']);
        }
        else{
            $json_result = array('ResultCode'=>101);
        }
        echo json_encode($json_result);
    }

    /**
     * @desc 处理站内信，未读变已读
     */
    public function DisposeMemberMessage(){
        $MessageSendModule = new MemberMessageSendModule();
        $ID = $_POST['ID'];
        $Result = $MessageSendModule->UpdateInfoByKeyID(array('Status'=>2),$ID);
        if($Result){
            $json_result = array('ResultCode'=>200,'Message'=>'操作成功');
        }
        else{
            $json_result = array('ResultCode'=>101);
        }
        echo json_encode($json_result);
    }

    /**
     * @desc 批量删除站内信
     */
    public function DelMemberMessage(){
        $MessageSendModule = new MemberMessageSendModule();
        $IDs = $_POST['IDS'];
        foreach($IDs as $val){
            $MessageSendModule->UpdateInfoByKeyID(array('Status'=>3),$val);
        }
        $json_result = array('ResultCode'=>200,'Message'=>'操作成功');
        echo json_encode($json_result);
    }

}