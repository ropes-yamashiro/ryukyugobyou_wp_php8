<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
function pl($label, $val){	print $label . "<br>";	dp($val);}

function dp($var){print "<pre>";echo var_export($var,true);print "</pre><hr>";}

function p($value){
	print "<p>" . $value . "</p>";	
}

function pr($value,$label = null){
	print $label .":";
	print_r($value) ;
	print "<br>";	
}

function hr(){
	print "<hr>";	
}

/** デバックハッシュを作成します*/
function nbug($debug){
	$hash["debug"] = sbug($debug);
	return $hash;
}

/** デバック値を追加します*/
function abug($hash,$debug){
	$hash["debug"] = sbug($debug);
	return $hash;
}

/** デバック値を作成しますします*/
function sbug($debug){
	return $debug ? _debug : "";
}

?>