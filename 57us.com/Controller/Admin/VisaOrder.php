<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/9/8
 * Time: 15:55
 */
class VisaOrder
{
    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH.'/Modules/Visa/Class.VisaOrderModule.php';
    }

    //签证订单列表
    public function Lists(){
        $VisaOrderModule=new VisaOrderModule();
        $StatusInfo = $VisaOrderModule->Status;
        // 分页开始
        $SqlWhere = '';
        $Page = intval($_GET['Page']);
        $Page = $Page ? $Page : 1;
        $PageSize = 10;
        // 搜索条件
        $PageUrl = '';
        if ($_GET['Title'] ){
            $Title = trim($_GET ['Title']);
            $SqlWhere .=' and (concat(OrderName) like \'%' . $Title . '%\' or  concat(OrderNumber)  like \'%'. $Title .'%\')';
            $PageUrl .='&Title='.$Title;
        }
        if ($_GET ['Status']){
            $Status = trim($_GET ['Status']);
            $SqlWhere .=' and `Status` = \''. $Status .'\'';
            $PageUrl .='&Status='.$Status;
        }
        // 跳转到该页面
        if ($_POST['page']) {
            $page = $_POST['page'];
            tourl('/index.php?Module=VisaOrder&Action=Lists&Page=' . $page . $PageUrl);
        }
        $Rscount = $VisaOrderModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $VisaOrderModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            MultiPage($Data, 10);
        }
        include template("VisaOrderLists");
    }
    //签证订单详情
    public function Detail(){
        $VisaOrderModule=new VisaOrderModule();
        $StatusInfo = $VisaOrderModule->Status;
        $Pay = $VisaOrderModule->PaymentMethod;
        if( $_GET){
            $ID = $_GET['ID'];
            $OderInfo = $VisaOrderModule->GetInfoByKeyID($ID);
        }
        include template("VisaOrderDetail");
    }
    //签证订单管理
    public function Edit(){
        $VisaOrderModule=new VisaOrderModule();
        $StatusInfo = $VisaOrderModule->Status;
        $Pay = $VisaOrderModule->PaymentMethod;
        if( $_GET){
            $ID = $_GET['ID'];
            $OderInfo = $VisaOrderModule->GetInfoByKeyID($ID);
        }
        if($_POST){
            $ID = $_POST['ID'];
            if ($_POST['Status']){
                //开启事务
                global $DB;
                $DB->query("BEGIN");//开始事务定义
                $Date['Status'] = trim($_POST['Status']);
                if(!$Date['Status']){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                }
                $OrderInfo = $VisaOrderModule->GetInfoByKeyID($ID);
                if(!$OrderInfo){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                }
                //添加订单状态更新日志
                include SYSTEM_ROOTPATH.'/Modules/Tour/Class.TourProductOrderLogModule.php';
                $OrderLogModule = new TourProductOrderLogModule();
                $LogData = array('OrderNumber'=>$OrderInfo['OrderNumber'],'AdminID'=>$_SESSION['AdminID'],'OldStatus'=>$OrderInfo['Status'],'NewStatus'=> $Date['Status'],'OperateTime'=>date("Y-m-d H:i:s",time()),'IP'=>GetIP(),'Type'=>'2','Remarks'=>'后台操作');
                $InsertInfo = $OrderLogModule->InsertInfo($LogData);
                if(!$InsertInfo){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                }else{
                    $DB->query("COMMIT");//执行事务
                }
            }
            if ($_POST['Remarks']){
                $Date['Remarks'] = trim($_POST['Remarks']);
            }
            $Updatestatus = $VisaOrderModule->UpdateInfoByKeyID($Date,$ID);
            if($Updatestatus){
                alertandgotopage ( "更新成功", '/index.php?Module=VisaOrder&Action=Lists');
            } else {
                alertandgotopage ( "更新失败", '/index.php?Module=VisaOrder&Action=Lists');
            }
        }
        include template("VisaOrderEdit");
    }
}