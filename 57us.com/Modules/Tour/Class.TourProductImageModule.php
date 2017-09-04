<?php
	/**
	* @desc  旅游产品图片总表
	* Class TourProductImageModule
	*/
class TourProductImageModule extends CommonModule{

	public $KeyID = 'ImageID';
	public $TableName = 'tour_product_image';

	public function GetListsByTourProductID($TourProductID) {
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where TourProductID=' . $TourProductID . ' order by IsDefault DESC';
		return $DB->select ( $sql );
	}

	public function GetInfoByTourProductID($TourProductID) {
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where TourProductID=' . $TourProductID . ' order by IsDefault DESC';
		return $DB->getone ( $sql );
	}

	public function DeleteInfoByTourProductID($TourProductID) {
		global $DB;
		$sql = 'delete from `' . $this->TableName . '` where TourProductID=' . $TourProductID;
		return $DB->delete ( $sql );
	}

	public function UpdateInfoByTourProductID($Info, $TourProductID) {
		global $DB;
		return $DB->updateWhere ( $this->TableName, $Info, 'TourProductID=' . intval ( $TourProductID ) );
	}

}