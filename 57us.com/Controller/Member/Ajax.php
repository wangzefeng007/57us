<?php

/**
 * @desc 会员中心Ajax
 * Class Ajax
 */
class Ajax
{
    public function Index()
    {
        $Intention = trim($_POST['Intention'])?trim($_POST['Intention']):trim($_GET['Intention']);
        if ($Intention == '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            if($_GET['Intention']){
                echo 'jsonpCallback('.json_encode($json_result).')';
            }
            elseif($_POST['Intention']){
                echo json_encode($json_result);
            }
            exit;
        }
        $this->$Intention ();
    }

    //----------------------------------  判断是否登录  --------------------------------//
    private function LoginStatus(){
        if(!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])){
            $json_result=array('ResultCode'=>0,'Message'=>'请先登录');
        }else{
            $json_result=array('ResultCode'=>1,'Message'=>'已登录');
        }
        echo 'LoginStatus('.json_encode($json_result).')';
    }

    /**
     * @desc  检测账号是否可用
     */
    private function CheckUser()
    {
        $Account = trim($_POST ['User']);
        if ($Account) {
            $User = new MemberUserModule ();
            $UserInfo = $User->AccountExists($Account);
            if ($UserInfo) {
                $result = array('ResultCode' => 100, 'Message' => '账号已存在');
            } else {
                $result = array('ResultCode' => 200, 'Message' => '账号可用');
            }
        } else {
            $result = array('ResultCode' => 100, 'Message' => '账号已存在');
        }
        echo json_encode($result);
    }

    /**
     * @desc  检测昵称是否可用
     */
    private function CheckNickName()
    {
        $NickName = trim($_POST ['NickName']);
        if ($NickName) {
            $UserInfo = new MemberUserInfoModule ();
            $SearchResult = $UserInfo->CheckNickName($NickName);
            if ($SearchResult) {
                $result = array('ResultCode' => 100);
            } else {
                $result = array('ResultCode' => 200);
            }
        } else {
            $result = array('ResultCode' => 100);
        }
        echo json_encode($result);
    }

    /**
     * @desc  会员中心我的资料保存用户信息
     */
    private function ModifyUserInfo()
    {
        $MemberUserInfoModule = new MemberUserInfoModule ();
        $Data = array();
        if (isset ($_POST ['NickName']) && trim($_POST ['NickName']) != '') {
            $Data ['NickName'] = trim($_POST ['NickName']);
            $UserInfo = $MemberUserInfoModule->CheckNickName($Data ['NickName']);
            if ($UserInfo && $UserInfo['UserID'] != $_SESSION['UserID']) {
                $json_result = array('ResultCode' => 100, 'Message' => '保存失败,昵称已经存在');
                echo json_encode($json_result);
                exit ();
            }
        }
        if (isset ($_POST ['Sex']) && trim($_POST ['Sex']) != '') {
            $Data ['Sex'] = $_POST ['Sex'];
        }
        if (isset ($_POST ['BirthDay']) && trim($_POST ['BirthDay']) != '') {
            $Data ['BirthDay'] = trim($_POST ['BirthDay']);
        }
        if (isset ($_POST ['Province']) && trim($_POST ['Province']) != '') {
            $Data ['Province'] = trim($_POST ['Province']);
        }
        if (isset ($_POST ['City']) && trim($_POST ['City']) != '') {
            $Data ['City'] = trim($_POST ['City']);
        }
        if (isset ($_POST ['Area']) && trim($_POST ['Area']) != '') {
            $Data ['Area'] = trim($_POST ['Area']);
        }
        if (isset ($_POST ['Address']) && trim($_POST ['Address']) != '') {
            $Data ['Address'] = trim($_POST ['Address']);
        }
        if (isset ($_POST ['Signaure'])) {
            $Data ['Signature'] = trim($_POST ['Signaure']);
        }
        if (isset ($_POST ['RealName'])) {
            $Data ['RealName'] = trim($_POST ['RealName']);
        }
        if (isset($_POST['Image'])) {
            $Data ['Avatar'] = $this->UploadPictures(current($_POST['Image']), '/Uploads/User/');
        }
        if (isset ($_POST ['CardNum'])) {
            $Data ['CardNum'] = trim($_POST ['CardNum']);
        }
        if (isset ($_POST ['Occupation']) && trim($_POST ['Occupation']) != '') {
            $Data ['Occupation'] = trim($_POST ['Occupation']);
        }
        if (isset ($_POST ['CardPositive'])) {
            $Data ['CardPositive'] = trim($_POST ['CardPositive']);
        }
        if (isset ($_POST ['CardBack'])) {
            $Data ['CardBack'] = trim($_POST ['CardBack']);
        }
        if (count($Data)) {
            $Result = $MemberUserInfoModule->UpdateData($Data, $_SESSION ['UserID']);
            if ($Result || $Result === 0) {
                $_SESSION['NickName'] = $Data ['NickName'];
                if ($_POST['identity'] == 0) {
                    $json_result = array('ResultCode' => 200, 'Message' => '保存成功');
                } else {
                    if ($_POST['identity'] == 1) {
                        $Url = '/consultantmember/certification/';
                    } elseif ($_POST['identity'] == 2) {
                        $Url = '/teachermember/certification/';
                    }
                    $json_result = array('ResultCode' => 200, 'Message' => '保存成功', 'Url' => $Url);
                }

            } else {
                $json_result = array('ResultCode' => 101, 'Message' => '保存失败,重新尝试');
            }
        } else {
            $_SESSION['NickName'] = $Data ['NickName'];
            $json_result = array('ResultCode' => 200, 'Message' => '保存成功');
        }
        echo json_encode($json_result);
    }

    //-------------------------------- 站内信(开始) -----------------------------------------//
    /**
     * @desc 获取站内信内容
     */
    private function ReadMessageContent(){
        $SendID = $_POST['ID'];
        $MessageSendModule = new MemberMessageSendModule();
        $MemberMessageInfoModule = new MemberMessageInfoModule();
        $MessageSendInfo = $MessageSendModule->GetInfoByKeyID($SendID);
        if($MessageSendInfo['Status'] == 1){
            $MessageSendModule->UpdateInfoByKeyID(array('Status'=>2),$SendID);
        }
        $MessageInfo = $MemberMessageInfoModule->GetInfoByKeyID($MessageSendInfo['MessageID']);
        $json_result = array('ResultCode'=>200,'text'=>$MessageInfo['Content']);
        echo json_encode($json_result);
    }

    /**
     * @desc  批量处理站内信为已读
     */
    private function MessageMultiOperate(){
        $MessageSendModule = new MemberMessageSendModule();
        $IDs = $_POST['IDs'];
        $Status = $_POST['Status'];
        foreach($IDs as $val){
            $MessageSendModule->UpdateInfoByKeyID(array('Status'=>$Status),$val);
        }
        $json_result = array('ResultCode'=>200,'Message'=>'操作成功');
        echo json_encode($json_result);
    }

    /**
     * @desc  删除单条站内信
     */
    private function OneMessageDel(){
        $MessageSendModule = new MemberMessageSendModule();
        $ID = $_POST['ID'];
        $MessageSendModule->UpdateInfoByKeyID(array('Status'=>3),$ID);
        $json_result = array('ResultCode'=>200,'Message'=>'操作成功');
        echo json_encode($json_result);
    }

    //-------------------------------- 站内信(结束) -----------------------------------------//

    /**
     * @desc  上传头像
     */
    private function SaveAvatar()
    {
        $Img = trim($_POST['Img']);
        $Img = preg_replace('/^data\:image\/jpeg\;base64\,/iU', '', $Img);
        $ImgUrl = '/up/' . date('Y') . '/' . date('md') . '/' . date('YmdHis') . mt_rand(100, 999) . '.jpg';
        if ($Img) {
            $MemberUserInfoModule = new MemberUserInfoModule();
            $UserInfo = $MemberUserInfoModule->GetInfoByUserID($_SESSION['UserID']);
            if ($UserInfo['Avatar'] != '') {
                if (!strpos($UserInfo['Avatar'], 'http://')) {
                    //删除图片
                    DelFromImgServ($UserInfo['Avatar']);
                }
            }
            if ($MemberUserInfoModule->UpdateData(array('Avatar' => $ImgUrl), $_SESSION['UserID'])) {
                //上传图片服务器
                if (SendToImgServ($ImgUrl, $Img) == 'true') {
                    $_SESSION['Avatar'] = LImageURL . $ImgUrl;
                    $json_result = array('ResultCode' => 200, 'Message' => '保存成功', 'ImgUrl' => $_SESSION['Avatar']);
                } else {
                    $json_result = array('ResultCode' => 102, 'Message' => '上传失败');
                }
            } else {
                $json_result = array('ResultCode' => 101, 'Message' => '保存失败');
            }
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '保存失败');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc 获取客户SESSION信息
     */
    private function GetSession()
    {
        $MemberUserInfoModule = new MemberUserInfoModule ();
        $Data ['UserID'] = $_POST ['ID'];
        $Data ['Account'] = $_POST ['Account'];
        $UserInfo = $MemberUserInfoModule->GetUserInfo($Data ['UserID']);
        $Data ['Identity'] = $UserInfo['Identity'];
        $Data ['NickName'] = $UserInfo ['NickName'];
        $Data ['Level'] = $UserInfo ['Level'];
        $Data ['CountIntegral'] = $UserInfo ['CountIntegral'];
        $Data ['Integral'] = $UserInfo ['Integral'];
        $Data ['Avatar'] = $UserInfo ['Avatar'];
        $MemberMessageSend = new MemberMessageSendModule();
        $SendInfo = $MemberMessageSend->GetInfoByWhere(' and UserID= '.$Data['UserID'].' and Status = 1',true);
        $Data['IsHaveMessage'] = count($SendInfo);
        echo json_encode($Data);
    }

    //----------------------------------  我的收藏  ------------------------------------//
    /**
     * @desc  添加收藏
     */
    private function Cross_Domain_Collection()
    {
        MemberService::IsLogin();
        if ($_GET){
            $CollectionModule = new MemberCollectionModule();
            $Data['Category'] = intval($_GET['Type']);
            $Data['RelevanceID'] = $_GET['ID'];
            $Data['UserID'] = $_SESSION['UserID'];
            $Data['AddTime'] = date('Y-m-d H:i:s', time());
            $Collection = $CollectionModule->GetInfoByWhere(' and UserID = '. $Data['UserID'].' and Category = '.$Data['Category'].' and RelevanceID = '.$Data['RelevanceID']);
            if ($Collection){
                $json_result = array('ResultCode' => '100', 'Message' => '您已收藏');
            }else{
                $Result = $CollectionModule->InsertInfo($Data);
                if ($Result){
                    $json_result = array(
                        'Intention' => 'Cross_Domain_Collection',
                        'ResultCode' => '200',
                        'Message' => '收藏成功'
                    );
                }else{
                    $json_result = array(
                        'Intention' => 'Cross_Domain_Collection',
                        'ResultCode' => '101',
                        'Message' => '收藏失败'
                    );
                }
            }
            echo 'Cross_Domain_Collection('.json_encode($json_result).')';
            exit;
        }
    }
    /**
     * @desc  取消收藏
     */
    private function CancelColl()
    {
        MemberService::IsLogin();
        $CollectionModule = new MemberCollectionModule();
        $ID = $_POST['ID'];
        $result = $CollectionModule->DeleteByWhere(' and CollectionID = '.$ID.' and UserID = '.$_SESSION['UserID']);
        if ($result){
            $array = array('ResultCode' => '200', 'Message' => '取消成功');
        }else{
            $array = array('ResultCode' => '101', 'Message' => '取消失败');
        }
        echo json_encode($array);
        exit;
    }
    //----------------------------------  登录验证  ------------------------------------//
    /**
     * @desc 新增旅客信息
     */
    private function PassengerAdd(){
        MemberService::IsLogin();
        if ($_POST){
            $Data =array();
            $Data ['UserID'] = $_SESSION['UserID'];
            $Data ['ZhName'] = $_POST ['ZhName'];
            $Data ['ZhXinPin'] = trim($_POST ['ZhXinPin']);
            $Data ['ZhMingPin'] = trim($_POST ['ZhMingPin']);
            $Data ['Sex'] = $_POST ['Sex']>0?'男':'女';
            $Data ['BirthDay'] = $_POST ['BirthDay'];
            $Data ['Mobile'] = $_POST ['Mobile'];
            $Data ['Mail'] = $_POST ['Mail'];
            $Data ['IdCard'] = $_POST ['IdCard'];
            $Data ['CardEndDate'] = $_POST ['CardEndDate'];
            $Data ['CardType'] = '护照';
            $Data ['Nationality'] = $_POST ['Nationality'];
            $MemberPassengerModule = new MemberPassengerModule();
            $PassengerID = intval($_POST ['PassengerID']);
            //判断该用户是否有旅客信息，如果没有，新增的第一个为默认旅客
            $PassengerInfo = $MemberPassengerModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
            if ($PassengerInfo){
                $Data ['IsDefault'] = 0;
            }else{
                $Data ['IsDefault'] = 1;
            }
            if ($PassengerID>0){
                $UpdateInfo = $MemberPassengerModule->UpdateInfoByWhere($Data,' PassengerID = '.$PassengerID.' and UserID = '.$_SESSION['UserID']);
              if ($UpdateInfo){
                  $Result = array('ResultCode' => '200', 'Message' => '修改成功','Url' => WEB_MEMBER_URL.'/member/passengerlist/');
              }
              else{
                  $Result = array('ResultCode' => '100', 'Message' => '未操作修改');
              }
            }else{
                $InsertInfo = $MemberPassengerModule->InsertInfo($Data);
                if ($InsertInfo){
                    $Result = array('ResultCode' => '200', 'Message' => '新增成功','Url' => WEB_MEMBER_URL.'/member/passengerlist/');
                }
                else{
                    $Result = array('ResultCode' => '100', 'Message' => '新增失败');
                }
            }
        }
        else{
            $Result = array('ResultCode' => '102', 'Message' => '新增失败');
        }
        echo json_encode($Result);
        exit;
    }
    /**
     * @desc 设置默认旅客
     */
    private function PassengerSetDef(){
        MemberService::IsLogin();
       if ($_POST['ID']){
           $ID = intval($_POST['ID']);
           $MemberPassengerModule = new MemberPassengerModule();
           $MemberPassengerModule->UpdateInfoByWhere(array('IsDefault'=>0),' UserID ='.$_SESSION['UserID']);
           $UpdateInfo = $MemberPassengerModule->UpdateInfoByKeyID(array('IsDefault'=>1),$ID);
           if ($UpdateInfo){
               $Result = array('ResultCode' => '200', 'Message' => '设置成功');
           }else{
               $Result = array('ResultCode' => '100', 'Message' => '设置失败');
           }
           echo json_encode($Result);
           exit;
       }
    }
    /**
     * @desc 搜索旅客
     */
    private function PassengerSearch(){
        MemberService::IsLogin();
        if ($_POST['SearchVal']!=''){
            $Data =array();
            $Data ['ZhName'] = trim($_POST['SearchVal']);
            $MemberPassengerModule = new MemberPassengerModule();
            $PassengerList = $MemberPassengerModule->GetInfoByWhere(' and UserID ='.$_SESSION['UserID'].' and ZhName = \''.$Data ['ZhName'].'\'',true);
            if ($PassengerList){
                foreach ($PassengerList as $key=>$value){
                    $Data['Data'][$key]['List_ID'] = $value['PassengerID'];
                    $Data['Data'][$key]['List_Name'] = $value['ZhName'];
                    $Data['Data'][$key]['List_Sex'] = $value ['Sex'];
                    $Data['Data'][$key]['List_Mobile'] = $value['Mobile'];
                    $Data['Data'][$key]['List_Card'] = $value['IdCard'];
                    $Data['Data'][$key]['List_Url'] = WEB_MEMBER_URL.'/member/passengerlist/'.$value['PassengerID'];
                    $Data['Data'][$key]['List_Default'] = $value['IsDefault'];
                }
                $Data['ResultCode'] = 200;
                $Data['Message'] = '加载成功';
            }else{
                $Data['ResultCode'] = 100;
                $Data['Message'] = '未找到该旅客信息';
            }
        }
        else{
            $Data['ResultCode'] = 101;
            $Data['Message'] = '请输入输入旅客姓名';
        }
        echo json_encode($Data);
        exit;
    }
    /**
     * @desc 删除旅客信息
     */
    private function DelPassenger(){
        MemberService::IsLogin();
        $MemberPassengerModule = new MemberPassengerModule();
        if (isset($_POST['ID'])) {
            $PassengerID = intval($_POST['ID']);
            $DeletePassenger = $MemberPassengerModule->DeleteByWhere(' and PassengerID = '.$PassengerID.' and UserID = '.$_SESSION['UserID']);
            if ($DeletePassenger){
                $Result = array('ResultCode' => '200', 'Message' => '删除成功');
            }else{
                $Result = array('ResultCode' => '100', 'Message' => '删除失败');
            }
            echo json_encode($Result);
            exit;
        }
    }
    /**
     * @desc 新增修改收货地址
     */
    private function AddressAdd()
    {
        MemberService::IsLogin();
        $ID = intval($_POST['ID']);
        $Data['UserID'] = $_SESSION['UserID'];
        $Data['Recipients'] = trim($_POST['Recipients']);
        $Data['Address'] = trim($_POST['Address']);
        $Data['Mobile'] = trim($_POST['Mobile']);
        $Data['Tel'] = trim($_POST['TelArea']).'-'.trim($_POST['Tel']).'-'.trim($_POST['TelExtension']);
        $Data['Postcode'] = trim($_POST['Postcode']);
        $Data['Province'] = trim($_POST['Province']);
        $Data['City'] = trim($_POST['City']);
        $Data['Area'] = trim($_POST['Area']);
        $ShippingAddressModule = new MemberShippingAddressModule();
        //判断该用户是否有收货地址信息，如果没有，新增的第一个为默认收货地址
        $ShippingAddressInfo = $ShippingAddressModule->GetInfoByWhere(' and UserID = '.$_SESSION['UserID']);
        if ($ShippingAddressInfo){
            $Data ['IsDefault'] = 0;
        }else{
            $Data ['IsDefault'] = 1;
        }
        if ($ID>0) {
            $Result = $ShippingAddressModule->UpdateInfoByWhere($Data,' ShippingAddressID = '.$ID.' and UserID = '.$_SESSION['UserID']);
            if ($Result){
                $res = array('ResultCode' => '200', 'Message' => '修改成功','Url' => WEB_MEMBER_URL.'/member/addresslist/');
            }else{
                $res = array('ResultCode' => '100', 'Message' => '未操作修改');
            }
        } else {
            $Result = $ShippingAddressModule->InsertInfo($Data);
            if ($Result) {
                $res = array('ResultCode' => '200', 'Message' => '新增成功','Url' => WEB_MEMBER_URL.'/member/addresslist/');
            } else {
                $res = array('ResultCode' => '101', 'Message' => '新增失败');
            }
        }
        echo json_encode($res);
        exit;
    }
    /**
     * @desc 设置默认收货地址
     */
    private function AddressSetDef(){
        MemberService::IsLogin();
        if ($_POST['ID']){
            $ID = intval($_POST['ID']);
            $ShippingAddressModule = new MemberShippingAddressModule();
            $ShippingAddressModule->UpdateInfoByWhere(array('IsDefault'=>0),' UserID ='.$_SESSION['UserID']);
            $UpdateInfo = $ShippingAddressModule->UpdateInfoByKeyID(array('IsDefault'=>1),$ID);
            if ($UpdateInfo){
                $Result = array('ResultCode' => '200', 'Message' => '设置成功');
            }else{
                $Result = array('ResultCode' => '100', 'Message' => '设置失败');
            }
            echo json_encode($Result);
            exit;
        }
    }

    /**
     * @desc 删除收货地址
     */
    private function DelAddress()
    {
        MemberService::IsLogin();
        $ShippingAddressModule = new MemberShippingAddressModule();
        if (isset($_POST['ID'])) {
            $ShippingAddressID = intval($_POST['ID']);
            $result = $ShippingAddressModule->DeleteByWhere(' and ShippingAddressID = '.$ShippingAddressID.' and UserID = '.$_SESSION['UserID']);
        }
        if ($result) {
            $res = array('ResultCode' => '200', 'Message' => '删除成功');
        } else {
            $res = array('ResultCode' => '100', 'Message' => '删除失败');
        }
        echo json_encode($res);
        exit;
    }

    /**
     * @desc  保存个人资料
     */
    private function PersonalProfile(){
        if (! $_POST) {
            $Data['ResultCode'] = 100;
            EchoResult($Data);
        }
        if ($_POST){
            $UserID = $_SESSION ['UserID'];
            $MemberUserInfoModule = new MemberUserInfoModule();
            $MemberUserModule = new MemberUserModule();
            $Data['NickName'] = trim($_POST['NickName']);
            $Data['Sex'] = trim($_POST['Sex']);
            $Date['Mobile'] = trim($_POST['Mobile']);
            $Date['E-Mail'] = trim($_POST['Email']);
            $UpdateUserInfo = $MemberUserInfoModule->UpdateInfoByWhere($Data,' UserID = '.$UserID);
            $UpdateUser = $MemberUserModule->UpdateInfoByKeyID($Date,$UserID);
            if ($UpdateUserInfo>=0 && $UpdateUser>=0){
                $Data['ResultCode'] = 200;
                $Data['Message'] = '更新成功';
                EchoResult($Data);
            }else{
                $Data['ResultCode'] = 101;
                $Data['Message'] = '更新失败';
            }
        }
    }

    //=====================================================评价开始========================================//
    /**
     * @desc  提交评价
     */
    private function AddEvaluate(){
        $Data['UserID']=$_SESSION['UserID'];
        $Data['TourProductID']=intval($_POST['TourProductID']);
        $Data['OrderNumber']=trim($_POST['OrderNumber']);
        if(($Data['TourProductID']<=0 && is_numeric($Data['TourProductID'])) || empty($Data['OrderNumber'])){
            $json_result = array(
                'ResultCode' => 101,
                'Message' => '评价失败，操作异常',
                'Url' => ''
            );
            echo json_encode($json_result);
            exit;
        }

        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义

        //判断是否有评价资格
        $TourProductOrderModule = new TourProductOrderModule();
        $TourProductOrderInfoModule = new TourProductOrderInfoModule();

        //查询是否为已付款已确认的订单
        $OrderHad=$TourProductOrderModule->GetInfoByWhere(" and UserID={$Data['UserID']} and OrderNumber='{$Data['OrderNumber']}' and `Status`=4");
        //判断该订单是否有效
        $OrderInfoHad=$TourProductOrderInfoModule->GetInfoByWhere(" and TourProductID={$Data['TourProductID']} and OrderNumber='{$Data['OrderNumber']}'");
        if($OrderHad && $OrderInfoHad){
            $Data['ServerFraction']=intval($_POST['ServerFraction']);
            $Data['ConvenientFraction']=intval($_POST['ConvenientFraction']);
            $Data['ExperienceFraction']=intval($_POST['ExperienceFraction']);
            $Data['PerformanceFraction']=intval($_POST['PerformanceFraction']);
            $Data['Content']=$_POST['Content'];
            $Data['AddTime']=time();
            $Data['PraiseNum']=0;
            $Data['FromIP']=GetIP();
            $HasImg=false;
            //上传图片
            $ImageArr=$_POST['Pics'];
            if(!empty($ImageArr) && is_array($ImageArr)){
                foreach($ImageArr as $key=>$val){
                    if(strpos($val,'data:image/jpeg;base64')!==false){
                        $ImageFullUrl='/up/'.date('Y').'/'.date('md').'/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                        SendToImgServ($ImageFullUrl,str_replace('data:image/jpeg;base64,','',$val));
                        $ImageArr[$key]=$ImageFullUrl;
                    }
                }
                $HasImg=true;
                $Data['Images']=json_encode($ImageArr);
            }
            $TourOrderEvaluateModule = new TourOrderEvaluateModule();
            $Result=$TourOrderEvaluateModule->InsertInfo($Data);
            if($Result){
                $Result2 = $TourProductOrderModule->UpdateInfoByKeyID(array('EvaluateDefault'=>1),$OrderHad['OrderID']);
                if(!$Result2){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $json_result = array('ResultCode' => 103, 'Message' => 'TourProductOrderModule更新失败');
                }
                else{
                    $TourOrderEvaluateCountModule=new TourOrderEvaluateCountModule();
                    $TourOrderEvaluateCountInfo=$TourOrderEvaluateCountModule->GetInfoByWhere(" and TourProductID={$Data['TourProductID']}");
                    $TOECData['UpdateTime']=time();
                    if($TourOrderEvaluateCountInfo){
                        $TOECData['ServerFractionAll']=$TourOrderEvaluateCountInfo['ServerFractionAll']+$Data['ServerFraction'];
                        $TOECData['ConvenientFractionAll']=$TourOrderEvaluateCountInfo['ConvenientFractionAll']+$Data['ConvenientFraction'];
                        $TOECData['ExperienceFractionAll']=$TourOrderEvaluateCountInfo['ExperienceFractionAll']+$Data['ExperienceFraction'];
                        $TOECData['PerformanceFractionAll']=$TourOrderEvaluateCountInfo['PerformanceFractionAll']+$Data['PerformanceFraction'];
                        $TOECData['Times']=$TourOrderEvaluateCountInfo['Times']+1;
                        if($HasImg){
                            $TOECData['ImagesTimes']=$TourOrderEvaluateCountInfo['ImagesTimes']+1;
                        }
                        $Result3 = $TourOrderEvaluateCountModule->UpdateInfoByWhere($TOECData,"TourProductID={$Data['TourProductID']}");
                    }else{
                        $TOECData['TourProductID']=$Data['TourProductID'];
                        $TOECData['ServerFractionAll']=$Data['ServerFraction'];
                        $TOECData['ConvenientFractionAll']=$Data['ConvenientFraction'];
                        $TOECData['ExperienceFractionAll']=$Data['ExperienceFraction'];
                        $TOECData['PerformanceFractionAll']=$Data['PerformanceFraction'];
                        $TOECData['Times']=1;
                        if($HasImg){
                            $TOECData['ImagesTimes']=1;
                        }else{
                            $TOECData['ImagesTimes']=0;
                        }
                        $TourProductOrderModule->UpdateInfoByWhere(array("EvaluateDefault"=>1)," UserID={$Data['UserID']} and OrderNumber='{$Data['OrderNumber']}'");
                        $Result3 = $TourOrderEvaluateCountModule->InsertInfo($TOECData);
                    }
                    if($Result3 === false){
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $json_result = array('ResultCode' => 103, 'Message' => 'TourOrderEvaluateCountModule更新失败');
                    }
                    else{
                        $DB->query("COMMIT");//执行事务
                        $json_result = array('ResultCode' => 200, 'Message' => '评价成功', 'Url'=>WEB_MEMBER_URL.'/membertour/tourorderlist/');
                    }
                }
            }else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $json_result = array('ResultCode' => 101, 'Message' => '评价失败,系统异常');
            }
        }else{
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            $json_result = array('ResultCode' => 101, 'Message' => '评价失败,没有权限评价此产品', 'Url' => '');
        }
        echo json_encode($json_result);
    }
    //=====================================================评价结束========================================//

    /**
     * @desc  获取资金账户流水信息
     */
    private function MoneyDetails(){
        $MoneyType = $_POST['MoneyType']?$_POST['MoneyType']:0;
        $UserID = $_SESSION['UserID'];
        $BankFlowModule = new MemberUserBankFlowModule();
        //提现状态
        $WithdrawStatus = $BankFlowModule->WithdrawStatus;
        $MysqlWhere ='';
        if ($MoneyType == 1){ //收入
            $MysqlWhere .=  ' and `OperateType` in (1,4,5)';
        }
        elseif($MoneyType == 2){ //支出
            $MysqlWhere .=  ' and `OperateType`=2';
        }
        elseif($MoneyType == 3){ //提现
            $MysqlWhere .=  ' and `OperateType`=3';
        }
        $MysqlWhere .= ' and UserID = '.$UserID.' order by AddTime desc';
        $Page = intval($_POST['Page'])< 1 ? 1 : intval($_POST['Page']);
        $PageSize = 5;
        $Rscount = $BankFlowModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data ['Data'] = $BankFlowModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            $DataResult = array();
            foreach($Data ['Data'] as $key=>$val){
                $DataResult[$key]['List_Time'] = date("Y-m-d",$val['AddTime']);
                $DataResult[$key]['List_Money'] = $val['Amt'];
                $DataResult[$key]['List_Details'] = $val['Remarks'];
                $DataResult[$key]['List_Status'] = '操作成功';
            }
        }
        if($Data ['Data']){
            $result_json = array('ResultCode'=>200,'PageCount'=>$Data ['PageCount'],'Page'=>$Page,'Data'=>$DataResult);
        }
        else{
            $result_json = array('ResultCode'=>201,'Message'=>'没有交易明细');
        }
        echo json_encode($result_json);
    }

    /**
     * @desc 资金提现
     */
    private function WithdrawDeposit(){
        $Amt = $_POST['TransactionMoney'];
        $Account = $_POST['TransactionAccount'];
        $AccountType = $_POST['TransactionType'];
        $UserID = $_SESSION['UserID'];

        $BankModule = new MemberUserBankModule();
        $BankInfo = $BankModule->GetInfoByWhere(' and UserID='.$_SESSION['UserID']);
        if ($Amt<=0){
            $result_json = array('ResultCode'=>204,'Message'=>'您提现的金额不能为零');
        }elseif($BankInfo['FreeBalance'] < $Amt){
            $result_json = array('ResultCode'=>203,'Message'=>'您提现的金额大于您的余额');
        }
        else{
            $BankWithdrawModule = new MemberUserBankWithdrawModule();
            $WithdrawType = $BankWithdrawModule->WithdrawType;
            //开启事务
            global $DB;
            $DB->query("BEGIN");//开始事务定义
            $Data = array(
                'UserID'=>$UserID,
                'Amt'=>$Amt, //此次操作金额
                'Remarks'=>$WithdrawType[$AccountType].':'.$Account,
                'FromIP'=>GetIP(),
                'AddTime'=>time(),
                'WithdrawStatus'=>1,//提现中
                'WithdrawAccounts'=>$Account,
                'WithdrawType'=>$AccountType
            );
            $Result1 = $BankWithdrawModule->InsertInfo($Data);
            if(!$Result1){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $result_json = array('ResultCode'=>201,'Message'=>'提现失败','Describe'=>'BankWithdrawModule更新失败');
            }
            else{
                $Data1 = array(
                    'FrozenBalance'=>$BankInfo['FrozenBalance']+$Amt,
                    'FreeBalance'=>$BankInfo['FreeBalance']-$Amt
                );
                $Result2 = $BankModule->UpdateInfoByKeyID($Data1,$BankInfo['BankID']);
                if(!$Result2){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $result_json = array('ResultCode'=>202,'Message'=>'提现失败','Describe'=>'BankModule更新失败');
                }
                else{
                    $UserBankFlowModule = new MemberUserBankFlowModule();
                    $FlowData = array('UserID'=>$UserID,'Amt'=>'-'.$Amt,'Amount'=>$BankInfo['TotalBalance']-$Amt,'OperateType'=>3,'Remarks'=>'提现帐号:'.$Account.'-提现中','Type'=>3,'PayType'=>0,'FromIP'=>GetIP(),'AddTime'=>time());
                    $Result3 = $UserBankFlowModule->InsertInfo($FlowData);
                    if(!$Result3){
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $result_json = array('ResultCode'=>202,'Message'=>'提现失败','Describe'=>'MemberUserBankFlowModule更新失败');
                    }
                    else{
                        $DB->query("COMMIT");//执行事务
                        $result_json = array('ResultCode'=>200,'Message'=>'提现成功');
                    }
                }
            }
        }
        echo json_encode($result_json);
    }

    //=====================================================安全中心开始========================================//
    /**
     * @desc  密码修改
     */
    private function ModifyPass()
    {
        $PassWord = md5($_POST ['Pass']);
        $MemberUserModule = new MemberUserModule ();
        $User = $MemberUserModule->GetUserByID($_SESSION ['UserID']);
        if ($User['PassWord'] == '' || $PassWord == $User ['PassWord']) {
            $Data ['PassWord'] = md5($_POST ['NewPass']);
            $Result = $MemberUserModule->UpdateUser($Data, $_SESSION ['UserID']);
            if ($Result || $Result === 0) {
                $json_result = array('ResultCode' => 200, 'Message' => '修改成功', 'Url' => '/member/accountsafety/');
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '修改失败,请重试');
            }
        } else {
            $json_result = array('ResultCode' => 101, 'Message' => '原密码输入错误');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc 手机
     * @desc 验证手机号码是否绑定过其它账号
     */
    private function MobileExists(){
        $Mobile = $_POST['Mobile'];
        $UserModule = new MemberUserModule();
        $Result = $UserModule->GetInfoByWhere(' and Mobile = '.$Mobile);
        if(!$Result){
            $json_result = array('ResultCode' => 200);
        }
        else{
            $json_result = array('ResultCode' => 100, 'Message' => '该手机号码已绑定过其它账号');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc 手机
     * @desc 绑定手机获取验证码，更换手机获取验证码
     */
    private function VerificationCode(){
        if($_POST['Mobile']){
            $Mobile = $_POST['Mobile'];
        }
        else{
            $UserModule = new MemberUserModule();
            $User = $UserModule->GetInfoByKeyID($_SESSION['UserID']);
            $Mobile = $User['Mobile'];
        }
        //调用短信验证码发送接口
        $Result = MemberService::SendMobileVerificationCode($Mobile);
        echo json_encode($Result);
    }

    /**
     * @desc 手机
     * @desc 验证旧手机验证码是否正确
     */
    private function VerifyMobile(){
        $VerifyCode = intval(trim($_POST['Code']));
        $UserModule = new MemberUserModule();
        $User = $UserModule->GetInfoByKeyID($_SESSION['UserID']);
        //验证短信验证码
        $Result = MemberService::VerifySendCode($VerifyCode,$User['Mobile']);
        echo json_encode($Result);
    }

    /**
     * @desc 手机
     * @desc 绑定手机号码
     */
    private function BindingMobile(){
        //判断是否登陆
        MemberService::IsLogin();
        $VerifyCode = intval(trim($_POST['Code']));
        $Mobile = $_POST['Mobile'];
        //验证短信验证码
        $Result = MemberService::VerifySendCode($VerifyCode,$Mobile);
        if($Result['ResultCode'] == 200){ //验证码验证通过
            $UserModule = new MemberUserModule();
            $Result1 = $UserModule->UpdateInfoByKeyID(array('Mobile'=>$Mobile),$_SESSION['UserID']);
            if($Result1){
                $result_josn = array('ResultCode' => 200, 'Message' => '手机号码更新成功');
            }
            else{
                $result_josn = array('ResultCode' => 101, 'Message' => '手机号码更新失败');
            }
        }
        else{
            $result_josn = $Result;
        }
        echo json_encode($result_josn);
    }

    /**
     * @desc 邮箱
     * @desc 验证邮箱号码是否绑定过其它账号
     */
    private function MailExists(){
        $Mail = $_POST['Mail'];
        $UserModule = new MemberUserModule();
        $Result = $UserModule->GetInfoByWhere("and `E-Mail` = '{$Mail}'");
        if(!$Result){
            $json_result = array('ResultCode' => 200);
        }
        else{
            $json_result = array('ResultCode' => 100, 'Message' => '该邮箱已绑定过其它账号');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc 邮箱
     * @desc 绑定邮箱获取验证码，更换邮箱获取验证码
     */
    private function VerificationMailCode(){
        if($_POST['Mail']){
            $Email = $_POST['Mail'];
        }
        else{
            $UserModule = new MemberUserModule();
            $User = $UserModule->GetInfoByKeyID($_SESSION['UserID']);
            $Email = $User['E-Mail'];
        }
        //调用短信验证码发送接口
        $Result = MemberService::SendMailVerificationCode($Email);
        echo json_encode($Result);
    }

    /**
     * @desc 邮箱
     * @desc 验证旧邮箱验证码是否正确
     */
    private function VerifyMail(){
        $VerifyCode = intval(trim($_POST['Code']));
        $UserModule = new MemberUserModule();
        $User = $UserModule->GetInfoByKeyID($_SESSION['UserID']);
        //验证短信验证码
        $Result = MemberService::VerifySendCode($VerifyCode,$User['E-Mail']);
        echo json_encode($Result);
    }

    /**
     * @desc 邮箱
     * @desc 绑定邮箱号码
     */
    private function BindingMail(){
        //判断是否登陆G
        MemberService::IsLogin();
        $VerifyCode = intval(trim($_POST['Code']));
        $Email = $_POST['Mail'];
        //验证短信验证码
        $Result = MemberService::VerifySendCode($VerifyCode,$Email);
        if($Result['ResultCode'] == 200){ //验证码验证通过
            $UserModule = new MemberUserModule();
            $Result1 = $UserModule->UpdateInfoByKeyID(array('E-Mail'=>$Email),$_SESSION['UserID']);
            if($Result1){
                $result_josn = array('ResultCode' => 200, 'Message' => '邮箱更新成功');
            }
            else{
                $result_josn = array('ResultCode' => 101, 'Message' => '邮箱更新失败');
            }
        }
        else{
            $result_josn = $Result;
        }
        echo json_encode($result_josn);
    }

}