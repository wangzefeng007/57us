<?php

/**
 * @desc  办理签证
 * Class  StudyConsultantTransactVisaModule
 */
Class StudyConsultantTransactVisaModule extends CommonModule {

    public $KeyID = 'ID';
    public $TableName = 'study_consultant_transact_visa';

    //状态
    public $Status = array('1'=>'办理中','2'=>'获签','3'=>'拒签');
}
