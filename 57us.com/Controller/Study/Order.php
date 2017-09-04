<?php
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class Order extends CommonController{
    
   public function __construct() {
   }

    /**
     * @desc 教师订单填写
     */
    public function Course(){
        $CourseID=intval($_GET['ID']);
        $CourseNum=intval($_GET['ProductNum']);
        $StudyTeacherCourseModule=new StudyTeacherCourseModule();
        $CourseInfo=$StudyTeacherCourseModule->GetInfoByKeyID($CourseID);
        if($CourseInfo){
            //培训科目
            $CourseType=$StudyTeacherCourseModule->CourseType;
            //上课方式
            $TeachType=$StudyTeacherCourseModule->TeachType;
            //班级规模
            $ClassSize=$StudyTeacherCourseModule->ClassSize;
            //顾问信息
            $MemberUserInfoModule=new MemberUserInfoModule();
            $UserInfo=$MemberUserInfoModule->GetInfoByUserID($CourseInfo['UserID']);
            //订单金额
            $Money=$CourseInfo['CoursePrice']*$CourseNum;
        }else{
            alertandback('不存在该课程');
        }
        $Title="美国留学_购买课程 - 57美国网";
        include template ('OrderCourse');
    }

    /**
     * @desc 顾问订单填写
     */
    public function Service(){
        $ServiceID=intval($_GET['ID']);
        $StudyConsultantServiceModule=new StudyConsultantServiceModule();
        $ServiceInfo=$StudyConsultantServiceModule->GetInfoByKeyID($ServiceID);
        if($ServiceInfo){
            //服务类型
            $ServiceType=$StudyConsultantServiceModule->ServiceType;
            //顾问信息
            $MemberUserInfoModule=new MemberUserInfoModule();
            $UserInfo=$MemberUserInfoModule->GetInfoByUserID($ServiceInfo['UserID']);
        }else{
            alertandback('不存在该服务');
        }
        $Title="美国留学_购买服务 - 57美国网";
        include template ('OrderService');
    }
   
    /**
     * @desc 创建服务订单
     */
    public function CreateOrder(){
        $UserModule = new MemberUserModule();
        $Mobile = trim($_POST['Mobile']);
        $VerifyCode = intval($_POST['VerifyCode']);
        if ($VerifyCode) {
            $Authentication = new MemberAuthenticationModule();
            $Validate = $Authentication->ValidateAccount($Mobile, $VerifyCode, 0);
            if ($Validate) {
                $Data = array('Mobile' => $Mobile, 'State' => 1, 'AddTime' => time());
                $UserID = $UserModule->InsertInfo($Data);
                if(!$UserID){
                    $json_result = array('ResultCode' => 101, 'Message' => '订单生成失败,发生异常，请重试', 'LogMessage' => '操作失败(联系人关联失败)');
                }
                else{
                    $UserInfoModule = new  MemberUserInfoModule();
                    $InfoData['UserID'] = $UserID;
                    $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                    $InfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
                    $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                    $InfoData['Sex'] = 1;
                    $InfoData['Avatar']='/img/man3.0.png';
                    $Result1 = $UserInfoModule->InsertInfo($InfoData);
                }
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '短信验证码错误', 'LogMessage' => '操作失败(短信验证码错误)');
            }
        } else {
            if ($_SESSION['UserID']) {
                $UserID = $_SESSION['UserID'];
            } else {
                $UserInfo = $UserModule->GetUserIDbyMobile($Mobile);
                $UserID = $UserInfo['UserID'];
            }
            if(!$UserID){
                $json_result = array('ResultCode' => 102, 'Message' => '订单生成失败,请输入验证码', 'LogMessage' => '操作失败(联系人关联失败)');
            }
        }
        $Intention=trim($_POST['Intention']);
        if($Intention && $UserID){
            $StudyOrderLogModule=new StudyOrderLogModule();
            $StudyOrderModule=new StudyOrderModule();
            //获取留学顾问服务的订单号
            $OrderData['OrderNum']= StudyService::GetConsultantOrderNumber();
            $OrderData['Status']=1;
            $OrderData['AddTime']=time();
            if($Intention=='OrdreConsultant'){
                //购买服务
                $ServiceID=intval($_POST['ProductId']);
                $StudyConsultantServiceModule=new StudyConsultantServiceModule();
                $ServiceInfo=$StudyConsultantServiceModule->GetInfoByWhere("and ServiceID=$ServiceID and `Status`=3");
                if($ServiceInfo){
                    $OrderData['UserID']=$UserID;
                    $OrderData['OrderName']=$ServiceInfo['ServiceName'];
                    $OrderData['ProductID']=$ServiceID;
                    $OrderData['Money']=$ServiceInfo['SalePrice'];
                    $OrderData['OrderType']=1;
                    $OrderData['RelationID']=$ServiceInfo['UserID'];
                    $OrderData['Contacts']=trim($_POST['Contacts']);
                    $OrderData['Tel']=$Mobile;
                    $OrderData['StudyTarget']=trim($_POST['Goal']);
                    $OrderData['StudyDate']=trim($_POST['StudyTime']);
                    $OrderData['LeaveMessage']=trim($_POST['Message']);
                    $OrderData['IsHesitate'] = 0;
                    $Result=$StudyOrderModule->InsertInfo($OrderData);
                    if($Result){
                        $json_result=array('ResultCode'=>200,'Message'=>'订单创建成功','Url'=>WEB_STUDY_URL."/order/{$OrderData['OrderNum']}.html");
                        $LogData['UserID']=$UserID;
                        $LogData['OrderNumber']=$OrderData['OrderNum'];
                        $LogData['OldStatus']=0;
                        $LogData['NewStatus']=1;
                        $LogData['Remarks']='创建订单,待支付';
                        $LogData['OperateTime']=time();
                        $LogData['IP']=GetIP();
                        $StudyOrderLogModule->InsertInfo($LogData);
                    }else{
                        $json_result=array('ResultCode'=>102,'Message'=>'订单生成失败');
                    }
                }else{
                    $json_result=array('ResultCode'=>103,'Message'=>'订单提交失败,该服务不存在或暂未上架');
                }
            }else{
                //购买课程
                $CourseID=intval($_POST['ProductId']);
                $CourseNum=intval($_POST['ProductNum']);
                $StudyTeacherCourseModule=new StudyTeacherCourseModule();
                $CourseInfo=$StudyTeacherCourseModule->GetInfoByWhere("and CourseID=$CourseID and `Status`=3");
                if($CourseInfo){
                    $OrderData['UserID']=$UserID;
                    $OrderData['OrderName']=$CourseInfo['CourseName'];
                    $OrderData['ProductID']=$CourseID;
                    $OrderData['Money']=$CourseInfo['CoursePrice']*$CourseNum;
                    $OrderData['CoursePrice']=$CourseInfo['CoursePrice'];
                    $OrderData['CoursePackage']=$CourseNum;
                    $OrderData['OrderType']=2;
                    $OrderData['RelationID']=$CourseInfo['UserID'];
                    $OrderData['Contacts']=trim($_POST['Contacts']);
                    $OrderData['Tel']=$Mobile;
                    $OrderData['StudyTarget']=trim($_POST['Goal']);
                    $OrderData['StudyDate']=trim($_POST['StudyTime']);
                    $OrderData['LeaveMessage']=trim($_POST['Message']);
                    $Result=$StudyOrderModule->InsertInfo($OrderData);
                    if($Result){
                        $json_result=array('ResultCode'=>200,'Message'=>'订单创建成功','Url'=>WEB_STUDY_URL."/order/{$OrderData['OrderNum']}.html");
                        $LogData['UserID']=$UserID;
                        $LogData['OrderNumber']=$OrderData['OrderNum'];
                        $LogData['OldStatus']=0;
                        $LogData['NewStatus']=1;
                        $LogData['Remarks']='创建订单,待支付';
                        $LogData['OperateTime']=time();
                        $LogData['IP']=GetIP();
                        $StudyOrderLogModule->InsertInfo($LogData);
                    }else{
                        $json_result=array('ResultCode'=>102,'Message'=>'订单生成失败');
                    }
                }else{
                    $json_result=array('ResultCode'=>103,'Message'=>'订单提交失败,该课程不存在或暂未上架');
                }                
            }            
        }
        echo json_encode($json_result);
    }
    /**
     * @desc 游学提交订单
     */
    public function StudyTourOrder(){
        $UserModule = new MemberUserModule();
        $OrderData['OrderNum']= StudyService::GetStrdyTourOrderNumber();
        $Phone = $_POST['Mobile'];
        $VerifyCode = intval($_POST['VerifyCode']);
        if ($VerifyCode) {
            $Authentication = new MemberAuthenticationModule();
            $Validate = $Authentication->ValidateAccount($Phone, $VerifyCode, 0);
            if ($Validate) {
                $Data = array('Mobile' => $Phone, 'State' => 1, 'AddTime' => time());
                $UserID = $UserModule->InsertInfo($Data);
                if(!$UserID){
                    $json_result = array('ResultCode' => 101, 'Message' => '订单生成失败', 'LogMessage' => '操作失败(联系人关联失败)');
                }else{
                    $UserInfoModule = new  MemberUserInfoModule();
                    $InfoData['UserID'] = $UserID;
                    $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                    $InfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
                    $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                    $InfoData['Sex'] = 1;
                    $InfoData['Avatar']='/img/man3.0.png';
                    $Result1 = $UserInfoModule->InsertInfo($InfoData);
                }
                $json_result = $this->Operate($OrderData['OrderNum'],$UserID,$_POST);
            } else {
                $json_result = array('ResultCode' => 100,'Message' => '短信验证码错误','LogMessage'=>'操作失败');
            }
        } else {
            if ($_SESSION['UserID']) {
                $UserID = $_SESSION['UserID'];
            }else{
                $UserInfo = $UserModule->GetUserIDbyMobile($Phone);
                $UserID = $UserInfo['UserID'];
            }
            $json_result = $this->Operate($OrderData['OrderNum'],$UserID,$_POST);
        }
        //添加订单操作日志
        $StudyOrderLogModule = new StudyOrderLogModule();
        $LogData = array('OrderNumber'=>$OrderData['OrderNum'],'UserID'=>$UserID,'Remarks'=>$json_result['Message'],'OldStatus'=>0,'NewStatus'=>1,'OperateTime'=>time(),'IP'=>GetIP(),'Type'=>3);
        $StudyOrderLogModule->InsertInfo($LogData);
        echo json_encode($json_result);exit;
    }

    /**
     * @desc 游学订单实际操作
     */
    private function Operate($OrderNumber,$UserID,$Post){
        if ($Post) {
            $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
            $StudyYoosureModule = new StudyYoosureModule ();
            //下订单
            $InsertInfo ['YoosureID'] = $Post ['YoosureID'];
            $StudyYoosureInfo = $StudyYoosureModule->GetInfoByKeyID ( $InsertInfo ['YoosureID'] );
            if (empty ( $StudyYoosureInfo )) {
                $JsonResult = array ('ResultCode' => 103, 'Message' => '产品不存在!','LogMessage'=>'操作失败(产品不存在)');
            }else{
                $NowTime = time();
                $InsertInfo ['UserID'] = $UserID;
                $InsertInfo ['OrderNum'] = $OrderNumber;
                $InsertInfo ['OrderName'] = $StudyYoosureInfo ['Title'];
                $InsertInfo ['Mobile'] = $Post ['Mobile'];
                $InsertInfo ['CreateTime'] = date ( "Y-m-d H:i:s",$NowTime );
                $InsertInfo ['UpdateTime'] = date ( "Y-m-d H:i:s",$NowTime );
                $InsertInfo ['ExpirationTime'] = date("Y-m-d H:i:s",$NowTime+900);
                $InsertInfo ['PaymentMethod'] = 0;
                $InsertInfo ['IP'] = GetIP ();
                $InsertInfo ['Status'] = 1;
                $InsertInfo ['Contact'] = trim($Post ['Contacts']);//联系人
                $InsertInfo ['GoDate'] = $Post ['Date'];//去游学时间
                $InsertInfo ['Email'] = $Post ['Email'];//邮箱
                $InsertInfo ['Num'] = $Post ['Num'];
                $InsertInfo ['OneMoney'] = $StudyYoosureInfo ['Price'];
                $InsertInfo ['Money'] = $StudyYoosureInfo ['Price'] * $InsertInfo ['Num']; //金额
                $InsertInfo ['Message'] = $Post ['Message'];
                //出行人信息
                $TravelerInformation = array();
                foreach ($Post ['Travellers'] as $key=>$value){
                    $TravelerInformation[$key]['Name'] = $value['lvname'];
                    if ($value['yesnohz']==1){
                        $TravelerInformation[$key]['PassPort'] = '';
                    }elseif ($value['yesnohz']==0){
                        $TravelerInformation[$key]['PassPort'] = $value['hz'];
                    }
                }
                $InsertInfo ['TravelerInformation'] = json_encode($TravelerInformation,JSON_UNESCAPED_UNICODE);
                //出行人信息
                $IsOk = $StudyYoosureOrderModule->InsertInfo ( $InsertInfo );
                if ($IsOk) {
                    //发送给用户
                    ToolService::SendSMSNotice($InsertInfo ['Mobile'], '【57美国网】您购买的“'. $InsertInfo ['OrderName'] .'”订单已经提交，离美国游学还差最后一步啦，请及时支付。');
                    //发送给运营
                    ToolService::SendSMSNotice(15659827860,$InsertInfo ['Contact'].'用户,已产生游学订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['Contact'].' ，联系电话：'.$InsertInfo ['Mobile'].'。');
                    ToolService::SendSMSNotice(15160090744,$InsertInfo ['Contact'].'用户,已产生游学订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['Contact'].' ，联系电话：'.$InsertInfo ['Mobile'].'。');
                    ToolService::SendSMSNotice(15980805724,$InsertInfo ['Contact'].'用户,已产生游学订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['Contact'].' ，联系电话：'.$InsertInfo ['Mobile'].'。');
                    $JsonResult=array('ResultCode'=>200,'Message'=>'订单创建成功','Url'=>WEB_STUDY_URL."/order/{$InsertInfo['OrderNum']}.html");
                } else {
                    $JsonResult = array ('ResultCode' => 100, 'Message' => '提交订单失败,请重试!','LogMessage'=>'操作失败' );
                }
            }
        } else {
            $JsonResult = array ('ResultCode' => 101, 'Message' => '非法数据！','LogMessage'=>'操作失败(提交数据有误)');
        }
        return $JsonResult;
    }
    /**
     * @desc 选择支付方式
     */    
    public function ChoicePay(){
        $OrderNum=trim($_GET['ID']);
        $StudyOrderModule=new StudyOrderModule();
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $OrderInfo=$StudyOrderModule->GetInfoByWhere("and OrderNum='$OrderNum'");
        $YoosureOrderInfo = $StudyYoosureOrderModule->GetInfoByWhere("and OrderNum='$OrderNum'");
        if($OrderInfo){//选择顾问服务订单支付页面
            $MemberUserInfoModule=new MemberUserInfoModule();
            $UserInfo=$MemberUserInfoModule->GetInfoByUserID($OrderInfo['RelationID']);
            if($OrderInfo['OrderType']==1){
                $StudyConsultantServiceModule=new StudyConsultantServiceModule();
                $ServiceInfo=$StudyConsultantServiceModule->GetInfoByKeyID($OrderInfo['ProductID']);
                $ServiceType=$StudyConsultantServiceModule->ServiceType;
                $TplName="ConsultantChoicePay";
            }else{//选择教师课程订单支付页面
                $StudyTeacherCourseModule=new StudyTeacherCourseModule();
                $CourseInfo=$StudyTeacherCourseModule->GetInfoByKeyID($OrderInfo['ProductID']);          
                $TplName="TeacherChoicePay";
            }
            include template($TplName);            
        }elseif(!$OrderInfo &&$YoosureOrderInfo){//选择游学订单支付页面
            $TplName="StudyTourChoicePay";
            include template($TplName);
        }else{
            alertandgotopage("不存在该订单", WEB_STUDY_URL);
        }
    }
    
    /**
     * @desc 前往支付
     */    
    public function Pay()
    {
        $Type = trim($_GET['Type']);
        $OrderNo = trim($_GET['ID']);
        //服务订单
        $StudyOrderModule = new StudyOrderModule();
        $Order = $StudyOrderModule->GetInfoByWhere("and OrderNum='$OrderNo'");
        //游学订单
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $YoosureOrder = $StudyYoosureOrderModule->GetInfoByWhere("and OrderNum='$OrderNo'");
        if ($Order && $Order['Status'] == 1) {  //服务订单支付
            if ($Type == 'alipay') {
                $Data['OrderNo'] = $Order['OrderNum'];
                $Data['Subject'] = html_entity_decode($Order['OrderName'], ENT_QUOTES);
                $Data['Money'] = $Order['Money'];
                $Data['Body'] = html_entity_decode($Order['OrderName'], ENT_QUOTES);
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/paystudy/payreturn/';
                $Data['NotifyUrl'] = WEB_MEMBER_URL . '/paystudy/payreturn/';
                if($Order['OrderType']==1){
                    $Data['ProductUrl'] = WEB_STUDY_URL . "/consultant_service/{$Order['ProductID']}.html";
                }else{
                    $Data['ProductUrl'] = WEB_STUDY_URL . "/consultant_course/{$Order['ProductID']}.html";
                }
                $Data['RunTime'] = time();
                $Data['Sign'] = $this->VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/alipay/', $Data);
            } elseif ($Type == 'wxpay') {
                $Data['OrderNo'] = $Order['OrderNum'];
                $Data['Subject'] = html_entity_decode($Order['OrderName'], ENT_QUOTES);
                $Data['Money'] = $Order['Money'];
                $Data['Body'] = html_entity_decode($Order['OrderName']);
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/paystudy/payreturn/';
                $Data['RunTime'] = time();
                $Data['Sign'] = $this->VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/wxpay/', $Data);
            }
        }elseif(!$Order && $YoosureOrder && $YoosureOrder['Status'] == 1){//游学订单支付
            if ($Type == 'alipay') {
                $Data['OrderNo'] = $YoosureOrder['OrderNum'];
                $Data['Subject'] = html_entity_decode($YoosureOrder['OrderName'], ENT_QUOTES);
                $Data['Money'] = $YoosureOrder['Money'];
                $Data['Body'] = html_entity_decode($YoosureOrder['OrderName'], ENT_QUOTES);
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/paystudy/studytourpayreturn/';//游学订单支付返回
                $Data['NotifyUrl'] = WEB_MEMBER_URL . '/paystudy/studytourpayreturn/';//游学订单支付返回
                $Data['ProductUrl'] = WEB_STUDY_URL . "/studytour/{$YoosureOrder['YoosureID']}.html";
                $Data['RunTime'] = time();
                $Data['Sign'] = $this->VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/alipay/', $Data);
            } elseif ($Type == 'wxpay') {
                $Data['OrderNo'] = $YoosureOrder['OrderNum'];
                $Data['Subject'] = html_entity_decode($YoosureOrder['OrderName'], ENT_QUOTES);
                $Data['Money'] = $YoosureOrder['Money'];
                $Data['Body'] = html_entity_decode($YoosureOrder['OrderName']);
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/paystudy/studytourpayreturn/';//游学订单支付返回
                $Data['RunTime'] = time();
                $Data['Sign'] = $this->VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/wxpay/', $Data);
            }
        } else {
            alertandback('不能操作的订单');
        }
    }

    /**
     * @desc  支付成功提示
     */
    public function Result()
    {
        $sign = $_POST['Sign'];
        $Message = $_POST['Message'];
        unset($_POST['Sign']);
        unset($_POST['Message']);
        $VerifySign = $this->VerifyData($_POST);
        if ($VerifySign == $sign) {
            $PayResult = $_POST['PayResult'];
            if ($PayResult == 'SUCCESS') {
                $OrderNo = $_POST['OrderNo'];
                $Money = $_POST['Money'];
                $RedirectUrl = $_POST['RedirectUrl'];
                include template('OrderPaySuccess');
            } else {
                include template('OrderPayFAIL');
            }
        } else {
            include template('OrderPayFAIL');
        }
    }

    /**
     * @desc  数据验证
     * @param $para
     * @return bool|string
     */
    private function VerifyData($para)
    {
        if (!is_array($para)) {
            return false;
        } else {
            $arg = "";
            while (list ($key, $val) = each($para)) {
                $arg .= $key . "=" . $val . "&";
            }
            // 去掉最后一个&字符
            $arg = substr($arg, 0, count($arg) - 2);

            // 如果存在转义字符，那么去掉转义
            if (get_magic_quotes_gpc()) {
                $arg = stripslashes($arg);
            }
            $SignKey = '57us3cjq29vcu38cn2q0dj01d9c57is7';
            $sign = md5($arg . $SignKey);
            return $sign;
        }
    }    
}
