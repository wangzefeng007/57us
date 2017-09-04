<?php

Class StudyService{


    /**
     * @desc 获取留学顾问服务的订单号
     * @return string
     * 静态方法
     */
    public static function GetConsultantOrderNumber(){
        $OrderNumber = 'C' . date("YmdHis") . rand(100, 999);
        return $OrderNumber;
    }

    /**
     * @desc  获取留学老师服务的订单号
     * @return string
     * 静态方法
     */
    public static function GetTeacherOrderNumber(){
        $OrderNumber = 'E' . date("YmdHis") . rand(100, 999);
        return $OrderNumber;
    }

    /**
     * @desc   获取游学订单号
     * @return string
     * 静态方法
     */
    public static function GetStrdyTourOrderNumber(){
        $OrderNumber = 'Y' . date("YmdHis") . rand(100, 999);
        return $OrderNumber;
    }
}

