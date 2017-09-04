<?php

/**
 * @desc 站内信_信息内容表
 * Class MemberMessageInfoModule
 */
Class MemberMessageInfoModule extends CommonModule  {
	public $KeyID = 'MessageID';
	public $TableName = 'member_message_info';

	public $SendType = array('1'=>'全部用户','2'=>'普通用户','3'=>'顾问用户','4'=>'教师用户');
	public $SendStatus = array('1'=>'未发布','2'=>'已发布');
}
