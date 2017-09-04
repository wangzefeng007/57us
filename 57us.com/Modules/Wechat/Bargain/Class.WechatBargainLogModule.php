<?php

/**
 * @desc  微信砍价活动日志
 * Class WechatBargainLogModule
 */
class WechatBargainLogModule  extends  CommonModule {

	public $KeyID = 'LogID';
	public $TableName = 'wechat_bargain_log';

	/**
	 * @desc 获取砍价高手列表
	 */
	public function GetBargainSuperior($UserID,$Offset, $Num){
		global $DB;
		$sql = 'select a.BargainAmount,a.BargainTime,b.UserID,b.Nickname,b.HeadImgUrl from wechat_bargain_log AS a,wechat_user AS b where a.ToBargainUserID = '.$UserID.' and a.BargainUserID = b.UserID order by a.BargainAmount desc,b.UserID asc ';
		return $DB->select ( $sql, $Offset, $Num );
	}
}
?>