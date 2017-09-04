<?php

/**
 * @desc  会员中心
 * Class Member
 */
class Member
{
    public function __construct()
    {
        $this->Nav = 'Member';
    }

    /**
     * @desc 登录验证
     */
    private function IsLogin()
    {
        if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
            $this->Login();
            exit;
        }
    }

    /**
     * @desc 会员中心首页(我的主页)
     */
    public function Index()
    {
        MemberService::IsLogin();
        $MemberUserModule = new MemberUserModule();
        $MemberUserInfoModule = new MemberUserInfoModule();
        $MemberUserBankModule = new MemberUserBankModule();
        $MemberCollectionModule = new MemberCollectionModule();
        //会员基本信息
        $User = $MemberUserModule->GetUserByID($_SESSION['UserID']);
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        //会员金额
        $UserBank = $MemberUserBankModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
        //账户安全度
        $SafeLevel = 1;
        if ($User['E-Mail'] != '') {
            $SafeLevel += 1;
        }
        if ($User['Mobile'] != '') {
            $SafeLevel += 1;
        }
        //最新收藏
        $CollectionList = $MemberCollectionModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID'].' order by AddTime desc LIMIT 0,3',true);
        if ($CollectionList)
            foreach ($CollectionList as $key=>$value){
                $Collection =  $this->GetCollect($value['Category'],$value['RelevanceID']);//获取收藏信息
                $CollectionList[$key]['Type'] =$Collection['Type'];
                $CollectionList[$key]['ProductName'] = $Collection['ProductName'];
                $CollectionList[$key]['ImageUrl'] = $Collection['ImageUrl'];
                $CollectionList[$key]['Url'] =  $Collection['Url'];
            }
            //浏览记录
        $MemberBrowsingHistoryModule = new MemberBrowsingHistoryModule();
        $BrowsingHistoryList = $MemberBrowsingHistoryModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID'].' order by AddTime desc LIMIT 0,6',true);

        if ($BrowsingHistoryList)
            foreach ($BrowsingHistoryList as $key=>$value){
                $BrowsingHistory =  $this->GetCollect($value['Category'],$value['RelevanceID']);//获取浏览信息
                $BrowsingHistoryList[$key]['Type'] =$BrowsingHistory['Type'];
                $BrowsingHistoryList[$key]['ProductName'] = $BrowsingHistory['ProductName'];
                $BrowsingHistoryList[$key]['ImageUrl'] = $BrowsingHistory['ImageUrl'];
                $BrowsingHistoryList[$key]['Url'] =  $BrowsingHistory['Url'];
            }
        //浏览记录
        $UserNav ='Index';
        //旅游订单总数量
        $MysqlWhere = ' and UserID= '.$_SESSION['UserID'];
        $TourProductOrderModule = new TourProductOrderModule();
        $ZucheOrderModule = new ZucheOrderModule();
        $HotelOrderModule = new HotelOrderModule();
        $VisaOrderModule = new VisaOrderModule();
        $TourPrivateOrderModule = new TourPrivateOrderModule();
        $TourProductOrderRscount = $TourProductOrderModule->GetListsNum($MysqlWhere);
        $ZucheOrderRscount = $ZucheOrderModule->GetListsNum($MysqlWhere);
        $HotelOrderRscount = $HotelOrderModule->GetListsNum($MysqlWhere);
        $VisaOrderRscount = $VisaOrderModule->GetListsNum($MysqlWhere);
        $TourPrivateOrderRscount = $TourPrivateOrderModule->GetListsNum($MysqlWhere);
        $TourCount =$TourProductOrderRscount['Num']+$ZucheOrderRscount['Num']+$HotelOrderRscount['Num']+$VisaOrderRscount['Num']+$TourPrivateOrderRscount['Num'];
        //留学订单总数量
        $StudyOrderModule = new StudyOrderModule();
        $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
        $StudyOrderRscount = $StudyOrderModule->GetListsNum($MysqlWhere);
        $StudyYoosureRscount = $StudyYoosureOrderModule->GetListsNum($MysqlWhere);
        $StudyCount =$StudyOrderRscount['Num']+$StudyYoosureRscount['Num'];
        include template('MemberIndex');
    }

    /**
     * @desc 我的资料
     */
    public function Information()
    {
        MemberService::IsLogin();
        $UserNav ='Information';
        $MemberUserInfoModule = new MemberUserInfoModule();
        $MemberUserModule = new MemberUserModule();
        $User = $MemberUserModule->GetUserByID($_SESSION['UserID']);
        $User['E-Mail'] = strlen($User['E-Mail']) ? substr_replace($User['E-Mail'], '****', 1, strpos($User['E-Mail'], '@') - 2) : '';
        $User['Mobile'] = strlen($User['Mobile']) ? substr_replace($User['Mobile'], '****', 3, 4) : '';
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $arr['Mobile'] =$User['Mobile'];
        $arr['E-Mail'] =$User['E-Mail'];
        $arr['NickName'] =$UserInfo['NickName'];
        $arr['RealName'] =$UserInfo['RealName'];
        $arr['Sex'] =$UserInfo['Sex'];
        $arr['Occupation'] =$UserInfo['Occupation'];
        $arr['BirthDay'] =$UserInfo['BirthDay'];
        $arr['Address'] =$UserInfo['Address'];
        $Completion =round((count($arr)/8*100)).'%';
        include template('MemberInformation');
    }

    /**
     * @desc 站内消息
     */
    public function MessageList()
    {
        MemberService::IsLogin();
        $UserNav ='MessageList';
        unset($_SESSION['IsHaveMessage']);
        $MemberMessageInfoModule = new MemberMessageInfoModule();
        $MemberMessageSendModule = new MemberMessageSendModule();

        $MysqlWhere = ' and Status in (1,2) and UserID = '.$_SESSION['UserID'].' order by SendID desc';
        $Page = intval($_GET['p']) < 1 ? 1 : intval($_GET['p']); // 页码 可能是空
        $PageSize = 5;
        $Rscount = $MemberMessageSendModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount']){
                $Page = $Data ['PageCount'];
            }
            $Data ['Data'] = $MemberMessageSendModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data ['Data'] as $key=>$val){
                $MessageInfo = $MemberMessageInfoModule->GetInfoByKeyID($val['MessageID']);
                $Data ['Data'][$key]['Title'] =$MessageInfo['Title'];
                $Data ['Data'][$key]['Content'] = $MessageInfo['Content'];
                $Data ['Data'][$key]['SendType'] = $MessageInfo['SendType'];
                $Data ['Data'][$key]['AddTime'] =  date("Y-m-d",$MessageInfo['AddTime']);
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }
        //echo "<pre>";print_r($Data);exit;
        $Title = '会员中心_站内消息 - 57美国网';
        include template('MemberMessageList');
    }

    //===========================================安全中心=====================================================//
    /**
     * @desc 账户安全
     */
    public function AccountSafety()
    {
        MemberService::IsLogin();
        $UserNav ='AccountSafety';
        $Title = '会员中心_安全中心 - 57美国网';

        $MemberUserModule = new MemberUserModule();
        $User = $MemberUserModule->GetUserByID($_SESSION['UserID']);
        $User['E-Mail'] = strlen($User['E-Mail']) ? substr_replace($User['E-Mail'], '****', 1, strpos($User['E-Mail'], '@') - 2) : '';
        $User['Mobile'] = strlen($User['Mobile']) ? substr_replace($User['Mobile'], '****', 3, 4) : '';
        $SafeLevel = 1;
        if ($User['E-Mail'] != '') {
            $SafeLevel += 1;
        }
        if ($User['Mobile'] != '') {
            $SafeLevel += 1;
        }
        $_SESSION['SafeLevel'] = $SafeLevel;
        $MemberUserInfoModule = new MemberUserInfoModule();
        $UserInfo = $MemberUserInfoModule->GetUserInfo($_SESSION['UserID']);
        include template("MemberAccountSafety");
    }

    /**
     * @desc 修改密码
     */
    public function ChangePassword()
    {
        MemberService::IsLogin();
        $UserNav ='AccountSafety';
        include template("MemberChangePassword");
    }

    /**
     * @desc 更换/绑定手机
     */
    public function ChangeMobile()
    {
        MemberService::IsLogin();
        $UserNav ='AccountSafety';
        $Do = $_GET['do'];
        $UserID = $_SESSION['UserID'];
        $UserModule = new MemberUserModule();
        $UserInfo = $UserModule->GetInfoByKeyID($UserID);
        $UserInfo['Mobile'] = strlen($UserInfo['Mobile']) ? substr_replace($UserInfo['Mobile'], '****', 3, 4) : '';
        include template("MemberChangeMobile");
    }

    /**
     * @desc 更换/绑定邮箱
     */
    public function ChangeMail()
    {
        MemberService::IsLogin();
        $UserNav ='AccountSafety';
        $Do = $_GET['do'];
        $UserID = $_SESSION['UserID'];
        $UserModule = new MemberUserModule();
        $UserInfo = $UserModule->GetInfoByKeyID($UserID);
        $UserInfo['E-Mail'] = strlen($UserInfo['E-Mail']) ? substr_replace($UserInfo['E-Mail'], '****', 1, strpos($UserInfo['E-Mail'], '@') - 2) : '';
        include template("MemberChangeMail");
    }

    //===========================================安全中心结束=====================================================//

    /**
     * @desc 我的收藏
     */
    public function MyCollect ()
    {
        MemberService::IsLogin();
        $UserNav ='MyCollect';
        $MemberCollectionModule = new MemberCollectionModule();
        $MysqlWhere ='';
        $Type = $_GET['t'];
        if ($Type!=''){
            $Category = $this->GetCollectCategory($Type);//获取收藏类别
            $MysqlWhere .=  ' and `Category` IN ('.$Category.')';
        }
        $MysqlWhere .= ' and UserID = '.$_SESSION['UserID'].' order by AddTime desc';
        $Page = intval($_GET['Page']);
        $Page = intval($_GET['p']) < 1 ? 1 : intval($_GET['p']); // 页码 可能是空
        $PageSize = 9;
        $Rscount = $MemberCollectionModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $MemberCollectionModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data ['Data'] as $key=>$value){
                $Collection =  $this->GetCollect($value['Category'],$value['RelevanceID']);//获取收藏信息
                $Data ['Data'][$key]['Type'] =$Collection['Type'];
                $Data ['Data'][$key]['ProductName'] = $Collection['ProductName'];
                $Data ['Data'][$key]['ImageUrl'] = $Collection['ImageUrl'];
                $Data ['Data'][$key]['Url'] =  $Collection['Url'];
            }
            $ClassPage = new Page($Rscount['Num'], $PageSize,3);
            $ShowPage = $ClassPage->showpage();
        }
        include template("MemberMyCollect");
    }
    /**
     * @dese 常用旅客列表
     */
    public function PassengerList ()
    {
        MemberService::IsLogin();
        $UserNav ='Passenger';
        $MemberPassengerModule = new MemberPassengerModule();
        $Data['Data'] = $MemberPassengerModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID'],true);
        include template("MemberPassengerList");
    }

    /**
     * @dese 新增常用旅客
     */
    public function PassengerAdd ()
    {
        MemberService::IsLogin();
        $UserNav ='Passenger';
        $MemberPassengerModule = new MemberPassengerModule();
        $ID = $_GET['ID'];
        $PassengerInfo = $MemberPassengerModule->GetInfoByKeyID($ID);
        include template("MemberPassengerAdd");
    }
    /**
     * @dese 邮寄地址列表
     */
    public function AddressList ()
    {
        MemberService::IsLogin();
        $UserNav ='Address';
        $ShippingAddressModule = new MemberShippingAddressModule();
        $Data['Data'] = $ShippingAddressModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID'],true);
        include template("MemberAddressList");
    }
    /**
     * @dese 新增邮寄地址
     */
    public function AddressAdd ()
    {
        MemberService::IsLogin();
        $UserNav ='Address';
        $ShippingAddressModule = new MemberShippingAddressModule();
        $ID = $_GET['ID'];
        $AddressInfo = $ShippingAddressModule->GetInfoByKeyID($ID);
        //电话号码
        $Tel = explode('-',$AddressInfo['Tel']);
        $AddressInfo['TelArea']=$Tel[0];
        $AddressInfo['Tel']=$Tel[1];
        $AddressInfo['TelExtension']=$Tel[2];
        include template("MemberAddressAdd");
    }
    /**
     * @dese 获取收藏类别
     */
    private function  GetCollectCategory($Type)
    {
        switch ($Type) {
            case 'service'://服务
                $Category='1';
                break;
            case 'course'://课程
                $Category='2';
                break;
            case 'yoosure'://游学
                $Category='3';
                break;
            case 'travel'://出游
                $Category='4';
                break;
            case 'hotel'://酒店
                $Category='5';
                break;
            case 'carrent'://租车,目前没有
                $Category='6';
                break;
            case 'visa'://签证
                $Category='7';
                break;
            case 'school'://院校
                $Category='8,9';
                break;
            case 'news'://资讯
                $Category='10,11,12,13';
                break;
            case 'ticket'://门票
                $Category='14';
                break;
            default:
                break;
        }
        return $Category;

    }
    /**
     * @dese 获取收藏信息和浏览记录
     */
    private function  GetCollect($Category,$RelevanceID)
    {
        switch ($Category) {
            case '1'://服务
                $StudyConsultantServiceModule = new StudyConsultantServiceModule();
                $StudyConsultantService = $StudyConsultantServiceModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='服务';
                $Collection['ProductName'] =  $StudyConsultantService['ServiceName'];
                $ImagesJson = json_decode($StudyConsultantService['ImagesJson'],true);
                $Collection['ImageUrl']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($StudyConsultantService['ImagesJson'],true)[$StudyConsultantService['CoverImageKey']]):(ImageURL.'/img/study/defaultService3.0.jpg');
                $Collection['Url'] =  WEB_STUDY_URL.'/consultant_service/'. $StudyConsultantService['ServiceID'].'.html';
                break;
            case '2'://课程
                $StudyTeacherCourseModule = new StudyTeacherCourseModule();
                $StudyTeacherCourse = $StudyTeacherCourseModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='课程';
                $Collection['ProductName'] =  $StudyTeacherCourse['CourseName'];
                $ImagesJson = json_decode($StudyTeacherCourse['ImagesJson'],true);
                $Collection['ImageUrl']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($StudyTeacherCourse['ImagesJson'],true)[$StudyTeacherCourse['CoverImageKey']]):(ImageURL.'/img/study/defaultClass3.0.jpg');
                $Collection['Url'] =  WEB_STUDY_URL.'/teacher_course/'. $StudyTeacherCourse['CourseID'].'.html';
                break;
            case '3'://游学产品
                $StudyYoosureModule = new StudyYoosureModule();
                $StudyYoosure = $StudyYoosureModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='游学';
                $Collection['ProductName'] = $StudyYoosure['Title'];
                $StudyYoosureImageModule = new StudyYoosureImageModule();
                $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$RelevanceID.' and IsDefault = 1');
                if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                    $Collection['ImageUrl'] = LImageURL.$YoosureImage['Image'];
                }else{
                    $Collection['ImageUrl'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
                $Collection['Url'] =  WEB_STUDY_URL.'/studytour/'.$StudyYoosure['YoosureID'].'.html';
                break;
            case '4'://出游 （当地参团、国内跟团、特色体验、一日游）
                $TourProductModule = new TourProductModule();
                $TourProductImageModule = new TourProductImageModule();
                $TourProduct = $TourProductModule->GetInfoByKeyID($RelevanceID);
                $TourProductImage = $TourProductImageModule->GetInfoByTourProductID($RelevanceID);
                $Collection['Type'] ='出游';
                $Collection['ProductName'] = $TourProduct['ProductName'];
                $Collection['ImageUrl'] =  ImageURLP2.$TourProductImage['ImageUrl'];
                if ($TourProduct['Category']=='4'||$TourProduct['Category']=='12'){
                    $Collection['Url'] =  WEB_TOUR_URL.'/group/'.$TourProduct['TourProductID'].'.html';
                }elseif($TourProduct['Category']=='6'||$TourProduct['Category']=='9'){
                    $Collection['Url'] =  WEB_TOUR_URL.'/play/'.$TourProduct['TourProductID'].'.html';
                }
                break;

            case '5'://酒店
                $HotelBaseInfoModule = new HotelBaseInfoModule();
                $HotelBaseInfo = $HotelBaseInfoModule->GetInfoByWhere(' and HotelID ='.$RelevanceID);
                $Collection['Type'] ='酒店';
                $Collection['ProductName'] = $HotelBaseInfo['Name_Cn'];
                $Collection['ImageUrl'] =  ImageURLP2.$HotelBaseInfo['Image'];
                $Collection['Url'] =  WEB_HOTEL_URL.'/hotel/'.$HotelBaseInfo['HotelID'].'.html';
                break;
            case '6'://租车,目前没有
                break;
            case '7'://签证
                $VisaProducModule = new VisaProducModule();
                $VisaInfo = $VisaProducModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='签证';
                $Collection['ProductName'] = $VisaInfo['Title'];
                $Collection['ImageUrl'] =  ImageURLP2.$VisaInfo['Image'];
                $Collection['Url'] =  WEB_VISA_URL.'/visadetail/'.$VisaInfo['VisaID'].'.html';
                break;
            case '8'://高中院校
                $StudyHighSchoolModule = new StudyHighSchoolModule();
                $StudyHighSchool = $StudyHighSchoolModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='学校';
                $Collection['ProductName'] = $StudyHighSchool['HighSchoolName'];
                $Collection['ImageUrl'] =  $StudyHighSchool['Icon'];
                $Collection['Url'] =  WEB_STUDY_URL.'/highschool/'.$StudyHighSchool['HighSchoolID'].'.html';
                break;
            case '9'://大学院校
                $StudyCollegeModule = new StudyCollegeModule();
                $StudyCollege = $StudyCollegeModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='学校';
                $Collection['ProductName'] = $StudyCollege['CollegeName'];
                $Collection['ImageUrl'] =  $StudyCollege['LogoUrl'];
                $Collection['Url'] =  WEB_STUDY_URL.'/college/'.$StudyCollege['CollegeID'].'.html';
                break;
            case '10'://旅游资讯
                $TblTourModule = new TblTourModule();
                $TblTour = $TblTourModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='资讯';
                $Collection['ProductName'] = $TblTour['Title'];
                $Collection['ImageUrl'] =  LImageURL.$TblTour['Image'];
                $Collection['Url'] =  WEB_MAIN_URL.'/tour/'.$TblTour['TourID'].'.html';
                break;
            case '11'://留学资讯
                $TblStudyAbroadModule = new TblStudyAbroadModule();
                $TblStudyAbroad = $TblStudyAbroadModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='资讯';
                $Collection['ProductName'] = $TblStudyAbroad['Title'];
                $Collection['ImageUrl'] =  LImageURL.$TblStudyAbroad['Image'];
                $Collection['Url'] =  WEB_MAIN_URL.'/study/'.$TblStudyAbroad['StudyID'].'.html';
                break;
            case '12'://移民资讯
                $TblImmigrationModule = new TblImmigrationModule();
                $TblImmigration = $TblImmigrationModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='资讯';
                $Collection['ProductName'] = $TblImmigration['Title'];
                $Collection['ImageUrl'] =  LImageURL.$TblImmigration['Image'];
                $Collection['Url'] =  WEB_MAIN_URL.'/immigrant/'.$TblImmigration['ImmigrationID'].'.html';
                break;
            case '13'://游记资讯
                $TblTravelsModule = new TblTravelsModule();
                $TblTravels = $TblTravelsModule->GetInfoByKeyID($RelevanceID);
                $Collection['Type'] ='资讯';
                $Collection['ProductName'] = $TblTravels['Title'];
                $Collection['ImageUrl'] =  LImageURL.$TblTravels['Image'];
                $Collection['Url'] =  WEB_MAIN_URL.'/travels/'.$TblTravels['TravelsID'].'.html';
                break;
            case '14':// 门票
                $TourProductModule = new TourProductModule();
                $TourProductImageModule = new TourProductImageModule();
                $TourProduct = $TourProductModule->GetInfoByKeyID($RelevanceID);
                $TourProductImage = $TourProductImageModule->GetInfoByTourProductID($RelevanceID);
                $Collection['Type'] ='门票';
                $Collection['ProductName'] = $TourProduct['ProductName'];
                $Collection['ImageUrl'] =  ImageURLP2.$TourProductImage['ImageUrl'];
                $Collection['Url'] =  WEB_TOUR_URL.'/play/'.$TourProduct['TourProductID'].'.html';
                break;
            default:
                break;
        }
        return $Collection;
    }
    /**
     * @desc 我的资产
     */
    public function MyProperty ()
    {
        MemberService::IsLogin();
        $UserNav ='MyProperty';
        $UserBankModule = new MemberUserBankModule();
        $UserBank = $UserBankModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
        if (!$UserBank) {
            $UserBank['UserID'] = $_SESSION['UserID'];
            $UserBank['TotalBalance'] = 0.00;
            $UserBank['FrozenBalance'] = 0.00;
            $UserBank['FreeBalance'] = 0.00;
            $UserBankModule->InsertData($UserBank);
        }
        $Title = '会员中心_我的资产 - 57美国网';
        $Nav = 'Wallet';
        include template("MemberMyProperty");
    }

    /**
     * @desc  自动创建会员
     * @param $Account 帐号
     * @param $Type    类型：1-普通会员，2-顾问，3-教师
     */
    public function AutoCreationMember(){
        $Account = $_GET['U'];
        $Type = $_GET['T']?$_GET['T']:1;
        //会员表
        $MemberUserModule = new MemberUserModule();
        //会员基础信息表
        $MemberUserInfoModule = new MemberUserInfoModule();
        //会员资金表
        $MemberUserBankModule = new MemberUserBankModule();
        $NowTime = time();

        $MemberData = array('PassWord'=>md5('admin888'),'AddTime'=>$NowTime,'State'=>1);
        if (is_numeric($Account)) {
            $IsExit = $MemberUserModule->GetInfoByWhere(' and Mobile='.$Account);
            if($IsExit){
                alert('帐号'.$Account.'已存在');
                return false;
            }
            $MemberData['Mobile'] = $Account;
            $MemberData['E-Mail'] = '';
        } elseif (strpos($Account, '@')) {
            $IsExit = $MemberUserModule->GetInfoByWhere("and `E-Mail`='".$Account."'");
            if($IsExit){
                alert('帐号'.$Account.'已存在');
                return false;
            }
            $MemberData['Mobile'] = '';
            $MemberData['E-Mail'] = $Account;
        }
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        //添加MemberUser表数据
        $UserID = $MemberUserModule->InsertInfo($MemberData);
        if(!$UserID){
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            alert('MemberUser表数据添加失败');
        }
        else{
            $UserInfoData = array(
                'UserID'=>$UserID,
                'NickName'=>'57US_'.date('i').mt_rand(100,999),
                'Avatar'=>'/img/man3.0.png',
                'Identity'=>$Type,
                'IdentityState'=>0,
            );
            //添加MemberUserInfo表数据
            $MemberUserInfoResult = $MemberUserInfoModule->InsertInfo($UserInfoData);
            if(!$MemberUserInfoResult){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                alert('MemberUserInfo表数据添加失败');
                return false;
            }
            else{
                $UserBankData = array('UserID'=>$UserID,'TotalBalance'=>0,'FrozenBalance'=>0,'FreeBalance'=>0);
                //添加MemberUserBank表数据
                $UserBankResult = $MemberUserBankModule->InsertInfo($UserBankData);
                if(!$UserBankResult){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    alert('MemberUserBank表数据添加失败');
                    return false;
                }
                else{
                    if($Type == 2){ //顾问
                        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
                        $ConsultantInfoData = array('UserID'=>$UserID,'Grade'=>1,'TutorialObject'=>0);
                        $ConsultantInfoResult = $StudyConsultantInfoModule->InsertInfo($ConsultantInfoData);
                        if(!$ConsultantInfoResult){
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            alert('StudyConsultantInfo表数据添加失败');
                            return false;
                        }
                        else{
                            $DB->query("COMMIT");//执行事务
                            alert('用户'.$Account.'添加成功');
                            return false;
                        }
                    }
                    elseif($Type == 3){ //教师
                        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
                        $StudyTeacherInfoData = array('UserID'=>$UserID,'Grade'=>1,'TutorialObject'=>0);
                        $StudyTeacherInfoResult = $StudyTeacherInfoModule->InsertInfo($StudyTeacherInfoData);
                        if(!$StudyTeacherInfoResult){
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            alert('StudyTeacherInfo表数据添加失败');
                            return false;
                        }
                        else{
                            $DB->query("COMMIT");//执行事务
                            alert('用户'.$Account.'添加成功');
                            return false;
                        }
                    }
                    else{
                        $DB->query("COMMIT");//执行事务
                        alert('用户'.$Account.'添加成功');
                        return false;
                    }
                }
            }
        }
    }


    //===========================================注册登录=====================================================//
    /**
     * @desc  登入页或登录操作
     */
    public function Login()
    {
        //如果已登陆，直接跳转到会员中心
        if (isset($_SESSION['UserID']) && !empty($_SESSION['UserID'])) {
            header('Location:' . WEB_MEMBER_URL);
        }
        $Title = '会员登录 - 57美国网';
        include template('MemberLogin');
    }

    /**
     * @desc  退出登录
     */
    public function SignOut()
    {
        unset($_SESSION);
        setcookie("UserID", '', time() - 1, "/", WEB_HOST_URL);
        setcookie("Account", '', time() - 1, "/", WEB_HOST_URL);
        setcookie("session_id", session_id(), time() - 1, "/", WEB_HOST_URL);
        session_destroy();
        header("location:" . WEB_MEMBER_URL);
    }

    /**
     * @desc  注册
     */
    public function Register()
    {
        $Title = '会员登录_注册 - 57美国网';
        include template('MemberRegister');
    }

    /**
     * @desc  找回密码
     */
    public function RetrievePassword()
    {
        $Title = '会员登录_找回密码 - 57美国网';
        include template('MemberRetrievePassword');
    }

    /**
     * @desc  教师注册
     */
    public function TeacherRegister()
    {
        if (isset($_SESSION['UserID']) && !empty($_SESSION['UserID'])) {
            header('Location:' . WEB_STUDY_URL.'/teachermanage/mycenter/');
        }
        $Title = '会员登录_教师注册 - 57美国网';
        include template('MemberTeacherRegister');
    }

    /**
     * @desc  顾问注册
     */
    public function ConsultantRegister()
    {
        if (isset($_SESSION['UserID']) && !empty($_SESSION['UserID'])) {
            header('Location:' . WEB_STUDY_URL.'/consultantmanage/mycenter/');
        }
        $Title = '会员登录_顾问注册 - 57美国网';
        include template('MemberConsultantRegister');
    }

    /**
     * @desc  转为教师身份
     */
    public function TransitionTeacher()
    {
        $Title = '转为教师身份 - 57美国网';
        include template('MemberTransitionTeacher');
    }

    /**
     * @desc  转为顾问身份
     */
    public function TransitionConsultant()
    {
        $Title = '转为顾问身份 - 57美国网';
        include template('MemberTransitionConsultant');
    }


}