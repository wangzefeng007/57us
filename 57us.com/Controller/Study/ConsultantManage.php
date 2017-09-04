<?php
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class ConsultantManage extends CommonController{

    public function __construct(){
        //$_SESSION['UserID'] = 321;
        //判断顾问登录状态
        $this->ConsultantLoginStatus();
        //JudgeHaveConsultantInfo
        $this->JudgeHaveConsultantInfo();
    }

    /**
     * @desc 顾问个人主页
     */
    public function MyCenter(){
        //获取用户基础信息
        $UserInfoModule = new MemberUserInfoModule();
        $UserInfo = $UserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        //获取用户头像
        if(strpos($UserInfo['Avatar'],"http://")===false && $UserInfo['Avatar']){
            $UserInfo['Avatar']=LImageURL.$UserInfo['Avatar'];
        }
        else{
            $UserInfo['Avatar']=LImageURL.'/img/man3.0.png';
        }
        //获取顾问审核资料信息
        $ConsultantInfoModule = new StudyConsultantInfoModule();
        $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere("and UserID={$_SESSION['UserID']}");
        $ConsultantGrade = $ConsultantInfoModule->Grade;
        //获取服务数量
        $StudyConsultantServiceModule = new StudyConsultantServiceModule();
        $ServiceAmount=$StudyConsultantServiceModule->GetListsNum("and UserID={$_SESSION['UserID']} and `Status`<>5");
        //获取在办客户的数量
        $StudyConsultantStudentInfoModule = new StudyConsultantStudentInfoModule();
        $ServiceStudentAmount=$StudyConsultantStudentInfoModule->GetListsNum("and ConsultantID={$_SESSION['UserID']} and IsComplete=1");
        //获取消息
        $Status=isset($_GET['S'])?intval($_GET['S']):1;
        $WaitProcess=isset($_GET['W_P'])?intval($_GET['W_P']):0;
        $Page=isset($_GET['Page'])?intval($_GET['Page']):1;
        $PageSize=6;
        $Data=$this->MemberMessageLists($_SESSION['UserID'],$Status,$WaitProcess,$Page,$PageSize);
        $Paging=new Page($Data['RecordCount'],$PageSize,2);
        $PageHTML=$Paging->showpage();
        $Nav='mycenter';
        include template ( 'ConsultantManageMyCenter' );
    }

    /**
     * @desc 顾问我的资产
     */
    public function Assets(){
        $Nav ='assets';
        $MemberUserBankModule = new MemberUserBankModule();
        $UserBankFlowModule = new MemberUserBankFlowModule();
        $MemberUserInfoModule = new MemberUserInfoModule();
        //$Type = 2;
        $MysqlWhere = ' and UserID = '.$_SESSION['UserID'];
        $T =  intval($_GET['T']);
        if ($T){
            $MysqlWhere .= ' and OperateType = '.$T;
        }
        $Page = intval($_GET['page'])<1?1:intval($_GET['page']);
        $pageSize = 5;
        $Rscount = $UserBankFlowModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($pageSize ? $pageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $pageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $UserBankFlowModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            $ClassPage = new Page($Rscount['Num'], $pageSize,2);
            $ShowPage = $ClassPage->showpage();
        }
        $SafeLeval=$this->MemberSafeLevel();
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $FreeBalance = $MemberUserBankModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
        include template ( 'ConsultantManageAssets' );
    }

    /**
     * @desc 顾问成功案例列表
     */
    public function SuccessCase(){
        $Nav="successcase";
        include template ( 'ConsultantManageSuccessCase');
    }

    /**
     * @desc 顾问成功案例保存
     */
    public function AddSuccessCase(){
        $CaseID = $_GET['CaseID'];
        $Nav="successcase";
        include template ( 'ConsultantManageAddSuccessCase' );
    }

    /**
     * @desc 顾问服务列表
     */
    public function ServiceList(){
        $Status=isset($_GET['S'])?intval($_GET['S']):3;
        $Nav='servicelist';
        include template ( 'ConsultantManageServiceList' );
    }

    /**
     * @desc 顾问添加服务
     */
    public function AddService(){
        $ServiceID=intval($_GET['ID']);
        $StudyConsultantServiceModule = new StudyConsultantServiceModule();
        $ServiceType = $StudyConsultantServiceModule->ServiceType;
        if($ServiceID){
            $ServiceInfo=$StudyConsultantServiceModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and ServiceID=$ServiceID and `Status`<>5");
            $ServiceInfo['ServiceDetails']=StrReplaceImages($ServiceInfo['ServiceDetails']);
        }
        $Nav='servicelist';
        include template ( 'ConsultantManageAddService' );
    }

    /**
     * @desc 顾问服务提交保存成功
     */
    public function SaveSuccess(){
        $ServiceID=intval($_GET['ID']);
        if($ServiceID){
            $StudyConsultantServiceModule = new StudyConsultantServiceModule();
            $ServiceInfo=$StudyConsultantServiceModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and ServiceID=$ServiceID");
            if($ServiceInfo){
                include template ('ConsultantManageSaveSuccess');
            }else{
                alertandgotopage("异常的操作请求",WEB_STUDY_URL);
            }
                        
        }else{
            alertandgotopage("异常的操作请求",WEB_STUDY_URL);
        }
    }

    /**
     * @desc 顾问服务提交审核中
     */
    public function UnderReview(){
        $ServiceID=intval($_GET['ID']);
        if($ServiceID){
            $StudyConsultantServiceModule = new StudyConsultantServiceModule();
            $ServiceInfo=$StudyConsultantServiceModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and ServiceID=$ServiceID");
            if($ServiceInfo){
                include template ( 'ConsultantManageUnderReview' );
            }else{
                alertandgotopage("异常的操作请求",WEB_STUDY_URL);
            }
                        
        }else{
            alertandgotopage("异常的操作请求",WEB_STUDY_URL);
        }
    }

    /**
     * @desc 顾问客户管理
     */
    /*public function Customer(){
        $Nav="customer";
        include template ('ConsultantManageCustomer');
    }*/

    /**
     * @desc 顾问个人信息设置
     */
    public function MyInfoSettings(){
        //获取用户基础信息
        $UserID = $_SESSION['UserID'];
        $UserInfoModule = new MemberUserInfoModule();
        if(isset($_GET['rs']) && $_GET['rs']==1){
            $UserInfoModule->UpdateInfoByWhere(array('IdentityState'=>0),"UserID=$UserID");
        }
        $UserInfo = $UserInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
       if($UserInfo['IdentityState']==2){
            header("Location:/consultantmanage/approvemyinfo/");
        }else{
           $StudyConsultantInfoModule = new StudyConsultantInfoModule();
           $ConsultantInfo = $StudyConsultantInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
           $ServiceProject = $StudyConsultantInfoModule->ServiceProject;
           //echo "<pre>";print_r($ServiceProject);exit;
           if(strpos($UserInfo['CardPositive'],"http://")===false && $UserInfo['CardPositive']!=''){
               $UserInfo['CardPositive']=LImageURL.$UserInfo['CardPositive'];
           }
           $Tags = json_decode($ConsultantInfo['Tags'],true);
           $PastExperience = json_decode($ConsultantInfo['PastExperience'],true);
           $Nav="myinfosettings";
           include template ('ConsultantManageMyInfoSettings');
        }
    }

    /**
     * @desc 个人信息预览框架
     */
    public function MyInfoAuditView(){
        $UserID = $_SESSION['UserID'];
        $UserInfoModule = new MemberUserInfoModule();
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $ConsultantInfo = $StudyConsultantInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
        $UserInfo = $UserInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
        $Tags = json_decode($ConsultantInfo['Tags'],true);
        $PastExperience = json_decode($ConsultantInfo['PastExperience'],true);
        include template ( 'ConsultantManageMyInfoAuditView' );
    }

    /**
     * @desc 顾问审核通过个人信息设置
     */
    public function ApproveMyInfo(){
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $ServiceProject = $StudyConsultantInfoModule->ServiceProject;
        $UserID = $_SESSION['UserID'];
        $UserInfoModule = new MemberUserInfoModule();
        $UserInfo = $UserInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
        if($UserInfo['IdentityState']==2){
            $ConsultantInfo = $StudyConsultantInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
            if(strpos($UserInfo['CardPositive'],"http://")===false && $UserInfo['CardPositive']!=''){
                $UserInfo['CardPositive']=LImageURL.$UserInfo['CardPositive'];
            }
            $Tags = json_decode($ConsultantInfo['Tags'],true);
            $PastExperience = json_decode($ConsultantInfo['PastExperience'],true);
            $Nav="myinfosettings";
            include template ( 'ConsultantManageApproveMyInfo' );            
        }else{
            header("Location:/consultantmanage/myinfosettings/");
        } 
    }


    /**
     * @desc  获取左侧列表数据
     * @param $Data   数据
     * @param $IsComplete  是否完成
     * @return array
     */
    public function GetLeftData($MysqlWhere,$IsComplete){
        //学生数据
        $StudentInfoModule = new StudyConsultantStudentInfoModule();
        $Data   = $StudentInfoModule->GetInfoByWhere($MysqlWhere,true);
        //数据整理
        $MemberUserInfo = new MemberUserInfoModule();
        $Result = array();
        foreach($Data as $key=>$val){
            $UserInfo = $MemberUserInfo->GetInfoByUserID($val['StudentID']);
            $Result[$key] = array(
                'StudentID'=>$val['StudentID'],
                'Avatar'=>ImageURLP2.$UserInfo['Avatar'],
                'StudentName'=>$val['StudentName'],
                'Depict'=> '美国'.$val['EducationalBackground'].'   '.$val['GoTime'],
                'IsComplete'=>$IsComplete
            );
        }
        return $Result;
    }

    /**
     * @desc 客户管理
     */
    public function ClientManage(){
        $Nav = 'customer';
        $IsComplete = intval($_GET['IsComplete'])?intval($_GET['IsComplete']):1; //默认在办理
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        $Keyword = trim($_POST['Keyword']);
        if($Keyword){
            $MysqlWhere.= " and StudentName LIKE '%".$Keyword."%'" ;
        }
        //获取客户数据
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);
        include template ( 'ConsultantManageClientManage' );
    }

    /**
     * @desc  学生信息
     */
    public function ClientManageStudent(){
        $Nav = 'customer';
        $IsComplete = intval($_GET['IsComplete'])?intval($_GET['IsComplete']):1; //默认在办理
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);

        $Navv = 'StudentInfo';
        $UserID = intval($_GET['ID']);
        $StudentInfoModule = new StudyConsultantStudentInfoModule();
        $StudentInfo = $StudentInfoModule->GetInfoByWhere(' and StudentID = '.$UserID.' and ConsultantID='.$_SESSION['UserID']);
        $StudentInfo['Remarks'] = $StudentInfo['Remarks']?json_decode($StudentInfo['Remarks'],true):'';
        //echo "<pre>";print_r($StudentInfo);exit;
        include template ( 'ConsultantManageClientManageStudent' );//背景提升
    }


    /**
     * @desc  顾问客户，TA的服务
     */
    public function ClientManageService(){
        $Nav = 'customer';
        $UserID = intval($_GET['ID']);
        $IsComplete = $_GET['IsComplete'];
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        //获取左侧客户数据
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);

        $OrderModule = new StudyOrderModule();
        $ServiceModule = new StudyConsultantServiceModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $Part = $OrderConsultantModule->Part;

        //$Status = $IsComplete == 1 ?2:3;

        $OrderStatus = $OrderModule->Status;
        $OrderPayType = $OrderModule->PayType;
        $OrderData = $OrderModule->GetInfoByWhere(" and UserID = {$UserID} and RelationID={$_SESSION['UserID']} order by AddTime desc",true);

        $Result = array();
        foreach ($OrderData as $key=>$val){
            $Result[$key]['OrderId'] = $val['OrderID']; //订单id
            $Result[$key]['OrderNum'] = $val['OrderNum']; //订单编号
            $Result[$key]['AddTime'] = date("Y-m-d H:i:s",$val['AddTime']); //订单生成时间
            $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and (Status = 1 or Status = 2) and OrderID ='.$val['OrderID']);
            $Result[$key]['Progress'] = $Part[$OrderConsultantInfo['Type']]?$Part[$OrderConsultantInfo['Type']]['Title'].'服务中':"已完成"; //办理进度
            $Result[$key]['ProgressType'] = $OrderConsultantInfo['Type'];
            $Result[$key]['OrderName'] = $val['OrderName'];   //订单名称
            $Result[$key]['Money'] = $val['Money']; //订单价格
            $Result[$key]['Status'] = $OrderStatus[$val['Status']]?$OrderStatus[$val['Status']]:''; //订单状态
            $Result[$key]['PayType'] = $OrderPayType[$val['PayType']]?$OrderPayType[$val['PayType']]:''; //支付方式
            $ServiceInfo = $ServiceModule->GetInfoByKeyID($val['ProductID']);
            $ImageJson = json_decode($ServiceInfo['ImagesJson'],true);
            $Result[$key]['CoverImageKey'] = ($ImageJson[$ServiceInfo['CoverImageKey']]!='')?(ImageURLP4.$ImageJson[$ServiceInfo['CoverImageKey']]):(ImageURLP2.'/Uploads/Study/Service/service.jpg');  //产品图片
        }
        include template ( 'ConsultantManageClientManageServices' );
    }

    /**
     * @desc 顾问我的订单
     */
    public function MyOrder(){
        $Nav ='myorder';
        include template ( 'ConsultantManageMyOrder' );
    }

    /**
     * @desc 顾问我的订单详情
     */
    public function MyOrderDetails(){
        $Nav ='myorder';
        $OrderID=intval($_GET['ID']);
        if($OrderID){
            $StudyConsultantInfoModule = new StudyConsultantInfoModule();
            $StudyOrderConsultantModule = new StudyOrderConsultantModule();
            $StudyOrderModule=new StudyOrderModule();
            $Part = $StudyOrderConsultantModule->Part;
            $OrderInfo=$StudyOrderModule->GetInfoByWhere("and OrderID=$OrderID and RelationID={$_SESSION['UserID']} and OrderType=1");
            $ConsultantInfo = $StudyConsultantInfoModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
            $StudyConsultantServiceModule = new StudyConsultantServiceModule();
            $ServiceInfo=$StudyConsultantServiceModule->GetInfoByWhere("and ServiceID={$OrderInfo['ProductID']}");
            $ServiceImg=json_decode($ServiceInfo['ImagesJson'],true);
            if(!empty($ServiceImg)){
                if(strpos($ServiceImg[$ServiceInfo['CoverImageKey']],"http://")===false){
                    $ServiceImg[$ServiceInfo['CoverImageKey']]=ImageURLP4.$ServiceImg[$ServiceInfo['CoverImageKey']];
                }
            }else{
                $ServiceImg[$ServiceInfo['CoverImageKey']]=ImageURLP2.'/Uploads/Study/Service/service.jpg';
            }
            //获取订单详情
            $OrderConsultant = $StudyOrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID,true);
            $MemberUserInfoModule= new MemberUserInfoModule();
            $MemberInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
            if (strpos($MemberInfo['Avatar'], 'http://') === false && $MemberInfo['Avatar'] != '') {
                $MemberInfo['Avatar'] = LImageURL . $MemberInfo['Avatar'];
            }
            //获取总金额
            $CountMoney =0;
            foreach ($OrderConsultant as $value){
                $CountMoney = $CountMoney +$value['Amt'];
            }
            $endTime = $OrderInfo['AddTime']+259200-time();
            $TimeEnd =date("Y-m-d H:i:s",$OrderInfo['AddTime']+259200);
            //订单状态
            $Status=$StudyOrderModule->Status;
            //服务类型
            $ServiceType=$StudyConsultantServiceModule->ServiceType;
            //支付类型
            $PayType=$StudyOrderModule->PayType;
            switch ($ServiceInfo['ServiceType']){
                case '1':
                    include template ( 'ConsultantManageMyOrderDetails' );//全程服务
                    break;
                case '2':
                    include template ( 'ConsultantApplyMyOrderDetails' );//申请学校
                    break;
                case '3':
                    include template ( 'ConsultantInstrumentsMyOrderDetails' );//文书服务
                    break;
                case '4':
                    include template ( 'ConsultantChoseMyOrderDetails' );//定校选校
                    break;
                case '5':
                    include template ( 'ConsultantVisaMyOrderDetails' );//签证培训
                    break;
                case '6':
                    include template ( 'ConsultantTranslationMyOrderDetails' );//材料翻译
                    break;
                case '7':
                    include template ( 'ConsultantBackMyOrderDetails' );//背景提升
                    break;
            }
        }else{
            alertandback("不存在该订单");
        }
    }

    /**
     * @desc  项目分配
     */
    public function ItemAllot(){
        $OrderID = intval($_GET['ID']);
        $Type = intval($_GET['Type']);
        if(intval($_GET['Type'])){
            $Type = intval($_GET['Type']);
        }
        else{
            $OrderModule = new StudyOrderModule();
            $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
            //顾问服务详情
            $ServiceModule = new StudyConsultantServiceModule();
            $ProductID = $OrderInfo['ProductID'];
            $ServiceInfo = $ServiceModule->GetInfoByKeyID($ProductID);
            $ServiceType = $ServiceInfo['ServiceType'];
            if($ServiceType == 5 || $ServiceType == 6 || $ServiceType == 7){
                $Type = $ServiceType;
            }
            else{
                $Type = 1;
            }
        }
        $IsComplete = intval($_GET['IsComplete']);
        switch ($Type){
            case '1': //调查表
                header("Location: /consultantmanage/itemquestionnaire/?OrderID={$OrderID}&Type=1&IsComplete={$IsComplete}");
                break;
            case '2': //选校定校
                header("Location: /consultantmanage/itemschoolchoose/?OrderID={$OrderID}&Type={$Type}&IsComplete={$IsComplete}");
                break;
            case '3': //文书服务
                header("Location: /consultantmanage/itemdocument/?OrderID={$OrderID}&Type={$Type}&IsComplete={$IsComplete}");
                break;
            case '4': //申请学校
                header("Location: /consultantmanage/itemschoolapply/?OrderID={$OrderID}&Type={$Type}&IsComplete={$IsComplete}");
                break;
            case '5': //签证办理
                header("Location: /consultantmanage/itemvisa/?OrderID={$OrderID}&Type={$Type}&IsComplete={$IsComplete}");
                break;
            case '6': //材料翻译
                header("Location: /consultantmanage/itemtranslate/?OrderID={$OrderID}&Type={$Type}&IsComplete={$IsComplete}");
                break;
            case '7': //背景提升
                header("Location: /consultantmanage/myorderdetails/?ID={$OrderID}");
                break;
        }
    }

    /**
     * @desc  调查表(处理)
     */
    public function ItemQuestionnaire(){
        $Nav = 'customer';
        $IsComplete = $_GET['IsComplete'];
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        //获取左侧客户数据
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);

        $OrderID = $_GET['OrderID'];
        $Type = $_GET['Type'];//点击其他服务时传递(未必有值)
        //获取公共信息
        $CommonData = $this->GetItemCommonInfo($OrderID,$Type,$IsComplete);
        $UserID = $CommonData['UserID'];

        //调查表信息
        $QuestionnaireModule = new StudyConsultantQuestionnaireModule();
        if($CommonData['ProgressStatus'] == 2){
            //第一次上传的调查表信息
            $FirstInfo =$QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID asc');
            $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document'],'UpQuestion_Message'=>$FirstInfo['Describe']);
            $OneWhere = ' and ID <> '.$FirstInfo['ID'];
            //当前最后一条的调查表信息
            $LastInfo = $QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID desc');
            if($FirstInfo['ID'] != $LastInfo['ID'] && $LastInfo){
                $TwoWhere = ' and ID <> '.$LastInfo['ID'];
                $FileName = $LastInfo['DocumentName']?$LastInfo['DocumentName']:'';
                $File = $LastInfo['Document']?FileURL.$LastInfo['Document']:'';
                $Result['Data'] = array('OperateID'=>$LastInfo['ID'],'QuestionnaireID'=>$LastInfo['ID'],'Question_Date'=>date("Y-m-d H:i",$LastInfo['AddTime']),'Question_FileName'=>$FileName,'Question_DownUrl'=>$File,'Question_Message'=>$LastInfo['Describe'],'Question_CoupleBackName'=>$LastInfo['Feedback']==1?'学生反馈':'我的反馈','Feedback'=>$LastInfo['Feedback']);
            }
            else{
                $Result['Data'] = '';
                $TwoWhere = '';
            }
            $OtherInfo = $QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.$OneWhere.$TwoWhere.' order by ID desc',true);
            if($OtherInfo){
                foreach($OtherInfo as $key => $val){
                    $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName']?$val['DocumentName']:'','Question_DownUrl'=>$val['Document']?FileURL.$val['Document']:'','Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈','Feedback'=>$val['Feedback']);
                }
            }
            else{
                $Result['Data2'] = '';
            }
        }
        elseif($CommonData['ProgressStatus'] == 3){
            //定稿数据
            $ConfirmInfo = $QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Status=2');
            if($ConfirmInfo){
                $Result['DeadCopyData'] = array('DeadCopyd_Date'=>date("Y-m-d H:i",$ConfirmInfo['ConfirmTime']),'DeadCopyd_FileName'=>$ConfirmInfo['DocumentName'],'DeadCopyd_DownUrl'=>FileURL.$ConfirmInfo['Document']);
                $Where = ' and ID <>'.$ConfirmInfo['ID'];
            }
            else{
                $Result['DeadCopyData'] = '';
                $Where = '';
            }
            //第一次上传的调查表信息
            $FirstInfo =$QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID asc');
            $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document']);
            $OneWhere = ' and ID <> '.$FirstInfo['ID'];
            $OtherInfo = $QuestionnaireModule->GetInfoByWhere(' and OrderID='.$OrderID.$Where.$OneWhere.' order by ID desc',true);
            if($OtherInfo){
                foreach($OtherInfo as $key => $val){
                    $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName'],'Question_DownUrl'=>FileURL.$val['Document'],'Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈','Feedback'=>$val['Feedback']);
                }
            }
            else{
                $Result['Data2'] = '';
            }
        }
        include template ( 'ConsultantManageItemQuestionnaire' );
    }

    /**
     * @desc  选校操作
     */
    public function ItemSchoolChoose(){
        $Nav = 'customer';
        $IsComplete = $_GET['IsComplete'];
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        //获取左侧客户数据
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);

        $OrderID = $_GET['OrderID'];
        $Type = $_GET['Type'];//点击其他服务时传递(未必有值)
        //获取公共信息
        $CommonData = $this->GetItemCommonInfo($OrderID,$Type,$IsComplete);
        $UserID = $CommonData['UserID'];


        //选校信息
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $ChooseSchoolModule = new StudyConsultantChooseSchoolModule();
        //是否存在旧的保存数据，如果没返回空数组(保存好的列表)
        $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and Type = 2 and OrderID='.$OrderID);
        $Result['ChooseSchoolStatus'] = $OrderConsultantInfo['Status'];

        if($OrderConsultantInfo['OtherInfos']){
            $Result['SchoolSelectionNewData'] = json_decode($OrderConsultantInfo['OtherInfos'],true);
        }
        else{
            $Result['SchoolSelectionNewData'] = '';
        }
        //是否存已经发送过给学生的学校，如果无返回空数组（已发送的列表）
        $ChooseInfo = $ChooseSchoolModule->GetInfoByWhere(' and OrderID = '.$OrderID,true);
        if($ChooseInfo){
            foreach($ChooseInfo as $key => $val){
                $Selection[$key]['SchoolName'] = $val['SchoolName'];
                $Selection[$key]['SchoolTime'] = $val['SchoolOpensTime'];
                $Selection[$key]['SchoolSystem'] = $val['EducationalSystem'];
                $Selection[$key]['LanguageRequirement'] = $val['LanguageRequirements'];
                $Selection[$key]['SchoolUrl'] = $val['Links'];
                if($val['Status'] == 1){
                    $Selection[$key]['Status'] = '确认中';
                }
                elseif($val['Status'] == 2){
                    $Selection[$key]['Status'] = '已确认';
                }
                elseif($val['Status'] == 3){
                    $Selection[$key]['Status'] = '已拒绝';
                }
                $Selection[$key]['Remark'] = $val['Describe'];
            }
            $Result['SchoolSelectionHistoryData'] = $Selection;
        }
        else{
            $Result['SchoolSelectionHistoryData'] = '';
        }
        include template ( 'ConsultantManageItemSchoolChoose' );
    }

    /**
     * @desc 文书服务
     */
    public function ItemDocument(){
        $Nav = 'customer';
        $IsComplete = $_GET['IsComplete'];
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        //获取左侧客户数据
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);

        $OrderID = $_GET['OrderID'];
        $Type = $_GET['Type']?$_GET['Type']:3;//点击其他服务时传递(未必有值)
        $DodumentType = $_GET['DType']?$_GET['DType']:1;
        //获取公共信息
        $CommonData = $this->GetItemCommonInfo($OrderID,$Type,$IsComplete);
        $UserID = $CommonData['UserID'];

        //文书数据
        $DocumemtModule = new StudyConsultantDocumentModule();
        //当前最后一条的文书表信息
        $LastInfo  = $DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type='.$DodumentType.' order by ID desc');

        if(empty($LastInfo)){
            $ProgressStatus = '1';    //代表文书表走到几步，1代表初始化，2代表对话处理中，3代表已定稿
        }
        else{
            $ProgressStatus = $LastInfo['Status'];    //代表调查表走到几步，1代表初始化，2代表对话处理中，3代表已定稿
            if($ProgressStatus == 2){ //处理中
                //第一次上传的文书表信息
                $FirstInfo =$DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type='.$DodumentType.' order by ID asc');
                if($FirstInfo){
                    $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document']);
                    $OneWhere = ' and ID <> '.$FirstInfo['ID'];
                }
                else{
                    $OneWhere = '';
                }
                if($FirstInfo['ID'] != $LastInfo['ID'] && $LastInfo){
                    //最后一次上传的文书表信息
                    $TwoWhere = ' and ID <> '.$LastInfo['ID'];
                    $FileName = $LastInfo['DocumentName']?$LastInfo['DocumentName']:'';
                    $File = $LastInfo['Document']?FileURL.$LastInfo['Document']:'';
                    $Result['Data'] = array('OperateID'=>$LastInfo['ID'],'QuestionnaireID'=>$LastInfo['ID'],'Question_Date'=>date("Y-m-d H:i",$LastInfo['AddTime']),'Question_FileName'=>$FileName,'Question_DownUrl'=>$File,'Question_Message'=>$LastInfo['Describe'],'Question_CoupleBackName'=>$LastInfo['Feedback']==1?'学生反馈':'我的反馈');
                }
                else{
                    $TwoWhere = '';
                }
                $OtherInfo = $DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.$OneWhere.$TwoWhere.' and Type='.$DodumentType.' order by ID desc',true);
                if($OtherInfo){
                    foreach($OtherInfo as $key => $val){
                        $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName']?$val['DocumentName']:'','Question_DownUrl'=>$val['Document']?FileURL.$val['Document']:'','Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈');
                    }
                }
                else{
                    $Result['Data2'] = '';
                }
            }
            elseif($ProgressStatus == 3){ //已定稿
                //定稿数据
                $ConfirmInfo = $DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type='.$DodumentType.' and Status=3');
                $Result['DeadCopyData'] = array('DeadCopyd_Date'=>date("Y-m-d H:i",$ConfirmInfo['ConfirmTime']),'DeadCopyd_FileName'=>$ConfirmInfo['DocumentName'],'DeadCopyd_DownUrl'=>FileURL.$ConfirmInfo['Document']);
                $Where = ' and ID <>'.$ConfirmInfo['ID'];
                //第一次上传的调查表信息
                $FirstInfo =$DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Type='.$DodumentType.' order by ID asc');
                $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document']);
                $OneWhere = ' and ID <> '.$FirstInfo['ID'];
                $OtherInfo = $DocumemtModule->GetInfoByWhere(' and OrderID='.$OrderID.$Where.$OneWhere.' and Type = '.$DodumentType.' order by ID desc',true);
                if($OtherInfo){
                    foreach($OtherInfo as $key => $val){
                        $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName'],'Question_DownUrl'=>FileURL.$val['Document'],'Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈');
                    }
                }
                else{
                    $Result['Data2'] = '';
                }
            }
        }
        $Result['ResultCode'] = 200;
        $Result['ProgressStatus'] = $ProgressStatus;
        include template ( 'ConsultantManageItemDocument' );
    }

    /**
     * @desc 学校申请
     */
    public function ItemSchoolApply(){
        $Nav = 'customer';
        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        $IsComplete = $_GET['IsComplete'];
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        //获取左侧客户数据
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);

        $OrderID = $_GET['OrderID'];
        $Type = $_GET['Type'];//点击其他服务时传递(未必有值)
        //获取公共信息
        $CommonData = $this->GetItemCommonInfo($OrderID,$Type,$IsComplete);
        $UserID = $CommonData['UserID'];


        //申请学校数据
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $SchoolEnrollModule = new StudyConsultantSchoolEnrollModule();
        $SchoolEnrollInfo = $SchoolEnrollModule->GetInfoByWhere(' and OrderID='.$OrderID);
        $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and Type = 4 and OrderID='.$OrderID);

        $Result['SchoolEnrollStatus'] = $OrderConsultantInfo['Status'];
        if(empty($SchoolEnrollInfo)){
            $SchoolEnrollID = $SchoolEnrollModule->InsertInfo(array('OrderID'=>$OrderID,'Status'=>1));
            $SchoolEnrollInfo = $SchoolEnrollModule->GetInfoByKeyID($SchoolEnrollID);
        }
        //申请院校数据
        $Result['SchoolApplyNewData'] = $SchoolEnrollInfo['ApplyData']?json_decode($SchoolEnrollInfo['ApplyData'],true):'';
        if ($SchoolEnrollInfo['SchoolName']){
            $Result['SchoolMajorName'] = $SchoolEnrollInfo['SchoolName'].'/'.$SchoolEnrollInfo['SpecialtyName'];
        }else{
            $Result['SchoolMajorName'] = '';
        }

        if($SchoolEnrollInfo['Status'] == 1){
            $Result['SchoolApplicationStatus'] = '确认中';
        }
        else{
            $Result['SchoolApplicationStatus'] = '已确认';
        }
        //录取院校数据
        $Result['EnrollSchoolHistoryData'] = $SchoolEnrollInfo['EnrollData']?json_decode($SchoolEnrollInfo['EnrollData'],true):'';
        //echo "<pre>";print_r($Result);exit;
        include template ( 'ConsultantManageItemSchoolApply' );
    }

    /**
     * @desc  签证
     */
    public function ItemVisa(){
        $Nav = 'customer';
        /*error_reporting(E_ALL);
         ini_set('display_errors', '1');*/
        $IsComplete = $_GET['IsComplete'];
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        //获取左侧客户数据
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);

        $OrderID = $_GET['OrderID'];
        $Type = $_GET['Type'];//点击其他服务时传递(未必有值)
        //获取公共信息
        $CommonData = $this->GetItemCommonInfo($OrderID,$Type,$IsComplete);
        $UserID = $CommonData['UserID'];

        //签证数据
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $VisaModule = new StudyConsultantTransactVisaModule();
        $Status = $VisaModule->Status;
        $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and Type = 5 and OrderID='.$OrderID);
        $Result['TransactVisaStatus'] = $OrderConsultantInfo['Status'];
        $VisaInfo = $VisaModule->GetInfoByWhere(' and OrderID = '.$OrderID);
        $Result['SubmitTime'] = $VisaInfo['SubmitVisaTime']?$VisaInfo['SubmitVisaTime']:''; //递交签证日期
        $Result['ResultTime'] = $VisaInfo['VisaEndTime']?$VisaInfo['VisaEndTime']:''; //签证结束日期
        $Result['VisaState'] = $VisaInfo['Country']?$VisaInfo['Country']:''; //签证国家
        $Result['AttendSchool'] = $VisaInfo['AdmissionSchool']?$VisaInfo['AdmissionSchool']:''; //入读学校
        $Result['EntranceTime'] = $VisaInfo['StartSchoolTime']?$VisaInfo['StartSchoolTime']:''; //入学时间
        $Result['Remark'] = $VisaInfo['Remarks']?$VisaInfo['Remarks']:''; //备注
        $Result['Status'] = $VisaInfo['Status']; //状态

        include template ( 'ConsultantManageItemVisa' );
    }

    /**
     * @desc  材料翻译
     */
    public function ItemTranslate(){
        $Nav = 'customer';
        $IsComplete = $_GET['IsComplete'];
        $MysqlWhere = ' and ConsultantID = '.$_SESSION['UserID'] .' and IsComplete = '.$IsComplete;
        //获取左侧客户数据
        $LeftData = $this->GetLeftData($MysqlWhere,$IsComplete);

        $OrderID = $_GET['OrderID'];
        $Type = $_GET['Type'];//点击其他服务时传递(未必有值)
        //获取公共信息
        $CommonData = $this->GetItemCommonInfo($OrderID,$Type,$IsComplete);
        $UserID = $CommonData['UserID'];

        $TranslateModule = new StudyConsultantTranslateModule();
        if($CommonData['ProgressStatus'] == 2){
            //第一次上传的调查表信息
            $FirstInfo =$TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID asc');
            $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document'],'UpQuestion_Message'=>$FirstInfo['Describe']);
            $OneWhere = ' and ID <> '.$FirstInfo['ID'];
            //当前最后一条的调查表信息
            $LastInfo = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.$OneWhere.' order by ID desc');
            if($LastInfo){
                $TwoWhere = ' and ID <> '.$LastInfo['ID'];
                $FileName = $LastInfo['DocumentName']?$LastInfo['DocumentName']:'';
                $File = $LastInfo['Document']?FileURL.$LastInfo['Document']:'';
                $Result['Data'] = array('OperateID'=>$LastInfo['ID'],'QuestionnaireID'=>$LastInfo['ID'],'Question_Date'=>date("Y-m-d H:i",$LastInfo['AddTime']),'Question_FileName'=>$FileName,'Question_DownUrl'=>$File,'Question_Message'=>$LastInfo['Describe'],'Question_CoupleBackName'=>$LastInfo['Feedback']==1?'学生提交':'我的反馈');
            }
            else{
                $Result['Data'] = '';
                $TwoWhere = '';
            }
            $OtherInfo = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.$OneWhere.$TwoWhere.' order by ID desc',true);
            if($OtherInfo){
                foreach($OtherInfo as $key => $val){
                    $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName']?$val['DocumentName']:'','Question_DownUrl'=>$val['Document']?FileURL.$val['Document']:'','Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生反馈':'我的反馈');
                }
            }
            else{
                $Result['Data2'] = '';
            }
        }
        elseif($CommonData['ProgressStatus'] == 3){
            //定稿数据
            $ConfirmInfo = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Status=2');
            if($ConfirmInfo){
                $Result['DeadCopyData'] = array('DeadCopyd_Date'=>date("Y-m-d H:i",$ConfirmInfo['ConfirmTime']),'DeadCopyd_FileName'=>$ConfirmInfo['DocumentName'],'DeadCopyd_DownUrl'=>FileURL.$ConfirmInfo['Document']);
                $Where = ' and ID <>'.$ConfirmInfo['ID'];
            }
            else{
                $Result['DeadCopyData'] = '';
                $Where = '';
            }
            //第一次上传的调查表信息
            $FirstInfo =$TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.' order by ID asc');
            $Result['UpQuestion'] = array('UpQuestion_Date'=>date("Y-m-d H:i",$FirstInfo['AddTime']),'UpQuestion_FileName'=>$FirstInfo['DocumentName'],'UpQuestion_DownUrl'=>FileURL.$FirstInfo['Document']);
            $OneWhere = ' and ID <> '.$FirstInfo['ID'];
            $OtherInfo = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.$Where.$OneWhere.' order by ID desc',true);
            if($OtherInfo){
                foreach($OtherInfo as $key => $val){
                    $Result['Data2'][$key] = array('Question_Date'=>date("Y-m-d H:i",$val['AddTime']),'Question_FileName'=>$val['DocumentName'],'Question_DownUrl'=>FileURL.$val['Document'],'Question_Message'=>$val['Describe'],'Question_CoupleBackName'=>$val['Feedback']==1?'学生提交':'我的反馈');
                }
            }
            else{
                $Result['Data2'] = '';
            }
        }
        //echo "<pre>";print_r($Result);exit;
        include template ( 'ConsultantManageItemTranslate' );
    }

    /**
     * @desc  获取公告信息
     * @param $OrderID 订单ID
     */
    private function GetItemCommonInfo($OrderID,$Type,$IsComplete){
        $OrderConsultantModule = new StudyOrderConsultantModule();
        //订单详情
        $OrderModule = new StudyOrderModule();
        $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
        //学生信息
        $UserID = $OrderInfo['UserID'];
        $UserInfoModule = new MemberUserInfoModule();
        $StudentInfo = $UserInfoModule->GetInfoByUserID($UserID);
        //顾问服务详情
        $ServiceModule = new StudyConsultantServiceModule();
        $ProductID = $OrderInfo['ProductID'];
        $ServiceInfo = $ServiceModule->GetInfoByKeyID($ProductID);
        $ServiceType = $ServiceInfo['ServiceType'];

        //订单流程详情(拼凑html代码)
        $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' Order by ID asc',true);
        $Part = $OrderConsultantModule->Part;

        $ResultData = $this->HandleOrderFlow($OrderConsultantInfo,$Part,$OrderID,$IsComplete,$Type);
        if($Type){
            $ResultType = $Type;
        }
        else{
            $ResultType = $ResultData['Type'];
        }
        //如果有存在调查表/文书管理的简历，查看调查表
        if($ResultType == 1){ //调查表
            $OrderConInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Type = 1');
            if($OrderConInfo && $OrderConInfo['Status'] != 0){
                $ProgressStatus = $OrderConInfo['Status'];
            }
        }
        elseif($ResultType == 3){ //文书服务
            $OrderConInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Type = 3');
            if($OrderConInfo && $OrderConInfo['Status'] != 0){
                $ProgressStatus = $OrderConInfo['Status'];
            }
        }
        elseif($ResultType == 6){
            $OrderConInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Type = 6');
            if($OrderConInfo && $OrderConInfo['Status'] != 0){
                $ProgressStatus = $OrderConInfo['Status'];
            }
        }
        else{
            $ProgressStatus = '';
        }
        $Result['Intention'] = $ServiceType;  //服务类型：全程服务，申请学校，文书管理， ，材料翻译，背景提升，签证指导
        $Result['Type'] = $ResultData['Type'];  //代表全程服务（其他服务类型也一样）第几步，从1开始计算
        $Result['ProgressTitle'] = $ResultData['Html'];    //拼凑html代码，上部分服务类型导航，第一个div内带上此订单对应的ID
        $Result['ProgressStatus'] = $ProgressStatus;    //代表调查表走到几步，1代表初始化，2代表对话处理中，3代表已定稿
        $Result['ClienteleName'] = $StudentInfo['RealName']?$StudentInfo['RealName']:$StudentInfo['NickName'];    //学生姓名
        $Result['UserID'] = $StudentInfo['UserID'];
        /*$Result['OrderInfo'] = $OrderInfo;       //订单信息
        $Result['StudentInfo'] = $StudentInfo;  //学生信息
        $Result['ServiceInfo'] = $ServiceInfo;  //服务信息*/
        return $Result;
    }

    /**
     * @desc  处理顾问订单流程
     * @param $OrderConsultantInfo  顾问订单信息
     * @param $Part                 流程详细步骤
     * @param $OrderID              订单ID
     * @param $ServiceType          服务类型：全程服务，申请学校，文书管理， ，材料翻译，背景提升，签证指导
     * @return array
     */
    private function HandleOrderFlow($OrderConsultantInfo,$Part,$OrderID,$IsComplete,$ServiceType){
        $Html = "<div class='serviceProcess' data-id='{$OrderID}'>";
        foreach($OrderConsultantInfo as $key => $val){
            if($val['Status'] == 0){
                $Carryout = '';
                $On = '';
                $DataType = '';
            }
            elseif($val['Status'] == 1){
                $Carryout = 'carryout';
                $On = '';
                $DataType = $Part[$val['Type']]['Headline'];
                $Type = $val['Type'];
            }
            elseif($val['Status'] == 2){
                $Carryout = 'carryout';
                $On = '';
                $DataType = $Part[$val['Type']]['Headline'];
                $Type = $val['Type'];
            }
            else{
                $Carryout = 'carryout';
                $On = '';
                $DataType = $Part[$val['Type']]['Headline'];
                $Type = $val['Type'];
            }
            if($val['Type'] == $ServiceType){
                $Carryout = 'carryout';
                $On = 'on';
                $DataType = $Part[$val['Type']]['Headline'];
                $Type = $val['Type'];
            }
            if($Carryout == 'carryout'){
                $Url = '/consultantmanage/itemallot/?ID='.$OrderID.'&Type='.$val['Type'].'&IsComplete='.$IsComplete;
            }
            else{
                $Url = 'javascript:void(0)';
            }
            if($key == 0){
                $first = 'first';
            }
            else{
                $first = '';
            }
            $Html .= "<a href='{$Url}' class='{$first} {$Carryout} {$On}' data-type='{$DataType}'>{$Part[$val['Type']]['Title']}</a><em></em>";
        }
        $Html .='</div>';
        $Result = array(
            'Html'=>$Html,
            'Type'=>$Type
        );
        return $Result;
    }








}
