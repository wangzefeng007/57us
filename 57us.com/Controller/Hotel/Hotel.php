<?php
/**
 * 酒店首页
 */

Class Hotel{

    public function __construct(){
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC',0,200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC',0,200);
    }
    
    //首页
    public function Index(){
        include SYSTEM_ROOTPATH.'/Controller/Hotel/HotelApi.php';
        $HotelApi=new HotelApi();
        
        $Title = '美国酒店_美国酒店预订_美国住宿_美国宾馆_美国民宿- 57美国网';
        $Keywords = '美国酒店,美国酒店预订,美国住宿,美国宾馆,美国民宿, 美国酒店价格,美国酒店预订,预定美国酒店,美国酒店预定,美国酒店预订网站,美国宾馆价格';
        $Description = '美国网酒店频道，为您提供最新的美国住宿信息、美国酒店查询及预订服务，酒店档次不一，上万家酒店供您选择，价格优惠、可查看真实入客人对酒店的评价。';
        $city = array('洛杉矶');    //人工推荐城市

        $HotelModule = new HotelHotRecModule();
        $HotelModule_city = new HotelCountryUsModule();

        $hot_info = $HotelModule->GetHotelHot(4);  //热门推荐
        $hot_city = $HotelModule_city->GetHomeCity(9); //首页热门城市

        include template ( 'HotelIndex' );
    }

    //列表
    public function HotelList(){

        $citycode = $_GET['ct'];
        if($citycode){
            $m = new HotelCountryUsModule();
            $infos  = $m->GetCityName($citycode,1);
            $Title = $infos.'酒店预订_'.$infos.'酒店价格_'.$infos.'酒店推荐-57美国网';
            $Keywords = $infos.'酒店,'.$infos.'住宿,'.$infos.'酒店预订,'.$infos.'酒店价格,'.$infos.'酒店推荐,'.$infos.'酒店排名,'.$infos.'酒店查询,'.$infos.'五星级酒店';
            $Description = '57美国网酒店频道，为您提供美国'.$infos.'住宿信息、美国'.$infos.'酒店查询及预订服务，酒店档次不一，上万家酒店供您选择，价格优惠、可查看真实入客人对'.$infos.'酒店的评价。';
        }

        $HotelModule = new HotelHotRecModule();
        $hot_info = $HotelModule->GetHotelHot(4);  //热门推荐

        $star = array('1'=>'<i></i>','2'=>'<i></i><i></i>','2.5'=>'<i></i><i></i><i></i>','3'=>'<i></i><i></i><i></i>','3.5'=>'<i></i><i></i><i></i><i></i>','4'=>'<i></i><i></i><i></i><i></i>','4.5'=>'<i></i><i></i><i></i><i></i><i></i>','5'=>'<i></i><i></i><i></i><i></i><i></i>');  //星级

        include template('HotelList');
    }

    /**
     * @desc  关键字搜索
     */
    public function HotelSearchList(){
        $TagNav ='hotel';
        $HotelBaseModule = new HotelBaseInfoModule();
        $HotelAmenityModule = new HotelAmenityModule();
        $Keyword = $_GET['K'];
        if($Keyword != ''){
            $SoWhere = '?K=' . $Keyword;
        }
        $MysqlWhere = ' AND `Status`=1 AND `LowPrice`>0 AND `Image`!=\'\' and (`Name` like \'%'. $Keyword .'%\' or `Name_Cn` like \'%'. $Keyword .'%\')';
        $MyPageUrl = WEB_HOTEL_URL.'/hotel/hotelsearchlist/?K='.$Keyword;
        $Page = intval ( $_GET ['p'] )>1?$_GET['p']:1;
        $Rscount = $HotelBaseModule->GetListsNum ( $MysqlWhere );
        $PageSize = 10;
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data ['Data'] = $HotelBaseModule->GetLists ( $MysqlWhere, $Offset, $Data ['PageSize'] );
            foreach ( $Data ['Data'] as $Key => $Value ) {
                $Data ['Data'] [$Key] ['TagArray'] = explode ( ',', $Value ['Tag'] );
                $String = '';
                for($s=0;$s<$Value['StarRating'];$s++){
                    $String .= '<i></i>';
                }
                $Data ['Data'] [$Key] ['Star'] = $String;
                $Data ['Data'] [$Key] ['Amenitys'] = $HotelAmenityModule->GetAmenityById($Value['HotelID']);
            }
            $Page=new Page($Data['RecordCount'],8,3);
            $ShowPage=$Page->showpage();
        }
        $HotelModule = new HotelHotRecModule();
        $hot_info = $HotelModule->GetHotelHot(4);  //热门推荐
        include template('HotelSearchList');
    }

    //详情
    public function Detail(){

        $hotelmodule = new HotelBaseInfoModule();
        $Amenity = new HotelAmenityModule();
        $IMG = new HotelImageModule();
        $HotelID = trim($_GET['HotelID']);

        $hotel_info = $hotelmodule->GetHotelByID($HotelID);
        $hotel_info['hotel_name'] = $hotel_info['Name'];
        if($hotel_info['Name_Cn']){
            $hotel_info['hotel_name'] = $hotel_info['Name'].'('.$hotel_info['Name_Cn'].')';
        }
        //添加浏览记录
        $Type=5;
        MemberService::AddBrowsingHistory($HotelID,$Type);
        $Title =  $hotel_info['hotel_name'].'预订_'. $hotel_info['hotel_name'].'价格 - 57美国网';
        $Keywords = $hotel_info['hotel_name'].'预订, '. $hotel_info['hotel_name'].'价格, '. $hotel_info['hotel_name'].'查询';
        $Description = '57美国网提供'. $hotel_info['hotel_name'].'最优的价格预订及查询服务，更有'. $hotel_info['hotel_name'].'图片、地址、点评、联系电话等相关酒店信息，为您带来更优惠、更安全的美国酒店预订服务。';
        $star = array('1'=>'<i></i>','2'=>'<i></i><i></i>','2.5'=>'<i></i><i></i><i></i>','3'=>'<i></i><i></i><i></i>','3.5'=>'<i></i><i></i><i></i><i></i>','4'=>'<i></i><i></i><i></i><i></i>','4.5'=>'<i></i><i></i><i></i><i></i><i></i>','5'=>'<i></i><i></i><i></i><i></i><i></i>');
        if($hotel_info['Amenity']){
            $amenity_info = $Amenity->GetAmenity($hotel_info['Amenity']);
        }
        $room = $hotel = array();
        if($amenity_info){
            foreach($amenity_info as $v){
                if($v['Type']=='Hotel'){
                    $hotel[] = $v['AmenityName'];
                }else{
                    $room[] = $v['AmenityName'];
                }
            }
        }
        $img_info = $IMG->GetImgByHotelID($HotelID);//图片
        //设施
        $Modules = new HotelAmenityModule();
        $amenity = $Modules->GetAmenityById($hotel_info['HotelID']);
        $start_time = $_GET['sd'];
        $end_time = $_GET['ed'];

        $HotelModule = new HotelHotRecModule();
        $hot_info = $HotelModule->GetHotelHot(4);  //热门推荐

        include template('HotelDetail');
    }
    
    //酒店预定页面
    public function Order(){
        $HotelID=intval($_GET['HotelID']);
        $RatePlanID=trim($_GET['RatePlanID']);           
        $CheckInDate=trim($_GET['CheckInDate']);
        $CheckOutDate=trim($_GET['CheckOutDate']);
        $Days=(strtotime($CheckOutDate)-strtotime($CheckInDate))/(3600*24);
        $NumOfRooms=1;
        $AdultCount=intval($_GET['AdultCount']);
        $ChildCount=isset($_GET['ChildCount'])?intval($_GET['ChildCount']):0;
        $ChildAge=isset($_GET['ChildAge'])?explode(',',$_GET['ChildAge']):array();
        $ChildAgeStr='[';
        foreach($ChildAge as $Age){
            $ChildAges.=$Age.',';
        }
        $ChildAgeStr.=rtrim($ChildAges,',').']';
        include SYSTEM_ROOTPATH.'/Controller/Hotel/HotelApi.php';
        $HotelApi=new HotelApi();
        //确认酒店价格
        $result=$HotelApi->PriceConfirm($HotelID, $RatePlanID, $CheckInDate, $CheckOutDate, $NumOfRooms, array(array('AdultCount'=>$AdultCount,'ChildCount'=>$ChildCount,'ChildAge'=>$ChildAge)));
        if(isset($result['Success']) && isset($result['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList'])){
            $HotelBaseInfoModule=new HotelBaseInfoModule();
            $HotelInfo=$HotelBaseInfoModule->GetHotelByID($HotelID);
            if($HotelInfo['Image']){
                $HotelInfo['Img']=ImageURLP4.$HotelInfo['Image'];
            }else{
                $HotelInfo['Img']=ImageURL.'/img/common/loadpic.jpg';
            }
            $OrderInfo['RoomName']=$result['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList']['RatePlan']['RatePlanName'];
            $BedType=$result['Success']['PriceDetails']['HotelList']['Hotel']['RatePlanList']['RatePlan']['BedType'];
            $HotelBedTypeModule=new HotelBedTypeModule();
            $BedTypeInfo=$HotelBedTypeModule->GetInfoByKeyID($BedType);
            $OrderInfo['BedType']=$BedTypeInfo['Name_Cn']."({$BedTypeInfo['Name']})";
            $OrderInfo['Cancel']=$result['Success']['PriceDetails']['HotelList']['Hotel']['CancellationPolicyList'];
            $OrderInfo['Price']=ceil($result['Success']['PriceDetails']['HotelList']['Hotel']['TotalPrice']*0.06+$result['Success']['PriceDetails']['HotelList']['Hotel']['TotalPrice']);
            include template('HotelOrder');
        }else{
            alertandback('该房间已满，请更换房间数或入住人数重新查询房间信息');
        }
    }
}