<?php
/**
 * @desc  供应商表
 * Class TourSupplierModule
 */
class TourSupplierModule extends CommonModule{
	public $KeyID = 'SupplierID';
	public $TableName = 'tour_supplier';
	
	public function TourSupplierSelect(){
		global $DB;
		$sql='select * from '.$this->TableName;
		return $DB->Select($sql);
	}
	public function GetTourSupplierList($MysqlWhere) {

		global $DB;
		$sql = 'select * from ' . $this->TableName . ' where 1=1' . $MysqlWhere;
		return $DB->Select ($sql);
	}
}