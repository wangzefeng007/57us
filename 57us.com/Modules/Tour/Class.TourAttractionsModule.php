<?php
/**
 * @desc 旅游景点表
 * Class TourAttractionsModule
 */
Class TourAttractionsModule extends CommonModule{
	public function __construct() {
		$this->TableName = 'tour_attractions';
		$this->KeyID = 'ID';
	}
}