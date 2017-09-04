<?php
class AjaxTourOrder {
    public function __construct()
    {
    }

    public function Index()
    {
        $Intention = trim($_POST ['Intention']);
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
    
    /**
     * 判断登录
     */
    private function NeedLogin(){
        if(!$_SESSION['UserID']){
            $json_result = array(
                'ResultCode' => 102,
                'Message' => '请先登录',
                'Url'=>WEB_MUSER_URL
            );
            echo json_encode($json_result);
            exit;
        }
    }
    /**
     * 订单列表
     */
    private function MyOrder(){
        $this->NeedLogin();
        $UserID=$_SESSION['UserID'];
        $Status=$_POST['Status']?intval($_POST['Status']):'';
        switch ($Status) {
            case '0': //全部
                $MysqlWhere = ' and UserID= ' . $UserID;
                break;
            case '1': //待支付
                $MysqlWhere = ' and UserID = ' . $UserID . ' and Status = 1';
                break;
            case '2': //已支付
                $MysqlWhere = " and UserID=$UserID and `Status` in (2,3,4)";
                break;
            case '3': //取消
                $MysqlWhere = " and UserID=$UserID and `Status` in (10,11,12,13)";
                break;
            case '4': //未评价
                $MysqlWhere = " and UserId=$UserID and `Status`=4 and EvaluateDefault=0";
                break;
            case '5': //已完成
                $MysqlWhere = " and UserID=$UserID and `Status`=4";
                break;
            case '6': //退款中
                $MysqlWhere = " and UserID=$UserID and `Status` in (5,6,8)";
                break;
            case '7': //已退款
                $MysqlWhere = " and UserID=$UserID and `Status`=9";
                break;
        }
            
        $TourProductOrderModule=new TourProductOrderModule();
        $Rscount = $TourProductOrderModule->GetListsNum($MysqlWhere);
        $Page=intval($_POST['Page'])?intval($_POST['Page']):0;
        if ($Page < 1) {
            $Page = 1;
        }
        if ($Rscount['Num']) {
            $PageSize=6;
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($Page > $Data['PageCount']){
                $OrderList=array();
            }else{
                $Data['Page'] = min($Page, $Data['PageCount']);
                $Offset = ($Page - 1) * $Data['PageSize'];
                $MysqlWhere .=' order by OrderID desc';
                $OrderList = $TourProductOrderModule->GetLists($MysqlWhere,$Offset,$Data['PageSize']);
            }        
            $JsonData=array();
            $TourProductOrderInfoModule = new TourProductOrderInfoModule ();
            $TourProductLineModule = new TourProductLineModule ();
            $CategoryModule = new TourProductCategoryModule();
            $TourProductPlayBaseModule = new TourProductPlayBaseModule();
            //前台状态
            $StatusArr=array(1=>1,2=>2,3=>2,4=>5,5=>6,6=>6,8=>6,9=>7,10=>3,11=>3,12=>3,13=>3);
            foreach ($OrderList as $Key => $Value) {
                $TourProductOrderInfoInfo= $TourProductOrderInfoModule->GetInfoByOrderNumber($Value ['OrderNumber']);
                $TourProductInfo = $TourProductLineModule->GetInfoByTourProductID($TourProductOrderInfoInfo ['TourProductID']);
                $JsonData[$Key]['Category'] = '出游';
                $JsonData[$Key]['OrderNum'] = $Value ['OrderNumber'];
                if (!empty($TourProductInfo)) {
                    $JsonData[$Key]['PayUrl'] = WEB_M_URL.'/group/'.$JsonData[$Key]['OrderNum'].'.html';
                    $JsonData[$Key]['ProductUrl'] = WEB_M_URL.'/group/'.$TourProductInfo ['TourProductID'].'.html';
                } else {
                    $TourProductInfo = $TourProductPlayBaseModule->GetInfoByTourProductID($TourProductOrderInfoInfo ['TourProductID']);
                    $JsonData[$Key]['PayUrl'] = WEB_M_URL.'/playorder/'.$JsonData[$Key]['OrderNum'].'.html';
                    $JsonData[$Key]['ProductUrl'] = WEB_M_URL.'/play/'.$TourProductInfo ['TourProductID'].'.html';
                }
                //$CatecoryInfo = $CategoryModule->GetInfoByKeyID($TourProductInfo['Category']);
                $JsonData[$Key]['Depart'] = date("Y-m-d",strtotime($TourProductOrderInfoInfo['Depart']));
                $JsonData[$Key]['DepartEnd'] = date("Y-m-d",(strtotime($TourProductOrderInfoInfo['Depart'])+86400*$TourProductInfo['Days']));
                $JsonData[$Key]['TourProductID'] = $TourProductInfo ['TourProductID'];
                $JsonData[$Key]['OrderName'] = $TourProductInfo ['ProductName'];
                if($Value['Status']==4){
                    if($Value['EvaluateDefault']==1){
                        $JsonData[$Key]['PayType'] = 5;
                    }else{
                        $JsonData[$Key]['PayType'] = 4;
                    }
                }else{
                    $JsonData[$Key]['PayType'] = $StatusArr[$Value['Status']];
                }
                $JsonData[$Key]['OrderUrl'] = WEB_MUSER_URL.'/musertour/travelorderdetail/?NO='.$JsonData[$Key]['OrderNum'];
                $JsonData[$Key]['EvaluateUrl'] = WEB_MUSER_URL.'/musertour/evaluate/?NO='.$JsonData[$Key]['OrderNum'];
            }
            if(count($JsonData)){
                $ResultCode=200;
                $Message='';
            }else{
                //$ResultCode=101;
                $ResultCode=200;
                $Message='没有更多数据了';
            }
        }else{
            $ResultCode=102;
            $Message='没有数据';
        }
        $json_result=array('ResultCode'=>$ResultCode,'Data'=>$JsonData,'RecordCount'=>$Rscount['Num'],'Message'=>$Message);
        echo json_encode($json_result);
    }

}
