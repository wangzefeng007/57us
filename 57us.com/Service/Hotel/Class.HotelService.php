<?php
Class HotelService {
    /*
     * @desc 获取酒店的订单号
     *
     */
    public static function GetOrderNumber()
    {
        $OrderNumber = 'H' . date("YmdHis") . rand(100, 999);
        return $OrderNumber;
    }    
}
