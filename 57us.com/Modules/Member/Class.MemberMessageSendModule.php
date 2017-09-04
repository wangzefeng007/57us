<?php

/**
 * @desc  站内信_信息发送表
 * Class MemberMessageSendModule
 */
Class MemberMessageSendModule extends CommonModule  {
	public $KeyID = 'SendID';
	public $TableName = 'member_message_send';

	public $SendType = array('1'=>'全部用户','2'=>'普通用户（顾问/教师以外的）','3'=>'顾问','4'=>'教师');

}
