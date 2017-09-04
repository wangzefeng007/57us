<?php

class TourProductOrder
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderInfoModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductOrderModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlayBaseModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductCategoryModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberUserModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlaySkuModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineSkuModule.php';
    }


    public function TourProductOrderList()
    {
        // curl_postsend('http://member.57us.com/smsapi_sendsms',$OrderData);
        $TourProductOrderModule = new TourProductOrderModule();
        $TourProductCategoryModule = new TourProductCategoryModule();
        $StatusInfo = $TourProductOrderModule->Status;
        $SqlWhere = '';
        // 搜索条件
        $PageUrl = '';
        $OrderNumber = trim($_GET['OrderNumber']);
        if ($OrderNumber != '') {
            $SqlWhere .= ' and concat(OrderNumber) like \'%' . $OrderNumber . '%\'';
            $PageUrl .= '&OrderNumber=' . $OrderNumber;
        }
        if ($_GET ['Status']){
            $Status = trim($_GET ['Status']);
            $SqlWhere .=' and `Status` = \''. $Status .'\'';
            $PageUrl .='&Status='.$Status;
        }
        // 跳转到该页面
        if ($_POST['page']) {
            $page = $_POST['page'];
            tourl('/index.php?Module=TourProductOrder&Action=TourProductOrderList&Page=' . $page . $PageUrl);
        }
        // 分页开始
        $Page = intval($_GET['Page']);

        $Page = $Page ? $Page : 1;
        $PageSize = 20;
        $Rscount = $TourProductOrderModule->GetListsNum($SqlWhere);

        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $TourProductOrderModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $Key => $Value) {
                if ($Value['Category'] > 0) {
                    $TourCategoryInfo = $TourProductCategoryModule->GetInfoByKeyID($Value['Category']);
                    $Data['Data'][$Key]['CnName'] = $TourCategoryInfo['CnName'];
                }
            }
            MultiPage($Data, 10);
        }
        // 分页结束
        include template('TourProductOrderList');
    }
    // 订单管理
    public function TourProductOrderEdit()
    {
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductOrderModule = new TourProductOrderModule();
        $TourProductOrderInfoModule = new TourProductOrderInfoModule();
             
        if ($_POST) {
            $OrderID = intval($_POST['OrderID']);
            if ($_POST['submit1']) {
                $ProductOrder = $TourProductOrderModule->GetInfoByKeyID($OrderID);
                $Email = $ProductOrder['Email'];
                $Data['Consumercode'] = trim($_POST['Consumercode']);
                $IsOK = $TourProductOrderModule->UpdateInfoByWhere($Data,  ' OrderID ='. $OrderID);
                if ($IsOK){
                    $mailsubject = "57美国网订单确认函";
                    $Emessage = $this->Emessage($OrderID);
                    ToolService::SendEMailNotice($Email,$mailsubject,$Emessage);
                }
            }
            if ($_POST['submit2']) {
                $Data['Status'] = trim($_POST['Status']);
                $IsOK = $TourProductOrderModule->UpdateInfoByWhere($Data,  ' OrderID ='.$OrderID);
            }
            if ($_POST['submit3']) {
                $Data['Remarks'] = trim($_POST['Remarks']);
                $IsOK = $TourProductOrderModule->UpdateInfoByWhere($Data, ' OrderID ='.$OrderID);
            }
            if ($IsOK) {
                alertandgotopage("操作成功", "/index.php?Module=TourProductOrder&Action=TourProductOrderEdit&OrderID=" . $OrderID);
            } else {
                alertandgotopage("操作未修改", "/index.php?Module=TourProductOrder&Action=TourProductOrderEdit&OrderID=" . $OrderID);
            }
        }else{
            $OrderID = $_GET['OrderID'];
            $ListByOrderID = $TourProductOrderModule->GetInfoByKeyID($OrderID);
            // 出游人信息
            $ListByOrderID['TravelPeopleInfo'] = json_decode($ListByOrderID['TravelPeopleInfo'], true);
            //$ListByOrderID['TravelPeopleInfo'] = $ListByOrderID['TravelPeopleInfo'][0]['name'] . $ListByOrderID['TravelPeopleInfo'][0]['last'];
            $OrderNumber = $ListByOrderID['OrderNumber'];
            $ListByOrderNumber = $TourProductOrderInfoModule->GetInfoByWhere(' and OrderNumber = \'' . $OrderNumber . '\'');
            $TourProductID = $ListByOrderNumber['TourProductID'];
            $ListCategoryID = $TourProductPlayBaseModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            if (! $ListCategoryID) {
                $ListCategoryID = $TourProductLineModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
                $CategoryType=1;
            }else{
                $CategoryType=2;
            }
            $TourProductCategoryModule =new TourProductCategoryModule();
            $CategoryInfo=$TourProductCategoryModule->GetInfoByKeyID($ListCategoryID['Category']);
            // 出发城市
            $TourAreaModule = new TourAreaModule();
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($ListCategoryID['Departure']);
            //套餐信息
            $OrderInfoList = $TourProductOrderInfoModule->GetInfoByWhere(' and OrderNumber = \'' . $OrderNumber . '\'',true);
            if($CategoryType==1){
                $TourProductLineSkuModule=new TourProductLineSkuModule();
                foreach($OrderInfoList as $key=>$val){
                    $SkuInfo=$TourProductLineSkuModule->GetInfoByKeyID($val['TourProductSkuID']);
                    $OrderInfoList[$key]['SkuName']=$SkuInfo['SKUName'];
                    $OrderInfoList[$key]['AdultNum']=$SkuInfo['AdultNum'];
                    $OrderInfoList[$key]['ChildrenNum']=$SkuInfo['ChildrenNum'];
                    $OrderInfoList[$key]['PeopleNum']=$SkuInfo['PeopleNum'];
                }
            }else{
                $TourProductPlaySkuModule=new TourProductPlaySkuModule();
                foreach($OrderInfoList as $key=>$val){
                    $SkuInfo=$TourProductPlaySkuModule->GetInfoByKeyID($val['TourProductSkuID']);
                    $OrderInfoList[$key]['SkuName']=$SkuInfo['SKUName'];
                }
            }
        }
        include template('TourProductOrderEdit');
    }

    public function TourProductOrderAdd()
    {
        $TourProductOrderModule = new TourProductOrderModule();
        include template('TourProductOrderAdd');
    }
    // 订单详情
    public function TourProductOrderDetail()
    {
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductOrderModule = new TourProductOrderModule();
        $TourProductOrderInfoModule = new TourProductOrderInfoModule();
        $OrderID = $_GET['OrderID'];
        $ListByOrderID = $TourProductOrderModule->GetInfoByKeyID($OrderID);
        // 出游人信息
        $ListByOrderID['TravelPeopleInfo'] = json_decode($ListByOrderID['TravelPeopleInfo'], true);


        $OrderNumber = $ListByOrderID['OrderNumber'];
        $ListByOrderNumber = $TourProductOrderInfoModule->GetInfoByWhere(' and OrderNumber = \'' . $OrderNumber . '\'');
        $TourProductID = $ListByOrderNumber['TourProductID'];
        $ListCategoryID = $TourProductPlayBaseModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
        if (!$ListCategoryID) {
            $ListCategoryID = $TourProductLineModule->GetInfoByWhere(' and TourProductID = ' . $TourProductID);
            $CategoryType=1;
        }else{
            $CategoryType=2;
        }
        $TourProductCategoryModule =new TourProductCategoryModule();
        $CategoryInfo=$TourProductCategoryModule->GetInfoByKeyID($ListCategoryID['Category']);
         // 出发城市
        $TourAreaModule = new TourAreaModule();
        $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($ListCategoryID['Departure']);
        //套餐信息
        $OrderInfoList = $TourProductOrderInfoModule->GetInfoByWhere(' and OrderNumber = \'' . $OrderNumber . '\'',true);
        if($CategoryType==1){
            $TourProductLineSkuModule=new TourProductLineSkuModule();   
            foreach($OrderInfoList as $key=>$val){
                $SkuInfo=$TourProductLineSkuModule->GetInfoByKeyID($val['TourProductSkuID']);
                $OrderInfoList[$key]['SkuName']=$SkuInfo['SKUName'];
                $OrderInfoList[$key]['AdultNum']=$SkuInfo['AdultNum'];
                $OrderInfoList[$key]['ChildrenNum']=$SkuInfo['ChildrenNum'];
                $OrderInfoList[$key]['PeopleNum']=$SkuInfo['PeopleNum'];
            }
        }else{
            $TourProductPlaySkuModule=new TourProductPlaySkuModule();            
            foreach($OrderInfoList as $key=>$val){
                $SkuInfo=$TourProductPlaySkuModule->GetInfoByKeyID($val['TourProductSkuID']);
                $OrderInfoList[$key]['SkuName']=$SkuInfo['SKUName'];
            }            
        }
        include template('TourProductOrderDetail');
    }
    public function Emessage($OrderID =''){
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineDetailedModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlayDetailedModule.php';
        $TourProductPlayDetailed = new TourProductPlayDetailedModule();
        $TourProductLineDetailed = new TourProductLineDetailedModule();
        $TourProductOrderInfoModule = new TourProductOrderInfoModule();
        $TourProductOrderModule = new TourProductOrderModule();
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $ProductOrder = $TourProductOrderModule->GetInfoByKeyID($OrderID);
        $OrderInfo = $TourProductOrderInfoModule->GetInfoByWhere(' and OrderNumber = \'' . $ProductOrder['OrderNumber'] . '\'');
        if ($OrderInfo['TourLineSnapshotID']){
            $ProductList = $TourProductLineModule->GetInfoByTourProductID($OrderInfo['TourProductID']);
            $Detailed = $TourProductLineDetailed->GetInfoByTourProductID($OrderInfo['TourProductID']);
        }elseif ($OrderInfo['TourPlaySnapshotID']){
            $ProductList = $TourProductPlayBaseModule->GetInfoByTourProductID($OrderInfo['TourProductID']);
            $Detailed = $TourProductPlayDetailed->GetInfoByTourProductID($OrderInfo['TourProductID']);
            
        }
        $TravelPeopleInfo = json_decode($ProductOrder['TravelPeopleInfo'],true);

        $NewContent = json_decode($Detailed['NewContent'],true);
        $num = count($TravelPeopleInfo);
        foreach ($NewContent['TodayTitle'] as $key =>$value){
            $time = $key+1;
            $xingcheng .= '<li><span class="dateBox">第'.$time.'天 :</span><p>'.$value.'</p></li>';
        }
        $Explanation = json_decode($Detailed['Explanation'],true);
        foreach ($Explanation['ExpTitle'] as $key=>$value){
            $Explanations .= '<div class="FreeTit">'.$value.'</div><p class="f18 freeText mt10">'.$Explanation['ExpContent'][$key].'</p>';
        }
        return $Emessage ='<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>确认函</title><style type="text/css">
body{background:#fff;font-size:14px;line-height:24px;font-family:\'\5FAE\8F6F\96C5\9ED1\',\'\5B8B\4F53 \', SimSun, Tahoma, Verdana, Arial, sans-serif !important;color:#666;min-width:1200px;}
body,html,a,img,div,form,select,input,ul,ol,li,h1,h2,h3,h4,h5,h6,dd,dl,dt,p,label,em,span,i{margin:0;padding:0}
input{font-family:"\5FAE\8F6F\96C5\9ED1","\5B8B\4F53",tahoma,arial,"Hiragino Sans GB",simsun,sans-serif;font-size:14px;}
input:focus,select:focus,textarea:focus{outline:none;}
select:focus{outline:none;}
i,em{font-style: normal;}
a{color:#3c3c3c;text-decoration:none;transition:color linear 0.3s;-webkit-transition:color linear 0.3s;-moz-transition:color linear 0.3s;-ms-transition:color linear 0.3s;-o-transition:color linear 0.3s; outline: none;}
a:hover{color:#f7534c;text-decoration:none}
ol,ul,li{list-style:none}
img{border:none;vertical-align:top;}
.transition{transition: all 0.3s linear;-webkit-transition: all 0.3s linear;-ms-transition: all 0.3s linear;-moz-transition: all 0.3s linear;-o-transition: all 0.3s linear;}
.cf{*zoom:1}
.cf:after{content:\'\';display:block;height:0;clear:both}
.fl{float:left !important;}
.fr{float:right !important;}
.w1200{width:1200px;height:auto;margin:0 auto;}
.w1003{width:1003px;height:auto;margin:0 auto;}
.mt5{margin:5px auto 0 !important}
.mt10{margin:10px auto 0 !important}
.mt15{margin:15px auto 0 !important}
.mt20{margin:20px auto 0 !important}
.mt25{margin:25px auto 0 !important}
.mt30{margin:30px auto 0 !important}
.mt35{margin:35px auto 0 !important}
.mt40{margin:40px auto 0 !important}
.mt45{margin:45px auto 0 !important}
.mt50{margin:50px auto 0 !important}
.pl10{ padding-left:10px !important;}
.pl20{ padding-left:20px !important;}
.pl30{ padding-left:30px !important;}
.pl40{ padding-left:40px !important;}
.pr10{ padding-left:10px !important;}
.pr20{ padding-left:20px !important;}
.pr30{ padding-left:30px !important;}
.pr40{ padding-left:40px !important;}
.tac{ text-align:center !important}
.tal{ text-align: left !important; }
.tar{ text-align: right !important}
.hidden{display:none !important;}
.ovh{height: auto !important;overflow: hidden;}
.overflow{ overflow:hidden}
.cupo{ cursor:pointer}
.wrap{ width:100%; height:auto; overflow:hidden}
.fbd{font-weight: bold;}
.f12{font-size: 12px;}
.f14{font-size: 14px;}
.f16{font-size: 16px;}
.dis{display: inline-block;vertical-align: top;}
.red{ color:#ff5863;}
/*ie8支持placeholder属性*/
.placeholder{color: gray;}
			body{min-width: 790px;color: #595757;}
			.conBody{ background: url(http://images.57us.com/img/confirmation/bg3.0.png) left top repeat-y;width: 790px; }
			.conBodyWrap{background: url(http://images.57us.com/img/confirmation/ConfirmationBg3.0.png) left bottom no-repeat;width:100%;height: auto; min-height: 1472px; overflow: hidden; padding-bottom: 30px;}
			.sHeader{ background: url(http://images.57us.com/img/confirmation/sHeader3.0.png) no-repeat; height: 347px;}
			.conM{padding: 0 25px; height: auto; overflow: hidden;}
			.conTab th,.conTab td{ padding: 11px 0;}
			.conTab th{line-height: 19px; color: #595757;font-weight: normal;text-align: left;}
			.conTab input{width: 225px; height: 36px;border: 1px solid #01d1a1; line-height: 36px; text-indent: 10px;}
			.FreeTit{ background: #00cdc1; width: 116px; height: 31px; line-height: 31px; text-align: center; font-size: 18px; color: #fff;margin-top: 10px;}
			.dateList{ background: url(http://images.57us.com/img/confirmation/dateBg.png) left top repeat-y; padding-left: 15px; position: relative;}
			.dateList .up,.dateList .down{width: 10px; height: 25px;display: block;position: absolute;left: 0;}
			.dateList .up{top: 0;background: url(http://images.57us.com/img/confirmation/up3.0.png) center center no-repeat #fff;}
			.dateList .down{bottom: 0;background: url(http://images.57us.com/img/confirmation/down3.0.png) center center no-repeat #fff;}
			.dateList li{position: relative;font-size: 18px; padding: 2px 0 2px 65px;}
			.dateList li .dateBox{position: absolute;top: 2px; left: 0; display: block;}
		</style></head><body><div class="conBody"><div class="conBodyWrap"><div class="sHeader"></div><div class="conM"><table border="0" cellspacing="0" cellpadding="0" width="100%" class="conTab"><tr><th width="128"><p>Booking number<br>消费码</p></th><td width="242"><input type="" name="" id="" value="'.$ProductOrder['Consumercode'].'" readonly="readonly" /></td><th width="128"><p>Order Number<br>订单号</p></th><td width="242"><input type="" name="" id="" value="'.$ProductOrder['OrderNumber'].'" readonly="readonly" /></td></tr><tr><th><p>Customer Rep<br>游客代表</p></th><td><input type="" name="" id="" value="'.$ProductOrder['Contacts'].'" readonly="readonly" /></td><th><p>Contact Number<br>联系电话</p></th><td><input type="" name="" id="" value="'.$ProductOrder['Tel'].'" readonly="readonly" /></td></tr><tr><th><p>Customer Pax<br>游玩人数</p></th><td colspan="3"><input type="" name="" id="" value="'.$num.'" readonly="readonly" style="width: 595px;" /></td></tr><tr><th><p>Product Name<br>产品名称 </p></th><td colspan="3"><input type="" name="" id="" value="'.$ProductList['ProductName'].'" readonly="readonly" style="width: 595px;" /></td></tr><tr><th><p>Departure Time/<br>Date出发时间/日期</p></th><td colspan="3"><input type="" name="" id="" value="'.$OrderInfo['Depart'].'" readonly="readonly" style="width: 595px;" /></td></tr><tr><th><p>Departure Address<br>出发地点</p></th><td colspan="3"><input type="" name="" id="" value="'.$ProductList['Destination'].''.$ProductList['City'].'" readonly="readonly" style="width: 595px;" /></td></tr><tr><th><p>Validity Date<br>有效期</p></th><td colspan="3"><input type="" name="" id="" value="'.$OrderInfo['Depart'].'" readonly="readonly" style="width: 595px;" /></td></tr></table><div class="cf"><img src="http://images.57us.com/img/confirmation/tit13.0.png"/></div><div class="dateList"><span class="up"></span><ul><li>'.$xingcheng.'<span class="down"></span></div><div class="cf mt25"><img src="http://images.57us.com/img/confirmation/tit23.0.png"/></div>'.$Explanations.'<div class="cf mt15"><img src="http://images.57us.com/img/confirmation/tit33.0.png"/></div><div class="cf foot tac mt20">美华通（厦门）网络科技有限公司<br>Meihuatong （Xiamen）Network Technology Co. Ltd.</div></div></div></div></body></html>';
    }
}
?>