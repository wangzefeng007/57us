<?php

class AjaxTour
{
    public function __construct(){

    }
    
    public function Index(){
        $Intention = trim($_POST ['Intention']);
        unset($_POST ['Intention']);
        if ($Intention == '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            echo  json_encode($json_result);
            exit;
        }
        $this->$Intention ();
    }

}
