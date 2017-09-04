<?php

/**
 * @desc  留学语言培训教师案例表
 * Class  StudyTeacherInfoModule
 */
Class StudyTeacherCaseModule extends CommonModule {

    public $KeyID = 'CaseID';
    public $TableName = 'study_teacher_case';
    
    //培训科目
    public $CourseType=array('托福','雅思','SAT','ACT','GRE','SAMT','PTE');
    
    //培训类别
    public $TrainingType=array('阅读','口语','写作','听力');
}
