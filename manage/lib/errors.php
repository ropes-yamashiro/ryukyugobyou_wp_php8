<?php
#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History: 2009/04/21 メールの同一値チェック
#	   2009/04/21 電話番号の全角を通すように変更
#==================================
require_once("hashstr.php");

function errinfo($path){
	$ret = array();
	$file = file($path);
	
	$nownumber = null; $nowtemp = null;
	foreach($file as $line){
		$line = trim($line,"\r\n\t");
		
		if($line == ""){continue;}
		elseif(ereg("^//",$line)){continue;	} //コメント
		elseif(ereg("^\[([0-9]+)\]",$line,$value)){ //フォーム番号
			$nownumber = $value[1];$ret[$nownumber] = array("normal"=> array(), "extend"=>array());
			$nowtemp = null;
			continue;
		}

		if($nownumber == null) die("フォーム番号が設定されていない状態で、テンプレートが定義されました");			
		if(ereg("^%(.*)$",$line,$value)){ //テンプレート
			$nowtemp = $value[1];
			continue;
		}
		
		if($nowtemp == null) die("テンプレートが設定されていない状態で、判定条件が定義されました");
		
		$vars = tlist(split(",",$line));
		$c = count($vars);

		if(ereg("^@",$line)){ //特殊設定
			$name = substr($vars[0],1);
			$one = array($nowtemp,$name);
			if($c > 1) array_push($one,$vars[1],null);
			if($c > 2) $one[3] = $vars[2];
			if($c > 3) array_push($one,array_slice($vars,3));
			array_push($ret[$nownumber]['extend'],$one);
		}
		else{
			if($c < 3) 	pout("判定条件のデータは３つ以上必要です。");	
			$one = array($nowtemp,	$vars[0],$vars[1],$vars[2],null);		
			if($c > 3) $one[4] = $vars[3];
			if($c > 4) array_push($one,array_slice($vars,4));
			array_push($ret[$nownumber]['normal'],$one);
		}
	}
	return $ret;
}

function errvarhash($errinfo,$list,$no){
	$ret = array();
	$lbase = $list;
	foreach($lbase as $one){
		$ret["err" .  $one] = "";		
	}
	foreach($errinfo[$no]['normal'] as $one){
		$ret['err' . $one[1]] = "";
		if($one[4] != null) $ret['err' .  $one[4]] = "";
	}
	foreach($errinfo[$no]['extend'] as $one){
		if(substr($one[1],0,1) == "@") $one[1] = substr($one[1],1);
		$ret["err" . $one[1]] = "";	
		if($one[3] != null){
			if(substr($one[3],0,1) == "@") $one[3] = substr($one[3],1);	
			$ret["err" .  $one[3]] = "";
		}
	}
	return $ret;
}

function gn_checkinfo($errinfo,$no){
	if(!array_key_exists(intval($no),$errinfo)) return array();
	if(!array_key_exists('extend',$errinfo[intval($no)])) return array();
	
	$ret = array();
	foreach($errinfo[intval($no)]['extend'] as $one){
		if(!array_key_exists($one[1],$one)) $ret[$one[1]] = array();
		array_push($ret[$one[1]],$one);
	}
	return $ret;
}

function gs_checkinfo($errinfo,$name,$no){
	if(!array_key_exists(intval($no),$errinfo)) return array();
	if(!array_key_exists("normal",$errinfo[intval($no)])) return array();
	
	$ret = array();
	foreach($errinfo[intval($no)]['normal'] as $one){
		if($one[1] == $name){ 	array_push($ret,$one);	}		
	}
	return $ret;
}

function errcheck($post,$errinfo,$formno){

	$anyerr = false; $ret = array();
	foreach($post as $key => $value){
//--- START 1.00a 2014-10-01 SB/K.Arikata --------------
//		$value = trim($value);
		if( !is_array($value) ){
			$value = trim($value);
		}
		//配列をtrim()すると値がなくなってしまう
//--- END 1.0a 2014-10-01 SB/K.Arikata --------------------------------------------------
		$errval = gs_checkinfo($errinfo,$key,$formno);
		foreach($errval as $errone){
			$err = false;
			list($etemp,$key,$etype,$mes,$tokey,$items) = $errone;
			switch($etype){
				case "check_need" :{
//--- START 1.00a 2014-10-01 SB/K.Arikata --------------
//					$val = toarray($post[$key]);
					$val = $post[$key];
					//toarray()に空文字を渡すと、[0] => ""の配列になってしまうので、count()==1、empty()==falseになってしまう
//					$err = count($val) == 0;
					$err = empty($val);
//--- END 1.0a 2014-10-01 SB/K.Arikata --------------------------------------------------
				}break;
				case "check_count" :{
					$val = toarray($post[$key]);

					$err = !(count($val) >= intval($items[0]) && count($val) <= intval($items[1]));
				}break;
				case "check_flist" :{
					$val = toarray($post[$key]);
					//dp($errone);
					foreach($val as $vone){
						if(!isflist($vone,$items[0])){$err = true;break;}
					}
				}break;
				case "check_list" :{
					$val = toarray($post[$key]);
					foreach($val as $vone){
						if(!in_array($vone,$items)){$err = true;break;}
					}
				}break;
				case "fileneed" :{
					$err = !array_key_exists($key,$_FILES);
					$err = $_FILES[$key]['name'] == "" && $post[$key] == "";
				}break;
				case "need":  $err = empty($value);				break;
				case "num0":  
					if($value == ""){$err = false;break;}
					$err = !ereg("^[0-9]+$",$value);	break;
				case "num" :  $err = $value != null && !ereg("^[0-9]+$",$value);		break;
				case "num3":  $err = $value != null && !isNum3($value);				break;
				case "num4":  $err = $value != null && !isNum4($value);				break;
				case "mail":  $err = $value != null && !ismail($value);				break;
				case "list":  $err = $value != null && !in_array($value,$items);                break;
				case "kana":  $err = $value != null && !isBigKana($value);			break;
				case "hira":  $err = $value != null && !isBigHira($value);			break;
				case "zip":   $err = $value != null && !isZip($value);				break;
				case "phone": $err = $value != null && !isPhone2All($value);			break;
				case "pref":  $err = $value != null && !isPref($value);				break;
				case "flist": $err = $value != null && !isflist($value,$items[0]);	break;
				case "hirakata" : $err = $value != null && !isBigHiraKata($value);  break;
				case "phoneall" : $err = $value != null && !isPhone2All($value);		break;
				case "equal" :    $err = $_POST[$key] != $_POST[$items[0]];break;
				case "spam":
					//スパムチェック　reCAPTCHA v3
					if ($value != null) {
						
//$fname=dirname(__FILE__)."/_debug_errors_errcheck.txt";touch($fname, 0666);$fn=fopen($fname,"a");
//fwrite($fn, "--------------------\n");
//fwrite($fn, '$post='.print_r($post, true)."\n");
						$recaptcha_secret_key = $items[0];
						$url = 'https://www.google.com/recaptcha/api/siteverify';
						
						$post_data = array(
								'secret' => $recaptcha_secret_key,
								'response' => $_REQUEST['token'],
								'remoteip' => $_SERVER['REMOTE_ADDR'], // 任意
						);
						$options = array(
								'http' => array(
										'method' => 'POST',
										'header' => 'Content-type: application/x-www-form-urlencoded',
										'content' => http_build_query($post_data),
								)
						);
						$context = stream_context_create($options);
						$response = json_decode(file_get_contents($url, false, $context));
//fwrite($fn, '$response='.print_r($response, true)."\n");
//fclose($fn);
						

						if (!$response->success) {
							$fname=dirname(__FILE__)."/_reCAPTCHA_errors.log";touch($fname, 0666);$fn=fopen($fname,"a");
							fwrite($fn, "--------------------\n");
							fwrite($fn, "time:". date_create()->format("Y-m-d H:i:s")."\n");
							fwrite($fn, "client:". $_SERVER['REMOTE_ADDR']."\n");
							fwrite($fn, '$response='.print_r($response, true)."\n");
							fclose($fn);
							
							$err = true;
						}
					}
					break;
			}
			$mes = rep($etemp,"error",$mes);$mes = rep($mes,"value",$value);
			$out = $tokey == null ? $key : $tokey;
			if($ret["err$out"] == null || $ret["err$out"] == "") $ret["err$out"] = $err ? $mes : "";
			$anyerr |= $err;
			if($err) break;
		}
	}
	
	$gn = gn_checkinfo($errinfo,$formno);
	foreach($gn as $key => $value){
		
		foreach($value as $one){
			list($etemp,$etype,$mes,$tokey,$v) = $one;
			switch($etype){
				case "day" : $err = !isDay($post,$v[0],$v[1],$v[2]); break;
				case "any" : $err = $anyerr; break;
			}
			$mes = rep($etemp,"error",$mes);
			if($v != null) $mes = ephash(shash($v,$post),$mes);
			$out = $tokey == null ? $key : $tokey;
			if($ret["err$out"]== null || $ret["err$out"] == "")  $ret["err$out"] = $err ? $mes : "";
			$anyerr |= $err;
		}	
	}
	
	$ret = array($anyerr,$ret);
	return $ret;
}
function isflist($val,$path){
	$var = readlist($path);
	return in_array($val,$var);
}
function isPref($val){
	$pref = "北海道,青森県,岩手県,宮城県,秋田県,山形県,福島県,茨城県,栃木県,群馬県,埼玉県," .
		"千葉県,東京都,神奈川県,新潟県,富山県,石川県,福井県,山梨県,長野県,岐阜県,静岡県," .
		"愛知県,三重県,滋賀県,京都府,大阪府,兵庫県,奈良県,和歌山県,鳥取県,島根県,岡山県," .
		"広島県,山口県,徳島県,香川県,愛媛県,高知県,福岡県,佐賀県,長崎県,熊本県,大分県,宮崎県,鹿児島県,沖縄県";

	$prefhash = chash($pref);
	return array_key_exists($val,$prefhash);	
}

function removeLine($val){
	$cl =clist("-,ー,‐,―,－,-");
	foreach($cl as $cone){
		$val = str_replace($cone,"",$val);
	}
	$val = str_replace(" ",null,$val);
	$val = str_replace("　",null,$val);
	return $val;
}


function isBigHiraKata($val){
	
	$jchk = "^[あいうえおかきくけこさしすせそたちつてとなにぬねの".
		"はひふへほまみむめもやゆよらりるれろわをん".
		"がぎぐげござじずぜぞだぢづでどばびぶべぼ".
		"ぱぴぷぺぽぁぃぅぇぉゃゅょっ" .
	    "アイウエオカキクケコサシスセソタチツテトナニヌネノ".
		"ハヒフヘホマミムメモヤユヨラリルレロワヲン".
		"ガギグゲゴザジズゼゾダヂヅデドバビブベボ".
		"パピプペポァィゥェォャュョーッ 　]+$";

	return regjpn($jchk,$val);
}
function isBigHira($val){
	$jchk = "^[あいうえおかきくけこさしすせそたちつてとなにぬねの".
	"はひふへほまみむめもやゆよらりるれろわをん".
	"がぎぐげござじずぜぞだぢづでどばびぶべぼ".
	"ぱぴぷぺぽぁぃぅぇぉゃゅょーっ 　]+$";
	return regjpn($jchk,$val);
}
function isIntval($val){
	return preg_match("/^[0-9]+$/",$val);
}
function isBigKana($val){
	$jchk = "^[ 　アイウエオカキクケコサシスセソタチツテトナニヌネノ".
		"ハヒフヘホマミムメモヤユヨラリルレロワヲン".
		"ガギグゲゴザジズゼゾダヂヅデドバビブベボ".
		"パピプペポァィゥェォャュョーッ]+$";
	return regjpn($jchk,$val);
}
/** 範囲内の数字か */
function isNumReg($value,$start,$end){return $value >= $start && $value <= $end;}
function isDate($text){
    list($y,$m,$d) = split('/',$text);
    return checkdate($m,$d,$y);
}
function isDay($post,$y,$m,$d){return checkdate($post[$m],$post[$d],$post[$y]);}
function ismail($text) {return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $text);}
function isEngword($text){
	return ereg("^[0-9a-zA-Z]+$",$text);
}
function isNum3($value){
	
	$jchk = "^[０１２３４５６７８９]{3}$";
	return regjpn($jchk,$value) || ereg("^[0-9]{3}$", $value);
}
function isNum4($value){
	$jchk = "^[０１２３４５６７８９]{4}$";
	return regjpn($jchk,$value)|| ereg("^[0-9]{4}$", $value);
}
function isZip($value){
	if(ereg("^[0-9]{3}-[0-9]{4}$",$value)) return true;
	$value = removeLine($value);

	if(ereg("^[0-9]{7}$",$value)) return true;
	$jchk = "^[０１２３４５６７８９]{7}$";
	return regjpn($jchk,$value);
	
}

/** イメージの拡張子かどうか */
/** 拡張子がjpegのファイルがアップ出来なかった（2015/02/04にスズカ電工さんで発覚） 有方さんに調査してもらい修正済み 'jpeg'を追加*/
function isImageExt($filepath) {
    if($filepath == null)return null;
    $in = array('gif','jpg','png','swf','swc','psd','tiff','bmp','iff','jp2','jpx','jb2','jpc','xbm','jpeg');
    $var = pathinfo($filepath);
    $ext = strtolower($var['extension']);
    return in_array($ext,$in) ? null : $ext;
}


/** 全角もオッケー版 */
function isPhone2All($val){
	//半角の確認
	$val = mb_convert_kana($val,'a','utf-8');

	if(isPhoneAll($val)) return true;
	$val = removeLine($val);

	$jchk = "^[0-9]{10,11}$";
	return regjpn($jchk,$val);
}


function regjpn($jchk,$val){
       
	$val = str_replace("\0", '', $val);
	$jchk = "/". $jchk ."/";
	$ret = preg_match($jchk,$val);
        return $ret;
}

function isPhone($val){
	if (ereg("^[0-9]{2}-[0-9]{4}-[0-9]{4}$", $val)) return true;
	if (ereg("^[0-9]{3}-[0-9]{4}-[0-9]{4}$", $val))	return true;
	if (ereg("^[0-9]{3}-[0-9]{3}-[0-9]{4}$", $val)) return true;
	return false;
}
function isPhoneAll($val){
	return (ereg("^[0-9]{10,11}$",$val) || isPhone($val));
}
?>