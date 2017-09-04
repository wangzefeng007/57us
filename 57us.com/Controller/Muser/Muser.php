<?php
/**
 * 资讯会员中心
 * By Leo
 */
class Muser
{
    public function __construct(){
    }
    
     public function  Index()
    {
        $this->Login();
    }
    
    /**
     * 已经登录
     */
    private function WasLogin()
    {
        if (isset($_SESSION['UserID']) && !empty($_SESSION['UserID'])) {
            header('Location:' . WEB_MUSER_URL.'/member/mycenter/');
            exit;
        }
    }
    
    /**
     * 普通注册页面
     */
    public function Register(){
        $this->WasLogin();
        include template('MuserRegister');
    }
  
    /**
     * @desc 重置密码
     */
    public function ResetPassword(){
        include template('MuserResetPassword');
    }
    
    /**
     * @desc  账号登录页
     */
    public function Login(){
        if (isset($_SESSION['UserID']) && !empty($_SESSION['UserID'])) {
            header('Location:' . WEB_MUSER_URL.'/member/mycenter/');
            exit;
        }
        /*
        if(strpos($_SERVER['HTTP_REFERER'],WEB_M_URL.'/study/')!==false){
            $ComeFrom=1;
        }elseif(strpos($_SERVER['HTTP_REFERER'],WEB_M_URL.'/tour/')!==false || strpos($_SERVER['HTTP_REFERER'],WEB_M_URL.'/group/')!==false || strpos($_SERVER['HTTP_REFERER'],WEB_M_URL.'/play/')!==false){
            $ComeFrom=0;
        }
         */
        $Title = '会员登录 - 57美国网';
        include template('MuserLogin');
    }

    /**
     * @desc  手机号码登录页
     */
    public function MobileLogin(){
        $this->WasLogin();
        include template('MuserMobileLogin');
    }
    
    /**
     * @desc  个人中心
     */
    public function MyCenter(){
        MuserService::IsLogin();
        if(strpos($_SERVER['HTTP_REFERER'],WEB_M_URL.'/study/')!==false){
            header('Location:/muserstudy/');
        }elseif(strpos($_SERVER['HTTP_REFERER'],WEB_M_URL.'/tour/')!==false || strpos($_SERVER['HTTP_REFERER'],WEB_M_URL.'/group/')!==false || strpos($_SERVER['HTTP_REFERER'],WEB_M_URL.'/play/')!==false){
            header('Location:/musertour/');
        }else{
            $MemberUserBankModule = new MemberUserBankModule();
            $MemberUserInfoModule = new MemberUserInfoModule();
            $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION ['UserID']);
            $UserBank = $MemberUserBankModule->GetInfoByWhere(' and UserID = '.$_SESSION ['UserID']);
            $Title = '会员中心 - 57美国网';
            include template('MuserMyCenter');
        }
    }
    
    /**
     * @decs 我的资金
     */
    public function MyWallet(){
        MuserService::IsLogin();
        $UserID=$_SESSION['UserID'];
        $UserBankModule = new MemberUserBankModule();
        $UserBank = $UserBankModule->GetInfoByWhere(" and UserID=$UserID");
        if (!$UserBank) {
            $UserBank['UserID'] = $UserID;
            $UserBank['TotalBalance'] = 0.00;
            $UserBank['FrozenBalance'] = 0.00;
            $UserBank['FreeBalance'] = 0.00;
            $UserBankModule->InsertData($UserBank);
        }
        $MysqlWhere=" and UserID=$UserID";
        $UserBankFlowModule=new MemberUserBankFlowModule();
        $Rscount = $UserBankFlowModule->GetListsNum($MysqlWhere);
        $Page=intval($_POST['Page'])?intval($_POST['Page']):0;
        if ($Page < 1) {
            $Page = 1;
        }
        if ($Rscount['Num']) {
            $PageSize=6;
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($Page > $Data['PageCount']){
                $BankFlowList=array();
            }else{
                $Data['Page'] = min($Page, $Data['PageCount']);
                $Offset = ($Page - 1) * $Data['PageSize'];
                $MysqlWhere .=' order by AddTime desc';
                $BankFlowList = $UserBankFlowModule->GetLists($MysqlWhere,$Offset,$Data['PageSize']);
            }
            //操作类型
            $FlowType=$UserBankFlowModule->OperateType;
            //AJAX需要封装数据
            if($_POST){             
                $JsonData=array();
                foreach($BankFlowList as $Key=>$BankFlow){
                    $JsonData[$Key]['Title']=$FlowType[$BankFlow['OperateType']];
                    $JsonData[$Key]['AddTime']=date('Y-m-d H:i:s',$BankFlow['AddTime']);
                    $JsonData[$Key]['Money']=$BankFlow['Amt'];
                }
                if(count($JsonData)){
                    $ResultCode=200;
                    $Message='';
                }else{
                    $ResultCode=101;
                    $Message='没有更多数据了';
                }
            }
        }else{
            $ResultCode=102;
            $Message='没有数据';
        }
        //AJAX分页
        if($_POST){
            $json_result=array('ResultCode'=>$ResultCode,'Data'=>$JsonData,'RecordCount'=>$Rscount['Num'],'Message'=>$Message);
            echo json_encode($json_result);
        }else{
            $Title = '会员中心_我的资产 - 57美国网';
            include template('MuserMyWallet');
        }
    }
    
    /**
     * 提现
     */
    public function Withdraw(){
        MuserService::IsLogin();
        $UserID=$_SESSION['UserID'];
        $UserBankModule = new MemberUserBankModule();
        $UserBank = $UserBankModule->GetInfoByWhere(" and UserID=$UserID");
        //存在提现操作
        if(isset($_POST['Money']) && is_numeric($_POST['Money'])){
            if($_POST['Money']<=$UserBank['FreeBalance']){
                $UserBankWithdrawModule=new MemberUserBankWithdrawModule();
                $Data=array();
                $Data['UserID']=$UserID;
                $Data['Amt']=intval($_POST['Money']);
                $Data['Remarks']='支付宝:'.trim($_POST['Accounts']);
                $Data['FromIP']=GetIP();
                $Data['AddTime']=time();
                $Data['WithdrawStatus']=1;
                $Data['WithdrawAccounts']=trim($_POST['Accounts']);
                $Data['WithdrawType']=0;
                if ($_POST['Money']<=0){
                    $json_result=array('ResultCode'=>101,'Message'=>'提现余额不能为零');
                    echo json_encode($json_result);
                }
                global $DB;
                //开启事物
                $DB->query("BEGIN");
                $InResult=$UserBankWithdrawModule->InsertInfo($Data);
                if($InResult){
                    $UserBankData['FreeBalance']=$UserBank['FreeBalance']-$Data['Amt'];
                    $UserBankData['FrozenBalance']=$UserBank['FrozenBalance']+$Data['Amt'];
                    $UpResult=$UserBankModule->UpdateInfoByWhere($UserBankData, "UserID=$UserID");
                    if($UpResult){
                        //提交
                        $DB->query("COMMIT"); 
                        $json_result=array('ResultCode'=>200,'Message'=>'提现申请成功','Url'=>'/member/withdrawresult/');
                    }else{
                        //回滚
                        $DB->query("ROLLBACK");
                        $json_result=array('ResultCode'=>101,'Message'=>'提现失败,账户资金变更出错');
                    }
                }else{
                    //回滚
                    $DB->query("ROLLBACK");
                    $json_result=array('ResultCode'=>101,'Message'=>'提现申请失败');
                }
            }else{
                if($UserBank['FreeBalance']==0){
                    $json_result=array('ResultCode'=>101,'Message'=>'无可提现余额');
                }else{
                    $json_result=array('ResultCode'=>101,'Message'=>'提现金额不能大于可提现金额');
                }
            }
            echo json_encode($json_result);
        }else{
            $Title="会员中心_提现 - 57美国网";
            include template('MuserWithdraw');
        }
    }
    
    /**
     * 提现成功提示页面
     */
    public function WithdrawResult(){
        MuserService::IsLogin();
        $Title="会员中心_提现 - 57美国网";
        include template('MuserWithdrawResult');
    }
    
    /**
     * @desc  个人中心（我的匹配、个人资料、修改密码）
     */
    public function Information(){
        MuserService::IsLogin();
        $UserID = $_SESSION ['UserID'];
        $MemberUserModule=new MemberUserModule();
        $User=$MemberUserModule->GetInfoByKeyID($UserID);
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($UserID);
        $SexArr=$MemberUserInfoModule->SexArr;
        $Title="会员中心_个人资料 - 57美国网";
        include template('MuserInformation');
    }
    
    /**
     * @desc 常用信息编辑
     */
    public function EditInformation(){
        MuserService::IsLogin();
        $UserID = $_SESSION ['UserID'];
        $Type=$_GET['t'];
        $MemberUserModule=new MemberUserModule();
        $User=$MemberUserModule->GetInfoByKeyID($UserID);
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($UserID);
        switch ($Type){
            //昵称
            case 'nickname':
                $TemplateName="MuserEditInformationNickName";
                break;
            //真实姓名
            case 'realname':
                $TemplateName="MuserEditInformationRealName";
                break;
            //绑定手机
            case 'bindmobile':
                $TemplateName="MuserEditInforamtionEditMobile";
                break;
            //验证手机
            case 'verifymobile':
                $TemplateName="MuserEditInforamtionEditMobile";
                break;
            //绑定邮箱
            case 'bindmail': 
                $TemplateName="MuserEditInformationEditMail";
                break;
            //验证邮箱
            case 'verifymail':
                $TemplateName="MuserEditInformationEditMail";
                break;
            //性别
            case 'sex':
                $TemplateName="MuserEditInformationSex";
                break;
        }
        $Title='会员中心_个人资料 - 57美国网';
        include template($TemplateName);
    }

    /**
     * @desc 站内消息
     */
    public function MessageList()
    {
        MuserService::IsLogin();
        $MemberMessageSendModule = new MemberMessageSendModule();
        $MysqlWhere = ' and Status in (1,2) and UserID = '.$_SESSION['UserID'];
        $Rscount = $MemberMessageSendModule->GetListsNum($MysqlWhere);
        $Title = '会员中心_站内消息 - 57美国网';
        include template('MuserMessageList');
    }

    /**
     * @desc  个人中心（修改密码）
     */
    public function EditPassword(){
        MuserService::IsLogin();
        $Title='会员中心_修改密码 - 57美国网';
        include template('MuserEditPassword');
    }
    
    /**
     * @desc 我的收藏
     */
    public function Collection(){
        MuserService::IsLogin();
        $Type=$_GET['t']?$_GET['t']:'news';
        $Title='会员中心_我的收藏 - 57美国网';
        include template('MuserCollection');
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
        header("location:" . WEB_MUSER_URL);
    }

    
}