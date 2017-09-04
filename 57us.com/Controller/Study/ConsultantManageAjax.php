<?php
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class ConsultantManageAjax extends CommonController{

    public function __construct(){
        $this->ConsultantLoginStatus();
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

    /**
     * @desc 顾问服务列表
     */
    public function ConsultantServiceList(){
        $StudyConsultantServiceModule = new StudyConsultantServiceModule();
        $MysqlWhere="and UserID={$_SESSION['UserID']}";
        $Status=isset($_POST['Status'])?intval($_POST['Status']):3;
        $PageUrl="";
        if($Status==0){
            $MysqlWhere.=" and (`Status`=0 or `Status`=4 or `Status`=2)";
        }else{
            $MysqlWhere.=" and `Status`=$Status";
        }
        
        $KeyWords=trim($_POST['Keyword']);
        if($KeyWords){
            $MysqlWhere.=" and ServiceName like '%$KeyWords%'";
        }
        $Rscount = $StudyConsultantServiceModule->GetListsNum($MysqlWhere);
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
            $Data['Data'] = $StudyConsultantServiceModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
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
                $ServiceList[$Key]['ServiceList_Id']=$Val['ServiceID'];
                $ImageArr=json_decode($Val['ImagesJson'],true);
                if(!empty($ImageArr)){
                    if(strpos($ImageArr[$Val['CoverImageKey']],'http://')!==false){
                        $ServiceList[$Key]['ServiceList_Img']=$ImageArr[$Val['CoverImageKey']];
                    }else{
                        $ServiceList[$Key]['ServiceList_Img']=LImageURL.$ImageArr[$Val['CoverImageKey']];
                    }
                }else{
                    $ServiceList[$Key]['ServiceList_Img']=ImageURLP2.'/Uploads/Study/Service/service.jpg';
                }
                $ServiceList[$Key]['ServiceList_Name']=$Val['ServiceName'];
                $ServiceList[$Key]['ServiceList_Depict']= _substr($Val['ServiceDescription'],95);
                $ServiceList[$Key]['ServiceList_Picre']=$Val['SalePrice'];
                $ServiceList[$Key]['ServiceList_Url']=WEB_STUDY_URL.'/consultant_service/'.$Val['ServiceID'].'.html';
            }
            if($KeyWords!=''){
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
            if($KeyWords!=''){
               $json_result=array('ResultCode'=>103,'Message'=>'没有找到记录');
            }else{
               $json_result=array('ResultCode'=>101,'Message'=>'没有找到记录'); 
            }
        }
        echo json_encode($json_result);
    }
    
    /**
    * @desc 顾问服务上下架 
     */
    public function ConsultantServiceListOperation(){
        $StudyConsultantServiceModule = new StudyConsultantServiceModule();
        $ServiceID=intval($_POST['id']);
        $ServiceInfo=$StudyConsultantServiceModule->GetInfoByKeyID($ServiceID);
        if($ServiceInfo){
            if($ServiceInfo['Status']==3){
                $Data['Status']=4;
            }elseif($ServiceInfo['Status']==4){
                $Data['Status']=3;
            }
            $result=$StudyConsultantServiceModule->UpdateInfoByWhere($Data,"UserID={$_SESSION['UserID']} and ServiceID=$ServiceID");
            if($result!==false){
                $json_result=array('ResultCode'=>200,'Message'=>'操作成功');
            }else{
                $json_result=array('ResultCode'=>101,'Message'=>'操作失败');
            }
        }else{
            $json_result=array('ResultCode'=>100,'Message'=>'该服务不存在');
        }
        echo json_encode($json_result);
    }
    
    /**
    * @desc 顾问服务删除 
     */
    public function ConsultantServiceListDelete(){
        $StudyConsultantServiceModule = new StudyConsultantServiceModule();
        $ServiceID=intval($_POST['id']);
        $ServiceInfo=$StudyConsultantServiceModule->GetInfoByKeyID($ServiceID);
        if($ServiceInfo){
            $Data['Status']=5;
            $result=$StudyConsultantServiceModule->UpdateInfoByWhere($Data,"UserID={$_SESSION['UserID']} and ServiceID=$ServiceID");
            if($result!==false){
                $json_result=array('ResultCode'=>200,'Message'=>'操作成功');
            }else{
                $json_result=array('ResultCode'=>101,'Message'=>'操作失败');
            }
        }else{
            $json_result=array('ResultCode'=>100,'Message'=>'该服务不存在');
        }
        echo json_encode($json_result);        
    }
    
    /**
    * @desc 顾问服务提交审核
     */
    public function ConsultantServiceSubmitAudit(){
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        if($UserInfo['IdentityState']!=2){
            $json_result=array('ResultCode'=>100,'Message'=>'您的身份还未审核通过');
        }else{
            $StudyConsultantServiceModule = new StudyConsultantServiceModule();
            $ServiceID=intval($_POST['id']);
            $ServiceInfo=$StudyConsultantServiceModule->GetInfoByKeyID($ServiceID);
            if($ServiceInfo){
                $Data['Status']=1;
                $result=$StudyConsultantServiceModule->UpdateInfoByWhere($Data,"UserID={$_SESSION['UserID']} and ServiceID=$ServiceID");
                if($result!==false){
                    $json_result=array('ResultCode'=>200,'Message'=>'操作成功');
                }else{
                    $json_result=array('ResultCode'=>101,'Message'=>'操作失败');
                }
            }else{
                $json_result=array('ResultCode'=>100,'Message'=>'该服务不存在');
            }
        }
        echo json_encode($json_result);        
    }
    /**
    * @desc 顾问服务添加
     */
    public function AddService(){
        $ServiceID=intval($_POST['ID']);
        $SubmitType=trim($_POST['SubmitType']);
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);        
        if($SubmitType=='SubmitAudit' && $UserInfo['IdentityState']==2){
            $Data['Status']=1;
        }else{
            $Data['Status']=0;
        }
        $Data['ServiceName']=trim($_POST['ServiceName']);
        $Data['SalePrice']=trim($_POST['ServicePrice']);
        $Data['TargetCountry']='美国';
        $ServiceParameter=$_POST['ServiceParameter'];
        switch (trim($_POST['ServiceType'])){
            //全程服务
            case '1':
                $Data['ServiceType']=1;
                $Data['TargetPeople']=trim($ServiceParameter['WholeServiceCrowd']);
                $Data['ServiceCycle']=trim($ServiceParameter['WholeServicePeriod']);
                $Data['DocumentsModifyNum']=trim($ServiceParameter['WholeServiceWriterEditTime']);
                $Data['DocumentsNum']=trim($ServiceParameter['WholeServiceWriterNum']);
                $Data['SchoolNum']=trim($ServiceParameter['WholeServiceApplySchoolNum']);
                $Data['OtherFees']=trim($ServiceParameter['WholeServiceTPOSPrice']);
                $Data['VisaDirectNum']=trim($ServiceParameter['WholeServiceVisaDirectNum']);
                break;
            //申请学校
            case '2':
                $Data['ServiceType']=2;
                $Data['TargetPeople']=trim($ServiceParameter['ApplySchoolCrowd']);
                $Data['ServiceCycle']=trim($ServiceParameter['ApplySchoolPeriod']);
                $Data['DocumentsModifyNum']=trim($ServiceParameter['ApplySchoolWriterEditTime']);
                $Data['DocumentsNum']=trim($ServiceParameter['ApplySchoolWriterNum']);
                $Data['SchoolNum']=trim($ServiceParameter['ApplySchoolApplySchoolNum']);
                $Data['OtherFees']=trim($ServiceParameter['ApplySchoolTPOSPrice']);
                $Data['VisaDirectNum']=trim($ServiceParameter['ApplySchoolVisaDirectNum']);
                break;
            //定校方案修改
            case '4':
                $Data['ServiceType']=4;
                $Data['TargetPeople']=trim($ServiceParameter['DocumentManageCrowd']);
                $Data['ServiceCycle']=trim($ServiceParameter['ChooseSchoolsModifyPeriod']);
                break;
            //文书服务
            case '3':
                $Data['ServiceType']=3;
                $Data['TargetPeople']=trim($ServiceParameter['DocumentManageCrowd']);
                $Data['ServiceCycle']=trim($ServiceParameter['DocumentManagePeriod']);
                $Data['DocumentsModifyNum']=trim($ServiceParameter['DocumentManageWriterEditTime']);
                $Data['DocumentsNum']=trim($ServiceParameter['DocumentManageWriterNum']);                
                break;
            //材料翻译
            case '6':
                $Data['ServiceType']=6;
                $Data['TargetPeople']=trim($ServiceParameter['DataTranslationCrowd']);
                $Data['ServiceCycle']=trim($ServiceParameter['DataTranslationPeriodPeriod']);
                $Data['DocumentsModifyNum']=trim($ServiceParameter['DataTranslationEditTime']);
                break;
            //背景提升
            case '7':
                $Data['ServiceType']=7;  
                $Data['TargetPeople']=trim($ServiceParameter['BackgroundPromotionCrowd']);
                break;
            //签证指导
            case '5':
                $Data['ServiceType']=5;
                $Data['TargetPeople']=trim($ServiceParameter['VisaDirectCrowd']);
                $Data['VisaDirectNum']=trim($ServiceParameter['VisaDirectVisaDirectNum']); 
                break;
        }
        switch (trim($_POST['ApplyLevel'])){
            case '高中':
                $Data['TargetLevel']=1;
                break;
            case '本科':
                $Data['TargetLevel']=2;
                break;
            case '研究生':
                $Data['TargetLevel']=3;
                break;
            case '转学':
                $Data['TargetLevel']=4;
                break;         
        }
        $Data['ServiceTags']=json_encode($_POST['ServiceTag'],JSON_UNESCAPED_UNICODE);
        $Data['ServiceDescription']=trim($_POST['ServiceDescription']);
        $ImageArr=$_POST['ServiceImg'];
        if(!empty($ImageArr)){
            foreach($ImageArr as $key=>$val){
                if(strpos($val['Img'],'data:image/jpeg;base64')!==false){
                    $ImageFullUrl='/up/'.date('Y').'/'.date('md').'/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                    SendToImgServ($ImageFullUrl,str_replace('data:image/jpeg;base64,','',$val['Img']));
                    $ImageArr[$key]=$ImageFullUrl;
                }else{
                    //系统封面模板
                    $ImageArr[$key]=str_replace(array(LImageURL,ImageURLP2,ImageURLP4,ImageURLP6,ImageURLP8),"",$val['Img']);
                    //$ImageArr[$key]=$val['Img'];
                }
            }
            $Data['ImagesJson']=json_encode($ImageArr);
            $Data['CoverImageKey']=$_POST['ServiceDefaultImg'];
        }
        //文本图片处理-----------------------------------------------------------------------------
        $Data['ServiceDetails'] = $_POST['ServiceDetails'];
        $Pattern=array();
        $Replacement=array();
        $ImgArr=Array();
        preg_match_all('/<img.*src="(.*)".*>/iU',stripcslashes($Data['ServiceDetails']),$ImgArr);
        if(count($ImgArr[0])){
            foreach($ImgArr[0] as $Key => $ImgTag){
                $Pattern[]=$ImgTag;
                $Replacement[]=preg_replace("/http:\/\/images\.57us\.com\/l/iU","",preg_replace(array('/title=".*"/iU','/alt=".*"/iU'),'',$ImgTag));
            }
        }        
        $Data['ServiceDetails'] = addslashes(str_replace($Pattern,$Replacement,stripcslashes($Data['ServiceDetails'])));
        //文本图片处理-------------------------------------------------------------------------------                    
        $Data['AddTime']=time();
        
        $StudyConsultantServiceModule = new StudyConsultantServiceModule();
        if(!$ServiceID){
            $Data['UserID']=$_SESSION['UserID'];    
            $Data['UpdateTime']=$Data['AddTime'];
            $Result=$StudyConsultantServiceModule->InsertInfo($Data);
        }else{
            $Data['UpdateTime']=time();
            $Result=$StudyConsultantServiceModule->UpdateInfoByKeyID($Data,$ServiceID);
        }
        if($Result){
            if($Data['Status']==1){
                if(!$ServiceID){
                    $Url=WEB_STUDY_URL.'/consultantmanage/underreview/?ID='.$Result;
                }else{
                    $Url=WEB_STUDY_URL.'/consultantmanage/underreview/?ID='.$ServiceID;
                }
            }else{
                if(!$ServiceID){
                    $Url=WEB_STUDY_URL.'/consultantmanage/savesuccess/?ID='.$Result;      
                }else{
                    $Url=WEB_STUDY_URL.'/consultantmanage/savesuccess/?ID='.$ServiceID;       
                }
            }
            if($SubmitType=='SubmitAudit' && $UserInfo['IdentityState']==2){
                $json_result=array('ResultCode'=>200,'Message'=>'保存成功','Url'=>$Url);
            }elseif($SubmitType=='SubmitAudit' && $UserInfo['IdentityState']!=2){
                $json_result=array('ResultCode'=>200,'Message'=>'请先审核身份，暂时为你保存到草稿箱。','Url'=>$Url);
            }else{
                $json_result=array('ResultCode'=>200,'Message'=>'保存成功','Url'=>$Url);
            }
        }else{
            $json_result=array('ResultCode'=>101,'Message'=>'保存失败');
        }
        echo json_encode($json_result);        
    }    

    /**
     * @desc  顾问个人信息头像设置
     */
    public function ConsultantMyInfoUpImg(){
        $UserInfoModule = new MemberUserInfoModule();
        if ($_POST) {
            $Image = trim($_POST['Img']);
            if (!empty($Image)) {
                if (strpos($Image, 'data:image/jpeg;base64') !== false) {
                    $ImageFullUrl = '/up/' . date('Y') . '/' . date('md') . '/' . date('YmdHis') . mt_rand(1000, 9999) . '.jpg';
                    SendToImgServ($ImageFullUrl, str_replace('data:image/jpeg;base64,', '', $Image));
                    $Data['Avatar'] = $ImageFullUrl;
                    $Update = $UserInfoModule->UpdateInfoByWhere($Data,' UserID = '.$_SESSION['UserID']);
                    if ($Update){
                        $json_result=array('ResultCode'=>200,'Message'=>'上传成功');
                    }else{
                        $json_result=array('ResultCode'=>201,'Message'=>'上传失败');
                    }
                    echo json_encode($json_result);exit;
                }
            }
        }
    }
    /**
     * @desc  顾问个人信息设置
     */
    public function ConsultantMyInfoIndex1(){
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $UserInfoModule = new MemberUserInfoModule();
        if(!$UserInfoModule->GetInfoByUserID($_SESSION['UserID'])){
            $UserInfoModule->InsertInfo(array('UserID'=>$_SESSION['UserID']));
        }
        if ($_POST){
            $Data['RealName'] = trim($_POST['Name']);
            $Data['Nickname'] = trim($_POST['Nickname']);
            $City = $_POST['City'];
            $Data['Country'] = trim($City['Country']);
            $Data['Province'] = trim($City['Province']);
            $Data['City'] = trim($City['City']);
            $Data['Sex'] = trim($_POST['Sex']);
            $Data['IdentityState'] = 1;
            $Date['ServiceDeclaration'] = trim($_POST['ServiceManifesto']);
            foreach ($_POST['MyTag'] as $key=>$value){
                if ($value['tag']!=''){
                    $tags[] = $value['tag'];
                }
            }
            $Date['Tags'] = json_encode($tags,JSON_UNESCAPED_UNICODE);
            $UpdateUserInfo = $UserInfoModule->UpdateInfoByWhere($Data,' UserID = '.$_SESSION['UserID']);
            if ($UpdateUserInfo === false){
                $json_result=array('ResultCode'=>101,'Message'=>'更新个人信息失败');
                echo json_encode($json_result);exit;
            }
            if($StudyConsultantInfoModule->GetInfoByWhere("and UserID={$_SESSION['UserID']}")){
                $UpdateConsultantInfo = $StudyConsultantInfoModule->UpdateInfoByWhere($Date,' UserID = '.$_SESSION['UserID']);
            }else{
                $Date['UserID']=$_SESSION['UserID'];
                $UpdateConsultantInfo = $StudyConsultantInfoModule->InsertInfo($Date);
            }
           
            if ($UpdateConsultantInfo === false){
                $json_result=array('ResultCode'=>102,'Message'=>'更新顾问基本信息失败');
                echo json_encode($json_result);exit;
            }
            if ($UpdateConsultantInfo>=0 || $UpdateUserInfo>=0){
                $json_result=array('ResultCode'=>200,'Message'=>'更新成功','Url'=>'/consultantmanage/myinfosettings/');

            }else{
                $json_result=array('ResultCode'=>200,'Message'=>'未修改');
            }
            echo json_encode($json_result);exit;

        }
    }
    /**
     * @desc  顾问背景资料设置
     */
    public function ConsultantMyInfoIndex2(){
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $MemberUserInfoModule = new MemberUserInfoModule();
        if ($_POST){
            $Data['WorkingAge'] = trim($_POST['Experience']);
            $Data['Introduction'] = trim($_POST['Introduction']);
            $PastExperience = $_POST['WorkExperience'];
            if ($Data['WorkingAge'] ==''){
                $json_result=array('ResultCode'=>101,'Message'=>'工作经验必填');
                echo json_encode($json_result);exit;
            }
            if ($Data['Introduction'] ==''){
                $json_result=array('ResultCode'=>102,'Message'=>'自我介绍必填');
                echo json_encode($json_result);exit;
            }
            foreach ($PastExperience as $key=>$value){
                if ($value['startdate']==''||$value['enddate']==''||$value['company']==''||$value['undergo']==''){
                    $json_result=array('ResultCode'=>103,'Message'=>'从业经历必填');
                    echo json_encode($json_result);exit;
                }
                $PastExperience[$key]['undergo']= str_replace(array("\r","\n"),"",nl2br($value['undergo']));
            }
            $ServiceProject = $_POST['ServiceProject'];
            $ServiceProjectInfo = '';
            foreach($ServiceProject as $val){
                $ServiceProjectInfo .= $val.',';
            }
            $ServiceProjectInfo = substr($ServiceProjectInfo,0,strlen($ServiceProjectInfo)-1);
            $Data['ServiceProject'] = $ServiceProjectInfo;
            $Data['PastExperience'] = json_encode($PastExperience,JSON_UNESCAPED_UNICODE);
            $Date['IdentityState'] = 1;
            $UpdateUserInfo =$MemberUserInfoModule->UpdateInfoByWhere($Date,' UserID = '.$_SESSION['UserID']);
            if ($UpdateUserInfo === false){
                $json_result=array('ResultCode'=>101,'Message'=>'更新审核状态失败');
                echo json_encode($json_result);exit;
            }
            $UpdateInfo = $StudyConsultantInfoModule ->UpdateInfoByWhere($Data,' UserID = '.$_SESSION['UserID']);
            if ($UpdateInfo >=0){
                $json_result=array('ResultCode'=>200,'Message'=>'更新成功','Url'=>'/consultantmanage/myinfosettings/');
            }else{
                $json_result=array('ResultCode'=>201,'Message'=>'更新失败');
            }
            echo json_encode($json_result);exit;
        }
    }
    /**
     * @desc  顾问身份验证设置
     */
    public function ConsultantMyInfoIndex3(){
        $MemberUserInfoModule = new MemberUserInfoModule();
        if ($_POST){
            $Data['CardType'] =1;
            $Data['CardNum'] = trim($_POST['IdCard']);
            $Image = trim($_POST['CardImg']);
            if (!empty($Image)) {
                if (strpos($Image, 'data:image/jpeg;base64') !== false) {
                    $ImageFullUrl = '/up/' . date('Y') . '/' . date('md') . '/' . date('YmdHis') . mt_rand(1000, 9999) . '.jpg';
                    SendToImgServ($ImageFullUrl, str_replace('data:image/jpeg;base64,', '', $Image));
                    $Data['CardPositive'] = $ImageFullUrl;
                }
            }
            $Data['IdentityState'] =1;
            $UpdateInfo = $MemberUserInfoModule ->UpdateInfoByWhere($Data,' UserID = '.$_SESSION['UserID']);
            if ($UpdateInfo >=0){
                $json_result=array('ResultCode'=>200,'Message'=>'更新成功');
            }else{
                $json_result=array('ResultCode'=>201,'Message'=>'更新失败');
            }
            echo json_encode($json_result);exit;
        }
    }

    /**
     * @desc  顾问审核通过，重新提交个人信息
     */
    public function ConsultantManageApproveIndex1(){
        $this->ConsultantMyInfoIndex1();
    }

    /**
     * @desc  顾问审核通过，重新提交背景资料
     */
    public function ConsultantManageApproveIndex2(){
        $this->ConsultantMyInfoIndex2();
    }

    /**
     * @desc  顾问成功案例列表
     */
    public function SuccessCaseList(){
        $Status = $_POST['CaseColumn']?$_POST['CaseColumn']:2;
        $CaseModule = new StudyConsultantCaseModule();
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'] .' and Status = '.$Status;
        $Data = $CaseModule->GetInfoByWhere($MysqlWhere,true);
        $Result = array();
        //echo "<pre>";print_r($Data);exit;
        foreach($Data as $key=>$val){
            $Result[$key]['CaseID'] = $val['CaseID'];
            $Result[$key]['StudentName'] = $val['StudentName'];
            $Result[$key]['StudentImage'] = $val['StudentImage']?ImageURLP2.$val['StudentImage']:'';
            $Result[$key]['ApplySeason'] = $val['ApplySeason']?$val['ApplySeason']:'';
            $Result[$key]['AdmissionSchool'] = $val['AdmissionSchool']?$val['AdmissionSchool']:'';
            $Result[$key]['ApplySchool'] = $val['ApplySchool']?$val['ApplySchool']:'';
        }
        if($Data){
            $json_result=array(
                'ResultCode'=>200,
                'CaseListData'=>$Result
            );
        }else{
            $json_result=array(
                'ResultCode'=>100,
                'Message'=>'没有数据'
            );
        }
        echo json_encode($json_result);
    }

    /**
     * @desc   成功案例查看详情
     */
    public function CaseDetails(){
        $CaseModule = new StudyConsultantCaseModule();
        $CaseID = $_POST['CaseID'];
        $CaseInfo = $CaseModule->GetInfoByKeyID($CaseID);
        $OfferImage = json_decode($CaseInfo['OfferImage']);
        foreach($OfferImage as $key=>$val){
            $OfferImage[$key] = ImageURLP4.$val;
        }
        $CaseInfo['OfferImage'] = $OfferImage;
        $CaseInfo['StudentImage'] = $CaseInfo['StudentImage']?ImageURLP2.$CaseInfo['StudentImage']:'';
        $CaseInfo['ApplySeason'] = $CaseInfo['ApplySeason']?$CaseInfo['ApplySeason']:'';
        $CaseInfo['AdmissionSchool'] = $CaseInfo['AdmissionSchool']?$CaseInfo['AdmissionSchool']:'';
        $CaseInfo['ApplySchool'] = $CaseInfo['ApplySchool']?$CaseInfo['ApplySchool']:'';
        $CaseInfo['AttendSchool'] = $CaseInfo['AttendSchool']?$CaseInfo['AttendSchool']:'';
        $CaseInfo['Scholarship'] = $CaseInfo['Scholarship']?$CaseInfo['Scholarship']:'';
        $CaseInfo['AdmissionSpecialty'] = $CaseInfo['AdmissionSpecialty']?$CaseInfo['AdmissionSpecialty']:'';
        $CaseInfo['OnSchool'] = $CaseInfo['OnSchool']?$CaseInfo['OnSchool']:'';
        $CaseInfo['OnSpecialty'] = $CaseInfo['OnSpecialty']?$CaseInfo['OnSpecialty']:'';
        $CaseInfo['GPA'] = ($CaseInfo['GPA']>1)?$CaseInfo['GPA']:'';
        $CaseInfo['TOEFL'] = $CaseInfo['TOEFL']?$CaseInfo['TOEFL']:'';
        $CaseInfo['IELTS'] = ($CaseInfo['IELTS']>1)?$CaseInfo['IELTS']:'';
        $CaseInfo['GRE'] = $CaseInfo['GRE']?$CaseInfo['GRE']:'';
        $CaseInfo['GMAT'] = $CaseInfo['GMAT']?$CaseInfo['GMAT']:'';
        $CaseInfo['SAT'] = $CaseInfo['SAT']?$CaseInfo['SAT']:'';
        $CaseInfo['SSAT'] = $CaseInfo['SSAT']?$CaseInfo['SSAT']:'';
        $CaseInfo['ACT'] = $CaseInfo['ACT']?$CaseInfo['ACT']:'';
        $CaseInfo['Advantage'] = $CaseInfo['Advantage']?$CaseInfo['Advantage']:'';
        $CaseInfo['Disadvantage'] = $CaseInfo['Disadvantage']?$CaseInfo['Disadvantage']:'';
        $CaseInfo['ApplySummary'] = $CaseInfo['ApplySummary']?$CaseInfo['ApplySummary']:'';
        if($CaseInfo){
            $result_json = $CaseInfo;
            $result_json['ResultCode'] = 200;
        }
        else{
            $result_json['ResultCode'] = 101;
            $result_json['Message'] = '数据错误';
        }
        echo json_encode($result_json);
    }

    /**
     * @desc 成功案例保存
     */
    public function SaveSuccessCase(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/

        if($_POST['CaseID']){
            $CaseID = intval($_POST['CaseID']);
            unset($_POST['CaseID']);
        }
        $DoType = trim($_POST['SubmitType']);
        //echo "<pre>";print_r($DoType);exit;
        unset($_POST['SubmitType']);    
        if($DoType == 'ImmediatelyShow'){ //已展示的立即展示
            $Data['Status'] = '2';
            $Url = '/consultantmanage/successcase?S=2'; //保存成功页面,可预览
        }
        elseif($DoType == 'DraftRelease'){ //草稿箱的立即发布
            $Data['Status'] = '2';
            $Url = '/consultantmanage/successcase?S=2'; //保存成功页面,可预览
        }
        elseif($DoType == 'DraftSave'){ //草稿箱的保存
            $Data['Status'] = '1'; //保存成功页面,可预览
            $Url = '/consultantmanage/successcase?S=1'; //保存成功页面,可预览
        }
        elseif($DoType == 'SubmitAudit'){
            $Data['Status'] = '2'; //保存成功页面,可预览
            $Url = '/consultantmanage/successcase?S=2'; //保存成功页面,可预览
        }
        elseif($DoType == 'SaveView'){
            $Data['Status'] = '1'; //保存成功页面,可预览
            $Url = '/consultantmanage/successcase?S=1'; //保存成功页面,可预览
        }
        //判断是否审核通过，没通过的存入草稿箱
        $HasNotice=false;
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);  
        if($Data['Status'] == '2' && $UserInfo['IdentityState']!=2){
            $HasNotice=true;
            $Data['Status'] = '1';
            $Url = '/consultantmanage/successcase?S=1';
        }
        
        //echo "<pre>";print_r($Data);exit;
        $Data['UserID'] = intval($_SESSION['UserID']); //顾问ID

        $Data['StudentName'] = trim($_POST['StudentName']); //学生姓名
        $Data['ApplySeason'] = trim($_POST['ApplySeason']); //申请季
        $Data['AdmissionSchool'] = trim($_POST['AdmissionSchool']); //录取学校，逗号隔开，可多个
        $Data['ApplySchool'] = trim($_POST['ApplySchool']); //申请学校，逗号隔开，可多个
        $Data['AttendSchool'] = trim($_POST['AttendSchool']); //入读学校
        $Data['Scholarship'] = trim($_POST['Scholarship']); //奖学金
        $Data['AdmissionSpecialty'] = trim($_POST['AdmissionSpecialty']); //录取学校专业，逗号隔开，可多个
        $Data['GPA'] = doubleval($_POST['GPA']);
        $Data['TOEFL'] = intval($_POST['TOEFL']);
        $Data['IELTS'] = floatval($_POST['IELTS']);
        $Data['GRE'] = intval($_POST['GRE']);
        $Data['GMAT'] = intval($_POST['GMAT']);
        $Data['SAT'] = intval($_POST['SAT']);
        $Data['SSAT'] = intval($_POST['SSAT']);
        $Data['ACT'] = intval($_POST['ACT']);
        $Data['OnSchool'] = trim($_POST['OnSchool']); //背景学校
        $Data['OnSpecialty'] = trim($_POST['OnSpecialty']); //背景专业

        $Data['Advantage'] = trim($_POST['Advantage']); //案例优势分析
        $Data['Disadvantage'] = trim($_POST['Disadvantage']); //案例劣势分析
        $Data['ApplySummary'] = trim($_POST['ApplySummary']); //申请总结

        //学生头像
        $StudentImage = $_POST['PicPortraits'][0]['Img'];
        if(strpos($StudentImage,'data:image/jpeg;base64')!==false){
            $ImageFullUrl='/up/'.date('Y').'/'.date('md').'/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
            SendToImgServ($ImageFullUrl,str_replace('data:image/jpeg;base64,','',$StudentImage));
            $Data['StudentImage']=$ImageFullUrl;
        }
        else{
            $Data['StudentImage']=str_replace(ImageURLP2,'',$StudentImage);
        }
        //offer图片
        $OfferImage=$_POST['PicOffer'];
        if(!empty($OfferImage)){
            $NewOfferImage = array();
            foreach($OfferImage as $key=>$val){
                if(strpos($val['Img'],'data:image/jpeg;base64')!==false || strpos($val['Img'],'data:image/png;base64')!==false || strpos($val['Img'],'data:image/jpg;base64')!==false){
                    $ImageFullUrl='/up/'.date('Y').'/'.date('md').'/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                    SendToImgServ($ImageFullUrl,str_replace('data:image/jpeg;base64,','',$val['Img']));
                    $NewOfferImage[$key] = $ImageFullUrl;
                }
                else{
                    $NewOfferImage[$key] = str_replace(ImageURLP4,'',$val['Img']);
                }
            }
            $Data['OfferImage']=json_encode($NewOfferImage);
        }
        $CaseModule = new StudyConsultantCaseModule();
        if($CaseID){
            $Result = $CaseModule->UpdateInfoByKeyID($Data,$CaseID);
        }
        else{
            $Data['AddTime'] = time();
            $Result = $CaseModule->InsertInfo($Data);
        }
        if($Result){
            if($UserInfo['IdentityState']!=2 && $HasNotice){
                $json_result=array('ResultCode'=>200,'Message'=>'请先审核身份，暂时为你保存到草稿箱。','Url'=>$Url);
            }else{
                $json_result=array('ResultCode'=>200,'Message'=>'保存成功','Url'=>$Url);
            }
        }
        elseif($Result === 0){
            $json_result=array('ResultCode'=>102,'Message'=>'您没有做任何修改');
        }else{
            $json_result=array('ResultCode'=>101,'Message'=>'保存失败');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  更新成功案例状态
     */
    public function UpdateCaseStatus(){
        $CaseModule = new StudyConsultantCaseModule();
        $Status = $_POST['Status'];
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);        
        if($Status=='2' && $UserInfo['IdentityState']!=2){
            $json_result=array('ResultCode'=>101,'Message'=>'设置展示失败，您的身份还未审核通过');
        }else{
            $CaseID = $_POST['CaseID'];
            $result = $CaseModule->UpdateInfoByKeyID(array('Status'=>$Status),$CaseID);
            if($result){
                $json_result=array('ResultCode'=>200,'Message'=>'更新成功');
            }
            else{
                $json_result=array('ResultCode'=>101,'Message'=>'更新失败');
            }
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  顾问客户管理列表
     */
    /*public function CustomerList(){
        $StudentInfoModule = new StudyConsultantStudentInfoModule();
        $MemberUserInfo = new MemberUserInfoModule();
        $IsComplete = intval($_POST['IsComplete'])?intval($_POST['IsComplete']):1; //默认在办理
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'];
        $Keyword = trim($_POST['Keyword']);
        if($Keyword){
            $MysqlWhere.= " and StudentName LIKE '%".$Keyword."%'" ;
        }
        //获取客户数据
        $Data = $StudentInfoModule->GetInfoByWhere($MysqlWhere,true);
        if($Data){
            $json_result['ResultCode'] = '200';
            foreach($Data as $key=>$val){
                $UserInfo = $MemberUserInfo->GetInfoByUserID($val['StudentID']);
                if(strpos($UserInfo['Avatar'],'http://') === false){
                    $CustomerList_Img = ImageURLP2.$UserInfo['Avatar'];
                }
                else{
                    $CustomerList_Img = $UserInfo['Avatar'];
                }
                $Result[$key] = array(
                    'CustomerList_Id'=>$val['StudentID'],
                    'CustomerList_Img'=>$CustomerList_Img,
                    'CustomerList_Name'=>$val['StudentName'],
                    'CustomerList_Depict'=> '美国'.$val['EducationalBackground'].'   '.$val['GoTime'],
                    'CustomerList_IsComplete'=>$IsComplete
                );
            }
            $json_result['Data'] = $Result;
        }
        else{
            $json_result['ResultCode'] = '101';
            $json_result['Message'] = '没有已完成用户';
        }
        echo json_encode($json_result,JSON_UNESCAPED_UNICODE);
    }*/

    /**
     * @desc  顾问客户资料管理页面
     */
    /*public function CustomerData(){
        $StudentID = intval($_POST['Id']);
        $StudentInfoModule = new StudyConsultantStudentInfoModule();
        $StudentInfo = $StudentInfoModule->GetInfoByWhere(' and StudentID = '.$StudentID.' and ConsultantID='.$_SESSION['UserID']);
        $StudentInfo['Remarks'] = $StudentInfo['Remarks']?json_decode($StudentInfo['Remarks']):'';
        if(!empty($StudentInfo)){
            $StudentInfo['ResultCode'] = 200;
        }
        else{
            $StudentInfo['ResultCode'] = 101;
        }
        echo json_encode($StudentInfo,JSON_UNESCAPED_UNICODE);
    }*/

    /**
     * @desc 顾问客户资料保存
     */
    public function CustomerDataSave(){
        $ID = $_POST['ID'];
        unset($_POST['ID']);
        $Data = $_POST;
        $Data['IELTS'] = floatval($Data['IELTS']);
        foreach ($Data['Remarks'] as $key=>$val){
            $Data['Remarks'][$key]['text'] = str_replace(array("\r\n", "\r", "\n"), "<br>", $val['text']);
        }
        $Data['Remarks'] = json_encode($Data['Remarks'],JSON_UNESCAPED_UNICODE);
        $StudentInfoModule = new StudyConsultantStudentInfoModule();
        $Result = $StudentInfoModule->UpdateInfoByKeyID($Data,$ID);

        if($Result){
            $result_json['ResultCode'] = 200;
            $result_json['Message'] = '保存成功!';
        }
        elseif($Result === 0){
            $result_json['ResultCode'] = 200;
            $result_json['Message'] = '您没有修改任何信息!';
        }
        else{
            $result_json['ResultCode'] = 101;
            $result_json['Message'] = '保存失败!';
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  顾问客户，TA的服务
     */
    /*public function GetCustomerOrder(){
        $UserID = intval($_POST['ID']);
        $OrderModule = new StudyOrderModule();
        $ServiceModule = new StudyConsultantServiceModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $Part = $OrderConsultantModule->Part;

        $IsComplete = intval($_POST['IsComplete']) == 1 ?2:3;

        $OrderStatus = $OrderModule->Status;
        $OrderPayType = $OrderModule->PayType;
        $OrderData = $OrderModule->GetInfoByWhere(" and UserID = {$UserID} and RelationID={$_SESSION['UserID']} and Status = {$IsComplete}",true);
        $Result = array();
        foreach ($OrderData as $key=>$val){
            $Result[$key]['CustomerService_Id'] = $val['OrderID']; //订单id
            $Result[$key]['CustomerService_OrderId'] = $val['OrderNum']; //订单编号
            $Result[$key]['CustomerService_Date'] = date("Y-m-d H:i:s",$val['AddTime']); //订单生成时间
            $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and (Status = 1 or Status = 2) and OrderID ='.$val['OrderID']);
            $Result[$key]['CustomerService_Progress'] = $Part[$OrderConsultantInfo['Type']]?$Part[$OrderConsultantInfo['Type']]['Title'].'服务中':"已完成"; //办理进度
            $Result[$key]['CustomerService_Name'] = $val['OrderName'];   //订单名称
            $Result[$key]['CustomerService_Price'] = $val['Money']; //订单价格
            $Result[$key]['CustomerService_WhetherPay'] = $OrderStatus[$val['Status']]?$OrderStatus[$val['Status']]:''; //订单状态
            $Result[$key]['CustomerService_PayManner'] = $OrderPayType[$val['PayType']]?$OrderPayType[$val['PayType']]:''; //支付方式
            $ServiceInfo = $ServiceModule->GetInfoByKeyID($val['ProductID']);
            $ImageJson = json_decode($ServiceInfo['ImagesJson'],true);
            $Result[$key]['CustomerService_Img'] = ($ImageJson[$ServiceInfo['CoverImageKey']]!='')?(ImageURLP4.$ImageJson[$ServiceInfo['CoverImageKey']]):(ImageURLP2.'/Uploads/Study/Service/service.jpg');  //产品图片
        }
        if($OrderData){
            $result_json['ResultCode'] = 200;
            $result_json['Data'] = $Result;
        }
        else{
            $result_json['ResultCode'] = 101;
            if($IsComplete == 2){
                $result_json['Message'] = '暂无在办订单数据';
            }
            else{
                $result_json['Message'] = '暂无已完成订单数据';
            }
        }
        echo json_encode($result_json);
    }*/

    /**
     * @desc 服务订单列表页，跳往详情页，返回具体进行到哪一步
     */
    public function CustomerServiceMore(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        $OrderModule = new StudyOrderModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $ServiceModule = new StudyConsultantServiceModule();

        $UserInfoModule = new MemberUserInfoModule();
        //接受订单ID
        $OrderID = $_POST['OrderId'];
        //订单详情
        $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
        //点击其他服务时传递
        $Type = $_POST['Type'];

        //学生信息
        $StudentID = $OrderInfo['UserID'];
        $StudentInfo = $UserInfoModule->GetInfoByUserID($StudentID);
        //顾问服务详情
        $ProductID = $OrderInfo['ProductID'];
        $ServiceInfo = $ServiceModule->GetInfoByKeyID($ProductID);
        $ServiceType = $ServiceInfo['ServiceType'];
        if(empty($Type)){
            //订单流程详情(拼凑html代码)
            $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' Order by ID asc',true);
            $Part = $OrderConsultantModule->Part;
            $ResultData = $this->HandleOrderFlow($OrderConsultantInfo,$Part,$OrderID);
        }
        else{
            $ResultData['Type'] = $Type;
        }
        //如果有存在调查表/文书管理的简历，查看调查表
        if($ResultData['Type'] == 1){ //调查表
            $OrderConInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Type = 1');
            if($OrderConInfo && $OrderConInfo['Status'] != 0){
                $ProgressStatus = $OrderConInfo['Status'];
            }
        }
        elseif($ResultData['Type'] == 3){ //文书服务
            $OrderConInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Type = 3');
            if($OrderConInfo && $OrderConInfo['Status'] != 0){
                $ProgressStatus = $OrderConInfo['Status'];
            }
        }
        elseif($ResultData['Type'] == 6){
            $OrderConInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Type = 6');
            if($OrderConInfo && $OrderConInfo['Status'] != 0){
                $ProgressStatus = $OrderConInfo['Status'];
            }
        }
        else{
            $ProgressStatus = '';
        }
        $Result['Intention'] = $ServiceType;  //服务类型：全程服务，申请学校，文书管理， ，材料翻译，背景提升，签证指导
        $Result['Type'] = $ResultData['Type'];  //代表全程服务（其他服务类型也一样）第几步，从1开始计算
        $Result['ProgressTitle'] = $ResultData['Html'];    //拼凑html代码，上部分服务类型导航，第一个div内带上此订单对应的ID
        $Result['ProgressStatus'] = $ProgressStatus;    //代表调查表走到几步，1代表初始化，2代表对话处理中，3代表已定稿
        if($ResultData['Type'] == 1){ //调查表
            $QuestionnaireModule = new StudyConsultantQuestionnaireModule();
            if($ProgressStatus == 2){
                //第一次上传的调查表信息
                $FirstInfo =$QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID asc');
                $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document'],'UpQuestion_Message'=>$FirstInfo['Describe']);
                $OneWhere = ' and ID <> '.$FirstInfo['ID'];
                //当前最后一条的调查表信息
                $LastInfo = $QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID desc');
                if($LastInfo){
                    $TwoWhere = ' and ID <> '.$LastInfo['ID'];
                    $FileName = $LastInfo['DocumentName']?$LastInfo['DocumentName']:'';
                    $File = $LastInfo['Document']?FileURL.$LastInfo['Document']:'';
                    $Result['Data'] = array('OperateID'=>$LastInfo['ID'],'QuestionnaireID'=>$LastInfo['ID'],'Question_Date'=>date("Y-m-d H:i",$LastInfo['AddTime']),'Question_FileName'=>$FileName,'Question_DownUrl'=>$File,'Question_Message'=>$LastInfo['Describe'],'Question_CoupleBackName'=>$LastInfo['Feedback']==1?'学生反馈':'我的反馈');
                }
                else{
                    $Result['Data'] = '';
                    $TwoWhere = '';
                }
                $OtherInfo = $QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.$OneWhere.$TwoWhere.' order by ID desc',true);
                if($OtherInfo){
                    foreach($OtherInfo as $key => $val){
                        $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName']?$val['DocumentName']:'','Question_DownUrl'=>$val['Document']?FileURL.$val['Document']:'','Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈');
                    }
                }
                else{
                    $Result['Data2'] = '';
                }
            }
            elseif($ProgressStatus == 3){
                //定稿数据
                $ConfirmInfo = $QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Status=2');
                if($ConfirmInfo){
                    $Result['DeadCopyData'] = array('DeadCopyd_Date'=>date("Y-m-d H:i",$ConfirmInfo['ConfirmTime']),'DeadCopyd_FileName'=>$ConfirmInfo['DocumentName'],'DeadCopyd_DownUrl'=>FileURL.$ConfirmInfo['Document']);
                    $Where = ' and ID <>'.$ConfirmInfo['ID'];
                }
                else{
                    $Result['DeadCopyData'] = '';
                    $Where = '';
                }
                //第一次上传的调查表信息
                $FirstInfo =$QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID asc');
                $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document']);
                $OneWhere = ' and ID <> '.$FirstInfo['ID'];
                $OtherInfo = $QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.$Where.$OneWhere.' order by ID desc',true);
                if($OtherInfo){
                    foreach($OtherInfo as $key => $val){
                        $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName'],'Question_DownUrl'=>FileURL.$val['Document'],'Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈');
                    }
                }
                else{
                    $Result['Data2'] = '';
                }
            }
        }
        elseif($ResultData['Type'] == 2){//选校定校
            $ChooseSchoolModule = new StudyConsultantChooseSchoolModule();
            //是否存在旧的保存数据，如果没返回空数组(保存好的列表)
            $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and Type = 2 and OrderID='.$OrderID);
            $Result['ChooseSchoolStatus'] = $OrderConsultantInfo['Status'];

            if($OrderConsultantInfo['OtherInfos']){
                $Result['SchoolSelectionNewData'] = json_decode($OrderConsultantInfo['OtherInfos'],true);
            }
            else{
                $Result['SchoolSelectionNewData'] = '';
            }
            //是否存已经发送过给学生的学校，如果无返回空数组（已发送的列表）
            $ChooseInfo = $ChooseSchoolModule->GetInfoByWhere(' and OrderID = '.$OrderID,true);
            if($ChooseInfo){
                foreach($ChooseInfo as $key => $val){
                    $Selection[$key]['SchoolName'] = $val['SchoolName'];
                    $Selection[$key]['SchoolTime'] = $val['SchoolOpensTime'];
                    $Selection[$key]['SchoolSystem'] = $val['EducationalSystem'];
                    $Selection[$key]['LanguageRequirement'] = $val['LanguageRequirements'];
                    $Selection[$key]['SchoolUrl'] = $val['Links'];
                    if($val['Status'] == 1){
                        $Selection[$key]['Status'] = '确认中';
                    }
                    elseif($val['Status'] == 2){
                        $Selection[$key]['Status'] = '已确认';
                    }
                    elseif($val['Status'] == 3){
                        $Selection[$key]['Status'] = '已拒绝';
                    }
                    $Selection[$key]['Remark'] = $val['Describe'];
                }
                $Result['SchoolSelectionHistoryData'] = $Selection;
            }
            else{
                $Result['SchoolSelectionHistoryData'] = '';
            }
        }
        elseif($ResultData['Type'] == 4){ //申请学校
            $SchoolEnrollModule = new StudyConsultantSchoolEnrollModule();
            $SchoolEnrollInfo = $SchoolEnrollModule->GetInfoByWhere(' and OrderID='.$OrderID);
            $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and Type = 4 and OrderID='.$OrderID);
            $Result['SchoolEnrollStatus'] = $OrderConsultantInfo['Status'];
            if(empty($SchoolEnrollInfo)){
                $SchoolEnrollID = $SchoolEnrollModule->InsertInfo(array('OrderID'=>$OrderID,'Status'=>1));
                $SchoolEnrollInfo = $SchoolEnrollModule->GetInfoByKeyID($SchoolEnrollID);
            }
            //申请院校数据
            $Result['SchoolApplyNewData'] = $SchoolEnrollInfo['ApplyData']?json_decode($SchoolEnrollInfo['ApplyData']):'';
            if ($SchoolEnrollInfo['SchoolName']){
                $Result['SchoolMajorName'] = $SchoolEnrollInfo['SchoolName'].'/'.$SchoolEnrollInfo['SpecialtyName'];
            }else{
                $Result['SchoolMajorName'] = '';
            }

            if($SchoolEnrollInfo['Status'] == 1){
                $Result['SchoolApplicationStatus'] = '确认中';
            }
            else{
                $Result['SchoolApplicationStatus'] = '已确认';
            }
            //录取院校数据
            $Result['EnrollSchoolHistoryData'] = $SchoolEnrollInfo['EnrollData']?json_decode($SchoolEnrollInfo['EnrollData']):'';
        }
        elseif($ResultData['Type'] == 5) { //办理签证
            $VisaModule = new StudyConsultantTransactVisaModule();
            $Status = $VisaModule->Status;
            $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and Type = 5 and OrderID='.$OrderID);
            $Result['TransactVisaStatus'] = $OrderConsultantInfo['Status'];
            $VisaInfo = $VisaModule->GetInfoByWhere(' and OrderID = '.$OrderID);
            $Result['SubmitTime'] = $VisaInfo['SubmitVisaTime']?$VisaInfo['SubmitVisaTime']:''; //递交签证日期
            $Result['ResultTime'] = $VisaInfo['VisaEndTime']?$VisaInfo['VisaEndTime']:''; //签证结束日期
            $Result['VisaState'] = $VisaInfo['Country']?$VisaInfo['Country']:''; //签证国家
            $Result['AttendSchool'] = $VisaInfo['AdmissionSchool']?$VisaInfo['AdmissionSchool']:''; //入读学校
            $Result['EntranceTime'] = $VisaInfo['StartSchoolTime']?$VisaInfo['StartSchoolTime']:''; //入学时间
            $Result['Remark'] = $VisaInfo['Remarks']?$VisaInfo['Remarks']:''; //备注
            $Result['Status'] = $VisaInfo['Status']?$Status[$VisaInfo['Status']]:''; //状态
        }
        elseif($ResultData['Type'] == 6){ //材料翻译
            $TranslateModule = new StudyConsultantTranslateModule();
            if($Result['ProgressStatus'] == 2){
                //第一次上传的调查表信息
                $FirstInfo =$TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID asc');
                $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document'],'UpQuestion_Message'=>$FirstInfo['Describe']);
                $OneWhere = ' and ID <> '.$FirstInfo['ID'];
                //当前最后一条的调查表信息
                $LastInfo = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.$OneWhere.' order by ID desc');
                if($LastInfo){
                    $TwoWhere = ' and ID <> '.$LastInfo['ID'];
                    $FileName = $LastInfo['DocumentName']?$LastInfo['DocumentName']:'';
                    $File = $LastInfo['Document']?FileURL.$LastInfo['Document']:'';
                    $Result['Data'] = array('OperateID'=>$LastInfo['ID'],'QuestionnaireID'=>$LastInfo['ID'],'Question_Date'=>date("Y-m-d H:i",$LastInfo['AddTime']),'Question_FileName'=>$FileName,'Question_DownUrl'=>$File,'Question_Message'=>$LastInfo['Describe'],'Question_CoupleBackName'=>$LastInfo['Feedback']==1?'学生提交':'我的反馈');
                }
                else{
                    $Result['Data'] = '';
                    $TwoWhere = '';
                }
                $OtherInfo = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.$OneWhere.$TwoWhere.' order by ID desc',true);
                if($OtherInfo){
                    foreach($OtherInfo as $key => $val){
                        $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName']?$val['DocumentName']:'','Question_DownUrl'=>$val['Document']?FileURL.$val['Document']:'','Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈');
                    }
                }
                else{
                    $Result['Data2'] = '';
                }
            }
            elseif($Result['ProgressStatus'] == 3){
                //定稿数据
                $ConfirmInfo = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Status=2');
                if($ConfirmInfo){
                    $Result['DeadCopyData'] = array('DeadCopyd_Date'=>date("Y-m-d H:i",$ConfirmInfo['ConfirmTime']),'DeadCopyd_FileName'=>$ConfirmInfo['DocumentName'],'DeadCopyd_DownUrl'=>FileURL.$ConfirmInfo['Document']);
                    $Where = ' and ID <>'.$ConfirmInfo['ID'];
                }
                else{
                    $Result['DeadCopyData'] = '';
                    $Where = '';
                }
                //第一次上传的调查表信息
                $FirstInfo =$TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID asc');
                $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document']);
                $OneWhere = ' and ID <> '.$FirstInfo['ID'];
                $OtherInfo = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.$Where.$OneWhere.' order by ID desc',true);
                if($OtherInfo){
                    foreach($OtherInfo as $key => $val){
                        $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName'],'Question_DownUrl'=>FileURL.$val['Document'],'Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生提交':'我的反馈');
                    }
                }
                else{
                    $Result['Data2'] = '';
                }
            }
        }
        elseif($ResultData['Type'] == 7){ //背景提升
            $Result['Url'] = '/consultantmanage/myorderdetails/?ID='.$OrderID;
        }
        $Result['ClienteleName'] = $StudentInfo['RealName']?$StudentInfo['RealName']:$StudentInfo['NickName'];    //学生姓名
        $Result['ResultCode'] = '200';
        echo json_encode($Result);
    }

    /**
     * @desc  处理顾问订单流程
     * @param $OrderConsultantInfo 顾问订单信息
     * @param $Part                 流程详细步骤
     * @param $OrderID  订单ID
     * @return array
     */
    private function HandleOrderFlow($OrderConsultantInfo,$Part,$OrderID){
        $Html = "<div class='serviceProcess' data-id='{$OrderID}'>";
        //echo "<pre>";print_r($OrderConsultantInfo);exit;
        foreach($OrderConsultantInfo as $key => $val){
            if($val['Status'] == 0){
                $Carryout = '';
                $On = '';
                $DataType = '';
            }
            elseif($val['Status'] == 1){
                $Carryout = 'carryout';
                $On = 'on';
                $DataType = $Part[$val['Type']]['Headline'];
                $Type = $val['Type'];
            }
            elseif($val['Status'] == 2){
                $Carryout = 'carryout';
                $On = 'on';
                $DataType = $Part[$val['Type']]['Headline'];
                $Type = $val['Type'];
            }
            else{
                $Carryout = 'carryout';
                $On = '';
                $DataType = $Part[$val['Type']]['Headline'];
                $Type = $val['Type'];
            }
            if($key == 0){
                $first = 'first';
            }
            else{
                $first = '';
            }
            $Html .= "<a href='javascript:void(0)' class='{$first} {$Carryout} {$On}' data-type='{$DataType}'>{$Part[$val['Type']]['Title']}</a><em></em>";
        }
        $Html .='</div>';
        $Result = array(
            'Html'=>$Html,
            'Type'=>$Type
        );
        return $Result;
    }

    /**
     * @desc  顾问服务，调查表点击发送
     */
    public function QuestionDelivery(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        $OperateID = $_POST['OperateID'];
        $ProgressStatus = $_POST['ProgressStatus']; //上次操作的状态
        $Document = $_POST['FileData']?$_POST['FileData']:''; //文件（进制数据）
        $Data['OrderID'] = intval($_POST['ID']);
        $Data['DocumentName'] = trim($_POST['FileName']); //文件名称
        $Data['Describe'] = trim($_POST['Message']); //反馈描述
        $Data['AddTime'] = time(); //添加时间
        //处理上传文件
        include SYSTEM_ROOTPATH.'/Service/Common/Class.ToolService.php';
        $DocumentFile = ToolService::HandleUploadFile('study',$Document);
        $Data['Document'] = $DocumentFile;
        if($ProgressStatus == 1){ //初始化，第一步，顾问反馈
            $OrderConsultantModule = new StudyOrderConsultantModule();
            $OrderConsultantModule->UpdateInfoByWhere(array('Status'=>2),' OrderID ='.$Data['OrderID'].' and Type=1');
        }
        $Data['Status'] = 1; //状态服务中
        $Data['Feedback'] = 2; //顾问身份
        $QuestionnaireModule = new StudyConsultantQuestionnaireModule();
        if($OperateID){
            $Result = $QuestionnaireModule->UpdateInfoByKeyID($Data,$OperateID);
            $ID = $OperateID;
        }
        else{
            $Result = $QuestionnaireModule->InsertInfo($Data);
            $ID = $Result;
        }
        if($Result || $Result === 0){
            $result_json = array('ResultCode'=>200,'Message'=>'发送成功','DownUrl'=>FileURL.$Data['Document'],'OperateID'=>$ID);
        }
        else{
            $result_json = array('ResultCode'=>101,'Message'=>'发送失败');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  调查表顾问确认定稿
     */
    public function QuestionDeadCopy(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        $OrderID = intval($_POST['ID']);//订单ID
        $QuestionnaireID = intval($_POST['QuestionnaireID']);//调查表ID

        $QuestionnaireModule = new StudyConsultantQuestionnaireModule();
        $OrderModule = new StudyOrderModule();
        $ServiceModule = new StudyConsultantServiceModule();
        $ConsultantModule = new StudyOrderConsultantModule();

        //订单详情
        $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
        //服务详情
        $ServiceInfo = $ServiceModule->GetInfoByKeyID($OrderInfo['ProductID']);
        $ServiceType = $ServiceInfo['ServiceType'];
        $ConfirmTime = time();

        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        $QuestionnaireInfo = $QuestionnaireModule->GetInfoByKeyID($QuestionnaireID);
        if(empty($QuestionnaireInfo['Document'])){
            $HasDocumentInfo = $QuestionnaireModule->GetInfoByWhere(' and (`Document` is not null and `Document`<>"") and Feedback = 1 and OrderID ='.$OrderID.' order by ID desc');
            $Result = $QuestionnaireModule->UpdateInfoByKeyID(array('Status'=>2,'ConfirmTime'=>$ConfirmTime,'Document'=>$HasDocumentInfo['Document'],'DocumentName'=>$HasDocumentInfo['DocumentName']),$QuestionnaireID);
            $QuestionnaireInfo['Document'] = $HasDocumentInfo['Document'];
            $QuestionnaireInfo['DocumentName'] = $HasDocumentInfo['DocumentName'];
        }
        else{
            $Result = $QuestionnaireModule->UpdateInfoByKeyID(array('Status'=>2,'ConfirmTime'=>$ConfirmTime),$QuestionnaireID);
        }
        if($Result){
            //更新调查表状态为完成
            $Result1 = $ConsultantModule->UpdateInfoByWhere(array('Status'=>3,'ConfirmTime'=>$ConfirmTime),' OrderID = '.$OrderID.' and Type=1');
            if($Result1){
                //更新调查表下一步流程状态为初始化
                $NextFlow = $ConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and status = 0 order by ID asc');
                $Result2  = $ConsultantModule->UpdateInfoByKeyID(array('Status'=>1),$NextFlow['ID']);
                if($Result2){
                    //判断是否还在犹豫期，如果是，则将犹豫期状态改掉。
                    if($OrderInfo['IsHesitate'] == 0){ //还在犹豫期
                        $Result3 = $OrderModule->UpdateInfoByKeyID(array('IsHesitate'=>1),$OrderID);
                        if(!$Result3){
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $result_json = array('ResultCode'=>'104','Message'=>'定稿失败','Describe'=>'更新订单表犹豫期状态失败');
                        }
                        else{
                            if($ServiceType == 1 || $ServiceType == 2){ //全程服务,//学校申请
                                $ConsultantInfoModule = new StudyConsultantInfoModule();
                                $Scale = $ConsultantInfoModule->Scale;
                                $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
                                //服务所占比例
                                $ServiceScale = $ServiceType == 1?$ConsultantModule->AllLifeService[1]:$ConsultantModule->SchoolApply['1'];
                                //直接全款*顾问应获比例*服务占订单的比例
                                $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']]*$ServiceScale;//操作金额,当前服务的订单部分金额

                                //更新订单流程表结算金额
                                $ConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 1');

                                $result_json = $this->AmountOperate($_SESSION['UserID'],$Amt,$OrderInfo['OrderName'].'-调查表服务',$DB);
                                if($result_json['ResultCode'] == 200){
                                    $DB->query("COMMIT");//执行事务
                                    $result_json = array('ResultCode'=>'200','Message'=>'定稿成功','DownUrl'=>FileURL.$QuestionnaireInfo['Document'],'DeadCopy_FileName'=>$QuestionnaireInfo['DocumentName'],'DeadCopy_Date'=>$ConfirmTime);
                                }
                                else{
                                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                                }
                            }
                            else{
                                $DB->query("COMMIT");//执行事务
                                $result_json = array('ResultCode'=>'200','Message'=>'定稿成功','DownUrl'=>FileURL.$QuestionnaireInfo['Document'],'DeadCopy_FileName'=>$QuestionnaireInfo['DocumentName'],'DeadCopy_Date'=>$ConfirmTime);
                            }
                        }
                    }
                    else{
                        if($ServiceType == 1 || $ServiceType == 2){ //全程服务,//学校申请
                            $ConsultantInfoModule = new StudyConsultantInfoModule();
                            $Scale = $ConsultantInfoModule->Scale;
                            $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
                            //服务所占比例
                            $ServiceScale = $ServiceType == 1?$ConsultantModule->AllLifeService[1]:$ConsultantModule->SchoolApply['1'];
                            //直接全款*顾问应获比例*服务占订单的比例
                            $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']]*$ServiceScale;//操作金额,当前服务的订单部分金额
                            //更新订单流程表结算金额
                            $ConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 1');
                            $result_json = $this->AmountOperate($_SESSION['UserID'],$Amt,$OrderInfo['OrderName'].'-调查表服务',$DB);
                            if($result_json['ResultCode'] == 200){
                                $DB->query("COMMIT");//执行事务
                                $result_json = array('ResultCode'=>'200','Message'=>'定稿成功','DownUrl'=>FileURL.$QuestionnaireInfo['Document'],'DeadCopy_FileName'=>$QuestionnaireInfo['DocumentName'],'DeadCopy_Date'=>$ConfirmTime);
                            }
                            else{
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                            }
                        }
                        else{
                            $DB->query("COMMIT");//执行事务
                            $result_json = array('ResultCode'=>'200','Message'=>'定稿成功','DownUrl'=>FileURL.$QuestionnaireInfo['Document'],'DeadCopy_FileName'=>$QuestionnaireInfo['DocumentName'],'DeadCopy_Date'=>$ConfirmTime);
                        }
                    }
                }
                else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $result_json = array('ResultCode'=>'103','Message'=>'定稿失败','Describe'=>'初始化下一个流程失败');
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>'102','Message'=>'定稿失败','Describe'=>'更新订单流程状态失败');
            }
        }
        else{
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            $result_json = array('ResultCode'=>'101','Message'=>'定稿失败','Describe'=>'更新调查表状态失败');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  资金操作
     * @param $ConsultantID 顾问ID
     * @param $Amt  操作金额
     * @param $Remarks 产品名称-服务名称
     * @param $DB
     * @return array
     */
    public function AmountOperate($ConsultantID,$Amt,$Remarks,$DB){
        $BankFlowModule = new MemberUserBankFlowModule();
        $BankModule = new MemberUserBankModule();
        //操作资金
        $BankInfo = $BankModule->GetInfoByWhere(' and UserID='.$ConsultantID);
        if(!$BankInfo){
            $BankID = $BankModule->InsertInfo(array('UserID'=>$ConsultantID,'TotalBalance'=>0,'FrozenBalance'=>0,'FreeBalance'=>0));
            $BankInfo = $BankModule->GetInfoByKeyID($BankID);
        }
        //操作后的总金额
        $Amount = $BankInfo['TotalBalance']+$Amt;
        $BankIsOk = $BankModule->UpdateInfoByKeyID(array('TotalBalance'=>$Amount,'FreeBalance'=>$BankInfo['FreeBalance']+$Amt),$BankInfo['BankID']);
        if($BankIsOk){
            //操作资金记录,OperateType=>系统入账,Type=>留学
            $BankFlowData = array('FromIP'=>GetIP(),'UserID'=>$ConsultantID,'Amount'=>$Amount,'Amt'=>$Amt,'OperateType'=>4,'Remarks'=>$Remarks,'Type'=>2,'AddTime'=>date("Y-m-d H:i:s",time()));
            $BankFlowIsOK = $BankFlowModule->InsertInfo($BankFlowData);
            if($BankFlowIsOK){
                $Result = array('ResultCode'=>'200','Message'=>'操作成功');
            }
            else{
                $Result = array('ResultCode'=>'113','Message'=>'确认失败','Describe'=>'资金日志更新失败');
            }
        }
        else{
            $Result = array('ResultCode'=>'112','Message'=>'确认失败','Describe'=>'资金表更新失败');
        }
        return $Result;
    }

    /**
     * @desc  保存定校选校
     */
    public function SchoolSelectionSave(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        $OrderConModule = new StudyOrderConsultantModule();
        $OrderID = intval($_POST['ID']);
        $Type = intval($_POST['Type']); //保存或者发送
        $OrderConInfo = $OrderConModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type = 2');
        if($Type == 1){ //保存
            $PostData = json_encode($_POST['SchoolData'],JSON_UNESCAPED_UNICODE);
            if($OrderConInfo['Status'] == 1){
                $Result = $OrderConModule->UpdateInfoByKeyID(array('OtherInfos'=>$PostData,'Status'=>2),$OrderConInfo['ID']);
            }
            else{
                $Result = $OrderConModule->UpdateInfoByKeyID(array('OtherInfos'=>$PostData),$OrderConInfo['ID']);
            }
            if($Result){
                $result_json = array('ResultCode'=>'200','Message'=>'保存成功',);
            }
            else{
                $result_json = array('ResultCode'=>'101','Message'=>'保存失败',);
            }
        }
        else{//发送
            $ChooseSchoolModule = new StudyConsultantChooseSchoolModule();
            $PostData = $_POST['SchoolData'];
            $i = 0;
            //开启事务
            global $DB;
            $DB->query("BEGIN");//开始事务定义
            foreach($PostData as $key=>$val){
                $Data[$key] = array(
                    'OrderID'=>$OrderID,
                    'SchoolName'=>trim($val['SchoolName']), //学校名称
                    'SchoolOpensTime'=>trim($val['SchoolTime']), //开学时间
                    'EducationalSystem'=>trim($val['SchoolSystem']), //学制
                    'LanguageRequirements'=>trim($val['LanguageRequirement']), //语言要求
                    'Links'=>trim($val['SchoolUrl']),  //链接
                    'Status'=>1, //确认中
                    'Describe'=>trim($val['Remark']), //备注
                    'AddTime'=>time(),
                );
                $Result = $ChooseSchoolModule->InsertInfo($Data[$key]);
                if(!$Result){
                    $i++;
                }
            }
            if($i>0){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>'101','Message'=>'发送失败','Describe'=>'选校定校表添加失败');
            }else{
                $Result1 = $OrderConModule->UpdateInfoByKeyID(array('OtherInfos'=>''),$OrderConInfo['ID']);
                if($Result1 || $Result1 === 0){
                    $DB->query("COMMIT");//执行事务
                    $result_json = array('ResultCode'=>'200','Message'=>'发送成功');
                }
                else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $result_json = array('ResultCode'=>'102','Message'=>'发送失败','Describe'=>'更新订单流程表OtherInfo信息失败');
                }
            }
        }
        echo json_encode($result_json);
    }

    /**
     * @desc 文书管理数据获取
     */
    public function DocumentManageLoad(){

        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/

        $OrderID = intval($_POST['ID']);
        $Type = intval($_POST['Type'])?intval($_POST['Type']):1;
        $DocumemtModule = new StudyConsultantDocumentModule();
        $StudyOrderConsultantModule =  new StudyOrderConsultantModule();
        /*$StudyOrderConsultantInfo = $StudyOrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Type = 3');
        $ProgressStatus = $StudyOrderConsultantInfo['Status'];//代表文书服务走到几步，1代表初始化，2代表对话处理中，3代表已定稿*/
        //当前最后一条的文书表信息
        $LastInfo  = $DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type='.$Type.' order by ID desc');
        if(empty($LastInfo)){
            $ProgressStatus = '1';    //代表文书表走到几步，1代表初始化，2代表对话处理中，3代表已定稿
        }
        else{
            $ProgressStatus = $LastInfo['Status'];    //代表调查表走到几步，1代表初始化，2代表对话处理中，3代表已定稿
            if($ProgressStatus == 2){ //处理中
                //第一次上传的文书表信息
                $FirstInfo =$DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type='.$Type.' order by ID asc');
                if($FirstInfo){
                    $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document']);
                    $OneWhere = ' and ID <> '.$FirstInfo['ID'];
                }
                else{
                    $OneWhere = '';
                }
                if($FirstInfo['ID'] != $LastInfo['ID'] && $LastInfo){
                    //最后一次上传的文书表信息
                    $TwoWhere = ' and ID <> '.$LastInfo['ID'];
                    $FileName = $LastInfo['DocumentName']?$LastInfo['DocumentName']:'';
                    $File = $LastInfo['Document']?FileURL.$LastInfo['Document']:'';
                    $Result['Data'] = array('OperateID'=>$LastInfo['ID'],'QuestionnaireID'=>$LastInfo['ID'],'Question_Date'=>date("Y-m-d H:i",$LastInfo['AddTime']),'Question_FileName'=>$FileName,'Question_DownUrl'=>$File,'Question_Message'=>$LastInfo['Describe'],'Question_CoupleBackName'=>$LastInfo['Feedback']==1?'学生反馈':'我的反馈');
                }
                else{
                    $TwoWhere = '';
                    $Result['Data'] = '';
                }
                $OtherInfo = $DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.$OneWhere.$TwoWhere.' and Type='.$Type.' order by ID desc',true);
                if($OtherInfo){
                    foreach($OtherInfo as $key => $val){
                        $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName']?$val['DocumentName']:'','Question_DownUrl'=>$val['Document']?FileURL.$val['Document']:'','Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈');
                    }
                }
                else{
                    $Result['Data2'] = '';
                }
            }
            elseif($ProgressStatus == 3){ //已定稿
                //定稿数据
                $ConfirmInfo = $DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type='.$Type.' and Status=3');
                $Result['DeadCopyData'] = array('DeadCopyd_Date'=>date("Y-m-d H:i",$ConfirmInfo['ConfirmTime']),'DeadCopyd_FileName'=>$ConfirmInfo['DocumentName'],'DeadCopyd_DownUrl'=>FileURL.$ConfirmInfo['Document']);
                $Where = ' and ID <>'.$ConfirmInfo['ID'];
                //第一次上传的调查表信息
                $FirstInfo =$DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type='.$Type.' order by ID asc');
                $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document']);
                $OneWhere = ' and ID <> '.$FirstInfo['ID'];
                $OtherInfo = $DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.$Where.$OneWhere.' and Type = '.$Type.' order by ID desc',true);
                if($OtherInfo){
                    foreach($OtherInfo as $key => $val){
                        $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName'],'Question_DownUrl'=>FileURL.$val['Document'],'Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈');
                    }
                }
                else{
                    $Result['Data2'] = '';
                }
            }
        }
        $Result['ResultCode'] = 200;
        $Result['ProgressStatus'] = $ProgressStatus;
        echo json_encode($Result);
    }

    /**
     * @desc  顾问文书服务，点击发送
     */
    public function DocumentManageDelivery(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        $OperateID = $_POST['OperateID'];
        $ProgressStatus = $_POST['ProgressStatus']; //上次操作的状态
        $Document = $_POST['FileData']; //文件（进制数据）
        $Data['OrderID'] = intval($_POST['ID']);
        $Data['DocumentName'] = trim($_POST['FileName']); //文件名称
        $Data['Describe'] = trim($_POST['Message']); //反馈描述
        $Data['Type'] = intval($_POST['PresentType']);
        $Data['AddTime'] = time(); //添加时间

        //处理上传文件
        include_once SYSTEM_ROOTPATH.'/Service/Common/Class.ToolService.php';
        $DocumentFile = ToolService::HandleUploadFile('study',$Document);
        $Data['Document'] = $DocumentFile;
        if($ProgressStatus == 1){ //初始化，第一步，顾问反馈
            $OrderConsultantModule = new StudyOrderConsultantModule();
            $OrderConsultantModule->UpdateInfoByWhere(array('Status'=>2),' OrderID ='.$Data['OrderID'].' and Type=3');
        }
        $Data['Status'] = 2; //状态服务中
        $Data['Feedback'] = 2; //顾问
        $DocumentModule = new StudyConsultantDocumentModule();
        if($OperateID){
            $Result = $DocumentModule->UpdateInfoByKeyID($Data,$OperateID);
            $ID = $OperateID;
        }
        else{
            $Result = $DocumentModule->InsertInfo($Data);
            $ID = $Result;
        }
        if($Result){
            $result_json = array('ResultCode'=>200,'Message'=>'发送成功','DownUrl'=>FileURL.$Data['Document'],'OperateID'=>$ID);
        }
        else{
            $result_json = array('ResultCode'=>101,'Message'=>'发送失败');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc 学校申请点击保存发送
     */
    public function SchoolApplySave(){
        $SchoolEnrollModule = new StudyConsultantSchoolEnrollModule();
        $Data = array('ApplyData'=>json_encode($_POST['SchoolData'],JSON_UNESCAPED_UNICODE));
        $Result = $SchoolEnrollModule->UpdateInfoByWhere($Data,' OrderID = '.$_POST['ID']);
        if($Result){
            $result_json = array('ResultCode'=>200,'Message'=>'发送成功');
        }
        else{
            $result_json = array('ResultCode'=>101,'Message'=>'发送失败');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc 学校录取点击保存发送
     */
    public function EnrollSchoolSave(){
        $SchoolEnrollModule = new StudyConsultantSchoolEnrollModule();
        $OrderID = $_POST['ID'];
        if($_POST['SchoolData']){
            $EnrollData = $_POST['SchoolData'];
            foreach($EnrollData as $key=>$val){
                if(strpos($val['OfferImgUrl'],'data:image/jpeg;base64')!==false){
                    $ImageFullUrl='/up/'.date('Y').'/'.date('md').'/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                    SendToImgServ($ImageFullUrl,str_replace('data:image/jpeg;base64,','',$val['OfferImgUrl']));
                    $EnrollData[$key]['OfferImgUrl']=LImageURL.$ImageFullUrl;
                }
            }
        }
        $Result = $SchoolEnrollModule->UpdateInfoByWhere(array('EnrollData'=>json_encode($EnrollData,JSON_UNESCAPED_UNICODE)),' OrderID = '.$OrderID);
        if($Result){
            $result_json = array('ResultCode'=>200,'Message'=>'发送成功');
        }
        else{
            $result_json = array('ResultCode'=>101,'Message'=>'发送失败');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc 办理签证保存
     */
    public function ApplyVisaSave(){
        $VisaModule = new StudyConsultantTransactVisaModule();
        $OrderID = $_POST['ID'];
        $VisaInfo = $VisaModule->GetInfoByWhere(' and OrderID = '.$OrderID);
        $Data = array(
            'OrderID'=>$OrderID,
            'SubmitVisaTime'=>$_POST['SubmitTime'],//递交签证日期
            'Country'=>trim($_POST['VisaState']),//签证国家
            'AdmissionSchool'=>trim($_POST['AttendSchool']),//入读学校
            'StartSchoolTime'=>trim($_POST['EntranceTime']),//计划入学时间
            'VisaEndTime'=>trim($_POST['ResultTime']),//签证结束日期
            'Status'=>$_POST['Status'],  //签证状态
            'Remarks'=>trim($_POST['Remark']),//备注
            'AddTime'=>time() //添加时间
        );
        if($VisaInfo){
            $Result = $VisaModule->UpdateInfoByKeyID($Data,$VisaInfo['ID']);
        }
        else{
            $Result = $VisaModule->InsertInfo($Data);
        }
        if($Result>0){
            $result_json = array('ResultCode'=>200,'Message'=>' 保存发送成功');
        }
        elseif($Result === 0){
            $result_json = array('ResultCode'=>200,'Message'=>' 您未做任何修改');
        }
        else{
            $result_json = array('ResultCode'=>101,'Message'=>' 保存发送失败');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  顾问提交材料翻译资料
     */
    public function DataTranslationDelivery(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/

        $Document = $_POST['FileData']?$_POST['FileData']:''; //文件（进制数据）
        $TranslateID = intval($_POST['TranslateID']); //材料翻译ID
        $Data['OrderID'] = intval($_POST['ID']);  //订单ID
        $Data['DocumentName'] = trim($_POST['FileName']); //文件名称
        $Data['Describe'] = trim($_POST['Message']); //反馈描述
        $Data['AddTime'] = time(); //添加时间
        //处理上传文件
        include SYSTEM_ROOTPATH.'/Service/Common/Class.ToolService.php';
        $DocumentFile = ToolService::HandleUploadFile('study',$Document);
        $Data['Document'] = $DocumentFile;


        $Data['Status'] = 1; //状态服务中
        $Data['Feedback'] = 2; //顾问身份

        $TranslateModule = new StudyConsultantTranslateModule();
        if($TranslateID){
            $Result = $TranslateModule->UpdateInfoByKeyID($Data,$TranslateID);
            $ID = $TranslateID;
        }else{
            $Result = $TranslateModule->InsertInfo($Data);
            $ID = $Result;
        }
        if($Result || $Result === 0){
            $result_json = array('ResultCode'=>200,'Message'=>'发送成功','DownUrl'=>FileURL.$Data['Document'],'OperateID'=>$ID);
        }
        else{
            $result_json = array('ResultCode'=>101,'Message'=>'发送失败');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc 顾问订单管理
     */    
    public function OrderList(){
        $StudyOrderModule=new StudyOrderModule();
        switch (intval($_POST['Type'])){
            case 1 :
                $MysqlWhere="and RelationID={$_SESSION['UserID']}";
                break;
            case 2:
                $MysqlWhere="and RelationID={$_SESSION['UserID']} and (`Status`= 2 or `Status`= 4 or `Status`= 5 or `Status`=7)";
                break;
            case 3:
                $MysqlWhere="and RelationID={$_SESSION['UserID']} and `Status`=3";
                break;
            case 4:
                $MysqlWhere="and RelationID={$_SESSION['UserID']} and `Status`=1";
                break;
            case 5:
                $MysqlWhere="and RelationID={$_SESSION['UserID']} and `Status`= 6";
                break;
            default:
                $MysqlWhere="and RelationID={$_SESSION['UserID']}";
                break;
        }
        $Rscount = $StudyOrderModule->GetListsNum($MysqlWhere);
        $Page=intval($_POST['Page']);
        if ($Page < 1) {
            $Page = 1;
        }
        $PageSize=3;
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            $Data['Data'] = $StudyOrderModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            $MemberUserInfoModule = new MemberUserInfoModule();
            $StudyConsultantServiceModule=new StudyConsultantServiceModule();
            //订单状态
            $Status=$StudyOrderModule->Status;
            //服务类型
            $ServiceType=$StudyConsultantServiceModule->ServiceType;
            //支付类型
            $PayType=$StudyOrderModule->PayType;
            $OrderList=array();
             foreach($Data['Data'] as $Key=>$Val){
                 $OrderList[$Key]['Id']=$Val['OrderID'];
                 $OrderList[$Key]['OrderId']=$Val['OrderNum'];
                 $OrderList[$Key]['OrderDate']=date('Y-m-d',$Val['AddTime']);
                $MemberInfo=$MemberUserInfoModule->GetInfoByUserID($Val['UserID']);
                if (strpos($MemberInfo['Avatar'], 'http://') === false && $MemberInfo['Avatar'] != '') {
                    $MemberInfo['Avatar'] = LImageURL . $MemberInfo['Avatar'];
                }
                $OrderList[$Key]['OrderNameImg']=$MemberInfo['Avatar'];
                $OrderList[$Key]['OrderUrl']=WEB_STUDY_URL.'/consultantmanage/myorderdetails/?ID='.$Val['OrderID'];
                $OrderList[$Key]['OrderName']=$MemberInfo['NickName'];
                $ServiceInfo=$StudyConsultantServiceModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and ServiceID={$Val['ProductID']}");
                $OrderList[$Key]['OrderServiceStatus']=$Status[$Val['Status']];
                $OrderList[$Key]['OrderServiceType']=$ServiceType[$ServiceInfo['ServiceType']];
                $OrderList[$Key]['ServiceType']=$ServiceInfo['ServiceType'];
                $OrderList[$Key]['OrderEndDate']=date('Y-m-d H:i:s',$Val['ConsiderTime']);
                $ServiceImg=json_decode($ServiceInfo['ImagesJson'],true);
                if(!empty($ServiceImg)){
                    if(strpos($ServiceImg[$ServiceInfo['CoverImageKey']],"http://")===false){
                        $ServiceImg[$ServiceInfo['CoverImageKey']]=ImageURLP4.$ServiceImg[$ServiceInfo['CoverImageKey']];
                    }
                    $OrderList[$Key]['OrderImg']=$ServiceImg[$ServiceInfo['CoverImageKey']];
                }else{
                    $OrderList[$Key]['OrderImg']=ImageURLP2.'/Uploads/Study/Service/service.jpg';;
                }
                $OrderList[$Key]['OrderServiceName']=$ServiceInfo['ServiceName'];
                $OrderList[$Key]['OrderPrice']=$Val['Money'];
                if($Val['PayType']>0){
                    $OrderList[$Key]['OrderPayment']=$PayType[$Val['PayType']];
                }else{
                    $OrderList[$Key]['OrderPayment']='';
                }
                if($Val['Status']>1){
                    $OrderList[$Key]['OrderWhetherPay']='已支付';
                }else{
                    $OrderList[$Key]['OrderWhetherPay']='未支付';
                }
            }
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
            $result_json=array(
                'ResultCode'=>200,
                'RecordCount'=>$Data['RecordCount'],
                'PageSize'=>$Data['PageSize'],
                'PageCount'=>$Data['PageCount'],
                'Page'=>$Data['Page'],
                'NextPage'=>$NextPage,
                'BackPage'=>$BackPage,
                'LastPage'=>$Data['PageCount'],
                'FirstPage'=>1,
                'PageNums'=>$Data['PageNums'],
                'OrderList'=>$OrderList
            );
        }else{
            $result_json=array(
                'ResultCode'=>100,
                'Message'=>'目前没有此栏目相关数据'
            );
        }
        echo json_encode($result_json,JSON_UNESCAPED_UNICODE);
    }
}
