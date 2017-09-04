<?php

class News
{

    public function __construct()
    {
        //控制模板输出条数
        $this->OnePageSize = 7;
    }

    /**
     * 主站搜索列表页
     * Author Zf
     */
    public function Search()
    {
        $Type = trim($_GET['Type']);
        if ($Type == 'immigrant') {
            // 移民
            $this->SearchImmigrant();
        } elseif ($Type == 'study') {
            // 留学
            $this->SearchStudy();
        } elseif ($Type == 'travels') {
            // 留学
            $this->SearchTravels();
        } elseif ($Type == 'tour') {
            // 旅游
            $this->SearchTour();
        } else {
            $BackUrl = $_SERVER['HTTP_REFERER'];
            if (strstr($BackUrl, 'study')) {
                $SoType = 'study';
            } elseif (strstr($BackUrl, 'immigrant')) {
                $SoType = 'immigrant';
            } elseif (strstr($BackUrl, 'travels')) {
                $SoType = 'travels';
            } else {
                $SoType = 'tour';
            }
            $Title = '美国资讯信息搜索 - 57美国网';
            $Keywords = '信息搜索,美国搜索,美国资讯搜索,美国资讯信息搜索';
            $Description = '57美国网搜索频道，建立绿色通道，为您提供检索功能，帮助快速搜索到您所需的美国热点资讯内容。';
            include template('NewsSearch');
        }
        exit();
    }

    /**
     * 旅游搜索
     */
    public function SearchTour()
    {
        $TblTourModule = new TblTourModule();
        $Type = trim($_GET['Type']);
        $Keyword = $_GET['KeyWord'];
        $TheKeyword = _substr($Keyword, 10);
        $Offset = 0;
        $GoPageUrl = '/search_' . $Type . '_' . $Keyword;
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $Rscount = $TblTourModule->GetListsNum($MysqlWhere);
        $page = intval($_GET['page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 32;
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
        }
        MultiPage ( $Data, 2 );
        $Title = '搜索' . $Keyword . '结果_美国旅游  - 57美国网';
        $Keywords = '美国旅游搜索' . $Keyword . '结果';
        $Description = '美国旅游搜索' . $Keyword . '结果,' . $Data['Data'][0]['Description'];
        include template('NewsTourSearch');
    }

    /**
     * 游学搜索
     */
    public function SearchTravels()
    {
        $TblTravelsModule = new TblTravelsModule();
        $Type = trim($_GET['Type']);
        $Keyword = $_GET['KeyWord'];
        $TheKeyword = _substr($Keyword, 10);
        $Offset = 0;
        $GoPageUrl = '/search_' . $Type . '_' . $Keyword;
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $Rscount = $TblTravelsModule->GetListsNum($MysqlWhere);
        $page = intval($_GET['page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 32;
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
                foreach (json_decode($value['Content'], true) as $k => $val) {
                    $Data['Data'][$key]['ContentImage'] = _GetPicToContent($val['Content']);
                    if ($Data['Data'][$key]['ContentImage']) {
                        break;
                    }
                }
            }
        }
        MultiPage ( $Data, 2 );
        $Title = '搜索' . $Keyword . '结果_美国游记  - 57美国网';
        $Keywords = '美国游记搜索' . $Keyword . '结果';
        $Description = '美国游记搜索' . $Keyword . '结果,' . $Data['Data'][0]['Description'];
        include template('NewsTravelsSearch');
    }

    /**
     * 留学搜索
     */
    public function SearchStudy()
    {
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $Type = trim($_GET['Type']);
        $Keyword = $_GET['KeyWord'];
        $TheKeyword = _substr($Keyword, 10);
        $Offset = 0;
        $GoPageUrl = '/search_' . $Type . '_' . $Keyword;
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $Rscount = $TblStudyAbroadModule->GetListsNum($MysqlWhere);
        $page = intval($_GET['page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 32;
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TblStudyAbroadModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
        }
        MultiPage ( $Data, 2 );
        $Title = '搜索' . $Keyword . '结果_美国留学  - 57美国网';
        $Keywords = '美国留学搜索' . $Keyword . '结果';
        $Description = '美国留学搜索' . $Keyword . '结果,' . $Data['Data'][0]['Description'];
        include template('NewsStudySearch');
    }

    /**
     * 移民搜索
     */
    public function SearchImmigrant()
    {
        $TblImmigrationModule = new TblImmigrationModule();
        $Type = trim($_GET['Type']);
        $Keyword = $_GET['KeyWord'];
        $TheKeyword = _substr($Keyword, 10);
        $Offset = 0;
        $GoPageUrl = '/search_' . $Type . '_' . $Keyword;
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $Rscount = $TblImmigrationModule->GetListsNum($MysqlWhere);
        $page = intval($_GET['page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 32;
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TblImmigrationModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
        }
        MultiPage ( $Data, 2 );
        $Title = '搜索' . $Keyword . '结果_美国移民  - 57美国网';
        $Keywords = '美国移民搜索' . $Keyword . '结果';
        $Description = '美国移民搜索' . $Keyword . '结果,' . $Data['Data'][0]['Description'];
        include template('NewsImmigrantSearch');
    }

    /**
     * 获取搜索条数
     *
     * @param string $Keyword
     * @return mixed
     */
    public function GetNum($Keyword = '')
    {
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $TblTourModule = new TblTourModule();
        $Rscount['Tour'] = $TblTourModule->GetListsNum($MysqlWhere);
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $Rscount['Study'] = $TblStudyAbroadModule->GetListsNum($MysqlWhere);
        $TblImmigrationModule = new TblImmigrationModule();
        $Rscount['Immigration'] = $TblImmigrationModule->GetListsNum($MysqlWhere);
        $TblTravelsModule = new TblTravelsModule();
        $Rscount['Travels'] = $TblTravelsModule->GetListsNum($MysqlWhere);
        return $Rscount;
    }

    /* ##################################### */

    /**
     * 分类
     */
    public function Categories()
    {
        //旅游分类
        $TblTourCategoryModule = new TblTourCategoryModule();
        $TblTourCategoryLists = $TblTourCategoryModule->GetInfoByWhere(' and ParentCategoryID = 0', true);
        //留学分类
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $TblStudyAbroadCategoryLists = $TblStudyAbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 0', true);
        //移民分类
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $TblImmigrationCategoryLists = $TblImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = 0', true);
        $Title = '美国旅游_美国留学_美国移民_美国资讯分类 - 57美国网';
        $Keywords = '美国旅游,美国留学,美国移民,美国资讯分类,资讯类别,资讯类目';
        $Description = '57美国网资讯分类频道，汇集了美国旅游、美国留学、美国移民的目录分类指南，帮助您快速找到所需的信息、解决您的困惑。';
        $MyAction = 'Categories';
        include template('NewsCategories');
    }

    /* ##################################### */
    /**
     * 旅游资讯首页
     */
    public function TourIndex()
    {
        $TblTourModule = new TblTourModule();
        $TourTuijian = $TblTourModule->GetInfoByWhere(' and M2 = 1 order by Sort asc,AddTime desc limit 5', true);
        // 广告信息获取
        $WapTourIndexBaner = NewsGetAdInfo('WapTourIndexBaner');
        $WapTourIndexJingCai = NewsGetAdInfo('WapTourIndexJingCai');
        $Title = '美国旅游_美国旅游资讯_美国游记_旅游资讯网 - 57美国网';
        $Keywords = '美国旅游资讯,美国游记,旅游资讯网,美国旅游攻略';
        $Description = '57美国网旅游资讯频道，为您提供实时的美国当地热门旅游资讯，帮助您出行前能够快速了解美国当地风情，同时更有经典游记推荐、当季推荐，美食购物等旅游信息！';
        include template('NewsTourIndex');
    }

    /**
     * 旅游资讯列表
     */
    public function TourLists()
    {
        $TblTourCategoryModule = new TblTourCategoryModule();
        $TblTourModule = new TblTourModule();

        $Alias = $_GET['Alias'];
        $Info = $TblTourCategoryModule->GetInfoByWhere(' and Alias = \'' . $Alias . '\' ');
        $CategoryID = $Info['CategoryID'];

        // 旅游专题页列表start
        $Offset = 0;
        $MysqlWhere = ' and  CategoryID = ' . $CategoryID . ' order by AddTime DESC';
        $Info = $TblTourCategoryModule->GetInfoByKeyID($CategoryID);
        $CategoryName = '';
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 32;
        $Rscount = $TblTourModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($pageSize ? $pageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $pageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TblTourModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
        }
        MultiPage ( $Data, 2 );
        // 旅游专题页列表end
        $Title = $Info['SeoTitle'] ? $Info['SeoTitle'] . ' - 57美国网' : $Info['Title'] . ' - 57美国网';
        $Keywords = $Info['SeoKeywords'] ? $Info['SeoKeywords'] : $Info['Title'];
        $Description = $Info['SeoDescription'] ? $Info['SeoDescription'] : '57美国网（57us.com）给您提供' . $Info['Title'] . ',' . $Info['Description'];
        include template('NewsTourLists');
    }

    /**
     * 旅游资讯内容
     */
    public function TourDetail()
    {
        $ID = $_GET['ID'];
        $TblTourCategoryModule = new TblTourCategoryModule();
        $TblTourModule = new TblTourModule();
        $TourProductImageModule = new TourProductImageModule();
        $TourInfo = $TblTourModule->GetInfoByKeyID($ID);
        $TourInfo['Content'] = StrReplaceImages($TourInfo['Content'], $TourInfo['Title']);
        $CategoryInfo = $TblTourCategoryModule->GetInfoByKeyID($TourInfo['CategoryID']);
        // 跟团游产品推荐（旅游首页跟团游推荐获取）
        $TourProductLineModule = new TourProductLineModule();
        $R2SqlWhere = 'and R2 = 1 order by S2 DESC';
        $ListsR2 = $TourProductLineModule->GetLists($R2SqlWhere, 0, 2);

        foreach ($ListsR2 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR2[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR2[$key]['TagInfo'] = explode(',', $value['TagInfo']);
        }
        // 增加阅读量
        $TblTourModule->UpdateViewCount($ID);
        $Title = $TourInfo['SeoTitle'] ? $TourInfo['SeoTitle'] . ' - 57美国网' : $TourInfo['Title'] . ' - 57美国网';
        $Keywords = $TourInfo['SeoKeywords'] ? $TourInfo['SeoKeywords'] : $TourInfo['Title'];
        $Description = $TourInfo['SeoDescription'] ? $TourInfo['SeoDescription'] : '57美国网（57us.com）给您提供' . $TourInfo['Title'] . ',' . $TourInfo['Description'];
        include template('NewsTourDetail');
    }

    /* ##################################### */

    /**
     * 游记列表
     */
    public function TravelsLists()
    {
        $TravelsModule = new TblTravelsModule();

        // 旅游专题页列表start
        $Offset = 0;
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 32;
        $MysqlWhere = '';
        $Rscount = $TravelsModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($pageSize ? $pageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $pageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount']) {
                $page = $Data['PageCount'];
            }
            $Data['Data'] = $TravelsModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                foreach (json_decode($value['Content'], true) as $k => $val) {
                    $Data['Data'][$key]['ContentImage'] = _GetPicToContent($val['Content']);
                    if ($Data['Data'][$key]['ContentImage']) {
                        break;
                    }
                }
            }
        }
        MultiPage ( $Data, 2 );
        // 旅游专题页列表end

        $Title = '美国游记攻略_美国热门游记_美国旅游攻略 - 57美国网';
        $Keywords = '美国游记攻略,美国热门游记,美国旅游攻略,美国购物攻略';
        $Description = '57美国网美国游记频道，聚集了美国游记攻略，由旅行达人与驴友分享美国旅游心得、旅行新玩法、行程线路规划，并推荐必去景点、当地玩乐、特色美食等美国旅游的实用信息。';

        include template('NewsTravelsLists');
    }

    /**
     * 游记内容
     */
    public function TravelsDetail()
    {
        $ID = $_GET['ID'];
        $TravelsModule = new TblTravelsModule();
        $TourProductImageModule = new TourProductImageModule();
        $TourInfo = $TravelsModule->GetInfoByKeyID($ID);

        $Content = json_decode($TourInfo['Content'], true);
        foreach ($Content as $key => $val) {
            $Content[$key]['Content'] = StrReplaceImages($val['Content'], $val['Title']);
        }
        foreach ($Content as $key => $value) {
            $Content[$key]['key'] = $key + 1;
        }
        $TourInfo['TripInformation']=  json_decode($TourInfo['TripInformation'],true);
        $TblTravelsCategoriesModule = new TblTravelsCategoriesModule();
        // 出发地点
        $TravelsCategories = $TblTravelsCategoriesModule->GetInfoByKeyID($TourInfo['CategoryID']);
        $TourInfo['Departure'] = $TravelsCategories['CategoryName'];
        // 跟团游产品推荐（旅游首页跟团游推荐获取）
        $TourProductLineModule = new TourProductLineModule();
        $R2SqlWhere = 'and R2 = 1 order by S2 DESC';
        $ListsR2 = $TourProductLineModule->GetLists($R2SqlWhere, 0, 2);

        foreach ($ListsR2 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR2[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR2[$key]['TagInfo'] = explode(',', $value['TagInfo']);
        }
        // 增加阅读量
        $TravelsModule->UpdateViewCount($ID);

        $Title = $TourInfo['SeoTitle'] ? $TourInfo['SeoTitle'] . ' - 57美国网' : $TourInfo['Title'] . ' - 57美国网';
        $Keywords = $TourInfo['SeoKeywords'] ? $TourInfo['SeoKeywords'] : $TourInfo['Title'];
        $Description = $TourInfo['SeoDescription'] ? $TourInfo['SeoDescription'] : '57美国网（57us.com）给您提供' . $TourInfo['Title'] . ',' . $TourInfo['Description'];

        include template('NewsTravelsDetail');
    }

    /* ##################################### */
    /**
     * 留学资讯首页
     */
    public function StudyIndex()
    {
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $TourTuijian = $TblStudyAbroadModule->GetInfoByWhere(' and M2 = 1 order by Sort asc,AddTime desc limit 5', true);

        // 广告信息获取
        $WapTourIndexBaner = NewsGetAdInfo('WapStudyIndexBaner');
        $WapTourIndexJingCai = NewsGetAdInfo('WapStudyIndexJingCai');
        $Title = '美国留学_美国留学费用_美国留学网  - 57美国网';
        $Keywords = '美国留学,美国留学费用,美国留学条件,美国留学中介,美国留学签证 ';
        $Description = '57美国网（57us.com）给您提供美国留学费用清单,留学条件,留学考试,签证指南,留学中介,留学签证 ,留学签规划 ,美国留学签机构，美国留学签证材料，美国留学办理流程，美国留学材料清单等信息。';
        include template('NewsStudyIndex');
    }

    /**
     * 留学资讯一级类别
     */
    public function StudyParentLists()
    {
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $Alias = $_GET['Alias'];
        $Info = $TblStudyAbroadCategoryModule->GetInfoByWhere(' and Alias = \'' . $Alias . '\' ');
        $CategoryID = $Info['CategoryID'];
        $CategoryLists = $TblStudyAbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $CategoryID .' order by GlobalDisplayOrder desc', true);
        $In = $CategoryID;
        foreach ($CategoryLists as $key=>$value){
            $In .= ','.$value['CategoryID'];
        }
        // 推荐文章
        $TourTuijian = $TblStudyAbroadModule->GetInfoByWhere(' and  MATCH(`CategoryID`) AGAINST (\'' . $In . '\' IN BOOLEAN MODE)  and M3 = 1 order by Sort asc,AddTime desc limit 5', true);
        // 广告信息获取
        $WapTourIndexBaner = NewsGetAdInfo('WapStudySpecialBaner');
        $WapTourIndexJingCai = NewsGetAdInfo('WapStudySpecialJingCai');

        $Title = $Info['SeoTitle'] . ' - 57美国网';
        $Keywords = $Info['SeoKeywords'];
        $Description = $Info['SeoDescription'];

        include template('NewsStudyParentLists');
    }

    /**
     * 留学资讯列表
     */
    public function StudyLists()
    {
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();

        $Alias = $_GET['Alias'];
        $Info = $TblStudyAbroadCategoryModule->GetInfoByWhere(' and Alias = \'' . $Alias . '\' ');
        $CategoryID = $Info['CategoryID'];
        // 旅游专题页列表start
        $Offset = 0;
        $MysqlWhere = ' and  CategoryID = ' . $CategoryID . ' order by AddTime DESC';

        $Info = $TblStudyAbroadCategoryModule->GetInfoByKeyID($CategoryID);
        $CategoryName = '';
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 32;
        $Rscount = $TblStudyAbroadModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($pageSize ? $pageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $pageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TblStudyAbroadModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
        }
        MultiPage ( $Data, 2 );
        // 旅游专题页列表end
        $Title = $Info['SeoTitle'] . ' - 57美国网';
        $Keywords = $Info['SeoKeywords'];
        $Description = $Info['SeoDescription'];
        include template('NewsStudyLists');
    }

    /**
     * 留学资讯内容
     */
    public function StudyDetail()
    {
        $ID = $_GET['ID'];
        $TblStudyAbroadModule = new TblStudyAbroadModule();

        $TourProductImageModule = new TourProductImageModule();

        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();

        $TourInfo = $TblStudyAbroadModule->GetInfoByKeyID($ID);
        $TourInfo['Content'] = StrReplaceImages($TourInfo['Content'], $TourInfo['Title']);
        $CategoryInfo = $TblStudyAbroadCategoryModule->GetInfoByKeyID($TourInfo['CategoryID']);
        // 跟团游产品推荐（旅游首页跟团游推荐获取）
        $TourProductLineModule = new TourProductLineModule();
        $R2SqlWhere = 'and R2 = 1 order by S2 DESC';
        $ListsR2 = $TourProductLineModule->GetLists($R2SqlWhere, 0, 2);

        foreach ($ListsR2 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR2[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR2[$key]['TagInfo'] = explode(',', $value['TagInfo']);
        }
        // 增加阅读量
        $TblStudyAbroadModule->UpdateViewCount($ID);
        $Title = $TourInfo['SeoTitle'] ? $TourInfo['SeoTitle'] . ' - 57美国网' : $TourInfo['Title'] . ' - 57美国网';
        $Keywords = $TourInfo['SeoKeywords'] ? $TourInfo['SeoKeywords'] : $TourInfo['Title'];
        $Description = $TourInfo['SeoDescription'] ? $TourInfo['SeoDescription'] : '57美国网（57us.com）给您提供' . $TourInfo['Title'] . ',' . $TourInfo['Description'];
        include template('NewsStudyDetail');
    }

    /* ##################################### */
    /**
     * 移民资讯首页
     */
    public function ImmigrantIndex()
    {
        $TblImmigrationModule = new TblImmigrationModule();
        $TourTuijian = $TblImmigrationModule->GetInfoByWhere(' and M2 = 1 order by Sort asc,AddTime desc limit 5', true);

        // 广告信息获取
        $WapTourIndexBaner = NewsGetAdInfo('WapImmigrantIndexBaner');
        $WapTourIndexJingCai = NewsGetAdInfo('WapImmigrantIndexJingCai');
        $Title = '美国移民_美国投资移民_美国移民条件_美国移民政策 - 57美国网';
        $Keywords = '美国移民,美国投资移民,美国移民条件,美国移民政策,美国移民中介,投资移民美国,美国买房移民,美国购房移民,美国移民生活,美国亲属移民,移民美国需要什么条件,美国移民排期,美国移民排表,美国移民签证,美国技术移民,如何移民美国,美国移民费用,移民美国多少钱,美国移民中介';
        $Description = '57美国网移民频道，给您提供美国移民条件、移民政策、移民生活、移民文化、技术移民、投资移民、移民费用、美国就业机会、医疗分布、法律法规、投资指南、房产资讯等移民信息,57美国网拥有最完善最成熟最全面的移民服务系统为客户创造价值，实现美国移民愿景。';
        include template('NewsImmigrantIndex');
    }

    /**
     * 移民资讯一级类别
     */
    public function ImmigrantParentLists()
    {
        $TblImmigrationModule = new TblImmigrationModule();
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $Alias = $_GET['Alias'];
        $Info = $TblImmigrationCategoryModule->GetInfoByWhere(' and Alias = \'' . $Alias . '\' ');
        $CategoryID = $Info['CategoryID'];
        $CategoryLists = $TblImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $CategoryID, true);
        // 推荐文章
        $TourTuijian = $TblImmigrationModule->GetInfoByWhere(' and M3 = 1 order by Sort asc,AddTime desc limit 5', true);

        // 广告信息获取
        $WapTourIndexBaner = NewsGetAdInfo('WapImmigrantSpecialBaner');
        $WapTourIndexJingCai = NewsGetAdInfo('WapImmigrantSpecialJingCai');

        $Title = $Info['SeoTitle'] ? $Info['SeoTitle'] . ' - 57美国网' : $Info['Title'] . ' - 57美国网';
        $Keywords = $Info['SeoKeywords'] ? $Info['SeoKeywords'] : $Info['Title'];
        $Description = $Info['SeoDescription'] ? $Info['SeoDescription'] : '57美国网（57us.com）给您提供' . $Info['Title'] . ',' . $Info['Description'];

        include template('NewsImmigrantParentLists');
    }

    /**
     * 移民资讯列表
     */
    public function ImmigrantLists()
    {
        $TblImmigrationModule = new TblImmigrationModule();
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();

        $Alias = $_GET['Alias'];
        $Info = $TblImmigrationCategoryModule->GetInfoByWhere(' and Alias = \'' . $Alias . '\' ');
        $CategoryID = $Info['CategoryID'];
        // 旅游专题页列表start
        $Offset = 0;
        $MysqlWhere = ' and  CategoryID = ' . $CategoryID . ' order by AddTime DESC';

        $Info = $TblImmigrationCategoryModule->GetInfoByKeyID($CategoryID);
        $CategoryName = '';
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 32;
        $Rscount = $TblImmigrationModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($pageSize ? $pageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $pageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TblImmigrationModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
        }
        MultiPage ( $Data, 2 );
        // 旅游专题页列表end

        $Title = $Info['SeoTitle'] ? $Info['SeoTitle'] . ' - 57美国网' : $Info['Title'] . ' - 57美国网';
        $Keywords = $Info['SeoKeywords'] ? $Info['SeoKeywords'] : $Info['Title'];
        $Description = $Info['SeoDescription'] ? $Info['SeoDescription'] : '57美国网（57us.com）给您提供' . $Info['Title'] . ',' . $Info['Description'];

        include template('NewsImmigrantLists');
    }

    /**
     * 移民资讯内容
     */
    public function ImmigrantDetail()
    {
        $ID = $_GET['ID'];
        $TblImmigrationModule = new TblImmigrationModule();
        $TourProductImageModule = new TourProductImageModule();
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();

        $TourInfo = $TblImmigrationModule->GetInfoByKeyID($ID);
        $TourInfo['Content'] = StrReplaceImages($TourInfo['Content'], $TourInfo['Title']);
        $CategoryInfo = $TblImmigrationCategoryModule->GetInfoByKeyID($TourInfo['CategoryID']);
        // 跟团游产品推荐（旅游首页跟团游推荐获取）
        $TourProductLineModule = new TourProductLineModule();
        $R2SqlWhere = 'and R2 = 1 order by S2 DESC';
        $ListsR2 = $TourProductLineModule->GetLists($R2SqlWhere, 0, 2);

        foreach ($ListsR2 as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $ListsR2[$key]['ImageUrl'] = $ImageInfo['ImageUrl'];
            $ListsR2[$key]['TagInfo'] = explode(',', $value['TagInfo']);
        }
        // 增加阅读量
        $TblImmigrationModule->UpdateViewCount($ID);

        $Title = $TourInfo['SeoTitle'] ? $TourInfo['SeoTitle'] . ' - 57美国网' : $TourInfo['Title'] . ' - 57美国网';
        $Keywords = $TourInfo['SeoKeywords'] ? $TourInfo['SeoKeywords'] : $TourInfo['Title'];
        $Description = $TourInfo['SeoDescription'] ? $TourInfo['SeoDescription'] : '57美国网（57us.com）给您提供' . $TourInfo['Title'] . ',' . $TourInfo['Description'];

        include template('NewsImmigrantDetail');
    }
}
