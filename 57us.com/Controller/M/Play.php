<?php

/**
 * @desc  当地玩乐
 * Class Play
 */
class Play
{

    public function __construct()
    {
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }
    // 当地玩乐首页
    public function Index()
    {
        $PTourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductImageModule = new TourProductImageModule();
        $TourCategoryModule = new TourProductCategoryModule();
        // 特价产品
        $R3SqlWhere = ' and IsClose = 0 and `Status` = 1 and R3 = 1 order by S3 DESC limit 2';
        $ListsR3 = $PTourProductPlayBaseModule->GetLists($R3SqlWhere, 0, 6);
        foreach ($ListsR3 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR3[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR3[$key]['TagInfo'] = explode(',', $value['TagInfo']);
            $ListsR3[$key]['LowPrice'] = intval($value['LowPrice']);
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($value['Category']);
            $ListsR3[$key]['CnName'] = $CategoryName['CnName'];
        }
        
        // 热门产品
        $R4SqlWhere = ' and IsClose = 0 and `Status` = 1 and R4 = 1 order by S4 DESC limit 5';
        $ListsR4 = $PTourProductPlayBaseModule->GetLists($R4SqlWhere, 0, 6);
        foreach ($ListsR4 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR4[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR4[$key]['TagInfo'] = explode(',', $value['TagInfo']);
            $ListsR4[$key]['LowPrice'] = intval($value['LowPrice']);
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($value['Category']);
            $ListsR4[$key]['CnName'] = $CategoryName['CnName'];
        }
        // 广告信息获取
        $WapTourIndexBaner = NewsGetAdInfo('m_tour_play_banner');
        $WapTourIndexTJ = NewsGetAdInfo('m_tour_play_bottom_banner');
        
        $Title = '美国当地玩乐_美国休闲娱乐_美国休闲娱乐推荐 - 57美国网';
        $Keywords = '美国当地玩乐,美国休闲娱乐,美国休闲娱乐推荐';
        $Description = '57美国网当地玩乐频道，为您推荐美国丰富多彩的休闲娱乐项目，这里只有最好玩最精彩的当地玩乐体验，唯有体验后才会不虚此行。';
        
        include template('TourPlayIndex');
    }

    /**
     * 当地玩乐列表
     *
     * @author bob
     */
    public function Lists()
    {
        if ($_POST) {
            $this->GetLists();
        }
        
        // 搜索用
        $Keyword = trim($_GET['K']);
        if ($Keyword != '') {
            $SoWhere = '?K=' . $Keyword;
        }
        $STRING = $_SERVER['QUERY_STRING'];
        if (strstr($STRING, 'daily')) {
            $TagNav = 'daily';
            if ($Keyword == '') {
                $Title = '美国一日游_美国当地一日游_美国一日游线路_美国一日游行程 - 57美国网';
                $Keywords = '美国一日游,美国当地一日游,美国一日游价格,美国一日游线路,美国一日游行程,美国一日游景点';
                $Description = '57美国网一日游频道，为您提供美国当地一日景点信息,美国一日游线路推荐，让你快速找到美国一日游必去景点、最佳线路。';
            } else {
                $Title = '搜索' . $Keyword . '一日游_' . $Keyword . '行程_旅行团报价- 57美国网';
                $Keywords = $Keyword . '' . $Keyword . '一日游,' . $Keyword . '行程, ' . $Keyword . '旅行团报价';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点，查看最新' . $Keyword . '热门旅游线路及旅行团费用就上57美国网！57美国网只专注美国的旅游平台！';
            }
            include template('TourPlayDaily');
        } elseif (strstr($STRING, 'feature')) {
            $TagNav = 'feature';
            if ($Keyword == '') {
                $Title = '美国特色_美国特色体验_美国特色体验推荐 - 57美国网';
                $Keywords = '美国特色,美国特色体验,美国特色体验推荐';
                $Description = '57美国网特色体验频道，为您推荐美国当地最具特色的娱乐项目，领略和体验美国的本土风情和特色，不枉走美国一趟。';
            } else {
                $Title = '搜索' . $Keyword . '特色体验_休闲娱乐- 57美国网';
                $Keywords = $Keyword . '特色体验, ' . $Keyword . '休闲娱乐';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点，提供当地休闲娱乐、特色体验等旅游产品在线预订服务，查看最新最热门的' . $Keyword . '旅游产品就上57美国网！57美国网只专注美国的旅游平台！';
            }
            include template('TourPlayFeature');
        } elseif (strstr($STRING, 'ticket')) {
            $TagNav = 'ticket';
            if ($Keyword == '') {
                $Title = '美国门票_美国景点门票_美国景区门票_美国城市通票_美国铁路通票_美国通票 - 57美国网';
                $Keywords = '美国门票,美国城市通票,美国铁路通票,美国通票,美国通票价,美国景点,美国景点门票,美国景区门票,美国景点门票预订,美国景点推荐,美国景点介绍';
                $Description = '57美国网门票频道，为您提供美国旅游景点门票、城市及铁路通票的在线预订服务，价格最优最低，出票速度快，免去排队烦恼，让您的美国之旅更加舒心。';
            } else {
                $Title = '搜索' . $Keyword . '门票_' . $Keyword . '门票价格_门票报价- 57美国网';
                $Keywords = $Keyword . '门票, ' . $Keyword . '门票价格, ' . $Keyword . '门票报价';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点门票在线预订服务，查看最新最优惠的' . $Keyword . '门票报价就上57美国网！57美国网只专注美国的旅游平台！';
            }
            include template('TourPlayTicket');
        } else {
            echo '404';
        }
        exit();
    }

    /**
     * 当地玩乐条件获取
     *
     * @author bob
     */
    private function GetMysqlWhere($Intention = '')
    {
        if ($Intention == 'PlayDaily') {
            // 一日游
            return $this->GetDaily();
        } elseif ($Intention == 'PlayFeature') {
            // 特色主题
            return $this->GetFeature();
        } elseif ($Intention == 'PlayTicket') {
            // 票务
            return $this->GetTicket();
        } elseif ($Intention == 'DetailsPic') {
            // 详情页图片
            return $this->DetailsPic();
        } else {
            return '';
        }
    }

    /**
     * 一日游列表条件
     *
     * @author bob
     */
    private function GetDaily()
    {
        $Keyword = trim($_POST['Keyword']); // 搜索关键字
        $StartCity = intval($_POST['StartCity']); // 出发城市
        $StartDate = $_POST['StartDate']; // 出发时间 201608,201609,
        $Theme = $_POST['Theme']; // 特色主题
        $Sort = trim($_POST['Sort']); // 排序 默认:Default;价格从高到低：PicerDown;价格从低到高：PicerAsce;销量从高到低：SalesDown;销量从低到高：SalesAsce
        $MysqlWhere .= ' and Category=9';
        if ($StartCity > 0) {
            $MysqlWhere .= ' and City=' . $StartCity;
        }
        if ($StartDate[0] != 'All') {
            foreach ($StartDate as $value) {
                $Date .= $value . ',';
            }
            $Date = substr($Date, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Month`) AGAINST (\'' . $Date . '\' IN BOOLEAN MODE)';
        }
        if ($Theme[0] != 'All') {
            foreach ($Theme as $value) {
                $Themes .= $value . ',';
            }
            $Themes = substr($Themes, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Features`) AGAINST (\'' . $Themes . '\' IN BOOLEAN MODE)';
        }
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        if ($Sort == 'Default') {
            $MysqlWhere .= ' order by AddTime DESC';
        } elseif ($Sort == 'PicerDown') {
            $MysqlWhere .= ' order by LowPrice ASC';
        } elseif ($Sort == 'PicerAsce') {
            $MysqlWhere .= ' order by LowPrice DESC';
        } elseif ($Sort == 'SalesDown') {
            $MysqlWhere .= ' order by Sales DESC';
        } elseif ($Sort == 'SalesAsce') {
            $MysqlWhere .= ' order by Sales ASC';
        }
        return $MysqlWhere;
    }

    /**
     * 特色体验条件
     *
     * @author bob
     */
    private function GetFeature()
    {
        $Keyword = trim($_POST['Keyword']); // 搜索关键字
        $StartDate = $_POST['StartDate']; // 出发时间 201608,201609,
        $EndCity = intval($_POST['EndCity']); // 目的地
        $Theme = $_POST['Theme']; // 特色主题
        $Sort = trim($_POST['Sort']); // 排序 默认:Default;价格从高到低：PicerDown;价格从低到高：PicerAsce;销量从高到低：SalesDown;销量从低到高：SalesAsce
        $MysqlWhere .= ' and Category=6';
        if ($EndCity > 0) {
            $MysqlWhere .= ' and City=' . $EndCity;
        }
        if ($StartDate[0] != 'All') {
            foreach ($StartDate as $value) {
                $Date .= $value . ',';
            }
            $Date = substr($Date, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Month`) AGAINST (\'' . $Date . '\' IN BOOLEAN MODE)';
        }
        if ($Theme[0] != 'All') {
            foreach ($Theme as $value) {
                $Themes .= $value . ',';
            }
            $Themes = substr($Themes, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Features`) AGAINST (\'' . $Themes . '\' IN BOOLEAN MODE)';
        }
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        if ($Sort == 'Default') {
            $MysqlWhere .= ' order by AddTime DESC';
        } elseif ($Sort == 'PicerDown') {
            $MysqlWhere .= ' order by LowPrice ASC';
        } elseif ($Sort == 'PicerAsce') {
            $MysqlWhere .= ' order by LowPrice DESC';
        } elseif ($Sort == 'SalesDown') {
            $MysqlWhere .= ' order by Sales DESC';
        } elseif ($Sort == 'SalesAsce') {
            $MysqlWhere .= ' order by Sales ASC';
        }
        return $MysqlWhere;
    }

    /**
     * 票务条件
     *
     * @author bob
     */
    private function GetTicket()
    {
        $Keyword = trim($_POST['Keyword']); // 搜索关键字
        $TicketType = trim($_POST['TicketType']); // 类型
        $EndCity = intval($_POST['EndCity']); // 目的地
        $Theme = $_POST['Theme']; // 特色主题
        $TicketPrice = trim($_POST['TicketPrice']);
        $Sort = trim($_POST['Sort']); // 排序 默认:Default;价格从高到低：PicerDown;价格从低到高：PicerAsce;销量从高到低：SalesDown;销量从低到高：SalesAsce
        $MysqlWhere .= ' and Category=8';
        if ($TicketType == '景区门票') {
            $MysqlWhere .= ' and Category=8';
        }
        if ($TicketType == '城市通票') {
            $MysqlWhere .= ' and Category=7';
        }
        if ($EndCity > 0) {
            $MysqlWhere .= ' and City=' . $EndCity;
        }
        if ($Theme[0] != 'All') {
            foreach ($Theme as $value) {
                $Themes .= $value . ',';
            }
            $Themes = substr($Themes, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Features`) AGAINST (\'' . $Themes . '\' IN BOOLEAN MODE)';
        }
        if ($TicketPrice != 'All') {
            $TicketPriceArray = explode('-', $TicketPrice);
            // echo "<pre>";print_r($TicketPriceArray);exit;
            if (strstr($TicketPrice, 'All')) {
                if ($TicketPriceArray[0] == 'All') {
                    $MysqlWhere .= ' and LowPrice < ' . $TicketPriceArray[1];
                } elseif ($TicketPriceArray[1] == 'All') {
                    $MysqlWhere .= ' and LowPrice > ' . $TicketPriceArray[0];
                }
            } else {
                $MysqlWhere .= ' and LowPrice > ' . $TicketPriceArray[0] . ' and LowPrice < ' . $TicketPriceArray[1] . '';
            }
        }
        
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        
        if ($Sort == 'Default') {
            $MysqlWhere .= ' order by R2 DESC,UpdateTime DESC';
        } elseif ($Sort == 'PicerDown') {
            $MysqlWhere .= ' order by LowPrice ASC';
        } elseif ($Sort == 'PicerAsce') {
            $MysqlWhere .= ' order by LowPrice DESC';
        } elseif ($Sort == 'SalesDown') {
            $MysqlWhere .= ' order by Sales DESC';
        } elseif ($Sort == 'SalesAsce') {
            $MysqlWhere .= ' order by Sales ASC';
        }
        return $MysqlWhere;
    }

    /**
     * 列表接口
     *
     * @abstract bob
     *           @URL tour.57us.cn/play/getlists/
     */
    public function GetLists()
    {
        if (! $_POST) {
            $Data['ResultCode'] = 100;
            EchoResult($Data);
        }
        $Keyword = trim($_POST['Keyword']);
        $MysqlWhere = ' and IsClose!=1 and `Status` = 1';
        $Intention = trim($_POST['Intention']);
        $MysqlWhere .= $this->GetMysqlWhere($Intention);
        $Page = intval($_POST['Page']) < 1 ? 1 : intval($_POST['Page']); // 页码 可能是空
        $PageSize = 18;
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $Rscount = $TourProductPlayBaseModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            if ($Data['Page'] < $Data['PageCount']) {
                $Data['NextPage'] = $Data['Page'] + 1;
            }
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount']) {
                $Page = $Data['PageCount'];
            }
            
            $Lists = $TourProductPlayBaseModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            if (!empty($Lists))
            {
                foreach ($Lists as $Key => $Value) {
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
                    $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'] ? $TourAreaInfo['CnName'] : '';
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = ImageURLP2 . $TourImagesInfo['ImageUrl'];
                    unset($TourImagesInfo);
                    $Data['Data'][$Key]['TourName'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourId'] = $Value['TourProductID'];
                    $Data['Data'][$Key]['TourStroke'] = $Value['Times'] ? $Value['Times'] : '1天';
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourTag'] = $TagHtml;
                    $Data['Data'][$Key]['TourUrl'] = WEB_M_URL . '/play/' . $Value['TourProductID'] . '.html';
                }
                MultiPage($Data, 6);
            }else{
                $Data['Data'] = array();
                unset($Data['PageCount'],$Data['PageNums'],$Data['PageSize']);
            }
            
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 200;
            }
        } else {
            if ($Keyword != '') {
                $Data['ResultCode'] = 103;
            } else {
                $Data['ResultCode'] = 101;
            }
            // $Data['Message'] = '没有找到相关产品';
            $MysqlWhere = ' and IsClose=0 and `Status` = 1 order by R3 DESC,LowPrice ASC';
            $Lists = $TourProductPlayBaseModule->GetLists($MysqlWhere, 0, $PageSize);
            $Data['Data'] = array();
            foreach ($Lists as $Key => $Value) {
                $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                // 出发城市
                $TourAreaModule = new TourAreaModule();
                $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
                $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'] ? $TourAreaInfo['CnName'] : '';
                // 图片
                $TourProductImageModule = new TourProductImageModule();
                $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                $Data['Data'][$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP2 . $TourImagesInfo['ImageUrl'] : '';
                $Data['Data'][$Key]['TourName'] = $Value['ProductName'];
                $Data['Data'][$Key]['TourId'] = $Value['TourProductID'];
                $Data['Data'][$Key]['TourStroke'] = $Value['Times'] ? $Value['Times'] : '1天';
                $TagArr = explode(',', $Value['TagInfo']);
                $TagHtml = '';
                if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                    foreach ($TagArr as $list) {
                        $TagHtml .= "<span>$list</span>";
                    }
                }
                $Data['Data'][$Key]['TourTag'] = $TagHtml;
                $Data['Data'][$Key]['TourUrl'] = WEB_M_URL . '/play/' . $Value['TourProductID'] . '.html';
            }
        }
        unset($Lists);
        EchoResult($Data);
    }

    /**
     * 当地玩乐详情页
     */
    public function Details()
    {
        if ($_POST['Intention'] == 'DetailsPic') {
            $this->DetailsPic();
        } elseif ($_POST['Intention'] == 'DetailsAndPic') {
            $this->GetDetailsAndPic();
        }
        // 产品ID
        $TourProductID = intval($_GET['TourProductID']);
        $PlayBaseModule = new TourProductPlayBaseModule();
        $TourPlayInfo = $PlayBaseModule->GetInfoByTourProductID($TourProductID);
        // 不是搜索引擎在判断
        if (empty($TourPlayInfo) || $TourPlayInfo['IsClose'] == 1) {
            alertandback('该商品不存在了！');
        }
        
        $TourCatecoryModule = new TourProductCategoryModule();
        $PlayDetailedModule = new TourProductPlayDetailedModule();
        $TourProductImageModule = new TourProductImageModule();
        $AreaModule = new TourAreaModule();
        // 出发城市
        $City = $AreaModule->GetInfoByKeyID($TourPlayInfo['City'])['CnName'];
        if ($TourPlayInfo['TagInfo']) {
            $TagInfo = explode(',', $TourPlayInfo['TagInfo']);
        }
        $SqlWhere = ' and TourCategoryID = ' . $TourPlayInfo['Category'];
        // 分类信息
        $CateInfo = $TourCatecoryModule->GetInfoByWhere(' and TourCategoryID = ' . $TourPlayInfo['Category']);
        $TagNav = $CateInfo['Alias'];
        $ProductSimpleName = '';
        $TourPlayInfo['ProductSimpleName'] = explode("\n", $TourPlayInfo['ProductSimpleName']);
        foreach ($TourPlayInfo['ProductSimpleName'] as $key => $val) {
            $ProductSimpleName .= "<li title='{$val}'>{$val}</li>";
        }
        // 当地玩乐内容信息
        $DetailInfo = $PlayDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
        // 产品图片信息
        $ProductImages = $TourProductImageModule->GetListsByTourProductID($TourProductID);
        // 详细内容信息
        $Description1 = json_decode($DetailInfo['Description'], true);
        if (strstr($DetailInfo['Description'],"<img"))
        {
            $PicI = 1;
        }
        else 
        {
            $PicI = 0;
        }
        foreach ($Description1['DesContent'] as $K => $Val) {
            $Content = StrReplaceImages($Val);
            $Content = explode('[显示图片]', $Content);
            $ContentNew = '';
            foreach ($Content as $VV)
            {
                $ContentNew .= $VV;
            }
            $Description1['DesContent'][$K] = _DelPicToContent($ContentNew);
        }
        $TimeInfo = json_decode($DetailInfo['TimeInfo'], true);
        foreach ($TimeInfo['TimesContent'] as $K => $Val) {
            $TimeInfo['TimesContent'][$K] = $TimeInfo['TimesContent'][$K];
        }
        $BookingPolicy = json_decode($DetailInfo['BookingPolicy'], true);
        foreach ($BookingPolicy['BookContent'] as $K => $Val) {
            $BookingPolicy['BookContentPic'][$K] = StrReplaceImages($Val);
        }
        $ConsumerNotice = json_decode($DetailInfo['ConsumerNotice'], true);
        foreach ($ConsumerNotice['ConContent'] as $K => $Val) {
            $ConsumerNotice['ConContentPic'][$K] = StrReplaceImages($Val);
        }
        $Explanation = json_decode($DetailInfo['Explanation'], true);
        foreach ($Explanation['ExpContent'] as $K => $Val) {
            $Explanation['ExpContentPic'][$K] = StrReplaceImages($Val);
        }
        $Title = $TourPlayInfo['ProductName'] . ' - 57美国网';
        $Keywords = $TourPlayInfo['Keywords'] ? $TourPlayInfo['Keywords'] : $TourPlayInfo['ProductName'];
        $Description = _substr(strip_tags($Description1['DesContent'][0]), 150) . ',了解美国旅游攻略，规划美国旅游行程，预订美国旅游线路，尽在57美国网！';
        include template('TourPlayDetails');
    }
    // 详情页图片
    private function DetailsPic()
    {
        $TourProductImageModule = new TourProductImageModule();
        if ($_POST) {
            $TourProductID = intval($_POST['ID']);
            $ImageList = $TourProductImageModule->GetListsByTourProductID($TourProductID);
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
    }
    // 详情页文本
    private function GetDetailsAndPic()
    {
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductPlayDetailedModule = new TourProductPlayDetailedModule();
        $PlayBaseModule = new TourProductPlayBaseModule();
        $DetailInfo = $TourProductPlayDetailedModule->GetInfoByTourProductID($TourProductID);
        $TourPlayInfo = $PlayBaseModule->GetInfoByTourProductID($TourProductID);
        if (empty($DetailInfo)) {
            $Data['ResultCode'] = 101;
            $Data['Content'] = '';
            $Data['Message'] = '产品不存在';
        } else {
            $NewContent = json_decode($DetailInfo['Description'], true);
            foreach ($NewContent['DesContent'] as $K => $Val) {
                $MyVal = StrReplaceImages($Val);
                $ContentData = explode('[显示图片]', $MyVal);
                $ContentNew = '<article>'.$NewContent['DesTitle'][$K];
                foreach ($ContentData as $Ks=>$Vs)
                {
                    $Vs = StrReplaceImages($Vs,'','P2');
                    $PicArray = _GetPicToContent($Vs);
                    $ContentNew .= _DelPicToContent($Vs);
                    if (!empty($PicArray))
                    {
                        $ContentNew .= '<div class="ins_img">';
                        foreach ($PicArray as $PV)
                        {
                            $ContentNew .= '<p><img src="' . $PV . '"></p>';
                        }
                        $ContentNew .= '</div>';
                    }
                }
            }
            $ContentNew = $ContentNew.'</article>';
            $Data['ResultCode'] = 200;
            $Data['Content'] = $ContentNew;
            $Data['Message'] = '';
        }
        echo json_encode($Data);
        exit();
    }

    /*
     * 当地玩乐预定选择套餐
     */
    public function ChoseCombo()
    {
        $PlaySkuModule = new TourProductPlaySkuModule();
        $PlayErverdayPriceModule = new TourProductPlayErverdayPriceModule();
        $TourProductID = $_GET['ID'];
        // 当地玩乐Sku
        $PlaySkuInfo = $PlaySkuModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID, true);
        foreach ($PlaySkuInfo as $key => $val) {
            $PlayErverdayPrices = $PlayErverdayPriceModule->GetInfoByWhere(' and ProductSkuID = ' . $val['ProductSkuID'] . ' order by DayPriceID asc ', true);
            foreach ($PlayErverdayPrices as $k => $v) {
                if (strtotime($v['Date']) > time()) {
                    $PlaySkuInfo[$key]['NPrice'] = $v['Price'];
                    $PlaySkuInfo[$key]['Date'] = $v['Date'];
                    break;
                }
            }
        }
        include template('TourPlayChoseCombo');
    }

    /**
     * 当地玩乐下单
     */
    public function PlayPlaceOrder()
    {
        $Step1 = 1;
        $TourProductID = intval($_GET['TourProductID']);
        $ProductSkuID = intval($_GET['ProductSkuID']);
        $DayPriceID = intval($_GET['DayPriceID']);
        $Number = intval($_GET['Number']);
        
        $PlayBaseModule = new TourProductPlayBaseModule();
        $TourCatecoryModule = new TourProductCategoryModule();
        $PlaySkuModule = new TourProductPlaySkuModule();
        $PlayErverdayPriceModule = new TourProductPlayErverdayPriceModule();
        $TourProductImageModule = new TourProductImageModule();
        $PlayDetailedModule = new TourProductPlayDetailedModule();
        
        // 产品基本信息
        $TourPlayInfo = $PlayBaseModule->GetInfoByTourProductID($TourProductID);
        // 查询城市信息
        $AreaModule = new TourAreaModule();
        $City = $AreaModule->GetInfoByKeyID($TourPlayInfo['City'])['CnName'];
        
        // 产品详细信息
        $JsonDetailedInfo = $PlayDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
        // 分类信息
        $TourPlayCate = $TourCatecoryModule->GetInfoByKeyID($TourPlayInfo['Category']);
        // 产品SKU信息
        $PlaySkuInfo = $PlaySkuModule->GetInfoByKeyID($ProductSkuID);
        // 产品价格信息
        $ErverdayPriceInfo = $PlayErverdayPriceModule->GetInfoByKeyID($DayPriceID);
        $GoTime = date('Y-m-d', strtotime($ErverdayPriceInfo['Date']));
        // 产品图片信息
        $ProductImages = $TourProductImageModule->GetInfoByTourProductID($TourProductID);
        $AllPrice = ceil($ErverdayPriceInfo['Price'] * $Number);
        $Title = $TourPlayInfo['ProductName'];
        include template('TourPlayLineOrder');
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
                $UserID = $UserInfo['UserID'];
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
        $TourProductID = intval($Post['TourProductID']);
        $ProductSkuID = intval($Post['ProductSkuID']);
        $Date = date("Ymd", strtotime($Post['Date']));
        // 获取单价、总价、库存
        $PlayErverdayPriceModule = new TourProductPlayErverDayPriceModule();
        $ErverdayPriceInfo = $PlayErverdayPriceModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID . ' and ProductSkuID = ' . $ProductSkuID . ' and Date = ' . $Date);
        if ($ErverdayPriceInfo['Inventory'] != 0) {
            // 产品信息
            $TourProductID = intval($Post['TourProductID']);
            $TourPlayModule = new TourProductPlayBaseModule();
            $TourPlayInfo = $TourPlayModule->GetInfoByTourProductID($TourProductID);
            // 产品详情信息
            $TourPlayDetailModule = new TourProductPlayDetailedModule();
            $TourPlayDetailInfo = $TourPlayDetailModule->GetInfoByWhere(' and TourProductID=' . $TourProductID);
            // Sku信息
            $ProductSkuID = intval($Post['ProductSkuID']);
            $PlaySkuModule = new TourProductPlaySkuModule();
            $PlaySkuInfo = $PlaySkuModule->GetInfoByKeyID($ProductSkuID);
            // 其他信息
            $TourCategoryModule = new TourProductCategoryModule();
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($TourPlayInfo['Category']);
            $AreaModule = new TourAreaModule();
            $CityInfo = $AreaModule->GetInfoByKeyID($TourPlayInfo['City']);
            $SupplierModule = new TourSupplierModule();
            $SupplierInfo = $SupplierModule->GetInfoByKeyID($TourPlayInfo['SupplierID']);
            $OtherInfo = array(
                'CategoryName' => $CategoryName['CnName'],
                'CityCnName' => $CityInfo['CnName'],
                'CityEnName' => $CityInfo['EnName'],
                'SupplierCnName' => $SupplierInfo['CnName'],
                'SupplierEnName' => $SupplierInfo['EnName']
            );
            // 快照信息
            $SnapshotModule = new TourProductPlaySnapshotModule();
            $SnapshotInfo = $SnapshotModule->GetInfoByWhere(' and TourProductID=' . $TourProductID . ' order by AddTime desc');
            if (empty($SnapshotInfo) || strtotime($SnapshotInfo['AddTime']) < strtotime($TourPlayInfo['UpdateTime'])) {
                // 创建产品快照
                $SnapshotData = array(
                    'TourProductID' => $TourProductID,
                    'AddTime' => date("Y-m-d H:i:s", time()),
                    'BaseInfo' => addcslashes(json_encode($TourPlayInfo, JSON_UNESCAPED_UNICODE), "'"),
                    'DetailedInfo' => addcslashes(json_encode($TourPlayDetailInfo, JSON_UNESCAPED_UNICODE), "'"),
                    'SkuInfo' => json_encode($PlaySkuInfo, JSON_UNESCAPED_UNICODE),
                    'PriceInfo' => json_encode($ErverdayPriceInfo, JSON_UNESCAPED_UNICODE),
                    'OtherInfo' => json_encode($OtherInfo, JSON_UNESCAPED_UNICODE)
                );
                $TourPlaySnapshotID = $SnapshotModule->InsertInfo($SnapshotData);
            } else {
                $TourPlaySnapshotID = $SnapshotInfo['TourPlaySnapshotID'];
            }
            // 出行人数
            $Number = intval($Post['Number']);
            // 旅客信息
            $Travellers = $Post['Travellers'];
            // 联系人信息
            $Contacts = $Post['Contacts'];
            $Email = $Post['Email'];
            // 用户下单留言
            $Message = $Post['Message'];
            $OneAmout = $ErverdayPriceInfo['Price'];
            $OrderData = array(
                'OrderNumber' => $OrderNumber,
                'UserID' => $UserID,
                'TotalAmount' => $OneAmout * $Number,
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
            $OrderInfoData = array(
                'OrderNumber' => $OrderNumber,
                'Depart' => date("Y-m-d", strtotime($ErverdayPriceInfo['Date'])),
                'Num' => $Number,
                'UnitPrice' => $OneAmout,
                'Money' => $OneAmout * $Number,
                'TourProductID' => $TourProductID,
                'TourProductSkuID' => $ProductSkuID,
                'TourPlaySnapshotID' => $TourPlaySnapshotID
            );
            $OrderModule = new TourProductOrderModule();
            $OrderInfoModule = new TourProductOrderInfoModule();
            // 开启事务
            global $DB;
            $DB->query("BEGIN"); // 开始事务定义
                                 // 更新库存信息
            if ($ErverdayPriceInfo['Inventory'] > 0) {
                $SkuResult = $PlayErverdayPriceModule->UpdateSkuInventory($ErverdayPriceInfo['DayPriceID']);
                if (! $SkuResult) {
                    $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                    return $json_result = array(
                        'ResultCode' => 104,
                        'Message' => '订单提交失败',
                        'LogMessage' => '操作失败(更新库存失败)'
                    );
                }
            }
            // 添加订单信息
            $result = $OrderModule->InsertInfo($OrderData);
            if (! $result) {
                $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                return $json_result = array(
                    'ResultCode' => 101,
                    'Message' => '订单提交失败',
                    'LogMessage' => '操作失败(订单信息添加失败)'
                );
            } else {
                // 添加订单详细信息
                $result1 = $OrderInfoModule->InsertInfo($OrderInfoData);
                if (! $result1) {
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
                    include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductModule.php';
                    $TourProductModule = new TourProductModule();
                    $TourProduct = $TourProductModule->GetInfoByKeyID($OrderInfoData['TourProductID']);
                    $result = ToolService::SendSMSNotice($OrderData['Tel'], "订单号" . $OrderData['OrderNumber'] . "，" . $OrderData['Contacts'] . "  $times  " . $TourProduct['ProductName'] . "，总价￥" . $OrderInfoData['Money'] . "。人数：" . $OrderInfoData['Num'] . "。我们将于15分钟后自动关闭未付款订单，请登录会员中心查看订单详情或致电400-018-5757。");
                    return $json_result = array(
                        'ResultCode' => 200,
                        'Message' => '订单提交成功',
                        'LogMessage' => '操作成功',
                        'Url' => WEB_M_URL . '/playorder/' . $OrderNumber . '.html'
                    );
                }
            }
        } else {
            return $json_result = array(
                'ResultCode' => 103,
                'Message' => '订单提交失败，该商品已无库存',
                'LogMessage' => '操作失败(该商品没有库存)'
            );
        }
    }

    /**
     * 选择支付页
     */
    public function ChoicePay()
    {
        $Step1 = 1;
        $Step2 = 1;
        $Title = '订单支付';
        $OrderNumber = $_GET['OrderNumber'];
        $NoPay = intval($_GET['NoPay']);
        $OrderModule = new TourProductOrderModule();
        $Order = $OrderModule->GetInfoByOrderNumber($OrderNumber);
        $OrderInfoModule = new TourProductOrderInfoModule();
        $OrderInfo = $OrderInfoModule->GetInfoByOrderNumber($OrderNumber);
        $GoToUrl = WEB_M_URL . '/play/' . $OrderInfo['TourProductID'] . '.html';
        if ($Order && $Order['Status'] == 1) {
            if (strtotime($Order['ExpirationTime']) > time()) {
                $Title = '订单支付';
                $SkuModule = new TourProductPlaySkuModule();
                $SkuInfo = $SkuModule->GetInfoByKeyID($OrderInfo['TourProductSkuID']);
                include template('TourPlayPayOrder');
            } else {
                $UpData['Status'] = 10;
                $UpData['Remarks'] = '订单超时未支付';
                $Result = $OrderModule->UpdateInfoByKeyID($UpData, $Order['OrderID']);
                if ($Result) {
                    $LogMessage = '操作失败(超时状态更新失败)';
                } else {
                    $LogMessage = '超时未支付,订单取消';
                }
                // 添加订单状态更改日志
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
                    'Remarks' => $LogMessage,
                    'Type' => 1
                );
                $LogResult = $OrderLogModule->InsertInfo($LogData);
                // 添加一个操作，还原sku数量
                $ErverdayPriceModule = new TourProductPlayErverDayPriceModule();
                $ErverdayPriceModule->UpdateSkuInventoryBy($OrderInfo['ProductSkuID'], date('Ymd', strtotime($OrderInfo['Depart'])));
                alertandgotopage('订单超时未支付', $GoToUrl);
            }
        } else {
            alertandgotopage('不能操作的订单', $GoToUrl);
        }
    }

    /**
     * 准备支付
     */
    public function Pay()
    {
        $Type = trim($_GET['Type']);
        $OrderNo = trim($_GET['ID']);
        $OrderModule = new TourProductOrderModule();
        $Order = $OrderModule->GetInfoByOrderNumber($OrderNo);
        if ($Order && $Order['Status'] == 1) {
            $OrderInfoModule = new TourProductOrderInfoModule();
            $OrderInfo = $OrderInfoModule->GetInfoByOrderNumber($OrderNo);
            $PlayModule = new TourProductPlayBaseModule();
            $ProductInfo = $PlayModule->GetInfoByTourProductID($OrderInfo['TourProductID']);
            $PlaySkuModule = new TourProductPlaySkuModule();
            $PlaySkuInfo = $PlaySkuModule->GetInfoByKeyID($OrderInfo['TourProductSkuID']);
            if ($Type == 'alipay') {
                $Data['OrderNo'] = $Order['OrderNumber'];
                $Data['Subject'] = html_entity_decode($ProductInfo['ProductName'], ENT_QUOTES);
                $Data['Money'] = $Order['TotalAmount'];
                $Data['Body'] = html_entity_decode($ProductInfo['ProductName'] . '·' . _StrtrString($PlaySkuInfo['SKUName']), ENT_QUOTES);
                $Data['ReturnUrl'] = WEB_MUSER_URL . '/paytour/playresult/';
                $Data['NotifyUrl'] = WEB_MUSER_URL . '/paytour/playresult/';
                $Data['ProductUrl'] = WEB_MUSER_URL . "/tour/{$OrderInfo['TourOrderInfoID']}.html";
                $Data['RunTime'] = time();
				$Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL.'/pay/alipay/',$Data);
            } elseif ($Type == 'wxpay') {
                $Data['OrderNo'] = $Order['OrderNumber'];
                $Data['Subject'] = html_entity_decode(_substr($ProductInfo['ProductName'], 40), ENT_QUOTES);
                $Data['Money'] = $Order['TotalAmount'];
                $Data['Body'] = html_entity_decode($ProductInfo['ProductName'] . '·' . _StrtrString($PlaySkuInfo['SKUName']), ENT_QUOTES);
                $Data['ReturnUrl'] = WEB_MUSER_URL . '/paytour/playresult/';
                $Data['RunTime'] = time();
				$Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL.'/pay/wxpay/',$Data);
            }
        } else {
            alertandback('不能操作的订单');
        }
    }

    /**
     * 当地玩乐详情页，获取出行日期接口
     * 获取根据SkuID获取日期
     */
    public function GetDate()
    {
        $ProductSkuID = intval($_GET['ProductSkuId']);
        if ($ProductSkuID == 0) {
            $json_result['ResultCode'] = 101;
            $json_result['Message'] = '产品不存在';
            echo json_encode($json_result);
            exit();
        }
        $StartDateStr = date('Ymd', time());
        $EndDateStr = date('Ymd', time() + 3600 * 24 * 30 * 6);
        $TourProductPlayErverdayPriceModule = new TourProductPlayErverdayPriceModule();
        $ErverdayPriceLists = $TourProductPlayErverdayPriceModule->GetLists(" and Date>=$StartDateStr and Date<=$EndDateStr and ProductSkuID={$ProductSkuID} and (Inventory>0 or Inventory=-1) group by Date order by Date asc", 0, 200, array(
            'min(Price) as Price',
            'Date','TourProductID'
        ));
        
        
        if ($ErverdayPriceLists) {
            //判断要提前预定的时间
            $PlayBaseModule = new TourProductPlayBaseModule();
            $TourPlayInfo = $PlayBaseModule->GetInfoByTourProductID($ErverdayPriceLists[0]['TourProductID']);
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
}
