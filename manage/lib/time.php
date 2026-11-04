<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
/** 現在が指定したひから、$within日以内か? */
function inTime($year,$month,$day,$within){
	$time = mktime(0,0,0,$month,$day,$year);
	$time = strtotime("+" . $within -1 . " day",$time);
	$now  = strtotime(date("Y/m/d"));
	return $time  >= $now ;
}

/** 現在が指定したひから、$within日以内か?
 * @param $hash year,month,dayをキーに持つハッシュ*/
function inTimeh($hash,$within){
	return inTime($hash['year'],$hash['month'],$hash['day'],$within);	
}

/** 過去の日か  
 * @param $hash year,month,dayをキーに持つハッシュ*/
function outTimeh($hash){
//	2011.06.24
//	return outTime($hash['year'],$hash['month'],$hash['day']);
	return outTime($hash['view_year'],$hash['view_month'],$hash['view_day']);
}

/** 過去の日か */
function outTime($year,$month,$day){
	$time = mktime(0,0,0,$month,$day,$year);
	$ny = date("Y"); $nm = date("m"); $nd = date("d");
	$now = mktime(0,0,0,$nm,$nd,$ny);
	return $time <= $now;
}
?>