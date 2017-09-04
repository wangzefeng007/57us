<?php
	/**
	 * @desc  旅游资讯表
	 * Class TblTourModule
	 * Author Zf
	 */
class TblTourModule  extends  CommonModule {

	public $KeyID = 'TourID';
	public $TableName = 'tbl_tour';
	public $ViewCount = 'ViewCount';

	/**
	 * @desc 获取相关阅读
	 */
	public function GetCorrelationNews($Correlation){
		global $DB;
		$sql = "SELECT * FROM ".$this->TableName." WHERE MATCH (Description,Content) AGAINST ('{$Correlation}' IN BOOLEAN MODE) limit 8";
		return $DB->select($sql);
	}

}
?>