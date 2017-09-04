<?php

class StudyAbroadNewsCategory
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadCategoryModule.php';
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadModule.php';
        $this->TopModule = "StudyAbroadNews";
    }

    /**
     * @desc  添加分类
     */
    public function Index()
    {
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $CategoryList = $TblStudyAbroadCategoryModule->GetInfoByWhere(" order by GlobalDisplayOrder asc", true);
        if ($CategoryList) {
            foreach ($CategoryList as $key => $val) {
                $GlobalDisplayOrder[$key] = $val['GlobalDisplayOrder'];
            }
            array_multisort($GlobalDisplayOrder, SORT_NATURAL, $CategoryList);
        }
        $CategoryID = intval($_GET['CategoryID']);
        if ($CategoryID) {
            $CategoryInfo = $TblStudyAbroadCategoryModule->GetInfoByKeyID($CategoryID);
            if (!$CategoryInfo) {
                alertandback('很抱歉, 您指定的分类不存在! 无法编辑!');
            }
        }
        $TopNavs = 'CategoryAdd';
        include template("StudyAbroadNewsCategoryIndex");
    }

    /**
     * @desc  修改或新增
     */
    public function Add()
    {
        if ($_POST) {
            $Data['CategoryName'] = trim($_POST['CategoryName']);
            if (!$Data['CategoryName'] || strlen($Data['CategoryName']) > 60) {
                alertandback('很抱歉, 分类名称是必填项,且长度不得超过20个汉字(60个半角字符)!');
            }
            $CategoryID = intval($_POST['CategoryID']);
            $Data['ParentCategoryID'] = intval($_POST['ParentCategoryID']);
            $Data['Alias'] = trim($_POST['Alias']);
            $Data['SeoKeywords'] = trim($_POST['SeoKeywords']);
            $Data['SeoTitle'] = trim($_POST['SeoTitle']);
            $Data['SeoDescription'] = trim($_POST['SeoDescription']);
            if (!$Data['Alias'] || strlen($Data['Alias']) > 30) {
                alertandback('很抱歉, 分类别名是必填项,且长度不得超过30个字符!');
            }
            if (!preg_match("/^([A-Za-z0-9\-]+)$/", $Data['Alias'])) {
                alertandback('很抱歉, 分类别名只接受英文字母子与数字字符及连结符（减号）!');
            }
            $Data['DisplayOrder'] = intval($_POST['DisplayOrder']);
            $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
            if ($CategoryID) {
                $CategoryExists = $TblStudyAbroadCategoryModule->GetInfoByWhere(" and `Alias`='{$Data['Alias']}' and CategoryID<>$CategoryID");
            } else {
                $CategoryExists = $TblStudyAbroadCategoryModule->GetInfoByWhere(" and `Alias`='{$Data['Alias']}'");
            }
            if ($CategoryExists) {
                alertandback('很抱歉, 分类别名“' . $Data['Alias'] . '”是已经使用，请使用其它的别名!');
            }
            if (!$CategoryID) {
                $LastCategoryID = $TblStudyAbroadCategoryModule->Create($Data);
                if ($LastCategoryID) {
                    $TblStudyAbroadCategoryModule->UpdateDisplayOrder(0);
                    alertandgotopage('分类资料保存成功!', $_SERVER['HTTP_REFERER']);
                } else {
                    alertandback('分类资料保存失败! 请重试!');
                }
            } else {
                if ($TblStudyAbroadCategoryModule->Update($CategoryID, $Data) !== false) {
                    $TblStudyAbroadCategoryModule->UpdateDisplayOrder(0);
                    alertandgotopage('分类资料保存成功!', $_SERVER['HTTP_REFERER']);
                } else {
                    alertandback('分类资料保存失败! 请重试!');
                }
            }
        } else {
            alertandback('很抱歉,您没有提交任何信息!');
        }
    }

    /**
     * @desc  分类列表
     */
    public function Lists()
    {
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $CategoryList = $TblStudyAbroadCategoryModule->GetInfoByWhere(" order by GlobalDisplayOrder asc", true);
        if ($CategoryList) {
            foreach ($CategoryList as $key => $val) {
                $GlobalDisplayOrder[$key] = $val['GlobalDisplayOrder'];
            }
            array_multisort($GlobalDisplayOrder, SORT_NATURAL, $CategoryList);
        }
        $TopNavs = 'CategoryLists';
        include template('StudyAbroadNewsCategoryLists');
    }
}

?>