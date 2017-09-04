<?php
Class HotelImageModule extends CommonModule{

    public $KeyID = 'ID';
    public $TableName = 'hotel_image';


    /**
     * 查询酒店所有图片
     * @param $HotelID
     * @return array
     */
    public function GetImgByHotelID($HotelID){
        global $DB;
        $sql = 'select * from '.$this->TableName.' where `HotelID`='.$HotelID;
        $info = $DB->select($sql);
        return $info;
    }

    /**
     * 批量查询图片
     * @param $ids
     * @return array
     */
    public function GetImgByHotelIds($ids){
        global $DB;
        $sql = 'select * from '.$this->TableName.' where `ImageType`="Featured Image" AND `HotelID` in('.implode(',',$ids).')';
        $info = $DB->select($sql);
        return $info;
    }

}