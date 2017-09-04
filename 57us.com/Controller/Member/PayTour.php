<?php

class PayTour
{
    public function __construct()
    {
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }

    /**
     * @desc  订单支付
     */
    public function PaymentOrder()
    {
        $TourProductOrderModule = new TourProductOrderModule ();
        $NO = trim($_GET ['no']);
        $Type = trim($_GET ['type']);
        $OrderInfo = $TourProductOrderModule->GetInfoByOrderNumber($NO);
        //提交订单
        $OrderInfo ['TotalAmount'] = $OrderInfo['TotalAmount'];
        $PhoneUrl = WEB_MEMBER_URL . '/pay/' . $Type . '/';
        if ($Type == 'alipay') {
            $APIData ['OrderNo'] = $NO;
            $APIData ['Subject'] = '旅游产品';
            $APIData ['Money'] = $OrderInfo ['TotalAmount'];
            $APIData ['Body'] = '';
            $APIData ['ReturnUrl'] = WEB_MEMBER_URL . '/paytour/payreturn/';
            $APIData ['NotifyUrl'] = '';
            $APIData ['ProductUrl'] = '';
            $APIData ['RunTime'] = time();
        } elseif ($Type == 'wxpay') {
            $APIData ['OrderNo'] = $NO;
            $APIData ['Subject'] = '旅游产品';
            $APIData ['Money'] = $OrderInfo ['TotalAmount'];
            $APIData ['Body'] = '';
            $APIData ['ReturnUrl'] = WEB_MEMBER_URL . '/paytour/payreturn/';
            $APIData ['RunTime'] = time();
        }
        $APIData ['Sign'] = ToolService::VerifyData($APIData);
		echo ToolService::PostForm($PhoneUrl, $APIData);
    }


    /**
     * @desc  当地玩乐_支付回调操作
     */
    public function PlayResult()
    {
        $Sign = $_GET['Sign'];
        unset($_GET['Sign']);
        $VerifySign = ToolService::VerifyData($_GET);
        if ($VerifySign == $Sign) {
            if ($_GET['ResultCode'] == "SUCCESS") {
                $LogMessage = '支付成功';
                $TourOrderModule = new TourProductOrderModule();
                $Order = $TourOrderModule->GetInfoByOrderNumber($_GET['OrderNo']);
                if ($_SESSION['UserID'] && !empty($_SESSION['UserID'])) {
                    $UserID = $_SESSION['UserID'];
                } else {
                    $MemberUserModule = new MemberUserModule();
                    $UserInfo = $MemberUserModule->GetUserIDbyMobile($Order['Tel']);
                    $UserID = $UserInfo['UserID'];
                }
                if ($Order) {
                    if ($_GET['Money'] == $Order['TotalAmount']) {
                        $Data['OrderNo'] = $Order['OrderNumber'];
                        $Data['Money'] = $Order['TotalAmount'];
                        $Data['PayResult'] = 'SUCCESS';
                        $Data['RedirectUrl'] = WEB_MEMBER_URL .'/tourorderdetail/'.$Order['OrderNumber'].'.html';
                        if ($Order['Status'] == 1) {
                            //更新销量
                            $OrderInfoModule = new TourProductOrderInfoModule();
                            $OrderInfo = $OrderInfoModule->GetInfoByWhere(' and OrderNumber=\''.$Order['OrderNumber'].'\'');
                            $TourProductPlayBaseModule=new TourProductPlayBaseModule();
                            $TourProductPlayBaseModule->AddSales($OrderInfo['TourProductID']);
                            if ($_GET['PayType'] == '支付宝') {
                                $PayType = 1;
                            } elseif ($_GET['PayType'] == '微信支付') {
                                $PayType = 2;
                            } elseif ($_GET['PayType'] == '银联支付'){
                                $PayType = 3;
                            }
                            $result = $TourOrderModule->UpdateInfoByOrderNumber(array('Status' => 2, 'PaymentMethod' => $PayType), $Data['OrderNo']);
                            if($result){
                                //更新资金流
                                $BankFlowModule = new MemberUserBankFlowModule();
                                $BankInfo = MemberService::GetUserBankInfo($UserID);
                                $BankFlowData=array(
                                    'UserID'=>$UserID,
                                    'Amt'=>$Data['Money'],
                                    'Amount'=>$BankInfo['TotalBalance'],
                                    'OperateType'=>2,
                                    'Remarks'=>'当地玩乐消费',
                                    'Type'=>1,
                                    'PayType'=>$PayType,
                                    'FromIP'=>GetIP(),
                                    'AddTime'=>time()
                                    );
                                $BankFlowModule->InsertInfo($BankFlowData);
                                ToolService::SendSMSNotice(15160090744, '出游订单已支付，订单号：'.$Order['OrderNumber'].'，预订人：'.$Order['Contacts'].' ，联系电话：'.$Order['Tel'].'。');
                                ToolService::SendSMSNotice(18750258578, '出游订单已支付，订单号：'.$Order['OrderNumber'].'，预订人：'.$Order['Contacts'].' ，联系电话：'.$Order['Tel'].'。');
                                ToolService::SendSMSNotice(18050016313, '出游订单已支付，订单号：'.$Order['OrderNumber'].'，预订人：'.$Order['Contacts'].' ，联系电话：'.$Order['Tel'].'。');
                                //发送短信通知客户
                                $times = date("Y/m/d",time());
                                $TourProductModule = new TourProductModule();
                                $TourProduct = $TourProductModule->GetInfoByKeyID($OrderInfo['TourProductID']);
                                @ToolService::SendSMSNotice($Order['Tel'], "订单号".$Order['OrderNumber']."，".$Order['Contacts']."  $times  ".$TourProduct['ProductName']."，总价￥".$OrderInfo['Money']."。人数：".$OrderInfo['Num']."。我们将于15分钟内确认库存，请登录会员中心查看订单详情或致电400-018-5757。");
                                //发送邮箱通知
                                @ToolService::SendEMailNotice($Order['Email'], "57美国网旅游订单通知", "您已成功付款！订单编号：{$Data['OrderNo']}。请登录官网会员中心查询订单。客服电话：+86 592-5919203，微信公众号：57美国网，57美国网祝您旅途愉快！");
                            }
                            else{
                                $LogMessage = '订单状态更新失败';
                                $Data['PayResult'] = 'FAIL';
                            }
                        }
                    } else {
                        $LogMessage = '订单金额出错';
                        $Data['PayResult'] = 'FAIL';
                    }
                } else {
                    $LogMessage = '订单不存在';
                    $Data['PayResult'] = 'FAIL';
                }
            } else {
                $LogMessage = '支付失败';
                $Data['PayResult'] = 'FAIL';
            }
        } else {
            $LogMessage = '数据验证失败';
            $Data['PayResult'] = 'FAIL';
        }
        //添加订单日志
        $OrderLogModule = new TourProductOrderLogModule();
        $LogData = array('OrderNumber' => $Data['OrderNo'], 'Remarks' => $_GET['ResultCode'], 'UserID' => $UserID, 'OldStatus' => 1, 'NewStatus' => 2, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 1,);
        $OrderLogModule->InsertInfo($LogData);

        $Data['RunTime'] = time();
        $Data['Sign'] = ToolService::VerifyData($Data);
        if ($Data['PayResult'] == 'SUCCESS') {
            $Data['Message'] = "<div class=\"PayMoney\">¥<i>" . $Data['Money'] . "</i></div><div class=\"PayInstro\">订单信息已发送至联系人的手机与邮箱，请注意查收<br>工作人员会与您联系，帮助您了解更多信息</div>";
        }
		echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/result/', $Data);
    }

    /**
     * @desc  跟团游_支付回调操作
     */
    public function GroupResult()
    {
        $Sign = $_GET['Sign'];
        unset($_GET['Sign']);
        $VerifySign = ToolService::VerifyData($_GET);
        if ($VerifySign == $Sign) {
            if ($_GET['ResultCode'] == "SUCCESS") {
                $TourOrderModule = new TourProductOrderModule();
                $Order = $TourOrderModule->GetInfoByOrderNumber($_GET['OrderNo']);
                if ($_SESSION['UserID'] && !empty($_SESSION['UserID'])) {
                    $UserID = $_SESSION['UserID'];
                } else {
                    $MemberUserModule = new MemberUserModule();
                    $UserInfo = $MemberUserModule->GetUserIDbyMobile($Order['Tel']);
                    $UserID = $UserInfo['UserID'];
                }
                if ($Order) {
                    if ($_GET['Money'] == $Order['TotalAmount']) {
                        $Data['OrderNo'] = $Order['OrderNumber'];
                        $Data['Money'] = $Order['TotalAmount'];
                        $Data['PayResult'] = 'SUCCESS';
                        $Data['RedirectUrl'] = WEB_MEMBER_URL . '/tourorderdetail/'.$Order['OrderNumber'].'.html';
                        if ($Order['Status'] == 1) {
                            //更新销量
                            $OrderInfoModule = new TourProductOrderInfoModule();
                            $OrderInfo = $OrderInfoModule->GetInfoByWhere(' and OrderNumber=\''.$Order['OrderNumber'].'\'');
                            $TourProductLineModule=new TourProductLineModule();
                            $TourProductLineModule->AddSales($OrderInfo['TourProductID']);                            
                            if ($_GET['PayType'] == '支付宝') {
                                $PayType = 1;
                            } elseif ($_GET['PayType'] == '微信支付') {
                                $PayType = 2;
                            } elseif ($_GET['PayType'] == '银联支付'){
                                $PayType = 3;
                            }
                            //更新订单状态为已支付
                            $result = $TourOrderModule->UpdateInfoByOrderNumber(array('Status' => 2, 'PaymentMethod' => $PayType), $Data['OrderNo']);
                            if (!$result) {
                                $LogMessage = '订单状态更新失败';
                                $Data['PayResult'] = 'FAIL';
                            } else {
                                //更新资金流
                                $BankFlowModule = new MemberUserBankFlowModule();
                                $BankInfo = MemberService::GetUserBankInfo($UserID);
                                $BankFlowData=array(
                                    'UserID'=>$UserID,
                                    'Amt'=>$Data['Money'],
                                    'Amount'=>$BankInfo['TotalBalance'],
                                    'OperateType'=>2,
                                    'Remarks'=>'跟团游消费',
                                    'Type'=>1,
                                    'PayType'=>$PayType,
                                    'FromIP'=>GetIP(),
                                    'AddTime'=>time()
                                    );
                                $BankFlowModule->InsertInfo($BankFlowData);
                                ToolService::SendSMSNotice(15160090744, '出游订单已支付，订单号：'.$Order['OrderNumber'].'，预订人：'.$Order['Contacts'].' ，联系电话：'.$Order['Tel'].'。');
                                ToolService::SendSMSNotice(18750258578, '出游订单已支付，订单号：'.$Order['OrderNumber'].'，预订人：'.$Order['Contacts'].' ，联系电话：'.$Order['Tel'].'。');
                                ToolService::SendSMSNotice(18050016313, '出游订单已支付，订单号：'.$Order['OrderNumber'].'，预订人：'.$Order['Contacts'].' ，联系电话：'.$Order['Tel'].'。'); 
                                //发送短信通知
                                @ToolService::SendSMSNotice($Order['Tel'], "您已成功付款！订单编号：{$Data['OrderNo']}。请登录官网会员中心查询订单。");
                                //发送邮箱通知
                                @ToolService::SendEMailNotice($Order['Email'], "57美国网旅游订单通知", "您已成功付款！订单编号：{$Data['OrderNo']}。请登录官网会员中心查询订单。客服电话：+86 592-5919203，微信公众号：57美国网，57美国网祝您旅途愉快！");
                            }
                        }
                    } else {
                        $LogMessage = '金额不一致';
                        $Data['PayResult'] = 'FAIL';
                    }
                } else {
                    $LogMessage = '订单不存在';
                    $Data['PayResult'] = 'FAIL';
                }
            } else {
                $LogMessage = '支付失败';
                $Data['PayResult'] = 'FAIL';
            }
        } else {
            $LogMessage = '数据验证错误';
            $Data['PayResult'] = 'FAIL';
        }
        //添加订单日志
        $OrderLogModule = new TourProductOrderLogModule();
        $LogData = array('OrderNumber' => $Data['OrderNo'], 'Remarks' => $_GET['ResultCode'], 'UserID' => $UserID, 'OldStatus' => 1, 'NewStatus' => 2, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 1,);
        $OrderLogModule->InsertInfo($LogData);
        $Data['RunTime'] = time();
        $Data['Sign'] = ToolService::VerifyData($Data);
        if ($Data['PayResult'] == 'SUCCESS') {
            $Data['Message'] = "<div class=\"PayMoney\">¥<i>" . $Data['Money'] . "</i></div><div class=\"PayInstro\">订单信息已发送至联系人的手机与邮箱，请注意查收<br>工作人员会与您联系，帮助您了解更多信息</div>";
        }
		echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/result/', $Data);
    }
}