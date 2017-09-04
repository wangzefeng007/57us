<?php

/**
 * @desc 旅游景点推荐
 */
class TourPassAttractions
{
    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourPassAttractionsModule.php';
    }
    
    // 旅游景点列表
    public function Lists()
    {
        $TourPassAttractionsModule = new TourPassAttractionsModule();
        $SqlWhere = ' order by R1 DESC,AddTime Desc';
        $Lists = $TourPassAttractionsModule->GetInfoByWhere($SqlWhere,true);
        include template('TourPassAttractionsList');
    }
    
    // 设置热门景点
    public function SetHot()
    {
        $TourPassAttractionsModule = new TourPassAttractionsModule();
        $TourPassAttractionsID = $_GET['ID'];
        $R1 = $_GET['R1'];
        if ($R1 == 1) {
            $UpdateInfo['R1'] = 0;
        } else {
            $UpdateInfo['R1'] = 1;
        }
        $IsOk = $TourPassAttractionsModule->UpdateInfoByKeyID($UpdateInfo, $TourPassAttractionsID);
        $Url = '/index.php?Module=TourPassAttractions&Action=Lists';
        if ($IsOk) {
            alertandgotopage("修改成功", $Url);
        } else {
            alertandgotopage("修改失败", $Url);
        }
    }
    // 删除
    public function Delete()
    {
        $TourPassAttractionsModule = new TourPassAttractionsModule();
        $TourPassAttractionsID = $_GET['ID'];
        $IsOk = $TourPassAttractionsModule->DeleteByKeyID($TourPassAttractionsID);
        $Url = '/index.php?Module=TourPassAttractions&Action=Lists';
        if ($IsOk) {
            alertandgotopage("修改成功", $Url);
        } else {
            alertandgotopage("修改失败", $Url);
        }
    }
}