<?php
/**
 * @desc  当地玩乐产品基础信息
 * Class TourProductPlayBaseModule
 */
class TourProductPlayBaseModule extends CommonModule {

	public $KeyID = 'TourProductPlayID';
	public $TableName = 'tour_product_play_base';

	public function GetInfoByTourProductID($TourProductID) {
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where `TourProductID`=\'' . $TourProductID . '\'';
		return $DB->getone ( $sql );
	}
	
	public function UpdateByTourProductID($Info, $TourProductID) {
		global $DB;
		return $DB->UpdateWhere ( $this->TableName, $Info, '`TourProductID`=\'' . $TourProductID . '\'' );
	}
        
        //增加销量
        public function AddSales($TourProductID){
            if($TourProductID=='' || !is_numeric($TourProductID)){
                return false;
            }
            global $DB;
            return $DB->Update("update ".$this->TableName." set Sales=Sales+1 where TourProductID=$TourProductID");
        }
}