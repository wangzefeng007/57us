<?php
/**
 * @desc  临时订单表
 * Class  MemberOrderTempModule
 */
Class MemberOrderTempModule  extends CommonModule{
    public function __construct() {
        $this->KeyID = 'OrderID';
        $this->TableName = 'member_order_temp';
    }


    /**
     * 获取订单信息
     * 成功返回 数组
     * @param string OrderID
     * @param int UserID
     * @return boolean
     */
    public function GetOrderByID($OrderID){
        global $DB;
        $Sql='select * from `'.$this->TableName.'` where OrderID=\''.$OrderID.'\'';
        return $DB->GetOne($Sql);
    }

    /**
     * 更新账目
     * 返回布尔值
     * @param array $Data 账目信息
     * @param int $ID 用户ID
     * @param int or false
     */
    public function UpdateData($Data,$OrderNo){
        global $DB;
        return $DB->UpdateWhere($this->TableName,$Data,'`OrderID`=\''.$OrderNo.'\'');
    }
    /**
     * 删除账目
     * 返回布尔值
     * @param int $OrderNo 账单
     * @param int or false
     */
    public function DeleteData($OrderNo){
        global $DB;
        $sql='delete from `'.$this->TableName.'` where '.$this->KeyID.'=\''.$OrderNo.'\'';
        return $DB->Delete($sql);
    }
}
