<?php

/**
 * @desc  顾问案例表
 * Class  StudyConsultantCaseModule
 */
Class StudyConsultantCaseModule extends CommonModule {

    public $KeyID = 'CaseID';
    public $TableName = 'study_consultant_case';

    //状态
    public $Status = array('1'=>'已保存','2'=>'展示','3'=>'删除');
}
