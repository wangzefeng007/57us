<?php
/**
 * @desc  租租车接口
 * Class ZuZuCheAPI
 */
class ZuZuCheAPI{
    private $User="F11163220-acg9^&";
    private $PassWord="quBEP!#)";
    private $ApiUrl="http://api.zuzuche.com/2.0/standard/";
//    private $User="F11163220-TEST";
//    private $PassWord="TESTsBNR23";
//    private $ApiUrl="http://test.api.zuzuche.com/2.0/standard/";

    //获取国家资源
    public function GetCountry(){
        return $this->CurlByGet('region.php');
    }

    //获取城市资源
    public function GetCity($regionId){
        return $this->CurlByGet('city.php',array('regionId'=>$regionId));
    }

    //获取地标资源
    public function GetLandMark($cityId){
        return $this->CurlByGet('landmark.php', array('cityId'=>$cityId));
    }

    //查询报价
    public function QueryQuote($Data){
        return $this->CurlByGet('queryQuote.php', $Data);
    }

    //报价详细
    public function QuoteInfo($QuoteId){
        return $this->CurlByGet('quoteDetail.php', array('id'=>$QuoteId));
    }

    //租车协议
    public function RentalTerms($QuoteId){
        return $this->CurlByGet('rentalTerms.php', array('id'=>$QuoteId));
    }

    //订单附录信息查询
    public function QuoteAppendix($QuoteId){
        return $this->CurlByGet('quoteAppendix.php', array('id'=>$QuoteId));
    }

    //预定
    public function SaveOrder($Data){
        return $this->CurlByPost('saveOrder.php',$Data);
    }

    //查询订单信息
    public function OrderInfo($Data){
        return $this->CurlByGet('queryOrder.php',$Data);
    }

    //取消正式订单
    public function CancelOrder($Data){
        return $this->CurlByGet('cancelOrder.php',$Data);
    }
    //确认订单
    public function confirmOrder($Data){
        return $this->CurlByGet('confirmOrder.php', $Data);
    }
    //取消临时订单
    public function cancelTmpOrder($Data){
        return $this->CurlByGet('cancelTmpOrder.php', $Data);
    }
    //下载提车单
    public function voucherPdf($Data){
        return $this->CurlByGet('voucherPdf.php', $Data);
    }

    public function CurlByGet($url,$data=array()){
        $ch = curl_init ();
        //设置选项，包括URL
        curl_setopt ( $ch, CURLOPT_URL, $this->ApiUrl.$url.'?'.http_build_query ($data));
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch,CURLOPT_USERPWD,$this->User.":".$this->PassWord);
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 ); //定义超时3秒钟
        //执行并获取url地址的内容
        $output = curl_exec ( $ch );
        $errorCode = curl_errno ( $ch );
        //释放curl句柄
        curl_close ( $ch );
        if (0 !== $errorCode) {
            return false;
        }
        return json_decode($output,true);
    }

    public function CurlByPost($url,$data=array()){
        $ch = curl_init ();
        //设置选项，包括URL
        curl_setopt ( $ch, CURLOPT_URL, $this->ApiUrl.$url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch,CURLOPT_USERPWD,$this->User.":".$this->PassWord);
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 ); //定义超时3秒钟
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
        return json_decode($output,true);
    }
}
