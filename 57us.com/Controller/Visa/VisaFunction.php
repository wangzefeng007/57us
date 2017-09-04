<?php
function VisaSearchArray($A = '') {
    $Array ['c'] = array ('c01', 'c02', 'c03', 'c04', 'c05', 'c06', 'c07', 'c08', 'c09', 'c10', 'c11', 'c12', 'c13', 'c14', 'c15', 'c16', 'c17', 'c18', 'c19', 'c20', 'c21', 'c22', 'c23', 'c24', 'c25', 'c26', 'c27', 'c28', 'c29', 'c30' );
    $Array ['t'] = array ('t01', 't02', 't03' );

    if ($A == '')
        return $Array;
    else
        return $Array [$A];
}
function SearchNameArray($A = '') {
    $Array = array ('c01' => '北京', 'c02' => '天津', 'c03' => '河北', 'c04' => '山西', 'c05' => '内蒙古', 'c06' => '江西', 'c07' => '山东', 'c08' => '河南', 'c09' => '陕西', 'c10' => '甘肃', 'c11' => '青海', 'c12' => '宁夏', 'c13' => '新疆', 'c14' => '上海', 'c15' => '江苏', 'c16' => '浙江', 'c17' => '福建', 'c18' => '湖北', 'c19' => '湖南', 'c20' => '广东', 'c21' => '广西', 'c22' => '海南', 'c23' => '重庆', 'c24' => '四川', 'c25' => '贵州', 'c26' => '云南', 'c27' => '西藏', 'c28' => '辽宁', 'c29' => '吉林', 'c30' => '黑龙江', 't01' => '个人旅游签证', 't02' => '探亲访友签证', 't03' => '商务签证' );
    if ($A == '')
        return $Array;
    else
        return $Array [$A];
}
function SearchTypeArray($Tepe = '') {
    $Array  = array ('t01'=>'个人旅游签证', 't02'=>'探亲访友签证', 't03'=>'商务签证' );
    if ($Tepe == '')
        return $Array;
    else
        return $Array [$Tepe];
}

function GetSEOInfo($SoUrl = '') {
    $SearchNameArray = SearchNameArray ();
    $SEOArray = array ();
    foreach ( $SearchNameArray as $Key => $Value ) {
        if (strstr ( $SoUrl, $Key )) {
            $SEOArray [substr ( $Key, 0, 1 )] = $Value;
        }
    }
    if ($SoUrl == '') {
        //没有选择条件
        $SEOArray ['Title'] = '办理美国签证 - 57美国网';
        $SEOArray ['Keywords'] = '办理美国签证';
        $SEOArray ['Description'] = '57美国网签证频道，为您提供签证办理服务，多年美国签证办理经验，流程清晰，服务省高效、过签率高，是您美国签证代办服务之首选。';
    } elseif (strstr ( $SoUrl, 'c' ) && ! strstr ( $SoUrl, 't' )) {
        //只有地址
        $SEOArray ['Title'] = '办理美国签证_在' . $SEOArray ['c'] . '办理美国签证 - 57美国网';
        $SEOArray ['Keywords'] = '办理美国签证,在' . $SEOArray ['c'] . '办理美国签证';
        $SEOArray ['Description'] = '57美国网签证频道，为您提供' . $SEOArray ['c'] . '美国的签证办理服务，多年美国签证办理经验，流程清晰，服务省高效、过签率高，是您美国签证代办服务之首选。';
    } elseif (strstr ( $SoUrl, 't' ) && ! strstr ( $SoUrl, 'c' )) {
        //只有类型
        $SEOArray ['Title'] = '办理美国签证_美国' . $SEOArray ['t'] . ' - 57美国网';
        $SEOArray ['Keywords'] = '办理美国签证,美国' . $SEOArray ['t'];
        $SEOArray ['Description'] = '57美国网签证频道，为您提供' . $SEOArray ['t'] . '的签证办理服务，多年美国签证办理经验，流程清晰，服务省高效、过签率高，是您美国签证代办服务之首选。';
    } else {
        $SEOArray ['Title'] = '办理美国签证_在' . $SEOArray ['c'] . '办理美国签证_美国' . $SEOArray ['t'] . ' - 57美国网';
        $SEOArray ['Keywords'] = '办理美国签证,在' . $SEOArray ['c'] . '办理美国签证,美国' . $SEOArray ['t'];
        $SEOArray ['Description'] = '57美国网签证频道，为您提供' . $SEOArray ['c'] . '美国' . $SEOArray ['t'] . '的签证办理服务，多年美国签证办理经验，流程清晰，服务省高效、过签率高，是您美国签证代办服务之首选。';
    }
    return $SEOArray;
}

function GetVisaUrl($SoUrl = '') {
    if ($SoUrl == '')
        return '';
    $AllSearchArray = VisaSearchArray ();
    $NewAllSearchArray = array ();
    $NewSoUrl = '';
    foreach ( $AllSearchArray as $Key => $Value ) {
        $NewAllSearchArray = array_merge ( $NewAllSearchArray, $Value );
    }
    foreach ( $NewAllSearchArray as $K => $Val ) {
        if (strstr ( $SoUrl, $Val )) {
            $NewSoUrl .= $Val;
        }
    }
    return $NewSoUrl;
}

function GetMysqlWhere($SoUrl = '') {
    if ($SoUrl == '')
        return '';
    $MysqlWhere = '';
    if (strstr ( $SoUrl, 't01' ))
        $MysqlWhere .= ' and Type=\'t01\'';
    if (strstr ( $SoUrl, 't02' ))
        $MysqlWhere .= ' and Type=\'t02\'';
    if (strstr ( $SoUrl, 't03' ))
        $MysqlWhere .= ' and Type=\'t03\'';

    if (strstr ( $SoUrl, 'c01' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c02' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c03' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c04' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c05' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c06' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c07' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c08' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c09' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c10' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c11' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c12' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c13' ))
        $MysqlWhere .= ' and Area=\'c01\'';
    if (strstr ( $SoUrl, 'c14' ))
        $MysqlWhere .= ' and Area=\'c14\'';
    if (strstr ( $SoUrl, 'c15' ))
        $MysqlWhere .= ' and Area=\'c14\'';
    if (strstr ( $SoUrl, 'c16' ))
        $MysqlWhere .= ' and Area=\'c14\'';
    if (strstr ( $SoUrl, 'c17' ))
        $MysqlWhere .= ' and Area=\'c20\'';
    if (strstr ( $SoUrl, 'c18' ))
        $MysqlWhere .= ' and Area=\'c20\'';
    if (strstr ( $SoUrl, 'c19' ))
        $MysqlWhere .= ' and Area=\'c20\'';
    if (strstr ( $SoUrl, 'c20' ))
        $MysqlWhere .= ' and Area=\'c20\'';
    if (strstr ( $SoUrl, 'c21' ))
        $MysqlWhere .= ' and Area=\'c20\'';
    if (strstr ( $SoUrl, 'c22' ))
        $MysqlWhere .= ' and Area=\'c20\'';
    if (strstr ( $SoUrl, 'c23' ))
        $MysqlWhere .= ' and Area=\'c24\'';
    if (strstr ( $SoUrl, 'c24' ))
        $MysqlWhere .= ' and Area=\'c24\'';
    if (strstr ( $SoUrl, 'c25' ))
        $MysqlWhere .= ' and Area=\'c24\'';
    if (strstr ( $SoUrl, 'c26' ))
        $MysqlWhere .= ' and Area=\'c24\'';
    if (strstr ( $SoUrl, 'c27' ))
        $MysqlWhere .= ' and Area=\'c24\'';
    if (strstr ( $SoUrl, 'c28' ))
        $MysqlWhere .= ' and Area=\'c28\'';
    if (strstr ( $SoUrl, 'c29' ))
        $MysqlWhere .= ' and Area=\'c28\'';
    if (strstr ( $SoUrl, 'c30' ))
        $MysqlWhere .= ' and Area=\'c28\'';
    return $MysqlWhere;
}
function VisaSearch($SoUrl = '', $S = '', $T = '') {
    if ($T == 'c') {
        $ActionArray = VisaSearchArray ( 'c' );
    } elseif ($T == 't') {
        $ActionArray = VisaSearchArray ( 't' );
    }

    $SoUrl = str_replace ( $ActionArray, '', $SoUrl );
    $SoUrl = $SoUrl . $S;
    $SoUrl = GetVisaUrl ( $SoUrl );
    if ($SoUrl == '') {
        $SoUrl = WEB_VISA_URL . '/visalists/';
    } else {
        $SoUrl = WEB_VISA_URL . '/visalists/' . $SoUrl . '/';
    }
    return $SoUrl;
}
function GetIsOn($SoUrl = '', $S = '', $T = '') {
    if ($S == '') {
        if (! strstr ( $SoUrl, $T )) {
            return 'class="on"';
        }
    } else {
        if (strstr ( $SoUrl, $S ))
            return 'class="on"';
        else
            return '';
    }
}
//选中内容	暂时不用
function HasSelectedInfo($SoUrl = '') {
    $ActionArrayAll = SearchNameArray ();
    $I = 0;
    foreach ( $ActionArrayAll as $Key => $Value ) {
        if (strstr ( $SoUrl, $Key )) {
            $NewSoUrl = str_replace ( $Key, '', $SoUrl );
            if ($NewSoUrl == '') {
                $NewSoUrl = WEB_VISA_URL . '/visalists/';
            } else {
                $NewSoUrl = WEB_VISA_URL . '/visalists/' . $NewSoUrl . '/';
            }
            $SelectedArray [$I] ['SoUrl'] = $NewSoUrl;
            $SelectedArray [$I] ['Key'] = $Key;
            $SelectedArray [$I] ['Value'] = $Value;
            $I ++;
        }
    }
    return $SelectedArray;
}

//选中内容
function SelectedAll($SoUrl = '', $T = '') {
    if (strstr ( $SoUrl, $T )) {
        $SoUrl = str_replace ( VisaSearchArray ( $T ), '', $SoUrl );
    }
    if ($SoUrl == '') {
        $SoUrl = WEB_VISA_URL . '/visalists/';
    } else {
        $SoUrl = WEB_VISA_URL . '/visalists/' . $SoUrl . '/';
    }
    return $SoUrl;
}

