<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/1
 * Time: 20:01
 * @desc  微信公共类
 */

class WechatCommon{

    /**
     * @desc  操作图片
     */
    public function OperateImage($WechatImage)
    {
        $ImageBase64 = base64_encode($WechatImage);
        //上传图片服务器
        $ImgUrl = '/up/' . date('Y') . '/' . date('md') . '/' . date('YmdHis') . mt_rand(100, 999) . '.jpg';
        if (SendToImgServ($ImgUrl, $ImageBase64) == 'true') {
            return $ImgUrl;
        } else {
            return '';
        }
    }

    /**
     * @desc  获取图片
     * @param $url
     * @return mixed|null
     */
    public function GetPic($url)
    {
        $curl = curl_init($url); //初始化
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);  //将结果输出到一个字符串中，而不是直接输出到浏览器
        curl_setopt($curl, CURLOPT_REFERER, 'http://wx.qlogo.cn/'); //最重要的一步，手动指定Referer
        $re = curl_exec($curl); //执行
        if (curl_errno($curl)) {
            curl_close($curl);
            return NULL;
        }
        curl_close($curl);
        return $re;
    }

    /**
     * @desc  判断安卓还是ios
     * @desc  如果是安卓返回9.如果是ios返回1
     * @return string
     */
    public function GetDeviceType()
    {
        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $num = '';
        //分别进行判断
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            $num = 1;
        }
        if (strpos($agent, 'android')) {
            $num = 9;
        }
        return $num;
    }

    /**
     * @desc  授权创建微信用户
     * @desc  $Code 微信回调的code
     */
    public function FoundUser($Code){
        $UserInfo = $this->ApiWechatModule->GetWechatUserInfo($Code);
        if ($UserInfo) {
            $WechatUserModule = new WechatVoteUserModule();
            $Data = array(
                'Nickname' => $UserInfo['nickname'],
                'Sex' => $UserInfo['sex'],
                'Country' => $UserInfo['country'],
                'Province' => $UserInfo['province'],
                'City' => $UserInfo['city'],
                'HeadImgUrl' => $UserInfo['headimgurl'],
            );
            $CheckWechat = $WechatUserModule->GetInfoByWhere(" and ForeignKey = '{$UserInfo['openid']}'");
            if ($CheckWechat) {
                $_SESSION['UserInfo'] = $CheckWechat;
            }
            else {
                $Data['ForeignKey'] = $UserInfo['openid'];
                $Data['AddTime'] = time();
                $Data['UserID'] = $WechatUserModule->InsertInfo($Data);
                $_SESSION['UserInfo'] = $Data;
            }
            header("Location:" . $this->GoToUrl);
        }
    }

    /**
     * @desc  微信名特殊符号过滤
     * @param $Name
     * @return mixed|string
     */
    public function NameFilter($Name) {
        if($Name){
            $Name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $Name);
            $Name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S','?', $Name);
            $return = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#ie","",json_encode($Name)));
            if(!$return){
                return $this->jsonName($return);
            }
        }else{
            $return = '';
        }
        return $return;
    }
}