<?php

/**
 * @desc  旅游当地玩乐产品每日价格表
 * Class TourProductPlaySkuModule
 */
class TourProductLineErverdayPriceModule extends CommonModule{
	public $KeyID = 'DayPriceID';
	public $TableName = 'tour_product_line_erverday_price';

	/**
	 * @desc  根据skuID 出行日期 更新库存
	 * @param $TourProductSkuID
	 * @param $Depart
	 * @return int|string
	 */
	public function UpdateSkuInventoryBy($TourProductSkuID,$Depart){
		if ($TourProductSkuID=='' || $Depart=='')
			return '';
		global $DB;
		return $DB->Update ( 'Update ' . $this->TableName .' set Inventory=Inventory+1 where `Date`=' . $Depart.' and ProductSkuID='.$TourProductSkuID );
	}
}