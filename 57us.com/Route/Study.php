<?php
    $RouteArr=array(
        //'控制器小写@方法小写'=>'真实控制器名@真实方法名'
        
        //公共路由
        'commonajax@index'=>'CommonAjax@Index',
        'commoncontroller@identityselection'=>'CommonController@IdentitySelection',
        'commoncontroller@messagelist'=>'CommonController@RedirectMessagePage',  
        'commoncontroller@redirectmessagepage'=>'CommonController@RedirectMessagePage',        
        'tools@gpa'=>'Tools@GPA',
        
        //订单 Order.php
        'order@course'=>'Order@Course',
        'order@service'=>'Order@Service',
        'order@createorder'=>'Order@CreateOrder',
        'order@studytourorder'=>'Order@StudyTourOrder',
        'order@choicepay'=>'Order@ChoicePay',
        'order@pay'=>'Order@Pay',
        'order@result'=>'Order@Result',
        
        //首页 StudyTour.php
        'study@index'=>'Study@Index',

        //首页搜索 StudyTour.php
        'study@search'=>'Study@Search',
        
        //高中院校 HighSchool.php
        'highschool@index'=>'HighSchool@Index',
        'highschool@details'=>'HighSchool@Details',
        'highschool@getlists'=>'HighSchool@GetLists',
        'highschool@getmysqlwhere'=>'HighSchool@GetMysqlWhere',        
        
        //本科院校 College.php
        'college@index'=>'College@Index',
        'college@details'=>'College@Details',
        'college@getlists'=>'College@GetLists',
        'college@getmysqlwhere'=>'College@GetMysqlWhere',
        
        //研究生院校 GraduateSchool.php
        'graduateschool@index'=>'GraduateSchool@Index',
        'college@details'=>'GraduateSchool@Details',
        'graduateschool@majorgrad'=>'GraduateSchool@MajorGrad',
        'graduateschool@getlists'=>'GraduateSchool@GetLists',
        'graduateschool@getmysqlwhere'=>'GraduateSchool@GetMysqlWhere',
        
        //顾问前台 Consultant.php
        'consultantajax@index'=>'ConsultantAjax@Index',        
        'consultant@lists'=>'Consultant@Lists',   
        'consultant@detail'=>'Consultant@Detail',   
        'consultant@servicelists'=>'Consultant@ServiceLists',   
        'consultantajax@servicedetail'=>'Consultant@ServiceDetail',   
        
        //教师前台 Teacher.php
        'teacherajax@index'=>'TeacherAjax@Index',
        'teacher@lists'=>'Teacher@Lists',
        'teacher@detail'=>'Teacher@Detail',
        'teacher@courselists'=>'Teacher@CourseLists',
        'teacher@coursesdetail'=>'Teacher@CoursesDetail',
        
        //学生后台路由 StudentManage.php 
        'studentmanageajax@index'=>'StudentManageAjax@Index', // 学生后台AJAX StudentManageAjax.php
        'studentmanage@index'=>'StudentManage@Index',
        'studentmanage@myorder'=>'StudentManage@MyOrder',// 学生服务订单
        'studentmanage@mytourorder'=>'StudentManage@MyTourOrder',// 学生游学订单
        'studentmanage@myorderdetails'=>'StudentManage@MyOrderDetails',// 学生服务订单详情
        'studentmanage@mytourorderdetails'=>'StudentManage@MyTourOrderDetails',// 学生游学订单详情
        'studentmanage@messages'=>'StudentManage@Messages',        
        'studentmanage@collection'=>'StudentManage@Collection',        
        'studentmanage@applyschool'=>'StudentManage@ApplySchool',
        'studentmanage@choseschool'=>'StudentManage@ChoseSchool',        
        'studentmanage@vackground'=>'StudentManage@BackGround',        
        'studentmanage@instruments'=>'StudentManage@Instruments',
        'studentmanage@collectinfo'=>'StudentManage@CollectInfo',
        'studentmanage@translation'=>'StudentManage@Translation',     
        'studentmanage@visa'=>'StudentManage@Visa',
        'studentmanage@wallet'=>'StudentManage@Wallet',
        'studentmanage@ishesitate'=>'StudentManage@IsHesitate',
        'studentmanage@background'=>'StudentManage@BackGround',
        //顾问后台路由 ConsultantManage.php
        'consultantmanageajax@index'=>'ConsultantManageAjax@Index', // 顾问后台AJAX ConsultantManageAjax.php
        'consultantmanage@mycenter' => 'ConsultantManage@MyCenter',
        'consultantmanage@myorder' => 'ConsultantManage@MyOrder',
        'consultantmanage@myorderdetails' => 'ConsultantManage@MyOrderDetails',
        'consultantmanage@assets' => 'ConsultantManage@Assets',        
        'consultantmanage@successcase' => 'ConsultantManage@SuccessCase',
        'consultantmanage@addsuccesscase' => 'ConsultantManage@AddSuccessCase',
        'consultantmanage@servicelist' => 'ConsultantManage@ServiceList',
        'consultantmanage@addservice' => 'ConsultantManage@AddService',
        'consultantmanage@savesuccess' => 'ConsultantManage@SaveSuccess',    
        'consultantmanage@customer' => 'ConsultantManage@Customer',
        'consultantmanage@underreview' => 'ConsultantManage@UnderReview',        
        'consultantmanage@customer' => 'ConsultantManage@Customer',
        'consultantmanage@myinfosettings' => 'ConsultantManage@MyInfoSettings',
        'consultantmanage@myinfoauditview' => 'ConsultantManage@MyInfoAuditView',
        'consultantmanage@approvemyinfo' => 'ConsultantManage@ApproveMyInfo',

        'consultantmanage@clientmanage'=>'ConsultantManage@ClientManage',
        'consultantmanage@clientmanageservice'=>'ConsultantManage@ClientManageService',
        'consultantmanage@clientmanagestudent'=>'ConsultantManage@ClientManageStudent',
        'consultantmanage@itemallot'=>'ConsultantManage@ItemAllot',
        'consultantmanage@itemtranslate'=>'ConsultantManage@ItemTranslate',
        'consultantmanage@itemquestionnaire'=>'ConsultantManage@ItemQuestionnaire',
        'consultantmanage@itemschoolchoose'=>'ConsultantManage@ItemSchoolChoose',
        'consultantmanage@itemdocument'=>'ConsultantManage@ItemDocument',
        'consultantmanage@itemschoolapply'=>'ConsultantManage@ItemSchoolApply',
        'consultantmanage@itemvisa'=>'ConsultantManage@ItemVisa',







        //教师后台路由 TeacherManage.php
        'teachermanageajax@index'=>'TeacherManageAjax@Index', // 教师后台AJAX TeacherManageAjax.php
        'teachermanage@mycenter'=>'TeacherManage@MyCenter',
        'teachermanage@courselist'=>'TeacherManage@CourseList',
        'teachermanage@courseadd'=>'TeacherManage@CourseAdd',
        'teachermanage@savesuccess'=>'TeacherManage@SaveSuccess',
        'teachermanage@underreview'=>'TeacherManage@UnderReview',
        'teachermanage@assets'=>'TeacherManage@Assets',
        'teachermanage@successcase'=>'TeacherManage@SuccessCase',
        'teachermanage@successcaseadd'=>'TeacherManage@SuccessCaseAdd',
        'teachermanage@myinfosettings'=>'TeacherManage@MyInfoSettings',
        'teachermanage@myinfoauditview'=>'TeacherManage@MyInfoAuditView',
        'teachermanage@approvemyInfo'=>'TeacherManage@ApproveMyInfo',
        'teachermanage@myorder'=>'TeacherManage@MyOrder',
        //游学前台路由 StudyTour.php
        'studytour@index'=>'StudyTour@Index',
        'studytour@detail'=>'StudyTour@Detail',
        'studytour@placeorder'=>'StudyTour@PlaceOrder',
        
        //问答路由
        'ask@index'=>'Ask@Index',
        'ask@team'=>'Ask@Team',
        'ask@teamdetail'=>'Ask@TeamDetail',
    );