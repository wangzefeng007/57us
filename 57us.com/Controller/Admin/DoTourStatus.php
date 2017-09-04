<?php

/* 定时更新旅游产品下架，并取消推荐 http://admin.57us.com/index.php?Module=DoTourStatus&Action=DoTourStatus */
class DoTourStatus
{

    public function __construct()
    {
        set_time_limit(0);
        ini_set('display_errors', '1');
    }
    /**
     * @desc  关闭浏览器
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
    public function DoTourStatus()
    {
        global $DB;
        // 当地玩乐价格更新
        $GroupLists = $DB->select('select TourProductID from tour_product_line where Status=1');
        foreach ($GroupLists as $Value) {
            $this->DoGroupStatusInfo($Value['TourProductID']);
        }
        // 跟团游批量去掉已经下架的推荐
        $GroupUpdateInfo['R1'] = 0;
        $GroupUpdateInfo['R2'] = 0;
        $GroupUpdateInfo['R3'] = 0;
        $DB->updateWhere('tour_product_line', $GroupUpdateInfo, 'Status=0');
        
        $PlayLists = $DB->select('select TourProductID from tour_product_play_base where Status=1');
        foreach ($PlayLists as $Value) {
            $this->DoPlayStatusInfo($Value['TourProductID']);
        }
        // 当地玩乐批量去掉已经下架的推荐
        $PlayUpdateInfo['R1'] = 0;
        $PlayUpdateInfo['R2'] = 0;
        $DB->updateWhere('tour_product_play_base', $PlayUpdateInfo, 'Status=0');
        
        $this->CloseIE();
    }

    public function DoGroupStatusInfo($TourProductID = '')
    {
        global $DB;
        if ($TourProductID == '') {
            return '';
        }
        $ToDay = intval(date("Ymd"));
        $TourProductInfo = $DB->getone('select * from tour_product_line_erverday_price where TourProductID=' . $TourProductID . ' and (Date=' . $ToDay . ' or Date>' . $ToDay . ') and Inventory != 0');
        if (empty($TourProductInfo)) {
            // 没有可购买的日期，直接下架
            $UpdateInfo['Status'] = 0;
            $DB->updateWhere('tour_product_line', $UpdateInfo, 'TourProductID=' . intval($TourProductID));
        }
    }

    public function DoPlayStatusInfo($TourProductID = '')
    {
        global $DB;
        if ($TourProductID == '') {
            return '';
        }
        $ToDay = intval(date("Ymd"));
        $TourProductInfo = $DB->getone('select * from tour_product_play_erverday_price where TourProductID=' . $TourProductID . ' and (Date=' . $ToDay . ' or Date>' . $ToDay . ') and Inventory != 0');
        if (empty($TourProductInfo)) {
            // 没有可购买的日期，直接下架
            $UpdateInfo['Status'] = 0;
            $DB->updateWhere('tour_product_play_base', $UpdateInfo, 'TourProductID=' . intval($TourProductID));
        }
    }
}
