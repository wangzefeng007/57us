<?php

/**
 * @desc  留学语言培训教师课程表
 * Class  StudyTeacherInfoModule
 */
Class StudyTeacherCourseModule extends CommonModule {

    public $KeyID = 'CourseID';
    public $TableName = 'study_teacher_course';
    
    //培训科目
    public $CourseType=array('1'=>'雅思','2'=>'托福','3'=>'SAT','4'=>'ACT','5'=>'GAMT','6'=>'GRE','7'=>'PTE');
    //审核状态
    public $Status=array('0'=>'草稿','1'=>'提交审核','2'=>'审核失败','3'=>'上架','4'=>'下架','5'=>'删除','7'=>'PTE');
    //上课方式
    public $TeachType=array('1'=>'线上','2'=>'线下');    
    //班级规模
    public $ClassSize=array('1'=>'小班','2'=>'大班','3'=>'一对一');        
    
    //查询符合条件的教师 a-课程表 b-基础信息表
    public function SelectCourseMemberInfo($MysqlWhere='',$Fields=''){
        global $DB;
        if($Fields==""){
            //查询全部
            $sql="select * from study_teacher_course as a,member_user_info as b where a.UserID=b.UserID and b.Identity=3 and b.IdentityState=2 $MysqlWhere ";
        }elseif(is_array($Fields)){
            //以数组形式查询字段
            $FieldsStr=implode(',',$Fields);
            $sql="select $FieldsStr from study_teacher_course as a,member_user_info as b where a.UserID=b.UserID and b.Identity=3 and b.IdentityState=2 $MysqlWhere ";
        }else{
            //以SQL语句形式查询
            $sql="select $Fields from study_teacher_course as a,member_user_info as b where a.UserID=b.UserID and b.Identity=3 and b.IdentityState=2 $MysqlWhere ";
        }
        return $DB->select($sql);
    }        
}
