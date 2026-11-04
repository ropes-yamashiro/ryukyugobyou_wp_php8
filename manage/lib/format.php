<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
/** 数値をコンマ区切りの値に変更します */
function format_ToYen($str_before){
	if (strlen($str_before)!=0) {
		$ret;
		// 文字列が3で割り切れない場合、文字列先頭の余り分を配列にセット
		$str_mod = strlen($str_before)%3;
		if ($str_mod != 0) {
			$str_array[] = substr($str_before,0,$str_mod);
			$str_before = substr($str_before, $str_mod, strlen($str_before));
		}
		// 文字列を3文字分ずつ区切って配列にセット
		for ($i=0;$i<strlen($str_before);$i+=3) {
			$str_array[] = substr($str_before, $i, 3);
		}
		while (count($str_array)>1) {
			$ret .= array_shift($str_array);
			$ret .= ",";
		}
		$ret .= array_shift($str_array);
		return $ret;
	}
	return $str_before;
}

/**
 * getdateにweek(日本語を設定したものを返す)
 * @param <type> $time
 */
function getdate_week($time = null){
    $time = $time == null ? time() : $time;
    $ret = getdate($time);

    $week = array('日','月','火','水','木','金'.'土');
    $ret['week'] = $week[$ret['wday']];
    return $ret;
}



?>