<?php
/**
 * @desc  留学资讯标签表
 * Class TblStudyAbroadKeywordModule
 * Author Zf
 */
class TblStudyAbroadKeywordModule extends  CommonModule {

	public $KeyID = 'KeyID';
	public $Keyword = 'Keyword';
	public $TableName = 'tbl_study_abroad_keywords';
	/**
	 * ID转换标签字符串
	 * 返回结果 标签名
	 * 
	 * @param string $ids
	 * @return string 
	 * Author Leo
	 */
	public function ToKeywordName($ids) {
		if (empty ( $ids )) {
			return '';
		} else {
			$keywords = array ();
			$ids_arr = explode ( ',', $ids );
			global $DB;
			foreach ( $ids_arr as $id ) {
				$sql = 'select `' . $this->Keyword . '` from `' . $this->TableName . '` where `' . $this->KeyID . '`=\'' . $id . '\'';
				$result = $DB->GetOne ( $sql );
				if ($result) {
					$keywords [] = $result [$this->Keyword];
				}
			}
			return implode ( ',', $keywords );
		}
	}

}
?>