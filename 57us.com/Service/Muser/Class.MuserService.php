<?php
Class MuserService{
    /**
     * @desc  判断是否登录并返回登录页面
     */
    public static function IsLogin(){
        if (!isset ($_SESSION ['UserID']) || empty ($_SESSION ['UserID'])) {
            header('Location:' . WEB_MUSER_URL . '/member/login/');
            exit;
        }
    }
    
}