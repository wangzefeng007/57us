<?php
	/**
	 * @desc  旅游资讯标签表
	 * Class TblTourKeywordModule
	 * Author Zf
	 */
class TblTourKeywordModule extends  CommonModule{

	public $TableName = 'tbl_tour_keywords';
	public $Keyword = 'Keyword';
	public $KeyID = 'KeyID';
    /**
	 * ID转换标签字符串
	 * 返回结果 标签名
     *
     * @param string $ids
     * @return string
     * Author Leo
	 */            
    public function ToKeywordName($ids){
        if(empty($ids)){
            return '';
        }else{
            $keywords=array();
            $ids_arr = explode(',',$ids);
            global $DB;
            foreach($ids_arr as $id){
                $sql='select `'.$this->Keyword.'` from `'.$this->TableName.'` where `'.$this->KeyID.'`=\''.$id.'\'';
                $result=$DB->getone($sql);
                if($result){
                    $keywords[]=$result[$this->Keyword];
                }
            }
            return implode(',',$keywords);
        }
    }

}
?>