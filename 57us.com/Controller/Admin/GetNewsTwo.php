<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/9/26
 * Time: 14:24
 */
class GetNewsTwo
{
    public function __construct()
    {
        IsLogin();
    }
    public function Index(){
    }
    public function TourInfo()
    {
        if ($_GET['ColumnID']) {
            $ColumnID = $_GET['ColumnID'];
            $CaijiColumnUrlModule = new CaijiColumnUrlModule();
            $ColumnUrlInfo = $CaijiColumnUrlModule->GetInfoByKeyID($ColumnID);
            $Url = $ColumnUrlInfo['Url'];
        }
        echo '抓取链接：' . $Url;
        if ($_GET['url']) {
            $Url = $_GET['url'];
        }
        if ($Url == 'http://www.gousa.cn/blogs?=&category=All') {
            for ($I = 0; $I < 140; $I++) {
                $Url = 'http://www.gousa.cn/blogs?category=All&page=' . $I;
                $Html = file_get_contents($Url);
                $ListZZ = '/<div class="views-field views-field-field-promotional-image">(.*)<\/div>/isU';
                $ListZ1 = '/<a href="(.*)">/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray [1] as $Value) {
                    preg_match_all($ListZ1, $Value, $Return);
                    $this->GetGousaCnInfo($Return[1][0], $ColumnID, $ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url == 'http://www.citytripinfo.com/travel/category/travel-guide') {
            for ($I = 1; $I < 11; $I++) {
                $Url = 'http://www.citytripinfo.com/travel/category/travel-guide/page/' . $I;
                $Html = file_get_contents($Url);
                $ListZZ = '/<h2 class="entry-title"><a href="(.*)" rel="bookmark" title=".*">.*<\/a><\/h2>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetCitytripinfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url == 'http://www.joytrav.com/info/') {
            for ($I = 1; $I < 11; $I++) {
                $Url = 'http://www.citytripinfo.com/travel/category/travel-guide/page/' . $I;
                $Html = file_get_contents($Url);
                $ListZZ = '/<h2 class="entry-title"><a href="(.*)" rel="bookmark" title=".*">.*<\/a><\/h2>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetCitytripinfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url == 'http://www.joytrav.com/info/tour/') {
            for ($I = 1; $I < 11; $I++) {
                $Url = 'http://www.joytrav.com/info/tour/'.$I.'.html';
                $Html = file_get_contents($Url);
                $ListZZ = '/<div class="infor_txt">.*<a target="_blank" href="(.*)" title=".*">.*<\/div>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetCitytripinfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url == 'http://www.7niuyue.com/category_more.php?lmid=27&id=27') {
            for ($I = 1; $I < 11; $I++) {
                $Url = 'http://www.7niuyue.com/category_more.php?lmid=27&page=' . $I;
                $Html = file_get_contents($Url);
                $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
                $ListZZ1 = '/<ul class=\"itemList itemListSecondMore\">(.*)<div class=\"page pageListMore\">/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray);
                $ListZZ = '/<\/span><a href=\"(.*)\"/isU';
                preg_match_all($ListZZ, $ReturnArray[1][0], $ReturnArrayTwo);
                foreach ($ReturnArrayTwo[1] as $Val) {
                    $this->Get7niuyueInfo('http://www.7niuyue.com/' . $Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url == 'http://www.7niuyue.com/category_more.php?lmid=26&id=26') {
            for ($I = 1; $I < 11; $I++) {
                $Url = 'http://www.7niuyue.com/category_more.php?lmid=26&page=' . $I;
                $Html = file_get_contents($Url);
                $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
                $ListZZ1 = '/<ul class=\"itemList itemListSecondMore\">(.*)<div class=\"page pageListMore\">/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray);
                $ListZZ = '/<\/span><a href=\"(.*)\"/isU';
                preg_match_all($ListZZ, $ReturnArray[1][0], $ReturnArrayTwo);
                foreach ($ReturnArrayTwo[1] as $Val) {
                    $this->Get7niuyueInfo('http://www.7niuyue.com/' . $Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url == 'http://www.7niuyue.com/category_more.php?lmid=24&id=24') {
            for ($I = 1; $I < 11; $I++) {
                $Url = 'http://www.7niuyue.com/category_more.php?lmid=24&page=' . $I;
                $Html = file_get_contents($Url);
                $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
                $ListZZ1 = '/<ul class=\"itemList itemListSecondMore\">(.*)<div class=\"page pageListMore\">/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray);
                $ListZZ = '/<\/span><a href=\"(.*)\"/isU';
                preg_match_all($ListZZ, $ReturnArray[1][0], $ReturnArrayTwo);
                foreach ($ReturnArrayTwo[1] as $Val) {
                    $this->Get7niuyueInfo('http://www.7niuyue.com/' . $Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url == 'http://www.7niuyue.com/category_more.php?lmid=22&id=22') {
            for ($I = 1; $I < 11; $I++) {
                $Url = 'http://www.7niuyue.com/category_more.php?lmid=22&page=' . $I;
                $Html = file_get_contents($Url);
                $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
                $ListZZ1 = '/<ul class=\"itemList itemListSecondMore\">(.*)<div class=\"page pageListMore\">/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray);
                $ListZZ = '/<\/span><a href=\"(.*)\"/isU';
                preg_match_all($ListZZ, $ReturnArray[1][0], $ReturnArrayTwo);
                foreach ($ReturnArrayTwo[1] as $Val) {
                    $this->Get7niuyueInfo('http://www.7niuyue.com/' . $Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url == 'http://www.7niuyue.com/category_more.php?lmid=20&id=20') {
            for ($I = 1; $I < 11; $I++) {
                $Url = 'http://www.7niuyue.com/category_more.php?lmid=20&page=' . $I;
                $Html = file_get_contents($Url);
                $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
                $ListZZ1 = '/<ul class=\"itemList itemListSecondMore\">(.*)<div class=\"page pageListMore\">/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray);
                $ListZZ = '/<\/span><a href=\"(.*)\"/isU';
                preg_match_all($ListZZ, $ReturnArray[1][0], $ReturnArrayTwo);
                foreach ($ReturnArrayTwo[1] as $Val) {
                    $this->Get7niuyueInfo('http://www.7niuyue.com/' . $Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
    }
    public function Get7niuyueInfo($Url = '',$ColumnID='',$CategoryID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' .$Url .'\'');
        if ($UrlInfo) {
            return 0;
        }
        unset($UrlInfo);
        $Html = file_get_contents($Url);
        $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
        $GuoLvArray = array(
            '/<img(.*)>/isU',
            '/<a(.*)>/isU',
            '/<\/a>/isU',
            '/style=\"(.*)\"/isU',
            '/<script(.*)script>/isU',
            '/<style(.*)style>/isU'
        );
        $GuoLvJieGuo = array(
            '',
            '',
            '',
            '',
            '',
            ''
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);
        $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"con\">(.*)<div class=\"tag\">/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Title = trim($ReturnArray[1][0]);
        $Content = trim($ReturnArray[2][0]);
        $Content = str_replace ( "\n\r", "<br>", $Content );
        $Content = str_replace ( "\r", "<br>", $Content );
        $Content = str_replace ( "\n", "<br>", $Content );
        $Content = str_replace ( '&ldquo;', '“', $Content );
        $Content = str_replace ( '&rdquo;', '”', $Content );
        $Content = substr($Content, 0, - 6);
        $InsertInfo['Title'] = addslashes($Title);
        if ($InsertInfo['Title']!='')
        {
            $InsertInfo['ArticleType'] = 2;
            $InsertInfo['Content'] = addslashes($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($Content, 180)));
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['CategoryID'] = $CategoryID;
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            if ($Insert){
                $InsertUrlAllInfo['GetTime'] = date("Y-m-d H:i:s");
                $InsertUrlAllInfo['Url'] = $Url;
                $InsertUrl = $CaijiUrlAllModule->InsertInfo($InsertUrlAllInfo);
                $Data['LastGetTime'] = date("Y-m-d H:i:s");
                $CaijiColumnUrlModule = new CaijiColumnUrlModule();
                $CaijiColumnUrlModule->UpdateInfoByKeyID($Data,$ColumnID);
                $CaijiColumnUrlModule->UpdateNum($ColumnID);
            }
        }
        return 1;
    }
    public function GetCitytripinfo($Url = '',$ColumnID='',$CategoryID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' .$Url .'\'');
        if ($UrlInfo) {
            return 0;
        }
        unset($UrlInfo);
        $Html = file_get_contents($Url);
        $GuoLvArray = array(
            '/<img(.*)>/isU',
            '/<a(.*)>/isU',
            '/<\/a>/isU',
            '/style=\"(.*)\"/isU',
            '/<script(.*)script>/isU',
            '/<ins(.*)>/isU',
            '/<style(.*)style>/isU',
            '/美国旅游中文网/isU',
            '/出国留学网/isU'
        );
        $GuoLvJieGuo = array(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '57美国网',
            '57美国网'
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);


        if (strstr($Html, '<h3 class="t_article_title">')){
            $ListZZ = '/<h3 class="t_article_title">(.*)<\/h3>.*<div class="t_article_txt">(.*)<\/div>/isU';
        }else{
            $ListZZ = '/<h1 class="entry-title">(.*)<\/h1>.*<div class="single-content">(.*)<\/div>/isU';
        }
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Title = trim($ReturnArray[1][0]);
        $Content = trim($ReturnArray[2][0]);
        $Content = $this->DoFilterInfo($Content);
        $InsertInfo['Title'] = addslashes($Title);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 2;
            $InsertInfo['Content'] = addslashes($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($Content, 180)));
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['CategoryID'] = intval($CategoryID);
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            if ($Insert){
                $InsertUrlAllInfo['GetTime'] = date("Y-m-d H:i:s");
                $InsertUrlAllInfo['Url'] = $Url;
                $InsertUrl = $CaijiUrlAllModule->InsertInfo($InsertUrlAllInfo);
                $Data['LastGetTime'] = date("Y-m-d H:i:s");
                $CaijiColumnUrlModule = new CaijiColumnUrlModule();
                $CaijiColumnUrlModule->UpdateInfoByKeyID($Data,$ColumnID);
                $CaijiColumnUrlModule->UpdateNum($ColumnID);
            }
        }
        return 1;
    }
    public function GetGousaCnInfo($Url = '',$ColumnID='',$CategoryID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' .$Url .'\'');
        if ($UrlInfo) {
            return 0;
        }
        unset($UrlInfo);
        $Html = file_get_contents($Url);
        $GuoLvArray = array(
            '/<a(.*)>/isU',
            '/<\/a>/isU',
            '/style=\"(.*)\"/isU',
            '/<script(.*)script>/isU',
            '/<style(.*)style>/isU',
            '/出国移民网/isU',
            '/yimin.liuxue86.com/isU',
            '/meiguo.liuxue86.com/isU',
            '/www.liuxue86.com/isU',
            '/美国国家旅游局GoUSA/isU'
        );
        $GuoLvJieGuo = array(
            '',
            '',
            '',
            '',
            '',
            '57美国网',
            'www.57us.com',
            'www.57us.com',
            'www.57us.com',
            '57美国网'
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);
        $ListZZ = '/<h1 class="page__title title" id="page-title">(.*)<\/h1>.*<div class="inside panels-flexible-region-inside panels-flexible-region-110-center-inside">(.*)<\/div>/isU';
        preg_match_all ( $ListZZ, $Html, $ReturnArray );
        $Title = trim ( $ReturnArray [1] [0] );
        $Content = trim ( $ReturnArray [2] [0] );
        $Content = $this->DoFilterInfo($Content);
        $listImg = '/<img alt=".*" title=".*" height=".*" width=".*" class=".*" typeof="foaf:Image" src="(.*.jpg).*" \/>/isU';
        preg_match_all ( $listImg, $Content, $ReturnImg );
        foreach ($ReturnImg[1] as $value){
            $imgUrl = '/up/'.date('Y').'/'.date('md').'/'.date('YmdHis'). rand(1000,9999).'.jpg';
            $img =SendToImgServ($imgUrl, base64_encode(file_get_contents($value)));
            if ($img ==true)
            $Content = str_replace ($value,'http://images.57us.com/l'.$imgUrl, $Content );
        }
        $InsertInfo['Title'] = addslashes($Title);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 2;
            $InsertInfo['Content'] = addslashes($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($Content, 180)));
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['CategoryID'] = intval($CategoryID);
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            if ($Insert){
                $InsertUrlAllInfo['GetTime'] = date("Y-m-d H:i:s");
                $InsertUrlAllInfo['Url'] = $Url;
                $InsertUrl = $CaijiUrlAllModule->InsertInfo($InsertUrlAllInfo);
                $Data['LastGetTime'] = date("Y-m-d H:i:s");
                $CaijiColumnUrlModule = new CaijiColumnUrlModule();
                $CaijiColumnUrlModule->UpdateInfoByKeyID($Data,$ColumnID);
                $CaijiColumnUrlModule->UpdateNum($ColumnID);
            }
        }
        return 1;
    }
    //过滤内容
    public function DoFilterInfo($Content = '')
    {
        if ($Content == '')
            return 0;
        $Content = trim ( $Content );
        $Content = str_replace ( '（', '(', $Content );
        $Content = str_replace ( '）', ')', $Content );
        $Content = preg_replace ( "/<(\/?div.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?font.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?strong.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?span.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?a.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?pre.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?p.*?)>/si", "", $Content );
        $Content = str_replace ( "\n\r", "<br>", $Content );
        $Content = str_replace ( "\r", "<br>", $Content );
        $Content = str_replace ( "\n", "<br>", $Content );
        $Content = str_replace ( '&ldquo;', '“', $Content );
        $Content = str_replace ( '&rdquo;', '”', $Content );
        $Content = str_replace ( "'", "’", $Content );
        $Content = preg_replace ( "/<(\/?table.*?)>/si", "", $Content );
        $Content = trim ( $Content );
        return $Content;
    }
}