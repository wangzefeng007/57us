<?php
/**
 * @desc  回答点赞表
 * Class  AskInfoModule
 */
Class AskThumbupModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'ask_thumbup';
        $this->KeyID = 'LogID';
    }
}
