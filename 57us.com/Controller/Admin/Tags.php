<?php

/**
 * Class Tags
 * @desc  文章标签模块
 */
class Tags
{

    public function __construct()
    {
        IsLogin();
    }

    /**
     * @desc  标签列表
     */
    public function Lists()
    {
        $TagsModule = new TblTagsModule();
        $TagsList = $TagsModule->GetInfoByWhere('', true);
        include template("TagsLists");
    }

    /**
     * @desc 删除标签
     */
    public function Delete()
    {
        $ID = $_REQUEST['ID'];
        if (!empty($ID)) {
            $TagsModule = new TblTagsModule();
            if ($TagsModule->DeleteByKeyID($ID)) {
                alertandgotopage('已完成删除操作!', $_SERVER['HTTP_REFERER']);
            } else {
                alertandback('删除失败!');
            }
        } else {
            alertandback('您没有选择准备删除的记录!');
        }
    }

    /**
     * @desc 标签编辑
     */
    public function Edit()
    {
        $TagsID = $_GET['ID'];
        if ($TagsID) {
            $TagsModule = new TblTagsModule();
            $TagsInfo = $TagsModule->GetInfoByKeyID($TagsID);
        }
        include template("TagsEdit");
    }

    /**
     * @desc 标签保存操作
     */
    public function Save()
    {
        $TagsID = intval($_POST['TagsID']);
        $Data['TagsName'] = trim($_POST['TagsName']);
        $Data['TagsUrl'] = trim($_POST['TagsUrl']);
        $Data['Sort'] = trim($_POST['Sort']);
        $TagsModule = new TblTagsModule();
        if ($TagsID) {
            $Result = $TagsModule->UpdateInfoByKeyID($Data, $TagsID);
        } else {
            $Result = $TagsModule->InsertInfo($Data);
        }
        if ($Result === 0) {
            alertandback('您没有做任何修改');
        }
        if ($Result) {
            alertandgotopage('保存成功', '/index.php?Module=Tags&Action=Lists');
        } else {
            alertandback('保存失败');
        }
    }

}