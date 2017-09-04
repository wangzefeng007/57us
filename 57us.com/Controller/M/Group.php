<?php

/**
 * @desc  跟团游
 * Class Group
 */
class Group
{

    public function __construct()
    {
    }
    
    // 跟团游首页
    public function Index()
    {
        $TourProductImageModule = new TourProductImageModule();
        $TourProductLineModule = new TourProductLineModule();
        $TourCategoryModule = new TourProductCategoryModule();
        // 特价优惠
        $R4SqlWhere = 'and R4 = 1 and `Status` = 1 order by S4 DESC limit 2';
        $ListsR4 = $TourProductLineModule->GetLists($R4SqlWhere, 0, 6);
        foreach ($ListsR4 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR4[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR4[$key]['TagInfo'] = explode(',', $value['TagInfo']);
            $ListsR4[$key]['LowPrice'] = intval($value['LowPrice']);
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($value['Category']);
            $ListsR4[$key]['CnName'] = $CategoryName['CnName'];
        }
        
        // 热门产品
        $R5SqlWhere = 'and R5 = 1 and `Status` = 1 order by S5 DESC limit 5';
        $ListsR5 = $TourProductLineModule->GetLists($R5SqlWhere, 0, 6);
        foreach ($ListsR5 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR5[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR5[$key]['TagInfo'] = explode(',', $value['TagInfo']);
            $ListsR5[$key]['LowPrice'] = intval($value['LowPrice']);
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($value['Category']);
            $ListsR5[$key]['CnName'] = $CategoryName['CnName'];
        }
        // 推荐国内参团
        $HomeSqlWhere = 'and R2 = 1 and Category=4 and `Status` = 1 order by S2 DESC limit 5';
        $ListsHome = $TourProductLineModule->GetLists($HomeSqlWhere, 0, 6);
        foreach ($ListsHome as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsHome[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsHome[$key]['TagInfo'] = explode(',', $value['TagInfo']);
            $ListsHome[$key]['LowPrice'] = intval($value['LowPrice']);
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($value['Category']);
            $ListsHome[$key]['CnName'] = $CategoryName['CnName'];
        }
        // 推荐当地参团
        $LocalSqlWhere = 'and R2 = 1 and Category=12 and `Status` = 1 order by S2 DESC limit 5';
        $ListsLocal = $TourProductLineModule->GetLists($LocalSqlWhere, 0, 6);
        foreach ($ListsLocal as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsLocal[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsLocal[$key]['TagInfo'] = explode(',', $value['TagInfo']);
            $ListsLocal[$key]['LowPrice'] = intval($value['LowPrice']);
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($value['Category']);
            $ListsLocal[$key]['CnName'] = $CategoryName['CnName'];
        }
        
        // 广告信息获取
        $WapTourIndexBaner = NewsGetAdInfo('m_tour_group_banner');
        $WapTourIndexTJ = NewsGetAdInfo('m_tour_group_bottom_banner');
        
        $Title = '美国跟团游_美国跟团旅游_美国跟团旅游线路 - 57美国网';
        $Keywords = '美国跟团游,美国跟团旅游, 美国跟团旅游线路, 美国跟团游价格,美国跟团';
        $Description = '57美国网跟团游频道，为您提供精品美国跟团旅游线路，多城市出发，包含详细的跟团介绍、行程安排及线路报价等。';
        
        include template('TourGroupIndex');
    }
    // 获取国内参团列表
    private function GroupHome()
    {
        $MysqlWhere = ' and `Status` = 1 and Category=4 and IsClose!=1 and RelationProductID=0';
        // 出发城市
        $StartCity = trim($_POST['StartCity']);
        if ($StartCity != '' && $StartCity != 'All') {
            $MysqlWhere .= " and Departure=$StartCity";
        }
        
        // 途径城市
        $WayCity = trim($_POST['WayCity']);
        if ($WayCity != '' && $WayCity != 'All') {
            $WayCity = rtrim($WayCity, ',');
            $MysqlWhere .= " and MATCH (`AfterAttractions`) AGAINST ('$WayCity' IN BOOLEAN MODE)";
        }
        // 特色主题
        $Theme = trim($_POST['Theme']);
        if ($Theme != '' && $Theme != 'All') {
            $Theme = rtrim($Theme, ',');
            $MysqlWhere .= " and MATCH (`Features`) AGAINST ('$Theme' IN BOOLEAN MODE)";
        }
        // 行程天数
        $Stroke = trim($_POST['Stroke']);
        if ($Stroke != '' && $Stroke != 'All') {
            $Days = explode('-', $Stroke);
            if ($Days[1] == 'All') {
                $MysqlWhere .= " and Days>{$Days[0]}";
            } else {
                if ($Days[0] != 0) {
                    $MysqlWhere .= " and (Days>={$Days[0]} and Days<={$Days[1]})";
                } else {
                    $MysqlWhere .= " and (Days>{$Days[0]} and Days<={$Days[1]})";
                }
            }
        }
        // 出发时间
        $StartDate = trim($_POST['StartDate']);
        if ($StartDate != '' && $StartDate != 'All') {
            $StartDate = rtrim($StartDate, ',');
            $MysqlWhere .= " and MATCH (`Month`) AGAINST ('$StartDate' IN BOOLEAN MODE)";
        }
        // 搜索
        $Keyword = trim($_POST['Keyword']);
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        // 排序
        $ListSort = trim($_POST['Sort']);
        switch ($ListSort) {
            case 'Default':
                $MysqlWhere .= ' order by R2 DESC,UpdateTime DESC';
                break;
            case 'PicerDown':
                $MysqlWhere .= ' order by LowPrice desc';
                break;
            case 'PicerAsce':
                $MysqlWhere .= ' order by LowPrice asc';
                break;
            case 'SalesDown':
                $MysqlWhere .= ' order by Sales desc';
                break;
            case 'SalesAsce':
                $MysqlWhere .= ' order by Sales asc';
                break;
            default:
                break;
        }
        $TourProductLineModule = new TourProductLineModule();
        $Rscount = $TourProductLineModule->GetListsNum($MysqlWhere);
        $page = intval($_POST['Page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 6;
        $Data = array();
        if ($Rscount['Num']) {
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 200;
            }
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $TourProductLineLists = $TourProductLineModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            $Data['Data'] = array();
            foreach ($TourProductLineLists as $Key => $Value) {
                $Data['Data'][$Key]['TourName'] = $Value['ProductName'];
                $Data['Data'][$Key]['TourId'] = $Value['TourProductID'];
                // 图片
                $TourProductImageModule = new TourProductImageModule();
                $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP2 . $TourImagesInfo['ImageUrl'] : '';
                $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                $TagArr = explode(',', $Value['TagInfo']);
                $TagHtml = '';
                if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                    foreach ($TagArr as $list) {
                        $TagHtml .= "<span>$list</span>";
                    }
                }
                $Data['Data'][$Key]['TourTag'] = $TagHtml;
                $Data['Data'][$Key]['TourStroke'] = $Value['Days'];
                $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
            }
        } else {
            if ($Keyword != '') {
                $Data['ResultCode'] = 103;
            } else {
                $Data['ResultCode'] = 101;
            }
        }
        echo json_encode($Data);
        exit();
    }
    
    // 获取当地参团列表
    private function GroupLocal()
    {
        $MysqlWhere = ' and Category=12 and `Status` = 1 and Category=12 and IsClose!=1 and RelationProductID=0';
        // 出发城市
        $StartCity = trim($_POST['StartCity']);
        if ($StartCity != '' && $StartCity != 'All') {
            $MysqlWhere .= " and Departure=$StartCity";
        }
        // 结束城市
        $EndCity = trim($_POST['EndCity']);
        if ($EndCity != '' && $EndCity != 'All') {
            $MysqlWhere .= " and Destination=$EndCity";
        }
        // 途径城市
        $WayCity = trim($_POST['WayCity']);
        if ($WayCity != '' && $WayCity != 'All') {
            $WayCity = rtrim($WayCity, ',');
            $MysqlWhere .= " and MATCH (`AfterAttractions`) AGAINST ('$WayCity' IN BOOLEAN MODE)";
        }
        // 特色主题
        $Theme = trim($_POST['Theme']);
        if ($Theme != '' && $Theme != 'All') {
            $Theme = rtrim($Theme, ',');
            $MysqlWhere .= " and MATCH (`Features`) AGAINST ('$Theme' IN BOOLEAN MODE)";
        }
        // 行程天数
        $Stroke = trim($_POST['Stroke']);
        if ($Stroke != '' && $Stroke != 'All') {
            $Days = explode('-', $Stroke);
            if ($Days[1] == 'All') {
                $MysqlWhere .= " and Days>{$Days[0]}";
            } else {
                if ($Days[0] != 0) {
                    $MysqlWhere .= " and (Days>={$Days[0]} and Days<={$Days[1]})";
                } else {
                    $MysqlWhere .= " and (Days>{$Days[0]} and Days<={$Days[1]})";
                }
            }
        }
        // 出发时间
        $StartDate = trim($_POST['StartDate']);
        if ($StartDate != '' && $StartDate != 'All') {
            $StartDate = rtrim($StartDate, ',');
            $MysqlWhere .= " and MATCH (`Month`) AGAINST ('$StartDate' IN BOOLEAN MODE)";
        }
        
        // 搜索
        $Keyword = trim($_POST['Keyword']);
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        
        // 排序
        $ListSort = trim($_POST['Sort']);
        switch ($ListSort) {
            case 'Default':
                $MysqlWhere .= ' order by R2 DESC,UpdateTime DESC';
                break;
            case 'PicerDown':
                $MysqlWhere .= ' order by LowPrice desc';
                break;
            case 'PicerAsce':
                $MysqlWhere .= ' order by LowPrice asc';
                break;
            case 'SalesDown':
                $MysqlWhere .= ' order by Sales desc';
                break;
            case 'SalesAsce':
                $MysqlWhere .= ' order by Sales asc';
                break;
            default:
                break;
        }
        $TourProductLineModule = new TourProductLineModule();
        $Rscount = $TourProductLineModule->GetListsNum($MysqlWhere);
        $page = intval($_POST['Page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 6;
        $Data = array();
        if ($Rscount['Num']) {
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 200;
            }
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $TourProductLineLists = $TourProductLineModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            $Data['Data'] = array();
            foreach ($TourProductLineLists as $Key => $Value) {
                $Data['Data'][$Key]['TourName'] = $Value['ProductName'];
                $Data['Data'][$Key]['TourId'] = $Value['TourProductID'];
                // 图片
                $TourProductImageModule = new TourProductImageModule();
                $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP2 . $TourImagesInfo['ImageUrl'] : '';
                $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                $TagArr = explode(',', $Value['TagInfo']);
                $TagHtml = '';
                if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                    foreach ($TagArr as $list) {
                        $TagHtml .= "<span>$list</span>";
                    }
                }
                $Data['Data'][$Key]['TourTag'] = $TagHtml;
                $Data['Data'][$Key]['TourStroke'] = $Value['Days'];
                $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
            }
        } else {
            if ($Keyword != '') {
                $Data['ResultCode'] = 103;
            } else {
                $Data['ResultCode'] = 101;
            }
        }
        
        echo json_encode($Data);
        exit();
    }
    
    // 详情页图片
    private function DetailsPic()
    {
        $TourProductImageModule = new TourProductImageModule();
        $ID = intval($_POST['ID']);
        $ImageList = $TourProductImageModule->GetListsByTourProductID($ID);
        if ($ImageList) {
            $Data['ResultCode'] = 200;
            $Data['DataPic'] = array();
            foreach ($ImageList as $val) {
                $Data['DataPic'][] = ImageURLP4 . $val['ImageUrl'];
            }
        } else {
            $Data['ResultCode'] = 100;
            $Data['Message'] = '没有图片';
        }
        echo json_encode($Data);
        exit();
    }
    
    // 当地参团
    public function Local()
    {
        if ($_POST['Intention'] == 'GroupLocal') {
            $this->GroupLocal();
        } else {
            $Keyword = trim($_GET['K']);
            if ($Keyword != '') {
                $SoWhere = '?K=' . $Keyword;
                $Title = '搜索' . $Keyword . '当地参团_' . $Keyword . '行程_旅行团报价 - 57美国网';
                $Keywords = $Keyword . '当地参团,' . $Keyword . '行程, ' . $Keyword . '旅行团报价';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点，查看最新' . $Keyword . '热门旅游线路及旅行团费用就上57美国网！57美国网只专注美国的旅游平台！';
            } else {
                $Title = "美国当地参团_美国当地游_美国当地参团游_美国当地参团旅游 - 57美国网";
                $Keywords = "美国当地参团,美国当地游,美国当地参团游,美国当地参团旅游, 参团美国游,美国旅游参团,美国参团旅游";
                $Description = "57美国网当地参团频道，为您提供精品美国当地参团旅游线路，包含详细的当地参团游介绍、行程安排及线路报价等。";
            }
            include template('TourGroupLocal');
        }
    }
    
    // 国内参团
    public function Home()
    {
        if ($_POST['Intention'] == 'GroupHome') {
            $this->GroupHome();
        } else {
            // 搜索用
            $Keyword = trim($_GET['K']);
            if ($Keyword != '') {
                $SoWhere = '?K=' . $Keyword;
                $Title = '搜索' . $Keyword . '国内参团_' . $Keyword . '行程_旅行团报价- 57美国网';
                $Keywords = $Keyword . '国内参团,' . $Keyword . '行程, ' . $Keyword . '旅行团报价';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点，查看最新' . $Keyword . '热门旅游线路及旅行团费用就上57美国网！57美国网只专注美国的旅游平台！';
            } else {
                $Title = "美国跟团游_美国跟团旅游_美国跟团旅游线路 - 57美国网";
                $Keywords = "美国跟团游,美国跟团旅游, 美国跟团旅游线路, 美国跟团游价格,美国跟团";
                $Description = "57美国网跟团游频道，为您提供精品美国跟团旅游线路，多城市出发，包含详细的跟团介绍、行程安排及线路报价等。";
            }
            include template('TourGroupHome');
        }
    }
    
    // 跟团游详情页
    public function LineDetails()
    {
        if ($_POST['Intention'] == 'DetailsPic') {
            $this->DetailsPic();
        } elseif ($_POST['Intention'] == 'DetailsAndPic') {
            $this->GetDetailsAndPic();
        }
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
        // 不是搜索引擎在判断
        if (empty($TourProductLineInfo) || $TourProductLineInfo['IsClose'] == 1) {
            alertandback('该商品不存在了！');
        }
        
        $TourProductImageModule = new TourProductImageModule();
        $TourImages = $TourProductImageModule->GetListsByTourProductID($TourProductLineInfo['TourProductID']);
        // 出发城市
        $TourAreaModule = new TourAreaModule();
        $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($TourProductLineInfo['Departure']);
        $DepartureID = $TourProductLineInfo['Departure'];
        $TourProductLineInfo['Departure'] = $TourAreaInfo['CnName'];
        // 结束城市
        $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($TourProductLineInfo['Destination']);
        $DestinationID = $TourProductLineInfo['Destination'];
        $TourProductLineInfo['Destination'] = $TourAreaInfo['CnName'];
        
        $TourProductLineDetailedModule = new TourProductLineDetailedModule();
        $TourProductLineDeatils = $TourProductLineDetailedModule->GetInfoByTourProductID($TourProductLineInfo['TourProductID']);
        $Description = json_decode($TourProductLineDeatils['Description'], true);
        $NewContent = json_decode($TourProductLineDeatils['NewContent'], true);
        
        foreach ($NewContent['Trip'] as $K => $Val) {
            $NewContent['Trip'][$K] = StrReplaceImages($Val);
            $NewContent['pic'][$K] = _GetPicToContent($NewContent['Trip'][$K]);
            $NewContent['Trip'][$K] = _DelPicToContent($NewContent['Trip'][$K]);
            $PicString = "";
            if (! empty($NewContent['pic'][$K])) {
                $PicString = '<div class="ins_img">';
                foreach ($NewContent['pic'][$K] as $Pk => $PVal) {
                    $PicString .= '
                    <p><img src="' . $PVal . '" alt="' . $TourProductLineInfo['ProductName'] . '" title="' . $TourProductLineInfo['ProductName'] . '"></p>
                    ';
                }
                $PicString .= '</div>';
            }
            $NewContent['TripPic'][$K] .= $PicString;
        }
        unset($NewContent['pic']);
        $Explanation = json_decode($TourProductLineDeatils['Explanation'], true);
        foreach ($Explanation['ExpContent'] as $K => $Val) {
            $Explanation['ExpContentPic'][$K] = StrReplaceImages($Val);
        }
        $Notice = json_decode($TourProductLineDeatils['Notice'], true);
        foreach ($Notice['NotContent'] as $K => $Val) {
            $Notice['NotContentPic'][$K] = StrReplaceImages($Val);
        }
        $Watch = json_decode($TourProductLineDeatils['Watch'], true);
        foreach ($Watch['WatContent'] as $K => $Val) {
            $Watch['WatContentPic'][$K] = StrReplaceImages($Val);
        }
        // ------------------------------------------------------------------
        $TourProductLineInfo['ProductSimpleName'] = explode("\r\n", $TourProductLineInfo['ProductSimpleName']);
        // -----------------------------------------------------------------
        // 出行日期
        
        $StartDateStr = date('Ymd', time());
        $EndDateStr = date('Ymd', time() + 3600 * 24 * 30 * 6);
        $TourProductLineErverdayPriceModule = new TourProductLineErverdayPriceModule();
        $ErverdayPriceLists = $TourProductLineErverdayPriceModule->GetLists(" and Date>=$StartDateStr and Date<=$EndDateStr and TourProductID={$TourProductLineInfo['TourProductID']} and (Inventory>0 or Inventory=-1) group by Date order by Date asc", 0, 200, array(
            'min(Price) as Price',
            'Date'
        ));
        if ($ErverdayPriceLists) {
            $JsonArr = array();
            foreach ($ErverdayPriceLists as $val) {
                $Data = array();
                $Data['Date'] = date('Y-m-d', strtotime($val['Date']));
                $Data['Price'] = strval(ceil($val['Price']));
                $JsonArr[] = $Data;
            }
        }
        $ErverJson = json_encode($JsonArr);
        
        // 套餐列表
        if ($TourProductLineInfo['RelationProductID'] == 0) {
            $MysqlWhere = " and RelationProductID={$TourProductLineInfo['TourProductID']} and Status=1 and IsClose!=1";
        } else {
            $MysqlWhere = " and RelationProductID={$TourProductLineInfo['RelationProductID']} and Status=1 and IsClose!=1";
        }
        $PackageLists = $TourProductLineModule->GetLists($MysqlWhere, 0, 50);
        $Title = "{$TourProductLineInfo['ProductName']} - 57美国网";
        $Keywords = $TourProductLineInfo['Keywords'] ? $TourProductLineInfo['Keywords'] : $TourProductLineInfo['ProductName'];
        $Description = _substr(strip_tags($Description['DesContent'][0]), 150) . ',了解美国旅游攻略，规划美国旅游行程，预订美国旅游线路，尽在57美国网！';         
        include template('TourGroupLineDetails');
    }
    // 跟团游详情页
    public function GetDetailsAndPic()
    {
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductLineDetailedModule = new TourProductLineDetailedModule();
        $TourProductLineDeatils = $TourProductLineDetailedModule->GetInfoByTourProductID($TourProductID);
        if (empty($TourProductLineDeatils)) {
            $Data['ResultCode'] = 101;
            $Data['Content'] = '';
            $Data['Message'] = '产品不存在';
        } else {
            $NewContent = json_decode($TourProductLineDeatils['NewContent'], true);
            foreach ($NewContent['Trip'] as $K => $Val) {
                $NewContent['Trip'][$K] = StrReplaceImages($Val);
                $NewContent['pic'][$K] = _GetPicToContent($NewContent['Trip'][$K]);
                $NewContent['Trip'][$K] = _DelPicToContent($NewContent['Trip'][$K]);
                $PicString = "";
                if (! empty($NewContent['pic'][$K])) {
                    $PicString = '<div class="ins_img">';
                    foreach ($NewContent['pic'][$K] as $Pk => $PVal) {
                        // 原图替换小图
                        if (strstr($PVal, LImageURL)) {
                            $PVal = str_replace(LImageURL, ImageURLP2, $PVal);
                        }
                        $PicString .= '
                    <p><img src="' . $PVal . '"></p>
                    ';
                    }
                    $PicString .= '</div>';
                }
                $NewContent['TripPic'][$K] .= $PicString;
            }
            $Content = '';
            foreach ($NewContent['TodayTitle'] as $Key => $Value) {
                $Day = $Key + 1;
                $Content .= '<div class="dayBox">';
                $Content .= '<p class="dayBoxT"><span class="day">第' . $Day . '天</span><span class="tit">' . $NewContent['TodayTitle'][$Key] . '</span></p>';
                $Content .= '<div class="dayBoxM">';
                $Content .= '<dl><dt><span class="ico"><i class="icon iconfont">&#x347e;</i></span>交通</dt><dd>' . $NewContent['Traffic'][$Key] . '</dd></dl>';
                $Content .= '<dl><dt><span class="ico"><i class="icon iconfont">&#xe603;</i></span>前往景点</dt><dd>' . $NewContent['Spot'][$Key] . '</dd></dl>';
                $Content .= '<dl><dt><span class="ico"><i class="icon iconfont">&#xe740;</i></span>用餐安排</dt><dd>' . $NewContent['Diet'][$Key] . '</dd></dl>';
                $Content .= '<dl><dt><span class="ico"><i class="icon iconfont">&#x347f;</i></span>酒店住宿</dt><dd>' . $NewContent['Hotel'][$Key] . '</dd></dl>';
                $Content .= '<dl><dt><span class="ico"><i class="icon iconfont">&#x3483;</i></span>行程简介</dt><dd>' . $NewContent['Trip'][$Key] . '</dd>' . $NewContent['TripPic'][$Key] . '</dl>';
                $Content .= '</div>';
                $Content .= '</div>';
            }
            $Data['ResultCode'] = 200;
            $Data['Content'] = $Content;
            $Data['Message'] = '';
        }
        echo json_encode($Data);
        exit();
    }
    
    // 跟团游预定选择日期
    public function GroupChoiceDate()
    {
        include template('TourGroupDate');
    }
    
    // 获取产品可购买日期
    public function GetDate()
    {
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            $json_result['ResultCode'] = 101;
            $json_result['Message'] = '产品不存在';
            echo json_encode($json_result);
            exit();
        }
        $StartDateStr = date('Ymd', time());
        $EndDateStr = date('Ymd', time() + 3600 * 24 * 30 * 6);
        $TourProductLineErverdayPriceModule = new TourProductLineErverdayPriceModule();
        $ErverdayPriceLists = $TourProductLineErverdayPriceModule->GetLists(" and Date>=$StartDateStr and Date<=$EndDateStr and TourProductID={$TourProductID} and (Inventory>0 or Inventory=-1) group by Date order by Date asc", 0, 200, array(
            'min(Price) as Price',
            'Date'
        ));

        if ($ErverdayPriceLists) {
            //判断要提前预定的时间
            $TourProductLineModule = new TourProductLineModule();
            $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($ErverdayPriceLists[0]['TourProductID']);
            $StartDate = date('Ymd', strtotime('+'.$TourPlayInfo['AdvanceDays'].' day'));
            
            $JsonArr = array();
            foreach ($ErverdayPriceLists as $val) {
                if ($StartDate<=$val['Date'])
                {
                    $Data = array();
                    $Data['Date'] = date('Y-m-d', strtotime($val['Date']));
                    $Data['Price'] = strval(ceil($val['Price']));
                    $Month[] = date('Y-m', strtotime($val['Date']));
                    $JsonArr[] = $Data;
                }
            }
        }
        $OneMonth = $Month['0'];
        $MonthArray[] = date("y年m月", strtotime("$OneMonth +0 month"));
        $MonthArray[] = date("y年m月", strtotime("$OneMonth +1 month"));
        $MonthArray[] = date("y年m月", strtotime("$OneMonth +2 month"));
        $MonthArray[] = date("y年m月", strtotime("$OneMonth +3 month"));
        $MonthArray[] = date("y年m月", strtotime("$OneMonth +4 month"));
        $MonthArray[] = date("y年m月", strtotime("$OneMonth +5 month"));
        $ThisYear = date("y") . '年';
        foreach ($MonthArray as $K => $val) {
            if (strstr($val, $ThisYear)) {
                $MonthArray[$K] = str_replace($ThisYear, "", $val);
            }
        }
        
        $MonthArrayTwo[] = date("Y-m", strtotime("$OneMonth +0 month"));
        $MonthArrayTwo[] = date("Y-m", strtotime("$OneMonth +1 month"));
        $MonthArrayTwo[] = date("Y-m", strtotime("$OneMonth +2 month"));
        $MonthArrayTwo[] = date("Y-m", strtotime("$OneMonth +3 month"));
        $MonthArrayTwo[] = date("Y-m", strtotime("$OneMonth +4 month"));
        $MonthArrayTwo[] = date("Y-m", strtotime("$OneMonth +5 month"));
        
        $json_result['ResultCode'] = 200;
        $json_result['Message'] = '获取成功！';
        $json_result['MonthArr'] = $MonthArray;
        $json_result['MonthArr2'] = $MonthArrayTwo;
        $json_result['Date'] = $JsonArr;
        echo json_encode($json_result);
    }
    
    // 日期AJAX
    public function SearchByDate()
    {
        $TourProductID = intval($_POST['TourProductID']);
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineInfo = $TourProductLineModule->GetInfoByWhere(" and TourProductID=$TourProductID");
        
        if ($TourProductLineInfo['SkuType'] == 1) {
            // 区分儿童和成人
            $JsonResult = $this->GetSkuTypeIsOne();
        } elseif ($TourProductLineInfo['SkuType'] == 2) {
            // 区分儿童和成人
            $JsonResult = $this->GetSkuTypeIsTwo();
        } else {
            $JsonResult['ResultCode'] = 101;
            $JsonResult['Message'] = '产品不存在！';
        }
        echo json_encode($JsonResult);
        exit();
    }
    // 通过日期获取区分成人和儿童的sku
    private function GetSkuTypeIsOne()
    {
        $DateStr = trim($_POST['Date']);
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($_POST['TourProductID']);
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineErverPriceLists = $TourProductLineErverdayPriceModule->GetLists(" and `Date`=$DateStr and TourProductID=$TourProductID", 0, 50);
        $RoomPriceList['0'] = $RoomPriceList['1'] = $RoomPriceList['2'] = $RoomPriceList['3'] = 1000000;
        foreach ($TourProductLineErverPriceLists as $key => $val) {
            $TourProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and ProductSkuID={$val['ProductSkuID']} and Status=1 and IsClose!=1");
            if ($TourProductLineSkuInfo) {
                $Sku[$TourProductLineSkuInfo['ProductSkuID']] = $TourProductLineSkuInfo['AdultNum'];
            }
            // 按人数算价格最低
            if (($TourProductLineSkuInfo['AdultNum'] + $TourProductLineSkuInfo['ChildrenNum'] == 1) && $val['Price'] < $RoomPriceList['0']) {
                $RoomPriceList['0'] = ceil($val['Price']);
            }
            if (($TourProductLineSkuInfo['AdultNum'] + $TourProductLineSkuInfo['ChildrenNum'] == 2) && $val['Price'] < $RoomPriceList['1']) {
                $RoomPriceList['1'] = ceil($val['Price']/2);
            }
            if (($TourProductLineSkuInfo['AdultNum'] + $TourProductLineSkuInfo['ChildrenNum'] == 3) && $val['Price'] < $RoomPriceList['2']) {
                $RoomPriceList['2'] = ceil($val['Price']/3);
            }
            if (($TourProductLineSkuInfo['AdultNum'] + $TourProductLineSkuInfo['ChildrenNum'] == 4) && $val['Price'] < $RoomPriceList['3']) {
                $RoomPriceList['3'] = ceil($val['Price']/4);
            }
        }
        
        $Sku = array_unique($Sku);
        asort($Sku);
        // 默认可选成人数
        $I = 0;
        foreach ($Sku as $K => $V) {
            $DultData[$I]['skuid'] = $K;
            $DultData[$I]['num'] = $V;
            $I ++;
        }
        // 默认可选成人数匹配的儿童
        $TourChildrenSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and TourProductID=" . $TourProductID . " and AdultNum=" . $DultData[0]['num'] . " and Status=1 and IsClose!=1 order by ChildrenNum asc", true);
        foreach ($TourChildrenSkuInfo as $ChildrenK => $ChildrenVal) {
            $ChildData[$ChildrenK]['skuid'] = $ChildrenVal['ProductSkuID'];
            $ChildData[$ChildrenK]['num'] = $ChildrenVal['ChildrenNum'];
            if ($ChildrenK == 0) {
                // 获取默认价格
                $PriceData['dultnum'] = $ChildrenVal['AdultNum'];
                $PriceData['childnum'] = $ChildrenVal['ChildrenNum'];
                $PriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and `Date`=$DateStr and ProductSkuID=" . $ChildrenVal['ProductSkuID']);
                $PriceData['cost'] = ceil($PriceInfo['Price']);
                $PriceData['skuid'] = $ChildrenVal['ProductSkuID'];
            }
        }
        $JsonResult['ResultCode'] = 200;
        $JsonResult['Type'] = 0;
        $JsonResult['DultData'] = $DultData;
        $JsonResult['ChildData'] = $ChildData;
        $JsonResult['PriceData'] = $PriceData;
        $JsonResult['RoomPriceList'] = $RoomPriceList;
        return $JsonResult;
    }
    // 通过日期获取不分人群的sku
    private function GetSkuTypeIsTwo()
    {
        $DateStr = trim($_POST['Date']);
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($_POST['TourProductID']);
        
        $JsonResult['Type'] = 1;
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
        $TourProductLineModule = new TourProductLineModule();
        
        $TourProductLineErverPriceLists = $TourProductLineErverdayPriceModule->GetLists(" and `Date`=$DateStr and TourProductID=$TourProductID", 0, 50);
        $I = 0;
        foreach ($TourProductLineErverPriceLists as $key => $val) {
            $TourProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and ProductSkuID={$val['ProductSkuID']} and Status=1 and IsClose!=1");
            if ($TourProductLineSkuInfo) {
                $JsonResult['AdultData'][$I]['skuid'] = $TourProductLineSkuInfo['ProductSkuID'];
                $JsonResult['AdultData'][$I]['num'] = $TourProductLineSkuInfo['PeopleNum'];
                $PriceLists[$TourProductLineSkuInfo['PeopleNum']] = ceil($val['Price']);
                $RoomPriceList[$TourProductLineSkuInfo['PeopleNum']] = ceil($val['Price']);
                $I ++;
            }
        }
        sort($JsonResult['AdultData']);
        $PriceData['checknum'] = $JsonResult['AdultData'][0]['num'];
        $PriceData['cost'] = $PriceLists[$PriceData['checknum']];
        $PriceData['skuid'] = $JsonResult['AdultData'][0]['skuid'];
        $JsonResult['PriceData'] = $PriceData;
        $JsonResult['RoomPriceList'] = array(
            ceil($RoomPriceList[1]),
            ceil($RoomPriceList[2]/2),
            ceil($RoomPriceList[3]/3),
            ceil($RoomPriceList[4]/4)
        );
        $JsonResult['ResultCode'] = 200;
        $JsonResult['Message'] = '获取数据成功！';
        return $JsonResult;
    }
    
    // 跟团游预定选择日期
    public function GroupChoiceRoom()
    {
        if ($_POST['Intention'] == 'GroupRoomInit') {
            // 选择日期获取页面套餐信息
            $this->SearchByDate();
        } elseif ($_POST['Intention'] == 'GroupRoomDult') {
            // 存在成人和儿童套餐情况下，触发选择成人
            $this->SearchByAdult();
        } elseif ($_POST['Intention'] == 'GroupRoomAdult') {
            // 不分成人和儿童套餐情况下，触发选择人
            $this->GroupRoomAdult();
        } elseif ($_POST['Intention'] == 'GroupRoomChild') {
            // 触发儿童接口后取数据
            $this->SearchByChild();
        } elseif ($_POST['Intention'] == 'GroupRoomSubmit') {
            // 出发填写旅客页面
            $this->GroupRoomSubmit();
        } else {
            include template('TourGroupChoseRoom');
        }
    }
    // 存在成人和儿童套餐情况下，触发选择成人
    private function SearchByAdult()
    {
        $DateStr = trim($_POST['Date']);
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($_POST['TourProductID']);
        $SkuID = intval($_POST['SkuID']);
        
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
        $TourProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($SkuID);
        if (empty($TourProductLineSkuInfo)) {
            $JsonResult['ResultCode'] = 101;
            $JsonResult['Message'] = '该产品不支持购买！';
        } else {
            // 价格数据
            $PriceData['dultnum'] = $TourProductLineSkuInfo['AdultNum'];
            $PriceData['childnum'] = $TourProductLineSkuInfo['ChildrenNum'];
            $TourProductLineErverdayPriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and ProductSkuID=" . $SkuID . " and Date=" . $DateStr . ' and Inventory!=0');
            $PriceData['cost'] = ceil($TourProductLineErverdayPriceInfo['Price']);
            $PriceData['skuid'] = $SkuID;
            
            // 儿童房间的数据
            $TourProductLineErverdayPriceLists = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and TourProductID=" . $TourProductID . " and Date=" . $DateStr . ' and Inventory!=0', true);
            $I = 0;
            foreach ($TourProductLineErverdayPriceLists as $Key => $Value) {
                $SkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($Value['ProductSkuID']);
                if ($SkuInfo['Status'] == 1 && $SkuInfo['IsClose'] == 0 && $SkuInfo['AdultNum'] == $TourProductLineSkuInfo['AdultNum']) {
                    $ChildData[$I]['skuid'] = $SkuInfo['ProductSkuID'];
                    $ChildData[$I]['num'] = $SkuInfo['ChildrenNum'];
                    $I ++;
                }
            }
            $JsonResult['ResultCode'] = 200;
            $JsonResult['Message'] = '获取成功！';
            $JsonResult['PriceData'] = $PriceData;
            $JsonResult['ChildData'] = $ChildData;
        }
        echo json_encode($JsonResult);
        exit();
    }
    
    // 不分成人和儿童套餐情况下，触发选择成人
    private function GroupRoomAdult()
    {
        $DateStr = trim($_POST['Date']);
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($_POST['TourProductID']);
        $SkuID = intval($_POST['SkuID']);
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
        $TourProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($SkuID);
        $TourProductLineErverdayPriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and ProductSkuID=" . $SkuID . " and Date=" . $DateStr . ' and Inventory!=0');
        if (empty($TourProductLineErverdayPriceInfo) || empty($TourProductLineSkuInfo)) {
            $JsonResult['ResultCode'] = 101;
            $JsonResult['Message'] = '产品暂不支持购买！';
        } else {
            $PriceData['checknum'] = $TourProductLineSkuInfo['PeopleNum'];
            $PriceData['cost'] = ceil($TourProductLineErverdayPriceInfo['Price']);
            $PriceData['skuid'] = $SkuID;
            $JsonResult['Type'] = 1;
            $JsonResult['ResultCode'] = 200;
            $JsonResult['Message'] = '获取成功！';
            $JsonResult['PriceData'] = $PriceData;
        }
        echo json_encode($JsonResult);
        exit();
    }
    
    // 选择儿童接口
    private function SearchByChild()
    {
        $DateStr = trim($_POST['Date']);
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($_POST['TourProductID']);
        $SkuID = intval($_POST['SkuID']);
        
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
        
        $TourLineSkuPriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and ProductSkuID=$SkuID");
        $TourProductSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and ProductSkuID=$SkuID and Status=1 and IsClose=0");
        if (empty($TourLineSkuPriceInfo) || empty($TourProductSkuInfo)) {
            $JsonResult['ResultCode'] = 102;
            $JsonResult['Message'] = '产品暂不支持购买！';
        } else {
            $PriceData['dultnum'] = $TourProductSkuInfo['AdultNum'];
            $PriceData['childnum'] = $TourProductSkuInfo['ChildrenNum'];
            $PriceData['cost'] = ceil($TourLineSkuPriceInfo['Price']);
            $PriceData['skuid'] = $SkuID;
            $JsonResult['ResultCode'] = 200;
            $JsonResult['Message'] = '获取成功！';
            $JsonResult['PriceData'] = $PriceData;
        }
        echo json_encode($JsonResult);
        exit();
    }
    
    // 跟团游提交数据传递参数
    private function GroupRoomSubmit()
    {
        $DateStr = trim($_POST['Date']);
        $TourProductID = intval($_POST['TourProductID']);
        $SkuIDArray = $_POST['SkuID'];
        // $CostList = $_POST['CostList'];
        $SkuIDString = implode(',', $SkuIDArray);
        $Url = WEB_M_URL . '/group/order/?id=' . $TourProductID . '&d=' . $DateStr . '&sku=' . $SkuIDString;
        $JsonResult['ResultCode'] = 200;
        $JsonResult['Message'] = '获取成功！';
        $JsonResult['Url'] = $Url;
        echo json_encode($JsonResult);
        exit();
    }

    /**
     * 跟团游填写订单 http://m.57us.net/group/order/
     */
    public function LineOrder()
    {
        if ($_POST['Intention']=='GroupLineOrder')
        {
            $this->ConfirmOrder();
        }
        elseif ($_GET['Load']==1)
        {
            $DateStr = trim($_GET['d']);
            $DateStr = date('Ymd', strtotime($DateStr));
            $TourProductID = intval($_GET['id']);
            $SkuIDString = $_GET['sku'];
            $SkuIDArray = explode(',',$SkuIDString);
            $String = '';
            $TourProductLineModule = new TourProductLineModule();
            $TourProductLineSkuModule = new TourProductLineSkuModule();
            $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
            $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
            $I = 1;
            $Num = 0;
            foreach ($SkuIDArray as $Value) {
                $PriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and ProductSkuID=" . $Value . " and Date=" . $DateStr);
                $SkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($Value);
                if ($TourProductLineInfo['SkuType'] == 1) {
                    $String .= '<div class="row freeDetailList" data-id="' . $Value . '" data-value="' . $PriceInfo['Price'] . '"><div class="col-50">房间' . $I . '：' . $SkuInfo['AdultNum'] . '成人，' . $SkuInfo['ChildrenNum'] . '儿童</div><div class="col-50">费用：<span class="red">￥<i>' . ceil($PriceInfo['Price']) . '</i></span></div></div>';
                    $Num += $SkuInfo['AdultNum'];
                    $Num += $SkuInfo['ChildrenNum'];
                } else {
                    $String .= '<div class="row freeDetailList" data-id="' . $Value . '" data-value="' . $PriceInfo['Price'] . '"><div class="col-50">房间' . $I . '：' . $SkuInfo['PeopleNum'] . '人</div><div class="col-50">费用：<span class="red">￥<i>' . ceil($PriceInfo['Price']) . '</i></span></div></div>';
                    $Num += $SkuInfo['PeopleNum'];
                }
                unset($PriceInfo);
                $I ++;
            }
            $JsonResult['ResultCode'] = 200;
            $JsonResult['Message'] = '获取成功！';
            $JsonResult['Date'] = $_GET['d'];
            $JsonResult['TourProductID'] = $TourProductID;
            $JsonResult['Num'] = $Num;
            $JsonResult['CostList'] = $String;
            echo json_encode($JsonResult);
            exit();
        }
        else
        {
            include template('TourGroupLineOrder');
        }
    }
    
    // 支付部分---------------------------------------------------------------------------------------------
    
    
    /**
     * 确认订单
     */
    public function ConfirmOrder()
    { 
        $UserModule = new MemberUserModule();
        $OrderNumber = TourService::GetTourOrderNumber();
        $Mobile = $_POST['Mobile'];
        $VerifyCode = intval($_POST['VerifyCode']);
        if ($VerifyCode) {
            $Authentication = new MemberAuthenticationModule();
            $Validate = $Authentication->ValidateAccount($Mobile, $VerifyCode, 0);
            if ($Validate) {
                $Data = array('Mobile' => $Mobile, 'State' => 1, 'AddTime' => time());
                $UserID = $UserModule->InsertInfo($Data);
                if(!$UserID){
                    $json_result = array('ResultCode' => 101, 'Message' => '订单生成失败', 'LogMessage' => '操作失败(联系人关联失败)');
                }
                else{
                    $UserInfoModule = new  MemberUserInfoModule();
                    $InfoData['UserID'] = $UserID;
                    $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                    $InfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
                    $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                    $InfoData['Sex'] = 1;
                    $InfoData['Avatar']='/img/man3.0.png';
                    $Result1 = $UserInfoModule->InsertInfo($InfoData);
                }                
                $json_result = $this->Operate($OrderNumber, $UserID, $_POST);
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '短信验证码错误', 'LogMessage' => '操作失败(短信验证码错误)');
            }
        } else {
            if ($_SESSION['UserID']) {
                $UserID = $_SESSION['UserID'];
            } else {
                $UserInfo = $UserModule->GetUserIDbyMobile($Mobile);
                $UserID = $UserInfo['UserID'];
            }
            $json_result = $this->Operate($OrderNumber, $UserID, $_POST);
        }
        //添加订单操作日志
        $OrderLogModule = new TourProductOrderLogModule();
        $LogData = array('OrderNumber' => $OrderNumber, 'UserID' => $UserID, 'Remarks' => $json_result['LogMessage'], 'OldStatus' => 0, 'NewStatus' => 1, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 1);
        $OrderLogModule->InsertInfo($LogData);
        echo json_encode($json_result);
        exit;
    }
    
    /**
     * 确认订单_步骤
     */
    private function Operate($OrderNumber, $UserID, $Post)
    {
        // Sku信息
        $ProductSkuID = $Post['SkuID'];
        $DateStr = $Post['Date'];
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($Post['TourProductID']);
        // 查询价格,库存
        $TourProductLineErverDayPriceModule = new TourProductLineErverDayPriceModule();
        $TourPriceArr = array();
        $Success = true;
        $Money = 0;
        foreach ($ProductSkuID as $val) {
            $TourPriceInfo = $TourProductLineErverDayPriceModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID={$val} and `Date`=$DateStr and Inventory!=0");
            if ($TourPriceInfo) {
                $Money += $TourPriceInfo['Price'];
                if (isset($TourPriceArr[$val])) {
                    $TourPriceArr[$val]['Num'] = $TourPriceArr[$val]['Num'] + 1;
                } else {
                    $TourPriceArr[$val] = array(
                        'Num' => 1,
                        'Info' => $TourPriceInfo
                    );
                }
            } else {
                $Success = false;
            }
        }
        if ($Success) {
            // 基础信息
            $TourProductLineModule = new TourProductLineModule();
            $TourProductLineInfo = $TourProductLineModule->GetInfoByWhere(" and TourProductID=$TourProductID");
            // 详情信息
            $TourProductLineDetailedModule = new TourProductLineDetailedModule();
            $TourProductLineDetailedInfo = $TourProductLineDetailedModule->GetInfoByWhere(" and TourProductID=$TourProductID");
            // SKU信息
            $TourProductLineSkuModule = new TourProductLineSkuModule();
            $TourProductLineSkuLists = $TourProductLineSkuModule->GetInfoByWhere(" and TourProductID=$TourProductID", true);
            // 当日价格
            $TourProductLineSkuPriceModule = new TourProductLineSkuPriceModule();
            $TourProductLineSkuPriceLists = $TourProductLineSkuPriceModule->GetInfoByWhere(" and TourProductID=$TourProductID", true);
            // 其他信息
            $TourCategoryModule = new TourProductCategoryModule();
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($TourProductLineInfo['Category']);
            $AreaModule = new TourAreaModule();
            // 出行地点
            $DepartureCityInfo = $AreaModule->GetInfoByKeyID($TourProductLineInfo['Departure']);
            // 目的地
            $DestinationCityInfo = $AreaModule->GetInfoByKeyID($TourProductLineInfo['Destination']);
            $DestinationName = '';
            if ($DestinationCityInfo) {
                $DestinationName = $DestinationCityInfo['CnName'];
            }
            // 供应商信息
            $SupplierModule = new TourSupplierModule();
            $SupplierInfo = $SupplierModule->GetInfoByKeyID($TourProductLineInfo['SupplierID']);
            $OtherInfo = array(
                'CategoryName' => $CategoryName['CnName'],
                'DepartureName' => $DepartureCityInfo['CnName'],
                'DestinationName' => $DestinationName,
                'SupplierName' => $SupplierInfo['CnName']
            );
            // 快照信息
            $SnapshotModule = new TourProductLineSnapshotModule();
            $SnapshotInfo = $SnapshotModule->GetInfoByWhere(" and TourProductID=$TourProductID order by AddTime desc");
            if (empty($SnapshotInfo) || strtotime($SnapshotInfo['AddTime']) < strtotime($TourProductLineInfo['UpdateTime'])) {
                // 创建产品快照
                $SnapshotData = array(
                    'TourProductID' => $TourProductID,
                    'AddTime' => date("Y-m-d H:i:s", time()),
                    'LineInfo' => addcslashes(json_encode($TourProductLineInfo, JSON_UNESCAPED_UNICODE), "'"),
                    'DetailedInfo' => addcslashes(json_encode($TourProductLineDetailedInfo, JSON_UNESCAPED_UNICODE), "'"),
                    'SkuInfo' => json_encode($TourProductLineSkuLists, JSON_UNESCAPED_UNICODE),
                    'PriceInfo' => json_encode($TourProductLineSkuPriceLists, JSON_UNESCAPED_UNICODE),
                    'OtherInfo' => json_encode($OtherInfo, JSON_UNESCAPED_UNICODE)
                );
                $TourLineSnapshotID = $SnapshotModule->InsertInfo($SnapshotData);
            } else {
                $TourLineSnapshotID = $SnapshotInfo['TourLineSnapshotID'];
            }
            // 出行人数
            // $Number = intval($Post['Number']);
            // 旅客信息
            $Travellers = $Post['Travellers'];
            // 联系人信息
            $Contacts = $Post['Contacts'];
            $Email = $Post['Email'];
            // 用户下单留言
            $Message = $Post['Message'];
            // 订单编号
            $OrderData = array(
                'OrderNumber' => $OrderNumber,
                'UserID' => $UserID,
                'TotalAmount' => $Money,
                'FromIP' => GetIP(),
                'AddTime' => date('Y-m-d H:i:s', time()),
                'Status' => 1,
                'ExpirationTime' => date("Y-m-d H:i:s", time() + 900),
                'Contacts' => $Contacts,
                'Tel' => $Post['Mobile'],
                'Email' => $Email,
                'Message' => $Message,
                'TravelPeopleInfo' => json_encode($Travellers)
            );
            $OrderModule = new TourProductOrderModule();
            $OrderInfoModule = new TourProductOrderInfoModule();
            // 开启事务
            global $DB;
            $DB->query("BEGIN"); // 开始事务定义
            foreach ($TourPriceArr as $key => $val) {
                if ($val['Info']['Inventory'] != -1) {
                    $Inventory = $val['Info']['Inventory'] - $val['Num'];
                    if ($Inventory >= 0) {
                        $SkuResult = $TourProductLineErverDayPriceModule->UpdateInfoByKeyID(array(
                            'Inventory' => $Inventory
                        ), $val['Info']['DayPriceID']);
                        if (!$SkuResult) {
                            $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                            return $json_result = array(
                                'ResultCode' => 104,
                                'Message' => '订单提交失败',
                                'LogMessage' => '操作失败(库存数量更新失败)',
                            );
                        }
                    } else {
                        $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                        return $json_result = array(
                            'ResultCode' => 104,
                            'Message' => '订单提交失败',
                            'LogMessage' => '操作失败(库存数量更新失败,库存已为负数)',
                        );
                    }
                }
            }
            $result = $OrderModule->InsertInfo($OrderData);
            
            if (intval($result)==0) {
                $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                return $json_result = array(
                    'ResultCode' => 101,
                    'Message' => '订单提交失败',
                    'LogMessage' => '操作失败(订单添加失败)',
                );
            } else {
                $OrderInfoFalse = false;
                foreach ($TourPriceArr as $key => $val) {
                    $OrderInfoData = array(
                        'OrderNumber' => $OrderNumber,
                        'Depart' => date("Y-m-d", strtotime($val['Info']['Date'])),
                        'Num' => $val['Num'],
                        'UnitPrice' => $val['Info']['Price'],
                        'Money' => $val['Info']['Price'] * $val['Num'],
                        'TourProductID' => $TourProductID,
                        'TourProductSkuID' => $key,
                        'TourLineSnapshotID' => $TourLineSnapshotID
                    );
                    $result1 = $OrderInfoModule->InsertInfo($OrderInfoData);
                    if (!$result1) {
                        $OrderInfoFalse = true;
                    }
                }
                if ($OrderInfoFalse) {
                    $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                    return $json_result = array(
                        'ResultCode' => 102,
                        'Message' => '订单提交失败',
                        'LogMessage' => '操作失败(订单详细信息添加失败)',
                    );
                } else {
                    $DB->query("COMMIT"); // 执行事务
                    ToolService::SendSMSNotice(15160090744, '已产生出游订单，订单号：' . $OrderData['OrderNumber'] . '，预订人：' . $OrderData['Contacts'] . ' ，联系电话：' . $OrderData['Tel'] . '。');
                    ToolService::SendSMSNotice(18750258578, '已产生出游订单，订单号：' . $OrderData['OrderNumber'] . '，预订人：' . $OrderData['Contacts'] . ' ，联系电话：' . $OrderData['Tel'] . '。');
                    ToolService::SendSMSNotice(18050016313, '已产生出游订单，订单号：' . $OrderData['OrderNumber'] . '，预订人：' . $OrderData['Contacts'] . ' ，联系电话：' . $OrderData['Tel'] . '。');
                    ToolService::SendSMSNotice(15980805724, '已产生出游订单，订单号：' . $OrderData['OrderNumber'] . '，预订人：' . $OrderData['Contacts'] . ' ，联系电话：' . $OrderData['Tel'] . '。');
                    $times = date("Y/m/d", time());
                    $TourProductModule = new TourProductModule();
                    $TourProduct = $TourProductModule->GetInfoByKeyID($OrderInfoData['TourProductID']);
                    $result = ToolService::SendSMSNotice($OrderData['Tel'], "订单号" . $OrderData['OrderNumber'] . "，" . $OrderData['Contacts'] . "  $times  " . $TourProduct['ProductName'] . "，总价￥" . $OrderInfoData['Money'] . "。人数：" . $OrderInfoData['Num'] . "。我们将于15分钟后自动关闭未付款订单，请登录会员中心查看订单详情或致电400-018-5757。");
                    return $json_result = array(
                        'ResultCode' => 200,
                        'Message' => '订单提交成功',
                        'Url' => WEB_M_URL . '/group/' . $OrderNumber . '.html',
                        'LogMessage' => '订单创建成功,等待支付'
                    );
                }
            }
        } else {
            return $json_result = array(
                'ResultCode' => 103,
                'Message' => '订单提交失败,该商品已无库存',
                'LogMessage' => '操作失败(该商品已无库存)',
            );
        }
    }
    
    // 支付选择
    public function ChoicePay()
    {
        $OrderNumber = $_GET['OrderNumber'];
        $OrderModule = new TourProductOrderModule();
        $OrderInfoModule = new TourProductOrderInfoModule();
        $Order = $OrderModule->GetInfoByWhere(" and OrderNumber='$OrderNumber' and `Status`=1");
        $OrderInfo = $OrderInfoModule->GetInfoByWhere(" and OrderNumber='$OrderNumber'");
        if ($Order && $OrderInfo) {
            if (strtotime($Order['ExpirationTime']) > time()) {
                // 获取快照信息
                $SnapshotModule = new TourProductLineSnapshotModule();
                $SnapshotInfo = $SnapshotModule->GetInfoByKeyID($OrderInfo['TourLineSnapshotID']);
                $LineInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['LineInfo']), true);
                $SkuInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['SkuInfo']), true);
                $NewSkuInfo = array();
                foreach ($SkuInfo as $key => $val) {
                    if ($LineInfo['SkuType'] == 1) {
                        $NewSkuInfo[$val['ProductSkuID']] = array(
                            'AdultNum' => $val['AdultNum'],
                            'ChildNum' => $val['ChildrenNum']
                        );
                    } elseif ($LineInfo['SkuType'] == 2) {
                        $NewSkuInfo[$val['ProductSkuID']] = array(
                            'AdultNum' => $val['PeopleNum'],
                            'ChildNum' => 0
                        );
                    }
                }
                // 计算出行人数
                $PeopleNum = 0;
                $OrderInfoList = $OrderInfoModule->GetInfoByWhere(" and OrderNumber='$OrderNumber'", true);
                foreach ($OrderInfoList as $val) {
                    $PeopleNum += ($NewSkuInfo[$val['TourProductSkuID']]['AdultNum'] + $NewSkuInfo[$val['TourProductSkuID']]['ChildNum']) * $val['Num'];
                }
                $Step1 = 1;
                $Step2 = 1;
                include template("TourGroupPayOrder");
            } else {
                $UpData['Status'] = 10;
                $UpData['Remarks'] = '超时未支付,订单自动取消';
                if ($OrderModule->UpdateInfoByWhere($UpData,  '`OrderNumber`=\'' . $OrderNumber . '\'')) {
                    $OrderInfoLists = $OrderInfoModule->GetInfoByWhere(" and OrderNumber='$OrderNumber'", true);
                    $TourProductLineErverdayPriceModule = new TourProductLineErverdayPriceModule();
                    foreach ($OrderInfoLists as $val) {
                        $DateStr = date('Ymd', strtotime($val['Depart']));
                        $TourPriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and TourProductID={$val['TourProductID']} and ProductSkuID={$val['TourProductSkuID']} and `Date`='$DateStr'");
                        if ($TourPriceInfo && $TourPriceInfo['Inventory'] != - 1) {
                            $TourProductLineErverdayPriceModule->UpdateInfoByKeyID(array(
                                'Inventory' => $TourPriceInfo['Inventory'] + $val['Num']
                            ), $TourPriceInfo['DayPriceID']);
                        }
                    }
                    // 添加订单日志
                    $OrderLogModule = new TourProductOrderLogModule();
                    if ($_SESSION['UserID'] && ! empty($_SESSION['UserID'])) {
                        $UserID = $_SESSION['UserID'];
                    } else {
                        $MemberUserModule = new MemberUserModule();
                        $UserInfo = $MemberUserModule->GetUserIDbyMobile($Order['Tel']);
                        $UserID = $UserInfo['UserID'];
                    }
                    $LogData = array(
                        'OrderNumber' => $OrderNumber,
                        'UserID' => $UserID,
                        'OldStatus' => 1,
                        'NewStatus' => 10,
                        'OperateTime' => date("Y-m-d H:i:s", time()),
                        'Remarks'=>'超时未支付,订单取消',
                        'IP' => GetIP(),
                        'Type' => 1
                    );
                    $OrderLogModule->InsertInfo($LogData);
                }
                alertandgotopage('超时未支付,订单已取消', WEB_M_URL . '/group/local/');
            }
        } else {
            alertandgotopage('订单异常', WEB_M_URL . '/group/local/');
        }
    }
    
    // 支付准备
    public function Pay()
    {
        $Type = trim($_GET['Type']);
        $OrderNo = trim($_GET['ID']);
        $OrderModule = new TourProductOrderModule();
        $OrderInfoModule = new TourProductOrderInfoModule();
        $Order = $OrderModule->GetInfoByWhere(" and OrderNumber='$OrderNo' and `Status`=1");
        $OrderInfo = $OrderInfoModule->GetInfoByWhere(" and OrderNumber='$OrderNo'");
        if ($Order && $OrderInfo) {
            // 获取快照信息
            $SnapshotModule = new TourProductLineSnapshotModule();
            $SnapshotInfo = $SnapshotModule->GetInfoByKeyID($OrderInfo['TourLineSnapshotID']);
            $LineInfo = json_decode(str_replace("\r\n", "\\r\\n", $SnapshotInfo['LineInfo']), true);
            if ($Type == 'alipay') {
                $Data['OrderNo'] = $Order['OrderNumber'];
                $Data['Subject'] = html_entity_decode(_substr($LineInfo['ProductName'], 40), ENT_QUOTES);
                $Data['Money'] = $Order['TotalAmount'];
                if ($LineInfo['ProductPackage']) {
                    $Data['Body'] = html_entity_decode($LineInfo['ProductName'] . '·' . $LineInfo['ProductPackage'], ENT_QUOTES);
                } else {
                    $Data['Body'] = html_entity_decode($LineInfo['ProductName'], ENT_QUOTES);
                }
                $Data['ReturnUrl'] = WEB_MUSER_URL . '/paytour/groupresult/';
                $Data['NotifyUrl'] = WEB_MUSER_URL . '/paytour/groupresult/';
                $Data['ProductUrl'] = WEB_M_URL . "/group/{$OrderInfo['TourProductID']}.html";
                $Data['RunTime'] = time();
				$Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MUSER_URL.'/pay/alipay/',$Data);
            } elseif ($Type == 'wxpay') {
                $Data['OrderNo'] = $Order['OrderNumber'];
                $Data['Subject'] = html_entity_decode(_substr($LineInfo['ProductName'], 40), ENT_QUOTES);
                $Data['Money'] = $Order['TotalAmount'];
                if ($LineInfo['ProductPackage']) {
                    $Data['Body'] = html_entity_decode($LineInfo['ProductName'] . '·' . $LineInfo['ProductPackage'], ENT_QUOTES);
                } else {
                    $Data['Body'] = html_entity_decode($LineInfo['ProductName'], ENT_QUOTES);
                }
                $Data['ReturnUrl'] = WEB_MUSER_URL . '/paytour/groupresult/';
                $Data['RunTime'] = time();
                $Data['Sign'] = $this->VerifyData($Data);
				echo ToolService::PostForm(WEB_MUSER_URL.'/pay/wxpay/',$Data);
            }
        } else {
            alertandgotopage('不能操作的订单', WEB_MUSER_URL . '/musertour/myorder/');
        }
    }
}
