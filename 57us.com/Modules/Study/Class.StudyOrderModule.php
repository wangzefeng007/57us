<?php

/**
 * @desc  留学订单表
 * Class  StudyOrderModule
 */
Class StudyOrderModule extends CommonModule {

    public $KeyID = 'OrderID';
    public $TableName = 'study_order';

    //订单状态
    //public $Status = array('1'=>'未支付','2'=>'已支付（进行中）','3'=>'订单完成','4'=>'申请退款','5'=>'退款中','6'=>'退款成功','7'=>'退款失败');
    public $Status = array('1'=>'未支付','2'=>'已支付','3'=>'订单完成','4'=>'申请退款','5'=>'退款中','6'=>'退款成功','7'=>'退款失败');
    //支付方式
    public $PayType = array('1'=>'支付宝','2'=>'微信','3'=>'网银');
}
