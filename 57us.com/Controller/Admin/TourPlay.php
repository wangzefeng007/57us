<?php

class TourPlay
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourSupplierModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlayBaseModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlayDetailedModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductCategoryModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlaySkuModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlaySkuPriceModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlayErverDayPriceModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductImageModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderInfoModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderModule.php';
    }

    public function TourPlayList()
    {
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductCategoryModule = new TourProductCategoryModule();
        $TourSupplierModule = new TourSupplierModule();
        $TourAreaModule = new TourAreaModule();
        $TourProductModule = new TourProductModule();
        //批量上下架产品
        if ($_POST['StatusAll']){
            $TourProductIDs ='';
            if ($_POST['button']==1){
                $StatusInfo['Status']  = 1;
            }elseif($_POST['button']==0){
                $StatusInfo['Status']  = 0;
            }
            $StatusAll = $_POST['StatusAll'];
            foreach ($StatusAll as $value){
                $TourProductIDs .= $value.',';
            }
            $TourProductIDs =  substr($TourProductIDs,0,-1);
            $StatusWhere = ' TourProductID in('.$TourProductIDs.')';
            $TourProductModule->UpdateInfoByWhere($StatusInfo,$StatusWhere);
            $TourProductPlaySkuModule = new TourProductPlaySkuModule();
            $TourProductPlaySkuModule->UpdateInfoByWhere($StatusInfo,$StatusWhere);
            $update = $TourProductPlayBaseModule->UpdateInfoByWhere($StatusInfo,$StatusWhere);
            if ($update){
                alertandback('更新成功');
            }else{
                alertandback('更新失败');
            }
        }

        // 供应商
        $TourSupplierlist = $TourSupplierModule->TourSupplierSelect();
        $TourAreaLists = $TourAreaModule->GetInfoByWhere(' and `ParentID` = 0',true);
        foreach ($TourAreaLists as $key => $value) {
            $AreaAddprovince = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$value['AreaID'],true);
            $TourAreaLists[$key]['Province'] = $AreaAddprovince;
            if ($AreaAddprovince) {
                foreach ($AreaAddprovince as $k => $v) {
                    $AreaAddCity = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$v['AreaID'],true);
                    $TourAreaLists[$key]['Province'][$k]['City'] = $AreaAddCity;
                }
            }
        }
        // 类别
        $Tourlist = $TourProductCategoryModule->TourSelectByParent(3);
        $SqlWhere = ' and IsClose=0';
        // 搜索条件
        $PageUrl = '';
        $ProductName = trim($_GET['ProductName']);
        $Category = trim($_GET['Category']);
        $Status = trim($_GET['Status']);
        $SupplierID = trim($_GET['SupplierID']);
        
        $R1 = trim($_GET['R1']);
        $R2 = trim($_GET['R2']);
        $R3 = trim($_GET['R3']);
        $R4 = trim($_GET['R4']);
        $R5 = trim($_GET['R5']);
        
        if ($ProductName != '') {
            $SqlWhere .= ' and (TourProductID=\'' . $ProductName . '\' or GroupNO like \'%' . $ProductName . '%\' or concat(ProductName) like \'%' . $ProductName . '%\')';
            $PageUrl .= '&ProductName=' . $ProductName;
        }
        if ($Category != '') {
            $CategoryList = $TourProductCategoryModule->GetInfoByKeyID($Category);
            $SqlWhere .= ' and Category = ' . $Category;
            $PageUrl .= '&Category=' . $Category;
        }
        if ($Status != '') {
            $SqlWhere .= ' and concat(Status) like \'%' . $Status . '%\'';
            $PageUrl .= '&Status=' . $Status;
        }
        if ($SupplierID != '') {
            $TourSupplier = $TourSupplierModule->GetInfoByKeyID($SupplierID);
            $SqlWhere .= ' and concat(SupplierID) = ' . $SupplierID;
            $PageUrl .= '&SupplierID=' . $SupplierID;
        }
        
        if ($R1 != '') {
            $SqlWhere .= ' and R1=1';
            $PageUrl .= '&R1=1';
        }
        
        if ($R2 != '') {
            $SqlWhere .= ' and R2=1';
            $PageUrl .= '&R2=1';
        }
        
        if ($R3 != '') {
            $SqlWhere .= ' and R3=1';
            $PageUrl .= '&R3=1';
        }
        
        if ($R4 != '') {
            $SqlWhere .= ' and R4=1';
            $PageUrl .= '&R4=1';
        }
        
        if ($R5 != '') {
            $SqlWhere .= ' and R5=1';
            $PageUrl .= '&R5=1';
        }
        // 跳转到该页面
        if ($_POST['page']) {
            $page = $_POST['page'];
            tourl('index.php?Module=TourPlay&Action=TourPlayList&Page=' . $page . $PageUrl);
        }
        
        // 分页开始
        $Page = intval($_GET['Page']);
        $Page = $Page ? $Page : 1;
        $PageSize = 10;
        $Rscount = $TourProductPlayBaseModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TourProductPlayBaseModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $Key => $Value) {
                if ($Value['Category'] > 0) {
                    $TourCategoryInfo = $TourProductCategoryModule->GetInfoByKeyID($Value['Category']);
                    $Data['Data'][$Key]['CnName'] = $TourCategoryInfo['CnName'];
                }
                if ($Value['SupplierID'] > 0) {
                    $TourSupplierInfo = $TourSupplierModule->GetInfoByKeyID($Value['SupplierID']);
                    $Data['Data'][$Key]['SupplierName'] = $TourSupplierInfo['CnName'];
                }
            }
            MultiPage($Data, 10);
        }
        $PageMax = $Data['PageCount']; // 最后一页
        if ($Page >= 1 && $Page < $PageMax) {
            $Next = $Page + 1; // 上一页
        }
        if ($Page > 1 && $Page <= $PageMax) {
            $Previous = $Page - 1; // 下一页
        }
        // 分页结束
        include template('TourProductPlayBaseList');
    }

    public function Add()
    {
        $_GET['Action'] = 'Add';
        $TourProductCategoryModule = new TourProductCategoryModule();
        $TourProductModule = new TourProductModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourSupplierModule = new TourSupplierModule();
        $TourAreaModule = new TourAreaModule();
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID > 0) {
            $ProductInfo = $TourProductPlayBaseModule->GetInfoByTourProductID($TourProductID);
        }
        // 特色主题start
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourSpecialSubjectModule.php';
        $TourSpecialSubjectModule = new TourSpecialSubjectModule();
        $ZuTiCategory = array(
            2,
            3,
            4,
            5
        );
        foreach ($ZuTiCategory as $Val) {
            $ZuTi[$Val] = $TourSpecialSubjectModule->GetInfoByWhere(' and Category = ' . $Val . ' order by Sort Desc', true);
        }
        // 特色主题end
        // 供应商
        $TourSupplierlist = $TourSupplierModule->TourSupplierSelect();
        $TourAreaLists = $TourAreaModule->GetInfoByWhere(' and `ParentID` = 0',true);
        foreach ($TourAreaLists as $key => $value) {
            $AreaAddprovince = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$value['AreaID'],true);
            $TourAreaLists[$key]['Province'] = $AreaAddprovince;
            if ($AreaAddprovince) {
                foreach ($AreaAddprovince as $k => $v) {
                    $AreaAddCity = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$v['AreaID'],true);
                    $TourAreaLists[$key]['Province'][$k]['City'] = $AreaAddCity;
                }
            }
        }
        // 类别
        $Tourlist = $TourProductCategoryModule->TourSelectByParent(3);
        foreach ($Tourlist as $key => $value) {
            $Tourlists = $TourProductCategoryModule->TourSelectByParent($value['TourCategoryID']);
            $Tourlist[$key]['parent'] = $Tourlists;
        }
        if ($_POST) {
            $TourProductID = intval($_POST['TourProductID']);
            $Data['ProductName'] = trim($_POST['ProductName']);
            $Data['Category'] = intval($_POST['Category']);
            $Data['FromIP'] = GetIP();
            $Data['Status'] = trim($_POST['Status']);
            $Data['SupplierID'] = intval($_POST['SupplierID']);
            if ($Data['ProductName'] == '' || $Data['Category'] == '' || $_POST['Keywords'] == '') {
                alertandback('信息填写不完整');
            }
            if ($TourProductID > 0) {
                // 修改
                $Info = $TourProductModule->GetInfoByKeyID($TourProductID);
                if ($Info) {
                    $IsOk = $TourProductModule->UpdateInfoByKeyID($Data, $TourProductID);
                } else {
                    $Data['TourProductID'] = $TourProductModule->InsertInfo($Data);
                }
                $Data['TourProductID'] = $TourProductID;
            } else {
                // 添加
                $Data['TourProductID'] = $TourProductModule->InsertInfo($Data);
            }
            unset($Data['FromIP']);
            $Data['Departure'] = intval($_POST['Departure']);
            $Data['AddTime'] = date('Y-m-d H:i:s', time());
            $Data['ProductSimpleName'] = trim($_POST['ProductSimpleName']);
            $Data['TagInfo'] = trim($_POST['TagInfo']);
            $Data['City'] = trim($_POST['City']); // 所在城市
            $Data['GroupNO'] = trim($_POST['GroupNO']); // 所在城市
            $Data['Address'] = trim($_POST['Address']); // 消费地址
            $Data['AdvanceDays'] = trim($_POST['AdvanceDays']);
            $Data['Times'] = trim($_POST['Times']);
            $Data['Longitude'] = trim($_POST['Longitude']);
            $Data['Latitude'] = trim($_POST['Latitude']);
            $Data['ServiceLanguage'] = trim($_POST['ServiceLanguage']);
            $Data['Keywords'] = trim($_POST['Keywords']);
            $Data['GroupNO'] = trim($_POST['GroupNO']);
            $Data['R1'] = intval($_POST['R1']);
            $Data['S1'] = intval($_POST['S1']);
            $Data['R2'] = intval($_POST['R2']);
            $Data['S2'] = intval($_POST['S2']);
            $Data['R3'] = intval($_POST['R3']);
            $Data['S3'] = intval($_POST['S3']);
            $Data['R4'] = intval($_POST['R4']);
            $Data['S4'] = intval($_POST['S4']);
            if ($Data['R3']>0||$Data['S3']>0||$Data['R4']>0||$Data['S4']>0){
                if ($Info['Status']==0)
                alertandback('下架产品，不可推荐！');
            }
            // 主题处理START
            $Features = $_POST['Features'];
            if ($Data['Category'] == 9) {
                $ZuTiCategory = 2;
            } elseif ($Data['Category'] == 8) {//8=门票
                $ZuTiCategory = 3;
            }elseif ($Data['Category'] == 7) {//7=城市通票
                $ZuTiCategory = 3;
            } elseif ($Data['Category'] == 6) {//特色体验
                $ZuTiCategory = 4;
            }elseif($Data['Category'] == 22){//wifi
                $ZuTiCategory = 5;
            }elseif($Data['Category'] == 21){//接送机
                $ZuTiCategory = '';
            }
            if ($ZuTiCategory != ''){
                $ZuTi = $TourSpecialSubjectModule->GetInfoByWhere(' and Category = ' . $ZuTiCategory, true);
                foreach ($ZuTi as $key => $value) {
                    $ZuTiArray[$key] = $value['TourSpecialSubjectID'];
                }
                $FeaturesArray = array_intersect($Features, $ZuTiArray);
                $FeaturesString = '';
                foreach ($FeaturesArray as $K => $Val) {
                    $FeaturesString .= $Val . ',';
                }
                $Data['Features'] = substr($FeaturesString, 0, - 1);
            }
            // 主题处理END
            if ($TourProductID > 0) {
                // 修改
                $IsOkOk = $TourProductPlayBaseModule->UpdateByTourProductID($Data, $TourProductID);
            } else {
                // 添加
                $TourlineAdd = $TourProductPlayBaseModule->InsertInfo($Data);
            }
            if ($TourlineAdd || $IsOkOk) {
                alertandgotopage('操作成功', '/index.php?Module=TourPlay&Action=Add&TourProductID=' . $Data['TourProductID']);
            } else {
                alertandgotopage('操作失败', '/index.php?Module=TourPlay&Action=Add&TourProductID=' . $Data['TourProductID']);
            }
        }
        include template('TourProductPlayAdd');
    }

    public function DeletePlay()
    {
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            alertandback("参数错误");
        }
        $Data['IsClose'] = 1;
        $TourProductModule = new TourProductModule();
        $UpDataTourProduct = $TourProductModule->UpdateInfoByWhere($Data, ' TourProductID =' . $TourProductID);
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $UpDataTourProductLine = $TourProductPlayBaseModule->UpdateInfoByWhere($Data, ' TourProductID =' . $TourProductID);
        $TourProductPlaySkuModule = new TourProductPlaySkuModule();
        $Data['Status'] = 0;
        $UpDataTourProductLineSku = $TourProductPlaySkuModule->UpdateInfoByWhere($Data, ' TourProductID =' . $TourProductID);
        alertandback("操作成功");
    }

    public function SetPlayDescription()
    {
        $_GET['Action'] = 'SetPlayDescription';
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductPlayDetailedModule = new TourProductPlayDetailedModule();
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourPlay&Action=SetPlayDescription');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['DesTitle'] as $Key => $Value) {
                if ($POST['DesContent'.$Key] != '') {
                $UpdateInfo['DesTitle'][$SK] = $Value;
                // 文本图片处理-----------------------------------------------------------------------------
                $UpdateInfo['DesScc'][$SK] = $POST['DesScc' . $Key];
                $UpdateInfo['DesContent'][$SK] = $POST['DesContent' . $Key];
                $Pattern = array();
                $Replacement = array();
                $ImgArr = Array();
                preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($UpdateInfo['DesContent'][$SK]), $ImgArr);
                if (count($ImgArr[0])) {
                    foreach ($ImgArr[0] as $ImgTag) {
                        $Pattern[] = $ImgTag;
                        $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                            '/title=".*"/iU',
                            '/alt=".*"/iU'
                        ), '', $ImgTag));
                    }
                }
                $UpdateInfo['DesContent'][$SK] = str_replace($Pattern, $Replacement, stripcslashes($UpdateInfo['DesContent'][$SK]));
                // 文本图片处理-------------------------------------------------------------------------------
                $SK++;
            }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['Description'] = addslashes($UpdateString);
            $info = $TourProductPlayDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductPlayDetailedModule->UpdateInfoByWhere($UpdateData, ' TourProductID = ' . $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductPlayDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourPlay&Action=SetPlayDescription&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductPlayDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['Description'] != '') {
            $NewContentArray = json_decode($NewContentInfo['Description'], true);
            $NewContentArray['DesContent'] = $this->DoContent($NewContentArray['DesContent']);
        }
        $I = count($NewContentArray['DesTitle']) + 1;
        include template('SetPlayDescription');
    }
    // 处理编辑器内容不能显示
    private function DoContent($String = '')
    {
        if (is_array($String)) {
            // 只适合二维数组
            foreach ($String as $K => $Val) {
                $String[$K] = str_replace(array(
                    '<div>',
                    '</div>',
                    '<ul>',
                    '<li>',
                    '</ul>',
                    '</li>'
                ), array(
                    '<p>',
                    '</p>',
                    '',
                    '',
                    '',
                    ''
                ), $String[$K]);
                $String[$K] = str_replace(array(
                    "\r\n",
                    "\r",
                    "\n"
                ), "", $String[$K]);
            }
        } else {
            $String = str_replace(array(
                '<div>',
                '</div>',
                '<ul>',
                '<li>',
                '</ul>',
                '</li>'
            ), array(
                '<p>',
                '</p>',
                '',
                '',
                '',
                ''
            ), $String);
            $String = str_replace(array(
                "\r\n",
                "\r",
                "\n"
            ), "", $String);
        }
        return $String;
    }
    // 设置预订须知
    public function SetPlayBookingPolicy()
    {
        $_GET['Action'] = 'SetPlayBookingPolicy';
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductPlayDetailedModule = new TourProductPlayDetailedModule();
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourPlay&Action=SetPlayBookingPolicy');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['BookTitle'] as $Key => $Value) {
                if ($POST['BookContent'.$Key] != '') {
                    $UpdateInfo['BookTitle'][$SK] = $Value;
                    // 文本图片处理-----------------------------------------------------------------------------
                    $UpdateInfo['BookCss'][$SK] = $POST['BookCss' . $Key];
                    $UpdateInfo['BookContent'][$SK] = $POST['BookContent' . $Key];
                    $Pattern = array();
                    $Replacement = array();
                    $ImgArr = Array();
                    preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($UpdateInfo['BookContent'][$SK]), $ImgArr);
                    if (count($ImgArr[0])) {
                        foreach ($ImgArr[0] as $ImgTag) {
                            $Pattern[] = $ImgTag;
                            $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                                '/title=".*"/iU',
                                '/alt=".*"/iU'
                            ), '', $ImgTag));
                        }
                    }
                    $UpdateInfo['BookContent'][$SK] = str_replace($Pattern, $Replacement, stripcslashes($UpdateInfo['BookContent'][$SK]));
                    // 文本图片处理-------------------------------------------------------------------------------
                    $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['BookingPolicy'] = addslashes($UpdateString);
            $info = $TourProductPlayDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductPlayDetailedModule->UpdateInfoByWhere($UpdateData, ' TourProductID = ' . $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductPlayDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourPlay&Action=SetPlayBookingPolicy&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductPlayDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['BookingPolicy'] != '') {
            $NewContentArray = json_decode($NewContentInfo['BookingPolicy'], true);
            $NewContentArray['BookContent'] = $this->DoContent($NewContentArray['BookContent']);
        }
        $I = count($NewContentArray['BookTitle']) + 1;
        include template('SetPlayBookingPolicy');
    }
    // 设置消费须知
    public function SetPlayConsumerNotice()
    {
        $_GET['Action'] = 'SetPlayConsumerNotice';
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductPlayDetailedModule = new TourProductPlayDetailedModule();
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourPlay&Action=SetPlayConsumerNotice');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['ConTitle'] as $Key => $Value) {
                if ($POST['ConContent' . $Key]!=''){
                    $UpdateInfo['ConTitle'][$SK] = $Value;
                    // 文本图片处理-----------------------------------------------------------------------------
                    $UpdateInfo['ConCss'][$SK] = $POST['ConCss' . $Key];
                    $UpdateInfo['ConContent'][$SK] = $POST['ConContent' . $Key];
                    $Pattern = array();
                    $Replacement = array();
                    $ImgArr = Array();
                    preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($UpdateInfo['ConContent'][$SK]), $ImgArr);
                    if (count($ImgArr[0])) {
                        foreach ($ImgArr[0] as $ImgTag) {
                            $Pattern[] = $ImgTag;
                            $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                                '/title=".*"/iU',
                                '/alt=".*"/iU'
                            ), '', $ImgTag));
                        }
                    }
                    $UpdateInfo['ConContent'][$SK] = str_replace($Pattern, $Replacement, stripcslashes($UpdateInfo['ConContent'][$SK]));
                    // 文本图片处理-------------------------------------------------------------------------------
                $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['ConsumerNotice'] = addslashes($UpdateString);
            $info = $TourProductPlayDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductPlayDetailedModule->UpdateInfoByWhere($UpdateData, ' TourProductID = ' . $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductPlayDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourPlay&Action=SetPlayConsumerNotice&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductPlayDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['ConsumerNotice'] != '') {
            $NewContentArray = json_decode($NewContentInfo['ConsumerNotice'], true);
            $NewContentArray['ConContent'] = $this->DoContent($NewContentArray['ConContent']);
        }
        $I = count($NewContentArray['ConTitle']) + 1;
        include template('SetPlayConsumerNotice');
    }

    public function SetPlayVisaInformation()
    {
        $_GET['Action'] = 'SetPlayVisaInformation';
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductPlayDetailedModule = new TourProductPlayDetailedModule();
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourPlay&Action=SetPlayVisaInformation');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['ExpTitle'] as $Key => $Value) {
                if ($POST['ExpContent' . $Key]!=''){
                    $UpdateInfo['ExpTitle'][$SK] = $Value;
                    // 文本图片处理-----------------------------------------------------------------------------
                    $UpdateInfo['ExpCss'][$SK] = $POST['ExpCss' . $Key];
                    $UpdateInfo['ExpContent'][$SK] = $POST['ExpContent' . $Key];
                    $Pattern = array();
                    $Replacement = array();
                    $ImgArr = Array();
                    preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($UpdateInfo['ExpContent'][$SK]), $ImgArr);
                    if (count($ImgArr[0])) {
                        foreach ($ImgArr[0] as $ImgTag) {
                            $Pattern[] = $ImgTag;
                            $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                                '/title=".*"/iU',
                                '/alt=".*"/iU'
                            ), '', $ImgTag));
                        }
                    }
                    $UpdateInfo['ExpContent'][$SK] = str_replace($Pattern, $Replacement, stripcslashes($UpdateInfo['ExpContent'][$SK]));
                    // 文本图片处理-------------------------------------------------------------------------------
                $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['Explanation'] = addslashes($UpdateString);
            $info = $TourProductPlayDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductPlayDetailedModule->UpdateInfoByWhere($UpdateData, ' TourProductID = ' . $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductPlayDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourPlay&Action=SetPlayVisaInformation&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductPlayDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['Explanation'] != '') {
            $NewContentArray = json_decode($NewContentInfo['Explanation'], true);
            $NewContentArray['ExpContent'] = $this->DoContent($NewContentArray['ExpContent']);
        }
        $I = count($NewContentArray['ExpTitle']) + 1;
        include template('SetPlayVisaInformation');
    }

    public function SetPlayTimeline()
    {
        $_GET['Action'] = 'SetPlayTimeline';
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductPlayDetailedModule = new TourProductPlayDetailedModule();
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourPlay&Action=SetPlayTimeline');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['TimesTitle'] as $Key => $Value) {
                if ($POST['TimesContent' . $Key]!=''){
                    $UpdateInfo['TimesTitle'][$SK] = $Value;
                    // 文本图片处理-----------------------------------------------------------------------------
                    $UpdateInfo['Times'][$SK] = $POST['Times'][$Key];
                    $UpdateInfo['TimesContent'][$SK] = $POST['TimesContent' . $Key];
                    $Pattern = array();
                    $Replacement = array();
                    $ImgArr = Array();
                    preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($UpdateInfo['TimesContent'][$SK]), $ImgArr);
                    if (count($ImgArr[0])) {
                        foreach ($ImgArr[0] as $ImgTag) {
                            $Pattern[] = $ImgTag;
                            $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                                '/title=".*"/iU',
                                '/alt=".*"/iU'
                            ), '', $ImgTag));
                        }
                    }
                    $UpdateInfo['TimesContent'][$SK] = str_replace($Pattern, $Replacement, stripcslashes($UpdateInfo['TimesContent'][$SK]));
                    // 文本图片处理-------------------------------------------------------------------------------
                $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['TimeInfo'] = addslashes($UpdateString);
            $info = $TourProductPlayDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductPlayDetailedModule->UpdateInfoByWhere($UpdateData, ' TourProductID = ' . $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductPlayDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourPlay&Action=SetPlayTimeline&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductPlayDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['TimeInfo'] != '')
            $NewContentArray = json_decode($NewContentInfo['TimeInfo'], true);
        $I = count($NewContentArray['TimesTitle']) + 1;
        include template('SetPlayTimeline');
    }
    // 设置图片
    public function TourProductPlayImages()
    {
        $_GET['Action'] = 'TourProductPlayImages';
        $TourProductID = intval($_GET['TourProductID']);
        //unset($_SESSION['TourProductID']);
        //$_SESSION['TourProductID'] = $TourProductID;
        $TourProductImageModule = new TourProductImageModule();
        $TourImagesList = $TourProductImageModule->GetListsByTourProductID($TourProductID);
        include template('TourProductPlayImages');
    }
    
    // 删除图片
    public function DeleteTourPlayImages()
    {
        $TourProductImageModule = new TourProductImageModule();
        $TourProductID =  intval($_GET['TourProductID']);
        $ImageID = intval($_GET['ImageID']);
        if ($TourProductID >0){
            $Lists = $TourProductImageModule->GetListsByTourProductID($TourProductID);
            foreach ($Lists as $key=>$value){
                if ($value['ImageUrl'] ){
                    DelFromImgServ($value['ImageUrl']);
                }
            }
            $IsOk =  $TourProductImageModule->DeleteByWhere(' and TourProductID = '.$TourProductID);
            if ($IsOk) {
                alertandback("操作成功");
            } else {
                alertandback("操作失败");
            }
        }
        if ($ImageID == 0) {
            alertandback("参数错误");
        }

        $TourImagesInfo = $TourProductImageModule->GetInfoByKeyID($ImageID);
        if (empty($TourImagesInfo)) {
            alertandback("参数错误");
        }
        // 删除图片文件
        if ($TourImagesInfo['ImageUrl'])
            DelFromImgServ($TourImagesInfo['ImageUrl']);
            // 删除数据库
        $IsOk = $TourProductImageModule->DeleteByKeyID($ImageID);
        if ($IsOk) {
            alertandback("操作成功");
        } else {
            alertandback("操作失败");
        }
    }

    public function TourPlayImagesSetDefault()
    {
        $ImageID = intval($_GET['ImageID']);
        if ($ImageID == 0) {
            alertandback("参数错误");
        }
        $TourProductImageModule = new TourProductImageModule();
        $TourImagesInfo = $TourProductImageModule->GetInfoByKeyID($ImageID);
        if (empty($TourImagesInfo)) {
            alertandback("参数错误");
        }
        $UpdateData['IsDefault'] = 0;
        $TourProductImageModule->UpdateInfoByTourProductID($UpdateData, $TourImagesInfo['TourProductID']);
        $UpdateInfo['IsDefault'] = 1;
        $IsOk = $TourProductImageModule->UpdateInfoByKeyID($UpdateInfo, $ImageID);
        if ($IsOk) {
            alertandback("操作成功");
        } else {
            alertandback("操作失败");
        }
    }
    // SKU列表 设置价格
    public function TourProductPlaySkuList()
    {
        $_GET['Action'] = 'TourProductPlaySkuList';
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductPlaySkuModule = new TourProductPlaySkuModule();
        $SkuLists = $TourProductPlaySkuModule->GetInfoByWhere(' and TourProductID=' . $TourProductID,true);
        include template('TourProductPlaySkuList');
    }

    public function TourProductPlaySkuEdit()
    {
        $TourProductID = intval($_GET['TourProductID']);
        $ProductSkuID = intval($_GET['ProductSkuID']);
        $TourAreaModule = new TourAreaModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductPlaySkuModule = new TourProductPlaySkuModule();
        $ProductSku = $TourProductPlaySkuModule->GetInfoByKeyID($ProductSkuID);
        if ($_POST) {
            $TourProductID = intval($_POST['TourProductID']);
            $ProductSkuID = intval($_POST['ProductSkuID']);
            $TourProductPlayBaseInfo = $TourProductPlayBaseModule->GetInfoByWhere(' and TourProductID =' . $TourProductID);
            if ($TourProductPlayBaseInfo['City'] == '') {
                alertandback("产品缺少目的地,不能设置SKU！");
            }
            if ($TourProductPlayBaseInfo['Category'] == '') {
                alertandback("产品缺少产品类别,不能设置SKU！");
            }
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($TourProductPlayBaseInfo['City']);
            $Data['TourProductID'] = intval($TourProductID);
            $Data['SKUName'] = trim($_POST['SKUName']);
            if ($Data['SKUName'] == '') {
                alertandback("SKU名称不能为空！");
            }
            $Data['Status'] = intval($_POST['Status']);
            $Data['IsNeedHotel'] = intval($_POST['IsNeedHotel']);
            $Data['IsNeedGiveAirport'] = intval($_POST['IsNeedGiveAirport']);
            $Data['IsNeedSendAirport'] = intval($_POST['IsNeedSendAirport']);
            $MoreSqlWhere = '';
            if ($ProductSkuID > 0) {
                $MoreSqlWhere .= ' and ProductSkuID!=' . $ProductSkuID;
            } else {
                // SKU名称生成
                $CategoryID = $TourProductPlayBaseInfo['Category'];
                if ($TourProductPlayBaseInfo['Category'] < 10) {
                    $CategoryID = '0' . $TourProductPlayBaseInfo['Category'];
                }
                $Data['SkuNO'] = $TourAreaInfo['ShorEnName'] . $CategoryID . $TourProductID . rand(100, 999);
            }
            // 判断名称不能重复，SKU名称生成
            $ProductLineSkuInfo = $TourProductPlaySkuModule->GetInfoByWhere(' and SKUName=\'' . $Data['SKUName'] . '\'' . $MoreSqlWhere . ' and ProductSkuID!=' . $ProductSkuID);
            if (! empty($ProductLineSkuInfo)) {
                alertandback("SKU名称已经存在");
            }
            if ($ProductSkuID > 0) {
                $IsOK = $TourProductPlaySkuModule->UpdateInfoByKeyID($Data, $ProductSkuID);
                // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
                /* if ($IsOK) {
                    $this->UpdatePriceAdnOrder($ProductLineSkuInfo['TourProductID'], $ProductLineSkuInfo['ProductSkuID']);
                } */
            } else {
                $ProductSkuID = $TourProductPlaySkuModule->InsertInfo($Data);
            }
            if ($IsOK || $ProductSkuID) {
                alertandgotopage("操作成功", '/index.php?Module=TourPlay&Action=TourProductPlaySkuList&TourProductID=' . $TourProductID);
            } else {
                alertandgotopage("未做操作", '/index.php?Module=TourPlay&Action=TourProductPlaySkuList&TourProductID=' . $TourProductID);
            }
        }
        include template('TourProductPlaySkuEdit');
    }
    // 设置SKU装填
    public function TourProductPlaySkuStatus()
    {
        $ProductSkuID = intval($_GET['ProductSkuID']);
        $Status = trim($_GET['Status']);
        $TourProductPlaySkuModule = new TourProductPlaySkuModule();
        if ($ProductSkuID > 0 && $Status != '') {
            $Data['Status'] = $Status;
            $IsOk = $TourProductPlaySkuModule->UpdateInfoByKeyID($Data, $ProductSkuID);
        }
        if ($IsOk) {
            $ProductPlaySkuInfo = $TourProductPlaySkuModule->GetInfoByKeyID($ProductSkuID);
            // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
            //$this->UpdatePriceAdnOrder($ProductPlaySkuInfo['TourProductID'], $ProductPlaySkuInfo['ProductSkuID']);
            alertandback("操作成功");
        } else {
            alertandback('操作失败');
        }
    }

    // 设置SKU价格
    public function TourProductPlaySkuPriceList()
    {
        $TourProductPlaySkuModule = new TourProductPlaySkuModule();
        $TourProductPlaySkuPriceModule = new TourProductPlaySkuPriceModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductPlayErverDayPriceModule = new TourProductPlayErverDayPriceModule();
        $ProductSkuID = intval($_GET['ProductSkuID']);
        $ProductPlaySkuInfo = $TourProductPlaySkuModule->GetInfoByKeyID($ProductSkuID);
        $TourProductID = $ProductPlaySkuInfo['TourProductID'];
        $SKUPriceList = $TourProductPlaySkuPriceModule->GetInfoByWhere(' and ProductSkuID = ' . $ProductSkuID, true);
        $TourPricetID = intval($_GET['TourPricetID']);
        if ($TourPricetID)
            $SkuPriceInfo = $TourProductPlaySkuPriceModule->GetInfoByKeyID($TourPricetID);
        if ($ProductSkuID == 0) {
            alertandback('操作失败');
        }
        if ($_POST) {
            if ($_POST['startDate'] == '' || $_POST['endDate'] == '') {
                alertandback('缺少时间，操作失败!');
            }
            if ($_POST['Price'] == '') {
                alertandback('缺少优惠价，操作失败!');
            }
            $TourPricetID = intval($_POST['TourPricetID']);
            $Date['ProductSkuID'] = intval($_POST['ProductSkuID']);
            $Date['TourProductID'] = intval($_POST['TourProductID']);
            $Date['StartDate'] = trim($_POST['startDate']);
            $Date['EndDate'] = trim($_POST['endDate']);
            $Date['Monday'] = intval($_POST['Monday']);
            $Date['Tuesday'] = intval($_POST['Tuesday']);
            $Date['Wednesday'] = intval($_POST['Wednesday']);
            $Date['Thursday'] = intval($_POST['Thursday']);
            $Date['Friday'] = intval($_POST['Friday']);
            $Date['Saturday'] = intval($_POST['Saturday']);
            $Date['Sunday'] = intval($_POST['Sunday']);
            $Date['Price'] = trim($_POST['Price']);
            $Date['MarketPrice'] = trim($_POST['MarketPrice']);
            $Date['PurchasePrice'] = trim($_POST['PurchasePrice']);
            $Date['SellPrice'] = trim($_POST['Price']);
            $Date['Profit'] = trim($Date['SellPrice'] - $Date['PurchasePrice']);
            $InventoryType = intval($_POST['InventoryType']);
            
            $IsUpdate = $this->IsUpdate($Date, $TourPricetID);
            if ($IsUpdate) {
                alertandback('更新时间重复，更新失败，请查询后重新提交操作！');
            }
            if ($TourPricetID > 0) {
                // 修改
                $IsOk = $TourProductPlaySkuPriceModule->UpdateInfoByKeyID($Date, $TourPricetID);
            } else {
                if ($InventoryType == 1) {
                    $Date['ErveryDayInventory'] = '-1';
                } else {
                    $Date['ErveryDayInventory'] = trim($_POST['ErveryDayInventory']);
                }
                // 添加
                $IsOk = $TourProductPlaySkuPriceModule->InsertInfo($Date);
            }
            // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
            //$this->UpdatePriceAdnOrder($Date['TourProductID'], $Date['ProductSkuID']);
            alertandback("操作成功");
        }
        include template('TourProductPlaySkuPriceList');
    }
    // 删除价格记录
    public function DeleteTourProductPlaySkuInfo()
    {
        $TourPricetID = intval($_GET['TourPricetID']);
        $TourProductPlayErverDayPriceModule = new TourProductPlayErverDayPriceModule();
        $TourProductPlaySkuPriceModule = new TourProductPlaySkuPriceModule();
        $SkuPriceInfo = $TourProductPlaySkuPriceModule->GetInfoByKeyID($TourPricetID);
        if (empty($SkuPriceInfo)) {
            alertandback("参数错误!");
        }
        $IsOk = $TourProductPlaySkuPriceModule->DeleteByKeyID($TourPricetID);
        if ($IsOk) {
            // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
            //$this->UpdatePriceAdnOrder($SkuPriceInfo['TourProductID'], $SkuPriceInfo['ProductSkuID']);
            alertandback("操作成功");
        } else {
            alertandback("操作失败");
        }
    }
    // 判断价格能不能更新
    private function IsUpdate($Date = array(), $TourPricetID = '')
    {
        $TourProductPlayErverDayPriceModule = new TourProductPlayErverDayPriceModule();
        $TwoDays = $this->DiffBetweenTwoDays($Date['StartDate'], $Date['EndDate']);
        for ($I = 0; $I <= $TwoDays; $I ++) {
            $StartDate = $Date['StartDate'];
            $StartDate = date("Y-m-d", strtotime("+$I day", strtotime($StartDate)));
            $ThisXingQiString = ',';
            if ($Date['Monday'] == 1)
                $ThisXingQiString .= '1,';
            if ($Date['Tuesday'] == 1)
                $ThisXingQiString .= '2,';
            if ($Date['Wednesday'] == 1)
                $ThisXingQiString .= '3,';
            if ($Date['Thursday'] == 1)
                $ThisXingQiString .= '4,';
            if ($Date['Friday'] == 1)
                $ThisXingQiString .= '5,';
            if ($Date['Saturday'] == 1)
                $ThisXingQiString .= '6,';
            if ($Date['Sunday'] == 1)
                $ThisXingQiString .= '0,';
            $ThisXingQi = date('w', strtotime($StartDate));
            if (strstr($ThisXingQiString, $ThisXingQi)) {
                $PriceInfo = $TourProductPlayErverDayPriceModule->GetInfoByWhere(' and TourPricetID != ' . $TourPricetID . ' and ProductSkuID=' . $Date['ProductSkuID'] . ' and Date=\'' . date("Ymd", strtotime($StartDate)) . '\'');
                if (! empty($PriceInfo)) {
                    // 不能添加
                    return 1;
                }
            }
        }
        return 0;
    }
    // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
    private function UpdatePriceAdnOrder($TourProductID = 0, $ProductSkuID = 0)
    {
        // 更新产品每日价格
        $delete = $this->UpdateLineErverDayPrice($ProductSkuID);
        // 更新产品基础表信息
        $this->UpdateLinePriceCache($TourProductID);
        // 更新产品未支付订单是否过期
        $this->UpdateOrderCloseRemarks($TourProductID, $ProductSkuID);
    }
    // 更新产品价格改变的订单，设置为关闭的订单
    private function UpdateOrderCloseRemarks($TourProductID = 0, $ProductSkuID = 0)
    {
        if ($TourProductID == 0 || $ProductSkuID == 0) {
            return '';
        }
        $TourProductOrderModule = new TourProductOrderModule();
        $TourProductOrderInfoModule = new TourProductOrderInfoModule();
        $TourProductPlayErverDayPriceModule = new TourProductPlayErverDayPriceModule();
        $OrderLists = $TourProductOrderModule->GetInfoByWhere(' and Status=1', true);
        
        foreach ($OrderLists as $Value) {
            $OrderInfoLists = $TourProductOrderInfoModule->GetInfoByWhere(' and `OrderNumber` =\'%' . $Value['OrderNumber'] . '%\'', true);
            foreach ($OrderInfoLists as $Val) {
                $ErverDayPriceInfo = $TourProductPlayErverDayPriceModule->GetInfoByWhere(' and ProductSkuID=' . $Val['TourProductSkuID'] . ' and `Date`=\'' . date("Ymd", strtotime($Val['Depart'])) . '\'');
                if (empty($ErverDayPriceInfo)) {
                    // 该日期产品禁止购买，关闭订单！
                    $Data['ExpirationTime'] = date("Y-m-d H:i:s");
                    $Data['Status'] = 12;
                    $TourProductOrderModule->UpdateInfoByWhere($Data, ' and OrderNumber =' . $Val['OrderNumber']);
                } elseif ($Val['UnitPrice'] != $ErverDayPriceInfo['Price']) {
                    // 价格变化，关闭订单！
                    $Data['ExpirationTime'] = date("Y-m-d H:i:s");
                    $Data['Status'] = 11;
                    $TourProductOrderModule->UpdateInfoByWhere($Data, ' and OrderNumber =' . $Val['OrderNumber']);
                    // 更新库存(不存在购物车，如果有购物车需要判断产品类型在更新库存)
                    $UpdateErverDayPriceInfo['Inventory'] = $ErverDayPriceInfo['Inventory'] + 1;
                    $TourProductPlayErverDayPriceModule->UpdateInfoByKeyID($UpdateErverDayPriceInfo, $ErverDayPriceInfo['DayPriceID']);
                }
            }
        }
    }
    // 更新产品基础表信息
    private function UpdateLinePriceCache($TourProductID = 0)
    {
        if ($TourProductID == 0) {
            return '';
        }
        // 更新产品最低价\出团月份
        $TourProductPlaySkuPriceModule = new TourProductPlaySkuPriceModule();
        $RsPrice = $TourProductPlaySkuPriceModule->GetInfoByWhere(' and `TourProductID` =' . $TourProductID . ' order by Price asc');
        $RsMarketPrice = $TourProductPlaySkuPriceModule->GetInfoByWhere(' and `TourProductID` =' . $TourProductID . ' order by MarketPrice asc');
        $Data['LowPrice'] = $RsPrice['Price'];
        $Data['LowMarketPrice'] = $RsMarketPrice['MarketPrice'];
        $MonthString = '';
        $TourProductPlayErverDayPriceModule = new TourProductPlayErverDayPriceModule();
        for ($I = 0; $I < 12; $I ++) {
            $Month = date("Ym", strtotime("+" . $I . " month", time()));
            $IsRs = $TourProductPlayErverDayPriceModule->GetInfoByWhere(' and `TourProductID` =' . $TourProductID . ' and `Date` like \'' . $Month . '%\'');
            if (! empty($IsRs)) {
                $MonthString .= ',' . $Month;
            }
        }
        $Data['Month'] = substr($MonthString, 1);
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductPlayBaseModule->UpdateByTourProductID($Data, $TourProductID);
    }
    // 更新每日价格
    private function UpdateLineErverDayPrice($ProductSkuID = '')
    {
        if ($ProductSkuID == '') {
            return '';
        }
        $ToDayInt = date("Ymd");
        $TourProductPlayErverDayPriceModule = new TourProductPlayErverDayPriceModule();
        $TourProductPlaySkuPriceModule = new TourProductPlaySkuPriceModule();
        $TourProductPlaySkuModule = new TourProductPlaySkuModule();
        $TourProductPlaySkuInfo = $TourProductPlaySkuModule->GetInfoByKeyID($ProductSkuID);
        if (empty($TourProductPlaySkuInfo) || $TourProductPlaySkuInfo['Status'] == 0) {
            // SKU不存在或者被禁用，则不必更新缓存
            return '';
        }
        // SKU价格列表
        $SKUPriceList = $TourProductPlaySkuPriceModule->GetInfoByWhere(' and ProductSkuID=' . $ProductSkuID, true);
        $DayPriceIDString = '';
        foreach ($SKUPriceList as $Value) {
            $TwoDays = $this->DiffBetweenTwoDays($Value['StartDate'], $Value['EndDate']);
            for ($I = 0; $I <= $TwoDays; $I ++) {
                $StartDate = $Value['StartDate'];
                $StartDate = date("Y-m-d", strtotime("+$I day", strtotime($StartDate)));
                $ThisXingQiString = ',';
                if ($Value['Monday'] == 1)
                    $ThisXingQiString .= '1,';
                if ($Value['Tuesday'] == 1)
                    $ThisXingQiString .= '2,';
                if ($Value['Wednesday'] == 1)
                    $ThisXingQiString .= '3,';
                if ($Value['Thursday'] == 1)
                    $ThisXingQiString .= '4,';
                if ($Value['Friday'] == 1)
                    $ThisXingQiString .= '5,';
                if ($Value['Saturday'] == 1)
                    $ThisXingQiString .= '6,';
                if ($Value['Sunday'] == 1)
                    $ThisXingQiString .= '0,';
                $ThisXingQi = date('w', strtotime($StartDate));
                if (strstr($ThisXingQiString, $ThisXingQi)) {
                    $InsertInfo['UpdateTime'] = date("Y-m-d H:i:s");
                    $InsertInfo['TourPricetID'] = $Value['TourPricetID'];
                    $InsertInfo['Price'] = $Value['Price'];
                    $InsertInfo['MarketPrice'] = $Value['MarketPrice'];
                    $Date = date("Ymd", strtotime($StartDate));
                    $ThisDayInfo = $TourProductPlayErverDayPriceModule->GetInfoByWhere(" and ProductSkuID=" . $ProductSkuID . " and Date='" . $Date . "'");
                    if (! empty($ThisDayInfo)) {
                        // 更新
                        $TourProductPlayErverDayPriceModule->UpdateInfoByKeyID($InsertInfo, $ThisDayInfo['DayPriceID']);
                        $DayPriceIDString .= ',' . $ThisDayInfo['DayPriceID'];
                    } else {
                        // 新增
                        $InsertInfo['TourProductID'] = $Value['TourProductID'];
                        $InsertInfo['ProductSkuID'] = $Value['ProductSkuID'];
                        $InsertInfo['Date'] = $Date;
                        $InsertInfo['Inventory'] = $Value['ErveryDayInventory'];
                        $DayPriceID = $TourProductPlayErverDayPriceModule->InsertInfo($InsertInfo);
                        $DayPriceIDString .= ',' . $DayPriceID;
                    }
                }
            }
        }
        
        // 删除存在的日期
        $DayPriceIDString = substr($DayPriceIDString, 1); // 去掉第一个字符，
        if ($DayPriceIDString) {
            $MysqlWhere = ' and `DayPriceID` not in(' . $DayPriceIDString . ')';
        }
        $TourProductPlayErverDayPriceModule->DeleteByWhere(' and ProductSkuID=' . $ProductSkuID . $MysqlWhere); // 删除这个SKU下不包含所更新的日期。
    }

    private function DiffBetweenTwoDays($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }
}