<?php

/**
 * @desc 广告位表
 * Class TblAdModule
 */
class TblAdModule  extends  CommonModule {

	public $KeyID = 'ADID';
	public $TableName = 'tbl_ad';
    //广告类别
    public $Type = array('1'=>'资讯','2'=>'留学','3'=>'旅游','4'=>'移民');
}
?>