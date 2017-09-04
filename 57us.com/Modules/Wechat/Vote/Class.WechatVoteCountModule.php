<?php

/**
 * @desc  微信投票_票数表
 * Class WechatVoteUserModule
 */
class WechatVoteCountModule extends CommonModule
{

    public $KeyID = 'ID';
    public $TableName = 'wechat_vote_count';

    /**
     * @desc  更新票数
     * @param $UserID
     * @return int|string
     */
    public function UpdateVote($UserID)
    {
        if ($UserID == '')
            return '';
        global $DB;
        return $DB->Update('Update ' . $this->TableName . ' set VoteCount =VoteCount+1 where `UserID`=' . $UserID);
    }

    /**
     * @desc  更新围观人数
     * @param $UserID
     * @return int|string
     */
    public function UpdateOnlookers($UserID)
    {
        if ($UserID == '')
            return '';
        global $DB;
        return $DB->Update('Update ' . $this->TableName . ' set Onlookers =Onlookers+1 where `UserID`=' . $UserID);
    }


    /**
     * @desc
     * @param string $MysqlWhere
     * @return array
     */
    public function GetSearchListsAll($num = '')
    {
        global $DB;
        if ($num) {
            $sql = 'select * from wechat_vote_user AS a,wechat_vote_count AS b where a.IsVote = 1 and a.UserID = b.UserID order by b.VoteCount desc,b.UserID asc limit ' . $num;
        } else {
            $sql = 'select * from wechat_vote_user AS a,wechat_vote_count AS b where a.IsVote = 1 and a.UserID = b.UserID order by b.VoteCount desc,b.UserID asc';
        }
        return $DB->select($sql);
    }


    /**
     * @desc
     * @param string $MysqlWhere
     * @return array
     */
    public function GetNumListsAll()
    {
        global $DB;
        $sql = 'select count(`' . $this->KeyID . '`) as Num from wechat_vote_user AS a,wechat_vote_count AS b where a.IsVote = 1 and a.UserID = b.UserID ';
        return $DB->getone($sql);
    }

    /**
     * @desc  联合查询
     * @param string $MysqlWhere
     * @param int $Offset
     * @param int $Num
     * @param array $FieldArray
     * @return array
     */
    public function GetSearchList($MysqlWhere = '', $Offset = 0, $Num = 10, $FieldArray = array())
    {
        global $DB;
        $sql = 'select * from wechat_vote_user AS a,wechat_vote_count AS b where a.IsVote = 1 and a.UserID = b.UserID order by b.VoteCount desc,b.UserID asc ';
        return $data = $DB->select($sql, $Offset, $Num);
    }
}

?>