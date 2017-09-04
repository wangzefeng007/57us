<?php

class M
{

    public function __construct()
    {}

    /**
     * 资讯首页
     */
    public function Index()
    {
        // 旅游资讯推荐
        $TblTourModule = new TblTourModule();
        $TourTuijian = $TblTourModule->GetInfoByWhere(' and M1 = 1 order by Sort asc,AddTime desc limit 6', true);
        
        // 留学资讯推荐
        $AbroadModule = new TblStudyAbroadModule();
        $StudyTuijian = $AbroadModule->GetInfoByWhere(' and M1 = 1 order by Sort asc,AddTime desc limit 6', true);
        
        // 移民资讯推荐
        $ImmigrationModule = new TblImmigrationModule();
        $ImmigrationTuijian = $ImmigrationModule->GetInfoByWhere(' and M1 = 1 order by Sort asc,AddTime desc limit 6', true);
        $ArrayAll = array_merge($TourTuijian,$StudyTuijian,$ImmigrationTuijian);
        $ArrayAll = $this->NewSort($ArrayAll, 'AddTime', 'desc');
        
        // 广告信息获取
        $WapIndexBaner = NewsGetAdInfo('WapIndexBaner');
        $WapIndexJingCai = NewsGetAdInfo('WapIndexJingCai');
        
        $Title = '【美国旅游/留学/移民】一站式服务平台 - 57美国网(57us.com)';
        $Keywords = '美国旅游,美国留学,美国投资移民,美国自驾游费用,美国旅游攻略,美国留学费用,美国移民条件,美国游学夏令营';
        $Description = '57美国网是提供去美国旅游、留学、移民资讯、论坛和产品的一站式服务平台。去美国旅游和留学更轻松省钱，从旅行规划到完整行程预订一站搞定，并有赴美产子、赴美就医、游学、美国EB5投资移民等服务。美国留学顾问平台提供美国高中和大学申请，包括碎片化、全套服务和留学考试培训。去美国，就上57美国网！';
        $MyAction = 'Index';
        include template('NewsIndex');
    }

    public function NewSort($list, $field, $sortby = 'asc')
    {
        if (is_array($list)) {
            $refer = $resultSet = array();
            foreach ($list as $i => $data) {
                $refer[$i] = &$data[$field];
            }
            switch ($sortby) {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc': // 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val) {
                $resultSet[] = &$list[$key];
            }
            return $resultSet;
        }
        return false;
    }
}
