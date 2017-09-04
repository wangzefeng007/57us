<?php

/**
 * @desc  学校录取表
 * Class  StudyConsultantSchoolEnrollModule
 */
Class StudyConsultantSchoolEnrollModule extends CommonModule {

    public $KeyID = 'ID';
    public $TableName = 'study_consultant_school_enroll';

    //状态
    public $Status = array('0'=>'未发送','1'=>'确认中','2'=>'已确认','3'=>'已拒绝');
}
