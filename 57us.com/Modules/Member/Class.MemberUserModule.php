<?php
Class MemberUserModule extends CommonModule  {

    public $KeyID = 'UserID';
    public $TableName = 'member_user';

    /**
     * @desc  查询账号
     * @param $Account
     * @return array|bool
     */
    public function AccountExists($Account){
            global $DB;
            if(is_numeric($Account)){
                $Sql = 'SELECT '.$this->KeyID.' FROM ' . $this->TableName . ' where Mobile='.$Account;
            }elseif(strpos($Account,'@')){
                $Sql = 'SELECT '.$this->KeyID.' FROM ' . $this->TableName . ' where `E-Mail`=\''.$Account.'\'';
            }else{
                return false;
            }
            return $DB->GetOne($Sql);
    }

    /**
     * @desc 获取用户账号信息
     * @param $UserID
     * @return array
     */
    public function GetUserByID($UserID){
        global $DB;
        $Sql='select Mobile,`E-Mail`,PassWord from `'.$this->TableName.'` where `'.$this->KeyID.'`='.$UserID;
        return $DB->GetOne($Sql);
    }

    /**
     * @desc  登录验证
     * @param $Account
     * @param $PassWord
     * @return bool
     */
    public function CheckUser($Account,$PassWord){
        global $DB;
        if(is_numeric($Account)){
            $Sql = 'SELECT '.$this->KeyID.' FROM ' . $this->TableName . ' where Mobile='.$Account.' and PassWord=\''.$PassWord.'\'';
        }elseif(strpos($Account,'@')){
            $Sql = 'SELECT '.$this->KeyID.' FROM ' . $this->TableName . ' where `E-Mail`=\''.$Account.'\' and PassWord=\''.$PassWord.'\'';
        }else{
            return false;
        }
        $result=$DB->GetOne($Sql);
        if($result){
            return $result[$this->KeyID];
        }else{
            return false;
        }
    }

    /**
     * @desc  添加用户
     * @param $Data
     * @return int
     */
    public function InsertUser($Data){
        global $DB;
        return $DB->insertArray($this->TableName,$Data,true);
    }
    /**
     * @desc  更新用户
     * @param $Data
     * @param $ID
     * @return bool|int
     */
    public function UpdateUser($Data,$ID){
        global $DB;
        return $DB->UpdateWhere($this->TableName,$Data,'`'.$this->KeyID.'`='.$ID);
    }
    /**
     * @desc  更新用户
     * @param $Mobile
     * @return array
     */
    public function GetUserIDbyMobile($Mobile){
        global $DB;
        $sql='select * from `'.$this->TableName.'` where `Mobile`='.$Mobile;
        return $DB->GetOne($sql);
    }
}