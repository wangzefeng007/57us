<?php

/**
 * @desc  采集微信公众号文章
 * Class CaiJiWeiXin
 */
class CaiJiWeiXin
{
    public function __construct(){
        $this->TourImages = rand(1, 26) . '.jpg';
        $this->StudyImages = rand(1, 26) . '.jpg';
        $this->ImmigrantImages = rand(1, 26) . '.jpg';
    }

    /**
     * 微信公众号文章采集页面
     */
    public function Index(){
        $TblTourCategoryModule = new TblTourCategoryModule();
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $StudyCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
        foreach ($StudyCategory as $key => $value) {
            $ParentCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
            $StudyCategory[$key]['Parent'] = $ParentCategory;
            foreach ($ParentCategory as $K => $Val) {
                $TwoCategory = $TblStudyAbroadCategoryModule->GetLists(' and ParentCategoryID = ' . $Val['CategoryID'], 0, 50);
                $StudyCategory[$key]['Parent'][$K]['TwoCategory'] = $TwoCategory;
            }
        }
        $ImmigrationCategory = $TblImmigrationCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
        foreach ($ImmigrationCategory as $key => $value) {
            $ParentCategory = $TblImmigrationCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
            $ImmigrationCategory[$key]['Parent'] = $ParentCategory;
        }
        $TourCategory = $TblTourCategoryModule->GetLists(' and ParentCategoryID = 0', 0, 100);
        foreach ($TourCategory as $key => $value) {
            $ParentCategory = $TblTourCategoryModule->GetLists(' and ParentCategoryID = ' . $value['CategoryID'], 0, 100);
            $TourCategory[$key]['Parent'] = $ParentCategory;
        }

        include template("CaijiWeiXinIndex");
    }

    /**
     * @desc  开始采集
     */
    public function Start(){
        $CaijiWeiXinUrlModule = new CaijiWeiXinUrlModule();
        $Url = str_replace('amq;','',$_POST['Url']);
        $IsExit = $CaijiWeiXinUrlModule->GetInfoByWhere(" and Url = '{$Url}'");
        if ($IsExit) {
            alertandback("该篇文章已经采集过");
        }
        $Data['ArticleType'] = intval($_POST['ArticleType']);
        $CategoryID = $_POST['CategoryID'];
        foreach ($CategoryID as $value) {
            if ($value != '') {
                $Data['CategoryID'] = intval($value);
            }
        }
        $Data['Url'] = $Url;
        $Data['Title'] = trim($_POST['Title']);

        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        //添加采集的url记录
        $Result = $CaijiWeiXinUrlModule->InsertInfo($Data);
        if(!$Result){
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            alertandback("采集失败，添加CaijiWeiXinUrlModule表失败");
        }
        else{
            $Content = ToolService::HttpsRequest($Url);
            $DetailZZ1 = '/<div class="rich_media_content " id="js_content">(.*)<\/div>/isU';
            preg_match_all($DetailZZ1,$Content, $DetailReturn);
            //去除A标签
            $str1 = preg_replace("/<a[^>]*>/","", $DetailReturn[1][0]);
            $str1 = str_replace("","",$str1);
            //-------过滤微信特殊符号，表情----开始
            $tmpStr = json_encode($str1);
            $tmpStr = preg_replace("#(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})#ie","",$tmpStr);
            $str2 = json_decode($tmpStr);
            //-------过滤微信特殊符号，表情----结束
            $Result = preg_replace("/<\/a>/","", $str2);
            $ImageNames = $this->PostImg($DetailReturn[1][0]);
            if($ImageNames[0]){
                $Data1['Image'] = $ImageNames[0];
            }
            else{
                $Data1['Image'] = '';
            }
            $Pattern = array();
            $Replacement = array();
            $ImgArr = Array();
            preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($DetailReturn[1][0]), $ImgArr);
            if (count($ImgArr[1])) {
                foreach ($ImgArr[1] as $Key => $ImgTag) {
                    $Pattern[] = $ImgTag;
                    $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array('/title=".*"/iU', '/alt=".*"/iU'), '', $ImgTag));
                }
            }
            $Data1['Title'] = $Data['Title'];
            $Data1['Content'] = addslashes(str_replace(array_reverse($Replacement), array_reverse($ImageNames), stripcslashes($Result)));
            $Data1['SeoKeywords'] = addslashes(trim($Data1['Title']));
            $Data1['SeoTitle'] = addslashes(trim($Data1['Title']));
            $Data1['Description'] = addslashes(_substr(strip_tags($Data1['Content']), 80));
            $Data1['ArticleType'] =  $Data['ArticleType'];//留学
            $Data1['FromUrl'] = $Url;
            $Data1['CategoryID'] = $Data['CategoryID'];
            $Data1['AddTime'] = date("Y-m-d H:i:s",time());
            $Data1['UpdateTime'] = date("Y-m-d H:i:s",time());
            $Data1['IsHaveContent'] = $Data1['Content']?1:2;
            //echo "<pre>";print_r($Data1);exit;
            $Result2 = $this->InsertData($Data1);
            unset($Data1['ArticleType'],$Data1['IsHaveContent'],$Data1['FromUrl']);
            if(!$Result2){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                alertandback("采集失败，添加CaijiArticleModule失败");
            }
            else{
                $CaijiArticleModule = new CaijiArticleModule();
                if ($Data['ArticleType'] == 1) {
                    // 留学
                    if ($Data1['Image'] == '') {
                        $Data1['Image'] = '/up/new/study/' . $this->StudyImages;
                    }
                    $TblStudyAbroadKeywordModule = new TblStudyAbroadKeywordModule();
                    $TblStudyAbroadKeywordLists = $TblStudyAbroadKeywordModule->GetInfoByWhere(' order by Sort desc', true);
                    $Data1['Keywords'] = '';
                    foreach ($TblStudyAbroadKeywordLists as $Value) {
                        if (strstr($Data1['Content'], $Value['Keyword'])) {
                            $I ++;
                            $Data1['Keywords'] .= ',' . $Value['KeyID'];
                            if ($I == 2) {
                                break;
                            }
                        }
                    }
                    if ($Data1['Keywords'] != '') {
                        $Data1['Keywords'] = substr($Data1['Keywords'], 1);
                    }
                    $TblStudyAbroadModule = new TblStudyAbroadModule();
                    $TblStudyID = $TblStudyAbroadModule->InsertInfo($Data1);
                    if(!$TblStudyID){
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        alertandback("采集失败，添加TblStudyAbroadModule失败");
                    }
                    else{
                        $CaijiUrlLogModule = new CaijiUrlLogModule();
                        $InsertLog['MyUrl'] = 'http://www.57us.com/study/' . $TblStudyID . '.html';
                        $InsertLog['TheUrl'] = $Url;
                        $InsertLog['Title'] = $Data1['Title'];
                        $InsertLog['Type'] = $Data['ArticleType'];
                        $InsertLog['AddTime'] = time();
                        $Result3 = $CaijiUrlLogModule->InsertInfo($InsertLog);
                        if(!$Result3){
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            alertandback("采集失败，添加CaijiUrlLogModule失败");
                        }
                        else{
                            $UpdateInfo['IsAdd'] = 1;
                            $Result4 = $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $Result2);
                            if(!$Result4){
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                alertandback("采集失败，更新CaijiArticleModule失败");
                            }
                            else{
                                $DB->query("COMMIT");//执行事务
                                alertandback("采集成功,请即使检查采集内容");
                            }
                        }
                    }
                }
                elseif($Data['ArticleType'] == 2){
                    // 旅游
                    if ($Data1['Image'] == '') {
                        $Data1['Image'] = '/up/new/tour/' . $this->TourImages;
                    }
                    $TblTourKeywordModule = new TblTourKeywordModule();
                    $TblTourKeywordLists = $TblTourKeywordModule->GetInfoByWhere(' order by Sort desc', true);
                    $Data1['Keywords'] = '';
                    foreach ($TblTourKeywordLists as $Value) {
                        if (strstr($Data1['Content'], $Value['Keyword'])) {
                            $I ++;
                            $Data1['Keywords'] .= ',' . $Value['KeyID'];
                            if ($I == 2) {
                                break;
                            }
                        }
                    }
                    if ($Data1['Keywords'] != '') {
                        $Data1['Keywords'] = substr($Data1['Keywords'], 1);
                    }

                    $TblTourModule = new TblTourModule();
                    $TblTourID = $TblTourModule->InsertInfo($Data1);
                    if(!$TblTourID){
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        alertandback("采集失败，添加TblTourModule失败");
                    }
                    else{
                        $CaijiUrlLogModule = new CaijiUrlLogModule();
                        $InsertLog['MyUrl'] = 'http://www.57us.com/tour/' . $TblTourID . '.html';
                        $InsertLog['TheUrl'] = $Url;
                        $InsertLog['Title'] = $Data1['Title'];
                        $InsertLog['Type'] = $Data['ArticleType'];
                        $InsertLog['AddTime'] = time();
                        $Result3 = $CaijiUrlLogModule->InsertInfo($InsertLog);
                        if(!$Result3){
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            alertandback("采集失败，添加CaijiUrlLogModule失败");
                        }
                        else{
                            $UpdateInfo['IsAdd'] = 1;
                            $Result4 = $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $Result2);
                            if(!$Result4){
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                alertandback("采集失败，更新CaijiArticleModule失败");
                            }
                            else{
                                $DB->query("COMMIT");//执行事务
                                alertandback("采集成功,请即使检查采集内容");
                            }
                        }
                    }
                }
                elseif($Data['ArticleType'] == 3){
                    // 移民
                    if ($Data1['Image'] == '') {
                        $Data1['Image'] = '/up/new/immigrant/' . $this->TourImages;
                    }
                    $TblImmigrationKeywordModule = new TblImmigrationKeywordModule();
                    $TblImmigrationKeywordLists = $TblImmigrationKeywordModule->GetInfoByWhere(' order by Sort desc', true);
                    $Data1['Keywords'] = '';
                    foreach ($TblImmigrationKeywordLists as $Value) {
                        if (strstr($Data1['Content'], $Value['Keyword'])) {
                            $I ++;
                            $Data1['Keywords'] .= ',' . $Value['KeyID'];
                            if ($I == 2) {
                                break;
                            }
                        }
                    }
                    if ($Data1['Keywords'] != '') {
                        $Data1['Keywords'] = substr($Data1['Keywords'], 1);
                    }

                    $TblImmigrationModule = new TblImmigrationModule();
                    $TblImmigrationID = $TblImmigrationModule->InsertInfo($Data1);
                    if(!$TblImmigrationID){
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        alertandback("采集失败，添加TblImmigrationModule失败");
                    }
                    else{
                        $CaijiUrlLogModule = new CaijiUrlLogModule();
                        $InsertLog['MyUrl'] = 'http://www.57us.com/immigrant/' . $TblImmigrationID . '.html';
                        $InsertLog['TheUrl'] = $Url;
                        $InsertLog['Title'] = $Data1['Title'];
                        $InsertLog['Type'] = $Data['ArticleType'];
                        $InsertLog['AddTime'] = time();
                        $Result3 = $CaijiUrlLogModule->InsertInfo($InsertLog);
                        if(!$Result3){
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            alertandback("采集失败，添加CaijiUrlLogModule失败");
                        }
                        else{
                            $UpdateInfo['IsAdd'] = 1;
                            $Result4 = $CaijiArticleModule->UpdateInfoByKeyID($UpdateInfo, $Result2);
                            if(!$Result4){
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                alertandback("采集失败，更新CaijiArticleModule失败");
                            }
                            else{
                                $DB->query("COMMIT");//执行事务
                                alertandback("采集成功,请即使检查采集内容");
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @desc  图片上传
     * @param $Detail
     * @return mixed
     */
    private function PostImg($Detail){
        $Imgs = _GetPicToContent($Detail);
        if($Imgs){
            foreach($Imgs as $key=>$val){
                //$img_info = getimagesize($val);
                $ImgSrc = base64_encode(file_get_contents($val));
                $TitleName[$key] = '/up/'.date('Y').'/'.date('md').'/test/'.date("YmdHis").rand(1000,9999).'.jpg';
                SendToImgServ($TitleName[$key],$ImgSrc);
            }
            return $TitleName;
        }
        else{
            return '';
        }
    }

    /**
     * @desc  入库(caiji_article)
     */
    private function InsertData($Data){
        $CaijiArticleModule = new CaijiArticleModule();
        return $CaijiArticleModule->InsertInfo($Data);
    }

    /**
     * @desc 入库 caiji_url_all
     * @param $Data
     */
    private function InsertUrlData($Data){
        $CaijiAllUrlModule = new CaijiUrlAllModule();
        return $CaijiAllUrlModule->InsertInfo($Data);
    }

}