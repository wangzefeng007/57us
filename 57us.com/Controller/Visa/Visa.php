<?php
class Visa {
	public function __construct() {
		include SYSTEM_ROOTPATH . '/Controller/Visa/VisaFunction.php';
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
	}

	/**
	 * @name 签证首页
	 */
	public function Index() {
        $VisaProducModule = new VisaProducModule ();
        $VisaOrderModule = new VisaOrderModule();
        $MyUrl = WEB_VISA_URL . '/visalists/';
        $OrderList = $VisaOrderModule->GetInfoByWhere('',true);
        $City = SearchNameArray();
        //特价推荐
        $VisaR1 = $VisaProducModule->GetInfoByWhere(' and Status = 1 and R1=1 order by S1 DESC',true);
        //精品推荐
        $VisaR2 = $VisaProducModule->GetInfoByWhere(' and Status = 1 and R2=1 order by S2 DESC',true);
        //首页轮播广告
        $visa_indexLists=NewsGetAdInfo('visa_index_banner');
        //常见问题
        $VisaInfo = $VisaProducModule->GetInfoByKeyID(64);
        $VisaInfo['Problem'] = $this->DoFilterInfo($VisaInfo ['Problem']);
        $VisaInfo['Problem'] = json_decode ( $VisaInfo ['Problem'], true );
		$Title = '美国签证_美国旅游签证_美国签证办理_美国商务签证 - 57美国网';
		$Keywords = '美国签证,美国旅游签证, 美国签证办,美国留学签证,美国商务签证, 美国签证申请, 美国签证办理, 美国签证材料清单 ,美国签证申请流程';
		$Description = '57美国网签证频道，为您提供美国旅游、留学、商务等签证服务，多年美国签证办理经验，流程清晰，服务省高效、过签率高，是您美国签证代办服务之首选。';
		include template ( 'VisaIndex' );
	}
	/**
	 * @name 签证列表
	 */
	public function Lists() {
		$VisaProducModule = new VisaProducModule ();
        $TagNav = 'visa';
		$MyUrl = WEB_VISA_URL . '/visalists/';
		//分页开始
        $SoUrl = $_GET['SoUrl'];
        $City = SearchNameArray();
        $Type = $this->GetType($SoUrl);
        $Area = $this->GetArea($SoUrl);
        $MysqlWhere = ' and Status = 1';
        $MysqlWhere .= GetMysqlWhere($SoUrl);
        if ($_GET['K']){
            $Keyword = trim($_GET['K']);
            if ($Keyword != '') {
                $MysqlWhere .= " and Title like '%$Keyword%'";
                $SoWhere = '?K=' . $Keyword;
            }
        }
        if (strstr ( $SoUrl, 'u1' )){
            $Price = 'u1';
            $MysqlWhere .= ' order by PresentPrice ASC';
        }elseif (strstr ( $SoUrl, 'u2' )){
            $Price = 'u2';
            $MysqlWhere .= ' order by PresentPrice DESC';
        }
		$Page = intval ( $_GET ['p'] );
		if ($Page < 1) {
			$Page = 1;
		}
		$PageSize = 8;
		$Rscount = $VisaProducModule->GetListsNum ( $MysqlWhere );
		if ($Rscount ['Num']) {
			$Data = array ();
			$Data ['RecordCount'] = $Rscount ['Num'];
			$Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
			$Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
			$Data ['Page'] = min ( $Page, $Data ['PageCount'] );
			$Offset = ($Page - 1) * $Data ['PageSize'];
			if ($Page > $Data ['PageCount'])
				$Page = $Data ['PageCount'];
			$Data ['Data'] = $VisaProducModule->GetLists ( $MysqlWhere, $Offset, $Data ['PageSize'] );
			foreach ( $Data ['Data'] as $Key => $Value ) {
                $Data ['Data'][$Key] ['Area'] = SearchNameArray($Value['Area']);
                $Data ['Data'][$Key] ['Type'] = SearchTypeArray($Value['Type']);
				$Data ['Data'][$Key] ['TagArray'] = explode ( ',', $Value ['Tag'] );
			}
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
		}
		$SEOInfo = GetSEOInfo ( $SoUrl );
		$Title = $SEOInfo ['Title'];
		$Keywords = $SEOInfo ['Keywords'];
		$Description = $SEOInfo ['Description'];
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $CategoryLists = $TblStudyAbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = 1033  order by GlobalDisplayOrder desc', true);
        $In = 1033;
        foreach ($CategoryLists as $key=>$value){
            $In .= ','.$value['CategoryID'];
        }
        // 最新签证资讯
        $Tuijian = $TblStudyAbroadModule->GetInfoByWhere(' and  MATCH(`CategoryID`) AGAINST (\'' . $In . '\' IN BOOLEAN MODE)  order by  AddTime desc limit 5', true);
        //常见问题
        $VisaInfo = $VisaProducModule->GetInfoByKeyID(64);
        $VisaInfo['Problem'] = $this->DoFilterInfo($VisaInfo ['Problem']);
        $VisaInfo['Problem'] = json_decode ( $VisaInfo ['Problem'], true );
		include template ( 'VisaList' );
	}

	private function GetType($SoUrl = ''){
        if ($SoUrl == '')
            return '';
        $Type ='';
        if (strstr ( $SoUrl, 't01' ))
            $Type = 't01';
        if (strstr ( $SoUrl, 't02' ))
            $Type = 't02';
        if (strstr ( $SoUrl, 't03' ))
            $Type = 't03';
        return $Type;
    }
    private function GetArea($SoUrl = '') {
        if ($SoUrl == '')
            return '';
        $Area ='';
        if (strstr ( $SoUrl, 'c01' ))
            $Area = 'c01';
        if (strstr ( $SoUrl, 'c02' ))
            $Area = 'c02';
        if (strstr ( $SoUrl, 'c03' ))
            $Area = 'c03';
        if (strstr ( $SoUrl, 'c04' ))
            $Area = 'c04';
        if (strstr ( $SoUrl, 'c05' ))
            $Area = 'c05';
        if (strstr ( $SoUrl, 'c05' ))
            $Area = 'c05';
        if (strstr ( $SoUrl, 'c07' ))
            $Area = 'c07';
        if (strstr ( $SoUrl, 'c08' ))
            $Area = 'c08';
        if (strstr ( $SoUrl, 'c09' ))
            $Area = 'c09';
        if (strstr ( $SoUrl, 'c10' ))
            $Area = 'c10';
        if (strstr ( $SoUrl, 'c11' ))
            $Area = 'c11';
        if (strstr ( $SoUrl, 'c12' ))
            $Area = 'c12';
        if (strstr ( $SoUrl, 'c13' ))
            $Area = 'c13';
        if (strstr ( $SoUrl, 'c14' ))
            $Area = 'c14';
        if (strstr ( $SoUrl, 'c15' ))
            $Area = 'c15';
        if (strstr ( $SoUrl, 'c16' ))
            $Area = 'c16';
        if (strstr ( $SoUrl, 'c17' ))
            $Area = 'c17';
        if (strstr ( $SoUrl, 'c18' ))
            $Area = 'c18';
        if (strstr ( $SoUrl, 'c19' ))
            $Area = 'c19';
        if (strstr ( $SoUrl, 'c20' ))
            $Area = 'c20';
        if (strstr ( $SoUrl, 'c21' ))
            $Area = 'c21';
        if (strstr ( $SoUrl, 'c22' ))
            $Area = 'c22';
        if (strstr ( $SoUrl, 'c23' ))
            $Area = 'c23';
        if (strstr ( $SoUrl, 'c24' ))
            $Area = 'c24';
        if (strstr ( $SoUrl, 'c25' ))
            $Area = 'c25';
        if (strstr ( $SoUrl, 'c26' ))
            $Area = 'c26';
        if (strstr ( $SoUrl, 'c27' ))
            $Area = 'c27';
        if (strstr ( $SoUrl, 'c28' ))
            $Area = 'c28';
        if (strstr ( $SoUrl, 'c29' ))
            $Area = 'c29';
        if (strstr ( $SoUrl, 'c30' ))
            $Area = 'c30';
        return $Area;
    }
	/**
	 * @name 签证内容
	 */
	public function Detail() {
		$VisaID = intval ( $_GET ['VisaID'] );
		if ($VisaID == 0) {
			alertandgotopage ( '参数错误!', WEB_VISA_URL );
		}
		$VisaProducModule = new VisaProducModule ();
		$VisaInfo = $VisaProducModule->GetInfoByKeyID ( $VisaID );
		if (!$VisaInfo){
            alertandgotopage ( '该产品已下架!', WEB_VISA_URL );
        }
        //添加浏览记录
        $Type=7;
        MemberService::AddBrowsingHistory($VisaID,$Type);
        $VisaInfo['PresentPrice'] = intval( $VisaInfo ['PresentPrice'] );
        $VisaInfo['OriginalPrice'] = intval( $VisaInfo ['OriginalPrice'] );
		$VisaInfo['BaseInfo'] = stripslashes ( $VisaInfo ['BaseInfo'] );
        $VisaInfo['BaseInfo'] = $this->DoFilterInfo($VisaInfo ['BaseInfo']);
		$VisaInfo['Attention'] = stripslashes ( $VisaInfo ['Attention'] );
        $VisaInfo['Attention'] = $this->DoFilterInfo($VisaInfo ['Attention']);
        if(strpos($VisaInfo['Procedure'],"http://server.57us.com/")){
            $VisaInfo['Procedure'] = str_replace ( "http://server.57us.com", "http://images.57us.com/l",$VisaInfo['Procedure']);
        }
		$VisaInfo['Procedure'] = stripslashes ( $VisaInfo ['Procedure'] );
        $VisaInfo['Procedure'] = $this->DoFilterInfo($VisaInfo ['Procedure']);
        $VisaInfo['Procedure'] = json_decode ( $VisaInfo ['Procedure'], true );
		$VisaInfo['PackageArray'] = explode ( ',', $VisaInfo ['Package'] );
		$VisaInfo['MaterialRequestedArray'] = json_decode ( $VisaInfo ['MaterialRequested'], true );
		foreach ( $VisaInfo ['MaterialRequestedArray'] ['MaterialRequested'] as $Key => $Value ) {
			$VisaInfo ['MaterialRequestedArray'] ['MaterialRequested'] [$Key] = stripslashes ( $Value );
		}
        $VisaInfo['Problem'] = $this->DoFilterInfo($VisaInfo ['Problem']);
        $VisaInfo['Problem'] = json_decode ( $VisaInfo ['Problem'], true );
        $Title = $VisaInfo ['Title'] . ' - 57美国网';
		$Keywords = $VisaInfo ['Keywords'];
		$SubTitle = '面试：'.$VisaInfo['Interview'].',证件有效期：'.$VisaInfo['Validity'].',停留时间：'.$VisaInfo['Stay'].',入境次数：'.$VisaInfo['Entries'].',办理时间：'.$VisaInfo['Duration'];
		$Description = $VisaInfo ['Title'] . '，' . $SubTitle . '，了解美国签证办理流程，申请美国签证代办服务，尽在57美国网！ ';
		include template ( 'VisaDetails' );
	}

    //过滤内容
    public function DoFilterInfo($Content = '')
    {
        if ($Content == '')
            return 0;
        $Content = preg_replace ( "/<(\/?strong.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?span.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?p.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?sub.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?h3.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?li.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?table.*?)>/si", "", $Content );

        return $Content;
    }
	/**
	 * @name 签证订单
	 */
	public function Order() {
		$VisaID = intval($_GET ['id']);
		$Date = trim( $_GET ['d'] );
		$Num = intval( $_GET ['n'] );
		if ($VisaID == 0 || $Date == '' || $Num == 0) {
			alertandgotopage ( '参数错误!', WEB_VISA_URL );
		}
		$VisaProducModule = new VisaProducModule ();
		$VisaInfo = $VisaProducModule->GetInfoByKeyID ( $VisaID );
		$Price = $VisaInfo ['PresentPrice'] * $Num;
		$Title = '下订单 - 57美国网';
		include template ( "VisaOrder" );
	}
	
	/**
	 * @name 签证订单选择支付页
	 */
	public function Pay() {
		$NO = trim ( $_GET ['NO'] );
		$VisaOrderModule = new VisaOrderModule ();
		$VisaProducModule = new VisaProducModule ();
		
		$OrderInfo = $VisaOrderModule->GetInfoByOrderNumber ( $NO );
		$VisaInfo = $VisaProducModule->GetInfoByKeyID ( $OrderInfo ['VisaID'] );
		$GoToUrl = WEB_VISA_URL.'/visadetail/'.$OrderInfo ['VisaID'].'.html';
		if($OrderInfo && $OrderInfo['Status'] == 1){
			if (strtotime($OrderInfo['ExpirationTime']) > time()) {
				include template('VisaOrderPay');
			} else {

				$UpData['Status'] = 10;
				$UpData['Remarks'] = '订单超时未支付';
				$Result = $VisaOrderModule->UpdateInfoByKeyID($UpData, $OrderInfo['ID']);
				if($Result){
					$LogMessage = '操作失败(order状态更新失败)';
				}
				else{
					$LogMessage = '操作成功';
				}
				// 添加订单状态更改日志
				include SYSTEM_ROOTPATH.'/Modules/Tour/Class.TourProductOrderLogModule.php';
				$OrderLogModule = new TourProductOrderLogModule();
				if ($_SESSION['UserID'] && ! empty($_SESSION['UserID'])) {
					$UserID = $_SESSION['UserID'];
				} else {
					include SYSTEM_ROOTPATH . '/Modules/Member/Class.MemberUserModule.php';
					$MemberUserModule = new MemberUserModule();
					$UserInfo = $MemberUserModule->GetUserIDbyMobile($OrderInfo['Phone']);
					$UserID = $UserInfo['UserID'];
				}
				$LogData = array(
						'OrderNumber' => $NO,
						'UserID' => $UserID,
						'OldStatus' => 1,
						'NewStatus' => 10,
						'OperateTime' => date("Y-m-d H:i:s", time()),
						'IP' => GetIP(),
						'Remarks'=>$LogMessage,
						'Type'=>2
				);
				$OrderLogModule->InsertInfo($LogData);
				alertandgotopage('订单超时未支付', $GoToUrl);
			}
		}
		else{
			alertandgotopage('不能操作的订单',$GoToUrl);
		}
	}
}
