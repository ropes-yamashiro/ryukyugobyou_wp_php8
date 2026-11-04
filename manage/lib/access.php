<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

/** セッション・ファイル・POSTの消去 */
function destroy_all(){
	foreach($_POST as $key=>$value){
		unset($_POST[$key]);	
	}
	foreach($_FILES as $key=> $value){
		unset($_FILES[$key]);	
	}
	foreach($_SESSION as $key=>$value){
		unset($_SESSION[$key]);
	}
	session_destroy();
	session_id(true);
}


/** セッションの初期化 */
function session_exit(){
	$_SESSION = array();
	session_destroy();	
}

/** セッションの保存*/
function session_set($name,$value){
	if(!array_key_exists($name,$_SESSION)){
	}
	$_SESSION[$name] = $value;
}

/** セッションにデータを追加する */
function session_keyset($name,$key,$value){
	if(!array_key_exists($name,$_SESSION)){
		$_SESSION[$name] = array();
	}
	$_SESSION[$name][$key] = $value;
}

/** セッションにデータを追加する */
function session_key2set($name,$key1,$key2,$value){
	if(!array_key_exists($name,$_SESSION)){
		$_SESSION[$name] = array();
	}
	$_SESSION[$name][$key1][$key2] = $value;
}

/** セッションからデータを取得する */
function session_keyget($name,$key){
	if(!array_key_exists($name,$_SESSION))return null;
	return $_SESSION[$name][$key];
}

/** セッションからデータを取得する*/
function session_get($name){
	if(!array_key_exists($name,$_SESSION))return null;
	return $_SESSION[$name];
}

/** セッションをnullでクリア―する*/
function session_clear($name = null){
	if($name == null) $_SESSION = array();
	$_SESSION[$name] = null;	
}

/** セッションをarrayでクリア―する*/
function session_clearh($name = null){
	if($name == null) $_SESSION = array();
	$_SESSION[$name] = array();	
}

/** ヘッダーと値を出力させ、終了します*/
function pout($value,$charset = "shift-jis"){
	access_sjis($charset);
	echo $value;
	exit();
}

/** headerでSJISを出力します*/
function access_sjis($charset = "shift-jis",$cache = false){
	header("Content-type: text/html; charset=" . $charset);
	if($cache){
		header('Cache-Control: max-age=' . 3600);
		if (session_id()) {  
			header('Expires: ' . date('D, d M Y H:i:s', time() + 3600).' GMT');
			header('Pragma: cache'); 
		}
	}
}

function mb_encode_array(&$aValues, $sEncoding)
{
	foreach ($aValues as $sKey => $sValue)
	{
		if(is_array($sValue))
		{
			mb_encode_array($sValue,$sEncoding);
		}
		elseif (is_scalar($sValue)){

			$aValues[$sKey] = mb_convert_encoding($sValue, "SJIS", $sEncoding);
		}
	}
}
function mb_encode_filename($sEncoding){
	foreach($_FILES as $key=>$value){
		$_FILES[$key]["name"] = 	mb_convert_encoding($value["name"], "SJIS", $sEncoding);	
	}
}

/** 環境対策 */
function access_set(){

	ini_set("mbstring.http_output","pass");
	ini_set("mbstring.http_input","pass");

	if(false)
	{
		stripslashes_array($_GET);
		stripslashes_array($_POST);
		stripslashes_array($_COOKIE);
		//stripslashes_array($_REQUEST);
	}
	$var = mb_get_info("all");
	if($var["encoding_translation"] == "Off") return;
	$sAutoEncoding = mb_internal_encoding();

	if($sAutoEncoding != "SJIS" && $sAutoEncoding != null)
	{
		mb_encode_filename($sAutoEncoding);		
		mb_encode_array($_GET, $sAutoEncoding);
		mb_encode_array($_POST, $sAutoEncoding);
		mb_encode_array($_COOKIE, $sAutoEncoding);
	}
	//exit();

}


//magic_quotes_gpc対策
function stripslashes_array(&$aValues)
{
    foreach ($aValues as $sKey => $sValue)
    {
		if(is_scalar($sValue)) $aValues[$sKey] = stripslashes($sValue);
    }
}

/**
 * ユーザーエージェントによるテンプレートファイルの切替
 * @param String $template_file テンプレートファイル名(フルパス)
 * @return String 切替後テンプレートファイル名
 */
function switchSpTemplate($template_file) {
		
	if (isSPhone()) {
		$fileinfo = pathinfo($template_file);
		
		$return_file = $fileinfo["dirname"]."/".$fileinfo["filename"]."_sp.".$fileinfo["extension"];	//ファイル名にプレフィックスを付加

		//ファイル存在チェック
		if (file_exists($return_file)) {	//素パス
			return $return_file; 
		}
		else if (file_exists(userpath($return_file))) {		//ユーザーパス
			return $return_file; 
		}
	}
	
	return $template_file;
}
function isSPhone() {
	
	//ユーザーエージェントの取得
    $ua = $_SERVER['HTTP_USER_AGENT'];
	
    //スマホ（iPhone、Android、WindowsPhone）
    if (	((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false))
		||	(strpos($ua, 'iPhone') !== false)
		||	(strpos($ua, 'Windows Phone') !== false)
														) { 
		//スマホ
        return true;
    }
	elseif (	(strpos($ua, 'Android') !== false)
			||	(strpos($ua, 'iPad') !== false)
														) {
		//タブレット
        return true;
    }
	else {
		//PC
        return false;	
    }
}

?>