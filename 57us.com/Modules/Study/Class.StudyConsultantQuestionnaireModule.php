<?php

/**
 * @desc  调查表
 * Class  StudyConsultantQuestionnaireModule
 */
Class StudyConsultantQuestionnaireModule extends CommonModule {

    public $KeyID = 'ID';
    public $TableName = 'study_consultant_questionnaire';

    //反馈对象
    public $Feedback = array('1'=>'学生','2'=>'顾问');
    //状态
    public $Status = array('0'=>'未发送','1'=>'确认中','2'=>'已确认');
}
