<?php
/**
 * @desc 旅游地区表
 * Class TourAreaModule
 */
Class TourAreaModule extends CommonModule{
	public $KeyID = 'AreaID';
    public $TableName = 'tour_area';

    /**
     * 获取目的地列表
     * 返回结果 成功返回 array 失败 fasle
     *
     * @param string $MysqlWhere 查询条件
     * @param int $Offset 起始下标
     * @param int $Num 获取数量
     * @return mixed 返回信息
     */
    public function GetTourAreaList($MysqlWhere = '', $Offset, $Num, $OrderBy = ''){
        if (empty ( trim ( $OrderBy ) )) {
            $OrderBy = $this->KeyID . ' asc';
        }
        global $DB;
        $sql = 'select * from ' . $this->TableName . ' where 1=1' . $MysqlWhere . ' order by ' . $OrderBy;
        return $DB->Select ( $sql, $Offset, $Num );
    }
}