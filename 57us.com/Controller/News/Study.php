<?php

/**
 * @desc 留学
 * Class Study
 */
class Study
{

    public function __construct()
    {
        /* 底部调用热门旅游目的地、景点 */
        $this->SoType = 'study';
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }

    /**
     * 留学_留学资讯_首页
     */
    public function StudyAbroad()
    {
        $AbroadModule = new TblStudyAbroadModule();
        $AbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $KeyWordModule = new TblStudyAbroadKeywordModule();
        // 留学首页广告轮播
        $AdStudyAbroadIndex = NewsGetAdInfo('studyabroad_index');
        // 留学模块数据调用
        $AllInfo = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 0 order by DisplayOrder asc  ', true);
        foreach ($AllInfo as $key => $val) {
            $Two = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $val['CategoryID'] . ' order by DisplayOrder asc  ', true);
            if($val['CategoryID'] == 1031){
                $num = 2;
            }
            else{
                $num = 1;
            }
            foreach ($Two as $k => $v) {
                $Three = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $v['CategoryID'] . ' order by DisplayOrder asc  ', true);
                if ($Three) {
                    foreach ($Three as $k1 => $v1) {
                        $In = $v['CategoryID'];
                        $In .= ',' . $v1['CategoryID'];
                        $Two[$k]['NewsTopic'] = $AbroadModule->GetLists(' and TopicRecommend = 1 and CategoryID in (' . $In . ') ', 0, $num);
                        $ASqlWhere ='';
                        foreach ($Two[$k]['NewsTopic'] as $KeyA => $ValueA)
                        {
                            $ASqlWhere .= ' and StudyID != '.$ValueA['StudyID'];
                        }
                        $Two[$k]['News'] = $AbroadModule->GetLists($ASqlWhere.' and CategoryID in (' . $In . ') ', 0, 8);
                    }
                } else {
                    $Two[$k]['NewsTopic'] = $AbroadModule->GetLists(' and TopicRecommend = 1 and CategoryID = ' . $v['CategoryID'], 0, $num);
                    $BSqlWhere ='';
                    foreach ($Two[$k]['NewsTopic'] as $KeyB => $ValueB)
                    {
                        $BSqlWhere .= ' and StudyID != '.$ValueB['StudyID'];
                    }
                    $Two[$k]['News'] = $AbroadModule->GetLists($BSqlWhere.' and CategoryID = ' . $v['CategoryID'], 0, 8);
                }
            }
            $AllInfo[$key]['Two'] = $Two;
        }
        // ====================================右侧内容调用=====================================//
        // 推荐顾问教师数据
        $UserInfoModule = new MemberUserInfoModule();
        // 推荐顾问
        $ConsultantModule = new StudyConsultantInfoModule();
        $ConsultantInfo = $ConsultantModule->GetInfoByWhere(' and IsZZRecommend = 1 limit 4 ', true);
        $Consultant = array();
        foreach ($ConsultantInfo as $key => $val) {
            $UserInfo = $UserInfoModule->GetInfoByUserID($val['UserID']);
            $Consultant[$key]['RealName'] = $UserInfo['RealName']; // 真实姓名
            $Consultant[$key]['Avatar'] = LImageURL.$UserInfo['Avatar']; // 头像
            $Consultant[$key]['Grade'] = $ConsultantModule->Grade[$val['Grade']]; // 顾问等级
            $Consultant[$key]['UserID'] = $UserInfo['UserID']; // 会员ID
        }
        // 推荐老师
        $TeacherModule = new StudyTeacherInfoModule();
        $TeacherInfo = $TeacherModule->GetInfoByWhere(' and IsZZRecommend = 1 limit 4 ', true);
        $Teacher = array();
        foreach ($TeacherInfo as $key => $val) {
            $UserInfo1 = $UserInfoModule->GetInfoByUserID($val['UserID']);
            $Teacher[$key]['RealName'] = $UserInfo1['RealName']; // 真实姓名
            $Teacher[$key]['Avatar'] = LImageURL.$UserInfo1['Avatar']; // 头像
            $Teacher[$key]['Grade'] = $TeacherModule->Grade[$val['Grade']]; // 顾问等级
            $Teacher[$key]['UserID'] = $UserInfo1['UserID']; // 会员ID
        }
        $WhereHotKey = ' order by Traffic desc ';
        $Keyhot = $KeyWordModule->GetLists($WhereHotKey, 0, 12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();
        // 热门文章
        //$WhereHotNews = ' order by ViewCount DESC';
        $HotNews1 = $AbroadModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$AbroadModule->GetListsNum('')['Num'];
        $HotNews2 = $AbroadModule->GetLists(' order by AddTime desc',mt_rand(3,$Index),7);
        $HotNews = array_merge($HotNews1,$HotNews2);      
        //猜你喜欢
        $LikeNews = $AbroadModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-1),6);
        // ====================================右侧内容调用结束=====================================//

        $Title = '美国留学_美国留学费用_美国留学网  - 57美国网';
        $Keywords = '美国留学,美国留学费用,美国留学条件,美国留学中介,美国留学签证 ';
        $Description = '57美国网（57us.com）给您提供美国留学费用清单,留学条件,留学考试,签证指南,留学中介,留学签证 ,留学签规划 ,美国留学签机构，美国留学签证材料，美国留学办理流程，美国留学材料清单等信息。';
        include template('NewsStudyAbroadIndex');
    }

    /**
     * 留学_留学资讯_专题页 分类ID:1093留学、1031院校、1033签证、1069游学
     */
    public function StudyAbroadTopic()
    {
        $Type = trim($_GET['type']) ? trim($_GET['type']) : 'uscolege';
        // 推荐顾问教师数据
        $UserInfoModule = new MemberUserInfoModule();
        //专题页头部广告调用
        $AdStudyTopic = NewsGetAdInfo('studytopic_'.$Type);
        // 推荐顾问
        $ConsultantModule = new StudyConsultantInfoModule();
        $ConsultantInfo = $ConsultantModule->GetInfoByWhere(' and IsZZRecommend = 1 limit 8 ', true);
        $Consultant = array();
        foreach ($ConsultantInfo as $key => $val) {
            $UserInfo = $UserInfoModule->GetInfoByUserID($val['UserID']);
            $Consultant[$key]['RealName'] = $UserInfo['RealName']; // 真实姓名
            $Consultant[$key]['Avatar'] = LImageURL.$UserInfo['Avatar']; // 头像
            $Consultant[$key]['Grade'] = 留学顾问; // 顾问等级
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
        // 留学资讯数据
        $AbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $AbroadModule = new TblStudyAbroadModule();
        $CateInfo = $AbroadCategoryModule->GetInfoByWhere(" and `Alias` = '{$Type}'");

        $Title = $CateInfo['SeoTitle'] ? $CateInfo['SeoTitle'] . ' - 57美国网' : $CateInfo['CategoryName'] . ' - 57美国网';
        $Keywords = $CateInfo['SeoKeywords'];
        $Description = $CateInfo['SeoDescription'];
        $StrategyCate = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $CateInfo['CategoryID'] . ' order by DisplayOrder asc  ', true);
        if ($CateInfo['CategoryID'] != 1053) { // 除了考试模块
            // 获取分类下的新闻（子分类也求)
            foreach ($StrategyCate as $key => $val) {
                $Two = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $val['CategoryID'] . ' order by DisplayOrder asc  ', true);
                if ($Two) {
                    $In = $val['CategoryID'];
                    foreach ($Two as $k => $v) {
                        $In .= ',' . $v['CategoryID'];
                    }
                    $StrategyCate[$key]['TopNews'] = $AbroadModule->GetLists(' and Image != \'\' and CategoryID in (' . $In . ')' .' order by TopicRecommend desc,StudyID desc', 0, 1);
                    $ASqlWhere ='';
                    foreach ($StrategyCate[$key]['TopNews'] as $KeyA => $ValueA)
                    {
                        $ASqlWhere .= ' and StudyID != '.$ValueA['StudyID'];
                    }
                    $StrategyCate[$key]['News'] = $AbroadModule->GetLists($ASqlWhere.' and CategoryID in (' . $In . ') ' .' order by TopicRecommend desc,StudyID desc', 0, 6);
                } else {
                    $StrategyCate[$key]['TopNews'] = $AbroadModule->GetLists(' and Image != \'\' and CategoryID = ' . $val['CategoryID'] .' order by TopicRecommend desc,StudyID desc', 0, 1);
                    $ASqlWhere ='';
                    foreach ($StrategyCate[$key]['TopNews'] as $KeyA => $ValueA)
                    {
                        $ASqlWhere .= ' and StudyID != '.$ValueA['StudyID'];
                    }
                    $StrategyCate[$key]['News'] = $AbroadModule->GetLists($ASqlWhere.' and CategoryID = ' . $val['CategoryID'] .' order by TopicRecommend desc,StudyID desc', 0, 6);
                }
            }

            // 留学首页广告轮播
            include template('NewsStudyAbroadTopic');
        } else { // 考试模式
            foreach ($StrategyCate as $key => $val) {
                $StrategyCate[$key]['Two'] = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $val['CategoryID'] . ' order by DisplayOrder asc  ', true);
                foreach ($StrategyCate[$key]['Two'] as $k => $v) {
                    $StrategyCate[$key]['Two'][$k]['TopNews'] = $AbroadModule->GetLists(' and Image != \'\' and CategoryID = ' . $v['CategoryID'] .' order by TopicRecommend desc,StudyID desc', 0, 2);
                    $ASqlWhere ='';
                    foreach ($StrategyCate[$key]['Two'][$k]['TopNews'] as $KeyA => $ValueA)
                    {
                        $ASqlWhere .= ' and StudyID != '.$ValueA['StudyID'];
                    }
                    $StrategyCate[$key]['Two'][$k]['News'] = $AbroadModule->GetLists($ASqlWhere.' and CategoryID = ' . $v['CategoryID'] .' order by TopicRecommend desc,StudyID desc', 0, 6);
                }
            }
            include template('NewsStudyAbroadExamTopic');
        }
    }

    /**
     * 留学_留学资讯_列表页
     */
    public function StudyAbroadList()
    {
        $AbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $AbroadModule = new TblStudyAbroadModule();
        $KeyWordModule = new TblStudyAbroadKeywordModule();
        $MyUrl = '';
        $Type = trim($_GET['type']);
        $CateInfo = $AbroadCategoryModule->GetInfoByWhere(" and Alias = '{$Type}'");
        $Title = $CateInfo['SeoTitle'] ? $CateInfo['SeoTitle'] . ' - 57美国网' : $CateInfo['Title'] . ' - 57美国网';
        $Keywords = $CateInfo['SeoKeywords'] ? $CateInfo['SeoKeywords'] : $CateInfo['Title'];
        $Description = $CateInfo['SeoDescription'] ? $CateInfo['SeoDescription'] : '57美国网（57us.com）给您提供' . $CateInfo['Title'] . ',' . $CateInfo['Description'];
        $CateID = $CateInfo['CategoryID'];
        if ($CateInfo['ParentCategoryID'] != 0) {
            $PrveCateInfo = $AbroadCategoryModule->GetInfoByKeyID($CateInfo['ParentCategoryID']);
            if ($PrveCateInfo['ParentCategoryID'] != 0) {
                $TopCateInfo = $AbroadCategoryModule->GetInfoByKeyID($PrveCateInfo['ParentCategoryID']);
            }
        }
        $NextCateInfos = $AbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $CateInfo['CategoryID'], true);
        if ($NextCateInfos) {
            $NextCateOneInfo = current($NextCateInfos);
            $NextType = $_GET['nexttype'] ? $_GET['nexttype'] : $NextCateOneInfo['Alias'];
            $NextCateInfo = $AbroadCategoryModule->GetInfoByWhere(" and Alias = '{$NextType}'");
            $MysqlWhere = ' and CategoryID = ' . $NextCateInfo['CategoryID'];
        } else {
            $MysqlWhere = ' and CategoryID = ' . $CateID;
        }
        $Offset = 0;
        $MysqlWhere .= ' order by AddTime DESC';
        

        $Page = intval($_GET['page']);
        if ($Page < 1) {
            $Page = 1;
        }
        $pageSize = 12;
        $Rscount = $AbroadModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($pageSize ? $pageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $pageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $AbroadModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $val) {
                //获取内容图片
                $Data['Data'][$key]['ContentImage'] = _GetPicToContent($val['Content']);
                $str = substr($val['Description'], 400);
                if ($str == '') {
                    $Data['Data'][$key]['str'] = 1;
                }
                // 获取标签
                $Biaoqian = explode(",", $val['Keywords']);
                foreach ($Biaoqian as $k => $v) {
                    $Data['Data'][$key]['Biaoqian'][] = $KeyWordModule->GetInfoByKeyID($v);
                }
            }
            $ClassPage = new Page($Rscount['Num'], $pageSize);
            $ShowPage = $ClassPage->showpage();
        }
        // ====================================右侧内容调用=====================================//
        // 推荐顾问教师数据
        $UserInfoModule = new MemberUserInfoModule();
        // 推荐顾问
        $ConsultantModule = new StudyConsultantInfoModule();
        $ConsultantInfo = $ConsultantModule->GetInfoByWhere(' and IsZZRecommend = 1 limit 4 ', true);
        $Consultant = array();
        foreach ($ConsultantInfo as $key => $val) {
            $UserInfo = $UserInfoModule->GetInfoByUserID($val['UserID']);
            $Consultant[$key]['RealName'] = $UserInfo['RealName']; // 真实姓名
            $Consultant[$key]['Avatar'] = LImageURL.$UserInfo['Avatar']; // 头像
            $Consultant[$key]['Grade'] = 留学顾问; // 顾问等级
            $Consultant[$key]['UserID'] = $UserInfo['UserID']; // 会员ID
        }
        // 推荐老师
        $TeacherModule = new StudyTeacherInfoModule();
        $TeacherInfo = $TeacherModule->GetInfoByWhere(' and IsZZRecommend = 1 limit 4 ', true);
        $Teacher = array();
        foreach ($TeacherInfo as $key => $val) {
            $UserInfo1 = $UserInfoModule->GetInfoByUserID($val['UserID']);
            $Teacher[$key]['RealName'] = $UserInfo1['RealName']; // 真实姓名
            $Teacher[$key]['Avatar'] = LImageURL.$UserInfo1['Avatar']; // 头像
            $Teacher[$key]['Grade'] = $TeacherModule->Grade[$val['Grade']]; // 顾问等级
            $Teacher[$key]['UserID'] = $UserInfo1['UserID']; // 会员ID
        }
        $Keyhot = $KeyWordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();
        // 热门文章
        //$WhereHotNews = ' order by ViewCount DESC';
        $HotNews1 = $AbroadModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$AbroadModule->GetListsNum('')['Num'];
        $HotNews2 = $AbroadModule->GetLists(' order by AddTime desc',mt_rand(3,$Index),7);
        $HotNews = array_merge($HotNews1,$HotNews2);      
        //猜你喜欢
        $LikeNews = $AbroadModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-1),6);
        
        // ====================================右侧内容调用结束=====================================//
        if ($NextType) {
            $GoPageUrl = '/study_' . $Type . '_' . $NextType;
        } else {
            $GoPageUrl = '/study_' . $Type;
        }
        if ($Page > 1) {
            $TitlePage = '_第' . $Page . '页';
        }
        $Title = $CateInfo['SeoTitle'].' - 57美国网';
        $Keywords = $CateInfo['SeoKeywords'];
        $Description = $CateInfo['SeoDescription'];
        include template('NewsStudyAbroadList');
    }

    /**
     * 留学模块详情页
     */
    public function StudyAbroadDetail()
    {
        $KeyWordModule = new TblStudyAbroadKeywordModule();
        $AbroadModule = new TblStudyAbroadModule();
        $AbroadCateModule = new TblStudyAbroadCategoryModule();
        $TagsModule=new TblTagsModule();
        $ID = $_GET['ID'];
        // 资讯详细信息
        $NewsInfo = $AbroadModule->GetInfoByKeyID($ID);
        $NewsInfo['Content'] = StrReplaceImages($TagsModule->TiHuan($NewsInfo['Content']), $NewsInfo['Title']);
        // 获取标签
        $Biaoqian = explode(",", $NewsInfo['Keywords']);
        foreach ($Biaoqian as $k => $v) {
            $KeyWord[] = $KeyWordModule->GetInfoByKeyID($v);
        }
        // 获取分类信息
        $CateInfo = $AbroadCateModule->GetInfoByKeyID($NewsInfo['CategoryID']);
        if ($CateInfo['ParentCategoryID'] != 0) {
            $PrveCateInfo = $AbroadCateModule->GetInfoByKeyID($CateInfo['ParentCategoryID']);
            if ($PrveCateInfo['ParentCategoryID'] != 0) {
                $TopCateInfo = $AbroadCateModule->GetInfoByKeyID($PrveCateInfo['ParentCategoryID']);
            }
        }
        // 上一篇,下一篇
        $PrveSql = ' and CategoryID = ' . $NewsInfo['CategoryID'] . ' and StudyID > ' . $ID . ' order by StudyID asc';
        $PrveNews = $AbroadModule->GetInfoByWhere($PrveSql);
        $NextSql = ' and CategoryID = ' . $NewsInfo['CategoryID'] . ' and StudyID < ' . $ID . ' order by StudyID desc';
        $NextNews = $AbroadModule->GetInfoByWhere($NextSql);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();        
        // 右侧广告文章
        $Keyhot = $KeyWordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);
        // 热门文章
        //$WhereHotNews = ' order by ViewCount DESC';
        $HotNews1 = $AbroadModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$AbroadModule->GetListsNum('')['Num'];
        $HotNews2 = $AbroadModule->GetLists(' order by AddTime desc',mt_rand(3,$Index-1),7);
        $HotNews = array_merge($HotNews1,$HotNews2);  
        //猜你喜欢
        $LikeNews = $AbroadModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-1),6);
        // 最新文章
        $WhereNewNews = ' and Image != \'\' order by AddTime DESC';
        $NewNews = $AbroadModule->GetLists($WhereNewNews, 0, 8);

        // 相关阅读
        $Correlation = '';
        foreach ($KeyWord as $key => $val) {
            $Correlation .= $val['Keyword'] . ',';
        }
        $Correlation = substr($Correlation, 0, strlen($Correlation) - 1);
        $CorrelationNews = $AbroadModule->GetCorrelationNews($Correlation);
        if (! count($CorrelationNews) || $CorrelationNews==0) {
            $CorrelationNews = $AbroadModule->GetLists(' and StudyID <> '.$ID.' and Image != \'\' and CategoryID = ' . $CateInfo['CategoryID'], 0, 8);
        }

        // 增加阅读量
        $AbroadModule->UpdateViewCount($ID);
        //右边广告
        $AdStudyRight = NewsGetAdInfo('study_con_right');
        $Title = $NewsInfo['SeoTitle'] ? $NewsInfo['SeoTitle'] . ' - 57美国网' : $NewsInfo['Title'] . ' - 57美国网';
        $Keywords = $NewsInfo['SeoKeywords'] ? $NewsInfo['SeoKeywords'] : $NewsInfo['Title'];
        $Description = $NewsInfo['SeoDescription'] ? $NewsInfo['SeoDescription'] : '57美国网（57us.com）给您提供' . $NewsInfo['Title'] . ',' . $NewsInfo['Description'];
        include template('NewsStudyAbroadDetail');
    }

    /**
     * 留学标签搜索_列表页
     * Author Zf
     */
    public function StudySearchTag()
    {
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $KeywordModule = new TblStudyAbroadKeywordModule();
        $Offset = 0;
        $Keyword = $_GET['Keyword'];
        $Study = $KeywordModule->GetInfoByWhere(" and  `Keyword` = '".$Keyword.'\'');
        $GoPageUrl ='/study/tags_'.$Keyword;
        $MysqlWhere = ' and  MATCH(`Keywords`) AGAINST ('.$Study['KeyID'].' IN BOOLEAN MODE)';//搜索相关标签的数据
        $page = intval($_GET['page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 10;
        $Rscount = $TblStudyAbroadModule->GetListsNum($MysqlWhere);
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
                if ($key == 0) {
                    $Description = $value['Description'];
                }
                if (! empty($value['Keywords'])) {
                    $Keywords = $KeywordModule->ToKeywordName($value['Keywords']);
                    $Keywords = explode(',', $Keywords);
                    $Data['Data'][$key]['Keywords'] = $Keywords;
                }
            }
        }
        $Page = new Page($Rscount['Num'], $PageSize,1);
        $listpage = $Page->showpage();
        // 该标签下的最新文章抽取8条数据。
        $WhereNewest = ' order by AddTime DESC'; // 按照时间排序。
        $TimeArticle = $TblStudyAbroadModule->GetLists($WhereNewest, $Offset, 8);
        // 该标签下的最热文章抽取8条数据。
        $WhereHeat = ' order by ViewCount DESC'; // 按照阅读量排序。
        $hotarticle = $TblStudyAbroadModule->GetLists($WhereHeat, $Offset, 8);

        $Title = $Keyword . ' - 57美国网';
        $Keywords = $Keyword;
        $Description = $Keyword . ',' . $Description;

        // 热门标签
        $KeyWordModule = new TblStudyAbroadKeywordModule();
        $Keyhot = $KeyWordModule->GetLists(' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC', 0, 12);

        include template('StudySearchTag');
    }
    //留学讲堂专题
    public function LectureHall(){
        $TblTopicMessageModule = new TblTopicMessageModule();
        if ($_POST){
            $Data['UserName'] = trim($_POST['username']);
            $Data['Email'] = trim($_POST['mail']);
            $Data['Phone'] = trim($_POST['phone']);
            $Insert = $TblTopicMessageModule->InsertInfo($Data);
            if ($Insert){
                $json_result =  array ('ResultCode' => 200, 'Message' => '提交成功' ) ;
            }else{
                $json_result =  array ('ResultCode' => 100, 'Message' => '提交失败' ) ;
            }
            $EMail = '15659827860';
            $Message ='用户：'.$Data['UserName'].'，用户邮箱：'.$Data['Email'].'用户电话：'.$Data['Phone'];
            ToolService::SendSMSNotice($EMail,$Message);
            echo json_encode ($json_result);exit;
        }
        $Title ='留学讲堂_美国留学讲堂_留学讲师- 57美国网';
        $Keywords ='留学讲堂,美国留学讲堂,留学讲师';
        $Description ='57US留学讲堂，集结了一群专注提供专业留学服务的资深行业人士，为您提供一切留学相关的一站式服务，包括语言培训、院校咨询、申请指导、低领留学......等在内的所有内容。行业大牛、留学名师聚集，每期由不同名师分享不同课题，每周四20:00-21:00（节假日除外），请关注微信群“57US讲堂”。';
        include template('StudyLectureHall');
    }
}
