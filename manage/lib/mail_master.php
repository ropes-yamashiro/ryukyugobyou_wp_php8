<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
//require_once("hashstr.php");
//require_once("debug.php");
//require_once("hashstr.php"); 

/** 日本語環境用のメール */
class mailsender{
	
	var $file = array();
	
	/** Shift-JIS→ISO-2022-JPとBase64エンコードを実行します */
	function encode($word,$base = true){
		$word = mb_convert_encoding($word,"ISO-2022-JP","UTF-8");
		return $base ? base64_encode($word) : $word;
	}
	
	function japantext($name){
		if($name == null) return null;

		return " =?ISO-2022-JP?B?" . $this->encode($name) . "?= " ;
	}
	
	/** ファイルを追加します */
	function attach($name,$data){
		array_push($this->file,array('name'=>$name,'value'=>$data));
	}
	
	/** ハッシュを使用して送信します to,from,sub,con,myname,tonameが構成要素 */
	function sendhash($hash){
		$this->send($hash['to'],$hash['from'],$hash['sub'],$hash['con'],$hash['myname'],$hash['toname']);
	}
	
	/** データを送信します */
	function send($mailTo,$mailFrom,$subject,$contents,$fromName,$toName){

		if(empty($mailTo))return;

		//必要な情報
		$toName = $this->japantext($toName) . "<$mailTo>";	//送信先の設定
		$subject= $this->japantext($subject);			//タイトルの設定
		$boundary = md5(uniqid(rand()));
		$fromName = $this->japantext($fromName) . "<$mailFrom>"; //送信元の設定
		
		//ヘッダー部分
		$header = "From: $fromName\n"; //送信元
		$header .= "Return-path: $mailFrom\n";//送信できない場合送信先
		$header .= "MIME-Version: 1.0\n";
		
		//ファイル用ヘッダー
		if(count($this->file) > 0){
			$header .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n\n";
			$header .= "This is a multi-part message in MIME format.";
			$msg .= "\n\n--$boundary\n";
			$msg .= 'Content-Type: text/plain; charset="ISO-2022-JP"'; 
			$msg .=	"\nContent-Transfer-Encoding: 7bit\n\n";
		}
		else{
//			$header .= 'Content-Type: text/plain; charset="utf-8"';
			//$header .="\nContent-Transfer-Encoding: base64";
			$header .= 'Content-Type: text/plain; charset="ISO-2022-JP"';
			$header .="\nContent-Transfer-Encoding: 7bit";
		}
		$contents = str_replace("\r\n","\n",$contents);
		$contents = $msg . $this->encode($contents,false);

		
		foreach($this->file as $fone){
			$fname = $this->japantext($fone['name']);
			$ftype = getMIME($fone['name']);
			$fdata = chunk_split(base64_encode($fone['value']));	
			
			$info .= "\n\n--$boundary\n";
			$info .= "Content-Type: " . $upfile_type . ";\n";
			$info .= "\tname=\"$fname\"\n";
			$info .= "Content-Transfer-Encoding: base64\n";
			$info .= "Content-Disposition: attachment;\n";
			$info .= "\tfilename=\"$fname\"\n\n";
			$info .= "$fdata\n";
		}
		if(count($this->file) > 0) $info .= "\n\n--$boundary--";

                $contents .= $info;

		mail($toName,$subject,$contents,$header);
mb_internal_encoding("utf-8");
mb_language("uni");
mb_send_mail($toName,$subject,$contents,$header);
	}
}
?>