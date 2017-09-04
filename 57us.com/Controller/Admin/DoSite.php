<?php

/**
 * User: lushaobo
 * Date: 2016/9/14
 */
class DoSite
{
    /**
     * @desc  写入文档操作
     * @param string $FileName
     * @param string $String
     * @return string
     */
    public function WriteFile($FileName = '', $String = '')
    {
        if ($FileName == '' || $String == '') {
            return '';
        }
        file_put_contents($FileName, $String);
    }

    /**
     * @desc  关闭浏览器
     */
    private function CloseIE()
    {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title></title>
                <script language="javascript">window.opener=null;
                    window.open("","_self");
                    window.close();</script>
                </head>
                <body>
                </body>
                </html>';
        exit();
    }

    /**
     * @desc 生成资讯地图    http://admin.57us.com/index.php?Module=DoSite&Action=DoWwwSite
     */
    public function DoWwwSite()
    {
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadModule.php';
        include SYSTEM_ROOTPATH . "/Modules/News/Class.TblTourModule.php";
        include SYSTEM_ROOTPATH . "/Modules/News/Class.TblImmigrationModule.php";
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblTravelsModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $TblTourModule = new TblTourModule();
        $TblImmigrationModule = new TblImmigrationModule();
        $TblTravelsModule = new TblTravelsModule();
        $TourAreaModule = new TourAreaModule();
        $String = $this->GetWwwSiteTop();
        $TblStudyAbroadLists = $TblStudyAbroadModule->GetInfoByWhere('', true);
        foreach ($TblStudyAbroadLists as $Value) {
            $String .= '<url>
<loc>http://www.57us.com/study/' . $Value['StudyID'] . '.html</loc>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }

        $TblTourLists = $TblTourModule->GetInfoByWhere('', true);
        foreach ($TblTourLists as $Value) {
            $String .= '<url>
<loc>http://www.57us.com/tour/' . $Value['TourID'] . '.html</loc>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }

        $TblImmigrationLists = $TblImmigrationModule->GetInfoByWhere('', true);
        foreach ($TblImmigrationLists as $Value) {
            $String .= '<url>
<loc>http://www.57us.com/immigrant/' . $Value['ImmigrationID'] . '.html</loc>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }

        $TblTravelsLists = $TblTravelsModule->GetInfoByWhere('', true);
        foreach ($TblTravelsLists as $Value) {
            $String .= '<url>
<loc>http://www.57us.com/travels/' . $Value['TravelsID'] . '.html</loc>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        $String .= '</urlset>';
        $this->WriteFile('./Seo/57usSitemap/www.57us.com.xml', $String);
        $this->CloseIE();
    }

    private function GetWwwSiteTop()
    {
        $String = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
<loc>http://www.57us.com/</loc>
<priority>1.0</priority>
<changefreq>daily</changefreq>
</url>';
        $UrlArray = $this->GetWwwCategory();
        foreach ($UrlArray as $Value) {
            $String .= '<url>
<loc>http://www.57us.com/' . $Value . '/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        return $String;
    }

    /**
     * @desc   获取类别
     * @return array
     */
    private function GetWwwCategory()
    {
        return array(
            'tour_tournews',
            'tour_fengjing',
            'tour_meishi',
            'travels',
            'studytopic_uscolege',
            'study_schoolinfo',
            'study_hotprofession',
            'study_highschool',
            'study_college',
            'study_graduate',
            'studytopic_news',
            'study_scost',
            'study_lecture',
            'study_policyinfo',
            'study_interview',
            'study_burse',
            'study_notes',
            'study_life',
            'studytopic_exam',
            'study_ielts',
            'study_ielts_ieltsnous',
            'study_ielts_ieltsexam',
            'study_ielts_ieltsenroll',
            'study_ielts_ieltsvocabulary',
            'study_ielts_listening',
            'study_ielts_ieltscomposition',
            'study_ielts_ieltsread',
            'study_ielts_ieltsoral',
            'study_ielts_ieltstopic',
            'study_ielts_ieltsresearch',
            'study_ielts_ieltsforecast',
            'study_ielts_ieltsproforma',
            'study_toefl',
            'study_toefl_toeflcomposition',
            'study_toefl_toeflresearch',
            'study_toefl_toeflforecast',
            'study_toefl_toeflproforma',
            'study_toefl_toeflnous',
            'study_toefl_toeflexam',
            'study_toefl_toeflread',
            'study_toefl_toeflvocabulary',
            'study_toefl_toeflapply',
            'study_toefl_toeflening',
            'study_toefl_toefloral',
            'study_toefl_toefltopic',
            'study_sat',
            'study_sat_satvocabulary',
            'study_sat_satgapfilling',
            'study_sat_satmathematics',
            'study_sat_satreading',
            'study_sat_satsatreading',
            'study_sat_satgrammar',
            'study_gmat',
            'study_gmat_gmatvocabulary',
            'study_gmat_gmatreading',
            'study_gmat_gmatmathematics',
            'study_gmat_gmatwriting',
            'study_gmat_gmatgrammar',
            'study_gmat_gmatlogic',
            'study_gmat_gmatgapfilling',
            'study_gmat_gmatexperience',
            'study_act',
            'study_act_actmathematics',
            'study_act_actreading',
            'study_act_actsatreading',
            'study_act_actgrammar',
            'study_act_actgapfilling',
            'study_act_actvocabulary',
            'study_gre',
            'study_gre_vocabulary',
            'study_gre_reading',
            'study_gre_writing',
            'study_gre_gapfilling',
            'study_gre_mathematics',
            'study_gre_experience',
            'study_gre_er',
            'studytopic_learning',
            'study_tourtravel',
            'study_travelnotes',
            'study_aq',
            'studytopic_guide',
            'study_policy',
            'study_visa',
            'study_visaskills',
            'study_visaqa',
            'immigrant',
            'immigtopic_way',
            'immigrant_visaskills',
            'immigrant_procedure',
            'immigrant_notice',
            'immigrant_cost',
            'immigtopic_genre',
            'immigrant_skill',
            'immigrant_invest',
            'immigrant_sib',
            'immigrant_other',
            'immigtopic_guide',
            'immigrant_side',
            'immigrant_disport',
            'immigrant_medical',
            'immigrant_focus',
            'immigrant_work',
            'immigtopic_house',
            'immigrant_directory',
            'immigrant_regulations',
            'immigrant_houseinfo'
        );
    }

    /**
     * @desc  生成旅游地图    http://admin.57us.com/index.php?Module=DoSite&Action=DoTourSite
     */
    public function DoTourSite()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlayBaseModule.php';
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $String = $this->GetTourSiteTop();
        $TourProductLineLists = $TourProductLineModule->GetInfoByWhere(' and IsClose=0', true);
        foreach ($TourProductLineLists as $Value) {
            $String .= '<url>
<loc>http://tour.57us.com/group/' . $Value['TourProductID'] . '.html</loc>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        $TourProductPlayBaseLists = $TourProductPlayBaseModule->GetInfoByWhere(' and IsClose=0', true);

        foreach ($TourProductPlayBaseLists as $Value) {
            $String .= '<url>
<loc>http://tour.57us.com/play/' . $Value['TourProductID'] . '.html</loc>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        $String .= '</urlset>';
        $this->WriteFile('./Seo/57usSitemap/tour.57us.com.xml', $String);
        $this->CloseIE();
    }

    private function GetTourSiteTop()
    {
        $String = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
<loc>http://tour.57us.com/</loc>
<priority>1.0</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://tour.57us.com/dingzhi/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://tour.57us.com/group/local/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://tour.57us.com/group/home/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://tour.57us.com/play/feature/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://tour.57us.com/play/daily/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://tour.57us.com/play/ticket/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://hotel.57us.com/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://visa.57us.com/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>http://zuche.57us.com/</loc>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
';
        return $String;
    }
    
    /**
     * @desc 生成手机站地图（一定要放在最后执行）    http://admin.57us.com/index.php?Module=DoSite&Action=DoMSite
     */
    public function DoMSite()
    {
        
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadModule.php';
        include SYSTEM_ROOTPATH . "/Modules/News/Class.TblTourModule.php";
        include SYSTEM_ROOTPATH . "/Modules/News/Class.TblImmigrationModule.php";
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblTravelsModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $TblTourModule = new TblTourModule();
        $TblImmigrationModule = new TblImmigrationModule();
        $TblTravelsModule = new TblTravelsModule();
        $TourAreaModule = new TourAreaModule();
        //头部
        $String = $this->GetMSiteTop();
        
        $TblStudyAbroadLists = $TblStudyAbroadModule->GetInfoByWhere('', true);
        foreach ($TblStudyAbroadLists as $Value) {
            $String .= '<url>
<loc>http://m.57us.com/news/study/' . $Value['StudyID'] . '.html</loc>
<mobile:mobile type="mobile"/>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        
        $TblTourLists = $TblTourModule->GetInfoByWhere('', true);
        foreach ($TblTourLists as $Value) {
            $String .= '<url>
<loc>http://m.57us.com/news/tour/' . $Value['TourID'] . '.html</loc>
<mobile:mobile type="mobile"/>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        
        $TblImmigrationLists = $TblImmigrationModule->GetInfoByWhere('', true);
        foreach ($TblImmigrationLists as $Value) {
            $String .= '<url>
<loc>http://m.57us.com/news/immigrant/' . $Value['ImmigrationID'] . '.html</loc>
<mobile:mobile type="mobile"/>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        
        $TblTravelsLists = $TblTravelsModule->GetInfoByWhere('', true);
        foreach ($TblTravelsLists as $Value) {
            $String .= '<url>
<loc>http://m.57us.com/news/travels/' . $Value['TravelsID'] . '.html</loc>
<mobile:mobile type="mobile"/>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlayBaseModule.php';
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        
        $TourProductLineLists = $TourProductLineModule->GetInfoByWhere(' and IsClose=0', true);
        foreach ($TourProductLineLists as $Value) {
            $String .= '<url>
<loc>http://m.57us.com/group/' . $Value['TourProductID'] . '.html</loc>
<mobile:mobile type="mobile"/>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        $TourProductPlayBaseLists = $TourProductPlayBaseModule->GetInfoByWhere(' and IsClose=0', true);
        
        foreach ($TourProductPlayBaseLists as $Value) {
            $String .= '<url>
<loc>http://m.57us.com/play/' . $Value['TourProductID'] . '.html</loc>
<mobile:mobile type="mobile"/>
<priority>0.6</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        $String .= '</urlset>';
        
        $this->WriteFile('./Seo/57usSitemap/m.57us.com.xml', $String);
        $this->CloseIE();
    }
    
    private function GetMSiteTop()
    {
        $String = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/">
<url>
<loc>http://m.57us.com/</loc>
<mobile:mobile type="mobile"/>
<priority>1.0</priority>
<changefreq>daily</changefreq>
</url>';
        $UrlArray = $this->GetWwwCategory();
        foreach ($UrlArray as $Value) {
            $String .= '<url>
<loc>http://m.57us.com/news/' . $Value . '/</loc>
<mobile:mobile type="mobile"/>
<priority>0.8</priority>
<changefreq>daily</changefreq>
</url>
';
        }
        return $String;
    }

    
   /**
     * @desc  生成留学地图    http://admin.57us.com/index.php?Module=DoSite&Action=DoStudySite
     */
    public function DoStudySite()
    {
        $String = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        <url>
        <loc>http://study.57us.com/</loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant/</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant_service/</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant_service/?t=1</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant_service/?t=2</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant_service/?t=3</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant_service/?t=4</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant_service/?t=5</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant_service/?t=6</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/consultant_service/?t=7</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher/</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher_course/</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher_course/?t=1</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher_course/?t=2</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher_course/?t=3</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher_course/?t=4</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher_course/?t=5</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher_course/?t=6</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/teacher_course/?t=7</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/highschool/</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/college/</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>
        <url>
        <loc>http://study.57us.com/graduateschool/</loc>
        <priority>0.8</priority>
        <changefreq>daily</changefreq>
        </url>';
        include SYSTEM_ROOTPATH.'/Modules/Member/Class.MemberUserInfoModule.php';
        $MemberUserInfoModule=new MemberUserInfoModule();
        $ConsultantList=$MemberUserInfoModule->GetInfoByWhere("and Identity=2 and IdentityState=2", true);
        foreach ($ConsultantList as $Value) {
            $String .= '<url>
            <loc>http://study.57us.com/consultant/' . $Value['UserID'] . '.html</loc>
            <priority>0.6</priority>
            <changefreq>daily</changefreq>
            </url>
            ';
        }
        $TeacherList=$MemberUserInfoModule->GetInfoByWhere("and Identity=3 and IdentityState=2", true);
        foreach ($TeacherList as $Value) {
            $String .= '<url>
            <loc>http://study.57us.com/teacher/' . $Value['UserID'] . '.html</loc>
            <priority>0.6</priority>
            <changefreq>daily</changefreq>
            </url>
            ';
        }  
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyConsultantServiceModule.php';
        $StudyConsultantServiceModule=new StudyConsultantServiceModule();
        $ServiceList=$StudyConsultantServiceModule->GetInfoByWhere("and `Status`=3", true);
        foreach ($ServiceList as $Value) {
            $String .= '<url>
            <loc>http://study.57us.com/consultant_service/' . $Value['ServiceID'] . '.html</loc>
            <priority>0.6</priority>
            <changefreq>daily</changefreq>
            </url>
            ';
        }   
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyTeacherCourseModule.php';
        $StudyTeacherCourseModule=new StudyTeacherCourseModule();
        $CourseList=$StudyTeacherCourseModule->GetInfoByWhere("and `Status`=3", true);
        foreach ($CourseList as $Value) {
            $String .= '<url>
            <loc>http://study.57us.com/teacher_course/' . $Value['CourseID'] . '.html</loc>
            <priority>0.6</priority>
            <changefreq>daily</changefreq>
            </url>
            ';
        }
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyHighSchoolModule.php';
        $StudyHighSchoolModule=new StudyHighSchoolModule();
        $HighSchoolList=$StudyHighSchoolModule->GetInfoByWhere("", true);
        foreach ($HighSchoolList as $Value) {
            $String .= '<url>
            <loc>http://study.57us.com/highschool/' . $Value['HighSchoolID'] . '.html</loc>
            <priority>0.6</priority>
            <changefreq>daily</changefreq>
            </url>
            ';
        }
        include SYSTEM_ROOTPATH.'/Modules/Study/Class.StudyCollegeModule.php';
        $StudyCollegeModule=new StudyCollegeModule();
        $CollegeList=$StudyCollegeModule->GetInfoByWhere("", true);
        foreach ($CollegeList as $Value) {
            $String .= '<url>
            <loc>http://study.57us.com/college/' . $Value['CollegeID'] . '.html</loc>
            <priority>0.6</priority>
            <changefreq>daily</changefreq>
            </url>
            ';
        }        
        $String .= '</urlset>';
        $this->WriteFile('./Seo/57usSitemap/study.57us.com.xml', $String);
        $this->CloseIE();
    }    
}