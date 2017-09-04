<?php/** * @desc  会员中心订单Ajax * Class AjaxOrder */class AjaxOrder{    public function __construct()    {    }    public function Index()    {        $Intention = trim($_POST ['Intention']);        if ($Intention == '') {            $json_result = array(                'ResultCode' => 500,                'Message' => '系統錯誤',                'Url' => ''            );            echo json_encode($json_result);            exit;        }        $this->$Intention ();    }    public function CarRentOrderEdit()    {        if ($_POST) {            $ID = $_POST['OrderID'];//订单号            $Date['RefundReason'] = $_POST['Type'];//取消订单理由            if (!$ID) {                $JsonResult = array('ResultCode' => 102, 'Message' => '缺少订单id');                EchoResult($JsonResult);            }            $ZucheOrderModule = new ZucheOrderModule();            $CarAPI = new ZuZuCheAPI();            $OrderLogModule = new TourProductOrderLogModule();            //添加订单状态更新日志            $MysqlWhere = ' and ID= ' . $ID . ' and UserID = ' . $_SESSION['UserID'];            $OrderInfo = $ZucheOrderModule->GetInfoByWhere($MysqlWhere);            if ($OrderInfo['Status'] == '1') {                $Date['Status'] = '13';                $ZucheOrderModule->UpdateInfoByKeyID($Date, $ID);                $LogData = array('OrderNumber' => $OrderInfo['OrderNum'], 'UserID' => $_SESSION['UserID'], 'OldStatus' => $OrderInfo['Status'], 'NewStatus' => $Date['Status'], 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => '4');                $LogResult = $OrderLogModule->InsertInfo($LogData);                $Data['orderId'] = $OrderInfo['OrderNo'];                $result = $CarAPI->cancelTmpOrder($Data);                //******************发送通知短信到客服，通知用户关闭租车订单start********************//                $PInfo['Message'] = '有用户取消旅游租车订单，订单号为：' . $OrderInfo['OrderNum'] . '，麻烦24个小时内联系手机' . $OrderInfo['contractPhone'] . '。【57美国网】';                $Mobile = '18750258578';                $result = ToolService::SendSMSNotice($Mobile, $PInfo['Message']);                //******************发送通知短信到客服，通知用户关闭租车订单end********************//                $JsonResult = array('ResultCode' => 200, 'Message' => '关闭订单成功');                //******************发送通知邮件到客服，通知用户关闭租车订单start********************//                ToolService::SendEMailNotice('linling@57us.com', '用户关闭订单', '客户：' . $OrderInfo['contractGivenname'] . '，订单号：' . $OrderInfo['OrderNum'] . ' 联系人手机号码：' . $OrderInfo['contractPhone']);                //******************发送通知邮件到客服，通知用户关闭租车订单end********************//                EchoResult($JsonResult);            } elseif ($OrderInfo['Status'] == '2' || $OrderInfo['Status'] == '3') {                $Data['orderId'] = $OrderInfo['OrderNo'];                $Date['ExpirationTime'] = date("Y-m-d H:i:s", time() + 172800);                $Data['phone'] = $OrderInfo['contractPhone'];                $Data['email'] = $OrderInfo['contractEmail'];                $Data['cancelReason'] = $Date['RefundReason'];                $result = $CarAPI->CancelOrder($Data);//到租租车接口申请退款                $Date['Status'] = '5';                $ZucheOrderModule->GetInfoByKeyID($Date, $ID);                $LogData = array('OrderNumber' => $OrderInfo['OrderNum'], 'UserID' => $_SESSION['UserID'], 'OldStatus' => $OrderInfo['Status'], 'NewStatus' => $Date['Status'], 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => '4');                $LogResult = $OrderLogModule->InsertInfo($LogData);                //******************发送通知短信到客服，通知用户申请租车订单退款start********************//                $PInfo['Message'] = '有用户申请旅游租车订单退款，订单号为：' . $OrderInfo['OrderNum'] . '，麻烦24个小时内联系手机' . $OrderInfo['contractPhone'] . '。【57美国网】';                $Mobile = '18750258578';                //ToolService::SendSMSNotice($Mobile, $PInfo['Message']);                //******************发送通知短信到客服，通知用户申请租车订单退款end********************//                $JsonResult = array('ResultCode' => 200, 'Message' => '退款申请成功');                //******************发送通知邮件到客服，通知用户申请租车订单退款start********************//                $rs = ToolService::SendEMailNotice('linling@57us.com', '用户申请退款', '客户：' . $OrderInfo['contractGivenname'] . '，订单号：' . $OrderInfo['OrderNum'] . ', 联系人手机号码：' . $OrderInfo['contractPhone']);                //******************发送通知邮件到客服，通知用户申请租车订单退款end********************//                EchoResult($JsonResult);            }        } else {            $JsonResult = array('ResultCode' => 100, 'Message' => '返回失败');            EchoResult($JsonResult);        }    }    /**     * @desc  租车支付     */    public function OrderPay()    {        if ($_POST) {            $OrderNum = trim($_POST['OrderNum']);//订单号            if (!$_POST['Status']) {                $JsonResult = array('ResultCode' => 101, 'Message' => '缺少订单状态');                EchoResult($JsonResult);            }            if (!$OrderNum) {                $JsonResult = array('ResultCode' => 102, 'Message' => '缺少订单号');                EchoResult($JsonResult);            }            $ZucheOrderModule = new ZucheOrderModule();            $CarAPI = new ZuZuCheAPI();            if ($_POST['Status'] == 3) {                $Date['Status'] = '关闭订单';//订单状态            }            $update = $ZucheOrderModule->UpdateByOrderNum($Date, $OrderNum);            if ($update) {                $JsonResult = array('ResultCode' => 200, 'Message' => '订单状态更新成功');                EchoResult($JsonResult);            } else {                $JsonResult = array('ResultCode' => 201, 'Message' => '订单状态更新失败');                EchoResult($JsonResult);            }        } else {            $JsonResult = array('ResultCode' => 100, 'Message' => '返回失败');            EchoResult($JsonResult);        }    }    /**     * @desc  取消酒店订单     */    private function CancelHotelOrder()    {        if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {            $json_result = array('ResultCode' => 101, 'Message' => '请先登录', 'Url' => WEB_MEMBER_URL);        } else {            $OrderNo = trim($_POST['OrderNum']);            $UpData['Closereason'] = trim($_POST['text']);            $UpData['UpdateTime'] = date('Y-m-d H:i:s', time());            $HotelOrderModule = new HotelOrderModule();            $OrderInfo = $HotelOrderModule->GetByNoAndUID($OrderNo, $_SESSION['UserID']);            if ($OrderInfo) {                //插入日志                $TourProductOrderLogModule = new TourProductOrderLogModule();                $OrderLogData['OrderNumber'] = $OrderInfo['OrderNo'];                $OrderLogData['UserID'] = $OrderInfo['UserID'];                $OrderLogData['OperateTime'] = date('Y-m-d H:i:s');                $OrderLogData['IP'] = GetIP();                $OrderLogData['Type'] = 3;                $TourProductOrderLogModule->InsertInfo($OrderLogData);                if ($OrderInfo['Status'] == 1) {                    $OrderLogData['OldStatus'] = 1;                    $OrderLogData['NewStatus'] = 13;                    $UpData['Status'] = 13;                    if ($HotelOrderModule->UpdateByOrderNum($UpData, $OrderInfo['OrderNo'])) {                        $OrderLogData['Remarks'] = '取消订单工成功';                        $json_result = array('ResultCode' => 200, 'Message' => '取消订单成功');                    } else {                        $OrderLogData['Remarks'] = '取消订单工成功';                        $json_result = array('ResultCode' => 102, 'Message' => '取消订单失败');                    }                    $TourProductOrderLogModule->InsertInfo($OrderLogData);                } elseif ($OrderInfo['Status'] == 2 || $OrderInfo['Status'] == 3 || $OrderInfo['Status'] == 4) {                    if ($OrderInfo['PayType'] != 0) {                        $OrderLogData['OldStatus'] = 2;                        $OrderLogData['NewStatus'] = 5;                        $UpData['Status'] = 5;                        if ($HotelOrderModule->UpdateByOrderNum($UpData, $OrderInfo['OrderNo'])) {                            $OrderLogData['Remarks'] = '提交退款申请成功';                            $json_result = array('ResultCode' => 200, 'Message' => '提交退款申请成功');                        } else {                            $OrderLogData['Remarks'] = '提交退款申请失败';                            $json_result = array('ResultCode' => 104, 'Message' => '提交退款申请失败');                        }                    } else {                        $OrderLogData['Remarks'] = '提交退款申请失败,未支付的订单';                        $json_result = array('ResultCode' => 103, 'Message' => '提交退款申请失败，该单不能退款');                    }                    $TourProductOrderLogModule->InsertInfo($OrderLogData);                }            } else {                $json_result = array('ResultCode' => 100, 'Message' => '订单不存在');            }        }        echo json_encode($json_result);    }    /**     * @desc  取消高端定制订单     */    private function DingZhiOrderEdit()    {        if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {            $json_result = array('ResultCode' => 101, 'Message' => '请先登录', 'Url' => WEB_MEMBER_URL);        } else {            //发送短信module            require_once SYSTEM_ROOTPATH . '/Include/ManDaoSmsApi.php';            $smsapi = new ManDaoSmsApi();            $OrderNo = trim($_POST['OrderNum']);            $Data['RefundReason'] = trim($_POST['text']);            $TourPrivateOrderModule = new TourPrivateOrderModule();            //添加订单状态更新日志            $OrderLogModule = new TourProductOrderLogModule();            $OrderInfo = $TourPrivateOrderModule->GetInfoByWhere(" and OrderNo='" . $OrderNo . "' and UserID=" . $_SESSION['UserID']);            $OldStatus = $OrderInfo['Status'];            if ($OldStatus == 0 || $OldStatus == 1) {                $Date['Status'] = 13;                $UpdateByOrderNum = $TourPrivateOrderModule->UpdateByOrderNum($Date, $OrderNo);                $LogData = array('OrderNumber' => $OrderNo, 'UserID' => $_SESSION['UserID'], 'OldStatus' => $OldStatus, 'NewStatus' => $Date['Status'], 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => '5');                $LogResult = $OrderLogModule->InsertInfo($LogData);                if ($UpdateByOrderNum) {                    $PInfo['Message'] = '有用户取消旅游特色定制订单，订单号为：' . $OrderNo . '，麻烦24个小时内联系手机' . $OrderInfo['Phone'] . '。【57美国网】';                    $Mobile = '18750258578';                    $PInfo['MName'] = '客服';                    $result = $smsapi->sendSMS($Mobile, $PInfo['Message']);                    $json_result = array('ResultCode' => 200, 'Message' => '取消订单成功');                } else {                    $json_result = array('ResultCode' => 301, 'Message' => '取消订单失败');                }            } elseif ($OldStatus > 1 && $OldStatus < 5) {                $Data['Status'] = 5;                $Update = $TourPrivateOrderModule->UpdateByOrderNum($Data, $OrderNo);                $LogData = array('OrderNumber' => $OrderNo, 'UserID' => $_SESSION['UserID'], 'OldStatus' => $OldStatus, 'NewStatus' => $Data['Status'], 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => '5');                $LogResult = $OrderLogModule->InsertInfo($LogData);                if ($Update) {                    $PInfo['Message'] = '有用户申请退款旅游特色定制订单，订单号为：' . $OrderNo . '，麻烦24个小时内联系手机' . $OrderInfo['Phone'] . '。【57美国网】';                    $Mobile = '18750258578';                    $PInfo['MName'] = '客服';                    $result = $smsapi->sendSMS($Mobile, $PInfo['Message']);                    $json_result = array('ResultCode' => 200, 'Message' => '申请退款成功');                } else {                    $json_result = array('ResultCode' => 301, 'Message' => '申请退款失败');                }            }            echo json_encode($json_result);        }    }    /*     * @desc 取消旅游订单或者申请退款     */    public function CancelTourOrder()    {        if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {            $json_result = array('ResultCode' => 101, 'Message' => '请先登录', 'Url' => WEB_MEMBER_URL);        } else {            $TourOrderModule = new TourProductOrderModule();            $OrderID = trim($_POST['OrderID']);            $NewStatus = intval($_POST['Status']);            $OrderInfo = $TourOrderModule->GetInfoByWhere(" and OrderID='" . $OrderID . "' and UserID=" . $_SESSION['UserID']);            $OldStatus = $OrderInfo['Status'];            if ($OrderInfo && $OldStatus==1) {                //开启事务                global $DB;                $DB->query("BEGIN");//开始事务定义                $UpData['Status'] = 13;                $UpData['Status'] = $NewStatus;                $result = $TourOrderModule->UpdateInfoByOrderNumber($UpData, $OrderInfo['OrderNumber']);                if ($result) {                    //更新产品库存                    $TourOrderInfoModule = new TourProductOrderInfoModule();                    $OrderOrderInfo = $TourOrderInfoModule->GetInfoByKeyID($OrderID);                    if (!empty($OrderOrderInfo['TourLineSnapshotID'])) {                        $ErverdayPriceModule = new TourProductLineErverDayPriceModule();                        $OrderInfoLists = $TourOrderInfoModule->GetInfoByWhere(" and OrderID = $OrderID", true);                        $ErverdayPriceResult = true;                        foreach ($OrderInfoLists as $val) {                            $DateStr = date('Ymd', strtotime($val['Depart']));                            $TourPriceInfo = $ErverdayPriceModule->GetInfoByWhere(" and TourProductID={$val['TourProductID']} and ProductSkuID={$val['TourProductSkuID']} and `Date`='$DateStr'");                            if ($TourPriceInfo && $TourPriceInfo['Inventory'] != -1) {                                if (!$ErverdayPriceModule->UpdateInfoByKeyID(array('Inventory' => $TourPriceInfo['Inventory'] + $val['Num']), $TourPriceInfo['DayPriceID'])) {                                    $ErverdayPriceResult = false;                                }                            }                        }                    } elseif (!empty($OrderOrderInfo['TourPlaySnapshotID'])) {                        $ErverdayPriceModule = new TourProductPlayErverDayPriceModule();                        $Date = date('Ymd', strtotime($OrderOrderInfo['Depart']));                        $ErverdayPriceResult = $ErverdayPriceModule->UpdateSkuInventoryBy($OrderOrderInfo['TourProductSkuID'], $Date);                    }                    if ($ErverdayPriceResult) {                        $DB->query("COMMIT");//执行事务                        $json_result = array('ResultCode' => 200, 'Message' => '取消订单成功', 'Remarks' => '操作成功');                    } else {                        $DB->query("ROLLBACK");//判断当执行失败时回滚                        $json_result = array('ResultCode' => 104, 'Message' => '取消订单失败', 'Remarks' => '操作失败(库存更新失败)');                    }                } else {                    $DB->query("ROLLBACK");//判断当执行失败时回滚                    $json_result = array('ResultCode' => 102, 'Message' => '取消订单失败', 'Remarks' => '操作失败(订单状态更新失败)');                }            }elseif($OrderInfo && ($OldStatus==2 || $OldStatus==3)){                $UpData['Status'] = 5;                $result = $TourOrderModule->UpdateInfoByOrderNumber($UpData, $OrderInfo['OrderNumber']);                if ($result){                    $Title = '用户出游订单申请退款';                    $Message  = '';                    $Message  = '订单号：' .$OrderInfo['OrderNumber'];                    $Message  .= '产品名称：' .$OrderInfo['OrderNumber'];                    $Message  .='产品类型：' .$OrderInfo['OrderNumber'];                    $Message  .='用户姓名：'.$OrderInfo['Contacts'];                    $Message  .='联系电话：' .$OrderInfo['Tel'];                    $Message  .='用户邮箱：' .$OrderInfo['Email'];                    $Message  .='产品ID：' .$OrderInfo['OrderNumber'];                    $Message  .='退款数量：' .$OrderInfo['OrderNumber'];                    $Message .='退款原因：' .$OrderInfo['OrderNumber'];                    $Message  .='退款申请提交时间：' .$OrderInfo['OrderNumber'];//                    ToolService::SendEMailNotice('linling@57us.com', $Title, $Message);//                    ToolService::SendEMailNotice('gaoshuxin@57us.com', $Title, $Message);//                    ToolService::SendEMailNotice('gaoshuxin@57us.com', $Title, $Message);                    ToolService::SendEMailNotice('wangzefeng@57us.com', $Title, $Message);                    $json_result = array('ResultCode' => 200, 'Message' => '申请退款成功', 'Remarks' => '操作成功');                }else{                    $json_result = array('ResultCode' => 101, 'Message' => '申请退款失败', 'Remarks' => '操作失败(订单状态更新失败)');                }            } else {                $json_result = array('ResultCode' => 100, 'Message' => '订单不存在');            }        }        //添加订单状态更新日志        $OrderLogModule = new TourProductOrderLogModule();        $LogData = array('OrderNumber' => $OrderNo, 'UserID' => $_SESSION['UserID'], 'Remarks' => $json_result['Remarks'], 'OldStatus' => $OldStatus, 'NewStatus' => $NewStatus, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 1);        $OrderLogModule->InsertInfo($LogData);        echo json_encode($json_result);    }    /*     * @desc  签证订单超时,取消签证订单     */    public function CancelVisaOrder()    {        if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {            $json_result = array('ResultCode' => 101, 'Message' => '请先登录', 'Url' => WEB_MEMBER_URL);        } else {            $VisaOrderModule = new VisaOrderModule();            $OrderNo = trim($_POST['OrderNum']);            $NewStatus = intval($_POST['Status']);            $OrderInfo = $VisaOrderModule->GetInfoByWhere(" and OrderNumber='" . $OrderNo . "' and UserID=" . $_SESSION['UserID']);            $OldStatus = $OrderInfo['Status'];            if ($OrderInfo) {                //开启事务                global $DB;                $DB->query("BEGIN");//开始事务定义                $UpData['Status'] = $NewStatus;                $result = $VisaOrderModule->UpdateInfoByOrderNumber($UpData, $OrderInfo['OrderNumber']);                if ($result) {                    $DB->query("COMMIT");//执行事务                    $json_result = array('ResultCode' => 200, 'Message' => '取消订单成功', 'LogMessage' => '操作成功');                } else {                    $DB->query("ROLLBACK");//判断当执行失败时回滚                    $json_result = array('ResultCode' => 102, 'Message' => '取消订单失败', 'Remarks' => '订单状态更新失败', 'LogMessage' => '操作失败（订单状态更新失败）');                }            } else {                $json_result = array('ResultCode' => 100, 'Message' => '订单不存在', 'LogMessage' => '操作失败（订单不存在）');            }        }        //添加订单状态更新日志        $OrderLogModule = new TourProductOrderLogModule();        $LogData = array('OrderNumber' => $OrderNo, 'UserID' => $_SESSION['UserID'], 'Remarks' => $json_result['LogMessage'], 'OldStatus' => $OldStatus, 'NewStatus' => $NewStatus, 'OperateTime' => date("Y-m-d H:i:s", time()), 'IP' => GetIP(), 'Type' => 2);        $LogResult = $OrderLogModule->InsertInfo($LogData);        echo json_encode($json_result);    }}