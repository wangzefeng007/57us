<?php
class StudyYoosureModule extends CommonModule{
	public $KeyID = 'YoosureID';
	public $TableName = 'study_yoosure';
    public $ViewCount = 'ViewCount';

	public $Crowd = array('1'=>'学龄前','2'=>'小学生','3'=>'中学生','4'=>'大学生','5'=>'亲子团','6'=>'成人游学','7'=>'商务考察');
	public $YoosureTitle = array('1'=>'留学体验','2'=>'能力提升','3'=>'主题探索','4'=>'名校交流','5'=>'全真课堂','6'=>'领袖成长','7'=>'户外技能','8'=>'健康运动','9'=>'亲子营');
	public $DeparturePlace = array('1'=>'北京','2'=>'上海','3'=>'广州','4'=>'香港','5'=>'厦门');
	public $Status = array('1'=>'上架','2'=>'下架','3'=>'删除');

}