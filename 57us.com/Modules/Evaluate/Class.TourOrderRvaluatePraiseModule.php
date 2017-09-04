<?php
/**
 * @desc  点赞表
 * Class  TourOrderRvaluatePraiseModule
 */
Class TourOrderRvaluatePraiseModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'tour_order_evaluate_praise';
        $this->KeyID = 'PraiseID';
    }
    
}
