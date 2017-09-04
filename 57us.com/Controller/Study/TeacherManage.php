<?php
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class TeacherManage extends CommonController{

    public function __construct(){
        $this->TeacherLoginStatus();
    }

    /**
     * @desc 教师中心--我的主页
     */
    public function MyCenter(){
         //获取用户基础信息
        $UserInfo = $this->MemberInfo();
        if(strpos("http://",$UserInfo['Avatar'])===false && $UserInfo['Avatar']){
            $UserInfo['Avatar']=LImageURL.$UserInfo['Avatar'];
        }
        else{
            $UserInfo['Avatar']=LImageURL.'/img/man3.0.png';
        }
        //获取教师审核资料信息
        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
        $TeacherInfo = $StudyTeacherInfoModule->GetInfoByWhere("and UserID={$_SESSION['UserID']}");
        $TeacherGrade = $StudyTeacherInfoModule->Grade;
        //获取课程数量
        $StudyTeacherCourseModule = new StudyTeacherCourseModule();
        $CourseAmount=$StudyTeacherCourseModule->GetListsNum("and UserID={$_SESSION['UserID']} and `Status`<>5");
        //获取订单的数量
        $StudyOrderModule = new StudyOrderModule();
        $OrderAmount=$StudyOrderModule->GetListsNum("and RelationID={$_SESSION['UserID']} and OrderType=2");
        //获取消息
        $Status=isset($_GET['S'])?intval($_GET['S']):1;
        $WaitProcess=isset($_GET['W_P'])?intval($_GET['W_P']):0;
        $Page=isset($_GET['Page'])?intval($_GET['Page']):1;
        $PageSize=6;
        $Data=$this->MemberMessageLists($_SESSION['UserID'],$Status,$WaitProcess,$Page,$PageSize);
        $Paging=new Page($Data['RecordCount'],$PageSize,2);
        $PageHTML=$Paging->showpage();
        $Nav='mycenter';        
        include template ( 'TeacherManageMyCenter' );
    }    
    
    
    /**
     * @desc 教师课程列表
     */
    public function CourseList(){
        $Status=isset($_GET['S'])?intval($_GET['S']):3;
        $Nav='courselist';
        include template ( 'TeacherManageCourseList' );
    }

    /**
     * @desc 教师添加课程
     */
    public function CourseAdd(){
        $CourseID=intval($_GET['ID']);
        if($CourseID){
            $StudyTeacherCourseModule = new StudyTeacherCourseModule();
            $CourseInfo=$StudyTeacherCourseModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and CourseID=$CourseID and `Status`<>5");
            $CourseInfo['CoursePackage']=json_decode($CourseInfo['CoursePackage'],true);
            $CourseInfo['Content']=StrReplaceImages($CourseInfo['Content']);
        }
        $Nav='courselist'; 
        include template ( 'TeacherManageCourseAdd' );
    }
    
    /**
     * @desc 顾问服务提交保存成功
     */
    public function SaveSuccess(){
        $CourseID=intval($_GET['ID']);
        if($CourseID){
            $StudyTeacherCourseModule = new StudyTeacherCourseModule();
            $CourseInfo=$StudyTeacherCourseModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and CourseID=$CourseID");
            if($CourseInfo){
                include template ( 'TeacherManageSaveSuccess' );
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
        $CourseID=intval($_GET['ID']);
        if($CourseID){
            $StudyTeacherCourseModule = new StudyTeacherCourseModule();
            $CourseInfo=$StudyTeacherCourseModule->GetInfoByWhere("and UserID={$_SESSION['UserID']} and CourseID=$CourseID");
            if($CourseInfo){
                include template ( 'TeacherManageUnderReview' );
            }else{
                alertandgotopage("异常的操作请求",WEB_STUDY_URL);
            }
                        
        }else{
            alertandgotopage("异常的操作请求",WEB_STUDY_URL);
        }
    }

    /**
     * @desc 教师中心--我的资产
     */
    public function Assets(){
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
            foreach ($Data['Data'] as $key=>$value){
                $AddTime = date("Y-m-d H:i:s", $value['AddTime']);
                $Data['Data'][$key]['AddTime'] = $AddTime;
            }
            $ClassPage = new Page($Rscount['Num'], $pageSize,2);
            $ShowPage = $ClassPage->showpage();
        }
        $SafeLeval=$this->MemberSafeLevel();
        $UserBank = $UserBankFlowModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID'].' ORDER BY AddTime DESC');
        $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        $Nav="assets";
        include template ( 'TeacherManageAssets' );
    }

    /**
     * @desc 教师成功案例
     */
    public function SuccessCase(){
        $Nav='successcase';
        include template ( 'TeacherManageSuccessCase' );
    }

    /**
     * @desc 教师添加成功案例
     */
    public function SuccessCaseAdd(){
        $Nav='successcase';
        include template ( 'TeacherManageSuccessCaseAdd' );
    }

    /**
     * @desc 教师个人设置
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
            header("Location:/teachermanage/approvemyinfo/");
        }else{
            $StudyTeacherInfoModule = new StudyTeacherInfoModule();
            $TeacherInfo = $StudyTeacherInfoModule->GetInfoByWhere(' and UserID = '.$UserID);

            if(strpos($UserInfo['CardPositive'],"http://")===false && $UserInfo['CardPositive']!=''){
                $UserInfo['CardPositive']=LImageURL.$UserInfo['CardPositive'];
            }
            $Tags = json_decode($TeacherInfo['Tags'],true);
            $PastExperience = json_decode($TeacherInfo['PastExperience'],true);
            $Nav="myinfosettings";        
            include template ( 'TeacherManageMyInfoSettings' );            
        } 
    }

    /**
     * @desc 教师个人设置--预览
     */
    public function MyInfoAuditView(){
        $UserID = $_SESSION['UserID'];
        $UserInfoModule = new MemberUserInfoModule();
        $StudyTeacherInfoModule = new StudyTeacherInfoModule();
        $TeacherInfo = $StudyTeacherInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
        $UserInfo = $UserInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
        if(strpos($UserInfo['CardPositive'],"http://")===false){
            $UserInfo['CardPositive']=LImageURL.$UserInfo['CardPositive'];
        }        
        $Tags = json_decode($TeacherInfo['Tags'],true);
        $PastExperience = json_decode($TeacherInfo['PastExperience'],true);        
        $Nav="myinfosettings";
        include template ( 'TeacherManageMyInfoAuditView' );
    }

    /**
     * @desc 教师审核通过个人信息设置
     */
    public function ApproveMyInfo(){
        $UserID = $_SESSION['UserID'];
        $UserInfoModule = new MemberUserInfoModule();
        $UserInfo = $UserInfoModule->GetInfoByWhere(' and UserID = '.$UserID);
        if($UserInfo['IdentityState']==2){
            $StudyTeacherInfoModule = new StudyTeacherInfoModule();
            $TeacherInfo = $StudyTeacherInfoModule->GetInfoByWhere(' and UserID = '.$UserID);

            if(strpos($UserInfo['CardPositive'],"http://")===false && $UserInfo['CardPositive']!=''){
                $UserInfo['CardPositive']=LImageURL.$UserInfo['CardPositive'];
            }        
            $Tags = json_decode($TeacherInfo['Tags'],true);
            $PastExperience = json_decode($TeacherInfo['PastExperience'],true);        
            $Nav="myinfosettings";
            include template ( 'TeacherManageApproveMyInfo' );            
        }else{
            header("Location:/teachermanage/myinfosettings/");
        }
        
    }

    /**
     * @desc 教师我的订单
     */
    public function MyOrder(){
        $Nav='myorder';
        include template ( 'TeacherManageMyOrder' );
    }

}
