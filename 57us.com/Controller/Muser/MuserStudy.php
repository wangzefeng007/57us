<?php
class MuserStudy
{
    public function __construct(){
    }
    
    /**
     * 会员首页
     */
    public function Index(){
        MuserService::IsLogin();
        $UserID=$_SESSION['UserID'];
        $MemberUserInfoModule=new MemberUserInfoModule();
        $UserInfo=$MemberUserInfoModule->GetInfoByUserID($UserID);
        $Title = '会员中心 - 57美国网';
        include template('MuserStudyIndex');
    } 
    
    /**
     * @desc  我的匹配
     */
    public function Matching(){
        MuserService::IsLogin();
        include template('MuserStudyMatching');
    }

    /**
     * @desc 我的匹配详情
     */
    public function MatchingDetail(){
        MuserService::IsLogin();
        $MarrayInfoModule = new StudyMarryInfoModule();
        $TargetLevel = $MarrayInfoModule->TargetLevel;
        $ServiceType = $MarrayInfoModule->ServiceType;
        $MarryID = $_GET['MarryID'];
        $MarryInfo = $MarrayInfoModule->GetInfoByKeyID($MarryID);
        include template('MuserStudyMatchingDetail');
    }

    
}