<?php
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class CommonAjax extends CommonController{

    public function __construct(){
        //$this->MemberLoginStatus();
    }
    
    public function Index(){
        $Intention = trim($_POST ['Intention']);
        unset($_POST ['Intention']);
        if ($Intention == '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            echo  $json_result;
            exit;
        }
        $this->$Intention ();
    }

    /**
     * @desc 判断手机号码是否注册
     */
    public function JudgeIsRegister()
    {
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
        $Data ['Type'] = 0;
        $Authentication = new MemberAuthenticationModule ();
        $ID = $Authentication->searchAccount($Data ['Account']);
        if ($ID) {
            $result = $Authentication->UpdateUser($Data, $ID);
        } else {
            $result = $Authentication->InsertUser($Data);
        }
        if ($result) {
            $result = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
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
     * @desc  资金提现
     */
    private function CustomerManageAssets(){
        //判断是否登录
        $this->MemberLoginStatus();
        $UserBankFlowModule = new MemberUserBankFlowModule();
        $MemberUserBankModule = new MemberUserBankModule();
        $UserID = $_SESSION['UserID'];
        if ($_POST){
            $alipaymember = trim($_POST['alipaymember']);
            $money = trim($_POST['money']);
            $UserBank = $MemberUserBankModule->GetInfoByWhere(' and UserID = '.$UserID);
            if ($money>$UserBank['FreeBalance']){
                $json_result=array('ResultCode'=>102,'Message'=>'提现金额超出余额');
                echo json_encode($json_result);exit;
            }
            $Data['Remarks'] = '提现支付宝账号：'.$alipaymember.',提现金额：'.$money.',待操作。';
            $Data['UserID'] = $UserID;
            if ($alipaymember=='' || $money ==''){
                $json_result=array('ResultCode'=>201,'Message'=>'必填项未填');
                echo json_encode($json_result);exit;
            }
            $UserBankInfo = $UserBankFlowModule->GetInfoByWhere(' and UserID = '.$UserID.' ORDER BY AddTime DESC');
            $Data['Amount'] = intval($UserBankInfo['Amount'])-$money;
            $Data['Amt'] = $money;
            $Data['OperateType'] =3;
            $Data['FromIP'] = GetIP();
            $Data['AddTime'] = date("Y-m-d H:i:s",time());
            $Data['Type'] =2;
            $insert = $UserBankFlowModule->InsertInfo($Data);
            $DataInfo['FreeBalance'] = $UserBank['FreeBalance']-$money;
            $Update = $MemberUserBankModule->UpdateInfoByWhere($DataInfo,' UserID = '.$UserID);
            $Mobile ='15659827860';
            $result = ToolService::SendSMSNotice($Mobile, '站内的客服，有用户申请提现，会员ID: '.$UserID .' ，提现支付宝账号：'.$alipaymember.',提现金额：￥'.$money.' ,请尽快核实账户信息并为该客户转账提现。谢谢！');
            if ($insert && $Update){
                $json_result=array('ResultCode'=>200,'Message'=>'申请提现成功');
            }else{
                $json_result=array('ResultCode'=>202,'Message'=>'申请提现失败');
            }
        }else{
            $json_result=array('ResultCode'=>101,'Message'=>'无提现数据');
        }
        echo json_encode($json_result);
    }
    
    /**
     * @desc  首页获取申请方案
     */    
    private function IndexApply(){
        $Data['ApplyType']=trim($_POST['Project']);
        $Data['Grade']=trim($_POST['Grade']);
        $Data['Results']=trim($_POST['Results']);
        $Data['Tel']=trim($_POST['Tel']);
        $Data['AddTime']=time();
        $StudyApplySchemeModule=new StudyApplySchemeModule();
        $result=$StudyApplySchemeModule->InsertInfo($Data);
        if($result){
            //发送给客户
            ToolService::SendSMSNotice($Data['Tel'], "您好！我们已经收到了您的评估申请，我们的老师正在加班加点帮您拟定申请方案，我们将在1-2个工作日内给您反馈，谢谢！");
            //发送给运营
            ToolService::SendSMSNotice('15659827860', "手机{$Data['Tel']}号提交了申请评估信息，请马上跟进处理。申请项目：{$Data['ApplyType']}，目前：{$Data['Grade']}，国内平均绩点：{$Data['Results']}。请尽快联系用户。");
            ToolService::SendSMSNotice('15980805724', "手机{$Data['Tel']}号提交了申请评估信息，请马上跟进处理。申请项目：{$Data['ApplyType']}，目前：{$Data['Grade']}，国内平均绩点：{$Data['Results']}。请尽快联系用户。");
            $json_result=array('ResultCode'=>200,'Message'=>'提交成功，57us专业顾问将于1-2个工作日联系你。');
        }else{
            $json_result=array('ResultCode'=>101,'Message'=>'提交失败');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  用户身份选择 1-学生，2-顾问，3-老师
     */
    public function IdentitySelection(){
        $UserID = $_SESSION['UserID'];
        $MemberUserInfoModule = new MemberUserInfoModule();
        if ($_POST){
            $Data['Identity'] = intval($_POST['Type']);
            $UpdateIdentity = $MemberUserInfoModule->UpdateInfoByWhere($Data,' UserID = '.$UserID);
            if (($UpdateIdentity || $UpdateIdentity===0) && $Data['Identity']==1){
                $json_result=array('ResultCode'=>200,'Url'=>WEB_STUDY_URL.'/studentmanage/','Message'=>'选择成功');
            }elseif (($UpdateIdentity || $UpdateIdentity===0) && $Data['Identity']==2){
                $json_result=array('ResultCode'=>200,'Url'=>WEB_STUDY_URL.'/consultantmanage/mycenter/','Message'=>'选择成功');
            }elseif (($UpdateIdentity || $UpdateIdentity===0) && $Data['Identity']==3){
                $json_result=array('ResultCode'=>200,'Url'=>WEB_STUDY_URL.'/teachermanage/mycenter/','Message'=>'选择成功');
            }else{
                $json_result=array('ResultCode'=>101,'Url'=>WEB_STUDY_URL.'/commoncontroller/identityselection','Message'=>'选择失败');
            }
            echo json_encode($json_result);
        }
    }
}
