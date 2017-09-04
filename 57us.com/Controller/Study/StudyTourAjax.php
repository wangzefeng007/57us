<?php

/**
 * @desc 游学产品
 * Time: 15:07
 */
class StudyTourAjax
{

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
            echo  $json_result;
            exit;
        }
        $this->$Intention ();
    }

    /**
     * @desc 获取游学产品列表
     */
    public function StudyTour()
    {
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        $MysqlWhere = ' and Status = 1 ';
        // 游学主题
        $Theme = $_POST['Theme'];
        if ($Theme != '' && $Theme[0] != 'All') {
            $YoosureTitle ='';
            foreach ($Theme as $value){
                $YoosureTitle .= $value.',';
            }
            $YoosureTitle = substr($YoosureTitle, 0, -1);
            $MysqlWhere .= " and YoosureTitle in($YoosureTitle)";
        }

        // 适合人群
        $Crowd = $_POST['Crowd'];
        if ($Crowd != '' && $Crowd[0] != 'All') {
            $Crowds ='';
            foreach ($Crowd as $value){
                $Crowds .= $value.',';
            }
            $Crowds = substr($Crowds, 0, -1);
            $MysqlWhere .= " and Crowd in($Crowds)";
        }

        // 出行天数
        $Date = $_POST['Date'];
        if ($Date != '' && $Date[0] != 'All') {
            foreach ($Date as $value){
                $Dates .= $value;
            }
            if ($Dates=='0-10'){
                $MysqlWhere .= ' and Days > 0 and Days <10 ';
            }elseif ($Dates=='10-15'){
                $MysqlWhere .= ' and Days > 9 and Days <16 ';
            }elseif ($Dates=='15-All'){
                $MysqlWhere .= ' and Days > 15 ';
            }elseif(strstr($Dates,'0-10') && strstr($Dates,'10-15')&& !strstr($Dates,'15-All')){
                $MysqlWhere .= ' and Days > 0 and Days <16 ';
            }elseif (strstr($Dates,'0-10') && strstr($Dates,'15-All')&& !strstr($Dates,'10-15')){
                $MysqlWhere .= 'and (Days <10 or Days > 15) ';
            }elseif (strstr($Dates,'10-15') && strstr($Dates,'15-All')&& !strstr($Dates,'0-10')){
                $MysqlWhere .= ' and Days > 9 ';
            }elseif (strstr($Dates,'0-10') && strstr($Dates,'10-15')&& strstr($Dates,'15-All')){
                $MysqlWhere .=  '';
            }
        }
        // 出行地
        $StartCity = $_POST['StartCity'];
        if ($StartCity != '' && $StartCity[0] != 'All') {
            $DeparturePlace ='';
            foreach ($StartCity as $value){
                $DeparturePlace .= $value.',';
            }
            $DeparturePlace = substr($DeparturePlace, 0, -1);
            $MysqlWhere .= " and DeparturePlace in($DeparturePlace)";
        }
        // 搜索
        $Keyword = trim($_POST['Keyword']);
        if ($Keyword != '') {
            $MysqlWhere .= " and Title like '%$Keyword%'";
        }
        // 价格排序
        $Sort = trim($_POST['Sort']);
        switch ($Sort) {
            case 'Default':
                $MysqlWhere .= '';
                break;
            case 'PicerDown':
                $MysqlWhere .= ' order by Price desc';
                break;
            case 'PicerAsce':
                $MysqlWhere .= ' order by Price asc';
                break;
            default:
                break;
        }

        $Rscount = $StudyYoosureModule->GetListsNum($MysqlWhere);
        $page = intval($_POST['Page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 12;
        $Data = array();
        if ($Rscount['Num']) {
            if ($Keyword != '') {
                $Data['ResultCode'] = 102;
            } else {
                $Data['ResultCode'] = 200;
            }
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $StudyYoosureLists = $StudyYoosureModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($StudyYoosureLists as $Key => $Value) {
                $Data['Data'][$Key]['Study_ID'] = $Value['YoosureID'];
                $Data['Data'][$Key]['Study_Name'] = $Value['Title'];
                $Data['Data'][$Key]['Study_Recommend'] = $Value['R1'];
                $ApplyTime = json_decode($Value['ApplyTime'],true);
                $Data['Data'][$Key]['Study_Date'] = $ApplyTime[0];//报名截止时间
                $Data['Data'][$Key]['Study_OriginalPrice'] = intval($Value['OriginalPrice']);
                $Data['Data'][$Key]['Study_Picre'] = intval($Value['Price']);
                $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$Value['YoosureID'].' and IsDefault = 1');
                if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                    $Data['Data'][$Key]['Study_Img'] = LImageURL.$YoosureImage['Image'];
                }else{
                    $Data['Data'][$Key]['Study_Img'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
                $Data['Data'][$Key]['Study_Url'] = '/studytour/'.$Value['YoosureID'].'.html';
            }
            MultiPage($Data, 6);
            echo json_encode($Data);
            exit();
        }else{
            if ($Keyword != '') {
                $Data['ResultCode'] = 103;
            } else {
                $Data['ResultCode'] = 101;
            }
            $MysqlWhere = ' and Status=1 ';
            $StudyYoosureLists = $StudyYoosureModule->GetLists($MysqlWhere, 0, $PageSize);
            $Data['Data'] = array();
            foreach ($StudyYoosureLists as $Key => $Value) {
                $Data['Data'][$Key]['Study_ID'] = $Value['YoosureID'];
                $Data['Data'][$Key]['Study_Name'] = $Value['Title'];
                $Data['Data'][$Key]['Study_Recommend'] = $Value['R1'];
                $ApplyTime = json_decode($Value['ApplyTime'],true);
                $Data['Data'][$Key]['Study_Date'] = $ApplyTime[0];//报名截止时间
                $Data['Data'][$Key]['Study_OriginalPrice'] = intval($Value['OriginalPrice']);
                $Data['Data'][$Key]['Study_Picre'] = intval($Value['Price']);
                $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$Value['YoosureID'].' and IsDefault = 1');
                if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                    $Data['Data'][$Key]['Study_Img'] = LImageURL.$YoosureImage['Image'];
                }else{
                    $Data['Data'][$Key]['Study_Img'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
                $Data['Data'][$Key]['Study_Url'] = '/studytour/'.$Value['YoosureID'].'.html';
            }
            MultiPage($Data, 6);
            echo json_encode($Data);
            exit();
        }
    }
}