<?php

/**
 * @desc  微信投票_统计表
 * Class WechatVoteUserModule
 */
class WechatVoteStatisticsModule  extends  CommonModule {

	public $KeyID = 'ID';
	public $TableName = 'wechat_vote_statistics';

	/**
	 * @desc  字段加1
	 * @param $key  Partake，Visit，Vote
	 */
	public function Increase($key){
		if ($key=='')
			return '';
		global $DB;
		return $DB->Update ( 'Update ' . $this->TableName .' set '.$key.'='.$key.'+1 where `'. $this->KeyID . '`=1' );
	}

}
?>