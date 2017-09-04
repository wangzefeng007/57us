<?php
/**
 * 留学公寓
 */

Class House{

    /**
     * 公寓首页
     */
    public function Index(){
        include template ( 'HouseIndex' );
    }

    /**
     * 公寓列表
     */
    public function Lists(){
        include template ( 'HouseLists' );
    }

    /**
     * 公寓详情
     */
    public function Detail(){
        include template ( 'HouseDetail' );
    }

}