<?php
/**
 * @desc  本科院校库
 * Class College
 */
class College{
    /**
     * @desc 院校库本科列表
     */
    public function Index(){
        $SearchKeyWords=$_GET['K'];
        if ($_POST) {
            $this->GetLists();
        }
        $Page = intval($_GET['p']) < 1 ? 1 : intval($_GET['p']); // 页码 可能是空
        $PageSize = 10;
        $MysqlWhere='';
        $SearchKeyWords=trim($_GET['K']);
        if($SearchKeyWords!=''){
            $MysqlWhere .= " and (CollegeName like '%$SearchKeyWords%' or CollegeNameEng like '%$SearchKeyWords%')";
        }              
        $MysqlWhere.=' order by CollegeID ASC';
        $CollegeModule = new StudyCollegeModule();
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
                if ($value['CollegeName']==''){
                    $Data['Data'][$key]['Study_name'] = $value['CollegeNameEng'];
                }else{
                    $Data['Data'][$key]['Study_name'] = $value['CollegeName'];
                }

                $Data['Data'][$key]['StudyID'] = $value['CollegeID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Region'] .'  '. $value['Seat'];
                if ($value['SATACT'] == 'Not Required'){
                    $Data['Data'][$key]['StudySAT'] = '不需要';
                }else{
                    $Data['Data'][$key]['StudySAT'] = $value['SATMin'].'-'.$value['SATMax'];
                }
                $Data['Data'][$key]['StudySAT'] = $value['SATACT'];
                $Data['Data'][$key]['StudySchooRanking'] = $value['Ranking'];
                $Data['Data'][$key]['StudyAnnualCost'] = $value['TotalTuition'];
                $Data['Data'][$key]['StudyAcceptanceRate'] = $value['AcceptanceRate'];
                $Data['Data'][$key]['StudyTOEFL'] = $value['TOEFL'];
                $Data['Data'][$key]['StudyImg'] = $value['LogoUrl'];
                $Data['Data'][$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }else{
            //搜索无数据，返回6所热门院校
            $MysqlWhere = ' and HotRecommend =1 ';
            $Lists = $CollegeModule->GetLists($MysqlWhere, 0, 6);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['CollegeName'];
                $Data['Data'][$key]['StudyID'] = $value['CollegeID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Region'] .'  '. $value['Seat'];
                if ($value['SATACT'] == 'Not Required'){
                    $Data['Data'][$key]['StudySAT'] = '不需要';
                }else{
                    $Data['Data'][$key]['StudySAT'] = $value['SATMin'].'-'.$value['SATMax'];
                }
                $Data['Data'][$key]['StudySAT'] = $value['SATACT'];
                $Data['Data'][$key]['StudySchooRanking'] = $value['Ranking'];
                $Data['Data'][$key]['StudyAnnualCost'] = $value['TotalTuition'];
                $Data['Data'][$key]['StudyAcceptanceRate'] = $value['AcceptanceRate'];
                $Data['Data'][$key]['StudyTOEFL'] = $value['TOEFL'];
                $Data['Data'][$key]['StudyImg'] = $value['LogoUrl'];
                $Data['Data'][$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
            }

        }
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        //右侧最新资讯
        $TblStudyAbroad = $TblStudyAbroadModule->GetLists(' order by AddTime DESC',0,5);
        //右侧广告
        $StudyRightADLists=NewsGetAdInfo('study_school_right');
        $TagNav='college';
        $Title='美国本科留学_美国本科排名_美国大学排名_美国著名大学 - 57美国网';
        $Keywords='美国本科留学,美国本科排名,美国大学排名,美国大学本科排名,美国著名大学,美国的大学排名,美国公立大学,美国私立大学,美国大学前100,	美国大学分布,美国大学本科,美国大学介绍';
        $Description='57美国网本科频道，聚集了美国大学院校信息介绍，包括美国大学学校地域分布、美国大学学校排名、费用及学校类型等信息，帮助您快速选出适合自己的大学留学院校及专业。';           
        include template ('CollegeSchoolLists');
    }

    /**
     * @desc 列表接口
     */
    public function GetLists(){
        $CollegeModule = new StudyCollegeModule();
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
                $MysqlWhere .=' order by CollegeID ASC';
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
                if ($value['CollegeName']==''){
                    $Data['Data'][$key]['Study_name'] = $value['CollegeNameEng'];
                }else{
                    $Data['Data'][$key]['Study_name'] = $value['CollegeName'];
                }

                $Data['Data'][$key]['StudyID'] = $value['CollegeID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Region'] .'  '. $value['Seat'];
                if ($value['SATACT'] == 'Not Required'){
                    $Data['Data'][$key]['StudySAT'] = '不需要';
                }else{
                    $Data['Data'][$key]['StudySAT'] = $value['SATMin'].'-'.$value['SATMax'];
                }
                $Data['Data'][$key]['StudySAT'] = $value['SATACT'];
                $Data['Data'][$key]['StudySchooRanking'] = $value['Ranking'];
                $Data['Data'][$key]['StudyAnnualCost'] = $value['TotalTuition'];
                $Data['Data'][$key]['StudyAcceptanceRate'] = $value['AcceptanceRate'];
                $Data['Data'][$key]['StudyTOEFL'] = $value['TOEFL'];
                $Data['Data'][$key]['StudyImg'] = $value['LogoUrl'];
                $Data['Data'][$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
            }
            MultiPage($Data, 5);
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 200;
            }

        }else{
            //搜索无数据，返回6所热门院校
            $MysqlWhere = ' and HotRecommend =1 ';
            $Lists = $CollegeModule->GetLists($MysqlWhere, 0, 6);
            foreach ($Lists as $key=>$value){
                $Data['Data'][$key]['Study_name'] = $value['CollegeName'];
                $Data['Data'][$key]['StudyID'] = $value['CollegeID'];
                $Data['Data'][$key]['StudyLocation'] = $value['Region'] .'  '. $value['Seat'];
                if ($value['SATACT'] == 'Not Required'){
                    $Data['Data'][$key]['StudySAT'] = '不需要';
                }else{
                    $Data['Data'][$key]['StudySAT'] = $value['SATMin'].'-'.$value['SATMax'];
                }
                $Data['Data'][$key]['StudySAT'] = $value['SATACT'];
                $Data['Data'][$key]['StudySchooRanking'] = $value['Ranking'];
                $Data['Data'][$key]['StudyAnnualCost'] = $value['TotalTuition'];
                $Data['Data'][$key]['StudyAcceptanceRate'] = $value['AcceptanceRate'];
                $Data['Data'][$key]['StudyTOEFL'] = $value['TOEFL'];
                $Data['Data'][$key]['StudyImg'] = $value['LogoUrl'];
                $Data['Data'][$key]['StudyUrl'] = "/college/".$value['CollegeID'].'.html';
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
     * @desc  本科条件
     * @param string $Intention
     * @return string
     */
    public function GetMysqlWhere($Intention = ''){
        if ($Intention=='CourseLists'){
            $MysqlWhere ='';
            $Keyword = trim($_POST['Keyword']); // 搜索关键字
            $SchoolType = $_POST['SchoolType']; //学校类型
            if($SchoolType[0]!='All'){
                $MysqlWhere.=' and (';
                foreach($SchoolType as $val){
                    if ($val ==1){
                        $collegename = '国家综合大学';
                    }elseif ($val ==2){
                        $collegename = '文理学院';
                    }elseif ($val ==3){
                        $collegename = '南部学院';
                    }elseif ($val ==4){
                        $collegename = '北部学院';
                    }
                    $MysqlWhere .="CollegeType = '$collegename' or ";                    
                }
                $MysqlWhere=rtrim($MysqlWhere,' or ').')';
            }

            $SchooRanking = $_POST['SchooRanking']; //学校排名
            if($SchooRanking[0]!='All'){
                $MysqlWhere.=' and (';
                foreach($SchooRanking as $val){
                    if ($val =='1-30'){
                        $MysqlWhere .='(Ranking < 31) or ';
                    }elseif ($val =='30-60'){
                        $MysqlWhere .='(Ranking < 61 and Ranking > 29) or ';
                    }elseif ($val =='60-100'){
                        $MysqlWhere .='(Ranking < 101 and Ranking > 59) or ';
                    }elseif ($val =='100-All'){
                        $MysqlWhere .='(Ranking >99) or ';
                    }                   
                }
                $MysqlWhere=rtrim($MysqlWhere,' or ').')';
            }
           
            $AnnualCost = $_POST['AnnualCost']; // 年总费用
            if($AnnualCost[0]!='All'){
                $MysqlWhere.=' and (';
                foreach($AnnualCost as $val){
                    if ($val=='0-30000'){
                        $MysqlWhere .='(TotalTuition<= 30000) or ';
                    }elseif ($val=='30000-40000'){
                        $MysqlWhere .='(TotalTuition >= 30000 and TotalTuition <= 40000) or ';
                    }elseif ($val=='40000-50000'){
                        $MysqlWhere .='(TotalTuition >= 40000 and TotalTuition <= 50000) or ';
                    }elseif ($val=='50000-All'){
                        $MysqlWhere .='(TotalTuition >= 50000) or ';
                    }                 
                }
                $MysqlWhere=rtrim($MysqlWhere,' or ').')';
            }             
            
            $TOEFL = $_POST['TOEFL'];//托福
            if($TOEFL[0]!='All'){
                $MysqlWhere.=' and (';
                foreach($TOEFL as $val){
                    if ($val=='0-80'){
                        $MysqlWhere .='(TOEFL<= 80) or ';
                    }elseif ($val=='80-90'){
                        $MysqlWhere .='(TOEFL >= 80 and TOEFL <= 90) or ';
                    }elseif ($val=='90-100'){
                        $MysqlWhere .='(TOEFL >= 90 and TOEFL <= 100) or ';
                    }elseif ($val=='100-All'){
                        $MysqlWhere .='(TOEFL >= 100) or ';
                    }                 
                }
                $MysqlWhere=rtrim($MysqlWhere,' or ').')';
            }   
            
            $SAT = $_POST['SAT'];//SAT
            if($SAT[0]!='All'){
                $MysqlWhere.=' and (';
                foreach($SAT as $val){
                    if ($val=='1501-1800'){
                        $MysqlWhere .='((SATMin >= 1501  and SATMin <= 1800) or (SATMax >= 1501  and SATMax <= 1800)) or ';
                    }elseif ($val=='1800-2000'){
                        $MysqlWhere .='((SATMin >= 1800  and SATMin <= 2000) or (SATMax >= 1501  and SATMax <= 1800)) or ';
                    }elseif ($val=='2000-All'){
                        $MysqlWhere .='((SATMin >= 2000) or (SATMax >= 2000)) or ';
                    }                 
                }
                $MysqlWhere=rtrim($MysqlWhere,' or ').')';
            }               
            
            $Location = $_POST['Location'];//地理位置
            if($Location[0]!='All'){
                $Location=implode(',', $Location);
                $MysqlWhere .=" and Province in ($Location)";
            }
            $Sort = trim($_POST['Sort']);//排序
            $Page = trim($_POST['Page']); //页数
            if ($Keyword != '') {
                $MysqlWhere .= " and (CollegeName like '%$Keyword%' or CollegeNameEng like '%$Keyword%')";
            }

            if ($Sort =='APAsce'){
                $MysqlWhere .=' order by GPA ASC';
            }elseif ($Sort =='APDown'){
                $MysqlWhere .=' order by GPA DESC';
            }elseif ($Sort =='ExpensesAsce'){
                $MysqlWhere .=' order by TotalTuition ASC';
            }elseif ($Sort =='ExpensesDown'){
                $MysqlWhere .=' order by TotalTuition DESC';
            }elseif ($Sort =='SuccessRateAsce'){
                $MysqlWhere .=' order by AcceptanceRate ASC';
            }elseif ($Sort =='SuccessRateDown'){
                $MysqlWhere .=' order by AcceptanceRate DESC';
            }
            return $MysqlWhere;
        }
    }
}
