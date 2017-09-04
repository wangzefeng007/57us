<?php

class CommonController {

    /**
     * @desc  判断用户是否登录
     */
    public function MemberLoginStatus(){

        if(!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])){
            alertandgotopage("请先登录",WEB_MEMBER_URL);
            exit;
        }  
    }

    /**
     * @desc   获取用户信息
     * @return array|int
     */
    public function MemberInfo(){
        $MemberUserInfoModule=new MemberUserInfoModule();
        $MemberInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        return $MemberInfo;
    }
    
    /**
     * @desc 判断顾问登录状态
     */
    public function ConsultantLoginStatus(){
        if(isset($_SESSION['UserID']) && $_SESSION['UserID']>0){
            $MemberInfo=$this->MemberInfo();
            if($MemberInfo['Identity']!=2){
                alertandgotopage("访问被拒绝", WEB_MEMBER_URL);
            }
        }else{
            alertandgotopage("请先登录",WEB_MEMBER_URL);
            exit;
        }
    }
    
    /**
     * @desc 判断老师登录状态
     */
    public function TeacherLoginStatus(){
        if(isset($_SESSION['UserID']) && $_SESSION['UserID']>0){
            $MemberInfo=$this->MemberInfo();
            if($MemberInfo['Identity']!=3){
                alertandgotopage("访问被拒绝", WEB_MEMBER_URL);
            }
        }else{
            alertandgotopage("请先登录",WEB_MEMBER_URL);
            exit;
        }
    }
    
    /**
     * @desc 判断学生登录状态
     */
    public function StudentLoginStatus(){
        if(isset($_SESSION['UserID']) && $_SESSION['UserID']>0){
            $MemberInfo=$this->MemberInfo();
            if($MemberInfo['Identity']!=1){
                alertandgotopage("访问被拒绝", WEB_MEMBER_URL);
            }
        }else{
            alertandgotopage("请先登录",WEB_MEMBER_URL);
            exit;
        }
    }
 
    /**
     * @desc 判断安全等级
     */
    public function MemberSafeLevel(){
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
        $SafeMap=array('1'=>'警告','2'=>'中等','3'=>'安全');
        return $SafeMap[$SafeLevel];
    }

    /**
     * @desc  获取用户消息
     * @param $UserID
     * @param int $Status
     * @param int $Sign
     * @param int $Page
     * @param int $PageSize
     * @return array|bool
     */
    public function MemberMessageLists($UserID,$Status=0,$Sign=0,$Page=1,$PageSize=6){
        $MessageSendModule = new MemberMessageSendModule();
        $MysqlWhere="and UserID=$UserID and `Status`=$Status";
        if($Sign){
            $MysqlWhere.=" and Sign=$Sign";
        }
        $Rscount = $MessageSendModule->GetListsNum($MysqlWhere);
        if ($Page < 1) {
            $Page = 1;
        }
        $Data = false;
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            $Data['Data'] = $MessageSendModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            $MemberUserInfoModule = new MemberUserInfoModule();
            foreach($Data['Data'] as $Key=>$Val){
                if($Val['SendUserID'] == 0){
                    $Data['Data'][$Key]['Avatar']= LImageURL.'/img/man3.0.png';
                    $Data['Data'][$Key]['NickName'] = '系统';
                }
                else{
                    $MemberInfo=$MemberUserInfoModule->GetInfoByUserID($Val['SendUserID']);
                    if (strpos($MemberInfo['Avatar'], 'http://') === false && $MemberInfo['Avatar'] != '') {
                        $MemberInfo['Avatar'] = LImageURL . $MemberInfo['Avatar'];
                    }
                    $Data['Data'][$Key]['Avatar']=$MemberInfo['Avatar'];
                    $Data['Data'][$Key]['NickName']=$MemberInfo['NickName'];
                }
                $MessageInfoModule =new MemberMessageInfoModule();
                $MessageInfo = $MessageInfoModule->GetInfoByKeyID($Val['MessageID']);
                $Data['Data'][$Key]['Title']=$MessageInfo['Title'];
                $Data['Data'][$Key]['SendTime']=$MessageInfo['SendTime'];
            }
            MultiPage($Data,5);
        }
        return $Data;
    }

    /**
     * @desc 获取消息内容
     */
    public function GetMessageContent(){
        $MessageSendModule = new MemberMessageSendModule();
        $MessageSendInfo = $MessageSendModule->GetInfoByKeyID($_POST['ID']);
        $MessageInfoModule = new MemberMessageInfoModule();
        $MessageInfo = $MessageInfoModule->GetInfoByKeyID($MessageSendInfo['MessageID']);
        $json_result = array('ResultCode'=>200,'Date'=>date("Y-m-d H:i:s",$MessageInfo['SendTime']),'Name'=>$_SESSION['NickName'],'Title'=>$MessageInfo['Title'],'Message'=>$MessageInfo['Content']);
        echo json_encode($json_result);
    }

    /**
     * @desc 获取用户消息详情
     */            
    /*public function MessageInfo(){
        $ID=intval($_POST['ID']);
        $StudyMemberMessageModule=new StudyMemberMessageModule();
        $MessageInfo=$StudyMemberMessageModule->GetInfoByWhere("and MessageID=$ID and UserID={$_SESSION['UserID']}");
        if($MessageInfo){
            $Data['ResultCode']=200;
            $Data['Date']=date('Y-m-d H:i:s',$MessageInfo['AddTime']);
            $Data['Name']=$_SESSION['NickName'];
            $Data['Info']=$MessageInfo['Content'];
            if($MessageInfo['AssociatedID']){
                
            }else{
                $Data['Url']=$MessageInfo['URL'];
            }
        }else{
            $Data['ResultCode']=100;
            $Data['Message']='该信息不存在!';
        }
        echo json_encode($Data);
    }*/
    
    /**
     * @desc 身份选择
     */
    public function IdentitySelection(){
        include_once SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        $this->MemberLoginStatus();
        $MemberUserInfoModule=new MemberUserInfoModule();
        $MemberInfo=$MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
        if($MemberInfo['Identity']>0){
            if($MemberInfo['Identity']==1){
                header('Location:'.WEB_STUDY_URL.'/studentmanage/myorder/');
            }elseif($MemberInfo['Identity']==2){
                header('Location:'.WEB_STUDY_URL.'/consultantmanage/mycenter/');
            }elseif($MemberInfo['Identity']==3){
                header('Location:'.WEB_STUDY_URL.'/teachermanage/mycenter/');
            }
        }else{
            include template('CommonControllerIdentitySelection');
        }
    }

    /**
     * @desc 判断顾问是否在顾问基本表上有信息
     */
    public function JudgeHaveConsultantInfo(){
        include_once SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyConsultantInfoModule.php';
        $ConsultantInfoModule = new StudyConsultantInfoModule();
        $Info = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
        if(empty($Info)){
            $Data = array('UserID'=>$_SESSION['UserID'],'Grade'=>1);
            $ConsultantInfoModule->InsertInfo($Data);
        }
    }
    
    /**
     * @desc 跳转到对应消息列表
     */
    public function RedirectMessagePage(){
        $MemberInfo=$this->MemberInfo();
        if($MemberInfo['Identity']==1){
            header('Location:'.WEB_STUDY_URL.'/studentmanage/messages/');
        }elseif($MemberInfo['Identity']==2){
            header('Location:'.WEB_STUDY_URL.'/consultantmanage/mycenter/');
        }elseif($MemberInfo['Identity']==3){
            header('Location:'.WEB_STUDY_URL.'/teachermanage/mycenter/');
        }
    }
}
