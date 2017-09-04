<?php

/**
 * @desc  旅游城市专题表
 * Class TourCitySpecialModule
 */
class TourCitySpecialModule extends CommonModule{
	public $KeyID = 'CitySpecialID';
	public $TableName = 'tour_city_special';

	public $Status = array(
		'0'=>'下架',
		'1'=>'上架',
	);
}