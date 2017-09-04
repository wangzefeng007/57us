<?php
class PayHotel {
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
        $VerifySign=ToolService::VerifyData($_GET);
        if($VerifySign==$Sign){
            if($_GET['ResultCode']=="SUCCESS"){
                $LogMessage = '支付成功';
                $HotelOrderModule=new HotelOrderModule();
                $OrderInfo=$HotelOrderModule->GetOrderInfoByNo($_GET['OrderNo']);
                //获取用户ID
                if($_SESSION['UserID'] && !empty($_SESSION['UserID'])){
                        $UserID =$_SESSION['UserID'];
                }else{
                        $MemberUserModule=new MemberUserModule();
                        $UserInfo=$MemberUserModule->GetUserIDbyMobile($OrderInfo['ContactPhone']);
                        $UserID=$UserInfo['UserID'];
                }
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
                            $result = $HotelOrderModule->UpdateByOrderNum(array('Status'=>2,'PayType'=>$PayType,'UpdateTime'=>date("Y-m-d H:i:s",time())),$Data['OrderNo']);
                            if(!$result){
                                $LogMessage='支付成功(更新订单状态失败)';
                                $Data['PayResult']='FAIL';
                            }else{
                                $_SESSION['HotelOrderNo']=$OrderInfo['OrderNo'];
                                $_SESSION['HotelContactPhone']=$OrderInfo['ContactPhone'];
                                setcookie ( "session_id", session_id (), time () + 3600 * 24, "/", WEB_HOST_URL );
                                $CheckOrderResult=curl_postsend(WEB_HOTEL_URL.'/ajaxhotel.html',array('Intention'=>'CheckOrder','OrderNo'=>$OrderInfo['OrderNo'],'ContactPhone'=>$OrderInfo['ContactPhone'],'OrderID'=>$OrderInfo['OrderID']));
                                $CheckOrderResult=json_decode($CheckOrderResult,true);
                                if($CheckOrderResult['ResultCode']!=200){
                                    $LogMessage=$CheckOrderResult['Message'];
                                    $HotelOrderModule->UpdateByOrderNum(array('Status'=>5,'UpdateTime'=>date("Y-m-d H:i:s",time()),'Closereason'=>'出单失败,库存不足'),$Data['OrderNo']);
                                }else{
                                    //更新资金流
                                    $BankFlowModule = new MemberUserBankFlowModule();
                                    $BankInfo = MemberService::GetUserBankInfo($UserID);
                                    $BankFlowData=array(
                                        'UserID'=>$UserID,
                                        'Amt'=>$Data['Money'],
                                        'Amount'=>$BankInfo['TotalBalance'],
                                        'OperateType'=>2,
                                        'Remarks'=>'酒店预定',
                                        'Type'=>1,
                                        'PayType'=>$PayType,
                                        'FromIP'=>GetIP(),
                                        'AddTime'=>time()
                                        );
                                    $BankFlowModule->InsertInfo($BankFlowData);
                                    ToolService::SendSMSNotice(15160090744, '酒店订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                                    ToolService::SendSMSNotice(18750258578, '酒店订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                                    ToolService::SendSMSNotice(18050016313, '酒店订单已支付，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                                    //发送短信通知
                                    $times = date("Y/m/d",time());
                                    $RoomPersonNum=json_decode($OrderInfo['RoomPersonNum'],true);
                                    ToolService::SendSMSNotice($OrderInfo['ContactPhone'],"订单号".$Data['OrderNo']."，".$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName']." $times ".$OrderInfo['HotelName']."，总价￥".$OrderInfo['Money']."。成人数：".$RoomPersonNum[0]['AdultCount']."，儿童数：".$RoomPersonNum[0]['ChildCount']."。我们将于24小时内确认库存，请登录会员中心查看订单详情或致电400-018-5757。");
                                    //发送邮箱通知
                                    ToolService::SendEMailNotice($OrderInfo['ContactEMail'], "57美国网酒店订单通知", "您已成功付款！订单编号：{$Data['OrderNo']}。请登录官网会员中心查询订单。客服电话：+86 592-5919203，微信公众号：57美国网，57美国网祝您旅途愉快！");
                                }
                            }
                        }
                    }else{
                        $LogMessage='支付成功(订单金额校验出错)';
                        $Data['PayResult']='FAIL';
                    }
                }else{
                    $LogMessage='支付成功(订单编号查询出错)';
                    $Data['PayResult']='FAIL';
                }
                //插入日志
                $TourProductOrderLogModule=new TourProductOrderLogModule();
                $OrderLogData['OrderNumber']=$OrderInfo['OrderNo'];
                $OrderLogData['UserID']=$UserID;
                $OrderLogData['OldStatus']=1;
                $OrderLogData['NewStatus']=2;
                $OrderLogData['Remarks']=$LogMessage;
                $OrderLogData['OperateTime']=date('Y-m-d H:i:s');
                $OrderLogData['IP']=GetIP();
                $OrderLogData['Type']=3;
                $TourProductOrderLogModule->InsertInfo($OrderLogData);
            }else{
               $Data['PayResult']='FAIL';
            }
        }else{
            $Data['PayResult']='FAIL';
        }
        $Data['RunTime']=time();
        $Data['Sign']=ToolService::VerifyData($Data);
        if($Data['PayResult']=='SUCCESS'){
            $Data['Message']="<div class=\"PayMoney\">¥<i>".$Data['Money']."</i></div><div class=\"PayInstro\">订单信息已发送至联系人的手机与邮箱，请注意查收<br>工作人员会与您联系，帮助您了解更多信息</div>";
        }        
		echo ToolService::PostForm(WEB_MEMBER_URL.'/pay/result/',$Data);
    }
    
}
