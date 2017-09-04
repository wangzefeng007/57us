<?php

class TourLine
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourSupplierModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductCategoryModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineSkuModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineErverDayPriceModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineSkuPriceModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductImageModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineDetailedModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderInfoModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderModule.php';
        
        
    }
    // 克隆新产品
    public function Copy()
    {
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            alertandback("操作失败");
        }
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineDetailedModule = new TourProductLineDetailedModule();
        $TourProductImageModule = new TourProductImageModule();
        $TourProductModule = new TourProductModule();
        // 查产品总表并添加
        $TourProductInfo = $TourProductModule->GetInfoByKeyID($TourProductID);
        unset($TourProductInfo['TourProductID']);
        $NewTourProductID = $TourProductModule->InsertInfo($TourProductInfo);
        if ($NewTourProductID > 0) {
            // 查基础表并添加
            $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
            if (intval($TourProductLineInfo['RelationProductID']) == 0) {
                $TourProductLineInfo['RelationProductID'] = $TourProductID;
            }
            $TourProductLineInfo['TourProductID'] = $NewTourProductID;
            $TourProductLineInfo['AddTime'] = date("Y-m-d H:i:s");
            unset($TourProductLineInfo['TourProductLineID'], $TourProductLineInfo['Month'], $TourProductLineInfo['LowPrice'], $TourProductLineInfo['LowMarketPrice'], $TourProductLineInfo['Cent'], $TourProductLineInfo['Sales'], $TourProductLineInfo['OverallRating'], $TourProductLineInfo['Complex'], $TourProductLineInfo['R1'], $TourProductLineInfo['R2'], $TourProductLineInfo['R3']);
            $TourProductLineModule->InsertInfo($TourProductLineInfo);
            // 查详情表并添加
            $TourProductLineDetailedInfo = $TourProductLineDetailedModule->GetInfoByTourProductID($TourProductID);
            unset($TourProductLineDetailedInfo['TourLineDetailedID']);
            $TourProductLineDetailedInfo['TourProductID'] = $NewTourProductID;
            
            $TourProductLineDetailedInfo['Description'] = json_decode($TourProductLineDetailedInfo['Description'], true);
            $TourProductLineDetailedInfo['NewContent'] = json_decode($TourProductLineDetailedInfo['NewContent'], true);
            $TourProductLineDetailedInfo['Explanation'] = json_decode($TourProductLineDetailedInfo['Explanation'], true);
            $TourProductLineDetailedInfo['Notice'] = json_decode($TourProductLineDetailedInfo['Notice'], true);
            $TourProductLineDetailedInfo['Watch'] = json_decode($TourProductLineDetailedInfo['Watch'], true);
            
            $TourProductLineDetailedInfo['Description']['DesTitle'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['Description']['DesTitle']);
            $TourProductLineDetailedInfo['Description']['DesContent'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['Description']['DesContent']);
            $TourProductLineDetailedInfo['Description'] = json_encode($TourProductLineDetailedInfo['Description'], JSON_UNESCAPED_UNICODE);
            
            $TourProductLineDetailedInfo['NewContent']['TodayTitle'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['NewContent']['TodayTitle']);
            $TourProductLineDetailedInfo['NewContent']['Traffic'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['NewContent']['Traffic']);
            $TourProductLineDetailedInfo['NewContent']['Hotel'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['NewContent']['Hotel']);
            $TourProductLineDetailedInfo['NewContent']['Diet'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['NewContent']['Diet']);
            $TourProductLineDetailedInfo['NewContent']['Trip'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['NewContent']['Trip']);
            $TourProductLineDetailedInfo['NewContent']['City'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['NewContent']['City']);
            $TourProductLineDetailedInfo['NewContent']['Spot'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['NewContent']['Spot']);
            $TourProductLineDetailedInfo['NewContent'] = json_encode($TourProductLineDetailedInfo['NewContent'], JSON_UNESCAPED_UNICODE);
            
            $TourProductLineDetailedInfo['Explanation']['ExpTitle'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['Explanation']['ExpTitle']);
            $TourProductLineDetailedInfo['Explanation']['ExpContent'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['Explanation']['ExpContent']);
            $TourProductLineDetailedInfo['Explanation'] = json_encode($TourProductLineDetailedInfo['Explanation'], JSON_UNESCAPED_UNICODE);
            
            $TourProductLineDetailedInfo['Notice']['NotTitle'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['Notice']['NotTitle']);
            $TourProductLineDetailedInfo['Notice']['NotContent'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['Notice']['NotContent']);
            $TourProductLineDetailedInfo['Notice'] = json_encode($TourProductLineDetailedInfo['Notice'], JSON_UNESCAPED_UNICODE);
            
            $TourProductLineDetailedInfo['Watch']['WatTitle'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['Watch']['WatTitle']);
            $TourProductLineDetailedInfo['Watch']['WatContent'] = $this->_StripcslashesArray($TourProductLineDetailedInfo['Watch']['WatContent']);
            $TourProductLineDetailedInfo['Watch'] = json_encode($TourProductLineDetailedInfo['Watch'], JSON_UNESCAPED_UNICODE);
            $TourProductLineDetailedInfo = $this->_AddslashesArray($TourProductLineDetailedInfo);
            $TourProductLineDetailedModule->InsertInfo($TourProductLineDetailedInfo);
            // 图片查询并添加
            $TourProductImageLists = $TourProductImageModule->GetLists(' and TourProductID=' . $TourProductID, 0, 100);
            if (! empty($TourProductImageLists)) {
                foreach ($TourProductImageLists as $Value) {
                    $ImagesInfo['TourProductID'] = $NewTourProductID;
                    $ImagesInfo['ImageUrl'] = $Value['ImageUrl'];
                    $ImagesInfo['IsDefault'] = $Value['IsDefault'];
                    $ImagesInfo['ImageAlt'] = $Value['ImageAlt'];
                    $TourProductImageModule->InsertInfo($ImagesInfo);
                }
            }
            alertandback("操作成功");
        }
        alertandback("操作失败");
    }
    //删除反斜杠：
    private function _AddslashesArray($String = '') {
        if (is_array ( $String )) {
            foreach ( $String as $Key => $Value ) {
                $NewString[$Key] = addslashes( $Value );
            }
        }
        if (is_string ( $String )) {
            $NewString = addslashes ( $NewString );
        }
        return $NewString;
    }
    //删除反斜杠：
    private function _StripcslashesArray($String = '') {
        if (is_array ( $String )) {
            foreach ( $String as $Key => $Value ) {
                $NewString[$Key] = stripcslashes( $Value );
            }
        }
        if (is_string ( $String )) {
            $NewString = stripcslashes ( $NewString );
        }
        return $NewString;
    }
    // 跟团游列表
    public function TourLineList()
    {
        $TourProductLineModule = new TourProductLineModule();
        $TourProductCategoryModule = new TourProductCategoryModule();
        $TourSupplierModule = new TourSupplierModule();
        $TourAreaModule = new TourAreaModule();
        $TourProductModule = new TourProductModule();
        $SqlWhere = ' and IsClose=0';
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
            $TourProductLineSkuModule = new TourProductLineSkuModule();
            $TourProductLineSkuModule->UpdateInfoByWhere($StatusInfo,$StatusWhere);
            $update = $TourProductLineModule->UpdateInfoByWhere($StatusInfo,$StatusWhere);
            if ($update){
                alertandback('更新成功');
            }else{
                alertandback('更新失败');
            }
        }
        // 类别
        $Tourlist = $TourProductCategoryModule->TourSelectByParent(1);
        
        // 搜索条件
        $PageUrl = '';
        $ProductName = trim($_GET['ProductName']); // 产品名称
        $Status = trim($_GET['Status']); // 产品状态
        $Category = trim($_GET['Category']); // 产品类别
        $SupplierID = trim($_GET['SupplierID']); // 供应商
        
        $R1 = trim($_GET['R1']);
        $R2 = trim($_GET['R2']);
        $R3 = trim($_GET['R3']);
        $R4 = trim($_GET['R4']);
        $R5 = trim($_GET['R5']);
        
        if ($ProductName != '') {
            $SqlWhere .= ' and (TourProductID=\'' . $ProductName . '\' or GroupNO like \'%' . $ProductName . '%\' or concat(ProductName) like \'%' . $ProductName . '%\')';
            $PageUrl .= '&ProductName=' . $ProductName;
        }
        if ($Status != '') {
            $SqlWhere .= ' and Status = \'' . $Status . '\'';
            $PageUrl .= '&Status=' . $Status;
        }
        if ($Category != '') {
            $CategoryList = $TourProductCategoryModule->GetInfoByKeyID($Category);
            $SqlWhere .= ' and Category = ' . $Category;
            $PageUrl .= '&Category=' . $Category;
        }
        if ($SupplierID != '') {
            $TourSupplier = $TourSupplierModule->GetInfoByKeyID($SupplierID);
            $SqlWhere .= ' and SupplierID= ' . $SupplierID;
            $PageUrl .= '&SupplierID= ' . $SupplierID;
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
            tourl('/index.php?Module=TourLine&Action=TourlineList&Page=' . $page . $PageUrl);
        }
        // 分页开始
        $Page = intval($_GET['Page']);
        $Page = $Page ? $Page : 1;
        $PageSize = 10;
        
        $Rscount = $TourProductLineModule->GetListsNum($SqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TourProductLineModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $Key => $Value) {
                if ($Value['Category'] > 0) {
                    $TourCategoryInfo = $TourProductCategoryModule->GetInfoByKeyID($Value['Category']);
                    $Data['Data'][$Key]['CnName'] = $TourCategoryInfo['CnName'];
                    $TourSupplierInfo = $TourSupplierModule->GetInfoByKeyID($Value['SupplierID']);
                    $Data['Data'][$Key]['SupplierName'] = $TourSupplierInfo['CnName'];
                    
                    if ($Value['RelationProductID'] > 0) {
                        $RelationProductInfo = $TourProductLineModule->GetInfoByWhere(' and RelationProductID=' . $Value['RelationProductID'], true);
                        $IsRelation = $Value['RelationProductID'];
                    } else {
                        $RelationProductInfo = $TourProductLineModule->GetInfoByWhere(' and RelationProductID=' . $Value['TourProductID'], true);
                        if (! empty($RelationProductInfo)) {
                            $IsRelation = $Value['TourProductID'];
                        }
                    }
                    $Data['Data'][$Key]['IsRelation'] = $IsRelation;
                    $Data['Data'][$Key]['RelationProductInfo'] = $RelationProductInfo;
                    unset($IsRelation, $RelationProductInfo);
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
        include template('TourlineList');
    }
    // 添加或更新跟团游产品
    public function Add()
    {
        $_GET['Action'] = 'Add';
        $TourProductCategoryModule = new TourProductCategoryModule();
        $TourProductModule = new TourProductModule();
        $TourProductLineModule = new TourProductLineModule();
        $TourSupplierModule = new TourSupplierModule();
        $TourAreaModule = new TourAreaModule();
        // 特色主题start
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourSpecialSubjectModule.php';
        $TourSpecialSubjectModule = new TourSpecialSubjectModule();
        $GenTuanYou = $TourSpecialSubjectModule->GetInfoByWhere(' and Category = 1 order by Sort Desc', true);
        
        // 特色主题end
        $TourProductID = intval($_GET['TourProductID']);
        $IsRelation = 0;
        if ($TourProductID > 0) {
            $ProductInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
            $ProductInfo['Features'] = explode(',', $ProductInfo['Features']);
            foreach ($ProductInfo['Features'] as $key => $value) {
                foreach ($GenTuanYou as $K => $V) {
                    if ($V['TourSpecialSubjectID'] == $value)
                        $GenTuanYou[$K]['Features'] = 1;
                }
            }
            
            if ($ProductInfo['RelationProductID'] > 0) {
                $RelationProductInfo = $TourProductLineModule->GetInfoByWhere(' and RelationProductID=' . $ProductInfo['RelationProductID'], true);
                $IsRelation = $ProductInfo['RelationProductID'];
            } else {
                $RelationProductInfo = $TourProductLineModule->GetInfoByWhere(' and RelationProductID=' . $TourProductID, true);
                if (! empty($RelationProductInfo)) {
                    $IsRelation = $TourProductID;
                }
            }
        }
        // 供应商
        $TourSupplierlist = $TourSupplierModule->TourSupplierSelect();
        // 选择目的地
        $TourAreaLists = $TourAreaModule->GetInfoByWhere(' and `ParentID` = 1005',true);
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
        // 选择出发地
        $Departure = $TourAreaModule->GetInfoByWhere(' and `ParentID` = 0',true);
        foreach ($Departure as $key => $value) {
            $AreaAddprovince = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$value['AreaID'],true);
            $Departure[$key]['Province'] = $AreaAddprovince;
            if ($AreaAddprovince) {
                foreach ($AreaAddprovince as $k => $v) {
                    $AreaAddCity = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$v['AreaID'],true);
                    $Departure[$key]['Province'][$k]['City'] = $AreaAddCity;
                }
            }
        }
        unset($AreaAddprovince, $AreaAddCity);
        $DestinationLists = $TourAreaModule->GetInfoByWhere(' and `ParentID` = 1005',true);
        foreach ($DestinationLists as $key => $value) {
            $AreaAddprovince = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$value['AreaID'],true);
            $DestinationLists[$key]['Province'] = $AreaAddprovince;
            if ($AreaAddprovince) {
                foreach ($AreaAddprovince as $k => $v) {
                    $AreaAddCity = $TourAreaModule->GetInfoByWhere(' and `ParentID` = '.$v['AreaID'],true);
                    $DestinationLists[$key]['Province'][$k]['City'] = $AreaAddCity;
                }
            }
        }
        unset($AreaAddprovince, $AreaAddCity);
        // 类别
        $Tourlist = $TourProductCategoryModule->TourSelectByParent(1);
        foreach ($Tourlist as $key => $value) {
            $Tourlists = $TourProductCategoryModule->TourSelectByParent($value['TourCategoryID']);
            $Tourlist[$key]['parent'] = $Tourlists;
        }
        if ($_POST) {
            $TourProductID = intval($_POST['TourProductID']);
            $Data['ProductName'] = trim($_POST['ProductName']);
            
            $Data['Category'] = intval($_POST['Category']);
            if ($Data['Category'] == 0)
                $Data['Category'] = 5;
            $TourCategoryInfo = $TourProductCategoryModule->GetInfoByKeyID($Data['Category']); // 获取上级ID
            $Data['FromIP'] = GetIP();
            $Data['Status'] = intval($_POST['Status']);
            $Data['SupplierID'] = intval($_POST['SupplierID']);
            if ($Data['ProductName'] == '' || $Data['Category'] == '' || $_POST['Keywords'] == '') {
                alertandback('信息填写不完整');
            }
            if ($TourProductID > 0) {
                // 修改
                $IsOk = $TourProductModule->UpdateInfoByKeyID($Data, $TourProductID);
            } else {
                // 添加
                $Data['TourProductID'] = $TourProductModule->InsertInfo($Data);
            }
            foreach ($GenTuanYou as $key => $value) {
                if ($_POST['Features' . $key] == $value['TourSpecialSubjectID'])
                    $Data['Features'] .= $_POST['Features' . $key] . ',';
            }
            $Data['Features'] = substr($Data['Features'], 0, - 1);
            unset($Data['FromIP']);
            $Data['Keywords'] = trim($_POST['Keywords']);
            $Data['ProductSimpleName'] = trim($_POST['ProductSimpleName']);
            $Data['TagInfo'] = trim($_POST['TagInfo']);
            // $Data ['RelationProductID'] = intval ( $_POST ['RelationProductID'] );//套餐对应的产品ID
            $Data['Departure'] = intval($_POST['Departure']);
            $Data['Destination'] = intval($_POST['Destination']);
            $Data['GroupNO'] = trim($_POST['GroupNO']);
            $Data['AddTime'] = date('Y-m-d H:i:s', time());
            $Data['AdvanceDays'] = trim($_POST['AdvanceDays']);
            $Data['ProductPackage'] = trim($_POST['ProductPackage']);
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
            $Data['IsNeedHotel'] = intval($_POST['IsNeedHotel']);
            $Data['IsNeedGiveAirport'] = intval($_POST['IsNeedGiveAirport']);
            $Data['IsNeedSendAirport'] = intval($_POST['IsNeedSendAirport']);
            $Data['SpecialNote'] = trim($_POST['SpecialNote']);
            if ($TourProductID > 0) {
                // 修改
                $IsOkOk = $TourProductLineModule->UpdateInfoByourProductID($Data, $TourProductID);
            } else {
                // 添加
                $TourProductLineID = $TourProductLineModule->InsertInfo($Data);
            }
            if ($TourProductLineID) {
                $TourProductLine = $TourProductLineModule->GetInfoByKeyID($TourProductLineID);
                $TourProductID = $TourProductLine['TourProductID'];
            }
            if ($IsOkOk || $TourProductLineID) {
                alertandgotopage('操作成功', '/index.php?Module=TourLine&Action=Add&TourProductID=' . $TourProductID);
            } else {
                alertandgotopage('操作成功', '/index.php?Module=TourLine&Action=Add&TourProductID=' . $TourProductID);
            }
        }
        include template('TourlineAdd');
    }

    public function Delete()
    {
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            alertandback("参数错误");
        }
        $Data['IsClose'] = 1;
        $TourProductModule = new TourProductModule();
        $UpDataTourProduct = $TourProductModule->UpdateInfoByWhere($Data, ' TourProductID =' . $TourProductID);
        $TourProductLineModule = new TourProductLineModule();
        $UpDataTourProductLine = $TourProductLineModule->UpdateInfoByWhere($Data, ' TourProductID =' . $TourProductID);
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $Data['Status'] = 0;
        $UpDataTourProductLineSku = $TourProductLineSkuModule->UpdateInfoByWhere($Data, ' TourProductID =' . $TourProductID);
        alertandback("操作成功");
    }
    
    // 设置每日行程
    public function SetLineContent()
    {
        $_GET['Action'] = 'SetLineContent';
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourLine&Action=TourlineList');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        $TourProductLineDetailedModule = new TourProductLineDetailedModule();
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['TodayTitle'] as $Key => $Value) {
                if ($Value != '') {
                    $UpdateInfo['TodayTitle'][$SK] = $Value;
                    $UpdateInfo['Traffic'][$SK] = trim($POST['Traffic'][$Key]);
                    $UpdateInfo['Hotel'][$SK] = stripcslashes(trim($POST['Hotel'][$Key]));
                    $UpdateInfo['Diet'][$SK] = trim($POST['Diet'][$Key]);
                    $UpdateInfo['City'][$SK] = trim($POST['City'][$Key]);
                    $UpdateInfo['Spot'][$SK] = trim($POST['Spot'][$Key]);
                    $PassAttractions[] = $UpdateInfo['Spot'][$SK];
                    // 文本图片处理-----------------------------------------------------------------------------
                    $UpdateInfo['Trip'][$SK] = $POST['Trip' . $Key];
                    $Pattern = array();
                    $Replacement = array();
                    $ImgArr = Array();
                    preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($UpdateInfo['Trip'][$SK]), $ImgArr);
                    if (count($ImgArr[0])) {
                        foreach ($ImgArr[0] as $ImgTag) {
                            $Pattern[] = $ImgTag;
                            $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                                '/title=".*"/iU',
                                '/alt=".*"/iU'
                            ), '', $ImgTag));
                        }
                    }
                    $UpdateInfo['Trip'][$SK] = str_replace($Pattern, $Replacement, stripcslashes($UpdateInfo['Trip'][$SK]));
                    // 文本图片处理-------------------------------------------------------------------------------
                    $SK++;
                }
            }
            // 途经景点处理-----------------------------------------------------------------------------
            if ($PassAttractions != '') {
                include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourPassAttractionsModule.php';
                $TourPassAttractionsModule = new TourPassAttractionsModule();
                include SYSTEM_ROOTPATH . '/Plugins/pinyin/pinyin.php';
                foreach ($PassAttractions as $key => $value) {
                    $Spot = explode(',', $value);
                    foreach ($Spot as $K => $V) {
                        if ($V != '') {
                            $SpotPinYin = ChineseToPinyinTwo($V);
                            $Date['PassAttractionsName'] = $V;
                            $Date['AddTime'] = date('Y-m-d H:i:s', time());
                            $Date['FirstLetter'] = $SpotPinYin;
                            $PassAttractionsInfo = $TourPassAttractionsModule->GetInfoByWhere(' and PassAttractionsName = \'' . $Date['PassAttractionsName'] . '\'');
                            if ($PassAttractionsInfo) {
                                $TourPassAttractionsModule->UpdateInfoByKeyID($Date, $PassAttractionsInfo['TourPassAttractionsID']);
                            } else {
                                $TourPassAttractionsModule->InsertInfo($Date);
                            }
                        } else {
                            unset($K, $V);
                        }
                    }
                }
            }
            // 途经景点处理-----------------------------------------------------------------------------
            
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['NewContent'] = addslashes($UpdateString);
            $info = $TourProductLineDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductLineDetailedModule->UpdateInfoByourProductID($UpdateData, $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductLineDetailedModule->InsertInfo($UpdateData);
            }
            // 更新跟团游基础表信息（行程天数、途经景点）
            $TourProductLineModule = new TourProductLineModule();
            $Data['Days'] = count($POST['TodayTitle']);
            foreach ($UpdateInfo['Spot'] as $key => $value) {
                $Data['AfterAttractions'] .= $value . ',';
            }
            $TourProductLineModule->UpdateInfoByourProductID($Data, $TourProductID);
            alertandgotopage("操作成功", '/index.php?Module=TourLine&Action=SetLineContent&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductLineDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['NewContent'] != '')
            
            $NewContentArray = json_decode($NewContentInfo['NewContent'], true);
        if ($NewContentArray['Trip'] != '') {
            foreach ($NewContentArray['Trip'] as $key => $value) {
                $NewContentArray['Trip'][$key] = StrReplaceImages($value);
            }
        }
        $I = count($NewContentArray['TodayTitle']) + 1;
        include template('TourLineContent');
    }
    
    // 设置产品介绍
    public function SetLineDescription()
    {
        $_GET['Action'] = 'SetLineDescription';
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourLine&Action=TourlineList');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        $TourProductLineDetailedModule = new TourProductLineDetailedModule();
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['DesTitle'] as $Key => $Value) {
                if ($POST['DesContent' . $Key] != '') {
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
            $info = $TourProductLineDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductLineDetailedModule->UpdateInfoByourProductID($UpdateData, $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductLineDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourLine&Action=SetLineDescription&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductLineDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['Description'] != '')
            $NewContentArray = json_decode($NewContentInfo['Description'], true);
        $I = count($NewContentArray['DesTitle']) + 1;
        include template('SetLineDescription');
    }
    // 设置费用说明
    public function SetLineExplanation()
    {
        $_GET['Action'] = 'SetLineExplanation';
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourLine&Action=TourlineList');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        $TourProductLineDetailedModule = new TourProductLineDetailedModule();
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['ExpTitle'] as $Key => $Value) {
                if ($POST['ExpContent'][$Key] != '') {
                    $UpdateInfo['ExpTitle'][$SK] = $Value;
                    // 文本图片处理-----------------------------------------------------------------------------
                    $UpdateInfo['ExpCss'][$SK] = $POST['ExpCss' . $Key];
                    $UpdateInfo['ExpContent'][$SK] = $POST['ExpContent'][$Key];
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
            $info = $TourProductLineDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductLineDetailedModule->UpdateInfoByourProductID($UpdateData, $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductLineDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourLine&Action=SetLineExplanation&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductLineDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['Explanation'] != '')
            $NewContentArray = json_decode($NewContentInfo['Explanation'], true);
        $I = count($NewContentArray['ExpTitle']) + 1;
        include template('SetLineExplanation');
    }
    // 设置预定需知
    public function SetLineNoticet()
    {
        $_GET['Action'] = 'SetLineNoticet';
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourLine&Action=TourlineList');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        $TourProductLineDetailedModule = new TourProductLineDetailedModule();
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['NotTitle'] as $Key => $Value) {
                if ($POST['NotContent' . $Key] != '') {
                    $UpdateInfo['NotTitle'][$SK] = $Value;
                    // 文本图片处理-----------------------------------------------------------------------------
                    $UpdateInfo['NotCss'][$SK] = $POST['NotCss' . $Key];
                    $UpdateInfo['NotContent'][$SK] = $POST['NotContent' . $Key];
                    if ($UpdateInfo['NotContent'][$SK] == '') {
                        alertandback("内容不能为空");
                    }
                    $Pattern = array();
                    $Replacement = array();
                    $ImgArr = Array();
                    preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($UpdateInfo['NotContent'][$SK]), $ImgArr);
                    if (count($ImgArr[0])) {
                        foreach ($ImgArr[0] as $ImgTag) {
                            $Pattern[] = $ImgTag;
                            $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                                '/title=".*"/iU',
                                '/alt=".*"/iU'
                            ), '', $ImgTag));
                        }
                    }
                    $UpdateInfo['NotContent'][$SK] = str_replace($Pattern, $Replacement, stripcslashes($UpdateInfo['NotContent'][$SK]));
                    // 文本图片处理-------------------------------------------------------------------------------
                    $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['Notice'] = addslashes($UpdateString);
            $info = $TourProductLineDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductLineDetailedModule->UpdateInfoByourProductID($UpdateData, $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductLineDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourLine&Action=SetLineNoticet&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductLineDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['Notice'] != '')
            $NewContentArray = json_decode($NewContentInfo['Notice'], true);
        $I = count($NewContentArray['NotTitle']) + 1;
        include template('SetLineNoticet');
    }
    // 设置注意事项
    public function SetLineWatch()
    {
        $_GET['Action'] = 'SetLineWatch';
        $TourProductID = intval($_GET['TourProductID']);
        if ($TourProductID == 0) {
            alertandgotopage("操作失败", '/index.php?Module=TourLine&Action=TourlineList');
        }
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
        $TourProductLineDetailedModule = new TourProductLineDetailedModule();
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ($POST['WatTitle'] as $Key => $Value) {
                if ($POST['WatContent' . $Key] != '') {
                    $UpdateInfo['WatTitle'][$SK] = $Value;
                    // 文本图片处理-----------------------------------------------------------------------------
                    $UpdateInfo['WatCss'][$SK] = $POST['WatCss' . $Key];
                    $UpdateInfo['WatContent'][$SK] = $POST['WatContent' . $Key];
                    $Pattern = array();
                    $Replacement = array();
                    $ImgArr = Array();
                    preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($UpdateInfo['WatContent'][$SK]), $ImgArr);
                    if (count($ImgArr[0])) {
                        foreach ($ImgArr[0] as $ImgTag) {
                            $Pattern[] = $ImgTag;
                            $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array(
                                '/title=".*"/iU',
                                '/alt=".*"/iU'
                            ), '', $ImgTag));
                        }
                    }
                    $UpdateInfo['WatContent'][$SK] = str_replace($Pattern, $Replacement, stripcslashes($UpdateInfo['WatContent'][$SK]));
                    // 文本图片处理-------------------------------------------------------------------------------
                    $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['Watch'] = addslashes($UpdateString);
            $info = $TourProductLineDetailedModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if ($info) {
                $IsOk = $TourProductLineDetailedModule->UpdateInfoByourProductID($UpdateData, $TourProductID);
            } else {
                $UpdateData['TourProductID'] = $TourProductID;
                $IsOk = $TourProductLineDetailedModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourLine&Action=SetLineWatch&TourProductID=' . $TourProductID);
        }
        $NewContentInfo = $TourProductLineDetailedModule->GetInfoByTourProductID($TourProductID);
        if ($NewContentInfo['Watch'] != '')
            $NewContentArray = json_decode($NewContentInfo['Watch'], true);
        $I = count($NewContentArray['WatTitle']) + 1;
        include template('SetLineWatch');
    }
    // 设置产品图片
    public function TourImages()
    {
        $_GET['Action'] = 'TourImages';
        $TourProductID = intval($_GET['TourProductID']);
        //unset($_SESSION['TourProductID']);
        //$_SESSION['TourProductID'] = $TourProductID;
        $TourProductImageModule = new TourProductImageModule();
        $TourImagesList = $TourProductImageModule->GetListsByTourProductID($TourProductID);
        include template('TourImages');
    }
    
    // 删除产品图片
    public function DeleteTourImages()
    {
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductImageModule = new TourProductImageModule();
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
        if ($ImageID == 0 ) {
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

    public function TourImagesSetDefault()
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

    public function TourProductLineSkuList()
    {
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $_GET['Action'] = 'TourProductLineSkuList';
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
        $SkuLists = $TourProductLineSkuModule->GetLists(' and TourProductID = ' . $TourProductID);
        include template('TourProductLineSkuList');
    }
    // 更新产品售卖类型
    public function UpdateSkuType()
    {
        $UpdateInfo['SkuType'] = intval($_GET['SkuType']);
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductLineModule = new TourProductLineModule();
        $IsOk = $TourProductLineModule->UpdateInfoByWhere($UpdateInfo, 'TourProductID=' . intval($TourProductID));
        if ($IsOk) {
            $TourProductLineSkuModule = new TourProductLineSkuModule();
            $SkuUpdateInfo['Status'] = 0;
            $TourProductLineSkuModule->UpdateInfoByWhere($SkuUpdateInfo, 'TourProductID=' . intval($TourProductID));
            alertandback("操作成功，该产品SKU已被禁用，请重新设置后重新启用！");
        } else {
            alertandback("操作失败！");
        }
    }
    // 更新sku信息
    public function TourProductLineSkuEdit()
    {
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineModule = new TourProductLineModule();
        $ProductSkuID = intval($_GET['ProductSkuID']);
        $TourProductID = intval($_GET['TourProductID']);
        if ($ProductSkuID) {
            $ProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($ProductSkuID);
        }
        if ($TourProductID) {
            $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
        }
        if ($_POST) {
            $TourProductID = intval($_POST['TourProductID']);
            $ProductSkuID = intval($_POST['ProductSkuID']);
            $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
            if ($TourProductLineInfo['Departure'] == '') {
                alertandback("产品缺少出发城市,不能设置SKU！");
            }
            if ($TourProductLineInfo['Category'] == '') {
                alertandback("产品缺少产品类别,不能设置SKU！");
            }
            $TourAreaModule = new TourAreaModule();
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($TourProductLineInfo['Departure']);
            $Data['SKUName'] = trim($_POST['SKUName']);
            if ($Data['SKUName'] == '') {
                alertandback("SKU名称不能为空！");
            }
            $Data['TourProductID'] = $TourProductID;
            $Data['Status'] = $_POST['Status'];
            
            $Data['AdultNum'] = intval($_POST['AdultNum']);
            $Data['ChildrenNum'] = intval($_POST['ChildrenNum']);
            $Data['PeopleNum'] = intval($_POST['PeopleNum']);
            
            $MoreSqlWhere = '';
            if ($ProductSkuID > 0) {
                $MoreSqlWhere .= ' and ProductSkuID!=' . $ProductSkuID;
            } else {
                // SKU名称生成
                $CategoryID = $TourProductLineInfo['Category'];
                if ($TourProductLineInfo['Category'] < 10) {
                    $CategoryID = '0' . $TourProductLineInfo['Category'];
                }
                $Data['SkuNO'] = $TourAreaInfo['ShorEnName'] . $CategoryID . $TourProductID . rand(100, 999);
            }
            // 判断名称不能重复，SKU名称生成
            $ProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByWhere(' and SKUName=\'' . $Data['SKUName'] . '\'' . $MoreSqlWhere . ' and ProductSkuID!=' . $ProductSkuID);
            if (! empty($ProductLineSkuInfo)) {
                alertandback("SKU名称已经存在");
            }
            
            if ($ProductSkuID > 0) {
                $IsOK = $TourProductLineSkuModule->UpdateInfoByKeyID($Data, $ProductSkuID);
                // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
                /* if ($IsOK) {
                    $this->UpdatePriceAdnOrder($ProductLineSkuInfo['TourProductID'], $ProductLineSkuInfo['ProductSkuID']);
                } */
            } else {
                $ProductSkuID = $TourProductLineSkuModule->InsertInfo($Data);
            }
            if ($IsOK || $ProductSkuID) {
                alertandgotopage("操作成功", '/index.php?Module=TourLine&Action=TourProductLineSkuList&TourProductID=' . $TourProductID);
            } else {
                alertandgotopage("未做操作", '/index.php?Module=TourLine&Action=TourProductLineSkuList&TourProductID=' . $TourProductID);
            }
        }
        include template('TourProductLineSkuEdit');
    }
    
    // 更新sku状态
    public function TourProductLineSkuStatus()
    {
        if ($_GET) {
            $TourProductLineSkuModule = new TourProductLineSkuModule();
            $Data['Status'] = intval($_GET['Status']);
            $ProductSkuID = intval($_GET['ProductSkuID']);
            $IsOK = $TourProductLineSkuModule->UpdateInfoByKeyID($Data, $ProductSkuID);
            if ($IsOK) {
                $ProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($ProductSkuID);
                // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
                //$this->UpdatePriceAdnOrder($ProductLineSkuInfo['TourProductID'], $ProductLineSkuInfo['ProductSkuID']);
                alertandgotopage('操作成功!', '/index.php?Module=TourLine&Action=TourProductLineSkuList&TourProductID=' . $ProductLineSkuInfo['TourProductID']);
            } else {
                alertandback("操作失败");
            }
        }
    }
    // 删除SKU价格
    public function DeleteTourProductLineSkuInfo()
    {
        $TourPricetID = intval($_GET['TourPricetID']);
        if ($TourPricetID == 0) {
            alertandback("参数错误");
        }
        $TourProductLineSkuPriceModule = new TourProductLineSkuPriceModule();
        $TourProductLineSkuPriceInfo = $TourProductLineSkuPriceModule->GetInfoByKeyID($TourPricetID);
        $IsOk = $TourProductLineSkuPriceModule->DeleteByKeyID($TourPricetID);
        if ($IsOk) {
            // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
            //$this->UpdatePriceAdnOrder($TourProductLineSkuPriceInfo['TourProductID'], $TourProductLineSkuPriceInfo['ProductSkuID']);
            alertandback("操作成功");
        } else {
            alertandback("操作失败");
        }
    }
    // sku列表 编辑跟团游价格
    public function TourProductLineSkuPriceList()
    {
        $ProductSkuID = intval($_GET['ProductSkuID']);
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        $TourProductLineSkuPriceModule = new TourProductLineSkuPriceModule();
        $ProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($ProductSkuID);
        $TourProductID = $ProductLineSkuInfo['TourProductID'];
        $SKUPriceList = $TourProductLineSkuPriceModule->GetInfoByWhere(' and ProductSkuID = ' . $ProductSkuID, true);
        $TourPricetID = intval($_GET['TourPricetID']);
        if ($TourPricetID)
            $SkuPriceInfo = $TourProductLineSkuPriceModule->GetInfoByKeyID($TourPricetID);
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
                $IsOk = $TourProductLineSkuPriceModule->UpdateInfoByKeyID($Date, $TourPricetID);
            } else {
                if ($InventoryType == 1) {
                    $Date['ErveryDayInventory'] = '-1';
                } else {
                    $Date['ErveryDayInventory'] = trim($_POST['ErveryDayInventory']);
                }
                // 添加
                $IsOk = $TourProductLineSkuPriceModule->InsertInfo($Date);
            }
            // 更新产品每日价格、更新产品基础信息、更新为支付订单状态
            //$this->UpdatePriceAdnOrder($Date['TourProductID'], $Date['ProductSkuID']);
            alertandback('更新成功');
        }
        include template('TourProductLineSkuPriceList');
    }
    // 判断价格能不能更新
    private function IsUpdate($Date = array(), $TourPricetID = '')
    {
        $TourProductErverDayPriceModule = new TourProductLineErverDayPriceModule();
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
                $PriceInfo = $TourProductErverDayPriceModule->GetInfoByWhere(' and TourPricetID != ' . $TourPricetID . ' and ProductSkuID=' . $Date['ProductSkuID'] . ' and Date=\'' . date("Ymd", strtotime($StartDate)) . '\'');
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
        $this->UpdateLineErverDayPrice($ProductSkuID);
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
        $TourProductLineErverDayPriceModule = new TourProductLineErverDayPriceModule();
        $OrderLists = $TourProductOrderModule->GetInfoByWhere(' and Status=1', true);
        foreach ($OrderLists as $Value) {
            $OrderInfoLists = $TourProductOrderInfoModule->GetInfoByWhere(' and `OrderNumber` =\'%' . $Value['OrderNumber'] . '%\'', true);
            if ($OrderInfoLists) {
                foreach ($OrderInfoLists as $Val) {
                    $ErverDayPriceInfo = $TourProductLineErverDayPriceModule->GetInfoByWhere(' and ProductSkuID=' . $Val['TourProductSkuID'] . ' and `Date`=\'' . date("Ymd", strtotime($Val['Depart'])) . '\'');
                    if (empty($ErverDayPriceInfo)) {
                        // 该日期产品禁止购买，关闭订单！
                        $Data['ExpirationTime'] = date("Y-m-d H:i:s");
                        $Data['Status'] = 12;
                        $TourProductOrderModule->UpdateInfoByWhere($Data, ' and OrderNumber = ' . $Val['OrderNumber']);
                    } elseif ($Val['UnitPrice'] != $ErverDayPriceInfo['Price']) {
                        // 价格变化，关闭订单！
                        $Data['ExpirationTime'] = date("Y-m-d H:i:s");
                        $Data['Status'] = 11;
                        $TourProductOrderModule->UpdateInfoByWhere($Data, ' and OrderNumber = ' . $Val['OrderNumber']);
                        // 更新库存(不存在购物车，如果有购物车需要判断产品类型在更新库存)
                        $UpdateErverDayPriceInfo['Inventory'] = $ErverDayPriceInfo['Inventory'] + 1;
                        $TourProductLineErverDayPriceModule->UpdateInfoByKeyID($UpdateErverDayPriceInfo, $ErverDayPriceInfo['DayPriceID']);
                    }
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
        $TourProductLineSkuPriceModule = new TourProductLineSkuPriceModule();
        $Rs = $TourProductLineSkuPriceModule->GetInfoByWhere(' and `TourProductID` =' . $TourProductID . ' order by Price asc');
        
        $Data['LowPrice'] = $Rs['Price'];
        $Data['LowMarketPrice'] = $Rs['MarketPrice'];
        $MonthString = '';
        $TourProductErverDayPriceModule = new TourProductLineErverDayPriceModule();
        for ($I = 0; $I < 12; $I ++) {
            $Month = date("Ym", strtotime("+" . $I . " month", time()));
            $IsRs = $TourProductErverDayPriceModule->GetInfoByWhere(' and `TourProductID` =' . $TourProductID . ' and `Date` like \'' . $Month . '%\'');
            if (! empty($IsRs)) {
                $MonthString .= ',' . $Month;
            }
        }
        $Data['Month'] = substr($MonthString, 1);
        $TourProductLineModule = new TourProductLineModule();
        $TourProductLineModule->UpdateInfoByourProductID($Data, $TourProductID);
    }
    // 更新每日价格
    private function UpdateLineErverDayPrice($ProductSkuID = '')
    {
        if ($ProductSkuID == '') {
            return '';
        }
        $ToDayInt = date("Ymd");
        $TourProductErverDayPriceModule = new TourProductLineErverDayPriceModule();
        $TourProductLineSkuPriceModule = new TourProductLineSkuPriceModule();
        $TourProductLineSkuModule = new TourProductLineSkuModule();
        // SKU价格列表
        $TourProductLineSkuInfo = $TourProductLineSkuModule->GetInfoByKeyID($ProductSkuID);
        if (empty($TourProductLineSkuInfo) || $TourProductLineSkuInfo['Status'] == 0) {
            // SKU不存在或者被禁用，则不必更新缓存
            return '';
        }
        $SKUPriceList = $TourProductLineSkuPriceModule->GetInfoByWhere(' and ProductSkuID=' . $ProductSkuID, true);
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
                    $ThisDayInfo = $TourProductErverDayPriceModule->GetInfoByWhere(" and ProductSkuID=" . $ProductSkuID . " and Date='" . $Date . "'");
                    if (! empty($ThisDayInfo)) {
                        // 更新
                        $TourProductErverDayPriceModule->UpdateInfoByKeyID($InsertInfo, $ThisDayInfo['DayPriceID']);
                        $DayPriceIDString .= ',' . $ThisDayInfo['DayPriceID'];
                    } else {
                        // 新增
                        $InsertInfo['TourProductID'] = $Value['TourProductID'];
                        $InsertInfo['ProductSkuID'] = $Value['ProductSkuID'];
                        $InsertInfo['Date'] = $Date;
                        $InsertInfo['Inventory'] = $Value['ErveryDayInventory'];
                        $DayPriceID = $TourProductErverDayPriceModule->InsertInfo($InsertInfo);
                        $DayPriceIDString .= ',' . $DayPriceID;
                    }
                }
            }
        }
        // 删除存在的日期
        $DayPriceIDString = substr($DayPriceIDString, 1);
        if ($DayPriceIDString != '') {
            $TourProductErverDayPriceModule->DeleteByWhere(' and ProductSkuID=' . $ProductSkuID . ' and `DayPriceID` not in(' . $DayPriceIDString . ')');
        }
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