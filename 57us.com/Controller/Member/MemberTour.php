<?php

/**
 * @desc  会员中心旅游模块
 * Class MemberTour
 */
class MemberTour
{
    public function __construct()
    {
        $this->Nav = 'MemberTour';
    }
    /**
     * @desc 出游订单列表
     */
    public function TourOrderList()
    {
        MemberService::IsLogin();
        $UserNav = 'TourOrderList';
        $TourProductOrderModule = new TourProductOrderModule ();
        //前台订单状态
        $OrderStatus = $TourProductOrderModule->NStatus;
        $ZhiFuStatus = '2,3,4,5,6,7,8';
        $Status = $_GET['S'] ? $_GET['S'] : 0; //默认全部 0-全部
        $UserID = intval($_SESSION ['UserID']);
        switch ($Status) {
            case '0': //全部
                $MysqlWhere = ' and UserID= ' . $UserID;
                break;
            case '1': //待支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` = 1';
                break;
            case '2': //已支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` in (2,3,4)';
                break;
            case '3': //退款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` in (5,6,7,8,9)';
                break;
            case '4': //待评价
                $MysqlWhere = ' and UserID = ' . $UserID . ' and EvaluateDefault = 0 and  `Status` = 4';
                break;
            case '5': //已评价
                $MysqlWhere = ' and UserID = ' . $UserID . ' and EvaluateDefault = 1';
                break;
        }
        $CurrentTime= time();//当前时间
        //分页开始
        $Page = $_GET ['p'] ? intval($_GET ['p']) : 1;
        $PageSize = 6;
        $Rscount = $TourProductOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount ['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $MysqlWhere .= ' order by AddTime desc';
            $Data['Data'] = $TourProductOrderModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            $TourProductModule = new TourProductModule ();
            $TourProductOrderInfoModule = new TourProductOrderInfoModule ();
            $TourProductImageModule = new TourProductImageModule();
            $CategoryModule = new TourProductCategoryModule();
            foreach ($Data['Data'] as $Key => $Value) {
                $ProductOrderInfo = $TourProductOrderInfoModule->GetInfoByOrderNumber($Value ['OrderNumber']);
                $TourProductInfo = $TourProductModule->GetInfoByKeyID($ProductOrderInfo ['TourProductID']);
                $CatecoryInfo = $CategoryModule->GetInfoByKeyID($TourProductInfo['Category']);
                $Data['Data'] [$Key] ['CategoryID'] = $TourProductInfo['Category'];
                $Data['Data'] [$Key] ['CategoryName'] = $CatecoryInfo['CnName'];
                $Data['Data'] [$Key] ['Title'] = _substr($TourProductInfo ['ProductName'], 22);
                $Data['Data'] [$Key] ['TourProductID'] = $TourProductInfo ['TourProductID'];
                $Data['Data'] [$Key] ['ExpirationTime'] = strtotime($Value['ExpirationTime']);
                //图片
                $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($ProductOrderInfo ['TourProductID']);
                $Data['Data'] [$Key] ['ImageUrl'] = ImageURLP2.$TourImagesInfo ['ImageUrl'];
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }
        include template("MemberTourOrderList");
    }
    /**
     * @desc 租车订单列表
     */
    public function CarRentOrderList()

    {
        MemberService::IsLogin();
        $UserNav = 'CarRentOrderList';
        $ZucheOrderModule = new ZucheOrderModule();
        //前台订单状态
        $OrderStatus = $ZucheOrderModule->NStatus;
        $Status = $_GET['S'] ? $_GET['S'] : 0;//默认全部 0-全部
        $UserID = $_SESSION ['UserID'];
        switch ($Status) {
            case '0': //全部
                $MysqlWhere = ' and UserID = ' . $UserID;
                break;
            case '1': //未付款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` = 1';
                break;
            case '2': //已付款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` in (2,3,4)';
                break;
            case '3': //退款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` in (5,6,7,8,9)';
                break;
        }
        $CurrentTime= time();//当前时间
        $Page = $_GET ['p'] ? intval($_GET ['p']) : 1;
        $PageSize = 6;
        $Rscount = $ZucheOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount ['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $MysqlWhere .= ' order by CreateTime desc';
            $Data['Data'] = $ZucheOrderModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $Key => $Value) {
                $Data['Data'][$Key]['QuoteDetail'] = json_decode($Value['QuoteDetail'], true);
                $Data['Data'] [$Key] ['ExpirationTime'] = strtotime($Value['ExpirationTime']);
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }
        include template("MemberTourCarRentOrderList");
    }
    /**
     * @desc 酒店订单列表
     */
    public function HotelOrderList()
    {
        MemberService::IsLogin();
        $UserNav = 'HotelOrderList';
        $HotelOrderModule = new HotelOrderModule();
        //前台订单状态
        $OrderStatus = $HotelOrderModule->NStatus;
        $Status = $_GET['S'] ? $_GET['S'] : 0; //默认全部 0-全部
        $UserID = intval($_SESSION ['UserID']);
        switch ($Status) {
            case 0://全部
                $MysqlWhere = ' and UserID='.$UserID;
                break;
            case 1://未付款
                $MysqlWhere = " and UserID=$UserID and `Status`=1";
                break;
            case 2://已付款
                $MysqlWhere = " and UserID=$UserID and `Status` in (2,3,4)";
                break;
            case '3': //退款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` in (5,6,7,8,9)';
                break;
        }
        $CurrentTime= time();//当前时间
        $Page = $_GET ['p'] ? intval($_GET ['p']) : 1;
        $PageSize = 6;
        $Rscount = $HotelOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount ['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $MysqlWhere .= ' order by AddTime desc';
            $Data['Data'] = $HotelOrderModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            $HotelBaseInfoModule = new HotelBaseInfoModule();
            foreach ($Data['Data'] as $key => $val) {
                $Data['Data'] [$key] ['ExpirationTime'] = strtotime($val['ExpirationTime']);
                $HotelInfo = $HotelBaseInfoModule->GetHotelByID($val['HotelID']);
                if ($HotelInfo['Image']) {
                    $Data['Data'][$key]['Img'] = ImageURLP2 . $HotelInfo['Image'];
                } else {
                    $Data['Data'][$key]['Img'] = ImageURL . "/img/common/loadpic.jpg";
                }
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }
        include template("MemberTourHotelOrderList");
    }
    /**
     * @desc 签证订单列表
     */
    public function VisaOrderList()
    {
        MemberService::IsLogin();
        $UserNav = 'VisaOrderList';
        $VisaOrderModule = new VisaOrderModule();
        $VisaProductModule = new VisaProducModule();
        //前台订单状态
        $OrderStatus = $VisaOrderModule->NStatus;
        $Status = $_GET['S'] ? $_GET['S'] : 0; //默认全部 0-全部
        $UserID = intval($_SESSION ['UserID']);
        switch ($Status) {
            case '0': //全部
                $MysqlWhere = ' and UserID= ' . $UserID;
                break;
            case '1': //待支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status = 1';
                break;
            case '2': //已支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (2,3,4)';
                break;
            case '3': //退款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` in (5,6,7,8,9)';
                break;
        }
        $CurrentTime= time();//当前时间
        $Page = intval($_GET['p']) ? intval($_GET['p']) : 1;
        $PageSize = 6;
        $Rscount = $VisaOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount ['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $MysqlWhere .= ' order by CreateTime desc';
            $Data['Data'] = $VisaOrderModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $Key => $Value) {
                $ViseInfo = $VisaProductModule->GetInfoByKeyID($Value['VisaID']);
                $Data['Data'] [$Key] ['Image'] = ImageURLP2 . $ViseInfo['Image'];
                $Data['Data'] [$Key] ['Title'] = $ViseInfo['Title'];
                $Data['Data'] [$Key] ['ExpirationTime'] = strtotime($Value['ExpirationTime']);
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }
        include template("MemberTourVisaOrderList");
    }
    /**
     * @desc 高端定制订单列表
     */
    public function HighLevelOrderList()
    {
        MemberService::IsLogin();
        $UserNav = 'HighLevelOrderList';
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        //前台订单状态
        $OrderStatus = $TourPrivateOrderModule->NStatus;
        $UserID = intval($_SESSION ['UserID']);
        $MysqlWhere = ' and UserID= ' . $UserID;
        $Offset = 0;
        $Status = intval($_GET['S']) ? intval($_GET['S']) : 0; //默认全部 0-全部
        switch ($Status) {
            case '0': //未付款
                $MysqlWhere = ' and UserID = ' . $UserID;
                break;
            case '1': //未付款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (0,1)';
                break;
            case '2': //已付款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (2,3,4)';
                break;
            case '3': //退款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and `Status` in (5,6,7,8,9)';
                break;
        }
        $CurrentTime= time();//当前时间
        $Page = intval($_GET['p']) ? intval($_GET['p']) : 1;
        $PageSize = 6;
        $Rscount = $TourPrivateOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $MysqlWhere .= ' order by CreateTime desc';
            $Data['Data'] = $TourPrivateOrderModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                $Data['Data'] [$key] ['ExpirationTime'] = strtotime($value['ExpirationTime']);
                $endCity = str_replace(',', '-', $value['EndCity']);
                $endCity = substr($endCity, 0, -1);
                $City1 = _substr($endCity, 22);
                $Data['Data'][$key]['OrderName'] = $City1;
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }
        include template("MemberTourHighLevelOrderList");
    }
    /**
     * @desc 出游订单详情
     */
    public function TourOrderDetail()
    {
        MemberService::IsLogin();
        $UserNav = 'TourOrderList';
        $TourProductOrderModule = new TourProductOrderModule();
        //前台订单状态
        $OrderStatus = $TourProductOrderModule->NStatus;
        $UserID = $_SESSION ['UserID'];
        $NO = trim($_GET ['NO']);
        //当前时间
        $CurrentTime= time();
        //订单基本信息
        $OrderInfo = $TourProductOrderModule->GetInfoByOrderNumber($NO);
        //旅客信息
        $TravelPeopleInfo = json_decode($OrderInfo['TravelPeopleInfo'], true);
        $TourProductOrderInfoModule = new TourProductOrderInfoModule ();
        //订单详细信息
        $OrderOrderInfo = $TourProductOrderInfoModule->GetInfoByOrderNumber($NO);
        //跟团游产品信息
        if (!empty($OrderOrderInfo['TourLineSnapshotID'])) {
            $TourProductLineSnapshotModule = new TourProductLineSnapshotModule();
            $SnapshotInfo = $TourProductLineSnapshotModule->GetInfoByKeyID($OrderOrderInfo['TourLineSnapshotID']);
            $LineInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['LineInfo']), true);
            $BaseInfo['ProductName'] = $LineInfo['ProductName'];
            $FinishedDate = date('Y-m-d', strtotime($OrderOrderInfo['Depart']) + 3600 * 24 * $LineInfo['Days']);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['OtherInfo']), true);
        } //当地玩乐产品信息
        elseif (!empty($OrderOrderInfo['TourPlaySnapshotID'])) {
            $SnapshotModule = new TourProductPlaySnapshotModule();
            $TourProductPlaySkuModule = new TourProductPlaySkuModule();
            $TourProductPlaySku =$TourProductPlaySkuModule->GetInfoByKeyID($OrderOrderInfo['TourProductSkuID']);
            $SnapshotInfo = $SnapshotModule->GetInfoByKeyID($OrderOrderInfo['TourPlaySnapshotID']);
            $BaseInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['BaseInfo']), true);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['OtherInfo']), true);
        }
        //订单超时时间
        $OrderInfo['ExpirationTime'] = strtotime($OrderInfo['ExpirationTime']);
        include template("MemberTravelOrderDetail");
    }
    /**
     * @desc 租车订单详情
     */
    public function CarrentOrderDetail()
    {
        MemberService::IsLogin();
        $UserNav = 'CarRentOrderList';
        $UserID = $_SESSION ['UserID'];
        $NO = $_GET['NO'];
        //当前时间
        $CurrentTime= time();
        $ZucheOrderModule = new ZucheOrderModule();
        $OrderStatus = $ZucheOrderModule->NStatus;
        $MysqlWhere = ' and OrderNum= \'' . $NO . '\' and UserID ='.$UserID;
        $ZucheOrderInfo = $ZucheOrderModule->GetOrderByWhere($MysqlWhere);
        $ZucheOrderInfo['StatusName'] = $OrderStatus[$ZucheOrderInfo['Status']];
        $ZucheOrderInfo['QuoteDetail'] = json_decode($ZucheOrderInfo['QuoteDetail'], true);
        $Destination = explode(',', $ZucheOrderInfo['Destination']);
        $Attractions = explode(',', $ZucheOrderInfo['Attractions']);
        if ($Destination[1]) {
            $Destin = "$Destination[0],$Destination[1]";
            $ZucheOrderInfo['Destination'] = $Destin;
        }
        if ($Attractions[1]) {
            $Attrac = "$Attractions[0],$Attractions[1]";
            $ZucheOrderInfo['Attractions'] = $Attrac;
        }
//        echo "<pre>";print_r($ZucheOrderInfo);
        //订单超时时间
        $ZucheOrderInfo['ExpirationTime'] = strtotime($ZucheOrderInfo['ExpirationTime']);
        include template("MemberCarRentOrderDetail");
    }
    /**
     * @desc 酒店订单详情
     */
    public function HotelOrderDetail()
    {
        MemberService::IsLogin();
        $UserNav = 'HotelOrderList';
        $HotelOrderModule = new HotelOrderModule();
        //前台订单状态
        $OrderStatus = $HotelOrderModule->NStatus;
        //当前时间
        $CurrentTime= time();
        if (!isset($_SESSION['HotelOrderNo']) && !isset($_SESSION['HotelContactPhone']) && trim($_GET['NO'])=='') {
            $OrderID = $_GET['ID'];
            $OrderInfo = $HotelOrderModule->GetByKeyIDAndUID($OrderID, $_SESSION['UserID']);
        }elseif(trim($_GET['NO'])!=''){
            $NO = $_GET['NO'];
            $OrderInfo = $HotelOrderModule->GetByNoAndUID($NO, $_SESSION['UserID']);
        } else {
            $OrderID = $_GET['ID'];
            $OrderInfo = $HotelOrderModule->GetByOrderNoAndContactPhone($_SESSION['HotelOrderNo'], $_SESSION['HotelContactPhone'],$OrderID);
        }
        if ($OrderInfo) {
            if ($OrderInfo['Status'] == 2) {
                include SYSTEM_ROOTPATH . '/Controller/Hotel/HotelApi.php';
                $HotelApi = new HotelApi();
                $BookingInfo = $HotelApi->BookingSearch($OrderInfo['BookingID']);
                if (!isset($BookingInfo['Success']) || $BookingInfo['Success']['BookingDetailsList']['BookingDetails']['Status'] != 2) {
                    $HotelOrderModule->UpdateInfoByKeyID(array('Status' => 3, 'UpdateTime' => date("Y-m-d H:i:s", time())), $OrderInfo['OrderID']);
                }
            }
            $HotelBaseInfoModule = new HotelBaseInfoModule();
            $HotelInfo = $HotelBaseInfoModule->GetHotelByID($OrderInfo['HotelID']);
            if ($HotelInfo['Image']) {
                $HotelInfo['Img'] = ImageURLP2 . $HotelInfo['Image'];
            } else {
                $HotelInfo['Img'] = ImageURL . "/img/common/loadpic.jpg";
            }
            $HotelBedTypeModule = new HotelBedTypeModule();
            $BedTypeInfo = $HotelBedTypeModule->GetInfoByKeyID($OrderInfo['BedType']);
            $Days = (strtotime($OrderInfo['CheckOutDate']) - strtotime($OrderInfo['CheckInDate'])) / (3600 * 24);
            $RoomPersonNum = json_decode($OrderInfo['RoomPersonNum'], true);
            $GuestList = json_decode($OrderInfo['GuestList'], true);
            $Cancel = json_decode($OrderInfo['CancellationPolicy'], true);
            //订单超时时间
            $OrderInfo['ExpirationTime'] = strtotime($OrderInfo['ExpirationTime']);
            include template("MemberHotelOrderDetail");
        } else {
            alertandgotopage('无效的订单', WEB_HOTEL_URL);
        }
    }

    /**
     * @desc 签证订单详情
     */
    public function VisaOrderDetail()
    {
        MemberService::IsLogin();
        $UserNav = 'VisaOrderList';
        $NO = trim($_GET ['NO']);
        $VisaOrderModule = new VisaOrderModule ();
        $VisaProducModule = new VisaProducModule ();
        //前台订单状态
        $OrderStatus = $VisaOrderModule->NStatus;
        //当前时间
        $CurrentTime= time();
        $OrderInfo = $VisaOrderModule->GetInfoByOrderNumber($NO);
        $VisaInfo = $VisaProducModule->GetInfoByKeyID($OrderInfo ['VisaID']);
        //订单超时时间
        $OrderInfo['ExpirationTime'] = strtotime($OrderInfo['ExpirationTime']);
        include template("MemberVisaOrderDetail");
    }

    /**
     * @desc 高端定制订单详情
     */
    public function HighLevelOrderDetail()
    {
        MemberService::IsLogin();
        $UserNav = 'HighLevelOrderList';
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        $TourAreaModule = new TourAreaModule ();
        //前台订单状态
        $OrderStatus = $TourPrivateOrderModule->NStatus;
        $NO = $_GET['NO'];
        //当前时间
        $CurrentTime= time();
        $PrivateOrderInfo = $TourPrivateOrderModule->GetInfoByWhere('and `OrderNo`= \''.$NO.'\'');
        //人数
        $PrivateOrderInfo['Number'] = json_decode($PrivateOrderInfo['Number'],true);
        //订单超时时间
        $PrivateOrderInfo['ExpirationTime'] = strtotime($PrivateOrderInfo['ExpirationTime']);
        include template("MemberHighLevelOrderDetail");
    }

    /**
     * @desc  旅游订单评价
     */
    public function Evaluate(){
        $UserNav = 'TourOrderList';
        $UserID = $_SESSION ['UserID'];
        $TourProductOrderModule = new TourProductOrderModule ();
        $OrderStatus = $TourProductOrderModule->NStatus;
        $PaymentMethod = $TourProductOrderModule->PaymentMethod;
        $NO = trim($_GET ['NO']);
        $OrderInfo = $TourProductOrderModule->GetInfoByOrderNumber($NO);
        $TravelPeopleInfo = json_decode($OrderInfo['TravelPeopleInfo'], true);
        $TourProductOrderInfoModule = new TourProductOrderInfoModule ();
        $OrderOrderInfo = $TourProductOrderInfoModule->GetInfoByOrderNumber($NO);
        $ImageModule = new TourProductImageModule();
        $ImageInfo = $ImageModule->GetInfoByTourProductID($OrderOrderInfo['TourProductID']);
        //跟团游
        if (!empty($OrderOrderInfo['TourLineSnapshotID'])) {
            $Type = 'group';
            $TourProductLineSnapshotModule = new TourProductLineSnapshotModule();
            $SnapshotInfo = $TourProductLineSnapshotModule->GetInfoByKeyID($OrderOrderInfo['TourLineSnapshotID']);
            $LineInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['LineInfo']), true);
            $BaseInfo['ProductName'] = $LineInfo['ProductName'];
            $FinishedDate = date('Y-m-d', strtotime($OrderOrderInfo['Depart']) + 3600 * 24 * $LineInfo['Days']);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['OtherInfo']), true);
            $SkuInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['SkuInfo']), true);
            $NewSkuInfo = array();
            foreach ($SkuInfo as $key => $val) {
                if ($LineInfo['SkuType'] == 1) {
                    $NewSkuInfo[$val['ProductSkuID']] = array('AdultNum' => $val['AdultNum'], 'ChildNum' => $val['ChildrenNum']);
                } elseif ($LineInfo['SkuType'] == 2) {
                    $NewSkuInfo[$val['ProductSkuID']] = array('AdultNum' => $val['PeopleNum'], 'ChildNum' => 0);
                }
            }
            $OrderInfoList = $TourProductOrderInfoModule->GetInfoByWhere(" and OrderNumber='$NO'", true);
            include template('MemberTourEvaluateGroup');
        } //当地玩乐
        elseif (!empty($OrderOrderInfo['TourPlaySnapshotID'])) {
            $Type = 'play';
            $SnapshotModule = new TourProductPlaySnapshotModule();
            $SnapshotInfo = $SnapshotModule->GetInfoByKeyID($OrderOrderInfo['TourPlaySnapshotID']);
            $BaseInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['BaseInfo']), true);
            $PriceInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['PriceInfo']), true);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['OtherInfo']), true);
            include template('MemberTourEvaluatePlay');
        }
    }
}