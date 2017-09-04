<?php

class PayVisa
{
    public function __construct()
    {
    }

    /**
     * @desc 订单支付
     */
    public function Pay()
    {
        $NO = trim($_GET ['no']);
        $Type = trim($_GET ['type']);
        $VisaOrderModule = new VisaOrderModule();
        $VisaProducModule = new VisaProducModule();
        $OrderInfo = $VisaOrderModule->GetInfoByOrderNumber($NO);
        $VisaInfo = $VisaProducModule->GetInfoByKeyID($OrderInfo['VisaID']);
        if ($OrderInfo ['Status'] != 1) {
            //已经支付订单
            alertandback("订单已交易完成");
        }
        //提交订单
        $OrderInfo ['TotalAmount'] = $OrderInfo['Money'];
        $PhoneUrl = WEB_MEMBER_URL . '/pay/' . $Type . '/';
        if ($Type == 'alipay') {
            $APIData ['OrderNo'] = $NO;
            $APIData ['Subject'] = $VisaInfo['Title'];
            $APIData ['Money'] = $OrderInfo ['TotalAmount'];
            $APIData ['Body'] = '';
            $APIData ['ReturnUrl'] = WEB_MEMBER_URL . '/payvisa/payreturn/';
            $APIData ['NotifyUrl'] = '';
            $APIData ['ProductUrl'] = '';
            $APIData ['RunTime'] = time();
        } elseif ($Type == 'wxpay') {
            $APIData ['OrderNo'] = $NO;
            $APIData ['Subject'] = $VisaInfo['Title'];
            $APIData ['Money'] = $OrderInfo ['TotalAmount'];
            $APIData ['Body'] = '';
            $APIData ['ReturnUrl'] = WEB_MEMBER_URL . '/payvisa/payreturn/';
            $APIData ['RunTime'] = time();
        }
        $APIData ['Sign'] = ToolService::VerifyData($APIData);
		echo ToolService::PostForm($PhoneUrl, $APIData);
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
        $UserID =  $_SESSION['UserID'];
        //添加订单状态变更日志
        $OrderLogModule = new TourProductOrderLogModule();
        $LogData = array('OrderNumber' => $Result['OrderNo'], 'Remarks' => $Result['ResultCode'], 'UserID' => $UserID, 'OldStatus' => 1, 'NewStatus' => 2, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 2,);
        $OrderLogModule->InsertInfo($LogData);
        $VisaOrderModule = new VisaOrderModule();
        $OrderInfo = $VisaOrderModule->GetInfoByOrderNumber($Result['OrderNo']);
        $Sign = trim($_GET ['Sign']);
        unset ($_GET);
        $MySign = ToolService::VerifyData($Result);
        if ($MySign == $Sign) {
            $ResultUrl = WEB_MEMBER_URL . '/pay/result/';
            if ($Result['ResultCode'] == 'SUCCESS') {
                $LogMessage = '支付成功';
                if ($OrderInfo ['Status'] == '1' && $OrderInfo ['Money'] == $Result['Money']) {
                    $Data ['Status'] = '2';
                    if ($Result['PayType'] == '支付宝') {
                        $Data ['PaymentMethod'] = 1;
                    } elseif ($Result['PayType'] == '微信支付') {
                        $Data ['PaymentMethod'] = 2;
                    } elseif ($Result['PayType'] == '银联支付') {
                        $Data ['PaymentMethod'] = 3;
                    }
                    $VisaOrderModule->UpdateInfoByOrderNumber($Data, $Result['OrderNo']);
                    //资金操作日志
                    $BankFlowModule = new MemberUserBankFlowModule();
                    $BankInfo = MemberService::GetUserBankInfo($UserID);
                    $BankFlowData=array(
                        'UserID'=>$UserID,
                        'Amt'=>$Result['Money'],
                        'Amount'=>$BankInfo['TotalBalance'],
                        'OperateType'=>2,
                        'Remarks'=>'签证服务',
                        'Type'=>1,
                        'PayType'=> $Data ['PaymentMethod'],
                        'FromIP'=>GetIP(),
                        'AddTime'=>time()
                        );
                    $BankFlowModule->InsertInfo($BankFlowData);                    
                }

                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['Money'] = $OrderInfo ['Money'];
                $ShowData ['PayResult'] = 'SUCCESS';
                $ShowData ['RedirectUrl'] = WEB_MEMBER_URL . '/visaorderdetail/'.$Result ['OrderNo'].'.html';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = ToolService::VerifyData($ShowData);
                $ShowData['Message'] = "<div class=\"PayMoney\">¥<i>" . $ShowData ['Money'] . "</i></div><div class=\"PayInstro\">工作人员会与您联系，帮助您了解更多信息</div>";
                ToolService::SendSMSNotice(15160090744, '签证订单已支付，订单号：'.$OrderInfo['OrderNumber'].'，预订人：'.$OrderInfo['UserName'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                ToolService::SendSMSNotice(18750258578, '签证订单已支付，订单号：'.$OrderInfo['OrderNumber'].'，预订人：'.$OrderInfo['UserName'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                ToolService::SendSMSNotice(18050016313, '签证订单已支付，订单号：'.$OrderInfo['OrderNumber'].'，预订人：'.$OrderInfo['UserName'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                @ToolService::SendSMSNotice($OrderInfo['Phone'], "订单号".$OrderInfo['OrderNumber']."，".$OrderInfo['UserName']." ".$OrderInfo['OrderName']."，总价￥".$OrderInfo['Money']."。人数：".$OrderInfo['Num']."。我们将于15分钟内确认库存，请登录会员中心查看订单详情或致电400-018-5757。");
                //发送邮箱通知
                @ToolService::SendEMailNotice($OrderInfo['Email'], "57美国网签证订单通知", "您已成功付款！订单编号：{$ShowData['OrderNo']}。请登录官网会员中心查询订单。客服电话：+86 592-5919203，微信公众号：57美国网，57美国网祝您旅途愉快！");
            } else {
                $LogMessage = '支付失败';
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['PayType'] = $Result ['PayType'];
                $ShowData ['Money'] = $Result ['Money'];
                $ShowData ['PayResult'] = 'FAIL';
                $ShowData ['RedirectUrl'] = WEB_MEMBER_URL . '/visaorderdetail/'.$Result ['OrderNo'].'.html';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = ToolService::VerifyData($ShowData);
            }
            //添加订单状态变更日志
            $OrderLogModule = new TourProductOrderLogModule();
            $LogData = array('OrderNumber' => $Result['OrderNo'], 'Remarks' => $LogMessage, 'UserID' => $OrderInfo['UserID'], 'OldStatus' => 1, 'NewStatus' => 2, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 2,);
            $OrderLogModule->InsertInfo($LogData);
			echo ToolService::PostForm($ResultUrl, $ShowData);
        }
    }
}














