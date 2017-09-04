<?php
Class MemberUserBankFlowModule extends CommonModule  {

    public $KeyID = 'FlowID';
    public $TableName = 'member_user_bank_flow';

    public $OperateType = array('1'=>'充值','2'=>'消费扣款','3'=>'提现','4'=>'系统入账','5'=>'退款');

}
