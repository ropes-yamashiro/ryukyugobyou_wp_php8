<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

libload("hashstr.php");

/** 関数があれば実行する */
function ifFunc($name){
	$var = func_get_args();
	$cnt = count($var) - 1;
	if(function_exists($name)) return call_user_func_array($name,array_slice($var,1));
}

/** コード中の引用符なし識別子（未定義定数）を文字列にします。関数名・定義済み定数・true/false/null はそのまま */
function actFunc_quote_bareword($code){
	$parts = preg_split('/("(?:[^"\\\\]|\\\\.)*"|\'(?:[^\'\\\\]|\\\\.)*\')/s', $code, -1, PREG_SPLIT_DELIM_CAPTURE);
	foreach($parts as $i => $part){
		if($i % 2 == 1) continue; //引用符で囲まれた部分
		$parts[$i] = preg_replace_callback('/(?<![\w$>:])[A-Za-z_]\w*(?!\s*\(|\w)/', function($m){
			$w = $m[0];
			if(defined($w) || in_array(strtolower($w), array('true','false','null'))) return $w;
			return "'" . $w . "'";
		}, $part);
	}
	return implode('', $parts);
}

/** 関数の呼び出しを持つコードを実行します　prefix_url(A,B,C) もしくは $this->$prefix_url(A,B,C)を実行する
 * @param $prefix 関数の接頭語
 * @param $code   例:"url(A,B,C)" */
function actFunc($code,$prefix,$obj = null){
	if($prefix) $prefix .= "_";
	
	if(!$obj){
		//PHP8対応: 旧PHPでは未定義定数が文字列として扱われていたため、引用符なしの識別子(例: url("a.html", mail))を文字列に変換する
		$code = actFunc_quote_bareword($code);
		$func = function() use ($prefix, $code) { return eval("return $prefix$code;"); };
		return $func();
	}
	else{
		$pos  = strpos($code,"(");
		$method = $prefix . substr($code,0,$pos);
		$def  = trim(substr($code,$pos + 1),"()");
		$args = cword($def,",");
		$args = twlist($args);
		return call_user_func_array(array($obj,$method),$args);
	}
}

/** 関数だった実行する */	
function ifactFunc($code,$prefix =null){
	if(preg_match("/^\w+\(/",$code)){
		return actFunc($code,$prefix);
	}
	return $code;
}


?>