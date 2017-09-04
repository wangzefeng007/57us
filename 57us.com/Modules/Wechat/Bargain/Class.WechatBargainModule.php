<?php

/**
 * @desc  微信砍价活动
 * Class WechatBargainModule
 */
class WechatBargainModule  extends  CommonModule {

	public $KeyID = 'BargainID';
	public $TableName = 'wechat_bargain';

	/**
	 * @desc  更新砍价价格
	 * @param $UserID   购买者ID
	 * @param $Amount   此次砍价金额
	 * @param $Type     砍价活动类型
	 * @param $IsOwn    是否是自己砍价
	 * @return int
	 */
	public function UpdateAmount($UserID,$Amount,$Type,$IsBargain=0)
	{
		global $DB;
		if($IsBargain == 1){
			return $DB->Update('Update ' . $this->TableName . ' set Amount = Amount-'.$Amount.', BargainAmount = BargainAmount+'.$Amount.', Type='.$Type.', IsBargain = '.$IsBargain.'  where `UserID`=' . $UserID);
		}
		else{
			return $DB->Update('Update ' . $this->TableName . ' set Amount = Amount-'.$Amount.', BargainAmount = BargainAmount+'.$Amount.', Type='.$Type.'  where `UserID`=' . $UserID);
		}
	}

	/**
	 * @desc 获取砍价排行榜
	 */
	public function GetBargainRanking($Offset, $Num){
		global $DB;
		$sql = 'select a.Amount,b.UserID,b.Nickname,b.HeadImgUrl from wechat_bargain AS a,wechat_user AS b where  a.UserID = b.UserID order by a.Amount asc,b.UserID asc ';
		return $DB->select ( $sql, $Offset, $Num );
	}
}
?>