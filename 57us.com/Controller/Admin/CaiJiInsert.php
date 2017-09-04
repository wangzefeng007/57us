<?php

/**
 * Created by Zend.
 * User: bob
 * Date: 2017/1/5
 * Time: 16:43
 */
class CaiJiInsert
{

    public function __construct()
    {
        $this->TourImages = rand(1, 26) . '.jpg';
        $this->StudyImages = rand(1, 26) . '.jpg';
        $this->ImmigrantImages = rand(1, 26) . '.jpg';
    }

    /*
     * 数据入库
     * http://admin.57us.com/index.php?Module=CaiJiInsert&Action=Insert
     */
    public function Insert()
    {
        $CaijiArticleModule = new CaijiArticleModule();
        $ArticleType = rand(1, 3);
        $CaijiArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and ArticleType=' . $ArticleType . ' and IsAdd=0');
        $InsertInfo['CategoryID'] = $CaijiArticleInfo['CategoryID'];
        $InsertInfo['Title'] = addslashes(trim($CaijiArticleInfo['Title']));
        $InsertInfo['SeoKeywords'] = addslashes(trim($CaijiArticleInfo['Title']));
        $InsertInfo['SeoTitle'] = addslashes(trim($CaijiArticleInfo['Title']));
        $InsertInfo['Description'] = addslashes(_substr(strip_tags($CaijiArticleInfo['Content']), 80));
        $InsertInfo['Content'] = addslashes(trim($CaijiArticleInfo['Content']));
        // $InsertInfo['Content'] = _DelPicToContent($InsertInfo['Content']);
        $InsertInfo['Image'] = $CaijiArticleInfo['Image'];
        $InsertInfo['ViewCount'] = rand(1, 200);
        $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
        $InsertInfo['CategoryID'] = $CaijiArticleInfo['CategoryID'];
        $I = 0;
        if ($CaijiArticleInfo['ArticleType'] == 1) {
            // 留学
            if ($InsertInfo['Image'] == '') {
                $InsertInfo['Image'] = '/up/new/study/' . $this->TourImages;
            }
            $TblStudyAbroadKeywordModule = new TblStudyAbroadKeywordModule();
            $TblStudyAbroadKeywordLists = $TblStudyAbroadKeywordModule->GetInfoByWhere(' order by Sort desc', true);
            $InsertInfo['Keywords'] = '';
            foreach ($TblStudyAbroadKeywordLists as $Value) {
                if (strstr($CaijiArticleInfo['Content'], $Value['Keyword'])) {
                    $I ++;
                    $InsertInfo['Keywords'] .= ',' . $Value['KeyID'];
                    if ($I == 2) {
                        break;
                    }
                }
            }
            if ($InsertInfo['Keywords'] != '') {
                $InsertInfo['Keywords'] = substr($InsertInfo['Keywords'], 1);
            }
            if (strlen($InsertInfo['Content']) > 20) {
                $TblStudyAbroadModule = new TblStudyAbroadModule();
                $TblStudyID = $TblStudyAbroadModule->InsertInfo($InsertInfo);
            }
            if ($TblStudyID > 0) {
                $CaijiUrlLogModule = new CaijiUrlLogModule();
                $InsertLog['MyUrl'] = 'http://www.57us.com/study/' . $TblStudyID . '.html';
                $InsertLog['TheUrl'] = $CaijiArticleInfo['FromUrl'];
                $InsertLog['Title'] = $CaijiArticleInfo['Title'];
                $InsertLog['Type'] = $CaijiArticleInfo['ArticleType'];
                $InsertLog['AddTime'] = time();
                $CaijiUrlLogModule->InsertInfo($InsertLog);
                
                $UpdateInfo['IsAdd'] = 1;
                $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $CaijiArticleInfo['ArticleID']);
            } else {
                $UpdateInfo['IsAdd'] = 2;
                $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $CaijiArticleInfo['ArticleID']);
            }
        } elseif ($CaijiArticleInfo['ArticleType'] == 2) {
            // 旅游
            if ($InsertInfo['Image'] == '') {
                $InsertInfo['Image'] = '/up/new/tour/' . $this->TourImages;
            }
            $TblTourKeywordModule = new TblTourKeywordModule();
            $TblTourKeywordLists = $TblTourKeywordModule->GetInfoByWhere(' order by Sort desc', true);
            $InsertInfo['Keywords'] = '';
            foreach ($TblTourKeywordLists as $Value) {
                if (strstr($CaijiArticleInfo['Content'], $Value['Keyword'])) {
                    $I ++;
                    $InsertInfo['Keywords'] .= ',' . $Value['KeyID'];
                    if ($I == 2) {
                        break;
                    }
                }
            }
            if ($InsertInfo['Keywords'] != '') {
                $InsertInfo['Keywords'] = substr($InsertInfo['Keywords'], 1);
            }
            if (strlen($InsertInfo['Content']) > 20) {
                $TblTourModule = new TblTourModule();
                $TblTourID = $TblTourModule->InsertInfo($InsertInfo);
            }
            if ($TblTourID > 0) {
                $CaijiUrlLogModule = new CaijiUrlLogModule();
                $InsertLog['MyUrl'] = 'http://www.57us.com/tour/' . $TblTourID . '.html';
                $InsertLog['TheUrl'] = $CaijiArticleInfo['FromUrl'];
                $InsertLog['Title'] = $CaijiArticleInfo['Title'];
                $InsertLog['Type'] = $CaijiArticleInfo['ArticleType'];
                $InsertLog['AddTime'] = time();
                $CaijiUrlLogModule->InsertInfo($InsertLog);
                
                $UpdateInfo['IsAdd'] = 1;
                $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $CaijiArticleInfo['ArticleID']);
            } else {
                $UpdateInfo['IsAdd'] = 2;
                $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $CaijiArticleInfo['ArticleID']);
            }
        } elseif ($CaijiArticleInfo['ArticleType'] == 3) {
            // 移民
            if ($InsertInfo['Image'] == '') {
                $InsertInfo['Image'] = '/up/new/immigrant/' . $this->TourImages;
            }
            $TblImmigrationKeywordModule = new TblImmigrationKeywordModule();
            $TblImmigrationKeywordLists = $TblImmigrationKeywordModule->GetInfoByWhere(' order by Sort desc', true);
            $InsertInfo['Keywords'] = '';
            foreach ($TblImmigrationKeywordLists as $Value) {
                if (strstr($CaijiArticleInfo['Content'], $Value['Keyword'])) {
                    $I ++;
                    $InsertInfo['Keywords'] .= ',' . $Value['KeyID'];
                    if ($I == 2) {
                        break;
                    }
                }
            }
            if ($InsertInfo['Keywords'] != '') {
                $InsertInfo['Keywords'] = substr($InsertInfo['Keywords'], 1);
            }
            
            if (strlen($InsertInfo['Content']) > 20) {
                $TblImmigrationModule = new TblImmigrationModule();
                $TblImmigrationID = $TblImmigrationModule->InsertInfo($InsertInfo);
            }
            if ($TblImmigrationID > 0) {
                $CaijiUrlLogModule = new CaijiUrlLogModule();
                $InsertLog['MyUrl'] = 'http://www.57us.com/immigrant/' . $TblImmigrationID . '.html';
                $InsertLog['TheUrl'] = $CaijiArticleInfo['FromUrl'];
                $InsertLog['Title'] = $CaijiArticleInfo['Title'];
                $InsertLog['Type'] = $CaijiArticleInfo['ArticleType'];
                $InsertLog['AddTime'] = time();
                $CaijiUrlLogModule->InsertInfo($InsertLog);
                
                $UpdateInfo['IsAdd'] = 1;
                $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $CaijiArticleInfo['ArticleID']);
            } else {
                $UpdateInfo['IsAdd'] = 2;
                $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $CaijiArticleInfo['ArticleID']);
            }
        }else{
			echo '<script type="text/javascript" language="javascript">
function reloadyemian()
{
    window.location.href="http://admin.57us.com/index.php?Module=CaiJiInsert&Action=Insert";
}
window.setTimeout("reloadyemian();",1000);
</script>
';
        exit();
		}
        if (date("H") > 20) {
            $Times = 43200000;
        } else {
            $Times = rand(150000, 200000);
        }
        echo '<script type="text/javascript" language="javascript">
function reloadyemian()
{
    window.location.href="http://admin.57us.com/index.php?Module=CaiJiInsert&Action=Insert";
}
window.setTimeout("reloadyemian();",' . $Times . ');
</script>
';
        exit();
    }
}