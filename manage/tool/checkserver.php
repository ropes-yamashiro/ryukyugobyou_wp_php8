<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=uft-8">
<style type="text/css">
<!--
BODY {
	font-family: Verdana, Arial, "MS UI Gothic", Osaka, Helvetica, sans-serif;
	background-color: #EEE;
	margin: 0px;
	padding-left: 20px;padding-right: 20px;
	background-image: none;
}
.message{
 	font-size: 12px;
	font-weight: normal;
	margin: 10px;
	padding: 0px;
	padding-top:10px;
}
.error {
	font-size: 12px;
	font-weight: normal;
	color: #F30;
	margin: 10px;
	padding: 0px;
	padding-top:10px;
}
.btn {
	font-size: 12px;
	font-weight: normal;
	margin:10px;
}
//-->
</style>
<title>サーバーチェック</title>
</head>
<body>
<?php
//phpinfo();
error_reporting(E_ERROR | E_PARSE);

if(array_key_exists(to,$_POST)){pout(checks());	}
else{
	pout( '
				
				<form method="post">
				<p class=message>テストに必要なデータを入力してください</p><hr>
				<p class=message>メール送信先：<input type=text name="to" size="30"/>
				<br><br>形式を確認する機能が動作しない可能性
				があるため、アドレスの形式を正確に入力してください。<br></p>
				<input type=submit value="確認" class="btn" />
				</form>');
		
}

checks();
//print "aaa";
function checks(){
	$errs = p("注意：赤色のメッセージが表示されている場合、正常に動作しません");
	$errs .= "<hr>";
	
	//ライブラリの存在の有無
	//正規表現
	//http://jp2.php.net/manual/ja/ref.regex.php
	$errs .= !function_exists(ereg) || !function_exists(split) 
		? err("正規表現関数が組み込まれていません") 
		: p("正規表現関数を確認しました。");
	
	//ＵＲＬ関数
	//http://jp2.php.net/manual/ja/ref.url.php
	$errs .= !function_exists(base64_encode) 
		? err("URL 関数が組み込まれていません") 
		: p("URL 関数を確認しました");
	
	//マルチバイト関数
	$errs .= (!function_exists(mb_internal_encoding)
			|| !function_exists(mb_convert_encoding)
			|| !function_exists(mb_http_output)) 
		? err("マルチバイト文字列関数が組み込まれていません")
		: p("マルチバイト文字列関数を確認しました");
		
			
	
	//メール関数
	if(!function_exists(mail)){
		$errs .= err("メール関数が組み込まれていません");
	}
	
	else{
		//環境判定
		if(ini_get(sendmail_path) == ""){
			//windows環境対策用
			if(ini_get(SMTP) == "" || ini_get(smtp_port) == ""){
				$errs .= err("メールの設定が行われていません");
			}
		}
		$errs .= p("メールの設定を確認しました。");
		
		//メールチェック
		error_reporting(0);
		$to = $_POST[to];
		$mail = create_function("", 'return mail("' . $to . '","TestMail","System made a success of sending mail.");');
		if($mail()){
			$errs .= p("メールの送信に成功しました。受信を確認してください。");	
		}
		else{
			$errs .= err("メールの送信に失敗しました。メールサーバーの設定を確認してください。");
		}
		
	}
	
	//SQLite関数
	$errs .= (!function_exists(sqlite_open)
			|| !function_exists(sqlite_query)
			|| !function_exists(sqlite_close)) 
		? err("SQLite関数が組み込まれていません")
		: p("SQLite関数を確認しました");	
		
	//MySQL関数
	$errs .= (!function_exists(mysql_connect)
			|| !function_exists(mysql_query)
			|| !function_exists(mysql_free_result)) 
		? err("MySQL関数が組み込まれていません")
		: p("MySQL関数を確認しました");			
	
	$errs .= p("<b>チェックが終了しました</b>");
	
	$errs .= "<hr>";
	$mlimit = intval(str_replace("M" ,"",ini_get(memory_limit)));
	
	
	
	
	return $errs;
}
/** ヘッダーと値を出力させ、終了します*/
function pout($value,$charset = "shift-jis"){
	header("Content-type: text/html; charset=" . $charset);
	echo $value;
	exit();
}

function p($val){ $line = '<p class="message">{message}</p>';	return rep($line,message,$val);}
function err($val){ $line = '<p class="error">{message}</p>';	return rep($line,message,$val);}
function rep($line, $key,$value){	$dest = "{" . $key . "}"; 	return str_replace($dest,$value,$line);}


?>
</body>
</html>
