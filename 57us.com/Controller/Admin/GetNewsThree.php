<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/9/26
 * Time: 14:25
 */
class GetNewsThree
{
    public function __construct()
    {
        IsLogin();
    }
    public function Index(){
    }
    public function ImmigrantInfo()
    {
        if ($_GET['ColumnID']) {
            $ColumnID = $_GET['ColumnID'];
            $CaijiColumnUrlModule = new CaijiColumnUrlModule();
            $ColumnUrlInfo = $CaijiColumnUrlModule->GetInfoByKeyID($ColumnID);
            $Url = $ColumnUrlInfo['Url'];
        }
        echo '抓取链接：'.$Url;
        if ($_GET['url']) {
            $Url = $_GET['url'];
        }
        if ($Url == 'http://www.taisha.org/usa/immigrate/') {
            $ListZZ = '/<div class="title">.*<a href="(.*)" target="_blank">.*<\/a>.*<span>.*<\/span>.*<\/div>/isU';
            for ($I = 2; $I < 11; $I++) {
                $Url = 'http://www.taisha.org/usa/immigrate/' . $I . '.html';
                $Html = file_get_contents($Url);
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetTaiShaInfo($Val, $Url);
                }
            }
            $Url1 = 'http://www.taisha.org/usa/immigrate/index.html';
            $Html1 = file_get_contents($Url1);
            preg_match_all($ListZZ, $Html1, $ReturnArray1);
            foreach ($ReturnArray1[1] as $Val1) {
                $this->GetTaiShaInfo($Val1,$ColumnID,$ColumnUrlInfo['CategoryID']);
            }
        }
        if ($Url =='http://yimin.liuxue86.com/meiguo/yiminzhengce/'){
            for ($I = 1; $I < 6; $I ++) {
                $Url ='http://yimin.liuxue86.com/meiguo/yiminzhengce/' . $I . '.html';
                $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ =  '/<a target="_blank" href="(.*)">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }

        if ($Url =='http://yimin.liuxue86.com/meiguo/yiminzixun/'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://yimin.liuxue86.com/meiguo/yiminzixun/' . $I . '.html';
                $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ =  '/<a target="_blank" href="(.*)">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }

        if ($Url =='http://yimin.liuxue86.com/meiguo/yiminshenghuo/'){
            for ($I = 1; $I < 8; $I ++) {
                $Url ='http://yimin.liuxue86.com/meiguo/yiminshenghuo/' . $I . '.html';
                $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ =  '/<a target="_blank" href="(.*)">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }

        if ($Url =='http://yimin.liuxue86.com/meiguo/yiminjingyan/'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://yimin.liuxue86.com/meiguo/yiminjingyan/' . $I . '.html';
                $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a target="_blank" href="(.*)">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
    }

    public function GetLiuXue86Info($Url = '',$ColumnID='',$CategoryID=''){
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
            '/style=\"(.*)\"/isU',
            '/<script(.*)script>/isU',
            '/<style(.*)style>/isU',
            '/出国移民网/isU',
            '/yimin.liuxue86.com/isU',
            '/meiguo.liuxue86.com/isU',
            '/www.liuxue86.com/isU',
            '/出国留学网/isU'
        );
        $GuoLvJieGuo = array(
            '',
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
        if (strstr($Html, '<p>2016')){

        }
        //选择2016年数据
        if (strstr($Html, '<p>2016')){
            if (strstr($Html, '<div class="main_zhengw">')) {
                $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"main_zhengw\">(.*)<div class=\"guanggao3 clearfix\">/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                $Title = trim($ReturnArray[1][0]);
                $Content = trim($ReturnArray[2][0]);
                //相同class="main_zhengw" 尾部不一样<div id="pages" class=" clearfix">
                if (strstr($Html, '<div id="pages" class=" clearfix">')){
                    $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"main_zhengw\">(.*)<div id="pages" class=" clearfix">/isU';
                    preg_match_all($ListZZ, $Html, $ReturnArray);
                    $Title = trim($ReturnArray[1][0]);
                    $Content = trim($ReturnArray[2][0]);

                }elseif (strstr($Html, '<div class="ye_780_four3">')){
                    $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"main_zhengw\">(.*)<div class="ye_780_four3">/isU';
                    preg_match_all($ListZZ, $Html, $ReturnArray);
                    $Title = trim($ReturnArray[1][0]);
                    $Content = trim($ReturnArray[2][0]);
                }elseif (strstr($Html, '<div class="C_xinj">')){
                    $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"main_zhengw\">(.*)<div class="C_xinj">/isU';
                    preg_match_all($ListZZ, $Html, $ReturnArray);
                    $Title = trim($ReturnArray[1][0]);
                    $Content = trim($ReturnArray[2][0]);
                }elseif (strstr($Html, '<div class="zhengw_ccc">')) {
                    $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"zhengw_ccc\">(.*)<div class="zhengw_ccc">/isU';
                    preg_match_all($ListZZ, $Html, $ReturnArray);
                    $Title = trim($ReturnArray[1][0]);
                    $Content = trim($ReturnArray[2][0]);
                }
            } else {
                $ListZZ = '/<div id=\"content_head\">(.*)原文来源/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                $Html = $ReturnArray[0][0];
                unset($ReturnArray, $ListZZ);
                $ListZZ = '/<h1>(.*)<\/h1>.*<div id=\"digest\">(.*)原文来源/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                $Title = trim($ReturnArray[1][0]);
                $Content = trim($ReturnArray[2][0]);
            }
        }
        $Content = $this->DoFilterInfo($Content);
        // 添加临时文章表
        // $UrlInfo = $DB->GetOne("select * from $this->UrlTable where Url='$ListsUrl'");
        $InsertInfo['Title'] = addslashes($Title);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 3;
            $InsertInfo['Content'] = addslashes($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($Content, 180)));
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['CategoryID'] = intval($CategoryID);
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            if ($Insert) {
                $CaijiColumnUrlModule = new CaijiColumnUrlModule();
                $InsertUrlAllInfo['GetTime'] = date("Y-m-d H:i:s");
                $InsertUrlAllInfo['Url'] = $Url;
                $InsertUrl = $CaijiUrlAllModule->InsertInfo($InsertUrlAllInfo);
                $Data['LastGetTime'] = date("Y-m-d H:i:s");
                $CaijiColumnUrlModule->UpdateInfoByKeyID($Data,$ColumnID);
                $CaijiColumnUrlModule->UpdateNum($ColumnID);
            }
        }
        return 1;
    }
    public function GetTaiShaInfo($Url = '', $ColumnID = '',$CategoryID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if (!empty($UrlInfo)) {
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
            '/太傻网/isU',
            '/太傻留学/isU'
        );
        $GuoLvJieGuo = array(
            '',
            '',
            '',
            '',
            '',
            '57美国',
            '57美国'
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);
        $ListZZ = '/<div class="txt_title">.*<h2>(.*)<\/h2>.*<div>.*<span>.*<\/span>.*<\/div>.*<\/div>.*<div class="txt_content">(.*)<\/div>/isU';
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
            $InsertInfo['ArticleType'] = 3;
            $InsertInfo['Content'] = addslashes($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($Content, 180)));
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['CategoryID'] = $CategoryID;
            $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            $InsertUrlAllInfo['GetTime'] = date("Y-m-d H:i:s");
            $InsertUrlAllInfo['Url'] = $Url;
            $InsertUrl = $CaijiUrlAllModule->InsertInfo($InsertUrlAllInfo);
            $Data['LastGetTime'] = date("Y-m-d H:i:s");
            $CaijiColumnUrlModule = new CaijiColumnUrlModule();
            $CaijiColumnUrlModule->UpdateInfoByKeyID($Data,$ColumnID);
            $CaijiColumnUrlModule->UpdateNum($ColumnID);
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