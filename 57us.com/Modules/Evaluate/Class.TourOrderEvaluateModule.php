<?php
/**
 * @desc  评价内容表
 * Class  TourOrderEvaluateModule
 */
Class TourOrderEvaluateModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'tour_order_evaluate';
        $this->KeyID = 'EvaluateID';
    }
    /**
     * 更新点赞数
     * @param int $EvaluateID 评价ID
     * @param string $Type 增加 + 减少 -
     * by Leo
     */
    public function UpdatePraiseNum($EvaluateID,$Type='+'){
        if($EvaluateID==''){
            return false;
        }
        global $DB;
        if($Type=='+'){
            $Result=$DB->update("update ".$this->TableName." set PraiseNum=PraiseNum+1 where EvaluateID=$EvaluateID");
        }else{
            $Result=$DB->update("update ".$this->TableName." set PraiseNum=PraiseNum-1 where EvaluateID=$EvaluateID");
        }
        return $Result;
    }
}
