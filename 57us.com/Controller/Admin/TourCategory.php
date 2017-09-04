<?php

class TourCategory
{

    public function __construct()
    {
        IsLogin();
    }

    
    // 添加产品类别
    public function Add()
    {
        $TourCategoryID = intval($_GET['TourCategoryID']);
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductCategoryModule.php';
        $TourProductCategoryModule = new TourProductCategoryModule();
        $TourCategory = $TourProductCategoryModule->TourSelectByParent(0);
        if ($TourCategoryID > 0) {
            $TourCategoryDetails = $TourProductCategoryModule->GetInfoByKeyID($TourCategoryID);
        }
        if ($_POST) {
            $Date['CnName'] = trim($_POST['CnName']);
            $Date['ParentID'] = trim($_POST['ParentID']);
            $Date['Alias'] = trim($_POST['Alias']);
            $Date['Title'] = trim($_POST['Title']);
            $Date['Keywords'] = trim($_POST['Keywords']);
            $Date['Description'] = trim($_POST['Description']);
            if ($TourCategoryID > 0) {
                $IsOk = $TourProductCategoryModule->UpdateInfoByKeyID($Date, $TourCategoryID);
            } else {
                $IsOk = $TourProductCategoryModule->InsertInfo($Date);
            }
            if ($IsOk) {
                alertandgotopage("操作成功", '/index.php?Module=TourCategory&Action=TourCategoryList');
            } else {
                alertandgotopage("未做修改或添加操作", '/index.php?Module=TourCategory&Action=TourCategoryList');
            }
        }
        include template('TourtwoCategoryAdd');
    }
    // 产品类别列表
    public function TourCategoryList()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductCategoryModule.php';
        $TourProductCategoryModule = new TourProductCategoryModule();
        $TourCategoryList = $TourProductCategoryModule->TourSelectByParent(0);
        if ($TourCategoryList) {
            foreach ($TourCategoryList as $key => $value) {
                $TourCategoryLists = $TourProductCategoryModule->TourSelectByParent($value['TourCategoryID']);
                $TourCategoryList[$key]['parent'] = $TourCategoryLists;
            }
        }
        $Get = $_GET;
        $SqlWhere = '';
        if ($Get['CnName'] != '') {
            $SqlWhere .= ' and concat(CnName,Alias) like \'%' . $Get['CnName'] . '%\'';
            $TourCategoryList = $TourProductCategoryModule->GetLists($SqlWhere);
        }
        $CnName = $Get['CnName'];
        include template('TourCategoryList');
    }
}

?>