<?php
class UserMessage {
    public function __construct() {
    }
    
    //登录验证
    private function IsLogin(){
        if(!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])){
           header("Location:".WEB_MEMBER_URL);
           exit;
        }
    }


}
