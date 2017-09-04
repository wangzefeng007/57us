<?php

/**
 * @desc  阿里邮件推送，数据统计
 * Class AliMailLog
 */
class AliMailLog
{

    /**
     * @desc  处理用户数据（在本地跑）
     */
    public function HandleUserData(){
        $num = '16';
        $EmailModule = new EmailModule();
        $Info = $EmailModule->GetInfoByWhere(' and Email IS NOT NULL and status = 0 limit 10000',true);
        $i= 0;
        $ArrayInfo = array();
        foreach($Info as $key=>$val){
            $Data['status'] = 1;
            $EmailModule->UpdateInfoByKeyID($Data,$val['ID']);
            if(empty($val['Email'])){
                continue;
            }
            if(strstr($val['Email'],"qq")) {
                continue;
            }
            $i++;
            $ArrayInfo[$i] = $val['Email'].','.$val['Name'].',,,,'.$val['Tel'];
            if($i == 2000){
                break;
            }
        }
        foreach ($ArrayInfo as $key=>$val){
            $open=fopen("email_".$num.".txt","a+" );
            fwrite($open,$val."\r\n");
            fclose($open);
        }
        echo count($ArrayInfo);echo "<br>";
        echo 'email_'.$num."结束";exit;
    }

    /**
     * @desc  处理QQ数据
     */
    public function HandleQQUserData(){
        $num = '17';
        $EmailModule = new EmailModule();
        $Info = $EmailModule->GetInfoByWhere(' and QQ IS NOT NULL and Status = 0 limit 2000',true);
        $i= 0;
        $ArrayInfo = array();
        foreach($Info as $key=>$val){
            $Data['Status'] = 1;
            $EmailModule->UpdateInfoByKeyID($Data,$val['ID']);
            $ArrayInfo[$i] = $val['QQ'].'@qq.com,,,,,';
            $i++;
        }
        foreach ($ArrayInfo as $key=>$val){
            $open=fopen("qq_email_".$num.".txt","a+" );
            fwrite($open,$val."\r\n");
            fclose($open);
        }
        echo count($ArrayInfo);echo "<br>";
        echo 'email_'.$num."结束";exit;
    }

    /**
     * @desc 邮件用户点击跟踪
     */
    public function DataTrack(){
        $MailAliLogModule = new MailAliLogModule();
        if(!empty($_GET['Type'])){
            $Data = array(
                'UserName'=>$_GET['UserName'],
                'Tel'=>$_GET['Mobile'],
                'Email'=>$_GET['Email'],
                'Type'=>$_GET['Type'],
                'AddTime'=>date("Y-m-d H:i:s",time()),
                'IP'=>GetIP()
            );
            $MailAliLogModule->InsertInfo($Data);
        }
        /*header('Location:https://xmhwhqgl.alitrip.com/shop/view_shop.htm?spm=a220m.1000862.1000730.2.K45xRn&user_number_id=2868983696&rn=4599e421dd83afb648c60ce25ea79104');*///跳转到带www的网址
        header('Location:http://tour.57us.com/group/67208.html');
    }
}