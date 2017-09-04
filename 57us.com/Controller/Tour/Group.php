<?php

/**
 * @desc  跟团游
 * Class Group
 */
class Group
{

    public function __construct()
    {
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
        // 列表页右侧广告
        $this->TourGroupADLists  = NewsGetAdInfo('tour_list');
    }

    /**
     * 跟团游首页
     */
    public function LineLists()
    {
        $TagNav = 'local';
        $Title = "美国当地参团_美国当地游_美国当地参团游_美国当地参团旅游 - 57美国网";
        $Keywords = "美国当地参团,美国当地游,美国当地参团游,美国当地参团旅游, 参团美国游,美国旅游参团,美国参团旅游";
        $Description = "57美国网当地参团频道，为您提供精品美国当地参团旅游线路，包含详细的当地参团游介绍、行程安排及线路报价等。";
        include template('GroupLineLists');
    }
    
    // 当地参团
    public function Local()
    {
        if (empty($_POST)) {
            // 搜索用
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
            // 第一次载入页面
            $TagNav = 'local';
            if ($_GET['K']) {
                $Keyword = trim($_GET['K']);
                if ($Keyword != '') {
                    $MysqlWhere = " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
                }
            }
            // 初始化数据
            $MysqlWhere .= ' and Category=12 and Status=1 and IsClose!=1 and (RelationProductID=0 or RelationProductID is null)  order by R2 DESC,UpdateTime DESC';
            $TourProductLineModule = new TourProductLineModule();
            $Rscount = $TourProductLineModule->GetListsNum($MysqlWhere);
            $page = intval($_GET['p']);
            if ($page < 1) {
                $page = 1;
            }
            $PageSize = 6;
            $Data = array();
            if ($Rscount['Num']) {
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
                    $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
                    $Data['Data'][$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
                    // 结束城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Destination']);
                    $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                    $Data['Data'][$Key]['TouStroke'] = $Value['Days'];
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    $Data['Data'][$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
                    if ($Data['Data'][$Key]['TourCostPrice'] == 0) {
                        $Data['Data'][$Key]['TourCostPrice'] = ceil($Data['Data'][$Key]['TourPicre'] * 1.15);
                    }
                    $Data['Data'][$Key]['TourRecommend'] = $Value['R3'];
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
                    $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                    $Data['Data'][$Key]['Sales'] = $Value['Sales'];
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourService'] = $TagHtml;
                    foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                        $Data['Data'][$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
                    }
                }
                $ClassPage = new Page($Rscount['Num'], $PageSize, 3);
                $ShowPage = $ClassPage->showpage();
            } else {
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R3 DESC,LowPrice ASC';
                $TourProductLineLists = $TourProductLineModule->GetLists($MysqlWhere, 0, $PageSize);
                $Data['Data'] = array();
                foreach ($TourProductLineLists as $Key => $Value) {
                    $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
                    $Data['Data'][$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
                    // 结束城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Destination']);
                    $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                    $Data['Data'][$Key]['TouStroke'] = $Value['Days'];
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    $Data['Data'][$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
                    if ($Data['Data'][$Key]['TourCostPrice'] == 0) {
                        $Data['Data'][$Key]['TourCostPrice'] = ceil($Data['Data'][$Key]['TourPicre'] * 1.15);
                    }
                    $Data['Data'][$Key]['TourRecommend'] = $Value['R3'];
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
                    $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                    $Data['Data'][$Key]['Sales'] = $Value['Sales'];
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourService'] = $TagHtml;
                    foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                        $Data['Data'][$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
                    }
                }
            }
            include template('GroupLineLists');
        } else {
            $MysqlWhere = ' and Category=12 and Status=1 and IsClose!=1 and (RelationProductID=0 or RelationProductID is null) ';
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
                    $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
                    $Data['Data'][$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
                    // 结束城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Destination']);
                    $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                    $Data['Data'][$Key]['TouStroke'] = $Value['Days'];
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    $Data['Data'][$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
                    if ($Data['Data'][$Key]['TourCostPrice'] == 0) {
                        $Data['Data'][$Key]['TourCostPrice'] = ceil($Data['Data'][$Key]['TourPicre'] * 1.15);
                    }
                    $Data['Data'][$Key]['TourRecommend'] = $Value['R3'];
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
                    $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                    $Data['Data'][$Key]['Sales'] = $Value['Sales'];
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourService'] = $TagHtml;
                    foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                        $Data['Data'][$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
                    }
                }
                MultiPage($Data, 6);
            } else {
                if ($Keyword != '') {
                    $Data['ResultCode'] = 103;
                } else {
                    $Data['ResultCode'] = 101;
                }
                // $Data['Message'] = '没有找到相关产品';
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R3 DESC,LowPrice ASC';
                $TourProductLineLists = $TourProductLineModule->GetLists($MysqlWhere, 0, $PageSize);
                $Data['Data'] = array();
                foreach ($TourProductLineLists as $Key => $Value) {
                    $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
                    $Data['Data'][$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
                    // 结束城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Destination']);
                    $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                    $Data['Data'][$Key]['TouStroke'] = $Value['Days'];
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    $Data['Data'][$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
                    if ($Data['Data'][$Key]['TourCostPrice'] == 0) {
                        $Data['Data'][$Key]['TourCostPrice'] = ceil($Data['Data'][$Key]['TourPicre'] * 1.15);
                    }
                    $Data['Data'][$Key]['TourRecommend'] = $Value['R3'];
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
                    $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                    $Data['Data'][$Key]['Sales'] = $Value['Sales'];
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourService'] = $TagHtml;
                    foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                        $Data['Data'][$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
                    }
                }
            }
            echo json_encode($Data);
            exit();
        }
    }
    
    // 国内参团
    public function Home()
    {
        if (empty($_POST)) {
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
            // 第一次载入页面
            if ($_GET['K']) {
                $Keyword = trim($_GET['K']);
                if ($Keyword != '') {
                    $MysqlWhere = " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
                }
            }
            $TagNav = 'home';
            $MysqlWhere .= ' and Status=1 and Category=4 and IsClose!=1 and RelationProductID=0  order by R2 DESC,UpdateTime DESC';
            $TourProductLineModule = new TourProductLineModule();
            $Rscount = $TourProductLineModule->GetListsNum($MysqlWhere);
            $page = intval($_GET['p']);
            if ($page < 1) {
                $page = 1;
            }
            $PageSize = 6;
            $Data = array();
            if ($Rscount['Num']) {
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
                    $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
                    $Data['Data'][$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
                    $Data['Data'][$Key]['TouStroke'] = $Value['Days'];
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    $Data['Data'][$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
                    if ($Data['Data'][$Key]['TourCostPrice'] == 0) {
                        $Data['Data'][$Key]['TourCostPrice'] = ceil($Data['Data'][$Key]['TourPicre'] * 1.15);
                    }
                    $Data['Data'][$Key]['TourRecommend'] = $Value['R3'];
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
                    $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourService'] = $TagHtml;
                    foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                        $Data['Data'][$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
                    }
                }
                $ClassPage = new Page($Rscount['Num'], $PageSize, 3);
                $ShowPage = $ClassPage->showpage();
            } else {
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R3 DESC,LowPrice ASC';
                $TourProductLineLists = $TourProductLineModule->GetLists($MysqlWhere, 0, 6);
                $Data['Data'] = array();
                foreach ($TourProductLineLists as $Key => $Value) {
                    $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
                    $Data['Data'][$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
                    // 结束城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Destination']);
                    $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                    $Data['Data'][$Key]['TouStroke'] = $Value['Days'];
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    $Data['Data'][$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
                    if ($Data['Data'][$Key]['TourCostPrice'] == 0) {
                        $Data['Data'][$Key]['TourCostPrice'] = ceil($Data['Data'][$Key]['TourPicre'] * 1.15);
                    }
                    $Data['Data'][$Key]['TourRecommend'] = $Value['R3'];
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
                    $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourService'] = $TagHtml;
                    foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                        $Data['Data'][$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
                    }
                }
            }
            include template('GroupLineLists');
        } else {
            $MysqlWhere = ' and Status=1 and Category=4 and IsClose!=1 and RelationProductID=0';
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
                    $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
                    $Data['Data'][$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
                    $Data['Data'][$Key]['TouStroke'] = $Value['Days'];
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    $Data['Data'][$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
                    if ($Data['Data'][$Key]['TourCostPrice'] == 0) {
                        $Data['Data'][$Key]['TourCostPrice'] = ceil($Data['Data'][$Key]['TourPicre'] * 1.15);
                    }
                    $Data['Data'][$Key]['TourRecommend'] = $Value['R3'];
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
                    $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourService'] = $TagHtml;
                    foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                        $Data['Data'][$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
                    }
                }
                MultiPage($Data, 6);
            } else {
                if ($Keyword != '') {
                    $Data['ResultCode'] = 103;
                } else {
                    $Data['ResultCode'] = 101;
                }
                // $Data['Message'] = '没有找到相关产品';
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R3 DESC,LowPrice ASC';
                $TourProductLineLists = $TourProductLineModule->GetLists($MysqlWhere, 0, $PageSize);
                $Data['Data'] = array();
                foreach ($TourProductLineLists as $Key => $Value) {
                    $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
                    $Data['Data'][$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
                    // 结束城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Destination']);
                    $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                    $Data['Data'][$Key]['TouStroke'] = $Value['Days'];
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    $Data['Data'][$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
                    if ($Data['Data'][$Key]['TourCostPrice'] == 0) {
                        $Data['Data'][$Key]['TourCostPrice'] = ceil($Data['Data'][$Key]['TourPicre'] * 1.15);
                    }
                    $Data['Data'][$Key]['TourRecommend'] = $Value['R3'];
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
                    $Data['Data'][$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourService'] = $TagHtml;
                    foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                        $Data['Data'][$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
                    }
                }
            }
            echo json_encode($Data);
            exit();
        }
    }
    
    // 跟团游详情页
    public function LineDetails()
    {
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
            
		if (empty($TourProductLineInfo)) {
            alertandback('该商品不存在了！');
        }
        //添加浏览记录
        $Type=4;
        MemberService::AddBrowsingHistory($TourProductID,$Type);
        $TourProductImageModule = new TourProductImageModule();
        $TourImages = $TourProductImageModule->GetListsByTourProductID($TourProductLineInfo['TourProductID']);
        // 出发城市
        $TourAreaModule = new TourAreaModule();
        $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($TourProductLineInfo['Departure']);
        $DepartureID = $TourProductLineInfo['Departure'];
        $TourProductLineInfo['Departure'] = $TourAreaInfo['CnName'];
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
            //判断要提前预定的时间
            $StartDate = date('Ymd', strtotime('+'.$TourProductLineInfo['AdvanceDays'].' day'));
            foreach ($ErverdayPriceLists as $val) {
                if ($StartDate<=$val['Date'])
                {
                    $Data = array();
                    $Data['Date'] = date('Y-m-d', strtotime($val['Date']));
                    $Data['Price'] = strval(ceil($val['Price']));
                    $JsonArr[] = $Data;
                }
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
        
        if ($TourProductLineInfo['Category'] == 12) {
            $TagNav = 'local';
        } elseif ($TourProductLineInfo['Category'] == 4) {
            $TagNav = 'home';
        }
        
        // 同程推荐
        $CityRecommendedList = $TourProductLineModule->GetLists(" and Departure={$DepartureID} and Status = 1 and IsClose = 0", 0, 8);
        $CityWideRecommendNum = count($CityRecommendedList);
        if ($CityWideRecommendNum < 8) {
            $Num = 8 - $CityWideRecommendNum;
            $OtherRecommend = $TourProductLineModule->GetInfoByWhere(' and TourProductID !=' . $TourProductID . ' and Status = 1 order by TourProductLineID desc limit ' . $Num, true);
            $CityRecommendedList = array_merge($CityRecommendedList, $OtherRecommend);
        }
        foreach ($CityRecommendedList as $key => $val) {
            $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($val['TourProductID']);
            $CityRecommendedList[$key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
        }
        //商品评分
        $TourOrderEvaluateCountModule=new TourOrderEvaluateCountModule();
        $EvaluateCountInfo=$TourOrderEvaluateCountModule->GetInfoByWhere(" and TourProductID=$TourProductID");
        $AllCount=round(($EvaluateCountInfo['ServerFractionAll']+$EvaluateCountInfo['ConvenientFractionAll']+$EvaluateCountInfo['ExperienceFractionAll']+$EvaluateCountInfo['PerformanceFractionAll'])/4/$EvaluateCountInfo['Times'],1);
        //用户评价
        $TourOrderEvaluateModule=new TourOrderEvaluateModule();
        //真是评论用户数
        $CustomerNum=count($TourOrderEvaluateModule->GetInfoByWhere(" and TourProductID=$TourProductID group by UserID",true));
   
        $Title = "{$TourProductLineInfo['ProductName']} - 57美国网";
        $Keywords = $TourProductLineInfo['Keywords'] ? $TourProductLineInfo['Keywords'] : $TourProductLineInfo['ProductName'];
        $Description = _substr(strip_tags($Description['DesContent'][0]), 150) . ',了解美国旅游攻略，规划美国旅游行程，预订美国旅游线路，尽在57美国网！';
        include template('GroupLineDetails');
    }
    
    // 日期AJAX
    public function SearchByDate()
    {
        $DateStr = trim($_POST['date']);
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($_POST['ID']);
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
        $TourProductLineErverPriceLists = $TourProductLineErverdayPriceModule->GetLists(" and `Date`=$DateStr and TourProductID=$TourProductID", 0, 50, array(
            'ProductSkuID',
            'Price'
        ));
        // 初始化筛选数组
        $FilterArr = array(
            'Type' => 0,
            'AdultArr' => array(),
            'Price' => array()
        );
        $TourProductLineInfo = $TourProductLineModule->GetInfoByWhere(" and TourProductID=$TourProductID");
        if ($TourProductLineInfo) {
            $FilterArr['Type'] = $TourProductLineInfo['SkuType'];
            foreach ($TourProductLineErverPriceLists as $key => $val) {
                $TourProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and ProductSkuID={$val['ProductSkuID']} and Status=1 and IsClose!=1");
                if ($TourProductLineSkuInfo) {
                    // 填充筛选数组
                    if (empty($FilterArr['AdultArr'])) {
                        if ($TourProductLineInfo['SkuType'] == 1) {
                            $Data['Num'] = $TourProductLineSkuInfo['AdultNum'];
                            $Data['ChildArr'] = array();
                            array_unshift($Data['ChildArr'], array(
                                'Num' => $TourProductLineSkuInfo['ChildrenNum'],
                                'SkuID' => $val['ProductSkuID'],
                                'Price' => $val['Price']
                            ));
                            $FilterArr['AdultArr'][] = $Data;
                        } elseif ($TourProductLineInfo['SkuType'] == 2) {
                            $FilterArr['AdultArr'][] = array(
                                'Num' => $TourProductLineSkuInfo['PeopleNum'],
                                'SkuID' => $val['ProductSkuID'],
                                'Price' => $val['Price']
                            );
                        }
                        $FilterArr['Price'] = array(
                            'Money' => $val['Price'],
                            'SkuID' => $val['ProductSkuID']
                        );
                    } else {
                        if ($TourProductLineInfo['SkuType'] == 1) {
                            if ($TourProductLineSkuInfo['AdultNum'] < $FilterArr['AdultArr'][0]['Num']) {
                                $Data['Num'] = $TourProductLineSkuInfo['AdultNum'];
                                $Data['ChildArr'] = array();
                                array_unshift($Data['ChildArr'], array(
                                    'Num' => $TourProductLineSkuInfo['ChildrenNum'],
                                    'SkuID' => $val['ProductSkuID'],
                                    'Price' => $val['Price']
                                ));
                                array_unshift($FilterArr['AdultArr'], $Data);
                                $FilterArr['Price'] = array(
                                    'Money' => $val['Price'],
                                    'SkuID' => $val['ProductSkuID']
                                );
                            } else {
                                $return_key = $this->AdultNumExists($TourProductLineSkuInfo['AdultNum'], $FilterArr['AdultArr']);
                                if ($return_key !== false) {
                                    if ($TourProductLineSkuInfo['ChildrenNum'] < $FilterArr['AdultArr'][$return_key]['ChildArr'][0]['Num']) {
                                        array_unshift($FilterArr['AdultArr'][$return_key]['ChildArr'], array(
                                            'Num' => $TourProductLineSkuInfo['ChildrenNum'],
                                            'SkuID' => $val['ProductSkuID'],
                                            'Price' => $val['Price']
                                        ));
                                        $FilterArr['Price'] = array(
                                            'Money' => $val['Price'],
                                            'SkuID' => $val['ProductSkuID']
                                        );
                                    } else {
                                        $FilterArr['AdultArr'][$return_key]['ChildArr'][] = array(
                                            'Num' => $TourProductLineSkuInfo['ChildrenNum'],
                                            'SkuID' => $val['ProductSkuID'],
                                            'Price' => $val['Price']
                                        );
                                    }
                                } else {
                                    $Data['Num'] = $TourProductLineSkuInfo['AdultNum'];
                                    $Data['ChildArr'] = array();
                                    array_unshift($Data['ChildArr'], array(
                                        'Num' => $TourProductLineSkuInfo['ChildrenNum'],
                                        'SkuID' => $val['ProductSkuID'],
                                        'Price' => $val['Price']
                                    ));
                                    $FilterArr['AdultArr'][] = $Data;
                                }
                            }
                        } elseif ($TourProductLineInfo['SkuType'] == 2) {
                            if ($TourProductLineSkuInfo['PeopleNum'] < $FilterArr['AdultArr'][0]['Num']) {
                                array_unshift($FilterArr['AdultArr'], array(
                                    'Num' => $TourProductLineSkuInfo['PeopleNum'],
                                    'SkuID' => $val['ProductSkuID'],
                                    'Price' => $val['Price']
                                ));
                                $FilterArr['Price'] = array(
                                    'Money' => $val['Price'],
                                    'SkuID' => $val['ProductSkuID']
                                );
                            } else {
                                $return_key = $this->AdultNumExists($TourProductLineSkuInfo['PeopleNum'], $FilterArr['AdultArr']);
                                if ($return_key === false) {
                                    $FilterArr['AdultArr'][] = array(
                                        'Num' => $TourProductLineSkuInfo['PeopleNum'],
                                        'SkuID' => $val['ProductSkuID'],
                                        'Price' => $val['Price']
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        
        if ($FilterArr['Type'] == 0) {
            $json_result = array(
                'ResultCode ' => 100,
                'Message' => '没有可以选择的房间'
            );
        } else {
            $json_result = array();
            $json_result['ResultCode'] = 200;
            $FilterArr['AdultArr'] = $this->ReSortFilterArr($FilterArr['AdultArr']);
            $TourProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and ProductSkuID={$FilterArr['Price']['SkuID']}");
            // 筛选最低价格
            $Price = array();
            if ($FilterArr['Type'] == 1) {
                $json_result['Type'] = 0;
                foreach ($FilterArr['AdultArr'] as $key => $val) {
                    foreach ($val['ChildArr'] as $ckey => $cval) {
                        $PeoleNum = $val['Num'] + $cval['Num'];
                        if (! isset($Price[$PeoleNum]) || (isset($Price[$PeoleNum]) && $cval['Price'] < $Price[$PeoleNum])) {
                            $Price[$PeoleNum] = $cval['Price'];
                        }
                    }
                    if ($key == 0) {
                        $json_result['AdultData'] = '<a href="javascript:;" value="' . $val['Num'] . '" data-num="' . $val['Num'] . '" class="selected">' . $val['Num'] . '成人</a>';
                        foreach ($val['ChildArr'] as $ckey => $cval) {
                            $PeoleNum = $val['Num'] + $cval['Num'];
                            if (! isset($Price[$PeoleNum]) || (isset($Price[$PeoleNum]) && $cval['Price'] < $Price[$PeoleNum])) {
                                $Price[$PeoleNum] = $cval['Price'];
                            }
                            if ($ckey == 0) {
                                $json_result['ChildData'] = '<a href="javascript:;" value="' . $cval['SkuID'] . '" data-num="' . $cval['Num'] . '" class="selected">' . $cval['Num'] . '儿童</a>';
                            } else {
                                $json_result['ChildData'] .= '<a href="javascript:;" value="' . $cval['SkuID'] . '" data-num="' . $cval['Num'] . '">' . $cval['Num'] . '儿童</a>';
                            }
                        }
                    } else {
                        if (! isset($val['SkuID'])) {
                            $json_result['AdultData'] .= '<a href="javascript:;" value="' . $val['Num'] . '" data-num="' . $val['Num'] . '">' . $val['Num'] . '成人</a>';
                        } else {
                            $json_result['AdultData'] .= '<a href="javascript:;" value="' . $val['SkuID'] . '" data-num="' . $val['Num'] . '">' . $val['Num'] . '成人</a>';
                        }
                    }
                }
                $json_result['PriceData'] = array(
                    'skuid' => $FilterArr['Price']['SkuID'],
                    'cost' => ceil($FilterArr['Price']['Money']),
                    'checknum' => $TourProductLineSkuInfo['AdultNum'] . '成人，' . $TourProductLineSkuInfo['ChildrenNum'] . '儿童'
                );
            } elseif ($FilterArr['Type'] == 2) {
                $json_result['Type'] = 1;
                foreach ($FilterArr['AdultArr'] as $key => $val) {
                    $PeoleNum = $val['Num'];
                    if (! isset($Price[$PeoleNum]) || (isset($Price[$PeoleNum]) && $cval['Price'] < $Price[$PeoleNum])) {
                        $Price[$PeoleNum] = $val['Price'];
                    }
                    if ($key == 0) {
                        $json_result['AdultData'] = '<a href="javascript:;" value="' . $val['SkuID'] . '" data-num="' . $val['Num'] . '" class="selected">' . $val['Num'] . '人</a>';
                    } else {
                        if (! isset($val['SkuID'])) {
                            $json_result['AdultData'] .= '<a href="javascript:;" value="' . $val['Num'] . '" data-num="' . $val['Num'] . '">' . $val['Num'] . '成人</a>';
                        } else {
                            $json_result['AdultData'] .= '<a href="javascript:;" value="' . $val['SkuID'] . '" data-num="' . $val['Num'] . '">' . $val['Num'] . '人</a>';
                        }
                    }
                }
                $json_result['PriceData'] = array(
                    'skuid' => $FilterArr['Price']['SkuID'],
                    'cost' => ceil($FilterArr['Price']['Money']),
                    'checknum' => $TourProductLineSkuInfo['PeopleNum'] . '人'
                );
            }
            
            @ksort($Price);
            $RoomHtml = '';
            $NumArr = array(
                1,
                2,
                3,
                4
            );
            foreach ($NumArr as $val) {
                if (isset($Price[$val])) {
                    $RoomHtml .= '<span id="roomprice' . $val . '">￥' . ceil($Price[$val] / $val) . '/人起</span>';
                } else {
                    $RoomHtml .= '<span id="roomprice' . $val . '">暂无房间</span>';
                }
            }
            $json_result['RoomPriceList'] = $RoomHtml;
        }
        echo json_encode($json_result);
    }
    
    // 选择成人AJAX
    public function SearchByAdult()
    {
        $DateStr = trim($_POST['date']);
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($_POST['ID']);
        $SkuID = intval($_POST['skuid']);
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
        if ($SkuID < 5) {
            $TourProductLineSkuList = $TourProductLineSkuModule->GetInfoByWhere(" and AdultNum=$SkuID and TourProductID=$TourProductID and `Status`=1", true);
            $ChildNum = - 1;
            $SortArr = array();
            $json_result = array(
                'Type' => 0
            );
            foreach ($TourProductLineSkuList as $key => $val) {
                $TourLineSkuPriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID={$val['ProductSkuID']} and (Inventory>0 or Inventory=-1)");
                if ($TourLineSkuPriceInfo) {
                    $SortArr[$key] = $val['ChildrenNum'];
                    if ($ChildNum == - 1) {
                        $ChildNum = $val['ChildrenNum'];
                        $json_result['PriceData'] = array(
                            'skuid' => $val['ProductSkuID'],
                            'cost' => ceil($TourLineSkuPriceInfo['Price']),
                            'checknum' => $val['AdultNum'] . '成人，' . $val['ChildrenNum'] . '儿童'
                        );
                    } else {
                        if ($val['ChildrenNum'] < $ChildNum) {
                            $ChildNum = $val['ChildrenNum'];
                            $json_result['PriceData'] = array(
                                'skuid' => $val['ProductSkuID'],
                                'cost' => ceil($TourLineSkuPriceInfo['Price']),
                                'checknum' => $val['AdultNum'] . '成人，' . $val['ChildrenNum'] . '儿童'
                            );
                        }
                    }
                } else {
                    unset($TourProductLineSkuList[$key]);
                }
            }
            array_multisort($SortArr, SORT_NATURAL, $TourProductLineSkuList);
            foreach ($TourProductLineSkuList as $key => $val) {
                if ($key == 0) {
                    $json_result['ChildData'] = '<a href="javascript:;" value="' . $val['ProductSkuID'] . '" data-num="' . $val['ChildrenNum'] . '"class="selected">' . $val['ChildrenNum'] . '儿童</a>';
                } else {
                    $json_result['ChildData'] .= '<a href="javascript:;" value="' . $val['ProductSkuID'] . '" data-num="' . $val['ChildrenNum'] . '"class="selected">' . $val['ChildrenNum'] . '儿童</a>';
                }
            }
        } else {
            $TourLineSkuPriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID=$SkuID and Date=$DateStr and (Inventory>0 or Inventory=-1)");
            $TourProductSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID=$SkuID");
            if ($TourLineSkuPriceInfo) {
                $json_result = array(
                    'ResultCode' => 200,
                    'Type' => 1,
                    'PriceData' => array(
                        'skuid' => $SkuID,
                        'cost' => ceil($TourLineSkuPriceInfo['Price']),
                        'checknum' => $TourProductSkuInfo['PeopleNum'] . '人'
                    )
                );
            } else {
                $json_result = array(
                    'ResultCode' => 100,
                    'Message' => '该类型房间已售空'
                );
            }
        }
        echo json_encode($json_result);
    }
    
    // 选择儿童接口
    public function SearchByChild()
    {
        $DateStr = trim($_POST['date']);
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($_POST['ID']);
        $SkuID = intval($_POST['skuid']);
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
        $TourLineSkuPriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID=$SkuID and (Inventory>0 or Inventory=-1)");
        $TourProductSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID=$SkuID");
        if ($TourLineSkuPriceInfo) {
            $json_result = array(
                'ResultCode' => 200,
                'PriceData' => array(
                    'skuid' => $SkuID,
                    'cost' => ceil($TourLineSkuPriceInfo['Price']),
                    'checknum' => $TourProductSkuInfo['AdultNum'] . '成人，' . $TourProductSkuInfo['ChildrenNum'] . '儿童'
                )
            );
        } else {
            $json_result = array(
                'ResultCode' => 100,
                'Message' => '该类型房间已售空'
            );
        }
        echo json_encode($json_result);
    }
    
    // 添加房间
    public function AddRoom()
    {
        $DateStr = trim($_POST['date']);
        $DateStr = date('Ymd', strtotime($DateStr));
    }
    
    // 是否已经存在该选项
    private function AdultNumExists($num, $arr)
    {
        foreach ($arr as $key => $val) {
            if ($num == $val['Num']) {
                return $key;
            }
        }
        return false;
    }
    
    // 数组按重小到大排序
    private function ReSortFilterArr($arr)
    {
        $AdultSortArr = array();
        foreach ($arr as $key => $val) {
            if (isset($arr[$key]['ChildArr'])) {
                $ChildSortArr = array();
                foreach ($arr[$key]['ChildArr'] as $ckey => $cval) {
                    $ChildSortArr[$ckey] = $cval['Num'];
                }
                array_multisort($ChildSortArr, SORT_NATURAL, $arr[$key]['ChildArr']);
            }
            $AdultSortArr[$key] = $val['Num'];
        }
        array_multisort($AdultSortArr, SORT_NATURAL, $arr);
        return $arr;
    }
    
    // 支付部分---------------------------------------------------------------------------------------------
    // 生成预定地址
    public function GroupOrder()
    {
        $Data['ProductId'] = $_GET['ProductId'];
        $Data['Date'] = $_GET['Date'];
        $Data['Skuid'] = $_GET['Skuid'];
        if (! empty($Data['ProductId']) && ! empty($Data['Date']) && ! empty($Data['Skuid'])) {
            $Url = http_build_query($Data);
            $json_result = array(
                'ResultCode' => 200,
                'Url' => '/group/order/?' . $Url
            );
        } else {
            $json_result = array(
                'ResultCode' => 100,
                'Message' => '参数不正确'
            );
        }
        echo json_encode($json_result);
    }
    
    // 预定填写地址
    public function Order()
    {
        $TourProductID = intval($_GET['ProductId']);
        $DateStr = $_GET['Date'];
        $DateStr = date('Ymd', strtotime($DateStr));
        $SkuidArr = $_GET['Skuid'];
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineInfo = $TourProductLineModule->GetInfoByWhere(" and TourProductID=$TourProductID and Status=1 and IsClose!=1");
        if ($TourProductLineInfo) {
            // 出发城市
            $TourAreaModule = new TourAreaModule();
            $StartTourAreaInfo = $TourAreaModule->GetInfoByKeyID($TourProductLineInfo['Departure']);
            if ($TourProductLineInfo['Category'] == 12) {
                $EndTourAreaInfo = $TourAreaModule->GetInfoByKeyID($TourProductLineInfo['Destination']);
            }
            $EndDate = date('Y-m-d', strtotime($DateStr) + $TourProductLineInfo['Days'] * 3600 * 24);
            
            // 获取Sku详情
            $TourProductLineSkuModule = new TourProductLineSkuModule();
            $TourProductLineErverdayPriceModule = new TourProductLineErverDayPriceModule();
            $TotalPrice = 0;
            $PeopleNum = 0;
            foreach ($SkuidArr as $key => $val) {
                $TourProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID={$val['sku']}");
                $TourProductLineErverdayPriceInfo = $TourProductLineErverdayPriceModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID={$val['sku']}");
                $SkuidArr[$key]['Price'] = ceil($TourProductLineErverdayPriceInfo['Price']);
                $TotalPrice += $SkuidArr[$key]['Price'];
                if ($TourProductLineInfo['SkuType'] == 1) {
                    $SkuidArr[$key]['AdultNum'] = $TourProductLineSkuInfo['AdultNum'];
                    $SkuidArr[$key]['ChildNum'] = $TourProductLineSkuInfo['ChildrenNum'];
                    $PeopleNum += $TourProductLineSkuInfo['AdultNum'] + $TourProductLineSkuInfo['ChildrenNum'];
                } else {
                    $SkuidArr[$key]['AdultNum'] = $TourProductLineSkuInfo['PeopleNum'];
                    $PeopleNum += $TourProductLineSkuInfo['PeopleNum'];
                }
            }
            
            $Step1 = 1;
            $Title = "订单支付 - 57美国网";
            include template('GroupLineOrder');
        } else {
            alertandback('该产品已经下架');
        }
    }

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
                $Data = array(
                    'Mobile' => $Mobile,
                    'State' => 1,
                    'AddTime' => time()
                );
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
                $json_result = array(
                    'ResultCode' => 100,
                    'Message' => '短信验证码错误',
                    'LogMessage' => '操作失败(短信验证码错误)'
                );
            }
        } else {
            if ($_SESSION['UserID']) {
                $UserID = $_SESSION['UserID'];
            } else {
                $UserInfo = $UserModule->GetUserIDbyMobile($Mobile);
                if (empty($UserInfo)) {
                    // 新手机号码验证码为空的情况下
                    $json_result = array(
                        'ResultCode' => 105,
                        'Message' => '短信验证码不能为空',
                        'LogMessage' => '操作失败(短信验证码错误)'
                    );
                    echo json_encode($json_result);
                    exit();
                } else {
                    $UserID = $UserInfo['UserID'];
                }
            }
            $json_result = $this->Operate($OrderNumber, $UserID, $_POST);
        }
        // 添加订单操作日志
        $OrderLogModule = new TourProductOrderLogModule();
        $LogData = array(
            'OrderNumber' => $OrderNumber,
            'UserID' => $UserID,
            'Remarks' => $json_result['LogMessage'],
            'OldStatus' => 0,
            'NewStatus' => 1,
            'OperateTime' => date("Y-m-d H:i:s", time()),
            'IP' => GetIP(),
            'Type' => 1
        );
        $OrderLogModule->InsertInfo($LogData);
        echo json_encode($json_result);
        exit();
    }

    /**
     * 确认订单_步骤
     */
    private function Operate($OrderNumber, $UserID, $Post)
    {
        // Sku信息
        $ProductSkuID = $Post['ProductSkuID'];
        $DateStr = $Post['Date'];
        $DateStr = date('Ymd', strtotime($DateStr));
        $TourProductID = intval($Post['TourProductID']);
        // 查询价格,库存
        $TourProductLineErverDayPriceModule = new TourProductLineErverDayPriceModule();
        $TourPriceArr = array();
        $Success = true;
        $Money = 0;
        foreach ($ProductSkuID as $val) {
            $TourPriceInfo = $TourProductLineErverDayPriceModule->GetInfoByWhere(" and TourProductID=$TourProductID and ProductSkuID={$val['skuid']} and `Date`=$DateStr and (Inventory>0 or Inventory=-1)");
            if ($TourPriceInfo) {
                $Money += $TourPriceInfo['Price'];
                if (isset($TourPriceArr[$val['skuid']])) {
                    $TourPriceArr[$val['skuid']]['Num'] = $TourPriceArr[$val['skuid']]['Num'] + 1;
                } else {
                    $TourPriceArr[$val['skuid']] = array(
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
                    'SkuInfo' => json_encode($TourProductLineSkuLists,JSON_UNESCAPED_UNICODE),
                    'PriceInfo' => json_encode($TourProductLineSkuPriceLists,JSON_UNESCAPED_UNICODE),
                    'OtherInfo' => json_encode($OtherInfo,JSON_UNESCAPED_UNICODE)
                );
                $TourLineSnapshotID = $SnapshotModule->InsertInfo($SnapshotData);
            } else {
                $TourLineSnapshotID = $SnapshotInfo['TourLineSnapshotID'];
            }
            // 旅客信息
            $Post['Travellers'][0]=$Post['Travellers'][0];
            $Post['Travellers'][0]['Tel'] = trim($Post['TravellersTel']);
            $Post['Travellers'][0]['Weixin'] = trim($Post['TravellersWeixin']);
            /*
            //酒店信息
            if ($Post['HotelName']!='') {
                $Post['Traveller']['hotel']['HotelName'] = $Post['HotelName'];
                $Post['Traveller']['hotel']['HotelAddress'] = $Post['HotelAddress'];
                $Post['Traveller']['hotel']['HotelTel'] = $Post['HotelTel'];
            }
            //航班信息
            if ($Post['FlightJoinCourse']!=''){
                $Post['Traveller']['Flight']['FlightJoinDate'] =  $Post['FlightJoinDate'];
                $Post['Traveller']['Flight']['FlightJoinCourse'] =  $Post['FlightJoinCourse'];
                $Post['Traveller']['Flight']['FlightJoinTime'] =  $Post['FlightJoinTime'];
            }
            if ($Post['FlightDeliverCourse']!=''){
                $Post['Traveller']['Flight']['FlightDeliverDate'] =  $Post['FlightDeliverDate'];
                $Post['Traveller']['Flight']['FlightDeliverCourse'] =  $Post['FlightDeliverCourse'];
                $Post['Traveller']['Flight']['FlightDeliverTime'] =  $Post['FlightDeliverTime'];
            }
             */

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
                'TravelPeopleInfo' =>json_encode($Post['Travellers'])
            );
            $OrderModule = new TourProductOrderModule();
            $OrderInfoModule = new TourProductOrderInfoModule();
            // 开启事务
            global $DB;
            $DB->query("BEGIN"); // 开始事务定义
            foreach ($TourPriceArr as $key => $val) {
                if ($val['Info']['Inventory'] != - 1) {
                    $Inventory = $val['Info']['Inventory'] - $val['Num'];
                    if ($Inventory >= 0) {
                        $SkuResult = $TourProductLineErverDayPriceModule->UpdateInfoByKeyID(array(
                            'Inventory' => $Inventory
                        ), $val['Info']['DayPriceID']);
                        if (! $SkuResult) {
                            $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                            return $json_result = array(
                                'ResultCode' => 104,
                                'Message' => '订单提交失败',
                                'LogMessage' => '操作失败(库存数量更新失败)'
                            );
                        }
                    } else {
                        $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                        return $json_result = array(
                            'ResultCode' => 104,
                            'Message' => '订单提交失败',
                            'LogMessage' => '操作失败(库存数量更新失败,库存已为负数)'
                        );
                    }
                }
            }
            $result = $OrderModule->InsertInfo($OrderData);
            if (! $result) {
                $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                return $json_result = array(
                    'ResultCode' => 101,
                    'Message' => '订单提交失败',
                    'LogMessage' => '操作失败(订单添加失败)'
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
                    if (! $result1) {
                        $OrderInfoFalse = true;
                    }
                }
                if ($OrderInfoFalse) {
                    $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                    return $json_result = array(
                        'ResultCode' => 102,
                        'Message' => '订单提交失败',
                        'LogMessage' => '操作失败(订单详细信息添加失败)'
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
                        'Url' => WEB_TOUR_URL . '/group/' . $OrderNumber . '.html'
                    );
                }
            }
        } else {
            return $json_result = array(
                'ResultCode' => 103,
                'Message' => '订单提交失败,该商品已无库存',
                'LogMessage' => '操作失败(该商品已无库存)'
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
                include template("GroupLineChoicePay");
            } else {
                $UpData['Status'] = 10;
                $UpData['Remarks'] = '超时未支付,订单自动取消';
                if ($OrderModule->UpdateInfoByOrderNumber($UpData, $OrderNumber)) {
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
                        'IP' => GetIP(),
                        'Type' => 1
                    );
                    $OrderLogModule->InsertInfo($LogData);
                }
                alertandgotopage('超时未支付,订单已取消', WEB_TOUR_URL . '/group/local/');
            }
        } else {
            alertandgotopage('订单异常', WEB_TOUR_URL . '/group/local/');
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
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/paytour/groupresult/';
                $Data['NotifyUrl'] = WEB_MEMBER_URL . '/paytour/groupresult/';
                $Data['ProductUrl'] = WEB_TOUR_URL . "/group/{$OrderInfo['TourProductID']}.html";
                $Data['RunTime'] = time();
				$Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/alipay/', $Data);
            } elseif ($Type == 'wxpay') {
                $Data['OrderNo'] = $Order['OrderNumber'];
                $Data['Subject'] = html_entity_decode(_substr($LineInfo['ProductName'], 40), ENT_QUOTES);
                $Data['Money'] = $Order['TotalAmount'];
                if ($LineInfo['ProductPackage']) {
                    $Data['Body'] = html_entity_decode($LineInfo['ProductName'] . '·' . $LineInfo['ProductPackage'], ENT_QUOTES);
                } else {
                    $Data['Body'] = html_entity_decode($LineInfo['ProductName'], ENT_QUOTES);
                }
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/paytour/groupresult/';
                $Data['RunTime'] = time();
				$Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/wxpay/', $Data);
            }
        } else {
            alertandgotopage('不能操作的订单', WEB_MEMBER_URL . '/tourmember/travelorder/');
        }
    }
}
