<?php
/**
 * @desc  问答表
 * Class  AskInfoModule
 */
Class AskInfoModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'ask_info';
        $this->KeyID = 'AskID';
    }

    /**
     * @desc  更新浏览数
     * @param AskID
     * @return int|string
     */
    public function UpdateBrowseNum($AskID)
    {
        if ($AskID == '')
            return '';
        global $DB;
        return $DB->Update('Update ' . $this->TableName . ' set BrowseNum=BrowseNum+1,Cent=Cent+1 where `AskID`=' . $AskID);
    }

    /**
     * @desc  更新关注数
     * @param AskID
     * @return int|string
     */
    public function UpdateFollowNum($AskID)
    {
        if ($AskID == '')
            return '';
        global $DB;
        return $DB->Update('Update ' . $this->TableName . ' set FollowNum=FollowNum+1,Cent=Cent+2 where `AskID`=' . $AskID);
    }

    /**
     * @desc  更新回答数
     * @param AskID
     * @return int|string
     */
    public function UpdateAnswerNum($AskID)
    {
        if ($AskID == '')
            return '';
        global $DB;
        return $DB->Update('Update ' . $this->TableName . ' set AnswerNum=AnswerNum+1,Cent=Cent+2 where `AskID`=' . $AskID);
    }
    /**
     * @desc 取消关注数
     * @param AskID
     * @return int|string
     */
    public function UpdatedownFollowNum($AskID)
    {
        if ($AskID == '')
            return '';
        global $DB;
        return $DB->Update('Update ' . $this->TableName . ' set FollowNum=FollowNum-1,Cent=Cent-2 where `AskID`=' . $AskID);
    }

    /**
     * @desc  更新站队操作需要的数据
     * @param AskID
     * @return int|string
     */
    public function UpdateStandNum($AskID,$Stand)
    {
        if ($AskID == '')
            return '';
        global $DB;
        if($Stand == 1){
            return $DB->Update('Update ' . $this->TableName . ' set AnswerNum=AnswerNum+1,Cent=Cent+2,StandSquareNum=StandSquareNum+1 where `AskID`=' . $AskID);
        }
        else{
            return $DB->Update('Update ' . $this->TableName . ' set AnswerNum=AnswerNum+1,Cent=Cent+2,StandBackNum=StandBackNum+1 where `AskID`=' . $AskID);
        }
    }

}
