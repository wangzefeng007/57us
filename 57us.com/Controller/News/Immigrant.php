<?php
class immigrant{
    public function __construct(){
        $this->SoType = 'immigrant';
        /*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC',0,200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC',0,200);
    }
    /**
     * @desc  移民标签搜索_列表页
     * Author Zf
     */
    public function ImmigSearchTag(){
        $TblImmigrationModule = new TblImmigrationModule();
        $KeywordModule = new TblImmigrationKeywordModule();
        $Offset = 0;
        $Keyword = $_GET['Keyword'];
        $Immig = $KeywordModule->GetInfoByWhere(" and  `Keyword` = '".$Keyword.'\'');
        //跳转到的标签搜索页
        $GoPageUrl ='/immigrant/tags_'.$Keyword;
        $MysqlWhere = ' and  MATCH(`Keywords`) AGAINST ('.$Immig['KeyID'].' IN BOOLEAN MODE)';//搜索相关标签的数据
        $page = intval($_GET['page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 10;
        $Rscount = $TblImmigrationModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($page, $Data ['PageCount']);
            $Offset = ($page - 1) * $Data ['PageSize'];
            if ($page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data['Data'] = $TblImmigrationModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                if($key == 0){
                    $Description = $value['Description'];
                }
                $Keywords = $KeywordModule->ToKeywordName($value['Keywords']);
                $Keywords = explode(',', $Keywords);
                $Data['Data'][$key]['Keywords'] = $Keywords;
            }
        }
        $Page = new Page($Rscount ['Num'], $PageSize,1);
        //分页类
        $listpage = $Page->showpage();
        // 该标签下的最新文章抽取8条数据。
        $WhereNewest = ' and Image != \'\' order by AddTime DESC'; //按照时间排序。
        $TimeArticle = $TblImmigrationModule->GetLists($WhereNewest,$Offset,8);
        // 该标签下的最热文章抽取8条数据。
        $WhereHeat = ' order by ViewCount DESC'; //按照阅读量排序。
        $hotarticle = $TblImmigrationModule->GetLists($WhereHeat,$Offset,8);
        $WhereKeyhot = ' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC';
        $Keyhot = $KeywordModule->GetLists($WhereKeyhot,$Offset,12);
        $Title = $Keyword . ' - 57美国网';
        $Keywords = $Keyword;
        $Description = $Keyword . ',' . $Description;
        //移民专题广告
        $TopicTour = NewsGetAdInfo('immigrant_topic');
        include template ( 'ImmigSearchTag' );
    }
    /**
     * @desc  移民_专题首页
     */
    public function NewsImmig(){
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $TblImmigrationModule = new TblImmigrationModule();
        //移民专题广告
        $TopicTour = NewsGetAdInfo('immigrant_topic');
        //移民类别  分类ID:1126 , 生活指南  分类ID:1131  移民攻略  分类ID:1138  投资房产   分类ID:1143
        $AllInfo = $TblImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = 0 order by DisplayOrder asc  ', true);
        foreach ($AllInfo as $key => $val) {
            $Two = $TblImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $val['CategoryID'] . ' order by DisplayOrder asc  ', true);
            foreach ($Two as $k => $v) {
                //获取移民子类的类别
                $Three = $TblImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = ' . $v['CategoryID'] . ' order by DisplayOrder asc  ', true);
                if ($Three) {
                    foreach ($Three as $k1 => $v1) {
                        $In = $v['CategoryID'];
                        $In .= ',' . $v1['CategoryID'];
                        //移民子类的热门专题页的数据（获取2条数据）
                        $Two[$k]['NewsTopic'] = $TblImmigrationModule->GetLists(' and TopicRecommend = 1 and CategoryID in (' . $In . ') ', 0, 2);
                        $ASqlWhere ='';
                        foreach ($Two[$k]['NewsTopic'] as $KeyA => $ValueA)
                        {
                            $ASqlWhere .= ' and ImmigrationID != '.$ValueA['ImmigrationID'];
                        }
                        //移民子类的数据（获取8条数据）
                        $Two[$k]['News'] = $TblImmigrationModule->GetLists($ASqlWhere.' and CategoryID in (' . $In . ') ', 0, 8);
                    }
                } else {
                    $Two[$k]['NewsTopic'] = $TblImmigrationModule->GetLists(' and TopicRecommend = 1 and CategoryID = ' . $v['CategoryID'], 0, 2);
                    $BSqlWhere ='';
                    foreach ($Two[$k]['NewsTopic'] as $KeyB => $ValueB)
                    {
                        $BSqlWhere .= ' and ImmigrationID != '.$ValueB['ImmigrationID'];
                    }
                    $Two[$k]['News'] = $TblImmigrationModule->GetLists($BSqlWhere.' and CategoryID = ' . $v['CategoryID'], 0, 8);
                }
            }
            $AllInfo[$key]['Two'] = $Two;
        }
        $TblImmigrationKeywordModule=new TblImmigrationKeywordModule();
        //移民专题页广告调用（热门标签、热门文章）
        $WhereKeyhot = ' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC';
        $Keyhot = $TblImmigrationKeywordModule->GetLists($WhereKeyhot,$Offset,12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();        
        //热门文章
        //$WhereTourhot = ' order by ViewCount DESC';
        $Tourhot1 = $TblImmigrationModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$TblImmigrationModule->GetListsNum('')['Num'];
        $Tourhot2 = $TblImmigrationModule->GetLists(' order by AddTime desc',mt_rand(3,$Index-1),7);
        $Tourhot = array_merge($Tourhot1,$Tourhot2);  
        //猜你喜欢
        $TourLike = $TblImmigrationModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-1),6);        
        $Title = '美国移民_美国投资移民_美国移民条件_美国移民政策 - 57美国网';
        $Keywords = '美国移民,美国投资移民,美国移民条件,美国移民政策,美国移民中介,投资移民美国,美国买房移民,美国购房移民,美国移民生活,美国亲属移民,移民美国需要什么条件,美国移民排期,美国移民排表,美国移民签证,美国技术移民,如何移民美国,美国移民费用,移民美国多少钱,美国移民中介';
        $Description ='57美国网移民频道，给您提供美国移民条件、移民政策、移民生活、移民文化、技术移民、投资移民、移民费用、美国就业机会、医疗分布、法律法规、投资指南、房产资讯等移民信息,57美国网拥有最完善最成熟最全面的移民服务系统为客户创造价值，实现美国移民愿景。';
        include template ('NewsImmig');
    }

    /**
     * @desc  移民_专题页(移民类别、生活指南、移民攻略、投资房产)
     * Author Zf
     */

    public function NewsImmigTopic(){
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $TblImmigrationModule = new TblImmigrationModule();
        $ListURL = '/immigration/newsimmiglist';
        $DetailURL = '/immigration/newsimmigdetail';//详细页URL
        $list =array();
        //移民类别 分类ID:1126  genre 生活指南 分类ID:1131 guide 移民攻略 分类ID:1138 way 投资房产 分类ID:1143 house
        $Alias = $_GET['Alias'];
        $Category = $TblImmigrationCategoryModule->GetInfoByWhere(' and Alias = \'' .$Alias.'\' ');
        $CategoryID = $Category['CategoryID'];
        $info = $TblImmigrationCategoryModule->GetInfoByKeyID($CategoryID);
        $Immigration = $TblImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = '.$CategoryID,true);
        foreach ($Immigration as $key=>$value){
            $Immigration[$key]['key'] = $key+1;
            $Data = $TblImmigrationModule->GetInfoByWhere(' and  Image!=\'\' and CategoryID = '.$value['CategoryID'].' order by AddTime desc limit 7', true);
            $Immigration[$key]['info'] = $Data;
            foreach ($Immigration[$key]['info'] as $K=>$V){
                $Immigration[$key]['info'][$K]['key'] = $K+1;
                $AddTime= strtotime($V['AddTime']);
                $Immigration[$key]['info'][$K]['time'] = date('Y-m-d',$AddTime);
                $Immigration[$key]['info'][$K]['Description']  = trim(strip_tags(_substr($V['Description'], 60)));
            }
        }
        $Title = $Category ['SeoTitle']?$Category ['SeoTitle'].' - 57美国网': $Category ['CategoryName'];
        $Keywords = $Category ['SeoKeywords'];
        $Description = $Category['SeoDescription'];
        include template ('NewsImmigTopic');
    }

    /**
     * @desc  移民_列表页
     * Author Zf
     */
    public function NewsImmigList(){
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $TblImmigrationKeywordModule = new TblImmigrationKeywordModule();
        $TblImmigrationModule = new TblImmigrationModule();
        $Alias = $_GET['Alias'];
        $Category = $TblImmigrationCategoryModule->GetInfoByWhere(' and Alias = \'' .$Alias.'\' ');
        $ParentCategory = $TblImmigrationCategoryModule->GetInfoByKeyID($Category['ParentCategoryID']);
        $GoPageUrl = '/immigrant_'.$Category['Alias'];
        $CategoryID = $Category['CategoryID'];
        //移民专题头部文章调用
        $HeadWhere = ' and SetCategoryTop = 1';//分类头条(1-推荐)
        $HeadList = $TblImmigrationModule->GetLists($HeadWhere,0,3);
        foreach ($HeadList as $key=>$value) {
            $HeadList[$key]['key'] = $key;
        }
        //移民专题页列表start
        $MysqlWhere = ' and CategoryID = '.$CategoryID;//专题类别 技术移民1126
        $Offset =0;
        $MysqlWhere .=' and Image != \'\' order by AddTime DESC';                            //列表推荐SetListRecommend
        $Info = $TblImmigrationCategoryModule->GetInfoByKeyID($CategoryID);//调取类别名称
        $page = $_GET['page']?intval($_GET['page']):1;
        if ($page < 1) {
            $page = 1;
        }
        $pageSize = 10;
        $Rscount = $TblImmigrationModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($pageSize ? $pageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $pageSize);
            $Data ['Page'] = min($page, $Data ['PageCount']);
            $Offset = ($page - 1) * $Data ['PageSize'];
            if ($page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data['Data'] = $TblImmigrationModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data['Data'] as $key=>$value){
                $Keyword =  $TblImmigrationKeywordModule->ToKeywordName($value['Keywords']);
                $Keyword = explode(',', $Keyword);
                $Data['Data'][$key]['Keywords'] = $Keyword;
                $AddTime= strtotime($value['AddTime']);
                $Data['Data'][$key]['AddTime'] = date('Y-m-d',$AddTime);
            }
            $Page = new Page($Rscount ['Num'],$pageSize);
            $listpage = $Page->showpage();
        }
        $MyUrl = '/index.php?Module=Immigration&Action=NewsImmigList';
        //移民专题页列表end
        unset($Keyword);
        //移民专题页广告调用（热门标签、热门文章）
        $WhereKeyhot = ' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC';
        $Keyhot = $TblImmigrationKeywordModule->GetLists($WhereKeyhot,$Offset,12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();        
        //热门文章
        //$WhereTourhot = ' order by ViewCount DESC';
        $Tourhot1 = $TblImmigrationModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$TblImmigrationModule->GetListsNum('')['Num'];
        $Tourhot2 = $TblImmigrationModule->GetLists(' order by AddTime desc',mt_rand(3,$Index-1),7);
        $Tourhot = array_merge($Tourhot1,$Tourhot2);  
        //猜你喜欢
        $TourLike = $TblImmigrationModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-1),6);
        //移民专题广告
        $TopicTour = NewsGetAdInfo('immigrant_topic');
        $Title = $Category ['SeoTitle']?$Category ['SeoTitle'] . ' - 57美国网':$Category ['Title'] . ' - 57美国网';
        $Keywords = $Category ['SeoKeywords']?$Category ['SeoKeywords']:$Category ['Title'];
        $Description = $Category ['SeoDescription']?$Category ['SeoDescription']:'57美国网（57us.com）给您提供' . $Category ['Title'] . ',' . $Category ['Description'];
        include template ( 'NewsImmigList' );
    }

    /**
     * @desc  移民_详情页
     * Author Zf
     */
    public function NewsImmigDetail(){
        $ID = $_GET['ID'];
        $Offset =0;
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $TblImmigrationKeywordModule = new TblImmigrationKeywordModule();
        $TblImmigrationModule = new TblImmigrationModule();
        $TagsModule=new TblTagsModule();
        $list = $TblImmigrationModule->GetInfoByKeyID($ID);
        $list['Content'] = StrReplaceImages($TagsModule->TiHuan($list['Content']),$list['Title']);
        
        //增加阅读量
        $TblImmigrationModule->UpdateViewCount($ID);
        $Info = $TblImmigrationCategoryModule->GetInfoByKeyID($list['CategoryID']);
        //通过父类ID获取子类别类目
        $ParentCategory = $TblImmigrationCategoryModule->GetInfoByKeyID($Info['ParentCategoryID']);
        $Keyword =  $TblImmigrationKeywordModule->ToKeywordName($list['Keywords']);
        if ($Keyword){
            $list['Keywords'] = explode(',', $Keyword);
        }
        //相关阅读
        $Correlations = '';
        foreach($Keyword as $key=>$val){
            $Correlations.=$val['Keyword'].',';
        }
        $Correlations = substr($Correlations,0,strlen($Correlations)-1);
        $CorrelationNews = $TblImmigrationModule->GetInfoByKeyID($Correlations);
        if(!count($CorrelationNews)|| $CorrelationNews==0){
            $CorrelationNews = $TblImmigrationModule->GetLists(' and ImmigrationID <> '.$ID.' and Image!=\'\' and CategoryID = '.$Info['CategoryID'],0,8);
        }

        //旅游专题页广告调用（热门标签、热门文章、最新文章）

        $WhereKeyhot = ' and length(`Keyword`)<13 and length(`Keyword`)>5 order by Traffic DESC';
        $Keyhot = $TblImmigrationKeywordModule->GetLists($WhereKeyhot,$Offset,12);
        //标签云
        $TagsCloudList=NewsService::GetTagsCloudList();        
        //热门文章
        //$WhereTourhot = ' order by ViewCount DESC';
        $Tourhot1 = $TblImmigrationModule->GetLists(' order by AddTime desc', 0,3);
        $Index=$TblImmigrationModule->GetListsNum('')['Num'];
        $Tourhot2 = $TblImmigrationModule->GetLists(' order by AddTime desc',mt_rand(3,$Index-1),7);
        $Tourhot = array_merge($Tourhot1,$Tourhot2);  
        //猜你喜欢
        $TourLike = $TblImmigrationModule->GetLists(' order by AddTime desc',mt_rand(0,$Index-1),6);
        //最新文章8条数据
        $WhereTourTime = ' and Image != \'\' order by AddTime DESC';
        $TourTime = $TblImmigrationModule->GetLists($WhereTourTime,$Offset,8);
        unset($Keyword);
        //上一篇下一篇文章
        $PrveSql = ' and CategoryID = '.$Info['CategoryID'].' and ImmigrationID > '.$ID .' order by ImmigrationID asc';
        $prev = $TblImmigrationModule->GetInfoByWhere($PrveSql);
        $NextSql = ' and CategoryID = '.$Info['CategoryID'].' and ImmigrationID < '.$ID .' order by ImmigrationID desc';
        $next = $TblImmigrationModule->GetInfoByWhere($NextSql);
        //移民专题广告
        $TopicTour = NewsGetAdInfo('immigrant_topic');
        
        $Title = $list ['SeoTitle']?$list ['SeoTitle'] . ' - 57美国网':$list ['Title'] . ' - 57美国网';
        $Keywords = $list ['SeoKeywords']?$list ['SeoKeywords']:$list ['Title'];
        $Description = $list ['SeoDescription']?$list ['SeoDescription']:'57美国网（57us.com）给您提供' . $list ['Title'] . ',' . $list ['Description'];
        
        include template ( 'NewsImmigDetail' );
    }
}
