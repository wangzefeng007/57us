<?php

class TourSpecialSubject
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourSpecialSubjectModule.php';
    }
    // 旅游特色主题
    public function TourSpecialSubjectList()
    {
        $TourSpecialSubjectModule = new TourSpecialSubjectModule();
        $GenTuanYou = $TourSpecialSubjectModule->GetLists(' and Category=1 order by Sort Desc', 0, 1000);
        $YiRiYou = $TourSpecialSubjectModule->GetLists(' and Category=2 order by Sort Desc', 0, 1000);
        $PiaoWu = $TourSpecialSubjectModule->GetLists(' and Category=3 order by Sort Desc', 0, 1000);
        $TeSeTiYan = $TourSpecialSubjectModule->GetLists(' and Category=4 order by Sort Desc', 0, 1000);
        $Wifi = $TourSpecialSubjectModule->GetLists(' and Category=5 order by Sort Desc', 0, 1000);
        include template('TourSpecialSubjectList');
    }

    public function Edit()
    {
        $TourSpecialSubjectModule = new TourSpecialSubjectModule();
        $TourSpecialSubjectID = intval($_GET['TourSpecialSubjectID']);
        $TourSpecialSubjectInfo = $TourSpecialSubjectModule->GetInfoByKeyID($TourSpecialSubjectID);
        if ($_POST) {
            $TourSpecialSubjectID = intval($_POST['TourSpecialSubjectID']);
            $Data['Category'] = intval($_POST['Category']);
            $Data['SpecialSubjectName'] = trim($_POST['SpecialSubjectName']);
            $Data['Sort'] = trim($_POST['Sort']);
            $Data['UpdateTime'] = date('Y-m-d H:i:s', time());
            if ($Data['SpecialSubjectName'] == '') {
                alertandback("操作失败，信息填写不全！");
            }
            if ($TourSpecialSubjectID > 0) {
                $IsOk = $TourSpecialSubjectModule->UpdateInfoByKeyID($Data, $TourSpecialSubjectID);
            } else {
                $IsOk = $TourSpecialSubjectModule->InsertInfo($Data);
            }
            if ($IsOk) {
                alertandgotopage("操作成功", '/index.php?Module=TourSpecialSubject&Action=TourSpecialSubjectList');
            } else {
                alertandgotopage("操作失败", '/index.php?Module=TourSpecialSubject&Action=TourSpecialSubjectList');
            }
        }
        include template('TourSpecialSubjectEdit');
    }

    public function Delete()
    {
        $TourSpecialSubjectID = intval($_GET['TourSpecialSubjectID']);
        $TourSpecialSubjectModule = new TourSpecialSubjectModule();
        $Delete = $TourSpecialSubjectModule->DeleteByKeyID($TourSpecialSubjectID);
        if ($Delete == true) {
            alertandgotopage("操作成功", '/index.php?Module=TourSpecialSubject&Action=TourSpecialSubjectList');
        } else {
            alertandgotopage("操作失败", '/index.php?Module=TourSpecialSubject&Action=TourSpecialSubjectList');
        }
    }
}