<?php

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
//===================================================
//  定義機能

/* *データをすべて取り出します */
function moviebox_info_select_all() {
    $db = getDB();
    $sql = 'SELECT * FROM moviebox_info';
    return $db->QueryRow($sql);
}

/** コンテナIDで取り出します */
function moviebox_info_select_moviebox_temp_id($id) {
    $db = getDB();
    #$sql = 'SELECT * FROM moviebox_info WHERE moviebox_temp_id =:moviebox_id';
    $sql = 'SELECT * FROM moviebox_info WHERE id=:id';
    #$tbl = $db->QueryRow($sql, array('moviebox_id' => $movieboxid));
    $tbl = $db->QueryRow($sql, array('id' => $id));
    return $tbl;
}

//===================================================
//  基本機能

/** データベースに接続します */
function moviebox_info_sql() {
    throw new Exception("この関数使うな");
}

/** テーブルをリセットします */
function moviebox_info_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `moviebox_info`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function moviebox_info_create_table() {
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS `moviebox_info` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT "主キー",
  `title` varchar(512) default NULL COMMENT "タイトル部分",
  `group_id` int(10) unsigned default NULL COMMENT "グループ番号",
  `moviebox_temp_id` int(10) unsigned default NULL COMMENT "コンテナ番号",
  `paging_temp_id` int(10) unsigned default NULL COMMENT "ページング種類",
  `paging_count` int(10) unsigned default NULL COMMENT "ページングに表示する値",
  `paging_count_sp` int(10) unsigned default NULL COMMENT "ページングに表示する値(SP)",
  `list_count` int(10) unsigned default NULL COMMENT "リスト番号",
  `list_count_sp` int(10) unsigned default NULL COMMENT "リスト番号(SP)",
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT="動画管理の設定情報";';
    $db->ExecuteRow($sql);
}

/** id,からデータを探します */
function moviebox_info_find($id) {
    $db = getDB();
    $sql = 'SELECT * FROM moviebox_info WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** カテゴリー表示用データを探します */
function moviebox_info_find_for_category() {
    $db = getDB();
    $sql = 'SELECT * from moviebox_info order by id';
    return $db->QueryRow($sql,array());
}

/** データを追加します */
function moviebox_info_insert($value) {
    $db = getDB();

    $value = shash(array('group_id','title','moviebox_temp_id','paging_temp_id','paging_count_sp','list_count_sp','paging_count','list_count'),$value);

    $sql = '
        INSERT INTO moviebox_info
            (group_id,title,moviebox_temp_id,paging_temp_id,paging_count,list_count,paging_count_sp,list_count_sp)
        VALUES
            (:group_id,:title,:moviebox_temp_id,:paging_temp_id,:paging_count,:list_count,:paging_count_sp,:list_count_sp)';

    $null = moviebox_info_null_member();
    $value = whash($null, $value);
    unset($value['id']);

    $id = $db->ExecuteRow($sql, $value, true);
    return $id;
}

/** データを変更します */
function moviebox_info_update($value, $debug = false) {
    $db = getDB();
    $db->pdo->beginTransaction();
    $sql = 'SELECT * FROM moviebox_info WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $value['id']));
    $value = whash($now, $value);
    $value = shash(array('group_id','title','moviebox_temp_id','paging_temp_id','paging_count_sp','list_count_sp','paging_count','list_count','id'),$value);
    $sql = '
            UPDATE
                moviebox_info
            SET
                group_id=:group_id,
                title=:title,
                moviebox_temp_id=:moviebox_temp_id,
                paging_temp_id=:paging_temp_id,
                paging_count=:paging_count,
                list_count=:list_count,
                paging_count_sp=:paging_count_sp,
                list_count_sp=:list_count_sp
    WHERE id=:id';
    $db->ExecuteRow($sql, $value);
    $db->pdo->commit();
}

function moviebox_info_delete($id) {
    $db = getDB();
    $sql = 'DELETE FROM moviebox_info WHERE id=:id';
    $db->ExecuteRow($sql, array('id' => $id));
}

/** 指定したデータベースがあるか調べます */
function moviebox_info_exist_table($name = null) {
    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'moviebox_info';
    $exist = false;
    foreach ($res as $one) {
        foreach ($one as $val) {
            if ($val == $name) {return true;            }
        }
    }
    return false;
}

/**
 * メンバーのNULL値の取得
 */
function moviebox_info_null_member() {
    $db = getDB();
    $param = '@moviebox_info';
    if ($GLOBALS[$param] == null) {
        $db = getDB();
        $obj = $db->getNullMember('moviebox_info');
        $GLOBALS[$param] = $obj;
    }
    return $GLOBALS[$param];
}
?>
