<?php
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class TeacherAjax extends CommonController{

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
    * 获取教师列表 关联两张表 a-study_teacher_info  b-member_user_info
    */
    private function TeacherLists(){
        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
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
        $Rscount = $StudyTeacherInfoModule->SelectTeacherMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
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
            $Data['Data'] = $StudyTeacherInfoModule->SelectTeacherMemberInfo($MysqlWhere." limit $Offset,{$Data['PageSize']}");
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
            $TeacherList=array();
            foreach($Data['Data'] as $Key => $Val){
                $TeacherList[$Key]['Study_name']=$Val['NickName'];
                $TeacherList[$Key]['StudyID']=$Val['UserID'];
                $TeacherList[$Key]['StudyExperience']=$Val['WorkingAge'];
                $TeacherList[$Key]['StudyServiceRegion']=$Val['City'];
                $TeacherList[$Key]['StudySex']=$Val['Sex'];

                if($Val['Avatar']){
                    $TeacherList[$Key]['StudyImg']=(strpos($Val['Avatar'],"http://")===false)?LImageURL.$Val['Avatar']:$Val['Avatar'];
                }
                else{
                    $TeacherList[$Key]['StudyImg']= ImageURL.'/img/study/man3.0.png';
                }
                $TeacherList[$Key]['StudyUrl']=WEB_STUDY_URL.'/teacher/'.$Val['UserID'].'.html';
                $TagStr="";
                $TagArr=json_decode($Val['Tags'],true);
                if(!empty($TagArr)){
                    foreach($TagArr as $Tag){
                        $TagStr.="<span>$Tag</span>";
                    }
                }
                $TeacherList[$Key]['StudyTag']=$TagStr;
                $TeacherList[$Key]['StudyDepict']=  _substr($Val['Introduction'], 60);
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
                'Data'=>$TeacherList
            );        
        }else{
            if($Keyword!=''){
                $ResultCode=103;
            }else{
                $ResultCode=101;
            }
            $json_result=array('ResultCode'=>$ResultCode,'Message'=>'没有找到记录');
            $Data['Data']=$StudyTeacherInfoModule->SelectTeacherMemberInfo(' limit 0,15');
            if(!empty($Data['Data'])){
                $TeacherList=array();
                foreach($Data['Data'] as $Key => $Val){
                    $TeacherList[$Key]['Study_name']=$Val['NickName'];
                    $TeacherList[$Key]['StudyID']=$Val['UserID'];
                    $TeacherList[$Key]['StudyExperience']=$Val['WorkingAge'];
                    $TeacherList[$Key]['StudyServiceRegion']=$Val['City'];
                    $TeacherList[$Key]['StudySex']=$Val['Sex'];
                    $TeacherList[$Key]['StudyImg']=(strpos($Val['Avatar'],"http://")===false)?LImageURL.$Val['Avatar']:$Val['Avatar'];
                    $TeacherList[$Key]['StudyUrl']=WEB_STUDY_URL.'/teacher/'.$Val['UserID'].'.html';
                    $TagStr="";
                    $TagArr=json_decode($Val['Tags'],true);
                    if(!empty($TagArr)){
                        foreach($TagArr as $Tag){
                            $TagStr.="<span>$Tag</span>";
                        }
                    }
                    $TeacherList[$Key]['StudyTag']=$TagStr;
                    $TeacherList[$Key]['StudyDepict']=$Val['Introduction'];
                }
                $json_result['Data']=$TeacherList;
            }else{
                $json_result['Data']=array();
            }
        }
        echo json_encode($json_result);
    }    
    
    /*
    * 获取课程列表
    * 
    */
    private function CourseLists(){
        $StudyTeacherCourseModule=new StudyTeacherCourseModule();
        $MysqlWhere="and a.`Status`=3";
        //培训科目
        $CourseType=$_POST['TrainSubject'];
        if($CourseType[0]!='All'){
            $CourseType=implode(',',$CourseType);
            $MysqlWhere.=" and a.CourseType in ($CourseType)";
        }
        //上课方式
        $TeachType=$_POST['FormClass'];
        if($TeachType[0]!='All'){
            $TeachType=implode(',',$TeachType);
            $MysqlWhere.=" and a.TeachType in ($TeachType)";
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
            $MysqlWhere.=" and a.CourseName like '%$Keyword%'";
        }
        //排序
        $SortType=trim($_POST['Sort']);
        switch ($SortType){
            case 'PicerDown':
                $MysqlWhere.=" order by a.CoursePrice desc";
                break;
            case 'PicerAsce':
                $MysqlWhere.=" order by a.CoursePrice asc";
                break;
            case 'SalesDown':
                $MysqlWhere.=" order by a.SaleNum desc";
                break;
            case 'SalesAsce':
                $MysqlWhere.=" order by a.SaleNum asc";
                break;
        }
        //分页查询开始-------------------------------------------------
        $Rscount = $StudyTeacherCourseModule->SelectCourseMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
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
            $Data['Data'] = $StudyTeacherCourseModule->SelectCourseMemberInfo($MysqlWhere." limit $Offset,{$Data['PageSize']}");
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
                $CourseList[$Key]['Study_name']=$Val['CourseName'];
                $CourseList[$Key]['StudyID']=$Val['CourseID'];
                $CourseList[$Key]['StudyTrainSubject']=$StudyTeacherCourseModule->CourseType[$Val['CourseType']];
                $CourseList[$Key]['StudyFormClass']=$StudyTeacherCourseModule->TeachType[$Val['TeachType']];
                $CourseList[$Key]['StudyServiceRegion']=$Val['City'];
                $CourseList[$Key]['StudyPicre']=$Val['CoursePrice'];
                $ImagesJson = json_decode($Val['ImagesJson'],true);
                $CourseList[$Key]['StudyImg']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($Val['ImagesJson'],true)[$Val['CoverImageKey']]):(ImageURL.'/img/study/defaultClass3.0.jpg');
                $CourseList[$Key]['StudyUrl']=WEB_STUDY_URL.'/teacher_course/'.$Val['CourseID'].'.html';
                $TagStr="";
                $TagArr=json_decode($Val['CourseTags'],true);
                if(!empty($TagArr)){
                    foreach($TagArr as $Tag){
                        $TagStr.="<span>{$Tag['CourseTag']}</span>";
                    }
                }
                $CourseList[$Key]['StudyService']=$TagStr;
                $CourseList[$Key]['StudyDepict']=$Val['CourseDescription'];
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
                'Data'=>$CourseList
            );        
        }else{
            if($Keyword!=''){
                $ResultCode=103;
            }else{
                $ResultCode=101;
            }
            $json_result=array('ResultCode'=>$ResultCode,'Message'=>'没有找到记录');
            $Data['Data']=$StudyTeacherCourseModule->GetInfoByWhere('and `Status`=3 limit 0,6',true);
            if(!empty($Data['Data'])){
                $CourseList=array();
                foreach($Data['Data'] as $Key => $Val){
                    $CourseList[$Key]['Study_name']=$Val['CourseName'];
                    $CourseList[$Key]['StudyID']=$Val['CourseID'];
                    $CourseList[$Key]['StudyTrainSubject']=$StudyTeacherCourseModule->CourseType[$Val['CourseType']];
                    $CourseList[$Key]['StudyFormClass']=$StudyTeacherCourseModule->TeachType[$Val['TeachType']];
                    $CourseList[$Key]['StudyServiceRegion']=$Val['ServiceArea'];
                    $CourseList[$Key]['StudyPicre']=$Val['CoursePrice'];
                    $ImagesJson = json_decode($Val['ImagesJson'],true);
                    $CourseList[$Key]['StudyImg']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($Val['ImagesJson'],true)[$Val['CoverImageKey']]):(ImageURL.'/img/study/defaultClass3.0.jpg');
                    $CourseList[$Key]['StudyUrl']=WEB_STUDY_URL.'/teacher_course/'.$Val['CourseID'].'.html';
                    $TagStr="";
                    $TagArr=json_decode($Val['CourseTags'],true);
                    if(!empty($TagArr)){
                        foreach($TagArr as $Tag){
                            $TagStr.="<span>{$Tag['CourseTag']}</span>";
                        }
                    }
                    $CourseList[$Key]['StudyService']=$TagStr;
                    $CourseList[$Key]['StudyDepict']=$Val['CourseDescription'];
                }
                $json_result['Data']=$CourseList;
            }else{
                $json_result['Data']=array();
            }
        }
        echo json_encode($json_result);        
    }
}
