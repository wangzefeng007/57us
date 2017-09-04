<?php

class Wechat
{
    public function __construct(){

    }

    /**
     * @desc  微官网主站
     */
    public function Index(){
        include template('Wechat/WechatIndex');
    }

}
