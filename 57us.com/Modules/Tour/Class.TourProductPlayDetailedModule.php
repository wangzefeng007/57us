<?php

/**
 * @desc  旅游当地玩乐产品详情表
 * Class  TourProductPlaySkuModule
 */
class TourProductPlayDetailedModule extends CommonModule{

	public $KeyID = 'TourProductPlayDetailedID';
	public $TableName = 'tour_product_play_detailed';
	
	public function GetInfoByTourProductID($TourProductID) {
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where `TourProductID`=\'' . $TourProductID . '\'';
		return $DB->getone ( $sql );
	}
}