<?php
	/**
	 * @desc  游记资讯表
	 * Class TblTourModule
	 */
class TblTravelsModule  extends  CommonModule {
	public $KeyID = 'TravelsID';
	public $TableName = 'tbl_travels';
	public $ViewCount = 'ViewCount';

	public $Months = array('1'=>'1月','2'=>'2月','3'=>'3月','4'=>'4月','5'=>'5月','6'=>'6月','7'=>'7月','8'=>'8月','9'=>'9月','10'=>'10月','11'=>'11月','12'=>'12月');
	public $Days = array('1'=>'3天以内','2'=>'3-7天','3'=>'7-15天','4'=>'15天以上');
	/**
	 * @desc 获取相关阅读
	 */
	public function GetCorrelationNews($Correlation){
		global $DB;
		$sql = "SELECT * FROM ".$this->TableName." WHERE MATCH (Keywords) AGAINST ('{$Correlation}' IN BOOLEAN MODE) limit 8";
		return $DB->select($sql);
	}
}
?>