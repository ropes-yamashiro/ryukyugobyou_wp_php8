<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
/** ハッシュが等しいが調べる */
function eqhash($hash1,$hash2,$debug = false) {
    foreach((array)$hash1 as $key => $val) {
        if(!array_key_exists($key,$hash2)) {
            if($debug) pr($key,NON_EXIST);
            return false;
        }
        if($val != $hash2[$key]) {
            if($debug) pr($key .":" . $val,NOT_EQUAL);
            return false;
        }
    }
    return true;
}

function toarray($val) {
    if($val === null) return array();
    return is_array($val) ? $val : array($val);
}


/** $line内の$vhashのキーをvalueに置換えます
 $isvar=trueのとき場合は、{変数}として扱う */
function rephash($line,$vhash,$isvar = true) {
    foreach((array)$vhash as $key=>$value) {
        $line = $isvar ? rep($line,$key,$value) : str_replace($key,$value,$line);
    }
    return $line;
}

/** ハッシュをコピーします */
function copyhash($base) {
    $ret = array();
    foreach((array)$base as $key => $value) {
        $ret[$key] = $value;
    }
    return $ret;
}

/** targetにsourceを上書きします */
function whash($target,$source) {
    if($source == null)return $target;
    foreach((array)$source as $key=>$value) {
        $target[$key] = $value;
    }
    return $target;
}

/** リストからキーのみのハッシュを作製する　*/
function lhash($list) {
    foreach($list as $one) $ret[$one] = "";
    return $ret;
}

function ehash($vals) {
    foreach((array)$vals as $key => $value) {
        if($value == "") $vals[$key]  = "";
        else $vals[$key] = estr($value);
    }
    return $vals;
}
function chash($hashstr) {
    $cut = split(",",$hashstr);
    $hash;
    for($i = 0 ; $i < count($cut) ; $i++) {
        $hash[trim($cut[$i])] = "";
    }
    return $hash;
}
function phash($hash,$temp) {
    foreach((array)$hash as $key=> $value) {
        $temp = rep($temp,$key,$value);
    }
    return $temp;
}
function ephash($hash,$temp) {

    foreach((array)$hash as $key => $value) {
        //print "$key => $value <br>";
        if(!is_scalar($value)) $value = null;
        $temp = rep($temp,$key,estr($value));
    }
    return $temp;
}

function jhash($hlist,$hash) {
    foreach((array)$hlist as $key=> $value) {
        $ret[$key] = array_key_exists($key,$hash) ? $hash[$key] : "";
    }
    return $ret;
}
/** $vstr(要素をコンマで区切った文字列)をKeyにして、値を$tlistから順番に取り出したHashを作成する */
function jnhash($vstr,$tlist) {
    $ret = array();
    $list = clist($vstr);
    for($im = 0; $im < count($list) ; $im ++ ) {
        $ret[$list[$im]] = $tlist[$im];
    }
    return $ret;
}
/** Select Hash: $listの要素をキーにして、$hashの中から対応する値を取ったハッシュを作成する */
function shash($list,$hash) {
    foreach((array)$list as $one) {
        $ret[$one] = array_key_exists($one,$hash) ? $hash[$one] : "";
    }
    return $ret;
}
function clist($vstr) {
    return tlist(split(",",$vstr));
}
function ghash($name,$hash) {
    $ret = array();
    foreach((array)$hash as $key => $value) {
        if($key == $name) array_push($ret,$value);
    }
    return $ret;
}

/** $from～$toまでをキーと値に持つハッシュを作成します*/
function numhash($from,$to) {
    $ret = array();
    for($i = $from;$i <= $to;$i++) {
        $ret[$i] = $i;
    }
    return $ret;
}

/** リストの各要素にtrimをかける */
function tlist($list) {
    for($i = 0 ; $i < count($list) ; $i++) {
        $list[$i] = trim($list[$i]);
    }
    return $list;
}

/** リストの各要素にtrimをかける */
function twlist($list) {
    for($i = 0 ; $i < count($list) ; $i++) {
        $list[$i] = trim($list[$i],"\"'");
    }
    return $list;
}

/** ファイルをリストとして読み出す*/
function rlist($path) {
    $file = file($path);
    $ret = array();
    foreach($file as $line)array_push($ret,trim($line));
    return $ret;
}

/** $valueの値があれば変換なければ,null */
function ifrep($line,$key,$value) {
    return $value ? rep($line,$key,$value) : $value;
}

function rep($line, $key,$value) {
    $dest = "{" . $key . "}";
    if(!is_scalar($value)) $value = "";

//	2011.08.24 Start
    $value2 = str_replace('<br />', '', trim($value));
    if (empty($value2)) {
        $line = str_replace('{display-if(' . $key . ')}', 'style="display: none;"', $line);
    }
//	2011.08.24 End

    return str_replace($dest,$value,$line);
}
function estr($var) {
    return is_scalar($var) ? htmlspecialchars($var,ENT_QUOTES) : "";
}

/** M/Kがついている文字をバイトに修正します */
function strbytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // 'G' は PHP 5.1.0 以降で使用可能です
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}


/**
 * キーにプレフィックスを設定する
 * @param hash $hash
 * @param string $prefix
 * @return hash
 */
function array_prefix($hash,$prefix){
    foreach((array)$hash as $key => $value){
        $ret[$prefix . $key] = $value;
    }
    return (array)$ret;
}
?>