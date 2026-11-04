<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
/** 管理者用データを使用してメールを送信します*/
function smail($mailno,$vlist){
	userload("mail/mail.php");	
	$mconf = new mailconfig();
	$mlist = $mconf->getValue($vlist,$mailno,"mail");
	$sender = new mailsender();
	foreach($mlist as $mone){ $sender->sendhash($mone);	} 	
}

?>