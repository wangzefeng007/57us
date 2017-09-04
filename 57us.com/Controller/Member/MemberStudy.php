<?php

/**
 * @desc  会员中心留学模块
 * Class MemberStudy
 */

class MemberStudy
{
    public function __construct()
    {
        $this->Nav = 'MemberStudy';
    }
    /**
     * @desc 服务订单
     */
    public function ServiceOrderList(){
        $PreNav = 'order';
        $Nav = "serviceorder";
        $Title = '服务订单';
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
        $ServiceModule = new StudyConsultantServiceModule();
        $StudyOrderModule = new StudyOrderModule();
        $MemberUserInfoModule = new MemberUserInfoModule();
        $StatusInfo = $StudyOrderModule->Status;
        $ServiceType = $ServiceModule->ServiceType;
        $CourseType = $StudyTeacherCourseModule->CourseType;
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'];
        $Status=  intval($_GET['S']);
        if ($Status){
            $MysqlWhere .= ' and Status = '.$Status;
        }
        $Page = intval($_GET['page'])<1?1:intval($_GET['page']);
        $pageSize = 3;
        $Rscount = $StudyOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($pageSize ? $pageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $pageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $StudyOrderModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key=>$value){
                $Service = $ServiceModule->GetInfoByKeyID($value['ProductID']);
                $Data['Data'][$key]['ServiceType'] = $Service['ServiceType'];
                $Relation = $MemberUserInfoModule->GetInfoByUserID($value['RelationID']);
                if(strpos($Relation['Avatar'],'http://')!==false){
                    $Data['Data'][$key]['Avatar'] = $Relation['Avatar'];
                }else{
                    $Data['Data'][$key]['Avatar'] = LImageURL.$Relation['Avatar'];
                }
                $Data['Data'][$key]['RealName'] = $Relation['RealName'];
                $Data['Data'][$key]['TimeDown'] = $value['AddTime']+259200-time();
                $Data['Data'][$key]['endTime'] = date("Y-m-d H:i:s",$value['AddTime']+259200);
                if($value['OrderType']==1){
                    $Images = json_decode($Service['ImagesJson'], true);
                    if (strpos($Images[0], 'http://') !== false) {
                        $Data['Data'][$key]['Image'] = $Images[0];
                    } else {
                        if ($Images[0] != '') {
                            $Data['Data'][$key]['Image'] = ImageURLP2 . $Images[0];
                        } else {
                            $Data['Data'][$key]['Image'] = ImageURL . '/img/study/defaultService3.0.jpg';
                        }
                    }
                }
                //判断订单类型是教师课程
                if ($value['OrderType']==2){
                    $TeacherCourse = $StudyTeacherCourseModule->GetInfoByKeyID($value['ProductID']);
                    $Data['Data'][$key]['CourseType'] = $TeacherCourse['CourseType'];
                    $ImagesJson = json_decode($TeacherCourse['ImagesJson'],true);
                    $Data['Data'][$key]['Image']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($TeacherCourse['ImagesJson'],true)[$TeacherCourse['CoverImageKey']]):(ImageURL.'/img/study/defaultClass3.0.jpg');
                }
            }
            $ClassPage = new Page($Rscount['Num'], $pageSize,2);
            $ShowPage = $ClassPage->showpage();
        }
        include template('MemberStudyServiceOrderList');
    }

    /**
     * @desc  留学订单详情页
     */
    public function ServiceOrderDetail(){
        $PreNav = 'order';
        $Nav = "serviceorder";
        $OrderID=intval($_GET['ID']);
        if($OrderID){
            $StudyConsultantServiceModule = new StudyConsultantServiceModule();
            $StudyOrderModule=new StudyOrderModule();
            $StudyOrderConsultantModule = new StudyOrderConsultantModule();
            $MemberUserInfoModule= new MemberUserInfoModule();
            $OrderInfo=$StudyOrderModule->GetInfoByWhere("and OrderID=$OrderID and UserID={$_SESSION['UserID']} and OrderType=1");
            $ServiceInfo=$StudyConsultantServiceModule->GetInfoByWhere("and ServiceID={$OrderInfo['ProductID']}");
            $ServiceImg=json_decode($ServiceInfo['ImagesJson'],true);
            if(!empty($ServiceImg)){
                if(strpos($ServiceImg[$ServiceInfo['CoverImageKey']],"http://")===false){
                    $ServiceImg[$ServiceInfo['CoverImageKey']]=ImageURLP4.$ServiceImg[$ServiceInfo['CoverImageKey']];
                }
            }else{
                $ServiceImg[$ServiceInfo['CoverImageKey']]=ImageURLP2.'/Uploads/Study/Service/service.jpg';
            }
            $OrderConsultant = $StudyOrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID,true);
            $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
            $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
            $consultant = $MemberUserInfoModule->GetInfoByUserID($OrderInfo['RelationID']);
            $consultant['Avatar'] = LImageURL.$consultant['Avatar'];
            $endTime = $OrderInfo['AddTime']+259200-time();
            $TimeEnd =date("Y-m-d H:i:s",$OrderInfo['AddTime']+259200);
            //订单状态
            $Status=$StudyOrderModule->Status;
            //服务类型
            $ServiceType=$StudyConsultantServiceModule->ServiceType;
            //支付类型
            $PayType=$StudyOrderModule->PayType;
            $Title = $ServiceInfo['ServiceName'].'-订单详情';
            switch ($ServiceInfo['ServiceType']){
                case '1':
                    include template ( 'MemberStudyServiceOrderDetail' );//全程服务
                    break;
                case '2':
                    include template ( 'MemberStudyServiceOrderDetail2' );//申请学校
                    break;
                case '3':
                    include template ( 'MemberStudyServiceOrderDetail3' );//文书服务
                    break;
                case '4':
                    include template ( 'MemberStudyServiceOrderDetail4' );//定校选校
                    break;
                case '5':
                    include template ( 'MemberStudyServiceOrderDetail5' );//签证培训
                    break;
                case '6':
                    include template ( 'MemberStudyServiceOrderDetail6' );//材料翻译
                    break;
                case '7':
                    include template ( 'MemberStudyServiceOrderDetail7' );//背景提升
                    break;
            }
        }else{
            alertandback("不存在该订单");
        }
    }

    /**
     * @desc 游学订单
     */
    public function TourOrderList(){
        $PreNav = 'order';
        $Nav = "tourorder";
        $Title = '游学订单';
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $StudyYoosure  = new  StudyYoosureModule ();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $NStatus = $StudyYoosureOrderModule->NStatus;
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'];
        $Status=  intval($_GET['S']);
        if ($Status){
            $MysqlWhere .= ' and Status = '.$Status;
        }
        $Page = intval($_GET['page'])<1?1:intval($_GET['page']);
        $pageSize = 3;
        $Rscount = $StudyYoosureOrderModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($pageSize ? $pageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $pageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Data'] = $StudyYoosureOrderModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key=>$value){
                $StudyYoosureInfo = $StudyYoosure->GetInfoByKeyID($value['YoosureID']);
                if (strpos($StudyYoosureInfo['Image'],"http://")===false && $StudyYoosureInfo['Image']){
                    $Data['Data'][$key]['Image'] = LImageURL.$StudyYoosureInfo['Image'];
                }else{
                    $Data['Data'][$key]['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
            }
            $ClassPage = new Page($Rscount['Num'], $pageSize,2);
            $ShowPage = $ClassPage->showpage();
        }
        include template('MemberStudyTourOrderList');
    }

    /**
     * @desc  游学订单详情
     */
    public function TourOrderDetail(){
        $PreNav = 'order';
        $Nav = "tourorder";
        $OrderID=intval($_GET['ID']);
        if($OrderID){
            $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
            $MemberUserInfoModule= new MemberUserInfoModule();
            $StudyYoosure  = new  StudyYoosureModule ();
            $OrderInfo=$StudyYoosureOrderModule->GetInfoByWhere("and OrderID=$OrderID and UserID={$_SESSION['UserID']}");
            $UserInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
            if(strpos($UserInfo['Avatar'],'http://')!==false){
                $UserInfo['Avatar'] =$UserInfo['Avatar'];
            }else{
                $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
            }
            $StudyYoosureInfo = $StudyYoosure->GetInfoByKeyID($OrderInfo['YoosureID']);
            if (strpos($StudyYoosureInfo['Image'],"http://")===false && $StudyYoosureInfo['Image']){
                $OrderInfo['Image'] = LImageURL.$StudyYoosureInfo['Image'];
            }else{
                $OrderInfo['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
            }
            $OrderInfo['TravelerInformation'] = json_decode($OrderInfo['TravelerInformation'],true);
        }else{
            alertandback("不存在该订单");
        }
        $Title = $StudyYoosureInfo['Title'].'-订单详情';
        include template('MemberStudyTourOrderDetail');
    }

    /**
     * @desc  信息收集表
     */
    public function Information(){
        $Nav = "information";
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $QuestionnaireModule = new StudyConsultantQuestionnaireModule();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $StudyOrderModule = new StudyOrderModule();
        $Status = $QuestionnaireModule->Status;
        $Data = $OrderConsultantModule->GetInfoByWhere( ' and UserID = '.$_SESSION['UserID'].' and Type = 1 order by ID desc',true);
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']); //学生信息，学生头像
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        foreach ($Data as $key => $val) {
            $Data[$key]['OrderName'] = $val['OrderName'];
            //订单信息
            $OrderInfo = $StudyOrderModule->GetInfoByKeyID($val['OrderID']);
            //顾问会员信息
            $ConsultantUserInfo = $MemberUserInfoModule->GetInfoByUserID($OrderInfo['RelationID']);
            $Data[$key]['OrderName'] = $OrderInfo['OrderName'];
            $Data[$key]['RealName'] = $ConsultantUserInfo['RealName']?$ConsultantUserInfo['RealName']:$ConsultantUserInfo['NickName'];
            $Data[$key]['Avatar'] = LImageURL.$ConsultantUserInfo['Avatar']; //顾问头像
            $Questionnaire = $QuestionnaireModule->GetInfoByWhere(' and OrderID ='.$val['OrderID'].' order by ID desc',true);
            //调查表状态
            $FlowStatus = $OrderConsultantModule->Status;
            $Data[$key]['StatusName'] = $FlowStatus[$val['Status']];
            //判断是否定稿
            $Isok = $QuestionnaireModule->GetInfoByWhere(' and OrderID ='.$val['OrderID'] .' and Status = 2');
            if($Isok){
                $Data[$key]['Isok'] = 1;
            }
            else{
                $Data[$key]['Isok'] = 0;
            }
            $QuestionnaireCount = count($Questionnaire);
            $Data[$key]['QuestionnaireCount'] = $QuestionnaireCount;
            if($QuestionnaireCount > 5){
                foreach($Questionnaire as $k=>$v){
                    if($k<5){
                        $Data[$key]['Show'][] = $v;
                        unset($Questionnaire[$k]);
                    }
                }
                $Data[$key]['NoShow'] = $Questionnaire;
            }elseif ($QuestionnaireCount>0 && $QuestionnaireCount <= 5){
                $Data[$key]['Show'] = $Questionnaire;
                $Data[$key]['NoShow'] = '';
            }else{
                $Data[$key]['Show'] = '';
                $Data[$key]['NoShow'] = '';
            }
        }
        $Title = '信息收集表';
        include template('MemberStudyInformation');
    }

    /**
     * @desc 选校定校
     */
    public function ChooseSchool(){
        $Nav = "chooseschool";
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $ChooseSchoolModule = new StudyConsultantChooseSchoolModule();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $StudyOrderModule = new StudyOrderModule();
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'];
        $ChooseSchoolModule->Status;
        $Data = $OrderConsultantModule->GetInfoByWhere($MysqlWhere.' and Type = 2',true);
        $OrderList = $StudyOrderModule->GetInfoByWhere($MysqlWhere.' and Status>=2',true);
        $K=0;
        foreach ($OrderList as $key=>$value){
            $ChooseSchoolList = $ChooseSchoolModule->GetInfoByWhere(' and OrderID = '.$value['OrderID'].' and Status >0 order by AddTime desc',true);
            $consultant = $MemberUserInfoModule->GetInfoByUserID($value['RelationID']);
            foreach ($ChooseSchoolList as $Key=>$Val){
                if ($Val['Status']==1){
                    $List[$K]['Status'] = 1;
                }
            }
            $OrderConsultant = $OrderConsultantModule->GetInfoByWhere($MysqlWhere.' and OrderID = '.$value['OrderID'].' and Type = 2');
            if ($OrderConsultant){
                $List[$K]['Data'] = $ChooseSchoolList;
                $List[$K]['LastStatus'] = $OrderConsultant['Status'];
                $List[$K]['OrderID'] = $value['OrderID'];
                $List[$K]['OrderName'] = $value['OrderName'];
                $List[$K]['RealName'] =$consultant['RealName'];
                $List[$K]['Avatar'] = LImageURL.$consultant['Avatar'];
                $K++;
            }
            /*$FlowStatus = $OrderConsultantModule->Status;
            $List[$key]['StatusName'] = $FlowStatus[$OrderConsultant['Status']];*/
        }
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        $Title = '选校定校';
        include template('MemberStudyChooseSchool');
    }

    /**
     * @desc  文书材料
     */
    public function Document(){
        $Nav = "document";
        $DocumentModule = new StudyConsultantDocumentModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $StudyOrderModule = new StudyOrderModule();

        $T = $_GET['T']?intval($_GET['T']):1;
        $Data = $OrderConsultantModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID'].' and Type = 3',true);
        foreach($Data as $key => $val){
            //订单信息
            $OrderInfo = $StudyOrderModule->GetInfoByKeyID($val['OrderID']);
            //顾问会员信息
            $ConsultantUserInfo = $MemberUserInfoModule->GetInfoByUserID($OrderInfo['RelationID']);
            $Data[$key]['OrderName'] = $OrderInfo['OrderName'];
            $Data[$key]['RealName'] = $ConsultantUserInfo['RealName']?$ConsultantUserInfo['RealName']:$ConsultantUserInfo['NickName'];
            $Data[$key]['Avatar'] = LImageURL.$ConsultantUserInfo['Avatar'];
            $Document = $DocumentModule->GetInfoByWhere(' and OrderID ='.$val['OrderID'].' and Type = '.$T.' order by ID desc',true);
            //判断是否定稿
            $Isok = $DocumentModule->GetInfoByWhere(' and OrderID ='.$val['OrderID'].' and Type = '.$T .' and Status = 3');
            if($Isok){
                $Data[$key]['Isok'] = 1;
            }
            else{
                $Data[$key]['Isok'] = 0;
            }
            //文书上传数量
            $DocumentCount = count($Document);
            $Data[$key]['DocumentCount'] = $DocumentCount;
            if($DocumentCount > 5){
                foreach($Document as $k=>$v){
                    if($k<5){
                        $Data[$key]['Show'][] = $v;
                        unset($Document[$k]);
                    }
                }
                $Data[$key]['NoShow'] = $Document;
            }elseif ($DocumentCount>0 && $DocumentCount <= 5){
                $Data[$key]['Show'] = $Document;
                $Data[$key]['NoShow'] = '';
            }else{
                $Data[$key]['Show'] = '';
                $Data[$key]['NoShow'] = '';
            }
        }
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        $Title = '文书材料';
        include template('MemberStudyDocument');
    }

    /**
     * @desc 学校申请
     */
    public function SchoolApply(){
        $Nav = "schoolapply";
        $SchoolEnrollModule = new StudyConsultantSchoolEnrollModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $StudyOrderModule = new StudyOrderModule();
        $Status = $SchoolEnrollModule->Status;
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'];
        $OrderList = $StudyOrderModule->GetInfoByWhere($MysqlWhere.' and Status>=2',true);
        $K=0;
        foreach ($OrderList as $key=>$value){
            $sqlWhere = ' and OrderID = '.$value['OrderID'];
            $SchoolEnrollList = $SchoolEnrollModule->GetInfoByWhere($sqlWhere,true);
            $OrderConsultant = $OrderConsultantModule->GetInfoByWhere($MysqlWhere.' and OrderID = '.$value['OrderID'].' and Type = 4');
            if ($OrderConsultant){
                $consultant = $MemberUserInfoModule->GetInfoByUserID($value['RelationID']);
                foreach ($SchoolEnrollList as $val){
                    $ApplyData = json_decode($val['ApplyData'],true);
                    $EnrollData = json_decode($val['EnrollData'],true);
                    $ApplyCount = count($ApplyData);
                    $EnrollCount = count($EnrollData);
                    $List[$K]['Apply'] = $ApplyData;
                    $List[$K]['Enroll'] = $EnrollData;
                    $List[$K]['ApplyCount'] =$ApplyCount;
                    $List[$K]['EnrollCount'] =$EnrollCount;
                    $List[$K]['Status'] = $val['Status'];
                    $List[$K]['SchoolName'] = $val['SchoolName'];
                    $List[$K]['SpecialtyName'] = $val['SpecialtyName'];
                }
                $List[$K]['RealName'] =$consultant['RealName'];
                $List[$K]['Avatar'] = LImageURL.$consultant['Avatar'];
                $List[$K]['OrderID'] =$value['OrderID'];
                $List[$K]['OrderName'] =$value['OrderName'];
                $K++;
            }
        }
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        $Title = '学校申请';
        include template('MemberStudySchoolApply');
    }

    /**
     * @desc 签证申请
     */
    public function Visa(){
        $Nav = "visa";
        $VisaModule = new StudyConsultantTransactVisaModule();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $StudyOrderModule = new StudyOrderModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $Status = $VisaModule->Status;
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'];
        $OrderList = $StudyOrderModule->GetInfoByWhere($MysqlWhere.' and Status>=2',true);
        $K=0;
        foreach ($OrderList as $key=>$value){
            $VisaInfo = $VisaModule->GetInfoByWhere(' and OrderID = '.$value['OrderID'].' order by AddTime desc',true);
            $OrderConsultant =$OrderConsultantModule->GetInfoByWhere($MysqlWhere.' and OrderID = '.$value['OrderID'].' and Type = 5');
            if ($OrderConsultant){
                $consultant = $MemberUserInfoModule->GetInfoByUserID($value['RelationID']);
                $List[$K]['OrderConsultant'] = $OrderConsultant;
                $List[$K]['Data'] = $VisaInfo;
                $List[$K]['OrderID'] = $value['OrderID'];
                $List[$K]['OrderName'] = $value['OrderName'];
                $List[$K]['RealName'] = $consultant['RealName'];
                $List[$K]['Avatar'] = $consultant['Avatar'];
                $List[$K]['Avatar'] = LImageURL.$consultant['Avatar'];
                $K++;
            }
        }
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        $Title = '签证申请';
        include template('MemberStudyVisa');
    }

    /**
     * @desc 材料翻译
     */
    public function Translate(){
        $Nav = "translate";
        $TranslateModule = new StudyConsultantTranslateModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $StudyOrderModule = new StudyOrderModule();
        $Data = $OrderConsultantModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID'].' and Type = 6',true);
        foreach ($Data as $Key=>$val){
            $OrderInfo = $StudyOrderModule->GetInfoByKeyID($val['OrderID']);
            $consultant = $MemberUserInfoModule->GetInfoByUserID($val['RelationID']);
            $Translate = $TranslateModule->GetInfoByWhere(' and OrderID = '.$val['OrderID'].' order by ID desc',true);
            $Data[$Key]['OrderName'] = $OrderInfo['OrderName'];
            $Data[$Key]['RealName'] = $consultant['RealName'];
            if(strpos($consultant['Avatar'],'http://')!==false){
                $Data[$Key]['Avatar'] =$consultant['Avatar'];
            }else{
                $Data[$Key]['Avatar'] = LImageURL.$consultant['Avatar'];
            }
            $TranslateCount = count($Translate);
            $Data[$Key]['TranslateCount'] = $TranslateCount;
            if($TranslateCount > 5){
                foreach($Translate as $k=>$v){
                    if($k<5){
                        $Data[$Key]['Show'][] = $v;
                        unset($Translate[$k]);
                    }
                }
                $Data[$Key]['NoShow'] = $Translate;
            }elseif ($TranslateCount>0 && $TranslateCount <= 5){
                $Data[$Key]['Show'] = $Translate;
                $Data[$Key]['NoShow'] = '';
            }else{
                $Data[$Key]['Show'] = '';
                $Data[$Key]['NoShow'] = '';
            }
            //判断是否定稿
            $Isok = $TranslateModule->GetInfoByWhere(' and OrderID ='.$val['OrderID'].' and Status = 2');//Status=2 已确定
            if($Isok){
                $Data[$Key]['Isok'] = 1;
            }
            else{
                $Data[$Key]['Isok'] = 0;
            }
        }
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        $Title = '材料翻译';
        include template('MemberStudyTranslate');
    }

    /**
     * @desc 背景提升
     */
    public function Background(){
        $Nav = "background";
        $ServiceModule = new StudyConsultantServiceModule();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $StudyOrderModule = new StudyOrderModule();
        $ServiceType = $ServiceModule->ServiceType;
        $Status = $StudyOrderModule->Status;
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'];
        $OrderList = $StudyOrderModule->GetInfoByWhere($MysqlWhere.' and Status>=2 order by AddTime desc',true);
        $K=0;
        foreach ($OrderList as $key=>$value){
            $ServiceInfo = $ServiceModule->GetInfoByWhere(' and ServiceID ='.$value['ProductID'].' and ServiceType = 7');
            if($ServiceInfo){
                $consultant = $MemberUserInfoModule->GetInfoByUserID($value['RelationID']);
                $List[$K]['OrderNum'] =$value['OrderNum'];
                $List[$K]['AddTime'] =$value['AddTime'];
                $List[$K]['Money'] =$value['Money'];
                $List[$K]['OrderName'] =$value['OrderName'];
                $List[$K]['OrderID'] =$value['OrderID'];
                $List[$K]['Status'] =$value['Status'];
                $List[$K]['RealName'] =$consultant['RealName'];
                $List[$K]['ServiceType'] =$ServiceInfo['ServiceType'];
                $Images = json_decode($ServiceInfo['ImagesJson'],true);
                $List[$K]['Image'] = ($Images[0]!='')?(ImageURLP4.$Images[0]):(ImageURLP2.'/Uploads/Study/Service/service.jpg');
                $List[$K]['Avatar'] = LImageURL.$consultant['Avatar'];
            }
            $K++;
        }
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $UserInfo['Avatar'] = LImageURL.$UserInfo['Avatar'];
        $Title = '背景提升';
        include template('MemberStudyBackground');
    }

}
