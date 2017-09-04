<?php

/**
 * @desc  租租车Api接口
 * Class ZuzucheApi
 */
class ZuzucheApi{
    public function __construct() {
    }
    /**
     * 租租车订单下单
     */
    public function GetOrder(){
        if ($_POST){
            $Data['OrderNo'] = trim($_POST['OrderNo']);
            $Data['UserID'] = trim($_POST['UserID']);
            $Data['Money'] = trim($_POST['Money']);
            $Data['driverSurname'] = trim($_POST['driverSurname']);
            $Data['driverGivenname'] = trim($_POST['driverGivenname']);
            $Data['driverGender'] = trim($_POST['driverGender']);
            $Data['contractSurname'] = trim($_POST['contractSurname']);
            $Data['contractGivenname'] = trim($_POST['contractGivenname']);
            $Data['contractGender'] = trim($_POST['contractGender']);
            $Data['contractCityInfo'] = trim($_POST['contractCityInfo']);
            $Data['contractNationality'] = trim($_POST['contractNationality']);
            $Data['drivingLineceType'] = trim($_POST['drivingLineceType']);
            $Data['drivingLineceCode'] = trim($_POST['drivingLineceCode']);
            $Data['OrderName'] = trim($_POST['OrderName']);
            $Data['CreateTime'] = trim($_POST['CreateTime']);
            $Data['contractPhone'] = trim($_POST['contractPhone']);
            $Data['contractEmail'] = trim($_POST['contractEmail']);
            $Data['OrderInfo'] = trim($_POST['OrderInfo']);
            $Data['QuoteID'] = trim($_POST['QuoteID']);
            $Data['IP'] = trim($_POST['IP']);
            $Data['flightNum'] = trim($_POST['flightNum']);
            $Data['Extras'] = trim($_POST['Extras']);
            $I = 101;
            foreach ($Data as $Key=>$Value)
            {
                if ($Key != 'UserID' && $Key != 'flightNum' && $Key != 'Extras')
                {
                    if ($Value == '')
                    {
                        $JsonResult = array ("ResultCode" => $I, "Message" => $Key.'字段不能为空');
                        EchoResult ( $JsonResult );
                    }
                }
                $I++;
            }
			//日志START
//			$date = date("Ymd",time());
//			$fileName = "$date.log";
//			$log_path = SYSTEM_ROOTPATH."/Logs/Zuzuche/";
//			$time = date("y-m-d h:i:s",time());
//			$ip =$_SERVER['REMOTE_ADDR'];
//			$URL = $_SERVER['HTTP_REFERER'];
//			$arg ='&';
//			foreach ($Data as $Key=>$Value){
//				$arg.=$Key."=".$Value."&";
//			}
//			$argSign = md5('5151dfa@ADdfa@AD'.$arg.'5151dfa@ADdfa@AD');
//			$log_obj = new Logs($log_path, $fileName);//创建目录文件
//			$log_obj->setLog("ZuzucheApi | $time | $ip | $arg | $argSign |  ".$_POST['Sign']." | $URL\r\n");//写入日志内容
			//日志END
            $pregemail = '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
            preg_match_all($pregemail, $Data['contractEmail'], $contractEmail);
            if(empty($contractEmail[0][0])){
                $JsonResult = array ("ResultCode" => 206, "Message" => '邮箱格式不正确');
                EchoResult ( $JsonResult );
            }
            preg_match_all("/^1[34578]\d{9}$/", $Data['contractPhone'], $mobiles);
            if(empty($mobiles[0][0])){
                $JsonResult = array ("ResultCode" => 207, "Message" => '手机格式不正确');
                EchoResult ( $JsonResult );
            }
            //数据校验
            $Mysign = $this->VerifyData($Data);
            $Sign = trim($_POST['Sign']);
            $Data['OrderNum'] = 'DZC'.date('YmdHis',time()).rand(100,999);
            if ($Sign == $Mysign){
                //后台模拟用户注册,入用户数据库。
                if (intval($Data['UserID']) == '0') {
                    $MemberUserModule = new MemberUserModule();
                    $UserInfo =$MemberUserModule->GetUserIDbyMobile($Data['contractPhone']);
                    if ($UserInfo){
                        $UserID = $UserInfo['UserID'];
                    }else{
                    $Date['Mobile'] = $Data['contractPhone'];
                    $Date['AddTime'] = time();
                    $UserID =$MemberUserModule->InsertInfo($Date);
                        $MemberUserInfoModule = new MemberUserInfoModule ();
                        $InfoData ['UserID'] = $UserID;
                        $InfoData ['NickName'] = '57US_U' . date ( 'is' ) . mt_rand ( 1000, 9999 );
                        $InfoData ['BirthDay'] = date ( 'Y-m-d', $Data ['AddTime'] );
                        $InfoData ['LastLogin'] = date ( 'Y-m-d H:i:s', $Data ['AddTime'] );
                        $MemberUserInfoModule->InsertData($InfoData);
                    }
                }
                $Data['UserID'] = $UserID;
                $Data['UpdateTime'] = $Data['CreateTime'];
                $Data['ExpirationTime'] = date ( 'Y-m-d H:i:s', time () + 900 );
                $XpirationDate=time () + 3600 * 24;
                setcookie ( "session_id", session_id (), $XpirationDate, "/", "57us.com" );
                $_SESSION ['UserID'] = $UserID;
                $_SESSION ['NickName'] = $UserInfo['NickName'];
                setcookie ( "UserID", $_SESSION ['UserID'], time () + 3600 * 24, "/", "57us.com" );
                //订单入库
                $ZucheOrderModule = new ZucheOrderModule();
                $InsertOrder = $ZucheOrderModule->InsertInfo($Data);
                if ($InsertOrder){
                    ToolService::SendSMSNotice(15160090744, '已产生租车订单，订单号：'.$Data['OrderNum'].'，预订人：'.$Data['driverSurname'].$Data['driverGivenname'] .' ，联系电话：'.$Data['contractPhone'].'。');
                    ToolService::SendSMSNotice(18750258578, '已产生租车订单，订单号：'.$Data['OrderNum'].'，预订人：'.$Data['driverSurname'].$Data['driverGivenname'] .' ，联系电话：'.$Data['contractPhone'].'。');
                    ToolService::SendSMSNotice(18050016313, '已产生租车订单，订单号：'.$Data['OrderNum'].'，预订人：'.$Data['driverSurname'].$Data['driverGivenname'] .' ，联系电话：'.$Data['contractPhone'].'。');
                    $JsonResult = array ("ResultCode" => 200, "Message" => '返回成功',"OrderNum"=> $Data['OrderNum'] );
                }else{
                    $JsonResult = array ("ResultCode" => 201, "Message" => '返回失败');
                }
                EchoResult ( $JsonResult );
            }else{
                $JsonResult = array ('ResultCode' => 302, 'Message' => '数据校验不正确' );
                EchoResult ( $JsonResult );
            }
        }else{
            $JsonResult = array ('ResultCode' => 505, 'Message' => '未提交数据' );
            EchoResult ( $JsonResult );
        }
    }
    /**
     * 接口用户ID识别用户
     */
    public function GetUserInfo(){

        $UserID = trim ( $_POST ['UserID'] );
        $Date['UserID'] = $UserID;
        $Date['CreateTime'] = trim($_POST['CreateTime']);


        $Sign = trim($_POST['Sign']);
        $Mysign = $this->VerifyData($Date);
            if (! $UserID ) {
                 $JsonResult = array ('ResultCode' => 101, 'Message' => '缺少UserID' );
                  EchoResult ( $JsonResult );
            }
            if (! $Date['CreateTime'] ) {
                 $JsonResult = array ('ResultCode' => 102, 'Message' => '缺少CreateTime' );
                  EchoResult ( $JsonResult );
            }
            if(! $Sign){
                $JsonResult = array ('ResultCode' => 103, 'Message' => '缺少Sign' );
                EchoResult ( $JsonResult );
            }
        if ($Mysign == $Sign){
            $MemberUserInfoModule = new MemberUserInfoModule();
            $UserInfo = $MemberUserInfoModule->GetUserInfo($UserID);
            if ($UserInfo){
                $JsonResult = array ('ResultCode' => 200, 'Message' => '返回成功','IsLogin'=>'1','NickName' => $UserInfo['NickName'],'UserID' =>$UserInfo['UserID'] );
            }else{
                $JsonResult = array ('ResultCode' => 301, 'Message' => '未找到该用户' );
            }
            EchoResult ( $JsonResult );
        }else{
            $JsonResult = array ('ResultCode' => 302, 'Message' => '数据校验不正确' );
            EchoResult ( $JsonResult );
        }
    }
    /**
     * 接口申请取消订单
     */
    public function BackOrder(){
        $ZucheOrderModule = new ZucheOrderModule();
        $Date['OrderNum'] = trim($_POST['OrderNum']);
        $Date['RefundReason'] = trim($_POST['RefundReason']);
        $Date['CreateTime'] = trim($_POST['CreateTime']);

        $Sign = trim($_POST['Sign']);
        if(! $Date['OrderNum']){
            $JsonResult = array ('ResultCode' => 101, 'Message' => '缺少订单号' );
            EchoResult ( $JsonResult );
        }
        if(! $Date['CreateTime']){
            $JsonResult = array ('ResultCode' => 102, 'Message' => '缺少CreateTime' );
            EchoResult ( $JsonResult );
        }
        if(! $Sign){
            $JsonResult = array ('ResultCode' => 103, 'Message' => '缺少Sign' );
            EchoResult ( $JsonResult );
        }
        //数据校验
        $Mysign = $this->VerifyData($Date);
        if ($Mysign == $Sign){
            $Data['UpdateTime'] = $Date['CreateTime'];
            $Data['Status'] = '待退款';
            $Data['RefundReason'] = $Date['RefundReason'];
            //更新订单状态
            $UpdateStatus = $ZucheOrderModule ->UpdateByOrderNum($Data, $Date['OrderNum']);
            if ($UpdateStatus){
                $JsonResult = array ('ResultCode' => 200, 'Message' => '返回成功' );
            }else{
                $JsonResult = array ('ResultCode' => 301, 'Message' => '返回失败' );
            }
            EchoResult ($JsonResult);
        }else{
            $JsonResult = array ('ResultCode' => 302, 'Message' => '数据校验不正确' );
            EchoResult ($JsonResult);
        }
    }


    //数据验证
    private function VerifyData($para){
        if(!is_array($para)){
            return false;
        }else{
            $arg ='&';
			foreach ($para as $Key=>$Value){
				$arg.=$Key."=".$Value."&";
			}
            //如果存在转义字符，那么去掉转义
            if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
            $SignKey='5151dfa@ADdfa@AD';
            $sign=md5($SignKey.$arg.$SignKey);
            return $sign;
        }
    }
} 