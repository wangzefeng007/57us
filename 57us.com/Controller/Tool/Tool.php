<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/21
 * Time: 11:57
 */
Class Tool{

    /**
     * @desc  阿里云短信推送
     */
    public function AliYunSendSms(){

        $AccessKeyId = 'LTAIXwnkmAuMB0Sh';                       //Access Key ID
        $AccessKeySecret = 'uTnRA8WU9IZEhbTwrOu9MMUelQ47vj';   //Access Key Secret

        $TelModule = new TelModule();
        $Data = $TelModule->GetInfoByWhere(' and Status = 0 order by ID asc limit 99',true);
        $MobileData = '';
        foreach($Data as $key=>$val){
            $MobileData.=$val['NO'].',';
            $TelModule->UpdateInfoByKeyID(array('Status'=>1),$val['ID']);
        }

        $SmsService = new AliYunSmsService();
        $Result = $SmsService->SendPushSms($AccessKeyId,$AccessKeySecret,$MobileData);
        if($Result['Code']==200){
            echo $MobileData.$Result['Message'];
            $Data = $TelModule->GetInfoByWhere(' and Status = 0 order by ID asc limit 99',true);
            if($Data){
                echo "<script type='text/javascript'>setTimeout(function(){window.location.reload()},20000)</script>";
            }
            else{
                echo "发送完毕";exit;
            }
        }
        else{
            echo $MobileData.$Result['Message'];exit;
        }


    }

    /**
     * @desc  去掉重复数据
     */
    public function Operate(){
        $TelModule = new TelModule();
        $Data = $TelModule->GetData();
        foreach($Data as $key=>$val){
            if($val['NO']){
                $TelModule->DeleteByWhere('and NO ='.$val['NO'] .' and ID <> '.$val['ID']);
            }
        }
    }
}