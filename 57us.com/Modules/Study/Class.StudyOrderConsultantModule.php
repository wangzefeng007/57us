<?php

/**
 * @desc  留学顾问订单流程详情表
 * Class  StudyOrderConsultantModule
 */
Class StudyOrderConsultantModule extends CommonModule {

    public $KeyID = 'ID';
    public $TableName = 'study_order_consultant';

    //流程详细步骤
    public $Part = array(
        '1'=>array('Headline'=>'Questionnaire','Title'=>'调查表'),
        '2'=>array('Headline'=>'ChooseSchool','Title'=>'选校定校'),
        '3'=>array('Headline'=>'Document','Title'=>'文书服务'),
        '4'=>array('Headline'=>'SchoolApply','Title'=>'申请学校'),
        '5'=>array('Headline'=>'TransactVisa','Title'=>'签证办理'),
        '6'=>array('Headline'=>'Translate','Title'=>'材料翻译'),
        '7'=>array('Headline'=>'Background','Title'=>'背景提升')
    );

    //服务类型下的各项服务
    public $Flow = array(
        '1'=>array(1,2,3,4,5),//全程服务
        '2'=>array(1,2,3,4), //申请学校
        '3'=>array(1,3), //文书管理
        '4'=>array(1,2), //定校方案修改
        '5'=>array(5), //签证指导
        '6'=>array(6), //材料翻译
        '7'=>array(7), //背景提升
    );

    //全程服务的各项资金比例
    public $AllLifeService = array(
        '1'=>0.2,
        '3'=>0.2,
        '4'=>0.4,
        '5'=>0.2
    );

    //学校申请的各项资金比例
    public $SchoolApply = array(
        '1'=>0.2,
        '3'=>0.3,
        '4'=>0.5
    );

    public $Status = array(
        '0'=>'未开始服务',
        '1'=>'服务初始化',
        '2'=>'服务中',
        '3'=>'服务完成',
    );
}
