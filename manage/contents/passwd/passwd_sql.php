<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

/////////////////////////////////////////////////////////
//  取得関数

/**すべてのユーザー名を取得します hash(userid,passwd,name)+ or null */
function passwd_sql_all() {
    $db = getDB();
    $sql = 'SELECT * FROM passwd_info';
    $tbl = $db->QueryRow($sql);

    $ret = array();
    foreach($tbl as $row) {
        $row['passwd'] = passwd_sql_crypt($row['passwd'],false);
        $ret[] = $row;
    }
    return $ret;
}

/**
 * ユーザーIDから情報を取得します
 * @param int $userid
 * @return array hash(userid,passwd,name)
 */
function passwd_sql_info_by_userid($userid) {
    $db = getDB();
    $sql = 'SELECT * FROM passwd_info WHERE userid = :userid';
    $tbl = $db->QueryRow($sql,array('userid'=>$userid));

    if(count($tbl) == 0) return null;
    $row = $tbl[0];
    $row['passwd'] = passwd_sql_crypt($row['passwd'],false);
    return $row;
}

/**
 * IDから情報を取得します
 * @param int $id
 * @return array hash(userid,passwd,name)
 */
function passwd_sql_info_by_id($id) {
    $db = getDB();
    $sql = 'SELECT * FROM passwd_info WHERE `id` = :id';
    $tbl = $db->QueryRow($sql,array('id'=>$id));

    if(count($tbl) == 0) return null;
    $row = $tbl[0];
    $row['passwd'] = passwd_sql_crypt($row['passwd'],false);
    return $row;
}

/**
 * ページ番号・表示数から情報を取得します
 * @param int $pageno 1から始まるページ番号
 * @param int $count 一度に表示する項目数
 * @return array hash(userid,passwd,name)
 */
function passwd_sql_infos($pageno,$count) {
    $db = getDB();
    $top = intval(($pageno - 1) * $count);
    $count = intval($count);
    $sql = 'SELECT * FROM passwd_info ORDER BY `id` desc limit '. "{$top},{$count}";
    $tbl = $db->QueryRow($sql);

    $ret = array();
    foreach($tbl as $row) {
        $row['passwd'] = passwd_sql_crypt($row['passwd'],false);
        $ret[] = $row;
    }
    return $ret;
}

/**
 * ユーザー数を取得します
 */
function passwd_sql_usercount() {
    $db = getDB();
    $sql = 'SELECT count(*) FROM passwd_info';
    $tbl = $db->QueryRow($sql);
    return $tbl[0]['count(*)'];
}


/////////////////////////////////////////////////////////
//  編集関数

/**
 * データを変更
 */
function passwd_sql_change($newid,$pass,$name,$id) {
    $db = getDB();
    $pass = passwd_sql_crypt($pass);
    $param = array('id'=>$id,'userid'=>$newid,'passwd'=>$pass,'username'=>$name);
    $sql = 'UPDATE passwd_info SET `userid` = :userid,`passwd`=:passwd,`username`=:username WHERE `id`=:id';
    $db->ExecuteRow($sql, $param);
}

/**
 * データを削除
 * @param string $userid
 */
function passwd_sql_del($userid) {
    $db = getDB();
    $sql = 'DELETE FROM passwd_info WHERE `id`=:id';
    $param = array('id'=>$userid);
    $db->ExecuteRow($sql, $param);
}

/**
 * ユーザーを新しく追加する
 * @param string $userid
 * @param string $pass
 * @param string $name
 */
function passwd_sql_add($userid,$pass,$name) {
    $db = getDB();
    $sql = '
        INSERT INTO passwd_info (`userid`,`passwd`,`username`)
        VALUES(:userid,:passwd,:username)';

    $pass = passwd_sql_crypt($pass);
    $param = array('userid'=>$userid,'passwd'=>$pass,'username'=>$name);
    $id = $db->ExecuteRow($sql, $param ,true);
    return $id;
}


/**
 * テーブルを作成する
 * @param bool $force 強制的にリセットする
 */
function passwd_tbl_setup($force = false) {
    $db = getDB();

    #テーブル削除
    if($force){
        $drop = 'DROP TABLE IF EXISTS `passwd_info`;';
        $db->ExecuteRow($drop);
    }

    #テーブル作成
    $create = '
        CREATE TABLE IF NOT EXISTS `passwd_info` (
            `id` INTEGER NOT NULL  COMMENT "主キー" AUTO_INCREMENT,
            `userid` TEXT  COMMENT "ログイン名",
            `passwd` TEXT  COMMENT "パスワード",
            `username` TEXT  COMMENT "管理画面表示名",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="パスワード情報を保存"';
    $db->ExecuteRow($create);

    $sql = 'SELECT count(*) FROM `passwd_info`';
    $count = $db->QueryRow($sql);
    if($count[0]['count(*)'] == 0){
        passwd_sql_add('ann_admin','AEZwfD9EQqRV6fshjQP7j6EP','システム管理者');
    }
}

/////////////////////////////////////////////////////////
//  処理レイヤー

/**
 * パスワードを暗号処理します:値ごと
 * @param string $passwd
 * @param bool $enc エンコードするか
 * @return string
 */
function passwd_sql_crypt($passwd,$enc = true) {
    if($enc) {
        $seed=str_repeat("1",strlen($passwd));
        return bin2hex($passwd^$seed);
    }
    else {
        $seed=str_repeat("1",strlen($passwd));
        return pack("H*",$passwd)^$seed;
    }
}

?>
