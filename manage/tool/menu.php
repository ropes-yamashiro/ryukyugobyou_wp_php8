<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
require_once("../system/init.php");
ini_set('display_errors', 'On');
error_reporting(E_WARNING);
 ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
BODY {
	font-family: Verdana, Arial, "MS UI Gothic", Osaka, Helvetica, sans-serif;
	background-color: #EEE;
	margin: 0px;
	padding-left: 20px;padding-right: 20px;
	background-image: none;
	font-size: 12px;
}
th {
	font-size: 12px;
}
td{
	font-size: 12px;
}
//-->
</style>
<SCRIPT TYPE="text/javascript">
<!--

//更新データを保存する
function selector(cmd,id){
	if(cmd=="ch"){
	    if(window.confirm('データを変更しますか？')){
			document.formx.cmd.value = "ch";
			document.formx.param.value = id;
			document.formx.submit();
		}
	}
	if(cmd=="del"){
	    if(window.confirm('データを削除しますか？')){
			document.formx.cmd.value = "del";
			document.formx.param.value = id;
			document.formx.submit();
		}
	}
	if(cmd=="new"){
	    if(window.confirm('データを追加しますか？')){
			document.formx.cmd.value = "new";
			document.formx.param.value = id;
			document.formx.submit();
		}
	}
	return false;
}


// -->
</SCRIPT>
<title>メニュー編集</title>
</head>
<body>
<br>
<br>
<?php

userload("tools/tools.php");
$cmd = webparam("cmd");
$param = webparam("param");
$type = webparam("type","document");


if($type == 'topic'){
	if($cmd == "del"){ tools_topic_del($param);}
	if($cmd == "new"){tools_topic_add(webparam(newname));}
	if($cmd == "ch"){tools_topic_rename($param,webparam(label.$param));}
	print tools_topic_all();

}
elseif($type == "mail"){
	if($cmd == "del"){tools_mail_del($param);}
	if($cmd == "new"){tools_mail_add(webparam("newname"));}
	if($cmd == "ch"){tools_mail_rename($param,webparam("label".$param));}
	print tools_mail_all();
}
elseif($type == "document"){
	if($cmd == "del"){tools_document_del($param);}
	if($cmd == "new"){tools_document_add(webparam("newname"));}
	if($cmd == "ch"){tools_document_rename($param,webparam("label".$param));}
	print tools_document_all();
}
elseif($type == "movie"){
	if($cmd == "del"){tools_movie_del($param);}
	if($cmd == "new"){tools_movie_add(webparam("newname"));}
	if($cmd == "ch"){tools_movie_rename($param,webparam("label".$param));}
	print tools_movie_all();
}
elseif($type == "topicimg"){
	if($cmd == "del"){tools_topicimg_del($param);}
	if($cmd == "new"){tools_topicimg_add(webparam("newname"));}
	if($cmd == "ch"){tools_topicimg_rename($param,webparam("label".$param));}
	print tools_topicimg_all();
}
elseif($type == "customer"){
	if($cmd == "del"){tools_customer_del($param);}
	if($cmd == "new"){tools_customer_add(webparam("newname"));}
	if($cmd == "ch"){tools_customer_rename($param,webparam("label".$param));}
	print tools_customer_all();
}
elseif($type == "icon"){
	if($cmd == "del"){tools_icon_del($param);}
	if($cmd == "new"){tools_icon_add(webparam("newname"));}
	if($cmd == "ch"){tools_icon_rename($param,webparam("label".$param));}
	print tools_icon_all();
}
elseif($type == "hintpasswd"){
	if($cmd == "del"){tools_hintpasswd_del($param);}
	if($cmd == "new"){tools_hintpasswd_add(webparam("newname"));}
	if($cmd == "ch"){tools_hintpasswd_rename($param,webparam("label".$param));}
	print tools_hintpasswd_all();
}
?>
</body>
</html>