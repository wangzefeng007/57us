<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/9/20
 * Time: 10:40
 */
class GetStudyAbroad
{
    public function __construct()
    {
        IsLogin();
    }

    public function Index()
    {
    }

    public function Lists()
    {
        $ArticleType = 1;
        $CaijiArticleModule = new CaijiArticleModule();
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadCategoryModule.php';
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $StudyCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 50);
        foreach ($StudyCategory as $key => $value) {
            $ParentCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 50);
            $StudyCategory[$key]['Parent'] = $ParentCategory;
            foreach ($ParentCategory as $K => $Val) {
                $TwoCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = ' . $Val['CategoryID'], 0, 50);
                $StudyCategory[$key]['Parent'][$K]['TwoCategory'] = $TwoCategory;
            }
        }
        $SqlWhere = " and ArticleType = " . $ArticleType;
        $PageUrl = '';
        $Title = trim($_GET ['Title']);
        if ($Title) {
            $SqlWhere .= " and Title like '%$Title%'";
            $PageUrl .= "&Title=$Title";
        }
        $AddTime = trim($_GET ['AddTime']);
        if ($AddTime) {
            $SqlWhere .= " and AddTime like '%$AddTime%'";
            $PageUrl .= "&AddTime=$AddTime";
        }
        $CategoryID = intval($_GET ['CategoryID']);
        if ($CategoryID) {
            $SqlWhere .= " and CategoryID = " . $CategoryID;
            $PageUrl .= "&CategoryID=$CategoryID";
        }
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=GetStudyAbroad&Action=Lists&Page=' . $page . $PageUrl);
        }
        // 分页开始
        $Page = intval($_GET ['Page']);
        $Page = intval($Page) ? intval($Page) : 1;
        $PageSize = 10;
        $Rscount = $CaijiArticleModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $CaijiArticleModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                $StudyAbroadCategory = $TblStudyAbroadCategoryModule->GetInfoByKeyID($value['CategoryID']);
                $Data['Data'][$key]['CategoryName'] = $StudyAbroadCategory['CategoryName'];
            }
            $PageMax = $Data['PageCount'];
            MultiPage($Data, 10);

        }
        $TopNavs = 'CaijiStudyList';
        include template("CaijiStudyList");
    }

    public function Edit()
    {
        $CaijiArticleModule = new CaijiArticleModule();
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadCategoryModule.php';
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $StudyCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 50);
        foreach ($StudyCategory as $key => $value) {
            $ParentCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 50);
            $StudyCategory[$key]['Parent'] = $ParentCategory;
            foreach ($ParentCategory as $K => $Val) {
                $TwoCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = ' . $Val['CategoryID'], 0, 50);
                $StudyCategory[$key]['Parent'][$K]['TwoCategory'] = $TwoCategory;
            }
        }
        $ArticleID = trim($_GET ['ArticleID']);
        $ArticleInfo = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
        $ArticleInfo['Content'] = StrReplaceImages($ArticleInfo['Content']);
        $ArticleInfo['Content'] = DoEditorContent($ArticleInfo['Content']);
        if ($_POST) {
            include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadModule.php';
            $TblStudyAbroadModule = new TblStudyAbroadModule();
            $submit1 = $_POST['submit1'];
            $submit2 = $_POST['submit2'];
            $ArticleID = intval($_POST['ArticleID']);
            $Data['CategoryID'] = intval($_POST['CategoryID']);
            $Data['Title'] = trim($_POST['Title']);
            if (empty($Data['Title'])) {
                JsMessage('文章保存失败,标题不能为空.');
            }
            $Keywords = $_POST['Keywords'];
            foreach ($Keywords as $key=>$value){
                $In .= $value.',';
            }
            $In = substr($In, 0, -1);
            $Data['Keywords']=$In;
            $Data['Description'] = trim($_POST['Description']);
            $Data['SeoTitle'] = str_replace(array('，', ','), array('_', '_'), trim($_POST['SeoTitle']));
            $Data['SeoKeywords'] = trim(str_replace(array('，'), array(','), trim($_POST['SeoKeywords'])), ',');
            $Data['SeoDescription'] = trim($_POST['SeoDescription']);
            //文本图片处理-----------------------------------------------------------------------------
            $Data['Content'] = $_POST['Content'];
            $Pattern = array();
            $Replacement = array();
            $ImgArr = Array();
            preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($Data['Content']), $ImgArr);
            if (count($ImgArr[0])) {
                foreach ($ImgArr[0] as $Key => $ImgTag) {
                    $Pattern[] = $ImgTag;
                    $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array('/title=".*"/iU', '/alt=".*"/iU'), '', $ImgTag));
                }
            }
            $Data['Content'] = addslashes(str_replace($Pattern, $Replacement, stripcslashes($Data['Content'])));
            //文本图片处理-------------------------------------------------------------------------------
            $Data['Sort'] = trim($_POST['Sort']);
            $Data['ComeFrom'] = trim($_POST['ComeFrom']);
            $Data['Redactor'] = trim($_POST['Redactor']);
            $Data['IndexRecommend'] = intval($_POST['IndexRecommend']);
            $Data['TopicRecommend'] = intval($_POST['TopicRecommend']);
            //上传图片
            include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
            if ($_FILES['Image']['size'][0] > 0) {
                $Upload = new MultiUpload ('Image');
                $File = $Upload->upload();
                $Picture = $File[0] ? $File[0] : '';
                $Data ['Image'] = $Picture;
            }
            $now_time = time();
            if ($submit1) {
                unset($Data['Sort'],$Data['IndexRecommend'],$Data['TopicRecommend']);
                if (isset($Data['Image'])) {
                    $ArticleInfo = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
                    if ($ArticleInfo['Image']) {
                        DelFromImgServ($ArticleInfo['Image']);
                    }
                }
                $Data['UpdateTime'] = date('Y-m-d H:i:s', $now_time);
                $result = $CaijiArticleModule->UpdateInfoByKeyID($Data, $ArticleID);
                if ($result) {
                    alertandgotopage('保存成功!', "/index.php?Module=GetStudyAbroad&Action=Edit&ArticleID=$ArticleID");
                } else {
                    alertandback('操作未修改，请重新编辑');
                }
            } elseif($submit2) {
                $Info = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
                $Data ['Image'] = $Info['Image'];
                $Data['UpdateTime'] = date('Y-m-d H:i:s', $now_time);
                $Data['AddTime'] = date('Y-m-d H:i:s', $now_time);
                $Data['ViewCount'] = 0;
                $result = $TblStudyAbroadModule->InsertInfo($Data);
                if ($result){
                    $Delete = $CaijiArticleModule->DeleteByKeyID($ArticleID);
                }
                if ($Delete) {
                    alertandgotopage('保存并提交成功!', "/index.php?Module=GetStudyAbroad&Action=Lists");
                } else {
                    alertandback('保存并提交失败,请重新编辑.');
                }
            }

        }
        include template("CaijiStudyEdit");
    }

    public function Delete()
    {
        $CaijiArticleModule = new CaijiArticleModule();
        $ArticleIDs = '';
        if ($_POST) {
            $ArticleID = $_POST['ArticleID'];
            foreach ($ArticleID as $value) {
                $ArticleIDs .= $value . ',';
            }
            $ArticleIDs = substr($ArticleIDs, 0, -1);
            if (!$ArticleIDs){
                alertandback("删除未选中");
            }
            $Delete = $CaijiArticleModule->DeleteByWhere(' and ArticleID in(' . $ArticleIDs . ')');
            if ($Delete) {
                alertandback("删除成功");
            } else {
                alertandback("删除失败");
            }
        }
    }
    //==================================采集数据整理======================================//
    public function Caijizhengli(){
        include SYSTEM_ROOTPATH . '/Modules/CaiJi/Class.CaiJiTblImmigratModule.php';
        include SYSTEM_ROOTPATH . '/Modules/CaiJi/Class.CaijiTblStudyAbroadModule.php';
        include SYSTEM_ROOTPATH . '/Modules/CaiJi/Class.CaiJiTblTourModule.php';
        $CaiJiTblImmigratModule = new CaiJiTblImmigratModule();
        $CaijiTblStudyAbroadModule = new CaijiTblStudyAbroadModule();
        $CaiJiTblTourModule = new CaiJiTblTourModule();
        $CaijiArticleModule = new CaijiArticleModule();
        for ($I = 1; $I < 12827; $I++){
            $TblImmigrat = $CaiJiTblImmigratModule->GetInfoByKeyID($I);
            if ($TblImmigrat){
                $Data['ArticleType'] = 3;
                $Data['Title']  = addslashes($TblImmigrat['Title']);
                $Data['Content']  =addslashes($TblImmigrat['Content']);
                $Data['AddTime'] = $TblImmigrat['AddTime'];
                $Data['Description'] = $TblImmigrat['Description'];
                $CaijiArticleModule->InsertInfo($Data);
            }
        }
        for ($a = 1; $a < 7400; $a++){
            $TblStudyAbroad = $CaijiTblStudyAbroadModule->GetInfoByKeyID($a);
            if ($TblStudyAbroad){
                $Data['ArticleType'] = 1;
                $Data['Title']  = addslashes($TblStudyAbroad['Title']);
                $Data['Content']  =addslashes($TblStudyAbroad['Content']);
                $Data['AddTime'] = $TblStudyAbroad['AddTime'];
                $Data['Description'] = $TblStudyAbroad['Description'];
                $CaijiArticleModule->InsertInfo($Data);
            }
        }
        for ($b = 1; $b < 6788; $b++){
            $TblTour = $CaiJiTblTourModule->GetInfoByKeyID($b);
            if ($TblTour){
                $Data['ArticleType'] = 2;
                $Data['Title']  = addslashes($TblTour['Title']);
                $Data['Content']  =addslashes($TblTour['Content']);
                $Data['AddTime'] = $TblTour['AddTime'];
                $Data['Description'] = $TblTour['Description'];
                $CaijiArticleModule->InsertInfo($Data);
            }
        }
    }
    //==================================采集数据整理======================================//
}