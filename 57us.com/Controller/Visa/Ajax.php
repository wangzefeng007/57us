<?php
class Ajax {
    public function Index() {
        $Intention = trim ( $_POST ['Intention'] );
        if ($Intention == '') {
            $json_result = array( 'ResultCode' => 500,'Message' => '系統錯誤','Url' => '');
            echo json_encode($json_result);
            exit;
        }
        $this->$Intention ();
    }

    public function SubEmail() {
        if ($_POST) {
            $VisaID = intval ( $_POST ['ID'] );
            $TypeName = $_POST ['typeName'];
            $TypeName = implode ( ',', $TypeName );
            $Email = $_POST ['email'];
            $VisaProducModule = new VisaProducModule ();
            $VisaInfo = $VisaProducModule->GetInfoByKeyID ( $VisaID );
            if (count ( $VisaInfo ) == 0) {
                $JsonResult = array ('ResultCode' => 101, 'Message' => '产品不存在！' );
                EchoResult ( $JsonResult );
            }
            $MaterialRequested = json_decode ( $VisaInfo ['MaterialRequested'], true );
            $NewMaterialRequested = '';
            foreach ( $MaterialRequested ['Title'] as $Key => $Value ) {
                if (strstr ( $TypeName, $Value )) {
                    $NewMaterialRequested .= '<strong>'.$Value.'</strong><br>'.$MaterialRequested ['MaterialRequested'] [$Key].'<hr><br>';
                }
            }
            if(ToolService::SendEMailNotice($Email, $VisaInfo ['Title'] . '签证资料', $NewMaterialRequested)){
                $JsonResult = array ('ResultCode' => 200, 'Message' => '发送成功！' );
            }else{
                $JsonResult = array ('ResultCode' => 100, 'Message' => '发送失败！' );
            }
            EchoResult ( $JsonResult );
        }
    }

    /**
     * @desc 提交订单
     */
    public function VisaOrder() {
        $UserModule = new MemberUserModule();
        $OrderNumber = VisaService::GetVisaOrderNumber();
        $Phone = $_POST['Phone'];
        $Mobile = $_POST['Mobile'];
        $VerifyCode = intval($_POST['VerifyCode']);
        $City = explode('-',$_POST ['City']);
        $_POST ['Province'] = $City[0];
        $_POST ['City'] = $City[1];
        $_POST ['Area'] = $City[2];
        if ($VerifyCode) {
            $Authentication = new MemberAuthenticationModule();
            $Validate = $Authentication->ValidateAccount($Mobile, $VerifyCode, 0);
            if ($Validate) {
                $Data = array('Mobile' => $Mobile, 'State' => 1, 'AddTime' => time());
                $UserID = $UserModule->InsertInfo($Data);
                if(!$UserID){
                    $json_result = array('ResultCode' => 101, 'Message' => '订单生成失败', 'LogMessage' => '操作失败(联系人关联失败)');
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
                //确认订单
                $json_result = $this->Operate($OrderNumber,$UserID,$_POST);
            } else {
                $json_result = array('ResultCode' => 100,'Message' => '短信验证码错误','LogMessage'=>'操作失败');
            }
        } else {
            if ($_SESSION['UserID']) {
                $UserID = $_SESSION['UserID'];
            }
            else{
                $UserInfo = $UserModule->GetUserIDbyMobile($Mobile);
                $UserID = $UserInfo['UserID'];
            }
            //确认订单
            $json_result = $this->Operate($OrderNumber,$UserID,$_POST);
        }
        //添加订单操作日志
        $OrderLogModule = new TourProductOrderLogModule();
        $LogData = array('OrderNumber'=>$OrderNumber,'UserID'=>$UserID,'Remarks'=>$json_result['LogMessage'],'OldStatus'=>0,'NewStatus'=>1,'OperateTime'=>date("Y-m-d H:i:s",time()),'IP'=>GetIP(),'Type'=>2);
        $OrderLogModule->InsertInfo($LogData);
        echo json_encode($json_result);exit;
    }

    /**
     * @desc 订单实际操作
     */
    private function Operate($OrderNumber,$UserID,$Post){
        if ($Post) {
            $VisaOrderModule = new VisaOrderModule ();
            $VisaProducModule = new VisaProducModule ();
            //下订单
            $InsertInfo ['VisaID'] = $Post ['VisaID'];
            $VisaInfo = $VisaProducModule->GetInfoByKeyID ( $InsertInfo ['VisaID'] );
            if (empty ( $VisaInfo )) {
                $JsonResult = array ('ResultCode' => 103, 'Message' => '产品不存在!','LogMessage'=>'操作失败(产品不存在)');
            }
            else{
                $NowTime = time();
                $InsertInfo ['OrderNumber'] = $OrderNumber;
                $InsertInfo ['OrderName'] = $VisaInfo ['Title'].'-'.$VisaInfo ['Package'];
                $InsertInfo ['UserID'] = $Post ['UserID'];
                $InsertInfo ['UserName'] = $Post ['Contacts'];
                $InsertInfo ['Phone'] = $Post ['Mobile'];
                $InsertInfo ['Email'] = $Post ['Email'];
                $InsertInfo ['Province'] = $Post ['Province'];
                $InsertInfo ['City'] = $Post ['City'];
                $InsertInfo ['Area'] = $Post ['Area'];
                $InsertInfo ['Address'] = $InsertInfo ['Province'] . $InsertInfo ['City'] . $InsertInfo ['Area']  .$Post['Address'];
                $InsertInfo ['CreateTime'] = date ( "Y-m-d H:i:s",$NowTime );
                $InsertInfo ['UpdateTime'] = date ( "Y-m-d H:i:s",$NowTime );
                $InsertInfo ['PaymentMethod'] = 0;
                $InsertInfo ['IP'] = GetIP ();
                $InsertInfo ['Status'] = 1;
                $InsertInfo ['GoDate'] = $Post ['Time'];
                $InsertInfo ['Num'] = $Post ['Number'];
                $InsertInfo ['OneMoney'] = $VisaInfo ['PresentPrice'];
                $InsertInfo ['Money'] = $VisaInfo ['PresentPrice'] * $InsertInfo ['Num']; //金额
                $InsertInfo ['ExpirationTime'] = date("Y-m-d H:i:s",$NowTime+900);
                $InsertInfo ['UserID'] = $UserID;
                $IsOk = $VisaOrderModule->InsertInfo ( $InsertInfo );
                if ($IsOk) {
                    ToolService::SendSMSNotice(15160090744, '已产生签证订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['UserName'].' ，联系电话：'.$InsertInfo ['Phone'].'。');
                    ToolService::SendSMSNotice(18750258578, '已产生签证订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['UserName'].' ，联系电话：'.$InsertInfo ['Phone'].'。');
                    ToolService::SendSMSNotice(18050016313, '已产生签证订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['UserName'].' ，联系电话：'.$InsertInfo ['Phone'].'。');
                    ToolService::SendSMSNotice(15980805724, '已产生签证订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['UserName'].' ，联系电话：'.$InsertInfo ['Phone'].'。');
                    $JsonResult = array ('ResultCode' => 200, 'Message' => '下单成功', 'Url' => WEB_VISA_URL.'/visapay/' . $OrderNumber . '.html','LogMessage'=>'操作成功' );
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
     * @desc 判断手机号码是否注册
     */
    public function JudgeIsRegister (){
        $UserModule = new MemberUserModule();
        $Mobile = trim($_POST['Mobile']);
        $UserID = $UserModule->GetUserIDbyMobile($Mobile);
        if($UserID){
            $json_result = array('ResultCode'=>'200');
        }
        else{
            $json_result = array('ResultCode'=>'100');
        }
        echo json_encode($json_result);exit;
    }

    /**
     * @desc  发送手机验证码，验证手机
     */
    public function ValidateMobileCode(){
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
        echo json_encode ( $json_result );
    }
}
