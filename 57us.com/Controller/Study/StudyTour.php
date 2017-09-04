<?php
class StudyTour {
    public function __construct() {
    }
    /**
     * @desc 游学首页
     */
    public function Index(){
        $TagNav = 'studytour';
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        $MysqlWhere = ' and Status = 1 ';
        $SearchKeyWords =  $_GET['K'];
        if ($SearchKeyWords !=''){
            $MysqlWhere .= " and Title like '%$SearchKeyWords%'";
        }
        $page = intval($_GET['p']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 12;
        $Data = array();
        $Rscount = $StudyYoosureModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $StudyYoosureModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $Key=>$Value){
                $ApplyTime = json_decode($Value['ApplyTime'],true);
                $Data['Data'][$Key]['ApplyTime'] = $ApplyTime[0];//报名截止时间
                $Data['Data'][$Key]['OriginalPrice'] = intval($Value['OriginalPrice']);
                $Data['Data'][$Key]['Price'] = intval($Value['Price']);
                $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$Value['YoosureID'].' and IsDefault = 1');
                if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                    $Data['Data'][$Key]['Image'] = LImageURL.$YoosureImage['Image'];
                }else{
                    $Data['Data'][$Key]['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize, 3);
            $ShowPage = $ClassPage->showpage();
        }else{//搜索无产品推荐产品
            $Data['Data'] =  $StudyYoosureModule->GetInfoByWhere('and Status = 1 LIMIT 6',true);
            foreach ($Data['Data'] as $Key=>$Value){
                $ApplyTime = json_decode($Value['ApplyTime'],true);
                $Data['Data'][$Key]['ApplyTime'] = $ApplyTime[0];//报名截止时间
                $Data['Data'][$Key]['OriginalPrice'] = intval($Value['OriginalPrice']);
                $Data['Data'][$Key]['Price'] = intval($Value['Price']);
                $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$Value['YoosureID'].' and IsDefault = 1');
                if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                    $Data['Data'][$Key]['Image'] = LImageURL.$YoosureImage['Image'];
                }else{
                    $Data['Data'][$Key]['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
            }
        }
        $Title='美国游学_出国游学_游学夏令营_美国游学夏令营- 57美国网';
        $Keywords='美国游学网,美国游学,出国游学,游学夏令营,美国游学夏令营,美国游学费用,美国游学攻略,国际游学,暑期游学夏令营,游学夏令营,亲子游学夏令营,国际游学夏令营,海外游学夏令营,美国冬夏令营,美国冬令营';
        $Description='57美国网游学频道，为您提供美国游学多条线路，丰富的游学类别，更有达人定制的独一无二的游学体验，一睹名校风采，近距离体验美国人文文化。';
        //头部轮播广告
        $StudyTourIndex = NewsGetAdInfo('study_tour_index');
        include template('StudyTourIndex');
    }
    /**
     * @desc 游学产品详情页
     */
    public function Detail(){
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        $ID = $_GET['ID'];
        $StudyYoosure = $StudyYoosureModule->GetInfoByKeyID($ID);
        if ($StudyYoosure==false){
            alertandback('该商品不存在了！');
        }
        //添加浏览记录
        $Type=3;
        MemberService::AddBrowsingHistory($ID,$Type);
        $StudyYoosure['ApplyTime'] = json_decode($StudyYoosure['ApplyTime'],true);//报名截止时间
        $StudyYoosure['GoDate'] = json_decode($StudyYoosure['GoDate'],true);//出发时间
        //产品图片
        $StudyYoosureImage =$StudyYoosureImageModule->GetInfoByWhere(' and YoosureID= '.$ID,true);
        foreach ($StudyYoosureImage as $key=>$value){
            if (strpos($value['Image'],"http://")===false && $value['Image']) {
                $StudyYoosureImage[$key]['Image'] = LImageURL . $value['Image'];
            }else{
                $StudyYoosureImage[0]['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
            }
        }
        

        //游学主题
        $YoosureTitle = $StudyYoosureModule->YoosureTitle;
        //出发地
        $DeparturePlace = $StudyYoosureModule->DeparturePlace;
        //适合人群
        $Crowd = $StudyYoosureModule->Crowd;
        //行程特色
        $StudyYoosure['TravelPlan'] = preg_replace ( "/<(\/?span.*?)>/si", "",stripcslashes($StudyYoosure['TravelPlan']));
        //行程安排
        $StudyYoosure['Content'] = json_decode($StudyYoosure['Content'],true);
        foreach ($StudyYoosure['Content'] as $K => $Val) {
            $NewContent['Content'][$K] = StrReplaceImages($Val);
            $NewContent['Images'][$K] = _GetPicToContent($NewContent['Content'][$K]['Content']);
            $NewContent['Content'][$K] = _DelPicToContent($NewContent['Content'][$K]);
            $PicString = "";
            if (! empty($NewContent['Images'][$K])) {
                $PicString = '<div class="ins_img">';
                foreach ($NewContent['Images'][$K] as $Pk => $PVal) {
                    $PicString .= '
                    <p><img src="' . $PVal . '" alt="' . $StudyYoosure['Title'] .$K.$Pk. '" title="' . $StudyYoosure['Title'] .$K.$Pk. '"></p>
                    ';
                }
                $PicString .= '</div>';
            }
            $PicString = str_replace("http://images.57us.com/l", ImageURLP6, $PicString);
            $NewContent['ImagesArray'][$K] .= $PicString;
        }
        
        
        //费用说明
        $StudyYoosure['CostDescription'] = json_decode(stripcslashes($StudyYoosure['CostDescription']),true);
        //预定须知
        $StudyYoosure['BookingNotice'] = stripcslashes($StudyYoosure['BookingNotice']);
        //注意事项
        $StudyYoosure['Notice'] = stripcslashes($StudyYoosure['Notice']);
        //增加点击量
        $StudyYoosureModule->UpdateViewCount($ID);
        
        
        //相关推荐
        $StudyRelated = $StudyYoosureModule->GetInfoByWhere(' order by ViewCount DESC limit 8',true);
        foreach ($StudyRelated as $key=>$value){
            if (strpos($value['Image'],"http://")===false && $value['Image']){
                $StudyRelated[$key]['Image'] = LImageURL.$value['Image'];
            }else{
                $StudyRelated[$key]['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
            }
        }
        $Title="{$StudyYoosure['Title']} -57美国网";
        $Keywords="{$StudyYoosure['SeoKeywords']}";
        $Description=mb_substr($StudyYoosure['Description'], 0,30,'utf-8')."了解美国游学攻略，规划美国游学行程，预订美国游学线路，尽在57美国网！";
        //ViewCount
        include template('StudyTourDetail');
    }

    /**
     * @desc 游学订单填写页
     */
    public function PlaceOrder(){
        $StudyYoosureModule = new StudyYoosureModule();
        $ID = $_GET['id'];
        $StudyYoosure = $StudyYoosureModule->GetInfoByKeyID($ID);
        $DeparturePlace = $StudyYoosureModule->DeparturePlace;
        include template('StudyTourOrder');
    }
}