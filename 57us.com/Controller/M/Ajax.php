<?php

class Ajax
{
    public function __construct()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductModule.php';
    }

    public function Index()
    {
        $Intention = trim($_POST ['Intention']);
        if ($Intention == '') {
            $json_result = array( 'ResultCode' => 500,'Message' => '系統錯誤','Url' => '');
            echo json_encode($json_result);
            exit;
        }
        $this->$Intention ();
    }

    /**
     * @desc 判断手机号码是否注册
     */
    public function JudgeIsRegister()
    {
        include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberUserModule.php';
        $UserModule = new MemberUserModule();
        $Mobile = trim($_POST['Mobile']);
        $UserID = $UserModule->GetUserIDbyMobile($Mobile);
        if ($UserID) {
            $json_result = array('ResultCode' => '200');
        } else {
            $json_result = array('ResultCode' => '100');
        }
        echo json_encode($json_result);
        exit;
    }

    /**
     * @desc  发送手机验证码，验证手机
     */
    public function ValidateMobileCode()
    {
        $Data['Account'] = trim($_POST['Mobile']);
        $Data['VerifyCode'] = mt_rand(100000, 999999);
        $Data['XpirationDate'] = Time() + 60 * 30;
        include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberAuthenticationModule.php';
        $Data ['Type'] = 0;
        $Authentication = new MemberAuthenticationModule ();
        $ID = $Authentication->searchAccount($Data ['Account']);
        if ($ID) {
            $result = $Authentication->UpdateUser($Data, $ID);
        } else {
            $result = $Authentication->InsertUser($Data);
        }
        if ($result) {
            ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
            if ($result) {
                $json_result = array('ResultCode' => 200, 'Message' => '发送成功');
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '验证码发送失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '发送失败,系统异常');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  通过图形验证码发送用户短信
     */
    private function PhoneCode()
    {
        $ImageCode = strtolower(trim($_POST['ImageCode']));
        if ($ImageCode == $_SESSION['authnum_session']) {
            $Data['Account'] = trim($_POST['User']); // 用户手机号
            require_once SYSTEM_ROOTPATH . '/Include/ManDaoSmsApi.php';
            $smsapi = new ManDaoSmsApi();
            $Data['VerifyCode'] = mt_rand(100000, 999999);
            $Data['XpirationDate'] = Time() + 60 * 30;
            if (is_numeric($Data['Account'])) {
                $Data['Type'] = 0;
            } elseif (strpos($Data['Account'], '@')) {
                $Data['Type'] = 1;
            }
            include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberAuthenticationModule.php';
            $MemberAuthenticationModule = new MemberAuthenticationModule();
            $ID = $MemberAuthenticationModule->searchAccount($Data['Account']);
            if ($ID) {
                $MemberAuthenticationModule->UpdateUser($Data, $ID);
            } else {
                $MemberAuthenticationModule->InsertUser($Data);
            }
            $result = $smsapi->sendSMS($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。【57美国网】');
            if ($result == "success") {
                $json_result = array('ResultCode' => 200,'Message' => '发送成功');
            } else {
                $json_result = array('ResultCode' => 100,'Message' => '验证码发送失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 104,'Message' => '发送失败,图形验证码错误');
        }
        EchoResult($json_result);
    }

}