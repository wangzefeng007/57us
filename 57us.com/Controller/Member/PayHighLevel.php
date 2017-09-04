<?php

class PayHighLevel
{
    public function __construct()
    {
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC',0,200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC',0,200);
    }

    //订单支付
    public function PayOrder()
    {
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        $Type = trim($_GET['type']);
        $OrderNum = trim($_GET['id']);
        $OrderInfo = $TourPrivateOrderModule->GetInfoByWhere('and `OrderNo`= \'' . $OrderNum . '\'');
        $endCity =str_replace(',','-',$OrderInfo['EndCity']);
        $OrderInfo['EndCity'] = substr($endCity,0,strlen($endCity)-1);
        if ($OrderInfo) {
            if ($Type == 'alipay') {
                $Data['OrderNo'] = $OrderInfo['OrderNo'];
                $Data['Subject'] = $OrderInfo['EndCity'];
                $Data['Money'] = $OrderInfo['Money'];
                $Data['Body'] = $OrderInfo['EndCity'];
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/payhighlevel/payreturn/';
                $Data['NotifyUrl'] = '';
                $Data['RunTime'] = time();
                $Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/alipay/', $Data);
            } elseif ($Type == 'wxpay') {
                $Data['OrderNo'] = $OrderInfo['OrderNo'];
                $Data['Subject'] = $OrderInfo['EndCity'];
                $Data['Money'] = $OrderInfo['Money'];
                $Data['Body'] = $OrderInfo['EndCity'];
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/payhighlevel/payreturn/';
                $Data['RunTime'] = time();
                $Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/wxpay/', $Data);
            }
        } else {
            alertandback('不能操作的订单');
        }
    }

    public function PayReturn()
    {
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        $Result['OrderNo'] = trim($_GET['OrderNo']);
        $Result['Money'] = trim($_GET['Money']);
        $Result['PayType'] = trim($_GET['PayType']);
        $Result['ResultCode'] = trim($_GET['ResultCode']);
        $Result['RunTime'] = trim($_GET['RunTime']);
        $Sign = trim($_GET['Sign']);
        $UserID =  $_SESSION['UserID'];
        //添加订单状态变更日志
        $OrderLogModule = new TourProductOrderLogModule();
        $LogData = array('OrderNumber' => $Result['OrderNo'], 'Remarks' => $Result['ResultCode'], 'UserID' => $UserID, 'OldStatus' => 1, 'NewStatus' => 2, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 5,);
        $OrderLogModule->InsertInfo($LogData);
        unset($_GET);
        $MySign = ToolService::VerifyData($Result);
        if ($MySign == $Sign) {
            $ResultUrl = WEB_MEMBER_URL . '/payhighlevel/result/';
            if ($Result['ResultCode'] === 'SUCCESS') {
                $OrderInfo = $TourPrivateOrderModule->GetInfoByWhere('and `OrderNo`= \'' . $Result['OrderNo'] . '\'');
                 $Date['orderId'] = $OrderInfo['OrderNo'];
                //支付宝有异步传输，多加一个判断用于写入日志START
                if ($Result['PayType'] == '支付宝' && $OrderInfo['Money'] == $Result['Money']){
                    $Data['PayType'] = '1';
                }
                //支付宝有异步传输，多加一个判断用于写入日志END
                if ($OrderInfo['Status'] === '1' && $OrderInfo['Money'] === $Result['Money']) {
                    $Data['Status'] = '2';
                    if ($Result['PayType'] == '支付宝'){
                        $Data['PayType'] = '1';
                    }
                    if ($Result['PayType'] == '微信支付'){
                        $Data['PayType'] = '2';
                    }
                    $TourPrivateOrderModule->UpdateByOrderNum($Data, $Result['OrderNo']);
                    //发送下单信息邮件到管理员
                }
                //更新资金流
                $BankFlowModule = new MemberUserBankFlowModule();
                $BankInfo = MemberService::GetUserBankInfo($UserID);
                $BankFlowData=array(
                    'UserID'=>$UserID,
                    'Amt'=>$Result['Money'],
                    'Amount'=>$BankInfo['TotalBalance'],
                    'OperateType'=>2,
                    'Remarks'=>'高级定制',
                    'Type'=>1,
                    'PayType'=> $Data['PayType'],
                    'FromIP'=>GetIP(),
                    'AddTime'=>time()
                    );
                $BankFlowModule->InsertInfo($BankFlowData);
                $ShowData['OrderNo'] = $Result['OrderNo'];
                $ShowData['Money'] = $OrderInfo['Money'];
                $ShowData['PayResult'] = 'SUCCESS';
                $ShowData['RedirectUrl'] = WEB_MEMBER_URL . '/highlevelorderdetail/'.$Result['OrderNo'].'.html';
                $ShowData['RunTime'] = time();
                $ShowData['Sign'] = ToolService::VerifyData($ShowData);
                ToolService::SendSMSNotice(15160090744, '高端定制订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['Name'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                ToolService::SendSMSNotice(18750258578, '高端定制订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['Name'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                ToolService::SendSMSNotice(18050016313, '高端定制订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['Name'].' ，联系电话：'.$OrderInfo['Phone'].'。');
            } else {
                $ShowData['OrderNo'] = $Result['OrderNo'];
                $ShowData['PayType'] = $Result['PayType'];
                $ShowData['Money'] = $Result['Money'];
                $ShowData['PayResult'] = 'FAIL';
                $ShowData['RedirectUrl'] = WEB_MEMBER_URL . '/highlevelorderdetail/'.$Result['OrderNo'].'.html';
                $ShowData['RunTime'] = time();
                $ShowData['Sign'] = ToolService::VerifyData($ShowData);
            }
			echo ToolService::PostForm($ResultUrl, $ShowData);
        }
    }

    // 支付结果处理
    public function Result(){
        $sign=$_POST['Sign'];
        unset($_POST['Sign']);
        $VerifySign= ToolService::VerifyData($_POST);
        if($VerifySign==$sign){
            $PayResult=$_POST['PayResult'];
            if($PayResult=='SUCCESS'){
                $OrderNo=$_POST['OrderNo'];
                $Money=$_POST['Money'];
                $RedirectUrl=$_POST['RedirectUrl'];
                $TourPrivateOrderModule = new TourPrivateOrderModule();
                $OrderInfo = $TourPrivateOrderModule->GetInfoByWhere('and `OrderNo`= \''.$OrderNo.'\'');
                $Message ="<div class=\"PayMoney\">¥<i>".$Money."</i></div><div class=\"PayInstro\">确认邮件已发送至".$OrderInfo['Mail']."，请查收<br>工作人员会与您联系，准备高端定制相关材料，并为您提前预定</div>";
                include template('PayResultSUCCESS');
            }else{
                $OrderNo = $_POST['OrderNo'];
                include template('PayResultFAIL');
            }
        }else{
            include template('PayResultFAIL');
        }
    }
}