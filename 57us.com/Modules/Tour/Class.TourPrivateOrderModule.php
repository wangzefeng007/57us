<?php

/**
 * @desc 旅游高端定制订单表
 * Class TourPrivateOrderModule
 */
class TourPrivateOrderModule extends CommonModule
{
    public $KeyID = 'OrderID';
    public $TableName = 'tour_privateorder';

    /**
     * @desc 订单状态
     * @var array
     */
    public $Status = array(
        '0'=>'待定价',
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
    /**
     * @desc  前台展示状态
     * @var array
     */
    public $NStatus = array(
        '0'=>'待定价',
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
    // 更新订单
    public function UpdateByOrderNum($Data, $OrderNo)
    {
        global $DB;
        if ($Data == '' || ! is_array($Data)) {
            return 0;
        }
        return $DB->updateWhere($this->TableName, $Data, 'OrderNo=\'' . $OrderNo . '\'');
    }
}

?>