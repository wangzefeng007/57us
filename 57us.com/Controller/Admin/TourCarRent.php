<?php
class TourCarRent {
    public function __construct() {
        IsLogin();

    }
    public function TourCarRentList(){
        include SYSTEM_ROOTPATH . '/Modules/Zuche/Class.ZucheOrderModule.php';
        $ZucheOrderModule = new ZucheOrderModule();
        $Page = intval ( $_GET ['Page'] );
        $Page = $Page ? $Page : 1;
        $PageSize = 10;
        $MysqlWhere ='';
        $StatusInfo = $ZucheOrderModule->Status;
        // 搜索条件
        $PageUrl = '';
        if ($_GET['OrderNo'] ){
            $OrderNo = trim($_GET ['OrderNo']);
            $MysqlWhere .=' and concat(OrderNum) like \'%'. $OrderNo .'%\'';
            $PageUrl .='&OrderNo='.$OrderNo;
        }
        if ($_GET ['Status']){
            $Status = trim($_GET ['Status']);
            $MysqlWhere .=' and `Status` = \''. $Status .'\'';
            $PageUrl .='&Status='.$Status;
        }
        // 跳转到该页面
        if ($_POST['page']) {
            $page = $_POST['page'];
            tourl('/index.php?Module=TourCarRent&Action=TourCarRentList&Page=' . $page . $PageUrl);
        }
        $Rscount = $ZucheOrderModule->GetListsNum ( $MysqlWhere );
        if ($Rscount ['Num']) {
            $Data = array ();
            $Data ['RecordCount'] = $Rscount ['Num'];
            $Data ['PageSize'] = ($PageSize ? $PageSize : $Data ['RecordCount']);
            $Data ['PageCount'] = ceil ( $Data ['RecordCount'] / $PageSize );
            $Data ['Page'] = min ( $Page, $Data ['PageCount'] );
            $Offset = ($Page - 1) * $Data ['PageSize'];
            if ($Page > $Data ['PageCount'])
                $page = $Data ['PageCount'];
            $Data['Data'] = $ZucheOrderModule->GetLists ( $MysqlWhere, $Offset, $Data ['PageSize'] );
            foreach ( $Data ['Data'] as $Key => $Value ) {
                $Destination = explode(',',$Value['Destination']);
                $Attractions = explode(',',$Value['Attractions']);
                if($Destination[1]){
                    $Destin = "$Destination[0],$Destination[1]";
                    $Data ['Data'][$Key]['Destination'] = $Destin;
                }
                if($Attractions[1]){
                    $Attrac = "$Attractions[0],$Attractions[1]";
                    $Data ['Data'][$Key]['Attractions'] = $Attrac;
                }
            }
            MultiPage ( $Data, 10 );
        }
        include template ( 'TourCarRentList' );
    }
    public function TourCarRentDetail(){
        include SYSTEM_ROOTPATH . '/Modules/Zuche/Class.ZucheOrderModule.php';
        $ZucheOrderModule = new ZucheOrderModule();
        $StatusInfo = $ZucheOrderModule->Status;
        if( $_GET){
            $ID = $_GET['ID'];
            $OderInfo = $ZucheOrderModule->GetInfoByKeyID($ID);
        }
        include template ('TourCarRentDetail');
    }
    public function TourCarRentEdit(){
        include SYSTEM_ROOTPATH . '/Modules/Zuche/Class.ZucheOrderModule.php';
        $ZucheOrderModule = new ZucheOrderModule();
        $StatusInfo = $ZucheOrderModule->Status;
        if( $_GET){
            $ID = $_GET['ID'];
            $OderInfo = $ZucheOrderModule->GetInfoByKeyID($ID);
        }
        if($_POST){
            $ID = $_POST['ID'];
            if ($_POST['Status']){
                $Date['Status'] = trim($_POST['Status']);
                $OrderInfo=$ZucheOrderModule->GetInfoByKeyID($ID);
                //添加订单状态更新日志
                include SYSTEM_ROOTPATH.'/Modules/Tour/Class.TourProductOrderLogModule.php';
                $OrderLogModule = new TourProductOrderLogModule();
                $LogData = array('OrderNumber'=>$OrderInfo['OrderNum'],'AdminID'=>$_SESSION['AdminID'],'OldStatus'=>$OrderInfo['Status'],'NewStatus'=> $Date['Status'],'OperateTime'=>date("Y-m-d H:i:s",time()),'IP'=>GetIP(),'Type'=>'4','Remarks'=>'后台操作');
                $OrderLogModule->InsertInfo($LogData);
            }
            if ($_POST['Remarks']){
                $Date['Remarks'] = trim($_POST['Remarks']);
            }
            $Updatestatus = $ZucheOrderModule->UpdateInfoByKeyID($Date,$ID);
            if($Updatestatus){
                alertandgotopage ( "更新成功", '/index.php?Module=TourCarRent&Action=TourCarRentEdit&ID='.$ID);
            } else {
                alertandgotopage ( "更新失败", '/index.php?Module=TourCarRent&Action=TourCarRentEdit&ID='.$ID );
            }
        }
        include template ( 'TourCarRentEdit' );
    }
    public function TourRecommendList(){
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.ZucheRecommendModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Zuche/Class.TourAreaModule.php';
        $ZucheRecommendModule = new ZucheRecommendModule ();
        $TourAreaModule = new TourAreaModule ();
        $SqlWhere = '';
        if ($_GET ['Title'] != '') {
            $SqlWhere .= ' and concat(Title) like \'%'. $_GET['Title'] .'%\'';
        }
        $Data['Data'] =$ZucheRecommendModule->GetList($SqlWhere);
        foreach ($Data['Data'] as $key => $value){
            $TourAreaDetails = $TourAreaModule->GetInfoByKeyID( $value['AreaID'] );
            $Data['Data'][$key]['City'] = $TourAreaDetails['CnName'];
        }
        include template ( 'TourRecommendList' );
    }
    public function TourRecommendAdd(){
        include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
        include SYSTEM_ROOTPATH . '/Modules/Zuche/Class.ZucheRecommendModule.php';
        $TourAreaModule = new TourAreaModule ();

        $SqlWhere ='and ParentID = 5';
        $TourAreaLists = $TourAreaModule->GetInfoByWhere($SqlWhere,true);
        if($_POST){
            $Data ['AreaID'] = $_POST ['AreaID'];
            $Data ['Title'] = $_POST ['Title'];
            $Data ['QuoteUrl'] = $_POST ['QuoteUrl'];
            $Data ['Money'] = $_POST ['Money'];
            $Data ['R1'] = $_POST ['R1'];
            $Data ['S1'] = $_POST ['S1'];
            //上传图片
            $ID = $_POST['ID'];
            include SYSTEM_ROOTPATH .'/Include/MultiUpload.class.php';
            if ($_FILES['CarImg']['size'][0] > 0) {
                $Upload = new MultiUpload ( 'CarImg' );
                $File = $Upload->upload ();
                $Picture = $File[0] ? $File[0] : '';
                $Data ['CarImg'] = $Picture;
            }

            $ZucheRecommendModule = new ZucheRecommendModule();
            if ($ID > 0) {
                if (isset ( $Data ['CarImg'] )) {
                    $RecommendInfo = $ZucheRecommendModule->GetRecommendInfo ( $ID );
                    DelFromImgServ($RecommendInfo ['CarImg'] );
                }
                $Recommend = $ZucheRecommendModule->UpdateRecommend( $Data, $ID );
            }else{
                $ID = $ZucheRecommendModule->InsertInfo ( $Data );
            }
            if ($Recommend || $ID) {
                alertandgotopage ( "操作成功", '/index.php?Module=TourCarRent&Action=TourRecommendEdit&ID='.$ID);
            } else {
                alertandgotopage ( "操作失败", '/index.php?Module=TourCarRent&Action=TourRecommendEdit&ID='.$ID);
            }
        }
        include template ( 'TourRecommendAdd' );
    }

    public function TourRecommendEdit(){
        if( $_GET){
            $ID = $_GET['ID'];
            include SYSTEM_ROOTPATH . '/Modules/Tour/Class.TourAreaModule.php';
            include SYSTEM_ROOTPATH . '/Modules/Zuche/Class.ZucheRecommendModule.php';
            $ZucheRecommendModule = new ZucheRecommendModule();
            $RecommendInfo = $ZucheRecommendModule->GetRecommendInfo($ID);
            $RecommendInfo['CarImg']=LImageURL.$RecommendInfo['CarImg'];
            $TourAreaModule = new TourAreaModule ();
            $TourAreaDetails = $TourAreaModule->GetInfoByKeyID( $RecommendInfo['AreaID'] );
            $SqlWhere ='and ParentID = 5';
            $TourAreaLists = $TourAreaModule->GetInfoByWhere($SqlWhere,true);
        }
        include template ( 'TourRecommendAdd' );
    }

    public function RecommendDelete(){
        if( $_GET) {
            $ID = $_GET['ID'];
            include SYSTEM_ROOTPATH . '/Modules/Zuche/Class.ZucheRecommendModule.php';
            $ZucheRecommendModule = new ZucheRecommendModule();
            $DeleteRecommend =$ZucheRecommendModule->DeleteRecommend($ID);
            $RecommendInfo = $ZucheRecommendModule->GetRecommendInfo ( $ID );
            DelFromImgServ($RecommendInfo ['CarImg'] );

            if ($DeleteRecommend){
                alertandgotopage ( "删除成功", '/index.php?Module=TourCarRent&Action=TourRecommendList' );
            }else{
                alertandgotopage ( "删除失败", '/index.php?Module=TourCarRent&Action=TourRecommendList' );
            }
        }
    }
}

