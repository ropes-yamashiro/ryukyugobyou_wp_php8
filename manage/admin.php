<?php

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

require("system/init.php");
ini_set('display_errors', 'Off');
$ver = explode('.',phpversion());
if($ver[0] > 5){
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
}elseif($ver[0] == 5 && $ver[1] >= 3){
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
}else{
	error_reporting(E_ALL ^ E_NOTICE);
}
#アクセスするプラグイン
$typex = webparam('typex');

#ログイン・成功すれば、ログイン者のデータ------------------------------------------
$plug = getplugin(userpath("plugin.ini"));
$userinfo = login($typex);

#プラグインの実行----------------------------------------------------------------
$typex = $typex == 'login' ? $plug->default : $typex;
$contents = $plug->action($typex);

#ページのコンテナ作成------------------------------------------------------------
$html_menu = $plug->create_menu();
$html_page = admin_view($userinfo,$html_menu,$contents);

#ページの出力
header("Content-Type: text/html; charset=utf-8");
echo $html_page;

?>