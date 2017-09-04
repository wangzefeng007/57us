<?php
/**
 * 酒店基础信息
 */

Class HotelBedTypeModule extends CommonModule{

    public $KeyID = 'ID';
    public $TableName = 'hotel_bedtype';

    public function GetBedName($ID){
        global $DB;
        $select = 'select `Name_Cn` from '.$this->TableName.' where '.$this->KeyID.'='.$ID;
        $result = $DB->getone($select);
        return $result['Name_Cn'];
    }

}