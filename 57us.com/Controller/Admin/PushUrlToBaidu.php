<?php

/**
 * User: lushaobo
 * Date: 2016/11/1
 */
class PushUrlToBaidu
{
    //推送到百度
    public function PushUrl($Urls = '', $Domain = '')
    {
        $Api = 'http://data.zz.baidu.com/urls?site='.$Domain.'&token=um3KTTAogOInAbGU';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $Api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $Urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
    }
    
    //推送日志
    public function WriteFile($Return = '')
    {
        if ($Return == '') {
            return '';
        }
        $String = date("Y-m-d H:i:s").' | '.$Return.'
';
        file_put_contents('./Seo/Logs/PushUrl.txt', $String, FILE_APPEND);
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
     * @desc 推送资讯文章    http://admin.57us.com/index.php?Module=PushUrlToBaidu&Action=PushUrlWww
     */
    public function PushUrlWww()
    {
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblStudyAbroadModule.php';
        include SYSTEM_ROOTPATH . "/Modules/News/Class.TblTourModule.php";
        include SYSTEM_ROOTPATH . "/Modules/News/Class.TblImmigrationModule.php";
        include SYSTEM_ROOTPATH . '/Modules/News/Class.TblTravelsModule.php';
        $TblStudyAbroadModule = new TblStudyAbroadModule();
        $TblTourModule = new TblTourModule();
        $TblImmigrationModule = new TblImmigrationModule();
        $TblTravelsModule = new TblTravelsModule();
        
        $TblStudyAbroadLists = $TblStudyAbroadModule->GetInfoByWhere(' and HasPushBaidu=0', true);
        foreach ($TblStudyAbroadLists as $Value)
        {
            $UrlArray[] = 'http://www.57us.com/study/'.$Value['StudyID'].'.html';
        }
        $TblTourLists = $TblTourModule->GetInfoByWhere(' and HasPushBaidu=0', true);
        foreach ($TblTourLists as $Value)
        {
            $UrlArray[] = 'http://www.57us.com/tour/'.$Value['TourID'].'.html';
        }
        $TblImmigrationLists = $TblImmigrationModule->GetInfoByWhere(' and HasPushBaidu=0', true);
        foreach ($TblImmigrationLists as $Value)
        {
            $UrlArray[] = 'http://www.57us.com/immigrant/'.$Value['ImmigrationID'].'.html';
        }
        $TblTravelsLists = $TblTravelsModule->GetInfoByWhere(' and HasPushBaidu=0', true);
        foreach ($TblTravelsLists as $Value)
        {
            $UrlArray[] = 'http://www.57us.com/travels/'.$Value['TravelsID'].'.html';
        }
        if (count($UrlArray)==0)
        {
            $this->WriteFile('PushUrlWww | 没提交数据！');
            $this->CloseIE();
        }
        $Return = $this->PushUrl($UrlArray,'www.57us.com');
        //$Return = '{"remain":1,"success":2}';
        //更新已经推送过的记录
        $ReturnArray = json_decode($Return, true);
        $SuccessNums = $ReturnArray['success'];
        foreach ($UrlArray as $Key=>$Val)
        {
            $Nums = $Key+1;
            if ($Nums<$SuccessNums)
            {
                $UpdateInfo['HasPushBaidu'] = 1;
                if (strstr($Val, 'study'))
                {
                    $DeleteInfo = array("http://www.57us.com/study/",".html");
                    $LastInfo = array("","");
                    $StudyID = str_replace($DeleteInfo,$LastInfo,$Val);
                    $GroupIDString .= "http://www.57us.com/study/".$StudyID.".html,";
                    $TblStudyAbroadModule->UpdateInfoByKeyID($UpdateInfo,$StudyID);
                }
                elseif (strstr($Val, 'tour'))
                {
                    $DeleteInfo = array("http://www.57us.com/tour/",".html");
                    $LastInfo = array("","");
                    $TourID = str_replace($DeleteInfo,$LastInfo,$Val);
                    $GroupIDString .= "http://www.57us.com/tour/".$TourID.".html,";
                    $TblTourModule->UpdateInfoByKeyID($UpdateInfo,$TourID);
                }
                elseif (strstr($Val, 'immigrant'))
                {
                    $DeleteInfo = array("http://www.57us.com/immigrant/",".html");
                    $LastInfo = array("","");
                    $ImmigrationID = str_replace($DeleteInfo,$LastInfo,$Val);
                    $GroupIDString .= "http://www.57us.com/immigrant/".$ImmigrationID.".html,";
                    $TblImmigrationModule->UpdateInfoByKeyID($UpdateInfo,$ImmigrationID);
                }
                elseif (strstr($Val, 'travels'))
                {
                    $DeleteInfo = array("http://www.57us.com/travels/",".html");
                    $LastInfo = array("","");
                    $TravelsID = str_replace($DeleteInfo,$LastInfo,$Val);
                    $GroupIDString .= "http://www.57us.com/travels/".$TravelsID.".html,";
                    $TblTravelsModule->UpdateInfoByKeyID($UpdateInfo,$TravelsID);
                }
            }
        }
        $this->WriteFile('PushUrlWww | '.$Return.' | '.$GroupIDString);
        $this->CloseIE();
    }

    
    /**
     * @desc  推送旅游文章    http://admin.57us.com/index.php?Module=PushUrlToBaidu&Action=PushUrlTour
     */
    public function PushUrlTour()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductLineModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourProductPlayBaseModule.php';
        $TourProductLineModule = new TourProductLineModule();
        $TourProductPlayBaseModule = new TourProductPlayBaseModule();
        $TourProductLineLists = $TourProductLineModule->GetInfoByWhere(' and HasPushBaidu=0', true);
        foreach ($TourProductLineLists as $Value) {
            $UrlArray[] = 'http://tour.57us.com/group/'.$Value['TourProductID'].'.html';
        }
        
        $TourProductPlayBaseLists = $TourProductPlayBaseModule->GetInfoByWhere(' and HasPushBaidu=0', true);
        foreach ($TourProductPlayBaseLists as $Value) {
            $UrlArray[] = 'http://tour.57us.com/play/'.$Value['TourProductID'].'.html';
        }
        if (count($UrlArray)==0)
        {
            $this->WriteFile('PushUrlTour | 没提交数据！');
            $this->CloseIE();
        }
        $Return = $this->PushUrl($UrlArray,'tour.57us.com');
        //$Return = '{"remain":1,"success":3}';
        //更新已经推送过的记录
        $ReturnArray = json_decode($Return, true);
        $SuccessNums = $ReturnArray['success'];
        $GroupIDString = '';
        foreach ($UrlArray as $Key=>$Val)
        {
            if ($Key<$SuccessNums)
            {
                $UpdateInfo['HasPushBaidu'] = 1;
                $LastInfo = array("","");
                if (strstr($Val, 'group'))
                {
                    $DeleteInfo = array("http://tour.57us.com/group/",".html");
                    $GroupID = str_replace($DeleteInfo,$LastInfo,$Val);
                    $GroupIDString .= "http://tour.57us.com/group/".$GroupID.".html,";
                    $TourProductLineModule->UpdateInfoByWhere($UpdateInfo,'TourProductID='.intval($GroupID));
                }
                elseif (strstr($Val, 'play'))
                {
                    $DeleteInfo = array("http://tour.57us.com/play/",".html");
                    $PlayID = str_replace($DeleteInfo,$LastInfo,$Val);
                    $GroupIDString .= "http://tour.57us.com/play/".$PlayID.".html,";
                    $TourProductPlayBaseModule->UpdateInfoByWhere($UpdateInfo,'TourProductID='.intval($PlayID));
                }
            }
        }
        $this->WriteFile('PushUrlTour | '.$Return.' | '.$GroupIDString);
        $this->CloseIE();
    }
    
    
    /**
     * @desc  推送院校库    http://admin.57us.com/index.php?Module=PushUrlToBaidu&Action=PushUrlStudySchool
     */
    public function PushUrlStudySchool()
    {
        global $DB;
        $StudyCollegeLists = $DB->select("select CollegeID from study_college limit 2000");
        foreach ($StudyCollegeLists as $Value) {
            $UrlArray[] = 'http://study.57us.com/college/'.$Value['CollegeID'].'.html';
        }
    
        $StudyHighSchoolLists = $DB->select("select HighSchoolID from study_high_school");
        foreach ($StudyHighSchoolLists as $Value) {
            $UrlArray[] = 'http://study.57us.com/highschool/'.$Value['HighSchoolID'].'.html';
        }
        if (count($UrlArray)==0)
        {
            $this->WriteFile('PushUrlStudySchool | 没提交数据！');
            $this->CloseIE();
        }
        $Return = $this->PushUrl($UrlArray,'study.57us.com');
        //更新已经推送过的记录
        $ReturnArray = json_decode($Return, true);
        $SuccessNums = $ReturnArray['success'];
        $GroupIDString = '';
        foreach ($UrlArray as $Key=>$Val)
        {
            if ($Key<$SuccessNums)
            {
                $UpdateInfo['HasPushBaidu'] = 1;
                $LastInfo = array("","");
                if (strstr($Val, 'college'))
                {
                    $DeleteInfo = array("http://study.57us.com/college/",".html");
                    $CollegeID = str_replace($DeleteInfo,$LastInfo,$Val);
                    $GroupIDString .= "http://study.57us.com/college/".$CollegeID.".html,";
                    //$TourProductLineModule->UpdateInfoByWhere($UpdateInfo,'TourProductID='.intval($GroupID));
                }
                elseif (strstr($Val, 'highschool'))
                {
                    $DeleteInfo = array("http://study.57us.com/highschool/",".html");
                    $HighSchoolID = str_replace($DeleteInfo,$LastInfo,$Val);
                    $GroupIDString .= "http://study.57us.com/highschool/".$HighSchoolID.".html,";
                    //$TourProductPlayBaseModule->UpdateInfoByWhere($UpdateInfo,'TourProductID='.intval($PlayID));
                }
            }
        }
        $this->WriteFile('PushUrlStudySchool | '.$Return.' | '.$GroupIDString);
        $this->CloseIE();
    }
}





