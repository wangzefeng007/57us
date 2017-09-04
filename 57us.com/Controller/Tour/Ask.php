<?php

/**
 * @desc 旅游问答
 * Class Ask
 */
class Ask
{
    public function __construct()
    {
        $this->Identity = array('1'=>'','2'=>'顾问','3'=>'教师','4'=>'管理员');
        /* 底部调用热门旅游目的地、景点 */
        $TourFootTagModule = new TourFootTagModule();
        $this->TourFootTagM = $TourFootTagModule->GetLists(' and Type=1 order by Sort DESC', 0, 200);
        $this->TourFootTagJ = $TourFootTagModule->GetLists(' and Type=2 order by Sort DESC', 0, 200);
    }

    /**
     * @desc 旅游问答首页
     */
    public function Index(){
        $TagNav ='ask';
        //留学专区信息
        $AskCategoryModule = new AskCategoryModule();
        $TourCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 1 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);

        $Type = $_GET['t']?$_GET['t']:'1';
        $Page = intval($_GET['p']);

        $Where = ' and IsStand = 0 and `Column` =1';
        if($Type == '1'){ //最新问题
            $Where .= ' order by AddTime desc';
            $GoPageUrl = '/ask/?t=1';
            $Title = '出境旅游问答_解决美国旅游常见问题_旅游问答社区-57美国旅游';
        }
        elseif($Type == '2'){ //最热问题
            $Where .= ' order by BrowseNum desc ,AddTime desc';
            $GoPageUrl = '/ask/?t=2';
            $Title = '美国旅游最热问题_解决美国旅游常见问题_旅游问答社区-57美国旅游';
        }
        elseif($Type == '3'){ //待回答问题
            $Where .= ' and `AnswerNum` = 0 order by AddTime desc';
            $GoPageUrl = '/ask/?t=3';
            $Title = '美国旅游待回答问题_解决美国旅游常见问题_旅游问答社区-57美国旅游';
        }
        elseif($Type == '4'){ //我的问题
            $Where .= ' and UserID = '.$_SESSION['UserID'].' order by AddTime desc';
            $GoPageUrl = '/ask/?t=4';
            $Title = '出境旅游问答_解决美国旅游常见问题_旅游问答社区-57美国旅游';
        }
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
        //旅游专区信息
        $AskCategoryModule = new AskCategoryModule();
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 1 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //头部广告
        $StudyAskindexADLists=NewsGetAdInfo('tour_ask_index');
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('tour_ask');

        $Keywords = '旅游问答,美国旅游问答,出境旅游问答,旅游常见问题,美国旅游常见问题,出境旅游常见问题';
        $Description = '57美国旅游问答平台，给你一切美国旅行相关问题的答案，由旅游达人、官方旅游局、资深产品经理、在美华人及在美留学生等快速帮助解答你的旅游问题，涵盖了美国签证、气候、必备清单、交通、购物、自驾等各类旅游问题专区。 ';
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
            $Where .= ' and IsStand = '.$IsStand . ' and `Column` = 1';
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
            //echo "<pre>";print_r($Data);exit;
            $Page = new Page($Rscount['Num'], $PageSize,3);
            $Listpage = $Page->showpage();
        }
        else{
            $Data = '';
        }
        //旅游专区信息
        $AskCategoryModule = new AskCategoryModule();
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 1 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('tour_ask');
        //人气话题
        $HotTagList=$this->GetHotTagList();
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
        //判断是否关注
        $AskInfo['IsCollection']=$this->IsCollection($AskInfo['AskID']);
        //获取回答
        $Page = intval($_GET['page'])<1?1:intval($_GET['page']);
        $Where = ' and AskID = ' . $ID .' and Status =1';
        $PageSize = 5;
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
        $HotAsks = $AskInfoModule->GetInfoByWhere(' and `Column` = 1 order by BrowseNum desc ,AddTime desc limit 5',true);
        foreach($HotAsks as $key => $val){
            if (mb_strlen($val['AskInfo'],'utf-8')>30){
                $HotAsks[$key]['AskInfo'] = mb_substr($val['AskInfo'], 0, 30, 'utf-8').'...';
            }
        }
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('tour_ask');
        //旅游专区信息
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 1 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);

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
     * 人气话题
     * by Leo
     */
    private function GetHotTagList(){
        $AskTagModule=new AskTagModule();
        return $AskTagModule->GetInfoByWhere(' and `Status` = 1 and `Column` = 1 ORDER BY RAND() limit 12',true);
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
        //获取所有留学专区
        $AskCategoryList = $AskCategoryModule->GetInfoByWhere('and `Column` = 1 ',true);
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //问题类别
        $Type = $_GET['t'];
        $MysqlWhere = ' and  `Column` = 1 and `IsStand` = 0 and AskCategoryID = '.$ID;

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
        $PageSize = 8;
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
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('tour_ask');
        //旅游专区信息
        $AskCategoryModule = new AskCategoryModule();
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 1 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
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
        //获取所有留学专区
        $AskCategoryList = $AskCategoryModule->GetInfoByWhere('and `Column` = 1 ',true);
        //获取留学相关话题
        $AskTagList = $AskTagModule->GetInfoByWhere(' and `Status` = 1 and `Column` = 1 ORDER BY RAND() limit 5',true);
        //该标签下所有留学问答话题
        $MysqlWhere = ' and `Column` = 1 and `IsStand` = 0 and MATCH (`Tags`) AGAINST ('.$ID.' IN BOOLEAN MODE)';
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
        //旅游专区信息
        $AskCategoryModule = new AskCategoryModule();
        $StudyCateInfos = $AskCategoryModule->GetInfoByWhere(' and `Column` = 1 and `ParentCategoryID` = 0 and `Status`= 1 limit 8',true);
        //人气话题
        $HotTagList=$this->GetHotTagList();
        //右侧广告
        $StudyAskADLists=NewsGetAdInfo('tour_ask');

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
