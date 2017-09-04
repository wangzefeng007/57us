<?php

/**
 * @desc  投票活动
 * Class Vote
 */
class Vote extends WechatCommon {

    public $config;
    public function __construct()
    {
        //正式57us旅游
        /*$this->config = array(
            'appId'=>'wx9f862a9f9b6b28eb',
            'appSecret'=>'91f72a91b306b118f221e9f741f30d27',
            'token'=>'57uslvyouToken',
            'type'=>2  //旅游
        );*/

        //正式57us留学
        $this->config = array(
            'appId'=>'wx7d65f983bc0fe512',
            'appSecret'=>'3e9d1cf13c3c3fef1eea13b16978112d',
            'token'=>'57uslvyouToken',
            'type'=>1  //留学微信公众号
        );
        //测试公众号
        /*$this->appId = 'wx67074e58e8354f7d';
        $this->appSecret = '3e9d1cf13c3c3fef1eea13b16978112d';*/

        /*$GO = $_SERVER['REQUEST_URI'];
            $this->GoToUrl = WEB_WECHAT_URL.$GO;*/
        $this->GoToUrl = WEB_WECHAT_URL . '/vote/index/';
        include SYSTEM_ROOTPATH.'/Controller/Wechat/Class.ApiWechat.php';
    }

    /**
     * @desc  微信验证
     */
    public function Validate(){
        $WechatObj = new ApiWechat($this->config);
        $WechatObj->Valid();
    }

    /**
     * @desc 首页
     */
    public function Index()
    {
        //授权后跳转页
        $RedirectUrl = WEB_WECHAT_URL.'/vote/setuserinfosession/';
        //判断是否授权
        $this->JudgeAuthorize($RedirectUrl);
        $Nav = 'index';
        $Title = '57us';
        //微信接口
        /*$ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();
        //增加访问量
        $this->IncreaseNum('Visit');
        $UserModule = new WechatVoteUserModule();
        $CountModule = new WechatVoteCountModule();
        $StatisticsModule = new WechatVoteStatisticsModule();

        $Statistics = $StatisticsModule->GetInfoByKeyID('1');
        $JoinUserNum = count($UserModule->GetInfoByWhere(' and IsVote = 1 ', true));

        $UserRecommend = $UserModule->GetLists(' and IsVote = 1 and IsIndex = 1  ', 0, 3);
        foreach ($UserRecommend as $key => $val) {
            $CountInfo = $CountModule->GetInfoByWhere(' and UserID = ' . $val['UserID']);
            $UserRecommend[$key]['Count'] = $CountInfo['VoteCount'];
        }*/

        include template('Vote/VoteIndex');
    }

    /**
     * @desc  首页加载数据
     */
    /*public function IndexLoading()
    {
        $UserModule = new WechatVoteUserModule();
        $CountModule = new WechatVoteCountModule();
        $MysqlWhere = 'and IsVote = 1 and IsIndex = 1';
        $page = intval($_POST['page']) ? intval($_POST['page']) : '2';
        $PageSize = 3;
        $Rscount = $UserModule->GetListsNum($MysqlWhere);
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($page, $Data ['PageCount']);
            $Offset = ($page - 1) * $Data ['PageSize'];
            if ($page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data = $UserModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data as $key => $value) {
                $CountInfo = $CountModule->GetInfoByWhere(' and UserID = ' . $value['UserID']);
                $Data[$key]['Count'] = $CountInfo['VoteCount'];
            }
        }
        if ($Data) {
            $result = array('ResultCode' => 200, 'Data' => $Data);
        } else {
            $result = array('ResultCode' => 100, 'Message' => '亲，没有更多内容了！');
        }
        echo json_encode($result);
        exit;
    }*/

    /**
     * @desc  投票排行
     */
    public function Ranking()
    {
        //授权后跳转页
        $RedirectUrl = WEB_WECHAT_URL.'/vote/setuserinfosession/';
        //判断是否授权
        $this->JudgeAuthorize($RedirectUrl);
        
        $Nav = 'ranking';
        $Title = '投票排行_美腿女神总决赛';
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();
        //增加访问量
        $this->IncreaseNum('Visit');
        $VoteCountModule = new WechatVoteCountModule();
        $DataInfo = $VoteCountModule->GetSearchListsAll(5);
        include template('Vote/VoteRanking');
    }

    /**
     * @desc  排行加载数据
     */
    public function RankingLoading()
    {
        $UserModule = new WechatVoteUserModule();
        $CountModule = new WechatVoteCountModule();
        $MysqlWhere = '';
        $page = intval($_POST['page']) ? intval($_POST['page']) : '2';
        $PageSize = 5;
        $Rscount = $CountModule->GetNumListsAll();
        if ($Rscount ['Num']) {
            $Data = array();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            $Data ['Page'] = min($page, $Data ['PageCount']);
            $Offset = ($page - 1) * $Data ['PageSize'];
            if ($page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data = $CountModule->GetSearchList($MysqlWhere, $Offset, $Data ['PageSize']);
        }
        if ($Data) {
            $result = array('ResultCode' => 200, 'Data' => $Data);
        } else {
            $result = array('ResultCode' => 100, 'Message' => '亲，没有更多内容了！');
        }
        echo json_encode($result);
        exit;
    }


    /**
     * @desc  活动规则
     */
    public function Rule()
    {
        $Nav = 'rule';
        $Title = '活动规则_美腿女神总决赛';
        include template('Vote/VoteRule');
    }

    /**
     * @desc  活动奖品
     */
    public function Prize()
    {
        $Nav = 'prize';
        $Title = '活动奖品_美腿女神总决赛';
        include template('Vote/VotePrize');
    }

    /**
     * @desc 个人主页
     */
    public function Member()
    {
        //授权后跳转页
        $RedirectUrl = WEB_WECHAT_URL.'/vote/setuserinfosession/';
        $this->JudgeAuthorize($RedirectUrl);
        $UserID = $_GET['id'];
        $MyUserID = $_SESSION['UserInfo']['UserID'];
        $UserModule = new WechatVoteUserModule();
        $CountModule = new WechatVoteCountModule();

        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();
        if ($UserID == $MyUserID) {
            $Nav = 'member';
            $UserInfo = $UserModule->GetInfoByKeyID($UserID);
            $Title = $UserInfo['RealName'] ? $UserInfo['RealName'] : $UserInfo['Nickname'];
            $CountInfo = $CountModule->GetInfoByWhere(' and UserID = ' . $UserID);
            $Ranking = $this->GetRanking($UserID);
            //是否关注
            $subscribe = $this->JudgeFollow($UserInfo['ForeignKey']);
            if ($UserInfo['IsVote'] == 1) {
                $Images = json_decode($UserInfo['Images']);
                include template('Vote/VoteMemberIsVote');
            } else {
                $Url = WEB_WECHAT_URL . '/vote/joinvote/';
                include template('Vote/VoteMember');
            }
        } else {
            //更新访问量
            $this->IncreaseNum('Visit');
            $JoinVoteUrl = WEB_WECHAT_URL . '/vote/member?id=' . $MyUserID;
            //更新个人围观数
            $CountModule->UpdateOnlookers($UserID);
            $UserInfo = $UserModule->GetInfoByKeyID($UserID);
            $Images = json_decode($UserInfo['Images']);
            $Title = $UserInfo['RealName'] ? $UserInfo['RealName'] : $UserInfo['Nickname'];
            $CountInfo = $CountModule->GetInfoByWhere(' and UserID = ' . $UserID);
            $Ranking = $this->GetRanking($UserID);
            //当前用户信息
            $MyInfo = $UserModule->GetInfoByKeyID($MyUserID);
            $subscribe = $this->JudgeFollow($_SESSION['UserInfo']['ForeignKey']);
            include template('Vote/VoteElseMember');
        }
    }

    /**
     * @desc  投票操作
     */
    public function AddVoting()
    {
        $VoteID = $_POST['myid']; //投票人ID
        $ToVoteID = $_POST['userid'];  //被投票人ID
        if(time() < 1483027200){
            //判断用户今天是否还能投票
            if ($this->JudgeVoteToday($VoteID,1)) {
                $CountModule = new WechatVoteCountModule();
                $VoteLogModule = new WechatVoteLogModule();
                //开启事务
                global $DB;
                $DB->query("BEGIN");//开始事务定义
                $UpdateVote = $CountModule->UpdateVote($ToVoteID);
                if (!$UpdateVote) {
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $json_result = array('ResultCode' => 100, 'Message' => '更新票数失败');
                } else {
                    $Time = strtotime(date("Y-m-d", time()));
                    $Data = array(
                        'VoteUserID' => $VoteID,
                        'ToVoteUserID' => $ToVoteID,
                        'VoteTime' => time(),
                        'ClientIP' => GetIP(),
                        'VoteExpireTime' => $Time
                    );
                    $AddLog = $VoteLogModule->InsertInfo($Data);
                    if (!$AddLog) {
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $json_result = array('ResultCode' => 100, 'Message' => '添加投票日志失败');
                    } else {
                        //添加投票人次
                        $result = $this->IncreaseNum('Vote');
                        if (!$result) {
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                            $json_result = array('ResultCode' => 100, 'Message' => '投票人次更新失败');
                        } else {
                            $DB->query("COMMIT");//执行事务
                            $IsThree = $this->JudgeVoteToday($VoteID,1);  //true 还没到3次  false 3次
                            if(!$IsThree){
                                $IsThree = 1; //已经达到投票次数
                            }
                            else{
                                $IsThree = 2; //还没达到投票次数
                            }
                            $json_result = array('ResultCode' => 200, 'Message' => '投票成功','IsThree'=>$IsThree);
                        }
                    }
                }
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '您今天的投票次数已满3次');
            }
        }
        else{
            $json_result = array('ResultCode' => 100, 'Message' => '活动结束');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  判断今天是否还能投票
     * @param $UserID 用户ID
     * @param $Num    限制投票次数
     * @return bool
     */
    public function JudgeVoteToday($UserID,$Num)
    {
        $UserLogModule = new WechatVoteLogModule();
        $TimeData = strtotime(date("Y-m-d",time()));
        $Logs = $UserLogModule->GetInfoByWhere(' and VoteUserID = ' . $UserID . ' and VoteExpireTime = '.$TimeData.' order by LogID desc ',true);
        $TodayVoteTimes = $Logs?count($Logs):0;
        if($TodayVoteTimes < $Num){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * @desc  增加参与人数,访问量,投票人数
     * @param $key  Partake,Visit,Vote
     */
    private function IncreaseNum($key)
    {
        $StatisticsModule = new WechatVoteStatisticsModule();
        if ($StatisticsModule->Increase($key)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc  获取排名
     */
    private function GetRanking($UserID)
    {
        $CountModule = new WechatVoteCountModule();
        $Data = $CountModule->GetInfoByWhere(' order by VoteCount desc', true);
        $i = 1;
        foreach ($Data as $key => $val) {
            if ($val['UserID'] == $UserID) {
                break;
            }
            $i++;
        }
        return $i;
    }

    /**
     * @desc  参加投票活动
     */
    /*public function JoinVote()
    {
        $Title = '参加投票活动_美腿女神总决赛';
        //获取微信jssdk需要的参数
        $ApiWechatModule = new ApiWechat($this->config);
        $SignPackage = $ApiWechatModule->GetSignPackage();
        //判断安卓还是ios
        $DeviceTypeNum = $this->GetDeviceType();
        include template('Vote/VoteJoinVote');
    }*/

    /**
     * @desc  选择封面
     */
    /*public function ChooseHeadPortrait()
    {
        $Title = '设置封面图片';
        $UserID = $_SESSION['UserInfo']['UserID'];
        $UserModule = new WechatVoteUserModule();
        $UserInfo = $UserModule->GetInfoByKeyID($UserID);
        $Images = json_decode($UserInfo['Images'], true);
        include template('Vote/VoteChooseHeadPortrait');
    }*/

    /**
     * @desc  保存封面图片
     */
    /*public function SaveHeadPortrait()
    {
        $UserID = $_SESSION['UserInfo']['UserID'];
        $Data['HeadPortrait'] = str_replace("http://images.57us.com/l", "", $_POST['image']);
        $UserModule = new WechatVoteUserModule();
        $result = $UserModule->UpdateInfoByKeyID($Data, $UserID);
        if ($result) {
            $Url = WEB_WECHAT_URL . '/vote/member?id=' . $UserID;
            $json_result = array('ResultCode' => 200, 'Message' => '提交成功', 'Url' => $Url);
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '提交失败');
        }
        echo json_encode($json_result);
    }*/

    /**
     * @desc 保存微信投票会员SESSION，马上参加活动
     * @desc 参赛入口，不开放
     */
    public function SetUserInfoSession()
    {
        $ApiWechatModule = new ApiWechat($this->config);
        $UserCountModule = new WechatVoteCountModule();
        $UserInfo = $ApiWechatModule->GetWechatUserInfo($_GET['code']);
        if ($UserInfo) {
            $WechatUserModule = new WechatVoteUserModule();
            $CheckWechat = $WechatUserModule->GetInfoByWhere(" and ForeignKey = '{$UserInfo['openid']}'");
            if ($CheckWechat) {
                $_SESSION['UserInfo'] = $CheckWechat;
                $IsVote = $UserCountModule->GetInfoByWhere(' and UserID = '.$CheckWechat['UserID']);
                if(!$IsVote){
                    //开启事务
                    global $DB;
                    $DB->query("BEGIN");//开始事务定义
                    $CountData = array(
                        'UserID' => $CheckWechat['UserID'],
                        'VoteCount' => 0,
                        'Onlookers' => 1,
                        'Type'=>1,  //留学（游学视频）
                    );
                    $result = $UserCountModule->InsertInfo($CountData);
                    if (!$result) {
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                    } else {
                        //更新参与人数
                        $result1 = $this->IncreaseNum('Partake');
                        if($result1){
                            $DB->query("COMMIT");//执行事务
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                        }
                    }
                }
            } else {
                //开启事务
                global $DB;
                $DB->query("BEGIN");//开始事务定义
                $Data = array(
                    'Nickname' => $this->NameFilter($UserInfo['nickname']),
                    'Sex' => $UserInfo['sex'],
                    'Country' => $UserInfo['country'],
                    'Province' => $UserInfo['province'],
                    'City' => $UserInfo['city'],
                    'HeadImgUrl' => $UserInfo['headimgurl'],
                );
                $Data['ForeignKey'] = $UserInfo['openid'];
                $Data['AddTime'] = date('Y-m-d H:i:s', time());

                $Data['UserID'] = $WechatUserModule->InsertInfo($Data);
                if (!$Data['UserID']) {
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                } else {
                    $CountData = array(
                        'UserID' => $Data['UserID'],
                        'VoteCount' => 0,
                        'Onlookers' => 1,
                        'Type'=>1,  //留学（游学视频）
                    );
                    $result = $UserCountModule->InsertInfo($CountData);
                    if (!$result) {
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                    } else {
                        //更新参与人数
                        $result1 = $this->IncreaseNum('Partake');
                        if($result1){
                            $_SESSION['UserInfo'] = $Data;
                            $DB->query("COMMIT");//执行事务
                        }
                        else{
                            $DB->query("ROLLBACK");//判断当执行失败时回滚
                        }
                    }
                }
            }
            header("Location:" . $this->GoToUrl);
        }
    }

    /**
     * @desc  保存参加信息
     */
    /*public function SaveJoinInfo()
    {
        $Data['RealName'] = $_POST['name'];
        $Data['Content'] = trim($_POST['content']);
        $Images = array();
        $UserID = $_SESSION['UserInfo']['UserID'];
        $PostImage = substr($_POST['images'], 0, -1);
        $PostImage = explode(',', $PostImage);
        foreach ($PostImage as $key => $val) {
            if ($val) {
                $ApiWechatModule = new ApiWechat($this->config);
                $url = $ApiWechatModule->GetImageUrl($val);
                $img = $this->GetPic($url);
                $Images[$key] = $this->OperateImage($img);
            }
        }
        $Data['Images'] = json_encode($Images);
        //参加投票
        $Data['IsVote'] = 1;
        $UserModule = new WechatVoteUserModule();
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        $result = $UserModule->UpdateInfoByKeyID($Data, $UserID);
        if (!$result) {
            $DB->query("ROLLBACK");//判断当执行失败时回滚
            $json_result = array('ResultCode' => 100, 'Message' => '图片信息更新失败');
        } else {
            //更新参与人数
            $result1 = $this->IncreaseNum('Partake');
            if (!$result1) {
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $json_result = array('ResultCode' => 100, 'Message' => '参与人数更新失败');
            } else {
                $_SESSION['UserInfo']['IsVote'] = 1;
                $DB->query("COMMIT");//执行事务
                $Url = WEB_WECHAT_URL . '/vote/member?id=' . $UserID;
                $json_result = array('ResultCode' => 200, 'Message' => '提交成功', 'Url' => $Url);
            }
        }
        echo json_encode($json_result);
    }*/

    /**
     * @desc 保存微信投票会员SESSION
     */
    /*public function SetUserInfoSession()
    {
        $ApiWechatModule = new ApiWechat($this->config);
        $UserInfo = $ApiWechatModule->GetWechatUserInfo($_GET['code']);
        if ($UserInfo) {
            $WechatUserModule = new WechatVoteUserModule();
            $Data = array(
                'Nickname' => $this->NameFilter($UserInfo['nickname']),
                'Sex' => $UserInfo['sex'],
                'Country' => $UserInfo['country'],
                'Province' => $UserInfo['province'],
                'City' => $UserInfo['city'],
                'HeadImgUrl' => $UserInfo['headimgurl'],
            );
            $CheckWechat = $WechatUserModule->GetInfoByWhere(" and ForeignKey = '{$UserInfo['openid']}'");
            if ($CheckWechat) {
                $_SESSION['UserInfo'] = $CheckWechat;
            } else {
                //开启事务
                global $DB;
                $DB->query("BEGIN");//开始事务定义
                $Data['ForeignKey'] = $UserInfo['openid'];
                $Data['AddTime'] = date('Y-m-d H:i:s', time());
                $Data['UserID'] = $WechatUserModule->InsertInfo($Data);
                if (!$Data['UserID']) {
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                } else {
                    $CountData = array(
                        'UserID' => $Data['UserID'],
                        'VoteCount' => 0,
                        'Onlookers' => 1,
                    );
                    $UserCountModule = new WechatVoteCountModule();
                    $result = $UserCountModule->InsertInfo($CountData);
                    if (!$result) {
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                    } else {
                        $DB->query("COMMIT");//执行事务
                        $_SESSION['UserInfo'] = $Data;
                    }
                }
            }
            header("Location:" . $this->GoToUrl);
        }
    }*/

    /**
     * @desc 搜索用户
     */
    public function SearchUser()
    {
        $KeyWord = $_POST['keyword'];
        if ($KeyWord) {
            $UserModule = new WechatVoteUserModule();
            $sql = " and (Nickname = '{$KeyWord}' or RealName = '{$KeyWord}' or UserID = '{$KeyWord}')";
            //$sql = " and  MATCH(`UserID`,`RealName`,`Nickname`) AGAINST ('{$KeyWord}' IN BOOLEAN MODE)";
            $result = $UserModule->GetInfoByWhere($sql);
            if ($result) {
                $UserID = $result['UserID'];
                $Url = WEB_WECHAT_URL . '/vote/member?id=' . $UserID;
                $json_result = array('ResultCode' => 200, 'Url' => $Url);
            } else {
                $json_result = array('ResultCode' => 100, 'Message' => '搜索不存在');
            }
        } else {
            $json_result = array('ResultCode' => 100, 'Message' => '请输入搜索值');
        }
        echo json_encode($json_result);
    }

    /**
     * @desc  判断是否授权,并跳转
     * @param $RedirectUrl 跳转页面
     */
    public function JudgeAuthorize($RedirectUrl)
    {
        if (!$_SESSION['UserInfo']['UserID']) {
            $ApiWechatModule = new ApiWechat($this->config);
            $AuthorizeUrl = $ApiWechatModule->GetAuthorizeUrl($RedirectUrl);
            header("Location:" . $AuthorizeUrl);
        }
    }

    /**
     * @desc  判断用户是否关注公众号
     */
    public function JudgeFollow($ForeignKey)
    {
        $ApiWechatModule = new ApiWechat($this->config);
        return $ApiWechatModule->JudgeFollow($ForeignKey);
    }


}