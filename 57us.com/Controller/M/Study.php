<?php

class Study
{

    public function __construct() {
        
    }

    /**
     * @desc  留学手机站_主页
     */
    public function Index(){
        $Nav = 'Index';
        $ConsultantInfoModule = new StudyConsultantInfoModule();
        $MemberUserInfoModule = new MemberUserInfoModule();
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        //首页广告位
        $AdInfo = NewsGetAdInfo('m_study_index_banner');
        //首页优秀顾问
        $RecommendConsultant = $ConsultantInfoModule->GetConInfoByWhere(' and MIndexRecommend = 1 limit  3',array('a.UserID','a.Tags','a.WorkingAge','b.NickName','b.Avatar','b.City'));
        //首页游学推荐
        $StudyYoosureLists = $StudyYoosureModule->GetInfoByWhere(' and MIndexRecommend = 1 limit  4',true);
        foreach ($StudyYoosureLists as  $key=>$Value){
            $StudyYoosureImage =$StudyYoosureImageModule->GetInfoByWhere(' and YoosureID= '.$Value['YoosureID'],true);
            foreach ($StudyYoosureImage as $val){
                if (strpos($val['Image'],"http://")===false && $val['Image']) {
                    $StudyYoosureLists[$key]['Image'] = LImageURL . $val['Image'];
                }else{
                    $StudyYoosureLists[$key]['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
            }
        }
        $Title='美国留学_美国游学_美国高中留学_美国留学中介_美国留学申请 - 57美国留学服务平台';
        $Keywords='美国留学中介,美国留学费用,美国留学条件,美国留学签证,美国研究生留学,美国留学,美国游学,美国高中留学,美国本科留学,美国留学申请,高中生美国留学,美国留学网,美国留学机构,美国留学攻略,美国留学资讯,美国大学排名,美国留学考试,出国留学,留学中介,留学网';
        $Description='57美国网留学平台，专注美国留学申请及考试培训，聚集了美国留学申请（高中、本科、硕士）、美国游学、美国留学签证办理、美国留学考试培训、美国大学排名等全方位的留学信息及资深留学顾问在线指导及服务。';
        include template('StudyIndex');
    }

    /**
     * @desc  留学顾问列表
     */
    public function ConsultantList(){
        $Nav = 'ConsultantList';
        $Title='留学服务_留学服务中介_留学服务项目_留学服务指南- 57美国网';
        $Keywords='留学服务,留学服务中介,留学服务项目,留学服务指南';
        $Description='57美国网留学服务频道，聚集由资深美国留学顾问提供的全套留学服务项目，包括：留学全程申请、签证办理、定校方案、文书服务、行前指导、境外服务等在线咨询及预订服务。';
        include template('StudyConsultantList');
    }

    /**
     * @desc  顾问详情页
     */
    public function ConsultantDetail(){
        $ConsultantID = $_GET['ID'];
        $ConsultantInfoModule = new StudyConsultantInfoModule();
        $ConsultantInfo = $ConsultantInfoModule->GetConInfoByWhere(' and a.UserID = '.$ConsultantID);
        if($ConsultantInfo){
            $ConsultantInfo = current($ConsultantInfo);
        }
        else{
            echo "该顾问不存在";exit;
        }
        //判断是否需要隐藏超出字数部分
        $Count = mb_strlen($ConsultantInfo['Introduction'],'UTF8');
        if($Count>60){
            $IsShow = 1;
        }
        $Title="{$ConsultantInfo['NickName']}美国留学顾问 - 57美国网";
        $Keywords="{$ConsultantInfo['NickName']} - 美国留学顾问";
        $Description="57美国网留学顾问—{$ConsultantInfo['NickName']}，".mb_substr($ConsultantInfo['Introduction'], 0,100,'utf-8').'…';

        include template('StudyConsultantDetail');
    }

    /**
     * @desc  匹配顾问条件页1
     */
    public function MarryConsultantOne(){
        $Title="匹配顾问- 57美国网";
        $Keywords="美国留学匹配顾问";
        $Description="57美国网留学顾问—匹配顾问";
        include template('StudyMarryConsultantOne');
    }

    /**
     * @desc 匹配顾问条件页2
     */
    public function MarryConsultantTwo(){
        $Title="匹配顾问列表 - 57美国网";
        $Keywords="美国留学匹配顾问列表";
        $Description="57美国网留学顾问—匹配顾问列表";
        $MarryID = intval($_GET['MarryID']);
        include template('StudyMarryConsultantTwo');
    }

    /**
     * @desc 匹配顾问条件页3
     */
    public function MarryConsultantThree(){
        $Title="匹配顾问列表 - 57美国网";
        $Keywords="美国留学匹配顾问列表";
        $Description="57美国网留学顾问—匹配顾问列表";
        $ConsultantID = $_GET['ConsultantID'];
        $UserInfoModule = new MemberUserInfoModule();
        $Info = $UserInfoModule->GetInfoByUserID($ConsultantID);
        include template('StudyMarryConsultantThree');
    }

    /**
     * @desc  匹配顾问条件页4
     */
    public function MarryConsultantFour(){
        $Title="匹配顾问列表 - 57美国网";
        $Keywords="美国留学匹配顾问列表";
        $Description="57美国网留学顾问—匹配顾问列表";
        $MarryID = $_GET['MarryID'];
        $ConsultantID = $_GET['ConsultantID'];
        $UserInfoModule = new MemberUserInfoModule();
        $Info = $UserInfoModule->GetInfoByUserID($ConsultantID);
        include template('StudyMarryConsultantFour');
    }

    /**
     * @desc  免费评估页
     */
    public function Estimate(){
        $Title="留学评估_出国留学评估_美国留学评估_美国留学免费评估- 57美国网";
        $Keywords="留学评估,出国留学评估,美国留学评估,美国留学免费评估,在线留学评估,留学评估系统";
        $Description="57美国网留学评估系统，专业顾问24小时在线，为您提供专业的留学评估服务， 服务包含：定制留学方案、奖学金申请评估、留学签证、文书写作等留学相关问题咨询。";
        include template('StudyEstimate');
    }

    /**
     * @desc  获取地区
     */
    public function GetCity(){
        $MemberUserInfoModule = new MemberUserInfoModule();
        $DataCity = $MemberUserInfoModule->RemoveDuplicate('City',2);
        $result_json = array('ResultCode'=>200,'DataCity'=>$DataCity);
        echo json_encode($result_json,JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc  获取申请层次
     */
    public function GetTargetLevel(){
        $ServiceModule = new StudyConsultantServiceModule();
        $TargetLevel = $ServiceModule->TargetLevel;
        $Result = array();
        foreach($TargetLevel as $key => $val){
            $Result[] = array('ID'=>$key,'Name'=>$val);
        }
        echo json_encode($Result,JSON_UNESCAPED_UNICODE);
    }

    /**
     * @desc  获取申请层次
     */
    public function GetServiceType(){
        $ServiceModule = new StudyConsultantServiceModule();
        $ServiceType = $ServiceModule->ServiceType;
        $Result = array();
        foreach($ServiceType as $key => $val){
            $Result[] = array('ID'=>$key,'Name'=>$val);
        }
        echo json_encode($Result,JSON_UNESCAPED_UNICODE);
    }
    /**
     * @desc  游学列表
     */
    public function StudyTourList(){
        $Nav = 'StudyTourList';
        include template('StudyTourList');
    }
    /**
     * @desc  游学详情页
     */
    public function StudyTourDetail(){
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        $ID = $_GET['ID'];

        $StudyYoosure = $StudyYoosureModule->GetInfoByKeyID($ID);
        $StudyYoosure['ApplyTime'] = json_decode($StudyYoosure['ApplyTime'],true);//报名截止时间
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
      //  echo "<pre>";print_r($StudyYoosure['Content']);
        $Title =$StudyYoosure['Title'];
        foreach ($StudyYoosure['Content'] as $K => $Val) {
            $NewContent['Content'][$K] = StrReplaceImages($Val);
            $NewContent['Images'][$K] = _GetPicToContent($NewContent['Content'][$K]['Content']);
            $NewContent['Content'][$K] = _DelPicToContent($NewContent['Content'][$K]);
            $NewContent['Content'][$K] = preg_replace ( "/<(\/?p.*?)>/si", "", $NewContent['Content'][$K]);
            $NewContent['Content'][$K] = str_replace ( '&nbsp;', '',$NewContent['Content'][$K]);
            $PicString = "";
            if (! empty($NewContent['Images'][$K])) {
                foreach ($NewContent['Images'][$K] as $Pk => $PVal) {
                    $PicString .= '<div class="col-50"><img src="' . $PVal . '" alt="' . $StudyYoosure['Title'] .$K.$Pk. '" title="' . $StudyYoosure['Title'] .$K.$Pk. '" width="100%"/></div>';
                }
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
        include template('StudyTourDetail');
    }
    /**
     * @desc  游学订单选择出发时间
     */
    public function ChoiceDate(){
        $StudyYoosureModule = new StudyYoosureModule();
        $ID = $_GET['id'];
        $StudyYoosure = $StudyYoosureModule->GetInfoByKeyID($ID);
        $StudyYoosure['GoDate'] = json_decode($StudyYoosure['GoDate'],true);//出发时间
        include template('StudyTourChoiceDate');
    }
    /**
     * @desc  游学订单填写页
     */
    public function PlaceOrder(){
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        $ID = $_GET['id'];
        $Num = $_GET['n'];
        for($i = 0; $i <$Num; $i++){
            $Data[] = $i;
        }
        $StudyYoosure = $StudyYoosureModule->GetInfoByKeyID($ID);
        $StudyYoosureImage =$StudyYoosureImageModule->GetInfoByWhere(' and YoosureID= '.$ID,true);
        foreach ($StudyYoosureImage as $key=>$value){
            if (strpos($value['Image'],"http://")===false && $value['Image']) {
                $StudyYoosure['Image'] = LImageURL . $value['Image'];
            }else{
                $StudyYoosure['Image'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
            }
        }
        include template('StudyTourOrder');
    }
    /**
     * @desc  游学订单选择支付页
     */
    public function ChoicePay(){
        $Title = '订单支付';
        $OrderNumber = $_GET['OrderNumber'];
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $OrderInfo = $StudyYoosureOrderModule->GetInfoByWhere("and OrderNum='$OrderNumber'");
        if($OrderInfo){//选择游学订单支付页面
            $TplName="StudyTourChoicePay";
            include template('StudyTourChoicePay');
        }else{
            alertandgotopage("不存在该订单", WEB_STUDY_URL);
        }
    }

}
