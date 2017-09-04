<?php

class TourMember
{
    public function __construct()
    {
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }

    /**
     * @desc  旅游会员中心首页
     */
    public function Index()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $UserModule = new MemberUserModule();
        $UserID = $_SESSION['UserID'];
        $User = $UserModule->GetInfoByKeyID($UserID);
        $User['E-Mail'] = strlen($User['E-Mail']) ? substr_replace($User['E-Mail'], '****', 1, strpos($User['E-Mail'], '@') - 2) : '';
        $User['Mobile'] = strlen($User['Mobile']) ? substr_replace($User['Mobile'], '****', 3, 4) : '';
        $Account = $User['Mobile'] ? $User['Mobile'] : $User['E-Mail']; //账户
        $NickName = $_SESSION['NickName']; //昵称
        $Avatar = LImageURL.$_SESSION['Avatar'];  //头像
        $SafeLevel = 1;
        if ($User['E-Mail'] != '') {
            $SafeLevel += 1;
        }
        if ($User['Mobile'] != '') {
            $SafeLevel += 1;
        }
        //账户资金查询
        $UserBank = new MemberUserBankModule();
        $Money = $UserBank->GetWalletByID($UserID);

        // ---------------------------  收藏模块  -------------------------
        $Type = $_GET['type'] ? $_GET['type'] : 'all';
        $CollectionModule = new MemberCollectionModule();
        if ($Type == 'all') {
            $SqlWhere = " and Type = 1 and  UserID={$UserID}";
            $MyUrl = WEB_MEMBER_URL . '/tourmember/index/?type=all';
        } else {
            $SqlWhere = " and Category='$Type' and UserID={$UserID}";
            $MyUrl = WEB_MEMBER_URL . '/tourmember/index/?type=' . $Type;
        }
        $Page = intval($_GET['Page']);
        $Page = $Page ? $Page : 1;
        $PageSize = 6;
        $Rscount = $CollectionModule->GetListsNum($SqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            $Data ['Data'] = $CollectionModule->GetLists($SqlWhere, $Offset, $Data['PageSize'], array('Category', 'RelevanceID', 'CollectionID'));
            MultiPage($Data, 8);
            foreach ($Data['Data'] as $key => $val) {
                $Info = '';
                switch ($val['Category']) {
                    case 'travel':
                        $ProductModule = new TourProductModule();
                        $ImageModule = new TourProductImageModule();
                        $ProductInfo = $ProductModule->GetInfoByKeyID($val['RelevanceID']);
                        //echo "<pre>";print_r($ProductInfo);echo "<br>";
                        if ($ProductInfo['ParentCategory'] == '3') { //当地玩乐
                            $PlayBaseModule = new TourProductPlayBaseModule();
                            $Info = $PlayBaseModule->GetInfoByTourProductID($ProductInfo['TourProductID']);
                            $Data['Data'][$key]['Info'] = $Info;
                            $Data['Data'][$key]['Info']['Logo'] = ImageURLP4 . $ImageModule->GetInfoByTourProductID($ProductInfo['TourProductID'])['ImageUrl'];
                            $Data['Data'][$key]['Info']['Url'] = WEB_TOUR_URL . '/play/' . $val['RelevanceID'] . '.html';
                        } elseif ($ProductInfo['ParentCategory'] == '1') { //跟团游
                            $LineModule = new TourProductLineModule();
                            $Info = $LineModule->GetInfoByTourProductID($ProductInfo['TourProductID']);
                            $Data['Data'][$key]['Info'] = $Info;
                            $Data['Data'][$key]['Info']['Logo'] = $ImageModule->GetInfoByTourProductID($ProductInfo['TourProductID'])['ImageUrl'];
                            $Data['Data'][$key]['Info']['Url'] = WEB_TOUR_URL . '/group/' . $val['RelevanceID'] . '.html';
                        }
                        $Data['Data'][$key]['Info']['Name'] = $Info['ProductName'];
                        $Data['Data'][$key]['Info']['Price'] = $Info['LowPrice'];
                        $Data['Data'][$key]['Info']['CateName'] = '旅游';
                        break;
                    case 'hotel':
                        $HotelModule = new HotelBaseInfoModule();
                        $HotelImageModule = new HotelImageModule();
                        $Info = $HotelModule->GetHotelByID($val['RelevanceID']);
                        $Data['Data'][$key]['Info']['Logo'] = ImageURLP4 . $Info['Image'];
                        $Data['Data'][$key]['Info']['Name'] = $Info['Name'];
                        $Data['Data'][$key]['Info']['ENName'] = $Info['Name_Cn'];
                        $Data['Data'][$key]['Info']['Price'] = $Info['LowPrice'];
                        $Data['Data'][$key]['Info']['CateName'] = '酒店';
                        $Data['Data'][$key]['Info']['Url'] = WEB_HOTEL_URL . '/hotel/' . $val['RelevanceID'] . '.html';
                        break;
                    case 'visa':
                        $VisaModule = new VisaProducModule();
                        $Info = $VisaModule->GetInfoByKeyID($val['RelevanceID']);
                        $Data['Data'][$key]['Info']['Name'] = $Info['Title'];
                        $Data['Data'][$key]['Info']['Logo'] = ImageURLP4 . $Info['Image'];
                        $Data['Data'][$key]['Info']['Price'] = $Info['PresentPrice'];
                        $Data['Data'][$key]['Info']['CateName'] = '签证';
                        $Data['Data'][$key]['Info']['Url'] = WEB_VISA_URL . '/visadetail/' . $val['RelevanceID'] . '.html';
                        break;
                }
            }
        }
        include template('TourMemberIndex');
    }


    /**
     * @desc  美国旅游_出游订单列表(旅游，租车，酒店，签证)
     * @desc  (travel,carrent,hotel,visa)
     */
    public function TravelOrder()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $Nav = 'travel';
        if (!isset ($_SESSION ['UserID']) || empty ($_SESSION ['UserID'])) {
            header('Location:' . WEB_MEMBER_URL . '/member/login/');
        }
        $TourProductOrderModule = new TourProductOrderModule ();
        $OrderStatus = $TourProductOrderModule->NStatus;
        $PaymentMethod = $TourProductOrderModule->PaymentMethod;
        $ZhiFuStatus = '2,3,4,5,6,7,8';
        $Status = $_GET['Status'] ? $_GET['Status'] : 1; //默认全部 0-全部
        $UserID = intval($_SESSION ['UserID']);
        switch ($Status) {
            case '1': //全部
                $MysqlWhere = ' and UserID= ' . $UserID;
                break;
            case '2': //已支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (' . $ZhiFuStatus . ')';
                break;
            case '3': //待支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status = 1';
                break;
        }
        $MyUrl = WEB_MEMBER_URL . '/tourmember/travelorder/?Status=' . $Status;
        //分页开始
        $Page = intval($_GET ['P']);
        $Page = $Page ? $Page : 1;
        $PageSize = 6;
        $Rscount = $TourProductOrderModule->GetListsNum($MysqlWhere);

        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $MysqlWhere .= ' order by AddTime desc';
            $Data ['Data'] = $TourProductOrderModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            $TourProductModule = new TourProductModule ();
            $TourProductOrderInfoModule = new TourProductOrderInfoModule ();
            $TourProductImageModule = new TourProductImageModule();
            $CategoryModule = new TourProductCategoryModule();
            foreach ($Data ['Data'] as $Key => $Value) {
                $ProductOrderInfo = $TourProductOrderInfoModule->GetInfoByOrderNumber($Value ['OrderNumber']);
                $TourProductInfo = $TourProductModule->GetInfoByKeyID($ProductOrderInfo ['TourProductID']);
                $CatecoryInfo = $CategoryModule->GetInfoByKeyID($TourProductInfo['Category']);
                if ($CatecoryInfo ['ParentID'] == 1) {
                    $Data ['Data'] [$Key] ['CategoryAlias'] = 'group';
                } else {
                    $Data ['Data'] [$Key] ['CategoryAlias'] = 'play';
                }
                $Data ['Data'] [$Key] ['CategoryName'] = $CatecoryInfo['CnName'];
                $Data ['Data'] [$Key] ['Title'] = _substr($TourProductInfo ['ProductName'], 22);
                $Data ['Data'] [$Key] ['TourProductID'] = $TourProductInfo ['TourProductID'];
                $Data ['Data'] [$Key] ['Num'] = $ProductOrderInfo ['Num'];
                //图片
                $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($ProductOrderInfo ['TourProductID']);
                $Data ['Data'] [$Key] ['ImageUrl'] = $TourImagesInfo ['ImageUrl'];
                
                //查询是否评价过
                $TourOrderEvaluateModule=new TourOrderEvaluateModule();
                $TourOrderEvaluateInfo=$TourOrderEvaluateModule->GetInfoByWhere(" and UserID=$UserID and OrderNumber='{$Value['OrderNumber']}'");
                if($TourOrderEvaluateInfo){
                    $Data ['Data'] [$Key] ['HadEvaluate']=1;
                }else{
                    $Data ['Data'] [$Key] ['HadEvaluate']=0;
                }
            }
            MultiPage($Data, 10);
        }
        include template('TourMemberTravelOrder');
    }

    /**
     * @desc  旅游订单详情
     */
    public function TravelOrderDetail()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $Nav = 'travel';
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
        } //当地玩乐
        elseif (!empty($OrderOrderInfo['TourPlaySnapshotID'])) {
            $Type = 'play';
            $SnapshotModule = new TourProductPlaySnapshotModule();
            $SnapshotInfo = $SnapshotModule->GetInfoByKeyID($OrderOrderInfo['TourPlaySnapshotID']);
            $BaseInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['BaseInfo']), true);
            $PriceInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['PriceInfo']), true);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['OtherInfo']), true);
        }
        include template('TourMemberTravelOrderDetail');
    }
    
    /**
     * @desc  旅游订单评价
     * @author BOB
     */
    public function Evaluate()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $Nav = 'travel';
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
            include template('TourMemberEvaluateGroup');
        } //当地玩乐
        elseif (!empty($OrderOrderInfo['TourPlaySnapshotID'])) {
            $Type = 'play';
            $SnapshotModule = new TourProductPlaySnapshotModule();
            $SnapshotInfo = $SnapshotModule->GetInfoByKeyID($OrderOrderInfo['TourPlaySnapshotID']);
            $BaseInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['BaseInfo']), true);
            $PriceInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['PriceInfo']), true);
            $OtherInfo = json_decode(str_replace("\r\n", "\\r\\n",$SnapshotInfo['OtherInfo']), true);
            include template('TourMemberEvaluatePlay');
        }
        
    }

    /**
     * @desc  删除出游订单记录(更改状态而已)
     */
    public function DelTravelOrder()
    {
        MemberService::IsLogin();
        $TourProductOrderModule = new TourProductOrderModule();
        $IDs = $_POST['data'];
        foreach ($IDs as $val) {
            $result = $TourProductOrderModule->UpdateInfoByOrderNumber(array('IsPayment' => '关闭'), $val);
        }
        $array = array('ResultCode' => '200', 'Message' => '关闭成功');
        echo json_encode($array);
        exit;
    }

    /*
     * @desc 美国旅游_租车订单列表(旅游，租车，酒店，签证)
     * @desc   (travel,carrent,hotel,visa)
     */
    public function  CarrentOrder()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $Nav = 'carrent';
        if (!isset ($_SESSION ['UserID']) || empty ($_SESSION ['UserID'])) {
            header('Location:' . WEB_MEMBER_URL . '/member/login/');
        }
        $Title = "57美国网-租车订单列表";
        $MyPageUrl = WEB_MEMBER_URL . '/tourmember/carrentorder/';
        $ZucheOrderModule = new ZucheOrderModule();
        $Status = $_GET['Status'] ? $_GET['Status'] : 1;
        $UserID = $_SESSION ['UserID'];
        $MysqlWhere = ' and UserID= ' . $UserID;
        switch ($Status) {
            case '2': //已付款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (2,3,4,5,6,7,8,9)';
                break;
            case '3': //未付款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status = 1';
                break;
        }
        $MyUrl = WEB_MEMBER_URL . '/tourmember/carrentorder/?Status=' . $Status;
        $Page = intval($_GET['P']);
        if ($Page < 1) {
            $Page = 1;
        }
        $PageSize = 6;
        $Rscount = $ZucheOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data['Data'] = $ZucheOrderModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            //订单状态
            $OrderStatus = $ZucheOrderModule->NStatus;
            //支付方式
            $PayType = $ZucheOrderModule->PayType;
            foreach ($Data ['Data'] as $Key => $Value) {
                $Data['Data'][$Key]['QuoteDetail'] = json_decode($Value['QuoteDetail'], true);
                $Destination = explode(',', $Value['Destination']);
                $Attractions = explode(',', $Value['Attractions']);
                if ($Destination[1]) {
                    $Destin = "$Destination[0],$Destination[1]";
                    $Data ['Data'][$Key]['Destination'] = $Destin;
                }
                if ($Attractions[1]) {
                    $Attrac = "$Attractions[0],$Attractions[1]";
                    $Data ['Data'][$Key]['Attractions'] = $Attrac;
                }
            }
            MultiPage($Data, 10);
        }
        include template('TourMemberCarrentOrder');
    }

    /**
     * @desc  租车详情页
     */
    public function CarrentOrderDetail()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $UserID = $_SESSION ['UserID'];
        $Num = $_GET['NO'];
        if (!isset ($_SESSION ['UserID']) || empty ($_SESSION ['UserID'])) {
            header('Location:' . WEB_MEMBER_URL . '/member/login/');
        }
        $ZucheOrderModule = new ZucheOrderModule();
        $MysqlWhere = 'and OrderNum= \'' . $Num . '\' and UserID = ' . $UserID;
        $Info = $ZucheOrderModule->GetOrderByWhere($MysqlWhere);
        $OrderStatus = $ZucheOrderModule->NStatus;
        $PayType = $ZucheOrderModule->PayType;
        $Info['StatusName'] = $OrderStatus[$Info['Status']];
        $Info['PayType'] = $PayType[$Info['PayType']];
        $Info['QuoteDetail'] = json_decode($Info['QuoteDetail'], true);
        $Destination = explode(',', $Info['Destination']);
        $Attractions = explode(',', $Info['Attractions']);
        if ($Destination[1]) {
            $Destin = "$Destination[0],$Destination[1]";
            $Info['Destination'] = $Destin;
        }
        if ($Attractions[1]) {
            $Attrac = "$Attractions[0],$Attractions[1]";
            $Info['Attractions'] = $Attrac;
        }
        include template('TourMemberCarrentOrderDetail');
    }

    /**
     * @desc  取消租车订单记录(更改状态而已)
     */
    public function DelCarrentOrder()
    {
        MemberService::IsLogin();
        $ZucheOrderModule = new ZucheOrderModule();
        $IDs = $_POST['data'];
        foreach ($IDs as $val) {
            $result = $ZucheOrderModule->UpdateByOrderNum(array('Status' => 7), $val);
        }
        $array = array('ResultCode' => '200', 'Message' => '关闭成功');
        echo json_encode($array);
        exit;
    }


    /**
     * @desc 美国旅游_酒店订单列表(旅游，租车，酒店，签证)
     * @desc   (travel,carrent,hotel,visa)
     */
    public function  HotelOrder()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $Nav = 'hotel';
        $Title = "57美国网-酒店订单列表";
        $MyPageUrl = WEB_MEMBER_URL . '/tourmember/hotelorder/';
        $HotelOrderModule = new HotelOrderModule();
        $Status = $HotelOrderModule->NStatus;
        $S = $_GET['S'] ? $_GET['S'] : 0; //默认全部 0-全部
        $UserID = intval($_SESSION ['UserID']);
        switch ($S) {
            case 1:
                $MysqlWhere = " and UserID=$UserID and `Status`=1";
                break;
            case 2:
                $MysqlWhere = " and UserID=$UserID and `Status` in (2,3,4,5,6,7,8)";
                break;
            default:
                $MysqlWhere = ' and UserID= ' . $UserID;
                break;
        }
        $MyUrl = WEB_MEMBER_URL . '/tourmember/hotelorder/?S=' . $S;
        $Page = intval($_GET['P']);
        if ($Page < 1) {
            $Page = 1;
        }
        $PageSize = 6;
        $Rscount = $HotelOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data['Data'] = $HotelOrderModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            $HotelBaseInfoModule = new HotelBaseInfoModule();
            foreach ($Data['Data'] as $key => $val) {
                $HotelInfo = $HotelBaseInfoModule->GetHotelByID($val['HotelID']);
                if ($HotelInfo['Image']) {
                    $Data['Data'][$key]['Img'] = ImageURLP2 . $HotelInfo['Image'];
                } else {
                    $Data['Data'][$key]['Img'] = ImageURL . "/img/common/loadpic.jpg";
                }
            }
            MultiPage($Data, 10);
        }
        include template('TourMemberHotelOrder');
    }


    //酒店订单详情
    public function HotelOrderDetails()
    {
        if ((!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) && (!isset($_SESSION['HotelOrderNo']) || !isset($_SESSION['HotelContactPhone']))) {
            header('Location:' . WEB_MEMBER_URL . '/member/login/');
        } else {
            $HotelOrderModule = new HotelOrderModule();
            if ($_SESSION['UserID'] && !isset($_SESSION['HotelOrderNo']) && !isset($_SESSION['HotelContactPhone'])) {
                $OrderID = $_GET['ID'];
                $OrderInfo = $HotelOrderModule->GetByKeyIDAndUID($OrderID, $_SESSION['UserID']);
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
                $OrderStatus = $HotelOrderModule->NStatus[$OrderInfo['Status']];
                $HotelBedTypeModule = new HotelBedTypeModule();
                $BedTypeInfo = $HotelBedTypeModule->GetInfoByKeyID($OrderInfo['BedType']);
                $Days = (strtotime($OrderInfo['CheckOutDate']) - strtotime($OrderInfo['CheckInDate'])) / (3600 * 24);
                $RoomPersonNum = json_decode($OrderInfo['RoomPersonNum'], true);
                $GuestList = json_decode($OrderInfo['GuestList'], true);
                $Cancel = json_decode($OrderInfo['CancellationPolicy'], true);
                $UserNav = 'Tour';
                include template('TourMemberHotelOrderDetails');
            } else {
                alertandgotopage('无效的订单', WEB_HOTEL_URL);
            }
        }
    }


    /**
     * @desc  删除酒店订单记录(更改状态而已)
     */
    public function DelHotelOrder()
    {
        MemberService::IsLogin();
        $HotelOrderModule = new HotelOrderModule();
        $IDs = $_POST['data'];
        foreach ($IDs as $val) {
            $result = $HotelOrderModule->UpdateInfoByKeyID(array('Status' => 8), $val);
        }
        $array = array('ResultCode' => '200', 'Message' => '删除成功');
        echo json_encode($array);
        exit;
    }

    /**
     * @desc 美国旅游_签证订单列表(旅游，租车，酒店，签证)
     * @desc   (travel,carrent,hotel,visa)
     */
    public function  VisaOrder()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $Nav = 'visa';
        $Title = "57美国网-签证订单列表";
        $MyPageUrl = WEB_MEMBER_URL . '/tourmember/visaorder/';
        $VisaOrderModule = new VisaOrderModule();
        $VisaProductModule = new VisaProducModule();

        $Status = $VisaOrderModule->NStatus;
        $PaymentMethod = $VisaOrderModule->PaymentMethod;
        $ZhiFuStatus = '2,3,4,5,6,7,8';

        $S = $_GET['S'] ? $_GET['S'] : 1; //默认全部 0-全部
        $UserID = intval($_SESSION ['UserID']);
        switch ($S) {
            case '1': //全部
                $MysqlWhere = ' and UserID= ' . $UserID;
                break;
            case '2': //已支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (' . $ZhiFuStatus . ')';
                break;
            case '3': //待支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status = 1';
                break;
        }
        $MyUrl = WEB_MEMBER_URL . '/tourmember/visaorder/?S=' . $S;
        $Page = intval($_GET['P']) ? intval($_GET['P']) : 1;
        $PageSize = 6;
        $Rscount = $VisaOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data['Data'] = $VisaOrderModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data ['Data'] as $Key => $Value) {
                $Data ['Data'] [$Key] ['TagArray'] = explode(',', $Value ['Tag']);
                $Info = $VisaProductModule->GetInfoByKeyID($Value['VisaID']);
                $ViseInfo = $VisaProductModule->GetInfoByKeyID($Value['VisaID']);
                $Data ['Data'] [$Key] ['Image'] = LImageURL . $ViseInfo['Image'];
                $Data ['Data'] [$Key] ['Title'] = $ViseInfo['Title'];
            }
            MultiPage($Data, 10);
        }
        include template('TourMemberVisaOrder');
    }

    /**
     * @desc  签证详情页
     */
    public function VisaOrderDetail()
    {
        MemberService::IsLogin();
        $OrderNumber = trim($_GET ['id']);
        $VisaOrderModule = new VisaOrderModule ();
        $VisaProducModule = new VisaProducModule ();
        $Status = $VisaOrderModule->NStatus;
        $PaymentMethod = $VisaOrderModule->PaymentMethod;
        $OrderInfo = $VisaOrderModule->GetInfoByOrderNumber($OrderNumber);
        $VisaInfo = $VisaProducModule->GetInfoByKeyID($OrderInfo ['VisaID']);
        $UserNav = 'Tour';
        $Nav = 'visa';
        include template('TourMemberVisaOrderDetail');
    }

    /**
     * @desc  删除签证订单记录(更改状态而已)
     */
    public function DelVisaOrder()
    {
        MemberService::IsLogin();
        $VisaOrderModule = new VisaOrderModule();
        $IDs = $_POST['data'];
        foreach ($IDs as $val) {
            $result = $VisaOrderModule->UpdateInfoByKeyID(array('IsPayment' => '删除'), $val);
        }
        $array = array('ResultCode' => '200', 'Message' => '删除成功');
        echo json_encode($array);
        exit;
    }


    //收货地址
    public function Address()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $Nav = 'address';
        $ShippingAddressModule = new MemberShippingAddressModule();
        $ShippingAddress = $ShippingAddressModule->GetLists(" and UserID={$_SESSION['UserID']}", 0, 20);
        if (isset($_GET['ID'])) {
            $ShippingAddressID = intval($_GET['ID']);
            $AddressInfo = $ShippingAddressModule->GetLists(" and UserID={$_SESSION['UserID']} and ShippingAddressID=$ShippingAddressID", 0, 1);
            if (count($AddressInfo)) {
                $AddressInfo = $AddressInfo[0];
            }
        }
        $Title = '会员中心_收货地址 - 57美国网';
        include template('TourMemberAddress');
    }

    /**
     * @desc 美国旅游_高端定制订单详情页
     */

    public function HightOrderDetail()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $UserID = intval($_COOKIE ['UserID']);
        if ($_GET['NO']) {
            $OrderNo = $_GET['NO'];
            $TourPrivateOrderModule = new TourPrivateOrderModule();
            $TourAreaModule = new TourAreaModule ();
            $Data = $TourPrivateOrderModule->GetInfoByWhere('and `OrderNo`= \''.$OrderNo.'\'');
            $StatusInfo = $TourPrivateOrderModule->NStatus;
            $Data['StatusName'] = $StatusInfo[$Data['Status']];
            $endCity = str_replace(',', '-', $Data['EndCity']);
            $City1 = substr($endCity, 0, strlen($endCity) - 1);
            $endCitys = explode('-', $City1);
            $Customizatin = substr($Data['Customizatin'], 0, strlen($Data['Customizatin']) - 1);
            $Customizatin = explode(',', $Customizatin);
            $Demand = substr($Data['Demand'], 0, strlen($Data['Demand']) - 1);
            $Demand = explode(',', $Demand);
            $ScenicSpots = substr($Data['ScenicSpots'], 0, strlen($Data['ScenicSpots']) - 1);
            $ScenicSpots = explode(',', $ScenicSpots);
            $OtherDemand = substr($Data['OtherDemand'], 0, strlen($Data['OtherDemand']) - 1);
            $OtherDemand = explode(',', $OtherDemand);
            $TourAttractionsModule = new TourAttractionsModule();
            foreach ($ScenicSpots as $key => $value) {
                $Info = $TourAttractionsModule->GetInfoByWhere(' and AttractionsName = \'' .$value.'\'');
                $TourArea = $TourAreaModule->GetInfoByKeyID($Info['AreaID']);
                foreach ($endCitys as $k => $val) {
                    if ($TourArea['CnName'] == $val) {
                        $newCity[$val][] = $value;
                    }
                }
            }
            $Number = json_decode($Data['Number'], true);
            include template("TourMemberHightOrderDetail");
        }
    }

    /**
     * @desc 美国旅游_高端定制订单列表页
     */
    public function HighLevelOrder()
    {
        MemberService::IsLogin();
        $UserNav = 'Tour';
        $Nav = 'hight';
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        $MyPageUrl = WEB_MEMBER_URL . '/tourmember/highlevelorder/';
        $UserID = intval($_SESSION ['UserID']);
        $MysqlWhere = ' and UserID= ' . $UserID;
        $sqlWhere = ' and UserID= ' . $UserID;
        $Offset = 0;
        $Status = $_GET['Status'] ? $_GET['Status'] : 1; //默认全部 1-全部
        switch ($Status) {
            case '2': //已付款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (2,3,4,5,6,7,8)';
                break;
            case '3': //未付款
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (0,1)';
                break;
        }
        $count = $TourPrivateOrderModule->GetListsNum($MysqlWhere);
        $Page = intval($_GET['P']);
        if ($Page < 1) {
            $Page = 1;
        }
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
            $Data['Data'] = $TourPrivateOrderModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            $StatusInfo = $TourPrivateOrderModule->NStatus;
            foreach ($Data['Data'] as $key => $value) {
                $endCity = str_replace(',', '-', $value['EndCity']);
                $City1 = substr($endCity, 0, strlen($endCity) - 1);
                $Data['Data'][$key]['EndCity'] = $City1;
            }
            MultiPage($Data, 10);
        }
        include template("TourMemberHighLevelOrder");
    }

}
