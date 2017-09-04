<?php
/**
 * @desc  研究生
 * Class Graduate
 */
class GraduateSchool {
    public function __construct(){
    }
    /**
     * @desc 院校库研究生列表
     */
    public function Index(){
        $CollegeModule = new StudyCollegeModule();
        if ($_POST) {
            $this->GetLists();
        }
        $MysqlWhere='';
        $SearchKeyWords=trim($_GET['K']);
        if ($SearchKeyWords != '') {
            $MysqlWhere .= " and (CollegeName like '%$SearchKeyWords%' or CollegeNameEng like '%$SearchKeyWords%')";
        }        
        $MysqlWhere.= ' and Interests  is not null order by CollegeID ASC';
        $Page = intval($_GET['p']) < 1 ? 1 : intval($_GET['p']); // 页码 可能是空
        $PageSize = 10;
        $Rscount = $CollegeModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            if ($Data['Page'] < $Data['PageCount']) {
                $Data['NextPage'] = $Data['Page'] + 1;
            }
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount']) {
                $Page = $Data['PageCount'];
            }
            $Lists = $CollegeModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['CollegeName'].'-研究生院';
                $Data['Data'][$key]['Study_Englishname'] = $value['CollegeNameEng'];
                $Data['Data'][$key]['StudyID'] = $value['CollegeID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Seat'].'   '.$value['Region'];
                $Data['Data'][$key]['StudyImg'] = $value['LogoUrl'];
                $Data['Data'][$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
                $StudyGrduateMajorModule =new StudyGrduateMajorModule();
                $GrduateMajor = $StudyGrduateMajorModule->GetInfoByWhere(' and ParentID>0 and CollegeID = '.$value['CollegeID']);
                $Data['Data'][$key]['StudyMajor'] = '<span class=\"pl20\">'.'<a href="/majorgrad/'.$GrduateMajor['MajorID'].'.html">'.$GrduateMajor['ProfessionName'].'</a></span>';
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,2);
            $ShowPage = $ClassPage->showpage();
        }else{
            //搜索无数据，返回6所热门院校
            $MysqlWhere = '  and HotRecommend = 1 ';
            $Lists = $CollegeModule->GetLists($MysqlWhere, 0, 6);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['CollegeName'].'-研究生院';
                $Data['Data'][$key]['Study_Englishname'] = $value['CollegeNameEng'];
                $Data['Data'][$key]['StudyID'] = $value['CollegeID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Seat'].'   '.$value['Region'];
                $Data['Data'][$key]['StudyImg'] = $value['LogoUrl'];
                $Data['Data'][$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
                $StudyGrduateMajorModule =new StudyGrduateMajorModule();
                $GrduateMajor = $StudyGrduateMajorModule->GetInfoByWhere(' and ParentID>0 and CollegeID = '.$value['CollegeID']);
                $Data['Data'][$key]['StudyMajor'] = '<span class=\"pl20\">'.$GrduateMajor['ProfessionName'].'</span>';
            }
        }
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        //右侧最新资讯
        $TblStudyAbroad = $TblStudyAbroadModule->GetLists(' order by AddTime DESC',0,5);
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_school_right');
        $TagNav='graduateschool';
        $Title="美国研究生留学_美国大学研究生排名_美国硕士排名_美国研究生排名- 57美国网";
        $Keywords="美国研究生留学,美国研究生留学,美国硕士排名,美国研究生排名,美国大学研究生排名,美国商学院研究生排名,美国统计学研究生排名,美国建筑学研究生排名,美国金融硕士排名,美国经济学硕士排名";
        $Description="57美国网研究生频道，聚集了美国研究生院校信息介绍，包括美国研究生学校地域分布、美国大学学校排名、费用及学校类型等信息，帮助您快速选出适合自己的研究生留学院校及专业。";           
        include template ('GraduateSchoolLists');
    }
    /**
     * @desc 院校库研究生详情页面
     */
    public function Details(){
        $StudyCollegeImagesModule = new StudyCollegeImagesModule();
        $StudyCollegeMajorModule = new StudyCollegeMajorModule();
        $StudyCollegeModule = new StudyCollegeModule();
        $StudyGrduateMajorModule = new StudyGrduateMajorModule();
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $CollegeID = $_GET['CollegeID'];
        $CollegeInfo = $StudyCollegeModule->GetInfoByKeyID($CollegeID);
        $CollegeInfo['SchoolLife'] = json_decode($CollegeInfo['SchoolLife'],true);
        $CollegeInfo['Environment'] = json_decode($CollegeInfo['Environment'],true);
        $CollegeInfo['Safety'] = json_decode($CollegeInfo['Safety'],true);
        foreach ($CollegeInfo['Safety'] as $value){
            if ($value==''){
                $CollegeInfo['Safety']='';
            }
        }
        $CollegeInfo['Profession'] = json_decode($CollegeInfo['Profession'],true);
        //本科专业
        foreach ($CollegeInfo['Profession'] as $Key=>$List){
            $Direction[$Key] = $StudyCollegeMajorModule->GetInfoByKeyID($Key);
            foreach ($List as $value){
                $Specific[$Key][] = $StudyCollegeMajorModule->GetInfoByKeyID($value);
            }
        }
        //研究生专业和项目
        $ParentMajor = $StudyGrduateMajorModule->GetInfoByWhere(' and ParentID = 0 and CollegeID ='.$CollegeID,true);
        foreach ($ParentMajor as $key=>$value){
            $ParentMajors = $StudyGrduateMajorModule->GetInfoByWhere(' and ParentID = '.$value['MajorID'].' and CollegeID ='.$CollegeID,true);
            $ParentMajor[$key]['GrduateMajor'] = $ParentMajors;
        }
        //右侧最新资讯
        $TblStudyAbroad = $TblStudyAbroadModule->GetLists(' order by AddTime DESC',0,5);
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_school_right');
        //图片展示
        $CollegeImages = $StudyCollegeImagesModule->GetLists(' and CollegeID = '.$CollegeID);
        $TagNav='college';
        $Title="美国{$CollegeInfo['CollegeName']}_{$CollegeInfo['CollegeName']}留学_申请_学费_入学条件_大学排名 - 57美国网";
        $Keywords="美国{$CollegeInfo['CollegeName']},{$CollegeInfo['CollegeName']}怎样,{$CollegeInfo['CollegeName']}留学,{$CollegeInfo['CollegeName']}申请,{$CollegeInfo['CollegeName']}学费,{$CollegeInfo['CollegeName']}入学条件,{$CollegeInfo['CollegeName']}录取条件, {$CollegeInfo['CollegeName']}入学要求,{$CollegeInfo['CollegeName']}大学排名";
        $Description="57美国网为赴美学子提供{$CollegeInfo['CollegeName']}院校详细介绍、招生信息、录取条件、留学费用、入学要求、校园生活等留学相关信息。并提供详细的{$CollegeInfo['CollegeName']}专业列表，帮助赴美学子选择适合自己的留学专业。";
        include template ('GraduateSchoolDetails');
    }
    /**
     * @desc 院校库研究生专业详情页面
     */
    public function MajorGrad(){
        $TagNav='Graduate';
        $MajorID = $_GET['MajorID'];
        $StudyGrduateMajor = new StudyGrduateMajorModule();
        $StudyGrduateDetails = new StudyGrduateDetailsModule();
        $StudyCollegeModule = new StudyCollegeModule();
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_school_right');
        $GrduateDetails = $StudyGrduateDetails->GetInfoByWhere(' and MajorID = '.$MajorID);
        $GrduateMajor = $StudyGrduateMajor->GetInfoByKeyID($GrduateDetails['MajorID']);
        $Application = json_decode($GrduateDetails['Application'],true);
        $ParentMajor = $StudyGrduateMajor->GetInfoByKeyID($GrduateMajor['ParentID']);
        $Information = json_decode($GrduateDetails['Information'],true);
        $CollegeInfo = $StudyCollegeModule->GetInfoByKeyID($GrduateMajor['CollegeID']);
        if ($GrduateDetails['Type']>0){//项目详情
            foreach ($Application as $key=>$value){
                $Applicationkey[] = $key;
                $Applicationvalue[] = $value;
            }
            $References = json_decode($GrduateDetails['References'],true);
            foreach ($References as $key=>$value){
                $Referencekey[] =$key;
                $Referencevalue[] =$value;
            }
            include template ('GraduateProgram');
        }elseif ($GrduateDetails['Type']==0){//专业详情
            include template ('GraduateMajorGrad');
        }
    }
    /**
     * 列表接口
     */
    public function GetLists(){
        $StudyGrduateMajorModule =new StudyGrduateMajorModule();
        $CollegeModule = new StudyCollegeModule();
        if (!$_POST) {
            $Data['ResultCode'] = 100;
            EchoResult($Data);
        }
        $Keyword = trim($_POST['Keyword']);
        $Intention = trim($_POST['Intention']);
        $MysqlWhere = ' and Interests  is not null';
        if ($_POST) {
            $MysqlWhere .= $this->GetMysqlWhere($Intention);
            $Sort = trim($_POST['Sort']);
            if ($Sort=='Default'){
                $MysqlWhere .='  order by CollegeID ASC';
            }
        }
        $Page = intval($_POST['Page']) < 1 ? 1 : intval($_POST['Page']); // 页码 可能是空
        $PageSize = 10;
        $Rscount = $CollegeModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            if ($Data['Page'] < $Data['PageCount']) {
                $Data['NextPage'] = $Data['Page'] + 1;
            }
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount']) {
                $Page = $Data['PageCount'];
            }
            $Lists = $CollegeModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['CollegeName'].'-研究生院';
                $Data['Data'][$key]['Study_Englishname'] = $value['CollegeNameEng'];
                $Data['Data'][$key]['StudyID'] = $value['CollegeID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Seat'].'   '.$value['Region'];
                $Data['Data'][$key]['StudyImg'] = $value['LogoUrl'];
                $Data['Data'][$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
                $GrduateMajor = $StudyGrduateMajorModule->GetInfoByWhere(' and ParentID>0 and CollegeID = '.$value['CollegeID']);
                $Data['Data'][$key]['StudyMajor'] = '<span class=\"pl20\">'.'<a href="/majorgrad/'.$GrduateMajor['MajorID'].'.html" title="'.$GrduateMajor['ProfessionName'].'">'.$GrduateMajor['ProfessionName'].'</a></span>';
            }
            MultiPage($Data, 5);
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 200;
            }
        }else{
            //搜索无数据，返回6所热门院校
            $MysqlWhere = '  and HotRecommend = 1 ';
            $Lists = $CollegeModule->GetLists($MysqlWhere, 0, 6);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['CollegeName'].'-研究生院';
                $Data['Data'][$key]['Study_Englishname'] = $value['CollegeNameEng'];
                $Data['Data'][$key]['StudyID'] = $value['CollegeID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Seat'].'   '.$value['Region'];
                $Data['Data'][$key]['StudyImg'] = $value['LogoUrl'];
                $Data['Data'][$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
                $GrduateMajor = $StudyGrduateMajorModule->GetInfoByWhere(' and ParentID>0 and CollegeID = '.$value['CollegeID']);
                $Data['Data'][$key]['StudyMajor'] = '<span class=\"pl20\">'.$GrduateMajor['ProfessionName'].'</span>';
            }
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 101;
            }
        }
        unset($Lists);
        EchoResult($Data);
    }
    /**
     * 研究生条件
     */
    public function GetMysqlWhere($Intention = ''){
        if ($Intention=='CourseLists'){
            $MysqlWhere ='';
            $Keyword = trim($_POST['Keyword']); // 搜索关键字
            $ProfessionalEmphasis = trim($_POST['ProfessionalEmphasis']); //专业方向
            $SpecificDirection = $_POST['SpecificDirection'];//具体专业
            if ( $SpecificDirection[0]!='All'){
                $MysqlWhere .= ' and  MATCH(`Majors`) AGAINST (\'' .  implode(',',$SpecificDirection) . '\' IN BOOLEAN MODE)';
            }
            $Sort = trim($_POST['Sort']);//排序
            $Page = trim($_POST['Page']);
            if ($Keyword != '') {
                $MysqlWhere .= " and (CollegeName like '%$Keyword%' or CollegeNameEng like '%$Keyword%')";
            }
            if ( $ProfessionalEmphasis!='All'){
                $MysqlWhere .= ' and  MATCH(`Interests`) AGAINST (' . $ProfessionalEmphasis . ' IN BOOLEAN MODE)';
            }
            if ($Sort =='RankingAsce'){
                $MysqlWhere .=' order by Ranking ASC';
            }elseif ($Sort =='RankingDown'){
                $MysqlWhere .=' order by Ranking DESC';
            }
            return $MysqlWhere;
        }
    }
    /**
     * 生成专业静态json
     */
    public function Getjson(){
        $StudyGrduateKeywords = new StudyGrduateKeywordsModule();
        $Grduate = $StudyGrduateKeywords->GetInfoByWhere(' and ParentID = 0',true);
        foreach ($Grduate as $key=>$value){
            $Info = $StudyGrduateKeywords->GetInfoByKeyID($value['KeyID']);
            $Lists = $StudyGrduateKeywords->GetLists(' and ParentID='.$Info['KeyID']);
            foreach ($Lists as $Key=>$Value)
            {
                $AreaJson[$Key]['id'] = $Value['KeyID'];
                $AreaJson[$Key]['name'] = $Value['Keyword'];
            }
            $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
            file_put_contents(SYSTEM_ROOTPATH.'/Templates/Study/data/School/Graduate/'.$Info['KeyID'].'.json',$AreaString );
            unset($Info,$Lists,$Key,$Value,$AreaJson,$AreaString);
        }
    }
}
