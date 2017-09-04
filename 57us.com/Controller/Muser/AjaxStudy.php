<?php
class AjaxStudy {

  public function __construct() {
       if (!isset ($_SESSION ['UserID']) || empty ($_SESSION ['UserID'])) {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '请先登录',
                'Url' => WEB_MUSER_URL
            );
            echo json_encode($json_result);
            exit;
        }
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
            echo json_encode($json_result);
            exit;
        }
        $this->$Intention ();
    }
    
    //查询我的顾问匹配列表
    private function ConsultantMatching(){
        $MarryInfoModule = new StudyMarryInfoModule();
        //分页查询开始-------------------------------------------------
        $Rscount = $MarryInfoModule->GetListsNum(" and UserID={$_SESSION['UserID']}");
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
            $Data['Data'] = $MarryInfoModule->GetLists(" and UserID={$_SESSION['UserID']}",$Offset,$Data['PageSize']);
            $ResultArr=array();
            foreach($Data['Data'] as $key=>$val){
                $ResultArr[$key]['MarryID']=$val['MarryID'];
                $ResultArr[$key]['MarryName']=$val['MarryName'];
                $ResultArr[$key]['MarryCity']=$val['MarryCity'];
                $ResultArr[$key]['Consultants']=$val['Consultants'];
                $ResultArr[$key]['GoAbroadTime']=$val['GoAbroadTime'];
                $ResultArr[$key]['MarryGrade']=$val['MarryGrade'];
                $ResultArr[$key]['MarryServiceType']=$MarryInfoModule->ServiceType[$val['MarryServiceType']];
                $ResultArr[$key]['MarryTargetLevel']=$MarryInfoModule->TargetLevel[$val['MarryTargetLevel']];
                $ResultArr[$key]['Url'] = '/muserstudy/matchingdetail/?MarryID='.$val['MarryID'];
            }
            if($Page>$Data['PageCount']){
                $json_result=array('ResultCode'=>200,'Data'=>array(),'Count'=>$Rscount['Num']);
            }else{
                $json_result=array('ResultCode'=>200,'Data'=>$ResultArr,'Count'=>$Rscount['Num']);
            }
            
        }else{
            $json_result=array('ResultCode'=>101,'Message'=>'没有匹配的值');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  顾问我的匹配详情
     */
    private function ConsultantMatchingDetail(){

        $MarrayInfoModule = new StudyMarryInfoModule();
        $ConsultantInfoModule = new StudyConsultantInfoModule();
        $MarryID = $_POST['MarryID'];
        $MarryInfo = $MarrayInfoModule->GetInfoByKeyID($MarryID);
        $MarrayDetail = json_decode($MarryInfo['ConsultantJson'],true);
        //分页操作
        $Count = 6;
        $Page = $_POST['Page']?intval($_POST['Page']):1;
        $Data = PageArray($Count,$Page,$MarrayDetail);
        foreach($Data as $key => $val){
            $ConsultantInfo = $ConsultantInfoModule->GetInfoByWhere(' and UserID = '.$val['UserID']);
            $Data[$key]['Choosed'] = $ConsultantInfo['Choosed'];
            $TagStr="";
            $TagArr= $val['Tags'];
            if(!empty($TagArr)){
                foreach($TagArr as $Tag){
                    $TagStr.="<span>$Tag</span>";
                }
            }
            $Data[$key]['Tags'] = $TagStr;
        }
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
        echo json_encode(array('ResultCode'=>200,'Data'=>$Data,'Count'=>$MarryInfo['Consultants']));
    }
}
