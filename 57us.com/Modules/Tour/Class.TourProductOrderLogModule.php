<?php
/**
 * @desc  旅游订单日志表
 * Class TourProductOrdeLogrModule
 */
class TourProductOrderLogModule extends CommonModule {
	public $KeyID = 'LogID';
	public $TableName = 'tour_product_order_log';

	/**
	 * @desc 订单状态
	 * @var array
	 */
	public $OldStatus = array(
			'0'=>'未下单',
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
	);

	public $NewStatus = array(
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
	);

}


















