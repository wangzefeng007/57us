<?php

/* 定时旅游产品每日价格 http://admin.57us.com/index.php?Module=DoTourErverDayPrice&Action=DoTourPrice */
class DoTourErverDayPrice
{

    public function __construct()
    {
        set_time_limit(0);
        ini_set('display_errors', '0');
    }

    /**
     * 关闭浏览器
     */
    private function CloseIE()
    {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title></title>
                <script language="javascript">window.opener=null;
                    window.open("","_self");
                    window.close();</script>
                </head>
                <body>
                </body>
                </html>';
        exit();
    }

    public function DoOneTourPrice()
    {
        global $DB;
        $TourProductID = intval($_GET['TourProductID']);
        $TourProductInfo = $DB->getone('select TourProductID from tour_product_line where TourProductID=' . $TourProductID);
        if (! empty($TourProductInfo)) {
            $this->DoGroupPrice($TourProductInfo['TourProductID']);
            $this->UpdateGroupLowPrice($TourProductInfo['TourProductID']);
        } else {
            $PlayTourProductInfo = $DB->getone('select TourProductID from tour_product_play_base where TourProductID=' . $TourProductID);
            if (! empty($PlayTourProductInfo)) {
                $this->DoPlayStatusInfo($PlayTourProductInfo['TourProductID']);
                $this->UpdatePlayLowPrice($PlayTourProductInfo['TourProductID']);
            }
        }
        alertandback('更新价格成功！');
        $this->CloseIE();
    }

    public function DoTourPrice()
    {
        global $DB;
        // 当地玩乐价格更新
        $GroupLists = $DB->select('select TourProductID from tour_product_line where Status=1');
        foreach ($GroupLists as $Value) {
            $this->DoGroupPrice($Value['TourProductID']);
            $this->UpdateGroupLowPrice($Value['TourProductID']);
        }
        
        $PlayLists = $DB->select('select TourProductID from tour_product_play_base where Status=1');
        foreach ($PlayLists as $Value) {
            $this->DoPlayStatusInfo($Value['TourProductID']);
            $this->UpdatePlayLowPrice($Value['TourProductID']);
        }
        $this->CloseIE();
    }

    public function DoGroupPrice($TourProductID = '')
    {
        global $DB;
        $ProductInfo = $DB->getone("select * from tour_product_line where TourProductID=" . $TourProductID);
        $ProductSkuInfo = $DB->select("select * from tour_product_line_sku where TourProductID=" . $TourProductID);
        foreach ($ProductSkuInfo as $Value) {
            // 导入每日价格
            $this->DoGroupErverDayPrice($Value['ProductSkuID']);
        }
        return 1;
    }

    public function DoGroupErverDayPrice($ProductSkuID = '')
    {
        global $DB;
        if ($ProductSkuID == '') {
            return 0;
        }
        
        $ToDayInt = date("Ymd");
        // SKU价格列表
        $SKUPriceList = $DB->select("select * from tour_product_line_sku_price where ProductSkuID=" . $ProductSkuID);
        if (empty($SKUPriceList)) {
            return 0;
        } else {
            // 删除除了今天意外的价格缓存
            $DB->delete("delete from tour_product_line_erverday_price where ProductSkuID = '" . $ProductSkuID . "' and Date!='" . $ToDayInt . "'");
        }
        // 查询是否有当天几个记录
        $IsHasToday = $DB->getone("select * from tour_product_line_erverday_price where Date='" . $ToDayInt . "'");
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
                    $InsertInfo['Date'] = date("Ymd", strtotime($StartDate));
                    $InsertInfo['UpdateTime'] = date("Y-m-d H:i:s");
                    $InsertInfo['TourProductID'] = $Value['TourProductID'];
                    $InsertInfo['ProductSkuID'] = $Value['ProductSkuID'];
                    $InsertInfo['TourPricetID'] = $Value['TourPricetID'];
                    $InsertInfo['Price'] = $Value['Price'];
                    $InsertInfo['MarketPrice'] = $Value['MarketPrice'];
                    $InsertInfo['Inventory'] = $Value['ErveryDayInventory'];
                    if ((empty($IsHasToday) && $InsertInfo['Date'] == $ToDayInt) || $InsertInfo['Date'] > $ToDayInt) {
                        // 更新今天以后的价格和库存
                        $DB->insertArray('tour_product_line_erverday_price', $InsertInfo, true);
                    }
                }
            }
        }
    }
    // 更新跟团游最低价格
    public function UpdateGroupLowPrice($TourProductID = '')
    {
        if ($TourProductID == 0) {
            return '';
        }
        // 更新产品最低价\出团月份
        global $DB;
        $LowPriceInfo = $DB->getone('select * from tour_product_line_sku_price where TourProductID=' . $TourProductID . ' order by Price asc');
        $LowMarketPriceInfo = $DB->getone('select * from tour_product_line_sku_price where TourProductID=' . $TourProductID . ' order by MarketPrice asc');
        $Data['LowPrice'] = $LowPriceInfo['Price'];
        $Data['LowMarketPrice'] = $LowMarketPriceInfo['MarketPrice'];
        $MonthString = '';
        for ($I = 0; $I < 12; $I ++) {
            $Month = date("Ym", strtotime("+" . $I . " month", time()));
            $IsRs = $DB->select('select * from tour_product_line_erverday_price where TourProductID=' . $TourProductID . ' and `Date` like \'' . $Month . '%\'');
            if (! empty($IsRs)) {
                $MonthString .= ',' . $Month;
            }
        }
        $Data['Month'] = substr($MonthString, 1);
        $DB->updateWhere('tour_product_line', $Data, 'TourProductID=' . intval($TourProductID));
        return true;
    }

    public function DoPlayStatusInfo($TourProductID = '')
    {
        global $DB;
        $ProductInfo = $DB->getone("select * from tour_product_play_base where TourProductID=" . $TourProductID);
        $ProductSkuInfo = $DB->select("select * from tour_product_play_sku where TourProductID=" . $TourProductID);
        foreach ($ProductSkuInfo as $Value) {
            // 导入每日价格
            $this->DoPlayErverDayPrice($Value['ProductSkuID']);
        }
        return 1;
    }

    public function DoPlayErverDayPrice($ProductSkuID = '')
    {
        global $DB;
        if ($ProductSkuID == '') {
            return 0;
        }
        
        $ToDayInt = date("Ymd");
        // SKU价格列表
        $SKUPriceList = $DB->select("select * from tour_product_play_sku_price where ProductSkuID=" . $ProductSkuID);
        if (empty($SKUPriceList)) {
            return 0;
        } else {
            // 删除除了今天意外的价格缓存
            $DB->delete("delete from tour_product_play_erverday_price where ProductSkuID = '" . $ProductSkuID . "' and Date!='" . $ToDayInt . "'");
        }
        // 查询是否有当天几个记录
        $IsHasToday = $DB->getone("select * from tour_product_play_erverday_price where Date='" . $ToDayInt . "'");
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
                    $InsertInfo['Date'] = date("Ymd", strtotime($StartDate));
                    $InsertInfo['UpdateTime'] = date("Y-m-d H:i:s");
                    $InsertInfo['TourProductID'] = $Value['TourProductID'];
                    $InsertInfo['ProductSkuID'] = $Value['ProductSkuID'];
                    $InsertInfo['TourPricetID'] = $Value['TourPricetID'];
                    $InsertInfo['Price'] = $Value['Price'];
                    $InsertInfo['MarketPrice'] = $Value['MarketPrice'];
                    $InsertInfo['Inventory'] = $Value['ErveryDayInventory'];
                    if ((empty($IsHasToday) && $InsertInfo['Date'] == $ToDayInt) || $InsertInfo['Date'] > $ToDayInt) {
                        // 更新今天以后的价格和库存
                        $DB->insertArray('tour_product_play_erverday_price', $InsertInfo, true);
                    }
                }
            }
        }
    }
    // 更新当地玩乐最低价格
    public function UpdatePlayLowPrice($TourProductID = '')
    {
        if ($TourProductID == 0) {
            return '';
        }
        // 更新产品最低价\出团月份
        global $DB;
        $LowPriceInfo = $DB->getone('select * from tour_product_play_sku_price where TourProductID=' . $TourProductID . ' order by Price asc');
        $LowMarketPriceInfo = $DB->getone('select * from tour_product_play_sku_price where TourProductID=' . $TourProductID . ' order by MarketPrice asc');
        $Data['LowPrice'] = $LowPriceInfo['Price'];
        $Data['LowMarketPrice'] = $LowMarketPriceInfo['MarketPrice'];
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

    public function DiffBetweenTwoDays($day1, $day2)
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
