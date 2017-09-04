<?php
class Teacher {

    public function __construct(){
    }
    
    //老师列表
    public function Lists(){
        //右侧广告
        $StudyTeacherInfoModule=new StudyTeacherInfoModule();
        $StudyRightADLists=NewsGetAdInfo('study_teacher_right');    
        $TagNav='teacher';
        //分页查询开始-------------------------------------------------
        $MysqlWhere ='';
        //关键字
        $SearchKeyWords=trim($_GET['K']);    
        if($SearchKeyWords!=''){
            $MysqlWhere.=" and b.NickName like '%$SearchKeyWords%'";
        }
        $Rscount = $StudyTeacherInfoModule->SelectTeacherMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
        $Page=intval($_GET['p'])?intval($_GET['p']):0;
        if ($Page < 1) {
            $Page = 1;
        }
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
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }else{
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
            }
        }
        $Title="留学培训老师_留学教师_美国留学教师_著名留学培训教师- 57美国网";
        $Keywords="留学教师,留学培训教师,留学培训老师,美国留学教师,著名留学培训教师";
        $Description="57美国网找教师频道，聚集海内外知名留学培训老师，打造精品留学名师团队，发布最新名师留学讲堂及公开课，致力为美国留学考生提供最专业的留学培训课程服务。";          
        include template('TeacherLists');
    }
    
    //老师详情
    public function Detail(){
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_teacher_right');
        $UserID=intval($_GET['ID']);
        $CID=intval($_GET['CID']);
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($UserID);
        $StudyTeacherInfoModule=new StudyTeacherInfoModule();
        $TeacherInfo=$StudyTeacherInfoModule->GetInfoByWhere("and UserID={$UserID}");
        $StudyTeacherCourseModule=new StudyTeacherCourseModule();
        $CourseList=$StudyTeacherCourseModule->GetInfoByWhere("and UserID=$UserID and `Status`=3",true);
        $StudyTeacherCaseModule=new StudyTeacherCaseModule();
        $CaseList=$StudyTeacherCaseModule->GetInfoByWhere("and UserID=$UserID and `Status`=2",true);
        $Title="{$UserInfo['NickName']}美国留学教师 - 57美国网";
        $Keywords="{$UserInfo['NickName']}美国留学教师";
        $Description="57美国网留学顾问—{$UserInfo['NickName']}，".mb_substr($TeacherInfo['Introduction'], 0,100,'utf-8').'…';
        include template('TeacherDetail');
    }    
    
    //课程列表
    public function CourseLists(){
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_course_right');        
        $MysqlWhere =' and a.`Status`=3';
        //关键字
        $SearchKeyWords=trim($_GET['K']);
        if($SearchKeyWords!=''){
            $MysqlWhere.=" and a.CourseName like '%$SearchKeyWords%'";
        }        
        //分页查询开始-------------------------------------------------
        $Rscount = $StudyTeacherCourseModule->SelectCourseMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
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
            $Data['Data'] = $StudyTeacherCourseModule->SelectCourseMemberInfo($MysqlWhere." limit $Offset,{$Data['PageSize']}");
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
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }else{
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
            }
        }
        //获取最新资讯
        $TblStudyAbroadCategoryModule=new TblStudyAbroadCategoryModule();
        $CategoryList=$TblStudyAbroadCategoryModule->GetInfoByWhere("and MATCH (CategoryIDS) AGAINST ('1053' IN BOOLEAN MODE)",true);
        $CatgegoryStr='';
        foreach($CategoryList as $arr){
            $CatgegoryStr.=$arr['CategoryID'].',';
        }
        $CategoryStr=rtrim($CatgegoryStr,',');
        $TblStudyAbroadModule=new TblStudyAbroadModule();
        $NewsList=$TblStudyAbroadModule->GetLists("and CategoryID in ($CategoryStr) order by AddTime desc",0, 5);        
        $TagNav='course';
        $Title="留学课程_留学培训课程_美国留学培训_美国留学考试- 57美国网";
        $Keywords="留学课程,留学培训课程,美国留学培训,美国留学考试,美国留学考试培训,留学考试培训,留学考试";
        $Description=" 57美国网留学课程频道，由来自名校名师提供的美国留学培训课程服务,包括雅思考试培训、托福考试培训、SAT考试培训、GRE考试培训、GMAT考试培训、SSAT考试培训及PTE考试培训等出国留学考试课程培训。";        
        include template('TeacherCourseLists');
    }
    
    //课程详情
    public function CoursesDetail(){
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_course_right');        
        $CourseID=intval($_GET['ID']);
        //课程信息
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
        $CourseInfo=$StudyTeacherCourseModule->GetInfoByKeyID($CourseID);
        if(!$CourseInfo || ($CourseInfo['UserID']!=$_SESSION['UserID'] && $CourseInfo['Status']!=3)){
            alertandgotopage("不存在该课程",WEB_STUDY_URL.'/teacher_course/');
        }
        //添加浏览记录
        $Type=2;
        MemberService::AddBrowsingHistory($CourseID,$Type);
        //培训科目
        $CourseType=$StudyTeacherCourseModule->CourseType[$CourseInfo['CourseType']];
        //上课方式
        $TeachType=$StudyTeacherCourseModule->TeachType[$CourseInfo['TeachType']];
        //班级规模
        $ClassSize=$StudyTeacherCourseModule->ClassSize[$CourseInfo['ClassSize']];
        //课时选项
        $CoursePackage=json_decode($CourseInfo['CoursePackage'],true);
        sort($CoursePackage);
        //用户基本信息
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($CourseInfo['UserID']);
        //顾问信息
        $StudyTeacherInfoModule=new StudyTeacherInfoModule();
        $TeacherInfo=$StudyTeacherInfoModule->GetInfoByWhere("and UserID={$CourseInfo['UserID']}");
        //其他课程
        $CourseList=$StudyTeacherCourseModule->GetInfoByWhere("and UserID={$CourseInfo['UserID']} and CourseID<>$CourseID and `Status`=3",true);   
        //推荐课程
        $RecommendCourseList=$StudyTeacherCourseModule->GetInfoByWhere("and CourseID<>$CourseID and `Status`=3 and CourseType={$CourseInfo['CourseType']} limit 0,3",true);
        $TagNav='course';
        $Title="{$CourseInfo['CourseName']}_{$UserInfo['NickName']}留学培训老师- 57美国网";
        $Keywords="{$CourseInfo['CourseName']},{$UserInfo['NickName']}留学培训老师";
        $Description="57美国网留学培训—{$UserInfo['NickName']}提供：{$CourseInfo['CourseName']}留学培训，".mb_substr($CourseInfo['CourseDescription'], 0,100,'utf-8').'…';       
        include template('TeacherCourseDetail');
    }

    /*
     * @desc  获取教师地区
     */
    public function GetCity(){
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        $MemberUserInfoModule = new MemberUserInfoModule();
        $Info = $MemberUserInfoModule->TeacherRemoveDuplicate('City');
        echo json_encode($Info);
    }
}
