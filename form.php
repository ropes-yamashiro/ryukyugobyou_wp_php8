<?php

#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History: 2009/04/21 メールの半角化
#          2009/11/20 英数文字の半角化
#==================================
//ini_set('display_errors', 'On');
//ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

//設定ファイル
$system = "manage/system/init.php";
$config = "manage/config/form/config.ini";
$userencode = 'utf-8';
//$userencode = 'shift-jis';
$systemenc = 'utf-8';

//システム初期化
require_once($system);

access_set();
userload("form/form.php");
userload("mail/mail.php");

foreach ($_POST as $key => $val) {
    if (!is_array($val)) {
        if($userencode != $systemenc){
            $val = mb_convert_encoding($val, $systemenc,$userencode);
        }
        $val = mb_convert_kana($val, 'sKVa',$systemenc);

        $_POST[$key] = $val;

    } else {
        foreach ($val as $inkey => $inval) {
            if($userencode != $systemenc){
                $inval = mb_convert_encoding($inval, $systemenc,$userencode);
            }
//	2011.05.17 radio & checkboxの場合は、半角に変換しない
//            $inval = mb_convert_kana($inval, 'sKVa',$systemenc);
            $_POST[$key][$inkey] = $inval;
        }
    }
}
//基本データ
$type = webparam('typex', 1);
$formno = webparam('pid', 1);
$cmd = webparam('cmd');
$id = webparam('id', webparam('param'));

//イメージ対応
if ($cmd == 'del' && $id) {form_filedel($id);}
if ($cmd == 'view' && $id) {form_fileshow($id);exit();}

//設定情報の読み込み
$info = form_infoloader($config, $formno);
$errinfo = errinfo($info['err_info']);


//メール部分の変換
$_POST[$info['mail']] = mb_convert_kana($_POST[$info['mail']], 'a', $systemenc);

//初期値設定
$err = errvarhash($errinfo, $info['value'], $formno); //エラー初期値
$_POST = whash(lhash($info['value']), $_POST);   //フォーム値の設定・check radio対策

$hidden = array('typex' => "", 'pid' => $formno, 'param' => "", 'cmd' => "");
$var = copyhash($_POST);
$epost = html_file2post();
$page = file_get_contents($info['input_file']);

//ステート処理
switch (intval($type)) {
    //クッキーがあれば表示する状態
    case 0:
		$var = $_COOKIE;
        break;

    //値が何もない状態
    case 1:
		$var = form_default($info);
		$def = true;
        break;

    //戻ってきた状態
    case 2:
		break;

    //確認
    case 3:
		//エラーチェック
		list($has, $herr) = errcheck($epost, $errinfo, $formno);

		$err = whash($err, $herr);
		if ($has)
			break;

		//値の設定
		$hidden = whash($hidden, $_POST);
		$check = true;

		//確認画面用
		$page = file_get_contents($info['check_file']);

        break;
	
	//終了処理
	case 4:
		//エラーチェック
		list($has, $herr) = errcheck($epost, $errinfo, $formno);
		$err = whash($err, $herr);
		if ($has)
			break;
		
		//値の設定
		$hidden = whash($hidden, $_POST);
		$check = true;

		$page = file_get_contents($info['thanks_file']);
		form_setcookie($info);
		form_mail($info['mail_no'], $_POST, $info);
		form_fileexit($info, $_POST);
		form_filedel();
		$exit = true;
		//dp(4);

		break;
		
	//２段階フォームの１段階目の処理
	case 5:
		//config.iniの「previous」の生成式に基づいて、２段階目フォームに渡す値を生成
		foreach($info['previous'] as $expression)
		{
			list($parent, $child_expression) = explode("=", $expression);
			$children = explode("+", $child_expression);
			$parent_value = "";
			foreach($children as $child)
			{
				$parent_value .= $var[$child];
			}
			$previous[$parent] = $parent_value;
		}
		
		$default = form_default($info);
		$var = array_merge($default, $previous);
		
		$def = true;
		
		break;
		
    default:{
        } break;
}

//データの出力 
$page = phash(tLoader::nLoad(userpath("form/user.html")), $page);
if($userencode != $systemenc){
    $page = mb_convert_encoding($page, $systemenc,$userencode);
}
$page = rep($page, 'form', basename(__FILE__));
$page = phash($err, $page);
$page = rep($page, "hidden", html_hidden($hidden));

if ($check) {
    $page = form_fileview($page, $var, $info, 'check');
    $page = form_groupcheck($page, $var, $info);
    $page = html_ephash($var, $page);
    $page = form_pageview($page, $var, $info);
} else {
    $page = form_fileview($page, $var, $info, $def ? "default" : null);
    $page = form_pageview($page, $var, $info);
    $page = ephash($var, $page);
}
if ($exit) { destroy_all();}

if($userencode != $systemenc){
    $page = mb_convert_encoding($page, $userencode,$systemenc);
}
pout($page,"UTF-8");
//pout($page,$userencode);
?>