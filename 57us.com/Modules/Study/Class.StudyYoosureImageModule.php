<?php
class StudyYoosureImageModule extends CommonModule{

	public $KeyID = 'ImageID';
	public $TableName = 'study_yoosure_image';

	/**
	 * @desc  根据游学ID查询游学图片
	 */
	public function GetListsByYoosureID($YoosureID){
		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where YoosureID=' . $YoosureID . ' order by ImageID DESC';
		return $DB->Select ( $sql );
	}

}