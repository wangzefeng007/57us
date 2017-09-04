<?php
class TourPassAttractionsModule extends CommonModule{
	public function __construct() {
		$this->TableName = 'tour_pass_attractions';
		$this->KeyID = 'TourPassAttractionsID';
	}
}