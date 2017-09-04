<?php
/**
 * @desc  回答内容表
 * Class  AskAnswerInfoModule
 */
Class AskAnswerInfoModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'ask_answer_info';
        $this->KeyID = 'AnswerID';
    }
    /**
     * @desc  更新点赞数
     * @param AskID
     * @return int|string
     */
    public function UpdatePraiseNum($AnswerID)
    {
        if ($AnswerID == '')
            return '';
        global $DB;
        return $DB->Update('Update ' . $this->TableName . ' set PraiseNum=PraiseNum+1 where `AnswerID`=' . $AnswerID);
    }
}
