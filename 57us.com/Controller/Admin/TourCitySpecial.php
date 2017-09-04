<?php

class TourCitySpecial
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourCitySpecialModule.php';
        include SYSTEM_ROOTPATH . '/Include/MultiUpload.class.php';
    }
    // 添加城市专题基本信息
    public function Add()
    {
        $_GET['Action'] = 'Add';
        $TourCitySpecialModule = new TourCitySpecialModule();
        $AreaID = intval($_GET['AreaID']);
        if ($AreaID)
            $CitySpecialInfo = $TourCitySpecialModule->GetInfoByWhere(' and AreaID = ' . $AreaID);
        $CitySpecialID = intval($_GET['CitySpecialID']);
        if ($CitySpecialID)
            $CitySpecialInfo = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
        if ($_POST) {
            $CitySpecialID = intval($_POST['CitySpecialID']);
            if ($_FILES['Image']['size'][0] > 0) {
                $CitySpecialInfo = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
                if ($CitySpecialInfo['Image'])
                    DelFromImgServ($CitySpecialInfo['Image']);
                $Upload = new MultiUpload('Image');
                $File = $Upload->upload();
                $Picture = $File[0] ? $File[0] : '';
                $Data['Image'] = $Picture;
            }
            if ($_FILES['BannerImage']['size'][0] > 0) {
                $CitySpecialInfo = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
                if ($CitySpecialInfo['BannerImage'])
                    DelFromImgServ($CitySpecialInfo['BannerImage']);
                $Upload = new MultiUpload('BannerImage');
                $File = $Upload->upload();
                $Picture = $File[0] ? $File[0] : '';
                $Data['BannerImage'] = $Picture;
            }
            $Data['TourProductIDS'] = trim($_POST['TourProductIDS']);
            $Data['HotelIDS'] = trim($_POST['HotelIDS']);
            $Data['AreaID'] = intval($_POST['AreaID']);
            $Data['Status'] = intval($_POST['Status']);
            if ($CitySpecialID) {
                $IsOK = $TourCitySpecialModule->UpdateInfoByKeyID($Data, $CitySpecialID);
            } else {
                $IsOK = $TourCitySpecialModule->InsertInfo($Data);
                $CitySpecialID = $IsOK;
            }
            if ($IsOK) {
                alertandgotopage("操作成功", '/index.php?Module=TourCitySpecial&Action=Add&CitySpecialID=' . $CitySpecialID);
            } else {
                alertandgotopage("未操作", '/index.php?Module=TourCitySpecial&Action=Add&CitySpecialID=' . $CitySpecialID);
            }
        }
        include template('TourCitySpecialAdd');
    }
    // 城市专题列表
    public function Lists()
    {
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        $TourCitySpecialModule = new TourCitySpecialModule();
        $Status = $TourCitySpecialModule->Status;
        $TourAreaModule = new TourAreaModule();
        $MySqlWhere = '';
        $Data['Data'] = $TourCitySpecialModule->GetLists($MySqlWhere, 0, 100);
        foreach ($Data['Data'] as $key => $value) {
            $TourArea = $TourAreaModule->GetInfoByKeyID($value['AreaID']);
            $Data['Data'][$key]['City'] = $TourArea['CnName'];
        }
        include template('TourCitySpecialList');
    }
    // 删除城市专题
    public function Delete()
    {
        $TourCitySpecialModule = new TourCitySpecialModule();
        $CitySpecialID = intval($_GET['CitySpecialID']);
        $IsOK = $TourCitySpecialModule->DeleteByKeyID($CitySpecialID);
        if ($IsOK) {
            alertandgotopage("删除成功", '/index.php?Module=TourCitySpecial&Action=Lists');
        } else {
            alertandgotopage("删除失败", '/index.php?Module=TourCitySpecial&Action=Lists');
        }
    }

    /**
     * @desc  更新状态
     */
    public function UpdataStatus()
    {
        $TourCitySpecialModule = new TourCitySpecialModule();
        $Status = $_GET['Status'];
        $CitySpecialID = $_GET['CitySpecialID'];
        $Data['Status'] = $Status==1?0:1;
        $IsOK = $TourCitySpecialModule->UpdateInfoByKeyID($Data,$CitySpecialID);
        if ($IsOK) {
            alertandgotopage("更新成功", '/index.php?Module=TourCitySpecial&Action=Lists');
        } else {
            alertandgotopage("更新失败", '/index.php?Module=TourCitySpecial&Action=Lists');
        }
    }
    // 设置城市必游项目
    public function SpecialCityContent()
    {
        $_GET['Action'] = 'SpecialCityContent';
        $TourCitySpecialModule = new TourCitySpecialModule();
        $AreaID = intval($_GET['AreaID']);
        $CitySpecialID = intval($_GET['CitySpecialID']);
        if ($AreaID)
            $TourCitySpecialInfo = $TourCitySpecialModule->GetInfoByWhere(' and AreaID = ' . $AreaID);
        if ($CitySpecialID)
            $TourCitySpecialInfo = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
        if ($TourCitySpecialInfo['MustTravel'] != '') {
            $NewContentArray = json_decode($TourCitySpecialInfo['MustTravel'], true);
        }
        $I = count($NewContentArray['MusTitle']) + 1;
        if ($_POST) {
            $AreaID = intval($_POST['AreaID']);
            $CitySpecialID = intval($_POST['CitySpecialID']);
            foreach ($_POST['MusTitle'] as $Key => $Value) {
                if ($_POST['MusTitle'][$Key] != '') {
                    $UpdateInfo['MusTitle'][$Key] = $Value;
                    $UpdateInfo['MusURL'][$Key] = $_POST['MusURL'][$Key];
                    $UpdateInfo['MusIntroduce'][$Key] = $_POST['MusIntroduce'][$Key];
                    // 文本图片处理-----------------------------------------------------------------------------
                    if ($_FILES['TitleImage']['size'][0] > 0) {
                        $Upload = new MultiUpload('TitleImage');
                        $File = $Upload->upload();
                        $Picture = $File[0] ? $File[0] : '';
                        $UpdateInfo['TitleImage'][0] = $Picture;
                    } else {
                        $NewContentArray = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
                        $NewContentArray = json_decode($NewContentArray['MustTravel'], true);
                        $UpdateInfo['TitleImage'] = $NewContentArray['TitleImage'];
                    }
                    if ($_FILES['MusImage']['size'][$Key] > 0) {
                        $Upload = new MultiUpload('MusImage');
                        $File = $Upload->upload();
                        $Picture = $File[$Key] ? $File[$Key] : '';
                        $UpdateInfo['MusImage'][$Key] = $Picture;
                    }
                    if ($UpdateInfo['MusImage'][$Key] == '') {
                        $NewContentArray = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
                        $NewContentArray = json_decode($NewContentArray['MustTravel'], true);
                        $UpdateInfo['MusImage'][$Key] = $NewContentArray['MusImage'][$Key];
                    }
                    // 文本图片处理-----------------------------------------------------------------------------
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['MustTravel'] = addslashes($UpdateString);
            $info = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
            if ($info) {
                $IsOk = $TourCitySpecialModule->UpdateInfoByKeyID($UpdateData, $CitySpecialID);
            } else {
                $IsOk = $TourCitySpecialModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourCitySpecial&Action=SpecialCityContent&CitySpecialID=' . $CitySpecialID);
        }
        include template('SpecialCityContent');
    }
    // 设置吃喝玩乐
    public function SpecialPlayContent()
    {
        $_GET['Action'] = 'SpecialPlayContent';
        $TourCitySpecialModule = new TourCitySpecialModule();
        $AreaID = intval($_GET['AreaID']);
        $CitySpecialID = intval($_GET['CitySpecialID']);
        if ($AreaID)
            $TourCitySpecialInfo = $TourCitySpecialModule->GetInfoByWhere(' and AreaID = ' . $AreaID);
        if ($CitySpecialID)
            $TourCitySpecialInfo = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
        if ($TourCitySpecialInfo['NewRecommend'] != '') {
            $NewContentArray = json_decode($TourCitySpecialInfo['NewRecommend'], true);
        }
        $I = count($NewContentArray['NewTitle']) + 1;
        if ($_POST) {
            $AreaID = intval($_POST['AreaID']);
            $CitySpecialID = intval($_POST['CitySpecialID']);
            foreach ($_POST['NewTitle'] as $Key => $Value) {
                if ($_POST['NewTitle'][$Key] != '') {
                    $UpdateInfo['NewTitle'][$Key] = $Value;
                    $UpdateInfo['NewURL'][$Key] = $_POST['NewURL'][$Key];
                    if ($_FILES['NewImage']['size'][$Key] > 0) {
                        $Upload = new MultiUpload('NewImage');
                        $File = $Upload->upload();
                        $Picture = $File[$Key] ? $File[$Key] : '';
                        $UpdateInfo['NewImage'][$Key] = $Picture;
                    }
                    if ($UpdateInfo['NewImage'][$Key] == '') {
                        $NewContentArray = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
                        $NewContentArray = json_decode($NewContentArray['NewRecommend'], true);
                        $UpdateInfo['NewImage'][$Key] = $NewContentArray['NewImage'][$Key];
                    }
                }
            }
            
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['NewRecommend'] = addslashes($UpdateString);
            $info = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
            if ($info) {
                $IsOk = $TourCitySpecialModule->UpdateInfoByKeyID($UpdateData, $CitySpecialID);
            } else {
                $IsOk = $TourCitySpecialModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourCitySpecial&Action=SpecialPlayContent&CitySpecialID=' . $CitySpecialID);
        }
        include template('SpecialPlayContent');
    }
    // 设置活动专题
    public function SpecialTopicContent()
    {
        $_GET['Action'] = 'SpecialTopicContent';
        $TourCitySpecialModule = new TourCitySpecialModule();
        $AreaID = intval($_GET['AreaID']);
        $CitySpecialID = intval($_GET['CitySpecialID']);
        if ($AreaID)
            $TourCitySpecialInfo = $TourCitySpecialModule->GetInfoByWhere(' and AreaID = ' . $AreaID);
        if ($CitySpecialID)
            $TourCitySpecialInfo = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
        if ($TourCitySpecialInfo['SpecialEvent'] != '') {
            $NewContentArray = json_decode($TourCitySpecialInfo['SpecialEvent'], true);
        }
        $I = count($NewContentArray['SpeTitle']) + 1;
        if ($_POST) {
            $AreaID = intval($_POST['AreaID']);
            $CitySpecialID = intval($_POST['CitySpecialID']);
            foreach ($_POST['SpeTitle'] as $Key => $Value) {
                if ($_POST['SpeTitle'][$Key] != '') {
                    $UpdateInfo['SpeTitle'][$Key] = $Value;
                    $UpdateInfo['SpeURL'][$Key] = $_POST['SpeURL'][$Key];
                    if ($_FILES['SpeImage']['size'][$Key] > 0) {
                        $Upload = new MultiUpload('SpeImage');
                        $File = $Upload->upload();
                        $Picture = $File[$Key] ? $File[$Key] : '';
                        $UpdateInfo['SpeImage'][$Key] = $Picture;
                    }
                    if ($UpdateInfo['SpeImage'][$Key] == '') {
                        $NewContentArray = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
                        $NewContentArray = json_decode($NewContentArray['SpecialEvent'], true);
                        $UpdateInfo['SpeImage'][$Key] = $NewContentArray['SpeImage'][$Key];
                    }
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['SpecialEvent'] = addslashes($UpdateString);
            $info = $TourCitySpecialModule->GetInfoByKeyID($CitySpecialID);
            if ($info) {
                $IsOk = $TourCitySpecialModule->UpdateInfoByKeyID($UpdateData, $CitySpecialID);
            } else {
                $IsOk = $TourCitySpecialModule->InsertInfo($UpdateData);
            }
            alertandgotopage("操作成功", '/index.php?Module=TourCitySpecial&Action=SpecialTopicContent&CitySpecialID=' . $CitySpecialID);
        }
        include template('SpecialTopicContent');
    }
}

?>