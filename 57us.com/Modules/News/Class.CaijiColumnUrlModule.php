<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/9/13
 * Time: 15:19
 * CaijiColumnUrlModule
 * @desc  采集栏目表
 */
class CaijiColumnUrlModule extends  CommonModule {

    public $KeyID = 'ColumnID';
    public $TableName = 'caiji_column_url';
    public $Num = 'Num';//文章数量
    /**
     * @desc  更新文章数量
     * @param string $ColumnID
     * @return string
     */
    public function UpdateNum($ColumnID='') {
        if ($ColumnID=='')
            return '';
        global $DB;
        return $DB->Update ( 'Update ' . $this->TableName .' set '.$this->Num.'='.$this->Num.'+1 where `'. $this->KeyID . '`=' . $ColumnID );
    }
}
