<?php

/**
 * @desc 手机短信推送用户
 * Class TelModule
 */
class TelModule  extends  CommonModule {
	public $KeyID = 'ID';
	public $TableName = 'tel';


	public function SelectNum($Mobile){
		global $DB;
		$sql = 'select count(`ID`) as Num from tel where NO='.$Mobile;
		return $DB->getone ( $sql );
	}

	public function GetData(){
		global $DB;
		$sql = 'SELECT NO,ID FROM `tel` GROUP BY `NO` HAVING COUNT(*) > 1';
		return $DB->select ( $sql );
	}
}
?>