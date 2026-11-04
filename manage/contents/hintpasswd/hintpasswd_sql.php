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

/**すべてのユーザー名を取得します hash(userid,hintpasswd,name)+ or null */
function hintpasswd_sql_all() {
    $db = getDB();
    $sql = 'SELECT * FROM hintpasswd_info';
    $tbl = $db->QueryRow($sql);

    $ret = array();
    foreach($tbl as $row) {
        $row['hintpasswd'] = hintpasswd_sql_crypt($row['hintpasswd'],false);
        $ret[] = $row;
    }
    return $ret;
}

/**
 * ユーザーIDから情報を取得します
 * @param int $userid
 * @return array hash(userid,hintpasswd,name)
 */
function hintpasswd_sql_info_by_userid($userid) {
    $db = getDB();
    $sql = 'SELECT * FROM hintpasswd WHERE userid = :userid';
    $tbl = $db->QueryRow($sql,array('userid'=>$userid));

    if(count($tbl) == 0) return null;
    $row = $tbl[0];
    $row['hintpasswd'] = hintpasswd_sql_crypt($row['hintpasswd'],false);
    return $row;
}

/**
 * IDから情報を取得します
 * @param int $id
 * @return array hash(userid,hintpasswd,name)
 */
function hintpasswd_sql_info_by_id($id) {
    $db = getDB();
    $sql = 'SELECT * FROM hintpasswd WHERE `id` = :id';
    $tbl = $db->QueryRow($sql,array('id'=>$id));

    if(count($tbl) == 0) return null;
    $row = $tbl[0];
    $row['hintpasswd'] = hintpasswd_sql_crypt($row['hintpasswd'],false);
    return $row;
}

/**
 * ページ番号・表示数から情報を取得します
 * @param int $pageno 1から始まるページ番号
 * @param int $count 一度に表示する項目数
 * @return array hash(userid,hintpasswd,name)
 */
function hintpasswd_sql_infos($pageno,$count) {
    $db = getDB();
    $top = intval(($pageno - 1) * $count);
    $count = intval($count);
    $sql = 'SELECT * FROM hintpasswd ORDER BY `id` desc limit '. "{$top},{$count}";
    $tbl = $db->QueryRow($sql);

    $ret = array();
    foreach($tbl as $row) {
        $row['hintpasswd'] = hintpasswd_sql_crypt($row['hintpasswd'],false);
        $ret[] = $row;
    }
    return $ret;
}

/**
 * ユーザー数を取得します
 */
function hintpasswd_sql_usercount() {
    $db = getDB();
    $sql = 'SELECT count(*) FROM hintpasswd';
    $tbl = $db->QueryRow($sql);
    return $tbl[0]['count(*)'];
}


/////////////////////////////////////////////////////////
//  編集関数

/**
 * データを変更
 */
function hintpasswd_sql_change($newid,$pass,$name,$id) {
    $db = getDB();
    $pass = hintpasswd_sql_crypt($pass);
    $param = array('id'=>$id,'userid'=>$newid,'hintpasswd'=>$pass,'username'=>$name);
    $sql = 'UPDATE hintpasswd SET `userid` = :userid,`hintpasswd`=:hintpasswd,`username`=:username WHERE `id`=:id';
    $db->ExecuteRow($sql, $param);
}

/**
 * データを削除
 * @param string $userid
 */
function hintpasswd_sql_del($userid) {
    $db = getDB();
    $sql = 'DELETE FROM hintpasswd WHERE `id`=:id';
    $param = array('id'=>$userid);
    $db->ExecuteRow($sql, $param);
}

/**
 * ユーザーを新しく追加する
 * @param string $userid
 * @param string $pass
 * @param string $name
 */
function hintpasswd_sql_add($userid,$pass,$name) {
    $db = getDB();
    $sql = '
        INSERT INTO hintpasswd (`userid`,`hintpasswd`,`username`,`groupid`)
        VALUES(:userid,:hintpasswd,:username,:groupid)';

    $pass = hintpasswd_sql_crypt($pass);
    $param = array('userid'=>$userid,'hintpasswd'=>$pass,'username'=>$name,'groupid'=>webparam('id'));
    $id = $db->ExecuteRow($sql, $param ,true);
    return $id;
}


/**
 * テーブルを作成する
 * @param bool $force 強制的にリセットする
 */
function hintpasswd_tbl_setup($force = false) {
    $db = getDB();

    #テーブル削除
    if($force){
        $drop = 'DROP TABLE IF EXISTS `hintpasswd_info`;';
        $db->ExecuteRow($drop);
    }

    #テーブル作成
    $create = '
        CREATE TABLE IF NOT EXISTS `hintpasswd_info` (
            `id` INTEGER NOT NULL  COMMENT "主キー" AUTO_INCREMENT,
            `name` VARCHAR(20) COMMENT "名称",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="パスワード情報を保存"';
    $db->ExecuteRow($create);

    #テーブル作成 コンテンツ中身
    $create_tbl_contents = '
        CREATE TABLE IF NOT EXISTS `hintpasswd` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT COMMENT "主キー" ,
            `groupid` INTEGER NOT NULL COMMENT "メニュー番号",
            `userid` TEXT  COMMENT "ログイン名",
            `hintpasswd` TEXT  COMMENT "パスワード",
            `username` TEXT  COMMENT "管理画面表示名",
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="お客様の声コンテンツを保存"';

    $db->ExecuteRow($create_tbl_contents);

    $sql = 'SELECT count(*) FROM `hintpasswd`';
    $count = $db->QueryRow($sql);
}

/////////////////////////////////////////////////////////
//  処理レイヤー

/**
 * パスワードを暗号処理します:値ごと
 * @param string $hintpasswd
 * @param bool $enc エンコードするか
 * @return string
 */
function hintpasswd_sql_crypt($hintpasswd,$enc = true) {
    if($enc) {
        $seed=str_repeat("1",strlen($hintpasswd));
        return bin2hex($hintpasswd^$seed);
    }
    else {
        $seed=str_repeat("1",strlen($hintpasswd));
        return pack("H*",$hintpasswd)^$seed;
    }
}

?>