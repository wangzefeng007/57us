<?php
	/**
	 * @desc  旅游资讯标签表
	 * Class TblTourKeywordModule
	 * Author Zf
	 */
class TblTravelsKeywordModule extends CommonModule{
    
        public $TableName = 'tbl_travels_keywords';
	public $Keyword = 'Keyword';
	public $KeyID = 'KeyID';
        /**
	 * 插入标签 获取标签ID串
	 * 返回结果 标签ID
         *
         * @param string $keywords
         * @return string
         * Author Leo
	 */
        public function ToKeywordIDS($keywords){
            if(empty($keywords)){
                return '';
            }else{
                $ids=array();
                $keyword_arr = explode(',',$keywords);
				global $DB;
                foreach($keyword_arr as $keyword){
                    $sql='select `'.$this->KeyID.'` from `'.$this->TableName.'` where `'.$this->Keyword.'`=\''.$keyword.'\'';
                    $result=$DB->getone($sql);
                    if($result){
                        $ids[]=$result[$this->KeyID];
                    }else{
                        $data[$this->Keyword]=$keyword;
                        $result=$DB->insertArray($this->TableName,$data,true);
                        if($result){
                            $ids[]=$result;
                        }
                    }
                }
                return implode(',',$ids);
            }
        }
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