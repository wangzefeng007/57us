<?php

/**
 * @desc  留学订单日志表
 * Class  StudyOrderLogModule
 */
Class StudyOrderLogModule extends CommonModule {

    public $KeyID = 'LogID';
    public $TableName = 'study_order_log';

    //原订单状态
    public $OldStatus = array('0'=>'未下单','1'=>'未支付','2'=>'已支付（进行中）','3'=>'订单完成','4'=>'申请退款','5'=>'退款中','6'=>'退款成功','7'=>'退款失败');
    //最终订单状态
    public $NewStatus = array('1'=>'未支付','2'=>'已支付（进行中）','3'=>'订单完成','4'=>'申请退款','5'=>'退款中','6'=>'退款成功','7'=>'退款失败');
}
