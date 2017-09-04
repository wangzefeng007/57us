<?php

/**
 * @desc  自定义页面
 * Class About
 */
class About
{

    public function __construct()
    {
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
        $this->TourFootTagF = $TourFootTagModule->GetLists(' and Type=6 order by Sort DESC', 0, 200);
    }

    /**
     * @desc 整站关于我们
     */
    public function Index()
    {
        $Alias = $_GET['Alias'] ? $_GET['Alias'] : 'contacts';
        $CustomPageModule = new TblCustomPageModule();
        $CustomPageInfo = $CustomPageModule->GetInfoByWhere("and Alias = '{$Alias}'");
        include template('AboutIndex');
    }

    /**
     * @desc 旅游模块帮助中心
     */
//    public function TourHelp()
//    {
//        $Alias = $_GET['Alias'] ? $_GET['Alias'] : 'contacts';
//        $CustomPageModule = new TblCustomPageModule();
//        $CustomPageInfo = $CustomPageModule->GetInfoByWhere("and Alias = '{$Alias}'");
//        include template('AboutTourHelp');
//    }

    /**
     * @desc  站点地图
     */
    public function Map(){
        $CustomPageModule = new TblCustomPageModule();
        $CustomPageInfo = $CustomPageModule->GetInfoByWhere("and Alias = 'map'");
        include template('AboutMap');
    }
    /**
     * @desc 帮助中心
     */
    public function Help()
    {
        $Alias = $_GET['Alias'] ? $_GET['Alias'] : 'zhiyin';
        $TblArticlesModule = new TblArticlesModule();
        $TblArticlesCategoriesModule = new TblArticlesCategoriesModule();
        $CategoriesInfo = $TblArticlesCategoriesModule->GetInfoByWhere("and Alias = '{$Alias}'");
        $ParentCategoryID = $this->GetCategory($CategoriesInfo['CategoryID']);
        $ParentCategory = $TblArticlesCategoriesModule->GetInfoByKeyID($ParentCategoryID);
        $ArticlesInfo = $TblArticlesModule->GetInfoByWhere(' and CategoryID ='.$CategoriesInfo['CategoryID'],true);
        foreach ($ArticlesInfo as $K=>$Val)
        {
            $ArticlesInfo[$K]['Content'] = stripcslashes($Val['Content']);
        }
        $Category = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID ='.$ParentCategory['CategoryID'].' order by Sort asc',true);
        foreach ($Category as $key=>$value){
            $CategoryTwo = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = '.$value['CategoryID'].' order by Sort asc',true);
            if ($CategoryTwo){
                $Category[$key]['Two'] = $CategoryTwo;
                foreach ($CategoryTwo as  $k=>$val){
                    $CategoryThree = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = '.$val['CategoryID'].' order by Sort asc',true);
                    if ($CategoryThree){
                        $Category[$key]['Two'][$k]['Three'] = $CategoryThree;
                    }
                }
            }
        }
        if ($ParentCategory['Alias']=='studyhelp'){
            include template('AboutStudyHelp');
        }elseif($ParentCategory['Alias']=='tourhelp'){
            include template('AboutTourHelp');
        }elseif($ParentCategory['Alias']=='quanzhanhelp'){
        include template('AboutIndex');
        }
    }
    private function GetCategory($CategoryID=''){
        $TblArticlesCategoriesModule = new TblArticlesCategoriesModule();
        $CategoriesInfo = $TblArticlesCategoriesModule->GetInfoByKeyID($CategoryID);
        if($CategoriesInfo['ParentCategoryID']>0){
            $CategoriesInfo = $TblArticlesCategoriesModule->GetInfoByKeyID($CategoriesInfo['ParentCategoryID']);
            return $this->GetCategory($CategoriesInfo['CategoryID']);
        }elseif($CategoriesInfo['ParentCategoryID']==0){
            $CategoryID = intval($CategoriesInfo['CategoryID']);
            return $CategoryID;
        }else{
            return 'error';
        }
    }
    
    /**
     * @desc 内容页
     */
    public function HelpInfo()
    {
        $ID = intval($_GET['ID']);
        $TblArticlesModule = new TblArticlesModule();
        $ArticlesInfo = $TblArticlesModule->GetInfoByKeyID($ID);
        include template('HelpInfo');
    }
}
