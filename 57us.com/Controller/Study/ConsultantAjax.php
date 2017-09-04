<?php
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class ConsultantAjax extends CommonController{

    public function __construct(){
    }
    
    public function Index(){
        $Intention = trim($_POST ['Intention']);
        unset($_POST ['Intention']);
        if ($Intention == '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            echo $json_result;
            exit;
        }
        $this->$Intention ();
    }

    /*
    *
    * 获取顾问列表 关联两张表 a-study_consultant_info  b-member_user_info
    */
    private function ConsultantLists(){
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $MysqlWhere='';
        //工作年限
        $WorkingAge=$_POST['Experience'];
        if($WorkingAge[0]!='All'){
            $MysqlWhere.='and (';
            foreach($WorkingAge as $AgeStr){
                switch($AgeStr){
                    case "0-3":
                        $MysqlWhere.="(a.WorkingAge>=0 and a.WorkingAge<3) or ";
                        break;
                    case "3-5":
                        $MysqlWhere.="(a.WorkingAge>=3 and a.WorkingAge<=5)  or ";
                        break;
                    case "5-10":
                        $MysqlWhere.="(a.WorkingAge>=5 and a.WorkingAge<=10)  or ";
                        break;     
                    case "10-All":
                        $MysqlWhere.="(a.WorkingAge>10)  or ";
                        break;
                }                 
            }
            $MysqlWhere=rtrim($MysqlWhere,' or ').') ';
        }
        //选择地区
        $City=$_POST['Region'];
        if($City[0]!='All'){
            $MysqlWhere.='and (';
            foreach($City as $CityName){
                         $MysqlWhere.="b.City='$CityName' or ";
            }
            $MysqlWhere=rtrim($MysqlWhere,' or ').')';            
        }
        $ServiceProject = $_POST['ServiceProject'];
        if($ServiceProject[0]!='All'){
            $MysqlWhere = "and MATCH(`ServiceProject`) AGAINST ('";
            foreach($ServiceProject as $val){
                $MysqlWhere.=$val.' ';
            }
            $MysqlWhere .= "' IN BOOLEAN MODE)";
        }
        //关键字
        $Keyword=trim($_POST['Keyword']);
        if($Keyword!=''){
            $MysqlWhere.=" and b.NickName like '%$Keyword%'";
        }
        //排序
        $SortType=trim($_POST['Sort']);
        switch($SortType){
            case 'ExperienceDown':
                $MysqlWhere.=" order by a.WorkingAge desc";
                break;
            case 'ExperienceAsce':
                $MysqlWhere.=" order by a.WorkingAge asc";
        }
        //分页查询开始-------------------------------------------------
        $Rscount = $StudyConsultantInfoModule->SelectConsultantMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
        $Page=intval($_POST['Page'])?intval($_POST['Page']):0;
        if ($Page < 1) {
            $Page = 1;
        }
        $Data = false;
        if ($Rscount['Num']) {
            $PageSize=15;
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            $Data['Data'] = $StudyConsultantInfoModule->SelectConsultantMemberInfo($MysqlWhere." limit $Offset,{$Data['PageSize']}");
            if(($Page+1)>$Data['PageCount']){
                $NextPage=$Data['PageCount'];
            }else{
                $NextPage=$Page+1;
            }
            if(($Page-1)<1){
                $BackPage=1;
            }else{
                $BackPage=$Page-1;
            }
            MultiPage($Data,5);
            $ConsultantList=array();
            foreach($Data['Data'] as $Key => $Val){
                $ConsultantList[$Key]['Study_name']=$Val['NickName'];
                $ConsultantList[$Key]['StudyID']=$Val['UserID'];
                $ConsultantList[$Key]['StudyExperience']=$Val['WorkingAge'];
                $ConsultantList[$Key]['StudyServiceRegion']=$Val['City'];
                $ConsultantList[$Key]['StudySex']=$Val['Sex'];
                if($Val['Avatar']){
                    $ConsultantList[$Key]['StudyImg']=(strpos($Val['Avatar'],"http://")===false)?LImageURL.$Val['Avatar']:$Val['Avatar'];
                }elseif($Val['Avatar']==''){
                    $ConsultantList[$Key]['StudyImg']= ImageURL.'/img/common/default.png';
                }
                $ConsultantList[$Key]['StudyUrl']=WEB_STUDY_URL.'/consultant/'.$Val['UserID'].'.html';
                $TagStr="";
                $TagArr=json_decode($Val['Tags'],true);
                if(!empty($TagArr)){
                    foreach($TagArr as $Tag){
                        $TagStr.="<span>$Tag</span>";
                    }
                }
                $ConsultantList[$Key]['StudyTag']=$TagStr;
                $ConsultantList[$Key]['StudyDepict']=  _substr($Val['Introduction'],60);
            }
            if($Keyword!=''){
                $ResultCode=102;
            }else{
                $ResultCode=200;
            }
            $json_result=array(
                'ResultCode'=>$ResultCode,
                'RecordCount'=>$Data['RecordCount'],
                'PageSize'=>$Data['PageSize'],
                'PageCount'=>$Data['PageCount'],
                'Page'=>$Data['Page'],
                'NextPage'=>$NextPage,
                'BackPage'=>$BackPage,
                'LastPage'=>$Data['PageCount'],
                'FirstPage'=>1,
                'PageNums'=>$Data['PageNums'],
                'Data'=>$ConsultantList
            );
        }else{
            if($Keyword!=''){
                $ResultCode=103;
            }else{
                $ResultCode=101;
            }
            $json_result=array('ResultCode'=>$ResultCode,'Message'=>'没有找到记录');
            $Data['Data']=$StudyConsultantInfoModule->SelectConsultantMemberInfo(' limit 0,15');
            if(!empty($Data['Data'])){
                $ConsultantList=array();
                foreach($Data['Data'] as $Key => $Val){
                    $ConsultantList[$Key]['Study_name']=$Val['NickName'];
                    $ConsultantList[$Key]['StudyID']=$Val['UserID'];
                    $ConsultantList[$Key]['StudyExperience']=$Val['WorkingAge'];
                    $ConsultantList[$Key]['StudyServiceRegion']=$Val['City'];
                    $ConsultantList[$Key]['StudySex']=$Val['Sex'];
                    $ImagesJson = json_decode($Val['ImagesJson'],true);
                    $ConsultantList[$Key]['StudyImg']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($Val['ImagesJson'],true)[$Val['CoverImageKey']]):(ImageURL.'/img/study/defaultService3.0.jpg');
                    $ConsultantList[$Key]['StudyUrl']=WEB_STUDY_URL.'/consultant/'.$Val['UserID'].'.html';
                    $TagStr="";
                    $TagArr=json_decode($Val['Tags'],true);
                    if(!empty($TagArr)){
                        foreach($TagArr as $Tag){
                            $TagStr.="<span>$Tag</span>";
                        }
                    }
                    $ConsultantList[$Key]['StudyTag']=$TagStr;
                    $ConsultantList[$Key]['StudyDepict']=$Val['Introduction'];
                }
                $json_result['Data']=$ConsultantList;
            }else{
                $json_result['Data']=array();
            }
        }
        echo json_encode($json_result);
    }

    /*
    * 获取服务列表
    *
    */
    private function ConsultantServiceLists(){
        $StudyConsultantServiceModule=new StudyConsultantServiceModule();
        $MysqlWhere="and a.`Status`=3";
        //申请层次
        $TargetLevel=$_POST['Level'];
        if($TargetLevel[0]!='All'){
            $TargetLevel=implode(',',$TargetLevel);
            $MysqlWhere.=" and (a.TargetLevel in ($TargetLevel) or a.TargetLevel=0)";
        }
        //服务类型
        $ServiceType=$_POST['ServiceType'];
        if($ServiceType[0]!='All'){
            $ServiceType=implode(',',$ServiceType);
            $MysqlWhere.=" and a.ServiceType in ($ServiceType)";
        }
        //地区
        $ServiceArea=$_POST['Region'];
        if($ServiceArea[0]!='All'){
            $MysqlWhere.=' and (';
            foreach($ServiceArea as $CityName){
                         $MysqlWhere.="b.City='$CityName' or ";
            }
            $MysqlWhere=rtrim($MysqlWhere,' or ').')';            
        }

        //关键字
        $Keyword=trim($_POST['Keyword']);
        if($Keyword!=''){
            $MysqlWhere.=" and a.ServiceName like '%$Keyword%'";
        }
        //排序
        $SortType=trim($_POST['Sort']);
        switch ($SortType){
            case 'PicerDown':
                $MysqlWhere.=" order by a.Recommend desc,a.SalePrice desc";
                break;
            case 'PicerAsce':
                $MysqlWhere.=" order by a.Recommend desc,a.SalePrice asc";
                break;
            case 'SalesDown':
                $MysqlWhere.=" order by a.Recommend desc,a.SaleNum desc";
                break;
            case 'SalesAsce':
                $MysqlWhere.=" order by a.Recommend desc,a.SaleNum asc";
                break;
            default:
                $MysqlWhere.=" order by a.Recommend desc";
        }
        //分页查询开始-------------------------------------------------
        $Rscount = $StudyConsultantServiceModule->SelectServiceMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
        $Page=intval($_POST['Page'])?intval($_POST['Page']):0;
        if ($Page < 1) {
            $Page = 1;
        }
        $Data = false;
        if ($Rscount['Num']) {
            $PageSize=6;
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            $Data['Data'] = $StudyConsultantServiceModule->SelectServiceMemberInfo($MysqlWhere." limit $Offset,{$Data['PageSize']}");
            if(($Page+1)>$Data['PageCount']){
                $NextPage=$Data['PageCount'];
            }else{
                $NextPage=$Page+1;
            }
            if(($Page-1)<1){
                $BackPage=1;
            }else{
                $BackPage=$Page-1;
            }
            MultiPage($Data,5);
            $ServiceList=array();
            foreach($Data['Data'] as $Key => $Val){
                $ServiceList[$Key]['Study_name']=$Val['ServiceName'];
                $ServiceList[$Key]['StudyID']=$Val['ServiceID'];
                $ServiceList[$Key]['StudyLevel']=$Val['TargetLevel']?$StudyConsultantServiceModule->TargetLevel[$Val['TargetLevel']]:'';
                $ServiceList[$Key]['StudyServiceType']=$StudyConsultantServiceModule->ServiceType[$Val['ServiceType']];
                $ServiceList[$Key]['StudyServiceRegion']=$Val['City'];
                $ServiceList[$Key]['StudyPicre']=$Val['SalePrice'];
                $ImagesJson = json_decode($Val['ImagesJson'],true);
                $ServiceList[$Key]['StudyImg']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($Val['ImagesJson'],true)[$Val['CoverImageKey']]):(ImageURL.'/img/study/defaultService3.0.jpg');
                $ServiceList[$Key]['StudyUrl']=WEB_STUDY_URL.'/consultant_service/'.$Val['ServiceID'].'.html';
                $TagStr="";
                $TagArr=json_decode($Val['ServiceTags'],true);
                if(!empty($TagArr)){
                    foreach($TagArr as $Tag){
                        $TagStr.="<span>{$Tag['ServiceTag']}</span>";
                    }
                }
                $ServiceList[$Key]['StudyService']=$TagStr;
                $ServiceList[$Key]['StudyDepict']=$Val['ServiceDescription'];
            }
            //exit;
            if($Keyword!=''){
                $ResultCode=102;
            }else{
                $ResultCode=200;
            }
            $json_result=array(
                'ResultCode'=>$ResultCode,
                'RecordCount'=>$Data['RecordCount'],
                'PageSize'=>$Data['PageSize'],
                'PageCount'=>$Data['PageCount'],
                'Page'=>$Data['Page'],
                'NextPage'=>$NextPage,
                'BackPage'=>$BackPage,
                'LastPage'=>$Data['PageCount'],
                'FirstPage'=>1,
                'PageNums'=>$Data['PageNums'],
                'Data'=>$ServiceList
            );        
        }else{
            if($Keyword!=''){
                $ResultCode=103;
            }else{
                $ResultCode=101;
            }
            $json_result=array('ResultCode'=>$ResultCode,'Message'=>'没有找到记录');
            $Data['Data']=$StudyConsultantServiceModule->GetInfoByWhere('and `Status`=3 limit 0,6',true);
            if(!empty($Data['Data'])){
                $ServiceList=array();
                foreach($Data['Data'] as $Key => $Val){
                $ServiceList[$Key]['Study_name']=$Val['ServiceName'];
                $ServiceList[$Key]['StudyID']=$Val['ServiceID'];
                $ServiceList[$Key]['StudyLevel']=$StudyConsultantServiceModule->TargetLevel[$Val['TargetLevel']];
                $ServiceList[$Key]['StudyServiceType']=$StudyConsultantServiceModule->ServiceType[$Val['ServiceType']];
                $ServiceList[$Key]['StudyServiceRegion']=$Val['ServiceArea'];
                $ServiceList[$Key]['StudyPicre']=$Val['SalePrice'];
                $ImagesJson = json_decode($Val['ImagesJson'],true);
                $ServiceList[$Key]['StudyImg']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($Val['ImagesJson'],true)[$Val['CoverImageKey']]):(ImageURL.'/img/study/defaultService3.0.jpg');
                $ServiceList[$Key]['StudyUrl']=WEB_STUDY_URL.'/consultant_service/'.$Val['ServiceID'].'.html';
                $TagStr="";
                $TagArr=json_decode($Val['ServiceTags'],true);
                if(!empty($TagArr)){
                    foreach($TagArr as $Tag){
                        $TagStr.="<span>{$Tag['ServiceTag']}</span>";
                    }
                }
                $ServiceList[$Key]['StudyService']=$TagStr;
                $ServiceList[$Key]['StudyDepict']=$Val['ServiceDescription'];
                }
                $json_result['Data']=$ServiceList;
            }else{
                $json_result['Data']=array();
            }
        }
        echo json_encode($json_result);        
    }
}
