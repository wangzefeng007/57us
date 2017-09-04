<?php
class AjaxTour {
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
    
    //=====================================================评价开始========================================//
    /**
     * @desc  提交评价
     */
    private function AddEvaluate(){
        $this->NeedLogin();
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
}