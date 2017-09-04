<?php

class CaiJiStudy
{
    public function __construct()
    {
        global $Module;
        global $Action;
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiColumnUrlInfo = $CaijiColumnUrlModule->GetInfoByWhere(' and MyModule=\''.$Module.'\' and MyAction=\''.$Action.'\'');
        //$CaijiColumnUrlInfo['Html'] = file_get_contents($CaijiColumnUrlInfo['Url']);
        $this->CaijiColumnUrlInfo = $CaijiColumnUrlInfo;
    }
    /*
     * 采集数据
     * 出国留学网-奖学金
     * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86JiangXueJin
     * */
    public function LiuXue86JiangXueJin(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
     * 出国留学网-留学费用
     * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86FeiYong
     * */
    public function LiuXue86FeiYong(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
     * 出国留学网-海外打工
     * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86LiuXueJiuYe
     * */
    public function LiuXue86LiuXueJiuYe(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
     * 出国留学网-留学达人
     * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86LiuXueJingYan
     * */
    public function LiuXue86LiuXueJingYan(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-旅游
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86YiShiZhuXing
    * */
    public function LiuXue86YiShiZhuXing(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-雅思作文
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86Yszw
    * */
    public function LiuXue86Yszw(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-行程准备
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86XingQianZhunBei
    * */
    public function LiuXue86XingQianZhunBei(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-读研究生
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86YanJiuShengLiuXue
    * */
    public function LiuXue86YanJiuShengLiuXue(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        $Url = $this->CaijiColumnUrlInfo['Url'];
        if ($P<=$Page) {
            if($P != 1){
                $Url = $this->CaijiColumnUrlInfo['Url'].$P.'.html';
            }
            $CaijiColumnUrlInfo['Html'] = file_get_contents($Url);
            //匹配文章列表地址
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-读本科
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86BenKeLiuXue
    * */
    public function LiuXue86BenKeLiuXue(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        $Url = $this->CaijiColumnUrlInfo['Url'];
        if ($P<=$Page) {
            if($P != 1){
                $Url = $this->CaijiColumnUrlInfo['Url'].$P.'.html';
            }
            $CaijiColumnUrlInfo['Html'] = file_get_contents($Url);
            //匹配文章列表地址
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-读高中
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86GaoZhongLiuXue
    * */
    public function LiuXue86GaoZhongLiuXue(){
        $CaijiColumnUrlInfo = $this->CaijiColumnUrlInfo;
        $TblImmigrationModule = new TblImmigrationModule();
        $CaijiColumnUrlModule = new CaijiColumnUrlModule();
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiUrlAllModule = new CaijiUrlAllModule();
        $Page = $CaijiColumnUrlInfo['Page'];
        $P = $_GET['P']?$_GET['P']:1;
        $Url = $this->CaijiColumnUrlInfo['Url'];
        if ($P<=$Page) {
            if($P != 1){
                $Url = $this->CaijiColumnUrlInfo['Url'].$P.'.html';
            }
            $CaijiColumnUrlInfo['Html'] = file_get_contents($Url);
            //匹配文章列表地址
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-签证技巧
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86JiQiao
    * */
    public function LiuXue86JiQiao(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-签证办理
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86BanLiLiuCheng
    * */
    public function LiuXue86BanLiLiuCheng(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
    * 出国留学网-签证政策
    * http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86QianZhengZiXun
    * */
    public function LiuXue86QianZhengZiXun(){
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
            $ListZZ1= '/<ul class="txt">(.*)<\/ul>/isU';
            $ListZZ = '/<a href="(.*)"  title=".*" target="_blank">.*<\/a>/isU';
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
     *http://admin.57us.com/index.php?Module=CaiJiStudy&Action=LiuXue86
     * 出国留学网美国留学
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
            }elseif (strstr($Html, '<div class="bottom_main2 clearfix">')) {
                $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"zhengw_ccc\">(.*)<div class="bottom_main2 clearfix">/isU';
                preg_match_all($ListZZ, $Html, $ReturnArray);
                $Title = trim($ReturnArray[1][0]);
                $Content = $ReturnArray[2][0];
            }
            //相同class="zhengw_ccc" 头部 尾部不一样<div class=" bottom_main_2">
        }elseif(strstr($Html, '<div class="zhengw_ccc">')){
            if (strstr($Html, '<div class="bottom_main_2">')){
                $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"zhengw_ccc\">(.*)<div class="bottom_main_2">/isU';
            }else{
                $ListZZ = '/<h1>(.*)<\/h1>.*<div class=\"zhengw_ccc\">(.*)<div class="zhengw_ccc">/isU';
            }
            preg_match_all($ListZZ, $Html, $ReturnArray);
            $Title = trim($ReturnArray[1][0]);
            $Content = $ReturnArray[2][0];
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
        $Content = preg_replace ( "/<(\/?script.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?div.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?font.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?span.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?a.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<(\/?pre.*?)>/si", "", $Content );
        $Content = preg_replace ( "/<script(.*)script>/isU", "", $Content );
        $Content = preg_replace ( "/<style(.*)style>/isU", "", $Content );
        $Content = preg_replace ( "/86留学网/isU", "57美国网", $Content );
        $Content = preg_replace ( "/出国留学网/isU", "57美国网", $Content );
        $Content = preg_replace ( "/yimin.liuxue86.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/meiguo.liuxue86.com/isU", "www.57us.com", $Content );
        $Content = preg_replace ( "/www.liuxue86.com/isU", "www.57us.com", $Content );
        $Content = str_replace ( '（', '(', $Content );
        $Content = str_replace ( '）', ')', $Content );
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
        $Data['ArticleType']=$this->CaijiColumnUrlInfo['ArticleType'];//采集类型留学
        $Data['CategoryID']=$this->CaijiColumnUrlInfo['CategoryID'];//采集移民类别
        if ($NewImgArr[0]!=''){
            $Data['Image']=$NewImgArr[0];//封面图片
        }else{
            $Data['Image']='';
        }
        return $Data;
    }
    /*
     * 澳际教育-留学费用
     * http://us.aoji.cn/feiyong/
     * */
    public function AoJiFeiYong()
    {
        global $Module;
        global $Action;
        $AllPage = $this->CaijiColumnUrlInfo['Page'];
        $Page = $_GET['P']?$_GET['P']:1;
        $Url = $this->CaijiColumnUrlInfo['Url'];
        if($Page < $AllPage || $Page == $AllPage){
            if($Page != 1){
                $Url = $this->CaijiColumnUrlInfo['Url'].$Page.'.html';
            }
            $ListContent = file_get_contents($Url);
            $ListZZ1 = '/<div class="tab-con">(.*)<div id="pages" class="pager">/isU';
            preg_match_all($ListZZ1,$ListContent, $ListReturn);
            $ListZZ2 = '/<span class="sort">(.*)<a href="(.*)" target="_blank">(.*)<\/a>/isU';
            preg_match_all($ListZZ2,$ListReturn[1][0], $ListReturn2);
            foreach($ListReturn2[2] as $key=>$val){
                $CaijiAllUrlModule = new CaijiUrlAllModule();
                $SelectResult = $CaijiAllUrlModule->GetInfoByWhere(" and Url = '{$val}'");
                if($SelectResult){
                    continue;
                }
                $DetailContent = file_get_contents($val);
                $DetailZZ1 = '/id="aoji-article-main" class="aoji-article-main">(.*)<div class="pager"/isU';
                preg_match_all($DetailZZ1,$DetailContent, $DetailReturn);
                //去除A标签
                $str1 = preg_replace("/<a[^>]*>/","", $DetailReturn[1][0]);
                $Result = preg_replace("/<\/a>/","", $str1);

                $Result = str_replace('<img alt=\"\" src=\"http:\/\/img.aoji.cn\/2013\/0423\/3v9dEvFiuwvt.jpg\">', '', $Result);
                $Result = str_replace('澳际', '57us', $Result);
                $Result = str_replace('http://us.aoji.cn/', 'http://www.57us.com/', $Result);
                $Result = str_replace('400-601-0022', '400-018-5757', $Result);
                $Result = str_replace('010-65229780', '400-018-5757', $Result);
                $Result = str_replace('<!-- article main [E] -->','',$Result);
                $Result = str_replace('<!-- article main [S] -->','',$Result);


                $ImageNames = $this->PostImg($DetailReturn[1][0]);
                if($ImageNames[0]){
                    $Data['Image'] = $ImageNames[0];
                }
                else{
                    $Data['Image'] = '';
                }
                $Data['Title'] = $ListReturn2[3][$key];

                $Pattern = array();
                $Replacement = array();
                $ImgArr = Array();
                preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($DetailReturn[1][0]), $ImgArr);
                if (count($ImgArr[1])) {
                    foreach ($ImgArr[1] as $Key => $ImgTag) {
                        $Pattern[] = $ImgTag;
                        $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array('/title=".*"/iU', '/alt=".*"/iU'), '', $ImgTag));
                    }
                }
                $Data['Content'] = addslashes(str_replace(array_reverse($Replacement), array_reverse($ImageNames), stripcslashes($Result)));
                $Data['ArticleType'] =  1;//留学
                $Data['FromUrl'] = $val;
                $Data['CategoryID'] = $this->CaijiColumnUrlInfo['CategoryID'];
                $Data['AddTime'] = date("Y-m-d H:i:s",time());
                $Data['UpdateTime'] = date("Y-m-d H:i:s",time());
                $Data['IsHaveContent'] = $Data['Content']?1:2;
                $this->InsertData($Data);

                $EData['GetTime'] = $Data['AddTime'];
                $EData['Url'] = $val;
                $this->InsertUrlData($EData);
                unset($Data);
                unset($EData);
            }
            $Page++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$Page}\"</script>";
        }
        else{
            echo "采集".$Url.'结束';
            exit;
        }
    }

    /*
     * 澳际教育-[行前准备]
     * http://us.aoji.cn/xingqian/
     * */
    public function AoJiXingQian()
    {
        global $Module;
        global $Action;
        $AllPage = $this->CaijiColumnUrlInfo['Page'];
        $Page = $_GET['P']?$_GET['P']:1;
        $Url = $this->CaijiColumnUrlInfo['Url'];
        if($Page < $AllPage || $Page == $AllPage){
            if($Page != 1){
                $Url = $this->CaijiColumnUrlInfo['Url'].$Page.'.html';
            }
            $ListContent = file_get_contents($Url);
            $ListZZ1 = '/<div class="tab-con">(.*)<div id="pages" class="pager">/isU';
            preg_match_all($ListZZ1,$ListContent, $ListReturn);
            $ListZZ2 = '/<span class="sort">(.*)<a href="(.*)" target="_blank">(.*)<\/a>/isU';
            preg_match_all($ListZZ2,$ListReturn[1][0], $ListReturn2);
            foreach($ListReturn2[2] as $key=>$val){
                $CaijiAllUrlModule = new CaijiUrlAllModule();
                $SelectResult = $CaijiAllUrlModule->GetInfoByWhere(" and Url = '{$val}'");
                if($SelectResult){
                    continue;
                }
                $DetailContent = file_get_contents($val);
                $DetailZZ1 = '/id="aoji-article-main" class="aoji-article-main">(.*)<div class="pager"/isU';
                preg_match_all($DetailZZ1,$DetailContent, $DetailReturn);
                //去除A标签
                $str1 = preg_replace("/<a[^>]*>/","", $DetailReturn[1][0]);
                $Result = preg_replace("/<\/a>/","", $str1);

                $Result = str_replace('<img alt=\"\" src=\"http:\/\/img.aoji.cn\/2013\/0423\/3v9dEvFiuwvt.jpg\">', '', $Result);
                $Result = str_replace('澳际', '57us', $Result);
                $Result = str_replace('http://us.aoji.cn/', 'http://www.57us.com/', $Result);
                $Result = str_replace('400-601-0022', '400-018-5757', $Result);
                $Result = str_replace('010-65229780', '400-018-5757', $Result);
                $Result = str_replace('<!-- article main [E] -->','',$Result);
                $Result = str_replace('<!-- article main [S] -->','',$Result);

                $ImageNames = $this->PostImg($DetailReturn[1][0]);
                if($ImageNames[0]){
                    $Data['Image'] = $ImageNames[0];
                }
                else{
                    $Data['Image'] = '';
                }
                $Data['Title'] = $ListReturn2[3][$key];

                $Pattern = array();
                $Replacement = array();
                $ImgArr = Array();
                preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($DetailReturn[1][0]), $ImgArr);
                if (count($ImgArr[1])) {
                    foreach ($ImgArr[1] as $Key => $ImgTag) {
                        $Pattern[] = $ImgTag;
                        $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array('/title=".*"/iU', '/alt=".*"/iU'), '', $ImgTag));
                    }
                }
                $Data['Content'] = addslashes(str_replace(array_reverse($Replacement), array_reverse($ImageNames), stripcslashes($Result)));
                $Data['ArticleType'] =  1;//留学
                $Data['FromUrl'] = $val;
                $Data['CategoryID'] = $this->CaijiColumnUrlInfo['CategoryID'];
                $Data['AddTime'] = date("Y-m-d H:i:s",time());
                $Data['UpdateTime'] = date("Y-m-d H:i:s",time());
                $Data['IsHaveContent'] = $Data['Content']?1:2;
                $this->InsertData($Data);
                $EData['GetTime'] = $Data['AddTime'];
                $EData['Url'] = $val;
                $this->InsertUrlData($EData);
                unset($Data);
                unset($EData);
            }
            $Page++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$Page}\"</script>";
        }
        else{
            echo "采集".$Url.'结束';
            exit;
        }
    }

    /*
     * 澳际教育-[学校申请]
     * http://us.aoji.cn/shenqing/
     * */
    public function AoJiShenQing()
    {
        global $Module;
        global $Action;
        $AllPage = $this->CaijiColumnUrlInfo['Page'];
        $Page = $_GET['P']?$_GET['P']:1;
        $Url = $this->CaijiColumnUrlInfo['Url'];
        if($Page < $AllPage || $Page == $AllPage){
            if($Page != 1){
                $Url = $this->CaijiColumnUrlInfo['Url'].$Page.'.html';
            }
            $ListContent = file_get_contents($Url);
            $ListZZ1 = '/<div class="tab-con">(.*)<div id="pages" class="pager">/isU';
            preg_match_all($ListZZ1,$ListContent, $ListReturn);
            $ListZZ2 = '/<span class="sort">(.*)<a href="(.*)" target="_blank">(.*)<\/a>/isU';
            preg_match_all($ListZZ2,$ListReturn[1][0], $ListReturn2);
            foreach($ListReturn2[2] as $key=>$val){
                $CaijiAllUrlModule = new CaijiUrlAllModule();
                $SelectResult = $CaijiAllUrlModule->GetInfoByWhere(" and Url = '{$val}'");
                if($SelectResult){
                    continue;
                }
                $DetailContent = file_get_contents($val);
                $DetailZZ1 = '/id="aoji-article-main" class="aoji-article-main">(.*)<div class="pager"/isU';
                preg_match_all($DetailZZ1,$DetailContent, $DetailReturn);
                //去除A标签
                $str1 = preg_replace("/<a[^>]*>/","", $DetailReturn[1][0]);
                $Result = preg_replace("/<\/a>/","", $str1);

                $Result = str_replace('<img alt=\"\" src=\"http:\/\/img.aoji.cn\/2013\/0423\/3v9dEvFiuwvt.jpg\">', '', $Result);
                $Result = str_replace('澳际', '57us', $Result);
                $Result = str_replace('http://us.aoji.cn/', 'http://www.57us.com/', $Result);
                $Result = str_replace('http://www.aoji.cn/', 'http://www.57us.com/', $Result);


                $Result = str_replace('400-601-0022', '400-018-5757', $Result);
                $Result = str_replace('010-65229780', '400-018-5757', $Result);
                $Result = str_replace('<!-- article main [E] -->','',$Result);
                $Result = str_replace('<!-- article main [S] -->','',$Result);
                $ImageNames = $this->PostImg($DetailReturn[1][0]);
                if($ImageNames[0]){
                    $Data['Image'] = $ImageNames[0];
                }
                else{
                    $Data['Image'] = '';
                }
                $Data['Title'] = $ListReturn2[3][$key];

                $Pattern = array();
                $Replacement = array();
                $ImgArr = Array();
                preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($DetailReturn[1][0]), $ImgArr);
                if (count($ImgArr[1])) {
                    foreach ($ImgArr[1] as $Key => $ImgTag) {
                        $Pattern[] = $ImgTag;
                        $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array('/title=".*"/iU', '/alt=".*"/iU'), '', $ImgTag));
                    }
                }
                $Data['Content'] = addslashes(str_replace(array_reverse($Replacement), array_reverse($ImageNames), stripcslashes($Result)));
                $Data['ArticleType'] =  1;//留学
                $Data['FromUrl'] = $val;
                $Data['CategoryID'] = $this->CaijiColumnUrlInfo['CategoryID'];
                $Data['AddTime'] = date("Y-m-d H:i:s",time());
                $Data['UpdateTime'] = date("Y-m-d H:i:s",time());
                $Data['IsHaveContent'] = $Data['Content']?1:2;
                $this->InsertData($Data);

                $EData['GetTime'] = $Data['AddTime'];
                $EData['Url'] = $val;
                $this->InsertUrlData($EData);
                unset($Data);
                unset($EData);
            }
            $Page++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$Page}\"</script>";
        }
        else{
            echo "采集".$Url.'结束';
            exit;
        }
    }

    /*
     * 澳际教育-[专业推荐]
     * http://us.aoji.cn/zhuanyetuijian/
     * */
    public function AoJiZhuanYeTuiJian()
    {
        global $Module;
        global $Action;
        $AllPage = $this->CaijiColumnUrlInfo['Page'];
        $Page = $_GET['P']?$_GET['P']:1;
        $Url = $this->CaijiColumnUrlInfo['Url'];
        if($Page < $AllPage || $Page == $AllPage){
            if($Page != 1){
                $Url = $this->CaijiColumnUrlInfo['Url'].$Page.'.html';
            }
            $ListContent = file_get_contents($Url);
            $ListZZ1 = '/<div class="tab-con">(.*)<div id="pages" class="pager">/isU';
            preg_match_all($ListZZ1,$ListContent, $ListReturn);
            $ListZZ2 = '/<span class="sort">(.*)<a href="(.*)" target="_blank">(.*)<\/a>/isU';
            preg_match_all($ListZZ2,$ListReturn[1][0], $ListReturn2);
            foreach($ListReturn2[2] as $key=>$val){
                $CaijiAllUrlModule = new CaijiUrlAllModule();
                $SelectResult = $CaijiAllUrlModule->GetInfoByWhere(" and Url = '{$val}'");
                if($SelectResult){
                    continue;
                }
                $DetailContent = file_get_contents($val);
                $DetailZZ1 = '/id="aoji-article-main" class="aoji-article-main">(.*)<div class="pager"/isU';
                preg_match_all($DetailZZ1,$DetailContent, $DetailReturn);
                //去除A标签
                $str1 = preg_replace("/<a[^>]*>/","", $DetailReturn[1][0]);
                $Result = preg_replace("/<\/a>/","", $str1);

                $Result = str_replace('<img alt=\"\" src=\"http:\/\/img.aoji.cn\/2013\/0423\/3v9dEvFiuwvt.jpg\">', '', $Result);
                $Result = str_replace('澳际', '57us', $Result);
                $Result = str_replace('http://us.aoji.cn/', 'http://www.57us.com/', $Result);
                $Result = str_replace('http://www.aoji.cn/', 'http://www.57us.com/', $Result);


                $Result = str_replace('400-601-0022', '400-018-5757', $Result);
                $Result = str_replace('010-65229780', '400-018-5757', $Result);
                $Result = str_replace('<!-- article main [E] -->','',$Result);
                $Result = str_replace('<!-- article main [S] -->','',$Result);


                $ImageNames = $this->PostImg($DetailReturn[1][0]);
                if($ImageNames[0]){
                    $Data['Image'] = $ImageNames[0];
                }
                else{
                    $Data['Image'] = '';
                }
                $Data['Title'] = $ListReturn2[3][$key];

                $Pattern = array();
                $Replacement = array();
                $ImgArr = Array();
                preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($DetailReturn[1][0]), $ImgArr);
                if (count($ImgArr[1])) {
                    foreach ($ImgArr[1] as $Key => $ImgTag) {
                        $Pattern[] = $ImgTag;
                        $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array('/title=".*"/iU', '/alt=".*"/iU'), '', $ImgTag));
                    }
                }
                $Data['Content'] = addslashes(str_replace(array_reverse($Replacement), array_reverse($ImageNames), stripcslashes($Result)));
                $Data['ArticleType'] =  1;//留学
                $Data['FromUrl'] = $val;
                $Data['CategoryID'] = $this->CaijiColumnUrlInfo['CategoryID'];
                $Data['AddTime'] = date("Y-m-d H:i:s",time());
                $Data['UpdateTime'] = date("Y-m-d H:i:s",time());
                $Data['IsHaveContent'] = $Data['Content']?1:2;
                $this->InsertData($Data);

                $EData['GetTime'] = $Data['AddTime'];
                $EData['Url'] = $val;
                $this->InsertUrlData($EData);
                unset($Data);
                unset($EData);
            }
            $Page++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$Page}\"</script>";
        }
        else{
            echo "采集".$Url.'结束';
            exit;
        }
    }

    /*
     * 澳际教育-[常见问题]
     * http://www.aojiyouxue.com/zixun/wenti/
     * */
    public function AoJiChangJianWenTi()
    {
        global $Module;
        global $Action;
        $AllPage = $this->CaijiColumnUrlInfo['Page'];
        $Page = $_GET['P']?$_GET['P']:1;
        $Url = $this->CaijiColumnUrlInfo['Url'];
        if($Page < $AllPage || $Page == $AllPage){
            if($Page != 1){
                $Url = $this->CaijiColumnUrlInfo['Url'].$Page.'.html';
            }
            $ListContent = file_get_contents($Url);
            $ListZZ1 = '/<div class="diaryListCont">(.*)<div class="page">/isU';
            preg_match_all($ListZZ1,$ListContent, $ListReturn);
            $ListZZ2 = '/<a href="(.*)" target="_blank">(.*)<\/a>/isU';
            preg_match_all($ListZZ2,$ListReturn[1][0], $ListReturn2);
            //echo "<pre>";print_r($ListReturn2);exit;

            foreach($ListReturn2[1] as $key=>$val){
                $CaijiAllUrlModule = new CaijiUrlAllModule();
                $SelectResult = $CaijiAllUrlModule->GetInfoByWhere(" and Url = '{$val}'");
                if($SelectResult){
                    continue;
                }
                $DetailContent = file_get_contents($val);
                $DetailZZ1 = '/<div class="newsTxt">(.*)<div class="pager" id/isU';
                preg_match_all($DetailZZ1,$DetailContent, $DetailReturn);

                //去除A标签
                $str1 = preg_replace("/<a[^>]*>/","", $DetailReturn[1][0]);
                $Result = preg_replace("/<\/a>/","", $str1);
                $Result = preg_replace("/<\/div>/","", $Result);

                //echo "<pre>";print_r($Result);exit;
                //$Result = str_replace('<img alt=\"\" src=\"http:\/\/img.aoji.cn\/2013\/0423\/3v9dEvFiuwvt.jpg\">', '', $Result);
                $Result = str_replace('澳际', '57us', $Result);
                $Result = str_replace('http://us.aoji.cn/', 'http://www.57us.com/', $Result);
                $Result = str_replace('http://www.aoji.cn/', 'http://www.57us.com/', $Result);
                $Result = str_replace('400-601-0022', '400-018-5757', $Result);
                $Result = str_replace('010-65229780', '400-018-5757', $Result);
                $Result = str_replace('<!-- article main [E] -->','',$Result);
                $Result = str_replace('<!-- article main [S] -->','',$Result);

                $ImageNames = $this->PostImg($DetailReturn[1][0]);
                if($ImageNames[0]){
                    $Data['Image'] = $ImageNames[0];
                }
                else{
                    $Data['Image'] = '';
                }
                $Data['Title'] = $ListReturn2[2][$key];

                $Pattern = array();
                $Replacement = array();
                $ImgArr = Array();
                preg_match_all('/<img.*src="(.*)".*>/iU', stripcslashes($DetailReturn[1][0]), $ImgArr);
                if (count($ImgArr[1])) {
                    foreach ($ImgArr[1] as $Key => $ImgTag) {
                        $Pattern[] = $ImgTag;
                        $Replacement[] = preg_replace("/http:\/\/images\.57us\.com\/l/iU", "", preg_replace(array('/title=".*"/iU', '/alt=".*"/iU'), '', $ImgTag));
                    }
                }
                $Data['Content'] = addslashes(str_replace(array_reverse($Replacement), array_reverse($ImageNames), stripcslashes($Result)));
                $Data['ArticleType'] =  1;//留学
                $Data['FromUrl'] = $val;
                $Data['CategoryID'] = $this->CaijiColumnUrlInfo['CategoryID'];
                $Data['AddTime'] = date("Y-m-d H:i:s",time());
                $Data['UpdateTime'] = date("Y-m-d H:i:s",time());
                $Data['IsHaveContent'] = $Data['Content']?1:2;
                $this->InsertData($Data);

                $EData['GetTime'] = $Data['AddTime'];
                $EData['Url'] = $val;
                $this->InsertUrlData($EData);
                unset($Data);
                unset($EData);
            }
            $Page++;
            echo "<script>window.location=\"/index.php?Module={$Module}&Action={$Action}&ColumnID={$this->CaijiColumnUrlInfo['ColumnID']}&P={$Page}\"</script>";
        }
        else{
            echo "采集".$Url.'结束';
            exit;
        }
    }

    /**
     * @desc  图片上传
     * @param $Detail
     * @return mixed
     */
    private function PostImg($Detail){
        $Imgs = _GetPicToContent($Detail);
        if($Imgs){
            foreach($Imgs as $key=>$val){
                //$img_info = getimagesize($val);
                $ImgSrc = base64_encode(file_get_contents($val));
                $TitleName[$key] = '/up/'.date('Y').'/'.date('md').'/test/'.date("YmdHis").rand(1000,9999).'.jpg';
                SendToImgServ($TitleName[$key],$ImgSrc);
            }
            return $TitleName;
        }
        else{
            return '';
        }
    }

    /**
     * @desc  入库(caiji_article)
     */
    private function InsertData($Data){
        $CaijiArticleModule = new CaijiArticleModule();
        $CaijiArticleModule->InsertInfo($Data);
    }

    /**
     * @desc 入库 caiji_url_all
     * @param $Data
     */
    private function InsertUrlData($Data){
        $CaijiAllUrlModule = new CaijiUrlAllModule();
        $CaijiAllUrlModule->InsertInfo($Data);
    }

}