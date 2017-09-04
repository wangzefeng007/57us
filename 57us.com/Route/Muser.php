<?php

$RouteArr=array(
    //'控制器小写@方法小写'=>'真实控制器名@真实方法名'
    
    //资讯会员中心
    'member@register'=>'Muser@Register',  //快速注册
    'member@resetpassword'=>'Muser@ResetPassword', //找回密码
    'member@login'=>'Muser@Login', //账号登录页面
    'member@mobilelogin'=>'Muser@MobileLogin', //手机登录页面
    'member@mycenter'=>'Muser@MyCenter',//会员中心
    'member@mywallet'=>'Muser@MyWallet',//我的资产
    'member@withdraw'=>'Muser@Withdraw',//提现
    'member@information'=>'Muser@Information',//常用资料
    'member@editinformation'=>'Muser@EditInformation',//修改常用资料页
    'member@editpassword' => 'Muser@EditPassword',//修改密码
    'member@collection'=>'Muser@Collection',//我的收藏
    'member@messagelist'=>'Muser@MessageList', //消息列表
    'member@signout' => 'Muser@SignOut',//退出登录
    
    //旅游会员中心
    'musertour@index'=>'MuserTour@Index', //旅游会员中心首页
    'musertour@myorder'=>'MuserTour@MyOrder', //旅游订单列表
    'musertour@travelorderdetail'=>'MuserTour@TravelOrderDetail', //订单详情
    'musertour@commoninfo'=>'MuserTour@CommonInfo', //常用信息编辑
    'musertour@editpassenger'=>'MuserTour@EditPassenger', //编辑旅客
    'musertour@editshippingaddress'=>'MuserTour@EditShippingAddress', //编辑地址
    'musertour@evaluate'=>'MuserTour@Evaluate', //出游评价
    
    //留学会员中心
    'muserstudy@index'=>'MuserStudy@Index',
    'muserstudy@matching'=>'MuserStudy@Matching',
    'muserstudy@matchingdetail'=>'MuserStudy@MatchingDetail',
    
    //支付回调
    'paytour@groupresult'=>'PayTour@GroupResult',
    'paytour@playresult'=>'PayTour@PlayResult',
    
    //ajax
    'ajax@index'=>'Ajax@Index',
    'ajaxstudy@index'=>'AjaxStudy@Index',    
    'ajaxtourorder@index'=>'AjaxTourOrder@Index', //旅游会员订单
);

