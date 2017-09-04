<?php
Class MemberUserInfoModule extends CommonModule  {

    public $KeyID = 'InfoID';
    public $TableName = 'member_user_info';

    public $SexArr=array('0'=>'女','1'=>'男','2'=>'保密');
    
    /**
     * @desc  根据UserID查询单条数据详情
     * @param string $UserID
     * @return array|int
     */
    public function GetInfoByUserID($UserID = '') {

        global $DB;
        if ($UserID == '')
            return 0;
        $sql = 'select * from ' . $this->TableName . ' where  UserID = ' . $UserID;
        return $DB->getone ( $sql );
    }

    /**
     * @desc  更新用户信息
     * @param $Data
     * @param $ID
     * @return bool|int
     */
    public function UpdateData($Data,$ID){
        global $DB;
        return $DB->UpdateWhere($this->TableName,$Data,'`UserID`='.$ID);
    }    

    /**
     * @desc  查询顾问地址（去重）
     * @param $Field
     * @param $type 1=>pc端获取地区数据。2=>手机端获取地区数据
     * @return array
     */
    public function RemoveDuplicate($Field,$type = 1){
        global $DB;
        $sql = 'SELECT DISTINCT('.$Field.') FROM '.$this->TableName .' WHERE Identity = 2 AND IdentityState = 2';
        $result = $DB->select ( $sql );
        $array = array();
        foreach ($result as $key=>$val){
            if(!empty($val[$Field])){
                if($val[$Field] == '请选择城市'){
                    continue;
                }
                else{
                    if($type == 1){
                        $array[] = array('AeraID'=>$val[$Field],'name'=>$val[$Field]);;
                    }
                    elseif($type == 2){
                        $array[] = $val[$Field];
                    }
                }
            }
        }
        return $array;
    }
    
    /**
     * @desc  查询老师地址（去重）
     * @param $Field
     * @return array
     */
    public function TeacherRemoveDuplicate($Field){
        global $DB;
        $sql = 'SELECT DISTINCT('.$Field.') FROM '.$this->TableName .' WHERE Identity = 3 AND IdentityState = 2';
        $result = $DB->select ( $sql );
        $array = array();
        foreach ($result as $key=>$val){
            if(!empty($val[$Field])){
                if($val[$Field] == '请选择城市'){
                    continue;
                }
                else{
                    $array[] = array('AeraID'=>$val[$Field],'name'=>$val[$Field]);;
                }
            }
        }
        return $array;
    }


    /**
     * @desc 检测昵称
     * @param $NickName
     * @return array
     */
    public function CheckNickName($NickName){
        global $DB;
        $sql='select * from `'.$this->TableName.'` where `NickName`=\''.$NickName.'\'';
        return $DB->GetOne($sql);
    }

    /**
     * @desc 获取用户详情
     * @param $ID
     * @return array
     */
    public function GetUserInfo($ID){
        global $DB;
        $sql='select * from `'.$this->TableName.'` where `UserID`='.$ID;
        return $DB->GetOne($sql);
    }

    /**
     * @desc  添加用户信息
     * @param $Data
     * @return int
     */
    public function InsertData($Data){
        global $DB;
        return $DB->insertArray($this->TableName,$Data,true);
    }

}
