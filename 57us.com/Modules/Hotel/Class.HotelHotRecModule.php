<?php
/**
 * 酒店热门推荐
 */

Class HotelHotRecModule{

    public $KeyID = 'ID';
    public $TableName = 'hotel_hot_rec';


    /**
     * 热门推荐首页
     * @param string $limit
     * @return array
     */
    public function GetHotelHot($limit=''){

        global $DB;
//        $result = $_SESSION['hotel_hot'];

        if(empty($result)){
            $sql = 'select HotelID from '.$this->TableName.' where `Status`=1';
            $result = $DB->select($sql,0,$limit);
            if($result){
                $new = array();
                foreach($result as $v){
                    $new[] = $v['HotelID'];
                }
                $sql = 'select * from hotel_base_info where `HotelID` in('.implode(',',$new).')';
                $result = $DB->select($sql);
                $city_ids = array();
                $ids = array();
                foreach($result as $k=>$v){
                    if($v['Name_Cn']){
                        $result[$k]['hotel_name'] = $v['Name'].'('.$v['Name_Cn'].')';
                    }
                    $city_ids[] = $v['CityCode'];
                }
                if($city_ids){
                    $sql = 'select CityCode,CityName,CityCame_Cn from hotel_country_us where `CityCode` in('.implode(',',$city_ids).')';

                    $city_info = $DB->select($sql);
                    $names = $imgs = array();
                    foreach($city_info as $p){
                        $names[$p['CityCode']]['CityName'] = $p['CityName'];
                        $names[$p['CityCode']]['CityCame_Cn'] = $p['CityCame_Cn'];
                    }

                    foreach($result as $k=>$v){
                        if($names[$v['CityCode']]['CityCame_Cn']){
                            $result[$k]['CityCame'] = $names[$v['CityCode']]['CityCame_Cn'];
                        }else{
                            $result[$k]['CityCame'] = $names[$v['CityCode']]['CityName'];
                        }
                        $result[$k]['img'] = LImageURL.$result[$k]['Image'];
                    }



                }
//                $_SESSION['hotel_hot'] = $result;
                return $result;
            }
        }
        return $result;
    }
}