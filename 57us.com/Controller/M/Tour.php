<?php

class Tour
{

    public function __construct()
    {
    }

    /**
     * 出游主页面
     */
    public function Index()
    {
        // 跟团游产品推荐（旅游首页跟团游推荐获取）
        $TourProductImageModule = new TourProductImageModule();
        $TourProductLineModule = new TourProductLineModule();
        $TourCategoryModule = new TourProductCategoryModule();
        
        $R2SqlWhere = ' and `Status` = 1 and R2 = 1 order by S2 DESC';
        $ListsR2 = $TourProductLineModule->GetLists($R2SqlWhere, 0, 6);
        foreach ($ListsR2 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR2[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR2[$key]['TagInfo'] = explode(',', $value['TagInfo']);
            $ListsR2[$key]['LowPrice'] = intval($value['LowPrice']);
            $CategoryName = $TourCategoryModule->GetInfoByKeyID($value['Category']);
            $ListsR2[$key]['CnName'] = $CategoryName['CnName'];
        }
        
        // 广告信息获取
        $WapTourIndexBaner = NewsGetAdInfo('m_tour_index_banner');
        $WapTourIndexTJ = NewsGetAdInfo('m_tour_index_list_banner');
        
        $Title = '美国旅游_美国旅行_美国自由行_美国自驾游-57美国旅游服务预订平台';
        $Keywords = '美国旅游,美国旅行,美国旅游攻略,美国自由行,美旅游签证,纽约租车,美国自驾游,美国旅游线路,去美国旅游,美国景点,美国旅游报价, 美国游,美国东部旅游, 美国西部旅游';
        $Description = '57美国网旅游平台，为您提供美国跟团游、自由行、行程定制、景点门票、租车、境外wifi、签证等全方位的美国旅游在线预订服务。了解美国旅游攻略，规划美国旅游行程，预订美国旅游线路，尽在57美国网！';
        
        include template('TourIndex');
    }

    public function Search()
    {
        $BackUrl = $_SERVER['HTTP_REFERER'];
        if (strstr($BackUrl, 'home')) {
            $SoType = 'home';
        } elseif (strstr($BackUrl, 'local')) {
            $SoType = 'local';
        } elseif (strstr($BackUrl, 'feature')) {
            $SoType = 'feature';
        } elseif (strstr($BackUrl, 'daily')) {
            $SoType = 'daily';
        } elseif (strstr($BackUrl, 'ticket')) {
            $SoType = 'ticket';
        } else {
            $SoType = 'local';
        }
        $Title = '美国旅游搜索_搜索美国跟团游,搜索景点门票 - 57美国网';
        $Keywords = '美国旅游搜索,搜索美国跟团游,搜索美国门票,搜索景点门票';
        $Description = '57美国网旅游搜索频道，为您提供搜索检索功能，帮助您快速检索到您所需要的美国跟团游、当地玩乐、景点门票等旅游产品。';
        include template('TourSearch');
    }
}
