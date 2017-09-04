<?php

class Travels
{

    public function __construct()
    {
        $this->SoType = 'travels';
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }

    public function Index()
    {
        $Intention = trim('NewsTravelsList');
        $this->$Intention();
    }

    /**
     * 游记标签搜索_列表页
     * Author Zf
     */
    public function TravelsSearchTag()
    {
        $TblTravelsModule = new TblTravelsModule();
        $TblTravelsKeywordModule = new TblTravelsKeywordModule();
        $Offset = 0;
        $Keyword = $_GET['Keyword'];
        $GoPageUrl = '/travels/tags_' . $Keyword;
        $Tour = $TblTravelsKeywordModule->GetInfoByWhere(" and  `Keyword` = '".$Keyword.'\'');
        // 调用数据
        $MysqlWhere = '';
        $MysqlWhere .= ' and  MATCH(`Keywords`) AGAINST (' . $Tour['KeyID'] . ' IN BOOLEAN MODE) order by TravelsID desc'; // 搜索相关标签的数据
        $Rscount = $TblTravelsModule->GetListsNum($MysqlWhere);
        $DetailURL = '/tour/newstourdetail';
        $page = intval($_GET['page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 10;
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TblTravelsModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                if($key == 0){
                    $Description = $value['Description'];
                }
                if (! empty($value['Keywords'])) {
                    $Keywords = $TblTravelsKeywordModule->ToKeywordName($value['Keywords']);
                    $Keywords = explode(',', $Keywords);
                    $Data['Data'][$key]['Keywords'] = $Keywords;
                }
            }
        }
        $Page = new Page($Rscount['Num'], $PageSize, 1);
        $listpage = $Page->showpage();
        
        // 该标签下的最热门文章抽取8条数据。
        $WhereHeat = ' order by ViewCount DESC'; // 按照阅读量排序。
        $hotarticle = $TblTravelsModule->GetLists($WhereHeat, $Offset, 8);
        
        // 热门标签调用
        $Keyhot = $TblTravelsKeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        
        // 酒店、旅游产品广告
        $AdTour = NewsGetAdInfo('tour_list_tour');
        $AdHotel = NewsGetAdInfo('tour_list_hotel');
        
        //跟团游产品推荐
        $TourProductLine = $this->SearchTourLine($Keyword);

        $Title = $Keyword . ' - 57美国网';
        $Keywords = $Keyword;
        $Description = $Keyword . ',' . $Description;
        include template('TravelsSearchTag');
    }
    
    /**
     * 跟团游相关推荐
     * Author lusb
     */
    public function SearchTourLine($Keyword='')
    {
        $TourProductLineModule = new TourProductLineModule();
        $TourProductImageModule = new TourProductImageModule();

        $TourProductLine = $TourProductLineModule->GetInfoByWhere(" and Status=1 and `ProductName` like '%" . $Keyword . "%' order by `Cent` DESC limit 8", true);
        $Num = count($TourProductLine);
        if ($Num < 8) {
            $Limit = 8 - $Num;
            $TourProductLineTwo = $TourProductLineModule->GetInfoByWhere(" and Status=1 order by Cent DESC limit " . $Limit, true);
        }
        if ($Num == 0) {
            $TourProductList = $TourProductLineTwo;
        } elseif ($Num == 8) {
            $TourProductList = $TourProductLine;
        } else {
            $TourProductList = array_merge($TourProductLine, $TourProductLineTwo);
        }
        if (! empty($TourProductList)) {
            foreach ($TourProductList as $Key => $Value) {
                $TourProductImageInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                $TourProductList[$Key]['ImageUrl'] = $TourProductImageInfo['ImageUrl'];
            }
        }
        return $TourProductList;
    }

    /**
     * 游记_列表页
     */
    public function NewsTravelsList()
    {
        $MyUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/travels/';
        $SoUrl = trim($_GET['SoUrl']) ? trim($_GET['SoUrl']) : '';
        $DoneUrl = TourService::CreateSearchUrl($SoUrl, array(
            'rmmdd',
            'lyyf',
            'yjts'
        )); // 热门目的地，旅游月份，游记天数

        $TravelsModule = new TblTravelsModule();
        $TravelsCateModule = new TblTravelsCategoriesModule();
        $TravelsKeywordModule = new TblTravelsKeywordModule();
        // 旅游分类（旅游地点）
        $Rmmdd = $TravelsCateModule->GetInfoByWhere('', true); // 热门目的地
        $Lyyf = array(
            '1' => '1月',
            '2' => '2月',
            '3' => '3月',
            '4' => '4月',
            '5' => '5月',
            '6' => '6月',
            '7' => '7月',
            '8' => '8月',
            '9' => '9月',
            '10' => '10月',
            '11' => '11月',
            '12' => '12月'
        );
        $Days = array(
            '1' => '3天以内',
            '2' => '3-7天',
            '3' => '7-15天',
            '4' => '15天以上'
        );
        $Page = $DoneUrl['p']['Page'];
        $MysqlWhere = ' and Image != ""';
        $PageSize = 8;
        if ($Page < 1) {
            $Page = 1;
        }
        if ($DoneUrl['z']['rmmdd'] != '') {
            $rmmdd = $DoneUrl['z']['rmmdd'];
            $MysqlWhere .= ' and CategoryID= ' . $rmmdd;
        }
        if ($DoneUrl['z']['lyyf'] != '') {
            $lyyf = $DoneUrl['z']['lyyf'];
            $MysqlWhere .= ' and Months= ' . $lyyf;
        }
        if ($DoneUrl['z']['yjts'] != '') {
            $yjts = $DoneUrl['z']['yjts'];
            if ($yjts == '1') {
                $MysqlWhere .= ' and Days < 3 and Days > 0';
            } elseif ($yjts == '2') {
                $MysqlWhere .= ' and Days < 7 and Days >= 3';
            } elseif ($yjts == '3') {
                $MysqlWhere .= ' and Days < 15 and Days >= 7';
            } else {
                $MysqlWhere .= ' and Days >= 15';
            }
        }
        $MysqlWhere .= ' order by TravelsID desc';
        $Rscount = $TravelsModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TravelsModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
        }
        foreach ($Data['Data'] as $key => $value) {
            $Keywords = $TravelsKeywordModule->ToKeywordName($value['Keywords']);
            $Keywords = explode(',', $Keywords);
            $Data['Data'][$key]['Keywords'] = $Keywords;
            $AddTime = explode(' ', $value['AddTime']);
            $Data['Data'][$key]['AddTime'] = $AddTime[0];
            $Data['Data'][$key]['CategoryName'] = $TravelsCateModule->GetInfoByKeyID($value['CategoryID'])['CategoryName'];
            foreach (json_decode($value['Content'], true) as $k => $val) {
                $Data['Data'][$key]['ContentImage'] = _GetPicToContent($val['Content']);
                if ($Data['Data'][$key]['ContentImage']) {
                    break;
                }
            }
        }
        MultiPage($Data, 7);
        // 头部轮播广告
        $AdHead = NewsGetAdInfo('index_travels');
        // 游记列表页数据调用（热门标签、热门文章）
        $Keyhot = $TravelsKeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();
        //热门文章
        //$WhereTourhot = ' order by ViewCount DESC';
        $Travelshot1 = $TravelsModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$TravelsModule->GetListsNum('')['Num'];
        $Travelshot2 = $TravelsModule->GetLists(' order by AddTime desc',mt_rand(3,$Index-7),7);
        $Travelshot = array_merge($Travelshot1,$Travelshot2);
        //猜你喜欢
        $TourLike = $TravelsModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-6),6);
        foreach ($Travelshot as $key => $value) {
            $Travelshot[$key]['key'] = $key + 1;
            $AddTime = strtotime($value['AddTime']);
            $Travelshot[$key]['AddTime'] = date('Y-m-d', $AddTime);
        }
        
        $tag1 = $TravelsCateModule->GetInfoByKeyID($rmmdd)['CategoryName'];
        $tag1 = $tag1 ? $tag1 : '';
        $tag2 = $Lyyf[$lyyf] ? $Lyyf[$lyyf] : '';
        $tag3 = $Days[$yjts] ? $Days[$yjts] : '';
        if ($tag1=='' && $tag2=='' && $tag3=='')
        {
            $Title = '美国游记攻略_美国热门游记_美国旅游攻略 - 57美国网';
            $Keywords = '美国游记攻略,美国热门游记,美国旅游攻略,美国购物攻略';
            $Description = '57美国网美国游记频道，聚集了' . $tag2 . $tag1 . $tag3 . '游记攻略，由旅行达人与驴友分享美国' . $tag1 . '旅游心得、旅行新玩法、行程线路规划，并推荐必去景点、当地玩乐、特色美食等美国' . $tag1 . '旅游的实用信息。';
            
        }
        else
        {
            $Title = '美国游记_' . $tag2 . $tag1 . $tag3 . '游记攻略_美国热门游记 - 57美国网';
            $Keywords = $tag1 . '游记, ' . $tag2 . $tag1 . '热门游记, ' . $tag1 . $tag3 . '游记攻略,';
            $Description = '57美国网美国游记频道，聚集了' . $tag2 . $tag1 . $tag3 . '游记攻略，由旅行达人与驴友分享美国' . $tag1 . '旅游心得、旅行新玩法、行程线路规划，并推荐必去景点、当地玩乐、特色美食等美国' . $tag1 . '旅游的实用信息。';
        }
        // 酒店、旅游产品广告
        $AdHotel = NewsGetAdInfo('tour_list_hotel');
        $AdTour = NewsGetAdInfo('tour_list_tour');
        include template('NewsTravelsList');
    }

    /**
     * 游记_详情页
     * Author Zf
     */
    public function NewsTravelsDetail()
    {
        $TravelsModule = new TblTravelsModule();
        $TblTravelsKeywordModule = new TblTravelsKeywordModule();
        $TblTravelsCategoriesModule = new TblTravelsCategoriesModule();
        $TagsModule=new TblTagsModule();
        $DetailURL = '/travels/newstravelsdetail'; // 详情页URL
        $ID = $_GET['ID'];
        $list = $TravelsModule->GetInfoByKeyID($ID);
        $list['TripInformation']=json_decode($list['TripInformation'],true);
        $Title = $list['SeoTitle'] ? $list['SeoTitle'] . ' - 57美国网' : $list['Title'] . ' - 57美国网';
        $Keywords = $list['SeoKeywords'] ? $list['SeoKeywords'] : $list['Title'];
        $Description = $list['SeoDescription'] ? $list['SeoDescription'] : '57美国网（57us.com）给您提供' . $list['Title'] . ',' . $list['Description'];
        // 出发地点
        $TravelsCategories = $TblTravelsCategoriesModule->GetInfoByKeyID($list['CategoryID']);
        $list['Departure'] = $TravelsCategories['CategoryName'];
        // 文章标签
        $Keyword = $TblTravelsKeywordModule->ToKeywordName($list['Keywords']);
        // 相关阅读通过标签匹配
        $Correlations = '';
        foreach ($Keyword as $key => $val) {
            $Correlations .= $val['Keyword'] . ',';
        }
        $Correlations = substr($Correlations, 0, strlen($Correlations) - 1);
        $CorrelationNews = $TravelsModule->GetCorrelationNews($Correlations);
        if (! count($CorrelationNews) || $CorrelationNews==0){
            $CorrelationNews = $TravelsModule->GetLists(' and TravelsID <> '.$ID.' and CategoryID = ' . $list['CategoryID'], 0, 8);
        }
        $Keyword = explode(',', $Keyword);
        $list['Keywords'] = $Keyword;
        // 发布时间年月日
        $AddTime = strtotime($list['AddTime']);
        $StartTime = date('Y-m-d', $AddTime);
        // 文章内容
        $list['Content'] = json_decode($list['Content'], true);
        $list['Content']=$TagsModule->TiHuan($list['Content']);
        // 游记数据调用（热门标签）
        $Keyhot = $TblTravelsKeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();
        //热门文章
        //$WhereTourhot = ' order by ViewCount DESC';
        $Travelshot1 = $TravelsModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$TravelsModule->GetListsNum('')['Num'];
        $Travelshot2 = $TravelsModule->GetLists(' order by AddTime desc',mt_rand(3,$Index-7),7);
        $Travelshot = array_merge($Travelshot1,$Travelshot2);
        //猜你喜欢
        $TourLike = $TravelsModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-6),6);
        foreach ($Travelshot as $key => $value) {
            $Travelshot[$key]['key'] = $key + 1;
            $AddTime = strtotime($value['AddTime']);
            $Travelshot[$key]['AddTime'] = date('Y-m-d', $AddTime);
        }
        
        // 增加阅读量
        $TravelsModule->UpdateViewCount($ID);
        // 上一篇下一篇文章
        $PrveSql = ' and CategoryID = ' . $list['CategoryID'] . ' and TravelsID > ' . $ID . ' order by TravelsID asc';
        $prev = $TravelsModule->GetInfoByWhere($PrveSql);
        $NextSql = ' and CategoryID = ' . $list['CategoryID'] . ' and TravelsID < ' . $ID . ' order by TravelsID desc';
        $next = $TravelsModule->GetInfoByWhere($NextSql);
        // 酒店、旅游产品广告
        $AdTour = NewsGetAdInfo('tour_list_tour');
        $AdHotel = NewsGetAdInfo('tour_list_hotel');
        
        $Title = $list['SeoTitle'] ? $list['SeoTitle'] . ' - 57美国网' : $list['Title'] . ' - 57美国网';
        $Keywords = $list['SeoKeywords'] ? $list['SeoKeywords'] : $list['Title'];
        $Description = $list['SeoDescription'] ? $list['SeoDescription'] : '57美国网（57us.com）给您提供' . $list['Title'] . ',' . $list['Description'];
        include template('NewsTravelsDetail');
    }
}
