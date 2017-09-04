<?php

/**
 * @desc  游学订单表
 * Class StudyYoosureOrderModule
 */
class StudyYoosureOrderModule extends CommonModule{

	public $KeyID = 'OrderID';
	public $TableName = 'study_yoosure_order';

    /**
     * @desc  前台展示状态
     * @var array
     */
    public $NStatus = [
        '1' => '未支付',
        '2' => '已支付',
        '3' => '已支付',
        '4' => '已支付',
        '10' => '交易终止',
        '11' => '交易终止',
    ];
    /**
     * @desc  前台展示状态
     * @var array
     */
    public $Status =  [
        '1' => '待付款',
        '2' => '已付款待确认',
        '3' => '已付款确认中',
        '4' => '已付款已确认',
        '10' => '交易关闭(超时)',
        '10' => '交易关闭(下架)',
        ];
}