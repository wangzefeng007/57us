<?php

/**
 * @desc 会员中心登陆注册，忘记密码
 * Class AjaxLogin
 */
class AjaxLogin
{
    public function Index()
    {
        $Intention = trim($_POST['Intention'])?trim($_POST['Intention']):trim($_GET['Intention']);
        if ($Intention == '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            if($_GET['Intention']){
                echo 'jsonpCallback('.json_encode($json_result).')';
            }
            elseif($_POST['Intention']){
                echo json_encode($json_result);
            }
            exit;
        }
        $this->$Intention ();
    }

    //=================================================弹窗，登陆 ============================================//
    /**
     * @desc 弹窗会员账号密码登录
     */
    private function Tc_Login(){
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
            $PassWord = md5($_GET['Password']);
            $UserID = $User->CheckUser($Account, $PassWord);
            if ($UserID) {
                MemberService::SendSystemMessage($UserID);
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
                    'Intention' => 'Tc_Login',
                    'ResultCode' => 200,
                    'Message' => '登录成功',
                    'Url' => WEB_MEMBER_URL
                );
            } else {
                $json_result = array(
                    'Intention' => 'Tc_Login',
                    'ResultCode' => 100,
                    'Message' => '您输入的密码错误，请重新输入!'
                );
            }
        }
        echo 'Tc_Login('.json_encode($json_result).')';
        exit;
    }

    /**
     * @desc  手机号码直接登录（获取短信验证码）
     */
    private function Tc_Sms_Code(){
        $ImageCode = strtolower($_GET['ImgCode']);
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
                    'Intention' => 'Tc_Sms_Code',
                    'ResultCode' => 200,
                    'Message' => '发送成功'
                );
            } else {
                $json_result = array(
                    'Intention' => 'Tc_Sms_Code',
                    'ResultCode' => 100,
                    'Message' => '验证码发送失败,请重试'
                );
            }
        } else {
            $json_result = array(
                'Intention' => 'Tc_Sms_Code',
                'ResultCode' => 101,
                'Message' => '验证码错误'
            );
        }
        echo 'Tc_Sms_Code('.json_encode($json_result).')';
        exit();
    }

    /**
     * @desc 手机号码直接登录（短信验证）
     */
    private function Tc_Mobile_Login(){
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
                    'Intention' => 'Tc_Mobile_Login',
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
                    'Intention' => 'Tc_Mobile_Login',
                    'ResultCode' => 200,
                    'Message' => '登录成功',
                    'Url' => $Url
                );
            }
        } else {
            $json_result = array(
                'Intention' => 'Tc_Mobile_Login',
                'ResultCode' => 102,
                'Message' => '短信验证码错误'
            );
        }
        echo 'Tc_Mobile_Login('.json_encode($json_result).')';
        exit();
    }
    //=================================================弹窗，登陆结束 ============================================//

    //=================================================PC注册登陆开始 ============================================//
    /**
     * @desc  PC端登陆
     */
    private function Login()
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
                $json_result = array('ResultCode' => 106,'Message' => '未注册的账号！', 'Url' => '');
                echo json_encode($json_result);
                exit;
            }
            $PassWord = md5($_POST['Pass']);
            $UserID = $User->CheckUser($Account, $PassWord);
            //发送系统消息
            if ($UserID) {
                MemberService::SendSystemMessage($UserID);
                $XpirationDate = time() + 3600 * 24;
                if ($_POST['AutoLogin'] == 1) {
                    setcookie("UserID", $UserID, $XpirationDate, "/", WEB_HOST_URL);
                    setcookie("Account", $Account, $XpirationDate, "/", WEB_HOST_URL);
                }
                //同步SESSIONID
                setcookie("session_id", session_id(), $XpirationDate, "/", WEB_HOST_URL);
                $_SESSION['UserID'] = $UserID;
                $_SESSION['Account'] = $Account;
                setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
                $UserInfoModule = new MemberUserInfoModule();
                $Data['LastLogin'] = date('Y-m-d H:i:s', time());
                $UserInfoModule->UpdateData($Data, $UserID);
                $UserInfo=$UserInfoModule->GetInfoByUserID($UserID);
                $ComeFrom=intval($_POST['ComeFrom']);
                if($ComeFrom){
                    if($UserInfo['Identity']==2){
                        $Url = WEB_STUDY_URL.'/consultantmanage/mycenter/';
                    } elseif($UserInfo['Identity']==3){
                        $Url=WEB_STUDY_URL.'/teachermanage/mycenter/';
                    }else{
                        $Url=WEB_MEMBER_URL;
                    }
                }else{
                    $Url=WEB_MEMBER_URL;
                }
                $json_result = array('ResultCode' => 200, 'Message' => '登录成功', 'Url' => $Url);
            }
            else {
                // 设置密码超过三次
                $PasswordErrTimes = intval($_COOKIE['PasswordErrTimes']) + 1;
                setcookie("PasswordErrTimes", $PasswordErrTimes, time() + 3600, "/", WEB_HOST_URL);
                if ($PasswordErrTimes > 2) {
                    $json_result = array('ResultCode' => 105, 'Message' => '您输入的密码错误，请重新输入!');
                } else {
                    $json_result = array('ResultCode' => 100, 'Message' => '您输入的密码错误，请重新输入!');
                }
            }
        } else {
            $json_result = array('ResultCode' => 101, 'Message' => '验证码错误');
        }
        echo json_encode($json_result);
        exit;
    }

    /**
     * @desc  发送 注册/找回密码 验证码
     */
    private function RegisterSendCode(){
        $ImageCode = $_POST['ImageCode'];
        $Account = $_POST['User'];
        $UserModule = new MemberUserModule();
        if ($UserModule->AccountExists($Account)) {
            $json_result = array('ResultCode' => 106, 'Message' => '该帐号已被注册过了,请更换帐号注册');
        }
        else{
            if($_SESSION['authnum_session'] == $ImageCode){
                if (is_numeric($Account)) { //手机
                    $json_result = MemberService::SendMobileVerificationCode($Account);
                } elseif (strpos($Account, '@')) { //邮箱
                    $json_result = MemberService::SendMailVerificationCode($Account);
                }
            }
            else{
                $json_result = array('ResultCode' => 101, 'Message' => '验证码错误');
            }
        }
        echo json_encode($json_result);exit;
    }

    /**
     * @desc 注册验证/找回密码 手机/邮箱 验证码
     */
    private function RegisterVerifyCode(){
        $json_result = MemberService::VerifySendCode($_POST['Code'],$_POST['User']);
        if($json_result['ResultCode'] == 200 ){
            $_SESSION['temp_account'] = $_POST['User'];
        }
        echo json_encode($json_result);exit;
    }

    /**
     * @desc  注册
     */
    private function Register()
    {
        if (isset($_SESSION['temp_account']) && !empty($_SESSION['temp_account'])) {
            $UserModule = new MemberUserModule();
            if ($UserModule->AccountExists($_SESSION['temp_account'])) {
                $result = array('ResultCode' => 101, 'Message' => '该帐号已被注册过了,请更换号码注册', 'Url' => '');
            } elseif (trim($_POST['PassWord']) != trim($_POST['PassWordConfirm'])) {
                $result = array('ResultCode' => 104, 'Message' => '两次密码不一致,请重试！', 'Url' => '');
            } else {
                if (is_numeric($_SESSION['temp_account'])) {
                    $Data['Mobile'] = $_SESSION['temp_account'];
                } elseif (strpos($_SESSION['temp_account'], '@')) {
                    $Data['E-Mail'] = $_SESSION['temp_account'];
                }
                $Data['AddTime'] = Time();
                $Data['State'] = 1;
                $Data['PassWord'] = md5(trim($_POST['PassWord']));
                //开始事务
                global $DB;
                $DB->query("BEGIN");
                //添加会员
                $UserID = $UserModule->InsertUser($Data);
                if ($UserID) {
                    //添加会员资金
                    $UserBankModule = new MemberUserBankModule();
                    $BankData = array('UserID'=>$UserID,'TotalBalance'=>0,'FrozenBalance'=>0,'FreeBalance'=>0);
                    $InsertBankResult = $UserBankModule->InsertInfo($BankData);
                    if($InsertBankResult){
                        //添加会员基础信息
                        $UserInfoModule = new MemberUserInfoModule();
                        $UserInfoData['UserID'] = $UserID;
                        $UserInfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                        $UserInfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
                        $UserInfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                        $UserInfoData['Sex'] = 1;
                        $UserInfoData['Avatar']='/img/man3.0.png';
                        $UserInfoData['Identity'] = $_POST['Identity'];
                        $UserInfoData['IdentityState'] = 0;
                        $UserInfoData['IP'] = GetIP();
                        $InsertUserInfoResult = $UserInfoModule->InsertInfo($UserInfoData);
                        if($InsertUserInfoResult){
                            if($_POST['Identity'] == 1){  //普通会员
                                $DB->query("COMMIT");//执行事务
                                $result = array('ResultCode' => 200, 'Message' => '注册成功', 'Url' => WEB_MEMBER_URL);
                            }
                            elseif($_POST['Identity'] == 2){ //顾问身份
                                $StudyConsultantInfoModule = new StudyConsultantInfoModule();
                                $ConsultantInfoData = array('UserID'=>$UserID,'Grade'=>1,'TutorialObject'=>0);
                                $ConsultantInfoResult = $StudyConsultantInfoModule->InsertInfo($ConsultantInfoData);
                                if($ConsultantInfoResult){
                                    $DB->query("COMMIT");//执行事务
                                    $result = array('ResultCode' => 200, 'Message' => '注册成功', 'Url' => WEB_STUDY_URL.'/consultantmanage/mycenter/');
                                }
                                else{
                                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                                    $result = array('ResultCode' => 104, 'Message' =>'发生异常,注册失败','Remark'=> '顾问详情添加失败');
                                }
                            }
                            elseif($_POST['Identity'] == 3){ //教师身份
                                $StudyTeacherInfoModule = new StudyTeacherInfoModule();
                                $StudyTeacherInfoData = array('UserID'=>$UserID,'Grade'=>1,'TutorialObject'=>0);
                                $StudyTeacherInfoResult = $StudyTeacherInfoModule->InsertInfo($StudyTeacherInfoData);
                                if($StudyTeacherInfoResult){
                                    $DB->query("COMMIT");//执行事务
                                    $result = array('ResultCode' => 200, 'Message' => '注册成功', 'Url' => WEB_STUDY_URL.'/teachermanage/mycenter/');
                                }
                                else{
                                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                                    $result = array('ResultCode' => 105, 'Message' =>'发生异常,注册失败','Remark'=> '教师详情添加失败');
                                }
                            }
                            else{
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                $result = array('ResultCode' => 106, 'Message' =>'发生异常,注册失败','Remark'=> '身份传输错误');
                            }
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $result = array('ResultCode' => 103, 'Message' =>'发生异常,注册失败','Remark'=> '添加会员基础信息失败');
                        }
                    }
                    else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $result = array('ResultCode' => 101, 'Message' =>'发生异常,注册失败','Remark'=> '添加会员资金失败');
                    }
                } else {
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $result = array('ResultCode' => 100, 'Message' => '发生异常,注册失败','Remark'=> '添加会员失败');
                }
            }
        } else {
            $result = array('ResultCode' => 102, 'Message' => '异常的请求,请重新注册','Url' => '');
        }
        if($result['ResultCode'] == 200){
            //同步SESSIONID
            setcookie("session_id", session_id(), time() + 3600 * 24, "/", WEB_HOST_URL);
            $_SESSION['UserID'] = $UserID;
            $_SESSION['NiceName'] = $UserInfoData['NickName'];
            $_SESSION['Account'] = $_SESSION['temp_account'];
            setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
            unset($_SESSION['temp_account']);
        }
        echo json_encode($result);
        exit();
    }

    /**
     * @desc  注册页面找回密码发送验证码
     */
    private function RetrievePasswordSendCode(){
        $ImageCode = $_POST['ImageCode'];
        $Account = $_POST['User'];
        $UserModule = new MemberUserModule();
        if (!$UserModule->AccountExists($Account)) {
            $json_result = array('ResultCode' => 106, 'Message' => '该帐号未注册,请先注册帐号');
        }
        else{
            if($_SESSION['authnum_session'] == $ImageCode){
                if (is_numeric($Account)) { //手机
                    $json_result = MemberService::SendMobileVerificationCode($Account);
                } elseif (strpos($Account, '@')) { //邮箱
                    $json_result = MemberService::SendMailVerificationCode($Account);
                }
            }
            else{
                $json_result = array('ResultCode' => 101, 'Message' => '图片验证码错误');
            }
        }
        echo json_encode($json_result);exit;
    }

    /**
     * @desc  注册页忘记密码
     */
    private function RetrievePassword(){
        $TempAccount = $_SESSION['temp_account'];
        $PassWord = md5($_POST ['PassWord']);
        $PassWordConfirm = md5($_POST ['PassWordConfirm']);
        if($PassWord == $PassWordConfirm){
            $MemberUserModule = new MemberUserModule ();
            if (is_numeric($TempAccount)) {
                $UserInfo = $MemberUserModule->GetInfoByWhere(' and Mobile ='.$TempAccount);
            } elseif (strpos($TempAccount, '@')) {
                $UserInfo = $MemberUserModule->GetInfoByWhere(' and E-Mail ='.$TempAccount);
            }
            $Result = $MemberUserModule->UpdateUser(array('PassWord'=>$PassWord), $UserInfo['UserID']);
            if($Result || $Result === 0){
                $json_result = array('ResultCode' => 200, 'Message' => '更新成功', 'Url' => '/member/index/');
            }
            else{
                $json_result = array('ResultCode' => 103, 'Message' => '更新失败');
            }
        }
        else{
            $json_result = array('ResultCode' => 102, 'Message' => '两次密码输入不一致');
        }
        echo json_encode($json_result);
    }


    /**
     * @desc  手机号码直接登录（获取短信验证码）
     */
    private function MpLogin()
    {
        $ImageCode = strtolower($_POST['ImageCode']);
        if ($ImageCode == $_SESSION['authnum_session']) {
            $Account = ($_POST['User']);
            if (is_numeric($Account)) { //手机
                $json_result = MemberService::SendMobileVerificationCode($Account);
            } elseif (strpos($Account, '@')) { //邮箱
                $json_result = MemberService::SendMailVerificationCode($Account);
            }
        }
        echo json_encode($json_result);exit;
    }


    /**
     * @desc 手机号码直接登录（短信验证）
     */
    private function MpLoginVerify()
    {
        $VerifyCode = intval(trim($_POST['Code']));
        $Account = trim($_POST['User']);
        //验证 手机/邮箱 验证码
        $json_result = MemberService::VerifySendCode($VerifyCode,$Account);
        if($json_result['ResultCode'] == 200 ){
            $MemberUserModule = new MemberUserModule ();
            $UserInfo = $MemberUserModule->GetInfoByWhere(' and Mobile ='.$Account);
            if (empty($UserInfo)) {
                $InsertUserInfo['Mobile'] = $Account;
                $InsertUserInfo['AddTime'] = time();
                $InsertUserInfo['State'] = 1;
                //开始事务
                global $DB;
                $DB->query("BEGIN");
                $UserID = $MemberUserModule->InsertInfo($InsertUserInfo);
                if($UserID){
                    $UserInfoModule = new MemberUserInfoModule();
                    $InfoData['UserID'] = $UserID;
                    $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                    $InfoData['BirthDay'] = date("Y-m-d H:i:s");
                    $InfoData['LastLogin'] = date('Y-m-d H:i:s');
                    $InfoData['Sex'] = 1;
                    $InfoData['Avatar']='/img/man3.0.png';
                    $InsertUserInfoResult = $UserInfoModule->InsertData($InfoData);
                    if($InsertUserInfoResult){
                        $BankModule = new MemberUserBankModule();
                        $InsertBankResult = $BankModule->InsertInfo(array('UserID'=>$UserID));
                        if($InsertBankResult){
                            $json_result = array('ResultCode' => 200 , 'Message' => '登陆成功','Url'=> WEB_MEMBER_URL . '?pass=null');
                        }
                        else{
                            $json_result = array('ResultCode' => 103 , 'Message' => '登录失败','Remark'=>'添加会员资金表失败');
                        }
                    }
                    else{
                        $json_result = array('ResultCode' => 102 , 'Message' => '登录失败','Remark'=>'添加会员详细信息失败');
                    }
                }
                else{
                    $json_result = array('ResultCode' => 101 , 'Message' => '登录失败','Remark'=>'添加会员失败');
                }
            } else {
                $UserID = $UserInfo['UserID'];
                MemberService::SendSystemMessage($UserID);
                if ($UserInfo['PassWord'] == '') {
                    $json_result = array('ResultCode' => 200 , 'Message' => '登陆成功','Url'=> WEB_MEMBER_URL . '?pass=null');
                } else {
                    $json_result = array('ResultCode' => 200 , 'Message' => '登陆成功','Url'=> WEB_MEMBER_URL);
                }
            }
            if($json_result['ResultCode'] == 200){
                $XpirationDate = time() + 3600 * 24;
                // 同步SESSIONID
                setcookie("session_id", session_id(), $XpirationDate, "/", WEB_HOST_URL);
                $_SESSION['UserID'] = $UserID;
                $_SESSION['Account'] = $Account;
                setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
                $UserInfo = new MemberUserInfoModule();
                $Data['LastLogin'] = date('Y-m-d H:i:s', time());
                $UserInfo->UpdateData($Data, $UserID);
            }
            echo json_encode($json_result);exit();
        }
        else{
            echo json_encode($json_result);exit();
        }
    }

    /**
     * @desc 快捷登录设置密码
     */
    /*private function MpLoginSetPass()
    {
        $Account = $_SESSION['UserID'];
        $PassWord = trim($_POST['PassWord']);
        $PassWordConfirm = trim($_POST['PassWordConfirm']);
        $User = new MemberUserModule();
        $UserInfo = $User->GetUserIDbyMobile($_SESSION['Account']);
        if ($PassWord != $PassWordConfirm) {
            $json_result = array('ResultCode' => 104, 'Message' => '两次密码不一致!');
        } elseif (strlen($PassWord) < 6) {
            $json_result = array('ResultCode' => 101, 'Message' => '密码格式错误!');
        } elseif (empty($UserInfo)) {
            $json_result = array('ResultCode' => 103, 'Message' => '您的账户有误!',);
        } elseif ($UserInfo['PassWord'] != '') {
            $json_result = array('ResultCode' => 102, 'Message' => '请求异常!');
        } else {
            $Data['PassWord'] = md5($PassWord);
            $User->UpdateInfoByKeyID($Data, $UserInfo['UserID']);
            $json_result = array('ResultCode' => 200, 'Message' => '设置密码成功', 'Url' => WEB_MEMBER_URL);
        }
        echo json_encode($json_result);exit;
    }*/


    //=================================================PC注册登陆结束 ============================================//

    //================================================= 身份更新提交 ============================================//
    /**
     * @desc  身份更新提交
     */
    private function TransitionIdentity(){
        $Data = array(
            'RealName'=>$_POST['ZhName'],
            'Mobile'=>$_POST['Mobile'],
            'ToUpdate'=>$_POST['Identity'],
            'AddTime'=>time()
        );
        $UpdateIdentityModule = new StudyUpdateIdentityModule();
        $Result = $UpdateIdentityModule->InsertInfo($Data);
        if($Result){
            $json_result = array('ResultCode' => 200, 'Message' => '提交成功','Url'=> WEB_STUDY_URL);
        }
        else{
            $json_result = array('ResultCode' => 101, 'Message' => '提交失败');
        }
        echo json_encode($json_result);exit;
    }
}