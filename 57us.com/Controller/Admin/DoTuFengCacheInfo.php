<?php
//admin.57us.com/index.php?Module=DoTuFengCacheInfo&Action=UpdatePlayPriceAll&TourProductID=

class DoTuFengCacheInfo
{

    public $SupplierID = 6;

    public $PriceTimes = 1;

    public $MarketPriceTimes = 1.1;

    public $AdultSKUName = '成人套餐';

    public $KidSKUName = '儿童套餐';

    public function __construct()
    {
        error_reporting(7);
        set_time_limit(0);
        ini_set('display_errors', '1');
    }

    public function UpdatePlayPriceAll()
    {
		$TourProductID = $_GET['TourProductID'];
        global $DB;
        // 当地玩乐价格更新
        $Lists = $DB->select('select TourProductID from tour_product_play_base where TourProductID='.$TourProductID.' and SupplierID=' . $this->SupplierID);
        $TourProductIDString = '';
        if (!empty($Lists))
        {
            foreach ($Lists as $Value) {
                $this->UpdatePlayPrice($Value['TourProductID']);
                $TourProductIDString .= $Value['TourProductID'] . ',';
            }
            echo '更新价格完成，更新到的产品ID:'.substr($TourProductIDString, 0,-1);
        }else{
            echo '没有执行操作！';
        }
        exit;
    }

    /*
     * 更新当地玩乐价格 http://s.57us.com/index.php?Module=DoTuFengCache&Action=UpdatePlayPrice
     */
    public function UpdatePlayPrice($TourProductID = '')
    {
        if ($TourProductID == '') {
            return '';
        }
        // 添加、更新SKU信息
        $IsOkOne = $this->UpdateSkuInfo($TourProductID);
        // 更新价格
        if ($IsOkOne) {
            $IsOkTwo = $this->UpdatePriceInfo($TourProductID);
        }
        // 更新每日价格
        if ($IsOkTwo) {
            $IsOkThere = $this->UpdateErverdayPriceInfo($TourProductID);
        }
        // 更新最低价格
        if ($IsOkThere) {
            $this->UpdateLowPrice($TourProductID);
        }
    }
    
    // 添加、更新SKU信息
    public function UpdateSkuInfo($TourProductID = '')
    {
        if ($TourProductID == '') {
            return false;
        }
        global $DB;
        // 当地玩乐价格更新
        $TourProductInfo = $DB->getone('select * from tour_product_play_base where TourProductID=' . $TourProductID);
        if (empty($TourProductInfo) || intval($TourProductInfo['SupplierProductID']) == 0) {
            return false;
        }
        // 产品所在城市
        $CityInfo = $DB->getone('select ShorEnName from tour_area where AreaID=' . $TourProductInfo['City']);
        $ShorEnName = $CityInfo['ShorEnName'];
        if ($ShorEnName == '') {
            $ShorEnName = 'US';
        }
        unset($CityInfo);
        $Category = $TourProductInfo['Category'];
        if (intval($Category) == 0) {
            $Category = '00';
        }
        
        // 获取途风产品详细
        $DetailInfo = $this->ProductGetInfoByAction('productDetail.json', $TourProductInfo['SupplierProductID']);
        print_r($DetailInfo);
        if ($DetailInfo['error'] == 1) {
            // 销售完毕，没有库存，做下架操作
            $UpdateInfo['Status'] = 0;
            $DB->updateWhere('tour_product_play_base', $UpdateInfo, 'TourProductID=' . intval($TourProductID));
            return false;
        }
        
        // 标题
        $MainTitle = $DetailInfo['data']['base']['main_title'];
        $MainTitle = $this->_StrtrString($MainTitle);
        
        // 第一次删除SKU和价格信息——临时使用 START
        //$DB->delete("delete from tour_product_play_sku where TourProductID=" . intval($TourProductID));
        // 第一次删除SKU和价格信息——临时使用 END
        
        $PriceText = $DetailInfo['data']['priceText']['default'];
        // 处理成人价
        $AdultSkuInfo = $DB->getone('select * from tour_product_play_sku where TourProductID=' . $TourProductID . ' and SKUName=\'' . $this->AdultSKUName . '\'');
        $IsAdultCloss = 0;//判断是否没有套餐要下架
        if (trim($PriceText['adult']['price']) == '$0.00' && ! empty($AdultSkuInfo)) {
            $IsAdultCloss = 1;
            $UpdateInfo['Status'] = 0;
            $DB->updateWhere('tour_product_play_sku', $UpdateInfo, 'ProductSkuID=' . intval($AdultSkuInfo['ProductSkuID']));
            $DB->delete("delete from tour_product_play_sku_price where ProductSkuID=" . $AdultSkuInfo['ProductSkuID']);
            $DB->delete("delete from tour_product_play_erverday_price where ProductSkuID=" . $AdultSkuInfo['ProductSkuID']);
        } elseif (trim($PriceText['adult']['price']) != '$0.00' && empty($AdultSkuInfo)) {
            $InsertAdultInfo['SKUName'] = $this->AdultSKUName;
            $InsertAdultInfo['TourProductID'] = $TourProductID;
            $InsertAdultInfo['Status'] = 1;
            $InsertAdultInfo['SkuNO'] = $ShorEnName . $Category . $TourProductID . rand(100, 999);
            $InsertAdultInfo['IsClose'] = 0;
            $DB->insertArray('tour_product_play_sku', $InsertAdultInfo);
            //开启产品售卖
            if ($TourProductInfo['Status']==0)
            {
                $UpdateProducInfo['Status'] = 1;
                $UpdateProducInfo['UpdateTime'] = date("Y-d-d H:i:s");
                $DB->updateWhere('tour_product_play_base', $UpdateProducInfo, 'TourProductID=' . intval($TourProductID));
            }
        }
        
        // 处理儿童价
        $KidSkuInfo = $DB->getone('select * from tour_product_play_sku where TourProductID=' . $TourProductID . ' and SKUName=\'' . $this->KidSKUName . '\'');
        $IsKidCloss = 0;//判断是否没有套餐要下架
        if (trim($PriceText['kid']['price']) == '$0.00' && ! empty($KidSkuInfo)) {
            $IsKidCloss = 1;
            $UpdateInfo['Status'] = 0;
            $DB->updateWhere('tour_product_play_sku', $UpdateInfo, 'ProductSkuID=' . intval($KidSkuInfo['ProductSkuID']));
            $DB->delete("delete from tour_product_play_sku_price where ProductSkuID=" . $KidSkuInfo['ProductSkuID']);
            $DB->delete("delete from tour_product_play_erverday_price where ProductSkuID=" . $KidSkuInfo['ProductSkuID']);
        } elseif (trim($PriceText['kid']['price']) != '$0.00' && empty($KidSkuInfo)) {
            $InsertKidInfo['SKUName'] = $this->KidSKUName;
            $InsertKidInfo['TourProductID'] = $TourProductID;
            $InsertKidInfo['Status'] = 1;
            $InsertKidInfo['SkuNO'] = $ShorEnName . $Category . $TourProductID . rand(100, 999);
            $InsertKidInfo['IsClose'] = 0;
            $DB->insertArray('tour_product_play_sku', $InsertKidInfo);
            //开启产品售卖
            if ($TourProductInfo['Status']==0)
            {
                $UpdateProducInfo['Status'] = 1;
                $UpdateProducInfo['UpdateTime'] = date("Y-d-d H:i:s");
                $DB->updateWhere('tour_product_play_base', $UpdateProducInfo, 'TourProductID=' . intval($TourProductID));
            }
        }
        
        //对于不能购买的产品下架
        if (($IsAdultCloss==1 && $IsKidCloss==1) || (empty($AdultSkuInfo) && empty($KidSkuInfo)))
        {
            $UpdateProducInfo['Status'] = 0;
            $UpdateProducInfo['UpdateTime'] = date("Y-d-d H:i:s");
            $DB->updateWhere('tour_product_play_base', $UpdateProducInfo, 'TourProductID=' . intval($TourProductID));
        }
        return true;
    }
    
    // 产品价格更新
    public function UpdatePriceInfo($TourProductID = '')
    {
        if ($TourProductID == '') {
            return false;
        }
        global $DB;
        // 当地玩乐价格更新
        $TourProductInfo = $DB->getone('select * from tour_product_play_base where TourProductID=' . $TourProductID);
        if (empty($TourProductInfo) || intval($TourProductInfo['SupplierProductID']) == 0) {
            return false;
        }
        // 获取产品价格
        $DepartureInfo = $this->ProductGetInfoByAction('productGetAttribute.json', $TourProductInfo['SupplierProductID']);
		print_r($DepartureInfo);
        if ($DepartureInfo['error'] == 1) {
            // 销售完毕，没有库存，做下架操作
            $UpdateInfo['Status'] = 0;
            $DB->updateWhere('tour_product_play_base', $UpdateInfo, 'TourProductID=' . intval($TourProductID));
            return false;
        }
        // 标题
        $AdultSkuInfo = $DB->getone('select * from tour_product_play_sku where TourProductID=' . $TourProductID . ' and SKUName=\'' . $this->AdultSKUName . '\'');
        $KidSkuInfo = $DB->getone('select * from tour_product_play_sku where TourProductID=' . $TourProductID . ' and SKUName=\'' . $this->KidSKUName . '\'');
        
        $PriceRuleArray = $DepartureInfo['data']['price_rule'];
        $DB->update("update tour_product_play_sku_price set ErveryDayInventory=0 where TourProductID=" . intval($TourProductID));
        //$DB->delete("delete from tour_product_play_sku_price where TourProductID=" . intval($TourProductID));
        $IsClose = 0;
        foreach ($PriceRuleArray as $Key => $Value) {
            $DoInfo['TourProductID'] = $TourProductID;
            $DoInfo['StartDate'] = $Key;
            $DoInfo['EndDate'] = $Key;
            $DoInfo['Monday'] = $this->GetXingQi($Key, 1);
            $DoInfo['Tuesday'] = $this->GetXingQi($Key, 2);
            $DoInfo['Wednesday'] = $this->GetXingQi($Key, 3);
            $DoInfo['Thursday'] = $this->GetXingQi($Key, 4);
            $DoInfo['Friday'] = $this->GetXingQi($Key, 5);
            $DoInfo['Saturday'] = $this->GetXingQi($Key, 6);
            $DoInfo['Sunday'] = $this->GetXingQi($Key, 0);
            
            if ($AdultSkuInfo['ProductSkuID'] > 0) {
                $AdultSkuPriceInfo = $DB->getone('select * from tour_product_play_sku_price where ProductSkuID=' . $AdultSkuInfo['ProductSkuID'] . ' and StartDate=\'' . $DoInfo['StartDate'] . '\' and EndDate=\'' . $DoInfo['EndDate'] . '\'');
                if (! empty($AdultSkuPriceInfo)) {
                    // 更新成人价格
                    $DoInfo['ProductSkuID'] = $AdultSkuInfo['ProductSkuID'];
                    $DoInfo['PurchasePrice'] = $Value['adult_cny'];
                    $DoInfo['Price'] = ceil($DoInfo['PurchasePrice'] * $this->PriceTimes);
                    $DoInfo['MarketPrice'] = ceil($DoInfo['PurchasePrice'] * $this->MarketPriceTimes);
                    $DoInfo['SellPrice'] = $DoInfo['PurchasePrice'];
                    $DoInfo['Profit'] = $DoInfo['Price'] - $DoInfo['PurchasePrice'];
                    if ($Value['soldout'] == 0 && intval($Value['adult_cny'])>0) {
                        $DoInfo['ErveryDayInventory'] = - 1;
                        $IsClose = 1;
                    } else {
                        $DoInfo['ErveryDayInventory'] = 0;
                    }
                    $DB->updateWhere('tour_product_play_sku_price', $DoInfo, 'TourPricetID=' . intval($AdultSkuPriceInfo['TourPricetID']));
                } else {
                    // 增加成人价格
                    $DoInfo['ProductSkuID'] = $AdultSkuInfo['ProductSkuID'];
                    $DoInfo['PurchasePrice'] = $Value['adult_cny'];
                    $DoInfo['Price'] = ceil($DoInfo['PurchasePrice'] * $this->PriceTimes);
                    $DoInfo['MarketPrice'] = ceil($DoInfo['PurchasePrice'] * $this->MarketPriceTimes);
                    $DoInfo['SellPrice'] = $DoInfo['PurchasePrice'];
                    $DoInfo['Profit'] = $DoInfo['Price'] - $DoInfo['PurchasePrice'];
                    if ($Value['soldout'] == 0  && intval($Value['adult_cny'])>0) {
                        $DoInfo['ErveryDayInventory'] = - 1;
                        $DB->insertArray('tour_product_play_sku_price', $DoInfo, true);
                        $IsClose = 1;
                    }
                    
                }
            }
            
            if ($KidSkuInfo['ProductSkuID'] > 0) {
                $KidSkuPriceInfo = $DB->getone('select * from tour_product_play_sku_price where ProductSkuID=' . $KidSkuInfo['ProductSkuID'] . ' and StartDate=\'' . $DoInfo['StartDate'] . '\' and EndDate=\'' . $DoInfo['EndDate'] . '\'');
                if (! empty($KidSkuPriceInfo)) {
                    // 更新儿童价格
                    $DoInfo['ProductSkuID'] = $KidSkuInfo['ProductSkuID'];
                    $DoInfo['PurchasePrice'] = $Value['kids_cny'];
                    $DoInfo['Price'] = ceil($DoInfo['PurchasePrice'] * $this->PriceTimes);
                    $DoInfo['MarketPrice'] = ceil($DoInfo['PurchasePrice'] * $this->MarketPriceTimes);
                    $DoInfo['SellPrice'] = $DoInfo['PurchasePrice'];
                    $DoInfo['Profit'] = $DoInfo['Price'] - $DoInfo['PurchasePrice'];
                    if ($Value['soldout'] == 0 && intval($Value['adult_cny'])>0) {
                        $DoInfo['ErveryDayInventory'] = - 1;
                        $IsClose = 1;
                    } else {
                        $DoInfo['ErveryDayInventory'] = 0;
                    }
                    $DB->updateWhere('tour_product_play_sku_price', $DoInfo, 'TourPricetID=' . intval($KidSkuPriceInfo['TourPricetID']));
                } else {
                    // 增加儿童价格
                    $DoInfo['ProductSkuID'] = $KidSkuInfo['ProductSkuID'];
                    $DoInfo['PurchasePrice'] = $Value['kids_cny'];
                    $DoInfo['Price'] = ceil($DoInfo['PurchasePrice'] * $this->PriceTimes);
                    $DoInfo['MarketPrice'] = ceil($DoInfo['PurchasePrice'] * $this->MarketPriceTimes);
                    $DoInfo['SellPrice'] = $DoInfo['PurchasePrice'];
                    $DoInfo['Profit'] = $DoInfo['Price'] - $DoInfo['PurchasePrice'];
                    if ($Value['soldout'] == 0 && intval($Value['kids_cny'])>0) {
                        $DoInfo['ErveryDayInventory'] = - 1;
                        $DB->insertArray('tour_product_play_sku_price', $DoInfo, true);
                        $IsClose = 1;
                    }
                }
            }
        }
        //对于不能购买的产品下架
        if ($IsClose==0)
        {
            $UpdateProducInfo['Status'] = 0;
            $UpdateProducInfo['UpdateTime'] = date("Y-d-d H:i:s");
            $DB->updateWhere('tour_product_play_base', $UpdateProducInfo, 'TourProductID=' . intval($TourProductID));
        }
        return true;
    }
    // 获取星期几
    public function GetXingQi($DataString = '', $XingQi = '')
    {
        $ThisXingQi = date('w', strtotime($DataString));
        if ($ThisXingQi == 1 && $XingQi == 1) {
            return 1;
        } elseif ($ThisXingQi == 2 && $XingQi == 3) {
            return 1;
        } elseif ($ThisXingQi == 3 && $XingQi == 3) {
            return 1;
        } elseif ($ThisXingQi == 4 && $XingQi == 4) {
            return 1;
        } elseif ($ThisXingQi == 5 && $XingQi == 5) {
            return 1;
        } elseif ($ThisXingQi == 6 && $XingQi == 6) {
            return 1;
        } elseif ($ThisXingQi == 0 && $XingQi == 0) {
            return 1;
        } else {
            return 0;
        }
    }
    // 更新每日价格
    private function UpdateErverdayPriceInfo($TourProductID = '')
    {
        global $DB;
        $DB->delete("delete from tour_product_play_erverday_price where TourProductID=" . $TourProductID);
        $SkuPriceLists = $DB->select('select * from tour_product_play_sku_price where TourProductID=' . $TourProductID .' and ErveryDayInventory!=0');
        foreach ($SkuPriceLists as $Value) {
            $InsertInfo['UpdateTime'] = date("Y-m-d H:i:s");
            $InsertInfo['Price'] = $Value['Price'];
            $InsertInfo['TourProductID'] = $TourProductID;
            $InsertInfo['ProductSkuID'] = $Value['ProductSkuID'];
            $InsertInfo['TourPricetID'] = $Value['TourPricetID'];
            $InsertInfo['MarketPrice'] = $Value['MarketPrice'];
            $InsertInfo['Date'] = date('Ymd', strtotime($Value['StartDate']));
            ;
            $InsertInfo['Inventory'] = $Value['ErveryDayInventory'];
            $DB->insertArray('tour_product_play_erverday_price', $InsertInfo, true);
        }
        return true;
    }
    // 更新最低价格
    public function UpdateLowPrice($TourProductID = '')
    {
        if ($TourProductID == 0) {
            return '';
        }
        // 更新产品最低价\出团月份
        global $DB;
        $LowPriceInfo = $DB->getone('select * from tour_product_play_sku_price where TourProductID=' . $TourProductID . ' and ErveryDayInventory!=0 order by Price asc');
        //$LowMarketPriceInfo = $DB->getone('select * from tour_product_play_sku_price where TourProductID=' . $TourProductID . ' and ErveryDayInventory!=0 order by MarketPrice asc');
        $Data['LowPrice'] = $LowPriceInfo['Price'];
        $Data['LowMarketPrice'] = $LowPriceInfo['MarketPrice'];
        $MonthString = '';
        for ($I = 0; $I < 12; $I ++) {
            $Month = date("Ym", strtotime("+" . $I . " month", time()));
            $IsRs = $DB->select('select * from tour_product_play_erverday_price where TourProductID=' . $TourProductID . ' and `Date` like \'' . $Month . '%\'');
            if (! empty($IsRs)) {
                $MonthString .= ',' . $Month;
            }
        }
        $Data['Month'] = substr($MonthString, 1);
        $DB->updateWhere('tour_product_play_base', $Data, 'TourProductID=' . intval($TourProductID));
        return true;
    }

    public function CurlByGet($Url = '')
    {
        $ch = curl_init();
        // 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 去除SSL证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 去除SSL证书验证
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 定义超时3秒钟
                                               // 执行并获取url地址的内容
        $output = curl_exec($ch);
        $errorCode = curl_errno($ch);
        // 释放curl句柄
        curl_close($ch);
        if (0 !== $errorCode) {
            return false;
        }
        return $output;
    }

    public function Object2Array(&$Object)
    {
        $Object = json_decode(json_encode($Object), true);
        return $Object;
    }

    public function GetAuthKeyInfo()
    {
        $appkey = 'cc2975aaa48a74af21424c869e2ce591eb0ea4f9';
        $AuthKeyInfo['AppID'] = '10000071';
        $AuthKeyInfo['IP'] = '139.224.187.5';
        $AuthKeyInfo['Time'] = time();
        $AuthKeyInfo['DoMain'] = 'cn.toursforfun.com';
        $AuthKeyInfo['Authkey'] = md5($appkey . $AuthKeyInfo['Time'] . $AuthKeyInfo['IP']);
        return $AuthKeyInfo;
    }

    public function ProductGetInfoByAction($Action = '', $productId = '')
    {
        if ($productId == '') {
            return '';
        }
        // $Action = 'productGetBase.json';
        $AuthKeyInfo = $this->GetAuthKeyInfo();
        $Url = 'http://' . $AuthKeyInfo['DoMain'] . '/api/1.0/' . $Action;
        $Url .= '?appid=' . $AuthKeyInfo['AppID'] . '&authkey=' . $AuthKeyInfo['Authkey'] . '&timestamp=' . $AuthKeyInfo['Time'];
        $Url .= '&productId=' . $productId . '&language=cn';
        $response = $this->CurlByGet($Url);
        $json = json_decode($response);
        $Array = $this->Object2Array($json);
        return $Array;
    }
    
    // 替换关键字
    public function _StrtrString($String = '')
    {
        if ($String == '') {
            return '';
        }
        if (is_array($String)) {
            foreach ($String as $Key => $Value) {
                $NewString = str_replace(array(
                    '途风',
                    '（携程旗下）'
                ), array(
                    '57美国网',
                    ''
                ), $Value);
                $Search = array(
                    "'<script[^>]*?>.*?</script>'si",
                    '/<a.*>/isU',
                    '/<\/a>/isU'
                );
                $Replace = array(
                    "",
                    "",
                    ""
                );
                $NewString = preg_replace($Search, $Replace, $NewString);
                $NewString = html_entity_decode($NewString);
                $NewString = addslashes($NewString);
                $String[$Key] = $NewString;
            }
        }
        if (is_string($String)) {
            $NewString = str_replace(array(
                '途风',
                '（携程旗下）'
            ), array(
                '57美国网',
                ''
            ), $String);
            $Search = array(
                "'<script[^>]*?>.*?</script>'si",
                '/<a.*>/isU',
                '/<\/a>/isU'
            );
            $Replace = array(
                "",
                "",
                ""
            );
            $NewString = preg_replace($Search, $Replace, $NewString);
            $NewString = html_entity_decode($NewString);
            $NewString = addslashes($NewString);
        }
        return $String;
    }
}
