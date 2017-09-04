<?php
class Tour {
    public function __construct() {
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }
    /**
     * 生成首页静态末班
     * @abstract WANG
     */
    public function DoIndexTour() {
        $aaa=$_GET['aaa'];
        if ($aaa =='aaa'){
            $Html = file_get_contents("http://tour.57us.com/Index.php?Module=Tour&Action=Index");
            $myfile = fopen(SYSTEM_ROOTPATH."/Templates/Tour/NewIndexTour.htm", "w") or die("Unable to open file!");
            fwrite($myfile, $Html);
            fclose($myfile);
        }
        include template ( "NewIndexTour" );
    }
    /**
     * 首页
     * @abstract WANG
     */
    public function Index(){
        $TagNav = 'search';
        //轮播广告
        $AdTourIndex = NewsGetAdInfo('tourindex');
        
        $SqlWhere ='';
        $ZucheRecommendModule = new ZucheRecommendModule();
        $TourStrokeModule = new TourStrokeModule();
        $TourAreaModule = new TourAreaModule ();
        $TourProductLineModule = new TourProductLineModule();
        $TourProductImageModule = new TourProductImageModule();
        $TourAttractionsModule = new TourAttractionsModule();
        
        //导航下快捷地区
        $Offset = 0;
        $CityCategoryWhere1 = '  and CityCategory = 1 and R1 = 1 order by S1 DESC';//东部城市
        $CityCategoryWhere2 = '  and CityCategory = 2 and R1 = 1 order by S1 DESC';//西部城市
        $CityEast = $TourAreaModule->GetLists($CityCategoryWhere1,$Offset, 6);
        $CityWest = $TourAreaModule->GetLists($CityCategoryWhere2,$Offset, 6);
        foreach ($CityEast as $key => $value){
            $AreaIDWhere = 'and R2 = 1 and AreaID='.$value['AreaID'].' order by S2 DESC';
            $Attractioninfo = $TourAttractionsModule->GetLists($AreaIDWhere, $Offset, 4);
            $CityEast[$key]['Attraction'] = $Attractioninfo;
        
        }
        foreach ($CityWest as $key => $value){
            $AreaIDWhere = 'and R2 = 1 and AreaID='.$value['AreaID'].' order by S2 DESC';
            $Attractioninfo =$TourAttractionsModule->GetLists($AreaIDWhere, $Offset, 4);
            $CityWest[$key]['Attraction'] = $Attractioninfo;
            
        }
        //导航下热门景点
        $AttractionsWhere = 'and R1 = 1 order by S1 DESC';
        $AttractionsList = $TourAttractionsModule->GetLists($AttractionsWhere,0,30);
        //行程推荐
        $StrokeWhere1 = ' and R1 = 1 and CityCategory = 1 order by S1 DESC';
        $StrokeList1 = $TourStrokeModule->GetLists($StrokeWhere1,$Offset,15);
        $StrokeWhere2 = ' and R1 = 1 and CityCategory = 2 order by S1 DESC';
        $StrokeList2 = $TourStrokeModule->GetLists($StrokeWhere2,$Offset,15);
        
        //租车
        $SqlWhere = ' and R1 = 1 order by S1 DESC';
        $CarRent['Data'] = $ZucheRecommendModule->GetList($SqlWhere);
        foreach ($CarRent['Data'] as $key => $value) {
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['AreaID']);
            $CarRent['Data'][$key]['City'] = $TourAreaInfo['CnName'];
        }
        //当地参团
        $localWhere = 'and R2 = 1 and Category = 12 order by S2 DESC';
        $local['Data'] = $TourProductLineModule-> GetLists($localWhere,$Offset,6);
        foreach ($local['Data'] as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['Departure']);
            $local['Data'][$key]['ImageUrl'] =$ImageInfo['ImageUrl'];
            $local['Data'][$key]['CnName'] = $TourAreaInfo['CnName'];
            $local['Data'][$key]['key'] = $key;
        }
        $domesticWhere = 'and R2 = 1 and Category = 4 order by S2 DESC';
        //国内出发
        $domestic ['Data'] = $TourProductLineModule-> GetLists($domesticWhere,$Offset,6);
        foreach ($domestic['Data'] as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['Departure']);
            $domestic['Data'][$key]['ImageUrl'] =$ImageInfo['ImageUrl'];
            $domestic['Data'][$key]['CnName'] = $TourAreaInfo['CnName'];
            $domestic['Data'][$key]['key'] = $key;
        }
        //东部城市特色改成特色体验
        $EastWhere  ='and R1 = 1 and Category = 6 order by S1 DESC';
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $east['Data'] = $TourProductPlayBaseModule-> GetLists($EastWhere,$Offset,7);
        foreach ($east['Data'] as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['City']);
            $east['Data'][$key]['ImageUrl'] =$ImageInfo['ImageUrl'];
            $east['Data'][$key]['CnName'] = $TourAreaInfo['CnName'];
            $east['Data'][$key]['key'] = $key;
        }
        //西部城市特色体验改成一日游
        $westWhere  ='and R1 = 1 and Category = 9  order by S1 DESC';
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $west['Data'] = $TourProductPlayBaseModule-> GetLists($westWhere,$Offset,7);
        foreach ($west['Data'] as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['City']);
            $west['Data'][$key]['ImageUrl'] =$ImageInfo['ImageUrl'];
            $west['Data'][$key]['CnName'] = $TourAreaInfo['CnName'];
            $west['Data'][$key]['key'] = $key;
        }
        //酒店
        //景点门票
        $AttractionsWhere  ='and R1 = 1 and Category = 8  order by S1 DESC';
        $Attractions['Data'] = $TourProductPlayBaseModule-> GetLists($AttractionsWhere,$Offset,7);
        foreach ($Attractions['Data'] as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['City']);
            $Attractions['Data'][$key]['ImageUrl'] =$ImageInfo['ImageUrl'];
            $Attractions['Data'][$key]['CnName'] = $TourAreaInfo['CnName'];
            $Attractions['Data'][$key]['key'] = $key;
        }
        //城市通票
        $CityPassWhere  ='and R1 = 1 and Category = 7 order by S1 DESC';
        $CityPass['Data'] = $TourProductPlayBaseModule-> GetLists($CityPassWhere,$Offset,7);
        foreach ($CityPass['Data'] as $key => $value) {
            $ImageInfo = $TourProductImageModule->GetInfoByTourProductID($value['TourProductID']);
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($value['City']);
            $CityPass['Data'][$key]['ImageUrl'] =$ImageInfo['ImageUrl'];
            $CityPass['Data'][$key]['CnName'] = $TourAreaInfo['CnName'];
            $CityPass['Data'][$key]['key'] = $key;
        }
        
        //友情链接

        $TblLinksModule = new TblLinksModule();
        $TblLinks = $TblLinksModule->GetInfoByWhere(' and Type=2 order by Sort DESC',true);

        $Title = '美国旅游_美国旅行_美国自由行_美国自驾游-57美国旅游服务预订平台';
        $Keywords = '美国旅游,美国旅行,旅游攻略,美国自由行,美国跟团游,美国自驾游,美国游,美国旅游网';
        $Description = '57美国网旅游平台，为您提供美国跟团游、自由行、行程定制、景点门票、租车、境外wifi、签证等全方位的美国旅游在线预订服务。了解美国旅游攻略，规划美国旅游行程，预订美国旅游线路，尽在57美国网！';
        include template('Index');
    }
    /**
     * @author lushaobo
     * @abstract 更新列表篩選地區選擇項
     * @url http://tour.57us.com/tour/doareainfo/
     *      
     */
    public function DoAreaInfo() {
        $TourAreaModule = new TourAreaModule();
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        //更新国内参团出发地推荐
        $TourProductLineLists = $TourProductLineModule->GetInfoByWhere(' and Category=4 and Status=1 and IsClose=0',true);
        $Departure = '';
        foreach ($TourProductLineLists as $Key=>$Value)
        {
            if (!strstr($Departure, $Value['Departure']))
            {
                $Departure .= ','.$Value['Departure'];
            }
        }
        $Departure = substr($Departure, 1);
        $UpdateAreaInfo['R3'] = 1;
        $TourAreaModule->UpdateInfoByWhere($UpdateAreaInfo,'AreaID in('.$Departure.')');
        unset($UpdateAreaInfo,$Departure,$TourProductLineLists);
        //更新当地参团出发地、目的地推荐
        $TourProductLineLists = $TourProductLineModule->GetInfoByWhere(' and Category=12 and Status=1 and IsClose=0',true);
        $Departure = '';
        $Destination = '';
        foreach ($TourProductLineLists as $Key=>$Value)
        {
            if (!strstr($Departure, $Value['Departure']))
            {
                $Departure .= ','.$Value['Departure'];
            }
            if (!strstr($Destination, $Value['Destination']))
            {
                $Destination .= ','.$Value['Destination'];
            }
        }
        $Departure = substr($Departure, 1);
        $Destination = substr($Destination, 1);
        $UpdateAreaInfo['R4'] = 1;
        $TourAreaModule->UpdateInfoByWhere($UpdateAreaInfo,'AreaID in('.$Departure.')');
        unset($UpdateAreaInfo,$Departure);
        $UpdateAreaInfo['R8'] = 1;
        $TourAreaModule->UpdateInfoByWhere($UpdateAreaInfo,'AreaID in('.$Destination.')');
        unset($UpdateAreaInfo,$Departure,$TourProductLineLists);
        
        //更新特色体验目的地推荐
        $TourProductPlayBaseLists = $TourProductPlayBaseModule->GetInfoByWhere(' and Category=6 and Status=1 and IsClose=0',true);
        $City = '';
        foreach ($TourProductPlayBaseLists as $Key=>$Value)
        {
            if (!strstr($City, $Value['City']))
            {
                $City .= ','.$Value['City'];
            }
        }
        $City = substr($City, 1);
        $UpdateAreaInfo['R6'] = 1;
        $TourAreaModule->UpdateInfoByWhere($UpdateAreaInfo,'AreaID in('.$City.')');
        unset($TourProductPlayBaseLists,$UpdateAreaInfo,$City);
        //更新一日游目的地推荐
        $TourProductPlayBaseLists = $TourProductPlayBaseModule->GetInfoByWhere(' and Category=9 and Status=1 and IsClose=0',true);
        $City = '';
        foreach ($TourProductPlayBaseLists as $Key=>$Value)
        {
            if (!strstr($City, $Value['City']))
            {
                $City .= ','.$Value['City'];
            }
        }
        $City = substr($City, 1);
        $UpdateAreaInfo['R5'] = 1;
        $TourAreaModule->UpdateInfoByWhere($UpdateAreaInfo,'AreaID in('.$City.')');
        unset($UpdateAreaInfo,$City,$TourProductPlayBaseLists);
        //更新当地参团目的地推荐
        $TourProductPlayBaseLists = $TourProductPlayBaseModule->GetInfoByWhere(' and (Category=7 or Category=8) and Status=1 and IsClose=0',true);
        $City = '';
        foreach ($TourProductPlayBaseLists as $Key=>$Value)
        {
            if (!strstr($City, $Value['City']))
            {
                $City .= ','.$Value['City'];
            }
        }
        $City = substr($City, 1);
        $UpdateAreaInfo['R7'] = 1;
        $TourAreaModule->UpdateInfoByWhere($UpdateAreaInfo,'AreaID in('.$City.')');
        unset($UpdateAreaInfo,$City,$TourProductPlayBaseLists);
        exit('更新完成');
    }
    /**
     * 生成列表静态
     * @abstract bob
     * @URL http://tour.57us.com/tour/dojson/
     * 
     * 以下为更新地区ID的语句
     * update tour_area set AreaID = AreaID + 1000;
     * update tour_area set ParentID = ParentID + 1000 where ParentID>0;
     * update tour_attractions set AreaID = AreaID + 1000;
     * update tour_product_line set Departure = Departure + 1000;
     * update tour_product_play_base set City = City + 1000;
     */
    public function DoJson() {
        $TourAreaModule = new TourAreaModule();
        $TourSpecialSubjectModule = new TourSpecialSubjectModule();
        $TourPassAttractionsModule = new TourPassAttractionsModule();
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        /*国内参团*/
        //出发地
        $Area = $TourAreaModule->GetInfoByWhere(' and R3=1 order by S3 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Home/AreaOut.json',$AreaString );
        unset($Area,$Key,$Value,$AreaJson,$AreaString);
        //跟团游特色主题
        $SpecialSubject = $TourSpecialSubjectModule->GetList(' and Category=1 order by Sort ASC');
        foreach ($SpecialSubject as $Key=>$Value)
        {
            $SpecialSubjectJson[$Key]['id'] = $Value['TourSpecialSubjectID'];
            $SpecialSubjectJson[$Key]['name'] = $Value['SpecialSubjectName'];
        }
        $TourSpecialSubjectString = json_encode($SpecialSubjectJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Home/Subject.json',$TourSpecialSubjectString );
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Local/Subject.json',$TourSpecialSubjectString );
        unset($SpecialSubject,$Key,$Value,$SpecialSubjectJson,$TourSpecialSubjectString);
        //行程天数
        $StrokeDays[0]=array('name'=>'5-7天','date'=>'5-7');
        $StrokeDays[1]=array('name'=>'8-10天','date'=>'8-10');
        $StrokeDays[2]=array('name'=>'11-13天','date'=>'11-13');
        $StrokeDays[3]=array('name'=>'14-16天','date'=>'14-16');
        $StrokeDays[4]=array('name'=>'16天以上','date'=>'16-All');
        $StrokeDaysString = json_encode($StrokeDays,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Home/Days.json',$StrokeDaysString );
        unset($StrokeDays,$StrokeDaysString);
        //跟团游途径城市热门
        $PassAttractions = $TourPassAttractionsModule->GetInfoByWhere(' and R1=1 order by S1 DESC',true);
        foreach ($PassAttractions as $Key=>$Value)
        {
            $PassAttractionsJson[$Key]['name'] = $Value['PassAttractionsName'];
        }
        $PassAttractionsString = json_encode($PassAttractionsJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Home/HotCitys.json',$PassAttractionsString );
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Local/HotCitys.json',$PassAttractionsString );
        unset($PassAttractions,$Key,$Value,$PassAttractionsJson,$PassAttractionsString);
        //跟团游途径城市所有
        $ZiMu = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        foreach ($ZiMu as $V)
        {
            $PassAttractions = $TourPassAttractionsModule->GetInfoByWhere(' and FirstLetter=\''.$V.'\' order by S1 ASC',true);
            foreach ($PassAttractions as $Key=>$Value)
            {
                $PassAttractionsJson[$Key]['name'] = $Value['PassAttractionsName'];
            }
            $PassAttractionsString = json_encode($PassAttractionsJson,JSON_UNESCAPED_UNICODE);
            file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Home/Citys/'.$V.'.json',$PassAttractionsString );
            file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Local/Citys/'.$V.'.json',$PassAttractionsString );
            unset($PassAttractions,$Key,$Value,$PassAttractionsJson,$PassAttractionsString);
        }
        unset($ZiMu,$V);
        /*当地参团*/
        //出发城市
        $Area = $TourAreaModule->GetInfoByWhere(' and R4=1 order by S4 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Local/AreaOut.json',$AreaString );
        unset($Area,$Key,$Value,$AreaJson,$AreaString);
        //结束城市
        $Area = $TourAreaModule->GetInfoByWhere(' and R8=1 order by S8 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Local/AreaEnter.json',$AreaString );
        unset($Area,$Key,$Value,$AreaJson,$AreaString);
        //行程天数
        $StrokeDays[0]=array('name'=>'1天内','date'=>'0-1');
        $StrokeDays[1]=array('name'=>'2-3天','date'=>'2-3');
        $StrokeDays[2]=array('name'=>'4-6天','date'=>'4-6');
        $StrokeDays[3]=array('name'=>'7-9天','date'=>'7-9');
        $StrokeDays[4]=array('name'=>'10-15天','date'=>'10-15');
        $StrokeDays[5]=array('name'=>'15天以上','date'=>'15-All');
        $StrokeDaysString = json_encode($StrokeDays,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Local/Days.json',$StrokeDaysString );
        unset($StrokeDays,$StrokeDaysString);
        
        /*一日游*/
        //出发地
        $Area = $TourAreaModule->GetInfoByWhere(' and R5=1 order by S5 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Daily/AreaOut.json',$AreaString);
        unset($Area,$Key,$Value,$AreaJson,$AreaString);
        //特色主题
        $SpecialSubject = $TourSpecialSubjectModule->GetInfoByWhere(' and Category=2 order by Sort DESC',true);
        foreach ($SpecialSubject as $Key=>$Value)
        {
            $SpecialSubjectJson[$Key]['id'] = $Value['TourSpecialSubjectID'];
            $SpecialSubjectJson[$Key]['name'] = $Value['SpecialSubjectName'];
        }
        $TourSpecialSubjectString = json_encode($SpecialSubjectJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Daily/Subject.json',$TourSpecialSubjectString);
        unset($SpecialSubject,$Key,$Value,$SpecialSubjectJson,$TourSpecialSubjectString);
        
        /*特色体验*/
        //目的地
        $Area = $TourAreaModule->GetInfoByWhere(' and R6=1 order by S6 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Feature/AreaEnter.json',$AreaString );
        unset($Area,$Key,$Value,$AreaJson,$AreaString);
        //特色主题
        $SpecialSubject = $TourSpecialSubjectModule->GetInfoByWhere(' and Category=4 order by Sort ASC',true);
        foreach ($SpecialSubject as $Key=>$Value)
        {
            $SpecialSubjectJson[$Key]['id'] = $Value['TourSpecialSubjectID'];
            $SpecialSubjectJson[$Key]['name'] = $Value['SpecialSubjectName'];
        }
        $TourSpecialSubjectString = json_encode($SpecialSubjectJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Feature/Subject.json',$TourSpecialSubjectString );
        unset($SpecialSubject,$Key,$Value,$SpecialSubjectJson,$TourSpecialSubjectString);
        
        /*票务*/
        //目的地
        $Area = $TourAreaModule->GetInfoByWhere(' and R7=1 order by S7 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Ticket/AreaEnter.json',$AreaString );
        unset($Area,$Key,$Value,$AreaJson,$AreaString);
        //特色主题
        $SpecialSubject = $TourSpecialSubjectModule->GetInfoByWhere(' and Category=3 order by Sort ASC',true);
        foreach ($SpecialSubject as $Key=>$Value)
        {
            $SpecialSubjectJson[$Key]['id'] = $Value['TourSpecialSubjectID'];
            $SpecialSubjectJson[$Key]['name'] = $Value['SpecialSubjectName'];
        }
        $TourSpecialSubjectString = json_encode($SpecialSubjectJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Ticket/Subject.json',$TourSpecialSubjectString );
        unset($SpecialSubject,$Key,$Value,$SpecialSubjectJson,$TourSpecialSubjectString);
        //价格
        $Price[0]=array('name'=>'200元以下','date'=>'0-200');
        $Price[1]=array('name'=>'200-500元','date'=>'200-500');
        $Price[3]=array('name'=>'500-800元','date'=>'500-800');
        $Price[4]=array('name'=>'800元以上','date'=>'800-All');
        $PriceString = json_encode($Price,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Ticket/Prices.json',$PriceString );
        unset($Price,$PriceString);
        //类型
        $Type[0]=array('name'=>'景区门票');
        $Type[1]=array('name'=>'城市通票');
        $TypeString = json_encode($Type,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Ticket/Types.json',$TypeString );
        unset($Type,$TypeString);
        //Wifi服务类型
        $SpecialSubject = $TourSpecialSubjectModule->GetInfoByWhere(' and Category=5 order by Sort ASC',true);
        foreach ($SpecialSubject as $Key=>$Value)
        {
            $SpecialSubjectJson[$Key]['id'] = $Value['TourSpecialSubjectID'];
            $SpecialSubjectJson[$Key]['name'] = $Value['SpecialSubjectName'];
        }
        $TourSpecialSubjectString = json_encode($SpecialSubjectJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/WiFi/Types.json',$TourSpecialSubjectString );
        unset($SpecialSubject,$Key,$Value,$SpecialSubjectJson,$TourSpecialSubjectString);
        //Wifi目的地
        $Area = $TourAreaModule->GetInfoByWhere(' and R9=1 order by S9 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/WiFi/AreaEnter.json',$AreaString );
        //接送机出发地
        $Area = $TourAreaModule->GetInfoByWhere(' and R10=1 order by S10 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Shuttle/AreaOut.json',$AreaString );
        unset($Area,$Key,$Value,$AreaJson,$AreaString);
        //接送机目的地
        $Area = $TourAreaModule->GetInfoByWhere(' and R10=1 order by S10 DESC',true);
        foreach ($Area as $Key=>$Value)
        {
            $AreaJson[$Key]['name'] = $Value['CnName'];
            $AreaJson[$Key]['AeraID'] = $Value['AreaID'];
        }
        $AreaString = json_encode($AreaJson,JSON_UNESCAPED_UNICODE);
        file_put_contents(SYSTEM_ROOTPATH.'/Templates/Tour/data/Shuttle/AreaEnter.json',$AreaString );
        unset($Area,$Key,$Value,$AreaJson,$AreaString);
		exit('生成成功');
    }

	//高端定制首页
	public function HighLevelIndex(){
		include template ("HighLevelIndex");
	}
	//高端定制填写页
	public function HighLevelDetail(){
	    $Title = '定制旅游_美国定制旅游_美国定制游_美国私人定制旅游_美国高端定制旅游 - 57美国网';
	    $Keywords = '定制旅游,旅游定制,美国定制旅游,美国定制游,美国私人定制旅游,美国高端定制旅游,美国定制旅游,美国个性定制旅游';
	    $Description = '57美国网高端定制频道，为您量身定制专属行程，拥有中文私人导游，您的每一个要求,我们都帮您超值实现，让您的美国之旅独一无二。';
		include template ("HighLevelDetail");
	}

    public function HighOrderPay(){
    	if ( $_GET['NO']) {
    	$OrderNo = $_GET['NO'];
		$TourPrivateOrderModule = new TourPrivateOrderModule();
			if ($_GET['a'] == '57us') {
				$Data['Money'] = '0.01';
				$TourPrivateOrderModule->UpdateByOrderNum($Data, $OrderNo);
			}
            $OrderInfo = $TourPrivateOrderModule->GetInfoByWhere('and `OrderNo`= \''.$OrderNo.'\'');
			$endCity =str_replace(',','-',$OrderInfo['EndCity']);
			$City = substr($endCity,0,strlen($endCity)-1);
			//定义超时支付时间
			$CreateTime= explode(" ",$OrderInfo['CreateTime']);
			$hours =  explode(":",$CreateTime[1]);
			$H = $hours[0]+12;
			$M = $hours[1];
			$S = $hours[2];
    	}
		include template ( "HighOrderPay" );
	}

    //=====================生成地区景点json静态文件=======================//

    public function Tourjson()
    {
        $a = $_GET['a'];
        if ($a == '57us..') {
            $TourAreaModule = new TourAreaModule ();
            $sqlwhere = ' and ParentID = 1005';
            $sqlwhere .= ' and R2 = 1';
            $TourAreaList = $TourAreaModule->GetInfoByWhere($sqlwhere,true);
            $number1 = '';
            $number2 = '';
            $number3 = '';

            foreach ($TourAreaList as $key => $value) {
                if ($value['CityCategory'] == 1) {
                    $value['EnName'] =str_replace(' ', '', $value['EnName']);
                    $value['Image'] ='http://images.57us.com/p4'.$value['Image'];
                    if($value['EnName'] ==''){
                        $value['EnName'] =null;
                    }
                    if($value['Description'] ==''){
                        $value['Description'] =null;
                    }
                    if($value['Image'] ==''){
                        $value['Image'] =null;
                    }
                    $myfile = fopen(SYSTEM_ROOTPATH . "/Templates/Tour/Customize/city/East.json", "w+") or die("Unable to open file!");
                    $arr = array('CnName' => $value['CnName'], 'AreaID' => $value['AreaID'],'EnName' => $value['EnName'],'img' => $value['Image'], 'Description' => $value['Description']);
                    $number1 .= json_encode($arr, JSON_UNESCAPED_UNICODE).',';$number1 = stripslashes($number1);
                    $left = '[';
                    $right = ']';
                    $result = $left.$number1.$right;$result =str_replace('},]','}]',$result);
                    fwrite($myfile, $result);
                    fclose($myfile);
                } elseif ($value['CityCategory'] == 2) {
                    $value['EnName'] =str_replace(' ', '', $value['EnName']);
                    $value['Image'] ='http://images.57us.com/p4'.$value['Image'];
                    if($value['EnName'] ==''){
                        $value['EnName'] =null;
                    }
                    if($value['Description'] ==''){
                        $value['Description'] =null;
                    }
                    if($value['Image'] ==''){
                        $value['Image'] =null;
                    }
                    $myfile = fopen(SYSTEM_ROOTPATH . "/Templates/Tour/Customize/city/West.json", "w+") or die("Unable to open file!");
                    $arr = array('CnName' => $value['CnName'], 'AreaID' => $value['AreaID'],'EnName' => $value['EnName'],'img' => $value['Image'], 'Description' => $value['Description']);
                    $number2 .= json_encode($arr, JSON_UNESCAPED_UNICODE).',';$number2 = stripslashes($number2);
                    $left = '[';
                    $right = ']';
                    $result = $left.$number2.$right;$result =str_replace('},]','}]',$result);
                    fwrite($myfile, $result);
                    fclose($myfile);
                } else {
                    $value['EnName'] =str_replace(' ', '', $value['EnName']);
                    $value['Image'] ='http://images.57us.com/p4'.$value['Image'];
                    if($value['EnName'] ==''){
                        $value['EnName'] =null;
                    }
                    if($value['Description'] ==''){
                        $value['Description'] =null;
                    }
                    if($value['Image'] ==''){
                        $value['Image'] =null;
                    }
                    $myfile = fopen(SYSTEM_ROOTPATH . "/Templates/Tour/Customize/city/Other.json", "w+") or die("Unable to open file!");
                    $arr = array('CnName' => $value['CnName'], 'AreaID' => $value['AreaID'], 'EnName' => $value['EnName'],'img' => $value['Image'], 'Description' => $value['Description']);
                    $number3 .= json_encode($arr, JSON_UNESCAPED_UNICODE).',';$number3 = stripslashes($number3);
                    $left = '[';
                    $right = ']';
                    $result = $left.$number3.$right;$result =str_replace('},]','}]',$result);
                    fwrite($myfile, $result);
                    fclose($myfile);
                }
            }
        }
        //获取所有城市数据
        if ($a =='57us...'){
            $TourAreaModule = new TourAreaModule ();
            $sqlwhere = ' and ParentID = 5';
            $sqlwhere = ' and R2 = 1 ';
            $TourAreaList = $TourAreaModule->GetInfoByWhere($sqlwhere,true);
            $number1 = '';
            foreach ($TourAreaList as $key =>$value){
                $myfile = fopen(SYSTEM_ROOTPATH."/Templates/Tour/Customize/city/all.json", "w+") or die("Unable to open file!");
                $value['Image'] ='http://images.57us.com/p4'.$value['Image'];
                $arr = array('CnName' => $value['CnName'], 'AreaID' => $value['AreaID'], 'EnName' => $value['EnName'],'img' => $value['Image'], 'Description' => $value['Description']);

                $number1 .= json_encode($arr,JSON_UNESCAPED_UNICODE).',';

                $number1 = stripslashes($number1);

                $left = '[';
                $right = ']';
                $result = $left.$number1.$right;$result =str_replace('},]','}]',$result);
                fwrite($myfile, $result);
                fclose($myfile);
            }
        }
        if ($a == '57us.') {
            $TourAttractionsModule = new TourAttractionsModule();
            $TourAreaModule = new TourAreaModule ();
            $sqlwhere = ' and ParentID = 5';
            $sqlwhere = ' and R2 = 1 ';
            $TourArea = $TourAreaModule->GetInfoByWhere($sqlwhere,true);
            foreach ($TourArea as $key =>$value){
                $AreaID =str_replace(' ', '', $value['AreaID']);
                $myfile = fopen(SYSTEM_ROOTPATH."/Templates/Tour/Customize/data/".$AreaID.".json", "w+") or die("Unable to open file!");
                $arr =array();
                $attraWhere = ' and AreaID = '.$value['AreaID'];
                $attraWhere .= ' and R3 = 1';
                $Attractions = $TourAttractionsModule->GetInfoByWhere($attraWhere,true);
                $number ='';
                foreach ($Attractions as $k => $val){
                    $val['EnName'] =str_replace(' ', '', $val['EnName']);
                    $val['Image'] ='http://images.57us.com/p4'.$val['Image'];
                    if($val['EnName'] ==''){
                        $val['EnName'] =null;
                    }
                    if($val['Description'] ==''){
                        $val['Description'] =null;
                    }
                    $arr = array("AttractionsName"=>$val['AttractionsName'],"enName"=>$val['EnName'],"img" => $val['Image'],"Description"=>$val['Description']);
                    $number .= json_encode($arr,JSON_UNESCAPED_UNICODE).',';
                }
                $number = stripslashes($number);$number = substr($number, 0, -1);
                $left = '[';
                $right = ']';
                $result = $left.$number.$right;
                fwrite($myfile, $result);
            }
            fclose($myfile);
        }
        if ($a == '57us') {
            $TourCustomFeaturesModule = new TourCustomFeaturesModule();
            $lists = $TourCustomFeaturesModule->GetLists();
            foreach ($lists as $key=>$value){
                $myfile = fopen(SYSTEM_ROOTPATH."/Templates/Tour/Customize/custom/custom.json", "w+") or die("Unable to open file!");
                $arr =array();
                $value['Image'] ='http://images.57us.com/p4'.$value['Image'];
                $arr = array('CustomName'=>$value['CustomName'],'EnName'=>$value['EnName'],'img' => $value['Image'],'Description'=>$value['Description']);
                $number .= json_encode($arr,JSON_UNESCAPED_UNICODE).',';
            }
            $number  =  substr($number, 0, -1);
            $number = stripslashes($number);
            $left = '[';
            $right = ']';
            $result = $left.$number.$right;$result =str_replace('},]','}]',$result);
            fwrite($myfile, $result);
            fclose($myfile);
        }
    }

    //=====================生成地区景点json静态文件=======================//  

    //---------------------------------------------------------------数据整理-------------------------------------------/////
    /**
     * @desc  处理数据/当地玩乐
     * @desc
     */
    public function HandleData(){
        $BaseModule = new TourProductPlayBaseModule();
        $Info = $BaseModule->GetInfoByWhere(" and !Month and UpdateDataStatus=0 limit 50",true);
        $ErverDayPriceModule = new TourProductPlayErverDayPriceModule();
        if($Info){
            foreach ($Info as $key=>$val){
                $PriceList = $ErverDayPriceModule->GetInfoByWhere(' and TourProductID = '.$val['TourProductID'],true);
                $Month = '';
                foreach($PriceList as $k1=>$v1){
                    $Nowtime = date('Ym',time());
                    $Gotime =  substr($v1['Date'] , 0 , 6);
                    if(strpos($Month,$Gotime) === false){     //使用绝对等于
                        //不包含
                        if($Gotime<$Nowtime){
                            continue;
                        }else{
                            $Month .= $Gotime.',';
                        }
                    }else{
                        //包含
                        continue;
                    }
                }
                $Data['Month'] = substr($Month, 0, - 1);
                $Data['UpdateDataStatus'] = 1;
                $BaseModule->UpdateInfoByKeyID($Data,$val['TourProductPlayID']);
            }
        }
        $NewInfo = $BaseModule->GetInfoByWhere(" and !Month and UpdateDataStatus=0 limit 50",true);
        if($NewInfo){
            echo "操作成功";
            echo "<script>setTimeout(function(){window.location.reload();},1000);location.reload();</script>";
        }
        else{
            echo "结束";exit;
        }
    }

    /**
     * @desc 处理数据/跟团游
     * @desc 把跟团游的产品全部下架,海鸥产品上架ID,纵横产品上架ID
     */
    public function HandleDataOne(){
        $IDs = array(66679,66106,66105,66102,66101,66099,66179,66094,66181,66677,66180,66096,66095,66091,66090,66089,66088,66087,66085,66084,66083,66082,66081,63577,66079,63617,63616,63615,63613,63612,63611,63610,63609,63608,63607,63606,63605,63575,63604,63603,63602,63601,63600,63599,63598,63597,63596,63595,63594,63593,63592,63576,63691,63590,63589,63588,63587,63586,63585,63584,63583,63582,63579,63581,63580,63578,67097,67096,67093,67092,67067,67015,66685,66684,66683,66682,66681,66680,66183,66163,66162,66164,66160,66159,66157,66156,66155,66147,66146,66145,66144,66143,66142,66141,66140,66138,66137,66136,66134,66135,66132,66131,66130,66678,66629,66630,66632,66639,66635,66637,66947,66627,66624,66623,66620,66619,66618,66617,66622,66616,66615,66607,66596,66595,66598,66597,66602,66603,66605,66577,66576,66575,66574,66573,66572,66571,66570,66569,66568,66567,66638,66566,66565,66564,66563,66562,66561,66560,66559,66558,66557,66556,66555,66554,66549,66849,66548,66541,66540,66536,66535,66518,66517,66515,66514,66513,66512,66511,66510,66509,66505,66504,66503,66502,66500,66499,66497,66492,66491,66490,66489,66488,66487,66485,66480,66479,66478,66477,66468,66453,66447,66428,66415,66414,66947,66637,66635,66639,66632,66630,66629,66627,66624,66623,66620,66619,66618,66617,66622,66616,66615,66607,66596,66595,66598,66597,66602,66603,66605,66577,66576,66575,66574,66573,66572,66571,66570);
        $i= 0;
        include SYSTEM_ROOTPATH.'/Modules/Tour/Class.TourProductLineModule.php';
        $LineModule = new TourProductLineModule();

        foreach($IDs as $key=>$val){
            $result2 = $LineModule->UpdateInfoByourProductID(array('Status'=>1,'IsClose'=>0),$val);
            if($result2){
                $i++;
            }
        }
        echo $i;exit;

        $result1 = $LineModule->UpdateInfoByWhere(array('Status'=>0),'1=1');
        foreach($IDs as $val){
            $Info = $LineModule->GetInfoByWhere(' and Status=1 and IsClose = 0 and TourProductID = '.$val);
            if($Info){
                $i++;
            }
        }
        echo $i;exit;
        echo count($IDs);EXIT;
        // 开启事务
        global $DB;
        $DB->query("BEGIN"); // 开始事务定义
        $result1 = $LineModule->UpdateInfoByWhere(array('Status'=>0),'1=1');
        if($result1){
            foreach($IDs as $key=>$val){
                $result2 = $LineModule->UpdateInfoByourProductID(array('Status'=>1,'IsClose'=>0),$val);
                if(!$result2){
                    $DB->query("ROLLBACK"); // 判断当执行失败时回滚
                }
            }
        }else{
            $DB->query("ROLLBACK"); // 判断当执行失败时回滚
            echo "更新失败";
        }
        if($DB->query("COMMIT")){
            echo "更新完成";exit;
        }
        else{
            echo "更新失败";exit;
        }
    }
    /**
     * @desc 首页全站搜索
     */
    public function Search(){
        $TagNav = 'search';
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourAreaModule = new TourAreaModule();
        $TourProductImageModule = new TourProductImageModule();
        $HotelBaseModule = new HotelBaseInfoModule();
        $HotelAmenityModule = new HotelAmenityModule();
        $HotelCountryUsModule = new HotelCountryUsModule();
        $VisaProducModule = new VisaProducModule ();
        $SearchKeyWords = $_GET['K'];
        if ($SearchKeyWords != '') {
            $SoWhere = '?K=' . $SearchKeyWords;
        }
        if ($SearchKeyWords==''){
            alertandgotopage('请输入您要搜索的内容！',WEB_STUDY_URL);
        }
        $HomeWhere  = " and Category=4 and `Status`=1 and IsClose!=1 and (ProductName like '%$SearchKeyWords%' or ProductSimpleName like '%$SearchKeyWords%')";//搜索国内参团
        $LocalWhere = " and Category=12 and `Status`=1 and IsClose!=1 and (ProductName like '%$SearchKeyWords%' or ProductSimpleName like '%$SearchKeyWords%')"; //搜索当地参团
        $FeatureWhere = " and Category=6 and `Status`=1 and IsClose!=1 and (ProductName like '%$SearchKeyWords%' or ProductSimpleName like '%$SearchKeyWords%')"; //搜索特色体验
        $DailyWhere = " and Category=9 and `Status`=1 and IsClose!=1 and (ProductName like '%$SearchKeyWords%' or ProductSimpleName like '%$SearchKeyWords%')"; //搜索一日游
        $TicketWhere = " and Category=8 and `Status`=1 and IsClose!=1 and (ProductName like '%$SearchKeyWords%' or ProductSimpleName like '%$SearchKeyWords%')";//搜索门票
        $HotelWhere = " and `Status`=1 and `LowPrice`>0 and `Image`!='' and (`Name` like '%$SearchKeyWords%' or `Name_Cn` like '%$SearchKeyWords%')";//搜索酒店
        $VisaWhere = " and `Status` = 1  and Title like '%$SearchKeyWords%'";;//搜索签证
        $HomeRscount = $TourProductLineModule->GetListsNum($HomeWhere);
        $LocalRscount = $TourProductLineModule->GetListsNum($LocalWhere);
        $FeatureRscount = $TourProductPlayBaseModule->GetListsNum($FeatureWhere);
        $DailyRscount = $TourProductPlayBaseModule->GetListsNum($DailyWhere);
        $TicketRscount = $TourProductPlayBaseModule->GetListsNum($TicketWhere);
        $HotelRscount = $HotelBaseModule->GetListsNum($HotelWhere);
        $VisaRscount = $VisaProducModule->GetListsNum($VisaWhere);
        if (!$HomeRscount['Num'] && !$LocalRscount['Num'] && !$FeatureRscount['Num'] && !$DailyRscount['Num'] && !$TicketRscount['Num'] && !$HotelRscount['Num'] && !$VisaRscount['Num']) {
            $Count = 1;//   判断所有类别搜索都为0
        }
        //搜索国内参团列表
        if ($HomeRscount['Num']){
            $HomeList = $TourProductLineModule->GetLists($HomeWhere,0,3);
        }else{
            $HomeList = $TourProductLineModule->GetInfoByWhere(' and Category=4 and `Status`=1 and IsClose!=1 ORDER BY RAND() LIMIT 0,3',true);
        }
        foreach ($HomeList as $Key => $Value) {
            $HomeList[$Key]['Tour_name'] = $Value['ProductName'];
            $HomeList[$Key]['TourID'] = $Value['TourProductID'];
            // 出发城市
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
            $HomeList[$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
            $HomeList[$Key]['TouStroke'] = $Value['Days'];
            $HomeList[$Key]['TourPicre'] = ceil($Value['LowPrice']);
            $HomeList[$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
            if ($HomeList[$Key]['TourCostPrice'] == 0) {
                $HomeList[$Key]['TourCostPrice'] = ceil($HomeList[$Key]['TourPicre'] * 1.15);
            }
            $HomeList[$Key]['TourRecommend'] = $Value['R3'];
            // 图片
            $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
            $HomeList[$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
            $HomeList[$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
            $TagArr = explode(',', $Value['TagInfo']);
            $TagHtml = '';
            if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                foreach ($TagArr as $list) {
                    $TagHtml .= "<span>$list</span>";
                }
            }
            $HomeList[$Key]['TourService'] = $TagHtml;
            foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                $HomeList[$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
            }
        }

        //搜索当地参团列表
        if ($LocalRscount['Num']){
            $LocalList = $TourProductLineModule->GetLists($LocalWhere,0,3);
        }else{
            $LocalList = $TourProductLineModule->GetInfoByWhere(' and Category=12 and `Status`=1 and IsClose!=1 ORDER BY RAND() LIMIT 0,3',true);
        }
        foreach ($LocalList as $Key => $Value) {
            $LocalList[$Key]['Tour_name'] = $Value['ProductName'];
            $LocalList[$Key]['TourID'] = $Value['TourProductID'];
            // 出发城市
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Departure']);
            $LocalList[$Key]['TourStartCity'] = $TourAreaInfo['CnName'];
            // 结束城市
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['Destination']);
            $LocalList[$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
            $LocalList[$Key]['TouStroke'] = $Value['Days'];
            $LocalList[$Key]['TourPicre'] = ceil($Value['LowPrice']);
            $LocalList[$Key]['TourCostPrice'] = ceil($Value['LowMarketPrice']);
            if ($LocalList[$Key]['TourCostPrice'] == 0) {
                $LocalList[$Key]['TourCostPrice'] = ceil($LocalList[$Key]['TourPicre'] * 1.15);
            }
            $LocalList[$Key]['TourRecommend'] = $Value['R3'];
            // 图片
            $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
            $LocalList[$Key]['TourImg'] = $TourImagesInfo['ImageUrl'] ? ImageURLP4 . $TourImagesInfo['ImageUrl'] : '';
            $LocalList[$Key]['TourUrl'] = '/group/' . $Value['TourProductID'] . '.html';
            $TagArr = explode(',', $Value['TagInfo']);
            $TagHtml = '';
            if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                foreach ($TagArr as $list) {
                    $TagHtml .= "<span>$list</span>";
                }
            }
            $LocalList[$Key]['TourService'] = $TagHtml;
            foreach (explode("\r\n", $Value['ProductSimpleName']) as $val) {
                $LocalList[$Key]['TourDepict'] .= "<p><b>●</b>{$val}</p>";
            }
        }
        //搜索特色体验列表
        if ($FeatureRscount['Num']){
            $FeatureList = $TourProductPlayBaseModule->GetLists($FeatureWhere,0,3);
        }else{
            $FeatureList = $TourProductPlayBaseModule->GetInfoByWhere(' and Category=6  and `Status`=1 and IsClose!=1 ORDER BY RAND() LIMIT 0,3',true);
        }
        foreach ($FeatureList as $Key => $Value) {
            $FeatureList[$Key]['TourPicre'] = ceil($Value['LowPrice']);
            $FeatureList[$Key]['TourCostPrice'] = $Value['LowMarketPrice'] ? ceil($Value['LowMarketPrice']) : ceil($Value['LowPrice'] * 1.15);
            // 出发城市
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
            $FeatureList[$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
            // 图片
            $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
            $FeatureList[$Key]['TourImg'] = ImageURLP4 . $TourImagesInfo['ImageUrl'];
            unset($TourImagesInfo);
            $FeatureList[$Key]['Tour_name'] = $Value['ProductName'];
            $FeatureList[$Key]['TourID'] = $Value['TourProductID'];
            $FeatureList[$Key]['TouDate'] = $Value['Times'] ? $Value['Times'] : '1天';
            $FeatureList[$Key]['TourRecommend'] = $Value['R3'] ? $Value['R3'] : '0';
            $FeatureList[$Key]['TourUrl'] = WEB_TOUR_URL . '/play/' . $Value['TourProductID'] . '.html';
        }
        //搜索一日游列表
        if ($DailyRscount['Num']){
            $DailyList = $TourProductPlayBaseModule->GetLists($DailyWhere,0,3);
        }else{
            $DailyList = $TourProductPlayBaseModule->GetInfoByWhere(' and Category=9 and `Status`=1 and IsClose!=1 ORDER BY RAND() LIMIT 0,3',true);
        }
        foreach ($DailyList as $Key => $Value) {
            $DailyList[$Key]['TourPicre'] = ceil($Value['LowPrice']);
            $DailyList[$Key]['TourCostPrice'] = $Value['LowMarketPrice'] ? ceil($Value['LowMarketPrice']) : ceil($Value['LowPrice'] * 1.15);
            // 出发城市
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
            $DailyList[$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
            // 图片
            $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
            $DailyList[$Key]['TourImg'] = ImageURLP4 . $TourImagesInfo['ImageUrl'];
            unset($TourImagesInfo);
            $DailyList[$Key]['Tour_name'] = $Value['ProductName'];
            $DailyList[$Key]['TourID'] = $Value['TourProductID'];
            $DailyList[$Key]['TouDate'] = $Value['Times'] ? $Value['Times'] : '1天';
            $DailyList[$Key]['TourRecommend'] = $Value['R3'] ? $Value['R3'] : '0';
            $DailyList[$Key]['TourUrl'] = WEB_TOUR_URL . '/play/' . $Value['TourProductID'] . '.html';
        }
        //搜索门票列表
        if ($TicketRscount['Num']){
            $TicketList = $TourProductPlayBaseModule->GetLists($TicketWhere,0,3);
        }else{
            $TicketList = $TourProductPlayBaseModule->GetInfoByWhere(' and Category=8 and `Status`=1 and IsClose!=1 ORDER BY RAND() LIMIT 0,3',true);
        }
        foreach ($TicketList as $Key => $Value) {
            $TicketList[$Key]['TourPicre'] = ceil($Value['LowPrice']);
            $TicketList[$Key]['TourCostPrice'] = $Value['LowMarketPrice'] ? ceil($Value['LowMarketPrice']) : ceil($Value['LowPrice'] * 1.15);
            // 出发城市
            $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
            $TicketList[$Key]['TourEndCity'] = $TourAreaInfo['CnName'];
            // 图片
            $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
            $TicketList[$Key]['TourImg'] = ImageURLP4 . $TourImagesInfo['ImageUrl'];
            unset($TourImagesInfo);
            $TicketList[$Key]['Tour_name'] = $Value['ProductName'];
            $TicketList[$Key]['TourID'] = $Value['TourProductID'];
            $TicketList[$Key]['TouDate'] = $Value['Times'] ? $Value['Times'] : '1天';
            $TicketList[$Key]['TourRecommend'] = $Value['R3'] ? $Value['R3'] : '0';
            $TicketList[$Key]['TourUrl'] = WEB_TOUR_URL . '/play/' . $Value['TourProductID'] . '.html';
        }
        //搜索酒店列表
        if ($HotelRscount['Num']){
            $HotelList = $HotelBaseModule->GetLists($HotelWhere,0,3);
        }else{
            $HotelList = $HotelBaseModule->GetInfoByWhere(" and `LowPrice`>0 and `Status`=1 and `Image`!='' ORDER BY RAND() LIMIT 0,3",true);
        }
        foreach ($HotelList as $Key => $Value ) {
           $CountryUs = $HotelCountryUsModule->GetInfoByWhere(' AND CityCode = '.$Value['CityCode']);
            $HotelList[$Key]['CityCame_Cn'] = $CountryUs['CityCame_Cn'];
           $HotelList[$Key]['TagArray'] = explode ( ',', $Value ['Tag'] );
            $String = '';
            for($s=0;$s<$Value['StarRating'];$s++){
                $String .= '<i></i>';
            }
           $HotelList[$Key]['Star'] = $String;
           $HotelList[$Key]['Amenitys'] = $HotelAmenityModule->GetAmenityById($Value['HotelID']);
        }
        //搜索签证列表
        if ($VisaRscount['Num']){
            $VisaList = $VisaProducModule->GetLists($VisaWhere,0,3);
        }else{
            $VisaList = $VisaProducModule->GetInfoByWhere(' and `Status` = 1 ORDER BY RAND() LIMIT 0,3',true);
        }
        include SYSTEM_ROOTPATH . '/Controller/Visa/VisaFunction.php';
        foreach ($VisaList as $Key => $Value ) {
           $VisaList[$Key]['Area'] = SearchNameArray($Value['Area']);
           $VisaList[$Key]['Type'] = SearchTypeArray($Value['Type']);
           $VisaList[$Key]['TagArray'] = explode ( ',', $Value ['Tag'] );
        }
        //右侧广告
        $TourSearchADLists=NewsGetAdInfo('tour_search_right');
        include template('TourSearch');
    }
}
