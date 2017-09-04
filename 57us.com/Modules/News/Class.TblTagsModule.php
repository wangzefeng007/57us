<?php
class TblTagsModule extends CommonModule{
	public function __construct() {
		$this->TableName = 'tbl_tags';
		$this->KeyID = 'TagsID';
	}
	function AllLists() {
		global $DB;
		$sql = 'select * from '.$this->TableName.' ORDER BY Sort DESC';
		return $DB->Select ( $sql );
	}
    function GetInfoByKeyID($TagsID='') {
        global $DB;
        $sql = 'select * from '.$this->TableName.' where '.$this->KeyID.'='.$TagsID;
        return $DB->GetOne ( $sql );
    }
    /**
     * 更新广告信息
     */
    public function UpdateInfo($Data, $TagsID) {
        global $DB;
        $Return = $DB->UpdateArray ( $this->TableName, $Data, array ($this->KeyID => intval($TagsID) ) );
        return $Return;
    }

    /**
     * 添加广告信息
     */
    public function InsertArray($Data) {
        global $DB;
        $Return = $DB->insertArray ( $this->TableName, $Data );
        return $Return;
    }

    /**
     * 删除广告信息
     */
    public function Delete($KeyID) {
        global $DB;
        $Sql = 'DELETE FROM '.$this->TableName.' WHERE '.$this->KeyID.'=' . $KeyID;
        return $DB->Delete ( $Sql );
    }

    /**
     * 前台调用
     *
     */
    public function TiHuan($Content='')
    {
        $AllLists = $this->AllLists();
        $I = 0;
        $TiHuanString = ',';
        preg_match_all("/(<img.*>)/iU",$Content,$ImgArr);
        $ImgNum=count($ImgArr[0]);
        if($ImgNum>0){
            for($i=0;$i<$ImgNum;$i++){
                $ReplaceStr[$i]='ReplaceStr_'.$i;
            }
            $Content=str_replace($ImgArr[0],$ReplaceStr,$Content);
        }
        foreach ($AllLists as $Value) {
            if (strstr($Content, $Value['TagsName']) && !strstr($TiHuanString, $Value['TagsName'])) {
                $TiHuanString.=$Value['TagsName'].',';
                $I ++;
                $Content = preg_replace('/'.$Value['TagsName'].'/', '<a href="' . $Value['TagsUrl'] . '" target="_blank" title="' . $Value['TagsName'] . '" class="red">' . $Value['TagsName'] . '</a>', $Content,1);
            }
            if ($I == 3) {
                break;
            }
        }
        $Content=str_replace(array_reverse($ReplaceStr),array_reverse($ImgArr[0]),$Content);
        return $Content;
    }
}
