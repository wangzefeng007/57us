<?php
Class Study{

    public function __construct(){
    }

    /**
     * @desc 首页
     */
    public function Index(){
        //弹出广告
        setcookie('tanchuad',1,time()+3600*24,'/','www.57us.com');
        setcookie('tanchuad',1,time()+3600*24,'/','study.57us.com');
        $TagNav = 'index';
        //首页轮播大图
        $BannerList=NewsGetAdInfo('study_index_banner');
        //首页精选服务
        $ServiceList=NewsGetAdInfo('study_index_service');
        //首页精选
        $CourseList=NewsGetAdInfo('study_index_course');
        //成功案例
        $StudyConsultantCaseModule=new StudyConsultantCaseModule();
        $ConsultantCaseList=$StudyConsultantCaseModule->GetLists("and `Status`=2 and R1=1 order by S1 asc",0,10);
        //首页底部横条广告
        $BottomBannerList=NewsGetAdInfo('study_index_bottom_banner');
        //最新资讯
        $TblStudyAbroadCategoryModule=new TblStudyAbroadCategoryModule();
        $CategoryList=$TblStudyAbroadCategoryModule->GetInfoByWhere("and MATCH (CategoryIDS) AGAINST ('1031' IN BOOLEAN MODE)",true);
        $CatgegoryStr='';
        foreach($CategoryList as $arr){
            $CatgegoryStr.=$arr['CategoryID'].',';
        }
        $CategoryStr=rtrim($CatgegoryStr,',');
        $TblStudyAbroadModule=new TblStudyAbroadModule();
        $NewsList=$TblStudyAbroadModule->GetLists("and CategoryID in ($CategoryStr) order by AddTime desc",0, 5);

        //友情链接
        $TblLinksModule=new TblLinksModule();
        $LinksList=$TblLinksModule->GetInfoByWhere("and Type=3 order by Sort desc",true);

        //热门学校
        $StudyCollegeModule=new StudyCollegeModule();
        $SchoolList=$StudyCollegeModule->GetLists("and HotRecommend=1", 0, 6);
        //底部热门学校
        $StudyHighSchoolModule=new StudyHighSchoolModule();
        //热门高中
        $FootHighList=$StudyHighSchoolModule->GetLists("and HotRecommend=1",0,8);
        //热门本科
        $FootCollegeList=$StudyCollegeModule->GetLists("and HotRecommend=1",0,8);
        //热门研究生院校
        $FootGrduateList=$StudyCollegeModule->GetLists("and HotRecommend=1 and Majors is not null",0,8);
        $Title='美国留学_美国游学_美国高中留学_美国留学中介_美国留学申请 - 57美国留学服务平台';
        $Keywords='美国留学中介,美国留学费用,美国留学条件,美国留学签证,美国研究生留学,美国留学,美国游学,美国高中留学,美国本科留学,美国留学申请,高中生美国留学,美国留学网,美国留学机构,美国留学攻略,美国留学资讯,美国大学排名,美国留学考试,出国留学,留学中介,留学网';
        $Description='57美国网留学平台，专注美国留学申请及考试培训，聚集了美国留学申请（高中、本科、硕士）、美国游学、美国留学签证办理、美国留学考试培训、美国大学排名等全方位的留学信息及资深留学顾问在线指导及服务。';
        include template('StudyIndex');
    }
    /**
     * @desc 首页全站搜索
     */
    public function Search(){
        $TagNav = 'index';
        $SearchKeyWords = $_GET['K'];
        if ($SearchKeyWords==''){
            alertandgotopage('请输入您要搜索的内容！',WEB_STUDY_URL);
        }
        $StudyConsultantServiceModule = new StudyConsultantServiceModule();
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
        $StudyHighSchoolModule = new StudyHighSchoolModule();
        $StudyCollegeModule = new StudyCollegeModule();
        $ServiceWhere = " and `Status` = 3 and ServiceName like '%$SearchKeyWords%'"; //搜索服务
        $CourseWhere =" and `Status` = 3 and a.CourseName like '%$SearchKeyWords%'"; //搜索课程
        $StudytourWhere = " and Title like '%$SearchKeyWords%'"; //搜索游学
        $ConsultantWhere =" and b.NickName like '%$SearchKeyWords%'"; //搜索顾问
        $TeacherWhere =" and b.NickName like '%$SearchKeyWords%'"; //搜索教师
        $HighSchoolWhere = " and (HighSchoolName like '%$SearchKeyWords%' or HighSchoolNameEng like '%$SearchKeyWords%')"; //搜索高中
        $CollegeWhere = " and (CollegeName like '%$SearchKeyWords%' or CollegeNameEng like '%$SearchKeyWords%')"; //搜索本科
        $GrduateWhere = " and (CollegeName like '%$SearchKeyWords%' or CollegeNameEng like '%$SearchKeyWords%') and Interests  is not null order by CollegeID ASC"; //搜索研究生
        $ServiceRscount = $StudyConsultantServiceModule->SelectServiceMemberInfo($ServiceWhere,"count(b.UserID) as Num")[0];//统计服务个数
        $CourseRscount = $StudyTeacherCourseModule->SelectCourseMemberInfo($CourseWhere,"count(b.UserID) as Num")[0];//统计课程个数
        $StudytourRscount = $StudyYoosureModule->GetListsNum($StudytourWhere);//统计游学个数
        $ConsultantRscount = $StudyConsultantInfoModule->SelectConsultantMemberInfo($ConsultantWhere," count(b.UserID) as Num")[0];//统计顾问个数
        $TeacherRscount = $StudyTeacherInfoModule->SelectTeacherMemberInfo($TeacherWhere,"count(b.UserID) as Num")[0];//统计教师个数
        $HighSchoolRscount = $StudyHighSchoolModule->GetListsNum($HighSchoolWhere);//统计高中个数
        $CollegeRscount = $StudyCollegeModule->GetListsNum($CollegeWhere);//统计本科个数
        $GrduateRscount = $StudyCollegeModule->GetListsNum($GrduateWhere);//统计研究生个数
        if (!$ServiceRscount['Num'] && !$CourseRscount['Num'] && !$StudytourRscount['Num'] && !$ConsultantRscount['Num'] && !$TeacherRscount['Num'] && !$HighSchoolRscount['Num'] && !$CollegeRscount['Num'] && !$GrduateRscount['Num']) {
            $Count = 1;//   判断所有类别搜索都为0
        }
        //搜索服务列表
        if ($ServiceRscount['Num']){
            $ConsultantServiceList = $StudyConsultantServiceModule->SelectServiceMemberInfo($ServiceWhere." limit 0,3");
        }else{
            $ConsultantServiceList = $StudyConsultantServiceModule->SelectServiceMemberInfo(' and `Status`=3  ORDER BY RAND() LIMIT 0,3');
        }
        foreach($ConsultantServiceList as $Key => $Value){
            $ServiceList[$Key]['Study_name']=$Value['ServiceName'];
            $ServiceList[$Key]['StudyID']=$Value['ServiceID'];
            $ServiceList[$Key]['StudyLevel']=$Value['TargetLevel']?$StudyConsultantServiceModule->TargetLevel[$Value['TargetLevel']]:'';
            $ServiceList[$Key]['StudyServiceType']=$StudyConsultantServiceModule->ServiceType[$Value['ServiceType']];
            $ServiceList[$Key]['StudyServiceRegion']=$Value['City'];
            $ServiceList[$Key]['StudyPicre']=$Value['SalePrice'];
            $ImagesJson = json_decode($Value['ImagesJson'],true);
            $ServiceList[$Key]['StudyImg']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($Value['ImagesJson'],true)[$Value['CoverImageKey']]):(ImageURL.'/img/study/defaultService3.0.jpg');
            $ServiceList[$Key]['StudyUrl']=WEB_STUDY_URL.'/consultant_service/'.$Value['ServiceID'].'.html';
            $TagStr="";
            $TagArr=json_decode($Value['ServiceTags'],true);
            if(!empty($TagArr)){
                foreach($TagArr as $Tag){
                    $TagStr.="<span>{$Tag['ServiceTag']}</span>";
                }
            }
            $ServiceList[$Key]['StudyService']=$TagStr;
            $ServiceList[$Key]['StudyDepict']=$Value['ServiceDescription'];
        }
        //搜索课程列表

        if ($CourseRscount['Num']){
            $CourseLists = $StudyTeacherCourseModule->SelectCourseMemberInfo($CourseWhere." limit 0,3");
        }else{
            $CourseLists = $StudyTeacherCourseModule->SelectCourseMemberInfo(' and `Status` = 3 ORDER BY RAND() LIMIT 0,3');
        }
        foreach($CourseLists as $Key => $Val){
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
        //搜索游学列表
        $StudyYoosureImageModule = new StudyYoosureImageModule();
         $StudytourRscount = $StudyYoosureModule->GetListsNum($StudytourWhere);
         if ($StudytourRscount['Num']){
             $StudyYoosureList = $StudyYoosureModule->GetLists($StudytourWhere,0,3);
         }else{
             $StudyYoosureList = $StudyYoosureModule->GetInfoByWhere(' ORDER BY RAND() LIMIT 3',true);
         }
         foreach ($StudyYoosureList as $Key=>$Value){
             $ApplyTime = json_decode($Value['ApplyTime'],true);
             $StudyYoosureList[$Key]['ApplyTime'] = $ApplyTime[0];//报名截止时间
             $StudyYoosureList[$Key]['OriginalPrice'] = intval($Value['OriginalPrice']);
             $StudyYoosureList[$Key]['Price'] = intval($Value['Price']);
             $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$Value['YoosureID'].' and IsDefault = 1');
             if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                 $StudyYoosureList[$Key]['Image'] = LImageURL.$YoosureImage['Image'];
             }else{
                 $StudyYoosureList[$Key]['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
             }
         }
        //搜索顾问列表
        $ConsultantRscount = $StudyConsultantInfoModule->SelectConsultantMemberInfo($ConsultantWhere," count(b.UserID) as Num")[0];
        if ($ConsultantRscount['Num']){
            $ConsultantList = $StudyConsultantInfoModule->SelectConsultantMemberInfo($ConsultantWhere." limit 0,3");
        }else{
            $ConsultantList = $StudyConsultantInfoModule->SelectConsultantMemberInfo(' ORDER BY RAND() LIMIT 0,3');
        }
        foreach($ConsultantList as $Key => $Value){
            $ConsultantList[$Key]['Study_name']=$Value['NickName'];
            $ConsultantList[$Key]['StudyID']=$Value['UserID'];
            $ConsultantList[$Key]['StudyExperience']=$Value['WorkingAge'];
            $ConsultantList[$Key]['StudyServiceRegion']=$Value['City'];
            $ConsultantList[$Key]['StudySex']=$Value['Sex'];
            if($Value['Avatar']){
                $ConsultantList[$Key]['StudyImg']=(strpos($Value['Avatar'],"http://")==false)?LImageURL.$Value['Avatar']:$Value['Avatar'];
            }elseif($Value['Avatar']==''){
                $ConsultantList[$Key]['StudyImg']= ImageURL.'/img/common/default.png';
            }
            $ConsultantList[$Key]['StudyUrl']=WEB_STUDY_URL.'/consultant/'.$Value['UserID'].'.html';
            $TagStr="";
            $TagArr=json_decode($Value['Tags'],true);
            if(!empty($TagArr)){
                foreach($TagArr as $Tag){
                    $TagStr.="<span>$Tag</span>";
                }
            }
            $ConsultantList[$Key]['StudyTag']=$TagStr;
            $ConsultantList[$Key]['StudyDepict']=  mb_substr($Value['Introduction'],0,60,'utf-8');
        }
        //搜索教师列表
        $TeacherRscount = $StudyTeacherInfoModule->SelectTeacherMemberInfo($TeacherWhere,"count(b.UserID) as Num")[0];
        if ($TeacherRscount['Num']){
            $TeacherList = $StudyTeacherInfoModule->SelectTeacherMemberInfo($TeacherWhere." limit 0,3");
        }else{
            $TeacherList = $StudyTeacherInfoModule->SelectTeacherMemberInfo(' ORDER BY RAND() LIMIT 0,3');
        }
        foreach($TeacherList as $Key => $Value){
            $TeacherList[$Key]['Study_name']=$Value['NickName'];
            $TeacherList[$Key]['StudyID']=$Value['UserID'];
            $TeacherList[$Key]['StudyExperience']=$Value['WorkingAge'];
            $TeacherList[$Key]['StudyServiceRegion']=$Value['City'];
            $TeacherList[$Key]['StudySex']=$Value['Sex'];
            if($Value['Avatar']){
                $TeacherList[$Key]['StudyImg']=(strpos($Value['Avatar'],"http://")===false)?LImageURL.$Value['Avatar']:$Value['Avatar'];
            }
            else{
                $TeacherList[$Key]['StudyImg']= ImageURL.'/img/study/man3.0.png';
            }
            $TeacherList[$Key]['StudyUrl']=WEB_STUDY_URL.'/teacher/'.$Value['UserID'].'.html';
            $TagStr="";
            $TagArr=json_decode($Value['Tags'],true);
            if(!empty($TagArr)){
                foreach($TagArr as $Tag){
                    $TagStr.="<span>$Tag</span>";
                }
            }
            $TeacherList[$Key]['StudyTag']=$TagStr;
            $TeacherList[$Key]['StudyDepict']=  _substr($Value['Introduction'], 60);
        }
        //搜索高中列表
        $HighSchoolRscount = $StudyHighSchoolModule->GetListsNum($HighSchoolWhere);
        if ($HighSchoolRscount['Num']){
            $HighSchoolList = $StudyHighSchoolModule->GetLists($HighSchoolWhere, 0,3);
        }else{
            $HighSchoolList = $StudyHighSchoolModule->GetInfoByWhere(' ORDER BY RAND() LIMIT 3',true);
        }
        foreach ($HighSchoolList as $key=>$value){
            $HighSchoolList[$key]['Study_name'] = $value['HighSchoolName'];
            $HighSchoolList[$key]['StudyID'] = $value['HighSchoolID'];
            $HighSchoolList[$key]['StudyLocation'] = $value['Location'];
            $HighSchoolList[$key]['StudySAT'] = $value['SAT'];
            $HighSchoolList[$key]['StudyAP'] = $value['AP'];
            $HighSchoolList[$key]['StudyAnnualCost'] = $value['Cost'];
            $HighSchoolList[$key]['StudyAccommodationMode'] = $value['Stay'];
            $HighSchoolList[$key]['StudyImg'] = $value['Icon'];
            $HighSchoolList[$key]['StudyUrl'] = "/highschool/".$value['HighSchoolID'].'.html';
        }
        //搜索本科列表
        $CollegeRscount = $StudyCollegeModule->GetListsNum($CollegeWhere);
        if ($CollegeRscount['Num']){
            $CollegeList = $StudyCollegeModule->GetLists($CollegeWhere, 0,3);
        }else{
            $CollegeList = $StudyCollegeModule->GetInfoByWhere(' ORDER BY RAND() LIMIT 3',true);
        }
        foreach ($CollegeList as $key=>$value){
            $CollegeList[$key]['StudyID'] = $value['CollegeID'];
            $CollegeList[$key]['StudyLocation'] = $value['Region'] .'  '. $value['Seat'];
            $CollegeList[$key]['StudySAT'] = $value['SATACT'];
            $CollegeList[$key]['StudySchooRanking'] = $value['Ranking'];
            $CollegeList[$key]['StudyAnnualCost'] = $value['TotalTuition'];
            $CollegeList[$key]['StudyAcceptanceRate'] = $value['AcceptanceRate'];
            $CollegeList[$key]['StudyTOEFL'] = $value['TOEFL'];
            $CollegeList[$key]['StudyImg'] = $value['LogoUrl'];
            $CollegeList[$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
            if ($value['SATACT'] == 'Not Required'){
                $CollegeList[$key]['StudySAT'] = '不需要';
            }else{
                $CollegeList[$key]['StudySAT'] = $value['SATMin'].'-'.$value['SATMax'];
            }
            if ($value['CollegeName']==''){
                $CollegeList[$key]['Study_name'] = $value['CollegeNameEng'];
            }else{
                $CollegeList[$key]['Study_name'] = $value['CollegeName'];
            }
        }
        //搜索研究生列表
        $StudyGrduateMajorModule = new StudyGrduateMajorModule();
        $GrduateRscount = $StudyCollegeModule->GetListsNum($GrduateWhere);
        if ($GrduateRscount['Num']){
            $GrduateList = $StudyCollegeModule->GetLists($CollegeWhere, 0,3);
        }else{
            $GrduateList = $StudyCollegeModule->GetInfoByWhere(' ORDER BY RAND() LIMIT 3',true);
        }
        foreach ($GrduateList as $key=>$value){
            $GrduateList[$key]['Study_name'] = $value['CollegeName'].'-研究生院';
            $GrduateList[$key]['Study_Englishname'] = $value['CollegeNameEng'];
            $GrduateList[$key]['StudyID'] = $value['CollegeID'];
            $GrduateList[$key]['StudyLocation'] = $value['Seat'].'   '.$value['Region'];
            $GrduateList[$key]['StudyImg'] = $value['LogoUrl'];
            $GrduateList[$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
            $GrduateMajor = $StudyGrduateMajorModule->GetInfoByWhere(' and ParentID>0 and CollegeID = '.$value['CollegeID']);
            $GrduateList[$key]['StudyMajor'] = '<span class=\"pl20\">'.$GrduateMajor['ProfessionName'].'</span>';
        }
        //右侧广告
        $StudySearchADLists=NewsGetAdInfo('study_search_right');
        //最新资讯
        $TblStudyAbroadModule=new TblStudyAbroadModule();
        $NewsList=$TblStudyAbroadModule->GetLists(" order by AddTime desc",0, 5);
        include template('StudySearch');
    }
}