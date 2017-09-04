<?php

class Ask
{
    public function __construct()
    {
        IsLogin();
    }
    public function Index(){
    }

    /**
     * @desc  旅游列表
     */
    public function Lists()
    {
        $AskInfoModule = new AskInfoModule();
        $Type = $_GET['Type'];
        $SqlWhere = ' and `IsStand` = 0 and Status <> 3 and `Column` = '.$Type;
        $PageUrl = '';
        $Keword = $_GET['Keword'];
        if ($Keword) {
            $SqlWhere .= " and AskInfo like '%$Keword%'";
            $PageUrl .= "&Keword=$Keword";
        }
        // 分页开始
        $Page = intval($_GET ['Page']);
        $Page = intval($Page) ? intval($Page) : 1;
        $PageSize = 10;
        $Rscount = $AskInfoModule->GetListsNum($SqlWhere);
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=Ask&Action=Lists&Type='.$Type.'&Page=' . $page . $PageUrl);
        }
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $AskInfoModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                if (mb_strlen($value['AskInfo'],'utf-8')>150){
                    $Data['Data'][$key]['AskInfo'] = mb_substr($value['AskInfo'], 0, 150, 'utf-8').'...';
                }
                $Data['Data'][$key]['TagInfo'] = $this->GetTagInfo($value['Tags']);
                $AskCategoryModule = new AskCategoryModule();
                $Data['Data'][$key]['AskCategoryName'] = $AskCategoryModule->GetInfoByKeyID($value['AskCategoryID'])['AskCategoryName'];
                $Data['Data'][$key]['ColumnName'] = $value['Column'] == 1?'旅游':'留学';
            }
            MultiPage($Data, 10);
        }
        $TopNavs = 'Lists';
        include template("AskInfoList");
    }

    /**
     * @desc  处理标签
     */
    public function GetTagInfo($TagsInfo){
        $TagsInfo = explode(',',$TagsInfo);
        $AskTagModule = new AskTagModule();
        $Result = '';
        foreach($TagsInfo as $key=>$val){
            $TagInfo = $AskTagModule->GetInfoByKeyID($val);
            if($TagInfo['Status'] == 1){
                $Result .= $TagInfo['TagName'].',';
            }
        }
        $Result = substr($Result,0,strlen($Result)-1);
        return $Result;
    }

    /**
     * @desc  删除问题
     */
    public function Delete()
    {
        if ($_GET['ID']) {
            global $DB;
            $DB->query("BEGIN");//开始事务定义
            $AskInfoModule = new AskInfoModule();
            $AskID = intval($_GET['ID']);
            $AskInfo = $AskInfoModule->GetInfoByKeyID($AskID);
            $Delete = $AskInfoModule->UpdateInfoByKeyID(array('Status'=>3),$AskID);
            if ($Delete) {
                if($AskInfo['IsStand'] == 0){
                    $AskCategoryModule = new AskCategoryModule();
                    $Result = $AskCategoryModule->DeleteProblemNum($AskInfo['AskCategoryID']);
                    if($Result){
                        $DB->query("COMMIT");//执行事务
                        alertandback("删除成功");
                    }
                    else{
                        $DB->query("ROLLBACK");//判断当执行失败时回滚
                        alertandback("删除失败");
                    }
                }
                else{
                    $DB->query("COMMIT");//执行事务
                    alertandback("删除成功");
                }
            } else {
                $DB->query("ROLLBACK");//判断当执行失败时回滚
                alertandback("删除失败");
            }
        }
    }

    /**
     * @desc  删除答案
     */
    public function DeleteAnswer()
    {
        $AskAnswerInfoModule = new AskAnswerInfoModule();
        if ($_GET['ID']) {
            $AnswerID = intval($_GET['ID']);
            $Delete = $AskAnswerInfoModule->UpdateInfoByKeyID(array('Status'=>3),$AnswerID);
            if ($Delete) {
                alertandback("删除成功");
            } else {
                alertandback("删除失败");
            }
        }
    }

    /**
     * @desc  问题详情
     */
    public function AskDetail(){
        $AskID = $_GET['ID'];
        $AskInfoModule = new AskInfoModule();
        $AskInfo = $AskInfoModule->GetInfoByKeyID($AskID);
        $Type = $AskInfo['Column'];
        $Stand = $AskInfo['IsStand'];
        $AskAnswerInfoModule = new AskAnswerInfoModule();
        $SqlWhere = ' and AskID ='.$AskID;
        // 分页开始
        $Page = intval($_GET ['Page']);
        $Page = intval($Page) ? intval($Page) : 1;
        $PageSize = 10;
        $Rscount = $AskAnswerInfoModule->GetListsNum($SqlWhere);
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=Ask&Action=AskDetail&ID='.$AskID.'&Page=' . $page);
        }
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $AskAnswerInfoModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            MultiPage($Data, 10);
        }
        include template("AskInfoDetail");
    }

    /**
     * @desc  留学站队列表
     */
    public function Stand()
    {
        $AskInfoModule = new AskInfoModule();
        $Type = $_GET['Type'];
        $SqlWhere = ' and `IsStand` = 1 and Status <> 3';
        $PageUrl = '';
        $Keword = $_GET['Keword'];
        if ($Keword) {
            $SqlWhere .= " and AskInfo like '%$Keword%'";
            $PageUrl .= "&Keword=$Keword";
        }
        // 分页开始
        $Page = intval($_GET ['Page']);
        $Page = intval($Page) ? intval($Page) : 1;
        $PageSize = 10;
        $Rscount = $AskInfoModule->GetListsNum($SqlWhere);
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=Ask&Action=Stand&Page=' . $page . $PageUrl);
        }
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $AskInfoModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            foreach ($Data['Data'] as $key => $value) {
                if (mb_strlen($value['AskInfo'],'utf-8')>150){
                    $Data['Data'][$key]['AskInfo'] = mb_substr($value['AskInfo'], 0, 150, 'utf-8').'...';
                }
            }
            MultiPage($Data, 10);
        }
        $TopNavs = 'Lists';
        include template("AskStand");
    }

    /**
     * @desc  站队详情
     */
    public function AskStandDetail(){
        $AskID = $_GET['ID'];
        $AskInfoModule = new AskInfoModule();
        $AskInfo = $AskInfoModule->GetInfoByKeyID($AskID);
        $AskAnswerInfoModule = new AskAnswerInfoModule();
        $SqlWhere = ' and AskID ='.$AskID;
        // 分页开始
        $Page = intval($_GET ['Page']);
        $Page = intval($Page) ? intval($Page) : 1;
        $PageSize = 10;
        $Rscount = $AskAnswerInfoModule->GetListsNum($SqlWhere);
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=Ask&Action=AskStandDetail&ID='.$AskID.'&Page=' . $page);
        }
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $AskAnswerInfoModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            MultiPage($Data, 10);
        }
        include template("AskStandDetail");
    }

    /**
     * @desc  标签
     */
    public function Tag(){
        $AskTagModule = new AskTagModule();
        $SqlWhere = '';
        $Type = $_GET['Type'];
        $Status = $_GET['Status'];
        if($Status){
            $SqlWhere .= ' and `Status` = '.$Status;
        }
        $SqlWhere .= ' and `Column` = '.$Type;
        $PageUrl = '';
        $Keword = $_GET['Keword'];
        if ($Keword) {
            $SqlWhere .= " and TagName like '%$Keword%'";
            $PageUrl .= "&Keword=$Keword";
        }
        // 分页开始
        $Page = intval($_GET ['Page']);
        $Page = intval($Page) ? intval($Page) : 1;
        $PageSize = 15;
        $Rscount = $AskTagModule->GetListsNum($SqlWhere);
        // 跳转到该页面
        if ($_POST['Page']) {
            $page = $_POST['Page'];
            tourl('/index.php?Module=Ask&Action=Tag&Type='.$Type.'&Page=' . $page . $PageUrl);
        }
        if ($Rscount['Num']) {
            $Data = array();
            $Data['RecordCount'] = $Rscount['Num'];
            $Data['PageSize'] = ($PageSize ? $PageSize : $Data['RecordCount']);
            $Data['PageCount'] = ceil($Data['RecordCount'] / $PageSize);
            $Data['Page'] = min($Page, $Data['PageCount']);
            $Offset = ($Page - 1) * $Data['PageSize'];
            if ($Page > $Data['PageCount'])
                $page = $Data['PageCount'];
            $Data['Data'] = $AskTagModule->GetLists($SqlWhere, $Offset, $Data['PageSize']);
            MultiPage($Data, 10);
        }
        $TopNavs = 'Lists';
        include template("AskTagList");
    }

    /**
     * @标签状态修改
     */
    public function TagUpdata(){
        $TagID = $_GET['ID'];
        $Status = $_GET['S'];
        $AskTagModule = new AskTagModule();
        $Result = $AskTagModule->UpdateInfoByKeyID(array('Status'=>$Status),$TagID);
        if ($Result) {
            alertandback("更新成功");
        } else {
            alertandback("更新失败");
        }

    }

}