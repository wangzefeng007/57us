<?php
	/**
	* @desc  旅游产品详情表（跟团游）
	* Class TourProductLineDetailedModule
	*/
class TourProductLineDetailedModule extends CommonModule{

	public $KeyID = 'TourProductLineID';
    public $TableName = 'tour_product_line_detailed';

	/**
	 * @desc  根据TourProductID获取产品信息
	 * @param Int $TourProductID
	 * @return Array
	 */
	public function GetInfoByTourProductID($TourProductID='') {
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where  TourProductID =' . $TourProductID;
		return $DB->getone ( $sql );
	}
	/**
	 * @desc  根据TourProductID更新产品信息
	 * @param Array $Info
	 * @param Int $TourProductID
	 * @return Bool
	 */
	public function UpdateInfoByourProductID($Data, $TourProductID='') {
		global $DB;
		return $DB->updateWhere ( $this->TableName, $Data, ' TourProductID=' . intval($TourProductID) );
	}
	/**
	 * @desc  根据Category获取产品信息
	 * @param Int $Category
	 * @return Array
	 */
	public function GetByCategoryID($Category) {
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where Category = ' . $Category;
		return $DB->getone ( $sql );
	}
}