<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
//このファイルはUTF-8で記述されています



/** コードを切り出す "'(内の$cutを無視してデータを取り出す
 @param $code 分割対象のデータ(SJIS限定) 
 @param $cut  分割する文字(SJIS限定) */
function cword($code,$cut){
	#$code = mb_convert_encoding($code,"UTF-8",SJIS);
	#$cut = mb_convert_encoding($cut,"UTF-8",SJIS);
	$ret = array(); $off = 0;
	
	while(true){
		$code = trim($code);
		if(!$code) return $ret;
		
		$cp = strpos($code,$cut,$off);
		if($cp === false){ break; }
		
		$size = strlen($code);
		$var = chash("\",',(");
		$end = array('"'=>'"',"'"=>"'","("=>")");
		
		foreach($var as $key => $value){
			$pos = strpos($code,$key,$off);
			$var[$key] = $pos === false ? $size : $pos;	
		}
		asort($var); reset($var);
		$ckey = key($var); $cpos = pos($var); 
		
		
		
		//先に区切り文字が来た場合
		if($cp < $cpos){
			
			//追加部分mb_convert_encoding(substr($code,0,$cp),SJIS,"UTF-8")
			array_push($ret,substr($code,0,$cp));
			//array_push($ret,mb_convert_encoding(substr($code,0,$cp),SJIS,"UTF-8"));
			$code = substr($code,$cp+1); $off = 0;
			continue;
		}
		//先に範囲文字が来たら
		else {
			//検索開始位置を移動
			$off = strpos($code,$end[$ckey],$cpos + 1) + 1;
			if($off === false){ p("out"); break;}
		}
	}

        //array_push($ret,mb_convert_encoding($code,SJIS,"UTF-8"));	
	if($code) array_push($ret,$code);	
	return $ret;
}
?>