<?php
	/**
	* @desc  旅游产品表（跟团游）
	* Class TourProductLineModule
	*/
class TourProductLineModule extends CommonModule{

	public $KeyID = 'TourProductLineID';
    public $TableName = 'tour_product_line';

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
 
        //增加销量
        public function AddSales($TourProductID){
            if($TourProductID=='' || !is_numeric($TourProductID)){
                return false;
            }
            global $DB;
            return $DB->Update("update ".$this->TableName." set Sales=Sales+1 where TourProductID=$TourProductID");
        }        
}