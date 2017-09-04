<?php

class Tour
{
    public function __construct(){

    }

    /**
     * @desc  微官网主站
     */
    public function Index(){
        include template('Tour/TourIndex');
    }

}
