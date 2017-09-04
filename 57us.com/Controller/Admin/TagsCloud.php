<?php

/**
 * Class TagsCloud
 * @desc  标签云
 */
class TagsCloud
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
        $TagsModule = new TblTagsCloudModule();
        $TagsList = $TagsModule->GetInfoByWhere('', true);
        include template("TagsCloudLists");
    }

    /**
     * @desc 删除标签
     */
    public function Delete()
    {
        $ID = $_REQUEST['ID'];
        if (!empty($ID)) {
            $TagsModule = new TblTagsCloudModule();
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
            $TagsModule = new TblTagsCloudModule();
            $TagsInfo = $TagsModule->GetInfoByKeyID($TagsID);
        }
        include template("TagsCloudEdit");
    }

    /**
     * @desc 标签保存操作
     */
    public function Save()
    {
        $TagsID = intval($_POST['TagsID']);
        $Data['TagsName'] = trim($_POST['TagsName']);
        $Data['TagsUrl'] = trim($_POST['TagsUrl']);
        $Data['Sort'] = 0;
        $TagsModule = new TblTagsCloudModule();
        if ($TagsID) {
            $Result = $TagsModule->UpdateInfoByKeyID($Data, $TagsID);
        } else {
            $Result = $TagsModule->InsertInfo($Data);
        }
        if ($Result === 0) {
            alertandback('您没有做任何修改');
        }
        if ($Result) {
            alertandgotopage('保存成功', '/index.php?Module=TagsCloud&Action=Lists');
        } else {
            alertandback('保存失败');
        }
    }
    /**
     * @desc 导入标签信息
     */
    public function Add()
    {
        $TagsModule = new TblTagsCloudModule();
        $TblImmigrationCategoryModule = new TblImmigrationCategoryModule();
        $TblStudyAbroadCategoryModule = new TblStudyAbroadCategoryModule();
        $TblTourCategoryModule = new TblTourCategoryModule();
        $TblTravelsCategoriesModule = new TblTravelsCategoriesModule();
        $MysqlWhere = ' and ParentCategoryID =0 ';
        $ParentImmigrationCategory = $TblImmigrationCategoryModule->GetInfoByWhere($MysqlWhere,true);
        $ParentStudyAbroadCategory = $TblStudyAbroadCategoryModule->GetInfoByWhere($MysqlWhere,true);
        $ParentTourCategory = $TblTourCategoryModule->GetInfoByWhere($MysqlWhere,true);
        $ParentTravelsCategories = $TblTravelsCategoriesModule->GetInfoByWhere($MysqlWhere,true);
        //移民标签云
        foreach ($ParentImmigrationCategory as $key=>$value){
            $ParentImmigrationTagsInfo = $TagsModule->GetInfoByWhere(" and TagsName = '.{$value['CategoryName']}'");
            if (!$ParentImmigrationTagsInfo){
                $Data['TagsName'] = trim($value['CategoryName']);
                $Data['TagsUrl'] =  '/immigtopic_'.$value['Alias'].'/';
                $Data['Sort'] = 0;
                $Result = $TagsModule->InsertInfo($Data);
            }else{
                echo '标签已存在';
            }
            $ImmigrationCategory =  $TblImmigrationCategoryModule->GetInfoByWhere(' and ParentCategoryID = '. $value['CategoryID'],true);
            foreach ($ImmigrationCategory as $K=>$V){
                $ImmigrationTagsInfo = $TagsModule->GetInfoByWhere(" and TagsName = '.{$V['CategoryName']}'");
                if (!$ImmigrationTagsInfo){
                    $Data['TagsName'] = trim($V['CategoryName']);
                    $Data['TagsUrl'] =  '/immigrant_'.$V['Alias'].'/';
                    $Data['Sort'] = 0;
                    $Result = $TagsModule->InsertInfo($Data);
                }else{
                    echo '标签已存在';
                }
            }
        }
        //旅游标签云
        foreach ($ParentTourCategory as $key=>$value){
            $ParentTagsInfo = $TagsModule->GetInfoByWhere(" and TagsName = '.{$value['CategoryName']}'");
            if (!$ParentTagsInfo){
                $Data['TagsName'] = trim($value['CategoryName']);
                $Data['TagsUrl'] =  '/tour_'.$value['Alias'].'/';
                $Data['Sort'] = 0;
                $Result = $TagsModule->InsertInfo($Data);
            }else{
                echo '标签已存在';
            }
        }

        //游记标签云
        foreach ($ParentTravelsCategories as $key=>$value){
            $TravelsCategoryName = $value['CategoryName'].'游记';
            $TravelsTagsInfo = $TagsModule->GetInfoByWhere(" and TagsName = '.{$TravelsCategoryName}'");
            if (!$TravelsTagsInfo){
                $Data['TagsName'] = $TravelsCategoryName;
                $Data['TagsUrl'] =  '/travels/_rmmdd'.$value['CategoryID'].'/';
                $Data['Sort'] = 0;
                $Result = $TagsModule->InsertInfo($Data);
            }else{
                echo '标签已存在';
            }
        }
        //留学标签云
        foreach ($ParentStudyAbroadCategory as $key=>$value){
            $ParentStudyTagsInfo = $TagsModule->GetInfoByWhere(" and TagsName = '.{$value['CategoryName']}'");
            if (!$ParentStudyTagsInfo){
                $Data['TagsName'] = trim($value['CategoryName']);
                $Data['TagsUrl'] = '/studytopic_'.$value['Alias'].'/';
                $Data['Sort'] = 0;
                $Result = $TagsModule->InsertInfo($Data);
            }else{
                echo '标签已存在';
            }
            $StudyAbroadCategory =  $TblStudyAbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = '. $value['CategoryID'],true);
            foreach ($StudyAbroadCategory as $K=>$V){
                $StudyTagsInfo = $TagsModule->GetInfoByWhere(" and TagsName = '.{$V['CategoryName']}'");
                if (!$StudyTagsInfo){
                    $Data['TagsName'] = trim($V['CategoryName']);
                    $Data['TagsUrl'] =  '/study_'.$V['Alias'].'/';
                    $Data['Sort'] = 0;
                    $Result = $TagsModule->InsertInfo($Data);
                }else{
                    echo '标签已存在';
                }
                $TwoStudyAbroadCategory  =$TblStudyAbroadCategoryModule->GetInfoByWhere(' and ParentCategoryID = '.$V['CategoryID'],true);
                if ($TwoStudyAbroadCategory){
                    foreach ($TwoStudyAbroadCategory as $ke=>$val){
                        $StudyCategoryName = $V['CategoryName'].$val['CategoryName'];
                        $TwoStudyTagsInfo = $TagsModule->GetInfoByWhere(" and TagsName = '.{$StudyCategoryName}'");
                        if (!$TwoStudyTagsInfo){
                            $Data['TagsName'] = $StudyCategoryName;
                            $Data['TagsUrl'] =  '/study_'.$V['Alias'].'_'.$val['Alias'].'/';
                            $Data['Sort'] = 0;
                            $Result = $TagsModule->InsertInfo($Data);
                        }else{
                            echo '标签已存在';
                        }
                    }
                }
            }
        }
    }
}