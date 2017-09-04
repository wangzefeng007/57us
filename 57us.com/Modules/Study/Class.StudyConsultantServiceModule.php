<?php

/**
 * @desc  留学顾问服务表
 * Class  StudyConsultantServiceModule
 */
Class StudyConsultantServiceModule extends CommonModule {

    public $KeyID = 'ServiceID';
    public $TableName = 'study_consultant_service';

    //服务类型
    public $ServiceType = array('1'=>'全程服务','2'=>'申请学校','3'=>'文书服务','4'=>'定校方案修改','5'=>'签证培训','6'=>'材料翻译','7'=>'背景提升');
    //申请层次
    public $TargetLevel = array('1'=>'高中','2'=>'本科','3'=>'研究生','4'=>'转学');
    //状态
    public $Status = array('0'=>'草稿','1'=>'提交审核','2'=>'审核失败','3'=>'上架','4'=>'下架','5'=>'删除');

    //查询符合条件的顾问 a-服务表 b-基础信息表
    public function SelectServiceMemberInfo($MysqlWhere='',$Fields=''){
        global $DB;
        if($Fields==""){
            //查询全部
            $sql="select * from study_consultant_service as a,member_user_info as b where a.UserID=b.UserID and b.Identity=2 and b.IdentityState=2 $MysqlWhere ";
        }elseif(is_array($Fields)){
            //以数组形式查询字段
            $FieldsStr=implode(',',$Fields);
            $sql="select $FieldsStr from study_consultant_service as a,member_user_info as b where a.UserID=b.UserID and b.Identity=2 and b.IdentityState=2 $MysqlWhere ";
        }else{
            //以SQL语句形式查询
            $sql="select $Fields from study_consultant_service as a,member_user_info as b where a.UserID=b.UserID and b.Identity=2 and b.IdentityState=2 $MysqlWhere ";
        }
        return $DB->select($sql);
    }   
}
