<?php
Class VisaService{
    /*
     * @desc 获取签证的订单号
     *
     */
    function GetVisaOrderNumber()
    {
        $OrderNumber = 'V' . date("YmdHis") . rand(100, 999);
        return $OrderNumber;
    }
}