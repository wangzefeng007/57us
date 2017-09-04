<?php

class Visa
{
    public function __construct()
    {
    }

    /**
     * @desc 订单支付
     */
    public function PaymentOrder()
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
            $APIData ['ReturnUrl'] = WEB_MEMBER_URL . '/visa/payreturn/';
            $APIData ['NotifyUrl'] = '';
            $APIData ['ProductUrl'] = '';
            $APIData ['RunTime'] = time();
        } elseif ($Type == 'wxpay') {
            $APIData ['OrderNo'] = $NO;
            $APIData ['Subject'] = $VisaInfo['Title'];
            $APIData ['Money'] = $OrderInfo ['TotalAmount'];
            $APIData ['Body'] = '';
            $APIData ['ReturnUrl'] = WEB_MEMBER_URL . '/visa/payreturn/';
            $APIData ['RunTime'] = time();
        }
        $APIData ['Sign'] = $this->VerifyData($APIData);
		echo ToolService::PostForm($PhoneUrl, $APIData);
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
        $VisaOrderModule = new VisaOrderModule();
        $OrderInfo = $VisaOrderModule->GetInfoByOrderNumber($Result['OrderNo']);
        $Sign = trim($_GET ['Sign']);
        unset ($_GET);
        $MySign = $this->VerifyData($Result);
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
                }
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['Money'] = $OrderInfo ['Money'];
                $ShowData ['PayResult'] = 'SUCCESS';
                $ShowData ['RedirectUrl'] = WEB_MEMBER_URL . '/tourmember/visaorder/';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = $this->VerifyData($ShowData);
                $ShowData['Message'] = "<div class=\"PayMoney\">¥<i>" . $ShowData ['Money'] . "</i></div><div class=\"PayInstro\">工作人员会与您联系，帮助您了解更多信息</div>";
                ToolService::SendSMSNotice(15160090744, '签证订单已支付，订单号：'.$OrderInfo['OrderNumber'].'，预订人：'.$OrderInfo['UserName'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                ToolService::SendSMSNotice(18750258578, '签证订单已支付，订单号：'.$OrderInfo['OrderNumber'].'，预订人：'.$OrderInfo['UserName'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                ToolService::SendSMSNotice(18050016313, '签证订单已支付，订单号：'.$OrderInfo['OrderNumber'].'，预订人：'.$OrderInfo['UserName'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                ToolService::SendSMSNotice(15980805724, '签证订单已支付，订单号：'.$OrderInfo['OrderNumber'].'，预订人：'.$OrderInfo['UserName'].' ，联系电话：'.$OrderInfo['Phone'].'。');
                @ToolService::SendSMSNotice($OrderInfo['Phone'], "订单号".$OrderInfo['OrderNumber']."，".$OrderInfo['UserName']." ".$OrderInfo['OrderName']."，总价￥".$OrderInfo['Money']."。人数：".$OrderInfo['Num']."。我们将于15分钟内确认库存，请登录会员中心查看订单详情或致电400-018-5757。");
                //发送邮箱通知
                @ToolService::SendEMailNotice($OrderInfo['Email'], "57美国网签证订单通知", "您已成功付款！订单编号：{$ShowData['OrderNo']}。请登录官网会员中心查询订单。客服电话：+86 592-5919203，微信公众号：57美国网，57美国网祝您旅途愉快！");
            } else {
                $LogMessage = '支付失败';
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['PayType'] = $Result ['PayType'];
                $ShowData ['Money'] = $Result ['Money'];
                $ShowData ['PayResult'] = 'FAIL';
                $ShowData ['RedirectUrl'] = WEB_MEMBER_URL . '/tourmember/visaorder/';
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = $this->VerifyData($ShowData);
            }
            //添加订单状态变更日志
            $OrderLogModule = new TourProductOrderLogModule();
            $LogData = array('OrderNumber' => $Result['OrderNo'], 'Remarks' => $LogMessage, 'UserID' => $OrderInfo['UserID'], 'OldStatus' => 1, 'NewStatus' => 2, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 2,);
            $OrderLogModule->InsertInfo($LogData);
			echo ToolService::PostForm($ResultUrl, $ShowData);
        }
    }
}














