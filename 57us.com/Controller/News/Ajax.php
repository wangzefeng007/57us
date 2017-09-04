<?php
class Ajax {
    public function Index() {
        $Intention = trim ( $_POST ['Intention'] );
        $this->$Intention ();
    }

    /**
     * @desc  收藏操作(添加收藏/移除收藏)
     */
    public function OperateCollection (){
        $ID = $_POST['id'];
        $Type = $_POST['type'];
        $UserID = $_SESSION['UserID'];
        if(isset($_SESSION['UserID']) && $_SESSION['UserID']!=''){
            $CollectionModule = new TblCollectionModule();
            $Data = array(
                'UserID'=>$UserID,
                'RelevanceID' => $ID,
                'Type' => $Type,
                'AddTime'=>date("Y-m-d H:i:s",time())
            );
            $Collection = $CollectionModule->GetInfoByWhere(' and RelevanceID = ' .$ID. ' and Type = '.$Type.' and UserID ='.$UserID );
            if($Collection){
                $json_result=array('ResultCode'=>100,'Message'=>'您已经收藏过该篇文章了');
            }
            else{
                $result = $CollectionModule->InsertInfo($Data);
                if($result){
                    $json_result =  array ('ResultCode' => 200, 'Message' => '收藏成功' ) ;
                }else{
                    $json_result =  array ('ResultCode' => 100, 'Message' => '收藏失败' ) ;
                }
            }
        }
        else{
            $json_result=array('ResultCode'=>101,'Message'=>'请先登录');
        }
        echo json_encode ($json_result);
    }
}