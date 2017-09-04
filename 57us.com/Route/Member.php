<?php
$RouteArr = array(
    //'控制器小写@方法小写'=>'真实控制器名@真实方法名'
    //AJAX
    //'evaluateajax@index'=>'EvaluateAjax@Index', 删除

    //会员--美国留学--服务订单
    'ajaxstudy@index' => 'AjaxStudy@Index',
    'memberstudy@serviceorderlist' => 'MemberStudy@ServiceOrderList',
    'memberstudy@serviceorderdetail' => 'MemberStudy@ServiceOrderDetail',
    'memberstudy@tourorderlist' => 'MemberStudy@TourOrderList',
    'memberstudy@tourorderdetail' => 'MemberStudy@TourOrderDetail',
    'memberstudy@information' => 'MemberStudy@Information',
    'memberstudy@chooseschool' => 'MemberStudy@ChooseSchool',
    'memberstudy@document' => 'MemberStudy@Document',
    'memberstudy@schoolapply' => 'MemberStudy@SchoolApply',
    'memberstudy@visa' => 'MemberStudy@Visa',
    'memberstudy@translate' => 'MemberStudy@Translate',
    'memberstudy@background' => 'MemberStudy@Background',


    //会员旅游
    'membertour@tourorderlist' => 'MemberTour@TourOrderList',
    'membertour@carrentorderlist' => 'MemberTour@CarRentOrderList',
    'membertour@hotelorderlist' => 'MemberTour@HotelOrderList',
    'membertour@visaorderlist' => 'MemberTour@VisaOrderList',
    'membertour@highlevelorderlist' => 'MemberTour@HighLevelOrderList',
    'membertour@tourorderdetail' => 'MemberTour@TourOrderDetail',
    'membertour@carrentorderdetail' => 'MemberTour@CarrentOrderDetail',
    'membertour@hotelorderdetail' => 'MemberTour@HotelOrderDetail',
    'membertour@visaorderdetail' => 'MemberTour@VisaOrderDetail',
    'membertour@highLevelorderdetail' => 'MemberTour@HighLevelOrderDetail',
    
    //会员中心
    'ajax@index' => 'Ajax@Index',
    'member@index' => 'Member@Index',
    'member@information' => 'Member@Information',
    'member@messagelist' => 'Member@MessageList',
    'member@changepassword' => 'Member@ChangePassword',
    'member@changemobile' => 'Member@ChangeMobile',
    'member@changemail' => 'Member@ChangeMail',
    'member@accountsafety' => 'Member@AccountSafety',
    'member@mycollect' => 'Member@MyCollect',
    'member@passengerlist' => 'Member@PassengerList',
    'member@addresslist' => 'Member@AddressList',

    'member@signout' => 'Member@SignOut',
    
    //支付
    'pay@alipay'=>'Pay@AliPay',
    'pay@wxpay'=>'Pay@WXPay',
    'pay@wapalipayreturn'=>'Pay@WapAliPayReturn',
    'pay@wapalipaynotify'=>'Pay@WapAliPayNotify',
    'pay@alipaynotify'=>'Pay@AliPayNotify',
    'pay@wxpaynotify'=>'Pay@WXPayNotify',
    'pay@orderstatus'=>'Pay@OrderStatus',
    
    //回调
    'paycarrent@payorder'=>'PayCarRent@PayOrder',
    'paycarrent@payreturn'=>'PayCarRent@PayReturn',
    'paycarrent@result'=>'PayCarRent@Result',
    'payhighlevel@payorder'=>'PayHighLevel@payorder',
    'payhighlevel@payreturn'=>'PayHighLevel@PayReturn',
    'payhighlevel@result'=>'PayHighLevel@Result',
    'payhotel@result'=>'PayHotel@Result',
    'paystudy@payreturn'=>'PayStudy@PayReturn',
    'paystudy@studytourpayreturn'=>'PayStudy@StudyTourPayReturn',
    'paytour@paymentorder'=>'PayTour@PaymentOrder',
    'paytour@playresult'=>'PayTour@PlayResult',
    'paytour@groupresult'=>'PayTour@GroupResult',
    'paytour@groupresult'=>'PayTour@GroupResult',
    'payvisa@paymentorder'=>'PayVisa@PaymentOrder',
    'payvisa@payreturn'=>'PayVisa@PayReturn',
);