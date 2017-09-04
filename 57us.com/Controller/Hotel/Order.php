<?php
Class Order{
    public function __construct(){
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC',0,200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC',0,200);
    }

    //选择支付方式
    public function ChoicePay(){
        $OrderNo=trim($_GET['ID']);
        $HotelOrderModule=new HotelOrderModule();
        $OrderInfo=$HotelOrderModule->GetOrderInfo($OrderNo,1);
        if($OrderInfo){
            $Days=(strtotime($OrderInfo['CheckOutDate'])-strtotime($OrderInfo['CheckInDate']))/(3600*24);
            if(strtotime($OrderInfo['ExpirationTime'])-time()>10){
                $ExpirationTime=strtotime($OrderInfo['ExpirationTime'])-time();
                $Hours=floor($ExpirationTime/3600);
                if($Hours<10){
                    $Hours='0'.$Hours;
                }
                $Minutes=floor(($ExpirationTime%3600)/60);
                if($Minutes<10){
                    $Minutes='0'.$Minutes;
                }
                $Seconds=($ExpirationTime%3600)%60;
                if($Seconds<10){
                    $Seconds='0'.$Seconds;
                }
            }else{
                //添加日志
                $TourProductOrderLogModule=new TourProductOrderLogModule();
                $OrderLogData['OrderNumber']=$OrderInfo['OrderNo'];
                $OrderLogData['UserID']=$OrderInfo['UserID'];
                $OrderLogData['OperateTime']=date('Y-m-d H:i:s');
                $OrderLogData['IP']=GetIP();
                $OrderLogData['Type']=3;
                $OrderLogData['OldStatus']=1;
                $OrderLogData['NewStatus']=10;
                $OrderLogData['Remarks']="订单超时";
                $TourProductOrderLogModule->InsertInfo($OrderLogData);
                $UpData['Status']=10;
                $UpData['Closereason']='超时未支付';
                $UpData['UpdateTime']=date('Y-m-d H:i:s',time());
                $HotelOrderModule->UpdateInfoByKeyID($UpData, $OrderInfo['OrderID']);
                alertandgotopage('订单已过期',WEB_MEMBER_URL.'/tourmember/hotelorder/');
            }
            $HotelBedTypeModule=new HotelBedTypeModule();
            $BedTypeInfo=$HotelBedTypeModule->GetInfoByKeyID($OrderInfo['BedType']);
            include template('OrderChoicePay');
        }else{
              alertandgotopage('不能操作的订单',WEB_MEMBER_URL.'/tourmember/hotelorder/');
        }
    }

    //支付准备
    public function Pay(){
        $Type=trim($_GET['Type']);
        $OrderNo=trim($_GET['ID']);
        $HotelOrderModule=new HotelOrderModule();
        $OrderInfo=$HotelOrderModule->GetOrderInfo($OrderNo,1);
        if($OrderInfo){
            if($Type=='alipay'){
                $Data['OrderNo']=$OrderInfo['OrderNo'];
                $Data['Subject']=$OrderInfo['HotelName'];
                $Data['Money']=$OrderInfo['Money'];
                $Data['Body']=$OrderInfo['HotelName'].'·'.$OrderInfo['RoomName'];
                $Data['ReturnUrl']=WEB_MEMBER_URL.'/payhotel/result/';
                $Data['NotifyUrl']=WEB_MEMBER_URL.'/payhotel/result/';
                $Data['ProductUrl']=WEB_HOTEL_URL."/hotel/{$OrderInfo['HotelID']}.html";
                $Data['RunTime']=time();
                $Data['Sign'] = ToolService::VerifyData($Data);
                echo ToolService::PostForm(WEB_MEMBER_URL.'/pay/alipay/',$Data);
            }elseif($Type=='wxpay'){
                $Data['OrderNo']=$OrderInfo['OrderNo'];
                $Data['Subject']=$OrderInfo['HotelName'];
                $Data['Money']=$OrderInfo['Money'];
                $Data['Body']=$OrderInfo['HotelName'].'·'.$OrderInfo['RoomName'];
                $Data['ReturnUrl']=WEB_MEMBER_URL.'/payhotel/result/';
                $Data['RunTime']=time();
                $Data['Sign'] = ToolService::VerifyData($Data);
                echo ToolService::PostForm(WEB_MEMBER_URL.'/pay/wxpay/',$Data);
            }
        }else{
            alertandgotopage('不能操作的订单',WEB_MEMBER_URL.'/tourmember/hotelorder/');
        }
    }    
}