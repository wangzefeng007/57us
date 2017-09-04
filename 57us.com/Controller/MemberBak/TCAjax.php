<?php
class TCAjax {
    public function __construct() {
    }

    public function Index() {
        $Intention = trim ( $_GET ['Intention'] );
        if ($Intention==''){
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            echo 'jsonpCallback('.json_encode($json_result).')';
            exit;
        }
        $this->$Intention ();
    }

    //----------------------------------  判断是否登录  --------------------------------//
    private function LoginStatus(){
        if(!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])){
            $json_result=array('ResultCode'=>0,'Message'=>'请先登录');
        }else{
            $json_result=array('ResultCode'=>1,'Message'=>'已登录');
        }
        echo 'LoginStatus('.json_encode($json_result).')';
    }

    //弹窗会员账号密码登录
    private function TCLogin(){
        $Account = ($_GET['User']);
        $User = new MemberUserModule();
        //判断账号是否注册
        $UserInfo = $User->AccountExists($Account);
        if (empty($UserInfo)){
            $json_result = array(
                'Intention' => 'TCLogin',
                'ResultCode' => 106,
                'Message' => '未注册的账号！',
                'Url' => ''
            );
        }else{
            $PassWord = md5($_GET['Pass']);
            $UserID = $User->CheckUser($Account, $PassWord);
            if ($UserID) {
                $XpirationDate = time() + 3600 * 24;
                if ($_GET['AutoLogin'] == 1) {
                    setcookie("UserID", $UserID, $XpirationDate, "/", WEB_HOST_URL);
                    setcookie("Account", $Account, $XpirationDate, "/", WEB_HOST_URL);
                }
                // 同步SESSIONID
                setcookie("session_id", session_id(), $XpirationDate, "/", WEB_HOST_URL);
                $_SESSION['UserID'] = $UserID;
                $_SESSION['Account'] = $Account;
                setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
                $UserInfo = new MemberUserInfoModule();
                $Data['LastLogin'] = date('Y-m-d H:i:s', time());
                $UserInfo->UpdateData($Data, $UserID);
                $json_result = array(
                    'Intention' => 'TCLogin',
                    'ResultCode' => 200,
                    'Message' => '登录成功',
                    'Url' => WEB_MEMBER_URL
                );
            } else {
                $json_result = array(
                    'Intention' => 'TCLogin',
                    'ResultCode' => 100,
                    'Message' => '您输入的密码错误，请重新输入!'
                );
            }
        }
        echo 'jsonpCallback('.json_encode($json_result).')';
        exit;
    }

    // 手机号码直接登录（获取短信验证码）
    private function MpLogin(){
        $ImageCode = strtolower($_GET['Code']);
        if ($ImageCode == $_SESSION['authnum_session']) {
            $Data['Account'] = ($_GET['User']);
            $Data['VerifyCode'] = mt_rand(100000, 999999);
            $Data['XpirationDate'] = Time() + 60 * 30;
            if (is_numeric($Data['Account'])) {
                $Data['Type'] = 0;
            } elseif (strpos($Data['Account'], '@')) {
                $Data['Type'] = 1;
            }
            $result = ToolService::SendSMSNotice($Data['Account'],'亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
            if ($result) {
                // 成功添加到本地表
                $Authentication = new MemberAuthenticationModule();
                $Authentication->InsertUser($Data);
                $json_result = array(
                    'Intention' => 'MpLogin',
                    'ResultCode' => 200,
                    'Message' => '发送成功'
                );
            } else {
                $json_result = array(
                    'Intention' => 'MpLogin',
                    'ResultCode' => 100,
                    'Message' => '验证码发送失败,请重试'
                );
            }
        } else {
            $json_result = array(
                'Intention' => 'MpLogin',
                'ResultCode' => 101,
                'Message' => '验证码错误'
            );
        }
        echo 'jsonpCallback('.json_encode($json_result).')';
        exit();
    }

    // 手机号码直接登录（短信验证）
    private function MpLoginVerify(){
        $VerifyCode = intval(trim($_GET['Code']));
        $Account = trim($_GET['User']);
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
                    'Intention' => 'MpLoginVerify',
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
                    $Url = WEB_MEMBER_URL . '?pass=null';
                } else {
                    $UserID = $UserInfo['UserID'];
                    if ($UserInfo['PassWord'] == '') {
                        $Url = WEB_MEMBER_URL . '?pass=null';
                    } else {
                        $Url = WEB_MEMBER_URL;
                    }
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
                    'Intention' => 'MpLoginVerify',
                    'ResultCode' => 200,
                    'Message' => '登录成功',
                    'Url' => $Url
                );
            }
        } else {
            $json_result = array(
                'Intention' => 'MpLoginVerify',
                'ResultCode' => 102,
                'Message' => '短信验证码错误'
            );
        }
        echo 'jsonpCallback('.json_encode($json_result).')';
        exit();
    }
}