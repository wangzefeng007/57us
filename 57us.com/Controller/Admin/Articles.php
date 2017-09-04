<?php

/**
 * Class Articles
 * @desc  自定义模块
 */
class Articles
{

    public function __construct()
    {
        IsLogin();
    }
    /**
     * @desc  自定义类别页面列表
     */
    public function CategoriesLists(){
        $TopNavs = 'CategoriesLists';
        $TblArticlesCategoriesModule = new TblArticlesCategoriesModule();
        $ArticlesCategoriesList = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = 0',true);
        if ($ArticlesCategoriesList) {
            foreach ($ArticlesCategoriesList as $key => $value) {
                $CategoryTwo = $TblArticlesCategoriesModule->GetInfoByWhere(' AND ParentCategoryID = '.$value['CategoryID'],true);
                $ArticlesCategoriesList[$key]['Two'] = $CategoryTwo;
                foreach ($CategoryTwo as $k => $val){
                    $CategoryThree = $TblArticlesCategoriesModule->GetInfoByWhere(' AND ParentCategoryID = '.$val['CategoryID'],true);
                    if ($CategoryThree)
                    $ArticlesCategoriesList[$key]['Two'][$k]['Three'] = $CategoryThree;
                }
            }
        }
        include template("ArticlesCategoriesLists");
    }
    /**
     * @desc  自定义页面列表
     */
    public function Lists(){
        $TopNavs = 'Lists';
        $TblArticlesModule = new TblArticlesModule();
        $TblArticlesCategoriesModule = new TblArticlesCategoriesModule();
        $ArticlesLists =  $TblArticlesModule->GetInfoByWhere('',true);
        foreach ($ArticlesLists as $key=>$value){
           $ArticlesCategories = $TblArticlesCategoriesModule->GetInfoByKeyID($value['CategoryID']);
            $ArticlesLists[$key]['CategoryNameThree'] = $ArticlesCategories['CategoryName'];
            $ArticlesLists[$key]['Alias'] = $ArticlesCategories['Alias'];
            if ($ArticlesCategories['ParentCategoryID']>0){
                $ParentCategoriesTwo = $TblArticlesCategoriesModule->GetInfoByKeyID($ArticlesCategories['ParentCategoryID']);
                $ArticlesLists[$key]['CategoryNameTwo'] = $ParentCategoriesTwo['CategoryName'];
                if ($ParentCategoriesTwo['ParentCategoryID']>0){
                    $ParentCategoriesOne = $TblArticlesCategoriesModule->GetInfoByKeyID($ParentCategoriesTwo['ParentCategoryID']);
                    $ArticlesLists[$key]['CategoryNameOne'] = $ParentCategoriesOne['CategoryName'];
                }
            }
        }
        include template("ArticlesLists");
    }
    /**
     * @desc  自定义页面编辑
     */
    public function ArticlesAdd(){
        $TopNavs = 'ArticlesAdd';
        $TblArticlesModule = new TblArticlesModule();
        $TblArticlesCategoriesModule = new TblArticlesCategoriesModule();
        if ($_POST) {
            $ID = $_POST['ArticleID'];
            $Data['Title'] = trim($_POST['Title']);
            $Data['CategoryID'] = trim($_POST['CategoryID']);
            $ArticlesCategories =$TblArticlesCategoriesModule->GetInfoByKeyID($Data['CategoryID']);
            $Data['Content'] = $_POST['Content'.$ArticlesCategories['IsEdit']];
            if ($ID > 0) {
                $IsOk = $TblArticlesModule->UpdateInfoByKeyID($Data, $ID);
            } else {
                $IsOk = $TblArticlesModule->InsertInfo($Data);
                $ID = $IsOk;
            }
            if ($IsOk) {
                alertandgotopage("操作成功", '/index.php?Module=Articles&Action=ArticlesAdd&ID='.$ID);
            } else {
                alertandgotopage("未做修改或添加操作", '/index.php?Module=Articles&Action=ArticlesAdd&ID='.$ID);
            }
        }
        $Category = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = 0',true);
        foreach ($Category as $key=>$value){
            $CategoryTwo = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = '.$value['CategoryID'],true);
            if ($CategoryTwo){
                $Category[$key]['Two'] = $CategoryTwo;
                foreach ($CategoryTwo as  $k=>$val){
                    $CategoryThree = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = '.$val['CategoryID'],true);
                    if ($CategoryThree){
                        $Category[$key]['Two'][$k]['Three'] = $CategoryThree;
                    }
                }
            }
        }

        $ID = $_GET['ID'];
        if ($ID)
            $ArticlesInfo = $TblArticlesModule->GetInfoByKeyID($ID);
        include template("ArticlesAdd");
    }
    /**
     * @desc  自定义类别页面编辑
     */
    public function CategoriesAdd(){
        $TopNavs = 'CategoriesAdd';
        $TblArticlesCategoriesModule = new TblArticlesCategoriesModule();
        if ($_POST) {
            $ID = $_POST['ID'];
            $Data['CategoryName'] = trim($_POST['CategoryName']);
            $Data['ParentCategoryID'] = trim($_POST['ParentCategoryID']);
            $Data['Alias'] = trim($_POST['Alias']);
            $Data['Sort'] = intval($_POST['Sort']);
            $Data['IsEdit'] = intval($_POST['IsEdit']);
            if ($ID){
                $MysqlWhere = ' and CategoryID !='.$ID;
            }
            $ArticlesInfo = $TblArticlesCategoriesModule->GetInfoByWhere("and Alias = '{$Data['Alias']}'".$MysqlWhere);
            if ($ArticlesInfo){
                alertandback("该别名已使用，请重新输入！");
            }
            if ($ID > 0) {
                $IsOk = $TblArticlesCategoriesModule->UpdateInfoByKeyID($Data, $ID);
            } else {
                $IsOk = $TblArticlesCategoriesModule->InsertInfo($Data);
            }
            if ($IsOk) {
                alertandgotopage("操作成功", '/index.php?Module=Articles&Action=CategoriesLists');
            } else {
                alertandgotopage("未做修改或添加操作", '/index.php?Module=Articles&Action=CategoriesLists');
            }
        }
        $ID = $_GET['ID'];
        $Category = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = 0',true);
        foreach ($Category as $key=>$value){
            $CategoryTwo = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = '.$value['CategoryID'],true);
            if ($CategoryTwo){
                $Category[$key]['Two'] = $CategoryTwo;
                foreach ($CategoryTwo as  $k=>$val){
                    $CategoryThree = $TblArticlesCategoriesModule->GetInfoByWhere(' and ParentCategoryID = '.$val['CategoryID'],true);
                    if ($CategoryThree){
                        $Category[$key]['Two'][$k]['Three'] = $CategoryThree;
                    }
                }
            }
        }
        $ArticlesCategoriesInfo = $TblArticlesCategoriesModule->GetInfoByKeyID($ID);
        include template("ArticlesCategoriesAdd");
    }
    /**
     * @desc  自定义页面删除
     */
    public function Delete(){
        $ID = $_REQUEST['ID'];
        if(!empty($ID)){
            $TblArticlesModule = new TblArticlesModule();
            if($TblArticlesModule->DeleteByKeyID($ID)){
                alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);
            }else{
                alertandback('删除失败!');
            }
        }
        else{
            alertandback('您没有选择准备删除的记录!');
        }
    }
    /**
     * @desc  自定义类别删除
     */
    public function CategoriesDelete(){
        $ID = $_REQUEST['ID'];
        if(!empty($ID)){
            $TblArticlesCategoriesModule = new TblArticlesCategoriesModule();
            if($TblArticlesCategoriesModule->DeleteByKeyID($ID)){
                alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);
            }else{
                alertandback('删除失败!');
            }
        }
        else{
            alertandback('您没有选择准备删除的记录!');
        }
    }
}