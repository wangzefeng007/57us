<?php

class AjaxStudy
{

    public function __construct() {

    }

    public function Index(){

        if (trim($_POST ['Intention']) == '' && trim($_GET ['Intention'])== '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            echo json_encode($json_result);
            exit;
        }
        if (trim($_GET ['Intention']) != ''){
            $Intention = trim($_GET ['Intention']);
            $this->$Intention ();
        }elseif(trim($_POST ['Intention']) != ''){
            $Intention = trim($_POST ['Intention']);
            $this->$Intention ();
        }
    }

    /**
     * @desc 获取顾问列表
     */
    public function GetConsultantList(){
        $StudyConsultantInfoModule = new StudyConsultantInfoModule();
        $MysqlWhere='';
        //工作年限
        $WorkingAge=trim($_POST['Term']);
        switch($WorkingAge){
            case "0-3":
                $MysqlWhere.="and a.WorkingAge>=0 and a.WorkingAge<3";
                break;
            case "3-5":
                $MysqlWhere.="and a.WorkingAge>=3 and a.WorkingAge<=5";
                break;
            case "5-10":
                $MysqlWhere.="and a.WorkingAge>=5 and a.WorkingAge<=10";
                break;
            case "10-All":
                $MysqlWhere.="and a.WorkingAge>10";
                break;
        }
        //选择地区
        $City=trim($_POST['City']);
        if($City!='All'){
            $MysqlWhere.=" and b.City like '%$City%'";
        }
        //关键字
        $Keyword=trim($_POST['Keyword']);
        if($Keyword!=''){
            $MysqlWhere.=" and b.NickName like '%$Keyword%'";
        }
        //分页查询开始-------------------------------------------------
        $Rscount = $StudyConsultantInfoModule->SelectConsultantMemberInfo($MysqlWhere,"count(b.UserID) as Num")[0];
        $Page=intval($_POST['Page'])?intval($_POST['Page']):0;
        if ($Page < 1) {
            $Page = 1;
        }
        $Data = false;
        if ($Rscount['Num']) {
            $PageSize=6;
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            $Data['Data'] = $StudyConsultantInfoModule->SelectConsultantMemberInfo($MysqlWhere." limit $Offset,{$Data['PageSize']}");

            $ConsultantList=array();
            foreach($Data['Data'] as $Key => $Val){
                $ConsultantList[$Key]['StudyId']=$Val['UserID']; //顾问ID
                $ConsultantList[$Key]['StudyName']=$Val['NickName']; //顾问昵称
                $ConsultantList[$Key]['StudyTerm']=$Val['WorkingAge'];
                $ConsultantList[$Key]['StudyPosition']=$Val['City'];
                if($Val['Avatar']){
                    $ConsultantList[$Key]['StudyImg']=(strpos($Val['Avatar'],"http://")===false)?LImageURL.$Val['Avatar']:$Val['Avatar'];
                }elseif($Val['Avatar']==''){
                    $ConsultantList[$Key]['StudyImg']= ImageURL.'/img/common/default.png';
                }
                $ConsultantList[$Key]['StudyUrl']=WEB_M_URL.'/study/consultant/'.$Val['UserID'].'.html';
                $TagStr="";
                $TagArr=json_decode($Val['Tags'],true);
                if(!empty($TagArr)){
                    foreach($TagArr as $Tag){
                        $TagStr.="<span>$Tag</span>";
                    }
                }
                $ConsultantList[$Key]['StudyTag']=$TagStr;
                $ConsultantList[$Key]['StudyDepict']=  _substr($Val['Introduction'],60);
            }
            if($Keyword!=''){
                $ResultCode=102;
            }else{
                $ResultCode=200;
            }
            $json_result=array(
                'ResultCode'=> $ResultCode,
                'RecordCount'=>$Data['RecordCount'],
                'Data'=>$ConsultantList
            );
        }
        else{
            if($Keyword!=''){
                $ResultCode=103;
            }else{
                $ResultCode=101;
            }
            $json_result=array('ResultCode'=>$ResultCode,'Message'=>'没有找到记录');
            $Data['Data']=$StudyConsultantInfoModule->SelectConsultantMemberInfo(' limit 0,6');
            if(!empty($Data['Data'])){
                $ConsultantList=array();
                foreach($Data['Data'] as $Key => $Val){
                    $ConsultantList[$Key]['Study_name']=$Val['NickName'];
                    $ConsultantList[$Key]['StudyID']=$Val['UserID'];
                    $ConsultantList[$Key]['StudyExperience']=$Val['WorkingAge'];
                    $ConsultantList[$Key]['StudyServiceRegion']=$Val['City'];
                    $ConsultantList[$Key]['StudySex']=$Val['Sex'];
                    $ImagesJson = json_decode($Val['ImagesJson'],true);
                    $ConsultantList[$Key]['StudyImg']=($ImagesJson[0]!='')?(ImageURLP2.json_decode($Val['ImagesJson'],true)[$Val['CoverImageKey']]):(ImageURL.'/img/study/defaultService3.0.jpg');
                    $ConsultantList[$Key]['StudyUrl']=WEB_STUDY_URL.'/study/consultant/'.$Val['UserID'].'.html';
                    $TagStr="";
                    $TagArr=json_decode($Val['Tags'],true);
                    if(!empty($TagArr)){
                        foreach($TagArr as $Tag){
                            $TagStr.="<span>$Tag</span>";
                        }
                    }
                    $ConsultantList[$Key]['StudyTag']=$TagStr;
                    $ConsultantList[$Key]['StudyDepict']=$Val['Introduction'];
                }
                $json_result['Data']=$ConsultantList;
            }else{
                $json_result['Data']=array();
            }
        }
        echo json_encode($json_result);
    }


    /**
     * @desc  保存匹配信息
     */
    public function MarryInfoSave(){
        $MarryInfoModule = new StudyMarryInfoModule();
        //将匹配信息添加入匹配表
        $MarryData = array(
            'UserID'=>$_SESSION['UserID'],
            'ConsultantJson'=>'',
            'MarryName'=>$_POST['MarryName'],  //姓名
            'MarrySex'=>$_POST['MarrySex'], //性别
            'GoAbroadTime'=>$_POST['GoAbroadTime'],  //预计出国时间
            'ContactWay'=>$_POST['ContactWay'],  //联系方式
            'MarryCity'=>$_POST['MarryCity'], //匹配城市
            'MarryTargetLevel'=>intval($_POST['MarryTargetLevel']),  //申请层次  1-高中 2-本科 3-研究生 4-转学
            'MarryServiceType'=>intval($_POST['MarryServiceType']),  //服务类型1-全程服务 2-申请学校 3-文书服务 4-定校选校 5-签证培训 6-材料翻译 7-背景提升
            'MarryGrade'=>$_POST['MarryGrade'], //匹配年级
            'AddTime'=>time(),
            'Times'=>5
        );
        $MarryID = $MarryInfoModule->InsertInfo($MarryData);
        $resultJson = array('MarryID'=>$MarryID,'ResultCode'=>200,'Url'=>'/study/marryconsultanttwo/?MarryID='.$MarryID);
        echo json_encode($resultJson);
    }

    /**
     * @desc  匹配查询顾问
     */
    public function MarrySelect(){
        $ConsultantInfoModule = new StudyConsultantInfoModule();
        $MarryInfoModule = new StudyMarryInfoModule();
        $MarryID = intval($_POST['MarryID']);
        $MarryInfo = $MarryInfoModule->GetInfoByKeyID($MarryID);
        if(empty(json_decode($MarryInfo['ConsultantJson'],true))){
            $City = $MarryInfo['MarryCity'];
            $ServiceType = $MarryInfo['MarryTargetLevel'];
            $TargetLevel = $MarryInfo['MarryServiceType'];

            $MysqlWhere = '';
            //关键字
            if($City && $ServiceType && $TargetLevel){
                $MysqlWhere.=" and ( b.City like '%$City%' or c.ServiceType = $ServiceType or c.TargetLevel = $TargetLevel)";
            }
            //查询出所有符合条件的数据
            $AllInfos = $ConsultantInfoModule->MobileSelectConsultantInfos($MysqlWhere,array('a.UserID','a.Tags','a.OneCondition','a.WorkingAge','a.Choosed','a.TwoCondition','a.ThreeCondition','b.NickName','b.RealName','b.Avatar','b.City','c.TargetLevel','c.ServiceType'));
            //遍历出顾问对应下的数组
            $ConsultantInfos = array();
            $UserAtID = '';
            foreach($AllInfos as $val){
                if($val['UserID'] != $UserAtID){
                    $UserAtID = $val['UserID'];
                    $ConsultantInfos[$UserAtID][] = $val;
                }
                else{
                    $ConsultantInfos[$val['UserID']][] = $val;
                }

            }
            //确定顾问的匹配等级
            $ResultInfo = array();
            foreach($ConsultantInfos as $k=>$v){
                foreach($v as $k1 => $v1){
                    $A = 0;
                    $B = 0;
                    $C = 0;
                    if(strpos($v1['City'],$City) !== false){
                        $A=1;
                    }
                    if($v1['TargetLevel'] == $TargetLevel){
                        $B=1;
                    }
                    if($v1['ServiceType'] == $ServiceType){
                        $C=1;
                    }
                }
                $Num = $A+$B+$C;
                $ResultInfo[$k]['Num']=$Num;
                $ResultInfo[$k] = $v[0];
                if($Num == 3){
                    $ResultInfo[$k]['scale'] = $v[0]['ThreeCondition']?$v[0]['ThreeCondition']:0;
                }
                elseif($Num == 2){
                    $ResultInfo[$k]['scale'] = $v[0]['TwoCondition']?$v[0]['TwoCondition']:0;
                }
                elseif($Num == 1){
                    $ResultInfo[$k]['scale'] = $v[0]['OneCondition']?$v[0]['OneCondition']:0;
                }
                else{
                    $ResultInfo[$k]['scale'] = 100;  //默认值
                }
                $ResultInfo[$k]['Avatar'] = (strpos($v[0]['Avatar'],LImageURL))?$v[0]['Avatar']:(LImageURL.$v[0]['Avatar']);
                $ResultInfo[$k]['Tags'] = $v[0]['Tags']?json_decode($v[0]['Tags']):'';
                $ResultInfo[$k]['Choosed'] = $v[0]['Choosed']?$v[0]['Choosed']:0;
                $ResultInfo[$k]['Url'] = WEB_M_URL.'/study/consultant/'.$v[0]['UserID'].'.html';
            }
            //排序
            foreach($ResultInfo as $key => $val){
                $Scale[$key] = $val['scale'];
                $UserID[$key] = $val['UserID'];
            }
            array_multisort( $Scale,SORT_DESC,$UserID,SORT_ASC, $ResultInfo);

            //更新匹配的顾问信息
            $MarryInfoModule->UpdateInfoByKeyID(array('ConsultantJson'=>json_encode($ResultInfo,JSON_UNESCAPED_UNICODE),'Consultants'=>count($ResultInfo)),$MarryID);
        }
        else{
            $ResultInfo = json_decode($MarryInfo['ConsultantJson'],true);
        }
        $AllCount = count($ResultInfo);

        //分页操作
        $Count = 6;
        $Page = $_POST['Page']?intval($_POST['Page']):1;
        $Data = PageArray($Count,$Page,$ResultInfo);

        $ChooseModule = new StudyMarryChooseModule();
        $ChooseInfo = $ChooseModule->GetInfoByWhere(' and MarryID = '.$MarryID,true);
        if($ChooseInfo){
            foreach($Data as $key => $val){
                foreach($ChooseInfo as $k => $v){
                    if($v['ConsultantID'] == $val['UserID']){
                        $Data[$key]['IsChoose'] = 1;
                        break;
                    }
                    else{
                        $Data[$key]['IsChoose'] = 0;
                    }
                }
            }
        }
        else{
            foreach($Data as $key => $val){
                $Data[$key]['IsChoose'] = 0;
            }
        }
        foreach($Data as $key => $val){
            $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$val['UserID']);
            $Data[$key]['Choosed'] = $ConsultantInfo['Choosed'];
            $TagStr="";
            $TagArr=$val['Tags'];
            if(!empty($TagArr)){
                foreach($TagArr as $Tag){
                    $TagStr.="<span>$Tag</span>";
                }
            }
            $Data[$key]['Tags'] = $TagStr;
        }
        echo json_encode(array('ResultCode'=>200,'Data'=>$Data,'Count'=>$AllCount,'Times'=>$MarryInfo['Times']));
    }

    /**
     * @desc  选择匹配顾问
     */
    public function MarryChoose(){
        $MarryChooseModule = new StudyMarryChooseModule();
        $MarryInfoModule = new StudyMarryInfoModule();
        $ConsultantInfoModule = new StudyConsultantInfoModule();

        $IsChoose = $MarryChooseModule->GetInfoByWhere(' and ConsultantID = '.$_POST['ConsultantID'].' and UserID='.$_SESSION['UserID'].' and MarryID='.$_POST['MarryID']);
        if(!$IsChoose){
            //开启事务
            global $DB;
            $DB->query("BEGIN");//开始事务定义
            $ChooseData = array(
                'UserID'=>$_SESSION['UserID'],
                'ConsultantID'=>$_POST['ConsultantID'],
                'MarryID'=>$_POST['MarryID'],
                'Status'=>0,
                'AddTime'=>time(),
                'ContactTimes'=>$_POST['ContactTimes']
            );
            $Result = $MarryChooseModule->InsertInfo($ChooseData);
            if($Result){
                $Result1 = $MarryInfoModule->UpdateTimesByID($_POST['MarryID']);
                if($Result1){
                    $Result2 = $ConsultantInfoModule->UpdateChoosedByID($_POST['ConsultantID']);
                    if($Result2){
                        $DB->query("COMMIT");//执行事务
                        $json_result=array('ResultCode'=>200,'Message'=>'选择成功','Url'=>'/study/marryconsultantfour/?ConsultantID='.$_POST['ConsultantID'].'&MarryID='.$_POST['MarryID']);
                    }
                    else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $json_result=array('ResultCode'=>103,'Message'=>'选择失败',);
                    }
                }
                else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $json_result=array('ResultCode'=>102,'Message'=>'选择失败','');
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $json_result=array('ResultCode'=>101,'Message'=>'选择失败');
            }
        }
        else{
            $json_result=array('ResultCode'=>104,'Message'=>'选择失败,您已经选择过该顾问');
        }
        echo json_encode($json_result);exit;
    }

    /**
     * @desc  免费评估保存
     */
    public function EstimateSave(){
        $MarryInfoModule = new StudyMarryInfoModule();
        $MarryData = array(
            'ApplyProject'=>trim($_POST['ApplyProject']),
            'AttendSchool'=>trim($_POST['AttendSchool']),
            'Average'=>$_POST['Average'],
            'Mobile'=>$_POST['Mobile'],
            'AddTime'=>time()
        );
        $Result = $MarryInfoModule->InsertInfo($MarryData);
        if($Result){
            $json_result=array('ResultCode'=>200,'Message'=>'保存成功');
        }
        else{
            $json_result=array('ResultCode'=>101,'Message'=>'保存失败');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  获取匹配顾问列表
     */
    public function GetMarryInfo(){
        $MarryInfoModule = new StudyMarryInfoModule();

        if (! $_POST) {
            $Data['ResultCode'] = 100;
            EchoResult($Data);
        }
        $MysqlWhere = ' and UserID = '.$_SESSION;
        $Page = intval($_POST['Page']) < 1 ? 1 : intval($_POST['Page']); // 页码 可能是空
        $PageSize = 6;
        $Rscount = $MarryInfoModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            if ($Data['Page'] < $Data['PageCount']) {
                $Data['NextPage'] = $Data['Page'] + 1;
            }
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount']) {
                $Page = $Data['PageCount'];
            }

            $Lists = $MarryInfoModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            if (!empty($Lists))
            {
                foreach ($Lists as $Key => $Value) {
                    $Data['Data'][$Key]['TourPicre'] = ceil($Value['LowPrice']);
                    // 出发城市
                    $TourAreaModule = new TourAreaModule();
                    $TourAreaInfo = $TourAreaModule->GetInfoByKeyID($Value['City']);
                    $Data['Data'][$Key]['TourEndCity'] = $TourAreaInfo['CnName'] ? $TourAreaInfo['CnName'] : '';
                    // 图片
                    $TourProductImageModule = new TourProductImageModule();
                    $TourImagesInfo = $TourProductImageModule->GetInfoByTourProductID($Value['TourProductID']);
                    $Data['Data'][$Key]['TourImg'] = ImageURLP2 . $TourImagesInfo['ImageUrl'];
                    unset($TourImagesInfo);
                    $Data['Data'][$Key]['TourName'] = $Value['ProductName'];
                    $Data['Data'][$Key]['TourId'] = $Value['TourProductID'];
                    $Data['Data'][$Key]['TourStroke'] = $Value['Times'] ? $Value['Times'] : '1天';
                    $TagArr = explode(',', $Value['TagInfo']);
                    $TagHtml = '';
                    if (isset($TagArr[0]) && ! empty($TagArr[0])) {
                        foreach ($TagArr as $list) {
                            $TagHtml .= "<span>$list</span>";
                        }
                    }
                    $Data['Data'][$Key]['TourTag'] = $TagHtml;
                    $Data['Data'][$Key]['TourUrl'] = WEB_M_URL . '/play/' . $Value['TourProductID'] . '.html';
                }
                MultiPage($Data, 6);
            }else{
                $Data['Data'] = array();
                unset($Data['PageCount'],$Data['PageNums'],$Data['PageSize']);
            }
        }
    }

    /**
     * @desc  首页获取申请方案
     */
    private function IndexApply(){
        $Data['ApplyType']=trim($_POST['Project']);
        $Data['Grade']=trim($_POST['Grade']);
        $Data['Results']=trim($_POST['Results']);
        $Data['Tel']=trim($_POST['phone']);
        $Data['AddTime']=time();
        $StudyApplySchemeModule=new StudyApplySchemeModule();
        $result=$StudyApplySchemeModule->InsertInfo($Data);
        if($result){
            //发送给客户
            ToolService::SendSMSNotice($Data['Tel'], "您好！我们已经收到了您的评估申请，我们的老师正在加班加点帮您拟定申请方案，我们将在1-2个工作日内给您反馈，谢谢！");
            //发送给运营
            ToolService::SendSMSNotice('15659827860', "手机{$Data['Tel']}号提交了申请评估信息，请马上跟进处理。申请项目：{$Data['ApplyType']}，目前：{$Data['Grade']}，国内平均绩点：{$Data['Results']}。请尽快联系用户。");
            ToolService::SendSMSNotice('15980805724', "手机{$Data['Tel']}号提交了申请评估信息，请马上跟进处理。申请项目：{$Data['ApplyType']}，目前：{$Data['Grade']}，国内平均绩点：{$Data['Results']}。请尽快联系用户。");
            $json_result=array('ResultCode'=>200,'Message'=>'提交成功,顾问将24小时内联系您！','Url'=>WEB_M_URL.'/study/');
        }else{
            $json_result=array('ResultCode'=>101,'Message'=>'提交失败');
        }
        echo json_encode($json_result);
    }
    /**
     * @desc 获取游学列表
     */
    public function StudyTourList()
    {
        $StudyYoosureModule = new StudyYoosureModule();
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        $MysqlWhere = ' and Status = 1 ';
        // 游学主题
        $Theme = $_POST['Theme'];
        if ($Theme != '' && $Theme[0] != '0') {
            $YoosureTitle ='';
            foreach ($Theme as $value){
                $YoosureTitle .= $value.',';
            }
            $YoosureTitle = substr($YoosureTitle, 0, -1);
            $MysqlWhere .= " and YoosureTitle in($YoosureTitle)";
        }

        // 适合人群
        $Crowd = $_POST['Crowd'];
        if ($Crowd != '' && $Crowd[0] != '0') {
            $Crowds ='';
            foreach ($Crowd as $value){
                $Crowds .= $value.',';
            }
            $Crowds = substr($Crowds, 0, -1);
            $MysqlWhere .= " and Crowd in($Crowds)";
        }

        // 出行天数
        $Date = $_POST['TripDate'];
        if ($Date != '' && $Date[0] != '0') {
            foreach ($Date as $value){
                $Dates .= $value;
            }
            if ($Dates=='0-10'){
                $MysqlWhere .= ' and Days > 0 and Days <10 ';
            }elseif ($Dates=='10-15'){
                $MysqlWhere .= ' and Days > 9 and Days <16 ';
            }elseif ($Dates=='15-All'){
                $MysqlWhere .= ' and Days > 15 ';
            }elseif(strstr($Dates,'0-10') && strstr($Dates,'10-15')&& !strstr($Dates,'15-All')){
                $MysqlWhere .= ' and Days > 0 and Days <16 ';
            }elseif (strstr($Dates,'0-10') && strstr($Dates,'15-All')&& !strstr($Dates,'10-15')){
                $MysqlWhere .= 'and (Days <10 or Days > 15) ';
            }elseif (strstr($Dates,'10-15') && strstr($Dates,'15-All')&& !strstr($Dates,'0-10')){
                $MysqlWhere .= ' and Days > 9 ';
            }elseif (strstr($Dates,'0-10') && strstr($Dates,'10-15')&& strstr($Dates,'15-All')){
                $MysqlWhere .=  '';
            }
        }
        // 出行地
        $StartCity = $_POST['TripPlace'];
        if ($StartCity != '' && $StartCity[0] != '0') {
            $DeparturePlace ='';
            foreach ($StartCity as $value){
                $DeparturePlace .= $value.',';
            }
            $DeparturePlace = substr($DeparturePlace, 0, -1);
            $MysqlWhere .= " and DeparturePlace in($DeparturePlace)";
        }
        // 搜索
        $Keyword = trim($_POST['Keyword']);
        if ($Keyword != '') {
            $MysqlWhere .= " and Title like '%$Keyword%'";
        }
        $Rscount = $StudyYoosureModule->GetListsNum($MysqlWhere);
        $page = intval($_POST['Page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 8;
        $Data = array();
        if ($Rscount['Num']) {
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $StudyYoosureLists = $StudyYoosureModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($StudyYoosureLists as $Key => $Value) {
                $Data['Data'][$Key]['StudyId'] = $Value['YoosureID'];
                $Data['Data'][$Key]['StudyTitle'] = $Value['Title'];
                $Data['Data'][$Key]['StudyWTRecommend'] = $Value['R1'];
                $ApplyTime = json_decode($Value['ApplyTime'],true);
                $Data['Data'][$Key]['StudyEndDate'] = $ApplyTime[0];//报名截止时间
                $Data['Data'][$Key]['StudyOriginalPrice'] = intval($Value['OriginalPrice']);
                $Data['Data'][$Key]['StudyPrice'] = intval($Value['Price']);
                $YoosureImage = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID = '.$Value['YoosureID'].' and IsDefault = 1');
                if (strpos($YoosureImage['Image'],"http://")===false && $YoosureImage['Image']){
                    $Data['Data'][$Key]['StudyImg'] = LImageURL.$YoosureImage['Image'];
                }else{
                    $Data['Data'][$Key]['StudyImg'] = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                }
            }
            MultiPage($Data, $PageSize);
            $Data['ResultCode'] = 200;
            echo json_encode($Data);
            exit();
        }else{
            $Data['ResultCode'] = 101;
            echo json_encode($Data);
            exit();
        }
    }
    /**
     * @desc 获取游学出发时间
     */
    public function GetStudyTourDate()
    {
        $StudyYoosureModule = new StudyYoosureModule();
        if ($_POST['id']){
            $ID = $_POST['id'];
            $StudyYoosure = $StudyYoosureModule->GetInfoByKeyID($ID);
            $StudyYoosure['GoDate'] = json_decode($StudyYoosure['GoDate'],true);//出发时间
            $StudyYoosure['ApplyTime'] = json_decode($StudyYoosure['ApplyTime'],true);//截止时间
            $Data =array();
            foreach ($StudyYoosure['GoDate']  as $key=>$value){
                $value= str_replace( '年', '-', $value );
                $value= str_replace( '月', '-', $value );
                $value = str_replace( '日', '', $value );
                $Data['Data'][] .= rtrim($value,',');
            }
            foreach ($StudyYoosure['ApplyTime']  as $key=>$value){
                $value= str_replace( '年', '-', $value );
                $value= str_replace( '月', '-', $value );
                $value = str_replace( '日', '', $value );
                $Data['Data2'][] .= rtrim($value,',');
            }
            echo json_encode($Data);exit();
        }
    }

    /**
     * @desc 游学提交订单
     */

    public function StudyTourOrder(){
        $UserModule = new MemberUserModule();
        $OrderData['OrderNum']= StudyService::GetStrdyTourOrderNumber();
        $Phone = $_POST['Mobile'];
        $UserID = $UserModule->GetUserIDbyMobile($Phone);
        if (!$UserID){
            $Data = array('Mobile' => $Phone, 'State' => 1, 'AddTime' => time());
            $UserID = $UserModule->InsertInfo($Data);
            $UserInfoModule = new  MemberUserInfoModule();
            $InfoData['UserID'] = $UserID;
            $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
            $InfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
            $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
            $InfoData['Sex'] = 1;
            $InfoData['Avatar']='/img/man3.0.png';
            $Result1 = $UserInfoModule->InsertInfo($InfoData);
        }else{
            $UserID = $_SESSION['UserID'];
        }
        $json_result = $this->Operate($OrderData['OrderNum'],$UserID,$_POST);
        //添加订单操作日志
        $OrderLogModule = new TourProductOrderLogModule();
        $LogData = array('OrderNumber'=>$OrderData['OrderNum'],'UserID'=>$UserID,'Remarks'=>$json_result['Message'],'OldStatus'=>0,'NewStatus'=>1,'OperateTime'=>date("Y-m-d H:i:s",time()),'IP'=>GetIP(),'Type'=>6);
        $OrderLogModule->InsertInfo($LogData);
        echo json_encode($json_result);exit;
    }

    /**
     * @desc 订单实际操作
     */
    private function Operate($OrderNumber,$UserID,$Post){
        if ($Post) {
            $StudyYoosureOrderModule = new StudyYoosureOrderModule ();
            $StudyYoosureModule = new StudyYoosureModule ();
            //下订单
            $InsertInfo ['YoosureID'] = $Post ['YoosureID'];
            $StudyYoosureInfo = $StudyYoosureModule->GetInfoByKeyID ( $InsertInfo ['YoosureID'] );
            if (empty ( $StudyYoosureInfo )) {
                $JsonResult = array ('ResultCode' => 103, 'Message' => '产品不存在!','LogMessage'=>'操作失败(产品不存在)');
            }else{
                $NowTime = time();
                $InsertInfo ['UserID'] = $UserID;
                $InsertInfo ['OrderNum'] = $OrderNumber;
                $InsertInfo ['OrderName'] = $StudyYoosureInfo ['Title'];
                $InsertInfo ['Mobile'] = $Post ['Mobile'];
                $InsertInfo ['CreateTime'] = date ( "Y-m-d H:i:s",$NowTime );
                $InsertInfo ['UpdateTime'] = date ( "Y-m-d H:i:s",$NowTime );
                $InsertInfo ['ExpirationTime'] = date("Y-m-d H:i:s",$NowTime+172800);
                $InsertInfo ['PaymentMethod'] = 0;
                $InsertInfo ['IP'] = GetIP ();
                $InsertInfo ['Status'] = 1;
                $InsertInfo ['Contact'] = trim($Post ['Contacts']);//联系人
                $InsertInfo ['GoDate'] =date('Y年m月d日',strtotime($Post ['Date']));//去游学时间
                $InsertInfo ['Email'] = $Post ['Email'];//邮箱
                $InsertInfo ['Num'] = $Post ['Num'];
                $InsertInfo ['OneMoney'] = $StudyYoosureInfo ['Price'];
                $InsertInfo ['Money'] = $StudyYoosureInfo ['Price'] * $InsertInfo ['Num']; //金额
                $InsertInfo ['Message'] = $Post ['Message'];
                //出行人信息
                $TravelerInformation = array();
                foreach ($Post ['Travellers'] as $key=>$value){
                    $TravelerInformation[$key]['Name'] = $value['zhname'];
                    if ($value['type']==1){
                        $TravelerInformation[$key]['PassPort'] = '';
                    }elseif ($value['type']==0){
                        $TravelerInformation[$key]['PassPort'] = $value['zhCard'];
                    }
                }
                $InsertInfo ['TravelerInformation'] = json_encode($TravelerInformation,JSON_UNESCAPED_UNICODE);
                //出行人信息
                $IsOk = $StudyYoosureOrderModule->InsertInfo ( $InsertInfo );
                if ($IsOk) {
                    //发送给用户
                    ToolService::SendSMSNotice($InsertInfo ['Mobile'], '【57美国网】您购买的“'. $InsertInfo ['OrderName'] .'”订单已经提交，离美国游学还差最后一步啦，请及时支付。');
                    //发送给运营
                    ToolService::SendSMSNotice(15659827860,$InsertInfo ['Contact'].'用户,已产生游学订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['Contact'].' ，联系电话：'.$InsertInfo ['Mobile'].'。');
                    ToolService::SendSMSNotice(15160090744,$InsertInfo ['Contact'].'用户,已产生游学订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['Contact'].' ，联系电话：'.$InsertInfo ['Mobile'].'。');
                    ToolService::SendSMSNotice(15980805724,$InsertInfo ['Contact'].'用户,已产生游学订单，订单号：'.$OrderNumber.'，预订人：'. $InsertInfo ['Contact'].' ，联系电话：'.$InsertInfo ['Mobile'].'。');
                    $JsonResult=array('ResultCode'=>200,'Message'=>'订单创建成功','Url'=>WEB_M_URL."/studytourorder/{$InsertInfo['OrderNum']}.html");
                } else {
                    $JsonResult = array ('ResultCode' => 100, 'Message' => '提交订单失败,请重试!','LogMessage'=>'操作失败' );
                }
            }
        } else {
            $JsonResult = array ('ResultCode' => 101, 'Message' => '非法数据！','LogMessage'=>'操作失败(提交数据有误)');
        }
        return $JsonResult;
    }
    // 游学详情图片
    private function DetailsPic()
    {
        $StudyYoosureImageModule = new StudyYoosureImageModule();
        if ($_POST) {
            $YoosureID = intval($_POST['ID']);
            $ImageList = $StudyYoosureImageModule->GetInfoByWhere(' and YoosureID= '.$YoosureID,true);
            if ($ImageList) {
                $Data['ResultCode'] = 200;
                foreach ($ImageList as $key=>$val) {
                    if (strpos($val['Image'],"http://")===false && $val['Image']) {
                        $Data['DataPic'][] = LImageURL . $val['Image'];
                    }else{
                        $Data['DataPic'][]  = LImageURL.'/ue_uploads/images/2016/0922/201609221620185282.jpg';
                    }
                }
            } else {
                $Data['ResultCode'] = 100;
                $Data['Message'] = '没有图片';
            }
            echo json_encode($Data);
            exit();
        }
    }
}