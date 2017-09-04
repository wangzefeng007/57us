<?php
/**
 * @desc  问答类别表
 * Class  AskCategoryModule
 */
Class AskCategoryModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'ask_category';
        $this->KeyID = 'AskCategoryID';
    }
    
    /**
     * 更新问答数量
     * @param int $AskCategoryID 问答类别ID
     * @param string $Type 增加 + 减少 -
     * by Leo
     */
    public function UpdateProblemNum($AskCategoryID,$Type='+'){
        if($AskCategoryID==''){
            return false;
        }
        global $DB;
        if($Type=='+'){
            $Result=$DB->update("update ".$this->TableName." set ProblemNum=ProblemNum+1,PartakeNum=PartakeNum+1 where AskCategoryID=$AskCategoryID");
        }else{
            $Result=$DB->update("update ".$this->TableName." set ProblemNum=ProblemNum-1,PartakeNum=PartakeNum-1 where AskCategoryID=$AskCategoryID");
        }
        return $Result;
    }
    
    /**
     * 更新参与人的数量/回答时用到
     * @param int $AskCategoryID 问答类别ID
     * @param string $Type 增加 + 减少 -
     * by Leo
     */
    public function UpdatePartakeNum($AskCategoryID,$Type='+'){
        if($AskCategoryID==''){
            return false;
        }
        global $DB;
        if($Type=='+'){
            $Result=$DB->update("update ".$this->TableName." set PartakeNum=PartakeNum+1 where AskCategoryID=$AskCategoryID");
        }else{
            $Result=$DB->update("update ".$this->TableName." set PartakeNum=PartakeNum-1 where AskCategoryID=$AskCategoryID"); 
        }
        return $Result;
    }

    /**
     * 更新问答数量
     * @param int $AskCategoryID 问答类别ID
     * @param string $Type 增加 + 减少 -
     * by Leo
     */
    public function DeleteProblemNum($AskCategoryID){
        if($AskCategoryID==''){
            return false;
        }
        global $DB;
        $Result=$DB->update("update ".$this->TableName." set ProblemNum=ProblemNum-1 where AskCategoryID=$AskCategoryID");
        return $Result;
    }
}
