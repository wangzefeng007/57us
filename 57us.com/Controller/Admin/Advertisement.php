<?php

/**
 * Class Advertisement
 * @desc  广告模块
 */
class Advertisement
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH.'/Modules/News/Class.TblAdModule.php';
        include SYSTEM_ROOTPATH.'/Modules/News/Class.TblAdContentModule.php';
    }

    /**
     * @desc  广告列表
     */
    public function Lists(){
        $AdModule = new TblAdModule();
        $Type = $AdModule->Type;
        $MysqlWhere ='';
        if ($_GET['Type'] >0){
            $MysqlWhere .= ' and Type = '.$_GET['Type'];
        }
        $AdList = $AdModule->GetInfoByWhere($MysqlWhere,true);
        include template("AdvertisementLists");
    }

    /**
     * @desc 删除广告
     */
    public function AdDelete(){
        $ID = $_REQUEST['ID'];
        if(!empty($ID)){
        $AdModule = new TblAdModule();
        if($AdModule->DeleteByKeyID($ID)){
            alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);
        }else{
            alertandback('删除失败!');
        }
        }else{
            alertandback('您没有选择准备删除的记录!');
        }
    }

    /**
     * @desc 广告编辑
     */
    public function AdEdit(){
        $AdID = $_GET['ID'];
        if($AdID){
            $AdModule = new TblAdModule();
            $AdInfo = $AdModule->GetInfoByKeyID($AdID);
        }
        include template("AdvertisementAdEdit");
    }

    /**
     * @desc 广告保存操作
     */
    public function AdSave(){
        $AdID = intval($_POST['ADID']);
        $Data['ADTitle'] = trim($_POST['ADTitle']);
        $Data['Key'] = trim($_POST['Key']);
        $Data['ADType'] = trim($_POST['ADType']);
        $Data['Type'] = intval($_POST['Type']);
        $Data['Remarks'] = trim($_POST['Remarks']);
        $AdModule = new TblAdModule();
        //广告示例图上传
        if ($_FILES['Images']['size'][0] > 0) {
            $AdInfo = $AdModule->GetInfoByKeyID($AdID);
            if ($AdInfo['Images'])
                DelFromImgServ($AdInfo['Images']);
            include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
            $Upload = new MultiUpload('Images');
            $File = $Upload->upload();
            $Picture = $File[0] ? $File[0] : '';
            $Data['Images'] = $Picture;
        }
        if($AdID){
            $Result = $AdModule->UpdateInfoByKeyID($Data,$AdID);
        }
        else{
            $Result = $AdModule->InsertInfo($Data);
        }
        if($Result === 0){
            alertandback('您没有做任何修改');
        }
        if($Result){
            alertandgotopage('保存成功','/index.php?Module=Advertisement&Action=AdEdit&ID='.$AdID);
        }
        else{
            alertandback('保存失败');
        }
    }

    /**
     * @desc 广告管理
     */
    public function AdManage(){
        $AdID = $_GET['ID'];
        $AdModule = new TblAdModule();
        $AdInfo = $AdModule->GetInfoByKeyID($AdID);
        $AdContentModule = new TblAdContentModule();
        $AdContent = $AdContentModule->GetInfoByWhere(' and ADID = '.$AdID,true);
        include template("AdvertisementAdManage");
    }

    /**
     * @desc 删除广告详情
     */
    public function AdManageDelete(){
        $ID = $_REQUEST['ID'];
        if(!empty($ID)){
            $AdContentModule = new TblAdContentModule();
            $AdContentInfo =$AdContentModule->GetInfoByKeyID($ID);
            if($AdContentModule->DeleteByKeyID($ID)){
                if($AdContentInfo['Picture']){
                    DelFromImgServ($AdContentInfo['Picture']);
                }
                alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);
            }else{
                alertandback('删除失败!');
            }
        }else{
            alertandback('您没有选择准备删除的记录!');
        }
    }

    /**
     * @desc 广告详细内容编辑
     */
    public function AdManageEdit(){
        $ADID = intval($_GET['ID']);
        $ContentID = intval($_GET['ConID']);
        if($ContentID){
            $AdContentModule = new TblAdContentModule();
            $AdContetInfo = $AdContentModule->GetInfoByKeyID($ContentID);
        }
        include template("AdvertisementAdManageEdit");
    }

    /**
     * @desc  广告详情内容保存
     */
    public function AdManageSave(){
        $AdContentID = intval($_POST['ContentID']);
        $ADID = intval($_POST['ADID']);
        $Data['Title'] = trim($_POST['Title']);
        $Data['Description'] = trim($_POST['Description']);
        $Data['Link'] = $_POST['Link'];
        $Data['DisplayOrder'] = intval($_POST['DisplayOrder']);
        $Data['ADID'] = $ADID;
        //echo "<pre>";print_r($Data);exit;
        include SYSTEM_ROOTPATH.'/Include/MultiUpload.class.php';
        $AdContentModule = new TblAdContentModule();
        if($AdContentID){
            //上传图片
            if ($_FILES['Image']['size'][0] > 0) {
                $Upload = new MultiUpload ( 'Image' );
                $File = $Upload->upload ();
                $Picture = $File[0] ? $File[0] : '';
                $Data['Picture'] = $Picture;
            }
            $Result = $AdContentModule->UpdateInfoByKeyID($Data,$AdContentID);
        }
        else{
            //上传图片
            if ($_FILES['Image']['size'][0] > 0) {
                $Upload = new MultiUpload ( 'Image' );
                $File = $Upload->upload ();
                $Picture = $File[0] ? $File[0] : '';
                $Data['Picture'] = $Picture;
            }
            else{
                alertandback('请上传图片');
            }
            $Result = $AdContentModule->InsertInfo($Data);
        }
        if($Result === 0){
            alertandback('您没有做任何修改');
        }
        if($Result){
            alertandgotopage('保存成功','/index.php?Module=Advertisement&Action=AdManage&ID='.$ADID);
        }
        else{
            alertandback('保存失败');
        }
    }

}