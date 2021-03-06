<?php

class Pay
{
    public function __construct()
    {
    }

    /**
     * @desc  支付宝
     * @throws Exception
     */
    public function AliPay()
    {
        $Sign = $_POST['Sign'];
        unset($_POST['Sign']);
        if ($Sign == ToolService::VerifyData($_POST)) {
            $notify_url = WEB_MUSER_URL . '/pay/alipaynotify/';
            $return_url = WEB_MUSER_URL . '/pay/alipaynotify/'; //必填
            $out_trade_no = $_POST['OrderNo']; //必填
            $subject = stripslashes($_POST['Subject']); //必填
            $total_fee = $_POST['Money']; //必填
            $body = stripslashes($_POST['Body']);
            $show_url = $_POST['ProductUrl'];
            $MemberOrderTempModule = new MemberOrderTempModule();
            $Data['OrderID'] = $out_trade_no;
            $Data['NotifyUrl'] = $_POST['ReturnUrl'];
            $Data['PayType'] = 0;
            $Data['CreateTime'] = time();
            $Data['ResultCode'] = 0;
            $OrderExists = $MemberOrderTempModule->GetOrderByID($Data['OrderID']);
            if (!$OrderExists) {
                $InsertResult = $MemberOrderTempModule->InsertInfo($Data);
            } else {
                if ($OrderExists['ResultCode'] == 1) {
                    alertandgotopage("该订单已支付完成!", WEB_MUSER_URL);
                } else {
                    $InsertResult = $MemberOrderTempModule->UpdateData($Data, $Data['OrderID']);
                }
            }
            if ($InsertResult) {
                if($this->IsOrNotMobile()){
                    include SYSTEM_ROOTPATH.'/Include/Alipay/wap/AopSdk.php';
                    $aop = new AopClient();
                    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
                    $aop->appId = '2016092601971945';
                    $aop->rsaPrivateKeyFilePath = SYSTEM_ROOTPATH.'/Include/Alipay/wap/rsa_private_key.pem';
                    $aop->alipayPublicKey=SYSTEM_ROOTPATH.'/Include/Alipay/wap/rsa_public_key.pem';
                    $aop->apiVersion = '1.0';
                    $aop->postCharset='utf-8';
                    $aop->format='json';
                    $request = new AlipayTradeWapPayRequest ();
                    $JsonData['body']=$body;
                    $JsonData['subject']=$subject;
                    $JsonData['out_trade_no']=$out_trade_no;
                    $JsonData['total_amount']=$total_fee;
                    $JsonData['product_code']="QUICK_WAP_PAY";
                    $request->setReturnUrl(WEB_MEMBER_URL . '/pay/wapalipayreturn/');
                    $request->setNotifyUrl(WEB_MEMBER_URL . '/pay/wapalipaynotify/');
                    $request->setBizContent(json_encode($JsonData));
                    $result = $aop->pageExecute ( $request); 
                    echo $result;
                }else{
                    include SYSTEM_ROOTPATH.'/Include/AliPay/AliPay.php';
                    $AliPay = new AliPay();
                    $AliPay->SubmitOrder(1, $notify_url, $return_url, $out_trade_no, $subject, $total_fee, $body, $show_url);
                }
            } else {
                alertandgotopage('订单支付出现异常,请重新尝试', WEB_MUSER_URL);
            }
        } else {
            alertandgotopage('异常的请求', WEB_MUSER_URL);
        }

    }

    /**
     * @desc 微信支付
     */
    public function WXPay()
    {
        $Sign = $_POST['Sign'];
        unset($_POST['Sign']);
        if ($Sign == ToolService::VerifyData($_POST)) {
            $MemberOrderTempModule = new MemberOrderTempModule();
            $Data['OrderID'] = $_POST['OrderNo'];
            $Data['NotifyUrl'] = $_POST['ReturnUrl'];
            $Data['PayType'] = 1;
            $Data['CreateTime'] = time();
            $Data['ResultCode'] = 0;
            $OrderExists = $MemberOrderTempModule->GetOrderByID($Data['OrderID']);
            if (!$OrderExists) {
                $InsertResult = $MemberOrderTempModule->InsertInfo($Data);
            } else {
                if ($OrderExists['ResultCode'] == 1) {
                    alertandgotopage("该订单已支付完成!", WEB_MUSER_URL);
                } else {
                    $InsertResult = $MemberOrderTempModule->UpdateData($Data, $Data['OrderID']);
                }
            }
            if ($InsertResult) {
                include SYSTEM_ROOTPATH . '/Include/WXPay/WxPay.NativePay.php';
                $notify = new NativePay();
                $input = new WxPayUnifiedOrder();
                $input->SetBody(stripslashes($_POST['Subject'])); //必填
                $input->SetDetail(stripslashes($_POST['Body']));
                $input->SetOut_trade_no($_POST['OrderNo']); //必填
                $input->SetTotal_fee($_POST['Money'] * 100); //必填
                $input->SetNotify_url(WEB_MUSER_URL . '/pay/wxpaynotify/'); //必填
                $input->SetTrade_type("NATIVE");
                $input->SetSpbill_create_ip(GetIP());
                $input->SetProduct_id($_POST['OrderNo']);
                $result = $notify->GetPayUrl($input);
                if ($result['code_url']) {
                    $WXPayUrl = $result["code_url"];
                    $WXPayUrl = "http://paysdk.weixin.qq.com/example/qrcode.php?data=" . urlencode($WXPayUrl);
                    include template("PayWXPay");
                } else {
                    alertandgotopage('订单异常', WEB_MUSER_URL);
                }
            } else {
                alertandgotopage('订单支付出现异常,请重新尝试', WEB_MUSER_URL);
            }
        } else {
            alertandgotopage('异常的请求', WEB_MUSER_URL);
        }
    }

    /**
     * @desc  支付宝手机支付回调
     */
    public function WapAliPayReturn(){
        include SYSTEM_ROOTPATH.'/Include/Alipay/wap/AopSdk.php';
        $aop = new AopClient();
        $aop->alipayPublicKey=SYSTEM_ROOTPATH.'/Include/Alipay/wap/rsa_public_key.pem';
        if($aop->rsaCheckV1($_GET,SYSTEM_ROOTPATH.'/Include/Alipay/wap/rsa_public_key.pem')==1){
            //验证通过
            $MemberOrderTempModule = new MemberOrderTempModule();
            $Data['ResultCode'] = 1;
            $Data['Money'] = trim($_GET['total_amount']);
            $Result=$MemberOrderTempModule->UpdateData($Data, trim($_GET['out_trade_no']));
            if($Result){
                $OrderInfo = $MemberOrderTempModule->GetOrderByID(trim($_GET['out_trade_no']));
                if ($OrderInfo) {
                    $VerifyData['OrderNo'] = trim($_GET['out_trade_no']);
                    $VerifyData['Money'] = $OrderInfo['Money'];
                    $VerifyData['PayType'] = "支付宝";
                    $VerifyData['ResultCode'] = 'SUCCESS';
                    $VerifyData['RunTime'] = time();
                    $VerifyData['Sign'] = ToolService::VerifyData($VerifyData);
                    header("Location:" . rtrim($OrderInfo['NotifyUrl'], '/') . '/?' . http_build_query($VerifyData));
                }else{
                    header("Location:/pay/result/");
                }                       
            }else{
                header("Location:/pay/result/");
            } 
        }else{
            header("Location:/pay/result/");
        }
    }

    /**
     * @desc  支付宝手机支付异步
     */
    public function WapAliPayNotify(){
        include SYSTEM_ROOTPATH.'/Include/Alipay/wap/AopSdk.php';
        $aop = new AopClient();
        $aop->alipayPublicKey=SYSTEM_ROOTPATH.'/Include/Alipay/wap/rsa_public_key.pem';
        //公钥
        //$aop->alipayrsaPublicKey="MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB";
        if($aop->rsaCheckV1($_POST,SYSTEM_ROOTPATH.'/Include/Alipay/wap/rsa_public_key.pem')==1){
            //验证通过
            if($_POST['trade_status']=='TRADE_SUCCESS'){
                $MemberOrderTempModule = new MemberOrderTempModule();
                $Data['ResultCode'] = 1;
                $Data['Money'] = trim($_POST['total_amount']);
                $Result=$MemberOrderTempModule->UpdateData($Data, trim($_POST['out_trade_no']));
                if($Result){
                    $OrderInfo = $MemberOrderTempModule->GetOrderByID(trim($_POST['out_trade_no']));
                    if ($OrderInfo) {
                        $VerifyData['OrderNo'] = trim($_POST['out_trade_no']);
                        $VerifyData['Money'] = $OrderInfo['Money'];
                        $VerifyData['PayType'] = "支付宝";
                        $VerifyData['ResultCode'] = 'SUCCESS';
                        $VerifyData['RunTime'] = time();
                        $VerifyData['Sign'] = ToolService::VerifyData($VerifyData);
                        $NotifyUrl = rtrim($OrderInfo['NotifyUrl'], '/') . '/?' . http_build_query($VerifyData);
                        @file_get_contents($NotifyUrl);
                    }                       
                } 
            }
        }
    }
    
    /**
     * @desc 支付宝回调
     */
    public function AliPayNotify()
    {
        include SYSTEM_ROOTPATH.'/Include/Alipay/AliPay.php';
        $AliPay = new AliPay();
        if (count($_POST)) {
            if ($AliPay->GetPayStatus($_POST) === 'true') {
                $MemberOrderTempModule = new MemberOrderTempModule();
                $Data['ResultCode'] = 1;
                $Data['Money'] = trim($_POST['total_fee']);
                $MemberOrderTempModule->UpdateData($Data, trim($_POST['out_trade_no']));
                $OrderInfo = $MemberOrderTempModule->GetOrderByID(trim($_POST['out_trade_no']));
                if ($OrderInfo) {
                    $VerifyData['OrderNo'] = trim($_POST['out_trade_no']);
                    $VerifyData['Money'] = $OrderInfo['Money'];
                    $VerifyData['PayType'] = "支付宝";
                    $VerifyData['ResultCode'] = 'SUCCESS';
                    $VerifyData['RunTime'] = time();
                    $VerifyData['Sign'] = ToolService::VerifyData($VerifyData);
                    $NotifyUrl = rtrim($OrderInfo['NotifyUrl'], '/') . '/?' . http_build_query($VerifyData);
                    @file_get_contents($NotifyUrl);
                } else {
                    header("Location:/pay/result/");
                }
            } else {
                header("Location:/pay/result/");
            }
        } else {
            if ($AliPay->GetPayStatus($_GET) === 'true') {
                $MemberOrderTempModule = new MemberOrderTempModule();
                $Data['ResultCode'] = 1;
                $Data['Money'] = trim($_GET['total_fee']);
                $MemberOrderTempModule->UpdateData($Data, trim($_GET['out_trade_no']));
                $OrderInfo = $MemberOrderTempModule->GetOrderByID(trim($_GET['out_trade_no']));
                if ($OrderInfo) {
                    $VerifyData['OrderNo'] = trim($_GET['out_trade_no']);
                    $VerifyData['Money'] = $OrderInfo['Money'];
                    $VerifyData['PayType'] = "支付宝";
                    $VerifyData['ResultCode'] = 'SUCCESS';
                    $VerifyData['RunTime'] = time();
                    $VerifyData['Sign'] = ToolService::VerifyData($VerifyData);
                    header("Location:" . rtrim($OrderInfo['NotifyUrl'], '/') . '/?' . http_build_query($VerifyData));
                } else {
                    header("Location:/pay/result/");
                }
            } else {
                header("Location:/pay/result/");
            }
        }
    }

    /**
     * @desc  微信支付回调
     * @throws WxPayException
     */
    public function WXPayNotify()
    {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $BackResult = json_decode(json_encode(@simplexml_load_string($xml, NULL, LIBXML_NOCDATA)), true);
        if (!array_key_exists("transaction_id", $BackResult)) {
            echo "<xml>
                <return_code><![CDATA[FAIL]]></return_code>
                <return_msg><![CDATA[输入参数不正确]]></return_msg>
             </xml>";
        } else {
            $transaction_id = $BackResult['transaction_id'];
            include SYSTEM_ROOTPATH . '/Include/WXPay/WxPay.Api.php';
            include SYSTEM_ROOTPATH . '/Include/WXPay/WxPay.Notify.php';
            $input = new WxPayOrderQuery();
            $input->SetTransaction_id($transaction_id);
            $result = WxPayApi::orderQuery($input);
            if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
                $MemberOrderTempModule = new MemberOrderTempModule();
                $Data['ResultCode'] = 1;
                $Data['Money'] = (trim($BackResult['total_fee']) / 100);
                $MemberOrderTempModule->UpdateData($Data, trim($BackResult['out_trade_no']));
                $OrderInfo = $MemberOrderTempModule->GetOrderByID(trim($BackResult['out_trade_no']));
                if ($OrderInfo) {
                    $VerifyData['OrderNo'] = trim($BackResult['out_trade_no']);
                    $VerifyData['Money'] = ($BackResult['total_fee'] / 100);
                    $VerifyData['PayType'] = "微信支付";
                    $VerifyData['ResultCode'] = 'SUCCESS';
                    $VerifyData['RunTime'] = time();
                    $VerifyData['Sign'] = ToolService::VerifyData($VerifyData);
                    $NotifyUrl = rtrim($OrderInfo['NotifyUrl'], '/') . '/?' . http_build_query($VerifyData);
                    @file_get_contents($NotifyUrl);
                }
                echo "<xml>
                        <return_code><![CDATA[SUCCESS]]></return_code>
                        <return_msg><![CDATA[OK]]></return_msg>
                    </xml>";
            } else {
                echo "<xml>
                <return_code><![CDATA[FAIL]]></return_code>
                <return_msg><![CDATA[交易未完成]]></return_msg>
                </xml>";
            }
        }
    }

    /**
     * @desc 支付结果查询
     * @desc 微信端查询交易状态
     */
    public function OrderStatus()
    {
        $DoMain = 'http://' . $_SERVER ["HTTP_HOST"];
        if ($DoMain == WEB_MUSER_URL) {
            $OrderID = $_POST['NO'];
            $MemberOrderTempModule = new MemberOrderTempModule();
            $OrderInfo = $MemberOrderTempModule->GetOrderByID($OrderID);
            if ($OrderInfo) {
                if ($OrderInfo['ResultCode'] == 1) {
                    $VerifyData['OrderNo'] = $OrderID;
                    $VerifyData['Money'] = $OrderInfo['Money'];
                    if ($OrderInfo['PayType'] == 0) {
                        $VerifyData['PayType'] = "支付宝";
                    } elseif ($OrderInfo['PayType'] == 1) {
                        $VerifyData['PayType'] = "微信支付";
                    }
                    $VerifyData['ResultCode'] = 'SUCCESS';
                    $VerifyData['RunTime'] = time();
                    $VerifyData['Sign'] = ToolService::VerifyData($VerifyData);
                    $NotifyUrl = rtrim($OrderInfo['NotifyUrl'], '/') . '/?' . http_build_query($VerifyData);
                    $json_result = array('Status' => 1, 'Url' => $NotifyUrl);
                } else {
                    $json_result = array('Status' => 0);
                }
            } else {
                $json_result = array('Status' => 0);
            }
        } else {
            $json_result = array('Status' => 0);
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  支付成功提示
     */
    public function Result()
    {
        $sign = $_POST['Sign'];
        if (isset($_POST['Message'])) {
            $Message = stripcslashes($_POST['Message']);
            unset($_POST['Message']);
        }
        unset($_POST['Sign']);
        $VerifySign = ToolService::VerifyData($_POST);
        if ($VerifySign == $sign) {
            $PayResult = $_POST['PayResult'];
            if ($PayResult == 'SUCCESS') {
                $OrderNo = $_POST['OrderNo'];
                $Money = $_POST['Money'];
                $RedirectUrl = $_POST['RedirectUrl'];
                include template('TourPaySuccess');
            } else {
                include template('TourPayFailure');
            }
        } else {
            include template('TourPayFailure');
        }
    }
    
    /*
     * 识别是不是手机端  
     */
    function IsOrNotMobile()
    { 
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        } 
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        { 
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        } 
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
                ); 
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            } 
        } 
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        { 
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            } 
        } 
        return false;
    }    
}