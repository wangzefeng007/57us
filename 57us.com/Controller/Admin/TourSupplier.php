<?php

class TourSupplier
{

    public function __construct()
    {
        IsLogin();
    }


    // 显示供应商列表
    public function TourSupplierList()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourSupplierModule.php';
        $TourSupplierModule = new TourSupplierModule();
        $Get = $_GET;
        $SqlWhere = '';
        $AddUrl = 'Module=TourSupplier&Action=TourSupplierList';
        if ($Get['CnName'] != '') {
            $SqlWhere .= ' and concat(CnName,EnName,Contacts) like \'%' . $Get['CnName'] . '%\'';
            $AddUrl .= '&CnName=' . $Get['CnName'];
        }
        $Data['Data'] = $TourSupplierModule->GetLists($SqlWhere, 0, 500);
        $CnName = $Get['CnName'];
        include template('TourSupplierList');
    }
    // 添加或更新供应商信息
    public function Add()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourSupplierModule.php';
        $TourSupplierModule = new TourSupplierModule();
        $SupplierID = intval($_GET['SupplierID']);
        $TourSupplierModules = $TourSupplierModule->GetInfoByKeyID($SupplierID);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (! $_POST['CnName'] || ! $_POST['Contacts']) {
                alertandgotopage("必填选项不能为空", '/index.php?Module=TourSupplier&Action=Add');
                exit();
            }
        }
        if ($_POST) {
            $SupplierID = intval($_POST['SupplierID']);
            $Date['CnName'] = trim($_POST['CnName']);
            $Date['EnName'] = trim($_POST['EnName']);
            $Date['Contacts'] = trim($_POST['Contacts']);
            $Date['ContactInfo'] = trim($_POST['ContactInfo']);
            $Date['CacheUrl'] = trim($_POST['CacheUrl']);
            $Date['Remark'] = trim($_POST['Remark']);
            $Date['UpdateTime'] = date('y-m-d h:i:s', time());
            $Date['FromIP'] = $_SERVER["REMOTE_ADDR"];
            if ($SupplierID > 0) {
                $TourSupplier = $TourSupplierModule->UpdateInfoByKeyID($Date, $SupplierID);
            } else {
                $TourSupplier = $TourSupplierModule->InsertInfo($Date);
            }
            if ($TourSupplier == true) {
                alertandgotopage("操作成功", '/index.php?Module=TourSupplier&Action=TourSupplierList');
            } else {
                alertandgotopage("操作失败", '/index.php?Module=TourSupplier&Action=TourSupplierList');
            }
        }
        $TourSupplierlist = $TourSupplierModule->TourSupplierSelect();
        include template('TourSupplierAdd');
    }
}
?>