<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
libload("htmltools.php");
userload("passwd/passwd.php");

/**
 * 管理画面を作成する
 * @param string $err
 * @return string
 */
function account_view($err = null) {
    $html_page = userread(switchSpTemplate("account/account.html"));
    $html_info = userread(switchSpTemplate("account/auth-info.html"));
    $html_line = userread(switchSpTemplate("account/auth-line.html"));
	
    $userlist = passwd_alldata();
    $userkey = 'userid';

    #アカントの一覧の作成--------------------------------------------------------
    $username = webparam($userkey,'ann_admin');

    foreach($userlist as $userid => $data) {
        if(!$username) $username = $data[$userkey];
        if($data[$userkey]) $hash[$data[$userkey]] = $data[$userkey];
    }
    $html_page = rep($html_page,$userkey,html_ophash((array)$hash,$username));

    #権限の一覧------------------------------------------------------------------
    $aclist = account_load($username);
    $auth   = account_allauth();
    foreach($auth as $title => $value){
        $autolist = "";
        foreach($value as $data) {
            $onekey = $data['key'];
            $authinfo  = ephash($data,$html_info) . "\n";
            $autolist  .= rep(
                    $authinfo,
                    "value_{$onekey}",
                    in_array($onekey,$aclist) ? 'checked' : "");
        }
        $line      = rep($html_line,'title',$title);
        $contents .= rep($line,'auth_info',$autolist);

    }
    $html_page = rep($html_page,'errcode',$err);
    $html_page = rep($html_page,'auth_line',$contents);
    return $html_page;
}

/**
 * フレームワークの実行起点
 * @return string
 */
function account_act() {
    if(!webauth('account')) return new plugin_result('<font color="red">不正アクセスエラー：権限がありません</font>');
    $cmd = webparam('cmd','view');
    switch($cmd) {
        case "view"  :  return new plugin_result(account_view());
        case "chuser" : return new plugin_result(account_view());
        case "chauth" : return new plugin_result(account_ch());
        default : return new plugin_result('<font color="red">不正アクセスエラー：コマンドが定義されていません</font>');
    }
}

/**
 * アカウント権限を実行できるか
 * @return bool
 */
function account_first() {
    return webauth('account');
}

/**
 * システムで使用できる権限の一覧
 * @return array
 */
function account_auth() {
    $auth = array('key'=>"account",'name'=>"アカウント設定",'info'=>"ユーザーごとに権限を割り当てられます。");
    $hash["アカウント"] = array($auth);
    return $hash;
}

/**
 * アカウントのメニュー
 */
function account_menu($line,$admin,$title) {
    if(!webauth('account')) return;
    $ret = rep($line,'typex',"account");
    return rep($ret,'title',"アカウント設定");
}


/**
 * CMSに設定されている権限をすべて取出す
 * @return array
 */
function account_allauth() {
    /* データ例
        $auth1 = array(key=>"topic_title1",name=>"タイトル1",info=>"新着情報:タイトル1の表示/編集が可能になります。");
	$auth2 = array(key=>"topic_title2",name=>"タイトル2",info=>"新着情報:タイトル2の表示/編集が可能になります。");
	$hash["新着情報"] = array($auth1,$auth2);
    */
    $ret = array();
    $plug = getplugin();
    foreach($plug->command as $cmd) {
        $func = "{$cmd}_auth";
        if ($cmd != 'icon') {
            if(!function_exists($func)) continue;
            $plug_auth = $func();
            foreach((array)$plug_auth as $key => $data){
//print_r($data);echo '<br />';
                $ret[$key] = $data;
            }
        }
    }

    return $ret;
}




/**
 *  アカウント情報を変更します
 */
function account_ch() {
    #データを取り出す
    $user = webparam('userid');
    $dest = webparam('param');

    if($user == 'ann_admin' && $dest == 'account'){
        return account_view('<p><font color="red">ann_adminのアカウント権限はoffにできません</font></p><br>');
    }

    #DBに保存
    $db = getDB();
    $param = array('user'=>$user,'auth'=>$dest);
    $sql = webparam("auth_{$dest}")
        ? 'INSERT INTO account_auth (`user`,`auth`) VALUES(:user,:auth);'
        : 'DELETE FROM account_auth WHERE `user`=:user and `auth`=:auth;';

    $db->ExecuteRow($sql, $param);
    return account_view();
}


/**
 * アカウントのデータを読込む
 * @param string $user ユーザー名
 * @return データ
 */
function account_load($user) {

    #権限情報を取得する
    $db = getDB();
    $sql = 'SELECT auth FROM account_auth WHERE `user`=:user';
    $table = $db->QueryRow($sql, array('user'=>$user));
    $authlist = array();
    foreach($table as $row){
        $authlist[] = $row['auth'];
    }
    return $authlist;
}


/**
 * テーブルを作成する
 * $force bool 強制リセットするかどうか
 */
function account_tbl_setup($force = false) {
    $db = getDB();

    #テーブル削除
    if($force){
        $drop = 'DROP TABLE IF EXISTS `accunt_auth`;';
        $db->ExecuteRow($drop);
    }

    #テーブル作成
    $create = '
        CREATE TABLE IF NOT EXISTS `account_auth` (
        `user` varchar(10) NOT NULL COMMENT "ログイン名",
        `auth` varchar(255) COMMENT "権限キー"
    ) ENGINE=InnoDB
      DEFAULT CHARSET=utf8
      COMMENT="権限情報を保存"';
    $db->ExecuteRow($create);

    $sql = 'SELECT count(*) FROM `account_auth`';
    $count = $db->QueryRow($sql);
    if($count[0]['count(*)'] == 0){
        $sql = 'INSERT INTO account_auth (`user`,`auth`) VALUES ("ann_admin","account")';
        $db->ExecuteRow($sql);
    }
}



?>
