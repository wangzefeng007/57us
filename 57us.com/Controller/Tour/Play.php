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
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        if (strstr($STRING, 'daily')) {
            $TagNav = 'daily';
            if ($_GET['K']){
                $Keyword = trim($_GET['K']);
                if ($Keyword != '') {
                    $MysqlWhere = " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
                }
            }
            $MysqlWhere .= ' and Category=9 and IsClose=0 and Status=1';
            $PageSize = 18;
            $Rscount = $TourProductPlayBaseModule->GetListsNum($MysqlWhere);
            if ($Rscount['Num']==0){
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R2 DESC,LowPrice ASC';
                $count=6;
                $Data['Data'] = $this->FirstList($MysqlWhere,$count,6);
            }else{
                $Data['Data'] = $this->FirstList($MysqlWhere,$Rscount['Num']);
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
            if ($Keyword == '') {
                $Title = '美国一日游_美国当地一日游_美国一日游线路_美国一日游行程 - 57美国网';
                $Keywords = '美国一日游,美国当地一日游,美国一日游价格,美国一日游线路,美国一日游行程,美国一日游景点';
                $Description = '57美国网一日游频道，为您提供美国当地一日景点信息,美国一日游线路推荐，让你快速找到美国一日游必去景点、最佳线路。';
            } else {
                $Title = '搜索' . $Keyword . '一日游_' . $Keyword . '行程_旅行团报价- 57美国网';
                $Keywords = $Keyword . '' . $Keyword . '一日游,' . $Keyword . '行程, ' . $Keyword . '旅行团报价';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点，查看最新' . $Keyword . '热门旅游线路及旅行团费用就上57美国网！57美国网只专注美国的旅游平台！';
            }
            include template('PlayDailyLists');
        } elseif (strstr($STRING, 'feature')) {
            $TagNav = 'feature';
            if ($_GET['K']){
                $Keyword = trim($_GET['K']);
                if ($Keyword != '') {
                    $MysqlWhere = " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
                }
            }
            $MysqlWhere .= ' and Category=6 and IsClose=0 and Status=1';
            $PageSize = 18;
            $Rscount = $TourProductPlayBaseModule->GetListsNum($MysqlWhere);
            if ($Rscount['Num']==0){
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R2 DESC,LowPrice ASC';
                $count=6;
                $Data['Data'] = $this->FirstList($MysqlWhere,$count,6);
            }else{
                $Data['Data'] = $this->FirstList($MysqlWhere,$Rscount['Num']);
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
            if ($Keyword == '') {
                $Title = '美国特色_美国特色体验_美国特色体验推荐 - 57美国网';
                $Keywords = '美国特色,美国特色体验,美国特色体验推荐';
                $Description = '57美国网特色体验频道，为您推荐美国当地最具特色的娱乐项目，领略和体验美国的本土风情和特色，不枉走美国一趟。';
            } else {
                $Title = '搜索' . $Keyword . '特色体验_休闲娱乐- 57美国网';
                $Keywords = $Keyword . '特色体验, ' . $Keyword . '休闲娱乐';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点，提供当地休闲娱乐、特色体验等旅游产品在线预订服务，查看最新最热门的' . $Keyword . '旅游产品就上57美国网！57美国网只专注美国的旅游平台！';
            }
            include template('PlayFeatureLists');
        } elseif (strstr($STRING, 'ticket')) {
            $TagNav = 'ticket';
            if ($_GET['K']){
                $Keyword = trim($_GET['K']);
                if ($Keyword != '') {
                    $MysqlWhere = " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
                }
            }
            $MysqlWhere .= ' and Category=8 and IsClose=0 and Status=1';
            $PageSize = 18;
            $Rscount = $TourProductPlayBaseModule->GetListsNum($MysqlWhere);
            if ($Rscount['Num']==0){
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R2 DESC,LowPrice ASC';
                $count=6;
                $Data['Data'] = $this->FirstList($MysqlWhere,$count,6);
            }else{
                $Data['Data'] = $this->FirstList($MysqlWhere,$Rscount['Num']);
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
            if ($Keyword == '') {
                $Title = '美国门票_美国景点门票_美国景区门票_美国城市通票_美国铁路通票_美国通票 - 57美国网';
                $Keywords = '美国门票,美国城市通票,美国铁路通票,美国通票,美国通票价,美国景点,美国景点门票,美国景区门票,美国景点门票预订,美国景点推荐,美国景点介绍';
                $Description = '57美国网门票频道，为您提供美国旅游景点门票、城市及铁路通票的在线预订服务，价格最优最低，出票速度快，免去排队烦恼，让您的美国之旅更加舒心。';
            } else {
                $Title = '搜索' . $Keyword . '门票_' . $Keyword . '门票价格_门票报价- 57美国网';
                $Keywords = $Keyword . '门票, ' . $Keyword . '门票价格, ' . $Keyword . '门票报价';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点门票在线预订服务，查看最新最优惠的' . $Keyword . '门票报价就上57美国网！57美国网只专注美国的旅游平台！';
            }
            include template('PlayTicketLists');
        } elseif (strstr($STRING, 'shuttle')) {
            $TagNav = 'shuttle';
            if ($_GET['K']){
                $Keyword = trim($_GET['K']);
                if ($Keyword != '') {
                    $MysqlWhere = " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
                }
            }
            $MysqlWhere .= ' and Category=21 and IsClose=0 and Status=1';
            $PageSize = 18;
            $Rscount = $TourProductPlayBaseModule->GetListsNum($MysqlWhere);
            if ($Rscount['Num']==0){
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R2 DESC,LowPrice ASC';
                $count=6;
                $Data['Data'] = $this->FirstList($MysqlWhere,$count,6);
            }else{
                $Data['Data'] = $this->FirstList($MysqlWhere,$Rscount['Num']);
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
            if ($Keyword == '') {
                $Title = '接送机 - 57美国网';
                $Keywords = '接送机';
                $Description = '57美国网门票频道，为您提供美国旅游景点门票、城市及铁路通票的在线预订服务，价格最优最低，出票速度快，免去排队烦恼，让您的美国之旅更加舒心。';
            } else {
                $Title = '搜索' . $Keyword . '接送机_' . $Keyword . '门票价格_门票报价- 57美国网';
                $Keywords = $Keyword . '门票, ' . $Keyword . '门票价格, ' . $Keyword . '门票报价';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点门票在线预订服务，查看最新最优惠的' . $Keyword . '门票报价就上57美国网！57美国网只专注美国的旅游平台！';
            }
            include template('PlayShuttleLists');
        } elseif (strstr($STRING, 'wifi')) {
            $TagNav = 'wifi';
            if ($_GET['K']){
                $Keyword = trim($_GET['K']);
                if ($Keyword != '') {
                    $MysqlWhere = " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
                }
            }
            $MysqlWhere .= ' and Category=22 and IsClose=0 and Status=1';
            $PageSize = 18;
            $Rscount = $TourProductPlayBaseModule->GetListsNum($MysqlWhere);
            if ($Rscount['Num']==0){
                $MysqlWhere = ' and IsClose=0 and Status=1 order by R2 DESC,LowPrice ASC';
                $count=6;
                $Data['Data'] = $this->FirstList($MysqlWhere,$count,6);
            }else{
                $Data['Data'] = $this->FirstList($MysqlWhere,$Rscount['Num']);
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
            if ($Keyword == '') {
                $Title = 'wifi - 57美国网';
                $Keywords = 'wifi';
                $Description = 'wifi';
            } else {
                $Title = '搜索' . $Keyword . 'wifi_' . $Keyword . '门票价格_门票报价- 57美国网';
                $Keywords = $Keyword . '门票, ' . $Keyword . '门票价格, ' . $Keyword . '门票报价';
                $Description = '57美国网为您推荐：' . $Keyword . '及周边热门旅游景点门票在线预订服务，查看最新最优惠的' . $Keyword . '门票报价就上57美国网！57美国网只专注美国的旅游平台！';
            }
            include template('PlayWiFiLists');
        } else {
            echo '404';
        }
    }

    /**
     * 当地玩乐条件获取
     *
     * @author bob
     */
    private function GetMysqlWhere($Intention = '')
    {
        if ($Intention == 'Daily') {
            // 一日游
            return $this->GetDaily();
        } elseif ($Intention == 'Feature') {
            // 特色主题
            return $this->GetFeature();
        } elseif ($Intention == 'Ticket') {
            // 票务
            return $this->GetTicket();
        } elseif ($Intention == 'WiFi') {
            // Wifi
            return $this->GetWiFi();
        } elseif ($Intention == 'Shuttle') {
            // 接送机
            return $this->GetShuttle();
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
        $StartDate = trim($_POST['StartDate']); // 出发时间 201608,201609,
        $Theme = trim($_POST['Theme']); // 特色主题
        $Sort = trim($_POST['Sort']); // 排序 默认:Default;价格从高到低：PicerDown;价格从低到高：PicerAsce;销量从高到低：SalesDown;销量从低到高：SalesAsce
        $MysqlWhere .= ' and Category=9';
        if ($StartCity > 0) {
            $MysqlWhere .= ' and City=' . $StartCity;
        }
        if ($StartDate != 'All') {
            $StartDate = substr($StartDate, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Month`) AGAINST (\'' . $StartDate . '\' IN BOOLEAN MODE)';
        }
        if ($Theme != 'All') {
            $Theme = substr($Theme, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Features`) AGAINST (\'' . $Theme . '\' IN BOOLEAN MODE)';
        }
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        if ($Sort == 'Default') {
            $MysqlWhere .= ' order by AddTime DESC';
        } elseif ($Sort == 'PicerDown') {
            $MysqlWhere .= ' order by LowPrice DESC';
        } elseif ($Sort == 'PicerAsce') {
            $MysqlWhere .= ' order by LowPrice ASC';
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
        $StartDate = trim($_POST['StartDate']); // 出发时间 201608,201609,
        $EndCity = intval($_POST['EndCity']); // 目的地
        $Theme = trim($_POST['Theme']); // 特色主题
        $Sort = trim($_POST['Sort']); // 排序 默认:Default;价格从高到低：PicerDown;价格从低到高：PicerAsce;销量从高到低：SalesDown;销量从低到高：SalesAsce
        $MysqlWhere .= ' and Category=6';
        if ($EndCity > 0) {
            $MysqlWhere .= ' and City=' . $EndCity;
        }
        if ($StartDate != 'All') {
            $StartDate = substr($StartDate, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Month`) AGAINST (\'' . $StartDate . '\' IN BOOLEAN MODE)';
        }
        if ($Theme != 'All') {
            $Theme = substr($Theme, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Features`) AGAINST (\'' . $Theme . '\' IN BOOLEAN MODE)';
        }
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        if ($Sort == 'Default') {
            $MysqlWhere .= ' order by AddTime DESC';
        } elseif ($Sort == 'PicerDown') {
            $MysqlWhere .= ' order by LowPrice DESC';
        } elseif ($Sort == 'PicerAsce') {
            $MysqlWhere .= ' order by LowPrice ASC';
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
        $Theme = trim($_POST['Theme']); // 特色主题
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
        if ($Theme != 'All') {
            $Theme = substr($Theme, 0, - 1);
            $MysqlWhere .= ' and MATCH (`Features`) AGAINST (\'' . $Theme . '\' IN BOOLEAN MODE)';
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
            $MysqlWhere .= ' order by LowPrice DESC';
        } elseif ($Sort == 'PicerAsce') {
            $MysqlWhere .= ' order by LowPrice ASC';
        } elseif ($Sort == 'SalesDown') {
            $MysqlWhere .= ' order by Sales DESC';
        } elseif ($Sort == 'SalesAsce') {
            $MysqlWhere .= ' order by Sales ASC';
        }
        return $MysqlWhere;
    }

    /**
     * WiFi条件
     * 
     * @author ZF
     */
    private function GetWiFi()
    {
        $Keyword = trim($_POST['Keyword']); // 搜索关键字
        $Type = trim($_POST['Type']); // 类型
        $EndCity = intval($_POST['EndCity']); // 目的地
        $Sort = trim($_POST['Sort']); // 排序 默认:Default;价格从高到低：PicerDown;价格从低到高：PicerAsce;销量从高到低：SalesDown;销量从低到高：SalesAsce
        $MysqlWhere = '';
        $MysqlWhere .= ' and Category=22';
        if ($EndCity > 0) {
            $MysqlWhere .= ' and City=' . $EndCity;
        }
        if ($Type != 'All') {
            $MysqlWhere .= ' and MATCH (`Features`) AGAINST (\'' . $Type . '\' IN BOOLEAN MODE)';
        }
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        
        if ($Sort == 'Default') {
            $MysqlWhere .= ' order by R2 DESC,UpdateTime DESC';
        } elseif ($Sort == 'PicerDown') {
            $MysqlWhere .= ' order by LowPrice DESC';
        } elseif ($Sort == 'PicerAsce') {
            $MysqlWhere .= ' order by LowPrice ASC';
        } elseif ($Sort == 'SalesDown') {
            $MysqlWhere .= ' order by Sales DESC';
        } elseif ($Sort == 'SalesAsce') {
            $MysqlWhere .= ' order by Sales ASC';
        }
        return $MysqlWhere;
    }

    /**
     * 接送机条件
     * 
     * @author ZF
     */
    private function GetShuttle()
    {
        $Keyword = trim($_POST['Keyword']); // 搜索关键字
        $StartCity = intval($_POST['StartCity']); // 出发地
        $EndCity = intval($_POST['EndCity']); // 目的地
        $Sort = trim($_POST['Sort']); // 排序 默认:Default;价格从高到低：PicerDown;价格从低到高：PicerAsce;销量从高到低：SalesDown;销量从低到高：SalesAsce
        $MysqlWhere = '';
        $MysqlWhere .= ' and Category=21';
        if ($StartCity > 0) {
            $MysqlWhere .= ' and Departure=' . $StartCity;
        }
        if ($EndCity > 0) {
            $MysqlWhere .= ' and City=' . $EndCity;
        }
        if ($Keyword != '') {
            $MysqlWhere .= " and (ProductName like '%$Keyword%' or ProductSimpleName like '%$Keyword%')";
        }
        if ($Sort == 'Default') {
            $MysqlWhere .= ' order by R2 DESC,UpdateTime DESC';
        } elseif ($Sort == 'PicerDown') {
            $MysqlWhere .= ' order by LowPrice DESC';
        } elseif ($Sort == 'PicerAsce') {
            $MysqlWhere .= ' order by LowPrice ASC';
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
        $MysqlWhere = ' and IsClose=0 and Status=1';
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
            foreach ($Lists as $Key => $Value) {
                $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                $Data['Data'][$Key]['TourCostPrice'] = $Value['LowMarketPrice'] ? ceil($Value['LowMarketPrice']) : ceil($Value['LowPrice'] * 1.15);
                // 出发城市
                $TourAreaModule = new TourAreaModule();
                $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
                $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                // 图片
                $TourProductImageModule = new TourProductImageModule();
                $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                $Data['Data'][$Key]['TourImg'] = ImageURLP4 . $TourImagesInfo['ImageUrl'];
                unset($TourImagesInfo);
                $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                $Data['Data'][$Key]['TouDate'] = $Value['Times'] ? $Value['Times'] : '1天';
                $Data['Data'][$Key]['TourRecommend'] = $Value['R3'] ? $Value['R3'] : '0';
                $Data['Data'][$Key]['TourUrl'] = WEB_TOUR_URL . '/play/' . $Value['TourProductID'] . '.html';
                $Data['Data'][$Key]['Sales'] = $Value['Sales'];
            }
            MultiPage($Data, 6);
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 200;
            }
        } else {
            $MysqlWhere = ' and IsClose=0 and Status=1 order by R2 DESC,LowPrice ASC';
            $Lists = $TourProductPlayBaseModule->GetLists($MysqlWhere, '', 6);
            foreach ($Lists as $Key => $Value) {
                $Data['Data'][$Key]['TourPicre'] = intval($Value['LowPrice']);
                $Data['Data'][$Key]['TourCostPrice'] = intval($Value['LowMarketPrice']);
                // 出发城市
                $TourAreaModule = new TourAreaModule();
                $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
                $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                // 图片
                $TourProductImageModule = new TourProductImageModule();
                $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                $Data['Data'][$Key]['TourImg'] = ImageURLP4 . $TourImagesInfo['ImageUrl'];
                unset($TourImagesInfo);
                $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                $Data['Data'][$Key]['TouDate'] = $Value['Times'] ? $Value['Times'] : '1天';
                $Data['Data'][$Key]['TourRecommend'] = $Value['R3'] ? $Value['R3'] : '0';
                $Data['Data'][$Key]['TourUrl'] = WEB_TOUR_URL . '/play/' . $Value['TourProductID'] . '.html';
                $Data['Data'][$Key]['Sales'] = $Value['Sales'];
            }
            if ($Keyword != '') {
                $Data['ResultCode'] = 103;
            } else {
                $Data['ResultCode'] = 101;
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
        // 产品ID
        $TourProductID = intval($_GET['TourProductID']);
        $PlayBaseModule = new TourProductPlayBaseModule();
        $TourPlayInfo = $PlayBaseModule->GetInfoByTourProductID($TourProductID);
        
        if (empty($TourPlayInfo) || $TourPlayInfo['IsClose'] == 1) {
            alertandback('该商品不存在了！');
        }
        //添加浏览记录
        $Type=14;
        MemberService::AddBrowsingHistory($TourProductID,$Type);
        //同步途风API
        if ($TourPlayInfo['SupplierID']==6 && $TourPlayInfo['Status']==1)
        {
            @file_get_contents("http://admin.57us.com/index.php?Module=DoTuFengCacheInfo&Action=UpdatePlayPriceAll&TourProductID=".$TourProductID);
        }
        
        $PlayErverdayPriceModule = new TourProductPlayErverdayPriceModule();
        $TourCatecoryModule = new TourProductCategoryModule();
        $PlaySkuModule = new TourProductPlaySkuModule();
        $PlayDetailedModule = new TourProductPlayDetailedModule();
        $TourProductImageModule = new TourProductImageModule();
        $AreaModule = new TourAreaModule();
        
        // 出发城市
        $City = $AreaModule->GetInfoByKeyID($TourPlayInfo['City'])['CnName'];
        $Departure = $AreaModule->GetInfoByKeyID($TourPlayInfo['City'])['CnName'];
        if ($TourPlayInfo['TagInfo']) {
            $TagInfo = explode(',', $TourPlayInfo['TagInfo']);
        }
        $SqlWhere = ' and TourCategoryID = ' . $TourPlayInfo['Category'];
        // 分类信息
        $CateInfo = $TourCatecoryModule->GetInfoByWhere(' and TourCategoryID = ' . $TourPlayInfo['Category']);
        $TagNav = $CateInfo['Alias'];
        // 当地玩乐Sku
        $PlaySkuInfo = $PlaySkuModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID.' and Status=1', true);
        $ProductSimpleName = '';
        $TourPlayInfo['ProductSimpleName'] = explode("\n", $TourPlayInfo['ProductSimpleName']);
        foreach ($TourPlayInfo['ProductSimpleName'] as $key => $val) {
            $ProductSimpleName .= "<li title='{$val}'><em>●</em>{$val}</li>";
        }
        foreach ($PlaySkuInfo as $key => $val) {
            $PlayErverdayPrices = $PlayErverdayPriceModule->GetInfoByWhere(' and ProductSkuID = ' . $val['ProductSkuID'] . ' order by DayPriceID asc ', true);
            foreach ($PlayErverdayPrices as $k => $v) {
                if (strtotime($v['Date']) > time()) {
                    $PlaySkuInfo[$key]['NPrice'] = $v['Price'];
                    break;
                }
            }
        }
        // 当地玩乐内容信息
        $DetailInfo = $PlayDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
        // 产品图片信息
        $ProductImages = $TourProductImageModule->GetListsByTourProductID($TourProductID);
        // 获取出行日期
        $GoOutDate = $this->GetDate($PlaySkuInfo,$TourPlayInfo);
        // 详细内容信息
        $Description1 = json_decode($DetailInfo['Description'], true);
        foreach ($Description1['DesContent'] as $K => $Val) {
            // 图片处理
            $DoDescription = 0;
            if (strstr($Val, '<p>[显示图片]</p>')) {
                $ProductDetailedInfo = explode('[显示图片]', $Val);
                $DoDescription = 1;
            } elseif (strstr($Val, '[显示图片]')) {
                $ProductDetailedInfo = explode('[显示图片]', $Val);
                $DoDescription = 1;
            }
            if ($DoDescription == 1) {
                foreach ($ProductDetailedInfo as $KeyPic => $ValuePic) {
                    $ProductDetailedInfo['Des'][$KeyPic]['P'] = _GetPicToContent($ValuePic);
                    foreach ($ProductDetailedInfo['Des'][$KeyPic]['P'] as $a => $b) {
                        $ProductDetailedInfo['Des'][$KeyPic]['P'][$a] = $b;
                    }
                    $ProductDetailedInfo['Des'][$KeyPic]['C'] = _DelPicToContent($ValuePic);
                }
                // 重组数据
                $ProductDetailedInfo['Description'] = '';
                foreach ($ProductDetailedInfo['Des'] as $ValueDes) {
                    $ProductDetailedInfo['Description'] .= $ValueDes['C'];
                    if (count($ValueDes['P']) > 0) {
                        $ProductDetailedInfo['Description'] .= '<div class=\'ins_img\'>';
                        foreach ($ValueDes['P'] as $ValuePic) {
                            if (strstr($ValuePic, 'http://')) {
                                $ProductDetailedInfo['Description'] .= '<p><img src="' . $ValuePic . '" alt="' . $TourPlayInfo[ProductName] . '"></p>';
                            } else {
                                $ProductDetailedInfo['Description'] .= '<p><img src="' . ImageURLP6 . $ValuePic . '" alt="' . $TourPlayInfo[ProductName] . '"></p>';
                            }
                        }
                        $ProductDetailedInfo['Description'] .= '</div>';
                    }
                }
                $Description1['DesContent'][$K] = $ProductDetailedInfo['Description'];
            } else {
                $Content = StrReplaceImages($Val);
                $Description1['Pic'][$K] = _GetPicToContent($Content);
                $Description1['DesContent'][$K] = _DelPicToContent($Content);
                if (! empty($Description1['Pic'][$K])) {
                    $PicString = '<div class="ins_img">';
                    foreach ($Description1['Pic'][$K] as $Pk => $PVal) {
                        if (strstr($PVal, 'http://')) {
                            $PicString .= '<p><img src="' . $PVal . '" alt="' . $TourPlayInfo['ProductName'] . '"></p>';
                        } else {
                            $PicString .= '<p><img src="' . ImageURLP6 . $PVal . '" alt="' . $TourPlayInfo['ProductName'] . '"></p>';
                        }
                    }
                    $PicString .= '</div>';
                }
                $Description1['DesContent'][$K] .= $PicString;
                $PicString = '';
            }
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
        // 同城推荐
        $CityWideRecommend = $PlayBaseModule->GetInfoByWhere(' and City = ' . $TourPlayInfo['City'] . ' and Status = 1 and IsClose = 0 limit 8', true);
        $CityWideRecommendNum = count($CityWideRecommend);
        if ($CityWideRecommendNum < 8) {
            $Num = 8 - $CityWideRecommendNum;
            $OtherRecommend = $PlayBaseModule->GetInfoByWhere(' and TourProductID !=' . $TourProductID . ' and Status = 1 and IsClose = 0 order by TourProductPlayID desc limit ' . $Num, true);
            $CityWideRecommend = array_merge($CityWideRecommend, $OtherRecommend);
        }
        foreach ($CityWideRecommend as $key => $val) {
            $CityWideRecommend[$key]['ImageUrl'] = $TourProductImageModule->GetInfoByTourProductID($val['TourProductID'])['ImageUrl'];
        }
        //商品评分
        $TourOrderEvaluateCountModule=new TourOrderEvaluateCountModule();
        $EvaluateCountInfo=$TourOrderEvaluateCountModule->GetInfoByWhere(" and TourProductID=$TourProductID");
        $AllCount=round(($EvaluateCountInfo['ServerFractionAll']+$EvaluateCountInfo['ConvenientFractionAll']+$EvaluateCountInfo['ExperienceFractionAll']+$EvaluateCountInfo['PerformanceFractionAll'])/4/$EvaluateCountInfo['Times'],1);
        //用户评价
        $TourOrderEvaluateModule=new TourOrderEvaluateModule();
        //真是评论用户数
        $CustomerNum=count($TourOrderEvaluateModule->GetInfoByWhere(" and TourProductID=$TourProductID group by UserID",true));
        $Title = $TourPlayInfo['ProductName'] . ' - 57美国网';
        $Keywords = $TourPlayInfo['Keywords'] ? $TourPlayInfo['Keywords'] : $TourPlayInfo['ProductName'];
        $Description = _substr(strip_tags($Description1['DesContent'][0]), 150) . ',了解美国旅游攻略，规划美国旅游行程，预订美国旅游线路，尽在57美国网！';
        include template('PlayDetail');
    }

    /**
     * 当地玩乐详情页，获取出行日期接口
     * 获取根据SkuID获取日期
     */
    public function GetDate($PlaySkuInfo=array(),$TourPlayInfo=array())
    {
        $PlayErverdayPriceModule = new TourProductPlayErverdayPriceModule();
        $Array = array();
        foreach ($PlaySkuInfo as $key => $val) {
            // 当地玩乐每日价格表
            $ErverdayPriceInfo = $PlayErverdayPriceModule->GetInfoByWhere(' and ProductSkuID = ' . $val['ProductSkuID'], true);
            //判断要提前预定的时间
            $StartDate = date('Ymd', strtotime('+'.$TourPlayInfo['AdvanceDays'].' day'));
            $i = 0;
            $j = 0;
            foreach ($ErverdayPriceInfo as $k => $v) {
                $time = strtotime($v['Date']);
                $IsTure = $time - time();
                if ($IsTure > 0) {
                    if ($time < time() + (60 * 60 * 24 * 30 * 6)) {
                        if ($StartDate<=$v['Date'])
                        {
                            $Array[$val['ProductSkuID']][$j]['Date'] = date('Y-m-d', $time);
                            $Array[$val['ProductSkuID']][$j]['Price'] = strval(ceil($v['Price']));
                            $Array[$val['ProductSkuID']][$j]['DayPriceID'] = $v['DayPriceID'];
                            $j ++;
                        }
                        $i ++;
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            }
        }
        foreach ($Array as $key => $val) {
            $Array[$key] = json_encode($val);
        }
        return $Array;
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
        include template('PlayPlaceOrder');
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
                if (empty($UserInfo))
                {
                    //新手机号码验证码为空的情况下
                    $json_result = array(
                        'ResultCode' => 105,
                        'Message' => '短信验证码不能为空',
                        'LogMessage' => '操作失败(短信验证码错误)'
                    );
                    echo json_encode($json_result);
                    exit();
                }else{
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
        // 当日价格ID
        $DayPriceID = intval($Post['DayPriceID']);
        // 获取单价、总价、库存
        $PlayErverdayPriceModule = new TourProductPlayErverDayPriceModule();
        $ErverdayPriceInfo = $PlayErverdayPriceModule->GetInfoByKeyID($DayPriceID);
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
                    'BaseInfo' => addcslashes(json_encode($TourPlayInfo,JSON_UNESCAPED_UNICODE), "'"),
                    'DetailedInfo' => addcslashes(json_encode($TourPlayDetailInfo,JSON_UNESCAPED_UNICODE), "'"),
                    'SkuInfo' => json_encode($PlaySkuInfo,JSON_UNESCAPED_UNICODE),
                    'PriceInfo' => json_encode($ErverdayPriceInfo,JSON_UNESCAPED_UNICODE),
                    'OtherInfo' => json_encode($OtherInfo,JSON_UNESCAPED_UNICODE)
                );
                $TourPlaySnapshotID = $SnapshotModule->InsertInfo($SnapshotData);
            } else {
                $TourPlaySnapshotID = $SnapshotInfo['TourPlaySnapshotID'];
            }
            // 出行人数
            $Number = intval($Post['Number']);
            // 旅客信息
            $Post['Travellers'][0] =$Post['Travellers'][0];
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
                'TravelPeopleInfo' => json_encode($Post['Travellers'])
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
                $SkuResult = $PlayErverdayPriceModule->UpdateSkuInventory($DayPriceID);
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
                        'Url' => WEB_TOUR_URL . '/playorder/' . $OrderNumber . '.html'
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
        $GoToUrl = WEB_TOUR_URL . '/play/' . $OrderInfo['TourProductID'] . '.html';
        if ($Order && $Order['Status'] == 1) {
            if (strtotime($Order['ExpirationTime']) > time()) {
                $SkuModule = new TourProductPlaySkuModule();
                $SkuInfo = $SkuModule->GetInfoByKeyID($OrderInfo['TourProductSkuID']);
                include template('PlayOrderBalance');
            } else {
                $UpData['Status'] = 10;
                $UpData['Remarks'] = '订单超时未支付';
                $Result = $OrderModule->UpdateInfoByKeyID($UpData, $Order['OrderID']);
                if ($Result) {
                    $LogMessage = '操作失败(order状态更新失败)';
                } else {
                    $LogMessage = '操作成功';
                }
                // 添加订单状态更改日志
                $OrderLogModule = new TourProductOrderLogModule();
                if ($_SESSION['UserID'] && ! empty($_SESSION['UserID'])) {
                    $UserID = $_SESSION['UserID'];
                } else {
                    include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberUserModule.php';
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
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/tour/playresult/';
                $Data['NotifyUrl'] = WEB_MEMBER_URL . '/tour/playresult/';
                $Data['ProductUrl'] = WEB_TOUR_URL . "/tour/{$OrderInfo['TourOrderInfoID']}.html";
                $Data['RunTime'] = time();
				$Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/alipay/', $Data);
            } elseif ($Type == 'wxpay') {
                
                $Data['OrderNo'] = $Order['OrderNumber'];
                $Data['Subject'] = html_entity_decode(_substr($ProductInfo['ProductName'], 40), ENT_QUOTES);
                $Data['Money'] = $Order['TotalAmount'];
                $Data['Body'] = html_entity_decode($ProductInfo['ProductName'] . '·' . _StrtrString($PlaySkuInfo['SKUName']), ENT_QUOTES);
                $Data['ReturnUrl'] = WEB_MEMBER_URL . '/tour/playresult/';
                $Data['RunTime'] = time();
				$Data['Sign'] = ToolService::VerifyData($Data);
				echo ToolService::PostForm(WEB_MEMBER_URL . '/pay/wxpay/', $Data);
            }
        } else {
            alertandback('不能操作的订单');
        }
    }
    //第一次列表页面载入
    public function FirstList($MysqlWhere ='',$RscountNum,$PageSize){
        $Page = intval($_GET['p']) < 1 ? 1 : intval($_GET['p']); // 页码 可能是空
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        if ($RscountNum) {
            $Data = array();
            if (!$PageSize)
            $PageSize=18;
            $Data['RecordCount'] = $RscountNum;
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
            foreach ($Lists as $Key => $Value) {
                $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                $Data['Data'][$Key]['TourCostPrice'] = $Value['LowMarketPrice'] ? ceil($Value['LowMarketPrice']) : ceil($Value['LowPrice'] * 1.15);
                // 出发城市
                $TourAreaModule = new TourAreaModule();
                $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
                $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
                // 图片
                $TourProductImageModule = new TourProductImageModule();
                $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                $Data['Data'][$Key]['TourImg'] = ImageURLP4 . $TourImagesInfo['ImageUrl'];
                unset($TourImagesInfo);
                $Data['Data'][$Key]['Tour_name'] = $Value['ProductName'];
                $Data['Data'][$Key]['TourID'] = $Value['TourProductID'];
                $Data['Data'][$Key]['TouDate'] = $Value['Times'] ? $Value['Times'] : '1天';
                $Data['Data'][$Key]['TourRecommend'] = $Value['R3'] ? $Value['R3'] : '0';
                $Data['Data'][$Key]['TourUrl'] = WEB_TOUR_URL . '/play/' . $Value['TourProductID'] . '.html';
            }
        }
        return $Data['Data'];
    }
   
}
