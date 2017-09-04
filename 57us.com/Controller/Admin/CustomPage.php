<?php

/**
 * Class CustomPage
 * @desc  自定义页面
 */
class CustomPage
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH.'/Modules/News/Class.TblCustomPageModule.php';
    }

    /**
     * @desc  自定义页面列表
     */
    public function Lists(){
        $CustomPageModule = new TblCustomPageModule();
        $PageList = $CustomPageModule->GetInfoByWhere('',true);
        include template("CustomPageLists");
    }

    /**
     * @desc 删除自定义页面
     */
    public function Delete(){
        $ID = $_REQUEST['ID'];
        if(!empty($ID)){
            $CustomPageModule = new TblCustomPageModule();
            if($CustomPageModule->DeleteByKeyID($ID)){
                alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);
            }else{
                alertandback('删除失败!');
            }
        }
        else{
            alertandback('您没有选择准备删除的记录!');
        }
    }

    /**
     * @desc 自定义页面编辑
     */
    public function Edit(){
        $PageID = $_GET['ID'];
        if($PageID){
            $CustomPageModule = new TblCustomPageModule();
            $CustomPage = $CustomPageModule->GetInfoByKeyID($PageID);
        }
        include template("CustomPageEdit");
    }

    /**
     * @desc 自定义页面保存操作
     */
    public function Save(){
        $PageID = intval($_POST['PageID']);
        $Data['Title'] = trim($_POST['Title']);
        $Data['Alias'] = trim($_POST['Alias']);
        $Data['Keywords'] = trim($_POST['Keywords']);
        $Data['Content'] = $_POST['Content'];
        //文本图片处理-----------------------------------------------------------------------------
        /*$Data['Content'] = $_POST['Content'];
        $Pattern=array();
        $Replacement=array();
        $ImgArr=Array();
        preg_match_all('/<img.*src="(.*)".*>/iU',stripcslashes($Data['Content']),$ImgArr);
        if(count($ImgArr[0])){
            foreach($ImgArr[0] as $Key => $ImgTag){
                $Pattern[]=$ImgTag;
                $Replacement[]=preg_replace("/http:\/\/images\.57us\.com\/l/iU","",preg_replace(array('/title=".*"/iU','/alt=".*"/iU'),'',$ImgTag));
            }
        }
        $Data['Content'] = addslashes(str_replace($Pattern,$Replacement,stripcslashes($Data['Content'])));*/
        //文本图片处理-------------------------------------------------------------------------------
        $CustomPageModule = new TblCustomPageModule();
        if($PageID){
            $Result = $CustomPageModule->UpdateInfoByKeyID($Data,$PageID);
        }
        else{
            $Result = $CustomPageModule->InsertInfo($Data);
        }
        if($Result === 0){
            alertandback('您没有做任何修改');
        }
        if($Result){
            alertandgotopage('保存成功','/index.php?Module=CustomPage&Action=Lists');
        }
        else{
            alertandback('保存失败');
        }
    }

}