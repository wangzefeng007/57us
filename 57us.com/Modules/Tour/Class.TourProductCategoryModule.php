<?php
/**
 * @desc 旅游产品类别表
 * Class TourProductCategoryModule
 */
class TourProductCategoryModule extends CommonModule{
	public $KeyID = 'TourCategoryID';
    public $TableName = 'tour_product_category';

    
	public function TourSelectByParent($ParentID) {
		global $DB;
		$sql = 'select * from `' . $this->TableName . '` where `ParentID` =' . $ParentID;
		return $DB->Select ( $sql );
	}
	public function GetTourCategoryInfoByAlias($Alias = '') {
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where Alias=\'' . $Alias . '\'';
		return $DB->GetOne ( $sql );
	}
}