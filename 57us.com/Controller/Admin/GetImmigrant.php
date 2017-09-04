<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/9/20
 * Time: 10:41
 */
class GetImmigrant
{
    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/News/Class.CaijiUrlAllModule.php';
        include SYSTEM_ROOTPATH . '/Modules/News/Class.CaijiArticleModule.php';
        include SYSTEM_ROOTPATH . '/Modules/News/Class.CaijiColumnUrlModule.php';
    }

    public function Lists()
    {
        $ArticleType = 3;
        $CaijiArticleModule = new CaijiArticleModule();
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblImmigrationCategoryModule.php';
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $ImmigrationCategory = $TblImmigrationCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
        foreach ($ImmigrationCategory as $key => $value) {
            $ParentCategory = $TblImmigrationCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
            $ImmigrationCategory[$key]['Parent'] = $ParentCategory;
        }
        $SqlWhere = " and ArticleType = " . $ArticleType;
        $PageUrl = '';
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
            tourl('/index.php?Module=GetImmigrant&Action=Lists&Page=' . $page . $PageUrl);
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
                $ImmigrationCategory = $TblImmigrationCategoryModule->GetInfoByKeyID($value['CategoryID']);
                $Data['Data'][$key]['CategoryName'] = $ImmigrationCategory['CategoryName'];
            }
            $PageMax = $Data['PageCount'];
            MultiPage($Data, 10);
        }
        $TopNavs = 'CaijiImmigrantList';
        include template("CaijiImmigrantList");
    }

    public function Edit()
    {
        $CaijiArticleModule = new CaijiArticleModule();
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblImmigrationCategoryModule.php';
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $ImmigrationCategory = $TblImmigrationCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
        foreach ($ImmigrationCategory as $key => $value) {
            $ParentCategory = $TblImmigrationCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
            $ImmigrationCategory[$key]['Parent'] = $ParentCategory;
        }
        $ArticleID = trim($_GET ['ArticleID']);
        $ArticleInfo = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
        $ArticleInfo['Content'] = StrReplaceImages($ArticleInfo['Content']);
        $ArticleInfo['Content'] = DoEditorContent($ArticleInfo['Content']);
        if ($_POST) {
            include SYSTEM_ROOTPATH . '/Modules/News/Class.TblImmigrationModule.php';
            $TblImmigrationModule = new TblImmigrationModule();
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
            if($submit1){
                unset($Data['Sort'],$Data['IndexRecommend'],$Data['TopicRecommend']);
                if(isset($Data['Image'])){
                    $ArticleInfo = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
                    if($ArticleInfo['Image']){
                        DelFromImgServ($ArticleInfo['Image']);
                    }
                }
                $Data['UpdateTime'] = date('Y-m-d H:i:s',$now_time);
                $result=$CaijiArticleModule->UpdateInfoByKeyID($Data,$ArticleID);
                if($result){
                    alertandgotopage('保存成功!',"/index.php?Module=GetImmigrant&Action=Edit&ArticleID=$ArticleID");
                }else{
                    alertandback('操作未修改，请重新编辑');
                }
            }elseif($submit2){
                $Info = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
                $Data ['Image'] = $Info['Image'];
                $Data['UpdateTime'] = date('Y-m-d H:i:s',$now_time);
                $Data['AddTime'] = date('Y-m-d H:i:s',$now_time);
                $Data['ViewCount'] = 0;
                $result=$TblImmigrationModule->InsertInfo($Data);
                if ($result){
                    $Delete = $CaijiArticleModule->DeleteByKeyID($ArticleID);
                }
                if($Delete){
                    alertandgotopage( '保存并提交成功!', "/index.php?Module=GetImmigrant&Action=Lists");
                }else{
                    alertandback( '保存并提交失败,请重新编辑.' );
                }
            }
        }
        include template("CaijiImmigrantEdit");
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
}