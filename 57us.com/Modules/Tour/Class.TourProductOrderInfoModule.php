<?php
/**
 * @desc  旅游订单内容表
 * Class TourProductOrderInfoModule
 */
class TourProductOrderInfoModule extends CommonModule {
	public $KeyID = 'TourOrderInfoID';
    public $TableName = 'tour_product_order_info';

	public function GetInfoByOrderNumber($OrderNumber) {
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where `OrderNumber`=\'' . $OrderNumber.'\'';
		return $DB->GetOne ( $sql );
	}
        
        //获取房间数量
        public function GetRoomNumByOrderNumber($OrderNumber) {
		global $DB;
		$sql = 'select count(Num) as Num from ' . $this->TableName . ' where `OrderNumber`=\'' . $OrderNumber.'\'';
		$Result=$DB->GetOne ( $sql );
                return $Result['Num'];
	}
        
	public function UpdateInfoByOrderNumber($Info, $OrderNumber) {
		global $DB;
		return $DB->UpdateWhere ( $this->TableName, $Info, '`OrderNumber`=\'' . $OrderNumber .'\'');
	}
}


















