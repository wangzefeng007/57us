<?php
Class MemberUserBankWithdrawModule extends CommonModule  {

    public $KeyID = 'WithdrawID';
    public $TableName = 'member_user_bank_withdraw';

    public $WithdrawType = array('0'=>'支付宝');
    public $WithdrawStatus = array('1'=>'提现中','2'=>'已提现','3'=>'拒绝提现');
    
}
