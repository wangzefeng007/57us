<?php
/**
 * 酒店基础信息
 */

Class HotelBaseInfoModule extends CommonModule{

    public $KeyID = 'ID';
    public $TableName = 'hotel_base_info';

    public function GetHotelByID($HotelID){

        global $DB;

        $info = $DB->getone('select * from '.$this->TableName.' where `HotelID`='.$HotelID);

        return $info;
    }

}