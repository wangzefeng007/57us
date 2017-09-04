<?php

/**
 * @desc  会员中心留学模块ajax
 */
include_once SYSTEM_ROOTPATH.'/Controller/Study/CommonController.php';
class AjaxStudy extends CommonController{

    public function __construct(){
        $this->StudentLoginStatus();
    }

    public function Index(){
        $Intention = trim($_POST ['Intention']);
        unset($_POST ['Intention']);
        if ($Intention == '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            echo $json_result;
            exit;
        }
        $this->$Intention ();
    }

    /**
     * @desc  学生调查表反馈ajax
     */
    public function StudentQuestion(){
        $QuestionnaireModule = new StudyConsultantQuestionnaireModule();
        if ($_POST){
            $Document = $_POST['FileData']; //文件（进制数据）
            $Data['OrderID'] = intval($_POST['ID']);
            $Data['DocumentName'] = trim($_POST['FileName']); //文件名称
            $Data['Describe'] = trim($_POST['Message']); //反馈描述
            $Data['AddTime'] = time(); //添加时间
            $Data['Status'] = 1; //状态服务中
            $Data['Feedback'] = 1; //学生

            //处理上传文件
            include_once SYSTEM_ROOTPATH.'/Service/Common/Class.ToolService.php';
            $DocumentFile = ToolService::HandleUploadFile('study',$Document);
            $Data['Document'] = $DocumentFile;

            $Questionnaire = $QuestionnaireModule->GetInfoByWhere(' and OrderID = '.$Data['OrderID'].' order by ID desc');
            if ($Questionnaire['Feedback']==1){
                $Result = $QuestionnaireModule->UpdateInfoByKeyID($Data,$Questionnaire['ID']);
            }else{
                $Result = $QuestionnaireModule->InsertInfo($Data);
            }
            if($Result){
                $result_json = array('ResultCode'=>200,'Message'=>'发送成功','DownUrl'=>FileURL.$Data['Document']);
            }
            else{
                $result_json = array('ResultCode'=>101,'Message'=>'发送失败');
            }
            echo json_encode($result_json);exit;
        }
    }
    /**
     * @desc  学生选校定校
     */
    public function StudentChoseSchool(){
        $ChooseSchoolModule = new StudyConsultantChooseSchoolModule();
        $ID = intval($_POST['ID']);
        if ($_POST){
            $Type = intval($_POST['Type']);
            if ($Type==1){
                $Data['Status'] = 2;
                $Result = $ChooseSchoolModule->UpdateInfoByKeyID($Data,$ID);
                if($Result){
                    $result_json['ResultCode'] = 200;
                    $result_json['Message'] = '确认成功';
                }
                else{
                    $result_json['ResultCode'] = 101;
                    $result_json['Message'] = '确认失败';
                }
            }elseif ($Type==0){
                $Data['Status'] = 3;
                $Result = $ChooseSchoolModule->UpdateInfoByKeyID($Data,$ID);
                if($Result){
                    $result_json['ResultCode'] = 200;
                    $result_json['Message'] = '驳回成功';
                }
                else{
                    $result_json['ResultCode'] = 101;
                    $result_json['Message'] = '驳回失败';
                }
            }
            echo json_encode($result_json);exit;
        }
    }

    /**
     * @desc 学生确认选校定校（如果是全程服务或者学校申请）
     */
    public function StudentSureSchool(){
        $OrderID = intval($_POST['ID']);
        $ChooseSchoolModule = new StudyConsultantChooseSchoolModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();
        $ConsultantModule = new StudyOrderConsultantModule();

        //判断是否已确认院校
        $ChooseSchoolInfo = $ChooseSchoolModule->GetInfoByWhere(' and Status = 2 and OrderID = '.$OrderID);
        if ($ChooseSchoolInfo ===false){
            $result_json = array('ResultCode'=>101,'Message'=>'未确认院校，不可定稿');
            echo json_encode($result_json);
            exit;
        }
        //订单详情
        $OrderModule = new StudyOrderModule();
        $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
        $UserID = $OrderInfo['UserID'];
        //订单顾问ID
        $ConsultantID = $OrderInfo['RelationID'];
        //订单流程表，选校定校信息
        $OrderConsultantInfo = $OrderConsultantModule->GetInfoByWhere(' and OrderID='.$OrderID.' and UserID='.$_SESSION['UserID'].' and Type=2');
        $ConfirmTime = time();
        if($OrderConsultantInfo){
            //开启事务
            global $DB;
            $DB->query("BEGIN");//开始事务定义
            $Result = $OrderConsultantModule->UpdateInfoByKeyID(array('Status'=>3,'ConfirmTime'=>$ConfirmTime),$OrderConsultantInfo['ID']);
            if($Result){
                //判断是否有下一个服务
                $IsNextService = $OrderConsultantModule->GetInfoByWhere(' and OrderID ='.$OrderID.' and Status=0 order by ID asc');
                if($IsNextService){
                    //更新下一条服务的状态为初始化（1）
                    $Result1 = $OrderConsultantModule->UpdateInfoByKeyID(array('Status'=>1),$IsNextService['ID']);
                    if($Result1){
                        $DB->query("COMMIT");//执行事务
                        $result_json = array('ResultCode'=>200,'Message'=>'确认成功,开始下一个服务');
                    }else{ //下一个服务状态更新失败，回滚
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $result_json = array('ResultCode'=>103,'Message'=>'确认失败','Describe'=>'下一个服务状态更新失败，回滚');
                    }
                }
                else{ //没有下一个服务，订单结束，更改状态，结算资金
                    //更新订单状态
                    $Result2 = $OrderModule->UpdateInfoByKeyID(array('Status'=>3),$OrderID);
                    if($Result2){
                        //添加订单操作日志
                        $Result3 = $this->OrderLogOperate($OrderInfo['OrderNum'],2,3,$OrderInfo['OrderName']);
                        if($Result3['ResultCode'] == 200){
                            //查询订单是否有为完成的
                            $this->JudgeUserIsComplete($UserID,$ConsultantID);
                            //资金及日志操作
                            $ConsultantInfoModule = new StudyConsultantInfoModule();
                            $Scale = $ConsultantInfoModule->Scale;
                            $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$ConsultantID);
                            //直接全款*顾问应获比例
                            $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']];//操作金额,当前服务的订单部分金额
                            //更新订单流程表结算金额
                            $ConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 2');
                            $result_json = $this->AmountOperate($OrderInfo['RelationID'],$Amt,$OrderInfo['OrderName'].'-选校定校服务');
                            if($result_json['ResultCode'] == 200){
                                $DB->query("COMMIT");//执行事务
                            }
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $result_json = array('ResultCode'=>103,'Message'=>'确认失败','Describe'=>'订单操作日志添加失败');
                        }
                    }
                    else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $result_json = array('ResultCode'=>102,'Message'=>'确认失败','Describe'=>'Order表订单状态更新失败');
                    }
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>102,'Message'=>'确认失败');
            }
        }
        else{
            $result_json = array('ResultCode'=>101,'Message'=>'订单数据错误');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  学生文书材料
     */
    public function  StudentInstruments(){
        $DocumentModule = new StudyConsultantDocumentModule();
        if ($_POST){
            $Document = $_POST['FileData']; //文件（进制数据）
            $Data['OrderID'] = intval($_POST['ID']);
            $Data['DocumentName'] = trim($_POST['FileName']); //文件名称
            $Data['Describe'] = trim($_POST['Message']); //反馈描述
            $Data['AddTime'] = time(); //添加时间
            $Data['Status'] = 2; //状态服务中
            $Data['Feedback'] = 1; //学生
            $Data['Type'] = intval($_POST['Type']); //类型

            //处理上传文件
            include_once SYSTEM_ROOTPATH.'/Service/Common/Class.ToolService.php';
            $DocumentFile = ToolService::HandleUploadFile('study',$Document);
            $Data['Document'] = $DocumentFile;

            $Questionnaire = $DocumentModule->GetInfoByWhere(' and Type = '.$Data['Type'].' and OrderID = '.$Data['OrderID'].' order by AddTime desc');
            if ($Questionnaire['Feedback']==1){
                $Result = $DocumentModule->UpdateInfoByKeyID($Data,$Questionnaire['ID']);
            }else{
                $Result = $DocumentModule->InsertInfo($Data);
            }
            if($Result){
                $result_json['ResultCode'] = 200;
                $result_json['Message'] = '发送成功';
                $result_json['DownUrl'] = FileURL.$Data['Document'];
            }
            else{
                $result_json['ResultCode'] = 101;
                $result_json['Message'] = '发送失败';
            }
            echo json_encode($result_json);exit;
        }
    }

    /**
     * @desc  顾问服务，文书确认定稿
     */
    public function StudentSureInstruments(){
        $Type = intval($_POST['Type']); //文书类型
        $OrderID = intval($_POST['ID']);//订单ID
        $DocumentModule = new StudyConsultantDocumentModule();
        $ConsultantModule = new StudyOrderConsultantModule();

        //订单详细信息
        $OrderModule = new StudyOrderModule();
        $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
        $UserID = $OrderInfo['UserID'];
        //服务详情
        $ProductInfo = $this->GetProductInfo($OrderInfo['ProductID']);
        $ConsultantID = $OrderInfo['RelationID'];

        $ConfirmTime = time();
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        $DocumentInfo = $DocumentModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Type ='.$Type.' order by ID desc');

        if(empty($DocumentInfo['Document'])){
            $HasDocumentInfo = $DocumentModule->GetInfoByWhere(' and (`Document` is not null and `Document`<>"") and Feedback = 2 and OrderID ='.$OrderID.' order by ID desc');
            $Result = $DocumentModule->UpdateInfoByKeyID(array('Status'=>3,'ConfirmTime'=>$ConfirmTime,'Document'=>$HasDocumentInfo['Document'],'DocumentName'=>$HasDocumentInfo['DocumentName']),$DocumentInfo['ID']);
            if(!$Result){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>'111','Message'=>'定稿失败','Describe'=>'更新文书表定稿失败');
            }
            $DocumentInfo['Document'] = $HasDocumentInfo['Document'];
            $DocumentInfo['DocumentName'] = $HasDocumentInfo['DocumentName'];
        }
        else{
            $Result = $DocumentModule->UpdateInfoByKeyID(array('Status'=>3,'ConfirmTime'=>$ConfirmTime),$DocumentInfo['ID']);
        }
        if($Result){
            //*判断其他文书是否完成*
            $Array = array(1,2,3,4);
            $Complete = '';
            foreach($Array as $key=>$val){
                if($val != $Type){
                    $ResultComplete = $DocumentModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and Status = 3 and Type = '.$val);
                    if(empty($ResultComplete)){    //还有待完成的文书服务
                        $Complete = 1;
                        break;
                    }
                    else{
                        $Complete = 0;
                    }
                }
                else{
                    continue;
                }
            }
            if($Complete == 0){ //没有待完成的文书服务，判断是否还有其他服务，如果有，则将下个服务状态改成初始化，否则结束订单
                //更新订单流程表，文书类型状态为完成
                $Result1 = $ConsultantModule->UpdateInfoByWhere(array('Status'=>3,'ConfirmTime'=>$ConfirmTime),' OrderID = '.$OrderID.' and Type=3');
                if($Result1){
                    if($ProductInfo['ServiceType'] == 1 || $ProductInfo['ServiceType'] == 2){ //有下一个服务，把状态改成初始化，'Status'=>1
                        //下一个服务详细信息
                        $NextFlow = $ConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and status = 0 order by ID asc');
                        $Result2  = $ConsultantModule->UpdateInfoByKeyID(array('Status'=>1),$NextFlow['ID']);
                        if($Result2){
                            //资金及日志操作
                            $ConsultantInfoModule = new StudyConsultantInfoModule();
                            $Scale = $ConsultantInfoModule->Scale;
                            $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$ConsultantID);
                            //服务所占比例
                            $ServiceScale = $ProductInfo['ServiceType'] == 1?$ConsultantModule->AllLifeService[3]:$ConsultantModule->SchoolApply[3];
                            //直接全款*顾问应获比例
                            $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']]*$ServiceScale;//操作金额,当前服务的订单部分金额
                            //更新订单流程表结算金额
                            $ConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 3');
                            $result_json = $this->AmountOperate($ConsultantID,$Amt,$OrderInfo['OrderName'].'-文书服务',$DB);
                            if($result_json['ResultCode'] == 200){
                                $DB->query("COMMIT");//执行事务
                                $result_json = array('ResultCode'=>'200','Message'=>'定稿成功,文书服务完成','DownUrl'=>FileURL.$DocumentInfo['Document'],'DeadCopy_FileName'=>$DocumentInfo['DocumentName'],'DeadCopy_Date'=>$ConfirmTime);
                            }
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $result_json = array('ResultCode'=>'102','Message'=>'定稿失败','Describe'=>'初始化下一个流程失败');
                        }
                    }
                    elseif($ProductInfo['ServiceType'] == 3){ //没有下一个服务，订单完成
                        //更新订单表状态为完成
                        $Result3 = $OrderModule->UpdateInfoByKeyID(array('Status'=>3),$OrderID);
                        if($Result3){
                            //添加订单操作日志
                            $Result3 = $this->OrderLogOperate($OrderInfo['OrderNum'],2,3,$OrderInfo['OrderName']);
                            if($Result3['ResultCode'] == 200){
                                //资金及日志操作
                                $ConsultantInfoModule = new StudyConsultantInfoModule();
                                $Scale = $ConsultantInfoModule->Scale;
                                $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$ConsultantID);
                                //直接全款*顾问应获比例
                                $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']];//操作金额,当前服务的订单部分金额
                                //更新订单流程表结算金额
                                $ConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 3');

                                $result_json = $this->AmountOperate($OrderInfo['RelationID'],$Amt,$OrderInfo['OrderName'].'-文书服务');
                                if($result_json['ResultCode'] == 200){
                                    //查询订单是否有为完成的
                                    $this->JudgeUserIsComplete($UserID,$ConsultantID);
                                    $DB->query("COMMIT");//执行事务
                                    $result_json = array('ResultCode'=>'200','Message'=>'定稿成功,文书服务完成','DownUrl'=>FileURL.$DocumentInfo['Document'],'DeadCopy_FileName'=>$DocumentInfo['DocumentName'],'DeadCopy_Date'=>$ConfirmTime);
                                }
                            }
                            else{
                                $DB->query("ROLLBACK");//判断当执行失败时回滚
                                $result_json = array('ResultCode'=>103,'Message'=>'确认失败','Describe'=>'订单操作日志添加失败');
                            }
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $result_json = array('ResultCode'=>'102','Message'=>'定稿失败，更新订单状态失败');
                        }
                    }
                }
                else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $result_json = array('ResultCode'=>'101','Message'=>'定稿失败，更新订单流程状态失败',);
                }
            }
            else{ //还有下一个文书服务
                $DB->query("COMMIT");//执行事务
                $result_json = array('ResultCode'=>'200','Message'=>'定稿成功','DownUrl'=>FileURL.$DocumentInfo['Document'],'DeadCopy_FileName'=>$DocumentInfo['DocumentName'],'DeadCopy_Date'=>$ConfirmTime);
            }
        }
        else{
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            $result_json = array('ResultCode'=>'101','Message'=>'定稿失败,更新文书状态失败',);
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  学生学校申请
     */
    public function StudentApplySchool(){
        $SchoolEnrollModule = new StudyConsultantSchoolEnrollModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();

        $OrderID =  intval($_POST['OrderID']);

        //订单详细信息
        $OrderModule = new StudyOrderModule();
        $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
        $UserID = $OrderInfo['UserID'];
        //服务详情
        $ProductInfo = $this->GetProductInfo($OrderInfo['ProductID']);
        $ConsultantID = $OrderInfo['RelationID'];

        $ID =  intval($_POST['ID']);
        $SchoolEnroll = $SchoolEnrollModule->GetInfoByWhere(' and OrderID = '.$OrderID);
        $EnrollData = json_decode($SchoolEnroll['EnrollData'],true);
        $Data['Status'] =2;
        $Data['SchoolName'] = $EnrollData[$ID]['AttendSchool'];
        $Data['SpecialtyName'] = $EnrollData[$ID]['AttendMajor'];
        $Data['ConfirmTime'] =time();

        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        //更新文书表状态为完成
        $Result = $SchoolEnrollModule->UpdateInfoByWhere($Data,' OrderID ='.$OrderID);
        if($Result){
            //更新订单流程表的学校申请状态为3
            $Result1 = $OrderConsultantModule->UpdateInfoByWhere(array('Status'=>3,'ConfirmTime'=>$Data['ConfirmTime']),' OrderID = '.$OrderID .' and Type = 4');
            if($Result1){
                if($ProductInfo['ServiceType'] == 1){  //全程服务，还有下一个服务
                    $NextFlow = $OrderConsultantModule->GetInfoByWhere(' and OrderID = '.$OrderID.' and status = 0 order by ID asc');
                    $Result2  = $OrderConsultantModule->UpdateInfoByKeyID(array('Status'=>1),$NextFlow['ID']);
                    if($Result2){
                        //资金及日志操作
                        $ConsultantInfoModule = new StudyConsultantInfoModule();
                        $Scale = $ConsultantInfoModule->Scale;
                        $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$ConsultantID);
                        //服务所占比例
                        $ServiceScale = $OrderConsultantModule->AllLifeService[4];
                        //直接全款*顾问应获比例
                        $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']]*$ServiceScale;//操作金额,当前服务的订单部分金额
                        //更新订单流程表结算金额
                        $OrderConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 4');

                        $result_json = $this->AmountOperate($ConsultantID,$Amt,$OrderInfo['OrderName'].'-学校申请服务',$DB);
                        if($result_json['ResultCode'] == 200){
                            $DB->query("COMMIT");//执行事务
                            $result_json = array('ResultCode'=>'200','Message'=>'定稿成功,申请学校服务完成');
                        }
                    }
                    else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $result_json = array('ResultCode'=>103,'Message'=>'申请学校确认失败','Describe'=>'更新下一个订单流程表状态失败');
                    }
                }
                elseif($ProductInfo['ServiceType'] == 2){  //学校申请，订单完成，没有下一个服务
                    //更改订单表状态
                    $Result3 = $OrderModule->UpdateInfoByKeyID(array('Status'=>3),$OrderID);
                    if($Result3){
                        //添加订单操作日志
                        $Result4 = $this->OrderLogOperate($OrderInfo['OrderNum'],2,3,$OrderInfo['OrderName']);
                        if($Result4['ResultCode'] == 200){
                            //资金及日志操作
                            $ConsultantInfoModule = new StudyConsultantInfoModule();
                            $Scale = $ConsultantInfoModule->Scale;
                            $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$ConsultantID);
                            //服务所占比例
                            $ServiceScale = $OrderConsultantModule->SchoolApply[4];
                            //直接全款*顾问应获比例
                            $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']]*$ServiceScale;//操作金额,当前服务的订单部分金额
                            //更新订单流程表结算金额

                            $OrderConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 4');

                            $result_json = $this->AmountOperate($ConsultantID,$Amt,$OrderInfo['OrderName'].'-申请学校服务',$DB);
                            if($result_json['ResultCode'] == 200){
                                //查询订单是否有为完成的
                                $this->JudgeUserIsComplete($UserID,$ConsultantID);
                                $DB->query("COMMIT");//执行事务
                                $result_json = array('ResultCode'=>'200','Message'=>'申请学校确认成功,文书服务完成');
                            }
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $result_json = array('ResultCode'=>104,'Message'=>'确认失败','Describe'=>'订单操作日志添加失败');
                        }
                    }
                    else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $result_json = array('ResultCode'=>103,'Message'=>'确认失败','Describe'=>'订单表状态更新失败');
                    }
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>102,'Message'=>'选校失败','Describe'=>'更新订单流程表状态失败');
            }
        }
        else{
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            $result_json = array('ResultCode'=>101,'Message'=>'选校失败');
        }
        echo json_encode($result_json);exit;
    }

    /**
     * @desc  办理签证确认
     */
    public function StudentVisa(){

        $OrderConsultantModule = new StudyOrderConsultantModule();
        $OrderID = intval($_POST['OrderID']);

        //订单详细信息
        $OrderModule = new StudyOrderModule();
        $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
        $UserID = $OrderInfo['UserID'];
        //服务详情
        $ServiceInfo = $this->GetProductInfo($OrderInfo['ProductID']);
        $ConsultantID = $OrderInfo['RelationID'];
        $ConfirmTime = time();


        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        //更改订单流程表
        $Result = $OrderConsultantModule->UpdateInfoByWhere(array('Status'=>3,'ConfirmTime'=>$ConfirmTime),' OrderID = '.$OrderID.' and Type=5');
        if($Result){
            //更新订单表状态为完成
            $Result1 = $OrderModule->UpdateInfoByKeyID(array('Status'=>3),$OrderID);
            if($Result1){
                //添加订单操作日志
                $Result3 = $this->OrderLogOperate($OrderInfo['OrderNum'],2,3,$OrderInfo['OrderName']);
                if($Result3['ResultCode'] == 200){
                    if($ServiceInfo['ServiceType'] == 1){  //全程服务
                        //资金及日志操作
                        $ConsultantInfoModule = new StudyConsultantInfoModule();
                        $Scale = $ConsultantInfoModule->Scale;
                        $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$ConsultantID);
                        //服务所占比例
                        $ServiceScale = $OrderConsultantModule->AllLifeService[5];
                        //直接全款*顾问应获比例*服务占订单的比例
                        $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']]*$ServiceScale;//操作金额,当前服务的订单部分金额
                        //更新订单流程表结算金额
                        $OrderConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 5');

                        $result_json = $this->AmountOperate($ConsultantID,$Amt,$OrderInfo['OrderName'].'-签证服务',$DB);
                        if($result_json['ResultCode'] == 200){
                            //查询订单是否有为完成的
                            $this->JudgeUserIsComplete($UserID,$ConsultantID);
                            $DB->query("COMMIT");//执行事务
                            $result_json = array('ResultCode'=>'200','Message'=>'签证确认成功,订单完成');
                        }
                    }
                    elseif($ServiceInfo['ServiceType'] == 5){ //签证指导（单单签证）
                        //添加订单操作日志
                        $Result3 = $this->OrderLogOperate($OrderInfo['OrderNum'],2,3,$OrderInfo['OrderName']);
                        if($Result3['ResultCode'] == 200){
                            //资金及日志操作
                            $ConsultantInfoModule = new StudyConsultantInfoModule();
                            $Scale = $ConsultantInfoModule->Scale;
                            $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$ConsultantID);
                            //直接全款*顾问应获比例
                            $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']];//操作金额,当前服务的订单部分金额
                            //更新订单流程表结算金额
                            $OrderConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 5');

                            $result_json = $this->AmountOperate($ConsultantID,$Amt,$OrderInfo['OrderName'].'-材料翻译服务',$DB);
                            if($result_json['ResultCode'] == 200){
                                //查询订单是否有为完成的
                                $this->JudgeUserIsComplete($UserID,$ConsultantID);
                                $DB->query("COMMIT");//执行事务
                                $result_json = array('ResultCode'=>'200','Message'=>'签证确认成功,订单完成');
                            }
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $result_json = array('ResultCode'=>103,'Message'=>'确认失败','Describe'=>'订单操作日志添加失败');
                        }
                    }
                }
                else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $result_json = array('ResultCode'=>103,'Message'=>'确认失败','Describe'=>'订单操作日志添加失败');
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>101,'Message'=>'确认失败','Describe'=>'订单状态失败');
            }
        }
        else{
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            $result_json = array('ResultCode'=>101,'Message'=>'确认失败');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  判断该用户与该顾问是否还存在未完成的服务
     */
    public function JudgeUserIsComplete($UserID,$ConsultantID){
        $StudyOrderModule = new StudyOrderModule();
        $Result = $StudyOrderModule->GetInfoByWhere(' and UserID = '.$UserID.' and RelationID = '.$ConsultantID.' and Status = 2');
        if(!$Result){
            $ConsultantStudentInfoModule = new StudyConsultantStudentInfoModule();
            $ConsultantStudentInfoModule->UpdateInfoByWhere(array('IsComplete'=>2),' StudentID ='.$UserID.' and ConsultantID ='.$ConsultantID);
        }
    }

    /**
     * @desc  材料翻译
     */
    public function StudentTranslation(){
        $OrderID = intval($_POST['OrderID']);
        $TranslateModule = new StudyConsultantTranslateModule();
        $OrderConsultantModule = new StudyOrderConsultantModule();

        $Data['Feedback'] = 1;
        $Document = $_POST['FileData'];
        //处理上传文件
        include SYSTEM_ROOTPATH.'/Service/Common/Class.ToolService.php';
        $DocumentFile = ToolService::HandleUploadFile('study',$Document);
        $Data['Document'] = $DocumentFile;
        $Data['OrderID'] = $OrderID;
        $Data['DocumentName'] = $_POST['FileName'];
        $Data['Describe'] = $_POST['Message'];
        $Data['Feedback'] = 1;
        $Data['Status'] = 1;
        $Data['AddTime'] = time();
        $MyTranslates = $TranslateModule->GetInfoByWhere(' and OrderID='.$OrderID.' and Feedback = 1');
        if($MyTranslates){ //不是第一条信息,直接添加
            $Result = $TranslateModule->InsertInfo($Data);
            if($Result){
                $result_json = array('ResultCode'=>200,'Message'=>'提交成功');
            }else{
                $result_json = array('ResultCode'=>101,'Message'=>'提交失败');
            }
        }else { //是第一条信息,直接添加，更改翻译状态为进行中
            //开启事务
            global $DB;
            $DB->query("BEGIN");//开始事务定义
            $Result = $TranslateModule->InsertInfo($Data);
            if($Result){
                $Result1 = $OrderConsultantModule->UpdateInfoByWhere(array('Status'=>2),' OrderID = '.$OrderID.' and Type=6');
                if($Result1){
                    $DB->query("COMMIT");//执行事务
                    $result_json = array('ResultCode'=>'200','Message'=>'提交成功');
                }
                else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $result_json = array('ResultCode'=>101,'Message'=>'提交失败');
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>102,'Message'=>'提交失败');
            }
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  确认材料翻译
     */
    public function StudentSureTranslation(){
        $TranslateModule = new StudyConsultantTranslateModule();

        $OrderID = intval($_POST['ID']);
        $OrderModule = new StudyOrderModule();
        $OrderInfo = $OrderModule->GetInfoByKeyID($OrderID);
        $ConsultantID = $OrderInfo['RelationID'];

        $ConfirmTime = time();
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        $TranslateInfo = $TranslateModule->GetInfoByWhere(' and Feedback = 2 and OrderID ='.$OrderID.' order by ID desc');
        if(empty($TranslateInfo['Document'])){
            $HasDocumentInfo = $TranslateModule->GetInfoByWhere(' and (`Document` is not null and `Document`<>"") and Feedback = 2 and OrderID ='.$OrderID.' order by ID desc');
            $Result = $TranslateModule->UpdateInfoByKeyID(array('Status'=>2,'ConfirmTime'=>$ConfirmTime,'Document'=>$HasDocumentInfo['Document'],'DocumentName'=>$HasDocumentInfo['DocumentName']),$TranslateInfo['ID']);
            $TranslateInfo['Document'] = $HasDocumentInfo['Document'];
            $TranslateInfo['DocumentName'] = $HasDocumentInfo['DocumentName'];
        }
        else{
            $Result = $TranslateModule->UpdateInfoByKeyID(array('Status'=>2,'ConfirmTime'=>$ConfirmTime),$TranslateInfo['ID']);
        }
        if($Result){
            $ConsultantModule = new StudyOrderConsultantModule();
            //更新订单流程表材料翻译状态为完成
            $Result1 = $ConsultantModule->UpdateInfoByWhere(array('Status'=>3,'ConfirmTime'=>$ConfirmTime),' OrderID = '.$OrderID.' and Type=6');
            if($Result1){
                $OrderModule = new StudyOrderModule();
                //更新订单表状态为完成
                $Result2 = $OrderModule->UpdateInfoByKeyID(array('Status'=>3),$OrderID);
                if($Result2){
                    //添加订单操作日志
                    $Result3 = $this->OrderLogOperate($OrderInfo['OrderNum'],2,3,$OrderInfo['OrderName']);
                    if($Result3['ResultCode'] == 200){
                        //资金及日志操作
                        $ConsultantInfoModule = new StudyConsultantInfoModule();
                        $Scale = $ConsultantInfoModule->Scale;
                        $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$ConsultantID);
                        //直接全款*顾问应获比例
                        $Amt = $OrderInfo['Money']*$Scale[$ConsultantInfo['Grade']];//操作金额,当前服务的订单部分金额
                        //更新订单流程表结算金额
                        $ConsultantModule->UpdateInfoByWhere(array('Amt'=>$Amt),' OrderID='.$OrderID.' and Type = 6');
                        $result_json = $this->AmountOperate($ConsultantID,$Amt,$OrderInfo['OrderName'].'-材料翻译服务',$DB);
                        if($result_json['ResultCode'] == 200){
                            $DB->query("COMMIT");//执行事务
                            $result_json = array('ResultCode'=>'200','Message'=>'确认成功,材料翻译服务完成','DownUrl'=>FileURL.$TranslateInfo['Document'],'DeadCopy_FileName'=>$TranslateInfo['DocumentName'],'DeadCopy_Date'=>$ConfirmTime);
                        }
                    }
                    else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $result_json = array('ResultCode'=>103,'Message'=>'确认失败','Describe'=>'订单操作日志添加失败');
                    }
                }else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $result_json = array('ResultCode'=>104,'Message'=>'确认失败','Describe'=>'更新订单表状态失败');
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>'102','Message'=>'确认失败',);
            }
        }
        else{
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            $result_json = array('ResultCode'=>'101','Message'=>'确认失败',);
        }
        echo json_encode($result_json);
    }

    /**
     * @desc  资金操作
     * @param $ConsultantID 顾问ID
     * @param $Amt  操作金额
     * @param $Remarks 产品名称-服务名称
     * @param $DB
     * @return array
     */
    public function AmountOperate($ConsultantID,$Amt,$Remarks,$DB){
        $BankFlowModule = new MemberUserBankFlowModule();
        $BankModule = new MemberUserBankModule();
        //操作资金
        $BankInfo = $BankModule->GetInfoByWhere(' and UserID='.$ConsultantID);
        //操作后的总金额
        $Amount = $BankInfo['TotalBalance']+$Amt;
        $BankIsOk = $BankModule->UpdateInfoByKeyID(array('TotalBalance'=>$Amount,'FreeBalance'=>$BankInfo['FreeBalance']+$Amt),$BankInfo['BankID']);
        if($BankIsOk){
            //操作资金记录,OperateType=>系统入账,Type=>留学
            $BankFlowData = array('FromIP'=>GetIP(),'UserID'=>$ConsultantID,'Amount'=>$Amount,'Amt'=>$Amt,'OperateType'=>4,'Remarks'=>$Remarks,'Type'=>2,'AddTime'=>date("Y-m-d H:i:s",time()));
            $BankFlowIsOK = $BankFlowModule->InsertInfo($BankFlowData);
            if($BankFlowIsOK){
                $Result = array('ResultCode'=>'200','Message'=>'操作成功');
            }
            else{
                $Result = array('ResultCode'=>'113','Message'=>'确认失败','Describe'=>'资金日志更新失败');
            }
        }
        else{
            $Result = array('ResultCode'=>'112','Message'=>'确认失败','Describe'=>'资金表更新失败');
        }
        return $Result;
    }

    /**
     * @desc  订单日志操作
     * @param $OrderNumber  订单编号
     * @param $OldStatus    原订单状态
     * @param $NewStatus    最终订单状态
     * @param $Remarks      订单备注,订单名称-服务名称
     * @return array
     */
    public function OrderLogOperate($OrderNumber,$OldStatus,$NewStatus,$Remarks){
        include_once SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyOrderLogModule.php';
        $OrderLogModule = new StudyOrderLogModule();
        $LogData = array('OrderNumber'=>$OrderNumber,'UserID'=>$_SESSION['UserID'],'OldStatus'=>$OldStatus,'NewStatus'=>$NewStatus,'OperateTime'=>time(),'Type'=>1,'IP'=>GetIP(),'Remarks'=>$Remarks);
        //添加订单日志
        $IsOk = $OrderLogModule->InsertInfo($LogData);
        if($IsOk){
            $Result = array('ResultCode'=>'200');
        }
        else{
            $Result = array('ResultCode'=>'111','Message'=>'确认失败','Describe'=>'订单日志添加失败');
        }
        return $Result;
    }

    /**
     * @desc  获取产品详情
     * @param $ProductID
     * @return array|int
     */
    public function GetProductInfo($ProductID){
        $ServiceModule = new StudyConsultantServiceModule();
        $ServiceInfo =  $ServiceModule->GetInfoByKeyID($ProductID);
        return $ServiceInfo;
    }
    /**
     * @desc  学生头像修改
     */
    public function  StudentUpImg(){
        $UserInfoModule = new MemberUserInfoModule();
        if ($_POST) {
            $Image = trim($_POST['Img']);
            if (!empty($Image)) {
                if (strpos($Image, 'data:image/jpeg;base64') !== false) {
                    $ImageFullUrl = '/up/' . date('Y') . '/' . date('md') . '/' . date('YmdHis') . mt_rand(1000, 9999) . '.jpg';
                    SendToImgServ($ImageFullUrl, str_replace('data:image/jpeg;base64,', '', $Image));
                    $Data['Avatar'] = $ImageFullUrl;
                    $Update = $UserInfoModule->UpdateInfoByWhere($Data,' UserID = '.$_SESSION['UserID']);
                    if ($Update){
                        $json_result=array('ResultCode'=>200,'Message'=>'修改成功');
                    }else{
                        $json_result=array('ResultCode'=>201,'Message'=>'修改失败');
                    }
                    echo json_encode($json_result);exit;
                }
            }
        }
    }
}