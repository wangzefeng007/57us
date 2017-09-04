<?php
class Hotel {
    public function __construct(){
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC',0,200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC',0,200);
    }

    //支付结果处理
    public function Result(){
        $Sign=$_GET['Sign'];
        unset($_GET['Sign']);
        $VerifySign=$this->VerifyData($_GET);
        if($VerifySign==$Sign){
            if($_GET['ResultCode']=="SUCCESS"){
                $HotelOrderModule=new HotelOrderModule();
                $OrderInfo=$HotelOrderModule->GetOrderInfoByNo($_GET['OrderNo']);
                if($OrderInfo){
                    if($_GET['Money']==$OrderInfo['Money']){
                        $Data['OrderNo']=$OrderInfo['OrderNo'];
                        $Data['Money']=$OrderInfo['Money'];
                        $Data['PayResult']='SUCCESS';
                        $Data['RedirectUrl']=WEB_MEMBER_URL.'/tourmember/hotelorderdetails/?ID='.$OrderInfo['OrderID'];
                        if($OrderInfo['Status']==1){
                          if($_GET['PayType'] == '支付宝'){
                                $PayType = 1;
                            }
                            elseif($_GET['PayType'] == '微信支付'){
                                $PayType = 2;
                            }
                            //插入日志
                            $TourProductOrderLogModule=new TourProductOrderLogModule();
                            $OrderLogData['OrderNumber']=$OrderInfo['OrderNo'];
                            if($_SESSION['UserID'] && !empty($_SESSION['UserID'])){
                                    $OrderLogData['UserID']=$_SESSION['UserID'];
                            }else{
                                    $MemberUserModule=new MemberUserModule();
                                    $UserInfo=$MemberUserModule->GetUserIDbyMobile($OrderInfo['ContactPhone']);
                                    $OrderLogData['UserID']=$UserInfo['UserID'];
                            }
                            $OrderLogData['OldStatus']=1;
                            $OrderLogData['NewStatus']=2;
                            $OrderLogData['Remarks']='完成支付,待确认';
                            $OrderLogData['OperateTime']=date('Y-m-d H:i:s');
                            $OrderLogData['IP']=GetIP();
                            $OrderLogData['Type']=3;
                            $TourProductOrderLogModule->InsertInfo($OrderLogData);
                            $result = $HotelOrderModule->UpdateByOrderNum(array('Status'=>2,'PayType'=>$PayType,'UpdateTime'=>date("Y-m-d H:i:s",time())),$Data['OrderNo']);
                            if(!$result){
                                $Data['PayResult']='FAIL';
                            }else{
                                $_SESSION['HotelOrderNo']=$OrderInfo['OrderNo'];
                                $_SESSION['HotelContactPhone']=$OrderInfo['ContactPhone'];
                                setcookie ( "session_id", session_id (), time () + 3600 * 24, "/", WEB_HOST_URL );
                                $CheckOrderResult=curl_postsend(WEB_HOTEL_URL.'/ajaxhotel.html',array('Intention'=>'CheckOrder','OrderNo'=>$OrderInfo['OrderNo'],'ContactPhone'=>$OrderInfo['ContactPhone'],'OrderID'=>$OrderInfo['OrderID']));
                                $CheckOrderResult=json_decode($CheckOrderResult,true);
                                if($CheckOrderResult['ResultCode']!=200){
                                    $HotelOrderModule->UpdateByOrderNum(array('Status'=>5,'UpdateTime'=>date("Y-m-d H:i:s",time()),'Closereason'=>'出单失败,库存不足'),$Data['OrderNo']);
                                }else{
                                    ToolService::SendSMSNotice(15160090744, '酒店订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                                    ToolService::SendSMSNotice(18750258578, '酒店订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                                    ToolService::SendSMSNotice(18050016313, '酒店订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                                    ToolService::SendSMSNotice(15980805724, '酒店订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                                    //发送短信通知
                                    $times = date("Y/m/d",time());
                                    $RoomPersonNum=json_decode($OrderInfo['RoomPersonNum'],true);
                                    ToolService::SendSMSNotice($OrderInfo['ContactPhone'],"订单号".$Data['OrderNo']."，".$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName']." $times ".$OrderInfo['HotelName']."，总价￥".$OrderInfo['Money']."。成人数：".$RoomPersonNum[0]['AdultCount']."，儿童数：".$RoomPersonNum[0]['ChildCount']."。我们将于24小时内确认库存，请登录会员中心查看订单详情或致电400-018-5757。");
                                    //发送邮箱通知
                                    @ToolService::SendEMailNotice($OrderInfo['ContactEMail'], "57美国网酒店订单通知", "您已成功付款！订单编号：{$Data['OrderNo']}。请登录官网会员中心查询订单。客服电话：+86 592-5919203，微信公众号：57美国网，57美国网祝您旅途愉快！");
                                }
                            }
                        }
                    }else{
                        $Data['PayResult']='FAIL';
                    }
                }else{
                    $Data['PayResult']='FAIL';
                }
            }else{
               $Data['PayResult']='FAIL';
            }
        }else{
            $Data['PayResult']='FAIL';
        }
        $Data['RunTime']=time();
        $Data['Sign']=$this->VerifyData($Data);
        if($Data['PayResult']=='SUCCESS'){
            $Data['Message']="<div class=\"PayMoney\">¥<i>".$Data['Money']."</i></div><div class=\"PayInstro\">订单信息已发送至联系人的手机与邮箱，请注意查收<br>工作人员会与您联系，帮助您了解更多信息</div>";
        }        
		echo ToolService::PostForm(WEB_MEMBER_URL.'/pay/result/', $Data);
    }
    
    //数据验证
    private function VerifyData($para){
        if(!is_array($para)){
            return false;
        }else{
            $arg  = "";
            while (list ($key, $val) = each ($para)) {
                    $arg.=$key."=".$val."&";
            }
            //去掉最后一个&字符
            $arg = substr($arg,0,count($arg)-2);

            //如果存在转义字符，那么去掉转义
            if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
            $SignKey='57us3cjq29vcu38cn2q0dj01d9c57is7';
            $sign=md5($arg.$SignKey);
            return $sign;
        }    
    }
}
