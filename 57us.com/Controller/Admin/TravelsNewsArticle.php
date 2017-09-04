<?php

class TravelsNewsArticle
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH.'/Modules/News/Class.TblTravelsCategoriesModule.php';
        include SYSTEM_ROOTPATH.'/Modules/News/Class.TblTravelsKeywordModule.php';
        include SYSTEM_ROOTPATH.'/Modules/News/Class.TblTravelsModule.php';
        $this->TopModule="TravelsNews";
    }
    
    public function CategoryLists(){
        $TblTravelsCategoriesModule=new TblTravelsCategoriesModule();
        $CategoryList=$TblTravelsCategoriesModule->GetInfoByWhere(" order by GlobalDisplayOrder asc",true);
        if($CategoryList){
            foreach($CategoryList as $key=>$val){
                $GlobalDisplayOrder[$key]= $val['GlobalDisplayOrder'];
            }
            array_multisort($GlobalDisplayOrder,SORT_NATURAL,$CategoryList);
        }
        return $CategoryList;
    }
    
    //文章列表
    public function Lists(){
        $AdminID = $_SESSION['AdminID'];
        $CategoryList=$this->CategoryLists();
        $MysqlWhere='';
        $PageUrl='';
        $SearByTitle=trim($_GET['SearByTitle']);
        $SearByCategory=intval($_GET['SearByCategory']);
        $AddTime = trim($_GET['AddTime']);
        $ID = trim($_GET['ID']);
        if($SearByCategory){
            $MysqlWhere.=" and CategoryID=$SearByCategory";
            $PageUrl.="&SearByCategory=$SearByCategory";
        }
        if($SearByTitle){
            $MysqlWhere.=" and Title like '%$SearByTitle%'";
            $PageUrl.="&SearByTitle=$SearByTitle";
        }
        if ($AddTime) {
            $MysqlWhere .= " and AddTime like '%$AddTime%'";
            $PageUrl.="&AddTime=$AddTime";
        }
        if ($ID) {
            $MysqlWhere .= " and AdminID=$ID";
            $PageUrl.="&ID=$ID";
        }
        $TblTravelsModule = new TblTravelsModule();
        $Page = intval($_REQUEST['Page'])?intval($_REQUEST['Page']):1;
        $ListsNum = $TblTravelsModule->GetListsNum($MysqlWhere);
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
            $Data['Data'] = $TblTravelsModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            if($Data['Data']){
                $TblTravelsCategoriesModule=new TblTravelsCategoriesModule();
                foreach ($Data['Data'] as $Key => $Value) {
                        $CategoryInfo = $TblTravelsCategoriesModule->GetInfoByKeyID($Data['Data'][$Key]['CategoryID']);
                        $Data['Data'][$Key]['CategoryName'] = $CategoryInfo['CategoryName'];
                }
            }
            MultiPage($Data,10);
        }
        $TopNavs="ArticleLists";
        include template("TravelsNewsArticleLists");
    }
    
    //编辑文章
    public function Add(){
        $CategoryList=$this->CategoryLists();
        $TravelsID=intval($_GET['TravelsID']);
        if($TravelsID){
            $TblTravelsModule = new TblTravelsModule();
            $TravelsInfo=$TblTravelsModule->GetInfoByKeyID($TravelsID);
            $TravelsInfo['TripPlan']=json_decode($TravelsInfo['TripPlan'],true);
            $TravelsInfo['Content']=json_decode($TravelsInfo['Content'],true);
            $TravelsInfo['TripInformation']=json_decode($TravelsInfo['TripInformation'],true);
            $KeywordModule = new TblTravelsKeywordModule();
            $KeywordInfo = $KeywordModule->GetInfoByWhere('',true);
            foreach ($KeywordInfo as $key=>$value){
                foreach ($TravelsInfo['Content'] as $val){
                    $in=strstr($val['Content'],$value['Keyword']);
                    if ($in){
                        $Data[$key]['KeyID'] = $value['KeyID'];
                        $Data[$key]['Keyword'] = $value['Keyword'];
                    }
                }
            }
            if (strpos($TravelsInfo['Keywords'],',')){
                $Keywords =explode(',',$TravelsInfo['Keywords']);
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
        }
        $TopNavs="ArticleAdd";
        include template("TravelsNewsArticleAdd");
    }
    
    //保存文章
    public function Save(){
        $TravelsID = intval($_POST['TravelsID']);
        $Data['CategoryID'] = intval($_POST['CategoryID']);
        $Data['Title'] = trim($_POST['Title']);
        if(empty($Data['Title'])){
            alertandback( '文章保存失败,标题不能为空.' ); 
        }
        $Keywords = $_POST['Keywords'];
        foreach ($Keywords as $key=>$value){
            $In .= $value.',';
        }
        $In = substr($In, 0, -1);
        $Data['Keywords']=$In;
        $Data['Description'] = trim($_POST['Description']);
        $Data['SeoTitle'] = str_replace(array('，',','),array('_','_'),trim($_POST['SeoTitle']));
        $Data['SeoKeywords'] = trim(str_replace(array('，'),array(','),trim($_POST['SeoKeywords'])),',');
        $Data['SeoDescription'] = trim($_POST['SeoDescription']);
        $Data['TripTitle'] = trim($_POST['TripTitle']);
        $Data['Days']=intval($_POST['TripInformation']['TripDays']);
        $Data['Months']=date('m',strtotime($_POST['TripInformation']['TripTime']));
        $Data['TripPlan'] = addslashes(json_encode($_POST['TripPlans']));
        $Data['Redactor'] = trim($_POST['Redactor']);
        $Data['TripInformation'] = addslashes(json_encode($_POST['TripInformation']));
        //文本图片处理-----------------------------------------------------------------------------
        foreach($_POST['Content'] as &$val){
            $Pattern=array();
            $Replacement=array();
            $ImgArr=Array();
            preg_match_all('/<img.*src="(.*)".*>/iU',stripcslashes($val['Content']),$ImgArr);
            if(count($ImgArr[0])){
                foreach($ImgArr[0] as $Key => $ImgTag){
                    $Pattern[]=$ImgTag;
                    $Replacement[]=preg_replace("/http:\/\/images\.57us\.com\/l/iU","",preg_replace(array('/title=".*"/iU','/alt=".*"/iU'),'',$ImgTag));
                }
                $val['Content']=str_replace($Pattern,$Replacement,stripcslashes($val['Content']));
            }        
        }
        $Data['Content'] = addslashes(json_encode($_POST['Content']));
        //文本图片处理-------------------------------------------------------------------------------
        $Data['Sort'] = intval(trim($_POST['Sort']));
        $Data['NewsIndexRecommend'] = intval($_POST['NewsIndexRecommend']);
        $Data['TopicRecommend'] = intval($_POST['TopicRecommend']);
        $Data['AdminID'] = $_SESSION['AdminID'];//添加AdminID
        //上传图片
        include SYSTEM_ROOTPATH.'/Include/MultiUpload.class.php';
        if ($_FILES['Image']['size'][0] > 0) {
            $Upload = new MultiUpload ( 'Image' );
            $File = $Upload->upload ();
            $Picture = $File[0] ? $File[0] : '';
            $Data ['Image'] = $Picture;
        }
        $now_time=time();
        $TblTravelsModule = new TblTravelsModule();
        if($TravelsID){
            if(isset($Data['Image'])){
                $TravelsInfo=$TblTravelsModule->GetInfoByKeyID($TravelsID);
                if($TravelsInfo['Image']){
                    DelFromImgServ($TravelsInfo['Image']);
                }
            }
            $Data['UpdateTime'] = date('Y-m-d H:i:s',$now_time);
            $result=$TblTravelsModule->UpdateInfoByKeyID($Data,$TravelsID);
            if($result){
                alertandgotopage('文章保存成功!',"/index.php?Module=TravelsNewsArticle&Action=Add&TravelsID=$TravelsID");
            }else{
                alertandback('文章保存失败,请重新编辑.');
            }                  
        }else{
            $Data['UpdateTime'] = date('Y-m-d H:i:s',$now_time);
            $Data['AddTime'] = date('Y-m-d H:i:s',$now_time);
            $Data['ViewCount'] = 0;
            $result=$TblTravelsModule->InsertInfo($Data);
            if($result){
                alertandgotopage( '文章保存成功!', "/index.php?Module=TravelsNewsArticle&Action=Add");
            }else{
                alertandback( '文章保存失败,请重新编辑.' );
            }
        }              
    }
    
    //删除文章
    public function Delete(){
        $ArticleIDs = $_REQUEST['TravelsID'];
        if(!empty($ArticleIDs)){
            $TblTravelsModule = new TblTravelsModule();
            if (is_array($ArticleIDs)) {         
                    foreach ($ArticleIDs as $ArticleID) {
                        $TravelsInfo=$TblTravelsModule->GetInfoByKeyID($ArticleID);
                        if($TblTravelsModule->DeleteByKeyID($ArticleID)){
                            if($TravelsInfo['Image']){
                                DelFromImgServ($TravelsInfo['Image']);
                            }
                        }
                    }
                    alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);
            } else {
                $TravelsInfo=$TblTravelsModule->GetInfoByKeyID($ArticleIDs);
                if($TblTravelsModule->DeleteByKeyID($ArticleIDs)){
                    if($TravelsInfo['Image']){
                        DelFromImgServ($TravelsInfo['Image']);
                    }
                    alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);                    
                }else{
                    alertandback('删除失败!');
                }
            }            
        }else{
            alertandback('您没有选择准备删除的记录!');
        }
    }
    //游记标签列表
    public function TagLists(){
        $TopNavs = 'TagLists';
        $Navs = 'TravelsNewsArticle';
        $KeywordModule = new TblTravelsKeywordModule();
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
    //游记标签编辑
    public function TagEdit(){
        $TopNavs = 'TagEdit';
        $Navs = 'TravelsNewsArticle';
        $KeywordModule = new TblTravelsKeywordModule();
        if ($_GET['ID']){
            $ID = $_GET['ID'];
            $TagInfo = $KeywordModule->GetInfoByKeyID($ID);
        }
        if ($_POST){
            $ID = intval($_POST['ID']);
            $Data['Keyword'] = trim($_POST['Keyword']);
            $Data['Traffic'] = intval($_POST['Traffic']);
            $Data['Hot'] = intval($_POST['Hot']);
            $Data['Sort'] = intval($_POST['Sort']);
            if (empty($ID)){
                $KeyID = $KeywordModule->InsertInfo($Data);
                if ($KeyID){
                    alertandgotopage('添加成功!','/index.php?Module=TravelsNewsArticle&Action=TagLists');
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
        include template("NewsTagEdit");
    }
    //删除游记标签
    public function DeleteTag(){
        $KeywordModule = new TblTravelsKeywordModule();
        $TblTravelsModule = new TblTravelsModule();
        if ($_GET['ID']) {
            $ID = $_GET['ID'];
            $TravelsInfo = $TblTravelsModule->GetInfoByWhere(' and  MATCH(`Keywords`) AGAINST ('.$ID.' IN BOOLEAN MODE)',true);
            if (!empty($TravelsInfo)){
                foreach ($TravelsInfo as $key=>$value){
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
                    $UpdateInfo = $TblTravelsModule->UpdateInfoByKeyID(array('Keywords'=>$In),$value['TravelsID']);
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