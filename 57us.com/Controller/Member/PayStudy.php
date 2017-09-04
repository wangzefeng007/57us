<?php

/**
 * @desc 留学订单支付
 * Class PayStudy
 */
class PayStudy
{
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
        $NowTime = time();
        $Sign = trim($_GET ['Sign']);
        unset ($_GET);
        $MySign = ToolService::VerifyData($Result);
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
                    //开始事务
                    global $DB;
                    $DB->query("BEGIN");
                    //更新订单状态
                    $StudyOrderUpdateResult=$StudyOrderModule->UpdateInfoByWhere($Data, "OrderNum='{$Result['OrderNo']}'");
                    if($StudyOrderUpdateResult){
                        if($OrderInfo['OrderType']==1){  //顾问服务
                            $LogData['Type']=1;
                            //更新服务销量
                            $UpdateData['SaleNum']=$ServiceInfo['SaleNum']+1;
                            $StudyConsultantServiceUpdateResult = $StudyConsultantServiceModule->UpdateInfoByKeyID($UpdateData,$OrderInfo['ProductID']);
                            $BankFlowModule = new MemberUserBankFlowModule();
                            $BankModule = new MemberUserBankModule();
                            if($StudyConsultantServiceUpdateResult){
                                if($ServiceInfo['ServiceType'] == 7){ //背景提升,直接完成订单，没有流程
                                    $StudyOrderConsultantModule = new StudyOrderConsultantModule();
                                    $ConsultantInfoModule = new StudyConsultantInfoModule();
                                    //顾问资金比例
                                    $Scale = $ConsultantInfoModule->Scale;
                                    $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$OrderInfo['RelationID']);
                                    //直接全款*顾问应获比例
                                    $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']];//操作金额,当前服务的订单部分金额
                                    $StudyOrderUpdateResult2 = $StudyOrderModule->UpdateInfoByWhere(array('Status'=>3), "OrderNum='{$Result['OrderNo']}'");
                                    if($StudyOrderUpdateResult2){
                                        $FlowData = array(
                                            'OrderID'=>$OrderInfo['OrderID'],
                                            'UserID'=>$OrderInfo['UserID'],
                                            'RelationID'=>$OrderInfo['RelationID'],
                                            'Type'=>$ServiceInfo['ServiceType'],
                                            'TypeName'=>$StudyOrderConsultantModule->Part[$ServiceInfo['ServiceType']]['Headline'],
                                            'Status'=>3, //直接完成订单
                                            'ConfirmTime'=>$NowTime,
                                            'Amt'=>$Amt
                                        );
                                        //添加订单流程表结算金额
                                        $StudyOrderConsultantInsertResult = $StudyOrderConsultantModule->InsertInfo($FlowData);
                                        if($StudyOrderConsultantInsertResult){
                                            //资金及资金日志操作
                                            $BankInfo = MemberService::GetUserBankInfo($OrderInfo['RelationID']);
                                            //操作后的总金额
                                            $Amount = $BankInfo['TotalBalance']+$Amt;
                                            //顾问资金表更新
                                            $BankUpdateResult = $BankModule->UpdateInfoByKeyID(array('TotalBalance'=>$Amount,'FreeBalance'=>$BankInfo['FreeBalance']+$Amt),$BankInfo['BankID']);
                                            if($BankUpdateResult){
                                                //顾问添加资金记录操作,OperateType=>系统入账,Type=>留学
                                                $BankFlowData = array('UserID'=>$OrderInfo['RelationID'],'Amt'=>$Amt,'Amount'=>$Amount,'OperateType'=>4,'FromIP'=>GetIP(),'Remarks'=>$OrderInfo['OrderName'].'-背景提升','Type'=>2,'PayType'=>0,'AddTime'=>$NowTime);
                                                $BankFlowInsertResult = $BankFlowModule->InsertInfo($BankFlowData);
                                                if(!$BankFlowInsertResult){
                                                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                                                    $LogMessage="支付成功(创建顾问资金日志失败)";
                                                }
                                                else{
                                                    //学生添加资金记录操作,OperateType=>系统入账,Type=>留学
                                                    $UserBankInfo = MemberService::GetUserBankInfo($OrderInfo['UserID']);
                                                    $UserBankFlowData = array('UserID'=>$OrderInfo['UserID'],'Amt'=>'-'.$Amt,'Amount'=>$UserBankInfo['TotalBalance'],'OperateType'=>2,'FromIP'=>GetIP(),'Remarks'=>$OrderInfo['OrderName'].'-背景提升','Type'=>2,'PayType'=>$Data ['PayType'],'AddTime'=>$NowTime);
                                                    $UserBankFlowInsertResult = $BankFlowModule->InsertInfo($UserBankFlowData);
                                                    if($UserBankFlowInsertResult){
                                                        $DB->query("COMMIT");//执行事务
                                                    }
                                                    else{
                                                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                                                        $LogMessage="支付成功(创建学生资金日志失败)";
                                                    }
                                                }
                                            }
                                            else{
                                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                                $LogMessage="支付成功(资金表更新失败，BankModule表)";
                                            }
                                        }
                                        else{
                                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                                            $LogMessage="支付成功(添加订单流程表结算金额失败，StudyOrderConsultantModule表)";
                                        }
                                    }
                                    else{
                                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                                        $LogMessage="支付成功(更新订单状态为完成失败，当订单为背景提升时)";
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
                                    //学生添加资金记录操作,OperateType=>系统入账,Type=>留学
                                    $UserBankInfo = MemberService::GetUserBankInfo($OrderInfo['UserID']);
                                    $UserBankFlowData = array('UserID'=>$OrderInfo['UserID'],'Amt'=>'-'.$OrderInfo['Money'],'Amount'=>$UserBankInfo['TotalBalance'],'OperateType'=>2,'FromIP'=>GetIP(),'Remarks'=>$OrderInfo['OrderName'],'Type'=>2,'PayType'=>$Data ['PayType'],'AddTime'=>$NowTime);
                                    $UserBankFlowInsertResult = $BankFlowModule->InsertInfo($UserBankFlowData);
                                    if($UserBankFlowInsertResult){
                                        //顾问服务流程添加
                                        $StudyOrderConsultantModule=new StudyOrderConsultantModule();
                                        $FlowArr=$StudyOrderConsultantModule->Flow[$ServiceInfo['ServiceType']];
                                        $FlowData=array();
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
                                    else{
                                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                                        $LogMessage="支付成功(创建学生资金日志失败)";
                                    }
                                }
                            }
                            else{
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                $LogMessage='支付成功(更新服务购买量失败)';
                            }
                        }elseif($OrderInfo['OrderType']==2){ //教师服务
                            $LogData['Type']=2;
                            $StudyTeacherCourseModule=new StudyTeacherCourseModule();
                            $CourseInfo=$StudyTeacherCourseModule->GetInfoByKeyID($OrderInfo['ProductID']);
                            $UpdateData['SaleNum'] = $CourseInfo['SaleNum']+1;
                            //更新课程销量
                            $Result1 = $StudyTeacherCourseModule->UpdateInfoByKeyID($UpdateData,$OrderInfo['ProductID']);
                            if($Result1){
                                $DB->query("COMMIT");//执行事务
                            }
                            else{
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                $LogMessage='支付成功(更新课程购买量失败)';
                            }
                        }
                    }else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $LogMessage='支付成功(订单状态更新失败)';
                    }
                }
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['Money'] = $OrderInfo ['Money'];
                $ShowData ['PayResult'] = 'SUCCESS';
                $ShowData ['RedirectUrl'] = WEB_MEMBER_URL . '/memberstudy/serviceorderlist/';
                $ShowData ['RunTime'] = $NowTime;
                $ShowData ['Sign'] = ToolService::VerifyData($ShowData);
            } else {
                $LogMessage = '支付失败';
                $LogData['NewStatus']=1;
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['PayType'] = $Result ['PayType'];
                $ShowData ['Money'] = $Result ['Money'];
                $ShowData ['PayResult'] = 'FAIL';
                $ShowData ['RedirectUrl'] = WEB_MEMBER_URL . '/memberstudy/serviceorderlist/';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = ToolService::VerifyData($ShowData);
            }
            //添加订单日志
            $LogData['UserID']=$OrderInfo['UserID'];
            $LogData['OrderNumber']=$Result['OrderNo'];
            $LogData['Remarks']=$LogMessage;
            $LogData['OperateTime']=$NowTime;
            $LogData['IP']=GetIP();
            $LogData['OldStatus']=1;
            $StudyOrderLogModule=new StudyOrderLogModule();
            $StudyOrderLogModule->InsertInfo($LogData);
            echo ToolService::PostForm($ResultUrl,$ShowData);
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
        $MySign = ToolService::VerifyData($Result);
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

                    //开始事务
                    global $DB;
                    $DB->query("BEGIN");
                    $UpdateResult=$StudyYoosureOrderModule->UpdateInfoByWhere($Data, "OrderNum='{$Result['OrderNo']}'");
                    if($UpdateResult){
                        $BankFlowModule  = new MemberUserBankFlowModule();
                        $BankInfo = MemberService::GetUserBankInfo($OrderInfo['UserID']);
                        $UserBankFlowData = array('UserID'=>$OrderInfo['UserID'],'Amt'=>'-'.$OrderInfo['Money'],'Amount'=>$BankInfo['TotalBalance'],'OperateType'=>2,'FromIP'=>GetIP(),'Remarks'=>$OrderInfo['OrderName'],'Type'=>2,'PayType'=>$Data ['PayType'],'AddTime'=>time());
                        $UserBankFlowInsertResult = $BankFlowModule->InsertInfo($UserBankFlowData);
                        if($UserBankFlowInsertResult){
                            $DB->query("COMMIT");//执行事务
                            $ShowData ['OrderNo'] = $Result ['OrderNo'];
                            $ShowData ['Money'] = $OrderInfo ['Money'];
                            $ShowData ['PayResult'] = 'SUCCESS';
                            $ShowData ['RedirectUrl'] = WEB_MEMBER_URL . '/memberstudy/tourorderlist/';
                            $ShowData ['RunTime'] = time();
                            $ShowData ['Sign'] = ToolService::VerifyData($ShowData);
                            $ShowData ['Message'] = "<div class=\"PayMoney\">¥<i>" . $ShowData ['Money'] . "</i></div><div class=\"PayInstro\">工作人员会与您联系，帮助您了解更多信息</div>";
                            //短信发送给用户
                            ToolService::SendSMSNotice($OrderInfo['Mobile'],'【57美国网】您的订单号：'.$OrderInfo['OrderNum'].'，已成功支付。请登录study.57us.com会员中心查看订单详情或致电0592-5951656。57美国网不会以订单异常为由要求您操作退款，请谨防诈骗！');
                            //短信发送给运营
                            ToolService::SendSMSNotice(15659827860,$OrderInfo['Contact'] .'用户,游学订单已支付，产品编号：'.$OrderInfo['YoosureID'].'，订单号：'.$OrderInfo['OrderNum'].'，总价￥'.$OrderInfo['Money'].'，预订人：'.$OrderInfo['Contact'].' ，联系电话：'.$OrderInfo['Mobile'].'。');
                            ToolService::SendSMSNotice(15659827860,$OrderInfo['Contact'] .'用户,游学订单已支付，产品编号：'.$OrderInfo['YoosureID'].'，订单号：'.$OrderInfo['OrderNum'].'，总价￥'.$OrderInfo['Money'].'，预订人：'.$OrderInfo['Contact'].' ，联系电话：'.$OrderInfo['Mobile'].'。');
                            ToolService::SendSMSNotice(15160090744,$OrderInfo['Contact']. '用户,游学订单已支付，产品编号：'.$OrderInfo['YoosureID'].'，订单号：'.$OrderInfo['OrderNum'].'，总价￥'.$OrderInfo['Money'].'，预订人：'.$OrderInfo['Contact'].' ，联系电话：'.$OrderInfo['Mobile'].'。');
                            //邮箱（给用户）
                            $Title ='57美国网-用户游学订单支付通知';
                            $Message ='亲爱的57美国网用户:<br>&nbsp;&nbsp;&nbsp;&nbsp;您好！57美国网游学订单通知, 您已成功付款！订单编号：'.$OrderInfo['OrderNum'].'。<br>感谢您选择57美国网游学服务。请登录study.57us.com会员中心查询订单详情及注意事项。<br>客服电话：+86 592-5951656，微信服务号：study57us。<br><img src="http://images.57us.com//img/common/wxstudy.jpg"  width="100" height="100" /><br> 57美国网祝您学业进步！旅途愉快！';
                            ToolService::SendEMailNotice($OrderInfo['Email'], $Title, $Message);
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $LogMessage='支付成功(资金记录更新失败)';
                        }
                    }else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
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
                $ShowData ['RedirectUrl'] = WEB_MEMBER_URL . '/memberstudy/tourorderlist/';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = ToolService::VerifyData($ShowData);
            }
            //添加订单日志
            $LogData['UserID']=$OrderInfo['UserID'];
            $LogData['OrderNumber']=$Result['OrderNo'];
            $LogData['Remarks']=$LogMessage;
            $LogData['OperateTime']=time();
            $LogData['IP']=GetIP();
            $LogData['OldStatus']=1;
            $StudyOrderLogModule=new StudyOrderLogModule();
            $StudyOrderLogModule->InsertInfo($LogData);
            echo ToolService::PostForm($ResultUrl, $ShowData);
        }
    }
}














