<?php
/**
 * @desc  租车产品推荐表
 * Class ZucheRecommendModule
 */
class ZucheRecommendModule extends CommonModule{
    public $KeyID = 'ID';
    public $TableName = 'zuche_recommend';

    /**
     * 添加租车信息
     * 返回结果 ID 或 false
     * @param array $Data 出租信息
     * @param int or false
     */
    public function InsertInfo($Data)
    {
        global $DB;
        return $DB->insertArray($this->TableName, $Data,true);
    }

    /**
     * @desc  根据keyID更新单数据
     * @param array $Array
     * @param string $KeyID
     * @return bool|int
     */

    public function UpdateRecommend($Data,$ID){
        global $DB;
        return $DB->UpdateWhere($this->TableName,$Data,'`'.$this->KeyID.'`='.$ID);
    }

    public function GetRecommendList($MysqlWhere = '', $Offset, $Num, $OrderBy = ''){
        if (empty ( trim ( $OrderBy ) )){
            $OrderBy = $this->KeyID . ' asc';
        }
        global $DB;
        $sql = 'select * from ' . $this->TableName . ' where 1=1' . $MysqlWhere . ' order by ' . $OrderBy;
        return $DB->Select ( $sql, $Offset, $Num );
    }

    public function GetList($SqlWhere){
        global $DB;
        $sql = 'select * from ' . $this->TableName . ' where 1=1' . $SqlWhere;
        return $DB->Select($sql);
    }

    public function DeleteRecommend($ID) {
        global $DB;
        $sql = 'delete from `' . $this->TableName . '` where ' . $this->KeyID . '=' . $ID;
        return $DB->Delete ( $sql );
    }

    public function GetRecommendInfo($ID) {
        global $DB;
        $sql = 'select * from ' . $this->TableName . ' where ' . $this->KeyID . '=' . $ID;
        return $DB->GetOne ( $sql );
    }
}
?>