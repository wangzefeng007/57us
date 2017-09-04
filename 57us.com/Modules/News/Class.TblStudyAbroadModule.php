<?php
	/**
	 * @desc  留学资讯表
	 * Class TblStudyAbroadModule
	 * Author Zf
	 */
class TblStudyAbroadModule extends  CommonModule {

	public $KeyID = 'StudyID';
	public $TableName = 'tbl_study_abroad';
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