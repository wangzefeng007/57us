<?php

class StudyAbroadNewsArticle
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadCategoryModule.php';
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadKeywordModule.php';
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadModule.php';
        $this->TopModule = "StudyAbroadNews";
    }

    public function CategoryLists()
    {
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $CategoryList = $TblStudyAbroadCategoryModule->GetInfoByWhere(" order by GlobalDisplayOrder asc", true);
        if ($CategoryList) {
            foreach ($CategoryList as $key => $val) {
                $GlobalDisplayOrder[$key] = $val['GlobalDisplayOrder'];
            }
            array_multisort($GlobalDisplayOrder, SORT_NATURAL, $CategoryList);
        }
        return $CategoryList;
    }

    /**
     * @desc  文章列表
     */
    public function Lists()
    {
        $AdminID = $_SESSION['AdminID'];
        $CategoryList = $this->CategoryLists();
        $MysqlWhere = '';
        $PageUrl = '';
        $SearByTitle = trim($_GET['SearByTitle']);
        $SearByCategory = intval($_GET['SearByCategory']);
        $AddTime = trim($_GET['AddTime']);
        $ID = trim($_GET['ID']);
        if ($SearByCategory) {
            $MysqlWhere .= " and CategoryID=$SearByCategory";
            $PageUrl .= "&SearByCategory=$SearByCategory";
        }
        if ($SearByTitle) {
            $MysqlWhere .= " and Title like '%$SearByTitle%'";
            $PageUrl .= "&SearByTitle=$SearByTitle";
        }
        if ($AddTime) {
            $MysqlWhere .= " and AddTime like '%$AddTime%'";
            $PageUrl .= "&AddTime=$AddTime";
        }
        if ($ID) {
            $MysqlWhere .= " and AdminID=$ID";
            $PageUrl.="&ID=$ID";
        }
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $Page = intval($_REQUEST['Page']) ? intval($_REQUEST['Page']) : 1;
        $ListsNum = $TblStudyAbroadModule->GetListsNum($MysqlWhere);
        $Rscount = $ListsNum ['Num'];
        $PageSize = 10;
        if ($Rscount) {
            $Data ['RecordCount'] = $Rscount;
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            $Data['Data'] = $TblStudyAbroadModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            if ($Data['Data']) {
                $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
                foreach ($Data['Data'] as $Key => $Value) {
                    $CategoryInfo = $TblStudyAbroadCategoryModule->GetInfoByKeyID($Data['Data'][$Key]['CategoryID']);
                    $Data['Data'][$Key]['CategoryName'] = $CategoryInfo['CategoryName'];
                }
            }
            MultiPage($Data, 10);
        }
        $TopNavs = "ArticleLists";
        include template("StudyAbroadNewsArticleLists");
    }

    /**
     * @desc 编辑文章
     */
    public function Add()
    {
        $CategoryList = $this->CategoryLists();
        $StudyID = intval($_GET['StudyID']);
        if ($StudyID) {
            $TblStudyAbroadModule = new TblStudyAbroadModule();
            $StudyAboradInfo = $TblStudyAbroadModule->GetInfoByKeyID($StudyID);
            $KeywordModule = new TblStudyAbroadKeywordModule();
            $KeywordInfo = $KeywordModule->GetInfoByWhere('',true);
            foreach ($KeywordInfo as $key=>$value){
                $in=strstr($StudyAboradInfo['Content'],$value['Keyword']);
                if ($in){
                    $Data[$key]['KeyID'] = $value['KeyID'];
                    $Data[$key]['Keyword'] = $value['Keyword'];
                }
            }
            if (strpos($StudyAboradInfo['Keywords'],',')){
                $Keywords =explode(',',$StudyAboradInfo['Keywords']);
            foreach ($Keywords as $k=>$val){
                $Keyword[$k] = $KeywordModule->GetInfoByKeyID($val);
            }
            foreach ($Data as $key=>$value){
                foreach ($Keyword as $K=>$V){
                    if ($V['KeyID']==$value['KeyID']){
                        unset($Keyword[$K]);
                    }
                }
            }
            $Data = array_merge($Data,$Keyword);
            }
            $StudyAboradInfo['Content'] = StrReplaceImages($StudyAboradInfo['Content']);
            $StudyAboradInfo['Content'] = DoEditorContent($StudyAboradInfo['Content']);
            $StudyAboradInfo['Content']=addslashes($StudyAboradInfo['Content']);
        }
        $TopNavs = "ArticleAdd";
        include template("StudyAbroadNewsArticleAdd");
    }

    /**
     * @desc  保存文章
     */
    public function Save()
    {
        $StudyID = intval($_POST['StudyID']);
        $Data['CategoryID'] = intval($_POST['CategoryID']);
        $Data['Title'] = trim($_POST['Title']);
        if ($Data['Title']=='') {
            alertandback('文章保存失败,标题不能为空.');
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
        $Data['M1'] = intval($_POST['M1']);
        $Data['M2'] = intval($_POST['M2']);
        $Data['M3'] = intval($_POST['M3']);
        $Data['AdminID'] = $_SESSION['AdminID'];//添加AdminID
        //上传图片
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        if ($_FILES['Image']['size'][0] > 0) {
            $Upload = new MultiUpload ('Image');
            $File = $Upload->upload();
            $Picture = $File[0] ? $File[0] : '';
            $Data ['Image'] = $Picture;
        }
        $now_time = time();
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        if ($StudyID) {
            if (isset($Data['Image'])) {
                $StudyAboradInfo = $TblStudyAbroadModule->GetInfoByKeyID($StudyID);
                if ($StudyAboradInfo['Image']) {
                    DelFromImgServ($StudyAboradInfo['Image']);
                }
            }
            $Data['UpdateTime'] = date('Y-m-d H:i:s', $now_time);
            $result = $TblStudyAbroadModule->UpdateInfoByKeyID($Data, $StudyID);
            if ($result) {
                alertandgotopage('文章保存成功!', "/index.php?Module=StudyAbroadNewsArticle&Action=Add&StudyID=$StudyID");
            } else {
                alertandback('文章保存失败,请重新编辑.');
            }
        } else {
            $Data['UpdateTime'] = date('Y-m-d H:i:s', $now_time);
            $Data['AddTime'] = date('Y-m-d H:i:s', $now_time);
            $Data['ViewCount'] = 0;
            $result = $TblStudyAbroadModule->InsertInfo($Data);
            if ($result) {
                alertandgotopage('文章保存成功!', "/index.php?Module=StudyAbroadNewsArticle&Action=Add");
            } else {
                alertandback('文章保存失败,请重新编辑.');
            }
        }
    }

    /**
     * @desc  删除文章
     */
    public function Delete()
    {
        $ArticleIDs = $_REQUEST['StudyID'];
        if (!empty($ArticleIDs)) {
            $TblStudyAbroadModule = new TblStudyAbroadModule();
            if (is_array($ArticleIDs)) {
                foreach ($ArticleIDs as $ArticleID) {
                    $StudyAboradInfo = $TblStudyAbroadModule->GetInfoByKeyID($ArticleID);
                    if ($TblStudyAbroadModule->DeleteByKeyID($ArticleID)) {
                        if ($StudyAboradInfo['Image']) {
                            DelFromImgServ($StudyAboradInfo['Image']);
                        }
                    }
                }
                alertandgotopage('已完成删除操作!', $_SERVER['HTTP_REFERER']);
            } else {
                $StudyAboradInfo = $TblStudyAbroadModule->GetInfoByKeyID($ArticleIDs);
                if ($TblStudyAbroadModule->DeleteByKeyID($ArticleIDs)) {
                    if ($StudyAboradInfo['Image']) {
                        DelFromImgServ($StudyAboradInfo['Image']);
                    }
                    alertandgotopage('已完成删除操作!', $_SERVER['HTTP_REFERER']);
                } else {
                    alertandback('删除失败!');
                }
            }
        } else {
            alertandback('您没有选择准备删除的记录!');
        }
    }
    //留学标签列表
    public function TagLists(){
        $TopNavs = 'TagLists';
        $Navs = 'StudyAbroadNewsArticle';
        $KeywordModule = new TblStudyAbroadKeywordModule();
        $Page = $_GET['Page']?intval($_GET['Page']):1;
        if ($Page < 1) {
            $Page = 1;
        }
        $pageSize = 150;
        $Rscount = $KeywordModule->GetListsNum('');
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($pageSize ? $pageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $pageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $KeywordList= $KeywordModule->GetLists('', $Offset, $Data ['PageSize']);
            MultiPage($Data,150);
            $PageMax = intval($Data ['PageCount']);
        }
        include template("NewsTagLists");
    }
    //留学标签编辑
    public function TagEdit(){
        $TopNavs = 'TagEdit';
        $Navs = 'StudyAbroadNewsArticle';
        $KeywordModule = new TblStudyAbroadKeywordModule();
        if ($_POST){
            $ID = intval($_POST['ID']);
            $Data['Keyword'] = trim($_POST['Keyword']);
            $Data['Traffic'] = intval($_POST['Traffic']);
            $Data['Hot'] = intval($_POST['Hot']);
            $Data['Sort'] = intval($_POST['Sort']);
            if (empty($ID)){
                $KeyID = $KeywordModule->InsertInfo($Data);
                if ($KeyID){
                    alertandgotopage('添加成功!','/index.php?Module=StudyAbroadNewsArticle&Action=TagLists');
                }else{
                    alertandback('添加失败!');
                }
            }else{
                $Update = $KeywordModule->UpdateInfoByKeyID($Data,$ID);
                if ($Update==true){
                    alertandback('操作成功!');
                }else{
                    alertandback('操作未更新!');
                }
            }
        }
        if ($_GET['ID']){
            $ID = $_GET['ID'];
            $TagInfo = $KeywordModule->GetInfoByKeyID($ID);
        }
        include template("NewsTagEdit");
    }
    //删除留学标签
    public function DeleteTag(){
        $KeywordModule = new TblStudyAbroadKeywordModule();
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        if ($_GET['ID']) {
            $ID = $_GET['ID'];
            $StudyInfo = $TblStudyAbroadModule->GetInfoByWhere(' and  MATCH(`Keywords`) AGAINST ('.$ID.' IN BOOLEAN MODE)',true);
            if (!empty($StudyInfo)){
                foreach ($StudyInfo as $key=>$value){
                    $Keywords=str_replace($ID,'',$value['Keywords']);
                    $Date = explode(',',$Keywords);
                    $Num = count($Date);
                    if ($Date[0]!=''||$Num>1){
                        foreach ($Date as $val){
                            if ($val!=''){
                                $In .= $val.',';
                            }
                        }
                        $In = substr($In, 0, -1);
                    }else{
                        $In=$Keywords;
                    }
                    $UpdateInfo = $TblStudyAbroadModule->UpdateInfoByKeyID(array('Keywords'=>$In),$value['StudyID']);
                    unset($In);
                }
            }
            if ($KeywordModule->DeleteByKeyID($ID)){
                alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);
            }else{
                alertandback('删除失败!');
            }
        }else{
            alertandback('您没有选择要删除的标签!');
        }
    }
}

?>