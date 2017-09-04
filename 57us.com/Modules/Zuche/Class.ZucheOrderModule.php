<?php
/**
 * @desc  租车订单表
 * Class ZucheOrderModule
 */
class ZucheOrderModule extends CommonModule {
    public $KeyID = 'ID';
    public $TableName = 'zuche_order';

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

    /**
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
     * @desc 支付方式
     * @var array
     */
    public $PayType = array(
        '1'=>'支付宝',
        '2'=>'微信',
        '3'=>'网银'
    );

    /**
     * @desc  获取总条数
     * @param string $MysqlWhere
     * @return array
     */
    public function GetZucheListNum($MysqlWhere=''){
        global $DB;
        $sql='select count(`'.$this->KeyID.'`) as Num from '.$this->TableName.' where 1=1'.$MysqlWhere;
        return $DB->GetOne($sql);
    }

    /**
     * @desc  获取租车列表
     * @param string $MysqlWhere
     * @param $Offset
     * @param $Num
     * @param string $OrderBy
     * @return array
     */
    public function GetZucheList($MysqlWhere='',$Offset,$Num,$OrderBy=''){
        if(empty(trim($OrderBy))){
            $OrderBy=$this->KeyID.' desc';
        }
        global $DB;
        $sql='select * from '.$this->TableName.' where 1=1'.$MysqlWhere.' order by '.$OrderBy;
        return $DB->Select($sql,$Offset,$Num);
    }
    /**
     * 获取租车订单信息
     * 返回结果 返回array 失败false
     * @global type $DB
     * @param 查询条件 $ID
     * @return array
     */
    public function GetZucheInfo($ID){
        global $DB;
        $sql='select * from `'.$this->TableName.'` where `ID`='.$ID;
        return $DB->GetOne($sql);
    }

    /**
     * @desc   获取租车订单信息
     * @param $OrderNum
     * @return array
     */
    public function GetOrderByOrderNum($OrderNum){
        global $DB;
        $sql='select * from `'.$this->TableName.'` where `OrderNum`=\''.$OrderNum.'\'';
        return $DB->GetOne($sql);
    }
    
    public function GetOrderByWhere($MysqlWhere){
        global $DB;
        $sql='select * from `'.$this->TableName.'` where 1=1 '.$MysqlWhere;
        return $DB->GetOne($sql);
    }
    /**
     * @desc  获取租车订单列表信息
     * @param $UserID
     * @return array
     */
    public function GetOrderListByUserID($UserID){
        global $DB;
        $sql='select * from `'.$this->TableName.'` where `UserID`=\''.$UserID.'\'';
        return $DB->Select($sql);
    }
    /**
     * @desc  更新租车订单信息
     * @param $Data
     * @param $OrderNum
     * @return bool|int
     */
    public function UpdateByOrderNum($Data,$OrderNum){
        global $DB;
        if($Data=='' || !is_array($Data)){
            return 0;
        }
        return $DB->updateWhere($this->TableName,$Data,'OrderNum=\''.$OrderNum.'\'');
    }
}
?>