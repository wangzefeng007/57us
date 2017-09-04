<?php
class Consultant {

    public function __construct(){
    }

    /**
     * @desc  顾问列表
     */
    public function Lists(){
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $MysqlWhere = '';
        //关键字
        $SearchKeyWords=trim($_GET['K']);
        if($SearchKeyWords!=''){
            $MysqlWhere.=" and b.NickName like '%$SearchKeyWords%'";
        }
        $Rscount = $StudyConsultantInfoModule->SelectConsultantMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
        $Page=intval($_GET['p'])?intval($_GET['p']):0;
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
            $MysqlWhere .=' order by WorkingAge desc';
            $Data['Data'] = $StudyConsultantInfoModule->SelectConsultantMemberInfo($MysqlWhere." limit $Offset,{$Data['PageSize']}");
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
                $ConsultantList[$Key]['StudyDepict']=  mb_substr($Val['Introduction'],0,60,'utf-8');
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }else{
            $Data['Data']=$StudyConsultantInfoModule->SelectConsultantMemberInfo(' limit 0,15');
            if(!empty($Data['Data'])){
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
                    $ConsultantList[$Key]['StudyDepict']=$Val['Introduction'];
                }
            }
        }
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_consultant_right');
        $SearchKeyWords=$_GET['K'];
        $TagNav='consultant';
        $Title='美国留学顾问_美国留学中介_留学咨询顾问_美国留学咨询 - 57美国网';
        $Keywords='留学顾问,美国留学顾问,美国留学中介,留学咨询顾问,美国留学咨询,美国留学机构,出国留学咨询,留学咨询顾问';
        $Description='57美国网找顾问频道，聚集资深美国留学顾问，丰富的经验，全方位解析美国留学信息，为各年龄学生提供留学服务，留学全程申请、签证办理、定校方案、文书服务、行前指导、境外服务等在线咨询及预订服务。';    
        include template('ConsultantLists');
    }

    /**
     * @desc  顾问详情
     */
    public function Detail(){
        $UserID=intval($_GET['ID']);
        $CID=intval($_GET['CID']);
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($UserID);
        $StudyConsultantInfoModule=new StudyConsultantInfoModule();
        $ConsultantInfo=$StudyConsultantInfoModule->GetInfoByWhere("and UserID={$UserID}");
        $ServiceProject = $StudyConsultantInfoModule->ServiceProject;
        $ServiceProjectInfo = explode(',',$ConsultantInfo['ServiceProject']);
        foreach($ServiceProjectInfo as $key => $val){
            $ServiceProjectInfo[$key] = $ServiceProject[$val];
        }
        $StudyConsultantServiceModule=new StudyConsultantServiceModule();
        $ServiceList=$StudyConsultantServiceModule->GetInfoByWhere("and UserID=$UserID and `Status`=3",true);
        $StudyConsultantCaseModule=new StudyConsultantCaseModule();
        $CaseList=$StudyConsultantCaseModule->GetInfoByWhere("and UserID=$UserID and `Status`=2",true);
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_consultant_right');
        $TagNav='consultant';
        $Title="{$UserInfo['NickName']}美国留学顾问 - 57美国网";
        $Keywords="{$UserInfo['NickName']}美国留学顾问";
        $Description="57美国网留学顾问—{$UserInfo['NickName']}，".mb_substr($ConsultantInfo['Introduction'], 0,100,'utf-8').'…';     
        include template('ConsultantDetail');
    }

    /**
     * @desc  服务列表
     */
    public function ServiceLists(){
        $StudyConsultantServiceModule=new StudyConsultantServiceModule();
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_service_right');
        //初始化获取列表数据
        $MysqlWhere='and a.`Status`=3';
                //关键字
        $SearchKeyWords=trim($_GET['K']);
        if($SearchKeyWords!=''){
            $MysqlWhere.=" and a.ServiceName like '%$SearchKeyWords%'";
        }
        $Rscount = $StudyConsultantServiceModule->SelectServiceMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
        $Page=intval($_GET['p'])?intval($_GET['p']):0;
        if ($Page < 1) {
            $Page = 1;
        }
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
            $MysqlWhere .=' order by Recommend desc';
            $Data['Data'] = $StudyConsultantServiceModule->SelectServiceMemberInfo($MysqlWhere." limit $Offset,{$Data['PageSize']}");
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
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }else{
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
            }
        }
        //获取最新资讯
        $TblStudyAbroadCategoryModule=new TblStudyAbroadCategoryModule();
        $CategoryList=$TblStudyAbroadCategoryModule->GetInfoByWhere("and MATCH (CategoryIDS) AGAINST ('1093' IN BOOLEAN MODE)",true);
        $CatgegoryStr='';
        foreach($CategoryList as $arr){
            $CatgegoryStr.=$arr['CategoryID'].',';
        }
        $CategoryStr=rtrim($CatgegoryStr,',');
        $TblStudyAbroadModule=new TblStudyAbroadModule();
        $NewsList=$TblStudyAbroadModule->GetLists("and CategoryID in ($CategoryStr) order by AddTime desc",0, 5);
        $TagNav='service';
        $Title='留学服务_留学服务中介_留学服务项目_留学服务指南- 57美国网';
        $Keywords='留学服务,留学服务中介,留学服务项目,留学服务指南';
        $Description='57美国网留学服务频道，聚集由资深美国留学顾问提供的全套留学服务项目，包括：留学全程申请、签证办理、定校方案、文书服务、行前指导、境外服务等在线咨询及预订服务。';        
        include template('ConsultantServiceLists');
    }

    /**
     * @desc  服务详情
     */
    public function ServiceDetail(){
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_service_right');        
        $ServiceID=intval($_GET['ID']);
        //服务信息
        $StudyConsultantServiceModule = new StudyConsultantServiceModule();
        $ServiceInfo=$StudyConsultantServiceModule->GetInfoByWhere("and ServiceID=$ServiceID");
        if(!$ServiceInfo || ($ServiceInfo['Status']!=3 && $ServiceInfo['UserID']!=$_SESSION['UserID'])){
            alertandgotopage("不存在该服务",WEB_STUDY_URL.'/consultant_service/');
        }
        //添加浏览记录
        $Type=1;
        MemberService::AddBrowsingHistory($ServiceID,$Type);
        //服务类型
        $ServiceType=$StudyConsultantServiceModule->ServiceType[$ServiceInfo['ServiceType']];
        //用户基本信息
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($ServiceInfo['UserID']);
        //顾问信息
        $StudyConsultantInfoModule=new StudyConsultantInfoModule();
        $ConsultantInfo=$StudyConsultantInfoModule->GetInfoByWhere("and UserID={$ServiceInfo['UserID']}");
        //申请层次
        $TargetLevel=$StudyConsultantServiceModule->TargetLevel;
        //其他服务
        $ServiceList=$StudyConsultantServiceModule->GetInfoByWhere("and UserID={$ServiceInfo['UserID']} and ServiceID<>$ServiceID and `Status`=3",true);
        //推荐的服务
        $RecommendServiceList=$StudyConsultantServiceModule->GetInfoByWhere("and ServiceID<>$ServiceID and `Status`=3 and ServiceType={$ServiceInfo['ServiceType']} limit 0,3",true);
        $TagNav='service';
        $Title="{$ServiceInfo['ServiceName']}_{$UserInfo['NickName']}美国留学顾问- 57美国网";
        $Keywords="{$ServiceInfo['ServiceName']},{$UserInfo['NickName']}美国留学顾问";
        $Description="57美国网留学顾问—{$UserInfo['NickName']}提供：{$ServiceInfo['ServiceName']}服务，".mb_substr($ServiceInfo['ServiceDescription'], 0,100,'utf-8').'…';           
        include template('ConsultantServiceDetail');
    }

    /*
     * @desc  获取顾问地区
     */
    public function GetCity(){
        $MemberUserInfoModule = new MemberUserInfoModule();
        $Info = $MemberUserInfoModule->RemoveDuplicate('City');
        echo json_encode($Info);
    }

    /*
     * @desc  获取服务项目
     */
    public function GetProject(){
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $ServiceProject = $StudyConsultantInfoModule->ServiceProject;
        $Result = array();
        foreach($ServiceProject as $key=>$val){
            $Result[$key]['id']= $key;
            $Result[$key]['name']= $val;
        }
        echo json_encode($Result);
    }
}
