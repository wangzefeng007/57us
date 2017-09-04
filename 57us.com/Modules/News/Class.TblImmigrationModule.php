<?php
	/**
	 * @desc  移民资讯表
	 * Class TblImmigrationModule
	 * Author Zf
	 */
class TblImmigrationModule extends  CommonModule {

	public $KeyID = 'ImmigrationID';
	public $TableName = 'tbl_immigration';
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