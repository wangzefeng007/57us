<?php

/**
 * @desc  留学会员匹配顾问，信息表
 * Class  StudyMarryInfoModule
 */
Class StudyMarryInfoModule extends CommonModule {

    public $KeyID = 'MarryID';
    public $TableName = 'study_marry_info';

    //服务类型
    public $ServiceType = array('1'=>'全程服务','2'=>'申请学校','3'=>'文书服务','4'=>'定校选校','5'=>'签证培训','6'=>'材料翻译','7'=>'背景提升');
    //申请层次
    public $TargetLevel = array('1'=>'高中','2'=>'本科','3'=>'研究生','4'=>'转学');

    /**
     * @desc  更新匹配选择顾问次数
     */
    public function UpdateTimesByID($ID){
        global $DB;
        $sql = "update {$this->TableName} set `Times` = `Times`-1 WHERE `MarryID` = {$ID}";
        return $DB->execute($sql);
    }
}
