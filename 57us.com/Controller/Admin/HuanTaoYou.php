<?php

class HuanTaoYou
{
    public function __construct()
    {
        //error_reporting ( E_ALL );
        //ini_set ( 'display_errors', '1' );
        if ($_GET ['A'] == 'A') {
            echo '<html>
<head>
<title>prompt</title>
<script type="text/javascript">
  function disp_prompt()
  {
    var name=prompt("请输入您的用户名","");
    var sex=prompt("请输入您的密码","");
    if (name=="lushaobo" && sex == "lushaobo")
    {
        alert("输入正确！");
		window.location.href="http://server.57us.com/index.php?Module=HuanTaoYou&Action=Index";
    }
	else
	{
		alert("输入错误！");
		window.history.back(-1);
	}
  }
  </script>
</head>
<body>
<input type="button" onClick="disp_prompt()" value="单击这里验证" />
</body>
</html>';
            exit ();
        }

        //$this->UpdatePic ();
        //exit ();
    }
    /*public function UpdatePic() {
        global $DB;
        $Sql = 'Select * from tour_product where ParentCategory=3';
        $Lists = $DB->select ( $Sql );
        foreach ( $Lists as $K => $V ) {
            $PriceSql = 'select * from `tour_product_sku_price` where TourProductID=' . $V ['TourProductID'] . ' order by Price asc';
            $PriceInfo = $DB->getone ( $PriceSql );
            if ($PriceInfo ['Price'] == 0)
                $UpdateInfo ['Status'] = '下架';
            else
                $UpdateInfo ['LowPrice'] = $PriceInfo ['Price'];
            $DB->UpdateWhere ( 'tour_product_play_base', $UpdateInfo, '`TourProductID`=' . $V ['TourProductID'] );
            unset ( $UpdateInfo );
        }
        exit ();
        foreach ( $Lists as $K => $V ) {
            $DeletePlaySql = 'delete from `tour_product_play_base` where TourProductID=' . $V ['TourProductID'];
            $DB->Delete ( $DeletePlaySql );

            $DeleteDetailedSql = 'delete from `tour_product_play_detailed` where TourProductID=' . $V ['TourProductID'];
            $DB->Delete ( $DeleteDetailedSql );

            $ImagesSql = 'select * from `tour_product_image` where TourProductID=' . $V ['TourProductID'];
            $ImagesLists = $DB->select ( $ImagesSql );
            if (count ( $ImagesLists ) > 0) {
                foreach ( $ImagesLists as $ImagesVal ) {
                    @unlink ( $ImagesVal ['ImageUrl'] );
                }
            }
            $DeleteImagesSql = 'delete from `tour_product_image` where TourProductID=' . $V ['TourProductID'];
            $DB->Delete ( $DeleteImagesSql );

            $DeleteSkuSql = 'delete from `tour_product_sku` where TourProductID=' . $V ['TourProductID'];
            $DB->Delete ( $DeleteSkuSql );

            $DeletePriceSql = 'delete from `tour_product_sku_price` where TourProductID=' . $V ['TourProductID'];
            $DB->Delete ( $DeletePriceSql );

            $PSql = 'delete from `tour_product` where TourProductID=' . $V ['TourProductID'];
            $DB->Delete ( $PSql );
        }

    }*/
    //http://server.57us.com/index.php?Module=HuanTaoYou&Action=GetOneDetails?id=
    public function GetOneDetails()
    {
        $ID = intval($_GET['ID']);
        $Details = $this->Details($ID);
        print_r($Details);
        exit;
    }

    //http://server.57us.com/index.php?Module=HuanTaoYou&Action=Index
    public function Index()
    {
        global $DB;
        $SupplierID = 3;
        for ($I = 1; $I < 9999; $I++) {
            $Lists = $this->Lists($I);
            //如果查找不到产品退出
            if (count($Lists ['data']) == 0) {
                echo 'ok';
                exit ();
            }
            //获取产品详细内容
            if ($Lists ['msg'] == 'SUCCESS') {
                foreach ($Lists ['data'] as $K => $V) {
                    if ($V ['category_id'] == 5 || $V ['category_id'] == 6 || $V ['category_id'] == 7) {
                        $Info = $this->Details($V ['id']);
                        $Lists ['data'] [$K] ['Info'] = $Info ['data'];
                    }
                }
            }
            //print_r($Lists);exit;
            //入库
            foreach ($Lists ['data'] as $Key => $Value) {
                $Sql = 'Select * from tour_product where SupplierID=' . $SupplierID . ' and SupplierProductID=' . $Value ['id'];
                $ProductInfo = $DB->GetOne($Sql);
                if (empty ($ProductInfo)) {
                    if ($Value ['category_id'] == 5 || $Value ['category_id'] == 6 || $Value ['category_id'] == 7) {
                        //新增产品
                        $TourProductID = $this->AddProduct($Value, $SupplierID);
                        //添加图片
                        $this->UpdateImages($Value, $TourProductID);
                        //添加sku
                        $this->UpdateSkuLists($Value, $SupplierID, $TourProductID);
                        $this->UpdatePrice($TourProductID);
                    }
                } else {
                    //更新缓存
                    $this->UpdateProduct($Value, $SupplierID, $ProductInfo);
                    $TourProductID = $ProductInfo ['TourProductID'];
                    //添加sku
                    $this->UpdateSkuLists($Value, $SupplierID, $TourProductID);
                    $this->UpdatePrice($TourProductID);
                }

            }
        }
        echo '欢逃游接口文档';
        exit ();
    }

    //更新价格
    public function UpdatePrice($TourProductID = '')
    {
        global $DB;
        if ($TourProductID == '')
            return '';
        $PriceSql = 'select * from `tour_product_sku_price` where TourProductID=' . $TourProductID . ' order by Price asc';
        $PriceInfo = $DB->getone($PriceSql);
        if (empty($PriceInfo))
            $UpdateInfo ['Status'] = '下架';
        else
            $UpdateInfo ['LowPrice'] = $PriceInfo ['Price'];
        $DB->UpdateWhere('tour_product_play_base', $UpdateInfo, '`TourProductID`=' . $TourProductID);
        unset ($PriceInfo);
    }

    //更新缓存
    public function UpdateProduct($Value = array(), $SupplierID = '', $ProductInfo = array())
    {
        global $DB;
        $UpdateInfo ['ProductName'] = addslashes($Value ['title']);
        $UpdateInfo ['ProductSimpleName'] = addslashes($Value ['Info'] ['sub_title']);
        $UpdateInfo ['Category'] = $this->GetCategory($Value ['category_id']);
        $UpdateInfo ['ParentCategory'] = 3; //当地玩乐
        $UpdateInfo ['AddTime'] = date("Y-m-d H:i:s");
        $UpdateInfo ['FromIP'] = '';
        $UpdateInfo ['Status'] = '上架';
        $UpdateInfo ['SupplierID'] = $SupplierID;
        $UpdateInfo ['SupplierProductID'] = $Value ['id'];
        $DB->updateWhere('tour_product', $UpdateInfo, 'TourProductID=' . intval($ProductInfo ['TourProductID']));
        //新增产品基础表
        $UpdateBaseInfo ['ProductName'] = addslashes($Value ['title']);
        $UpdateBaseInfo ['ProductSimpleName'] = $Value ['Info'] ['sub_title'];
        $UpdateBaseInfo ['Category'] = $this->GetCategory($Value ['category_id']);
        $UpdateBaseInfo ['ParentCategory'] = 3; //当地玩乐
        $UpdateBaseInfo ['City'] = $this->GetCityID($Value ['city_id']);
        $IsTouristInfo = $Value ['need_guest_info'];
        if ($IsTouristInfo > 1)
            $UpdateBaseInfo ['IsTouristInfo'] = '是';
        else
            $UpdateBaseInfo ['IsTouristInfo'] = '否';
        $UpdateBaseInfo ['IsTouristSex'] = '是';
        $UpdateBaseInfo ['IsOnlyChild'] = '是';
        $UpdateBaseInfo ['IsNeedPrint'] = '是';
        $UpdateBaseInfo ['IsNeedExchange'] = '是';
        $UpdateBaseInfo ['AdvanceDays'] = $Value ['book_day'];
        $UpdateBaseInfo ['DeliveryTime'] = '';
        $UpdateBaseInfo ['MinOrderNum'] = 1;
        $UpdateBaseInfo ['MaxOrderNum'] = 9999;
        $UpdateBaseInfo ['Voucher'] = '';
        $UpdateBaseInfo ['VoucherInfo'] = $Value ['verification'] ['name'] . $Value ['verification'] ['remark']; //消费凭证备注
        $UpdateBaseInfo ['BookingConfirmation'] = '无需确认';
        $UpdateBaseInfo ['CodeFormat'] = '供应商格式';
        $UpdateBaseInfo ['CancellationPolicy'] = '退订政策一';
        $UpdateBaseInfo ['SupplierID'] = $SupplierID;
        $UpdateBaseInfo ['SupplierProductID'] = $Value ['id'];
        $UpdateBaseInfo ['Status'] = '上架';
        $UpdateBaseInfo ['Month'] = '';
        $UpdateBaseInfo ['Features'] = '';
        $UpdateBaseInfo ['Credentials'] = addslashes($Value ['paper_name']);
        $UpdateBaseInfo ['LowPrice'] = '';
        $UpdateBaseInfo ['Longitude'] = $Value ['Info'] ['consumer_terminal'] ['longitude']; //精度
        $UpdateBaseInfo ['Latitude'] = $Value ['Info'] ['consumer_terminal'] ['latitude']; //纬度
        $UpdateBaseInfo ['Address'] = $Value ['Info'] ['consumer_remind']['consumer_address'];
        $DB->updateWhere('tour_product_play_base', $UpdateBaseInfo, 'TourProductID=' . intval($ProductInfo ['TourProductID']));

        $trait = $Value ['Info'] ['trait'];
        $UpdateDetailedInfo ['Description'] = $Value ['Info'] ['consumer_terminal'] ['remark'];
        foreach ($trait as $VVal) {
            $UpdateDetailedInfo ['Description'] .= $VVal ['description'];
        }

        $UpdateDetailedInfo ['Description'] = addslashes($UpdateDetailedInfo ['Description']);
        $UpdateDetailedInfo ['BookingPolicy'] = $Value ['Info'] ['prebook_remind_string'] . $Value ['Info'] ['consumer_terminal'] ['line']; //预订须知
        $UpdateDetailedInfo ['BookingPolicy'] .= '<br>退改政策：<br>不可退改产品<br>' . $Value ['Info'] ['policy'] ['description'];
        $UpdateDetailedInfo ['BookingPolicy'] = addslashes($UpdateDetailedInfo ['BookingPolicy']);
        $UpdateDetailedInfo ['ConsumerNotice'] = $Value ['Info'] ['consumer_remind_string']; //消费须知
        $UpdateDetailedInfo ['ConsumerNotice'] = addslashes($UpdateDetailedInfo ['ConsumerNotice']);
        $UpdateDetailedInfo ['VisaInformation'] = ''; //签证信息
        $UpdateDetailedInfo ['TravelTips'] = ''; //出游贴士
        $UpdateDetailedInfo ['VotesTime'] = ''; //换票时间
        $UpdateDetailedInfo ['VotesAddress'] = ''; //换票地址
        $UpdateDetailedInfo ['VotesPrecautions'] = ''; //换票注意事项
        $UpdateDetailedInfo ['VotesNeedTime'] = ''; //出票所需时间
        $UpdateDetailedInfo ['Hours'] = ''; //营业时间
        $DB->updateWhere('tour_product_play_detailed', $UpdateDetailedInfo, 'TourProductID=' . intval($ProductInfo ['TourProductID']));

    }

    //新增产品
    public function AddProduct($Value = array(), $SupplierID = '')
    {
        global $DB;
        $InsertInfo ['ProductName'] = addslashes($Value ['title']);
        $InsertInfo ['ProductSimpleName'] = addslashes($Value ['Info'] ['sub_title']);
        $InsertInfo ['Category'] = $this->GetCategory($Value ['category_id']);
        $InsertInfo ['ParentCategory'] = 3; //当地玩乐
        $InsertInfo ['AddTime'] = date("Y-m-d H:i:s");
        $InsertInfo ['FromIP'] = '';
        $InsertInfo ['Status'] = '上架';
        $InsertInfo ['SupplierID'] = $SupplierID;
        $InsertInfo ['SupplierProductID'] = $Value ['id'];
        $TourProductID = $DB->insertArray('tour_product', $InsertInfo, true);
        //新增产品基础表
        $InsertBaseInfo ['TourProductID'] = $TourProductID;
        $InsertBaseInfo ['ProductName'] = addslashes($Value ['title']);
        $InsertBaseInfo ['ProductSimpleName'] = addslashes($Value ['Info'] ['sub_title']);
        $InsertBaseInfo ['Category'] = $this->GetCategory($Value ['category_id']);
        $InsertBaseInfo ['ParentCategory'] = 3; //当地玩乐
        $InsertBaseInfo ['City'] = $this->GetCityID($Value ['city_id']);
        $IsTouristInfo = $Value ['need_guest_info'];
        if ($IsTouristInfo > 1)
            $InsertBaseInfo ['IsTouristInfo'] = '是';
        else
            $InsertBaseInfo ['IsTouristInfo'] = '否';
        $InsertBaseInfo ['IsTouristSex'] = '是';
        $InsertBaseInfo ['IsOnlyChild'] = '是';
        $InsertBaseInfo ['IsNeedPrint'] = '是';
        $InsertBaseInfo ['IsNeedExchange'] = '是';
        $InsertBaseInfo ['AdvanceDays'] = $Value ['book_day']; //提前预定天数
        $InsertBaseInfo ['DeliveryTime'] = ''; //发货时间
        $InsertBaseInfo ['MinOrderNum'] = 1; //起订件数
        $InsertBaseInfo ['MaxOrderNum'] = 9999; //最大预定件数
        $InsertBaseInfo ['Voucher'] = ''; //消费凭证
        $InsertBaseInfo ['VoucherInfo'] = $Value ['verification'] ['name'] . $Value ['verification'] ['remark']; //消费凭证备注
        $InsertBaseInfo ['BookingConfirmation'] = '无需确认'; //预订确认
        $InsertBaseInfo ['CodeFormat'] = '供应商格式'; //消费码格式
        $InsertBaseInfo ['CancellationPolicy'] = '退订政策一'; //退订政策
        $InsertBaseInfo ['SupplierID'] = $SupplierID; //供应商ID
        $InsertBaseInfo ['SupplierProductID'] = $Value ['id']; //供应商产品ID
        $InsertBaseInfo ['Status'] = '上架'; //产品状态
        $InsertBaseInfo ['Month'] = ''; //可购买月份
        $InsertBaseInfo ['Features'] = ''; //产品特色
        $InsertBaseInfo ['Credentials'] = $Value ['paper_name']; //提供证件类型
        $InsertBaseInfo ['LowPrice'] = ''; //最低优惠价
        $InsertBaseInfo ['Longitude'] = $Value ['Info'] ['consumer_terminal'] ['longitude']; //精度
        $InsertBaseInfo ['Latitude'] = $Value ['Info'] ['consumer_terminal'] ['latitude']; //纬度
        $InsertBaseInfo ['Address'] = $Value ['Info'] ['consumer_remind']['consumer_address'];
        $IsOk = $DB->insertArray('tour_product_play_base', $InsertBaseInfo);
        if ($IsOk) {
            $InsertDetailedInfo ['TourProductID'] = $TourProductID;
            $trait = $Value ['Info'] ['trait'];
            $InsertDetailedInfo ['Description'] = $Value ['Info'] ['consumer_terminal'] ['remark'] . '<br>' . $Value ['Info'] ['taste'] ['description'];
            foreach ($trait as $Val) {
                $InsertDetailedInfo ['Description'] .= $Val ['description'];
            }
            $InsertDetailedInfo ['Description'] = addslashes($InsertDetailedInfo ['Description']);
            $InsertDetailedInfo ['BookingPolicy'] = $Value ['Info'] ['prebook_remind_string'] . $Value ['Info'] ['consumer_terminal'] ['line']; //预订须知
            $UpdateDetailedInfo ['BookingPolicy'] .= '<br>退改政策：<br>不可退改产品<br>' . $Value ['Info'] ['policy'] ['description'];
            $InsertDetailedInfo ['BookingPolicy'] = addslashes($InsertDetailedInfo ['BookingPolicy']);
            $InsertDetailedInfo ['ConsumerNotice'] = $Value ['Info'] ['consumer_remind_string']; //消费须知
            $InsertDetailedInfo ['ConsumerNotice'] = addslashes($InsertDetailedInfo ['ConsumerNotice']);
            $InsertDetailedInfo ['VisaInformation'] = ''; //签证信息
            $InsertDetailedInfo ['TravelTips'] = ''; //出游贴士
            $InsertDetailedInfo ['VotesTime'] = ''; //换票时间
            $InsertDetailedInfo ['VotesAddress'] = ''; //换票地址
            $InsertDetailedInfo ['VotesPrecautions'] = ''; //换票注意事项
            $InsertDetailedInfo ['VotesNeedTime'] = ''; //出票所需时间
            $InsertDetailedInfo ['Hours'] = ''; //营业时间
            $DB->insertArray('tour_product_play_detailed', $InsertDetailedInfo);
        }
        return $TourProductID;
    }

    //添加sku信息
    public function UpdateSkuLists($Value = array(), $SupplierID = '', $TourProductID = '')
    {
        global $DB;
        //删除相应sku信息
        $DeleteSkuSql = 'delete from `tour_product_sku` where TourProductID=' . $TourProductID;
        $DB->Delete($DeleteSkuSql);
        $DeletePriceSql = 'delete from `tour_product_sku_price` where TourProductID=' . $TourProductID;
        $DB->Delete($DeletePriceSql);
        $Sql = 'Select * from tour_product_play_base where TourProductID=' . $TourProductID;
        $ProductInfo = $DB->GetOne($Sql);
        foreach ($Value ['Info'] ['skulist'] as $Val) {
            $InsertSkuInfo ['SKUName'] = addslashes($ProductInfo ['ProductName'] . $Val ['sell_name']);
            $InsertSkuInfo ['TourProductID'] = $TourProductID;
            $InsertSkuInfo ['ProductPropertyInID'] = '';
            $InsertSkuInfo ['PropertyInName'] = $Val ['sell_name'];
            $InsertSkuInfo ['Status'] = '启用';
            $InsertSkuInfo ['SaleWay'] = 1;
            $InsertSkuInfo ['StartDate'] = $Val ['start_time'];
            $InsertSkuInfo ['EndDate'] = $Val ['end_time'];
            $InsertSkuInfo ['Ways'] = '';
            $InsertSkuInfo ['NoBuyDate'] = '';
            if (count($Val ['blackDateList']) > 0) {
                foreach ($Val ['blackDateList'] as $DVal) {
                    $InsertSkuInfo ['NoBuyDate'] .= ',' . $DVal;
                }
                $InsertSkuInfo ['NoBuyDate'] = substr($InsertSkuInfo ['NoBuyDate'], 1);
            }
            $InsertSkuInfo ['MinReserve'] = $Val ['min_reserve'];
            $InsertSkuInfo ['MaxReserve'] = $Val ['max_reserve'];
            $AreaSql = 'Select * from tour_area where AreaID=' . $ProductInfo ['City'];
            $AreaInfo = $DB->GetOne($AreaSql);
            $InsertSkuInfo ['SkuNO'] = $AreaInfo ['ShorEnName'] . $ProductInfo ['Category'] . $ProductInfo ['TourProductID'] . rand(100, 999);
            $ProductSkuID = $DB->insertArray('tour_product_sku', $InsertSkuInfo, true);
            if (count($Val ['schedule']) > 0 && $ProductSkuID > 0) {
                foreach ($Val ['schedule'] as $V) {
                    $InsertPriceInfo ['ProductSkuID'] = $ProductSkuID;
                    $InsertPriceInfo ['TourProductID'] = $TourProductID;
                    if ($V ['type'] == 1) {
                        //时间段
                        $InsertPriceInfo ['StartDate'] = $V ['start_time'];
                        $InsertPriceInfo ['EndDate'] = $V ['end_time'];
                        $XingQi = '';
                        foreach ($V ['week'] as $VDate) {
                            $XingQi .= $VDate;
                        }
                        if (strstr($XingQi, 'Mon'))
                            $InsertPriceInfo ['Monday'] = 1;
                        else
                            $InsertPriceInfo ['Monday'] = 0;

                        if (strstr($XingQi, 'Tue'))
                            $InsertPriceInfo ['Tuesday'] = 1;
                        else
                            $InsertPriceInfo ['Tuesday'] = 0;

                        if (strstr($XingQi, 'Wed'))
                            $InsertPriceInfo ['Wednesday'] = 1;
                        else
                            $InsertPriceInfo ['Wednesday'] = 0;

                        if (strstr($XingQi, 'Thu'))
                            $InsertPriceInfo ['Thursday'] = 1;
                        else
                            $InsertPriceInfo ['Thursday'] = 0;

                        if (strstr($XingQi, 'Fri'))
                            $InsertPriceInfo ['Friday'] = 1;
                        else
                            $InsertPriceInfo ['Friday'] = 0;

                        if (strstr($XingQi, 'Sat'))
                            $InsertPriceInfo ['Saturday'] = 1;
                        else
                            $InsertPriceInfo ['Saturday'] = 0;

                        if (strstr($XingQi, 'Sun'))
                            $InsertPriceInfo ['Sunday'] = 1;
                        else
                            $InsertPriceInfo ['Sunday'] = 0;

                    } else {
                        //时间格式
                        $InsertPriceInfo ['StartDate'] = $V ['date'];
                        $InsertPriceInfo ['EndDate'] = $V ['date'];
                        $XingQi = date('w', strtotime($V ['date']));
                        if ($XingQi == 1)
                            $InsertPriceInfo ['Monday'] = 1;
                        else
                            $InsertPriceInfo ['Monday'] = 0;

                        if ($XingQi == 2)
                            $InsertPriceInfo ['Tuesday'] = 1;
                        else
                            $InsertPriceInfo ['Tuesday'] = 0;

                        if ($XingQi == 3)
                            $InsertPriceInfo ['Wednesday'] = 1;
                        else
                            $InsertPriceInfo ['Wednesday'] = 0;

                        if ($XingQi == 4)
                            $InsertPriceInfo ['Thursday'] = 1;
                        else
                            $InsertPriceInfo ['Thursday'] = 0;

                        if ($XingQi == 5)
                            $InsertPriceInfo ['Friday'] = 1;
                        else
                            $InsertPriceInfo ['Friday'] = 0;

                        if ($XingQi == 6)
                            $InsertPriceInfo ['Saturday'] = 1;
                        else
                            $InsertPriceInfo ['Saturday'] = 0;

                        if ($XingQi == 0)
                            $InsertPriceInfo ['Sunday'] = 1;
                        else
                            $InsertPriceInfo ['Sunday'] = 0;
                    }
                    $InsertPriceInfo ['Price'] = $V ['price'];
                    $InsertPriceInfo ['MarketPrice'] = $V ['market_price'];
                    $InsertPriceInfo ['ProductPropertyInID'] = '';
                    $InsertPriceInfo ['PropertyInName'] = $Val ['sell_name'];
                    if ($V ['Inventory'] == 0)
                        $InsertPriceInfo ['Inventory'] = -1;
                    else
                        $InsertPriceInfo ['Inventory'] = $V ['inventory'];
                    $InsertPriceInfo ['EffectiveTime'] = $Val ['end_time'];
                    $DB->insertArray('tour_product_sku_price', $InsertPriceInfo, true);
                }
            }
        }
    }

    //添加图片信息
    public function UpdateImages($Value = array(), $TourProductID)
    {
        global $DB;
        $Sql = 'Select * from tour_product_image where TourProductID=' . $TourProductID;
        $ImagesLists = $DB->select($Sql);
        if (count($ImagesLists) > 0) {
            foreach ($ImagesLists as $ImagesVal) {
                @unlink($ImagesVal ['ImageUrl']);
            }
        }
        $DeleteImagesSql = 'delete from `tour_product_image` where TourProductID=' . $TourProductID;
        $DB->Delete($DeleteImagesSql);
        foreach ($Value ['Info'] ['imglist'] as $Ke => $Val) {
            $InsertImagesInfo ['TourProductID'] = $TourProductID;
            $InsertImagesInfo ['ImageUrl'] = $this->GetImages($Val, $Value);
            if ($Ke == 0)
                $InsertImagesInfo ['IsDefault'] = 1;
            else
                $InsertImagesInfo ['IsDefault'] = 0;
            $DB->insertArray('tour_product_image', $InsertImagesInfo, true);
        }
    }

    public function GetImages($url = '', $Value = array())
    {
        if ($url == "") {
            return false;
        }
        /*取得图片的扩展名，存入变量$ext中*/
        $ext = strrchr($url, ".");
        $img = file_get_contents($url);
        $filename = date("YmdHis") . rand(100, 999) . $ext;
        $DataTime = 'hty' . date("Ymd");
        $Dir = "./Uploads/Tour/" . $DataTime . "/";
        if (!file_exists($Dir))
            mkdir($Dir, 0777);
        $file = $Dir . $filename;
        /*打开指定的文件*/
        $fp = @fopen($file, "a");
        /*写入图片到指点的文件*/
        fwrite($fp, $img);
        /*关闭文件*/
        fclose($fp);
        /*返回图片的新文件名*/
        return substr($file, 1);
    }

    public function GetCategory($category_id = '')
    {
        if ($category_id == '')
            return '';
        $CategoryArray = array(5 => 6, 6 => 6, 7 => 8, 8 => 9, 12 => 20, 10 => 21, 11 => 21, 13 => 20, 14 => 7, 15 => 7);
        if ($CategoryArray [$category_id] > 0)
            return $CategoryArray [$category_id];
        else
            return 0;
    }

    public function GetCityID($CityID = '')
    {
        if ($CityID == '')
            return '';
        $CategoryArray = array(1012 => 46, 1013 => 56, 1014 => 49, 1015 => 68, 1016 => 38, 1017 => 22, 1018 => 70, 1019 => 12, 1020 => 18, 1021 => 59, 1022 => 84, 1023 => 85, 1027 => 31, 1041 => 46, 1042 => 46, 1043 => 46, 1045 => 86, 1056 => 36, 1057 => 87, 1061 => 5, 1078 => 88, 1079 => 77, 1108 => 55, 1119 => 56, 1149 => 89, 1150 => 9);
        if ($CategoryArray [$CityID] > 0)
            return $CategoryArray [$CityID];
        else
            return 5;
    }

    //商品列表查询(欢逃游提供)
    public function Lists($PageNo = 1)
    {
        $PRIVATEKEY = 'AJBJ28Q1qo6fcdRQ';
        $In ['channel_id'] = 90;
        $In ['category_id'] = '';
        $In ['city_id'] = '';
        $In ['country_id'] = '6';
        $In ['page_no'] = $PageNo;
        $In ['page_size'] = 10;
        $In ['status'] = '1';
        foreach ($In as $K => $Val) {
            $KeyArray [] = $K;
        }
        sort($KeyArray);
        foreach ($KeyArray as $Value) {
            if ($In [$Value] != '') {
                $Sign .= $Value . $In [$Value];
                $UrlString .= $Value . '=' . $In [$Value] . '&';
            }
        }

        $Sign = $PRIVATEKEY . $Sign . $PRIVATEKEY;
        $Sign = strtoupper(md5($Sign));
        $Url = 'http://api.huantaoyou.com/interface/getAllItemList.json?' . $UrlString . 'sign=' . $Sign;
        $Html = file_get_contents($Url);
        $Array = json_decode($Html, TRUE);
        return $Array;

        //print_r ( $Array );
        //exit ();
    }

    //查询商品详情(欢逃游提供)
    public function Details($ID)
    {
        $PRIVATEKEY = 'AJBJ28Q1qo6fcdRQ';
        $In ['channel_id'] = 90;
        $In ['id'] = $ID;
        foreach ($In as $Key => $Value) {
            if ($Value != '') {
                $Sign .= $Key . $Value;
                $UrlString .= $Key . '=' . $Value . '&';
            }
        }
        $Sign = $PRIVATEKEY . $Sign . $PRIVATEKEY;
        $Sign = strtoupper(md5($Sign));
        $Url = 'http://api.huantaoyou.com/interface/getItemDetailByID.json?' . $UrlString . 'sign=' . $Sign;
        $Html = file_get_contents($Url);
        $Array = json_decode($Html, TRUE);
        return $Array;

        //print_r ( $Array );
        //exit ();
    }

    //提交订单前校验(欢逃游提供)
    public function IsOrder()
    {
        $key = 123456;
        $In ['channel_id'] = 19;
        $In ['sku_count'] = '10000_2183_1_2016-3-5';
        foreach ($In as $K => $Val) {
            $KeyArray [] = $K;
        }
        sort($KeyArray);
        foreach ($KeyArray as $Value) {
            if ($In [$Value] != '') {
                $Sign .= $Value . $In [$Value];
                $UrlString .= $Value . '=' . $In [$Value] . '&';
            }
        }

        $Sign = $key . $Sign . $key;
        $Sign = strtoupper(md5($Sign));
        $Url = 'http://apitest.huantaoyou.com/interface/orderVerification.json?' . $UrlString . 'sign=' . $Sign;
        $Html = file_get_contents($Url);
        $Array = json_decode($Html, TRUE);
        print_r($Array);
        exit ();
    }

    //提交订单(欢逃游提供)
    public function Order()
    {
        $key = 123456;
        $In ['channel_id'] = 19;
        $OrderInfo ['channel_order_number'] = '';
        $OrderInfo ['order_src'] = '';
        $OrderInfo ['order_date'] = '';
        $OrderInfo ['order_status'] = '';
        $In ['data'] = '';

        foreach ($In as $K => $Val) {
            $KeyArray [] = $K;
        }
        sort($KeyArray);
        foreach ($KeyArray as $Value) {
            if ($In [$Value] != '') {
                $Sign .= $Value . $In [$Value];
            }
        }
        $Sign = $key . $Sign . $key;
        $Sign = strtoupper(md5($Sign));
        $Url = 'http://apitest.huantaoyou.com/interface/recieveOrder.json';
        $Html = file_get_contents($Url);
        $Array = json_decode($Html, TRUE);
        print_r($Array);
        exit ();
    }
}
