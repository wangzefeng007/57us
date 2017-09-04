<?php

class Tour
{

    public function __construct()
    {
        $this->SoType = 'tour';
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }

    /**
     * 旅游标签搜索_列表页
     * Author Zf
     */
    public function TourSearchTag()
    {
        $TblTourModule = new TblTourModule();
        $KeywordModule = new TblTourKeywordModule();
        $Offset = 0;
        $Keyword = $_GET['Keyword'];
        $GoPageUrl = '/tour/tags_' . $Keyword;
        $Tour = $KeywordModule->GetInfoByWhere(" and  `Keyword` = '".$Keyword.'\'');
        // 调用数据
        $MysqlWhere = '';
        $MysqlWhere .= ' and  MATCH(`Keywords`) AGAINST (' . $Tour['KeyID'] . ' IN BOOLEAN MODE)'; // 搜索相关标签的数据
        $Rscount = $TblTourModule->GetListsNum($MysqlWhere);
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
            $Data['Data'] = $TblTourModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                if($key == 0){
                    $Description = $value['Description'];
                }
                if (! empty($value['Keywords'])) {
                    $Keywords = $KeywordModule->ToKeywordName($value['Keywords']);
                    $Keywords = explode(',', $Keywords);
                    $Data['Data'][$key]['Keywords'] = $Keywords;
                }
            }
        }
        $Page = new Page($Rscount['Num'], $PageSize, 1);
        $listpage = $Page->showpage();
        // 该标签下的最新文章抽取8条数据。
        $WhereNewest = ' order by AddTime DESC'; // 按照时间排序。
        $TimeArticle = $TblTourModule->GetLists($WhereNewest, $Offset, 8);
        // 该标签下的最热文章抽取8条数据。
        $WhereHeat = ' order by ViewCount DESC'; // 按照阅读量排序。
        $hotarticle = $TblTourModule->GetLists($WhereHeat, $Offset, 8);
        
        // 热门标签调用
        $Keyhot = $KeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        // 酒店、旅游产品广告
        $AdTour = NewsGetAdInfo('tour_list_tour');
        $AdHotel = NewsGetAdInfo('tour_list_hotel');
        $Title = $Keyword . ' - 57美国网';
        $Keywords = $Keyword;
        $Description = $Keyword . ',' . $Description;
        //跟团游推荐
        $TourProductLine = $this->SearchTourLine($Keyword);
        include template('TourSearchTag');
    }

    /**
     * 跟团游相关推荐
     * Author lusb
     */
    public function SearchTourLine($Keyword='')
    {
        $TourProductLineModule = new TourProductLineModule();
        $TourProductImageModule = new TourProductImageModule();
        //获取相关数据八条
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
     * 旅游_专题首页
     * Author Zf
     */
    public function NewsTour()
    {
        // 旅游类别 旅游新发现 分类ID:1047 美食购物 分类ID:1048 当季推荐 分类ID:1049
        $TblTourModule = new TblTourModule();
        $TblTravelsModule = new TblTravelsModule();
        
        // 旅游新发现数据调取
        $LyxfxID = 1047;
        //不需要推荐
        $AllLyxfx = $TblTourModule->GetInfoByWhere(' and  CategoryID = ' . $LyxfxID . ' order by AddTime desc limit 6', true);
        //需要推荐
        $Lyxfx = $TblTourModule->GetInfoByWhere(' and  CategoryID = ' . $LyxfxID . ' and TopicRecommend = 1 order by AddTime desc limit 5', true);
        // 美食购物数据调取
        $MsxhID = 1048;
        //不需要推荐
        $ALLMsxh = $TblTourModule->GetInfoByWhere(' and  CategoryID = ' . $MsxhID . ' and Image!=\'\' order by AddTime desc limit 6', true);
        $Msxh = $TblTourModule->GetInfoByWhere(' and  CategoryID = ' . $MsxhID . ' and Image!=\'\' order by AddTime desc limit 8', true);
        // 当季推荐数据调取
        $DjtjID = 1049;
        $DjtjTopicRecommend = $TblTourModule->GetInfoByWhere(' and  CategoryID = ' . $DjtjID . ' and TopicRecommend =1 order by AddTime desc');
        if($DjtjTopicRecommend){
            $Where = ' and TourID != '.$DjtjTopicRecommend['TourID'];
        }
        $Djtj = $TblTourModule->GetInfoByWhere(' and  CategoryID = ' . $DjtjID .$Where. ' and Image!=\'\' order by AddTime desc limit 4', true);
        foreach ($Djtj as $key => $value) {
            $Djtj[$key]['key'] = $key + 1;
        }
        //不需要推荐当季
        $ALLDjtj = $TblTourModule->GetInfoByWhere(' and  CategoryID = ' . $DjtjID .' and Image!=\'\' order by AddTime desc limit 6', true);
        
        // 美国游记数据调取
        $Mgyj = $TblTravelsModule->GetInfoByWhere(' order by AddTime desc limit 8', true);
        foreach ($Mgyj as $key => $value) {
            $AddTime = explode(' ', $value['AddTime']);
            $Mgyj[$key]['AddTime'] = $AddTime[0];
        }
        // 跟团游产品产品广告
        $AdGtycp = NewsGetAdInfo('index_tour_group');
        // 广告横幅1
        $AdTourOne = NewsGetAdInfo('tour_index_no1');
        $AdTourTwo = NewsGetAdInfo('tour_index_no2');
        $Title = '美国旅游_美国旅游资讯_美国游记_旅游资讯网 - 57美国网';
        $Keywords = '美国旅游资讯,美国游记,旅游资讯网,美国旅游攻略';
        $Description = '57美国网旅游资讯频道，为您提供实时的美国当地热门旅游资讯，帮助您出行前能够快速了解美国当地风情，同时更有经典游记推荐、当季推荐，美食购物等旅游信息！';

        include template ('NewsTour');
    }

    /**
     * 旅游_列表
     * Author Zf
     */
    public function NewsTourList()
    {
        $TblTourCategoryModule = new TblTourCategoryModule();
        $TblTourKeywordModule = new TblTourKeywordModule();
        $TblTourModule = new TblTourModule();
        $Alias = $_GET['Alias'];
        $Info = $TblTourCategoryModule->GetInfoByWhere(' and Alias = \'' . $Alias . '\' ');
        $GoPageUrl = '/tour_' . $Info['Alias'];
        $Title = $Info['SeoTitle'] ? $Info['SeoTitle'] . ' - 57美国网' : $Info['Title'] . ' - 57美国网';
        $Keywords = $Info['SeoKeywords'] ? $Info['SeoKeywords'] : $Info['Title'];
        $Description = $Info['SeoDescription'] ? $Info['SeoDescription'] : '57美国网（57us.com）给您提供' . $Info['Title'] . ',' . $Info['Description'];
        $CategoryID = $Info['CategoryID'];
        // 旅游专题头部文章调用
        $HeadWhere = ' and SetCategoryTop = 1'; // 分类头条(1-推荐)
        $HeadList = $TblTourModule->GetLists($HeadWhere, 0, 3);
        foreach ($HeadList as $key => $value) {
            $HeadList[$key]['key'] = $key;
        }
        // 旅游专题页列表start
        $Offset = 0;
        $MysqlWhere = ' and  CategoryID = ' . $CategoryID . ' order by AddTime DESC'; // 列表推荐SetListRecommend 1047\1048\1049
        
        $Info = $TblTourCategoryModule->GetInfoByKeyID($CategoryID); // 调取类别名称
        $CategoryName = '';
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 11;
        $Rscount = $TblTourModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($pageSize ? $pageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $pageSize);
            $Data ['Page'] = min($page, $Data ['PageCount']);
            $Offset = ($page - 1) * $Data ['PageSize'];
            if ($page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data['Data'] = $TblTourModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data['Data'] as $key=>$value){
                $Keyword =  $TblTourKeywordModule->ToKeywordName($value['Keywords']);
                $Keyword = explode(',', $Keyword);
                $Data['Data'][$key]['Keywords'] = $Keyword;
            }
            $Page = new Page($Rscount ['Num'],$pageSize);
            $listpage = $Page->showpage();
        }
        // 旅游专题页列表end
        
        // 旅游专题页广告调用（热门标签、热门文章）
        $Keyhot = $TblTourKeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();
        //热门文章
        //$WhereTourhot = ' order by ViewCount DESC';
        $Tourhot1 = $TblTourModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$TblTourModule->GetListsNum('')['Num'];
        $Tourhot2 = $TblTourModule->GetLists(' order by AddTime desc',mt_rand(3,$Index-1),7);
        $Tourhot = array_merge($Tourhot1,$Tourhot2);
        //猜你喜欢
        $TourLike = $TblTourModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-1),6);
        unset($Keyword);
        // 酒店、旅游产品广告
        $AdHotel = NewsGetAdInfo('tour_list_hotel');
        $AdTour = NewsGetAdInfo('tour_list_tour');
        include template('NewsTourList');
    }

    /**
     * 旅游_详情页
     * Author Zf
     */
    public function NewsTourDetail()
    {
        $ID = $_GET['ID'];
        $Offset = 0;
        $TblTourCategoryModule = new TblTourCategoryModule();
        $TblTourModule = new TblTourModule();
        $TblTourKeywordModule = new TblTourKeywordModule();
        $TagsModule=new TblTagsModule();
        $ArticleInfo = $TblTourModule->GetInfoByKeyID($ID);
        $ArticleInfo['Content'] = StrReplaceImages($TagsModule->TiHuan($ArticleInfo['Content']), $ArticleInfo['Title']);
        $Info = $TblTourCategoryModule->GetInfoByKeyID($ArticleInfo['CategoryID']);
        $Keyword = $TblTourKeywordModule->ToKeywordName($ArticleInfo['Keywords']);
        $Title = $ArticleInfo['SeoTitle'] ? $ArticleInfo['SeoTitle'] . ' - 57美国网' : $ArticleInfo['Title'] . ' - 57美国网';
        $Keywords = $ArticleInfo['SeoKeywords'] ? $ArticleInfo['SeoKeywords'] : $ArticleInfo['Title'];
        $Description = $ArticleInfo['SeoDescription'] ? $ArticleInfo['SeoDescription'] : '57美国网（57us.com）给您提供' . $ArticleInfo['Title'] . ',' . $ArticleInfo['Description'];
        // 相关阅读通过标签匹配
        $Correlations = '';
        if($Keyword){
            foreach ($Keyword as $key => $val) {
                $Correlations .= $val['Keyword'] . ',';
            }
        }
        $Correlations = substr($Correlations, 0, strlen($Correlations) - 1);
        if ($Correlations!='')
            $CorrelationNews = $TblTourModule->GetCorrelationNews($Correlations);
        if (! count($CorrelationNews) || $CorrelationNews==0) {
            $CorrelationNews = $TblTourModule->GetLists(' and TourID <> '.$ID.' and Image != \'\' and CategoryID = ' . $Info['CategoryID'], 0, 8);
        }
        // 文章内容标签
        if($Keyword){
            $Keyword = explode(',', $Keyword);
            $ArticleInfo['Keywords'] = $Keyword;
            unset($Keyword);
        }
        // 旅游专题页广告调用（热门标签、热门文章、最新文章）
        $Keyhot = $TblTourKeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();
        //热门文章
        //$WhereTourhot = ' order by ViewCount DESC';
        $Tourhot1 = $TblTourModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$TblTourModule->GetListsNum('')['Num'];
        $Tourhot2 = $TblTourModule->GetLists(' order by AddTime desc',mt_rand(3,$Index-1),7);
        $Tourhot = array_merge($Tourhot1,$Tourhot2);
        //猜你喜欢
        $TourLike = $TblTourModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-1),6);
        $WhereTourTime = ' and Image != \'\' order by AddTime DESC';
        $TourTime = $TblTourModule->GetLists($WhereTourTime, $Offset, 8);
        
        // 上一篇下一篇文章
        $PrveSql = ' and CategoryID = ' . $Info['CategoryID'] . ' and TourID > ' . $ID . ' order by TourID asc';
        $prev = $TblTourModule->GetInfoByWhere($PrveSql);
        $NextSql = ' and CategoryID = ' . $Info['CategoryID'] . ' and TourID < ' . $ID . ' order by TourID desc';
        $next = $TblTourModule->GetInfoByWhere($NextSql);
        // 增加阅读量
        $TblTourModule->UpdateViewCount($ID);
        // 酒店、旅游产品广告
        $AdHotel = NewsGetAdInfo('tour_list_hotel');
        $AdTour = NewsGetAdInfo('tour_list_tour');
        include template('NewsTourDetail');
    }
}