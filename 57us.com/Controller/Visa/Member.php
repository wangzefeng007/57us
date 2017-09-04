<?php
class Member {
	public function __construct() {
		if (! isset ( $_SESSION ['UserID'] ) || empty ( $_SESSION ['UserID'] )) {
			header ( 'Location:'.WEB_MEMBER_URL.'/member/login/' );
		}
		/*底部调用热门旅游目的地、景点*/
		$TourFootTagModule = new TourFootTagModule();
		$this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC',0,200);
		$this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC',0,200);
	}
	/**
	 * @name 
	 */
	public function Index() {
		include template ( 'MemberIndex' );
	}
	/**
	 * @name 退款
	 */
	public function Refund() {
		$OrderNumber = trim ( $_GET ['id'] );
		$VisaOrderModule = new VisaOrderModule ();
		$OrderInfo = $VisaOrderModule->GetInfoByOrderNumber ( $OrderNumber );
		if ($OrderInfo ['IsPayment'] != '已付款') {
			alertandback ( '操作失败' );
		}
		$UpdateInfo ['IsPayment'] = '退款中';
		$IsOk = $VisaOrderModule->UpdateInfoByOrderNumber ( $UpdateInfo, $OrderNumber );
		if ($IsOk) {
			alertandback ( '操作成功' );
		} else {
			alertandback ( '操作失败' );
		}
	}
	/**
	 * @name 取消退款
	 */
	public function NoRefund() {
		$OrderNumber = trim ( $_GET ['id'] );
		$VisaOrderModule = new VisaOrderModule ();
		$OrderInfo = $VisaOrderModule->GetInfoByOrderNumber ( $OrderNumber );
		if ($OrderInfo ['IsPayment'] != '退款中') {
			alertandback ( '操作失败' );
		}
		$UpdateInfo ['IsPayment'] = '已付款';
		$IsOk = $VisaOrderModule->UpdateInfoByOrderNumber ( $UpdateInfo, $OrderNumber );
		if ($IsOk) {
			alertandback ( '操作成功' );
		} else {
			alertandback ( '操作失败' );
		}
	}
}
