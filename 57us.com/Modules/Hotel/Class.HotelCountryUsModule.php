<?php
Class HotelCountryUsModule extends CommonModule{

    public $KeyID = 'CityCode';
    public $TableName = 'hotel_country_us';

    /**
     * 模糊查询城市名称
     * @param $keyword
     * @return array
     */
    public function GetCityByKeyWord($keyword){
        global $DB;
        $sql = 'select * from ' . $this->TableName.' where `CityLongName_Cn` LIKE "' . $keyword . '%"';
        return $DB->select( $sql );
    }

    /**
     * 单个查询城市
     * @param $ID
     * @return string
     */
    public function GetCityName($ID,$type=''){
        global $DB;
        $sql = 'select * from '.$this->TableName.' where `CityCode`='.$ID;
        $result = $DB->getone($sql);
        if($result['CityCame_Cn']){
            if($type){
                if(!$result['CityCame']){
                    return $result['CityCame_Cn'];
                }else{
                    return $result['CityCame'].'-'.$result['CityCame_Cn'];
                }
            }else{
                return $result['CityCame'].'（'.$result['CityCame_Cn'].'）';
            }
        }else{
            return $result['CityCame'];
        }
    }

    /**
     * 首页热门城市
     * @param string $limit
     * @return array
     */

    public function GetHomeCity($limit=''){

        global $DB;
        //$result = $_SESSION['hotel_city'];
        if(empty($result)){
            $sql = 'select CityCode,Img,Num,CityeName from hotel_city_rec';
            $result = $DB->select($sql,0,$limit);
//            $_SESSION['hotel_city'] = $result;
            return $result;
        }
    }
}