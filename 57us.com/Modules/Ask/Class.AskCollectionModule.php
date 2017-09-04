<?php
/**
 * @desc  问答收藏表
 * Class  AskCollectionModule
 */
Class AskCollectionModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'ask_collection';
        $this->KeyID = 'CollectionID';
    }
}
