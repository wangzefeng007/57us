<?php
Class NewsService{
    //获取标签云
    public static function GetTagsCloudList(){
        $TblTagsCloudModule= new TblTagsCloudModule();
        $Index=$TblTagsCloudModule->GetListsNum()['Num'];
        return $TblTagsCloudModule->GetLists('',mt_rand(0,$Index-20),20);
    }
}
