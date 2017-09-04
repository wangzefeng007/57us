<?php
Class ajax{

    public function Index() {
        $Intention = trim ( $_POST ['Intention'] );
        $this->$Intention ();
    }

    /**
     * 模糊搜索城市
     */
    public function GetCity(){

        $HotelModule = new HotelCountryUsModule();

        $keyword = trim($_GET['keyword']);
        $result = $HotelModule->GetCityByKeyWord($keyword);

        $need = array();
        if($result){
            foreach($result as $key=>$v){
                if($key>10){
                    break;
                }
                $need[$key]['CityCode'] = $v['CityCode'];
                $need[$key]['Name'] = $v['CityCame_Cn'];
                $need[$key]['Name_Long'] = $v['CityLongName_Cn'];
            }
        }
        EchoResult ( $need );
    }
    
    //根据条件获取酒店列表
    public function GetHotelList(){

        $Module = new HotelBaseInfoModule();
        $Modules = new HotelAmenityModule();
        $CityCode = trim($_GET['CityCode']);
        if(empty($CityCode)){
            $result = array('status'=>false,'msg'=>'目的地不能为空');
            EchoResult($result);
        }
        $StartTime = $_GET['StartTime'];
        $EndTime = $_GET['EndTime'];
        if(empty($StartTime) || empty($EndTime)){
            $result = array('status'=>false,'msg'=>'入住或退房时间不能为空');
            EchoResult($result);
        }

        $Page = $_GET['selectPage'] ? intval($_GET['selectPage']) : '1';
        $PageSize = 8;

        $where = ' AND `Status`=1 AND `CityCode`='.$CityCode.' AND `LowPrice`>0 AND `Image`!=\'\'';

        $Star = $_GET['starts'];
        if($Star){
            if($Star>1 && $Star<5){
                $Stars = $Star-0.5;
                $where .= ' AND (`StarRating`='.$Star.' or `StarRating`='.$Stars.')';
            }else{
                $where .= ' AND `StarRating`='.$Star;
            }
        }
        $Price = trim($_GET['price']);
        $Price = explode('-',$Price);
        if($Price[0]){
            $where .= ' AND `LowPrice`>='.$Price[0];
        }
        if($Price[1]){
            $where .= ' AND `LowPrice`<='.$Price[1];
        }
        
        $SearchKeyword=trim($_GET['Keyword']);
        if($SearchKeyword!=''){
            $where .=" AND (`Name` like '%$SearchKeyword%' OR replace(`Name_Cn`,' ','') like replace('%$SearchKeyword%',' ',''))";
        }
        
        $Amenity = $_GET['facilities'];
        if($Amenity[0] && $Amenity[0]!='不限'){
            $where .= ' AND MATCH (`Amenity`) AGAINST (\'' . implode(',',$Amenity) . '\' IN BOOLEAN MODE)';
        }
        
        $order = $_GET['sort'];   //排序
        if($order['type'] && $order['type']!='random'){
            $where .= ' order by '.$order['type'].' '.$order['value'];
        }else{
            $where .= ' order by Is_Rec asc';
        }

        $Rscount = $Module->GetListsNum ( $where );
        $Data=array();
        if ($Rscount ['Num']) {
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = intval(min ( $Page, $Data ['PageCount'] ));
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data ['Data'] = $Module->GetLists ( $where, $Offset, $Data ['PageSize'] );
            MultiPage($Data,6);
            foreach ( $Data ['Data'] as $Key => $Value ) {
                $Data ['Data'] [$Key] ['TagArray'] = explode ( ',', $Value ['Tag'] );
            }
        }
        if($Data){
            $Datas=array();
            foreach($Data['Data'] as $k=>$v){
                $shu['hotel_name'] = $v['Name'];
                if($v['Name_Cn']){
                    $shu['hotel_name'] = $v['Name'].'('.$v['Name_Cn'].')';
                }
                $shu['HotelID'] = $v['HotelID'];
                $shu['hotel_star'] = $v['StarRating'];
                $shu['hotal_tel'] = $v['Phone'];
                $shu['address'] = $v['Address'];
                $shu['price'] = $v['LowPrice'];
                $AmenityHtml=$Modules->GetAmenityById($v['HotelID']);
                if($AmenityHtml){
                    $shu['amenity'] =$AmenityHtml ;
                }else{
                    $shu['amenity']='';
                }
                if($Data['Data'][$k]['Image']){
                    $shu['hotel_img'] = ImageURLP6.$Data['Data'][$k]['Image'];
                }else{
                    $shu['hotel_img'] = ImageURL.'/img/common/loadpic.jpg';
                }
                $Datas[] = $shu;
            }
            $Data['Data'] = $Datas;
        }
        EchoResult($Data);
    }

    //获取房型信息
    public function GetRoom(){

        include 'HotelApi.php';
        $bedModule = new HotelBedTypeModule();
        $api = new HotelApi();
        $HotelID = trim($_GET['HotelID']);
        if(empty($HotelID)){
            EchoResult(array('status'=>false,'msg'=>'酒店ID不能为空'));
        }
        $start_time = $_GET['start_time'];
        $end_time = $_GET['end_time'];
        if(empty($start_time) || empty($end_time)){
            EchoResult(array('status'=>false,'msg'=>'入住时间与退房时间不能为空'));
        }
        $room_num = 1;

        $adult_count = trim($_GET['adult_count']);
        if(empty($adult_count)){
            EchoResult(array('status'=>false,'msg'=>'大人数量不能为空'));
        }
        $child_count = trim($_GET['child_count']);
        if(empty($child_count)){
            $child_count = 0;
        }
        $childAge=array();
        $childAgeStr=trim($_GET['child_age']);
        if($childAgeStr){
            $childAge=explode(',',$childAgeStr);
        }
        $room_info = $api->PriceSearchNow($HotelID,$start_time,$end_time,$room_num,$adult_count,$child_count,$childAge);
        $need = array();
        if($room_info['HotelList']['Hotel']['RatePlanList']['RatePlan']){
            if(empty($room_info['HotelList']['Hotel']['RatePlanList']['RatePlan'][0])){
                $room_info['HotelList']['Hotel']['RatePlanList']['RatePlan'] = array($room_info['HotelList']['Hotel']['RatePlanList']['RatePlan']);
            }
            foreach($room_info['HotelList']['Hotel']['RatePlanList']['RatePlan'] as $v){
                $need['RatePlanID'] = $v['RatePlanID'];
                $need['room_type'] = $v['RatePlanName'];
                $bedname = $bedModule->GetBedName($v['BedType']);
                $need['bed_name'] = $bedname;
                $need['num'] = $adult_count+$child_count;
                $rate = $v['RatePlanCancellationPolicyList']['CancellationPolicy']['Amount'];
                if($v['TotalPrice']==$rate){
                    $need['rate'] = '不可取消';
                }else{
                    $need['rate'] = '可取消';
                }
                if($v['BreakfastType']==1){
                    $need['Breakfast'] = '不含早餐';
                }else{
                    $need['Breakfast'] = '含早餐';
                }
                $need['price'] = ceil($v['TotalPrice']+$v['TotalPrice']*0.06);
                if($childAgeStr){
                    $need['url'] = WEB_HOTEL_URL.'/hotel/order/?HotelID='.$HotelID.'&RatePlanID='.$v['RatePlanID'].'&CheckInDate='.$start_time.'&CheckOutDate='.$end_time.'&AdultCount='.$adult_count.'&ChildCount='.$child_count.'&ChildAge='.$childAgeStr;
                }else{
                    $need['url'] = WEB_HOTEL_URL.'/hotel/order/?HotelID='.$HotelID.'&RatePlanID='.$v['RatePlanID'].'&CheckInDate='.$start_time.'&CheckOutDate='.$end_time.'&AdultCount='.$adult_count.'&ChildCount='.$child_count;                    
                }
                $result[] = $need;
            }
            $info = array('status'=>true,'Message'=>'获取成功','data'=>$result);
        }else{
            $info = array('status'=>false,'Message'=>'没有搜索到你想要的房间，可以尝试换个时间搜索');
        }
        EchoResult($info);
    }

    //查询是否可以下单
    private function CheckRoomPrice(){
        include SYSTEM_ROOTPATH.'/Controller/Hotel/HotelApi.php';
        $HotelAPI=new HotelApi();
        $HotelID=intval($_POST['HotelID']);
        $RatePlanID=trim($_POST['RatePlanID']);
        $CheckInDate=trim($_POST['CheckInDate']);
        $CheckOutDate=trim($_POST['CheckOutDate']);
        $NumOfRooms=intval($_POST['RoomNums']);
        $OccupancyDetails=$_POST['OccupancyDetails']; //入住人员详情 多维数组 array(array('AdultCount'=>2,'ChildCount'=>1,'ChildAge'=>array(5)))
        //$OccupancyDetails=array(array('AdultCount'=>2,'ChildCount'=>0,'ChildAge'=>array(0)));
        $result=$HotelAPI->PriceConfirm($HotelID,$RatePlanID,$CheckInDate,$CheckOutDate,$NumOfRooms,$OccupancyDetails);
        if(isset($result['Success']) && isset($result['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList'])){
            $OrderInfo['ReferenceNo']=$result['Success']['PriceDetails']['ReferenceNo'];
            $OrderInfo['Price']=ceil($result['Success']['PriceDetails']['HotelList']['Hotel']['TotalPrice']*0.06+$result['Success']['PriceDetails']['HotelList']['Hotel']['TotalPrice']);
            $OrderInfo['CancellationPolicy']='';
            $OrderInfo['Cancel']['CancellationPolicy']=$result['Success']['PriceDetails']['HotelList']['Hotel']['CancellationPolicyList'];
            if(isset($OrderInfo[Cancel][CancellationPolicy])){
                if(isset($OrderInfo[Cancel][CancellationPolicy][FromDate])){
                    if(strtotime($OrderInfo[Cancel][CancellationPolicy][FromDate])>time()){
                        $OrderInfo['CancellationPolicy']='<p>在 '.date('Y-m-d',strtotime($OrderInfo[Cancel][CancellationPolicy][FromDate])).' 之前可以免费取消，从 '.date('Y-m-d',strtotime($OrderInfo[Cancel][CancellationPolicy][FromDate])).' 开始取消订单,需缴纳罚金 ¥'.ceil($OrderInfo[Cancel][CancellationPolicy][Amount]*0.06+$OrderInfo[Cancel][CancellationPolicy][Amount]).'</p>';
                    }else{
                        $OrderInfo['CancellationPolicy']='<p>预定成功后，订单将不可申请退订或变更，如未到店入住，酒店收取全额房费</p>';
                    }
                }else{
                    $OrderInfo[Cancel][CancellationPolicy]=$OrderInfo[Cancel][CancellationPolicy][CancellationPolicy];
                    if(strtotime($OrderInfo[Cancel][CancellationPolicy][0][FromDate])>time()){
                            $OrderInfo['CancellationPolicy'].='<p>在 '.date('Y-m-d',strtotime($OrderInfo[Cancel][CancellationPolicy][0][FromDate])).' 之前可以免费取消，从 '.date('Y-m-d',strtotime($OrderInfo[Cancel][CancellationPolicy][0][FromDate])).' 开始取消订单,需缴纳罚金 ¥'.ceil($OrderInfo[Cancel][CancellationPolicy][count($OrderInfo[Cancel][CancellationPolicy])-1][Amount]*0.06+$OrderInfo[Cancel][CancellationPolicy][count($OrderInfo[Cancel][CancellationPolicy])-1][Amount]).'</p>';                            
                    }else{
                        $OrderInfo['CancellationPolicy']='<p>预定成功后，订单将不可申请退订或变更，如未到店入住，酒店收取全额房费</p>';
                    }
                }
            }else{
                $OrderInfo['CancellationPolicy']='<p>预定成功后，订单将不可申请退订或变更，如未到店入住，酒店收取全额房费</p>';                
            }
            $json_result=json_encode(array('ResultCode'=>200,'Message'=>'房间可以预定','OrderInfo'=>json_encode($OrderInfo)));
        }else{
            $json_result=json_encode(array('ResultCode'=>100,'Message'=>'当前没有符合条件的房间可以预定，您可以更换条件重新查询!'));
        }
        echo $json_result;
    }

    //创建订单
    private function CreateOrder(){
        include SYSTEM_ROOTPATH.'/Controller/Hotel/HotelApi.php';
        $HotelApi=new HotelApi();
        $HotelID=intval($_POST['HotelID']);
        $RatePlanID=trim($_POST['RatePlanID']);
        $CheckInDate=trim($_POST['CheckInDate']);
        $CheckOutDate=trim($_POST['CheckOutDate']);
        $NumOfRooms=intval($_POST['RoomNums']);
        $OccupancyDetails=$_POST['OccupancyDetails'];
//        $OccupancyDetails=array(array('AdultCount'=>2,'ChildCount'=>0,'ChildAge'=>array()));
        $CheckResult=$HotelApi->PriceConfirm($HotelID,$RatePlanID,$CheckInDate,$CheckOutDate,$NumOfRooms,$OccupancyDetails);
        if(isset($CheckResult['Success'])){
            $ReferenceNo=$CheckResult['Success']['PriceDetails']['ReferenceNo'];
            $GuestList=$_POST['GuestList']; //入住人员详情 三维数组 array('0'=>array(array('First'=>'SAN','Last'=>'ZHANG'),array('First'=>'SAN','Last'=>'ZHANG')),'1'=>array(array('First'=>'SAN','Last'=>'ZHANG'),array('First'=>'SAN','Last'=>'ZHANG')))
            $ContactInfo=$_POST['ContactInfo']; //联系人信息 array('First'=>'SAN','Last'=>'ZHANG','Phone'=>13600000000,'EMail'=>'xxxx@qq.com')
//            $GuestList=array(array(array('First'=>'SAN','Last'=>'ZHANG'),array('First'=>'SI','Last'=>'LI')));
//            $ContactInfo=array('First'=>'SAN','Last'=>'ZHANG','Phone'=>13600000000,'EMail'=>'xxxx@qq.com');
            $CustomerRequest=$_POST['CustomerRequest']?$_POST['CustomerRequest']:array();
            if(count($CustomerRequest)>1){
                $CustomerRequest=implode(',',$CustomerRequest);
                $OrderInfo['CustomerRequest']=$CustomerRequest;
            }
            $CurrentTime=time();
            $OrderInfo['BookingID']=$ReferenceNo;
            $ClientReference=HotelService::GetOrderNumber();
            $OrderInfo['OrderNo']=$ClientReference;
            $OrderInfo['Status']=1;
            $OrderInfo['HotelID']=$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['HotelID'];
            $OrderInfo['HotelName']=$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['HotelName'];
            if($NumOfRooms>1){
                $OrderInfo['RoomName']=$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList']['RatePlan'][0]['RatePlanName'];
                $OrderInfo['BedType']=$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList']['RatePlan'][0]['BedType'];
                $OrderInfo['Breakfast']=$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList']['RatePlan'][0]['BreakfastType'];                
            }else{
                $OrderInfo['RoomName']=$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList']['RatePlan']['RatePlanName'];
                $OrderInfo['BedType']=$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList']['RatePlan']['BedType'];
                $OrderInfo['Breakfast']=$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList']['RatePlan']['BreakfastType'];
            }
            $OrderInfo['Money']=ceil($CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['TotalPrice']*0.06+$CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['TotalPrice']);
            $OrderInfo['CheckInDate']=$CheckResult['Success']['PriceDetails']['CheckInDate'];
            $OrderInfo['CheckOutDate']=$CheckResult['Success']['PriceDetails']['CheckOutDate'];
            $OrderInfo['RoomNum']=$NumOfRooms;
            $OrderInfo['RoomPersonNum']=json_encode($OccupancyDetails);
            $OrderInfo['GuestList']=json_encode($GuestList);
            $OrderInfo['ExpirationTime']=date('Y-m-d H:i:s',$CurrentTime+900);
            $OrderInfo['ContactFirstName']=$ContactInfo['First'];
            $OrderInfo['ContactLastName']=$ContactInfo['Last'];
            $OrderInfo['ContactPhone']=$ContactInfo['Phone'];
            if(isset($_SESSION['UserID']) && $_SESSION['UserID']!=''){
                $OrderInfo['UserID']=$_SESSION['UserID'];
            }else{
                $MemberUserModule=new MemberUserModule();
                $UserAccount=$MemberUserModule->AccountExists($ContactInfo['Phone']);
                if($UserAccount){
                    $OrderInfo['UserID']=$UserAccount['UserID'];
                }else{
                    //新增用户
                    $Data['Mobile'] = $OrderInfo['ContactPhone'];
                    $Data['State'] = 1; 
                    $Data['AddTime'] = Time();
                    $insert_result=$MemberUserModule->InsertInfo($Data);
                    if ($insert_result) {
                        $AccountInfo = $MemberUserModule->AccountExists($Data['Mobile']);
                        $InfoData['UserID'] = $AccountInfo['UserID'];
                        $OrderInfo['UserID']=$AccountInfo['UserID'];
                        $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                        $InfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
                        $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                        $InfoData['Sex'] = 1;
                        $InfoData['Avatar']='/img/man3.0.png';                        
                        $MemberUserInfoModule=new MemberUserInfoModule();
                        $MemberUserInfoModule->InsertData($InfoData);
                    }
                }
            }
            $OrderInfo['ContactEMail']=$ContactInfo['EMail'];
            $OrderInfo['CancellationPolicy']=json_encode($CheckResult['Success']['PriceDetails']['HotelList']['Hotel']['CancellationPolicyList']);
            $OrderInfo['IsInvoice']=0;
            $OrderInfo['AddTime']=date('YmdHis',$CurrentTime);
            $OrderInfo['UpdateTime']=date('YmdHis',$CurrentTime);
            $OrderInfo['Remark']=$_POST['Remark'];
            $OrderInfo['Distributors']='道旅';
            $HotelOrderModule=new HotelOrderModule();
            $Result=$HotelOrderModule->InsertInfo($OrderInfo);
            //添加日志
            $TourProductOrderLogModule=new TourProductOrderLogModule();
            $OrderLogData['OrderNumber']=$OrderInfo['OrderNo'];
            $OrderLogData['UserID']=$OrderInfo['UserID'];
            $OrderLogData['OldStatus']=0;
            $OrderLogData['NewStatus']=1;
            $OrderLogData['OperateTime']=$OrderInfo['AddTime'];
            $OrderLogData['IP']=GetIP();
            $OrderLogData['Type']=3;
            if($Result){

                //发送短信开始
                ToolService::SendSMSNotice(15160090744, '已产生酒店订单，订单号：'.$OrderInfo['OrderNo'].'，预订人：'. $OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                ToolService::SendSMSNotice(18750258578, '已产生酒店订单，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.  $OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                ToolService::SendSMSNotice(18050016313, '已产生酒店订单，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.  $OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                ToolService::SendSMSNotice(15980805724, '已产生酒店订单，订单号：'.$OrderInfo['OrderNo'].'，预订人：'.  $OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName'].' ，联系电话：'.$OrderInfo['ContactPhone'].'。');
                $times = date("Y/m/d",time());
                $RoomPersonNum=json_decode($OrderInfo['RoomPersonNum'],true);
                ToolService::SendSMSNotice($OrderInfo['ContactPhone'],"订单号".$OrderInfo['OrderNo']."，".$OrderInfo['ContactLastName'].$OrderInfo['ContactFirstName']." $times ".$OrderInfo['HotelName']."，总价￥".$OrderInfo['Money']."。成人数：".$RoomPersonNum[0]['AdultCount']."，儿童数：".$RoomPersonNum[0]['ChildCount']."。我们将于48小时后自动关闭未付款订单，请登录会员中心查看订单详情或致电400-018-5757。");
                //发送短信结束

                $OrderLogData['Remarks']='创建订单,待支付';
                $json_result=json_encode(array('ResultCode'=>200,'Message'=>'订单创建完成','Url'=>WEB_HOTEL_URL.'/order/'.$ClientReference.'.html'));
            }else{
                $OrderLogData['Remarks']='创建订单失败';
                $json_result=json_encode(array('ResultCode'=>101,'Message'=>'站内订单生成失败'));
            }
            $TourProductOrderLogModule->InsertInfo($OrderLogData);
        }else{
            $json_result=json_encode(array('ResultCode'=>100,'Message'=>'当前没有符合条件的房间可以预定，您可以更换条件重新查询!'));
        }
        echo $json_result;
    }
    
    //订单确认
    private function CheckOrder(){
        include SYSTEM_ROOTPATH.'/Controller/Hotel/HotelApi.php';
        $OrderNo=$_POST['OrderNo'];
        $ContactPhone=$_POST['ContactPhone'];
        $OrderID=$_POST['OrderID'];
        $HotelOrderModule=new HotelOrderModule();
        $OrderInfo=$HotelOrderModule->GetByOrderNoAndContactPhone($OrderNo,$ContactPhone,$OrderID);
        if($OrderInfo){
            $HotelApi=new HotelApi();
            $BookingResult=$HotelApi->BookConfirm($OrderInfo['BookingID'],$OrderInfo['CheckInDate'], $OrderInfo['CheckOutDate'], $OrderInfo['RoomNum'],json_decode($OrderInfo['GuestList'],true),array('First'=>$OrderInfo['ContactFirstName'],'Last'=>$OrderInfo['ContactLastName'],'Phone'=>$OrderInfo['ContactPhone'],'EMail'=>$OrderInfo['ContactEMail']),$OrderInfo['CustomerRequest'],$OrderNo);
            if(isset($BookingResult['Success']) && $BookingResult['Success']['BookingDetails']['Status']==2){    
                $json_result=json_encode(array('ResultCode'=>200,'Message'=>'出单成功'));
            }else{          
                $json_result=json_encode(array('ResultCode'=>101,'Message'=>'出单失败'));
            }
        }else{
            $json_result=json_encode(array('ResultCode'=>100,'Message'=>'不能操作该订单'));
        }
        echo $json_result;
    }

}
