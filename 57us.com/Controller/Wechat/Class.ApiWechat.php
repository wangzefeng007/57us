<?php
class ApiWechat {

    public function __construct($config){
        $this->appId = $config['appId'];
        $this->appSecret = $config['appSecret'];
        $this->token = $config['token'];
        $this->type = $config['type']; //微信公众号类型  1-留学 2-旅游
    }

    public function Valid(){
        define("TOKEN",$this->token);
        $EchoStr = $_GET['echostr'];
        if($this->CheckSignature()){
            echo $EchoStr;
            exit;
        }
    }

    /**
     * @desc  检测token值。
     * @return bool
     * @throws Exception
     */
    private function CheckSignature(){
        if(!defined("TOKEN")){
            throw new Exception("TOKEN不存在");
        }
        $Signature = $_GET['signature'];
        $Timestamp = $_GET['timestamp'];
        $Nonce = $_GET['nonce'];

        $Token = TOKEN;
        $TmpArr = array($Token,$Timestamp,$Nonce);
        sort($TmpArr,SORT_STRING);
        $TmpStr = implode($TmpArr);
        $TmpStr = sha1($TmpStr);
        if( $TmpStr == $Signature){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * @desc  获取微信用户信息
     * @param $Code
     */
    public function GetWechatUserInfo($Code){
        $Url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appId.'&secret='.$this->appSecret.'&code='.$Code.'&grant_type=authorization_code';
        $GetInfo = ToolRequest($Url);
        $Content = json_decode($GetInfo,true);
        /*array(
            //网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
            'access_token' => 'kfnDoyt264_Rsa9KFRcUxY3GwkiTIgev0WinPgl-DUWRups6WiqRcLSv7eWhNK0dwYifnJB9TmSGWWaPF3fekvZeFUx4qxvWTtR3zgzRcF0',
            //access_token接口调用凭证超时时间，单位（秒）
            'expires_in'=> 7200,
            //用户刷新access_token
            'refresh_token' => 'Tm5VLuITQRtkkLzk7XMwiQiIZnp_chGs9HzDz6BKLJAhsiy4ofJ4n3IY-4tr9Nr_JHl2OLWSkYdhUqk3q9eOTC6IFTYK0pHpKl4UOmuj1WQ',
            //用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
            'openid' => 'onHmus3V2Phb-SGKmnTBJ0PwoKEs',
            //用户授权的作用域，使用逗号（,）分隔
            'scope' => 'snsapi_userinfo',
            //	只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
            'unionid' =>
        );*/
        $InfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$Content['access_token'].'&openid='.$Content['openid'].'&lang=zh_CN';
        $UserInfo = ToolRequest($InfoUrl);
        $UserInfo = json_decode($UserInfo,true);
        /*array(
            /======用户的唯一标识
            'openid' => 'onHmus3V2Phb-SGKmnTBJ0PwoKEs',
            /======用户昵称
            'nickname'=> 'Luwb',
            /======	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
            'sex' => 1,
            /====== 语言
            'language' => 'zh_CN',
            /======普通用户个人资料填写的城市
            'city' => '漳州',
            /======用户个人资料填写的省份
            'province' => '福建',
            /======国家
            'country' => '中国',
            /======用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
            'headimgurl'=> 'http://wx.qlogo.cn/mmopen/yRF09ndxnRozpLWic49KKs0gDcNGLmibrYkia0G2fwkKG0lQjDn4BZrYz6dqOhgsibAuMuGes0LHBhgnrkVkAR98d5OfQ9JDy0gG/0',
            /======用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
            'privilege' => array()
            /======只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
            'unionid'
        );*/
        return $UserInfo;
    }

    /**
     * @desc  判断用户是否关注
     */
    public function JudgeFollow($OpenID){
        //获取公众号的access_token
        $token = $this->GetAccessToken();
        //判断用户是否关注
        $subscribe_msg = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$OpenID;
        $subscribe_info = ToolRequest($subscribe_msg);
        $subscribe_info = json_decode($subscribe_info);
        return $subscribe_info->subscribe;
    }

    /**
     * @desc   获取授权url
     * @desc   $RedirectUrl 跳转的url
     * @return string
     */
    public function GetAuthorizeUrl($RedirectUrl){
        $RedirectUrl = urlencode($RedirectUrl);
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appId.'&redirect_uri='.$RedirectUrl.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
        return $url;
    }

    /**
     * @desc   获取微信jssdk需要的参数
     * @return array
     */
    public function GetSignPackage() {
        $jsapiTicket = $this->GetJsApiTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();

        $nonceStr = $this->CreateNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = 'jsapi_ticket='.$jsapiTicket.'&'.noncestr.'='.$nonceStr.'&'.timestamp.'='.$timestamp.'&'.url.'='.$url;
        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"        => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    /**
     * @desc   获取JsApiTicket
     * @return mixed
     */
    private function GetJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $ConfigParamModule = new WechatConfigParamModule();
        $key = 'jsapi_ticket';
        $JsapiTicketInfo = $ConfigParamModule->GetInfoByWhere(" and ParamName = '{$key}' and Type = {$this->type} ");
        if ($JsapiTicketInfo['ExpireTime'] < time() || !$JsapiTicketInfo) {
            $accessToken = $this->GetAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode(ToolRequest($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $Data = array('ParamValue'=>$ticket,'ExpireTime'=>time() + $res->expires_in);
                if(!$JsapiTicketInfo){
                    $Data['ParamName'] = $key;
                    $Data['Remarks'] = 'jssdk需要的参数(时效两个小时)';
                    $Data['Type'] = $this->type;
                    $ConfigParamModule->InsertInfo($Data);
                }
                elseif($JsapiTicketInfo['ExpireTime'] < time()){
                    $ConfigParamModule->UpdateInfoByWhere($Data," ParamName = '{$key}' and Type = {$this->type} ");
                }
            }
        } else {
            $ticket = $JsapiTicketInfo['ParamValue'];
        }
        return $ticket;
    }

    /**
     * @desc  获取随机数
     * @param int $length
     * @return string
     */
    private function CreateNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    /**
     * @desc  获取access_token
     */
    public function GetAccessToken(){
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $ConfigParamModule = new WechatConfigParamModule();
        $key = 'access_token';
        $AccessTokenInfo = $ConfigParamModule->GetInfoByWhere(" and ParamName = '{$key}' and Type = {$this->type}");
        if ($AccessTokenInfo['ExpireTime'] < time() || !$AccessTokenInfo) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appid&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode(ToolRequest($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $Data = array('ParamValue'=>$access_token,'ExpireTime'=>time() + $res->expires_in);
                if(!$AccessTokenInfo){
                    $Data['ParamName'] = $key;
                    $Data['Remarks'] = 'access_token(时效两个小时)';
                    $Data['Type'] = $this->type;
                    $ConfigParamModule->InsertInfo($Data);
                }
                elseif($AccessTokenInfo['ExpireTime'] < time()){
                    $ConfigParamModule->UpdateInfoByWhere($Data," ParamName = '{$key}' and Type = {$this->type} ");
                }
            }
        } else {
            $access_token = $AccessTokenInfo['ParamValue'];
        }
        return $access_token;
    }

    /**
     * @desc  暂时不用 window下有问题，获取不了
     * @desc  获取链接内容
     * @param $url
     * @return mixed
     */
    private function HttpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    public function GetImageUrl($media_id){
        $access_token = $this->GetAccessToken();
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$media_id;
        return $url;
    }

}
