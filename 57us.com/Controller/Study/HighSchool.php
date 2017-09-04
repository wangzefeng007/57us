<?php
/**
 * @desc  高中
 * Class HighSchool
 */
class HighSchool {
    public function __construct(){
    }


    /**
     * @desc 院校库高中列表
     */
    public function Index(){
        $SearchKeyWords=$_GET['K'];
        if ($_POST) {
            $this->GetLists();
        }
        $StudyHighSchoolModule = new StudyHighSchoolModule();
        $MysqlWhere='';
        $SearchKeyWords=trim($_GET['K']);
        if ($SearchKeyWords != '') {
            $MysqlWhere .= " and (HighSchoolName like '%$SearchKeyWords%' or HighSchoolNameEng like '%$SearchKeyWords%')";
        }        
        $MysqlWhere.= ' order by HighSchoolID ASC';
        $Page = intval($_GET['p']) < 1 ? 1 : intval($_GET['p']); // 页码 可能是空
        $PageSize = 10;
        $Rscount = $StudyHighSchoolModule->GetListsNum($MysqlWhere);
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
            $Lists = $StudyHighSchoolModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['HighSchoolName'];
                $Data['Data'][$key]['StudyID'] = $value['HighSchoolID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Location'];
                $Data['Data'][$key]['StudySAT'] = $value['SAT'];
                $Data['Data'][$key]['StudyAP'] = $value['AP'];
                $Data['Data'][$key]['StudyAnnualCost'] = $value['Cost'];
                $Data['Data'][$key]['StudyAccommodationMode'] = $value['Stay'];
                $Data['Data'][$key]['StudyImg'] = $value['Icon'];
                $Data['Data'][$key]['StudyUrl'] = "/highschool/".$value['HighSchoolID'].'.html';
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,2);
            $ShowPage = $ClassPage->showpage();
        }else{
            //搜索无数据，返回6所热门高中院校
            $MysqlWhere =' and HotRecommend = 1 ';
            $Lists = $StudyHighSchoolModule->GetLists($MysqlWhere,0,6);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['HighSchoolName'];
                $Data['Data'][$key]['StudyID'] = $value['HighSchoolID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Location'];
                $Data['Data'][$key]['StudySAT'] = $value['SAT'];
                $Data['Data'][$key]['StudyAP'] = $value['AP'];
                $Data['Data'][$key]['StudyAnnualCost'] = $value['Cost'];
                $Data['Data'][$key]['StudyAccommodationMode'] = $value['Stay'];
                $Data['Data'][$key]['StudyImg'] = $value['Icon'];
                $Data['Data'][$key]['StudyUrl'] = "/highschool/".$value['HighSchoolID'].'.html';
            }
        }

        $TblStudyAbroadModule = new TblStudyAbroadModule();
        //右侧最新资讯
        $TblStudyAbroad = $TblStudyAbroadModule->GetLists(' order by AddTime DESC',0,5);
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_school_right');
        $TagNav='highschool';
        $Title='美国高中留学_美国中学留学_美国中学排名_美国高中排名 - 57美国网';
        $Keywords='美国高中留学,美国中学留学,美国中学排名,美国高中排名,美国私立高中,美国公立高中,高中留学学校,美国最好的高中';
        $Description='57美国网中学频道，聚集了美国高中院校信息介绍，包括美国高中学校地域分布、美国高中学校排名、费用及学校类型等信息，帮助您快速选出适合自己的高中留学院校。';            
        include template ('HighSchoolLists');
    }
    /**
     * @desc 院校库高中详情页面
     */
    public function Details(){
        $StudyHighSchoolAp = new StudyHighSchoolApModule();
        $ImagesModule = new StudyHighSchoolImagesModule();
        $StudyHighSchoolModule = new StudyHighSchoolModule();
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $HighSchoolID = $_GET['HighSchoolID'];
        $HighSchoolInfo = $StudyHighSchoolModule->GetInfoByKeyID($HighSchoolID);
        $APContent = json_decode($HighSchoolInfo['APContent'],true);
        $SchoolAp = $StudyHighSchoolAp->GetLists(' order by APID ASC',0,40);
        foreach ($SchoolAp as $key=>$value){
            foreach ($APContent as $k=>$v){
                if ( $value['APID']==$v){
                    $SchoolAp[$key]['AP']=1;
                }
            }
        }
        //重组AP课程
        foreach ($SchoolAp as $key=>$value){
        for ( $i=0;$i<10;$i++){
            if ($key%14==$i){
                $SchoolAps[$i]['list'][] = $value;
            }
        }
        }
        //右侧最新资讯
        $TblStudyAbroad = $TblStudyAbroadModule->GetLists(' order by AddTime DESC',0,5);
        //附近学校
        $NearbySchool = $StudyHighSchoolModule->GetLists(' and HighSchoolID <> '.$HighSchoolID.' and Region = \''.$HighSchoolInfo['Region'] .'\'',0,4);
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_school_right');
        $Images = $ImagesModule->GetInfoByWhere(' and SchoolID = '.$HighSchoolID,true);
        $TagNav='highschool';
        $Title="美国{$HighSchoolInfo['HighSchoolName']}_{$HighSchoolInfo['HighSchoolName']}留学_申请_学费_入学条件 - 57美国网";
        $Keywords="美国{$HighSchoolInfo['HighSchoolName']},{$HighSchoolInfo['HighSchoolName']}怎样,{$HighSchoolInfo['HighSchoolName']}留学,{$HighSchoolInfo['HighSchoolName']}申请,{$HighSchoolInfo['HighSchoolName']}学费,{$HighSchoolInfo['HighSchoolName']}入学条件,{$HighSchoolInfo['HighSchoolName']}录取条件, {$HighSchoolInfo['HighSchoolName']}入学要求。";
        $Description="57美国网为赴美学子提供{$HighSchoolInfo['HighSchoolName']}院校详细介绍、招生信息、录取条件、留学费用、入学要求、校园生活等留学相关信息。并提供详细的{$HighSchoolInfo['HighSchoolName']}课程列表，帮助赴美学子选择适合自己的高中院校。";        
        include template ('HighSchoolDetails');
    }
    /**
     * 列表接口
     */
    public function GetLists(){
        $StudyHighSchoolModule = new StudyHighSchoolModule();
        if (!$_POST) {
            $Data['ResultCode'] = 100;
            EchoResult($Data);
        }
        $Keyword = trim($_POST['Keyword']);
        $Intention = trim($_POST['Intention']);
        $MysqlWhere = '';
        if ($_POST) {
            $MysqlWhere .= $this->GetMysqlWhere($Intention);
            $Sort = trim($_POST['Sort']);
            if ($Sort=='Default'){
                $MysqlWhere .=' order by HighSchoolID ASC';
            }
        }
        $Page = intval($_POST['Page']) < 1 ? 1 : intval($_POST['Page']); // 页码 可能是空
        $PageSize = 10;
        $Rscount = $StudyHighSchoolModule->GetListsNum($MysqlWhere);
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
            $Lists = $StudyHighSchoolModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['HighSchoolName'];
                $Data['Data'][$key]['StudyID'] = $value['HighSchoolID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Location'];
                $Data['Data'][$key]['StudySAT'] = $value['SAT'];
                $Data['Data'][$key]['StudyAP'] = $value['AP'];
                $Data['Data'][$key]['StudyAnnualCost'] = $value['Cost'];
                $Data['Data'][$key]['StudyAccommodationMode'] = $value['Stay'];
                $Data['Data'][$key]['StudyImg'] = $value['Icon'];
                $Data['Data'][$key]['StudyUrl'] = "/highschool/".$value['HighSchoolID'].'.html';
            }
            MultiPage($Data, 5);
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 200;
            }
        }else{
            //搜索无数据，返回6所热门高中院校
            $MysqlWhere =' and HotRecommend = 1 ';
            $Lists = $StudyHighSchoolModule->GetLists($MysqlWhere,0,6);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['HighSchoolName'];
                $Data['Data'][$key]['StudyID'] = $value['HighSchoolID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Location'];
                $Data['Data'][$key]['StudySAT'] = $value['SAT'];
                $Data['Data'][$key]['StudyAP'] = $value['AP'];
                $Data['Data'][$key]['StudyAnnualCost'] = $value['Cost'];
                $Data['Data'][$key]['StudyAccommodationMode'] = $value['Stay'];
                $Data['Data'][$key]['StudyImg'] = $value['Icon'];
                $Data['Data'][$key]['StudyUrl'] = "/highschool/".$value['HighSchoolID'].'.html';
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
     * 高中条件
     */
    public function GetMysqlWhere($Intention = ''){
        if ($Intention=='CourseLists'){
            $MysqlWhere ='';
            $Keyword = trim($_POST['Keyword']); // 搜索关键字
            $AP = $_POST['AP']; //AP数量
            if($AP[0]!='All'){
                $MysqlWhere.=' and (';
                foreach($AP as $val){
                    if ($val=='0-10'){
                        $MysqlWhere .='(AP<= 10) or ';
                    }elseif ($val=='10-15'){
                        $MysqlWhere .='(AP >= 10 and AP <= 15) or ';
                    }elseif ($val=='15-20'){
                        $MysqlWhere .='(AP >= 15 and AP <= 20) or ';
                    }elseif ($val=='20-All'){
                        $MysqlWhere .='(AP >= 20) or ';
                    }
                }
                $MysqlWhere=rtrim($MysqlWhere,' or ').')';
            }
            $AnnualCost = $_POST['AnnualCost'];//年总费用
            if($AnnualCost[0]!='All'){
                $MysqlWhere.=' and (';
                foreach($AnnualCost as $val){
                    if ($val=='0-30000'){
                        $MysqlWhere .='(Cost<= 30000) or ';
                    }elseif ($val=='30000-40000'){
                        $MysqlWhere .='(Cost >= 30000 and Cost <= 40000) or ';
                    }elseif ($val=='40000-50000'){
                        $MysqlWhere .='(Cost >= 40000 and Cost <= 50000) or ';
                    }elseif ($val=='50000-All'){
                        $MysqlWhere .='(Cost >= 50000) or ';
                    }
                }
                $MysqlWhere=rtrim($MysqlWhere,' or ').')';
            }
            $AccommodationMode = $_POST['AccommodationMode'];//住宿方式
            if($AccommodationMode[0]!='All'){
                $MysqlWhere.=' and (';
                foreach($AccommodationMode as $val){
                    if ($val ==1){
                        $Stay='寄宿家庭';
                       
                    }elseif ($val ==2){
                        $Stay='学校宿舍';
                    }elseif($val ==3){
                        $Stay='两者都提供';
                    }
                    $MysqlWhere .="Stay ='$Stay' or ";
                }
                $MysqlWhere=rtrim($MysqlWhere,' or ').')';
            }
           
            $Location = $_POST['Location'];
            if($Location[0]!='All'){
                $Location=implode(',', $Location);
                $MysqlWhere .=" and Province in ($Location)";
            }
            
            $Sort = trim($_POST['Sort']);
            $Page = trim($_POST['Page']);

            if ($Keyword != '') {
                $MysqlWhere .= " and (HighSchoolName like '%$Keyword%' or HighSchoolNameEng like '%$Keyword%')";
            }
            if ($Sort =='APAsce'){
                $MysqlWhere .=' order by AP ASC';
            }elseif ($Sort =='APDown'){
                $MysqlWhere .=' order by AP DESC';
            }elseif($Sort =='ExpensesAsce'){
            $MysqlWhere .=' order by Cost ASC';
            }elseif ($Sort =='ExpensesDown'){
            $MysqlWhere .=' order by Cost DESC';
            }

            return $MysqlWhere;
        }
    }
      
}
