<?php

/**
 * @desc  留学语言培训教师信息表
 * Class  StudyTeacherInfoModule
 */
Class StudyTeacherInfoModule extends CommonModule {

    public $KeyID = 'ID';
    public $TableName = 'study_teacher_info';

    //教师等级
    public $Grade=array('1'=>'教师','2'=>'资深教师');
    //辅导对象
    public $TutorialObject = array('1'=>'中学','2'=>'大学','3'=>'硕士','4'=>'桥梁','5'=>'双保录','6'=>'预科');
   
    //查询符合条件的教师 a-教师信息表 b-基础信息表 c-课程表筛选出的集合
    public function SelectTeacherMemberInfo($MysqlWhere='',$Fields=''){
        global $DB;
        if($Fields==""){
            //查询全部
            $sql="select * from study_teacher_info as a,member_user_info as b,(select count(CourseID) as Num,UserID from study_teacher_course where `Status`=3 group by UserID) as c where a.UserID=b.UserID and b.Identity=3 and b.IdentityState=2 and a.UserID=c.UserID $MysqlWhere ";
        }elseif(is_array($Fields)){
            //以数组形式查询字段
            $FieldsStr=implode(',',$Fields);
            $sql="select $FieldsStr from study_teacher_info as a,member_user_info as b,(select count(CourseID) as Num,UserID from study_teacher_course where `Status`=3 group by UserID) as c where a.UserID=b.UserID and b.Identity=3 and b.IdentityState=2 and a.UserID=c.UserID $MysqlWhere ";
        }else{
            //以SQL语句形式查询
            $sql="select $Fields from study_teacher_info as a,member_user_info as b,(select count(CourseID) as Num,UserID from study_teacher_course where `Status`=3 group by UserID) as c where a.UserID=b.UserID and b.Identity=3 and b.IdentityState=2 and a.UserID=c.UserID $MysqlWhere ";
        }
        return $DB->select($sql);
    }    
}
