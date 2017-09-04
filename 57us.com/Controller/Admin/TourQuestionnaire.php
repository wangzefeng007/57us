<?php
error_reporting(7);
// ini_set ( 'display_errors', '1' );
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/13
 * Time: 15:31
 */
class TourQuestionnaire
{

    public function __construct()
    {
        IsLogin();
    }

    /*
     *
     * 特色定制内容后台
     *
     */
    public function TourCustomFeaturesAdd()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourCustomFeaturesModule.php';
        $TourCustomFeaturesModule = new TourCustomFeaturesModule();
        $ID = intval($_GET['ID']);
        if ($ID > 0) {
            $TourCustomFeaturesInfo = $TourCustomFeaturesModule->GetInfoByKeyID($ID);
        }
        if ($_POST) {
            // 上传图片
            include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
            if ($_FILES['Image']['size'][0] > 0) {
                $Upload = new MultiUpload('Image');
                $DataDir = date("Ymd");
                $Upload->savePath = './Uploads/Features/' . $DataDir . '/';
                $File = $Upload->upload();
                $Picture = $File[0] ? $File[0] : '';
                $Data['Image'] = '/Uploads/Features/' . $DataDir . '/' . $Picture;
            }
            $ID = intval($_POST['ID']);
            $Data['CustomName'] = trim($_POST['CustomName']);
            $Data['EnName'] = trim($_POST['EnName']);
            $Data['Description'] = trim($_POST['Description']);
            $Data['Content'] = trim($_POST['Content']);
            if ($_POST['ID'] > 0) {
                if (isset($Data['Image'])) {
                    $TourCustomFeaturesInfo = $TourCustomFeaturesModule->GetInfoByKeyID($ID);
                    if ($TourCustomFeaturesInfo['Image'])
                        DelFromImgServ($TourCustomFeaturesInfo['Image']);
                }
                $Update = $TourCustomFeaturesModule->UpdateInfoByKeyID($Data, $ID);
            } else {
                $ID = $TourCustomFeaturesModule->InsertInfo($Data);
            }
            if ($Update || $ID) {
                alertandgotopage("操作成功", '/index.php?Module=TourQuestionnaire&Action=TourCustomFeaturesAdd&ID=' . $ID);
            } else {
                alertandgotopage("操作失败", '/index.php?Module=TourQuestionnaire&Action=TourCustomFeaturesAdd&ID=' . $ID);
            }
        }
        include template('TourCustomFeaturesAdd');
    }

    public function TourCustomFeaturesList()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourCustomFeaturesModule.php';
        $TourCustomFeaturesModule = new TourCustomFeaturesModule();
        // 分页开始
        $SqlWhere = '';
        $Page = intval($_GET['Page']);
        $Page = $Page ? $Page : 1;
        $PageSize = 10;
        if ($_GET['Title'] ){
            $Title = trim($_GET ['Title']);
            $SqlWhere .=' and concat(CustomName) like \'%'. $Title .'%\'';
        }
        $Rscount = $TourCustomFeaturesModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TourCustomFeaturesModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            MultiPage($Data, 10);
        }
        include template('TourCustomFeaturesList');
    }

    public function TourCustomFeaturesDelete()
    {
        if ($_GET) {
            $ID = $_GET['ID'];
            include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourCustomFeaturesModule.php';
            $TourCustomFeaturesModule = new TourCustomFeaturesModule();
            $DeleteRecommend = $TourCustomFeaturesModule->DeleteByKeyID($ID);
            $TourCustomFeaturesInfo = $TourCustomFeaturesModule->GetInfoByKeyID($ID);
            @unlink(SYSTEM_ROOTPATH . $TourCustomFeaturesInfo['Image']);
            
            if ($DeleteRecommend) {
                alertandgotopage("删除成功", '/index.php?Module=TourQuestionnaire&Action=TourCustomFeaturesList');
            } else {
                alertandgotopage("删除失败", '/index.php?Module=TourQuestionnaire&Action=TourCustomFeaturesList');
            }
        }
    }

    public function TourPrivateOrderList()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourPrivateOrderModule.php';
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        $StatusInfo = $TourPrivateOrderModule->Status;
        // 分页开始
        $SqlWhere = '';
        $Page = intval($_GET['Page']);
        $Page = $Page ? $Page : 1;
        $PageSize = 10;
        // 搜索条件
        $PageUrl = '';
        if ($_GET['OrderNo'] ){
            $OrderNo = trim($_GET ['OrderNo']);
            $SqlWhere .=' and concat(OrderNo) like \'%'. $OrderNo .'%\'';
            $PageUrl .='&OrderNo='.$OrderNo;
        }
        if ($_GET ['Status']){
            $Status = trim($_GET ['Status']);
            $SqlWhere .=' and `Status` = \''. $Status .'\'';
            $PageUrl .='&Status='.$Status;
        }
        // 跳转到该页面
        if ($_POST['page']) {
            $page = $_POST['page'];
            tourl('/index.php?Module=TourQuestionnaire&Action=TourPrivateOrderList&Page=' . $page . $PageUrl);
        }
        $Rscount = $TourPrivateOrderModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TourPrivateOrderModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            MultiPage($Data, 10);
        }
        include template('TourPrivateOrderList');
    }

    public function TourPrivateOrderEdit()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourPrivateOrderModule.php';
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        $OrderID = trim($_GET['OrderID']);

        $OderInfo = $TourPrivateOrderModule->GetInfoByKeyID($OrderID);
        $StatusInfo = $TourPrivateOrderModule->Status;
        $Number = json_decode($OderInfo['Number'], true);
        if ($_POST) {
            $OrderID = $_POST['OrderID'];
            if ($_POST['Money']) {
                $Date['Money'] = trim($_POST['Money']);
                $Date['Status'] = 1;
            }
            if ($_POST['Status']) {
                $Date['Status'] = intval($_POST['Status']);
                $OderInfo = $TourPrivateOrderModule->GetInfoByKeyID($OrderID);
                //添加订单状态更新日志
                include SYSTEM_ROOTPATH.'/Modules/Tour/Class.TourProductOrderLogModule.php';
                $OrderLogModule = new TourProductOrderLogModule();
                $LogData = array('OrderNumber'=>$OderInfo['OrderNo'],'AdminID'=>$_SESSION['AdminID'],'OldStatus'=>$OderInfo['Status'],'NewStatus'=> $Date['Status'],'OperateTime'=>date("Y-m-d H:i:s",time()),'IP'=>GetIP(),'Type'=>'5','Remarks'=>'后台操作');
                $OrderLogModule->InsertInfo($LogData);
            }
            if ($_POST['Remarks']||$_POST['Remarks']=='') {
                $Date['Remarks'] = trim($_POST['Remarks']);
            }
            $Update = $TourPrivateOrderModule->UpdateInfoByKeyID($Date, $OrderID);
            if ($Update) {
                alertandgotopage("更新成功", '/index.php?Module=TourQuestionnaire&Action=TourPrivateOrderEdit&OrderID=' . $OrderID);
            } else {
                alertandgotopage("更新失败", '/index.php?Module=TourQuestionnaire&Action=TourPrivateOrderEdit&OrderID=' . $OrderID);
            }
        }
        include template('TourPrivateOrderEdit');
    }

    public function TourPrivateOrderDetail()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourPrivateOrderModule.php';
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        $OrderID = trim($_GET['OrderID']);
        $OderInfo = $TourPrivateOrderModule->GetInfoByKeyID($OrderID);
        $Number = json_decode($OderInfo['Number'], true);
        include template('TourPrivateOrderDetail');
    }

    public function TourPrivateOrderDelete()
    {
        if ($_GET) {
            include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourPrivateOrderModule.php';
            $TourPrivateOrderModule = new TourPrivateOrderModule();
            $ID = $_GET['ID'];
            $Delete = $TourPrivateOrderModule->DeleteByKeyID($ID);
            
            if ($Delete) {
                alertandgotopage("删除成功", '/index.php?Module=TourQuestionnaire&Action=TourPrivateOrderList');
            } else {
                alertandgotopage("删除失败", '/index.php?Module=TourQuestionnaire&Action=TourPrivateOrderList');
            }
        }
    }
}