<?php
Class HotelAmenityModule{


    public $KeyID = 'ID';
    public $TableName = 'hotel_amenity';

    /**
     * 返回酒店设施
     * @param $string
     * @return array
     */

    public function GetAmenity($string){

        global $DB;
        if(is_array($string)){
            $need = implode(',',$string);
        }else{
            $need = $string;
        }
        $select = 'select * from '.$this->TableName.' where `ID` in('.$need.')';
        $result = $DB->select($select);
        return $result;
    }

    /**
     * 通过ID得到设施图标
     * @param $ID
     * @return mixed
     */
    public function GetAmenityById($ID){

        global $DB;
        $select = 'select `content` from hotel_amenitys where `HotelID`='.$ID;
        $result = $DB->getone($select);
        return $result['content'];
    }

}