<?php

class HotelOrder
{

    public function __construct()
    {
        IsLogin();
    }

    //酒店订单列表
    public function Lists()
    {
        $HotelOrderModule = new HotelOrderModule();
        $SqlWhere = '';
        $PageUrl = '';
        $Status = intval($_GET['Status']);
        $StatusInfo = $HotelOrderModule->Status;
        if ($Status > 0) {
            $SqlWhere .= " and `Status`=$Status";
            $PageUrl .= "&Status=$Status";
        }
        $OrderNo = trim($_GET['OrderNo']);
        if ($OrderNo) {
            $SqlWhere .= " and OrderNo like '%$OrderNo%'";
            $PageUrl .= "&OrderNo=$OrderNo";
        }
        // 分页开始
        $Page = intval($_REQUEST['Page']) ? intval($_REQUEST['Page']) : 1;
        $PageSize = 10;
        $Rscount = $HotelOrderModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $HotelOrderModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            MultiPage($Data, 10);
        }
        include template("HotelOrderLists");
    }

    //酒店订单详情
    public function Details()
    {
        $OrderID = intval($_GET['OrderID']);
        $Edit = intval($_GET['Edit']);
        $HotelOrderModule = new HotelOrderModule();
        $OrderInfo = $HotelOrderModule->GetInfoByKeyID($OrderID);
        $StatusInfo = $HotelOrderModule->Status;
        $Pay = $HotelOrderModule->PaymentMethod;
        $RoomPersonNum = json_decode($OrderInfo['RoomPersonNum'], true);
        if ($_POST) {
            $OrderID = $_POST['OrderID'];
            if ($_POST['Status']) {
                $Date['Status'] = trim($_POST['Status']);
                $Data['VoucherRemark']=$_POST['VoucherRemark'];
                $OrderInfo = $HotelOrderModule->GetInfoByKeyID($OrderID);
                //添加订单状态更新日志
                include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderLogModule.php';
                $OrderLogModule = new TourProductOrderLogModule();
                $LogData = array('OrderNumber' => $OrderInfo['OrderNo'], 'AdminID' => $_SESSION['AdminID'], 'OldStatus' => $OrderInfo['Status'], 'NewStatus' => $Date['Status'], 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => '3', 'Remarks' => '后台操作');
                $OrderLogModule->InsertInfo($LogData);
                //发送确认函
                if($Date['Status']==4){

                    $this->SendVoucher($OrderInfo,$Data['VoucherRemark']);
                }
            }
            if ($_POST['Remark']) {
                $Date['Remark'] = trim($_POST['Remark']);
            }
            if ($_POST['Confirm']) {
                $OrderID = $_POST['OrderID'];
                $OrderInfo = $HotelOrderModule->GetInfoByKeyID($OrderID);
                include SYSTEM_ROOTPATH . '/Controller/Hotel/HotelApi.php';
                $HotelApi = new HotelApi();
                $CancelResult = $HotelApi->BookingCancel($OrderInfo['BookingID']);
                if (isset($CancelResult['ConfirmID']) && $CancelResult['ConfirmID'] != '') {
                    $CancelPrice = ceil($CancelResult['Amount'] * 0.1 + $CancelResult['Amount']);
                    $CancelConfirmID = $CancelResult['ConfirmID'];
                    $HotelApi->BookingCancelConfirm($OrderInfo['BookingID'],$CancelConfirmID);
                    $UpData['Status'] = 5;
                    $UpData['Cancelamout'] = $CancelPrice;
                    $UpdateResult = $HotelOrderModule->UpdateInfoByKeyID($UpData, $OrderID);
                    //添加订单状态更新日志
                    include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderLogModule.php';
                    $OrderLogModule = new TourProductOrderLogModule();
                    $LogData = array('OrderNumber' => $OrderInfo['OrderNo'], 'AdminID' => $_SESSION['AdminID'], 'OldStatus' => $OrderInfo['Status'], 'NewStatus' => $UpData['Status'], 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => '3', 'Remarks' => '出单失败,库存不足');
                    if ($UpdateResult!==false) {
                        alertandback("取消订单成功");
                    } else {
                        alertandback("取消订单失败");
                    }
                } else {
                    alertandback("未取消订单");
                }
            }
            $Updatestatus = $HotelOrderModule->UpdateInfoByKeyID($Date, $OrderID);
            if ($Updatestatus) {
                alertandgotopage("更新成功", '/index.php?Module=HotelOrder&Action=Details&OrderID=' . $OrderID . '&Edit=1');
            } else {
                alertandgotopage("更新失败", '/index.php?Module=HotelOrder&Action=Details&OrderID=' . $OrderID . '&Edit=1');
            }
        }
        include template("HotelOrderDetails");
    }
    
    private function SendVoucher($OrderInfo,$VoucherRemark){
        $OrderInfo=$OrderInfo;
        $VoucherRemark=$VoucherRemark;
        $AdultNum=0;
        $ChildNum=0;
        $PersonArr=json_decode($OrderInfo['RoomPersonNum'],true);
        foreach($PersonArr as $arr){
            $AdultNum+=intval($arr['AdultCount']);
            $ChildNum+=intval($arr['ChildCount']);
        }
        //取消政策
        $Cancel=json_decode($OrderInfo['CancellationPolicy'],true);
        include SYSTEM_ROOTPATH.'/Modules/Hotel/Class.HotelBaseInfoModule.php';
        $HotelBaseInfoModule=new HotelBaseInfoModule();
        $HotelInfo=$HotelBaseInfoModule->GetHotelByID($OrderInfo['HotelID']);
        ob_start();
        include template('HotelOrderVoucher');
        $Message=ob_get_contents();
        ob_clean();
        include SYSTEM_ROOTPATH.'/Modules/Notice/Class.NoticeModule.php';
        $NoticeModule=new NoticeModule();
        $NoticeModule->SendEMailNotice($OrderInfo['ContactEMail'],'57美国-酒店入住确认函', $Message);
        $NoticeModule->SendEMailNotice('linling@57us.com','57美国-酒店入住确认函', $Message);
        $NoticeModule->SendEMailNotice('gaoshuxin@57us.com','57美国-酒店入住确认函', $Message);
        //短信通知
//         $SmsInfo = '';
//         $NoticeModule->SendSMSNotice($OrderInfo['ContactPhone'], $SmsInfo);
        
    }
}
