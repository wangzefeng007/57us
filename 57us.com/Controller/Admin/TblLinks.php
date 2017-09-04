<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/10/11
 * Time: 16:41
 */
class TblLinks
{
    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblLinksModule.php';
    }
    public function Lists(){
        $TblLinksModule = new TblLinksModule();
        $FirstLinks = $TblLinksModule->GetLists(' and Type=1 order by Sort Desc', 0, 1000);
        $TourLinks  = $TblLinksModule->GetLists(' and Type=2 order by Sort Desc', 0, 1000);
        $StudyLinks = $TblLinksModule->GetLists(' and Type=3 order by Sort Desc', 0, 1000);
        include template('TblLinksList');
    }
    // 编辑友情链接
    public function Edit()
    {
        $TblLinksModule = new TblLinksModule();
        $ID = $_GET['ID'];
        $TblLinksInfo = $TblLinksModule->GetInfoByKeyID($ID);
        if ($_POST) {
            $ID = intval($_POST['ID']);
            $Data['Type'] = intval($_POST['Type']);
            $Data['Name'] = trim($_POST['Name']);
            $Data['Url'] = trim($_POST['Url']);
            $Data['Sort'] = trim($_POST['Sort']);
            $Data['UpdateTime'] = date('Y-m-d h:i:s', time());
            if ($Data['Name'] == '' || $Data['Url'] == '') {
                alertandback("操作失败，信息填写不全！");
            }
            if ($ID > 0) {
                $IsOk = $TblLinksModule->UpdateInfoByKeyID($Data, $ID);
            } else {
                $IsOk = $TblLinksModule->InsertInfo($Data);
            }
            if ($IsOk) {
                alertandgotopage("操作成功", '/index.php?Module=TblLinks&Action=Lists');
            } else {
                alertandgotopage("操作失败", '/index.php?Module=TblLinks&Action=Lists');
            }
        }
        include template('TblLinksEdit');
    }
    // 删除友情链接
    public function Delete()
    {
        $TblLinksModule = new TblLinksModule();
        $ID = $_GET['ID'];
        $IsOk = $TblLinksModule->DeleteByKeyID($ID);
        if ($IsOk) {
            alertandgotopage("修改成功", '/index.php?Module=TblLinks&Action=Lists');
        } else {
            alertandgotopage("修改失败", '/index.php?Module=TblLinks&Action=Lists');
        }
    }
    public function Add(){
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourFootTagModule.php';
        $TblLinksModule = new TblLinksModule();
        $TourFootTagModule = new TourFootTagModule();
        $Data = $TourFootTagModule->GetLists(' and Type=6 order by Sort Desc', 0, 1000);
        foreach ($Data as $key=>$value){
            $Date['Type']= 1;
            $Date['Name']= $value['TourFootTagName'];
            $Date['Url']= $value['TourFootTagUrl'];
            $Date['Sort']= $value['Sort'];
            $Date['UpdateTime']= $value['UpdateTime'];
            $Name = $Date['Name'];
            $Info = $TblLinksModule->GetInfoByWhere(" and Name like '%$Name%'");
            if (!$Info)
            $Insert = $TblLinksModule->InsertInfo($Date);
            if ($Insert){
                echo 'susser'.'<br>';
            }
        }
    }
}