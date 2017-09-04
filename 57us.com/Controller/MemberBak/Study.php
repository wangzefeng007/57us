<?php
/**
 * 留学订单支付
 */
class Study
{
    public function __construct()
    {
    }

    /**
     * @desc 数据验证
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
            //去掉最后一个&字符
            $arg = substr($arg, 0, count($arg) - 2);
            //如果存在转义字符，那么去掉转义
            if (get_magic_quotes_gpc()) {
                $arg = stripslashes($arg);
            }
            $SignKey = '57us3cjq29vcu38cn2q0dj01d9c57is7';
            $sign = md5($arg . $SignKey);
            return $sign;
        }
    }

    /**
     * @desc  订单支付返回
     */
    public function PayReturn()
    {
        $Result['OrderNo'] = trim($_GET ['OrderNo']);
        $Result['Money'] = trim($_GET ['Money']);
        $Result['PayType'] = trim($_GET ['PayType']);
        $Result['ResultCode'] = trim($_GET ['ResultCode']);
        $Result['RunTime'] = trim($_GET ['RunTime']);
        $StudyOrderModule = new StudyOrderModule();
        $OrderInfo = $StudyOrderModule->GetInfoByWhere("and OrderNum='{$Result['OrderNo']}'");
        $Sign = trim($_GET ['Sign']);
        unset ($_GET);
        $MySign = $this->VerifyData($Result);
        if ($MySign == $Sign) {
            $ResultUrl = WEB_STUDY_URL . '/order/result/';
            if ($Result['ResultCode'] == 'SUCCESS') {
                $LogMessage = '支付成功';
                $LogData['NewStatus']=2;
                if ($OrderInfo ['Status'] == '1' && $OrderInfo ['Money'] == $Result['Money']) {
                    $Data ['Status'] = '2';
                    if ($Result['PayType'] == '支付宝') {
                        $Data ['PayType'] = 1;
                    } elseif ($Result['PayType'] == '微信支付') {
                        $Data ['PayType'] = 2;
                    } elseif ($Result['PayType'] == '银联支付') {
                        $Data ['PayType'] = 3;
                    }
                    $Data['UpdateTime']=time();
                    $StudyConsultantServiceModule=new StudyConsultantServiceModule();
                    $ServiceInfo=$StudyConsultantServiceModule->GetInfoByKeyID($OrderInfo['ProductID']);
                    //犹豫期
                    if($ServiceInfo['ServiceType']==1 || $ServiceInfo['ServiceType']==2){
                        $Data['ConsiderTime']=$Data['UpdateTime']+3600*72;
                        $Data['IsHesitate']=1;
                    }
                    $UpdateResult=$StudyOrderModule->UpdateInfoByWhere($Data, "OrderNum='{$Result['OrderNo']}'");
                    if($UpdateResult){
                        if($OrderInfo['OrderType']==1){
                            //更新销量
                            $UpdateData['SaleNum']=$ServiceInfo['SaleNum']+1;
                            $StudyConsultantServiceModule->UpdateInfoByKeyID($UpdateData,$OrderInfo['ProductID']);
                            if($ServiceInfo['ServiceType'] == 7){ //背景提升
                                //顾问服务流程
                                $StudyOrderConsultantModule=new StudyOrderConsultantModule();
                                //资金及日志操作
                                $ConsultantInfoModule = new StudyConsultantInfoModule();
                                $Scale = $ConsultantInfoModule->Scale;
                                $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$OrderInfo['RelationID']);
                                //直接全款*顾问应获比例
                                $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']];//操作金额,当前服务的订单部分金额
                                $FlowData = array(
                                    'OrderID'=>$OrderInfo['OrderID'],
                                    'UserID'=>$OrderInfo['UserID'],
                                    'RelationID'=>$OrderInfo['RelationID'],
                                    'Type'=>7,
                                    'TypeName'=>$StudyOrderConsultantModule->Part[7]['Headline'],
                                    'Status'=>3,
                                    'ConfirmTime'=>time(),
                                    'Amt'=>$Amt
                                );
                                $StudyOrderModule->UpdateInfoByWhere(array('Status'=>3), "OrderNum='{$Result['OrderNo']}'");
                                //添加订单流程表结算金额
                                $ConsultantModule = new StudyOrderConsultantModule();
                                $ConsultantModule->InsertInfo($FlowData);
                                //资金及资金日志操作
                                $BankFlowModule = new MemberUserBankFlowModule();
                                $BankModule = new MemberUserBankModule();
                                //操作资金
                                $BankInfo = $BankModule->GetInfoByWhere(' and UserID='.$OrderInfo['RelationID']);
                                //操作后的总金额
                                $Amount = $BankInfo['TotalBalance']+$Amt;
                                $BankIsOk = $BankModule->UpdateInfoByKeyID(array('TotalBalance'=>$Amount,'FreeBalance'=>$BankInfo['FreeBalance']+$Amt),$BankInfo['BankID']);
                                if($BankIsOk){
                                    //操作资金记录,OperateType=>系统入账,Type=>留学
                                    $BankFlowData = array('FromIP'=>GetIP(),'UserID'=>$OrderInfo['RelationID'],'Amount'=>$Amount,'Amt'=>$Amt,'OperateType'=>4,'Remarks'=>$OrderInfo['OrderName'].'-背景提升','Type'=>2,'AddTime'=>date("Y-m-d H:i:s",time()));
                                    $BankFlowIsOK = $BankFlowModule->InsertInfo($BankFlowData);
                                    if(!$BankFlowIsOK){
                                        $LogMessage="支付成功(创建资金日志失败)";
                                    }
                                }
                                else{
                                    $LogMessage="支付成功(资金表更新失败)";
                                }
                            }
                            else{
                                //判断该学生是否还有在办订单
                                $StudyConsultantStudentInfoModule=new StudyConsultantStudentInfoModule();
                                $RelationInfo=$StudyConsultantStudentInfoModule->GetInfoByWhere("and StudentID={$OrderInfo['UserID']} and ConsultantID={$OrderInfo['RelationID']}");
                                if(!$RelationInfo){
                                    $MemberUserInfoModule=new MemberUserInfoModule();
                                    $UserInfo=$MemberUserInfoModule->GetInfoByUserID($OrderInfo['UserID']);
                                    $StudyConsultantStudentInfoModule->InsertInfo(array('StudentID'=>$OrderInfo['UserID'],'ConsultantID'=>$OrderInfo['RelationID'],'StudentName'=>$UserInfo['NickName'],'Tel'=>$OrderInfo['Tel'],'AddTime'=>time(),'IsComplete'=>1));
                                }
                                elseif($RelationInfo['IsComplete'] == 2){
                                    $StudyConsultantStudentInfoModule->UpdateInfoByWhere(array('IsComplete'=>1),' StudentID='.$OrderInfo['UserID'].' and ConsultantID ='.$OrderInfo['RelationID']);
                                }
                                //顾问服务流程
                                $StudyOrderConsultantModule=new StudyOrderConsultantModule();
                                $FlowArr=$StudyOrderConsultantModule->Flow[$ServiceInfo['ServiceType']];
                                $FlowData=array();
                                global $DB;
                                $DB->query("BEGIN");//开始事务定义
                                $InsertFlowResult=true;
                                foreach($FlowArr as $key=>$val){
                                    $FlowData['OrderID']=$OrderInfo['OrderID'];
                                    $FlowData['UserID']=$OrderInfo['UserID'];
                                    $FlowData['RelationID']=$OrderInfo['RelationID'];
                                    $FlowData['Type']=$val;
                                    $FlowData['TypeName']=$StudyOrderConsultantModule->Part[$val]['Headline'];
                                    if($key==0){
                                        $FlowData['Status']=1;
                                    }else{
                                        $FlowData['Status']=0;
                                    }
                                    $FlowResult=$StudyOrderConsultantModule->InsertInfo($FlowData);
                                    if(!$FlowResult){
                                        $InsertFlowResult=false;
                                    }
                                }
                                if($InsertFlowResult){
                                    $DB->query("COMMIT");//执行事务
                                }else{
                                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                                    $LogMessage="支付成功(创建服务流程失败)";
                                }
                            }
                        }elseif($OrderInfo['OrderType']==2){
                            //更新销量
                            $StudyTeacherCourseModule=new StudyTeacherCourseModule();
                            $CourseInfo=$StudyTeacherCourseModule->GetInfoByKeyID($OrderInfo['ProductID']);
                            $UpdateData['SaleNum']=$CourseInfo['SaleNum']+1;
                            $StudyTeacherCourseModule->UpdateInfoByKeyID($UpdateData,$OrderInfo['ProductID']);
                        }
                    }else{
                        $LogMessage='支付成功(订单状态更新失败)';
                    }
                }
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['Money'] = $OrderInfo ['Money'];
                $ShowData ['PayResult'] = 'SUCCESS';
                $ShowData ['RedirectUrl'] = WEB_STUDY_URL . '/studentmanage/myorder/';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = $this->VerifyData($ShowData);
            } else {
                $LogMessage = '支付失败';
                $LogData['NewStatus']=1;
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['PayType'] = $Result ['PayType'];
                $ShowData ['Money'] = $Result ['Money'];
                $ShowData ['PayResult'] = 'FAIL';
                $ShowData ['RedirectUrl'] = WEB_STUDY_URL . '/studentmanage/myorder/';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = $this->VerifyData($ShowData);
            }
            //添加订单日志
            $LogData['UserID']=$OrderInfo['UserID'];
            $LogData['OrderNumber']=$Result['OrderNo'];
            $LogData['Remarks']=$LogMessage;
            $LogData['OperateTime']=date('Y-m-d H:i:s',$ShowData ['RunTime']);
            $LogData['IP']=GetIP();
            $LogData['OldStatus']=1;
            $StudyOrderLogModule=new StudyOrderLogModule();
            $StudyOrderLogModule->InsertInfo($LogData);
			echo ToolService::PostForm($ResultUrl, $ShowData);
        }
    }

    /**
     * @desc  游学订单支付返回
     */

    public function StudyTourPayReturn()
    {
        $Result['OrderNo'] = trim($_GET ['OrderNo']);
        $Result['Money'] = trim($_GET ['Money']);
        $Result['PayType'] = trim($_GET ['PayType']);
        $Result['ResultCode'] = trim($_GET ['ResultCode']);
        $Result['RunTime'] = trim($_GET ['RunTime']);
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $OrderInfo = $StudyYoosureOrderModule->GetInfoByWhere("and OrderNum='{$Result['OrderNo']}'");
        $Sign = trim($_GET ['Sign']);
        unset ($_GET);
        $MySign = $this->VerifyData($Result);
        if ($MySign == $Sign) {
            $ResultUrl = WEB_STUDY_URL . '/order/result/';
            if ($Result['ResultCode'] == 'SUCCESS') {
                $LogMessage = '支付成功';
                $LogData['NewStatus']=2;
                if ($OrderInfo ['Status'] == '1' && $OrderInfo ['Money'] == $Result['Money']) {
                    $Data ['Status'] = '2';
                    if ($Result['PayType'] == '支付宝') {
                        $Data ['PaymentMethod'] = 1;
                    } elseif ($Result['PayType'] == '微信支付') {
                        $Data ['PaymentMethod'] = 2;
                    } elseif ($Result['PayType'] == '银联支付') {
                        $Data ['PaymentMethod'] = 3;
                    }
                    $Data['UpdateTime']=time();
                    $UpdateResult=$StudyYoosureOrderModule->UpdateInfoByWhere($Data, "OrderNum='{$Result['OrderNo']}'");
                    if($UpdateResult){
                        $ShowData ['OrderNo'] = $Result ['OrderNo'];
                        $ShowData ['Money'] = $OrderInfo ['Money'];
                        $ShowData ['PayResult'] = 'SUCCESS';
                        $ShowData ['RedirectUrl'] = WEB_STUDY_URL . '/studentmanage/myorder/?T=2';
                        $ShowData ['RunTime'] = time();
                        $ShowData ['Sign'] = $this->VerifyData($ShowData);
                        $ShowData['Message'] = "<div class=\"PayMoney\">¥<i>" . $ShowData ['Money'] . "</i></div><div class=\"PayInstro\">工作人员会与您联系，帮助您了解更多信息</div>";
                        //短信发送给用户
                        ToolService::SendSMSNotice($OrderInfo['Mobile'],'【57美国网】您的订单号：'.$OrderInfo['OrderNum'].'，已成功支付。请登录study.57us.com会员中心查看订单详情或致电0592-5951656。57美国网不会以订单异常为由要求您操作退款，请谨防诈骗！');
                        //短信发送给运营
                        ToolService::SendSMSNotice(15659827860,$OrderInfo['Contact'] .'用户,游学订单已支付，产品编号：'.$OrderInfo['YoosureID'].'，订单号：'.$OrderInfo['OrderNum'].'，总价￥'.$OrderInfo['Money'].'，预订人：'.$OrderInfo['Contact'].' ，联系电话：'.$OrderInfo['Mobile'].'。');
                        ToolService::SendSMSNotice(15659827860,$OrderInfo['Contact'] .'用户,游学订单已支付，产品编号：'.$OrderInfo['YoosureID'].'，订单号：'.$OrderInfo['OrderNum'].'，总价￥'.$OrderInfo['Money'].'，预订人：'.$OrderInfo['Contact'].' ，联系电话：'.$OrderInfo['Mobile'].'。');
                        ToolService::SendSMSNotice(15160090744,$OrderInfo['Contact']. '用户,游学订单已支付，产品编号：'.$OrderInfo['YoosureID'].'，订单号：'.$OrderInfo['OrderNum'].'，总价￥'.$OrderInfo['Money'].'，预订人：'.$OrderInfo['Contact'].' ，联系电话：'.$OrderInfo['Mobile'].'。');
                        ToolService::SendSMSNotice(15980805724,$OrderInfo['Contact']. '用户,游学订单已支付，产品编号：'.$OrderInfo['YoosureID'].'，订单号：'.$OrderInfo['OrderNum'].'，总价￥'.$OrderInfo['Money'].'，预订人：'.$OrderInfo['Contact'].' ，联系电话：'.$OrderInfo['Mobile'].'。');
                        //邮箱（给用户）
                        $Title ='57美国网-用户游学订单支付通知';
                        $Message ='亲爱的57美国网用户:<br>&nbsp;&nbsp;&nbsp;&nbsp;您好！57美国网游学订单通知, 您已成功付款！订单编号：'.$OrderInfo['OrderNum'].'。<br>感谢您选择57美国网游学服务。请登录study.57us.com会员中心查询订单详情及注意事项。<br>客服电话：+86 592-5951656，微信服务号：study57us。<br><img src="http://images.57us.com//img/common/wxstudy.jpg"  width="100" height="100" /><br> 57美国网祝您学业进步！旅途愉快！';
                        ToolService::SendEMailNotice($OrderInfo['Email'], $Title, $Message);
                    }else{
                        $LogMessage='支付成功(订单状态更新失败)';
                    }
                }
            } else {
                $LogMessage = '支付失败';
                $LogData['NewStatus']=1;
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['PayType'] = $Result ['PayType'];
                $ShowData ['Money'] = $Result ['Money'];
                $ShowData ['PayResult'] = 'FAIL';
                $ShowData ['RedirectUrl'] = WEB_STUDY_URL . '/studentmanage/myorder/';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = $this->VerifyData($ShowData);
            }
            //添加订单日志
            $LogData['UserID']=$OrderInfo['UserID'];
            $LogData['OrderNumber']=$Result['OrderNo'];
            $LogData['Remarks']=$LogMessage;
            $LogData['OperateTime']=date('Y-m-d H:i:s',$ShowData ['RunTime']);
            $LogData['IP']=GetIP();
            $LogData['OldStatus']=1;
            $StudyOrderLogModule=new StudyOrderLogModule();
            $StudyOrderLogModule->InsertInfo($LogData);
			echo ToolService::PostForm($ResultUrl, $ShowData);
        }
    }
}














