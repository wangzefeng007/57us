<?php
/**
 * @desc  旅游特色主题表
 * Class TourSpecialSubjectModule
 */
class TourSpecialSubjectModule extends CommonModule{
	
	public $KeyID = 'TourSpecialSubjectID';
	public $TableName = 'tour_special_subject';
	
	public function GetList($SqlWhere){
	    global $DB;
	    $sql = 'select * from ' . $this->TableName . ' where 1=1' . $SqlWhere;
	    return $DB->Select($sql);
	}
}