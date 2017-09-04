<?php
	/**
	 * @desc  移民资讯类别表
	 * Class TblImmigrationCategoryModule
	 * Author Zf
	 */
class TblImmigrationCategoryModule extends CommonModule{
	
	public $KeyID = 'CategoryID';
	public $TableName = 'tbl_immigration_categories';

        /**
	 * 保存分类
	 * 返回保存结果 成功 分类ID 不存在 false;
	 *
	 * @param arr $Data 分类数据
	 * @return boolean  返回信息
	 */
	public function Create($Data) {
		global $DB;
		if (! $Data ['DisplayOrder'])
			$Data ['DisplayOrder'] = $this->GetDisplayOrder ( $Data ['ParentCategoryID'] );
		if ($Data ['ParentCategoryID']) {
			$ParentDetail = $this->GetDisplayOrder ( $Data ['ParentCategoryID'] );
			if ($ParentDetail) {
				$Data ['Level'] = $ParentDetail ['Level'] + 1;
				$Data ['CategoryIDS'] = $ParentDetail ['CategoryIDS'] . ',' . $Data ['ParentCategoryID'];
				$Data ['GlobalDisplayOrder'] = $ParentDetail ['GlobalDisplayOrder'] . ',' . $Data ['DisplayOrder'];
			}
		} else {
			$Data ['Level'] = 1;
			$Data ['CategoryIDS'] = '0';
			$Data ['GlobalDisplayOrder'] = $Data ['DisplayOrder'];
		}
                $r = $DB->insertArray ( $this->TableName, $Data, true );
		if ($r) {
			$DB->Update ( 'update `' . $this->TableName . '` set `CategoryIDS`=\'' . $Data ['CategoryIDS'] . ',' . $r . '\' where `' . $this->KeyID . '` = \'' . $r . '\'' );
			return $r;
		} else
			return false;
	}        

	/**
	 * 更新分类
	 * 返回保存结果
	 *
	 * @param int $CategoryID 分类ID
	 * @param arr $Data 分类数据
	 * @return boolean  返回信息
	 */
	public function Update($CategoryID, $Data) {
		global $DB;
		$ParentDetail = $this->GetInfoByKeyID($Data ['ParentCategoryID']);
		if ($ParentDetail) {
			$Data ['GlobalDisplayOrder'] = $ParentDetail ['GlobalDisplayOrder'] . ',' . $Data ['DisplayOrder'];
			$Data ['CategoryIDS'] = $ParentDetail ['CategoryIDS'] . ',' . $CategoryID;
		}
		return $DB->UpdateWhere ( $this->TableName, $Data, '`' . $this->KeyID . '` =' . $CategoryID );
	}        
        
        /**
	 * 查看当前排序
	 * 返回最新的排序值
	 *
	 * @param int $ParentCategoryID 分类信息
	 * @return int  返回信息
	 */
	public function GetDisplayOrder($ParentCategoryID) {
		global $DB;
		$sql = 'select max(`DisplayOrder`) as m from `' . $this->TableName . '` where `ParentCategoryID`=' . $ParentCategoryID;
		$result = $DB->getone ( $sql );
		return $result ? $result ['m'] + 1 : 1;
	}

/**
	 * 更新分类排序
	 *
	 * @param int $ParentCategoryID 父级分类ID
	 */
	public function UpdateDisplayOrder($ParentCategoryID) {
		global $DB;
		if ($ParentCategoryID) {
			$ParentDetail = $this->GetInfoByKeyID($ParentCategoryID);
		}
		$sql = 'select `' . $this->KeyID . '` from `' . $this->TableName . '` where `ParentCategoryID`=\'' . $ParentCategoryID . '\' order by  `DisplayOrder` asc';
		$rs = $DB->Select ( $sql );
		if ($rs) {
			foreach ( $rs as $i => $list ) {
				$NewDisplayOrder = $i + 1;
				if ($ParentDetail) {
					$NewGlobalDisplayOrder = $ParentDetail ['GlobalDisplayOrder'] . ',' . $NewDisplayOrder;
					$NewIDS = $ParentDetail ['CategoryIDS'] . ',' . $list [$this->KeyID];
					$NewLevel = $ParentDetail ['Level'] + 1;
				} else {
					$NewGlobalDisplayOrder = '0,' . $NewDisplayOrder;
					$NewIDS = '0,' . $list [$this->KeyID];
					$NewLevel = 1;
				}
				$DB->Update ( 'update `' . $this->TableName . '` set `GlobalDisplayOrder`=\'' . $NewGlobalDisplayOrder . '\',`DisplayOrder`=\'' . $NewDisplayOrder . '\',`CategoryIDS`=\'' . $NewIDS . '\', `Level`=\'' . $NewLevel . '\' where `' . $this->KeyID . '`=\'' . $list [$this->KeyID] . '\'' );
				$this->UpdateDisplayOrder ( $list [$this->KeyID] );
			}
		}
	}               
        
}
?>