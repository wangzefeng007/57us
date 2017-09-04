<?php
    $RouteArr=array(
        'm@index'=>'M@Index',
        
        //'控制器小写@方法小写'=>'真实控制器名@真实方法名'
        'news@tour'=>'News@TourIndex',
        'news@tourlists'=>'News@TourLists',
        'news@tourdetail'=>'News@TourDetail',
        'news@study'=>'News@StudyIndex',
        'news@studyparentlists'=>'News@StudyParentLists',
        'news@studylists'=>'News@StudyLists',
        'news@studydetail'=>'News@StudyDetail',
        'news@immigrant'=>'News@ImmigrantIndex',
        'news@immigrantparentlists'=>'News@ImmigrantParentLists',
        'news@immigrantlists'=>'News@ImmigrantLists',
        'news@immigrantdetail'=>'News@ImmigrantDetail',
        'news@class'=>'News@Categories',
        'news@travels'=>'News@TravelsLists',
        'news@travelsdetail'=>'News@TravelsDetail',
        'news@search'=>'News@Search',
        
        //旅游首页
        'tour@newstour'=>'Tour@Index',
        'tour@search'=>'Tour@Search',
        'tour@paysuccess'=>'Tour@PaySuccess',
        'tour@payfailure'=>'Tour@PayFailure',
        
        //旅游ajax
        'ajaxtour@index'=>'AjaxTour@Index',

        //跟团游
        'group@index'=>'Group@Index',
        'group@local'=>'Group@Local',
        'group@home'=>'Group@Home',
        'group@linedetails'=>'Group@LineDetails',
        'group@getdate'=>'Group@GetDate',
        'group@choicedate'=>'Group@GroupChoiceDate',// 日期AJAX
        'group@choiceroom'=>'Group@GroupChoiceRoom',
        'group@order'=>'Group@LineOrder',
        'group@choicepay'=>'Group@ChoicePay',
        
        //当地玩乐
        'play@index'=>'Play@Index',
        'play@lists'=>'Play@Lists',
        'play@details'=>'Play@Details',
        'play@chosecombo'=>'Play@ChoseCombo',
        'play@playplaceorder'=>'Play@PlayPlaceOrder',
        'play@choicepay'=>'Play@ChoicePay',
        'play@getdate'=>'Play@GetDate',
        'play@daily'=>'Play@Lists',
        'play@feature'=>'Play@Lists',
        'play@ticket'=>'Play@Lists',
        'play@getlists'=>'Play@GetLists',

        //留学
        'study@index'=>'Study@Index',
        'study@consultantlist'=>'Study@ConsultantList',
        'study@consultantdetail'=>'Study@ConsultantDetail',
        'study@marryconsultantone'=>'Study@MarryConsultantOne',
        'study@marryconsultanttwo'=>'Study@MarryConsultantTwo',
        'study@marryconsultantthree'=>'Study@MarryConsultantThree',
        'study@marryconsultantfour'=>'Study@MarryConsultantFour',
        'study@estimate'=>'Study@Estimate',
        'study@studyabroad'=>'Study@Index',
        'study@studytourlist'=>'Study@StudyTourList',
        'study@studytourdetail'=>'Study@StudyTourDetail',
        'study@choicedate'=>'Study@ChoiceDate',
        'study@placeorder'=>'Study@PlaceOrder'
    );