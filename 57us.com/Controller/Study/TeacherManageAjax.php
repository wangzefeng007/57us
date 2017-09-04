<?php
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class TeacherManageAjax extends CommonController{

    public function __construct(){
        $this->TeacherLoginStatus();
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
     * @desc 教师课程列表
     */
    public function TearchCourseList(){
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
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
            $MysqlWhere.=" and CourseName like '%$KeyWords%'";
        }
        $Rscount = $StudyTeacherCourseModule->GetListsNum($MysqlWhere);
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
            
            $Data['Data'] = $StudyTeacherCourseModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
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
            $CourseList=array();
            foreach($Data['Data'] as $Key => $Val){
                $CourseList[$Key]['CourseList_Id']=$Val['CourseID'];
                $ImageArr=json_decode($Val['ImagesJson'],true);
                if(!empty($ImageArr)){
                    if(strpos($ImageArr[$Val['CoverImageKey']],'http://')!==false){
                        $CourseList[$Key]['CourseList_Img']=$ImageArr[$Val['CoverImageKey']];
                    }else{
                        $CourseList[$Key]['CourseList_Img']=LImageURL.$ImageArr[$Val['CoverImageKey']];
                    }
                }else{
                    $CourseList[$Key]['CourseList_Img']=ImageURLP2.'/Uploads/Study/Service/service.jpg';
                }
                $CourseList[$Key]['CourseList_Name']=$Val['CourseName'];
                $CourseList[$Key]['CourseList_Depict']=$Val['CourseDescription'];
                $CourseList[$Key]['CourseList_Picre']=$Val['CoursePrice'];
                $CourseList[$Key]['CourseList_Url']=WEB_STUDY_URL.'/teacher_course/'.$Val['CourseID'].'.html';
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
                'Data'=>$CourseList
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
    * @desc 教师课程上下架 
     */
    public function TearchCourseListOperation(){
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
        $CourseID=intval($_POST['id']);
        $CourseInfo=$StudyTeacherCourseModule->GetInfoByKeyID($CourseID);
        if($CourseInfo){
            if($CourseInfo['Status']==3){
                $Data['Status']=4;
            }elseif($CourseInfo['Status']==4){
                $Data['Status']=3;
            }
            $result=$StudyTeacherCourseModule->UpdateInfoByWhere($Data,"UserID={$_SESSION['UserID']} and CourseID=$CourseID");
            if($result!==false){
                $json_result=array('ResultCode'=>200,'Message'=>'操作成功');
            }else{
                $json_result=array('ResultCode'=>101,'Message'=>'操作失败');
            }
        }else{
            $json_result=array('ResultCode'=>100,'Message'=>'该课程不存在');
        }
        echo json_encode($json_result);
    }
    
    /**
    * @desc 教师课程删除 
     */
    public function TeacherCourseListDelete(){
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
        $CourseID=intval($_POST['id']);
        $CourseInfo=$StudyTeacherCourseModule->GetInfoByKeyID($CourseID);
        if($CourseInfo){
            $Data['Status']=5;
            $result=$StudyTeacherCourseModule->UpdateInfoByWhere($Data,"UserID={$_SESSION['UserID']} and CourseID=$CourseID");
            if($result!==false){
                $json_result=array('ResultCode'=>200,'Message'=>'操作成功');
            }else{
                $json_result=array('ResultCode'=>101,'Message'=>'操作失败');
            }
        }else{
            $json_result=array('ResultCode'=>100,'Message'=>'该课程不存在');
        }
        echo json_encode($json_result);        
    }
    
    /**
    * @desc 教师课程提交审核
     */
    public function TeacherCourseSubmitAudit(){
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        if($UserInfo['IdentityState']!=2){
            $json_result=array('ResultCode'=>100,'Message'=>'您的身份还未审核通过');
        }else{
            $StudyTeacherCourseModule = new StudyTeacherCourseModule();
            $CourseID=intval($_POST['id']);
            $CourseInfo=$StudyTeacherCourseModule->GetInfoByKeyID($CourseID);
            if($CourseInfo){
                $Data['Status']=1;
                $result=$StudyTeacherCourseModule->UpdateInfoByWhere($Data,"UserID={$_SESSION['UserID']} and CourseID=$CourseID");
                if($result!==false){
                    $json_result=array('ResultCode'=>200,'Message'=>'操作成功');
                }else{
                    $json_result=array('ResultCode'=>101,'Message'=>'操作失败');
                }
            }else{
                $json_result=array('ResultCode'=>100,'Message'=>'该课程不存在');
            }            
        }
        echo json_encode($json_result);        
    }
    /**
    * @desc 教师课程添加
     */
    public function AddCourse(){
        $CourseID=intval($_POST['ID']);
        $SubmitType=trim($_POST['SubmitType']);
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);        
        if($SubmitType=='SubmitAudit' && $UserInfo['IdentityState']==2){
            $Data['Status']=1;
        }else{
            $Data['Status']=0;
        }
        $Data['CourseName']=trim($_POST['CourseName']);
        $CourseTypeKeyMap=array("IELTS"=>1,"TOEFL"=>2,"SAT"=>3,"ACT"=>4,"GAMT"=>5,"GRE"=>6,"PTE"=>7);
        $Data['CourseType']=$CourseTypeKeyMap[$_POST['TrainSubject']];
        $Data['TeachType']=intval($_POST['FormClass']);
        $Data['ClassSize']=intval($_POST['ClassSize']);
        $Data['CoursePrice']=intval($_POST['CoursePrice']);
        $CoursePackage=$_POST['CoursePeriod'];
        if(!empty($CoursePackage)){
            $Data['CoursePackage']=array();
            foreach($CoursePackage as $Val){
                if(!in_array($Val['Course'],$Data['CoursePackage'])){
                    $Data['CoursePackage'][]=$Val['Course'];
                }
            }
            $Data['CoursePackage']=json_encode($Data['CoursePackage']);
        }
        
        $Data['AllowCustom']=intval($_POST['WhetherCourse']);
        $Data['CourseTags']=json_encode($_POST['CourseTag'],JSON_UNESCAPED_UNICODE);
        $Data['CourseDescription']=trim($_POST['CourseIntroduction']);
        $ImageArr=$_POST['CourseImg'];
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
            $Data['CoverImageKey']=$_POST['CourseDefaultImg'];
        }
        //文本图片处理-----------------------------------------------------------------------------
        $Data['Content'] = $_POST['CourseDetails'];
        $Pattern=array();
        $Replacement=array();
        $ImgArr=Array();
        preg_match_all('/<img.*src="(.*)".*>/iU',stripcslashes($Data['Content']),$ImgArr);
        if(count($ImgArr[0])){
            foreach($ImgArr[0] as $Key => $ImgTag){
                $Pattern[]=$ImgTag;
                $Replacement[]=preg_replace("/http:\/\/images\.57us\.com\/l/iU","",preg_replace(array('/title=".*"/iU','/alt=".*"/iU'),'',$ImgTag));
            }
        }        
        $Data['Content'] = addslashes(str_replace($Pattern,$Replacement,stripcslashes($Data['Content'])));
        //文本图片处理-------------------------------------------------------------------------------                    
        $Data['AddTime']=time();
        
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
        if(!$CourseID){
            $Data['UserID']=$_SESSION['UserID'];    
            $Data['UpdateTime']=$Data['AddTime'];
            $Result=$StudyTeacherCourseModule->InsertInfo($Data);
        }else{
            $Data['UpdateTime']=time();
            $Result=$StudyTeacherCourseModule->UpdateInfoByKeyID($Data,$CourseID);
        }
        if($Result){
            if($Data['Status']==1){
                if(!$CourseID){
                    $Url=WEB_STUDY_URL.'/teachermanage/underreview/?ID='.$Result;
                }else{
                    $Url=WEB_STUDY_URL.'/teachermanage/underreview/?ID='.$CourseID;
                }
            }else{
                if(!$CourseID){
                    $Url=WEB_STUDY_URL.'/teachermanage/savesuccess/?ID='.$Result;      
                }else{
                    $Url=WEB_STUDY_URL.'/teachermanage/savesuccess/?ID='.$CourseID;       
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
     * @desc 教师订单管理
     */    
    public function OrderList(){
        $StudyOrderModule=new StudyOrderModule();
        switch (intval($_POST['Type'])){
            case 1 :
                $MysqlWhere="and RelationID={$_SESSION['UserID']}";
                break;
            case 2:
                $MysqlWhere="and RelationID={$_SESSION['UserID']} and `Status`=2";
                break;
            case 3:
                $MysqlWhere="and RelationID={$_SESSION['UserID']} and `Status`=3";
                break;
            case 4:
                $MysqlWhere="and RelationID={$_SESSION['UserID']} and `Status`=1";
                break;
            default:
                $MysqlWhere="and RelationID={$_SESSION['UserID']}";
                break;
        }
        $Rscount = $StudyOrderModule->GetListsNum($MysqlWhere);
        $Page=intval($_GET['Page']);
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
            $StudyTeacherCourseModule=new StudyTeacherCourseModule();
            //订单状态
            $Status=$StudyOrderModule->Status;
            //服务类型
            $CourseType=$StudyTeacherCourseModule->CourseType;
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
                $CourseInfo=$StudyTeacherCourseModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and CourseID={$Val['ProductID']}");
                $OrderList[$Key]['OrderTrainSubject']=$CourseType[$CourseInfo['CourseType']];
                $OrderList[$Key]['OrderCoursePrice']=$Val['CoursePrice'];
                $OrderList[$Key]['OrderCourseNum']=$Val['CoursePackage'];
                $CourseImg=json_decode($CourseInfo['ImagesJson'],true);
                if(!empty($CourseImg)){
                    if(strpos($CourseImg[$CourseInfo['CoverImageKey']],"http://")===false){
                        $CourseImg[$CourseInfo['CoverImageKey']]=ImageURLP4.$CourseImg[$CourseInfo['CoverImageKey']];
                    }
                    $OrderList[$Key]['OrderImg']=$CourseImg[$CourseInfo['CoverImageKey']];
                }else{
                    $OrderList[$Key]['OrderImg']=ImageURLP2.'/Uploads/Study/Service/service.jpg';
                }
                $OrderList[$Key]['OrderServiceName']=$CourseInfo['CourseName'];
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

    /**
     * @desc 成功案例保存
     */
    public function SuccessCaseAdd(){
        if($_POST['CaseID']){
            $CaseID = intval($_POST['CaseID']);
            unset($_POST['CaseID']);
        }
        $DoType = trim($_POST['SubmitType']);
        unset($_POST['SubmitType']);
        if($DoType == 'ImmediatelyShow'){ //已展示的立即展示
            $Data['Status'] = '2';
            $Url = '/teachermanage/successcase/?S=2'; //保存成功页面,可预览
        }
        elseif($DoType == 'DraftRelease'){ //草稿箱的立即发布
            $Data['Status'] = '2';
            $Url = '/teachermanage/successcase/?S=2'; //保存成功页面,可预览
        }
        elseif($DoType == 'DraftSave'){ //草稿箱的保存
            $Data['Status'] = '1'; //保存成功页面,可预览
            $Url = '/teachermanage/successcase/?S=1'; //保存成功页面,可预览
        }
        elseif($DoType == 'SubmitAudit'){
            $Data['Status'] = '2'; //保存成功页面,可预览
            $Url = '/teachermanage/successcase/?S=2'; //保存成功页面,可预览
        }
        elseif($DoType == 'SaveView'){
            $Data['Status'] = '1'; //保存成功页面,可预览
            $Url = '/teachermanage/successcase/?S=1'; //保存成功页面,可预览
        }
        //判断是否审核通过，没通过的存入草稿箱
        $HasNotice=false;
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);  
        if($Data['Status'] == '2' && $UserInfo['IdentityState']!=2){
            $HasNotice=true;
            $Data['Status'] = '1';
            $Url = '/teachermanage/successcase?S=1';
        }        
        $Data['UserID'] = intval($_SESSION['UserID']); //教师ID
        $Data['StudentName'] = trim($_POST['StudentName']); //学生姓名
        $Data['CourseType'] = trim($_POST['TrainSubject']); //申请季
        $Data['School'] = trim($_POST['AttendSchool']); //录取学校，逗号隔开，可多个
        $Data['TrainingType'] = trim($_POST['TrainCategory']);//培训类别
        $Data['BeforeTrainingResult'] = trim($_POST['TrainPreScore']); //培训前成绩
        $Data['AfterTrainingResult'] = trim($_POST['TrainHouScore']); //培训后成绩
        //学生头像
        $StudentImage = $_POST['PicPortraits'][0]['Img'];
        if(strpos($StudentImage,'data:image/jpeg;base64')!==false){
            $ImageFullUrl='/up/'.date('Y').'/'.date('md').'/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
            SendToImgServ($ImageFullUrl,str_replace('data:image/jpeg;base64,','',$StudentImage));
            $Data['StudentImage']=$ImageFullUrl;
        }
        else{
            $Data['StudentImage']=str_replace(array(LImageURL,ImageURLP2,ImageURLP4,ImageURLP6,ImageURLP8),"",$StudentImage);
        }
        //成绩证书图片
        $ResultImages=$_POST['PicScore'];
        if(!empty($ResultImages)){
            $NewResultImage = array();
            foreach($ResultImages as $key=>$val){
                if(strpos($val,'data:image/jpeg;base64')!==false || strpos($val,'data:image/png;base64')!==false || strpos($val,'data:image/jpg;base64')!==false){
                    $ImageFullUrl='/up/'.date('Y').'/'.date('md').'/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                    SendToImgServ($ImageFullUrl,str_replace('data:image/jpeg;base64,','',$val));
                    $NewResultImage[$key] = $ImageFullUrl;
                }
                else{
                    $NewResultImage[$key] = str_replace(array(LImageURL,ImageURLP2,ImageURLP4,ImageURLP6,ImageURLP8),"",$val);
                }
            }
            $Data['ResultImages']=json_encode($NewResultImage);
        }
        $Data['StudentFeedBack']=trim($_POST['StudentSurvey']);
        $Data['CaseDescription']=trim($_POST['CaseDescription']);
        $CaseModule = new StudyTeacherCaseModule();
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
     * @desc  教师成功案例列表
     */
    public function SuccessCaseList(){
        $Status = $_POST['CaseColumn']?$_POST['CaseColumn']:2;
        $CaseModule = new StudyTeacherCaseModule();
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'] .' and Status = '.$Status;
        $Data = $CaseModule->GetInfoByWhere($MysqlWhere,true);
        if($Data){
            $Result = array();
            $StudyTeacherCourseModule=new StudyTeacherCourseModule();
            $CourseType=$StudyTeacherCourseModule->CourseType;
            foreach($Data as $key=>$val){
                $Result[$key]['CaseID'] = $val['CaseID'];
                $Result[$key]['StudentName'] = $val['StudentName'];
                $Result[$key]['PicPortraits'] = $val['StudentImage']?ImageURLP2.$val['StudentImage']:'';
                $Result[$key]['TrainSubject'] = $val['CourseType'];
                $Result[$key]['TrainCategory']=$val['TrainingType'];
                $Result[$key]['TrainPreScore'] = $val['BeforeTrainingResult'];
                $Result[$key]['TrainHouScore'] = $val['AfterTrainingResult'];
            }
            $json_result=array(
                'ResultCode'=>200,
                'CaseListData'=>$Result
            );            
        }else{
            $json_result=array(
                'ResultCode'=>100,
                'Message'=>'目前没有此栏目数据'
            );            
        }

        echo json_encode($json_result);
    }

    /**
     * @desc  教师成功案例详情
     */
    public function CaseDetails(){
        $CaseID=intval($_POST['CaseID']);
        if($CaseID){
            $CaseModule = new StudyTeacherCaseModule();
            $CaseInfo=$CaseModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and CaseID=$CaseID");
            if($CaseInfo){
                $Data['ResultCode']=200;
                $Data['CaseID']=$CaseID;
                $Data['PicPortraits']=$CaseInfo['StudentImage']?ImageURLP2.$CaseInfo['StudentImage']:'';
                $Data['StudentName']=$CaseInfo['StudentName'];
                $Data['TrainSubject']=$CaseInfo['CourseType'];
                $Data['TrainCategory']=$CaseInfo['TrainingType'];
                $Data['AttendSchool']=$CaseInfo['School'];
                $Data['TrainPreScore']=$CaseInfo['BeforeTrainingResult'];
                $Data['TrainHouScore']=$CaseInfo['AfterTrainingResult'];
                $Data['StudentSurvey']=$CaseInfo['StudentFeedBack'];
                $Data['CaseDescription']=$CaseInfo['CaseDescription'];
                $ResultImages=json_decode($CaseInfo['ResultImages']);
                if(!empty($ResultImages)){
                    foreach($ResultImages as $Key=>$Val){
                        $ResultImages[$Key]=ImageURLP4.$ResultImages[$Key];
                    }
                }
                $Data['PicScore']=$ResultImages;
                $json_result=$Data;
            }else{
                $json_result=array('ResultCode'=>100,'Message'=>'该案例不存在');
            }
        }else{
            $json_result=array('ResultCode'=>100,'Message'=>'该案例不存在');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  教师成功案例编辑
     */
    public function CaseEdit(){    
        $CaseID=intval($_POST['CaseID']);
        if($CaseID){
            $CaseModule = new StudyTeacherCaseModule();
            $CaseInfo=$CaseModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and CaseID=$CaseID");
            if($CaseInfo){
                $Data['ResultCode']=200;
                $Data['CaseID']=$CaseID;
                $Data['PicPortraits']=$CaseInfo['StudentImage']?ImageURLP2.$CaseInfo['StudentImage']:'';
                $Data['StudentName']=$CaseInfo['StudentName'];
                $Data['TrainSubject']=$CaseInfo['CourseType'];
                $Data['TrainCategory']=$CaseInfo['TrainingType'];
                $Data['AttendSchool']=$CaseInfo['School'];
                $Data['TrainPreScore']=$CaseInfo['BeforeTrainingResult'];
                $Data['TrainHouScore']=$CaseInfo['AfterTrainingResult'];
                $Data['StudentSurvey']=$CaseInfo['StudentFeedBack'];
                $Data['CaseDescription']=$CaseInfo['CaseDescription'];
                $ResultImages=json_decode($CaseInfo['ResultImages']);
                if(!empty($ResultImages)){
                    foreach($ResultImages as $Key=>$Val){
                        $ResultImages[$Key]=ImageURLP4.$ResultImages[$Key];
                    }
                }
                $Data['PicScore']=$ResultImages;
                $json_result=$Data;
            }else{
                $json_result=array('ResultCode'=>100,'Message'=>'该案例不存在');
            }
        }else{
            $json_result=array('ResultCode'=>100,'Message'=>'该案例不存在');
        }
        echo json_encode($json_result);        
    }
    
    /**
     * @desc  教师成功案例下架
     */
    public function CaseUnshelve(){
        $CaseID=intval($_POST['CaseID']);
        if($CaseID){
            $CaseModule = new StudyTeacherCaseModule();
            $CaseInfo=$CaseModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and CaseID=$CaseID");
            if($CaseInfo){
                $Data['Status']=1;
                $Result=$CaseModule->UpdateInfoByWhere($Data," UserID={$_SESSION['UserID']} and CaseID=$CaseID");
                if($Result!==false){
                    $json_result=array('ResultCode'=>200,'Message'=>'下架成功');
                }else{
                    $json_result=array('ResultCode'=>101,'Message'=>'下架失败');
                }
            }else{
                $json_result=array('ResultCode'=>100,'Message'=>'该案例不存在');
            }
        }else{
            $json_result=array('ResultCode'=>100,'Message'=>'该案例不存在');
        }
        echo json_encode($json_result); 
    }    
    
     /**
     * @desc  教师成功案例展示
     */
    public function CaseAdded(){
        $CaseID=intval($_POST['CaseID']);
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);        
        if($UserInfo['IdentityState']!=2){
            $json_result=array('ResultCode'=>101,'Message'=>'设置展示失败，您的身份还未审核通过');
        }else{
            if($CaseID){
                $CaseModule = new StudyTeacherCaseModule();
                $CaseInfo=$CaseModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and CaseID=$CaseID");
                if($CaseInfo){
                    $Data['Status']=2;
                    $Result=$CaseModule->UpdateInfoByWhere($Data,"UserID={$_SESSION['UserID']} and CaseID=$CaseID");
                    if($Result!==false){
                        $json_result=array('ResultCode'=>200,'Message'=>'设置展示成功');
                    }else{
                        $json_result=array('ResultCode'=>101,'Message'=>'设置展示失败');
                    }
                }else{
                    $json_result=array('ResultCode'=>100,'Message'=>'该案例不存在');
                }
            }else{
                $json_result=array('ResultCode'=>100,'Message'=>'该案例不存在');
            }
        }
        echo json_encode($json_result); 
    }  
    
     /**
     * @desc  教师个人信息设置--上传头像
     */
    public function TeacherMyInfoUpImg(){
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
     * @desc  教师个人信息设置--基础资料
     */
    public function TeacherMyInfoIndex1(){
        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
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
            $Data['IdentityState'] = 0;
            $TeacherInfoData['ServiceDeclaration'] = trim($_POST['ServiceManifesto']);
            foreach ($_POST['MyTag'] as $key=>$value){
                if ($value['tag']!=''){
                    $tags[] = $value['tag'];
                }
            }
            $TeacherInfoData['Tags'] = json_encode($tags,JSON_UNESCAPED_UNICODE);
            $UpdateUserInfo = $UserInfoModule->UpdateInfoByWhere($Data,' UserID = '.$_SESSION['UserID']);
            if ($UpdateUserInfo === false){
                $json_result=array('ResultCode'=>101,'Message'=>'更新个人信息失败');
                echo json_encode($json_result);exit;
            }
            if($StudyTeacherInfoModule->GetInfoByWhere('and UserID='.$_SESSION['UserID'])){
                $UpdateConsultantInfo = $StudyTeacherInfoModule->UpdateInfoByWhere($TeacherInfoData,' UserID = '.$_SESSION['UserID']);
            }else{
                $TeacherInfoData['UserID']=$_SESSION['UserID'];
                $UpdateConsultantInfo = $StudyTeacherInfoModule->InsertInfo($TeacherInfoData);
            }
            if ($UpdateConsultantInfo === false){
                $json_result=array('ResultCode'=>102,'Message'=>'更新顾问基本信息失败');
                echo json_encode($json_result);exit;
            }
            if ($UpdateConsultantInfo!==false && $UpdateUserInfo!==false){
                $json_result=array('ResultCode'=>200,'Message'=>'更新成功');
            }else{
                $json_result=array('ResultCode'=>201,'Message'=>'更新失败');
            }
            echo json_encode($json_result);exit;
        }
    }
    
    /**
     * @desc  教师个人信息设置--背景资料
     */
    public function TeacherMyInfoIndex2(){
        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
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
            $Data['PastExperience'] = json_encode($PastExperience,JSON_UNESCAPED_UNICODE);
            $Date['IdentityState'] = 0;
            $UpdateUserInfo =$MemberUserInfoModule->UpdateInfoByWhere($Date,' UserID = '.$_SESSION['UserID']);
            if ($UpdateUserInfo === false){
                $json_result=array('ResultCode'=>101,'Message'=>'更新审核状态失败');
                echo json_encode($json_result);exit;
            }
            $UpdateInfo = $StudyTeacherInfoModule ->UpdateInfoByWhere($Data,' UserID = '.$_SESSION['UserID']);
            if ($UpdateInfo!==false){
                $json_result=array('ResultCode'=>200,'Message'=>'更新成功');
            }else{
                $json_result=array('ResultCode'=>201,'Message'=>'更新失败');
            }
            echo json_encode($json_result);exit;
        }
    }
    
    /**
     * @desc  教师个人信息设置--身份验证
     */
    public function TeacherMyInfoIndex3(){
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
            if ($UpdateInfo!==false){
                $json_result=array('ResultCode'=>200,'Message'=>'更新成功');
            }else{
                $json_result=array('ResultCode'=>201,'Message'=>'更新失败');
            }
            echo json_encode($json_result);exit;
        }
    }

    
}
