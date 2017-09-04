<?php
Class TourService {
    /*
     * @desc 获取出游的订单号
     */
    public static function GetTourOrderNumber()
    {
        $OrderNumber = 'T' . date("YmdHis") . rand(100, 999);
        return $OrderNumber;
    }
    /*
     * @desc 获取出游SKU号
     *
     */
    function GetTourSKUNumber($Info=array())
    {
        if (intval($Info['TourProductID'])=='')
        {
            return '';
        }
        if ($Info['ShorEnName']=='')
        {
            $Info['ShorEnName'] = 'US';
        }
        if ($Info['Category']=='')
        {
            $Info['Category'] = '00';
        }
        $SKUNumber = $Info['ShorEnName'] . $Info['Category']  . $Info['TourProductID'] . rand(100, 999);
        return $SKUNumber;
    }
    /**
     * @desc  创建查询url
     * @param string $SoUrl
     * @param $Array
     * @return string
     */
    function CreateSearchUrl($SoUrl = '',$XuanXiangArray) {
        if ($SoUrl == '')
            return '';
        $NewSoUrl = substr ( $SoUrl, 1 );
        $UrlArray = explode ( '_', $NewSoUrl );
        //获取分页START
        foreach ( $UrlArray as $V ) {
            if (strstr ( $V, 'pa' )) {
                $ReturnUrl ['p'] ['Page'] = intval ( str_replace ( 'pa', '', $V ) );
                $ReturnUrl ['p'] ['Url'] = str_replace ( '_' . $V, '', $SoUrl );
            } else {
                $ReturnUrl ['p'] ['Page'] = 1;
                $ReturnUrl ['p'] ['Url'] = $SoUrl;
            }
        }
        //获取分页END
        //采选条件START
        foreach ( $XuanXiangArray as $Value ) {
            foreach ( $UrlArray as $Val ) {
                if (! strstr ( $Val, $Value )) {
                    $ReturnUrl ['a'] [$Value] .= '_' . $Val;
                }
                if (strstr ( $Val, $Value )) {
                    $ReturnUrl ['b'] [$Value] = str_replace ( '_' . $Val, '', $SoUrl );
                    $ReturnUrl ['z'] [$Value] = str_replace ( $Value, '', $Val );
                    $ReturnUrl ['c'] [$Value] = $ReturnUrl ['z'] [$Value] ;
                }
            }
            $ReturnUrl ['a'] [$Value] = str_replace ( '_pa' . $ReturnUrl ['p'] ['Page'], '', $ReturnUrl ['a'] [$Value] );
        }
        //采选条件END
        return $ReturnUrl;
    }
}