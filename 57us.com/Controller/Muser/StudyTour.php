<?php

class StudyTour
{
    public function __construct()
    {
    }
    /**
     * @desc  订单支付
     */
    public function Pay()
    {
        $NO = trim($_GET ['ID']);
        $Type = trim($_GET ['Type']);
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $OrderInfo = $StudyYoosureOrderModule->GetInfoByWhere("and OrderNum='$NO'");
        if ($OrderInfo['Status']==2){
            alertandgotopage("订单已交易完成",WEB_MUSER_URL.'/muserstudytour/studytourorderdetail/?ID='.$OrderInfo['OrderID']);
        }
        //提交订单
        $OrderInfo ['Money'] = $OrderInfo['Money'];
        $PhoneUrl = WEB_MEMBER_URL . '/pay/' . $Type . '/';
        if ($Type == 'alipay') {
            $Data ['OrderNo'] = $NO;
            $Data ['Subject'] = $OrderInfo['OrderName'];
            $Data ['Money'] = $OrderInfo ['Money'];
            $Data ['Body'] = '';
            $Data ['ReturnUrl'] = WEB_MUSER_URL . '/studytour/studytourpayreturn/';
            $Data ['NotifyUrl'] = '';
            $Data ['ProductUrl'] = '';
            $Data ['RunTime'] = time();
        } elseif ($Type == 'wxpay') {
            $Data ['OrderNo'] = $NO;
            $Data ['Subject'] = $OrderInfo['OrderName'];
            $Data ['Money'] = $OrderInfo ['Money'];
            $Data ['Body'] = '';
            $Data ['ReturnUrl'] = WEB_MUSER_URL . '/studytour/studytourpayreturn/';
            $Data ['RunTime'] = time();
        }

        $Data ['Sign'] = ToolService::VerifyData($Data);
        echo ToolService::PostForm($PhoneUrl, $Data);
    }
    /**
     * @desc  游学订单支付回调
     */
    public function StudyTourPayReturn()
    {
        $Result['OrderNo'] = trim($_GET ['OrderNo']);
        $Result['Money'] = trim($_GET ['Money']);
        $Result['PayType'] = trim($_GET ['PayType']);
        $Result['ResultCode'] = trim($_GET ['ResultCode']);
        $Result['RunTime'] = trim($_GET ['RunTime']);

        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $OrderInfo = $StudyYoosureOrderModule->GetInfoByWhere("and OrderNum='{$Result['OrderNo']}'");var_dump($OrderInfo);
        //添加订单日志
        $LogData['UserID']=$OrderInfo['UserID'];
        $LogData['OrderNumber']=$Result['OrderNo'];
        $LogData['Remarks']= $Result['ResultCode'];
        $LogData['OperateTime']=date('Y-m-d H:i:s', $Result['RunTime']);
        $LogData['IP']=GetIP();
        $LogData['OldStatus']=1;
        $StudyOrderLogModule=new StudyOrderLogModule();
        $StudyOrderLogModule->InsertInfo($LogData);
        $Sign = trim($_GET ['Sign']);
        unset ($_GET);
        $MySign = ToolService::VerifyData($Result);
        if ($MySign == $Sign) {
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
                        $ShowData ['RedirectUrl'] = WEB_MUSER_URL.'/muserstudytour/studytourorderdetail/?ID='.$OrderInfo['OrderID'];
                        $ShowData ['RunTime'] = time();
                        $ShowData ['Sign'] = ToolService::VerifyData($ShowData);
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
                $LogData['NewStatus']=1;
                $ShowData ['OrderNo'] = $Result ['OrderNo'];
                $ShowData ['PayType'] = $Result ['PayType'];
                $ShowData ['Money'] = $Result ['Money'];
                $ShowData ['PayResult'] = 'FAIL';
                $ShowData ['RedirectUrl'] = WEB_MUSER_URL.'/muserstudytour/studytourorderdetail/?ID='.$OrderInfo['OrderID'];
                $ShowData ['RunTime'] = time();
                $ShowData ['Sign'] = ToolService::VerifyData($ShowData);
            }
            echo ToolService::PostForm(WEB_MUSER_URL . '/pay/result/', $ShowData);
        }
    }
}