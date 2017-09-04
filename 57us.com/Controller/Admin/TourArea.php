<?php

class TourArea
{
    public function __construct()
    {
        IsLogin();
    }

    /**
     * @desc  添加或更新地区信息
     */
    public function Add()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        $TourAreaModule = new TourAreaModule();
        $AreaID = intval($_GET['AreaID']);
        $TourAreaDetails = $TourAreaModule->GetInfoByKeyID($AreaID);
        $TourAreaLists = $TourAreaModule->GetInfoByWhere(' and `ParentID` = 0',true);
        foreach ($TourAreaLists as $key => $value) {
            $TourAreaAddprovince = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$value['AreaID'],true);
            $TourAreaLists[$key]['Province'] = $TourAreaAddprovince;
            if ($TourAreaAddprovince) {
                foreach ($TourAreaAddprovince as $k => $v) {
                    $TourAreaAddCity = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$v['AreaID'],true);
                    $TourAreaLists[$key]['Province'][$k]['City'] = $TourAreaAddCity;
                }
            }
        }
        if ($TourAreaDetails['ParentID'] > 0)
            $TourParent = $TourAreaModule->GetInfoByKeyID($TourAreaDetails['ParentID']);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!$_POST['CnName'] || !$_POST['EnName'] || !$_POST['Alias']) {
                alertandgotopage("必填选项不能为空", '/index.php?Module=TourArea&Action=TourAreaAdd');
            }
        }
        if ($_POST) {
            // 上传地区图片
            $AreaID = intval($_POST['AreaID']);
            include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
            if ($_FILES['Image']['size'][0] > 0) {
                $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($AreaID);
                if ($TourAreaInfo['Image'])
                    DelFromImgServ($TourAreaInfo['Image']);
                $Upload = new MultiUpload('Image');
                $File = $Upload->upload();
                $Picture = $File[0] ? $File[0] : '';
                $Data['Image'] = $Picture;
            }
            $Data['ParentID'] = $_POST['ParentID'];
            $Data['CnName'] = trim($_POST['CnName']);
            $Data['EnName'] = trim($_POST['EnName']);
            $Data['CityCategory'] = $_POST['CityCategory'];
            $Data['FullName'] = trim($_POST['FullName']);
            $Data['ShorEnName'] = trim($_POST['ShorEnName']);
            $Data['Alias'] = $_POST['Alias'];
            $Data['Level'] = $_POST['Level'];
            $Data['Description'] = trim($_POST['Description']);
            $Data['R1'] = intval($_POST['R1']);
            $Data['S1'] = intval($_POST['S1']);
            $Data['R2'] = intval($_POST['R2']);
            $Data['S2'] = intval($_POST['S2']);
            $Data['R3'] = intval($_POST['R3']);
            $Data['S3'] = intval($_POST['S3']);
            $Data['R4'] = intval($_POST['R4']);
            $Data['S4'] = intval($_POST['S4']);
            $Data['R5'] = intval($_POST['R5']);
            $Data['S5'] = intval($_POST['S5']);
            $Data['R6'] = intval($_POST['R6']);
            $Data['S6'] = intval($_POST['S6']);
            $Data['R7'] = intval($_POST['R7']);
            $Data['S7'] = intval($_POST['S7']);
            $Data['R9'] = intval($_POST['R9']);
            $Data['S9'] = intval($_POST['S9']);
            $Data['R10'] = intval($_POST['R10']);
            $Data['S10'] = intval($_POST['S10']);
            // 地区介绍文本图片处理-----------------------------------------------------------------------------
            $Data['Content'] = trim($_POST['Content']);
            $Pattern = array();
            $Replacement = array();
            $ImgArr = Array();
            preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($Data['Content']), $ImgArr);
            if (count($ImgArr[0])) {
                foreach ($ImgArr[0] as $Key => $ImgTag) {
                    $Pattern[] = $ImgTag;
                    $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                        '/title=".*"/iU',
                        '/alt=".*"/iU'
                    ), '', $ImgTag));
                }
            }
            $Data['Content'] = addslashes(str_replace($Pattern, $Replacement, stripcslashes($Data['Content'])));
            // 地区介绍文本图片处理-------------------------------------------------------------------------------
            if ($AreaID > 0) {
                $TourAreaUpdate = $TourAreaModule->UpdateInfoByKeyID($Data, $AreaID);
            } else {
                $AreaID = $TourAreaModule->InsertInfo($Data);
            }
            if ($AreaID == true || $TourAreaUpdate == true) {
                alertandgotopage("操作成功", '/index.php?Module=TourArea&Action=Add&AreaID=' . $AreaID);
            } else {
                alertandgotopage("操作失败", '/index.php?Module=TourArea&Action=Add&AreaID=' . $AreaID);
            }
        }
        include template('TourAreaAdd');
    }

    /**
     * @desc  地区列表信息
     */
    public function TourAreaList()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        $TourAreaModule = new TourAreaModule();
        $TourAreaLists = $TourAreaModule->GetInfoByWhere(' and `ParentID` = 0',true);
        if ($TourAreaLists) {
            foreach ($TourAreaLists as $key => $value) {
                $TourAreaAddprovince = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$value['AreaID'],true);
                $TourAreaLists[$key]['Province'] = $TourAreaAddprovince;
                if ($TourAreaAddprovince) {
                    foreach ($TourAreaAddprovince as $K => $V) {
                        $TourAreaAddCity = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$V['AreaID'],true);
                        $TourAreaLists[$key]['Province'][$K]['City'] = $TourAreaAddCity;
                    }
                }
            }
        }
        $Get = $_GET;
        $SqlWhere = '';
        if ($Get['PName'] != '') {
            $SqlWhere .= ' and concat(CnName,EnName,FullName) like \'%' . $Get['PName'] . '%\'';
            $TourAreaLists = $TourAreaModule->GetInfoByWhere($SqlWhere,true);
        }
        $PName = $Get['PName'];
        include template('TourAreaList');
    }

    /**
     * @desc  删除地区信息
     */
    public function Delete()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        $TourAreaModule = new TourAreaModule();
        $AreaID = $_GET['AreaID'];
        $TourAreaDetails = $TourAreaModule->GetInfoByKeyID($AreaID);
        DelFromImgServ($TourAreaDetails['Image']);
        $TourAreaModules = $TourAreaModule->DeleteByKeyID($AreaID);
        if (!$TourAreaModules) {
            alertandgotopage("删除失败", '/index.php?Module=TourArea&Action=TourAreaList');
        } else {
            alertandgotopage("删除成功", '/index.php?Module=TourArea&Action=TourAreaList');
        }
    }

    /**
     * @desc  添加旅游景点
     */
    public function AttractionsAdd()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAttractionsModule.php';
        $TourAttractionsModule = new TourAttractionsModule();
        $TourAreaModule = new TourAreaModule();
        $SqlWhere = '';
        $TourAreaLists = $TourAreaModule->GetInfoByWhere($SqlWhere,true);
        $ID = intval($_GET['ID']);
        if ($ID > 0) {
            $AttractionsInfo = $TourAttractionsModule->GetInfoByKeyID($ID);
            $TourParent = $TourAreaModule->GetInfoByKeyID($AttractionsInfo['AreaID']);
        }
        if ($_POST) {
            // 上传景点图片
            include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
            if ($_FILES['Image']['size'][0] > 0) {
                $Upload = new MultiUpload('Image');
                $File = $Upload->upload();
                $Picture = $File[0] ? $File[0] : '';
                $Data['Image'] = $Picture;
            }
            $Data['Description'] = trim($_POST['Description']);
            $Data['AreaID'] = intval($_POST['AreaID']);
            $Data['AttractionsName'] = trim($_POST['AttractionsName']);
            $Data['EnName'] = trim($_POST['EnName']);
            $Data['R1'] = intval($_POST['R1']);
            $Data['S1'] = intval($_POST['S1']);
            $Data['R2'] = intval($_POST['R2']);
            $Data['S2'] = intval($_POST['S2']);
            $Data['H1'] = intval($_POST['H1']);
            $Data['H2'] = intval($_POST['H2']);
            // 景点介绍文本图片处理-----------------------------------------------------------------------------
            $Data['Content'] = trim($_POST['Content']);
            $Pattern = array();
            $Replacement = array();
            $ImgArr = Array();
            preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($Data['Content']), $ImgArr);
            if (count($ImgArr[0])) {
                foreach ($ImgArr[0] as $Key => $ImgTag) {
                    $Pattern[] = $ImgTag;
                    $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                        '/title=".*"/iU',
                        '/alt=".*"/iU'
                    ), '', $ImgTag));
                }
            }
            $Data['Content'] = addslashes(str_replace($Pattern, $Replacement, stripcslashes($Data['Content'])));
            // 景点介绍文本图片处理-------------------------------------------------------------------------------
            if ($_POST['ID'] > 0) {
                $ID = intval($_POST['ID']);
                if (isset($Data['Image'])) {
                    $AttractionsInfo = $TourAttractionsModule->GetInfoByKeyID($ID);
                    DelFromImgServ($AttractionsInfo['Image']);
                }
                $Update = $TourAttractionsModule->UpdateInfoByKeyID($Data, $ID);
            } else {
                $ID = $TourAttractionsModule->InsertInfo($Data);
            }
            if ($Update || $ID) {
                alertandgotopage("操作成功", '/index.php?Module=TourArea&Action=AttractionsAdd&ID=' . $ID);
            } else {
                alertandgotopage("操作失败", '/index.php?Module=TourArea&Action=AttractionsAdd&ID=' . $ID);
            }
        }
        include template('TourAttractionsAdd');
    }

    /**
     * @desc  旅游景点列表
     */
    public function AttractionsList()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAttractionsModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        $TourAreaModule = new TourAreaModule();
        $TourAttractionsModule = new TourAttractionsModule();
        // 分页开始
        $SqlWhere = '';
        $Page = intval($_GET['Page']);
        $Page = $Page ? $Page : 1;
        $PageSize = 15;
        $Rscount = $TourAttractionsModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TourAttractionsModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['AreaID']);
                $Data['Data'][$key]['City'] = $TourAreaInfo['CnName'];
            }
            MultiPage($Data, 10);
        }
        include template('TourAttractionsList');
    }

    /**
     * @desc  删除旅游景点
     */
    public function AttractionsDelete()
    {
        if ($_GET) {
            $ID = $_GET['ID'];
            include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAttractionsModule.php';
            $TourAttractionsModule = new TourAttractionsModule();
            $DeleteRecommend = $TourAttractionsModule->DeleteByKeyID($ID);
            $AttractionsInfo = $TourAttractionsModule->GetInfoByKeyID($ID);
            DelFromImgServ($AttractionsInfo['Image']);
            if ($DeleteRecommend) {
                alertandgotopage("删除成功", '/index.php?Module=TourArea&Action=AttractionsList');
            } else {
                alertandgotopage("删除失败", '/index.php?Module=TourArea&Action=AttractionsList');
            }
        }
    }

    /**
     * @desc  行程推荐列表（首页数据调用）
     */
    public function TourStrokeList()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourStrokeModule.php';
        $TourStrokeModule = new TourStrokeModule();
        // 分页开始
        $SqlWhere = '';
        $Page = intval($_GET['Page']);
        $Page = $Page ? $Page : 1;
        $PageSize = 10;
        if ($_GET['Title']) {
            $Title = trim($_GET ['Title']);
            $SqlWhere .= ' and concat(Title) like \'%' . $Title . '%\'';
        }
        $Rscount = $TourStrokeModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TourStrokeModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            MultiPage($Data, 10);
        }
        include template('TourStrokeList');
    }

    /**
     * @desc  添加行程推荐（首页数据调用）
     */
    public function TourStrokeAdd()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourStrokeModule.php';
        $TourStrokeModule = new TourStrokeModule();
        $SqlWhere = '';
        $ID = intval($_GET['ID']);
        $Info = $TourStrokeModule->GetInfoByKeyID($ID);
        if ($_POST) {
            $Data['CityCategory'] = trim($_POST['CityCategory']);
            $Data['Title'] = trim($_POST['Title']);
            $Data['QuoteUrl'] = trim($_POST['QuoteUrl']);
            $Data['R1'] = intval($_POST['R1']);
            $Data['S1'] = intval($_POST['S1']);
            if ($_POST['ID'] > 0) {
                $ID = intval($_POST['ID']);
                $Update = $TourStrokeModule->UpdateInfoByKeyID($Data, $ID);
            } else {
                $ID = $TourStrokeModule->InsertInfo($Data);
            }
            if ($Update || $ID) {
                alertandgotopage("操作成功", '/index.php?Module=TourArea&Action=tourStrokeAdd&ID=' . $ID);
            } else {
                alertandgotopage("操作失败", '/index.php?Module=TourArea&Action=tourStrokeAdd&ID=' . $ID);
            }
        }
        include template('tourStrokeAdd');
    }

    /**
     * @desc  删除行程推荐（首页数据调用）
     */
    public function TourStrokeDelete()
    {
        if ($_GET) {
            $ID = $_GET['ID'];
            include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourStrokeModule.php';
            $TourStrokeModule = new TourStrokeModule();
            $DeleteRecommend = $TourStrokeModule->DeleteByKeyID($ID);
            if ($DeleteRecommend) {
                alertandgotopage("删除成功", '/index.php?Module=TourArea&Action=tourStrokeList');
            } else {
                alertandgotopage("删除失败", '/index.php?Module=TourArea&Action=tourStrokeList');
            }
        }
    }

    /**
     * @desc  通过文档更新景点数据
     */
    public function Updatetxt()
    {
        header('Content-Type:text/html;charset=utf-8');
        $ar = array_map('trim', file(SYSTEM_ROOTPATH . '/include/data.txt'));
        foreach ($ar as $key => $value) {
            $arr[] = explode('|', $value);
        }
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAttractionsModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        $TourAttractionsModule = new TourAttractionsModule();
        foreach ($arr as $key => $value) {
            $Data['AreaID'] = trim($value[0]);
            $Data['AttractionsName'] = mb_convert_encoding($value[1], 'utf-8', 'gbk');
            $Data['EnName'] = mb_convert_encoding($value[2], 'utf-8', 'gbk');
            $Data['Image'] = mb_convert_encoding($value[3], 'utf-8', 'gbk');
            $Data['Description'] = mb_convert_encoding($value[4], 'utf-8', 'gbk');
            $InfoResult = $TourAttractionsModule->GetInfoByWhere(' and CnName = \'' . $Data['AttractionsName'].'\'');
            if ($InfoResult) {
                $Result = $TourAttractionsModule->UpdateInfoByKeyID($Data, $InfoResult['ID']);
            } else {
                $Result = $TourAttractionsModule->InsertInfo($Data);
            }
            if ($Result) {
                echo '操作成功';
            }
        }
    }
    /**
     * @desc  匹配群文件
     */
    public function UpdateQun()
    {
        set_time_limit(0);
        global $DB;
        header('Content-Type:text/html;charset=utf-8');
        for ($i=1;$i<32;$i++){
            $ar = array_map('trim', file(SYSTEM_ROOTPATH . '/qunwenjian/'.$i.'.txt'));
            $ListZZ = '/<a href="(.*)"><\/a>/isU';
            foreach ($ar as $key => $value) {
                if (strstr($value, '<a href="')){
                    preg_match_all($ListZZ, $value, $Return);
                    preg_match_all("/\d+/", $Return[1][0],$arr);
                    $DB->query("BEGIN");//开始事务定义
                    if ($arr[0][0]){
                        $Data['QQ'] = $arr[0][0];
                        $Info = $DB->getone("Select * from tbl_qunwenjian " ." where  QQ = ". $Data['QQ']);
                        if (!$Info['QQ']){
                            $insert = $DB->insertArray('tbl_qunwenjian', $Data);
                            if (!$insert){
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                echo '数据插入失败<br>';

                            }else{
                                $DB->query("COMMIT");//执行事务
                                echo '操作成功<br>';
                            }
                        }else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            echo '该数据存在<br>';
                        }
                    }
                }
            }
        }
    }
}

?>