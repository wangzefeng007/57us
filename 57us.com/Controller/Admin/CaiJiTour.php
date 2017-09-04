<?php

/**
 * Created by Zend.
 * User: bob
 * Date: 2017/1/5
 * Time: 16:43
 */
class CaiJiTour
{
    public function __construct()
    {
        global $Module;
        global $Action;
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $ColumnID=intval($_GET['ColumnID']);
        $CaijiColumnUrlInfo = $CaijiColumnUrlModule->GetInfoByWhere(' and MyModule=\''.$Module.'\' and MyAction=\''.$Action.'\' and ColumnID='.$ColumnID);
        //$CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url']);
        $this->CaijiColumnUrlInfo = $CaijiColumnUrlInfo;
    }
    /*
     * 采集数据
     * */
    public function FromJoytrav()
    {
        //采集页数
        $Page=$this->CaijiColumnUrlInfo['Page'];
        $CurrentPage=intval($_GET['P'])?intval($_GET['P']):1;
        if($Page>=$CurrentPage){
            $Url='http://www.joytrav.com/info/tour/'.$CurrentPage.'.html';
        }else{
            die('采集完成');
        }
        //匹配文章列表地址
        $ListZZ = '/<div.*class="infor_mainbox".*>.*<a.*href="(.*)".*>(.*)<\/a>.*<\/div>/isU';
        $CJHtml=file_get_contents($Url);
        preg_match_all($ListZZ,$CJHtml,$ReturnArray);
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $CaijiArticleModule = new CaijiArticleModule();
        foreach ($ReturnArray[1] as $Value) {
            //判断地址是否已经采集
            $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
            if (empty($CaijiUrlAllInfo))
            {
                //没有采集过的地址采集内容
                $Data = $this->GetJoytravInfo($Value);
                //判断内容的标题是否在真是苦里面存在，不存在就添加
                $SerchResult=$CaijiArticleModule->GetInfoByWhere(" and Title='{$Data['Title']}'");
                if(!$SerchResult){
                    if($CaijiArticleModule->InsertInfo($Data)){
                        //添加采集过的url到地址库
                        $CaijiUrlAllModule->InsertInfo(array('GetTime'=>date('Y-m-d H:i:s'),'Url'=>$Value));
                    }
                }
            }
        }
        global $Module;
        global $Action;
        $CurrentPage+=1;
        echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$CurrentPage}\"</script>";
    }

    /*
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    private function GetJoytravInfo($Url = ''){
        $Url ='http://www.joytrav.com/info/2014-07-08/2289.html';
        if ($Url == '') {
            return 0;
        }
        $Content=file_get_contents($Url);
        $Data = array();
        $Data['ArticleType']=2;
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];
        //匹配标题
        preg_match('/<div.*class=".*travelogue_article_le".*>.*<h3.*class="t_article_title".*>(.*)<\/h3>/isU',$Content,$TitleArr);
        $Data['Title']=$TitleArr[1];
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        //匹配内容
        preg_match('/<div.*class="t_article_txt".*>(.*)<!--page end-->/isU',$Content,$ContentArr);
        $Data['Content']=preg_replace(array('/美国华人旅行社/is','/<a.*>(.*)<\/a>/'),array('57美国','$1'),$ContentArr[1]);
        //匹配图片
        preg_match_all('/<img.*src="(.*)".*>/isU',$Data['Content'],$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(file_get_contents($ImgUrl)));
            }
        }
        $Data['Content']=addslashes(str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Data['Content']));
        if(!empty($Data['Content'])){
            $Data['IsHaveContent']=2;
        }
        $Data['FromUrl']=$Url;
        $Data['Image']=$NewImgArr[0];
        return $Data;
    }

    /*
     * 采集数据
     * */
    public function From7niuyue()
    {
        //采集页数
        $Page=$this->CaijiColumnUrlInfo['Page'];
        $CurrentPage=intval($_GET['P'])?intval($_GET['P']):1;
        if($Page>=$CurrentPage){
            $Url=$this->CaijiColumnUrlInfo['Url'].'&page='.$CurrentPage;
        }else{
            die('采集完成');
        }
        //匹配文章列表地址
        $ListZZ = '/<ul\sclass="itemList\sitemListSecondMore*".*>.*<\/ul>/isU';
        $CJHtml=mb_convert_encoding(file_get_contents($Url),'utf-8','gb2312');
        preg_match($ListZZ,$CJHtml,$ListArr);
        if($ListArr[0]!=''){
            preg_match_all('/<a.*href="(.*)">(.*)<\/a>/isU', $ListArr[0],$ReturnArray);
        }else{
            $ReturnArray[1]=array();
        }
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $CaijiArticleModule = new CaijiArticleModule();
        foreach ($ReturnArray[1] as $Value) {
            $Value='http://www.7niuyue.com/'.$Value;
            //判断地址是否已经采集
            $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
            if (empty($CaijiUrlAllInfo))
            {
                //没有采集过的地址采集内容
                $Data = $this->Get7niuyueInfo($Value);
                //判断内容的标题是否在真是苦里面存在，不存在就添加
                $SerchResult=$CaijiArticleModule->GetInfoByWhere(" and Title='{$Data['Title']}'");
                if(!$SerchResult){
                    if($CaijiArticleModule->InsertInfo($Data)){
                        //添加采集过的url到地址库
                        $CaijiUrlAllModule->InsertInfo(array('GetTime'=>date('Y-m-d H:i:s'),'Url'=>$Value));
                    }
                }
            }
        }
        global $Module;
        global $Action;
        $CurrentPage+=1;
        echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$CurrentPage}\"</script>";
    }
    /*
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    private function Get7niuyueInfo($Url = ''){
    
        if ($Url == '') {
            return 0;
        }
        $Content=  mb_convert_encoding(file_get_contents($Url),'utf-8','gb2312');
        $Data = array();
        $Data['ArticleType']=2;
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];
        //匹配标题
        preg_match('/<div.*class="articleDetail".*>.*<h1>(.*)<\/h1>/isU',$Content,$TitleArr);
        $Data['Title']=$TitleArr[1];
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        //匹配内容
        preg_match('/<div\sclass="articleDetail".*>.*<div.*class="con">.*<p>.*<div>(.*)<\/div>.*<\/p>.*<\/div>/isU',$Content,$ContentArr);
        $Data['Content']=preg_replace(array('/美国华人旅行社/is','/<a.*>(.*)<\/a>/'),array('57美国','$1'),$ContentArr[1]);
        //匹配图片
        preg_match_all('/<img.*src="(.*)".*>/isU',$Data['Content'],$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(file_get_contents($ImgUrl)));
            }
        }
        $Data['Content']=addslashes(str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Data['Content']));
        if(!empty($Data['Content'])){
            $Data['IsHaveContent']=2;
        }
        $Data['FromUrl']=$Url;
        $Data['Image']=$NewImgArr[0];
        return $Data;
    }    
    
    /*
     * 采集数据
     * */
    public function FromCitytrip()
    {
        //采集页数
        $Page=$this->CaijiColumnUrlInfo['Page'];
        $CurrentPage=intval($_GET['P'])?intval($_GET['P']):1;
        if($Page>=$CurrentPage){
            $Url=$this->CaijiColumnUrlInfo['Url'].'/page/'.$CurrentPage;
        }else{
            die('采集完成');
        }
        //匹配文章列表地址
        $ListZZ = '/<h2.*class="entry-title">.*<a.*href="(.*)".*rel="bookmark".*title="(.*)">.*<\/a>.*<\/h2>/isU';
        $CJHtml= CurlGetHtml($Url,'default','random');
        preg_match_all($ListZZ,$CJHtml,$ReturnArray);
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $CaijiArticleModule = new CaijiArticleModule();
        foreach ($ReturnArray[1] as $Value) {
            //判断地址是否已经采集
            $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
            if (empty($CaijiUrlAllInfo))
            {
                //没有采集过的地址采集内容
                $Data = $this->GetCitytripInfo($Value);
                //判断内容的标题是否在真是苦里面存在，不存在就添加
                $SerchResult=$CaijiArticleModule->GetInfoByWhere(" and Title='{$Data['Title']}'");
                if(!$SerchResult){
                    if($CaijiArticleModule->InsertInfo($Data)){
                        //添加采集过的url到地址库
                        $CaijiUrlAllModule->InsertInfo(array('GetTime'=>date('Y-m-d H:i:s'),'Url'=>$Value));
                    }
                }
            }
        }
        global $Module;
        global $Action;
        $CurrentPage+=1;
        echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$CurrentPage}\"</script>";
    }
    /*
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    private function GetCitytripInfo($Url = ''){
    
        if ($Url == '') {
            return 0;
        }
        $Content=CurlGetHtml($Url,'default','random');
        $Data = array();
        $Data['ArticleType']=2;
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];
        //匹配标题
        preg_match('/<h1.*class="entry-title">(.*)<\/h1>/isU',$Content,$TitleArr);
        $Data['Title']=$TitleArr[1];
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        //匹配内容
        preg_match('/<div\sclass="single-content">(.*)<\/div>\s+<div.*class="clear">/isU',$Content,$ContentArr);
        $Data['Content']=preg_replace(array('/长岛华人协会/isU','/<a.*>(.*)<\/a>/isU','/<script.*>.*<\/script>/isU'),array('57美国','$1',''),$ContentArr[1]);
        //匹配图片
        preg_match_all('/<img.*src="(.*)".*>/isU',$Data['Content'],$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(CurlGetHtml($ImgUrl,'default','random')));
            }
        }
        $Data['Content']=addslashes(str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Data['Content']));
        if(!empty($Data['Content'])){
            $Data['IsHaveContent']=2;
        }
        $Data['FromUrl']=$Url;
        $Data['Image']=$NewImgArr[0];
        return $Data;
    }

 /*
     * 采集数据
     * */
    public function FromGousa()
    {
        //采集页数
        $Page=$this->CaijiColumnUrlInfo['Page'];
        $CurrentPage=intval($_GET['P'])?intval($_GET['P']):1;
        if($Page>=$CurrentPage){
            $Url=$this->CaijiColumnUrlInfo['Url'].'/page/'.$CurrentPage;
        }else{
            die('采集完成');
        }
        //匹配文章列表地址
        $ListZZ = '/<h2.*class="entry-title">.*<a.*href="(.*)".*rel="bookmark".*title="(.*)">.*<\/a>.*<\/h2>/isU';
        $CJHtml=file_get_contents($Url);
        preg_match_all($ListZZ,$CJHtml,$ReturnArray);
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $CaijiArticleModule = new CaijiArticleModule();
        foreach ($ReturnArray[1] as $Value) {
            //判断地址是否已经采集
            $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
            if (empty($CaijiUrlAllInfo))
            {
                //没有采集过的地址采集内容
                $Data = $this->GetGousaInfo($Value);
                //判断内容的标题是否在真是苦里面存在，不存在就添加
                $SerchResult=$CaijiArticleModule->GetInfoByWhere(" and Title='{$Data['Title']}'");
                if(!$SerchResult){
                    if($CaijiArticleModule->InsertInfo($Data)){
                        //添加采集过的url到地址库
                        $CaijiUrlAllModule->InsertInfo(array('GetTime'=>date('Y-m-d H:i:s'),'Url'=>$Value));
                    }
                }
            }
        }
        global $Module;
        global $Action;
        $CurrentPage+=1;
        echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$CurrentPage}\"</script>";
    }
    /*
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    private function GetGousaInfo($Url = ''){
    
        if ($Url == '') {
            return 0;
        }
        $Content=file_get_contents($Url);
        $Data = array();
        $Data['ArticleType']=2;
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];
        //匹配标题
        preg_match('/<h1.*class="entry-title">(.*)<\/h1>/isU',$Content,$TitleArr);
        $Data['Title']=$TitleArr[1];
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        //匹配内容
        preg_match('/<div\sclass="single-content">(.*)<\/div>\s+<div.*class="clear">/isU',$Content,$ContentArr);
        $Data['Content']=preg_replace(array('/长岛华人协会/isU','/<a.*>(.*)<\/a>/isU','/<script.*>.*<\/script>/isU'),array('57美国','$1',''),$ContentArr[1]);
        //匹配图片
        preg_match_all('/<img.*src="(.*)".*>/isU',$Data['Content'],$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(file_get_contents($ImgUrl)));
            }
        }
        $Data['Content']=addslashes(str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Data['Content']));
        if(!empty($Data['Content'])){
            $Data['IsHaveContent']=2;
        }
        $Data['FromUrl']=$Url;
        $Data['Image']=$NewImgArr[0];
        return $Data;
    }     
    
}