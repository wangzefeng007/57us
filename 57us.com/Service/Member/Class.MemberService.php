<?php
/**
 * @desc 处理逻辑函数
 */
Class MemberService{

    /**
     * @desc  判断是否登录并返回登录页面
     */
    public static function IsLogin(){
        if (!isset ($_SESSION ['UserID']) || empty ($_SESSION ['UserID'])) {
            header('Location:' . WEB_MEMBER_URL . '/member/login/');
        }
    }

    /**
     * @desc  发送手机验证码，验证手机
     * @param $Mobile 手机号码
     */
    public static function SendMobileVerificationCode($Mobile)
    {
        $Data['Account'] = trim($Mobile);
        $Data['VerifyCode'] = mt_rand(100000, 999999);
        $Data['XpirationDate'] = Time() + 60 * 30;
        $Data ['Type'] = 0;
        $Authentication = new MemberAuthenticationModule ();
        $ID = $Authentication->searchAccount($Data ['Account']);
        if ($ID) {
            $result = $Authentication->UpdateUser($Data, $ID);
        } else {
            $result = $Authentication->InsertUser($Data);
        }
        if ($result) {
            $result1 = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
            if ($result1) {
                $json_result = array('ResultCode' => 200, 'Message' => '发送成功');
            } else {
                $json_result = array('ResultCode' => 103, 'Message' => '验证码发送失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 104, 'Message' => '发送失败,系统异常');
        }
        return $json_result;
    }

    /**
     * @desc  发送邮箱验证码，验证邮箱
     * @param $Email 邮箱
     */
    public static function SendMailVerificationCode($Email){
        $Authentication = new MemberAuthenticationModule ();
        $Data ['Account'] = $Email;
        $Data ['VerifyCode'] = mt_rand(100000, 999999);
        $Data ['Type'] = 1;
        $Data ['XpirationDate'] = Time() + 1800;
        $ID = $Authentication->searchAccount($Data ['Account']);
        if ($ID) {
            $result = $Authentication->UpdateUser($Data, $ID);
        } else {
            $result = $Authentication->InsertUser($Data);
        }
        if ($result) {
            $result1 = ToolService::SendEMailNotice($Data['Account'], "57美国网邮箱验证", "<span>【57美国网】 您的邮箱验证码为 " . $Data['VerifyCode'] . " 如有疑问请致电：400-018-5757 </span>");
            if ($result1) {
                $json_result = array('ResultCode' => 200, 'Message' => '发送成功');
            } else {
                $json_result = array('ResultCode' => 103, 'Message' => '验证码发送失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 104, 'Message' => '发送失败,系统异常');
        }
        return $json_result;
    }

    /**
     * @desc  验证手机或邮箱验证码
     * @param $VerifyCode  验证码
     * @param $Account     帐号：手机或邮箱
     * @param $Type        帐号类型 0-手机 1-邮箱
     * @return array
     */
    public static function VerifySendCode($VerifyCode, $Account)
    {
        if (is_numeric($Account)) { //手机
            $Type = 0;
        } elseif (strpos($Account, '@')) { //邮箱
            $Type = 1;
        }
        $Authentication = new MemberAuthenticationModule();
        $TempUserInfo = $Authentication->GetAccountInfo($Account, $VerifyCode, $Type);
        if ($TempUserInfo) {
            $CurrentTime = time();
            if ($CurrentTime > $TempUserInfo['XpirationDate']) {
                $result = array('ResultCode' => 103, 'Message' => '短信验证码过期');
            } else {
                $_SESSION['temp_account'] = $Account;
                $Authentication->DeleteUser($TempUserInfo['ID']);
                $result = array('ResultCode' => 200, 'Message' => '短信验证通过',);
            }
        } else {
            $result = array('ResultCode' => 102, 'Message' => '短信验证码错误');
        }
        return $result;
    }

    /**
     * @desc  获取用户资金信息
     */
    public static function GetUserBankInfo($UserID){
        $BankModule = new MemberUserBankModule();
        $BankInfo = $BankModule->GetInfoByWhere(' and UserID = '.$UserID);
        if(!$BankInfo){
            $BankInfo['UserID'] = $UserID;
            $BankInfo['TotalBalance'] = 0;
            $BankInfo['FrozenBalance'] = 0;
            $BankInfo['FreeBalance'] = 0;
            $BankInfo['BankID'] = $BankModule->InsertInfo($BankInfo);
        }
        return $BankInfo;
    }

    /**
     * @desc  $desc   发送系统消息
     * @param $UserID
     */

    public static function SendSystemMessage($UserID){
        $MemberMessageInfoModule = new MemberMessageInfoModule();
        $MemberMessageSendModule = new MemberMessageSendModule();
        $UserInfoModule = new MemberUserInfoModule();
        $UserModule = new MemberUserModule();
        $MessageInfo = $MemberMessageInfoModule->GetInfoByWhere(' and SendStatus = 2',true);
        $UserInfo = $UserInfoModule->GetInfoByUserID($UserID);
        $User = $UserModule->GetInfoByKeyID($UserID);
        foreach($MessageInfo as $key => $val){
            if($val['SendStatus'] == 2){ //信息状态为发送时，才发送
                if($User['AddTime'] < $val['SendTime']){ //判断注册时间是否在发送信息之前
                    if( $val['SendType'] == 1 || ($val['SendType'] == 2 && $UserInfo['Identity'] == 1) || ($val['SendType'] == 3 && $UserInfo['Identity'] == 2) || ($val['SendType'] == 4 && $UserInfo['Identity'] == 3)){
                        $IsSend = $MemberMessageSendModule->GetInfoByWhere(' and MessageID = '.$val['MessageID'].' and UserID='.$UserID);
                        if(!$IsSend){ //开始发送消息
                            $SendData = array('MessageID'=>$val['MessageID'],'UserID'=>$UserID,'SendUserID'=>0,'SendType'=>$val['SendType'],'Status'=>1);
                            $MemberMessageSendModule->InsertInfo($SendData);
                        }
                    }
                }
            }
        }
    }
    /**
     * @desc  $desc  添加浏览记录（只添加旅游跟留学产品）
     * @param $ID 产品
     * @param $Type
     */
    public static function AddBrowsingHistory($ID,$Type){
        if ($_SESSION['UserID']>0){
            $MemberBrowsingHistoryModule = new MemberBrowsingHistoryModule();
            $BrowsingHistory = $MemberBrowsingHistoryModule->GetInfoByWhere(' and RelevanceID ='.$ID.' and Category = '.$Type.' and UserID ='.$_SESSION['UserID']);
            if (!$BrowsingHistory){
                $Data['Category'] = $Type;//产品类型
                $Data['UserID']= $_SESSION['UserID'];
                $Data['RelevanceID'] = $ID;//产品ID
                $Data['AddTime'] = date("Y-m-d H:i:s",time());
                $MemberBrowsingHistoryModule->InsertInfo($Data);
            }
        }
    }
}
