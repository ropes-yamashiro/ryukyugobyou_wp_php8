<?php

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

#★必須設定
userload("passwd/passwd.php");
userload("account/account.php");
userload("admin/admin.php");

/**
 * ログイン画面を作成する
 * @param bool $error エラーがあるかどうか
 * @return string
 */
function login_view($error = false) {
    $html_page = userread(switchSpTemplate("login/login.html"));
    $html_page = rep($html_page,'message',$error
            ? 'アカウントとパスワードを再入力してください。'
            : 'アカウントとパスワードを入力してください。');
    $html_page = rep($html_page,'type', $error ? 'error' : 'message');
    $html_page = rep($html_page,'userid',$error ? $_POST['userid'] : "");
    $html_page = rep($html_page,'auto',$error && $_POST['auto'] == 'on' ? "checked" : "");
    return $html_page;
}

/**
 * ログインチェックをする。
 * ログインしていない場合は、ログイン画面を表示して終了
 * @return array 個人情報
 */
function login($type) {
    
    #ログインしていたら個人情報を返す
    if(login_isloginOK()){
        return session::get('login_info');
    }
    #ログインでない場合
    if($type != 'login'){
        echo login_view(false);
        exit();
    }
    else{
        $userinfo = passuser_check($_POST['userid'],$_POST['passwd']);
        if($userinfo){
            #個人情報を保存する
            login_session_save($userinfo);

            #権限情報を取り出す
            $authlist = account_load($_POST['userid']);
            websetauth($authlist);

            return $userinfo;
        }
        else{
            echo login_view(true);
            exit();
        }
    }
}

//ログアウト
function logout_act() {
    session::destroy();
    echo login_view();
    exit();
}

/**
 * すでにログインしているかどうか
 * @return bool
 */
function login_isloginOK() {
    if(session::get('login_name') == null) return false;
    $crypt = crypt(session::get('login_name') . session::get('login_time'),'passkey');
    return session::get('login_session') == $crypt;
}

/**
 * ログイン情報を保存する
 * @param string $name
 * @param array $info
 */
function login_session_save($info) {
    $time = time() + (3600 * 24 * 7);
    $crypt = crypt($info['userid'] . $time,'passkey');
    session::set('login_name', $info['userid']);
    session::set('login_time', $time);
    session::set('login_session', $crypt);
    session::set('login_info', $info);
}

?>