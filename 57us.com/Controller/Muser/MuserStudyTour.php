<?php

/**
 */
class MuserStudyTour
{
    public function __construct(){
    }
    /**
     * @desc  登录验证
     */
    public function  IsLogin()
    {
        if (!isset ($_SESSION ['UserID']) || empty ($_SESSION ['UserID'])) {
            header('Location:' . WEB_MUSER_URL . '/muser/login/');
        }
    }
    /*
     * @desc 游学订单列表
     */
    public function StudyTourOrderList(){
        $Title = '游学订单';
        $this->IsLogin();
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $StudyYoosureImageModule = new StudyYoosureImageModule ();
        $OrderStatus = $StudyYoosureOrderModule->NStatus;
        $ZhiFuStatus = '2,3,4,5,6,7,8';
        $Status = $_GET['S'] ? $_GET['S'] : 1; //默认全部 0-全部
        $UserID = intval($_SESSION ['UserID']);
        switch ($Status) {
            case '1': //全部
                $MysqlWhere = ' and UserID= ' . $UserID;
                break;
            case '2': //已支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status in (' . $ZhiFuStatus . ')';
                break;
            case '3': //待支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status = 1';
                break;
            case '4': //已取消
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status =10';
                break;
        }
        //分页开始
        $Page = intval($_GET ['P']);
        $Page = $Page ? $Page : 1;
        $PageSize = 200;
        $Rscount = $StudyYoosureOrderModule->GetListsNum($MysqlWhere);

        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $MysqlWhere .= ' order by CreateTime desc';
            $Data ['Data'] = $StudyYoosureOrderModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data ['Data'] as $key=>$value){
                $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$value['YoosureID'].' and IsDefault = 1');
                if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                    $Data['Data'][$key]['Image'] = LImageURL.$YoosureImage['Image'];
                }else{
                    $Data['Data'][$key]['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
            }
            MultiPage($Data, 10);
        }
        include template('StudyTourOrderList');
    }

    /*
     * @desc 游学订单详情
     */
    public function StudyTourOrderDetail(){
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        $OrderStatus = $StudyYoosureOrderModule->NStatus;
        $ID = $_GET['ID'];
        $OrderInfo = $StudyYoosureOrderModule->GetInfoByKeyID($ID);
        if (strtotime($OrderInfo[''])<time()){
            $OrderID = $_POST['OrderID'];
            $Data['Status'] =10;
            $StudyYoosureOrderModule->UpdateInfoByKeyID($Data,$OrderID);
        }
        $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$OrderInfo['YoosureID']);
        if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
            $OrderInfo['Image'] = LImageURL.$YoosureImage['Image'];
        }else{
            $OrderInfo['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
        }
        $OrderInfo['TravelerInformation']= json_decode($OrderInfo['TravelerInformation'],true);
        include template('StudyTourOrderDetail');
    }
}