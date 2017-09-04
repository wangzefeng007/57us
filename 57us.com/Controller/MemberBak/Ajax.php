<?php

class Ajax
{
    public function __construct()
    {
        include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberUserModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberUserInfoModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberAuthenticationModule.php';
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
            echo $json_result;
            exit;
        }
        $this->$Intention ();
    }

    //检测账号是否可用
    private function CheckUser()
    {
        $Account = trim($_POST ['User']);
        if ($Account) {
            $User = new MemberUserModule ();
            $UserInfo = $User->AccountExists($Account);
            if ($UserInfo) {
                $result = array('ResultCode' => 100, 'Message' => '账号已存在');
            } else {
                $result = array('ResultCode' => 200, 'Message' => '账号可用');
            }
        } else {
            $result = array('ResultCode' => 100, 'Message' => '账号已存在');
        }
        echo json_encode($result);
    }


    //发送注册验证码
    private function SignVerifyCode()
    {
        $ImageCode = strtolower(trim($_POST['ImageCode']));
        if ($ImageCode == $_SESSION['authnum_session']) {
            $Data['Account'] = trim($_POST['User']);
            $MemberUserModule = new MemberUserModule();
            if ($MemberUserModule->AccountExists($Data['Account'])) {
                $json_result = array(
                    'ResultCode' => 102,
                    'Message' => '发送失败，账号已存在'
                );
            } elseif (strlen($Data['Account']) != 11) {
                $json_result = array(
                    'ResultCode' => 103,
                    'Message' => '账号错误！'
                );
            } else {
                $Data['VerifyCode'] = mt_rand(100000, 999999);
                $Data['XpirationDate'] = Time() + 60 * 30;
                if (is_numeric($Data['Account'])) {
                    $Data['Type'] = 0;
                } elseif (strpos($Data['Account'], '@')) {
                    $Data['Type'] = 1;
                }
                $Authentication = new MemberAuthenticationModule();
                $result = $Authentication->InsertUser($Data);
                if ($result) {
                    if ($Data['Type'] == 0) {
                        $result = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您注册的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
                        if ($result) {
                            $json_result = array(
                                'ResultCode' => 200,
                                'Message' => '发送成功'
                            );
                        } else {
                            $json_result = array(
                                'ResultCode' => 100,
                                'Message' => '验证码发送失败,请重试'
                            );
                        }
                    } else {
                        $result = ToolService::SendEMailNotice($Data['Account'], "57美国网邮箱验证", "<span>【57美国网】 您的邮箱验证码为 " . $Data['VerifyCode'] . " 如有疑问请致电：400-018-5757 </span>");
                        if ($result) {
                            $json_result = array(
                                'ResultCode' => 200,
                                'Message' => '发送成功'
                            );
                        } else {
                            $json_result = array(
                                'ResultCode' => 100,
                                'Message' => '验证码发送失败,请重试'
                            );
                        }
                    }
                } else {
                    $json_result = array(
                        'ResultCode' => 100,
                        'Message' => '发送失败,系统异常'
                    );
                }
            }
        } else {
            $json_result = array(
                'ResultCode' => 101,
                'Message' => '发送失败,图形验证码错误'
            );
        }
        echo json_encode($json_result);
        exit();
    }

    //发送注册验证码
    private function SendVerifyCode()
    {
        $Data ['Account'] = trim($_POST ['User']);
        $MemberUserModule = new MemberUserModule();
        if ($MemberUserModule->AccountExists($Data ['Account'])) {
            $json_result = array('ResultCode' => 102, 'Message' => '发送失败，账号已存在');
        } else {
            $Data ['VerifyCode'] = mt_rand(100000, 999999);
            $Data ['XpirationDate'] = Time() + 60 * 30;
            if (is_numeric($Data ['Account'])) {
                $Data ['Type'] = 0;
            } elseif (strpos($Data ['Account'], '@')) {
                $Data ['Type'] = 1;
            }
            $Authentication = new MemberAuthenticationModule ();
            $ID = $Authentication->searchAccount($Data ['Account']);
            if ($ID) {
                $result = $Authentication->UpdateUser($Data, $ID);
            } else {
                $result = $Authentication->InsertUser($Data);
            }
            if ($result) {
                if ($Data ['Type'] == 0) {
                    $result = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
                    if ($result) {
                        $json_result = array('ResultCode' => 200, 'Message' => '发送成功');
                    } else {
                        $json_result = array('ResultCode' => 100, 'Message' => '验证码发送失败,请重试');
                    }
                } else {
                    $result = ToolService::SendEMailNotice($Data['Account'], "57美国网邮箱验证", "<span>【57美国网】 您的邮箱验证码为 " . $Data['VerifyCode'] . " 如有疑问请致电：400-018-5757 </span>");
                    if ($result) {
                        $json_result = array('ResultCode' => 200, 'Message' => '发送成功');
                    } else {
                        $json_result = array('ResultCode' => 100, 'Message' => '验证码发送失败,请重试');
                    }
                }
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '发送失败,系统异常');
            }
        }
        echo json_encode($json_result);
    }

    //验证短信
    private function RegisterVerify()
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
                $result = array(
                    'ResultCode' => 103,
                    'Message' => '短信验证码过期'
                );
            } else {
                $_SESSION['temp_account'] = $Account;
                $Authentication->DeleteUser($TempUserInfo['ID']);
                $result = array(
                    'ResultCode' => 200,
                    'Message' => '短信验证通过',
                );
            }
        } else {
            $result = array(
                'ResultCode' => 102,
                'Message' => '短信验证码错误'
            );
        }
        echo json_encode($result);
        exit();
    }

    //注册用户
    private function RegisterUser()
    {
        if (isset($_SESSION['temp_account']) && !empty($_SESSION['temp_account'])) {
            $User = new MemberUserModule();
            if ($User->AccountExists($_SESSION['temp_account'])) {
                $result = array(
                    'ResultCode' => 101,
                    'Message' => '该手机已被注册过了,请更换号码注册',
                    'Url' => ''
                );
            } elseif (trim($_POST['PassWord']) != trim($_POST['PassWordConfirm'])) {
                $result = array(
                    'ResultCode' => 104,
                    'Message' => '两次密码不一致,请重试！',
                    'Url' => ''
                );
            } else {
                if (is_numeric($_SESSION['temp_account'])) {
                    $Data['Mobile'] = $_SESSION['temp_account'];
                } elseif (strpos($_SESSION['temp_account'], '@')) {
                    $Data['E-Mail'] = $_SESSION['temp_account'];
                }
                $Data['AddTime'] = Time();
                $Data['State'] = 1;
                $Data['PassWord'] = md5(trim($_POST['PassWord']));
                $insert_result = $User->InsertUser($Data);
                if ($insert_result) {
                    $AccountInfo = $User->AccountExists($_SESSION['temp_account']);
                    $UserInfo = new MemberUserInfoModule();
                    $InfoData['UserID'] = $AccountInfo['UserID'];
                    $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                    $InfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
                    $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                    $InfoData['Sex'] = 1;
                    $InfoData['Avatar']='/img/man3.0.png';
                    $insert_result1 = $UserInfo->InsertData($InfoData);
                    // 同步SESSIONID
                    setcookie("session_id", session_id(), time() + 3600 * 24, "/", WEB_HOST_URL);
                    $_SESSION['UserID'] = $InfoData['UserID'];
                    $_SESSION['NiceName'] = $InfoData['NickName'];
                    $_SESSION['Account'] = $_SESSION['temp_account'];
                    setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
                    unset($_SESSION['temp_account']);
                    $result = array(
                        'ResultCode' => 200,
                        'Message' => '注册成功',
                        'Url' => WEB_MEMBER_URL
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
                'Url' => ''
            );
        }
        echo json_encode($result);
        exit();
    }

    //会员登录
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
            if ($UserID) {
                $XpirationDate = time() + 3600 * 24;
                if ($_POST['AutoLogin'] == 1) {
                    setcookie("UserID", $UserID, $XpirationDate, "/", WEB_HOST_URL);
                    setcookie("Account", $Account, $XpirationDate, "/", WEB_HOST_URL);
                }
                // 同步SESSIONID
                setcookie("session_id", session_id(), $XpirationDate, "/", WEB_HOST_URL);
                $_SESSION['UserID'] = $UserID;
                $_SESSION['Account'] = $Account;
                setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
                $UserInfoModule = new MemberUserInfoModule();
                $Data['LastLogin'] = date('Y-m-d H:i:s', time());
                $UserInfoModule->UpdateData($Data, $UserID);
                $UserInfo=$UserInfoModule->GetInfoByUserID($UserID);
                $FromType=intval($_POST['Type']);
                if($FromType){
                    if($UserInfo['Identity']==0){
                        $Url=WEB_STUDY_URL.'/identity/';
                    }elseif($UserInfo['Identity']==1){
                        $Url=WEB_STUDY_URL.'/studentmanage/';
                    }elseif($UserInfo['Identity']==2){
                        $Url=WEB_STUDY_URL.'/consultantmanage/mycenter/';
                    }elseif($UserInfo['Identity']==3){
                        $Url=WEB_STUDY_URL.'/teachermanage/mycenter/';
                    }else{
                        $Url=WEB_MEMBER_URL;
                    }
                }else{
                    $Url=WEB_MEMBER_URL;
                }
                $this->AddBank();
                $json_result = array(
                    'ResultCode' => 200,
                    'Message' => '登录成功',
                    'Url' => $Url
                );
            } else {
                // 设置密码超过三次
                $PasswordErrTimes = intval($_COOKIE['PasswordErrTimes']) + 1;
                setcookie("PasswordErrTimes", $PasswordErrTimes, time() + 3600, "/", WEB_HOST_URL);
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

    // 手机号码直接登录（获取短信验证码）
    private function MpLogin()
    {
        $ImageCode = strtolower($_POST['ImageCode']);
        if ($ImageCode == $_SESSION['authnum_session']) {
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
                    'Message' => '发送成功'
                );
            } else {
                $json_result = array(
                    'ResultCode' => 100,
                    'Message' => '验证码发送失败,请重试'
                );
            }
        } else {
            $json_result = array(
                'ResultCode' => 101,
                'Message' => '验证码错误'
            );
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
                    if($UserID){
                        $UserInfoModule = new MemberUserInfoModule();
                        $InfoData['UserID'] = $UserID;
                        $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                        $InfoData['BirthDay'] = date("Y-m-d H:i:s");
                        $InfoData['LastLogin'] = date('Y-m-d H:i:s');
                        $InfoData['Sex'] = 1;
                        $InfoData['Avatar']='/img/man3.0.png';
                        $insert_result1 = $UserInfoModule->InsertData($InfoData);
                        // 同步SESSIONID
                        setcookie("session_id", session_id(), time() + 3600 * 24, "/", WEB_HOST_URL);
                        $_SESSION['UserID'] = $InfoData['UserID'];
                        $_SESSION['NiceName'] = $InfoData['NickName'];
                        $_SESSION['Account'] = $Account;
                        setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", WEB_HOST_URL);
                        unset($_SESSION['temp_account']);
                        $this->AddBank();
                    }
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
                $this->AddBank();
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

    // 快捷登录设置密码
    private function MpLoginSetPass()
    {
        $Account = $_SESSION['UserID'];
        $PassWord = trim($_POST['PassWord']);
        $PassWordConfirm = trim($_POST['PassWordConfirm']);
        $User = new MemberUserModule();
        $UserInfo = $User->GetUserIDbyMobile($_SESSION['Account']);
        if ($PassWord != $PassWordConfirm) {
            $json_result = array(
                'ResultCode' => 104,
                'Message' => '两次密码不一致!',
                'Url' => ''
            );
        } elseif (strlen($PassWord) < 6) {
            $json_result = array(
                'ResultCode' => 101,
                'Message' => '密码格式错误!',
                'Url' => ''
            );
        } elseif (empty($UserInfo)) {
            $json_result = array(
                'ResultCode' => 103,
                'Message' => '您的账户有误!',
                'Url' => ''
            );
        } elseif ($UserInfo['PassWord'] != '') {
            $json_result = array(
                'ResultCode' => 102,
                'Message' => '请求异常!',
                'Url' => ''
            );
        } else {
            $Data['PassWord'] = md5($PassWord);
            $User->UpdateInfoByKeyID($Data, $UserInfo['UserID']);
            $json_result = array(
                'ResultCode' => 200,
                'Message' => '设置密码成功',
                'Url' => WEB_MEMBER_URL
            );
        }
        echo json_encode($json_result);
        exit();
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

    //保存用户信息
    private function ModifyUserInfo()
    {
        $MemberUserInfoModule = new MemberUserInfoModule ();
        $Data = array();
        if (isset ($_POST ['NickName']) && trim($_POST ['NickName']) != '') {
            $Data ['NickName'] = trim($_POST ['NickName']);
            $UserInfo = $MemberUserInfoModule->CheckNickName($Data ['NickName']);
            if ($UserInfo && $UserInfo['UserID'] != $_SESSION['UserID']) {
                $json_result = array('ResultCode' => 100, 'Message' => '保存失败,昵称已经存在');
                echo json_encode($json_result);
                exit ();
            }
        }
        if (isset ($_POST ['Sex']) && trim($_POST ['Sex']) != '') {
            $Data ['Sex'] = $_POST ['Sex'];
        }
        if (isset ($_POST ['BirthDay']) && trim($_POST ['BirthDay']) != '') {
            $Data ['BirthDay'] = trim($_POST ['BirthDay']);
        }
        if (isset ($_POST ['Province']) && trim($_POST ['Province']) != '') {
            $Data ['Province'] = trim($_POST ['Province']);
        }
        if (isset ($_POST ['City']) && trim($_POST ['City']) != '') {
            $Data ['City'] = trim($_POST ['City']);
        }
        if (isset ($_POST ['Area']) && trim($_POST ['Area']) != '') {
            $Data ['Area'] = trim($_POST ['Area']);
        }
        if (isset ($_POST ['Address'])) {
            $Data ['Address'] = trim($_POST ['Address']);
        }
        if (isset ($_POST ['Signaure'])) {
            $Data ['Signature'] = trim($_POST ['Signaure']);
        }
        if (isset ($_POST ['RealName'])) {
            $Data ['RealName'] = trim($_POST ['RealName']);
        }
        if (isset($_POST['Image'])) {
            $Data ['Avatar'] = $this->UploadPictures(current($_POST['Image']), '/Uploads/User/');
        }
        if (isset ($_POST ['CardNum'])) {
            $Data ['CardNum'] = trim($_POST ['CardNum']);
        }
        if (isset ($_POST ['CardPositive'])) {
            $Data ['CardPositive'] = trim($_POST ['CardPositive']);
        }
        if (isset ($_POST ['CardBack'])) {
            $Data ['CardBack'] = trim($_POST ['CardBack']);
        }
        if (count($Data)) {
            $Result = $MemberUserInfoModule->UpdateData($Data, $_SESSION ['UserID']);
            if ($Result || $Result === 0) {
                $_SESSION['NickName'] = $Data ['NickName'];
                if ($_POST['identity'] == 0) {
                    $json_result = array('ResultCode' => 200, 'Message' => '保存成功');
                } else {
                    if ($_POST['identity'] == 1) {
                        $Url = '/consultantmember/certification/';
                    } elseif ($_POST['identity'] == 2) {
                        $Url = '/teachermember/certification/';
                    }
                    $json_result = array('ResultCode' => 200, 'Message' => '保存成功', 'Url' => $Url);
                }

            } else {
                $json_result = array('ResultCode' => 101, 'Message' => '保存失败,重新尝试');
            }
        } else {
            $_SESSION['NickName'] = $Data ['NickName'];
            $json_result = array('ResultCode' => 200, 'Message' => '保存成功');
        }
        echo json_encode($json_result);
    }

    //上传头像
    private function SaveAvatar()
    {
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
                    $_SESSION['Avatar'] = LImageURL . $ImgUrl;
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


    //密码修改
    private function ModifyPass()
    {
        $PassWord = md5($_POST ['Pass']);
        $MemberUserModule = new MemberUserModule ();
        $User = $MemberUserModule->GetUserByID($_SESSION ['UserID']);
        if ($User['PassWord'] == '' || $PassWord == $User ['PassWord']) {
            $Data ['PassWord'] = md5($_POST ['NewPass']);
            $Result = $MemberUserModule->UpdateUser($Data, $_SESSION ['UserID']);
            if ($Result || $Result === 0) {
                $json_result = array('ResultCode' => 200, 'Message' => '保存成功', 'Url' => WEB_MEMBER_URL);
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '保存失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 101, 'Message' => '密码错误');
        }
        echo json_encode($json_result);
    }

    //绑定邮箱或手机
    private function BindAccount()
    {
        $ImageCode = strtolower(trim($_POST ['ImageCode']));
        if ($ImageCode == $_SESSION ['authnum_session']) {
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
                            $json_result = array('ResultCode' => 200, 'Message' => '保存成功', 'Url' => WEB_MEMBER_URL . '/member/securitycenter/');
                        } else {
                            $json_result = array('ResultCode' => 100, 'Message' => '保存失败,请重试');
                        }
                    }
                } else {
                    $json_result = array('ResultCode' => 101, 'Message' => '短信验证码错误');
                }
            }
        } else {
            $json_result = array('ResultCode' => 103, 'Message' => '图形验证码错误');
        }
        echo json_encode($json_result);
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
                        $json_result = array('ResultCode' => 200, 'Message' => '保存成功', 'Url' => WEB_MEMBER_URL . '/member/securitycenter/');
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

    //获取短信验证
    private function SendMobileCode()
    {
        $MemberUserModule = new MemberUserModule ();
        $Authentication = new MemberAuthenticationModule ();
        $User = $MemberUserModule->GetUserByID($_SESSION ['UserID']);
        $Data ['Account'] = $User ['Mobile'];
        $Data ['VerifyCode'] = mt_rand(100000, 999999);
        $Data ['Type'] = 0;
        $Data ['XpirationDate'] = Time() + 1800;
        $ID = $Authentication->searchAccount($Data ['Account']);
        if ($ID) {
            $result = $Authentication->UpdateUser($Data, $ID);
        } else {
            $result = $Authentication->InsertUser($Data);
        }
        $result = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
        if ($result) {
            $json_result = array('ResultCode' => 200, 'Message' => '已发出');
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '验证码发送失败,请重试');
        }
        echo json_encode($json_result);
    }

    //获取邮箱验证
    private function SendMailCode()
    {
        $MemberUserModule = new MemberUserModule ();
        $Authentication = new MemberAuthenticationModule ();
        $User = $MemberUserModule->GetUserByID($_SESSION ['UserID']);
        $Data ['Account'] = $User ['E-Mail'];
        $Data ['VerifyCode'] = mt_rand(100000, 999999);
        $Data ['Type'] = 1;
        $Data ['XpirationDate'] = Time() + 1800;
        $ID = $Authentication->searchAccount($Data ['Account']);
        if ($ID) {
            $result = $Authentication->UpdateUser($Data, $ID);
        } else {
            $result = $Authentication->InsertUser($Data);
        }
        $result = ToolService::SendEMailNotice($Data['Account'], "57美国网邮箱验证", "<span>【57美国网】 您的邮箱验证码为 " . $Data['VerifyCode'] . " 如有疑问请致电：400-018-5757 </span>");
        if ($result) {
            $json_result = array('ResultCode' => 200, 'Message' => '已发出');
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '验证码发送失败,请重试');
        }
        echo json_encode($json_result);
    }

    //手机验证
    private function MobileVerifyCode()
    {
        $MemberUserModule = new MemberUserModule ();
        $Authentication = new MemberAuthenticationModule ();
        $User = $MemberUserModule->GetUserByID($_SESSION ['UserID']);
        $CurrentTime = time();
        $VerifyCode = $_POST ['VerifyCode'];
        $ID = $Authentication->ValidateAccount($User ['Mobile'], $VerifyCode, 0);
        if ($ID) {
            $TempUserInfo = $Authentication->GetAccountInfo($User ['Mobile'], $VerifyCode, 0);
            if ($CurrentTime > $TempUserInfo ['XpirationDate']) {
                $json_result = array('ResultCode' => 101, 'Message' => '验证码已过期');
            } else {
                $Authentication->DeleteUser($ID);
                $_SESSION['MobileVerify'] = 'success';
                $json_result = array('ResultCode' => 200, 'Message' => '验证成功', 'Url' => WEB_MEMBER_URL . '/member/securitycenter/?do=bindmobile');
            }
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '短信验证码错误');
        }
        echo json_encode($json_result);
    }

    //邮箱验证
    private function MailVerifyCode()
    {
        $MemberUserModule = new MemberUserModule ();
        $Authentication = new MemberAuthenticationModule ();
        $User = $MemberUserModule->GetUserByID($_SESSION ['UserID']);
        $CurrentTime = time();
        $VerifyCode = $_POST ['VerifyCode'];
        $ID = $Authentication->ValidateAccount($User ['E-Mail'], $VerifyCode, 1);
        if ($ID) {
            $CurrentTime = time();
            $TempUserInfo = $Authentication->GetAccountInfo($User ['E-Mail'], $VerifyCode, 1);
            if ($CurrentTime > $TempUserInfo ['XpirationDate']) {
                $json_result = array('ResultCode' => 101, 'Message' => '验证码已过期');
            } else {
                $Authentication->DeleteUser($ID);
                $_SESSION['EMailVerify'] = 'success';
                $json_result = array('ResultCode' => 200, 'Message' => '验证成功', 'Url' => WEB_MEMBER_URL . '/member/securitycenter/?do=bindmail');
            }
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '短信验证码错误');
        }

        echo json_encode($json_result);
    }

    //获取找回密码验证码
    private function FindPwdVerifyCode()
    {
        $ImageCode = strtolower(trim($_POST['ImageCode']));
        if ($ImageCode == $_SESSION['authnum_session']) {
            $User = new MemberUserModule();
            $Data['Account'] = trim($_POST['User']);
            if ($User->AccountExists($Data['Account'])) {
                if (is_numeric($Data['Account'])) {
                    $Data['Type'] = 0;
                } elseif (strpos($Data['Account'], '@')) {
                    $Data['Type'] = 1;
                }
                $Data['VerifyCode'] = mt_rand(100000, 999999);
                $Data['XpirationDate'] = Time() + 1800;
                $Authentication = new MemberAuthenticationModule();
                $ID = $Authentication->searchAccount($Data['Account']);
                if ($ID) {
                    $result = $Authentication->UpdateUser($Data, $ID);
                } else {
                    $result = $Authentication->InsertUser($Data);
                }
                if ($result) {
                    if ($Data['Type'] == 1) {
                        $result = ToolService::SendEMailNotice($Data['Account'], "57美国网邮箱验证", "<span>【57美国网】 您的邮箱验证码为 " . $Data['VerifyCode'] . " 如有疑问请致电：400-018-5757 </span>");
                        if ($result) {
                            $json_result = array(
                                'ResultCode' => 200,
                                'Message' => '已发出'
                            );
                        } else {
                            $json_result = array(
                                'ResultCode' => 100,
                                'Message' => '验证码发送失败,请重试'
                            );
                        }
                    } else {
                        $result = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
                        if ($result) {
                            $json_result = array(
                                'ResultCode' => 200,
                                'Message' => '已发出'
                            );
                        } else {
                            $json_result = array(
                                'ResultCode' => 100,
                                'Message' => '验证码发送失败,请重试'
                            );
                        }
                    }
                } else {
                    $json_result = array(
                        'ResultCode' => 100,
                        'Message' => '验证码发送失败,请重试'
                    );
                }
            } else {
                $json_result = array(
                    'ResultCode' => 102,
                    'Message' => '发送失败,账号不存在'
                );
            }
        } else {
            $json_result = array(
                'ResultCode' => 101,
                'Message' => '发送失败,验证码错误'
            );
        }
        echo json_encode($json_result);
    }

    //找回密码 短信身份验证
    private function FindPwdVerify()
    {
        $VerifyCode = intval(trim($_POST['Code']));
        $Account = trim($_POST['User']);
        $User = new MemberUserModule();
        if (!$User->AccountExists($Account)) {
            $result = array(
                'ResultCode' => 106,
                'Message' => '账号不存在'
            );
            echo json_encode($result);
            exit();
        }
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
                $result = array(
                    'ResultCode' => 103,
                    'Message' => '短信验证码过期'
                );
            } else {
                $_SESSION['findpwd_account'] = $Account;
                $Authentication->DeleteUser($TempUserInfo['ID']);
                $result = array(
                    'ResultCode' => 200,
                    'Message' => '身份验证通过'
                );
            }
        } else {
            $result = array(
                'ResultCode' => 102,
                'Message' => '短信验证码错误'
            );
        }
        echo json_encode($result);
        exit();
    }

    //重新设置密码
    private function ResetPass()
    {
        if (isset($_SESSION['findpwd_account']) && !empty($_SESSION['findpwd_account'])) {
            $MemberUserModule = new MemberUserModule();
            $UserInfo = $MemberUserModule->AccountExists($_SESSION['findpwd_account']);
            if (trim($_POST['PassWord']) != trim($_POST['PassWordConfirm'])) {
                $result = array(
                    'ResultCode' => 104,
                    'Message' => '两次密码不一样，请重试！',
                    'Url' => WEB_MEMBER_URL
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
                    $_SESSION['Account'] = $_SESSION['findpwd_account'];
                    unset($_SESSION['findpwd_account']);
                    $result = array(
                        'ResultCode' => 200,
                        'Message' => '修改成功',
                        'Url' => WEB_MEMBER_URL
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
                    'Url' => WEB_MEMBER_URL . '/member/findpassword/'
                );
            }
        } else {
            $result = array(
                'ResultCode' => 102,
                'Message' => '异常的请求,请先进行身份验证',
                'Url' => WEB_MEMBER_URL . '/member/findpassword/'
            );
        }
        echo json_encode($result);
        exit();
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
    
    private function AddBank(){
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserBankModule.php';
        $MemberUserBankModule=new MemberUserBankModule();
        $BankInfo=$MemberUserBankModule->GetInfoByWhere("and UserID={$_SESSION['UserID']}");
        if(!$BankInfo){
            $Data['UserID']=$_SESSION['UserID'];
            $Data['TotalBalance']=0;
            $Data['FrozenBalance']=0;
            $Data['FreeBalance']=0;
            $MemberUserBankModule->InsertData($Data);
        }
    }
    //----------------------------------  我的收藏  ------------------------------------//
    /**
     * @desc  删除收藏记录
     */
    private function DeleteCo()
    {
        $this->IsLogin();
        $CollectionModule = new MemberCollectionModule();
        $IDs = $_POST['data'];
        foreach ($IDs as $val) {
            $result = $CollectionModule->DeleteByWhere(' and CollectionID = '.$val.' and UserID = '.$_SESSION['UserID']);
        }
        $array = array('ResultCode' => '200', 'Message' => '删除成功');
        echo json_encode($array);
        exit;
    }

    //----------------------------------  常用地址  ------------------------------------//
    private function  IsLogin()
    {
        if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
            $res = array('ResultCode' => '100', 'Message' => '请先登录');
            echo json_encode($res);
            exit;
        }
    }

    //收货地址
    private function SaveAddress()
    {
        $this->IsLogin();
        $ID = intval($_POST['ID']);
        $Data['Address'] = trim($_POST['Address']);
        $Data['Tel'] = trim($_POST['Tel']);
        $Data['Contacts'] = trim($_POST['Contacts']);
        $Data['ZipCode'] = trim($_POST['ZipCode']);
        $Data['IsDefault'] = $_POST['IsDefault'] == 'true' ? 1 : 0;
        $City = trim($_POST['CitySet']);
        if ($City != '') {
            $CityArr = explode('-', $City);
            $Data['Province'] = $CityArr[0];
            $Data['City'] = $CityArr[1];
            $Data['Area'] = $CityArr[2];
        }
        $ShippingAddressModule = new MemberShippingAddressModule();
        if ($Data['IsDefault'] == 1) {
            $ShippingAddressModule->ResetIsDefault($_SESSION['UserID']);
        }
        if ($ID) {
            $result = $ShippingAddressModule->UpdateInfoByWhere($Data,' ShippingAddressID = '.$ID.' and UserID = '.$_SESSION['UserID']);
        } else {
            $Data['UserID'] = $_SESSION['UserID'];
            $result = $ShippingAddressModule->InsertInfo($Data);
        }
        if ($result || $result === 0) {
            $res = array('ResultCode' => '200', 'Message' => '保存成功');
        } else {
            $res = array('ResultCode' => '100', 'Message' => '保存失败');
        }
        echo json_encode($res);
        exit;
    }

    //删除收货地址
    private function DelAddress()
    {
        $this->IsLogin();
        $ShippingAddressModule = new MemberShippingAddressModule();
        if (isset($_POST['ID'])) {
            $ShippingAddressID = intval($_POST['ID']);
            $result = $ShippingAddressModule->DeleteByKeyID($ShippingAddressID, $_SESSION['UserID']);
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
     * @desc  设置默认地址
     */
    private function SetDefaultAddress()
    {
        $this->IsLogin();
        $ShippingAddressModule = new MemberShippingAddressModule();
        $ShippingAddressModule->ResetIsDefault($_SESSION['UserID']);
        $ID = $_POST['ID'];
        $Data = array('IsDefault' => 1);
        $result = $ShippingAddressModule->UpdateInfoByWhere($Data,' ShippingAddressID = '.$ID.' and UserID = '.$_SESSION['UserID']);
        if ($result) {
            $res = array('ResultCode' => '200', 'Message' => '设置成功');
        } else {
            $res = array('ResultCode' => '100', 'Message' => '设置失败');
        }
        echo json_encode($res);
        exit;
    }

    /**
     * @desc  获取单个地址信息
     */
    private function GetAddress()
    {
        $this->IsLogin();
        $ShippingAddressModule = new MemberShippingAddressModule();
        $ID = $_POST['ID'];
        $Info = $ShippingAddressModule->GetInfoByKeyID($ID);
        $CitySet = '';
        if ($Info['Province']) {
            $CitySet .= $Info['Province'];
            if ($Info['City']) {
                $CitySet .= '-' . $Info['Province'];
                if ($Info['Area']) {
                    $CitySet .= '-' . $Info['Area'];
                }
            }
        }
        $Info['CitySet'] = $CitySet;
        $res = array('Info' => $Info);
        echo json_encode($res);
        exit;
    }

    /**
     * @desc  保存个人资料
     */
    public function PersonalProfile(){
        if (! $_POST) {
            $Data['ResultCode'] = 100;
            EchoResult($Data);
        }
        if ($_POST){
            $UserID = $_SESSION ['UserID'];
            $MemberUserInfoModule = new MemberUserInfoModule();
            $MemberUserModule = new MemberUserModule();
            $Data['NickName'] = trim($_POST['NickName']);
            $Data['Sex'] = trim($_POST['Sex']);
            $Date['Mobile'] = trim($_POST['Mobile']);
            $Date['E-Mail'] = trim($_POST['Email']);
            $UpdateUserInfo = $MemberUserInfoModule->UpdateInfoByWhere($Data,' UserID = '.$UserID);
            $UpdateUser = $MemberUserModule->UpdateInfoByKeyID($Date,$UserID);
            if ($UpdateUserInfo>=0 && $UpdateUser>=0){
                $Data['ResultCode'] = 200;
                $Data['Message'] = '更新成功';
                EchoResult($Data);
            }else{
                $Data['ResultCode'] = 101;
                $Data['Message'] = '更新失败';
            }
        }
    }
}