<?php
    $RouteArr=array(
        //'控制器小写@方法小写'=>'真实控制器名@真实方法名'
        'hotel@index'=>'Hotel@Index',
        'hotel@hotellist'=>'Hotel@HotelList',
        'hotel@hotelsearchlist'=>'Hotel@HotelSearchList',
        'hotel@detail'=>'Hotel@Detail',
        //订单
        'hotel@order'=>'Hotel@Order',
        'order@choicepay'=>'Order@ChoicePay',
        'order@pay'=>'Order@Pay',
        //Ajax
        'ajax@index'=>'Ajax@Index',
        'ajax@gethotellist'=>'Ajax@GetHotelList',
        'ajax@getroom'=>'Ajax@GetRoom',
        'ajax@getcity'=>'Ajax@GetCity'
        
    );