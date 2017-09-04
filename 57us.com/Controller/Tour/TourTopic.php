<?php
class TourTopic {
	public function __construct() {
		/*底部调用热门旅游目的地、景点*/
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
	}
	/**
	 * @name
	 */
    public function TopicIndex() {
	
		$TourProductLineModule = new TourProductLineModule();
		$TourProductImageModule = new TourProductImageModule();
		$Title = '我有低价，你有空吗？暑期档美国出境游爆款来袭-57美国网';
		$Keywords = '暑假去哪旅游,暑期旅行，暑假哪里旅游,暑假旅游哪里好,暑假旅游去哪,暑假美国旅游,美国暑期旅游,暑期美国旅游,暑假去美国旅游,暑期旅游,2016暑期美国旅游,美国旅游特价,暑假美国游';
		$Description = '57美国网推出“我有低价，你有空吗？”暑期促销活动，美国爆款产品买2送1，单品最低至5折，活动赠送旅程免费wifi+57美国网旅行大礼包，带上死党一起乐不思暑。';
		$cc1 =array();
		$a1 = array(10103,10102,10096,10094,10090,10076);
		$a2 = array(10030,10028,10015,10014,10011,10012);
		$b1 = array(66113,66107,63612,63584,63557,63474);
		$b2 = array(63473,63476,63477,63480,63412,10064);
		$c1 = array(10050,10046,10092,10111,10120,10147);
		$c2 = array(63381,63394,63457,63508,63609,63614);

		foreach ($a1 as $val) {
			$Info = $TourProductLineModule->GetInfoByTourProductID($val);
			$TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID ($val);
			$aa1[] = array($val,$Info['ProductName'],$Info['LowPrice'],$TourImagesInfo['ImageUrl']);
		}
		foreach ($a2 as $val) {
			$Info = $TourProductLineModule->GetInfoByTourProductID($val);
			$TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID ($val);
			$aa2[] = array($val,$Info['ProductName'],$Info['LowPrice'],$TourImagesInfo['ImageUrl']);
		}
		foreach ($b1 as $val) {
			$Info = $TourProductLineModule->GetInfoByTourProductID($val);
			$TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID ($val);
			$bb1[] = array($val,$Info['ProductName'],$Info['LowPrice'],$TourImagesInfo['ImageUrl']);
		}
		foreach ($b2 as $val) {
			$Info = $TourProductLineModule->GetInfoByTourProductID($val);
			$TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID ($val);
			$bb2[] = array($val,$Info['ProductName'],$Info['LowPrice'],$TourImagesInfo['ImageUrl']);
		}
		foreach ($c1 as $val) {
			$Info = $TourProductLineModule->GetInfoByTourProductID($val);
			$TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID ($val);
			$cc1[] = array($val,$Info['ProductName'],$Info['LowPrice'],$TourImagesInfo['ImageUrl']);
		}
		foreach ($c2 as $val) {
			$Info = $TourProductLineModule->GetInfoByTourProductID($val);
			$TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID ($val);
			$cc2[] = array($val,$Info['ProductName'],$Info['LowPrice'],$TourImagesInfo['ImageUrl']);
		}
		include template ( '/TopicIndex');
	}
}