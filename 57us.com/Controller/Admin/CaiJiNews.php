<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/9/23
 * Time: 16:43
 */
class CaiJiNews
{

    public function __construct()
    {
        IsLogin();
    }

    public function Index()
    {}

    public function Lists()
    {
        $ArticleType = intval($_GET['Type']);
        $CaijiArticleModule = new CaijiArticleModule();
        if ($ArticleType == 1) {
            $TblCategoryModule = new TblStudyAbroadCategoryModule();
            $CategoryLists = $TblCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 50);
            foreach ($CategoryLists as $key => $value) {
                $ParentCategory = $TblCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 50);
                $CategoryLists[$key]['Parent'] = $ParentCategory;
                foreach ($ParentCategory as $K => $Val) {
                    $TwoCategory = $TblCategoryModule->GetLists(' and ParentCategoryID = ' . $Val['CategoryID'], 0, 50);
                    $CategoryLists[$key]['Parent'][$K]['TwoCategory'] = $TwoCategory;
                }
            }
        } elseif ($ArticleType == 2) {
            $TblCategoryModule = new TblTourCategoryModule();
            $CategoryLists = $TblCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
            foreach ($CategoryLists as $key => $value) {
                $ParentCategory = $TblCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
                $CategoryLists[$key]['Parent'] = $ParentCategory;
            }
        } elseif ($ArticleType == 3) {
            $TblCategoryModule = new TblImmigrationCategoryModule();
            $CategoryLists = $TblCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
            foreach ($CategoryLists as $key => $value) {
                $ParentCategory = $TblCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
                $CategoryLists[$key]['Parent'] = $ParentCategory;
            }
        }
        
        $SqlWhere = "  and IsAdd=0 and ArticleType = " . $ArticleType;
        $PageUrl = '';
        $Title = trim($_GET['Title']);
        if ($Title) {
            $SqlWhere .= " and Title like '%$Title%'";
            $PageUrl .= "&Title=$Title";
        }
        $AddTime = trim($_GET['AddTime']);
        if ($AddTime) {
            $SqlWhere .= " and AddTime like '%$AddTime%'";
            $PageUrl .= "&AddTime=$AddTime";
        }
        $CategoryID = intval($_GET['CategoryID']);
        if ($CategoryID) {
            $SqlWhere .= " and CategoryID = " . $CategoryID;
            $PageUrl .= "&CategoryID=$CategoryID";
        }
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            if ($ArticleType == 1) {
                tourl('/index.php?Module=GetStudyAbroad&Action=Lists&Page=' . $page . $PageUrl);
            } elseif ($ArticleType == 2) {
                tourl('/index.php?Module=GetTour&Action=Lists&Page=' . $page . $PageUrl);
            } elseif ($ArticleType == 3) {
                tourl('/index.php?Module=GetImmigrant&Action=Lists&Page=' . $page . $PageUrl);
            }
        }
        // 分页开始
        $Page = intval($_GET['Page']);
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
                $StudyAbroadCategory = $TblCategoryModule->GetInfoByKeyID($value['CategoryID']);
                $Data['Data'][$key]['CategoryName'] = $StudyAbroadCategory['CategoryName'];
            }
            $PageMax = $Data['PageCount'];
            MultiPage($Data, 10);
        }
        $TopNavs = 'CaijiNewsLists';
        include template("CaijiNewsLists");
    }

    /*
     * 采集入库的文章汇总
     */
    public function Logs()
    {
        $ArticleType = 'Log';
        $SqlWhere = "";
        $PageUrl = '';
        
        $StartTime = trim($_GET['StartTime']);
        $EndTime = trim($_GET['EndTime']);
        if ($StartTime != '') {
            $AddTimeStart = strtotime($StartTime . ' 00:00:00');
            $SqlWhere .= " and AddTime > " . $AddTimeStart;
            $PageUrl .= "&StartTime=$StartTime";
        }
        if ($EndTime != '') {
            $AddTimeEND = strtotime($EndTime . ' 23:59:59');
            $SqlWhere .= " and AddTime < " . $AddTimeEND;
            $PageUrl .= "&EndTime=$EndTime";
        }
        $Type = trim($_GET['Type']);
        if ($Type != '') {
            $SqlWhere .= " and Type = " . $Type;
            $PageUrl .= "&Type=$Type";
        }
        $CaijiUrlLogModule = new CaijiUrlLogModule();
        // 分页开始
        $Page = intval($_GET['Page']);
        $Page = intval($Page) ? intval($Page) : 1;
        $PageSize = 10;
        $Rscount = $CaijiUrlLogModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $CaijiUrlLogModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $Key => $Value) {
                if (strstr($Value['MyUrl'], 'immigrant')) {
                    $ID = str_replace("http://www.57us.com/immigrant/", "", $Value['MyUrl']);
                    $ID = str_replace(".html", "", $ID);
                    $Data['Data'][$Key]['EditUrl'] = '/index.php?Module=ImmigrantNewsArticle&Action=Add&ImmigrationID=' . $ID;
                } elseif (strstr($Value['MyUrl'], 'tour')) {
                    $ID = str_replace("http://www.57us.com/tour/", "", $Value['MyUrl']);
                    $ID = str_replace(".html", "", $ID);
                    $Data['Data'][$Key]['EditUrl'] = '/index.php?Module=TourNewsArticle&Action=Add&TourID=' . $ID;
                } elseif (strstr($Value['MyUrl'], 'study')) {
                    $ID = str_replace("http://www.57us.com/study/", "", $Value['MyUrl']);
                    $ID = str_replace(".html", "", $ID);
                    $Data['Data'][$Key]['EditUrl'] = '/index.php?Module=StudyAbroadNewsArticle&Action=Add&StudyID=' . $ID;
                }
            }
            $PageMax = $Data['PageCount'];
            MultiPage($Data, 10);
        }
        include template("CaijiLogLists");
    }

    /*
     * 删除采集入库的文章
     */
    public function DeleteLogs()
    {
        $LogID = intval($_GET['ID']);
        $CaijiUrlLogModule = new CaijiUrlLogModule();
        $CaijiUrlLogInfo = $CaijiUrlLogModule->GetInfoByKeyID($LogID);
        if ($CaijiUrlLogInfo) {
            if ($CaijiUrlLogInfo['Type']==3) {
                //留学
                $ID = str_replace("http://www.57us.com/immigrant/", "", $CaijiUrlLogInfo['MyUrl']);
                $ID = str_replace(".html", "", $ID);
                file_get_contents(WEB_ADMIN_URL."/index.php?Module=ImmigrantNewsArticle&Action=Delete&ImmigrationID=".$ID);
            } elseif ($CaijiUrlLogInfo['Type']==2) {
                $ID = str_replace("http://www.57us.com/tour/", "", $CaijiUrlLogInfo['MyUrl']);
                $ID = str_replace(".html", "", $ID);
                file_get_contents(WEB_ADMIN_URL."/index.php?Module=TourNewsArticle&Action=Delete&TourID=".$ID);
            } elseif ($CaijiUrlLogInfo['Type']==1) {
                $ID = str_replace("http://www.57us.com/study/", "", $CaijiUrlLogInfo['MyUrl']);
                $ID = str_replace(".html", "", $ID);
                file_get_contents(WEB_ADMIN_URL."/index.php?Module=StudyAbroadNewsArticle&Action=Delete&StudyID=".$ID);
            }
            $IsOk = $CaijiUrlLogModule->DeleteByKeyID($LogID);
            if ($IsOk)
            {
                alertandgotopage("删除成功！",$_SERVER['HTTP_REFERER']);
            }
            else 
            {
                alertandgotopage("删除失败！",$_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function Edit()
    {
        $ArticleType = intval($_GET['Type']);
        $CaijiArticleModule = new CaijiArticleModule();
        $ArticleID = trim($_GET['ArticleID']);
        $ArticleInfo = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
        $ArticleInfo['Content'] = StrReplaceImages($ArticleInfo['Content']);
        $ArticleInfo['Content'] = DoEditorContent($ArticleInfo['Content']);
        if ($ArticleType == 1) {
            $TblModule = new TblStudyAbroadModule();
            $KeywordModule = new TblStudyAbroadKeywordModule();
            $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
            $KeywordInfo = $KeywordModule->GetInfoByWhere('', true);
            foreach ($KeywordInfo as $key => $value) {
                $in = strstr($ArticleInfo['Content'], $value['Keyword']);
                if ($in) {
                    $Data[$key]['KeyID'] = $value['KeyID'];
                    $Data[$key]['Keyword'] = $value['Keyword'];
                }
            }
            $Category = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 50);
            foreach ($Category as $key => $value) {
                $ParentCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 50);
                $Category[$key]['Parent'] = $ParentCategory;
                foreach ($ParentCategory as $K => $Val) {
                    $TwoCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = ' . $Val['CategoryID'], 0, 50);
                    $Category[$key]['Parent'][$K]['TwoCategory'] = $TwoCategory;
                }
            }
        } elseif ($ArticleType == 2) {
            $TblModule = new TblTourModule();
            $KeywordModule = new TblTourKeywordModule();
            $TblTourCategoryModule = new TblTourCategoryModule();
            $KeywordInfo = $KeywordModule->GetInfoByWhere('', true);
            foreach ($KeywordInfo as $key => $value) {
                $in = strstr($ArticleInfo['Content'], $value['Keyword']);
                if ($in) {
                    $Data[$key]['KeyID'] = $value['KeyID'];
                    $Data[$key]['Keyword'] = $value['Keyword'];
                }
            }
            $Category = $TblTourCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
            foreach ($Category as $key => $value) {
                $ParentCategory = $TblTourCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
                $Category[$key]['Parent'] = $ParentCategory;
            }
        } elseif ($ArticleType == 3) {
            $TblModule = new TblImmigrationModule();
            $KeywordModule = new TblImmigrationKeywordModule();
            $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
            $KeywordInfo = $KeywordModule->GetInfoByWhere('', true);
            foreach ($KeywordInfo as $key => $value) {
                $in = strstr($ArticleInfo['Content'], $value['Keyword']);
                if ($in) {
                    $Data[$key]['KeyID'] = $value['KeyID'];
                    $Data[$key]['Keyword'] = $value['Keyword'];
                }
            }
            $Category = $TblImmigrationCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
            foreach ($Category as $key => $value) {
                $ParentCategory = $TblImmigrationCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
                $Category[$key]['Parent'] = $ParentCategory;
            }
        }
        
        if ($_POST) {
            $submit1 = $_POST['submit1'];
            $submit2 = $_POST['submit2'];
            $ArticleID = intval($_POST['ArticleID']);
            $Data['CategoryID'] = intval($_POST['CategoryID']);
            $Data['Title'] = trim($_POST['Title']);
            if (empty($Data['Title'])) {
                alertandback('文章保存失败,标题不能为空.');
            }
            $Keywords = $_POST['Keywords'];
            foreach ($Keywords as $key => $value) {
                $In .= $value . ',';
            }
            $In = substr($In, 0, - 1);
            $Data['Keywords'] = $In;
            $Data['Description'] = trim($_POST['Description']);
            $Data['SeoTitle'] = str_replace(array(
                '，',
                ','
            ), array(
                '_',
                '_'
            ), trim($_POST['SeoTitle']));
            $Data['SeoKeywords'] = trim(str_replace(array(
                '，'
            ), array(
                ','
            ), trim($_POST['SeoKeywords'])), ',');
            $Data['SeoDescription'] = trim($_POST['SeoDescription']);
            // 文本图片处理-----------------------------------------------------------------------------
            $Data['Content'] = $_POST['Content'];
            $Pattern = array();
            $Replacement = array();
            $ImgArr = Array();
            preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($Data['Content']), $ImgArr);
            if (count($ImgArr[0])) {
                foreach ($ImgArr[0] as $Key => $ImgTag) {
                    $Pattern[] = $ImgTag;
                    $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                        '/title=".*"/iU',
                        '/alt=".*"/iU'
                    ), '', $ImgTag));
                }
            }
            $Data['Content'] = addslashes(str_replace($Pattern, $Replacement, stripcslashes($Data['Content'])));
            // 文本图片处理-------------------------------------------------------------------------------
            $Data['Sort'] = trim($_POST['Sort']);
            $Data['ComeFrom'] = trim($_POST['ComeFrom']);
            $Data['Redactor'] = trim($_POST['Redactor']);
            $Data['IndexRecommend'] = intval($_POST['IndexRecommend']);
            $Data['TopicRecommend'] = intval($_POST['TopicRecommend']);
            $Data['M1'] = intval($_POST['M1']);
            $Data['M2'] = intval($_POST['M2']);
            $Data['M3'] = intval($_POST['M3']);
            // 上传图片
            include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
            if ($_FILES['Image']['size'][0] > 0) {
                $Upload = new MultiUpload('Image');
                $File = $Upload->upload();
                $Picture = $File[0] ? $File[0] : '';
                $Data['Image'] = $Picture;
            }
            $now_time = time();
            if ($submit1) {
                if (isset($Data['Image'])) {
                    $ArticleInfo = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
                    if ($ArticleInfo['Image']) {
                        DelFromImgServ($ArticleInfo['Image']);
                    }
                }
                $Data['UpdateTime'] = date('Y-m-d H:i:s', $now_time);
                $result = $CaijiArticleModule->UpdateInfoByKeyID($Data, $ArticleID);
                if ($result) {
                    alertandgotopage('保存成功!', "/index.php?Module=CaiJiNews&Action=Edit&ArticleID=" . $ArticleID . "&Type=" . $ArticleType);
                } else {
                    alertandback('操作未修改，请重新编辑');
                }
            } elseif ($submit2) {
                $Info = $CaijiArticleModule->GetInfoByKeyID($ArticleID);
                if (! $Data['Image']) {
                    $Data['Image'] = $Info['Image'];
                }
                $Data['UpdateTime'] = date('Y-m-d H:i:s', $now_time);
                $Data['AddTime'] = date('Y-m-d H:i:s', $now_time);
                $Data['ViewCount'] = 0;
                $Data['AdminID'] = $_SESSION['AdminID'];
                $result = $TblModule->InsertInfo($Data);
                if ($result) {
                    $Delete = $CaijiArticleModule->DeleteByKeyID($ArticleID);
                    if ($Delete) {
                        alertandgotopage('保存并提交成功!', "/index.php?Module=CaiJiNews&Action=Lists&Type=$ArticleType");
                    } else {
                        alertandgotopage('保存并提交失败,请重新编辑!', "/index.php?Module=CaiJiNews&Action=Lists&Type=$ArticleType");
                    }
                } else {
                    alertandgotopage('保存并提交失败', "/index.php?Module=CaiJiNews&Action=Lists&Type=$ArticleType");
                }
            }
        }
        include template("CaijiNewsEdit");
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
            $ArticleIDs = substr($ArticleIDs, 0, - 1);
            if (! $ArticleIDs) {
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