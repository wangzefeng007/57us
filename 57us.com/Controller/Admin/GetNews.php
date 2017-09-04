<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/9/13
 * Time: 9:08
 */
class GetNews
{
    public function __construct()
    {
        IsLogin();
    }
    public function Index(){
    }
    public function Lists()
    {
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $SqlWhere = '';
        $PageUrl = '';
        $Url = $_GET['Url'];
        if ($Url) {
            $SqlWhere .= " and Url like '%$Url%'";
            $PageUrl .= "&OrderNo=$Url";
        }
        // 分页开始
        $Page = intval($_GET ['Page']);
        $Page = intval($Page) ? intval($Page) : 1;
        $PageSize = 15;
        $Rscount = $CaijiColumnUrlModule->GetListsNum($SqlWhere);
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=GetNews&Action=Lists&Page=' . $page . $PageUrl);
        }
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $CaijiColumnUrlModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                if ($value['ArticleType'] == 1) {
                    $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
                    $CategoryInfo = $TblStudyAbroadCategoryModule->GetInfoByKeyID($value['CategoryID']);
                    $Data['Data'][$key]['Category'] = $CategoryInfo['CategoryName'];
                } elseif ($value['ArticleType'] == 2) {
                    $TblTourCategoryModule = new TblTourCategoryModule();
                    $CategoryInfo = $TblTourCategoryModule->GetInfoByKeyID($value['CategoryID']);
                    $Data['Data'][$key]['Category'] = $CategoryInfo['CategoryName'];
                } elseif ($value['ArticleType'] == 3) {
                    $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
                    $CategoryInfo = $TblImmigrationCategoryModule->GetInfoByKeyID($value['CategoryID']);
                    $Data['Data'][$key]['Category'] = $CategoryInfo['CategoryName'];
                }
            }
            MultiPage($Data, 10);
        }
        $TopNavs = 'Lists';
        include template("CaijiNewsList");
    }

    public function Add()
    {
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblImmigrationCategoryModule.php';
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadCategoryModule.php';
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblTourCategoryModule.php';
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $TblTourCategoryModule = new TblTourCategoryModule();
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        if ($_GET['ColumnID']) {
            $ColumnID = $_GET['ColumnID'];
            $Details = $CaijiColumnUrlModule->GetInfoByKeyID($ColumnID);

        }
        $SqlWhere = '';
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
        if ($_POST) {
            $ColumnID = intval($_POST['ColumnID']);
            $Data['ArticleType'] = intval($_POST['ArticleType']);
            $CategoryID = $_POST['CategoryID'];
            foreach ($CategoryID as $value) {
                if ($value != '') {
                    $Data['CategoryID'] = intval($value);
                }
            }
            $Data['Url'] = trim($_POST['Url']);
            $Data['MyModule'] = trim($_POST['MyModule']);
            $Data['MyAction'] = trim($_POST['MyAction']);
            $Data['ColumnTitle'] = trim($_POST['ColumnTitle']);
            $Data['LastGetTime'] = date("Y-m-d H:i:s");
            $Data['Page'] = trim($_POST['Page']);
            if ($ColumnID > 0) {
                $UpdateInfo = $CaijiColumnUrlModule->UpdateInfoByKeyID($Data, $ColumnID);
            } else {
                $InsertInfo = $CaijiColumnUrlModule->InsertInfo($Data);
            }
            if ($InsertInfo || $UpdateInfo) {
                alertandback("操作成功");
            } else {
                alertandback("操作失败");
            }
        }
        include template("CaijiNewsAdd");
    }

    public function Delete()
    {
        $CaijiColumnUrlModule = new  CaijiColumnUrlModule();
        if ($_GET['ColumnID']) {
            $ColumnID = intval($_GET['ColumnID']);
            $Delete = $CaijiColumnUrlModule->DeleteByKeyID($ColumnID);
            if ($Delete) {
                alertandback("删除成功");
            } else {
                alertandback("删除失败");
            }
        }
    }

    public function StudyTourInfo()
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
        if ($Url == 'http://www.huantongusa.com/helper/hlist/10/1.html#right-box') {
            $ListZZ = '/<a class="show-detail" href="(.*)">.*<\/a>/isU';
            $Url = 'http://www.huantongusa.com/helper/hlist/10/1.html#right-box';
            $Html = file_get_contents($Url);
            preg_match_all($ListZZ, $Html, $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                $this->GetHuantongUSAInfo($Value, $ColumnID);
            }
        }
        if ($Url == 'http://www.aojiyouxue.com/zixun/tour_use_info/') {
            $ListZZ = '/<h3 style="width:696px;">.*<span>.*<\/span>.*<a href="(.*)" target="_blank">.*<\/a>.*<\/h3>/isU';
            $Url = 'http://www.aojiyouxue.com/zixun/tour_use_info/';
            $Html = file_get_contents($Url);
            preg_match_all($ListZZ, $Html, $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                $this->GetAoJiYouXueInfo($Value, $ColumnID);
            }
        }
        if ($Url == 'http://www.aojiyouxue.com/zixun/wenti/') {
            $ListZZ = '/<h3 style="width:696px;">.*<span>.*<\/span>.*<a href="(.*)" target="_blank">.*<\/a>.*<\/h3>/isU';
            $Url ='http://www.aojiyouxue.com/zixun/wenti/';
            $Html = file_get_contents ( $Url );
            preg_match_all($ListZZ, $Html, $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                $this->GetAoJiYouXueInfo($Value,$Url,$ColumnID);
            }
        }
        if ($Url =='http://www.aojiyouxue.com/zixun/instant/'){
            $ListZZ = '/<div class="title">.*<a href="(.*)" target="_blank">.*<\/a>.*<span>.*<\/span>.*<\/div>/isU';
            $Url ='http://www.aojiyouxue.com/zixun/instant/';
            $Url = 'http://www.aojiyouxue.com/zixun/instant/';
            $Html = file_get_contents($Url);
            preg_match_all($ListZZ, $Html, $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                $this->GetAoJiYouXueInfo($Value, $ColumnID);
            }
        }
        if ($Url == 'http://www.taisha.org/usa/studytour/') {
            $ListZZ = '/<div class="title">.*<a href="(.*)" target="_blank">.*<\/a>.*<span>.*<\/span>.*<\/div>/isU';
            $Url = 'http://www.taisha.org/usa/studytour/';
            $Html = file_get_contents($Url);
            preg_match_all($ListZZ, $Html, $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                $this->GetTaiShaInfo($Value, $ColumnID);
            }
        }
        if ($Url == 'http://www.yoosure.com/raiders/meiguoyouxue/') {
            $ListZZ = '/<p class="tit">.*<a href="(.*)" target="_blank">.*<\/a>.*<\/p>/isU';
            $Url = 'http://www.yoosure.com/raiders/meiguoyouxue-0-';
            for ($I = 1; $I < 10; $I++) {
                $Url = 'http://www.yoosure.com/raiders/meiguoyouxue-0-' . $I;
                $Html = file_get_contents($Url);
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Value) {
                    $this->GetYooSureInfo($Value, $ColumnID);
                }
            }
        }
        if ($Url == 'http://www.quyouxuele.com/meiguo/') {
            for ($I = 1; $I < 19; $I++) {
                $Url = 'http://www.quyouxuele.com/meiguo/list_14_' . $I . '.html';
                $Html = file_get_contents($Url);
                $ListZZ = '/<dt><a href="(.*)">.*<\/a>.*<\/dt>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Value) {
                    $this->GetQuYouXueLeInfo('http://www.quyouxuele.com' . $Value, $ColumnID);
                }
            }
        }
        if ($Url == 'http://youxue.baike.com/category-1302.html') {
            for ($I = 1; $I < 4; $I++) {
                $Url = 'http://youxue.baike.com/category-1302-' . $I . '.html';
                $Html = file_get_contents($Url);
                $ListZZ = '/<div class="t_5_t"><a href="(.*)" target="_blank">.*<\/a>.*<\/div>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Value) {
                    $this->GetbaikeInfo('http://youxue.baike.com' . $Value, $ColumnID);
                }
            }
        }
        if ($Url == 'http://www.xiaoma.com/SAT/ACT/list_152_1.html') {

            for ($I = 1; $I < 19; $I++) {
                $Url = 'http://www.xiaoma.com/SAT/ACT/list_152_' . $I . '.html';
                $Html = file_get_contents($Url);
                $ListZZ = '/<a class="list_ula">.*<\/a>.*<a href="(.*)" title=".*" target="_blank">.*<\/a>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Value) {
                    $this->GetXiaoMaInfo($Value, $ColumnID);
                }
            }
        }
        if ($Url == 'http://goabroad.xdf.cn/list_943_1.html') {

            for ($I = 1; $I < 6; $I++) {
                $Url = 'http://goabroad.xdf.cn/list_943_' . $I . '.html';
                $Html = file_get_contents($Url);
                $ListZZ1 = '/<ul.*class="txt_lists01 f-f0">(.*)<\/ul>/isU';
                $ListZZ2 = '/<li><a href="(.*)">.*<\/a><cite class="time">.*<\/cite><\/li>/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray1);
                preg_match_all($ListZZ2, $ReturnArray1[1][0], $ReturnArray2);
                foreach ($ReturnArray2[1] as $Value) {
                    $this->GetGoAbroadInfo($Value, $ColumnID);
                }
            }
        }
        if ($Url == 'http://www.actclub.org/article') {

            $Url = 'http://www.actclub.org/article';
            $Html = file_get_contents($Url);
            $ListZZ = '/<div class="title"><a href="(.*)">.*<\/a><\/div>/isU';
            preg_match_all($ListZZ, $Html, $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                $this->GetACTClubInfo($Value, $ColumnID);
            }
        }
        if ($Url == 'http://www.oneplusone.cn/test_info/') {
            for ($I = 1; $I < 19; $I++) {
                $Url = 'http://www.oneplusone.cn/test_info/list_57_' . $I . '.html';
                $Html = file_get_contents($Url);
                $ListZZ1 = '/<ul class="list areaZslist">(.*)<\/ul>/isU';
                $ListZZ2 = '/<a href="(.*)" target="_blank" title=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray1);
                preg_match_all($ListZZ2, $ReturnArray1[1][0], $ReturnArray2);
                foreach ($ReturnArray2[1] as $Value) {
                    $this->GetOnePlusOneInfo('http://www.oneplusone.cn' . $Value, $ColumnID);
                }
            }
        }
        if ($Url == 'http://www.usaer.net/life/living/') {
            for ($I = 0; $I < 10; $I++) {
                if ($I == 0) {
                    $Url = 'http://www.usaer.net/life/living/index.html';
                } else {
                    $Url = 'http://www.usaer.net/life/living/index_' . $I . '.html';
                }
                $Html = iconv("gbk", "utf-8", file_get_contents($Url));
                $ListZZ1 = '/<ul class="other">(.*)<\/ul>/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray1);
                $ListZZ2 = '/<li><span class="date">.*<\/span>.*<a href=".*" title=".*">.*<\/a>.*<a href="(.*)" title=".*">.*<\/a>/isU';
                preg_match_all($ListZZ2, $ReturnArray1[1][0], $ReturnArray2);
                foreach ($ReturnArray2[1] as $Value) {
                    $this->GetUsaerInfo($Value,$ColumnID);
                }
            }
        }
        if ($Url =='http://www.meten.cn/metenact/'){
            $Urls[0] = 'http://meten.cn/act/english/';
            $Urls[1] = 'http://meten.cn/act/math/';
            $Urls[2] = 'http://meten.cn/act/reading/';
            $Urls[3] = 'http://meten.cn/act/science/';
            $Urls[4] = 'http://meten.cn/act/writing/';
            $Urls[5] = 'http://meten.cn/act/news/';
            $Urls[6] = 'http://meten.cn/act/beikao/';
            $Urls[7] = 'http://meten.cn/act/zhenti/';
            $ListZZ = '/<a title="".*href="(.*)" target="_blank">.*<\/a>.*<span>.*<\/span>/isU';
            foreach ($Urls as $value){
                $Html = file_get_contents($value);
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val){
                    $this->GetMetenInfo('http://meten.cn'.$Val,$ColumnID);
                }
            }
        }
        if ($Url =='http://www.usaer.net/life/tour/'){
            for($I = 0; $I < 6; $I ++){
                if ($I ==0){
                    $Url= 'http://www.usaer.net/life/tour/index.html';
                }else{
                    $Url= 'http://www.usaer.net/life/tour/index_'.$I.'.html';
                }
                $Html = iconv("gbk","utf-8",file_get_contents($Url));
                $ListZZ1 = '/<ul class="other">(.*)<\/ul>/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray1);
                $ListZZ2 = '/<li><span class="date">.*<\/span>.*<a href=".*" title=".*">.*<\/a>.*<a href="(.*)" title=".*">.*<\/a>/isU';
                preg_match_all($ListZZ2, $ReturnArray1[1][0], $ReturnArray2);
                foreach ($ReturnArray2[1] as $Value) {
                    $this->GetUsaerInfo($Value,$ColumnID);
                }
            }
        }
        if ($Url =='http://www.usaer.net/life/feeling/'){
            for($I = 0; $I < 10; $I ++){
                if ($I ==0){
                    $Url= 'http://www.usaer.net/life/feeling/index.html';
                }else{
                    $Url= 'http://www.usaer.net/life/feeling/index_'.$I.'.html';
                }
                $Html = iconv("gbk","utf-8",file_get_contents($Url));
                $ListZZ1 = '/<ul class="other">(.*)<\/ul>/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray1);
                $ListZZ2 = '/<li><span class="date">.*<\/span>.*<a href=".*" title=".*">.*<\/a>.*<a href="(.*)" title=".*">.*<\/a>/isU';
                preg_match_all($ListZZ2, $ReturnArray1[1][0], $ReturnArray2);
                foreach ($ReturnArray2[1] as $Value) {
                    $this->GetUsaerInfo($Value,$ColumnID);
                }
            }
        }
        if ($Url =='http://www.usaer.net/life/job/'){
            for($I = 0; $I < 10; $I ++){
                if ($I ==0){
                    $Url= 'http://www.usaer.net/life/job/index.html';
                }else{
                    $Url= 'http://www.usaer.net/life/job/index_'.$I.'.html';
                }
                $Html = iconv("gbk","utf-8",file_get_contents($Url));
                $ListZZ1 = '/<ul class="other">(.*)<\/ul>/isU';
                preg_match_all($ListZZ1, $Html, $ReturnArray1);
                $ListZZ2 = '/<li><span class="date">.*<\/span>.*<a href=".*" title=".*">.*<\/a>.*<a href="(.*)" title=".*">.*<\/a>/isU';
                preg_match_all($ListZZ2, $ReturnArray1[1][0], $ReturnArray2);
                foreach ($ReturnArray2[1] as $Value) {
                    $this->GetUsaerInfo($Value,$ColumnID);
                }
            }
        }
        if ($Url =='http://us.tiandaoedu.com/experience/'){
            for($I = 1; $I < 101; $I ++){
                $Url=  'http://us.tiandaoedu.com/experience/list_71_'.$I.'1.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<p class="ptit"><a href="(.*)">.*<\/a><\/p>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Value) {
                    $this->GetTianDaoEduInfo('http://us.tiandaoedu.com'.$Value,$ColumnID,$ColumnUrlInfo['CategoryID']);

                }
            }
        }
        if ($Url =='http://toefl.shanghai.gedu.org/act/'){
            for($I = 1; $I < 2; $I ++){
                $Url=  'http://toefl.shanghai.gedu.org/act/list_133_'.$I.'.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><span>.*<\/span>.*<a href="(.*)" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Value) {
                    $this->GetShangHaiInfo($Value,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://www.wiseway.com.cn/'){
            $ListZZ = '/<span class="left w500 oh">.*<a href="(.*)" target="_blank" class="big" title=".*">.*<\/a><\/span>.*<span class="f6 right">(.*)<\/span>/isU';
            $arr=array(0=>"remenzhuanye",1=>"yuanxiaopaiming",2=>"liuxuerexun",3=>"qianzhengzhinan",4=>"gaozhongsheng",5=>"liuxuefeiyong",6=>"jingcaidayi");
            foreach ($arr as $value){
                $Url = 'http://www.wiseway.com.cn/'.$value.'/meiguo/';
                if (strstr($Url,'liuxuerexun')){
                    for ($i=1; $i < 12; $i++) {
                        $Url = 'http://www.wiseway.com.cn/liuxuerexun/mgzhuanye/?p='.$i;

                    }
                }else{
                    $Url = 'http://www.wiseway.com.cn/'.$value.'/meiguo/';
                }
                $Html = file_get_contents($Url);
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $key =>$val) {
                    //选择2016年发布的文章
                    if(strstr($ReturnArray[2][$key],'2016')){
                        $vals = $ReturnArray[1][$key];
                        if (strstr($vals,'www.wiseway.com.cn')) {
                            $this->GetWisewayInfo($vals,$ColumnID,$ColumnUrlInfo['CategoryID']);
                        }else{
                            $this->GetWisewayInfo('http://www.wiseway.com.cn'.$vals,$ColumnID,$ColumnUrlInfo['CategoryID']);
                        }
                    }
                }
            }
        }
        if ($Url =='http://www.taisha.org/usa/visa/'){
            $ListZZ = '/<div class="title">.*<a href="(.*)" target="_blank">.*<\/a>.*<span>.*<\/span>.*<\/div>/isU';
            for ($I = 2; $I < 11; $I ++) {
                $Url ='http://www.taisha.org/usa/visa/' . $I . '.html';
                $Html = file_get_contents ( $Url );
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetTaiShaInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
            $Url1 = 'http://www.taisha.org/usa/visa/index.html';
            $Html1 = file_get_contents ( $Url1 );
            preg_match_all($ListZZ, $Html1, $ReturnArray1);
            foreach ($ReturnArray1[1] as $Val1) {
                $this->GetTaiShaInfo($Val1,$ColumnID,$ColumnUrlInfo['CategoryID']);
            }
        }
        if ($Url =='http://www.taisha.org/usa/visa/'){
            $ListZZ = '/<div class="title">.*<a href="(.*)" target="_blank">.*<\/a>.*<span>.*<\/span>.*<\/div>/isU';
            for ($I = 2; $I < 11; $I ++) {
                $Url ='http://www.taisha.org/usa/visa/' . $I . '.html';
                $Html = file_get_contents ( $Url );
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetTaiShaInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
            $Url1 = 'http://www.taisha.org/usa/visa/index.html';
            $Html1 = file_get_contents ( $Url1 );
            preg_match_all($ListZZ, $Html1, $ReturnArray1);
            foreach ($ReturnArray1[1] as $Val1) {
                $this->GetTaiShaInfo($Val1,$ColumnID,$ColumnUrlInfo['CategoryID']);
            }
        }

        if ($Url =='http://us.liuxue360.com/information/article-93.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/information/article-93-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/information/article-89.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/information/article-89-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/news/article-6.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/news/article-6-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/information/article-90.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/information/article-90-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/plan/article-82.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/plan/article-82-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/plan/article-83.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/plan/article-83-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/plan/article-84.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/plan/article-83-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/information/article-95.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/information/article-95-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://news.liuxue360.com/language/article-325.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://news.liuxue360.com/language/article-325-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/information/article-97.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/information/article-97-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/information/article-94.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/information/article-94-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://us.liuxue360.com/information/article-92.html'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://us.liuxue360.com/information/article-92-' . $I . '.html';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuxueInfo($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/jiangxuejin/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/jiangxuejin/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/feiyong/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/feiyong/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/live/liuxuejiuye/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/live/liuxuejiuye/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/live/liuxuejingyan/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/live/liuxuejingyan/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/live/yishizhuxing/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/feiyong/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://ielts.liuxue86.com/yszw/'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://ielts.liuxue86.com/yszw/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a target="_blank" href="(.*)">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/live/xingqianzhunbei/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/live/xingqianzhunbei/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/yanjiushengliuxue/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/yanjiushengliuxue/';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)" target="_blank">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/benkeliuxue/'){
            $Url ='http://meiguo.liuxue86.com/benkeliuxue/';
            $ListZZ1= '/<div class="list_1">(.*)<\/div>/isU';
            $Html = file_get_contents ( $Url );
            $ListZZ = '/<a href="(.*)" target="_blank">.*<\/a>/isU';
            preg_match_all($ListZZ1, $Html, $Html1);
            preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
            foreach ($ReturnArray[1] as $Val) {
                $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/gaozhongliuxue/'){
            $Url ='http://meiguo.liuxue86.com/gaozhongliuxue/';
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $Html = file_get_contents ( $Url );
            $ListZZ = '/<a href="(.*)" target=".*">.*<\/a>/isU';
            preg_match_all($ListZZ1, $Html, $Html1);
            preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
            foreach ($ReturnArray[1] as $Val) {
                $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
            }
        }
        if ($Url =='http://visa.liuxue86.com/meiguo/jiqiao/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://visa.liuxue86.com/meiguo/jiqiao/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://visa.liuxue86.com/meiguo/banliliucheng/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://visa.liuxue86.com/meiguo/banliliucheng/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://visa.liuxue86.com/meiguo/qianzhengzixun/'){
            for ($I = 1; $I < 11; $I ++) {
                $Url ='http://visa.liuxue86.com/meiguo/qianzhengzixun/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/yuanxiaozhuanye/jiaoyutixi/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/yuanxiaozhuanye/jiaoyutixi/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/yuanxiaozhuanye/guojia/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/yuanxiaozhuanye/guojia/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/yuanxiaozhuanye/zhuanyezixun/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/yuanxiaozhuanye/zhuanyezixun/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
        if ($Url =='http://meiguo.liuxue86.com/yuanxiaozhuanye/remenzhuanye/'){
            for ($I = 1; $I < 3; $I ++) {
                $Url ='http://meiguo.liuxue86.com/yuanxiaozhuanye/remenzhuanye/' . $I . '.html';
                $ListZZ1= '/<div class="list_wei_C">(.*)<\/div>/isU';
                $Html = file_get_contents ( $Url );
                $ListZZ = '/<a href="(.*)"  title=".*" target=".*">.*<\/a>/isU';
                preg_match_all($ListZZ1, $Html, $Html1);
                preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
                foreach ($ReturnArray[1] as $Val) {
                    $this->GetLiuXue86Info($Val,$ColumnID,$ColumnUrlInfo['CategoryID']);
                }
            }
        }
    }
    //=================采集http://www.liuxue86.com/站点===============//
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
            $InsertInfo['ArticleType'] = 1;
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
    //=================采集http://us.liuxue360.com/站点===============//
    public function  GetLiuxueInfo($Url = '',$ColumnID='',$CategoryID=''){
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
        $Html = iconv("gbk","utf-8",file_get_contents($Url));
        $GuoLvArray = array(
            '/<img(.*)>/isU',
            '/style=\"(.*)\"/isU',
            '/<script(.*)script>/isU',
            '/<style(.*)style>/isU',
            '/留学360/isU',
            '/百利天下/isU'

        );
        $GuoLvJieGuo = array(
            '',
            '',
            '',
            '',
            '57留学',
            '57美国'
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);

        $ListZZ = '/<h1>(.*)<\/h1>.*<\/div>.*<div class="post_content">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        $Content = trim($ReturnArray[2][0]);
        $Content = $this->DoFilterInfo($Content);
        if ($InsertInfo['Title'] !=''){
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $Content =addslashes($Content);
            $InsertInfo['Content'] =trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
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
    //=================采集http://toefl.shanghai.gedu.org/站点===============//
    public function GetWisewayInfo($Url = '',$ColumnID='',$CategoryID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
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
            '/威久留学/isU'
        );
        $GuoLvJieGuo = array(
            '',
            '',
            '',
            '',
            '57美国'
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);
        $ListZZ = '/<h1 class=".*">(.*)<\/h1>.*<\/div>.*<div class="text-in font14px top10px l30 articleContent">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        $Content = trim($ReturnArray[2][0]);
        $Content = $this->DoFilterInfo($Content);
        // 添加临时文章表
        // $UrlInfo = $DB->GetOne("select * from $this->UrlTable where Url='$ListsUrl'");
        if ($InsertInfo['Title'] !=''){
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $Content =addslashes($Content);
            $InsertInfo['Content'] =trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
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
    //=================采集http://toefl.shanghai.gedu.org/站点===============//
    public function GetShangHaiInfo($Url = '',$ColumnID='',$CategoryID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
//        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' .$Url .'\'');
//        if ($UrlInfo) {
//            return 0;
//        }
        $Html = file_get_contents($Url);
        $GuoLvArray = array(
            '/上海环球教育ACT/isU',
            '/上海环球教育/isU',
            '/环球教育/isU',
            '/上海ACT/isU',
            '/环球ACT/isU'
        );
        $GuoLvJieGuo = array(
            '57美国留学',
            '57美国留学',
            '57美国留学',
            '57美国留学',
            '57美国留学'
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);
        $ListZZ = '/<h1>(.*)<\/h1>.*<div class="zw">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] !=''){
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content =addslashes($Content);
            $InsertInfo['Content'] =trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
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
    //=================采集http://us.tiandaoedu.com/站点===============//
    public function GetTianDaoEduInfo($Url = '',$ColumnID='',$CategoryID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' .$Url .'\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = file_get_contents($Url);
        $ListZZ = '/<p class="wztit yh">(.*)<\/p>.*<div class="wzy_bot">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] !=''){
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content =addslashes($Content);
            $InsertInfo['Content'] =trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
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
    //=================采集http://meten.cn/站点===============//
    public function GetMetenInfo($Url = '',$ColumnID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' .$Url .'\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = file_get_contents($Url);
        $GuoLvArray = array(
            '/美联出国/isU',
            '/010-5338-2562/isU'
        );
        $GuoLvJieGuo = array(
            '57美国留学',
            '0592-5919203'
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);
        $ListZZ = '/<h1 id="title">(.*)<\/h1>.*<div class="page" id="content">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] !=''){
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content =addslashes($Content);
            $InsertInfo['Content'] =trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
            $InsertInfo['CategoryID'] = '1159';//ACT
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
    //=================采集http://www.usaer.net/站点===============//
    public function GetUsaerInfo($Url = '',$ColumnID=''){
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' .$Url .'\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = iconv("gbk","utf-8",file_get_contents($Url));
        $ListZZ = '/<h2>(.*)<\/h2>.*<div class="maintext">(.*)<div class="nextxx" id="nextxx"><\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] !=''){
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content =addslashes($Content);
            $InsertInfo['Content'] =trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
            $InsertInfo['CategoryID'] = '1084';
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
    //=================采集http://www.oneplusone.cn/站点===============//
    public function GetOnePlusOneInfo($Url = '', $ColumnID = '')
    {
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = file_get_contents($Url);
    }

    //=================采集http://www.actclub.org/站点===============//
    public function GetACTClubInfo($Url = '', $ColumnID = '')
    {
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = file_get_contents($Url);
        $ListZZ = '/<p class="title1 f-f0">(.*)<\/p>.*<div class="air_con f-f0">(.*)<\/div>/isU';
    }

    //=================采集http://goabroad.xdf.cn/站点===============//
    public function GetGoAbroadInfo($Url = '', $ColumnID = '')
    {
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = file_get_contents($Url);
        $ListZZ = '/<p class="title1 f-f0">(.*)<\/p>.*<div class="air_con f-f0">(.*)<\/div>/isU';
        $GuoLvArray = array(
            '/新东方网留学频道/isU',
            '/新东方/isU'
        );
        $GuoLvJieGuo = array(
            '57美国留学',
            '57美国'
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content = addslashes($Content);
            $InsertInfo['Content'] = trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
            $InsertInfo['CategoryID'] = '1160';//ACT数学
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            if ($Insert) {
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

    //=================采集http://www.xiaoma.com/站点===============//
    public function  GetXiaoMaInfo($Url = '', $ColumnID = '')
    {
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = file_get_contents($Url);
        $ListZZ = '/<h1>(.*)<\/h1>.* <div class="containerCont">(.*)<div style="TEXT-ALIGN: center">/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $Content = preg_replace("/<(\/?img.*?)>/si", "", $Content);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content = addslashes($Content);
            $InsertInfo['Content'] = trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
            $InsertInfo['CategoryID'] = '1160';//ACT数学
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            if ($Insert) {
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


    //=================采集http://www.quyouxuele.com/站点===============//
    public function  GetQuYouXueLeInfo($Url = '', $ColumnID = '')
    {
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = iconv("gbk", "utf-8", file_get_contents($Url));
        $ListZZ = '/<div class="d_wenzhang3">.*<h2>(.*)<\/h2>.*<div class="acontent">(.*)<\/div>/isU';
        $GuoLvArray = array(
            '/<a(.*)>/isU',
            '/<\/a>/isU',
            '/style=\"(.*)\"/isU',
            '/<script(.*)script>/isU',
            '/<style(.*)style>/isU',
        );
        $GuoLvJieGuo = array(
            '',
            '',
            '',
            '',
            '',
        );
        $Html = preg_replace($GuoLvArray, $GuoLvJieGuo, $Html);
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $Content = preg_replace("/<(\/?a.*?)>/si", "", $Content);
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content = addslashes($Content);
            $InsertInfo['Content'] = trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
            $InsertInfo['CategoryID'] = '1081';//游学游记
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            if ($Insert) {
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

    //=================采集http://www.huantongusa.com/站点===============//
    public function GetHuantongUSAInfo($Url = '', $ColumnID = '')
    {
        if ($Url == '') {
            return 0;
        }

        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if ($UrlInfo) {
            return 0;
        }
        $Html = file_get_contents($Url);
        $ListZZ = '/<span class="f16 strong font-gray">(.*)<\/span>.*<!-- -->.*<div>(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content =addslashes($Content);
            $InsertInfo['Content'] =trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
            $InsertInfo['CategoryID'] = '1157';//游学须知 ID
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            // 添加URL总表
            if ($Insert) {
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

    //=================采集ttp://youxue.baike.com/站点===============//
    public function GetbaikeInfo($Url = '', $ColumnID = '')
    {
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if (!empty($UrlInfo)) {
            return 0;
        }
        $ListZZ = '/<title>(.*) - 游学百科<\/title>.*<div class="nw_cot">(.*)<ul class="news_rel clearfix">/isU';
        $Html = file_get_contents($Url);
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $Content = preg_replace("/<(\/?img.*?)>/si", "", $Content);
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content = preg_replace('/style=\"(.*)\"/isU', '', $Content);
            $Content = addslashes($Content);
            $InsertInfo['Content'] = trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
            $InsertInfo['CategoryID'] = '1081';//游学游记ID
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
            if ($Insert) {
                // 添加URL总表
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

    //=================采集http://www.yoosure.com/站点===============//
    public function GetYooSureInfo($Url = '', $ColumnID = '')
    {
        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if (!empty($UrlInfo)) {
            return 0;
        }
        $Html = file_get_contents($Url);
        $ListZZ = '/<h1>(.*)<\/h1>.*<div class="gl-contxt">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $InsertInfo['Title'] = trim($ReturnArray[1][0]);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        $Content = $this->DoFilterInfo($ReturnArray[2][0]);
        $Content = preg_replace('/style=\"(.*)\"/isU', '', $Content);
        if ($InsertInfo['Title'] !=''){
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Title'] = addslashes($InsertInfo['Title']);
            $Content = addslashes($Content);
            $InsertInfo['Content'] = trim($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($InsertInfo['Content'], 180)));
            $InsertInfo['CategoryID'] = '1081';//游学游记ID
            $Insert = $CaijiArticleModule->InsertInfo($InsertInfo);
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

    //=================采集http://www.aojiyouxue.com/站点===============//
    public function GetAoJiYouXueInfo($Url = '', $ListsUrl,$ColumnID = ''){

        if ($Url == '') {
            return 0;
        }
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $UrlInfo = $CaijiUrlAllModule->GetInfoByWhere(' and `Url`=\'' . $Url . '\'');
        if (!empty($UrlInfo)) {
            return 0;
        }
        unset($UrlInfo);
        $Html = file_get_contents($Url);
        $ListZZ = '/<h2>(.*)<\/h2>.*<div class="newsTxt">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Title = trim($ReturnArray[1][0]);
        $Content = trim($ReturnArray[2][0]);
        $Content = $this->DoFilterInfo($Content);
        $Content = str_replace('http://www.aojiyouxue.com', 'http://study.57us.com', $Content);
        $Content = str_replace('澳际国际游学', '57美国游学', $Content);
        $Content = str_replace('400-601-0022', '', $Content);
        $Content = str_replace('<img alt="" src="http://img.aoji.cn/2015/0730/kjcbJyHeXIgT.jpg" style="width: 300px; height: 300px" />', '', $Content);
        $ImgURL = '/<img alt="" src="(.*)" style=".*" \/>/isU';
        preg_match_all($ImgURL, $Content, $ImgArray);
        $InsertInfo['Title'] = addslashes($Title);
        $ArticleInfo = $CaijiArticleModule->GetInfoByWhere(' and `Title`=\'' .$InsertInfo['Title'] .'\'');
        if ($ArticleInfo){
            return 0;
        }
        if ($InsertInfo['Title'] != '') {
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['Content'] = addslashes($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($Content, 180)));
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            if ($ListsUrl == 'http://www.aojiyouxue.com/zixun/tour_use_info/') {
                $InsertInfo['CategoryID'] = '1081';//游学游记ID
            }
            if ($ListsUrl == 'http://www.aojiyouxue.com/zixun/wenti/') {
                $InsertInfo['CategoryID'] = '1157';//游学须知ID
            }
            if ($ListsUrl == 'http://www.aojiyouxue.com/zixun/instant/') {
                $InsertInfo['CategoryID'] = '1080';//游学答疑ID
            }
            $CaijiArticleModule = new CaijiArticleModule();
            $Insert= $CaijiArticleModule->InsertInfo($InsertInfo);
            if ($Insert){
                // 添加URL总表
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

    //=================采集http://www.taisha.org/usa/studytour/站点===============//
    public function GetTaiShaInfo($Url = '', $ColumnID = '',$CategoryID='')
    {
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
            $InsertInfo['ArticleType'] = 1;
            $InsertInfo['Content'] = addslashes($Content);
            $InsertInfo['Description'] = trim(strip_tags(_substr($Content, 180)));
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            if ($CategoryID !=''){
                $InsertInfo['CategoryID'] = $CategoryID;
            }else{
                $InsertInfo['CategoryID'] = '1069';//游学ID
            }
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