<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
/** ファイルのアップロードサイズを調べる
　　0=成功 1=単体ファイルサイズエラー 2=ゼロサイズファイルエラー  
    キー@ が　0=成功　1=総合ファイルエラー
　　戻り値：(キー => エラー番号) の連想配列 */
function checkUploadSize(){
	
	$tmax = strbytes(ini_get('post_max_size'));
	$fmax = strbytes(ini_get('upload_max_filesize'));
	
	$utotal;
	if(count($_FILES) == 0) return array();
	foreach($_FILES as $key => $hash){
		if($hash['size'] == 0) $ret[$key] = 2;
		if($hash['size'] > $fmax) $ret[$key] = 1;
		$utotal += $hash['size'];
	}
	$ret["@"] = $utotal > $tmax ? 1 : 0;
	return $ret;
}



function existMIME($ext){
	$hash = array("txt"=>"text/plain","csv"=>"text/csv","tsv"=>"text/tab-separated-values","doc"=>"application/msword",
			"xls"=>"application/vnd.ms-excel",
			"xlsx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
			#"xlsx"=>"application/octet-stream",
			"docx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			#"docx"=>"application/octet-stream",
			"ppt"=>"application/vnd.ms-powerpoint","pdf"=>"application/pdf",
			"xdw"=>"application/vnd.fujixerox.docuworks","html"=>"text/html","htm"=>"text/html",
			"css"=>"text/css","js"=>"text/javascript","hdml"=>"text/x-hdml","jpg"=>"image/jpeg",
			"jpeg"=>"image/jpeg","png"=>"image/png","gif"=>"image/gif","bmp"=>"image/bmp",
			"ai"=>"application/postscript","mp3"=>"audio/mpeg","m4a"=>"audio/mp4","wav"=>"audio/x-wav",
			"mid"=>"audio/midi","midi"=>"audio/midi","mmf"=>"application/x-smaf","mpg"=>"video/mpeg",
			"mpeg"=>"video/mpeg","wmv"=>"video/x-ms-wmv","swf"=>"application/x-shockwave-flash",
			"3g2"=>"video/3gpp2","zip"=>"application/zip","lha"=>"application/x-lzh","lzh"=>"application/x-lzh",
			"tar"=>"application/x-tar","tgz"=>"application/x-tar","exe"=>"application/octet-stream");
	
	return array_key_exists($ext,$hash);
	
}


/** ファイル名からMIMEを取得します */
function getMIME($filename){
	$hash = array("txt"=>"text/plain","csv"=>"text/csv","tsv"=>"text/tab-separated-values","doc"=>"application/msword",
			"xls"=>"application/vnd.ms-excel",
			"xlsx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
			#"xlsx"=>"application/octet-stream",
	
			"docx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			#"docx"=>"application/octet-stream",
			"ppt"=>"application/vnd.ms-powerpoint","pdf"=>"application/pdf",
			"xdw"=>"application/vnd.fujixerox.docuworks","html"=>"text/html","htm"=>"text/html",
			"css"=>"text/css","js"=>"text/javascript","hdml"=>"text/x-hdml","jpg"=>"image/jpeg",
			"jpeg"=>"image/jpeg","png"=>"image/png","gif"=>"image/gif","bmp"=>"image/bmp",
			"ai"=>"application/postscript","mp3"=>"audio/mpeg","m4a"=>"audio/mp4","wav"=>"audio/x-wav",
			"mid"=>"audio/midi","midi"=>"audio/midi","mmf"=>"application/x-smaf","mpg"=>"video/mpeg",
			"mpeg"=>"video/mpeg","wmv"=>"video/x-ms-wmv","swf"=>"application/x-shockwave-flash",
			"3g2"=>"video/3gpp2","zip"=>"application/zip","lha"=>"application/x-lzh","lzh"=>"application/x-lzh",
			"tar"=>"application/x-tar","tgz"=>"application/x-tar","exe"=>"application/octet-stream");
	$info= pathinfo($filename);
	$ext = 	strtolower($info['extension']);
	if(array_key_exists($ext,$hash))return $hash[$ext];
	return $hash['exe'];
}


/**
 * 指定のURLにリダイレクトする
 * @param string $url
 * @param hash $param
 */
function redirect($url,$param = null){
    $paramtext = http_build_query2((array)$param);
    #テキストの内容に合わせてヘッダーを追加する
    if(strpos($url,'?')){
        if($paramtext) $url .= '&' . $paramtext;
    }
    else{
        if($paramtext) $url .= '?' . $paramtext;
    }


    #特定のページに戻す
    header('location: ' . $url);
}

function http_build_query2($param){
    foreach((array)$param as $key => $val){
        if($ret) $ret .= '&';
        $ret .= "$key=$val";
    }
    return $ret;
}


?>