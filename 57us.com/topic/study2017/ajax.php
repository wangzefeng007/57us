<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
if($_COOKIE['session_id']!=''){
    session_id($_COOKIE['session_id']);
}
session_start();
define('SYSTEM_ROOTPATH', dirname(__FILE__).'/../../');
class Ajax {
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
    
    //保存测试
    private function SaveTest(){
        $Fraction=trim($_POST['Fraction']);
        $TotalScore=trim($_POST['TotalScore']);
        $Tel=trim($_POST['Tel']);
        @$handle=fopen('./data/cusdata.txt','a');   
        @fwrite($handle,"{$Tel}|{$Fraction}|{$TotalScore}"."\r\n");
        @fclose($handle);
        include "../../Service/Common/Class.ToolService.php";
        $ToolService=new ToolService();
        //发送给客户
        $ToolService->SendSMSNotice($Tel, "您好！我们已经收到了您的留学测评申请，留学顾问正在加班加点为您制作测评报告，我们将在1-2个工作日内与您联系，谢谢！");
        //发送给Jason
        $ToolService->SendSMSNotice('18659297866', "来自“留学测评”的{$Tel}同学提交了申请评估信息，请马上跟进处理。");
	$ToolService->SendSMSNotice('15160090744', "来自“留学测评”的{$Tel}同学提交了申请评估信息，请马上跟进处理。");
        $json_result=array('ResultCode'=>200,'Message'=>'提交成功，我们将在1-2个工作日内与您联系，谢谢');
        echo json_encode($json_result);
    }
    
    //保存留言
    private function SaveFeedback(){
        //获取登录信息
        if($_SESSION['UserID'] && $_SESSION['Account']){
            if(!isset($_SESSION['Level'])){
                $json_data=$this->curl_postsend_usersession('http://member.57us.com/userajax.html',array('Intention'=>'GetSession','ID'=>$_SESSION['UserID'],'Account'=>$_SESSION['Account']));
                $_SESSION=json_decode($json_data,true);
            }
        }
        if ($_POST){
            include "../../Include/Class.Common.php";
            $InsertInfo['Name'] = trim($_POST['Name']);
            $InsertInfo['Email'] = trim($_POST['Email']);
            $InsertInfo['Tel'] = trim($_POST['Tel']);
            $InsertInfo['UserID'] = $_SESSION['UserID'];
            $InsertInfo['Type'] = 3;
            $InsertInfo['FromIP'] = GetIP();
            $InsertInfo['AddTime'] = date("Y-m-d H:i:s");
            if ($InsertInfo['Name'] =='' || strlen($InsertInfo['Name'])>30 || $InsertInfo['Email']=='' || $InsertInfo['Tel']=='')
            {
                $JsonResult = array(
                    'ResultCode' => 101,
                    'Message' => '信息填写错误！'
                );
            }else{
                include '../../Include/Class.Databasedriver.Mysql.php';
                include '../../Config.php';
                //初始化数据库连接类
                $DB = new DatabaseDriver_MySql ($NewsDbConfig);
                $IsOk = $DB->insertArray('tbl_eb5_message', $InsertInfo,true);
                if ($IsOk)
                {
                    include "../../Service/Common/Class.ToolService.php";
                    $ToolService=new ToolService();
                    $ToolService->SendSMSNotice('18659297866', "“新春专题”{$InsertInfo['Name']}{$InsertInfo['Tel']}，邮箱{$InsertInfo['Email']}。");
	            $ToolService->SendSMSNotice('15160090744', "“新春专题”{$InsertInfo['Name']}{$InsertInfo['Tel']}，邮箱{$InsertInfo['Email']}。");
                    $JsonResult = array(
                        'ResultCode' => 200,
                        'Message' => '提交成功'
                    );
                }else{
                    $JsonResult = array(
                        'ResultCode' => 102,
                        'Message' => '提交失败，请重新提交!'
                    );
                }
            }
            echo json_encode ( $JsonResult );
            exit ();
        }
    }
    
    //post函数
    private function curl_postsend_usersession($url, $data = array()) {
        $ch = curl_init ();
        //设置选项，包括URL
        curl_setopt ( $ch, CURLOPT_URL, "$url" );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 5 ); //定义超时3秒钟
        // POST数据
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        // POST参数
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $data ) );
        //执行并获取url地址的内容
        $output = curl_exec ( $ch );
        $errorCode = curl_errno ( $ch );
        //释放curl句柄
        curl_close ( $ch );
        if (0 !== $errorCode) {
            return false;
        }
        return $output;
    }
}

$Ajax= new Ajax();
$Ajax->Index();