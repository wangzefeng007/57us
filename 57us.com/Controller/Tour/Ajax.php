<?php

class Ajax
{
    public function __construct()
    {
    }

    public function Index()
    {
        $Intention = trim($_POST ['Intention']);
        if ($Intention == '') {
            $json_result = array( 'ResultCode' => 500,'Message' => '系統錯誤','Url' => '');
            echo json_encode($json_result);
            exit;
        }
        $this->$Intention ();
    }

    /**
     * @desc 判断手机号码是否注册
     */
    public function JudgeIsRegister()
    {
        $UserModule = new MemberUserModule();
        $Mobile = trim($_POST['Mobile']);
        $UserID = $UserModule->GetUserIDbyMobile($Mobile);
        if ($UserID) {
            $json_result = array('ResultCode' => '200');
        } else {
            $json_result = array('ResultCode' => '100');
        }
        echo json_encode($json_result);
        exit;
    }

    /**
     * @desc  发送手机验证码，验证手机
     */
    public function ValidateMobileCode()
    {
        $Data['Account'] = trim($_POST['Mobile']);
        $Data['VerifyCode'] = mt_rand(100000, 999999);
        $Data['XpirationDate'] = Time() + 60 * 30;
        $Data ['Type'] = 0;
        $Authentication = new MemberAuthenticationModule ();
        $ID = $Authentication->searchAccount($Data ['Account']);
        if ($ID) {
            $result = $Authentication->UpdateUser($Data, $ID);
        } else {
            $result = $Authentication->InsertUser($Data);
        }
        if ($result) {
            $result = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。');
            if ($result) {
                $json_result = array('ResultCode' => 200, 'Message' => '发送成功');
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '验证码发送失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '发送失败,系统异常');
        }
        echo json_encode($json_result);
    }

    /*
     * @desc 私人定制
     */
    public function PrivateCustom()
    {
        if ($_POST) {
            $TourPrivateOrderModule = new TourPrivateOrderModule();
            $UserID = $_SESSION['UserID'];
            $Code = trim($_POST['Code']);
            $Date['Phone'] = trim($_POST['Phone']);
            $MemberAuthenticationModule = new MemberAuthenticationModule();
            $ID = $MemberAuthenticationModule->ValidateAccount($Date['Phone'], $Code);
            $TempUserInfo = $MemberAuthenticationModule->GetAccountInfo($Date['Phone'], $Code);
            if (!$ID) {
                $json_result = array('ResultCode' => 102,'Message' => '短信验证码错误！');
                EchoResult($json_result);
            } else {
                $CurrentTime = time();
                if ($CurrentTime > $TempUserInfo['XpirationDate']) {
                    $json_result = array('ResultCode' => 103,'Message' => '短信验证码过期！');
                    EchoResult($json_result);
                }
            }
            $Date['Name'] = trim($_POST['Name']);
            $Date['Mail'] = trim($_POST['Mail']);
            $Date['StartCity'] = trim($_POST['StartCity']);
            $Date['StartDate'] = trim($_POST['StartDate']);
            $Date['EndDate'] = trim($_POST['EndDate']);
            $Date['adjust'] = trim($_POST['Adjust']);
            $arr = array('Addult' => $_POST['Addult'],'Minor' => $_POST['Minor']);
            $Number = json_encode($arr);
            $Date['Number'] = $Number;
            $Date['StarHotel'] = trim($_POST['StarHotel']);
            $Date['EndCity'] = trim($_POST['EndCity']);
            $Date['ScenicSpots'] = trim($_POST['ScenicSpots']);
            $Date['Customizatin'] = trim($_POST['Customizatin']);
            $Date['Demand'] = trim($_POST['Demand']);
            $Date['OtherDemand'] = trim($_POST['OtherDemand']);
            $I = 101;
            foreach ($Date as $key => $value) {
                if ($key != 'adjust' && $key != 'Customizatin' && $key != 'Demand' && $key != 'OtherDemand' && $key != 'ScenicSpots') {
                    if ($value == '') {
                        $JsonResult = array("ResultCode" => $I,"Message" => $key . '字段不能为空');
                        EchoResult($JsonResult);
                    }
                }
                $I++;
            }
            $Date['OrderNo'] = 'DZC' . date('YmdHis', time()) . rand(100, 999);
            $Date['Status'] = 0;
            $Date['CreateTime'] = date("Y-m-d H:i:s", time());
            $Date['UpdateTime'] = $Date['CreateTime'];
            $Date['IP'] = GetIP();
            // ======判断用户，模拟用户登陆======//
            if (!$UserID) {
                $MemberUserModule = new MemberUserModule();
                $UserInfo = $MemberUserModule->GetUserIDbyMobile($Date['Phone']);
                if ($UserInfo) {
                    $UserID = $UserInfo['UserID'];
                } else {
                    $Data['Mobile'] = $Date['Phone'];
                    $Data['AddTime'] = time();
                    $UserID = $MemberUserModule->InsertInfo($Data);
                    $MemberUserInfoModule = new MemberUserInfoModule();
                    $InfoData['UserID'] = $AccountInfo['UserID'];
                    $InfoData['NickName'] = '57US_'.date('i').mt_rand(100,999);
                    $InfoData['BirthDay'] = date('Y-m-d', $Data['AddTime']);
                    $InfoData['LastLogin'] = date('Y-m-d H:i:s', $Data['AddTime']);
                    $InfoData['Sex'] = 1;
                    $InfoData['Avatar']='/img/man3.0.png';
                    $MemberUserInfoModule->InsertData($InfoData);
                }
                $Date['UserID'] = $UserID;
            }
            $XpirationDate = time() + 3600 * 24;
            setcookie("session_id", session_id(), $XpirationDate, "/", "57us.com");
            $_SESSION['UserID'] = $UserID;
            setcookie("UserID", $_SESSION['UserID'], time() + 3600 * 24, "/", "57us.com");
            $InsertInfo = $TourPrivateOrderModule->InsertInfo($Date);
            if ($InsertInfo) {
                // =================================发送邮件start=================================//
                ToolService::SendSMSNotice(15160090744, '已产生高端定制订单，订单号：'.$Date['OrderNo'].'，预订人：'.$Date['Name'].' ，联系电话：'.$Date['Phone'].'。');
                ToolService::SendSMSNotice(18750258578, '已产生高端定制订单，订单号：'.$Date['OrderNo'].'，预订人：'.$Date['Name'].' ，联系电话：'.$Date['Phone'].'。');
                ToolService::SendSMSNotice(18050016313, '已产生高端定制订单，订单号：'.$Date['OrderNo'].'，预订人：'.$Date['Name'].' ，联系电话：'.$Date['Phone'].'。');
                ToolService::SendSMSNotice(15980805724, '已产生高端定制订单，订单号：'.$Date['OrderNo'].'，预订人：'.$Date['Name'].' ，联系电话：'.$Date['Phone'].'。');
                $EMail = $Date['Mail'];
                $Title = '旅游特色定制订单';
                $Message = '尊敬的用户，感谢您在57美国网定制特色旅游，我们的旅游规划师会在15分钟内给您联系，如有疑问可以致电：400-018-5757！';
                ToolService::SendEMailNotice($EMail, $Title, $Message);
                // =================================发送邮件end==================================//
                // ==================================发送短信start===============================//
                $Data['Mobile'] = $Date['Phone'];
                $result = ToolService::SendSMSNotice($Data['Mobile'], '尊敬的用户，感谢您在57美国网定制特色旅游，我们的旅游规划师会在15分钟内给您联系，如有疑问可以致电：400-018-5757！');
                if ($result == "success") {
                    $json_result = array('ResultCode' => 200,'Url' => WEB_MEMBER_URL . '/hightorderdetail/' . $Date['OrderNo'] . '.html','Message' => '发送短信成功');
                } else {
                    $json_result = array('ResultCode' => 100,'Message' => '发送短信失败');
                }
                // ==================================发送短信end===================================//
            } else {
                $json_result = array('ResultCode' => 201,'Message' => '返回失败');
            }
        } else {
            $json_result = array('ResultCode' => 100,'Message' => '返回失败');
        }
        EchoResult($json_result);
    }

    /**
     * @desc  通过图形验证码发送用户短信
     */
    private function PhoneCode()
    {
        $ImageCode = strtolower(trim($_POST['ImageCode']));
        if ($ImageCode == $_SESSION['authnum_session']) {
            $Data['Account'] = trim($_POST['User']); // 用户手机号
            $Data['VerifyCode'] = mt_rand(100000, 999999);
            $Data['XpirationDate'] = Time() + 60 * 30;
            if (is_numeric($Data['Account'])) {
                $Data['Type'] = 0;
            } elseif (strpos($Data['Account'], '@')) {
                $Data['Type'] = 1;
            }
            $MemberAuthenticationModule = new MemberAuthenticationModule();
            $ID = $MemberAuthenticationModule->searchAccount($Data['Account']);
            if ($ID) {
                $MemberAuthenticationModule->UpdateUser($Data, $ID);
            } else {
                $MemberAuthenticationModule->InsertUser($Data);
            }
            $result = ToolService::SendSMSNotice($Data['Account'], '亲爱的57美国网用户,您认证的验证码为:' . $Data['VerifyCode'] . '。如非本人操作，请忽略。【57美国网】');
            if ($result == "success") {
                $json_result = array('ResultCode' => 200,'Message' => '发送成功');
            } else {
                $json_result = array('ResultCode' => 100,'Message' => '验证码发送失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 104,'Message' => '发送失败,图形验证码错误');
        }
        EchoResult($json_result);
    }
    
    //评论点赞
    private function AddPraise(){
        $EvaluateID=intval($_POST['EvaluateID']);
        $IP=GetIP();
        $TourOrderRvaluatePraiseModule=new TourOrderRvaluatePraiseModule();
        $PraiseInfo=$TourOrderRvaluatePraiseModule->GetInfoByWhere(" and FromIP='$IP' and EvaluateID=$EvaluateID");
        if($PraiseInfo){
            $json_result = array(
                'ResultCode' => 101,
                'Message' => '您已经点过赞了',
            );
        }else{
            if(isset($_SESSION['UserID'])){
                $Data['UserID']=$_SESSION['UserID'];
            }
            $Data['EvaluateID']=$EvaluateID;
            $Data['AddTime']=time();
            $Data['FromIP']=$IP;
            $Result=$TourOrderRvaluatePraiseModule->InsertInfo($Data);
            if($Result){
                $TourOrderEvaluateModule=new TourOrderEvaluateModule();
                $TourOrderEvaluateModule->UpdatePraiseNum($EvaluateID,'+');
                $EvaluateInfo=$TourOrderEvaluateModule->GetInfoByKeyID($EvaluateID);
                $json_result = array(
                    'ResultCode' => 200,
                    'Message' => '点赞成功',
                    'Num'=>$EvaluateInfo['PraiseNum']
                );
            }else{
                $json_result = array(
                    'ResultCode' => 101,
                    'Message' => '点赞失败，请重试',
                );
            }
        }
        echo json_encode($json_result);
    }
    
    //评论列表
    private function TourComments(){
        $TourProductID=intval($_POST['TourProductID']);
        $TourOrderEvaluateModule=new TourOrderEvaluateModule();
        $MysqlWhere=" and TourProductID=$TourProductID";
        //只显示有图片的
        $Type=intval($_POST['Type']);
        if($Type==1){
            $MysqlWhere.=" and Images!=''";
        }
        $Rscount = $TourOrderEvaluateModule->GetListsNum($MysqlWhere);
        $page = intval($_POST['Page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 2;
        $ResultCode=101;
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            $Data['Data'] = $TourOrderEvaluateModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            $TourProductOrderInfoModule=new TourProductOrderInfoModule();
            $ProductOrderInfo=$TourProductOrderInfoModule->GetInfoByWhere(" and TourProductID=$TourProductID");
            if($ProductOrderInfo['TourLineSnapshotID']>0){
                //跟团游
                $Category=1;
                $TourProductLineModule = new TourProductLineModule();
                $TourProductLineInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductID);
                foreach($Data['Data'] as $key=>$val){
                    $Data['Data'][$key]['Score']=round(($val['ServerFraction']+$val['ConvenientFraction']+$val['ExperienceFraction']+$val['PerformanceFraction'])/4);
                    $Data['Data'][$key]['Images']=json_decode($val['Images'],true);
                    $OrderInfo=$TourProductOrderInfoModule->GetInfoByOrderNumber($val['OrderNumber']);
                    $Data['Data'][$key]['Depart']=$OrderInfo['Depart'];
                    $MemberUserInfoModule=new MemberUserInfoModule();
                    $Data['Data'][$key]['UserInfo']=$MemberUserInfoModule->GetInfoByUserID($val['UserID']);
                    $TourProductCategoryModule=new TourProductCategoryModule();
                    $Data['Data'][$key]['CategoryInfo']=$TourProductCategoryModule->GetInfoByKeyID($TourProductLineInfo['Category']);
                    $TourProductLineSkuModule=new TourProductLineSkuModule();
                    $SkuInfo=$TourProductLineSkuModule->GetInfoByKeyID($OrderInfo['TourProductSkuID']);
                    if($TourProductLineInfo['SkuType']==1){
                        $Data['Data'][$key]['AdultNum']=$SkuInfo['AdultNum'];
                        $Data['Data'][$key]['ChildrenNum']=$SkuInfo['ChildrenNum'];
                    }else{
                        $Data['Data'][$key]['AdultNum']=$SkuInfo['PeopleNum'];
                    }
                }           
            }else{
                $Category=2;
                $PlayBaseModule = new TourProductPlayBaseModule();
                $TourPlayInfo = $PlayBaseModule->GetInfoByTourProductID($TourProductID);
                //当地玩乐
                foreach($Data['Data'] as $key=>$val){
                    $Data['Data'][$key]['Score']=round(($val['ServerFraction']+$val['ConvenientFraction']+$val['ExperienceFraction']+$val['PerformanceFraction'])/4);
                    $Data['Data'][$key]['Images']=json_decode($val['Images'],true);
                    $OrderInfo=$TourProductOrderInfoModule->GetInfoByOrderNumber($val['OrderNumber']);
                    $Data['Data'][$key]['Depart']=$OrderInfo['Depart'];
                    $MemberUserInfoModule=new MemberUserInfoModule();
                    $Data['Data'][$key]['UserInfo']=$MemberUserInfoModule->GetInfoByUserID($val['UserID']);
                    $TourProductCategoryModule=new TourProductCategoryModule();
                    $Data['Data'][$key]['CategoryInfo']=$TourProductCategoryModule->GetInfoByKeyID($TourPlayInfo['Category']);
                }           
            }
            if(($page+1)>$Data['PageCount']){
                $NextPage=$Data['PageCount'];
            }else{
                $NextPage=$page+1;
            }
            if(($page-1)<1){
                $BackPage=1;
            }else{
                $BackPage=$page-1;
            }
            MultiPage($Data,6);
            $ResultCode=200;
        }
        //获取页面的缓存内容
        ob_start();
        include template('AjaxEvaluteList');
        $Html=ob_get_contents();
        ob_clean();
        $json_result=array(
                'ResultCode'=>$ResultCode,
                'RecordCount'=>$Data['RecordCount'],
                'PageSize'=>$Data['PageSize'],
                'PageCount'=>$Data['PageCount'],
                'Page'=>$Data['Page'],
                'NextPage'=>$NextPage,
                'BackPage'=>$BackPage,
                'LastPage'=>$Data['PageCount'],
                'FirstPage'=>1,
                'PageNums'=>$Data['PageNums'],
                'Data'=>$Html
         );        
        echo json_encode($json_result);
    }
}