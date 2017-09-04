<?php
class AskAjax
{
    public function __construct()
    {
    }
    public function Index()
    {
        $Intention = trim($_POST ['Intention']);
        unset($_POST ['Intention']);
        if ($Intention == '') {
            $json_result = array(
                'ResultCode' => 500,
                'Message' => '系統錯誤',
                'Url' => ''
            );
            echo $json_result;
            exit;
        }
        $this->$Intention ();
    }
    /**
     * @desc 人气话题
     */
    public function TopicHot(){
        $AskTagModule = new AskTagModule();
        if ($_POST){
            $Mysqlwhere  = ' and `Column` = 1 and `Status` = 1 ORDER BY RAND() limit 12';
            $AskTag = $AskTagModule->GetInfoByWhere($Mysqlwhere,true);
            foreach ($AskTag as $key=>$value){
                $Data['Data'][$key]['Id'] = $value['TagID'];
                $Data['Data'][$key]['Name'] = $value['TagName'];
                $Data['Data'][$key]['Url'] = '/ask/topic/?id='.$value['TagID'];
            }
            $Data['ResultCode'] = 200;
            EchoResult($Data);
        }else{
            $Data = array(
                'ResultCode' => 102,
                'Message' => '返回失败',
            );
            EchoResult($Data);
        }
    }

    /**
     * @desc 添加问题
     * by Leo
     */
    private function AddQuestion(){
        include SYSTEM_ROOTPATH . '/Include/badword.php';//包含敏感词
        //判断是否登录
        $this->NeedLogin();
        $UserID=$_SESSION['UserID'];
        $Data['Column']=1;
        //问答类别
        $Data['AskCategoryID']=intval($_POST['AskCategoryID']);
        $AskInfoModule=new AskInfoModule();
        $Data['IsStand']=0;
        $Data['AskInfo']=$_POST['AskInfo'];
        //过滤敏感字
        $Data['AskInfo']  =str_replace($badword,'***', $Data['AskInfo'] );
        //添加进标签库
        $Tags=$_POST['Tag'];
        if(is_array($Tags)){
            $TagIDs='';
            $AskTagModule=new AskTagModule();
            foreach($Tags as $val){
                //查询是否存在标签
                $TagInfo=$AskTagModule->GetInfoByWhere(" and TagName='$val'");
                if($TagInfo){
                    $TagIDs.=$TagInfo['TagID'].',';
                }else{
                    $TagData['Column']=1;
                    $TagData['Status']=0;
                    $TagData['AddTime']=time();
                    $TagData['UserID']=$UserID;
                    $TagData['TagName']=$val;
                    $TagData['ProblemNum'] = 1;
                    $TagID=$AskTagModule->InsertInfo($TagData);
                    if($TagID){
                        $TagIDs.=$TagID.',';
                    }
                }
            }

            $Data['Tags']=rtrim($TagIDs,',');
        }
        $Data['Status']=1;
        $Data['AddTime']=time();
        $Data['UserID']=$UserID;
        $AskID=$AskInfoModule->InsertInfo($Data);
        if($AskID){
            $AskCategoryModule = new AskCategoryModule();
            $UpdateProblemNum = $AskCategoryModule->UpdateProblemNum($Data['AskCategoryID']);
            if (!$UpdateProblemNum){
                $json_result = array(
                    'ResultCode' => 102,
                    'Message' => '提交失败',
                );
            }else{
                $json_result = array(
                    'ResultCode' => 200,
                    'Message' => '提交成功',
                    'ID'=>$AskID,
                    'Url'=>'/ask_section/'.$AskID.'.html',
                );
            }
        }else{
            $json_result = array(
                'ResultCode' => 101,
                'Message' => '提交失败',
            );
        }
        echo json_encode($json_result);
    }

    /**
     * @desc 回答提问
     */
    private function AddAnswer(){
        include SYSTEM_ROOTPATH . '/Include/badword.php';//包含敏感词
        //判断是否登录
        $this->NeedLogin();
        $UserID=$_SESSION['UserID'];
        $AskID=intval($_POST['ID']);
        $AskAnswerInfoModule=new AskAnswerInfoModule();
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        //普通问答
        if($AskID){
            $AskInfoModule=new AskInfoModule();
            $AskInfo=$AskInfoModule->GetInfoByWhere(" and AskID=$AskID and `Status`=1");
            if($AskInfo){
                $Data['AskID']=$AskID;
                $Data['AnswerInfo']=$_POST['AnswerInfo'];
                $Data['Status']=1;
                $Data['UserID']=$UserID;
                $Data['AddTime']=time();
                //过滤敏感字
                $Data['AnswerInfo']  =str_replace($badword,'***', $Data['AnswerInfo'] );
                $AnswerID=$AskAnswerInfoModule->InsertInfo($Data);
                if($AnswerID){
                    $UpdateAnswerNum = $AskInfoModule->UpdateAnswerNum($AskID);
                    if (!$UpdateAnswerNum){
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        $json_result = array(
                            'ResultCode' => 101,
                            'Message' => '提交失败,请重新尝试',
                        );
                    }else{
                        //更新专区（分类的参与人数PartakeNum）
                        $AskCategoryModule = new AskCategoryModule();
                        $Result2 = $AskCategoryModule->UpdatePartakeNum($AskInfo['AskCategoryID']);
                        if($Result2){
                            $DB->query("COMMIT");//执行事务
                            $json_result = array(
                                'ResultCode' => 200,
                                'Message' => '提交成功',
                                'ID'=>$AnswerID
                            );
                        }
                        else{
                            $json_result = array(
                                'ResultCode' => 105,
                                'Message' => '提交失败',
                                'Error'=>'更新问题参与人数失败'
                            );
                        }
                    }
                }else{
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $json_result = array(
                        'ResultCode' => 102,
                        'Message' => '提交失败,请重新尝试',
                    );
                }
            }else{
                $json_result=array(
                    'ResultCode'=>103,
                    'Message'=>'提交失败,不存在该问答'
                );
            }
        }else{
            $json_result=array(
                'ResultCode'=>104,
                'Message'=>'提交失败,不存在该问答'
            );
        }
        echo json_encode($json_result);
    }

    /**
     * 需要登录
     * by Leo
     */
    private function NeedLogin(){
        if(!$_SESSION['UserID']){
            $json_result = array(
                'ResultCode' => 102,
                'Message' => '请先登录',
                'Url'=>WEB_MEMBER_URL
            );
            echo json_encode($json_result);
            exit;
        }
    }

    /**
     * @desc 关注问题
     */
    public function AttentionAsk(){
        //判断是否登录
        $this->NeedLogin();
        $UserID=$_SESSION['UserID'];
        $AskID = $_POST['AskID'];
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        $AskCollectionModule = new AskCollectionModule();
        $CollectionInfo =  $AskCollectionModule->GetInfoByWhere(" and UserID={$_SESSION['UserID']} and AskID=$AskID");
        $AskInfoModule = new AskInfoModule();
        if ($CollectionInfo){ //取消关注
            $Result = $AskCollectionModule->DeleteByWhere(" and UserID={$_SESSION['UserID']} and AskID=$AskID");
            if($Result){
                $UpdatedownFollowNum = $AskInfoModule->UpdatedownFollowNum($AskID);
                if (!$UpdatedownFollowNum){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $json_result = array('ResultCode' => 102, 'Message' => '关注失败', 'Remarks' => '操作失败(问答表关注数更新失败)');
                }else{
                    $AskInfo = $AskInfoModule->GetInfoByKeyID($AskID);
                    $DB->query("COMMIT");//执行事务
                    $json_result = array('ResultCode' => 201, 'Message' => '取消关注成功','Num'=>$AskInfo['FollowNum']);
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $json_result = array('ResultCode' => 101, 'Message' => '关注失败', 'Remarks' => '操作失败(关注/收藏表更新失败)');
            }
        }else{ //关注
            $CollectionData = array('AskID'=>$AskID,'UserID'=>$UserID,'OperateTime'=>time(),'OperateIP'=>GetIP());
            $Result = $AskCollectionModule->InsertInfo($CollectionData);
            if($Result){
                $UpdateFollowNum = $AskInfoModule->UpdateFollowNum($AskID);
                if (!$UpdateFollowNum){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $json_result = array('ResultCode' => 103, 'Message' => '关注失败', 'Remarks' => '操作失败(问答表关注数更新失败)');
                }else{
                    $AskInfo = $AskInfoModule->GetInfoByKeyID($AskID);
                    $DB->query("COMMIT");//执行事务
                    $json_result = array('ResultCode' => 200, 'Message' => '关注成功','Num'=>$AskInfo['FollowNum']);
                }
            }
            else{
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $json_result = array('ResultCode' => 101, 'Message' => '关注失败', 'Remarks' => '操作失败(关注/收藏表更新失败)');
            }
        }
        echo json_encode($json_result);
        exit;
    }

    /**
     * @desc 点赞回答
     */
    public function AskThumbup(){
        $UserID=$_SESSION['UserID'];
        $AnswerID = $_POST['AnswerID'];
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        $AskThumbupModule = new AskThumbupModule();
        $OperateIP = GetIP();
        $ThumbupInfo = $AskThumbupModule->GetInfoByWhere(' and AnswerID = '.$AnswerID.' and OperateIP = \''.$OperateIP.'\'');
        if (!$ThumbupInfo){
            $ThumbupData = array('AnswerID'=>$AnswerID,'UserID'=>$UserID,'OperateTime'=>time(),'OperateIP'=>$OperateIP);
            $Result = $AskThumbupModule->InsertInfo($ThumbupData);
            if(!$Result){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                $json_result = array('ResultCode' => 101, 'Message' => '点赞失败', 'Remarks' => '操作失败(关注/收藏表更新失败)');
            }
            else{
                $AskAnswerInfoModule = new AskAnswerInfoModule();
                $UpdateFollowNum = $AskAnswerInfoModule->UpdatePraiseNum($AnswerID);
                if (!$UpdateFollowNum){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    $json_result = array('ResultCode' => 102, 'Message' => '点赞失败', 'Remarks' => '操作失败(问答表关注数更新失败)');
                }else{
                    $DB->query("COMMIT");//执行事务
                    $AnswerInfo = $AskAnswerInfoModule->GetInfoByKeyID($AnswerID);
                    $json_result = array('ResultCode' => 200, 'Message' => '点赞成功','Num'=>$AnswerInfo['PraiseNum']);
                }
            }
        }else{
            $DB->query("COMMIT");//执行事务
            $json_result = array('ResultCode' => 103, 'Message' => '该回答您已经点过赞了','Remarks' => '操作失败(该IP地址点赞过此回答)');
        }
        echo json_encode($json_result);
        exit;
    }
    /**
     * @desc 获取标签
     */
    public function OsTags(){

        if ($_POST){
            $AskInfo = $_POST['AskInfo'];
            $Tags = $this->GetTag($AskInfo);
            if (empty($Tags)){
                $json_result = array('ResultCode' => 200, 'Message' => '返回成功','Tags'=>'');
            }else{
                $json_result = array('ResultCode' => 200, 'Message' => '返回成功','Tags'=>$Tags);
            }
        }else{
            $json_result = array('ResultCode' => 101, 'Message' => '返回失败');
        }
        echo json_encode($json_result);
        exit;
    }
    private function GetTag($Info){
        $Tags = array();
        $AskTagModule = new AskTagModule();
        $AskTagList = $AskTagModule->GetInfoByWhere(" and `Column`=1 and `Status`=1 ",true);

        foreach ($AskTagList as $key=>$value){
            if (strstr($Info,$value['TagName'])){
                $Tags[] = $value['TagName'];
            }
        }
        $Tags = array_slice($Tags,0,7);
        return $Tags;
    }
}
