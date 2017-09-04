<?php
class AliYunSmsService {

    /**
     * @desc  发送推送短信
     * @param $RecNum  目标手机号，多个手机号可以逗号分隔
     */
    public function SendPushSms($accessKeyId,$accessKeySecret,$RecNum){
        session_start();
        header("Content-type:text/html; charset=UTF-8");

        date_default_timezone_set("GMT");
        $dateTimeFormat = 'Y-m-d\TH:i:s\Z';                      //2015-11-23T04:00:00Z

        //阿里云短信接口地址
        $RequstUrl = "https://sms.aliyuncs.com/?";

        $Data = array(
            // 接口参数
            'Action' => 'SingleSendSms',        //操作接口名，系统规定参数，取值：SingleSendSms
            'SignName'=>'57美国网',              //管理控制台中配置的短信签名（状态必须是验证通过）
            'TemplateCode' => 'SMS_35065216',  //管理控制台中配置的审核通过的短信模板的模板CODE（状态必须是验证通过）
            'RecNum' => $RecNum,                  //目标手机号，多个手机号可以逗号分隔
            'ParamString' => "{}",               //短信模板中的变量,此参数传递{“no”:”123456”}
            // 公共参数
            'Format' => 'XML',                    //返回值的类型，支持JSON与XML。默认为XML
            'Version' => '2016-09-27',           //API版本号，本版本对应为2016-09-27
            'AccessKeyId' => $accessKeyId,        //阿里云颁发给用户的访问服务所用的密钥ID
            'SignatureMethod' => 'HMAC-SHA1',   //签名方式，目前支持HMAC-SHA1
            'Timestamp' => date($dateTimeFormat), //请求的时间戳，日期格式按照ISO8601标准表示，并需要使用UTC时间。格式为2015-11-23T04:00:00Z
            'SignatureVersion' => '1.0',         //签名算法版本，目前版本是1.0
            'SignatureNonce'=> uniqid(),          //唯一随机数，用于防止网络重放攻击。用户在不同请求间要使用不同的随机数值，以微秒计的当前时间
        );
        //签名结果串，关于签名的计算方法，
        $Data['Signature'] = $this->computeSignature($Data, $accessKeySecret);

        // 发送短信请求
        $result = $this->xml_to_array($this->https_request($RequstUrl.http_build_query($Data)));

        if($result['Error']){
            return array('Code'=>100,'Message'=>"发送失败,Code:".$result['Error']['Code'].',Message:'.$result['Error']['Message']);
        }
        if($result['SingleSendSmsResponse']){
            return array('Code'=>200,'Message'=>"发送成功!");
        }
    }

    /**
     * @desc  获取签名机制
     * @param $parameters
     * @param $accessKeySecret
     * @return string
     */
    private function computeSignature($parameters, $accessKeySecret)
    {
        // 将参数Key按字典顺序排序
        ksort($parameters);
        // 生成规范化请求字符串
        $canonicalizedQueryString = '';
        foreach($parameters as $key => $value)
        {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key)
                . '=' . $this->percentEncode($value);
        }
        // 生成用于计算签名的字符串 stringToSign
        $stringToSign = 'GET&%2F&' . $this->percentEncode(substr($canonicalizedQueryString, 1));
        //echo "<br>".$stringToSign."<br>";
        // 计算签名，注意accessKeySecret后面要加上字符'&'
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
        return $signature;
    }

    /**
     * @desc 请求地址
     * @param $url
     * @return mixed|string
     */
    private function https_request($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
        curl_close($curl);
        return $data;
    }

    /**
     * @desc  XML转换成数组
     * @param $xml
     * @return mixed
     */
    private function xml_to_array($xml){
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
                $subxml= $matches[2][$i];
                $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = $this->xml_to_array( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return @$arr;
    }
    /**
     * @desc  使用urlencode编码后，将"+","*","%7E"做替换即满足ECS API规定的编码规范
     * @param $str
     * @return mixed|string
     */
    private function percentEncode($str){
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

}