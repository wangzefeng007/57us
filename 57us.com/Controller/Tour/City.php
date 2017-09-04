<?php
class City
{
    public function __construct()
    {
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }
    public function Index(){
        $TourCitySpecialModule = new TourCitySpecialModule();
        $TourAreaModule = new TourAreaModule();
        $MysqlWhere = ' and Status = 1';
        $Data['Data'] = $TourCitySpecialModule->GetLists($MysqlWhere, $Offset = 0, $Num = 12);
        foreach ($Data['Data'] as $key =>$value){
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['AreaID']);
            $Data['Data'][$key]['CnName'] = trim($TourAreaInfo['CnName']);
            $Data['Data'][$key]['Alias'] = trim($TourAreaInfo['Alias']);
            $Data['Data'][$key]['Description'] = trim($TourAreaInfo['Description']);
            $Data['Data'][$key]['EnName'] = trim($TourAreaInfo['EnName']);
        }
        $Title = '美国城市_美国旅游城市_美国东部城市_美国十大旅游城市- 57美国网';
        $Keywords = '美国城市,美国旅游城市,热门旅游城市,国东部城市,美国西部城市,美国中部城市,美国有哪些城市,美国旅游城市排名,美国十大旅游城市,美国西部旅行';
        $Description = '57美国网城市旅游频道，为您提供美国热门旅游城市的跟团游、自由行、行程定制、景点门票、租车、境外wifi、签证等全方位的旅游在线预订服务。了解美国城市旅游攻略，规划美国城市旅游行程，预订美国城市游线路，尽在57美国网！';
        include template ( "City" );
    }
    public function CityDetail(){
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductLineModule = new TourProductLineModule();
        $TourProductImageModule = new TourProductImageModule();
        $TourAreaModule = new TourAreaModule();
        $TourCitySpecialModule = new TourCitySpecialModule();
        $HotelBaseInfoModule = new HotelBaseInfoModule();
        $Alias = trim($_GET['City']);
        $TourAreaInfo = $TourAreaModule->GetInfoByWhere(' and Alias LIKE \'%'.$Alias.'%\'');
        $list = $TourCitySpecialModule->GetInfoByWhere(' and AreaID = '.$TourAreaInfo['AreaID']);
        $list['MustTravel'] = json_decode($list['MustTravel'],true);
        $list['NewRecommend'] = json_decode($list['NewRecommend'],true);
        $list['SpecialEvent'] = json_decode($list['SpecialEvent'],true);
        $list['TourProductIDS'] = explode(',',$list['TourProductIDS']);
        if ($list['TourProductIDS'][0]!=''){
            foreach ($list['TourProductIDS'] as $key=>$value){
                $ProductPlayBase = $TourProductPlayBaseModule->GetInfoByTourProductID($value);
                if ($ProductPlayBase != true){
                    $ProductLine = $TourProductLineModule->GetInfoByTourProductID($value);
                    $list['TourProduct'][$key]['title'] = $ProductLine['ProductName'];
                    $list['TourProduct'][$key]['LowPrice'] = $ProductLine['LowPrice'];
                    $list['TourProduct'][$key]['type'] = 2;
                }else{
                    $list['TourProduct'][$key]['title'] = $ProductPlayBase['ProductName'];
                    $list['TourProduct'][$key]['LowPrice'] = $ProductPlayBase['LowPrice'];
                    $list['TourProduct'][$key]['type'] = 1;
                }
                $ProductImage = $TourProductImageModule->GetInfoByTourProductID($value);
                $list['TourProduct'][$key]['Image'] = $ProductImage['ImageUrl'];
                $list['TourProduct'][$key]['ID'] = $value;
            }
        }
        $list['HotelIDS'] = explode(',',$list['HotelIDS']);
        if ($list['HotelIDS'][0]!='' ){
            foreach ( $list['HotelIDS'] as $key=>$value) {
                $BaseInfo =  $HotelBaseInfoModule->GetHotelByID($value);
                $list['Hotel'][$key]['Name_Cn'] = $BaseInfo['Name_Cn'];
                $list['Hotel'][$key]['LowPrice'] = $BaseInfo['LowPrice'];
                $list['Hotel'][$key]['Image'] = $BaseInfo['Image'];
                $list['Hotel'][$key]['ID'] = $value;
            }
        }
        $Title = $TourAreaInfo['CnName'].'旅游_'.$TourAreaInfo['CnName'].'旅行_'.$TourAreaInfo['CnName'].'自由行_美国'.$TourAreaInfo['CnName'].' - 57美国网';
        $Keywords = $TourAreaInfo['CnName'].'旅游,'.$TourAreaInfo['CnName'].'旅行,'.$TourAreaInfo['CnName'].'自由行,美国'.$TourAreaInfo['CnName'].','.$TourAreaInfo['CnName'].'旅游攻略,'.$TourAreaInfo['CnName'].'美食,'.$TourAreaInfo['CnName'].'多日游,'.$TourAreaInfo['CnName'].'一日游,'.$TourAreaInfo['CnName'].'跟团游,'.$TourAreaInfo['CnName'].'当地参团,'.$TourAreaInfo['CnName'].'景点门票,'.$TourAreaInfo['CnName'].'门票,'.$TourAreaInfo['CnName'].'吃喝玩乐';
        $Description = '57美国网'.$TourAreaInfo['CnName'].'旅游频道，详细介绍'.$TourAreaInfo['CnName'].'热门景点，推荐'.$TourAreaInfo['CnName'].'热门旅游产品、酒店住宿、吃喝玩乐攻略以及相关促销活动。了解'.$TourAreaInfo['CnName'].'旅游攻略，规划'.$TourAreaInfo['CnName'].'旅游行程，预订'.$TourAreaInfo['CnName'].'旅游线路，尽在57美国网！';
        include template ( "CityDetail" );
    }
}