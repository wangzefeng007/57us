<?php
Class HotelOrderModule extends CommonModule  {
		/**
	 * @desc 订单状态
	 * @var array
	 */
	public $Status = array(
			'1'=>'待付款',
			'2'=>'已付款待确认',
			'3'=>'已付款确认中',
			'4'=>'已付款已确认',
			'5'=>'退款中',
			'6'=>'退款处理中',
			'7'=>'退款审核不通过',
			'8'=>'退款审核通过',
			'9'=>'退款完成',
			'10'=>'交易关闭（超时）',
			'11'=>'交易关闭（改价格）',
			'12'=>'交易关闭（禁购买）',
			'13'=>'交易关闭(用户关闭)',
	);

	/*
	 * @desc  前台展示状态
	 * @var array
	 */

	public $NStatus = array(
			'1'=>'待付款',
			'2'=>'已付款',
			'3'=>'已付款',
			'4'=>'已付款',
			'5'=>'退款中',
			'6'=>'退款中',
			'7'=>'退款失败',
			'8'=>'退款中',
			'9'=>'退款成功',
			'10'=>'订单关闭',
			'11'=>'订单关闭',
			'12'=>'订单关闭',
			'13'=>'订单关闭',
	);

	/**
	 * @desc  支付方式
	 * @var array
	 */
	public $PaymentMethod = array(
			'1'=>'支付宝',
			'2'=>'微信',
			'3'=>'网银',
	);
    public $KeyID = 'OrderID';
    public $TableName = 'hotel_order';

    
    //根据订单号及状态码查询订单
    public function GetOrderInfo($OrderNo,$Status=1){
        global $DB;
        return $DB->getone("select * from ".$this->TableName." where OrderNo='$OrderNo' and `Status`=$Status");
    }
    //根据订单号
    public function GetOrderInfoByNo($OrderNo){
        global $DB;
        return $DB->getone("select * from ".$this->TableName." where OrderNo='$OrderNo'");
    }    

    //根据订单号及用户ID查询订单
    public function GetByNoAndUID($OrderNo,$UserID){
        global $DB;
        return $DB->getone("select * from ".$this->TableName." where OrderNo='$OrderNo' and UserID=$UserID");
    }        
    
    //根据主键及用户ID查询订单
    public function GetByKeyIDAndUID($OrderID,$UserID){
        global $DB;
        return $DB->getone("select * from ".$this->TableName." where ".$this->KeyID."=$OrderID and UserID=$UserID");
    }
    
    //根据订单编号及联系人电话查询订单
    public function GetByOrderNoAndContactPhone($OrderNo,$ContactPhone,$OrderID){
        global $DB;
        return $DB->getone("select * from ".$this->TableName." where OrderNo='$OrderNo' and ContactPhone=$ContactPhone and ".$this->KeyID."=$OrderID");
    }    
    
    //更新订单
    public function UpdateByOrderNum($Data,$OrderNo){
        global $DB;
        if($Data=='' || !is_array($Data)){
            return 0;
        }
        return $DB->updateWhere($this->TableName,$Data,'OrderNo=\''.$OrderNo.'\'');
    }    
}
