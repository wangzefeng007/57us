<?php

class TourFootTag
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourFootTagModule.php';
    }

    
    // 底部链接列表
    public function TourFootTagList()
    {
        $TourFootTagModule = new TourFootTagModule();
        $SqlWhere = ' order by Sort Desc';
        $MuDiDi = $TourFootTagModule->GetLists(' and Type=1 order by Sort Desc', 0, 1000);
        $JingDian = $TourFootTagModule->GetLists(' and Type=2 order by Sort Desc', 0, 1000);
        $USTour = $TourFootTagModule->GetLists(' and Type=3 order by Sort Desc', 0, 1000);
        $USStudy = $TourFootTagModule->GetLists(' and Type=4 order by Sort Desc', 0, 1000);
        $USImmigrant = $TourFootTagModule->GetLists(' and Type=5 order by Sort Desc', 0, 1000);
        include template('TourFootTagList');
    }
    // 编辑底部链接
    public function TourFootTagEdit()
    {
        $TourFootTagModule = new TourFootTagModule();
        $TourFootTagID = $_GET['ID'];
        $TourFootTagInfo = $TourFootTagModule->GetInfoByKeyID($TourFootTagID);
        if ($_POST) {
            $TourFootTagID = intval($_POST['ID']);
            $Data['Type'] = intval($_POST['type']);
            $Data['TourFootTagName'] = trim($_POST['TourFootTagName']);
            $Data['TourFootTagUrl'] = trim($_POST['TourFootTagUrl']);
            $Data['Sort'] = trim($_POST['Sort']);
            $Data['UpdateTime'] = date('Y-m-d h:i:s', time());
            if ($Data['TourFootTagName'] == '' || $Data['TourFootTagUrl'] == '') {
                alertandback("操作失败，信息填写不全！");
            }
            if ($TourFootTagID > 0) {
                $IsOk = $TourFootTagModule->UpdateInfoByKeyID($Data, $TourFootTagID);
            } else {
                $IsOk = $TourFootTagModule->InsertInfo($Data);
            }
            if ($IsOk) {
                alertandgotopage("操作成功", '/index.php?Module=TourFootTag&Action=TourFootTagList');
            } else {
                alertandgotopage("操作失败", '/index.php?Module=TourFootTag&Action=TourFootTagList');
            }
        }
        include template('TourFootTagEdit');
    }
    // 删除底部链接
    public function Delete()
    {
        $TourFootTagModule = new TourFootTagModule();
        $TourFootTagID = $_GET['ID'];
        $IsOk = $TourFootTagModule->DeleteByKeyID($TourFootTagID);
        if ($IsOk) {
            alertandgotopage("修改成功", '/index.php?Module=TourFootTag&Action=TourFootTagList');
        } else {
            alertandgotopage("修改失败", '/index.php?Module=TourFootTag&Action=TourFootTagList');
        }
    }
}