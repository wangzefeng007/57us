<?php
/**
 * @desc  产品评价统计
 * Class  TourOrderEvaluateCountModule
 */
Class TourOrderEvaluateCountModule extends CommonModule {
    public function __construct() {
        $this->TableName = 'tour_order_evaluate_count';
        $this->KeyID = 'CountID';
    }
    
}
