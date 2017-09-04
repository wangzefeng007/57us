<?php
define('HOTEL_API_URL','http://api.didatravel.com/Services/WebService'); //接口访问的地址
//define('CLIENT_ID','xmmht_api');   //接口所需的ID
//define('LICENSE_KEY','xmmht_api'); //接口所需的KEY
define('CLIENT_ID','XMMHT');   //接口所需的ID
define('LICENSE_KEY','XMMHT'); //接口所需的KEY
define('USA','US');   //美国

class HotelApi {

    public function Apitest(){

        $xml = "";
//        $start = '<GetBedTypeListRQ>';
//        $end = '</GetBedTypeListRQ>';
//        $result = $this->XmlPost($start,$end);
//        $result = $this->GetBreakfast();
        $result = $this->GetHotelList(266);
//
//        $sql = "INSERT INTO hotel_base_info (`id`,`name`,`name_cn`,`address`,`citycode`,`statecode`,`countrycode`,`room`,`zipcode`,`longitude`,`latitude`,`starrating`,`phone`) VALUES";
//        foreach($result as $key=>$v){
//            $enname = htmlentities($v['Name'],ENT_QUOTES, 'utf-8');
//            $enlongname = htmlentities($v['Name_CN'],ENT_QUOTES, 'utf-8');
//            $address = htmlentities($v['Address'],ENT_QUOTES, 'utf-8');
//            if($v['Rooms']['Room']){
//                $room = json_encode($v['Rooms']['Room']);
//            }
//            $sql .= " ('".$v['ID']."','".$enname."','".$enlongname."','".$address."','".$v['CityCode']."','".$v['StateCode']."','".$v['CountryCode']."','".$room."','".$v['ZipCode']."','".$v['Longitude']."','".$v['Latitude']."','".$v['StarRating']."','".$v['Telephone']."'),";
//        }
//        global $DB;
//        $sql = rtrim($sql,',');
////        echo $sql;die;
//        $result = $DB->query($sql);

//        $result = $this->PriceSearch(178280,'2016-05-29','2016-05-30',1,10000,1,1);
//        $result = $this->PriceSearchNow(1454,'2016-05-29','2016-05-31',1,1);
//                $result = $this->PriceSearchNow(1454,'2016-05-29','2016-05-31',1,1); //价格确认接口

//        $result = $this->PriceConfirm(146567,'5:204086:3058335:2:1331','2016-06-015','2016-06-02',1,1,1,1,5);
//        $result = $this->PriceConfirm(33201,'5:36314:154906:2:1331','2016-05-29','2016-05-31',1,1,1,0);
//        $g = array(array('first'=>'hang','last'=>'yi','gender'=>'M','phone'=>11234564566,'address'=>'厦门市软件园观日路34','email'=>'123@qq.com','age'=>30,'idtype'=>'Passport','id'=>350123199,'isadult'=>'true'));
//        $result = $this->BookConfirm('DHB160523105936186','2016-05-29','2016-05-31',1,1,$g,'hang','yi',16123456456,'123@qq.com','无烟','123');
//        $result = $this->BookingSearch('DHB160523105936186');
//        $result = $this->BookingSearchInfo('yi','hang','2');
//        $result = $this->BookingCancel('DHB160523105936186');
//        $result = $this->BookingCancelConfirm('DHB160523105936186','DCC160523135816014');
        print_r($result);
        exit;
    }

    /**
     * 取消订单确认接口
     * @param $BookingID
     * @param $ConﬁrmID
     * @param $Description
     * @return array
     */
    public function BookingCancelConfirm($BookingID,$ConﬁrmID,$Description=''){
        $xml = "<BookingID>".$BookingID."</BookingID>
                <ConfirmID>".$ConﬁrmID."</ConfirmID>
                <Description>".$Description."</Description> ";
        $start = '<HotelBookingCancelConfirmRQ>';
        $end = '</HotelBookingCancelConfirmRQ>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result;
    }

    /**
     * 取消订单接口
     * @param $BookingID //订单ID
     * @return array
     */
    public function BookingCancel($BookingID){
        $xml = "<BookingID>".$BookingID."</BookingID>";
        $start = '<HotelBookingCancelRQ>';
        $end = '</HotelBookingCancelRQ>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result['Success'];
    }

    /**
     * 订单查询详细
     * @param $Last /姓名后面字
     * @param string $Ming 第一个字
     * @param string $Status 状态0预定1确认2取消
     * @param string $CheckInDateRangeFrom /入住时间开始
     * @param string $CheckInDateRangeTo /入住时间截止
     * @param string $CheckOutDateRangeFrom /退房时间开始
     * @param string $CheckOutDateRangeTo /退房时间截止
     * @param string $BookDateRangeFrom /订房时间开始
     * @param string $BookDateRangeTo /订房时间截止
     * @param string $CityCode 城市ID
     * @param string $ClientReference 客户参考号
     * @return array
     */
    public function BookingSearchInfo($Last,$Ming='',$Status='',$CheckInDateRangeFrom=0,$CheckInDateRangeTo=0,$CheckOutDateRangeFrom=0,$CheckOutDateRangeTo=0,$BookDateRangeFrom=0,$BookDateRangeTo=0,$CityCode='',$ClientReference=''){
        $xml = "
                <SearchBy>
                <BookingInfo>
                <GuestName First=\"".$Ming."\" Last=\"".$Last."\"/>";
        if($Status){
            $xml .="<Status>".$Status."</Status>";
        }
        if($CheckInDateRangeFrom || $CheckInDateRangeTo){
            $xml .="<CheckInDateRange from=\"".$CheckInDateRangeFrom."\" to=\"".$CheckInDateRangeTo."\"/>";
        }
        if($CheckOutDateRangeFrom || $CheckOutDateRangeTo){
            $xml .="<CheckOutDateRange from=\"".$CheckOutDateRangeFrom."\" to=\"".$CheckOutDateRangeTo."\"/>";
        }
        if($BookDateRangeFrom || $BookDateRangeTo){
            $xml .="<BookDateRange from=\"".$BookDateRangeFrom."\" to=\"".$BookDateRangeTo."\"/>";
        }
        if($CityCode){
            $xml .="<CityCode>".$CityCode."</CityCode>";
        }
        $xml .="
                <ClientReference>".$ClientReference."</ClientReference>
                </BookingInfo>
                </SearchBy>";
        $start = '<HotelBookingSearchRQ>';
        $end = '</HotelBookingSearchRQ>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result;
    }

    /**
     * 订单查询
     * @param $BookingID /订单ID
     * @return mixed
     */

    public function BookingSearch($BookingID){
        $xml = "
                <SearchBy>
                <BookingID>".$BookingID."</BookingID>
                </SearchBy>";
        $start = '<HotelBookingSearchRQ>';
        $end = '</HotelBookingSearchRQ>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result;
    }

    /**
     * 订单创建
     * @param $ReferenceNo /订单参考号
     * @param $CheckInDate /入住时间
     * @param $CheckOutDate /退房时间
     * @param $NumOfRooms /房间数
     * @param $RoomNum /房间序号与房间总数相等
     * @param $GuestInfo /入住人信息 应为数组array()
     * @param $First /姓名第一个字
     * @param $Last  /姓名后面字
     * @param $Phone /联系人电话
     * @param $Email /联系人邮件
     * @param string $CustomerRequest /客户有无特殊需求 （如抽烟，无烟）
     * @param string $ClientReference /客户参考号，可填写客户内部订单号等，用户订单检索
     * @return array
     */
    public function BookConfirm($ReferenceNo,$CheckInDate,$CheckOutDate,$NumOfRooms,$GuestList,$ContactInfo,$CustomerRequest='',$ClientReference=''){
        $xml = "
                <ReferenceNo>".$ReferenceNo."</ReferenceNo>
                <CheckInDate>".$CheckInDate."</CheckInDate>
                <CheckOutDate>".$CheckOutDate."</CheckOutDate>
                <NumOfRooms>".$NumOfRooms."</NumOfRooms>
                <GuestList>";
        for($i=0;$i<$NumOfRooms;$i++){
            $xml.="
                    <Room RoomNum=\"".($i+1)."\">";
            foreach($GuestList[$i] as $Guest){
                $xml .= "
                        <GuestInfo>
                        <Name First=\"".strtoupper($Guest['First'])."\" Last=\"".strtoupper($Guest['Last'])."\"/>                      
                        <IsAdult>true</IsAdult>
                        </GuestInfo>";
//                        <Gender>".$Guest['gender']."</Gender>
//                        <Phone>".$Guest['phone']."</Phone>
//                        <Address>".$Guest['address']."</Address>
//                        <Email>".$Guest['email']."</Email>
//                        <Age>".$Guest['age']."</Age>
//                        <IDType>".$Guest['idtype']."</IDType>
//                        <ID>".$Guest['id']."</ID>
            }
            $xml.="
                    </Room>";
        }
        $xml .= "
                </GuestList>
                <Contact>
                <Name First=\"".strtoupper($ContactInfo['First'])."\" Last=\"".strtoupper($ContactInfo['Last'])."\"/>
                <Phone>".$ContactInfo['Phone']."</Phone>
                <Email>".$ContactInfo['EMail']."</Email>
                </Contact>
                <CustomerRequest>".$CustomerRequest."</CustomerRequest>
                <ClientReference>".$ClientReference."</ClientReference> ";
        $start = '<HotelBookingConfirmRQ>';
        $end = '</HotelBookingConfirmRQ>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result;
    }

    /**
     * 酒店价格确认
     * @param $HotelID /酒店ID(必填)
     * @param $RatePlanID /价格计划ID(必填)
     * @param $CheckInDate /入住时间(必填)
     * @param $CheckOutDate /退房时间(必填)
     * @param $NumOfRooms /房间数（必填）
     * @param $AdultCount /大人数量（必填）
     * @param int $ChildCount 孩子数量
     * @param string $ChildAge 孩子年龄（当孩子数量大于0为必填）
     * @return array
     */
    public function PriceConfirm($HotelID,$RatePlanID,$CheckInDate,$CheckOutDate,$NumOfRooms,$OccupancyDetails){
        $xml = "
                        <HotelID>".$HotelID."</HotelID>
                       <RatePlanID>".$RatePlanID."</RatePlanID>
                       <CheckInDate>".$CheckInDate."</CheckInDate>
                       <CheckOutDate>".$CheckOutDate."</CheckOutDate>
                       <NumOfRooms>".$NumOfRooms."</NumOfRooms>
                       <OccupancyDetails>";
        for($i=0;$i<$NumOfRooms;$i++){
            $xml.="
                    <RoomOccupancy RoomNum=\"".($i+1)."\" AdultCount=\"".$OccupancyDetails[$i]['AdultCount']."\" ChildCount=\"".$OccupancyDetails[$i]['ChildCount']."\">";
            if($OccupancyDetails[$i]['ChildCount']){
                $xml .="
                        <ChildAgeDetails>";
                for($j=0;$j<$OccupancyDetails[$i]['ChildCount'];$j++){
                    $xml.="
                            <ChildAge>".$OccupancyDetails[$i]['ChildAge'][$j]."</ChildAge>";
                }
                $xml.="
                        </ChildAgeDetails>";
            }
            $xml .="
                    </RoomOccupancy>";
        }
        $xml .="
                       </OccupancyDetails>
                       <PreBook>true</PreBook>";
        $start = '<PriceConfirmRequest>';
        $end = '</PriceConfirmRequest>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result; //成功Success 失败Error
    }

    /**
     * 获取实时价格
     * @param $HotelID /酒店ID(必填)
     * @param $CheckInDate /入住时间(必填)
     * @param $CheckOutDate /退房时间(必填)
     * @param $RoomCount /预定房间数(必填)
     * @param $AdultCount /大人数量(必填)
     * @param string $ChildCount /小孩数量
     * @param string $ChildAge /小孩年龄（当小孩数量大于0为必填）
     * @return array
     */
    public function PriceSearchNow($HotelID,$CheckInDate,$CheckOutDate,$RoomCount,$AdultCount,$ChildCount=0,$ChildAge=""){
        if($ChildCount>0 && empty($ChildAge)){
            return array('status'=>false,'msg'=>'已经有小孩人数，小孩年龄为必填');
        }
        $xml = "
                <HotelIDList>
                <HotelID>".$HotelID."</HotelID>
                </HotelIDList>
                <CheckInDate>".$CheckInDate."</CheckInDate>
                <CheckOutDate>".$CheckOutDate."</CheckOutDate>
                <IsRealTime RoomCount=\"".$RoomCount."\">true</IsRealTime>
                <RealTimeOccupancy AdultCount=\"".$AdultCount."\" ChildCount=\"".$ChildCount."\">";
        if($ChildCount>0){
            $xml .= "
                    <ChildAgeDetails>";
            foreach($ChildAge as $Age){
                $xml .= "
                        <ChildAge>".$Age."</ChildAge>";
            }  
            $xml .= "                
                    </ChildAgeDetails>";
        }
        $xml .= "
                </RealTimeOccupancy>";
        $start = '<PriceSearchRequest>';
        $end = '</PriceSearchRequest>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result['Success']['PriceDetails'];
    }

    /**
     * 获取酒店缓存价格信息
     * @param $city_id /城市ID(必填)
     * @param $HotelID /酒店ID(必填)
     * @param $CheckInDate /入住时间(必填)
     * @param $CheckOutDate /退房时间(必填)
     * @param string $price_from 价格区间开始
     * @param string $price_to 价格区间结束
     * @param string $bad_type 床铺类型
     * @param string $Breakfast 早餐类型
     * @param string $PersonCount 入住人数
     * @param string $HotelKeyWord 关键词
     * @param string $StarRating 星级
     * @return mixed
    <HotelIDList>
    <HotelID>".$HotelID."</HotelID>
    </HotelIDList>//
     */
    /**

    <FilterList>
    <BedTypeID>".$bad_type."</BedTypeID>
    <BreakfastTypeID>".$Breakfast."</BreakfastTypeID>
    <PersonCount>".$PersonCount."</PersonCount>
    <HotelKeyWord>".$HotelKeyWord."</HotelKeyWord>
    <StarRating>".$StarRating."</StarRating>
    <PriceRange from=\"".$price_from."\" to=\"".$price_to."\"/> </FilterList>
     */
    public function PriceSearch($city_id,$CheckInDate,$CheckOutDate,$price_from=0,$price_to=0,$bad_type=0,$Breakfast=0,$PersonCount=0,$HotelKeyWord='',$StarRating=0){
        //$xml = "<Destination CityCode=\"".$city_id."\"/>
            $xml = "<HotelIDList>
                        <HotelID>$city_id</HotelID>
                    </HotelIDList>
                <CheckInDate>".$CheckInDate."</CheckInDate>
                <CheckOutDate>".$CheckOutDate."</CheckOutDate>";
//        if($price_from || $price_to || $bad_type || $Breakfast || $PersonCount || $HotelKeyWord || $StarRating){
//            $xml .= "<FilterList>";
//            if($bad_type){
//                $xml .= "<BedTypeID>".$bad_type."</BedTypeID>";
//            }
//            if($Breakfast){
//                $xml .= "<BreakfastTypeID>".$Breakfast."</BreakfastTypeID>";
//            }
//            if($PersonCount){
//                $xml .= "<PersonCount>".$PersonCount."</PersonCount>";
//            }
//            if($HotelKeyWord){
//                $xml .= "<HotelKeyWord>".$HotelKeyWord."</HotelKeyWord>";
//            }
//            if($StarRating){
//                $xml .= "<StarRating>".$StarRating."</StarRating>";
//            }
//            if($price_from || $price_to){
//                $xml .= "<PriceRange from=\"".$price_from."\" to=\"".$price_to."\"/>";
//            }
//            $xml .= "</FilterList>";
//        }
        $start = '<PriceSearchRequest>';
        $end = '</PriceSearchRequest>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result['Success'];
    }

    /**
     * 通过酒店ID获取酒店列表（单个也就是对应ID的酒店）
     * @param $HotelId
     * @return mixed
     */
    public function GetHotelByHotelId($HotelId){
        $xml = "
                <HotelIDList>
                <HotelID>".$HotelId."</HotelID>
                </HotelIDList>
                <Filter>
                <IncludeRatePlans>true</IncludeRatePlans>
                </Filter>";
        $start = '<GetHotelListRQ>';
        $end = '</GetHotelListRQ>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result['Success']['Hotels']['Hotel'];
    }
    /**
     * 获取城市酒店列表
     * @param $city_id 城市ID
     * @return array
     */
    public function GetHotelList($city_id){
        $xml = "<CityCode>".$city_id."</CityCode>
                <Filter>
                    <IncludeRatePlans>true</IncludeRatePlans>
                </Filter>";
        $start = '<GetHotelListRQ>';
        $end = '</GetHotelListRQ>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result['Success']['Hotels']['Hotel'];
    }

    /**
     * 获取早餐的类型
     * @return mixed
     */
    public function GetBreakfast(){
        $start = '<GetBreakfastTypeListRQ>';
        $end = '</GetBreakfastTypeListRQ>';
        $result = $this->XmlPost($start,$end);
        return $result['Success']['Breakfasts']['Breakfast'];
    }

    /**
     * 获取床铺类型
     * @return mixed
     */
    public function GetBedType(){
        $start = '<GetBedTypeListRQ>';
        $end = '</GetBedTypeListRQ>';
        $result = $this->XmlPost($start,$end);
        return $result['Success']['BedTypes']['BedType'];
    }

    /**
     * 获取城市信息
     * @return array
     */
    public function GetCity(){
        $xml = "<CountryCode>".USA."</CountryCode> ";
        $start = '<GetCityListRQ>';
        $end = '</GetCityListRQ>';
        $result = $this->XmlPost($start,$end,$xml);
        return $result['Success']['Cities']['City'];
    }

    /**
     * POST到指定网址
     * @param $xmldata
     */
    private function XmlPost($start,$end,$xml=''){
            //首先检测是否支持curl
            if (!extension_loaded("curl")) {
                trigger_error("对不起，请开启curl功能模块！", E_USER_ERROR);
            }
            //构造xml
            $xmldata = '<?xml version="1.0" encoding="UTF-8"?>';
            $xmldata .= "
                        ".$start;
            $xmldata .= "
                       <Header>
                       <ClientID>".CLIENT_ID."</ClientID>
                       <LicenseKey>".LICENSE_KEY."</LicenseKey>
                       </Header>";
            $xmldata .= $xml;
            $xmldata .= $end;
            //初始一个curl会话
            $curl = curl_init();
            //设置url
            curl_setopt($curl, CURLOPT_URL,HOTEL_API_URL);
            //设置发送方式：
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER ,array('CLIENT-IP:139.224.187.5','X-FORWARDED-FOR:139.224.187.5'));
            //设置发送数据
            curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            //抓取URL并把它传递给浏览器
            $result =  curl_exec($curl);
            //关闭cURL资源，并且释放系统资源
            curl_close($curl);
            return  $this->simplest_xml_to_array($result);
    }


    /**
     * XML转数组
     * @param string $xmlstring XML字符串
     * @return array XML数组
     */
    function simplest_xml_to_array($xmlstring) {
        return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
    }

}