<?php
/**
 * @desc  签证订单表
 * Class VisaOrderModule
 */
class VisaOrderModule extends CommonModule
{
    public $KeyID = 'ID';
    public $TableName = 'visa_order';

    /**
     * @desc 订单状态
     * @var array
     */
    public $Status = array(
        '1' => '待付款',
        '2' => '已付款待确认',
        '3' => '已付款确认中',
        '4' => '已付款已确认',
        '5' => '退款中',
        '6' => '退款(处理中)',
        '7' => '退款审核不通过',
        '8' => '退款审核通过',
        '9' => '退款完成',
        '10' => '交易关闭（超时）',
        '11' => '交易关闭（改价格）',
        '12' => '交易关闭（禁购买）',
    );

    /**
     * @desc  前台展示状态
     * @var array
     */
    public $NStatus = array(
        '1' => '待付款',
        '2' => '已付款',
        '3' => '已付款',
        '4' => '已付款',
        '5' => '退款中',
        '6' => '退款中',
        '7' => '退款失败',
        '8' => '退款中',
        '9' => '退款成功',
        '10' => '订单关闭',
        '11' => '订单关闭',
        '12' => '订单关闭',
        '13' => '订单关闭',
    );

    /**
     * @desc  支付方式
     * @var array
     */
    public $PaymentMethod = array(
        '1' => '支付宝',
        '2' => '微信',
        '3' => '网银',
    );

    /**
     * @desc  根据订单号查询单条数据详情
     * @param string $OrderNumber
     * @return array|int
     */
    public function GetInfoByOrderNumber($OrderNumber = '')
    {
        global $DB;
        if ($OrderNumber == '')
            return 0;
        $sql = 'select * from ' . $this->TableName . ' where OrderNumber=\'' . $OrderNumber . '\'';
        return $DB->getone($sql);
    }
    /**
     * @desc  根据订单号更新单条数据详情
     * @param string $OrderNumber
     * @param array $Info
     * @return array|int
     */
    public function UpdateInfoByOrderNumber($Info, $OrderNumber)
    {
        global $DB;
        return $DB->UpdateWhere($this->TableName, $Info, '`OrderNumber`=\'' . $OrderNumber . '\'');
    }
}