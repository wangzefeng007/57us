<?php

class News
{

    public function __construct()
    {
        $this->SoType = 'tour';
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
        $this->TourFootTagF = $TourFootTagModule->GetLists(' and Type=6 order by Sort DESC', 0, 200);//友情链接
    }

    /**
     * 主站首页
     */
    public function Index()
    {
        //弹出广告
        setcookie('tanchuad',1,time()+3600*24,'/','www.57us.com');
        setcookie('tanchuad',1,time()+3600*24,'/','study.57us.com');
        
        // 广告信息获取
        $AdIndex = NewsGetAdInfo('index');
        
        // 旅游新发现（广告位） tour_discovery 5条
        $AdLyxfx = NewsGetAdInfo('index_tour_discovery');
        
        // 美国游记 表：tbl_travels 按点击量查询（两条）
        $TblTravelsModule = new TblTravelsModule();
        $Mgyj = $TblTravelsModule->GetInfoByWhere(' and NewsIndexRecommend = 1 order by TravelsID desc limit 2 ', true);
        // ============================= 旅游模块数据调用 =========================//
        $TblTourModule = new TblTourModule();
        $TourCateModule = new TblTourCategoryModule();
        // 旅游分类
        $TourCate = $TourCateModule->GetInfoByWhere(' and ParentCategoryID = 0', true);
        
        // 当季推荐 分类ID：1049 表：tbl_tour 按时间排序（6条）
        $Djtj = $TblTourModule->GetInfoByWhere(' and CategoryID = 1049 and Image!=\'\' and IndexRecommend=1 order by TourID desc limit 6', true);
        // 美食购物 分类ID：1048 表：tbl_tour 按时间排序（6条）
        $Msxh = $TblTourModule->GetInfoByWhere(' and CategoryID = 1048 and Image!=\'\' and IndexRecommend=1 order by TourID desc limit 6', true);
        
        // 跟团游产品广告位 （最少4条最多8条）
        $AdGtycp = NewsGetAdInfo('index_tour_group');
        // ============================= 旅游模块数据调用结束 =========================//
        
        // ============================= 留学模块数据调用 =========================//
        $AbroadModule = new TblStudyAbroadModule();
        $AbroadCategoryModule = new TblStudyAbroadCategoryModule();
        // 总分类
        $CateInfo = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 0 order by DisplayOrder asc', true);
        foreach ($CateInfo as $key => $val) {
            $CateInfo[$key]['Next'] = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $val['CategoryID'] . ' order by DisplayOrder asc', true);
        }
        $CateInfoNum = count($CateInfo);
        // ===============院校 分类ID：1031
        //院校资讯
        $UscolegeYXZX = $AbroadModule->GetInfoByWhere(' and CategoryID = 1052 and IndexRecommend = 1 order by StudyID DESC');

        $Uscolege = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 1031 and CategoryID != 1052 order by DisplayOrder asc', true);
        foreach ($Uscolege as $key => $val){
            $Uscolege[$key]['NewsImage'] = $AbroadModule->GetInfoByWhere(' and CategoryID = '.$val['CategoryID'].' and Image!=\'\' and IndexRecommend=1 order by StudyID desc');
            $Uscolege[$key]['News'] = $AbroadModule->GetLists(' and CategoryID = '.$val['CategoryID'].' and IndexRecommend=1 order by StudyID DESC', 0, 3);
        }

        // ===============留学 分类ID：1093
        // 除了留学生活分类ID:1084外，其他数据
        $NewsInfos = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 1093 and CategoryID <> 1084 order by DisplayOrder asc', true);
        foreach ($NewsInfos as $key => $val) {
            $NewsInfos[$key]['News'] = $AbroadModule->GetLists(' and CategoryID = ' . $val['CategoryID'].' order by IndexRecommend desc,StudyID desc', 0, 4);
        }
        // 留学生活数据 ID:1084
        $NewsLXSH = $this->GetNextCateAndNews(1084, 6);
        
        // ===============考试 分类ID：1053
        $ExamInfo = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 1053 order by DisplayOrder asc', true);
        foreach ($ExamInfo as $key => $val) {
            $ExamNextCate = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $val['CategoryID'] . ' order by DisplayOrder asc  ', true);
            $In = $val['CategoryID'];
            foreach ($ExamNextCate as $k => $v) {
                $In .= ',' . $v['CategoryID'];
            }
            $ExamInfo[$key]['NewsIndexRecommend'] = $AbroadModule->GetInfoByWhere(' and IndexRecommend = 1 and CategoryID in (' . $In . ') order by StudyID desc ' );
            $ExamInfo[$key]['TopNews'] = $AbroadModule->GetLists(' and IndexRecommend=1 and Image!=\'\' and CategoryID in (' . $In . ') order by StudyID desc ', 0, 2);
            $ExamInfo[$key]['News'] = $AbroadModule->GetLists(' and IndexRecommend=1 and CategoryID in (' . $In . ') order by StudyID desc ', 0, 6);
        }
        
        // ==============游学 分类ID：1069
        $LearningCateInfo = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 1069 order by DisplayOrder asc', true);
        // 游学须知 分类ID：1157
        $LearningLXXZ = $AbroadModule->GetInfoByWhere(' and CategoryID = 1157 and IndexRecommend = 1 order by StudyID desc');
        // 游学游记 分类ID：1081
        // 头条有图
        $LearningYXYJImages = $AbroadModule->GetInfoByWhere(' and CategoryID = 1081 and IndexRecommend = 1 order by StudyID desc');
        if($LearningYXYJImages){
            $LearningYXYJWhere = ' and StudyID !=' . $LearningYXYJImages['StudyID'] . '';
        }
        //
        $LearningYXYJ = $AbroadModule->GetLists(' and CategoryID =1081 '.$LearningYXYJWhere.' and IndexRecommend=1 order by StudyID desc ', 0, 4);
        // 游学答疑 分类ID：1080
        // 头条有图
        $LearningYXDYImages = $AbroadModule->GetInfoByWhere(' and CategoryID = 1080 and IndexRecommend = 1 order by StudyID desc');
        if($LearningYXDYImages){
            $LearningYXDYWhere = ' and StudyID !=' . $LearningYXDYImages['StudyID'] . '';
        }
        $LearningYXDY = $AbroadModule->GetLists(' and IndexRecommend=1 and CategoryID =1080 '.$LearningYXDYWhere.' order by StudyID desc ', 0, 4);
        
        // ===============签证 分类ID：1033
        $GuideInfo = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 1033 order by DisplayOrder asc', true);
        foreach ($GuideInfo as $key => $val) {
            $GuideInfo[$key]['NewsIndexRecommend'] = $AbroadModule->GetInfoByWhere(' and CategoryID = ' . $val['CategoryID'] . '  and IndexRecommend = 1 order by StudyID desc ');
            if($GuideInfo[$key]['NewsIndexRecommend']){
                $GuideInfoWhere = ' and StudyID !=' . $GuideInfo[$key]['NewsIndexRecommend']['StudyID'] . '';
            }
            $GuideInfo[$key]['News'] = $AbroadModule->GetLists(' and IndexRecommend=1 and CategoryID = ' . $val['CategoryID'] .$GuideInfoWhere. ' order by StudyID desc ', 0, 2);
        }
        // ============================= 留学模块数据调用结束 =========================//
        
        // =============================顾问、教师推荐数据调用 ========================//
        $UserInfoModule = new MemberUserInfoModule();
        // 推荐顾问
        $ConsultantModule = new StudyConsultantInfoModule();
        $ConsultantInfo = $ConsultantModule->GetInfoByWhere(' and IsZZRecommend = 1 limit 8 ', true);
        $Consultant = array();
        foreach ($ConsultantInfo as $key => $val) {
            $UserInfo = $UserInfoModule->GetInfoByUserID($val['UserID']);
            $Consultant[$key]['RealName'] = $UserInfo['RealName']; // 真实姓名
            $Consultant[$key]['Avatar'] = LImageURL.$UserInfo['Avatar']; // 头像
            //$Consultant[$key]['Grade'] = $ConsultantModule->Grade[$val['Grade']]; // 顾问等级
            $Consultant[$key]['Grade'] = "顾问"; // 顾问等级
            $Consultant[$key]['UserID'] = $UserInfo['UserID']; // 会员ID
        }
        // 推荐老师
        $TeacherModule = new StudyTeacherInfoModule();
        $TeacherInfo = $TeacherModule->GetInfoByWhere(' and IsZZRecommend = 1 limit 8 ', true);
        $Teacher = array();
        foreach ($TeacherInfo as $key => $val) {
            $UserInfo1 = $UserInfoModule->GetInfoByUserID($val['UserID']);
            $Teacher[$key]['RealName'] = $UserInfo1['RealName']; // 真实姓名
            $Teacher[$key]['Avatar'] = LImageURL.$UserInfo1['Avatar']; // 头像
            $Teacher[$key]['Grade'] = $TeacherModule->Grade[$val['Grade']]; // 顾问等级
            $Teacher[$key]['UserID'] = $UserInfo1['UserID']; // 会员ID
        }
        // =============================顾问、教师推荐数据调用结束 ========================//
        // 游学产品广告位 （2条）
        $Adyxcp = NewsGetAdInfo('index_study_tour');
        // ============================= 移民模块数据调用 =========================//
        // 美国移民：移民类别、生活指南、移民攻略、投资房产、移民百科 表：tbl_immigration
        $ImmigrationModule = new TblImmigrationModule();
        $ImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $Immigration = $ImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = 0', true);
        foreach ($Immigration as $key => $val) {
            $Immigration[$key]['Next'] = $ImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $val['CategoryID'], true);
            foreach ($Immigration[$key]['Next'] as $k => $v) {
                if($k == 0){
                    //echo "<pre>";print_r($v);exit;
                    $NewsIndexRecommend = $ImmigrationModule->GetLists(' and IndexRecommend = 1 and CategoryID =' . $v['CategoryID'] .' order by ImmigrationID desc ', 0, 4);
                    if($NewsIndexRecommend){
                        $Immigration[$key]['Next'][$k]['NewsIndexRecommend'] = $NewsIndexRecommend;
                    }
                    else{
                        $Immigration[$key]['Next'][$k]['NewsIndexRecommend'] = $ImmigrationModule->GetLists(' and IndexRecommend = 1 and CategoryID =' . $v['CategoryID'] .' order by ImmigrationID desc ', 0, 4);
                    }

                }
                elseif($k == 1){
                    $NewsImage = $ImmigrationModule->GetInfoByWhere(' and IndexRecommend = 1 and Image!= "" and CategoryID =' . $v['CategoryID'] .' order by ImmigrationID desc');
                    if($NewsImage){
                        $Immigration[$key]['Next'][$k]['NewsImage'] = $NewsImage;
                    }
                    else{
                        $Immigration[$key]['Next'][$k]['NewsImage'] = $ImmigrationModule->GetInfoByWhere(' and IndexRecommend = 1 and CategoryID =' . $v['CategoryID'] .' order by ImmigrationID desc');
                    }
                    $Immigration[$key]['Next'][$k]['News'] = $ImmigrationModule->GetLists(' and IndexRecommend = 1 and ImmigrationID != '.$Immigration[$key]['Next'][$k]['NewsImage']['ImmigrationID'].' and CategoryID =' . $v['CategoryID'] . ' order by ImmigrationID desc', 0, 6);
                }
                else{
                    $NewsIndexRecommend = $ImmigrationModule->GetInfoByWhere(' and IndexRecommend = 1 and CategoryID =' . $v['CategoryID'] .' order by ImmigrationID desc');
                    if($NewsIndexRecommend){
                        $Immigration[$key]['Next'][$k]['NewsIndexRecommend'] = $NewsIndexRecommend;
                    }
                    else{
                        $Immigration[$key]['Next'][$k]['NewsIndexRecommend'] = $ImmigrationModule->GetInfoByWhere(' and IndexRecommend = 1 and CategoryID  =' . $v['CategoryID'] .' order by ImmigrationID desc');
                    }
                    if($Immigration[$key]['Next'][$k]['NewsIndexRecommend']){
                        $ImmigrationWhere = ' and IndexRecommend = 1 and ImmigrationID !=' . $Immigration[$key]['Next'][$k]['NewsIndexRecommend']['ImmigrationID'] . '';
                    }
                    $Immigration[$key]['Next'][$k]['News'] = $ImmigrationModule->GetInfoByWhere(' and IndexRecommend = 1 and CategoryID  =' . $v['CategoryID']. $ImmigrationWhere .' order by ImmigrationID desc');
                }
            }
        }
        // ============================= 移民模块数据调用结束 =========================//
        // 移民广告位(1条)
        $AdYm = NewsGetAdInfo('index_immigrant');
        $TourFootTagModule = new TourFootTagModule();
        $USTour = $TourFootTagModule->GetLists(' and Type=3 order by Sort DESC', 0, 200);
        $USStudy = $TourFootTagModule->GetLists(' and Type=4 order by Sort DESC', 0, 200);
        $USImmigrant = $TourFootTagModule->GetLists(' and Type=5 order by Sort DESC', 0, 200);
        
        //友情链接
        $TblLinksModule = new TblLinksModule();
        $TblLinks = $TblLinksModule->GetInfoByWhere(' and Type=1 order by Sort DESC',true);
        
        $Title = '【美国旅游/留学/移民】一站式服务平台 - 57美国网(57us.com)';
        $Keywords = '美国旅游,美国留学,美国投资移民,美国自驾游费用,美国旅游攻略,美国留学费用,美国移民条件,美国游学夏令营';
        $Description = '57美国网：提供美国当地最新热门资讯，汇集最全的美国旅游指南及涵盖美国跟团游、定制游、自由行、酒店、门票、行程工具等服务；美国高中和大学留学好帮手，涵括美国游学、语言培训、院校申请、签证辅助、寄宿租房等服务；最专业的美国移民团队能够快速评估及申请。专注美国一站式服务-57美国网（57us.com）。';
        include template('NewsIndex');
    }

    /**
     * @desc  获取下级分类及分类底下的新闻
     * @param $CateID
     * @param $Limit
     * @return array
     */
    public function GetNextCateAndNews($CateID, $Limit)
    {
        $AbroadModule = new TblStudyAbroadModule();
        $AbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $Data = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $CateID, true);
        foreach ($Data as $k => $v) {
            $Data[$k]['News'] = $AbroadModule->GetLists(' and CategoryID = ' . $v['CategoryID'] .' order by IndexRecommend desc,StudyID desc', 0, $Limit);
        }
        return $Data;
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
        } else {
            // 旅游
            $this->SearchTour();
        }
    }

    /**
     * @desc  旅游搜索
     */
    public function SearchTour()
    {
        $TblTourModule = new TblTourModule();
        $KeywordModule = new TblTourKeywordModule();
        $TblTourCategoryModule = new TblTourCategoryModule();
        $Type = trim($_GET['Type']);
        $Keyword = $_GET['KeyWord'];
        $Offset = 0;
        $GoPageUrl = '/search_' . $Type . '_' . $Keyword;
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $Rscount = $TblTourModule->GetListsNum($MysqlWhere);
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
                $Category = $TblTourCategoryModule->GetInfoByKeyID($value['CategoryID']);
                $Data['Data'][$key]['Category'] = $Category['CategoryName'];
                if ($value['Keywords'] != '') {
                    $Keywords = $KeywordModule->ToKeywordName($value['Keywords']);
                    $Keywords = explode(',', $Keywords);
                    $Data['Data'][$key]['Keywords'] = $Keywords;
                }
            }
        }
        // 分页
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
        $NunArray = $this->GetNum($Keyword);
        // 酒店、旅游产品广告
        $AdTour = NewsGetAdInfo('tour_list_tour');
        $AdHotel = NewsGetAdInfo('tour_list_hotel');
        //跟团游产品推荐
        $TourProductLine = $this->SearchTourLine($Keyword);
        
        $Title = '搜索'.$Keyword.'结果_美国旅游  - 57美国网';
        $Keywords = '美国旅游搜索'.$Keyword.'结果';
        $Description = '美国旅游搜索'.$Keyword.'结果,'.$Data['Data'][0]['Description'];
        include template('TourSearchKeyword');
    }

    /**
     * @desc  游学搜索
     */
    public function SearchTravels()
    {
        $this->SoType = 'travels';
        $TblTravelsModule = new TblTravelsModule();
        $TblTravelsKeywordModule = new TblTravelsKeywordModule();
        $TblTravelsCategoriesModule = new TblTravelsCategoriesModule();
        $Type = trim($_GET['Type']);
        $Keyword = $_GET['KeyWord'];
        $Offset = 0;
        $GoPageUrl = '/search_' . $Type . '_' . $Keyword;
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $Rscount = $TblTravelsModule->GetListsNum($MysqlWhere);
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
                    foreach (json_decode($value['Content'], true) as $k => $val) {
                        $Data['Data'][$key]['ContentImage'] = _GetPicToContent($val['Content']);
                        if ($Data['Data'][$key]['ContentImage']) {
                            break;
                        }
                    }
                    $Category = $TblTravelsCategoriesModule->GetInfoByKeyID($value['CategoryID']);
                    $Data['Data'][$key]['Category'] = $Category['CategoryName'];
                    if ($value['Keywords'] != '') {
                        $Keywords = $TblTravelsKeywordModule->ToKeywordName($value['Keywords']);
                        $Keywords = explode(',', $Keywords);
                        $Data['Data'][$key]['Keywords'] = $Keywords;
                    }
                }
        }
        // 分页
        $Page = new Page($Rscount['Num'], $PageSize, 1);
        $listpage = $Page->showpage();
        // 该标签下的最热文章抽取8条数据。
        $WhereHeat = ' order by ViewCount DESC'; // 按照阅读量排序。
        $hotarticle = $TblTravelsModule->GetLists($WhereHeat, $Offset, 8);
    
        // 热门标签调用
        $Keyhot = $TblTravelsKeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        $NunArray = $this->GetNum($Keyword);
        
             // 酒店、旅游产品广告
             $AdTour = NewsGetAdInfo('tour_list_tour');
             $AdHotel = NewsGetAdInfo('tour_list_hotel');
             //跟团游产品推荐
             $TourProductLine = $this->SearchTourLine($Keyword);
             $Title = '搜索'.$Keyword.'结果_美国游记  - 57美国网';
             $Keywords = '美国游记搜索'.$Keyword.'结果';
             $Description = '美国游记搜索'.$Keyword.'结果,'.$Data['Data'][0]['Description'];
             include template('TravelsSearchKeyword');
    }
    /**
     * 跟团游相关推荐
     * Author lusb
     */
    public function SearchTourLine($Keyword='')
    {
        $TourProductLineModule = new TourProductLineModule();
        $TourProductImageModule = new TourProductImageModule();
    
        $TourProductLine = $TourProductLineModule->GetInfoByWhere(" and Status=1 and (`ProductName` like '%" . $Keyword . "%' or `ProductSimpleName` like '%" . $Keyword . "%') order by `Cent` DESC limit 8", true);
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
     * @desc 留学搜索
     */
    public function SearchStudy()
    {
        $this->SoType = 'study';
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $KeywordModule = new TblStudyAbroadKeywordModule();
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $Type = trim($_GET['Type']);
        $Keyword = $_GET['KeyWord'];
        $Offset = 0;
        $GoPageUrl = '/search_' . $Type . '_' . $Keyword;
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $Rscount = $TblStudyAbroadModule->GetListsNum($MysqlWhere);
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
            $Data['Data'] = $TblStudyAbroadModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                $Category = $TblStudyAbroadCategoryModule->GetInfoByKeyID($value['CategoryID']);
                $Data['Data'][$key]['Category'] = $Category['CategoryName'];
                if ($value['Keywords'] != '') {
                    $Keywords = $KeywordModule->ToKeywordName($value['Keywords']);
                    $Keywords = explode(',', $Keywords);
                    $Data['Data'][$key]['Keywords'] = $Keywords;
                }
            }
        }
        // 分页
        $Page = new Page($Rscount['Num'], $PageSize, 1);
        $listpage = $Page->showpage();
        // 该标签下的最新文章抽取8条数据。
        $WhereNewest = ' order by AddTime DESC'; // 按照时间排序。
        $TimeArticle = $TblStudyAbroadModule->GetLists($WhereNewest, $Offset, 8);
        
        // 该标签下的最热文章抽取8条数据。
        $WhereHeat = ' order by ViewCount DESC'; // 按照阅读量排序。
        $hotarticle = $TblStudyAbroadModule->GetLists($WhereHeat, $Offset, 8);
        
        // 热门标签调用
        $Keyhot = $KeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        $NunArray = $this->GetNum($Keyword);
        $Title = '搜索'.$Keyword.'结果_美国留学  - 57美国网';
        $Keywords = '美国留学搜索'.$Keyword.'结果';
        $Description = '美国留学搜索'.$Keyword.'结果,'.$Data['Data'][0]['Description'];
        include template('StudySearchKeyword');
    }

    /**
     * @desc  移民搜索
     */
    public function SearchImmigrant()
    {
        $this->SoType = 'immigrant';
        $TblImmigrationModule = new TblImmigrationModule();
        $TblImmigrationKeywordModule = new TblImmigrationKeywordModule();
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $Type = trim($_GET['Type']);
        $Keyword = $_GET['KeyWord'];
        $Offset = 0;
        $GoPageUrl = '/search_' . $Type . '_' . $Keyword;
        $MysqlWhere = ' and `Title` like \'%' . $Keyword . '%\''; // 搜索关键字条件
        $Rscount = $TblImmigrationModule->GetListsNum($MysqlWhere);
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
            $Data['Data'] = $TblImmigrationModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                $Category =$TblImmigrationCategoryModule->GetInfoByKeyID($value['CategoryID']);
                $Data['Data'][$key]['Category'] = $Category['CategoryName'];
                if ($value['Keywords'] != '') {
                    $Keywords = $TblImmigrationKeywordModule->ToKeywordName($value['Keywords']);
                    $Keywords = explode(',', $Keywords);
                    $Data['Data'][$key]['Keywords'] = $Keywords;
                }
            }
        }
        // 分页
        $Page = new Page($Rscount['Num'], $PageSize, 1);
        $listpage = $Page->showpage();
        // 该标签下的最新文章抽取8条数据。
        $WhereNewest = ' order by AddTime DESC'; // 按照时间排序
        $TimeArticle = $TblImmigrationModule->GetLists($WhereNewest, $Offset, 8);
        // 该标签下的最热文章抽取8条数据。
        $WhereHeat = ' order by ViewCount DESC'; // 按照阅读量排序
        $hotarticle = $TblImmigrationModule->GetLists($WhereHeat, $Offset, 8);
        
        // 热门标签调用
        $Keyhot = $TblImmigrationKeywordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        //移民专题广告
        $TopicTour = NewsGetAdInfo('immigrant_topic');
        $NunArray = $this->GetNum($Keyword);
        
        $Title = '搜索'.$Keyword.'结果_美国移民  - 57美国网';
        $Keywords = '美国移民搜索'.$Keyword.'结果';
        $Description = '美国移民搜索'.$Keyword.'结果,'.$Data['Data'][0]['Description'];
        include template('ImmigSearchKeyword');
    }

    /**
     * @desc  获取搜索条数
     * @param string $Keyword
     * @return mixed
     */
    public function GetNum($Keyword = ''){
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
}
