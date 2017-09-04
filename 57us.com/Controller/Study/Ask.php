<?php

/**
 * @desc 问答
 * Class Ask
 */
class Ask
{
    public function __construct()
    {
        $this->Identity = array('1'=>'','2'=>'顾问','3'=>'教师','4'=>'管理员');
    }

    /**
     * @desc 留学首页
     */
    public function Index(){
        $TagNav ='ask';
        //留学专区信息
        $AskCategoryModule = new AskCategoryModule();
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 2 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);

        $Type = $_GET['t']?$_GET['t']:'1';
        $Page = intval($_GET['p']);

        $Where = ' and IsStand = 0  and `Column` =2';
        if($Type == '1'){ //最新问题
            $Where .= ' order by AddTime desc';
            $GoPageUrl = '/ask/?t=1';
            $Title = '最全面最权威的美国留学问题解答_留学问答社区-57美国留学';
        }
        elseif($Type == '2'){ //最热问题
            $Where .= ' order by BrowseNum desc ,AddTime desc';
            $GoPageUrl = '/ask/?t=2';
            $Title = '美国留学最热问题_最全面的美国留学问答__留学问答社区-57美国留学';
        }
        elseif($Type == '3'){ //待回答问题
            $Where .= ' and `AnswerNum` = 0 order by AddTime desc';
            $GoPageUrl = '/ask/?t=3';
            $Title = '美国留学待回答问题_最全面的美国留学问答__留学问答社区-57美国留学';
        }
        elseif($Type == '4'){ //我的问题
            $Where .= ' and UserID = '.$_SESSION['UserID'].' order by AddTime desc';
            $GoPageUrl = '/ask/?t=4';
            $Title = '最全面最权威的美国留学问题解答_留学问答社区-57美国留学';
        }
        $Keywords = '留学问答,美国留学问答,美国留学问题,出国留学问答,美国留学签证问题,出国留学问题,美国留学安全问题,留学考试问答';
        $Description = '57美国留学问答平台，提供最全面最权威的美国留学问题解答，由在校留学生、留学导师及顾问快速帮助解答您的留学问题，涵盖了选校与专业定位、申请攻略、备考相关、文书写作、艺术留学、转学、美国高中、留学生活等问答专区，更有留学站队，站队效果显著，对学生的帮助很大。';

        if ($Page < 1) {
            $Page = 1;
        }
        $PageSize = 5;
        $AskInfoModule = new AskInfoModule();
        $Rscount = $AskInfoModule->GetListsNum($Where);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Data'] = $AskInfoModule->GetLists($Where, $Offset, $Data['PageSize']);
            foreach($Data['Data'] as $key => $val){
                if (mb_strlen($val['AskInfo'],'utf-8')>65){
                    $Data['Data'][$key]['AskInfo'] = mb_substr($val['AskInfo'], 0, 65, 'utf-8').'...';
                }
                $Data['Data'][$key]['NowTime'] = ToolService::FormatDate($val['AddTime']);
                //用户信息
                $UserInfo = $this->GetUserInfo($val['UserID']);
                $Data['Data'][$key]['NickName'] = $UserInfo['NickName'];
                $Data['Data'][$key]['Avatar'] = $UserInfo['Avatar'];
                $Data['Data'][$key]['IdentityName'] = $this->Identity[$UserInfo['Identity']];
                $Data['Data'][$key]['Identity'] = $UserInfo['Identity'];
                //标签
                $Data['Data'][$key]['Tags'] = $this->GetTagInfo($val['Tags']);
                //获取回答
                $Data['Data'][$key]['Answer'] = $this->GetListAnswer($val['AskID']);
            }
        }
        $Page = new Page($Rscount['Num'], $PageSize,3);
        $Listpage = $Page->showpage();

        //站队列表（推荐几篇）
        $StandData = $AskInfoModule->GetInfoByWhere(' and IsStand = 1  order by AddTime desc limit 5',true);
        foreach($StandData as $key => $val){
            $StandData[$key]['SquareScale'] = sprintf("%.4f", $val['StandSquareNum']/($val['StandSquareNum']+$val['StandBackNum']))*100;
            $StandData[$key]['BackScale'] = sprintf("%.4f", $val['StandBackNum']/($val['StandSquareNum']+$val['StandBackNum']))*100;
        }
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //头部广告
        $StudyAskindexADLists=NewsGetAdInfo('study_ask_index');
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('study_ask');
        //底部广告
        $StudyAskbottomADLists=NewsGetAdInfo('study_ask_index_bottom');
        include template('AskIndex');
    }

    /**
     * @desc  问答搜索
     */
    public function Search(){
        $TagNav ='ask';
        $Page = intval($_GET['p'])<1?1:intval($_GET['p']);
        $IsStand = intval($_GET['s'])?intval($_GET['s']):0;
        $Keyword = $_GET['K'];
        if($Keyword){
            $Where = ' and `AskInfo` like \'%' . $Keyword . '%\'';
            $Where .= ' and IsStand = '.$IsStand . ' and `Column` = 2';
            $PageSize = 5;
            $AskInfoModule = new AskInfoModule();
            $Rscount = $AskInfoModule->GetListsNum($Where);
            if ($Rscount['Num']) {
                $Data = array();
                $Data['RecordCount'] = $Rscount['Num'];
                $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
                $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
                $Data['Page'] = min($Page, $Data['PageCount']);
                $Offset = ($Page - 1) * $Data['PageSize'];
                if ($Page > $Data['PageCount'])
                    $Page = $Data['PageCount'];
                $Data['Data'] = $AskInfoModule->GetLists($Where, $Offset, $Data['PageSize']);
                if($IsStand == 0){
                    $GoPageUrl = '/ask/search/?K='.$Keyword.'&s=0';
                    foreach($Data['Data'] as $key => $val){
                        if (mb_strlen($val['AskInfo'],'utf-8')>65){
                            $Data['Data'][$key]['AskInfo'] = mb_substr($val['AskInfo'], 0, 65, 'utf-8').'...';
                        }
                        $Data['Data'][$key]['NowTime'] = ToolService::FormatDate($val['AddTime']);
                        //用户信息
                        $UserInfo = $this->GetUserInfo($val['UserID']);
                        $Data['Data'][$key]['NickName'] = $UserInfo['NickName'];
                        $Data['Data'][$key]['Avatar'] = $UserInfo['Avatar'];
                        $Data['Data'][$key]['IdentityName'] = $this->Identity[$UserInfo['Identity']];
                        $Data['Data'][$key]['Identity'] = $UserInfo['Identity'];
                        //标签
                        $Data['Data'][$key]['Tags'] = $this->GetTagInfo($val['Tags']);
                        //获取回答
                        $Data['Data'][$key]['Answer'] = $this->GetListAnswer($val['AskID']);
                    }
                }
                else{
                    $GoPageUrl = '/ask/search/?K='.$Keyword.'&s=1';
                    foreach($Data['Data'] as $key => $val){
                        $Data['Data'][$key]['SquareScale'] = sprintf("%.4f", $val['StandSquareNum']/($val['StandSquareNum']+$val['StandBackNum']))*100;
                        $Data['Data'][$key]['BackScale'] = sprintf("%.4f", $val['StandBackNum']/($val['StandSquareNum']+$val['StandBackNum']))*100;
                    }
                }
            }
            //echo "<pre>";print_r($Data);exit;
            $Page = new Page($Rscount['Num'], $PageSize,3);
            $Listpage = $Page->showpage();
        }
        else{
            $Data = '';
        }
        //获取所有留学专区
        $AskCategoryModule = new AskCategoryModule();
        $AskCategoryList = $AskCategoryModule->GetInfoByWhere('and `Column` = 2 ',true);
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('study_ask');
        include template('AskSearch');
    }

    /**
     * @desc  问题详情页
     */
    public function AskDetail()
    {
        $TagNav ='ask';
        $ID = $_GET['ID'];
        $AskInfoModule = new AskInfoModule();
        $AskInfo = $AskInfoModule->GetInfoByKeyID($ID);
        $AskCategoryModule = new AskCategoryModule();
        $AskCategoryInfo = $AskCategoryModule->GetInfoByKeyID($AskInfo['AskCategoryID']);
        //添加浏览数
        $AskInfoModule->UpdateBrowseNum($ID);
        //用户信息
        $UserInfo = $this->GetUserInfo($AskInfo['UserID']);
        $UserInfo['IdentityName'] = $this->Identity[$UserInfo['Identity']];
        //标签
        $AskInfo['Tags'] = $this->GetTagInfo($AskInfo['Tags']);
        $Tags = '';
        foreach($AskInfo['Tags'] as $key => $val){
            $Tags .=$val['TagName'].',';
        }
        //判断是否收藏
        $AskInfo['IsCollection']=$this->IsCollection($AskInfo['AskID']);
        //获取回答
        $Page = intval($_GET['page'])<1?1:intval($_GET['page']);
        $Where = ' and AskID = ' . $ID .' and Status =1';
        $PageSize = 10;
        $AskAnswerInfoModule = new AskAnswerInfoModule();
        $Rscount = $AskAnswerInfoModule->GetListsNum($Where);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Data'] = $AskAnswerInfoModule->GetLists($Where, $Offset, $Data['PageSize']);
            if (mb_strlen($Data['Data'][0]['AnswerInfo'],'utf-8')>100){
                $OneAnswer = mb_substr($Data['Data'][0]['AnswerInfo'], 0, 100, 'utf-8').'...';
            }
            else{
                $OneAnswer = $Data['Data'][0]['AnswerInfo'].',';
            }
            foreach ($Data['Data'] as $key => $val) {
                $Data['Data'][$key]['NowTime'] = ToolService::FormatDate($val['AddTime']);
                //用户信息
                $UserInfoElse = $this->GetUserInfo($val['UserID']);
                $Data['Data'][$key]['NickName'] = $UserInfoElse['NickName'];
                $Data['Data'][$key]['Avatar'] = $UserInfoElse['Avatar'];
                $Data['Data'][$key]['IdentityName'] = $this->Identity[$UserInfoElse['Identity']];
                $Data['Data'][$key]['Identity'] = $UserInfoElse['Identity'];
            }
        }
        $GoPageUrl = '/ask_section/'.$ID.'_';
        $Page = new Page($Rscount['Num'], $PageSize,1);
        $Listpage = $Page->showpage();
        //热门问题
        $HotAsks = $AskInfoModule->GetInfoByWhere(' order by BrowseNum desc ,AddTime desc limit 5',true);
        foreach($HotAsks as $key => $val){
            if (mb_strlen($val['AskInfo'],'utf-8')>30){
                $HotAsks[$key]['AskInfo'] = mb_substr($val['AskInfo'], 0, 30, 'utf-8').'...';
            }
        }
        //留学专区
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 2 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('study_ask');

        if (mb_strlen($AskInfo['AskInfo'],'utf-8')>25){
            $AskTitle = mb_substr($AskInfo['AskInfo'], 0, 25, 'utf-8');
        }
        else{
            $AskTitle = $AskInfo['AskInfo'];
        }
        $Title = $AskTitle.'_留学问答社区-57美国留学';
        $Keywords =  $AskTitle.','.$Tags;
        $Description = $OneAnswer.'最全面的美国留问题解答，尽在57美国留学平台！';

        include template('AskDetail');
    }


    /**

     * @desc  获取用户
     */
    public function GetUserInfo($UserID){
        $UserInfoModule = new MemberUserInfoModule();
        $UserInfo = $UserInfoModule->GetInfoByUserID($UserID);
        return $UserInfo;
    }

    /**
     * @desc  处理标签
     */
    public function GetTagInfo($TagsInfo){
        $TagsInfo = explode(',',$TagsInfo);
        $AskTagModule = new AskTagModule();
        $Result = array();
        foreach($TagsInfo as $key=>$val){
            $TagInfo = $AskTagModule->GetInfoByKeyID($val);
            if($TagInfo['Status'] == 1){
                $Result[$key] = array('TagID'=>$TagInfo['TagID'],'TagName'=>$TagInfo['TagName']);
            }
        }
        return $Result;
    }

    /**
     * @desc  获取问题列表的首个答案
     * @param $AskID
     * @return array
     */
    public function GetListAnswer($AskID){
        $AskAnswerInfoModule = new AskAnswerInfoModule();
        $Answer = $AskAnswerInfoModule->GetInfoByWhere(' and AskID = '.$AskID.' order by AddTime desc');
        if($Answer){
            $UserInfo = $this->GetUserInfo($Answer['UserID']);
            if (mb_strlen($Answer['AnswerInfo'],'utf-8')>30){
                $Answer['AnswerInfo'] = mb_substr($Answer['AnswerInfo'], 0, 30, 'utf-8').'…';
            }
            $Result = array('Answer'=>$Answer['AnswerInfo'],'NickName'=>$UserInfo['NickName'],'Avatar'=>$UserInfo['Avatar'],'Identity'=>$this->Identity[$UserInfo['Identity']]);
            return $Result;
        }
        else{
            return '';
        }
    }

    /**    
     * @desc 站队列表
     * by Leo
     */
    public function Team(){
        $AskInfoModule=new AskInfoModule();
        $Type=$_GET['t']?intval($_GET['t']):1;
        $MysqlWhere=" and `Column`=2 and IsStand=1";
        switch($Type){
            case 1:
                $MysqlWhere.=" and `Status`=1 order by AddTime desc";
                break;
            case 2:
                $MysqlWhere.=" and `Status`=1 order by Cent desc,AddTime desc";
                break;
            case 3:
                $MysqlWhere.=" and `Status`=1 and AnswerNum=0 order by AddTime desc";
                break;
            case 4:
                MemberService::IsLogin();
                $MysqlWhere.=" and UserID={$_SESSION['UserID']} order by AddTime desc";
                break;
        }
        //----------------------------------------分页开始------------------------------------------------
        $Rscount = $AskInfoModule->GetListsNum($MysqlWhere);
        $page = intval($_GET['page']);
        if ($page < 1) {
            $page = 1;
        }
        $PageSize = 6;
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            if ($page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Page'] = min($page, $Data['PageCount']);
            $Offset = ($page - 1) * $Data['PageSize'];
            $Data['Data'] = $AskInfoModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach($Data['Data'] as $key=>$val){
                if($val['StandSquareNum']==0 && $val['StandBackNum']==0){
                    $Data['Data'][$key]['SquareScale']=50;
                    $Data['Data'][$key]['BackScale']=50;
                }else{
                    $Data['Data'][$key]['SquareScale'] = sprintf("%.4f", $val['StandSquareNum']/($val['StandSquareNum']+$val['StandBackNum']))*100;
                    $Data['Data'][$key]['BackScale'] = sprintf("%.4f", $val['StandBackNum']/($val['StandSquareNum']+$val['StandBackNum']))*100;
                }
            }
        }
        $Page = new Page($Rscount['Num'], $PageSize,2);
        $listpage = $Page->showpage();
        //----------------------------------------分页结束----------------------------------------------------
        //留学专区
        $AskCategoryModule = new AskCategoryModule();
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 2 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
        //热门站队
        $HotTeamList=$this->GetHotTeamList();
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('study_ask');

        $Title = '大家来站队_留学问答社区-57美国留学';
        $Keywords =  '留学见解,留学经验分享,留学站队意见,留学建议,留学看法';
        $Description = '57美国留学【大家来站队】专区，由留学专家、在校留学生、留学顾问、留学导师及普通用户，根据各自的经验及见识，分享美国留学的见解及看法，并以投票正反方的形式直接表达出观念。';
        include template('AskTeam');
    }
    
    /**
     * @desc 站队详情
     * by Leo
     */
    public function TeamDetail(){
        $ID=intval($_GET['ID']);
        $AskInfoModule=new AskInfoModule();
        $AskInfo=$AskInfoModule->GetInfoByWhere(" and `Status`=1 and AskID=$ID");
        if($AskInfo){
            //添加浏览数
            $AskInfoModule->UpdateBrowseNum($ID);
            //转换标签
            $TagArr=$this->GetTagInfo($AskInfo['Tags']);
            //判断是否收藏
            $AskInfo['IsCollection']=$this->IsCollection($AskInfo['AskID']);
            //获取用户信息
            $AskInfo['UserInfo']=$this->GetUserInfo($AskInfo['UserID']);
            if($AskInfo['StandBackNum']!=0 || $AskInfo['StandSquareNum']!=0){
                $AskInfo['SquareScale']= sprintf("%.4f", $AskInfo['StandSquareNum']/($AskInfo['StandSquareNum']+$AskInfo['StandBackNum']))*100;
                $AskInfo['BackScale']=100-$AskInfo['SquareScale'];
            }else{
                $AskInfo['SquareScale']=50;
                $AskInfo['BackScale']=50;
            }
            $AskAnswerInfoModule=new AskAnswerInfoModule();
            $MysqlWhere=" and `Status`=1 and AskID={$AskInfo['AskID']} order by AddTime desc";
            //--------------------回答列表分页开始-----------------
             $Rscount = $AskAnswerInfoModule->GetListsNum($MysqlWhere);
             $page = intval($_GET['page']);
             if ($page < 1) {
                 $page = 1;
             }
             $PageSize = 6;
             if ($Rscount['Num']) {
                 $Data = array();
                 $Data['RecordCount'] = $Rscount['Num'];
                 $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
                 $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
                 if ($page > $Data['PageCount'])
                     $page = $Data['PageCount'];
                 $Data['Page'] = min($page, $Data['PageCount']);
                 $Offset = ($page - 1) * $Data['PageSize'];
                 $Data['Data'] = $AskAnswerInfoModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
                 if (mb_strlen($Data['Data'][0]['AnswerInfo'],'utf-8')>100){
                     $OneAnswer = mb_substr($Data['Data'][0]['AnswerInfo'], 0, 100, 'utf-8').'...';
                 }
                 else{
                     $OneAnswer = $Data['Data'][0]['AnswerInfo'].',';
                 }
                 foreach($Data['Data'] as $key=>$val){
                     $Data['Data'][$key]['UserInfo']=$this->GetUserInfo($val['UserID']);
                     $Data['Data'][$key]['AddTime']=ToolService::FormatDate($val['AddTime']);
                 }
             }
             $Page = new Page($Rscount['Num'], $PageSize,2);
             $listpage = $Page->showpage();
             //--------------------回答列表分页结束----------------
            //留学专区
            $AskCategoryModule = new AskCategoryModule();
            $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 2 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
             //热门站队
            $HotTeamList=$this->GetHotTeamList();
            //人气话题
            $HotTagList=$this->GetHotTagList();
            //右侧广告
            $StudyAskADLists=NewsGetAdInfo('study_ask');

            if (mb_strlen($AskInfo['AskInfo'],'utf-8')>25){
                $AskTitle = mb_substr($AskInfo['AskInfo'], 0, 25, 'utf-8');
            }
            else{
                $AskTitle = $AskInfo['AskInfo'];
            }
            $Title = $AskTitle.'_留学问答社区-57美国留学';
            $Keywords =  $AskTitle;
            $Description = $AskTitle.','.$OneAnswer.'真实的留学问答社区,帮助你寻找答案,分享见解与知识！';
            include template('AskTeamDetail');
        }else{
            alertandgotopage("异常的页面请求",WEB_STUDY_URL.'/ask/team/');
        }
    }
    
    /**
     * 热门站队
     * by Leo
     */
    private function GetHotTeamList(){
        $AskInfoModule=new AskInfoModule();
        return $AskInfoModule->GetInfoByWhere(" and `Column`=2 and IsStand=1 and `Status`=1 order by Cent desc,AddTime desc limit 0,5",true);
    }
    
    /**
     * 人气话题
     * by Leo
     */
    private function GetHotTagList(){
        $AskTagModule=new AskTagModule();
        return $AskTagModule->GetInfoByWhere(' and `Status` = 1 and `Column` = 2 ORDER BY RAND() limit 12',true);
    }
    
    /**
     * @desc 留学问答专区页
     */
    public function Section(){
        $AskCategoryModule = new AskCategoryModule();
        $AskInfoModule = new AskInfoModule();
        $AskTagModule = new AskTagModule();
        $ID = $_GET['id'];
        //获取该专区信息
        $AskCategoryInfo = $AskCategoryModule->GetInfoByKeyID($ID);
        if (!$AskCategoryInfo){
            alertandgotopage ( '不存在该专区!', WEB_STUDY_URL.'/ask/' );
        }
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //问题类别
        $Type = $_GET['t'];
        $MysqlWhere = ' and  `Column` = 2 and `IsStand` = 0 and AskCategoryID = '.$ID;
        if ($Type=='hot'){
            $MysqlWhere .= ' order by BrowseNum desc ,AddTime desc';    //最热回答
            $Title = $AskCategoryInfo['Title2'];
        }elseif($Type=='no'){
            $MysqlWhere .= ' and AnswerNum = 0';//待回答问题
            $Title = $AskCategoryInfo['Title3'];
        }elseif ($Type=='my'){
            MemberService::IsLogin();
            $UserID = $_SESSION['UserID'];
            $MysqlWhere .= ' and UserID = '.$UserID;//我的问题
            $Title = $AskCategoryInfo['Title1'];
        }elseif ($Type==''){
            $MysqlWhere .= ' order by AddTime desc';//最新问题
            $Title = $AskCategoryInfo['Title1'];
        }
        $Keywords = $AskCategoryInfo['Keywords'];
        $Description = $AskCategoryInfo['Descriptions'];
        $Page =$_GET['p']?$_GET['p']:1;
        $PageSize = 2;
        $Rscount = $AskInfoModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Data'] = $AskInfoModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                $Data['Data'][$key]['NowTime'] = ToolService::FormatDate($value['AddTime']);
                $UserInfo = $this->GetUserInfo($value['UserID']);
                $Data['Data'][$key]['NickName'] = $UserInfo['NickName'];
                $Data['Data'][$key]['Avatar'] = $UserInfo['Avatar'];
                $Data['Data'][$key]['IdentityName'] = $this->Identity[$UserInfo['Identity']];
                $Data['Data'][$key]['Identity'] = $UserInfo['Identity'];
                $TagsInfo = $this->GetTagInfo($value['Tags']);
                $Data['Data'][$key]['Tags'] = $TagsInfo;
                //判断此为题的回答人数大于0获取该问题最新回答
                if ($value['AnswerNum']>0){
                    $AskAnswerInfo = $this->GetListAnswer($value['AskID']);
                    $Data['Data'][$key]['Answer'] = $AskAnswerInfo['Answer'];
                    $Data['Data'][$key]['AnswerNickName'] = $AskAnswerInfo['NickName'];
                    $Data['Data'][$key]['AnswerAvatar'] = $AskAnswerInfo['Avatar'];
                }
            }
            $Page = new Page($Rscount['Num'], $PageSize,3);
            $Listpage = $Page->showpage();
        }
        //跳转页面
        if ($_POST['page']){
            $page = intval($_POST['page']);
            if ($Type!=''){
                $Url ='/ask/section/?id='.$ID.'&t='.$Type.'&p='.$page;
            }else{
                $Url ='/ask/section/?id='.$ID.'&p='.$page;
            }
            tourl($Url);
        }
        //留学专区信息
        $AskCategoryModule = new AskCategoryModule();
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 2 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('study_ask');
        include template('AskSection');
    }
    /**
     * @desc 留学话题页
     */
    public function Topic(){
        $AskCategoryModule = new AskCategoryModule();
        $AskInfoModule = new AskInfoModule();
        $AskTagModule = new AskTagModule();
        $ID = $_GET['id'];
        //获取该标签话题信息
        $AskTagInfo = $AskTagModule->GetInfoByKeyID($ID);
        if (!$AskTagInfo){
            alertandgotopage ( '不存在该话题!', WEB_STUDY_URL.'/ask/' );
        }
        if ($AskTagInfo['Status']==0){
            alertandback( '该话题待审核!');
        }
        //获取留学相关话题
        $AskTagList = $AskTagModule->GetInfoByWhere(' and `Status` = 1 and `Column` = 2 ORDER BY RAND() limit 5',true);
        //该标签下所有留学问答话题
        $MysqlWhere = ' and `Column` = 2 and `IsStand` = 0 and MATCH (`Tags`) AGAINST ('.$ID.' IN BOOLEAN MODE)';
        //问题类别
        $Type = $_GET['t'];
        if ($Type=='hot'){
            $MysqlWhere .= ' order by BrowseNum desc ,AddTime desc';//最热回答
        }elseif($Type=='no'){
            $MysqlWhere .= ' and AnswerNum = 0 order by AddTime desc';//待回答问题
        }elseif ($Type=='my'){
            MemberService::IsLogin();
            $UserID = $_SESSION['UserID'];
            $MysqlWhere .= ' and UserID = '.$UserID.' order by AddTime desc';//我的问题
        }elseif ($Type==''){
            $MysqlWhere .= ' order by AddTime desc';//最新问题
        }
        if ($Type=='my'){

        }
        $Page =$_GET['p']?$_GET['p']:1;
        $PageSize = 2;
        $Rscount = $AskInfoModule->GetListsNum($MysqlWhere);
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $Page = $Data['PageCount'];
            $Data['Data'] = $AskInfoModule->GetLists($MysqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                $Data['Data'][$key]['NowTime'] = ToolService::FormatDate($value['AddTime']);
                $UserInfo = $this->GetUserInfo($value['UserID']);
                $Data['Data'][$key]['NickName'] = $UserInfo['NickName'];
                $Data['Data'][$key]['Avatar'] = $UserInfo['Avatar'];
                $Data['Data'][$key]['IdentityName'] = $this->Identity[$UserInfo['Identity']];
                $Data['Data'][$key]['Identity'] = $UserInfo['Identity'];
                $TagsInfo = $this->GetTagInfo($value['Tags']);
                $Data['Data'][$key]['Tags'] = $TagsInfo;
                //判断此为题的回答人数大于0获取该问题最新回答
                if ($value['AnswerNum']>0){
                    $AskAnswerInfo = $this->GetListAnswer($value['AskID']);
                    $Data['Data'][$key]['Answer'] = $AskAnswerInfo['Answer'];
                    $Data['Data'][$key]['AnswerNickName'] = $AskAnswerInfo['NickName'];
                    $Data['Data'][$key]['AnswerAvatar'] = $AskAnswerInfo['Avatar'];
                }
            }
            $Page = new Page($Rscount['Num'], $PageSize,3);
            $Listpage = $Page->showpage();
        }
        //跳转页面
        if ($_POST['page']){
            $page = intval($_POST['page']);
            if ($Type!=''){
                $Url ='/ask/topic/?id='.$ID.'&t='.$Type.'&p='.$page;
            }else{
                $Url ='/ask/topic/?id='.$ID.'&p='.$page;
            }
            tourl($Url);
        }
        //留学专区信息
        $AskCategoryModule = new AskCategoryModule();
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 2 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('study_ask');

        $Title = $AskTagInfo['TagName'].'_留学问答社区-57美国留学';
        $Keywords =  $AskTagInfo['TagName'];
        $Description = '57美国留学问答'.$AskTagInfo['TagName'].'专区，聚集了所有'.$AskTagInfo['TagName'].'相关的美国留学问题及解答，帮助你快速找到所需的答案.';
        include template('AskTopic');
    }
    
    
    /**
     * 查询是否已关注
     * @param $AskID 问题ID
     * @return boolean 返回true或false
     * by Leo
     */
    public function IsCollection($AskID){
        if(!$_SESSION['UserID']){
            return false;
        }
        $AskCollectionModule=new AskCollectionModule();
        $AskCollectionInfo=$AskCollectionModule->GetInfoByWhere(" and UserID={$_SESSION['UserID']} and AskID=$AskID");
        if($AskCollectionInfo){
            return true;
        }else{
            return false;
        }
    }
}
