<?php

class Study
{
    public function __construct(){

    }

    /**
     * @desc  微官网主站
     */
    public function Index(){
        include template('Study/StudyIndex');
    }

}
