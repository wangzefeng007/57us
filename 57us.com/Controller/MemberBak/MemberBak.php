<?php

class Member
{
    public function __construct()
    {
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }

    //登录验证
    private function IsLogin()
    {
        if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
            $this->Login();
            exit;
        }
    }

    //已登录
    private function WasLogin()
    {
        if (isset($_SESSION['UserID']) && !empty($_SESSION['UserID'])) {
            header('Location:' . WEB_MEMBER_URL);
        }
    }

    //登入页或登录操作
    public function Login()
    {
        $this->WasLogin();
        if(strpos($_SERVER['HTTP_REFERER'],WEB_STUDY_URL)!==false){
            $ComeFrom=1;
        }else{
            $ComeFrom=0;
        }
        $Title = '会员登录 - 57美国网';
        include template('MemberLogin');
    }

    /**
     * @desc  退出登录
     */
    public function SignOut()
    {
        unset($_SESSION);
        setcookie("UserID", '', time() - 1, "/", WEB_HOST_URL);
        setcookie("Account", '', time() - 1, "/", WEB_HOST_URL);
        setcookie("session_id", session_id(), time() - 1, "/", WEB_HOST_URL);
        session_destroy();
        header("location:" . WEB_MEMBER_URL);
    }

    //注册
    public function Register()
    {
        $this->WasLogin();
        $Type = $_GET['type'];
        $Title = '会员登录_注册用户 - 57美国网';
        include template('MemberRegister');
    }

    //注册下一步
    public function SetPwd()
    {
        $this->WasLogin();
        if (isset($_SESSION['temp_account']) && !empty($_SESSION['temp_account'])) {
            $Title = '会员登录_注册用户 - 57美国网';
            include template('MemberSetPwd');
        } else {
            header("location:" . WEB_MEMBER_URL . "/member/register/");
        }
    }

    //找回密码
    public function FindPassWord()
    {
        $this->WasLogin();
        $Title = '会员登录_找回密码 - 57美国网';
        include template('MemberFindPassWord');
    }

    //重新设置密码
    public function ResetPwd()
    {
        $this->WasLogin();
        if (isset($_SESSION['findpwd_account']) && !empty($_SESSION['findpwd_account'])) {
            $MemberUserModule = new MemberUserModule();
            $User = $MemberUserModule->AccountExists($_SESSION['findpwd_account']);
            $MemberUserInfoModule = new MemberUserInfoModule();
            $UserInfo = $MemberUserInfoModule->GetUserInfo($User['UserID']);
            $Title = '会员登录_找回密码 - 57美国网';
            include template('MemberResetPwd');
        } else {
            header("location:" . WEB_MEMBER_URL . "/member/findpassword/");
        }
    }

    //会员中心
    public function Index()
    {
        $this->Information();
    }

    //个人资料
    public function Information()
    {
        $UserNav = 'User';
        $this->IsLogin();
        $MemberUserModule = new MemberUserModule();
        $Type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $User = $MemberUserModule->GetUserByID($_SESSION['UserID']);
        $User['E-Mail'] = strlen($User['E-Mail']) ? substr_replace($User['E-Mail'], '****', 1, strpos($User['E-Mail'], '@') - 2) : '';
        $User['Mobile'] = strlen($User['Mobile']) ? substr_replace($User['Mobile'], '****', 3, 4) : '';
        $SafeLevel = 1;
        if ($User['E-Mail'] != '') {
            $SafeLevel += 1;
        }
        if ($User['Mobile'] != '') {
            $SafeLevel += 1;
        }
        $_SESSION['SafeLevel'] = $SafeLevel;
        $MemberUserInfoModule = new MemberUserInfoModule();
        $UserInfo = $MemberUserInfoModule->GetUserInfo($_SESSION['UserID']);
        $GenderArr = array(0 => '女', 1 => '男', 2 => '保密');
        $_SESSION['NickName'] = $UserInfo['NickName'];
        if (strpos($UserInfo['Avatar'], 'http://') === false && $UserInfo['Avatar'] != '') {
            $UserInfo['Avatar'] = LImageURL . $UserInfo['Avatar'];
        }
        $_SESSION['Avatar'] = $UserInfo['Avatar'];
        $MemberUserMessageModule = new MemberUserMessageModule();
        $MessageResult = $MemberUserMessageModule->GetListsNum(" and UserID={$_SESSION['UserID']} and `Status`=0");
        $_SESSION['NoReadNum'] = $MessageResult['Num'];
        $Title = '会员中心_个人资料 - 57美国网';
        $Nav = 'Information';
        include template('MemberInformation');
    }

    //站内信息列表
    public function MessageList()
    {
        $UserNav = 'User';
        $this->IsLogin();
        $MemberUserMessageModule = new MemberUserMessageModule();
        //分页开始
        $Page = intval($_GET['Page']);
        if ($Page < 1)
            $Page = 1;
        $PageSize = 8;
        $MysqlWhere = 'and UserID=' . $_SESSION['UserID'];
        $Rscount = $MemberUserMessageModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            $Data['Data'] = $MemberUserMessageModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            MultiPage($Data, 10);
        }
        //未读条数
        $NoReadRscount = $MemberUserMessageModule->GetListsNum('and UserID=' . $_SESSION['UserID'] . ' and Status=0');
        $_SESSION['NoReadNum'] = $NoReadRscount['Num'];
        $Title = '会员中心_站内信息 - 57美国网';
        $Nav = 'Message';
        include template('MemberMessageList');
    }

    //安全中心
    public function SecurityCenter()
    {
        $UserNav = 'User';
        $this->IsLogin();
        $MemberUserModule = new MemberUserModule();
        $User = $MemberUserModule->GetUserByID($_SESSION['UserID']);
        $User['E-Mail'] = strlen($User['E-Mail']) ? substr_replace($User['E-Mail'], '****', 1, strpos($User['E-Mail'], '@') - 2) : '';
        $User['Mobile'] = strlen($User['Mobile']) ? substr_replace($User['Mobile'], '****', 3, 4) : '';
        $SafeLevel = 1;
        if ($User['E-Mail'] != '') {
            $SafeLevel += 1;
        }
        if ($User['Mobile'] != '') {
            $SafeLevel += 1;
        }
        $_SESSION['SafeLevel'] = $SafeLevel;
        $MemberUserInfoModule = new MemberUserInfoModule();
        $UserInfo = $MemberUserInfoModule->GetUserInfo($_SESSION['UserID']);
        $Title = '会员中心_安全中心 - 57美国网';
        $Nav = 'SecurityCenter';
        if (isset($_GET['do'])) {
            switch ($_GET['do']) {
                case 'modifymobile':
                    $Type = 'modifymobile';
                    break;
                case 'bindmobile':
                    $Type = 'bindmobile';
                    break;
                case 'modifymail':
                    $Type = 'modifymail';
                    break;
                case 'bindmail':
                    $Type = 'bindmail';
                    break;
                case 'modifypass':
                    $Type = 'modifypass';
                    break;
                default:
                    header("Location:/member/securitycenter/");
            }
            if ($Type == 'bindmobile') {
                if (!empty($User['Mobile'])) {
                    if (!isset($_SESSION['MobileVerify']) || $_SESSION['MobileVerify'] != 'success') {
                        header("Location:/member/securitycenter/");
                    }
                }
            } elseif ($Type == 'bindmail') {
                if (!empty($User['E-Mail'])) {
                    if (!isset($_SESSION['EMailVerify']) || $_SESSION['EMailVerify'] != 'success') {
                        header("Location:/member/securitycenter/");
                    }
                }
            }
            include template("MemberSecurityCenterModify");
        } else {
            include template("MemberSecurityCenter");
        }
    }
    //我的钱包
    public function Wallet()
    {
        $UserNav = 'Tour';
        $this->IsLogin();
        $UserBankModule = new MemberUserBankModule();
        $UserBank = $UserBankModule->GetWalletByID($_SESSION['UserID']);
        if (!$UserBank) {
            $UserBank['UserID'] = $_SESSION['UserID'];
            $UserBank['TotalBalance'] = 0.00;
            $UserBank['FrozenBalance'] = 0.00;
            $UserBank['FreeBalance'] = 0.00;
            $UserBankModule->InsertData($UserBank);
        }
        $Title = '会员中心_我的钱包 - 57美国网';
        $Nav = 'Wallet';
        include template('MemberWallet');
    }

    /**
     * @desc  自动创建会员
     * @param $Account 帐号
     * @param $Type    类型：1-普通会员，2-顾问，3-教师
     */
    public function AutoCreationMember(){
        $Account = $_GET['U'];
        $Type = $_GET['T']?$_GET['T']:1;
        //会员表
        $MemberUserModule = new MemberUserModule();
        //会员基础信息表
        $MemberUserInfoModule = new MemberUserInfoModule();
        //会员资金表
        $MemberUserBankModule = new MemberUserBankModule();
        $NowTime = time();

        $MemberData = array('PassWord'=>md5('admin888'),'AddTime'=>$NowTime,'State'=>1);
        if (is_numeric($Account)) {
            $IsExit = $MemberUserModule->GetInfoByWhere(' and Mobile='.$Account);
            if($IsExit){
                alert('帐号'.$Account.'已存在');
                return false;
            }
            $MemberData['Mobile'] = $Account;
            $MemberData['E-Mail'] = '';
        } elseif (strpos($Account, '@')) {
            $IsExit = $MemberUserModule->GetInfoByWhere("and `E-Mail`='".$Account."'");
            if($IsExit){
                alert('帐号'.$Account.'已存在');
                return false;
            }
            $MemberData['Mobile'] = '';
            $MemberData['E-Mail'] = $Account;
        }
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        //添加MemberUser表数据
        $UserID = $MemberUserModule->InsertInfo($MemberData);
        if(!$UserID){
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            alert('MemberUser表数据添加失败');
        }
        else{
            $UserInfoData = array(
                'UserID'=>$UserID,
                'NickName'=>'57US_'.date('i').mt_rand(100,999),
                'Avatar'=>'/img/man3.0.png',
                'Identity'=>$Type,
                'IdentityState'=>0,
            );
            //添加MemberUserInfo表数据
            $MemberUserInfoResult = $MemberUserInfoModule->InsertInfo($UserInfoData);
            if(!$MemberUserInfoResult){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                alert('MemberUserInfo表数据添加失败');
                return false;
            }
            else{
                $UserBankData = array('UserID'=>$UserID,'TotalBalance'=>0,'FrozenBalance'=>0,'FreeBalance'=>0);
                //添加MemberUserBank表数据
                $UserBankResult = $MemberUserBankModule->InsertInfo($UserBankData);
                if(!$UserBankResult){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    alert('MemberUserBank表数据添加失败');
                    return false;
                }
                else{
                    if($Type == 2){ //顾问
                        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
                        $ConsultantInfoData = array('UserID'=>$UserID,'Grade'=>1,'TutorialObject'=>0);
                        $ConsultantInfoResult = $StudyConsultantInfoModule->InsertInfo($ConsultantInfoData);
                        if(!$ConsultantInfoResult){
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            alert('StudyConsultantInfo表数据添加失败');
                            return false;
                        }
                        else{
                            $DB->query("COMMIT");//执行事务
                            alert('用户'.$Account.'添加成功');
                            return false;
                        }
                    }
                    elseif($Type == 3){ //教师
                        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
                        $StudyTeacherInfoData = array('UserID'=>$UserID,'Grade'=>1,'TutorialObject'=>0);
                        $StudyTeacherInfoResult = $StudyTeacherInfoModule->InsertInfo($StudyTeacherInfoData);
                        if(!$StudyTeacherInfoResult){
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            alert('StudyTeacherInfo表数据添加失败');
                            return false;
                        }
                        else{
                            $DB->query("COMMIT");//执行事务
                            alert('用户'.$Account.'添加成功');
                            return false;
                        }
                    }
                    else{
                        $DB->query("COMMIT");//执行事务
                        alert('用户'.$Account.'添加成功');
                        return false;
                    }
                }
            }
        }
    }
}
