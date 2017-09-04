<?php

class Visa
{

    public function __construct()
    {
        IsLogin();
        include SYSTEM_ROOTPATH.'/Modules/Visa/Class.VisaProducModule.php';
    }
    
    public function GetVisaSeach($A = '') {
        $Array ['c'] = array (
                'c01'=>'北京', 
                'c02'=>'天津', 
                'c03'=>'河北', 
                'c04'=>'山西', 
                'c05'=>'内蒙古', 
                'c06'=>'江西', 
                'c07'=>'山东', 
                'c08'=>'河南', 
                'c09'=>'陕西', 
                'c10'=>'甘肃', 
                'c11'=>'青海', 
                'c12'=>'宁夏', 
                'c13'=>'新疆', 
                'c14'=>'上海', 
                'c15'=>'江苏', 
                'c16'=>'浙江', 
                'c17'=>'福建', 
                'c18'=>'湖北', 
                'c19'=>'湖南', 
                'c20'=>'广东' ,
                'c21'=>'广西' ,
                'c22'=>'海南' ,
                'c23'=>'重庆' ,
                'c24'=>'四川' ,
                'c25'=>'贵州' ,
                'c26'=>'云南' ,
                'c27'=>'西藏' ,
                'c28'=>'辽宁' ,
                'c29'=>'吉林' ,
                'c30'=>'黑龙江'
        );
        $Array ['t'] = array ('t01'=>'个人旅游签证', 't02'=>'探亲访友签证', 't03'=>'商务签证' );
        if ($A == '')
                return $Array;
        else
                return $Array [$A];
    }    
    
    //产品列表
    public function Lists() {
            $VisaModule = new VisaProducModule();
            //分页开始
            $SqlWhere = '';
            $Page = intval ( $_REQUEST ['Page'] );
            $Page = $Page ? $Page : 1;
            $PageSize = 6;
            $Rscount = $VisaModule->GetListsNum( $SqlWhere );
            if ($Rscount ['Num']) {
                    $Data = array ();
                    $Data ['RecordCount'] = $Rscount ['Num'];
                    $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
                    $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
                    $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
                    $Offset = ($Page - 1) * $Data ['PageSize'];
                    if ($Page > $Data ['PageCount'])
                            $page = $Data ['PageCount'];
                    $Data ['Data'] = $VisaModule->GetLists( $SqlWhere, $Offset, $Data ['PageSize']);
                    MultiPage ( $Data, 10 );
            }
            include template ( 'VisaLists' );
            //分页结束
    }

    //基本信息
    public function Details() {
        include SYSTEM_ROOTPATH.'/Include/MultiUpload.class.php';
        $Nav = 'Details';
        $VisaID = intval ( $_GET ['ID'] );
        $VisaModule = new VisaProducModule ();
        if ($_POST) {
                $VisaID = intval ( $_POST ['ID'] );
                $Data ['Title'] = trim ( $_POST ['Title'] );
                $Data ['SubTitle'] = trim ( $_POST ['SubTitle'] );
                $Data ['Keywords'] = trim ( $_POST ['Keywords'] );
                if ($Data ['Title']=='' || $Data ['Keywords']=='')
                {
                        alertandback ( "标题和关键字必须填写" );
                }
                $Data ['Tag'] = trim ( $_POST ['Tag'] );
                $Data ['Tag'] = str_replace ( '，', ',', $Data ['Tag'] );
                $Data ['Package'] = trim ( $_POST ['Package'] );
                $Data ['Package'] = str_replace ( '，', ',', $Data ['Package'] );
                $Data ['Type'] = trim ( $_POST ['Type'] );
                $Data ['Area'] = trim ( $_POST ['Area'] );
                $Data ['Validity'] = trim ( $_POST ['Validity'] );
                $Data ['Entries'] = trim ( $_POST ['Entries'] );
                $Data ['Duration'] = trim ( $_POST ['Duration'] );
                $Data ['Interview'] = trim ( $_POST ['Interview'] );
                $Data ['Stay'] = trim ( $_POST ['Stay'] );
                $Data ['Region'] = trim ( $_POST ['Region'] );
                $Data ['OriginalPrice'] = trim ( $_POST ['OriginalPrice'] );
                $Data ['PresentPrice'] = trim ( $_POST ['PresentPrice'] );
                $Data ['ExpirationTime'] = trim ( $_POST ['ExpirationTime'] );
                $Data ['SuitedPerson'] = trim ( $_POST ['SuitedPerson'] );
                $Data ['SuitedAge'] = trim ( $_POST ['SuitedAge'] );
                $Data ['CostInclude'] = trim ( $_POST ['CostInclude'] );
                $Data ['AddTime'] = date("Y-m-d H:i:s");
                $Data ['Status'] = trim ( $_POST ['Status'] );
                $Data['R1'] = intval($_POST['R1']);
                $Data['S1'] = intval($_POST['S1']);
                $Data['R2'] = intval($_POST['R2']);
                $Data['S2'] = intval($_POST['S2']);
                if ($_FILES ['Image'] ['size'] [0] > 0) {
                        $Upload = new MultiUpload ( 'Image' );
                        $File = $Upload->upload ();
                        $Picture = $File [0] ? $File [0] : '';
                        $Data ['Image'] =  $Picture;
                }
                if (isset ( $Data ['Image'] )) {
                        $VisaDetails = $VisaModule->GetInfoByKeyID ( $VisaID );
                        DelFromImgServ($VisaDetails ['Image'] );
                }
                if ($VisaID > 0) {
                        $UpDate = $VisaModule->UpdateInfoByKeyID ( $Data, $VisaID );
                } else {
                        $VisaID = $VisaModule->InsertInfo ( $Data );
                }
                if ($UpDate || $VisaID) {
                        alertandgotopage ( "操作成功", '/index.php?Module=Visa&Action=Details&ID=' . $VisaID );
                } else {
                        alertandgotopage ( "操作失败", '/index.php?Module=Visa&Action=Details&ID=' . $VisaID );
                }
        }
        $VisaDetails = $VisaModule->GetInfoByKeyID ( $VisaID );
        $VisaDetails['Image']=LImageURL.$VisaDetails['Image'];
        $VisaSeachCity = $this->GetVisaSeach('c');
        $VisaSeachType = $this->GetVisaSeach('t');
        include template ( 'VisaDetails' );
    }
    //套餐介绍
    public function SetMeal() {
        include SYSTEM_ROOTPATH.'/Include/MultiUpload.class.php';
        $Nav = 'SetMeal';
        $VisaID = intval ( $_GET ['ID'] );
        $VisaModule = new VisaProducModule();
        $VisaDetails = $VisaModule->GetInfoByKeyID ( $VisaID );
        if ($_POST) {
            $VisaID = intval ( $_POST ['ID'] );
            $Data ['BaseInfo'] = trim ( $_POST ['BaseInfo'] );
                $UpDate = $VisaModule->UpdateInfoByKeyID($Data,$VisaID);
            if ($UpDate) {
                alertandgotopage ( "操作成功", '/index.php?Module=Visa&Action=SetMeal&ID=' . $VisaID );
            } else {
                alertandgotopage ( "操作失败", '/index.php?Module=Visa&Action=SetMeal&ID=' . $VisaID );
            }
        }
        include template ( 'VisaMeal' );
    }
    //办理流程
    public function SetProcess() {
        $VisaID = intval ( $_GET ['ID'] );
        $Nav = 'SetProcess';
        $VisaModule = new VisaProducModule ();
        $VisaDetails = $VisaModule->GetInfoByKeyID ( $VisaID );
        if ($_POST) {
            $POST = $_POST;
            $VisaID = $POST['ID'];
            $SK=0;
            foreach ($POST['ProTitle'] as $Key => $Value) {
                if ($POST['ProTitle'][$Key] != '') {
                    $UpdateInfo['ProTitle'][$SK] = $Value;
                    $UpdateInfo['ProContent'][$SK] = $POST['ProContent'][$Key];
                    $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['Procedure'] = addslashes($UpdateString);
            $IsOk = $VisaModule->UpdateInfoByKeyID($UpdateData,$VisaID);
            alertandgotopage("操作成功", '/index.php?Module=Visa&Action=SetProcess&ID=' . $VisaID);
        }

        $NewContentInfo = $VisaModule->GetInfoByKeyID($VisaID);
        if ($NewContentInfo['Procedure'] != '')
            $NewContentArray = json_decode($NewContentInfo['Procedure'], true);
        $I = count($NewContentArray['ProTitle']) + 1;
        include template ( 'VisaProcess' );
    }
    //所需材料
    public function SetMaterial() {
        $Nav = 'SetMaterial';
        $VisaID = intval ( $_GET ['ID'] );
        $VisaModule = new VisaProducModule();
        if ($_POST) {
            $POST = $_POST;
            $VisaID = intval ( $POST ['ID'] );
            $SK=0;
            foreach ($POST['Title'] as $Key => $Value) {
                if ($POST['Title'][$Key] != '') {
                    $UpdateInfo['Title'][$SK] = $Value;
                    $UpdateInfo['MaterialRequested'][$SK] = $POST['MaterialRequested'][$Key];
                    $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['MaterialRequested'] = addslashes($UpdateString);
            if ($UpdateData['MaterialRequested']==''){
                alertandgotopage ( "所需材料不能为空", '/index.php?Module=Visa&Action=SetMaterial&ID=' . $VisaID );
            }
            $UpDate = $VisaModule->UpdateInfoByKeyID($UpdateData,$VisaID);
            if ($UpDate) {
                alertandgotopage ( "操作成功", '/index.php?Module=Visa&Action=SetMaterial&ID=' . $VisaID );
            } else {
                alertandgotopage ( "操作失败", '/index.php?Module=Visa&Action=SetMaterial&ID=' . $VisaID );
            }
        }
        $NewContentInfo = $VisaModule->GetInfoByKeyID($VisaID);
        if ($NewContentInfo['MaterialRequested'] != '')
            $NewContentArray = json_decode($NewContentInfo['MaterialRequested'], true);
        $I = count($NewContentArray['Title']) + 1;
        include template ( 'VisaMaterialRequested' );
    }
    //预订须知
    public function SetNotice() {
        $Nav = 'SetNotice';
        $VisaID = intval ( $_GET ['ID'] );
        $VisaModule = new VisaProducModule();
        $VisaDetails = $VisaModule->GetInfoByKeyID ( $VisaID );
        if ($_POST) {
            $VisaID = intval ( $_POST ['ID'] );
            $Data ['Attention'] = trim ( $_POST ['Attention'] );
            $UpDate = $VisaModule->UpdateInfoByKeyID($Data,$VisaID);
            if ($UpDate) {
                alertandgotopage ( "操作成功", '/index.php?Module=Visa&Action=SetNotice&ID=' . $VisaID );
            } else {
                alertandgotopage ( "操作失败", '/index.php?Module=Visa&Action=SetNotice&ID=' . $VisaID );
            }
        }
        include template ( 'VisaAttention' );
    }
    //常见问题
    public function SetProblem() {
        $VisaID = intval ( $_GET ['ID'] );
        $Nav = 'SetProblem';
        $VisaModule = new VisaProducModule ();
        $VisaDetails = $VisaModule->GetInfoByKeyID ( $VisaID );
        if ($_POST) {
            $POST = $_POST;
            $VisaID = $POST['ID'];
            $SK=0;
            foreach ($POST['Title'] as $Key => $Value) {
                if ($POST['Title'][$Key] != '') {
                    $UpdateInfo['Title'][$SK] = $Value;
                    $UpdateInfo['Content'][$SK] = $POST['Content'][$Key];
                    $SK++;
                }
            }
            $UpdateString = json_encode($UpdateInfo, JSON_UNESCAPED_UNICODE);
            $UpdateData['Problem'] = addslashes($UpdateString);
            $IsOk = $VisaModule->UpdateInfoByKeyID($UpdateData,$VisaID);
            alertandgotopage("操作成功", '/index.php?Module=Visa&Action=SetProblem&ID=' . $VisaID);
        }
        $NewContentInfo = $VisaModule->GetInfoByKeyID($VisaID);
        if ($NewContentInfo['Problem'] != '')
            $NewContentArray = json_decode($NewContentInfo['Problem'], true);
        $I = count($NewContentArray['Title']) + 1;
        include template ( 'VisaProblem' );
    }
    //删除文章
    public function Delete(){
        $ArticleIDs = $_REQUEST['ID'];
        if(!empty($ArticleIDs)){
           $VisaProducModule = new VisaProducModule();
            if (is_array($ArticleIDs)) {         
                    foreach ($ArticleIDs as $ArticleID) {
                        $VisaInfo=$VisaProducModule->GetInfoByKeyID($ArticleID);
                        if($VisaProducModule->DeleteByKeyID($ArticleID)){
                            if($VisaInfo['Image']){
                                DelFromImgServ($VisaInfo['Image']);
                            }
                        }
                    }
                    alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);
            } else {
                $VisaInfo=$VisaProducModule->GetInfoByKeyID($ArticleIDs);
                if($VisaProducModule->DeleteByKeyID($ArticleIDs)){
                    if($VisaInfo['Image']){
                        DelFromImgServ($VisaInfo['Image']);
                    }
                    alertandgotopage('已完成删除操作!',$_SERVER['HTTP_REFERER']);                    
                }else{
                    alertandback('删除失败!');
                }
            }            
        }else{
            alertandback('您没有选择准备删除的记录!');
        }
    }
}
?>