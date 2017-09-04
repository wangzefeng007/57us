<?php

class CarRent
{
    public function __construct()
    {
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC',0,200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC',0,200);
    }

    public function PayOrder()
    {
        $ZucheOrderModule = new ZucheOrderModule();
        $Type = trim($_GET['type']);
        $OrderNum = trim($_GET['id']);
        $OrderInfo = $ZucheOrderModule->GetOrderByOrderNum($OrderNum);
        if ($OrderInfo) {
            if ($Type == 'alipay') {
                $Data['OrderNo'] = $OrderInfo['OrderNum']; // 租车订单字段为OrderNum
                $Data['Subject'] = $OrderInfo['OrderName'];// 订单名称
                $Data['Money'] = $OrderInfo['Money']; // 金额
                $Data['Body'] = $OrderInfo['OrderName'];
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/carrent/payreturn/';
                $Data['NotifyUrl'] = '';
                $Data['RunTime'] = time();
                $Data['Sign'] = $this->VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/alipay/', $Data);
            } elseif ($Type == 'wxpay') {
                $Data['OrderNo'] = $OrderInfo['OrderNum']; //租车订单字段为OrderNum
                $Data['Subject'] = $OrderInfo['OrderName'];
                $Data['Money'] = $OrderInfo['Money']; //测试金额
                $Data['Body'] = $OrderInfo['OrderName'];
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/carrent/payreturn/';
                $Data['RunTime'] = time();
                $Data['Sign'] = $this->VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/wxpay/', $Data);
            }
        } else {
            alertandback('不能操作的订单');
        }
    }
    //支付返回
    public function PayReturn()
    {
        $ZucheOrderModule = new ZucheOrderModule();
        $CarAPI = new ZuZuCheAPI();
        $Result['OrderNo'] = trim($_GET['OrderNo']);
        $Result['Money'] = trim($_GET['Money']);
        $Result['PayType'] = trim($_GET['PayType']);
        $Result['ResultCode'] = trim($_GET['ResultCode']);
        $Result['RunTime'] = trim($_GET['RunTime']);
        $Sign = trim($_GET['Sign']);
        unset($_GET);
        $MySign = $this->VerifyData($Result);
        if ($MySign == $Sign) {
            $ResultUrl = WEB_MEMBER_URL . '/carrent/result/';
            if ($Result['ResultCode'] === 'SUCCESS') {
                $OrderInfo = $ZucheOrderModule->GetOrderByOrderNum($Result['OrderNo']);
                $Date['orderId'] = $OrderInfo['OrderNo'];
                //支付宝有异步传输，多加一个判断用于写入日志START
                if ($Result['PayType'] == '支付宝' && $OrderInfo['Money'] == $Result['Money']){
                    $Data['PayType'] = '1';
                    $Return = $CarAPI->confirmOrder($Date);
                    if ($Return['success']){
                        $Data['ZuzucheStatus'] = '下单成功';
                        $message = '下单到租租车成功！';
                    }else{
                        $Data['ZuzucheStatus'] = '下单失败';
                        $message = '下单到租租车失败！';
                    }
                    $ZucheOrderModule->UpdateByOrderNum($Data, $Result['OrderNo']);
                    //日志END
                }
                //支付宝有异步传输，多加一个判断用于写入日志END
                if ($OrderInfo['Status'] === '1' && $OrderInfo['Money'] === $Result['Money']) {
                    $Data['Status'] = '2';//已付款
                    if ($Result['PayType'] == '支付宝'){
                        $Data['PayType'] = '1';
                    }
                    if ($Result['PayType'] == '微信支付'){
                        $Data['PayType'] = '2';
                    }
                    if ($Result['PayType'] == '银联支付'){
                        $Data['PayType'] = '3';
                    }
                    $Return = $CarAPI->confirmOrder($Date);
                    if ($Return['success']){
                        $Data['ZuzucheStatus'] = '下单成功';
                        $message = '下单到租租车成功！';
                    }else{
                        $Data['ZuzucheStatus'] = '下单失败';
                        $message = '下单到租租车失败！';
                    }
                    ToolService::SendSMSNotice(15160090744, '租车订单已支付，订单号：'.$OrderInfo['OrderNum'].'，预订人：'.$OrderInfo['contractGivenname'].' ，联系电话：'.$OrderInfo['contractPhone'].'。');
                    ToolService::SendSMSNotice(18750258578, '租车订单已支付，订单号：'.$OrderInfo['OrderNum'].'，预订人：'.$OrderInfo['contractGivenname'].' ，联系电话：'.$OrderInfo['contractPhone'].'。');
                    ToolService::SendSMSNotice(18050016313, '租车订单已支付，订单号：'.$OrderInfo['OrderNum'].'，预订人：'.$OrderInfo['contractGivenname'].' ，联系电话：'.$OrderInfo['contractPhone'].'。');
                    ToolService::SendSMSNotice(15980805724, '租车订单已支付，订单号：'.$OrderInfo['OrderNum'].'，预订人：'.$OrderInfo['contractGivenname'].' ，联系电话：'.$OrderInfo['contractPhone'].'。');
                    $ZucheOrderModule->UpdateByOrderNum($Data, $Result['OrderNo']);
                    ToolService::SendSMSNotice($OrderInfo['contractPhone'],"订单号".$OrderInfo['OrderNum']."，".$OrderInfo['contractSurname'].$OrderInfo['contractGivenname']." ".$OrderInfo['OrderName']."，总价￥".$OrderInfo['Money']."。我们将于24小时内确认库存，请登录会员中心查看订单详情或致电400-018-5757。");
                    //发送邮箱通知
                    @ToolService::SendEMailNotice($OrderInfo['contractEmail'], "57美国网租车订单通知", "您已成功付款！订单编号：{$OrderInfo['OrderNum']}。请登录官网会员中心查询订单。客服电话：+86 592-5919203，微信公众号：57美国网，57美国网祝您旅途愉快！");
                }
                $ShowData['OrderNo'] = $Result['OrderNo'];
                $ShowData['Money'] = $OrderInfo['Money'];
                $ShowData['PayResult'] = 'SUCCESS';
                $ShowData['RedirectUrl'] = WEB_MEMBER_URL . '/carrentorderdetail/'.$Result['OrderNo'].'.html';
                $ShowData['RunTime'] = time();
                $ShowData['Sign'] = $this->VerifyData($ShowData);
            } else {
                $ShowData['OrderNo'] = $Result['OrderNo'];
                $ShowData['PayType'] = $Result['PayType'];
                $ShowData['Money'] = $Result['Money'];
                $ShowData['PayResult'] = 'FAIL';
                $ShowData['RedirectUrl'] = WEB_MEMBER_URL . '/carrentorderdetail/'.$Result['OrderNo'].'.html';
                $ShowData['RunTime'] = time();
                $ShowData['Sign'] = $this->VerifyData($ShowData);
            }
			echo ToolService::PostForm($ResultUrl, $ShowData);
        }
    }
    // 支付结果处理
    public function Result(){
        $sign=$_POST['Sign'];
        unset($_POST['Sign']);
        $VerifySign=$this->VerifyData($_POST);
        if($VerifySign==$sign){
            $PayResult=$_POST['PayResult'];
            if($PayResult=='SUCCESS'){
                $OrderNo=$_POST['OrderNo'];
                $Money=$_POST['Money'];
                $RedirectUrl=$_POST['RedirectUrl'];
                $ZucheOrderModule = new ZucheOrderModule();
                $OrderInfo =$ZucheOrderModule->GetOrderByOrderNum($OrderNo);
                $Message ="<div class=\"PayMoney\">¥<i>".$Money."</i></div><div class=\"PayInstro\">确认邮件已发送至".$OrderInfo['contractEmail']."，请查收<br>工作人员会与您联系，准备租车相关材料，并为您提前预定</div>";
                include template('PayResultSUCCESS');
            }else{
                $OrderNo = $_POST['OrderNo'];
                include template('PayFaild');
            }
        }else{
            include template('PayFaild');
        }
    }
    // 数据验证
    private function VerifyData($para)
    {
        if (! is_array($para)) {
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
	
    // 租车订单支付
    public function Carrentpay()
    {
        $MemberUserModule = new MemberUserModule();
        $ZucheOrderModule = new ZucheOrderModule();
        $CarAPI = new ZuZuCheAPI();
        $title = "57美国网-租车在线支付";
        $OrderNum = trim($_GET['ID']); // 本站订单号
        if ($_GET['a'] == '57us') {
            $Data['Money'] = '0.01';
            $ZucheOrderModule->UpdateByOrderNum($Data, $OrderNum);
        }
        $OrderInfo = $ZucheOrderModule->GetOrderByOrderNum($OrderNum);
        if($OrderInfo) {
            if (strtotime($OrderInfo['ExpirationTime']) - time() > 0) {
                $CreateTime = strtotime($OrderInfo['CreateTime']);
                $CreateTime = $CreateTime + 3600*24;
                $CreateTime = date("Y-m-d H:i:s",$CreateTime);
                $XpirationDate=time () + 3600 * 24;
                setcookie ( "session_id", session_id (), $XpirationDate, "/", WEB_HOST_URL);
                $_SESSION ['UserID'] = $OrderInfo['UserID'];
                setcookie ( "UserID", $_SESSION ['UserID'], time () + 3600 * 24, "/", WEB_HOST_URL );
            } else {
                $UpData['Status'] = 10;
                $UpData['RefundReason'] = '超时未支付';
                $UpData['UpdateTime'] = date('Y-m-d H:i:s', time());
                $ZucheOrderModule->UpdateByOrderNum($UpData, $OrderNum);
                //添加订单状态更新日志
                $OrderLogModule = new TourProductOrderLogModule();
                $LogData = array('OrderNumber'=>$OrderNum,'UserID'=>$_SESSION['UserID'],'OldStatus'=>$OrderInfo['Status'],'NewStatus'=>$UpData['Status'],'OperateTime'=>date("Y-m-d H:i:s",time()),'IP'=>GetIP(),'Type'=>'4');
                $LogResult = $OrderLogModule->InsertInfo($LogData);
                $url = $_SERVER['HTTP_REFERER'];
                alertandgotopage('订单已过期',$url);
            }
        }
        if ($OrderInfo['QuoteDetail'] ==''){
            $QuoteId = $OrderInfo['QuoteID'];
            $result = $CarAPI ->QuoteInfo($QuoteId);
            $Data['QuoteDetail'] =  addslashes(json_encode($result));
            $ZucheOrderModule->UpdateByOrderNum($Data, $OrderNum);
        }else{
            $result = json_decode($OrderInfo['QuoteDetail'], true);
        }
        $QuoteID = $OrderInfo['QuoteID'];
        $Data['data'] = $CarAPI->QuoteInfo($QuoteID);
        $data = json_decode($OrderInfo['OrderInfo'], true);
        include template('TourMemberCarRentPay');
    }
    public function voucherPdf(){
        $Data['orderId'] = $_GET['orderId'];
        $CarAPI = new ZuZuCheAPI();
        $sdsd = $this->CurlByGet('voucherPdf.php', $Data);
    }
    public function CurlByGet($url,$data=array()){
        $User="F11163220-acg9^&";
        $PassWord="quBEP!#)";
        $ApiUrl="http://api.zuzuche.com/2.0/standard/";
        $fname = '提车凭证'.$data['orderId'].'.pdf';
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $ApiUrl.$url.'?'.http_build_query ($data));
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch,CURLOPT_USERPWD,$User.":".$PassWord);
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 ); //定义超时3秒钟
        $output = curl_exec ( $ch );
        header('Content-type: application/pdf');
        header('filename='.$fname);
        header('Content-Disposition: attachment; filename='.$fname);
        echo $output;
        $errorCode = curl_errno ( $ch );
        curl_close ( $ch );
        if (0 !== $errorCode) {
            return false;
        }
        return json_decode($output,true);
    }
}