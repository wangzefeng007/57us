<?php
class StudyTour {
    public function __construct() {
        IsLogin();
    }

    /**
     * @desc 添加游学列表
     */
    public function lists() {
        $StudyYoosureModule = new StudyYoosureModule();
        $SqlWhere = ' ';
        //搜索条件
        $PageUrl = '';
        $ProductName = trim ( $_GET ['ProductName'] ); //产品名称
        if ($ProductName != '') {
            $SqlWhere .= ' and concat(Title) like \'%' . $ProductName . '%\'';
            $PageUrl .= '&ProductName=' . $ProductName;
        }
        $StatusInfo = intval ( $_GET ['Status'] ); //产品状态
        if ($StatusInfo != '') {
            $SqlWhere .= ' and Status = \'' . $StatusInfo . '\'';
            $PageUrl .= '&Status=' . $StatusInfo;
        }
        //跳转到该页面
        if ($_POST ['page']) {
            $page = $_POST ['page'];
            tourl ( '/index.php?Module=StudyTour&Action=StudyTourList&Page=' . $page . $PageUrl );
        }
        //分页开始
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 20;
        $Rscount = $StudyYoosureModule->GetListsNum ( $SqlWhere );
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $StudyYoosureModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            MultiPage ( $Data, 10 );
        }
        $Status = $StudyYoosureModule->Status;
        $YoosureTitle = $StudyYoosureModule->YoosureTitle;
        $PageMax = $Data ['PageCount']; //最后一页
        if ($Page >= 1 && $Page < $PageMax) {
            $Next = $Page + 1; //上一页
        }
        if ($Page > 1 && $Page <= $PageMax) {
            $Previous = $Page - 1; //下一页
        }
        //分页结束
        include template ( 'StudyTourList' );
    }

    /**
     * @desc 添加游学
     */
    public function StudyTourAdd() {
        $TopNavs = 'StudyTourAdd';
        $StudyTourModule = new StudyYoosureModule();
        $YoosureID = intval ( $_GET ['YoosureID'] );
        if ($YoosureID > 0) {
            $ProductInfo = $StudyTourModule->GetInfoByKeyID( $YoosureID );
            $ProductInfo['TravelPlan']=StrReplaceImages($ProductInfo['TravelPlan']);
            $ProductInfo['CostDescription']=StrReplaceImages($ProductInfo['CostDescription']);
            $ProductInfo['VisaInfo']=StrReplaceImages($ProductInfo['VisaInfo']);
        }

        $ProductInfo['GoDate'] = json_decode($ProductInfo['GoDate'],true);//出发时间
        $ProductInfo['ApplyTime'] = json_decode($ProductInfo['ApplyTime'],true);//报名时间
        $ProductInfo['Content'] = json_decode($ProductInfo['Content']);//行程安排
        $ProductInfo['TravelPlan'] = trim($ProductInfo['TravelPlan']); //行程特色

        $Crowd = $StudyTourModule->Crowd;//适合人群
        $YoosureTitle = $StudyTourModule->YoosureTitle;//游学主题
        $DeparturePlace = $StudyTourModule->DeparturePlace;//出发地

        if ($_POST) {
            $YoosureID = intval ( $_POST ['YoosureID'] );
            $Data ['Title'] = trim ( $_POST ['Title'] );  //产品标题
            $Data ['SeoKeywords'] = trim ( $_POST ['SeoKeywords'] ); //SEO关键字
            $Data ['Description'] = trim ( $_POST ['Description'] ); //简介
            $POST['GoDate'] = $_POST['GoDate'];
            foreach ($POST['GoDate'] as $key=>$value){
                if ($value!=''){
                    $GoDate[$key] = $value;
                }
            }
            $POST['ApplyTime'] = $_POST['ApplyTime'];
            foreach ($POST['ApplyTime'] as $key=>$value){
                if ($value!=''){
                    $ApplyTime[$key] = $value;
                }
            }
            $Data ['GoDate'] = json_encode($GoDate,JSON_UNESCAPED_UNICODE); //出行时间
            $Data ['ApplyTime'] =json_encode($_POST['ApplyTime'],JSON_UNESCAPED_UNICODE); //截止报名时间
            $Data ['DeparturePlace'] = trim($_POST['DeparturePlace']); //出发地
            $Data ['YoosureTitle'] = intval($_POST['YoosureTitle']); //游学标题
            $Data ['Crowd'] = intval($_POST['Crowd']); //合适人群
            //$Data['Accommodation'] = trim($_POST['Accommodation']); //住宿安排
            $Data ['Price'] = $_POST['Price']; //产品价格
            $Data ['OriginalPrice'] = $_POST['OriginalPrice']; //产品原价
            //特色行程----------------------------------------------------------------------------
            $Data ['TravelPlan'] =addslashes($_POST['TravelPlan']);
            //特色行程----------------------------------------------------------------------------
            //预订须知----------------------------------------------------------------------------
            $Data ['BookingNotice'] = addslashes($_POST['BookingNotice']);
            //预订须知----------------------------------------------------------------------------
            //注意事项----------------------------------------------------------------------------
            $Data ['Notice'] = addslashes($_POST['Notice']);
            //注意事项----------------------------------------------------------------------------
            $Data ['Status'] = trim ( $_POST ['Status'] ); //产品状态
            $Data ['R1'] = intval($_POST['R1']);
            $Data ['Days'] = intval($_POST['Days']); //出行天数

            $Data ['UpdateTime'] = date ( 'Y-m-d H:i:s', time () );
            $Data ['FromIP'] = GetIP ();

            if ($Data ['Title'] == '') {
                alertandback ( '信息填写不完整' );
            }
            if ($YoosureID > 0) {
                //修改
                $Data['UpdateTime'] = date ( 'Y-m-d H:i:s', time () );
                $IsOk = $StudyTourModule->UpdateInfoByKeyID ( $Data, $YoosureID );
                if($IsOk){
                    alertandgotopage ( '操作成功', '/index.php?Module=StudyTour&Action=StudyTourAdd&YoosureID=' . $YoosureID );
                }
                elseif($IsOk === 0){
                    alertandgotopage ( '您没做任何修改', '/index.php?Module=StudyTour&Action=StudyTourAdd&YoosureID=' . $YoosureID );
                }
            } else {
                //添加
                $Data ['AddTime'] = date ( 'Y-m-d H:i:s', time ());
                $YoosureID = $StudyTourModule->InsertInfo ( $Data );
                if($YoosureID){
                    alertandgotopage ( '操作成功', '/index.php?Module=StudyTour&Action=StudyTourAdd&YoosureID=' . $YoosureID );
                }
            }
        }
        include template ( 'StudyTourAdd' );
    }
    /**
     * @desc 设置游学产品每日行程
     */
    public function SetStudyTourContent() {
        $TopNavs = 'SetStudyTourContent';
        $YoosureID = intval ( $_GET ['YoosureID'] );
        if ($YoosureID == 0) {
            alertandgotopage ( "操作失败", '/index.php?Module=StudyTour&Action=StudyTourList' );
        }
        $StudyYoosureModule = new StudyYoosureModule();
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ( $POST ['Title'] as $Key => $Value ) {
                if ($Value != '') {
                    $UpdateInfo [$SK] ['Title']  = $Value;
                    $UpdateInfo [$SK] ['Traffic']  = $POST ['Traffic'] [$Key];
                    $UpdateInfo [$SK]['Accommodation']  = $POST ['Accommodation'] [$Key];
                    $UpdateInfo [$SK]['Content']  = stripslashes($POST ['Content' . $Key]);
                    $SK++;
                }
            }
            $UpdateString = json_encode ( $UpdateInfo ,JSON_UNESCAPED_UNICODE);
            $UpdateData ['Content'] = addslashes ( $UpdateString );
            $IsOk = $StudyYoosureModule->UpdateInfoByKeyID( $UpdateData, $YoosureID );
            alertandgotopage ( "操作成功", '/index.php?Module=StudyTour&Action=SetStudyTourContent&YoosureID=' . $YoosureID );
        }
        $NewContentInfo = $StudyYoosureModule->GetInfoByKeyID($YoosureID);
        if ($NewContentInfo != ''){
            $NewContentArray = json_decode ( $NewContentInfo ['Content'], true );
        }
        $I = count ( $NewContentArray ) + 1;
        include template ( 'SetStudyTourContent' );
    }
    /**
     * @desc 设置游学产品费用说明
     */
    public function SetStudyTourCost() {
        $TopNavs = 'SetStudyTourCost';
        $YoosureID = intval ( $_GET ['YoosureID'] );
        if ($YoosureID == 0) {
            alertandgotopage ( "操作失败", '/index.php?Module=StudyTour&Action=SetStudyTourCost' );
        }
        $StudyYoosureModule = new StudyYoosureModule();
        if ($_POST) {
            $POST = $_POST;
            $SK=0;
            foreach ( $POST ['Title'] as $Key => $Value ) {
                if ($Value != '') {
                    $UpdateInfo [$SK] ['Title']  = $Value;
                    $UpdateInfo [$SK]['Content']  = $POST ['Content' . $Key];
                    $SK++;
                }
            }
            $UpdateString = json_encode ( $UpdateInfo,JSON_UNESCAPED_UNICODE );
            $UpdateData ['CostDescription'] = addslashes ( $UpdateString );
            $IsOk = $StudyYoosureModule->UpdateInfoByKeyID( $UpdateData, $YoosureID );
            alertandgotopage ( "操作成功", '/index.php?Module=StudyTour&Action=SetStudyTourCost&YoosureID=' . $YoosureID );
        }
        $NewContentInfo = $StudyYoosureModule->GetInfoByKeyID($YoosureID);
        if ($NewContentInfo != ''){
            $NewContentArray = json_decode ( $NewContentInfo ['CostDescription'], true );
        }
        $I = count ( $NewContentArray ) + 1;
        include template ( 'SetStudyTourCost' );
    }
    /**
     * @desc 设置图片
     */
    public function StudyTourImages() {
        $TopNavs = 'StudyTourImages';
        $YoosureID = intval ( $_GET ['YoosureID'] );
        unset ( $_SESSION ['YoosureID'] );
        $_SESSION ['YoosureID'] = $YoosureID;
        $StudyTourImageModule = new StudyYoosureImageModule();
        $StudyTourImagesList = $StudyTourImageModule->GetListsByYoosureID( $YoosureID );
        include template ( 'StudyTourImages' );
    }

    /**
     * @desc 删除游学
     */
    public function Delete() {
        $YoosureID = intval ( $_GET ['YoosureID'] );
        if ($YoosureID == 0) {
            alertandback ( "参数错误" );
        }
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyTourInfo = $StudyYoosureModule->GetInfoByKeyID( $YoosureID );
        if (empty ( $StudyTourInfo )) {
            alertandback ( "参数错误" );
        }
        //删除游学产品
        $result = $StudyYoosureModule->DeleteByKeyID($YoosureID);
        if($result){
            //删除图片
            $StudyTourImageModule = new StudyYoosureImageModule();
            $StudyTourImageLists = $StudyTourImageModule->GetListsByYoosureID ( $YoosureID );
            foreach ( $StudyTourImageLists as $Value ) {
                DelFromImgServ($Value ['Image']);
                $StudyTourImageModule->DeleteByKeyID ( $Value ['ImageID'] );
            }
        }
        alertandback ( "操作成功" );
    }
    //删除图片
    public function DeleteStudyTourImages() {
        $ImageID = intval ( $_GET ['ImageID'] );
        $YoosureID = intval ( $_GET ['YoosureID'] );
        if ($ImageID == 0 && $YoosureID ==0) {
            alertandback ( "参数错误" );
        }
        $StudyTourImageModule = new StudyYoosureImageModule ();
        $StudyTourImagesInfo = $StudyTourImageModule->GetInfoByKeyID ( $ImageID );
        $YoosureIDImages = $StudyTourImageModule->GetInfoByWhere(' and YoosureID = '.$YoosureID,true);
        if (empty ( $StudyTourImagesInfo ) && empty ( $YoosureIDImages)) {
            alertandback ( "参数错误" );
        }
        foreach ($YoosureIDImages as $value){
            DelFromImgServ($value['Image']);
        }
        DelFromImgServ($StudyTourImagesInfo ['Image']);
        //删除数据库
        $IsOk = $StudyTourImageModule->DeleteByKeyID ( $ImageID );
        $YoosureOk = $StudyTourImageModule->DeleteByWhere(' and YoosureID = '.$YoosureID);
        if ($IsOk || $YoosureOk) {
            alertandback ( "操作成功" );
        } else {
            alertandback ( "操作失败" );
        }
    }
    //更新游学产品图片
    public function StudyTourImagesSetDefault() {
        $StudyTourImageModule = new StudyYoosureImageModule ();
        $ImageID = intval ( $_GET ['ImageID'] );
        if ($ImageID == 0) {
            alertandback ( "参数错误" );
        }
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        $StudyTourImage = $StudyTourImageModule->GetInfoByKeyID($ImageID);
        if (!$StudyTourImage){
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            alertandback ( "获取图片信息失败" );
        }else{
            $UpdateData ['IsDefault'] = 0;
            $StudyTourImageModule->UpdateInfoByWhere( $UpdateData,' YoosureID = '.$StudyTourImage['YoosureID']);
            $UpdateInfo ['IsDefault'] = 1;
            $IsOk = $StudyTourImageModule->UpdateInfoByKeyID ( $UpdateInfo, $ImageID );
            if (!$IsOk) {
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                alertandback ( "操作失败" );
            } else {
                $DB->query("COMMIT");//执行事务
                alertandback ("操作成功");
            }
        }
    }

        /**
     * @desc  订单列表
     */
    public function OrderList(){
        $StudyTourModule= new StudyYoosureOrderModule();
        $SqlWhere = '';
        //搜索条件
        $PageUrl = 'Module=StudyTour&Action=OrderList';
        $OrderNumber = trim ( $_GET ['OrderNumber'] );
        if ($OrderNumber != '') {
            $SqlWhere .= ' and concat(OrderNumber) like \'%' . $OrderNumber . '%\'';
            $PageUrl .= '&OrderNumber=' . $OrderNumber;
        }
        //分页开始
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 20;
        $Rscount = $StudyTourModule->GetListsNum ( $SqlWhere );
        $Status = $StudyTourModule->Status;
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $StudyTourModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            MultiPage ( $Data, 10 );
        }
        include template ( 'StudyTourOrderList' );
    }

    /**
     * @desc 游学订单详情
     */
    public function OrderDetail(){
        include SYSTEM_ROOTPATH.'/Modules/Class.User.php';
        $YoosureOrderModule = new StudyYoosureOrderModule();
        $OrderID = $_GET ['OrderID'];
        $OrderInfo = $YoosureOrderModule->GetInfoByKeyID($OrderID);
        $Status = $YoosureOrderModule->Status;
        include template ( 'StudyTourOrderDetail' );
    }

    /**
     * @desc 留学身份审核管理
     */
    public function IdentityAuditList(){
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserModule.php';
        $UserInfoModule = new MemberUserInfoModule();
        $UserModule = new MemberUserModule();
        $SqlWhere = '';
        //搜索条件
        $Status = intval($_GET ['Status']);
        if ($Status != '') {
            $SqlWhere .= ' and IdentityState = '.$Status;
            $PageUrl .= '&Status=' . $Status;
        }
        $Title = trim($_GET ['Title']);
        if($Title !=''){
            $SqlWhere .= ' and (UserID=\'' . $Title . '\' or concat(RealName) like \'%' . $Title . '%\')';
            $PageUrl .= '&Title=' . $Title;
        }
        //分页开始
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 20;
        $Rscount = $UserInfoModule->GetListsNum ( $SqlWhere );
        //通过邮箱号、手机号搜索用户
        if ($Title !=''&& $Rscount ['Num']==0){
            $UserSqlWhere='';
            $UserSqlWhere .= ' and (`E-Mail` like \'%' . $Title . '%\'  or concat(Mobile) like \'%' . $Title . '%\')';
            $UserRscount = $UserModule->GetListsNum($UserSqlWhere);
            if ($UserRscount ['Num']) {
                $Data = array ();
                $Data ['RecordCount'] = $Rscount ['Num'];
                $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
                $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
                $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
                $Offset = ($Page - 1) * $Data ['PageSize'];
                if ($Page > $Data ['PageCount'])
                    $page = $Data ['PageCount'];
                $Data ['Data'] = $UserModule->GetLists($UserSqlWhere, $Offset, $Data ['PageSize'] );
                foreach ($Data ['Data'] as $Key => $Value ){
                    $UserInfo = $UserInfoModule->GetInfoByUserID($Value['UserID']);
                    $Data['Data'][$Key]['Email'] = $Value['E-Mail'];
                    $Data['Data'][$Key]['Mobile'] = $Value['Mobile'];
                    $Data['Data'][$Key]['RealName'] = $UserInfo['RealName'];
                }
                MultiPage ( $Data, 10 );
            }
        }
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $UserInfoModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            foreach ( $Data ['Data'] as $Key => $Value ) {
                $User = $UserModule->GetInfoByKeyID($Value['UserID']);
                $Data['Data'][$Key]['Email'] = $User['E-Mail'];
                $Data['Data'][$Key]['Mobile'] = $User['Mobile'];
            }
            MultiPage ( $Data, 10 );
        }
        include template ( 'StudyTourIdentityAuditList' );
    }

    /**
     * @desc 顾问服务列表
     */
    public function ServiceAuditList(){
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyConsultantServiceModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        $UserInfoModule = new MemberUserInfoModule();
        $ServiceModule = new StudyConsultantServiceModule();
        $Type = $ServiceModule->ServiceType;
        $Statu = $ServiceModule->Status;
        $SqlWhere = '';
        $PageUrl ='';
        //搜索条件
        $Status = intval( $_GET ['Status'] );
        $ServiceType = intval( $_GET ['ServiceType'] );
        $Title = trim( $_GET ['Title'] );
        if ($Status != '') {
            $SqlWhere .= ' and Status = '.$Status ;
            $PageUrl .= '&Status=' . $Status;
        }
        if ($ServiceType != ''){
            $SqlWhere .= ' and ServiceType = '.$ServiceType ;
            $PageUrl .= '&ServiceType=' . $ServiceType;
        }
        if ($Title != ''){
            $SqlWhere .= ' and (ServiceID=\'' . $Title . '\' or concat(ServiceName) like \'%' . $Title . '%\')';
            $PageUrl .= '&Title=' . $Title;
        }
        //分页开始
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 15;
        $Rscount = $ServiceModule->GetListsNum ( $SqlWhere );
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $ServiceModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            foreach($Data['Data'] as $key => $val){
                $Data['Data'][$key]['Status'] = $Statu[$val['Status']];
                $UserInfo = $UserInfoModule->GetInfoByUserID($val['UserID']);
                $Data['Data'][$key]['ConsultantName'] = $UserInfo['RealName'];
            }
            MultiPage ( $Data, 10 );
        }
        include template ( 'StudyTourServiceAuditList' );
    }

    /**
     * @desc 顾问服务审核详情
     */
    public function ServiceAuditDetail(){
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyConsultantServiceModule.php';
        $ServiceModule = new StudyConsultantServiceModule();
        if ($_GET['ID']){
            $ID = $_GET['ID'];
            $Service = $ServiceModule->GetInfoByKeyID($ID);
            $ServiceImages = json_decode($Service['ImagesJson'],true);
            $ServiceType = $ServiceModule->ServiceType;
            $ServiceImage = json_decode($Service['Images'],true);
        }
        if ($_POST){
            $Data['Status'] = intval($_POST['Status']);
            $ID = intval($_POST['ID']);
            $result = $ServiceModule->UpdateInfoByKeyID($Data,$ID);
            if($result){
                alertandgotopage('操作成功!','/index.php?Module=StudyTour&Action=ServiceAuditDetail&ID='.$ID);
            }elseif($result === 0){
                alertandback('状态未发生改变!');
            }else{
                alertandback('操作失败!');
            }
        }

        include template ( 'StudyTourServiceAuditDetail' );
    }
    /**
     * @desc 教师课程列表
     */
    public function CourseAuditList(){
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyTeacherCourseModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        $UserInfoModule = new MemberUserInfoModule();
        $CourseModule = new StudyTeacherCourseModule();
        $Type = $CourseModule->CourseType; //课程类型
        $TeachType = $CourseModule->TeachType; //上课方式
        $Statu = $CourseModule->Status; //审核状态
        $SqlWhere = '';
        $PageUrl ='';
        //搜索条件
        $Status = intval( $_GET ['Status'] );
        $CourseType = intval( $_GET ['CourseType'] );
        $Title = trim( $_GET ['Title'] );
        if ($Status != '') {
            $SqlWhere .= ' and Status = '.$Status ;
            $PageUrl .= '&Status=' . $Status;
        }
        if ($CourseType != '') {
            $SqlWhere .= ' and CourseType = '.$CourseType ;
            $PageUrl .= '&CourseType=' . $CourseType;
        }
        if($Title !=''){
            $SqlWhere .= ' and (CourseID=\'' . $Title . '\' or concat(CourseName) like \'%' . $Title . '%\')';
            $PageUrl .= '&Title=' . $Title;
        }
        //分页开始
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 15;
        $Rscount = $CourseModule->GetListsNum ( $SqlWhere );
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $CourseModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            foreach($Data['Data'] as $key => $val){
                $UserInfo = $UserInfoModule->GetUserInfo($val['UserID']);
                $Data['Data'][$key]['TeacherName'] = $UserInfo['RealName'];
            }
            MultiPage ( $Data, 10 );
        }
        include template ( 'StudyTourCourseAuditList' );
    }

    /**
     * @desc  课程审核详情页
     */
    public function CourseAuditDetail(){
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyTeacherCourseModule.php';
        $CourseModule = new StudyTeacherCourseModule();
        if ($_GET['ID']){
            $ID = $_GET['ID'];
            $Course = $CourseModule->GetInfoByKeyID($ID);
            $CourseType = $CourseModule->CourseType;
            $TeachType = $CourseModule->TeachType;
            $CourseImage = json_decode($Course['CourseImages'],true);
        }
        if ($_POST){
            $Data['Status'] = intval($_POST['Status']);
            $ID = intval($_POST['ID']);
            $result = $CourseModule->UpdateInfoByKeyID($Data,$ID);
            if($result){
                alertandgotopage('操作成功!','/index.php?Module=StudyTour&Action=CourseAuditDetail&ID='.$ID);
            }elseif($result === 0){
                alertandback('状态未发生改变!');
            }else{
                alertandback('操作失败!');
            }
        }
        include template ( 'StudyTourCourseAuditDetail' );
    }

    /**
     * @desc  更改课程审核状态
     */
    public function UpdateCourseAuditStatus(){
        $Status = $_GET['Status'];
        $ID = $_GET['ID'];
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyTeacherCourseModule.php';
        $CourseModule = new StudyTeacherCourseModule();
        $result = $CourseModule->UpdateInfoByKeyID(array('Status'=>$Status),$ID);
        if($result){
            alertandgotopage('操作成功!','/index.php?Module=StudyTour&Action=CourseAuditList');
        }
        elseif($result === 0){
            alertandback('状态未发生改变!');
        }
        else{
            alertandback('操作失败!');
        }
    }
    /**
     * @desc  顾问列表
     */
    public function ConsultantList(){
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserModule.php';
        $UserInfoModule = new MemberUserInfoModule();
        $UserModule = new MemberUserModule();
        //搜索条件
        $SqlWhere=' and Identity = 2';
        $PageUrl='';
        $Status = intval($_GET ['Status']);
        $Title = trim($_GET ['Title']);
        if ($Status != '') {
            $SqlWhere .= ' and IdentityState = '.$Status;
            $PageUrl .= '&Status=' . $Status;
        }
        if($Title !=''){
            $SqlWhere .= ' and (UserID=\'' . $Title . '\' or concat(RealName) like \'%' . $Title . '%\')';
            $PageUrl .= '&Title=' . $Title;
        }
        //分页开始
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 20;
        $Rscount = $UserInfoModule->GetListsNum ( $SqlWhere );
        //通过邮箱号、手机号搜索用户
        if ($Title !=''&& !$Rscount ['Num']){
            $UserSqlWhere='';
            $UserSqlWhere .= ' and (`E-Mail` like \'%' . $Title . '%\'  or concat(Mobile) like \'%' . $Title . '%\')';
            $UserRscount = $UserModule->GetListsNum($UserSqlWhere);
            if ($UserRscount ['Num']) {
                $Data = array ();
                $Data ['RecordCount'] = $Rscount ['Num'];
                $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
                $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
                $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
                $Offset = ($Page - 1) * $Data ['PageSize'];
                if ($Page > $Data ['PageCount'])
                    $page = $Data ['PageCount'];
                $Data ['Data'] = $UserModule->GetLists($UserSqlWhere, $Offset, $Data ['PageSize'] );
                foreach ($Data ['Data'] as $Key => $Value ){
                    $UserInfo = $UserInfoModule->GetInfoByUserID($Value['UserID']);
                    $Data['Data'][$Key]['Email'] = $Value['E-Mail'];
                    $Data['Data'][$Key]['Mobile'] = $Value['Mobile'];
                    $Data['Data'][$Key]['RealName'] = $UserInfo['RealName'];
                    $Data['Data'][$Key]['IdentityState'] = $UserInfo['IdentityState'];
                }
                MultiPage ( $Data, 10 );
            }
        }
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $UserInfoModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            foreach ( $Data ['Data'] as $Key => $Value ) {
                $User = $UserModule->GetInfoByKeyID($Value['UserID']);
                $Data['Data'][$Key]['Email'] = $User['E-Mail'];
                $Data['Data'][$Key]['Mobile'] = $User['Mobile'];
            }
            MultiPage ( $Data, 10 );
        }
        include template ( 'StudyTourConsultantList' );
    }
    /**
     * @desc  顾问详情管理
     */
    public function ConsultantDetail(){
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyConsultantInfoModule.php';
        $UserInfoModule = new MemberUserInfoModule();
        $UserModule = new MemberUserModule();
        $ConsultantInfoModule = new StudyConsultantInfoModule();
        $TutorialObject = $ConsultantInfoModule->TutorialObject;
        $Grade = $ConsultantInfoModule->Grade;
        if ($_GET['ID']){
            $UserID = $_GET['ID'];
            $User = $UserModule->GetInfoByKeyID($UserID);
            $UserInfo = $UserInfoModule->GetUserInfo($UserID);
            $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
            $ImageText = json_decode($ConsultantInfo['ImageText'],true);
            //顾问过往经历
            $PastExperience = json_decode($ConsultantInfo['PastExperience'],true);//var_dump($PastExperience);
            //认证照片
            $Account ='';
            if($User['Mobile']){
                $Account = $User['Mobile'];
                if($User['E-Mail']){
                    $Account .=','.$User['E-Mail'];
                }
            }else{
                $Account = $User['E-Mail'];
            }
        }
        if ($_POST['UserID']){
            $Data['IdentityState'] = intval($_POST['Status']);
            $UserID = intval($_POST['UserID']);
            $result = $UserInfoModule->UpdateData($Data,$UserID);
            if($result){
                alertandgotopage('操作成功!','/index.php?Module=StudyTour&Action=ConsultantDetail&ID='.$UserID);
            }elseif($result === 0){
                alertandback('状态未发生改变!');
            }else{
                alertandback('操作失败!');
            }
        }
        if ($_POST['ID']){
            $Data['Choosed'] = intval($_POST['Choosed']);
            $Data['OneCondition'] = trim($_POST['OneCondition']);
            $Data['TwoCondition'] = trim($_POST['TwoCondition']);
            $Data['ThreeCondition'] = trim($_POST['ThreeCondition']);
            $Data['MIndexRecommend'] = trim($_POST['MIndexRecommend']);
            $Data['RecommendSort'] = trim($_POST['RecommendSort']);
            $ID = intval($_POST['ID']);
            $ConsultantInfo = $ConsultantInfoModule->GetInfoByKeyID($ID);
            $result = $ConsultantInfoModule->UpdateInfoByKeyID($Data,$ID);
            if($result){
                alertandgotopage('操作成功!','/index.php?Module=StudyTour&Action=ConsultantDetail&ID='.$ConsultantInfo['UserID']);
            }elseif($result === 0){
                alertandback('状态未发生改变!');
            }else{
                alertandback('操作失败!');
            }
        }
        include template ( 'StudyTourConsultantDetail' );
    }
    /**
     * @desc  教师列表
     */
    public function  TeacherList(){
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserModule.php';
        $UserInfoModule = new MemberUserInfoModule();
        $UserModule = new MemberUserModule();
        //搜索条件
        $SqlWhere=' and Identity = 3';
        $PageUrl='';
        $Status = intval($_GET ['Status']);
        $Title = trim($_GET ['Title']);
        if ($Status != '') {
            $SqlWhere .= ' and IdentityState = '.$Status;
            $PageUrl .= '&Status=' . $Status;
        }
        if($Title !=''){
            $SqlWhere .= ' and (UserID=\'' . $Title . '\' or concat(RealName) like \'%' . $Title . '%\')';
            $PageUrl .= '&Title=' . $Title;
        }
        //分页开始
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 20;
        $Rscount = $UserInfoModule->GetListsNum ( $SqlWhere );
        //通过邮箱号、手机号搜索用户
        if ($Title !=''&& !$Rscount ['Num']){
            $UserSqlWhere='';
            $UserSqlWhere .= ' and (`E-Mail` like \'%' . $Title . '%\'  or concat(Mobile) like \'%' . $Title . '%\')';
            $UserRscount = $UserModule->GetListsNum($UserSqlWhere);
            if ($UserRscount ['Num']) {
                $Data = array ();
                $Data ['RecordCount'] = $Rscount ['Num'];
                $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
                $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
                $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
                $Offset = ($Page - 1) * $Data ['PageSize'];
                if ($Page > $Data ['PageCount'])
                    $page = $Data ['PageCount'];
                $Data ['Data'] = $UserModule->GetLists($UserSqlWhere, $Offset, $Data ['PageSize'] );
                foreach ($Data ['Data'] as $Key => $Value ){
                    $UserInfo = $UserInfoModule->GetInfoByUserID($Value['UserID']);
                    $Data['Data'][$Key]['Email'] = $Value['E-Mail'];
                    $Data['Data'][$Key]['Mobile'] = $Value['Mobile'];
                    $Data['Data'][$Key]['RealName'] = $UserInfo['RealName'];
                    $Data['Data'][$Key]['IdentityState'] = $UserInfo['IdentityState'];
                }
                MultiPage ( $Data, 10 );
            }
        }
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $UserInfoModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            foreach ( $Data ['Data'] as $Key => $Value ) {
                $User = $UserModule->GetInfoByKeyID($Value['UserID']);
                $Data['Data'][$Key]['Email'] = $User['E-Mail'];
                $Data['Data'][$Key]['Mobile'] = $User['Mobile'];
            }
            MultiPage ( $Data, 10 );
        }
        include template ( 'StudyTourTeacherList' );
    }
    /**
     * @desc  教师详情
     */
    public function TeacherDetail(){
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyTeacherInfoModule.php';
        $UserInfoModule = new MemberUserInfoModule();
        $UserModule = new MemberUserModule();
        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
        $TutorialObject = $StudyTeacherInfoModule->TutorialObject;
        $Grade = $StudyTeacherInfoModule->Grade;
        if ($_GET['ID']){
            $UserID = $_GET['ID'];
            $User = $UserModule->GetInfoByKeyID($UserID);
            $UserInfo = $UserInfoModule->GetUserInfo($UserID);
            $TeacherInfo = $StudyTeacherInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
            $ImageText = json_decode($TeacherInfo['ImageText'],true);
            //过往经历
            $PastExperience = json_decode($TeacherInfo['PastExperience'],true);//var_dump($PastExperience);
            //认证照片
            $Account ='';
            if($User['Mobile']){
                $Account = $User['Mobile'];
                if($User['E-Mail']){
                    $Account .=','.$User['E-Mail'];
                }
            }else{
                $Account = $User['E-Mail'];
            }
        }
        if ($_POST['UserID']){
            $Data['IdentityState'] = intval($_POST['Status']);
            $UserID = intval($_POST['UserID']);
            $result = $UserInfoModule->UpdateData($Data,$UserID);
            if($result){
                alertandgotopage('操作成功!','/index.php?Module=StudyTour&Action=TeacherDetail&ID='.$UserID);
            }elseif($result === 0){
                alertandback('状态未发生改变!');
            }else{
                alertandback('操作失败!');
            }
        }
        include template ( 'StudyTourTeacherDetail' );
    }
    /**
     * @desc  用户匹配顾问管理列表
     */
    public function StudyMarryList(){
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyMarryInfoModule.php';
        $UserInfoModule = new MemberUserInfoModule();
        $StudyMarryInfoModule = new StudyMarryInfoModule();
        $SqlWhere = '';
        //分页开始
        $Type = $StudyMarryInfoModule->ServiceType;
        $TargetLevel = $StudyMarryInfoModule->TargetLevel;
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 20;
        $Rscount = $StudyMarryInfoModule->GetListsNum ( $SqlWhere );
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $StudyMarryInfoModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            foreach ($Data ['Data'] as $key=>$value){
                $UserInfo =  $UserInfoModule->GetInfoByWhere(' and UserID = '.$value['UserID']);
                $Data ['Data'][$key]['RealName'] = $UserInfo['RealName'];
            }

            MultiPage ( $Data, 10 );
        }
        include template ( 'StudyTourStudyMarryList' );
    }
    /**
     * @desc  用户匹配顾问管理详情
     */
    public function StudyMarryDetail(){
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyMarryInfoModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyConsultantInfoModule.php';
        $StudyMarryInfoModule = new StudyMarryInfoModule();
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $MarryID = intval ( $_GET ['ID'] );
        $Type = $StudyMarryInfoModule->ServiceType;
        $TargetLevel = $StudyMarryInfoModule->TargetLevel;
        $StudyMarryInfo = $StudyMarryInfoModule->GetInfoByKeyID($MarryID);
        $ConsultantJson = json_decode($StudyMarryInfo['ConsultantJson'],true);
        include template ( 'StudyTourStudyMarryDetail' );
    }
    /**
     * @desc  免费评估管理
     */
    public function StudyMarryEstimate(){
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyEstimateModule.php';
        $StudyEstimateModule = new StudyEstimateModule();
        $SqlWhere ='';
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 20;
        $Rscount = $StudyEstimateModule->GetListsNum ( $SqlWhere );
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data ['Data'] = $StudyEstimateModule->GetLists ( $SqlWhere, $Offset, $Data ['PageSize'] );
            MultiPage ( $Data, 10 );
        }
        include template ( 'StudyTourStudyMarryEstimate' );
    }
}