<?php
/**
 * @desc  问答标签表
 * Class  AskInfoModule
 */
Class AskTagModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'ask_tag';
        $this->KeyID = 'TagID';
    }
}
