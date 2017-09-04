<?php
/**
 * Created by Zend.
 * User: bob
 * Date: 2017/1/5
 * Time: 16:43
 */
class CaiJiImmigrant
{
    public function __construct()
    {
        global $Module;
        global $Action;
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $ColumnID=intval($_GET['ColumnID']);
        $CaijiColumnUrlInfo = $CaijiColumnUrlModule->GetInfoByWhere(' and MyModule=\''.$Module.'\' and MyAction=\''.$Action.'\' and ColumnID='.$ColumnID);
        $this->CaijiColumnUrlInfo = $CaijiColumnUrlInfo;
        set_time_limit(0);
    }
    /*
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=Liuxue86YiMinFaliFAgui
     * */
    public function Liuxue86YiMinFaliFAgui()
    {
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        //$CaijiColumnUrlInfo['Html'] = mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK');
        print_r($CaijiColumnUrlInfo);exit;
        //匹配文章列表地址
        $ListZZ = '/<h3 style="width:696px;">.*<span>.*<\/span>.*<a href="(.*)" target="_blank">.*<\/a>.*<\/h3>/isU';
        $Url = 'http://www.aojiyouxue.com/zixun/tour_use_info/';
        preg_match_all($ListZZ, $this->CaijiColumnUrlInfo['Html'], $ReturnArray);
        print_r($ReturnArray);exit;
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        foreach ($ReturnArray[1] as $Value) {
            //判断地址是否已经采集
            $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
            if (empty($CaijiUrlAllInfo))
            {
                //没有采集过的地址采集内容
                $Data = $this->GetLiuxueInfo($Value);
                //判断内容的标题是否在真是苦里面存在，不存在就添加
                //添加采集过的url到地址库
            }
        }
    }

    /*
     * 侨外美国移民-移民类别
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=QiaoWaiXinWen
     * */
    public function QiaoWaiXinWen(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];//最大页数
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'list_27_'.$P.'.html');
            //匹配文章列表地址
            $ListZZ = '/<a class="b_title" href="(.*)">.*<\/a>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                $Url = 'http://www.qiaowai.net'.$Value;//补全资讯详情页地址
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Url.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetQiaoWaiInfo($Url);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 侨外美国移民-投资移民
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=QiaoWaiTouZi
     * */
    public function QiaoWaiTouZi(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'list_537_'.$P.'.html');
            //匹配文章列表地址
            $ListZZ = '/<a class="b_title" href="(.*)">.*<\/a>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                $Url = 'http://www.qiaowai.net'.$Value;//补全资讯详情页地址
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Url.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetQiaoWaiInfo($Url);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 侨外美国移民
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=QiaoWaiTouZi
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    public function GetQiaoWaiInfo($Url =''){
        if ($Url==''){
            return 0;
        }
        $Html= file_get_contents($Url);
        $ListZZ = '/<p class="art_t">(.*)<\/p>.*<div class="ytxt_line">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Title = trim($ReturnArray[1][0]);
        $Content = $ReturnArray[2][0];
        $Content = $this->DoFilterInfo($Content);//过滤样式
        $Data = array();
        //采集图片
        preg_match_all('/<img.*src="(.*)".*>/isU',$Content,$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(file_get_contents($ImgUrl)));
            }
        }
        //采集内容
        $Data['Title'] = addslashes($Title);
        $Data['Description'] = trim(strip_tags(_substr($Content, 180)));
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        $Data['Content']=str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Content);
        $Data['Content'] = addslashes($Data['Content']);
        $Data['FromUrl']=$Url;//采集源URL
        $Data['ArticleType']=$this->CaijiColumnUrlInfo['ArticleType'];//采集类型移民
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];//采集移民类别
        if ($NewImgArr[0]!=''){
            $Data['Image']=$NewImgArr[0];//封面图片
        }else{
            $Data['Image']='';
        }
        return $Data;
    }
    /*
     * 滴答网美国移民-办理流程
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagExperience
     * */
    public function TigTagBaike(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'?page='.$P);
            preg_match_all('/<div class="col-list">(.*)<\/div>/isU',mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK'),$CaijiColumnUrlInfo['Html']);
            $CaijiColumnUrlInfo['Html'] = $CaijiColumnUrlInfo['Html'][1][0];
            //匹配文章列表地址
            $ListZZ = '/<li><a href="(.*)" target="_blank">.*<\/a><span>.*<\/span><\/li>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetTigTag($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 滴答网美国移民-办理流程
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagExperience
     * */
    public function TigTagExperience(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'?page='.$P);
            preg_match_all('/<div class="col-list">(.*)<\/div>/isU',mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK'),$CaijiColumnUrlInfo['Html']);
            $CaijiColumnUrlInfo['Html'] = $CaijiColumnUrlInfo['Html'][1][0];
            //匹配文章列表地址
            $ListZZ = '/<li><a href="(.*)" target="_blank">.*<\/a><span>.*<\/span><\/li>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetTigTag($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 滴答网美国移民-法律法规
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagNews
     * */
    public function TigTagNews(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'?page='.$P);
            preg_match_all('/<div class="col-list">(.*)<\/div>/isU',mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK'),$CaijiColumnUrlInfo['Html']);
            $CaijiColumnUrlInfo['Html'] = $CaijiColumnUrlInfo['Html'][1][0];
            //匹配文章列表地址
            $ListZZ = '/<li><a href="(.*)" target="_blank">.*<\/a><span>.*<\/span><\/li>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetTigTag($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 滴答网美国移民-家属团聚
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagEmployment
     * */
    public function TigTagFamily(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'?page='.$P);
            preg_match_all('/<div class="col-list">(.*)<\/div>/isU',mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK'),$CaijiColumnUrlInfo['Html']);
            $CaijiColumnUrlInfo['Html'] = $CaijiColumnUrlInfo['Html'][1][0];
            //匹配文章列表地址
            $ListZZ = '/<li><a href="(.*)" target="_blank">.*<\/a><span>.*<\/span><\/li>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetTigTag($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
        /*
         * 滴答网美国移民-移民类别
         * 采集数据
         * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagEmployment
         * */
    public function TigTagEmployment(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'?page='.$P);
            preg_match_all('/<div class="col-list">(.*)<\/div>/isU',mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK'),$CaijiColumnUrlInfo['Html']);
            $CaijiColumnUrlInfo['Html'] = $CaijiColumnUrlInfo['Html'][1][0];
            //匹配文章列表地址
            $ListZZ = '/<li><a href="(.*)" target="_blank">.*<\/a><span>.*<\/span><\/li>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetTigTag($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 滴答网美国移民-生活周边
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagLiving
     * */
    public function TigTagLiving(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'?page='.$P);
            preg_match_all('/<div class="col-list">(.*)<\/div>/isU',mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK'),$CaijiColumnUrlInfo['Html']);
            $CaijiColumnUrlInfo['Html'] = $CaijiColumnUrlInfo['Html'][1][0];
            //匹配文章列表地址
            $ListZZ = '/<li><a href="(.*)" target="_blank">.*<\/a><span>.*<\/span><\/li>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetTigTag($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 滴答网美国移民-生活周边
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagLiving
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    private function GetTigTag($Url=''){
        if ($Url == '') {
            return 0;
        }
        $Html= file_get_contents($Url);
        $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
        $ListZZ = '/<div class="content1">.*<h1>(.*)<\/h1>(.*)<div class="artmix">/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Title = trim($ReturnArray[1][0]);
        $Content = $ReturnArray[2][0];
        //查看是否有分页内容
        if (strstr($Html,'<p align="center">')){
            preg_match_all('/<p align="center">(.*)<\/p>/isU', $Html, $ReturnPage);
            preg_match_all('/<a href="(.*)">.*<\/a>/isU', $ReturnPage[1][0], $ReturnPages);
           if (!empty($ReturnPages[1])){
               foreach ($ReturnPages[1] as $value){
                   $Htmls= file_get_contents($value);
                   $Htmls = mb_convert_encoding($Htmls, 'UTF-8', 'GBK');
                   preg_match_all($ListZZ, $Htmls, $ReturnArrays);
                   $Content .= $ReturnArrays[2][0];
               }
           }
        }
        $Content = preg_replace ("/滴答网/isU","57美国", $Content );
        $Content = preg_replace ("/<p align=\"center\">.*<\/p>/isU","", $Content );
        $Content = preg_replace ("/<div class=\"summary\">.*<\/div>/isU","", $Content );
        $Content = preg_replace ("/<div class=\"artinfo\">.*<\/div>/isU","", $Content );
        $Content = preg_replace ("/<p style=\"text-align:center;margin-bottom:0px;\">.*<\/p>/isU","", $Content );
        $Content = preg_replace ("/<script type=\"text\/javascript\">.*<\/script>/isU","", $Content );//过滤JS
        $Content = $this->DoFilterInfo($Content);//过滤样式
        $Content = preg_replace ("/<img src=\"http:\/\/img2.tigtag.com\/images\/content16\/t20160905.jpg\" style=\"width:560px;height:225px;min-height:0;max-width:615px;\" \/>/isU","", $Content );//过滤公众号
        $Content = preg_replace ("/<img src=\"http:\/\/img2.tigtag.com\/images\/content16\/ta20160905app.png\" style=\"width:650px;height:200px;min-height:0;max-width:615px;\" \/>/isU","", $Content );//过滤广告图片
        $Data = array();
        //采集图片
        preg_match_all('/<img.*src="(.*)".*>/isU',$Content,$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(file_get_contents($ImgUrl)));
            }
        }

        //采集内容
        $Data['Title'] = addslashes($Title);
        $Data['Description'] = trim(strip_tags(_substr($Content, 180)));
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        $Data['Content']=str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Content);//图片替换
        $Data['Content'] = addslashes($Data['Content']);
        $Data['FromUrl']=$Url;//采集源URL
        $Data['ArticleType']=$this->CaijiColumnUrlInfo['ArticleType'];//采集类型移民
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];//采集移民类别
        if ($NewImgArr[0]!=''){
            $Data['Image']=$NewImgArr[0];//封面图片
        }else{
            $Data['Image']='';
        }
        return $Data;
    }
    /*
     * 滴答网美国移民-投资移民
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagBusiness
     * */
    public function TigTagBusiness(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].'?page='.$P);
            preg_match_all('/<div class="col-list">(.*)<\/div>/isU',mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK'),$CaijiColumnUrlInfo['Html']);
            $CaijiColumnUrlInfo['Html'] = $CaijiColumnUrlInfo['Html'][1][0];
            //匹配文章列表地址
            $ListZZ = '/<li><a href="(.*)" target="_blank">.*<\/a><span>.*<\/span><\/li>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetTigTagBusinessInfo($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 滴答网美国移民-投资移民
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=TigTagBusiness
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    private function GetTigTagBusinessInfo($Url = ''){
        if ($Url == '') {
            return 0;
        }
        $Html= file_get_contents($Url);
        $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
        $ListZZ = '/<h1>(.*)<\/h1>.*<div class="articon">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Title = trim($ReturnArray[1][0]);
        $Content = $ReturnArray[2][0];
        $Content = preg_replace ("/滴答网/isU","57美国", $Content );
        $Content = preg_replace ("/<p style=\"text-align:center;margin-bottom:0px;\">.*<\/p>/isU","", $Content );
        $Content = preg_replace ("/<p align=\"center\">.*<\/p>/isU","", $Content );//过滤页码
        $Content = preg_replace ("/<script type=\"text\/javascript\">.*<\/script>/isU","", $Content );//过滤JS
        $Content = $this->DoFilterInfo($Content);//过滤样式
        $Content = preg_replace ("/<img src=\"http:\/\/img2.tigtag.com\/images\/content16\/t20160905.jpg\" style=\"width:560px;height:225px;min-height:0;max-width:615px;\" \/>/isU","", $Content );//过滤公众号

        $Content = preg_replace ("/<img src=\"http:\/\/img2.tigtag.com\/images\/content16\/ta20160905app.png\" style=\"width:650px;height:200px;min-height:0;max-width:615px;\" \/>/isU","", $Content );//过滤广告图片
        $Data = array();
        //采集图片
        preg_match_all('/<img.*src="(.*)".*>/isU',$Content,$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(file_get_contents($ImgUrl)));
            }
        }
        //采集内容
        $Data['Title'] = addslashes($Title);
        $Data['Description'] = trim(strip_tags(_substr($Content, 180)));
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        $Data['Content']=str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Content);//图片替换
        $Data['Content'] = addslashes($Data['Content']);
        $Data['FromUrl']=$Url;//采集源URL
        $Data['ArticleType']=$this->CaijiColumnUrlInfo['ArticleType'];//采集类型移民
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];//采集移民类别
        if ($NewImgArr[0]!=''){
            $Data['Image']=$NewImgArr[0];//封面图片
        }else{
            $Data['Image']='';
        }
        return $Data;
    }

    /*
     * 留学360美国移民-生活指南
     * 采集数据
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=liuxue360
     * */
    public function LiuXue360(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $Url = preg_replace ("/article-101.html/isU","article-101-$P.html", $CaijiColumnUrlInfo['Url']);
            $CaijiColumnUrlInfo['Html'] = file_get_contents($Url);
            $CaijiColumnUrlInfo['Html'] = mb_convert_encoding($CaijiColumnUrlInfo['Html'], 'UTF-8', 'GBK');
            //匹配文章列表地址
            $ListZZ = '/<li><cite>.*<\/cite><span><a href=".*" target="_blank">.*<\/a><\/span><a href="(.*)" title=".*" target="_blank">.*<\/a><\/li>/isU';
            preg_match_all($ListZZ, $CaijiColumnUrlInfo['Html'], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetLiuxue360Info($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 留学360美国移民
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=liuxue360
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    private function GetLiuxue360Info($Url = '')
    {
        if ($Url == '') {
            return 0;
        }
        $Html= file_get_contents($Url);
        $Html = mb_convert_encoding($Html, 'UTF-8', 'GBK');
        $ListZZ = '/<h1>(.*)<\/h1>.*<\/div>.*<div class="post_content">(.*)<\/div>/isU';
        preg_match_all($ListZZ, $Html, $ReturnArray);
        $Title = trim($ReturnArray[1][0]);
        $Content = $ReturnArray[2][0];
        $Content = $this->DoFilterInfo($Content);
        $Content = preg_replace ("/留学360/isU","57留学", $Content );
        $Content = preg_replace ("/美国教育联盟/isU","57美国", $Content );
        $Content = preg_replace ("/爱留学美国/isU","57美国", $Content );
        $Content = preg_replace ("/爱留学美国部/isU","57美国", $Content );
        $Content = preg_replace ("/美国教育网/isU","57美国", $Content );
        $Content = preg_replace ( "/www.liuxue360.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/www.edumg.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/school.ailiuxue.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/us.liuxue360.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/www.xueus.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/us.ailiuxue.com/isU", "www.57us.com", $Content );
        $Data = array();
        //采集图片
        preg_match_all('/<img.*src="(.*)".*>/isU',$Content,$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(file_get_contents($ImgUrl)));
            }
        }
        //采集内容
        $Data['Title'] = addslashes($Title);
        $Data['Description'] = trim(strip_tags(_substr($Content, 180)));
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        $Data['Content']=str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Content);
        $Data['Content'] = addslashes($Data['Content']);
        $Data['FromUrl']=$Url;//采集源URL
        $Data['ArticleType']=$this->CaijiColumnUrlInfo['ArticleType'];//采集类型移民
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];//采集移民类别
        if ($NewImgArr[0]!=''){
            $Data['Image']=$NewImgArr[0];//封面图片
        }else{
            $Data['Image']='';
        }
        return $Data;
    }
    /*
     * 采集数据
     * *出国留学网美国移民-移民类别
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=LiuXue86YiMinShengHuo
     * */
    public function LiuXue86YiMinZiXun(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].$P.'.html');
            //匹配文章列表地址
            $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
            $ListZZ = '/<a target="_blank" href="(.*)">.*<\/a>/isU';
            preg_match_all($ListZZ1, $CaijiColumnUrlInfo['Html'], $Html1);
            preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetLiuxue86Info($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 采集数据
     * *出国留学网美国移民-生活指南
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=LiuXue86YiMinShengHuo
     * */
    public function LiuXue86YiMinShengHuo(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].$P.'.html');
            //匹配文章列表地址
            $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
            $ListZZ = '/<a target="_blank" href="(.*)">.*<\/a>/isU';
            preg_match_all($ListZZ1, $CaijiColumnUrlInfo['Html'], $Html1);
            preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetLiuxue86Info($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 采集数据
     * *出国留学网美国移民-移民攻略
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=LiuXue86YiMinJingYan
     * */
    public function LiuXue86YiMinJingYan(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].$P.'.html');
            //匹配文章列表地址
            $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
            $ListZZ = '/<a target="_blank" href="(.*)">.*<\/a>/isU';
            preg_match_all($ListZZ1, $CaijiColumnUrlInfo['Html'], $Html1);
            preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetLiuxue86Info($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 采集数据
     * *出国留学网美国移民-投资房产
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=LiuXue86YiMinZhengCe
     * */
    public function LiuXue86YiMinZhengCe(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].$P.'.html');
            //匹配文章列表地址
            $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
            $ListZZ = '/<a target="_blank" href="(.*)">.*<\/a>/isU';
            preg_match_all($ListZZ1, $CaijiColumnUrlInfo['Html'], $Html1);
            preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetLiuxue86Info($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     * 采集数据
     * *出国留学网美国移民
     * http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=LiuXue86
     * */
    public function LiuXue86(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        if ($P<=$Page) {
            $CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url'].$P.'.html');
            //匹配文章列表地址
            $ListZZ1= '/<div class="gai_ul">(.*)<\/div>/isU';
            $ListZZ = '/<a target="_blank" href="(.*)">.*<\/a>/isU';
            preg_match_all($ListZZ1, $CaijiColumnUrlInfo['Html'], $Html1);
            preg_match_all($ListZZ, $Html1[1][0], $ReturnArray);
            foreach ($ReturnArray[1] as $Value) {
                //判断地址是否已经采集
                $CaijiUrlAllInfo = $CaijiUrlAllModule->GetInfoByWhere(' and Url=\''.$Value.'\'');
                if (empty($CaijiUrlAllInfo))
                {
                    //没有采集过的地址采集内容
                    $Data = $this->GetLiuxue86Info($Value);
                    if ($Data['Title']!='' && $Data['Content']!=''){
                        //判断内容的标题是否在正式数据库里面存在，不存在就添加
                        $ImmigrationInfo = $TblImmigrationModule->GetInfoByWhere(' and Title = \''.$Data['Title'].'\'');
                        if (!$ImmigrationInfo){
                            //插入数据库
                            $InsertArticle = $CaijiArticleModule->InsertInfo($Data);
                            //添加采集过的url到地址库
                            if ($InsertArticle){
                                $Date['Url'] = $Data['FromUrl'];//采集的URL
                                $Date['GetTime'] =  $Data['AddTime'];//采集时间
                                $CaijiUrlAllModule->InsertInfo($Date);
                                $CaijiColumnUrlModule->UpdateNum($CaijiColumnUrlInfo['ColumnID']);
                            }
                        }else{
                            echo '此源链接的采集标题已存在，未入数据库 '.$Data['FromUrl'];
                        }
                    }else{
                        echo '此源链接未采集到标题或内容 '.$Data['FromUrl'];
                    }
                }
            }
            global $Module;
            global $Action;
            $P++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$P}\"</script>";
        }else{
            echo "采集".$CaijiColumnUrlInfo['Url'].'结束';
            exit;
        }
    }
    /*
     *http://admin.57us.com/index.php?Module=CaiJiImmigrant&Action=LiuXue86
     * 出国留学网美国移民
     * 采集内容和处理数据，不需要操作任何数据库；必须要私有函数。
     * 返回要入库的数组
     * */
    private function GetLiuxue86Info($Url = ''){
        if ($Url == '') {
            return 0;
        }
        $Html= file_get_contents($Url);
        if (strstr($Html, '<div class="main_zhengw">')) {
            $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"main_zhengw\">(.*)<div class=\"guanggao3 clearfix\">/isU';
            preg_match_all($ListZZ, $Html, $ReturnArray);
            $Title = trim($ReturnArray[1][0]);
            $Content = $ReturnArray[2][0];
            //相同class="main_zhengw" 尾部不一样<div id="pages" class=" clearfix">
            if (strstr($Html, '<div id="pages" class=" clearfix">')){
                $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"main_zhengw\">(.*)<div id="pages" class=" clearfix">/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                $Title = trim($ReturnArray[1][0]);
                $Content = $ReturnArray[2][0];

            }elseif (strstr($Html, '<div class="ye_780_four3">')){
                $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"main_zhengw\">(.*)<div class="ye_780_four3">/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                $Title = trim($ReturnArray[1][0]);
                $Content = $ReturnArray[2][0];
            }elseif (strstr($Html, '<div class="C_xinj">')){
                $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"main_zhengw\">(.*)<div class="C_xinj">/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                $Title = trim($ReturnArray[1][0]);
                $Content = $ReturnArray[2][0];
            }elseif (strstr($Html, '<div class="zhengw_ccc">')) {
                $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"zhengw_ccc\">(.*)<div class="zhengw_ccc">/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                $Title = trim($ReturnArray[1][0]);
                $Content = $ReturnArray[2][0];
            }
        } else {
            $ListZZ = '/<div id=\"content_head\">(.*)原文来源/isU';
            preg_match_all($ListZZ, $Html, $ReturnArray);
            $Html = $ReturnArray[0][0];
            unset($ReturnArray, $ListZZ);
            $ListZZ = '/<h1>(.*)<\/h1>.*<div id=\"digest\">(.*)原文来源/isU';
            preg_match_all($ListZZ, $Html, $ReturnArray);
            $Title = trim($ReturnArray[1][0]);
            $Content = $ReturnArray[2][0];
        }
        $Content = str_replace ( '（', '(', $Content );
        $Content = str_replace ( '）', ')', $Content );
        $Content = preg_replace ( "/<(\/?script.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?div.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?font.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?span.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?a.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?pre.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<script(.*)script>/isU", "", $Content );
        $Content = preg_replace ( "/<style(.*)style>/isU", "", $Content );
        $Content = preg_replace ( "/出国移民网/isU", "57美国网", $Content );
        $Content = preg_replace ( "/美国国家旅游局GoUSA/isU", "57美国网", $Content );
        $Content = preg_replace ( "/yimin.liuxue86.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/meiguo.liuxue86.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/www.liuxue86.com/isU", "www.57us.com", $Content );
        $Content = str_replace ( "\n\r", "<br>", $Content );
        $Content = str_replace ( "\r", "<br>", $Content );
        $Content = str_replace ( "\n", "<br>", $Content );
        $Content = str_replace ( '&ldquo;', '“', $Content );
        $Content = str_replace ( '&rdquo;', '”', $Content );
        $Content = str_replace ( "'", "’", $Content );
        $Content = preg_replace ( "/<(\/?table.*?)>/si","", $Content );
        $Data = array();
        $Data['Title'] = addslashes($Title);
        $Data['Content'] =  $Content;
        $Data['Description'] = trim(strip_tags(_substr($Content, 180)));
        $Data['SeoKeywords']='';
        $Data['SeoTitle']='';
        $Data['SeoDescription']='';
        $Data['Keywords']='';
        $Data['AddTime']=date('Y-m-d H:i:s');
        $Data['UpdateTime']=date('Y-m-d H:i:s');
        preg_match_all('/<img.*src="(.*)".*>/isU',$Data['Content'],$ImageArr);
        if(count($ImageArr[1])){
            $NewImgArr=array();
            foreach($ImageArr[1] as $key=>$ImgUrl){
                $NewImgArr[$key]='/up/'.date('Y').'/'.date('md').'/test/'.date('YmdHis').mt_rand(1000,9999).'.jpg';
                $NewImgTagArr[$key]="<img src=\"{$NewImgArr[$key]}\">";
                //上传到图片服务器
                SendToImgServ($NewImgArr[$key],base64_encode(file_get_contents('http://www.liuxue86.com'.$ImgUrl)));
            }
        }
        $Data['Content']=str_replace(array_reverse($ImageArr[0]),array_reverse($NewImgTagArr),$Data['Content']);
        $Data['Content'] = addslashes($Data['Content']);
        $Data['FromUrl']=$Url;//采集源URL
        $Data['ArticleType']=$this->CaijiColumnUrlInfo['ArticleType'];//采集类型移民
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];//采集移民类别
        if ($NewImgArr[0]!=''){
            $Data['Image']=$NewImgArr[0];//封面图片
        }else{
            $Data['Image']='';
        }
        return $Data;
    }

    //过滤内容
    public function DoFilterInfo($Content = '')
    {
        if ($Content == '')
            return 0;
        $Content = str_replace ( '（', '(', $Content );
        $Content = str_replace ( '）', ')', $Content );
        $Content = preg_replace ( "/<(\/?script.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?div.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?font.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?span.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?a.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?b.*?)>/si", "", $Content );
        $Content = preg_replace ("/<b>.*<\/b>/isU","", $Content );
        $Content = preg_replace ( "/<(\/?pre.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<script(.*)script>/isU", "", $Content );
        $Content = preg_replace ( "/<style(.*)style>/isU", "", $Content );
        $Content = str_replace ( "\n\r", "", $Content );
        $Content = str_replace ( "\r", "", $Content );
        $Content = str_replace ( "\n", "", $Content );
        $Content = str_replace ( '&ldquo;', '“', $Content );
        $Content = str_replace ( '&rdquo;', '”', $Content );
        $Content = str_replace ( "'", "’", $Content );
        $Content = str_replace('<!-- article main [E] -->','',$Content);
        $Content = str_replace('<!-- article main [S] -->','',$Content);
        $Content = str_replace('/<!--.*-->/isU','',$Content);
        return $Content;
    }
    function GetHtml($Url='') {
        $GetHtmlArray = stream_context_create ( array ('http' => array ('method' => 'GET', 'header' => "www.weather.com.cn\r\n" . "Referer:http://www.weather.com.cn/html/weather/101020100.shtml\r\n" . "User-Agent:Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.2; Trident/4.0; .NET CLR 1.1.4322)\r\n" . "Cache-Control:no-cache\r\n" ) ) );
        $Html = file_get_contents ( $Url, null, $GetHtmlArray );
        return $Html;
    }
}