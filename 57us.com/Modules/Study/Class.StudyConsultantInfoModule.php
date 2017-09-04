<?php

/**
 * @desc 留学顾问信息表
 * Class  StudyConsultantInfoModule
 */
Class StudyConsultantInfoModule extends CommonModule {

    public $KeyID = 'ID';
    public $TableName = 'study_consultant_info';

    //服务类型
    public $ServiceProject = array('1'=>'美国高中','2'=>'美国本科','3'=>'美国研究生','4'=>'艺术留学','5'=>'艺术科研');
    //辅导对象
    public $TutorialObject = array('1'=>'中学','2'=>'大学', '3'=>'硕士', '4'=>'桥梁', '5'=>'双保录', '6'=>'预科');
    //顾问等级
    public $Grade = array('1'=>'非排他（在其他平台也注册）', '2'=>'排他（不在其他平台注册）');
    //顾问资金比例
    public $Scale = array('1'=>0.8,'2'=>0.85);
    
    
    //查询符合条件的顾问 a-顾问信息表 b-基础信息表 c-服务表筛选出的集合
    public function SelectConsultantMemberInfo($MysqlWhere='',$Fields=''){
        global $DB;
        if($Fields==""){
            //查询全部
            $sql="select * from study_consultant_info as a,member_user_info as b,(select count(ServiceID) as Num,UserID from study_consultant_service where `Status`=3 group by UserID) as c where a.UserID=b.UserID and b.Identity=2 and b.IdentityState=2 and a.UserID=c.UserID $MysqlWhere ";
        }elseif(is_array($Fields)){
            //以数组形式查询字段
            $FieldsStr=implode(',',$Fields);
            $sql="select $FieldsStr from study_consultant_info as a,member_user_info as b,(select count(ServiceID) as Num,UserID from study_consultant_service where `Status`=3 group by UserID) as c where a.UserID=b.UserID and b.Identity=2 and b.IdentityState=2 and a.UserID=c.UserID $MysqlWhere ";
        }else{
            //以SQL语句形式查询
            $sql="select $Fields from study_consultant_info as a,member_user_info as b,(select count(ServiceID) as Num,UserID from study_consultant_service where `Status`=3 group by UserID) as c where a.UserID=b.UserID and b.Identity=2 and b.IdentityState=2 and a.UserID=c.UserID $MysqlWhere ";
        }
        return $DB->select($sql);
    }

    /**
     * @desc  手机端匹配顾问
     * @param $Where  查询条件 数组
     */
    public function MobileSelectConsultantInfos($MysqlWhere='',$Fields=''){
        global $DB;
        $FieldsStr=implode(',',$Fields);
        $sql = "select $FieldsStr from study_consultant_info as a,member_user_info as b,study_consultant_service as c where a.UserID=b.UserID and b.Identity=2 and b.IdentityState=2 and a.UserID=c.UserID and `Status`=3 $MysqlWhere";
        return $DB->select($sql);
    }

    /**
     * @desc  根据条件获取顾问信息
     * @param string $MysqlWhere
     * @param string $Fields
     * @return array
     *
     */
    public function GetConInfoByWhere($MysqlWhere='',$Fields=''){
        global $DB;
        if($Fields){
            $FieldsStr=implode(',',$Fields);
        }
        else{
            $FieldsStr = '*';
        }
        $sql = "select $FieldsStr from study_consultant_info as a,member_user_info as b where a.UserID=b.UserID and b.Identity=2 and b.IdentityState=2  $MysqlWhere";
        return $DB->select($sql);
    }

    /**
     * @desc  添加选择该顾问的人数（手机端）
     * @param $ID
     * @return int
     */
    public function UpdateChoosedByID($ID){
        global $DB;
        $sql = "update {$this->TableName} set `Choosed` = `Choosed`+1 WHERE `UserID` = {$ID}";
        return $DB->execute($sql);
    }
}
