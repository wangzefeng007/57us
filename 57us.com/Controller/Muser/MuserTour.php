<?php
/**
 * 旅游会员中心
 * By Leo
 */
class MuserTour
{
    public function __construct(){

    }

    /**
     * 旅游会员中心首页
     */
    public function Index(){
        MuserService::IsLogin();
        $UserID=$_SESSION['UserID'];
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($UserID);
        $Title = '会员中心 - 57美国网';
        include template('MuserTourIndex');
    }

    /**
     * 我的订单
     */
    public function MyOrder(){
        MuserService::IsLogin();
        $Title = '会员中心_我的订单 - 57美国网';
        include template('MuserTourMyOrder');
    }

    /**
     * @desc  旅游订单详情
     */
    public function TravelOrderDetail()
    {
        MuserService::IsLogin();
        $OrderNumber = trim($_GET ['NO']);
        $CategoryModule = new TourProductCategoryModule();
        $TourProductOrderModule = new TourProductOrderModule ();
        $ImageModule = new TourProductImageModule();
        $TourProductOrderInfoModule = new TourProductOrderInfoModule ();
        $OrderStatus = $TourProductOrderModule->NStatus;
        $PaymentMethod = $TourProductOrderModule->PaymentMethod;
        $OrderInfo = $TourProductOrderModule->GetInfoByOrderNumber($OrderNumber);
        $ProductOrderInfo = $TourProductOrderInfoModule->GetInfoByOrderNumber($OrderNumber);
        $ImageInfo = $ImageModule->GetInfoByTourProductID($ProductOrderInfo['TourProductID']);
        $TravelPeopleInfo = json_decode($OrderInfo['TravelPeopleInfo'], true);
        //人数
        $PersonNum = count($TravelPeopleInfo);
        //跟团游
        if (!empty($ProductOrderInfo['TourLineSnapshotID'])){
            $Type = 'group';
            $TourProductLineSkuModule = new TourProductLineSkuModule();
            $TourProductLineSnapshotModule = new TourProductLineSnapshotModule();
            $SnapshotInfo = $TourProductLineSnapshotModule->GetInfoByKeyID($ProductOrderInfo['TourLineSnapshotID']);
            $LineInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['LineInfo']), true);
            $CatecoryInfo = $CategoryModule->GetInfoByKeyID($LineInfo['Category']);
            $FinishedDate = date('m'.'月'.'d'.'日', strtotime($ProductOrderInfo['Depart']) + 3600 * 24 * $LineInfo['Days']);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['OtherInfo']), true);
            $EndTime = strtotime($ProductOrderInfo['Depart']) + 3600 * 24 * $LineInfo['Days'];
            //获取星期
            //$Depart = $this->getWeek(strtotime($ProductOrderInfo['Depart']));
            //$EndTime = $this->getWeek($EndTime);
            //获取Sku
            $SkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($ProductOrderInfo['TourProductSkuID']);
            //房间数
            $RoomNum = $TourProductOrderInfoModule->GetRoomNumByOrderNumber($OrderNumber);
        } //当地玩乐
        elseif (!empty($ProductOrderInfo['TourPlaySnapshotID'])){
            $Type = 'playorder';
            $SnapshotModule = new TourProductPlaySnapshotModule();
            $TourProductPlaySkuModule = new TourProductPlaySkuModule();
            $SnapshotInfo = $SnapshotModule->GetInfoByKeyID($ProductOrderInfo['TourPlaySnapshotID']);
            $PlayInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['BaseInfo']), true);
            $CatecoryInfo = $CategoryModule->GetInfoByKeyID($PlayInfo['Category']);
            $SkuInfo = $TourProductPlaySkuModule->GetInfoByKeyID($ProductOrderInfo['TourProductSkuID']);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['OtherInfo']), true);
            //$Depart = $this->getWeek(strtotime($ProductOrderInfo['Depart']));
        }
        $Title='会员中心_订单详情 - 57美国网';
        include template('MuserTourTravelOrderDetail');
    }
    //获取星期
    function getWeek($times){
        $week = date("w",$times);
        switch($week){
            case 1:
                return "周一";
                break;
            case 2:
                return "周二";
                break;
            case 3:
                return "周三";
                break;
            case 4:
                return "周四";
                break;
            case 5:
                return "周五";
                break;
            case 6:
                return "周六";
                break;
            case 0:
                return "周日";
                break;
        }
    }

    /**
     * 常用信息编辑
     */
    public function CommonInfo(){
        MuserService::IsLogin();
        $UserID=$_SESSION['UserID'];
        //常用旅客信息
        $MemberPassengerModule=new MemberPassengerModule();
        $PassengerList=$MemberPassengerModule->GetInfoByWhere(" and UserID=$UserID",true);
        //常用收货地址
        $MemberShippingAddressModule=new MemberShippingAddressModule();
        $AddressList=$MemberShippingAddressModule->GetInfoByWhere(" and UserID=$UserID",true);
        $Title ='会员中心_常用信息 - 57美国网';
        include template('MuserTourCommonInfo');
    }

    /**
     * 编辑/添加旅客
     */
    public function EditPassenger(){
        MuserService::IsLogin();
        if(isset($_GET['ID']) && is_numeric($_GET['ID'])){
            $ID=intval($_GET['ID']);
            $MemberPassengerModule=new MemberPassengerModule();
            $PassengerInfo=$MemberPassengerModule->GetInfoByKeyID($ID);
        }
        $Title ='会员中心_旅客编辑 - 57美国网';
        include template('MuserTourEditPassenger');
    }

    /**
     * 编辑/添加收货地址
     */
    public function EditShippingAddress(){
        MuserService::IsLogin();
        if(isset($_GET['ID']) && is_numeric($_GET['ID'])){
            $ID=intval($_GET['ID']);
            $MemberShippingAddressModule=new MemberShippingAddressModule();
            $AddressInfo=$MemberShippingAddressModule->GetInfoByKeyID($ID);
        }
        $Title ='会员中心_地址编辑 - 57美国网';
        include template('MuserTourEditShippingAddress');
    }
    
    /**
     * 出游订单评价
     */
    public function Evaluate(){
        MuserService::IsLogin();
        $UserID = $_SESSION ['UserID'];
        $TourProductOrderModule = new TourProductOrderModule ();
        $OrderStatus = $TourProductOrderModule->NStatus;
        $PaymentMethod = $TourProductOrderModule->PaymentMethod;
        $NO = trim($_GET ['NO']);
        $OrderInfo = $TourProductOrderModule->GetInfoByWhere(" and OrderNumber='$NO' and UserID=$UserID");
        if(!$OrderInfo){
            alertandback('不能评价该订单');
        }
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
            include template('MuserTourEvaluateGroup');
        } //当地玩乐
        elseif (!empty($OrderOrderInfo['TourPlaySnapshotID'])) {
            $Type = 'play';
            $SnapshotModule = new TourProductPlaySnapshotModule();
            $SnapshotInfo = $SnapshotModule->GetInfoByKeyID($OrderOrderInfo['TourPlaySnapshotID']);
            $BaseInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['BaseInfo']), true);
            $PriceInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['PriceInfo']), true);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['OtherInfo']), true);
            include template('MuserTourEvaluatePlay');
        }        

    }
}