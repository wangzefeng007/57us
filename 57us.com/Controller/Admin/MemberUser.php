<?php

/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2016/10/8
 * Time: 14:07
 */
class MemberUser
{
    public function __construct()
    {
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserModule.php';
        IsLogin();
    }


    /**
     * @desc 会员列表
     */
    public function Lists()
    {
        $MemberUserModule = new MemberUserModule();
        $MemberUserInfoModule = new MemberUserInfoModule();
        $MysqlWhere = '';
        $PageSize = 10;
        $PageUrl = '';
        $Title = $_GET['Title'];
        if ($Title) {
            $MysqlWhere .= " and UserID like '%$Title%'";
            $PageUrl .= "&Title=$Title";
        }
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=MemberUser&Action=Lists&Page=' . $page . $PageUrl);
        }
        $Page = intval($_GET['Page']) ? intval($_GET['Page']) : 1;
        $ListsNum = $MemberUserModule->GetListsNum($MysqlWhere);
        $Rscount = $ListsNum ['Num'];
        if ($Rscount) {
            $Data ['RecordCount'] = $Rscount;
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            $Data['Data'] = $MemberUserModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            foreach ($Data['Data'] as $key=>$value){
                $UserInfo = $MemberUserInfoModule->GetInfoByUserID($value['UserID']);
                $Data['Data'][$key]['InfoID'] = $UserInfo['InfoID'];
                $Data['Data'][$key]['NickName'] = $UserInfo['NickName'];
                $Data['Data'][$key]['RealName'] = $UserInfo['RealName'];
                $Data['Data'][$key]['Identity'] = $UserInfo['Identity'];
                $Data['Data'][$key]['IdentityState'] = $UserInfo['IdentityState'];
            }
            MultiPage($Data, 10);
        }
        include template("MemberUserList");
    }

    /**
     * @desc  会员详情
     */
    public function Detail()
    {
        $MemberUserModule = new MemberUserModule();
        $MemberUserInfoModule = new MemberUserInfoModule();
        $UserID=$_GET['UserID'];
        $User = $MemberUserModule->GetUserByID($UserID);
        $UserInfo =$MemberUserInfoModule->GetInfoByUserID($UserID);
        include template("MemberUserDetail");
    }

    /**
     * @desc  提现列表
     */
    public function WithdrawList(){
        $BankWithdrawModule = new MemberUserBankWithdrawModule();
        $WithdrawType = $BankWithdrawModule->WithdrawType;
        $WithdrawStatus = $BankWithdrawModule->WithdrawStatus;
        $MysqlWhere = '';
        $PageSize = 10;
        $PageUrl = '';
        /*$Title = $_GET['Title'];
        if ($Title) {
            $MysqlWhere .= " and UserID like '%$Title%'";
            $PageUrl .= "&Title=$Title";
        }*/
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=MemberUser&Action=WithdrawList&Page=' . $page . $PageUrl);
        }
        $Page = intval($_GET['Page']) ? intval($_GET['Page']) : 1;
        $ListsNum = $BankWithdrawModule->GetListsNum($MysqlWhere);
        $Rscount = $ListsNum ['Num'];
        if ($Rscount) {
            $Data ['RecordCount'] = $Rscount;
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            $Data['Data'] = $BankWithdrawModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            $MemberUserInfoModule = new MemberUserInfoModule();
            $MemberUserModule = new MemberUserModule();
            foreach ($Data['Data'] as $key=>$value){
                $UserInfo = $MemberUserInfoModule->GetInfoByUserID($value['UserID']);
                $Data['Data'][$key]['InfoID'] = $UserInfo['InfoID'];
                $Data['Data'][$key]['NickName'] = $UserInfo['NickName'];
                $Data['Data'][$key]['RealName'] = $UserInfo['RealName'];
                $Data['Data'][$key]['Identity'] = $UserInfo['Identity'];
                $Data['Data'][$key]['IdentityState'] = $UserInfo['IdentityState'];
                $User = $MemberUserModule->GetInfoByKeyID($value['UserID']);
                $Data['Data'][$key]['Account'] = $User['Mobile']?$User['Mobile']:$User['E-Mail'];
            }
            MultiPage($Data, 10);
        }
        include template("MemberUserWithdrawList");
    }

    /**
     * @desc  提现操作
     */
    public function WithdrawOperate(){
        $WithdrawID = $_GET['ID'];
        $Status = $_GET['S'];
        $UserBankFlowModule = new MemberUserBankFlowModule();
        $UserBankModule = new MemberUserBankModule();
        $BankWithdrawModule = new MemberUserBankWithdrawModule();
        $WithdrawType = $BankWithdrawModule->WithdrawType;
        $BankWithdraw = $BankWithdrawModule->GetInfoByKeyID($WithdrawID);
        $UserBank = $UserBankModule->GetInfoByWhere(' and UserID = '.$BankWithdraw['UserID']);
        //开启事务
        global $DB;
        $DB->query("BEGIN");//开始事务定义
        $Result1 = $BankWithdrawModule->UpdateInfoByKeyID(array('WithdrawStatus'=>$Status),$WithdrawID);
        if($Status == 2){ //确认提现
            if(!$Result1){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                alertandback("确认提现失败");
            }
            else{
                $Data = array('TotalBalance'=>$UserBank['TotalBalance']-$BankWithdraw['Amt'],'FrozenBalance'=>$UserBank['FrozenBalance']-$BankWithdraw['Amt']);
                $Result2 = $UserBankModule->UpdateInfoByWhere($Data,'UserID = '.$BankWithdraw['UserID']);
                if(!$Result2){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    alertandback("确认提现失败");
                }
                else{
                    $FlowData = array(
                        'UserID'=>$BankWithdraw['UserID'],
                        'Amt'=>$BankWithdraw['Amt'],
                        'Amount'=>$UserBank['TotalBalance'],
                        'OperateType'=>3,
                        'Remarks'=>$WithdrawType[$BankWithdraw['WithdrawType']].':'.$BankWithdraw['WithdrawAccounts'],
                        'Type'=>3,
                        'FromIP'=>GetIP(),
                        'AddTime'=>time()
                    );
                    $Result3 = $UserBankFlowModule->InsertInfo($FlowData);
                    if(!$Result3){
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        alertandback("确认提现失败");
                    }
                    else{
                        $DB->query("COMMIT");//执行事务
                        alertandback("确认提现成功");
                    }
                }
            }
        }
        else{ //拒绝提现
            if(!$Result1){
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                alertandback("拒绝提现失败");
            }
            else{
                $Data = array('FreeBalance'=>$UserBank['FreeBalance']+$BankWithdraw['Amt'],'FrozenBalance'=>$UserBank['FrozenBalance']-$BankWithdraw['Amt']);
                $Result2 = $UserBankModule->UpdateInfoByWhere($Data,'UserID = '.$BankWithdraw['UserID']);
                if(!$Result2){
                    $DB->query("ROLLBACK");//判断当执行失败时回滚
                    alertandback("拒绝提现失败");
                }
                else{
                    $DB->query("COMMIT");//执行事务
                    alertandback("拒绝提现成功");
                }
            }
        }
    }

    /**
     * @desc  发送系统消息列表
     */
    public function SendMessageList(){
        $MessageInfoModule = new MemberMessageInfoModule();
        $SendType = $MessageInfoModule->SendType;
        $SendStatus = $MessageInfoModule->SendStatus;

        $MysqlWhere = '';
        $PageSize = 10;
        $PageUrl = '';
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=MemberUser&Action=SendMessageList&Page=' . $page . $PageUrl);
        }
        $Page = intval($_GET['Page']) ? intval($_GET['Page']) : 1;
        $ListsNum = $MessageInfoModule->GetListsNum($MysqlWhere);
        $Rscount = $ListsNum ['Num'];
        if ($Rscount) {
            $Data ['RecordCount'] = $Rscount;
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil($Data ['RecordCount'] / $PageSize);
            if ($Page > $Data ['PageCount'])
                $Page = $Data ['PageCount'];
            $Data ['Page'] = min($Page, $Data ['PageCount']);
            $Offset = ($Page - 1) * $Data ['PageSize'];
            $Data['Data'] = $MessageInfoModule->GetLists($MysqlWhere, $Offset, $Data ['PageSize']);
            MultiPage($Data, 10);
        }
        include template("MemberUserSendMessageList");
    }

    /**
     * @desc 发送信息页面
     */
    public function SendMessage(){
        $ID = $_GET['ID'];
        $MessageInfoModule = new MemberMessageInfoModule();
        $SendType = $MessageInfoModule->SendType;
        if($ID){
            $Data = $MessageInfoModule->GetInfoByKeyID($ID);
        }
        include template("MemberUserSendMessage");
    }

    /**
     * @desc  保存系统消息
     */
    public function SaveMessage(){
        $ID = intval($_POST['ID']);
        $Data['Title'] = trim($_POST['Title']);
        $Data['SendType'] = trim($_POST['SendType']);
        $Data['Content'] = trim($_POST['Content']);
        $Data['SendStatus'] = 1; //未发送
        $MessageInfoModule = new MemberMessageInfoModule();
        if ($ID) {
            $Result = $MessageInfoModule->UpdateInfoByKeyID($Data, $ID);
        } else {
            $Data['AddTime'] = time();
            $Result = $MessageInfoModule->InsertInfo($Data);
        }
        if ($Result > 0) {
            alertandgotopage('保存成功', '/index.php?Module=MemberUser&Action=SendMessageList');
        }
        elseif($Result == 0) {
            alertandback('您没有做任何修改');
        }
        else {
            alertandback('保存失败');
        }
    }

    /**
     * @desc  删除系统消息
     */
    public function DeleteMessage(){
        $ID = intval($_GET['ID']);
        $MessageInfoModule = new MemberMessageInfoModule();
        $Result = $MessageInfoModule->DeleteByKeyID($ID);
        if($Result){
            alertandback('删除成功');
        }
        else{
            alertandback('删除失败');
        }
    }

    /**
     * @desc  信息发布操作
     */
    public function MessageSend(){
        $ID = intval($_GET['ID']);
        $MessageInfoModule = new MemberMessageInfoModule();
        $Data = array('SendTime'=>time(),'SendStatus'=>2);
        $Result = $MessageInfoModule->UpdateInfoByKeyID($Data,$ID);
        if($Result){
            alertandback('发布成功');
        }
        else{
            alertandback('发布失败');
        }
    }

}