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
function container_info_select_all() {
    $db = getDB();
    $sql = 'SELECT * FROM container_info';
    return $db->QueryRow($sql);
}

/** コンテナIDで取り出します */
function container_info_select_container_temp_id($id) {
    $db = getDB();
    #$sql = 'SELECT * FROM container_info WHERE container_temp_id =:container_id';
    $sql = 'SELECT * FROM container_info WHERE id=:id';
    #$tbl = $db->QueryRow($sql, array('container_id' => $containerid));
    $tbl = $db->QueryRow($sql, array('id' => $id));
    return $tbl;
}

//===================================================
//  基本機能

/** データベースに接続します */
function container_info_sql() {
    throw new Exception("この関数使うな");
}

/** テーブルをリセットします */
function container_info_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `container_info`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function container_info_create_table() {
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS container_info (
	id INTEGER  UNSIGNED NOT NULL COMMENT "主キー" AUTO_INCREMENT
        ,title VARCHAR(512) COMMENT "タイトル部分"
        ,group_id INTEGER UNSIGNED COMMENT "グループ番号"
	,container_temp_id INTEGER UNSIGNED COMMENT "コンテナ番号"
	,paging_temp_id INTEGER  UNSIGNED COMMENT "ページング種類"
	,paging_count INTEGER  UNSIGNED COMMENT "ページングに表示する値"
	,paging_count_sp INTEGER  UNSIGNED COMMENT "ページングに表示する値(SP)"
    ,list_count INTEGER  UNSIGNED COMMENT "リスト番号"
    ,list_count_sp INTEGER  UNSIGNED COMMENT "リスト番号(SP)"
    ,PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
       DEFAULT CHARSET=utf8
       COMMENT="ブログの設定情報"';
    $db->ExecuteRow($sql);
}

/** id,からデータを探します */
function container_info_find($id) {
    $db = getDB();
    $sql = 'SELECT * FROM container_info WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** カテゴリー表示用データを探します */
function container_info_find_for_category() {
    $db = getDB();
    $sql = 'SELECT * from container_info order by id';
    return $db->QueryRow($sql,array());
}

/** データを追加します */
function container_info_insert($value) {
    $db = getDB();

    $value = shash(array('group_id','title','container_temp_id','paging_temp_id','paging_count_sp','list_count_sp','paging_count','list_count'),$value);
    $sql = '
        INSERT INTO container_info
            (group_id,title,container_temp_id,paging_temp_id,paging_count,list_count,paging_count_sp,list_count_sp)
        VALUES
            (:group_id,:title,:container_temp_id,:paging_temp_id,:paging_count,:list_count,:paging_count_sp,:list_count_sp)';

    $null = container_info_null_member();
    $value = whash($null, $value);
    unset($value['id']);

    $id = $db->ExecuteRow($sql, $value, true);
    return $id;
}

/** データを変更します */
function container_info_update($value, $debug = false) {
    $db = getDB();
    $db->pdo->beginTransaction();
    $sql = 'SELECT * FROM container_info WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $value['id']));
    $value = whash($now, $value);
    $value = shash(array('group_id','title','container_temp_id','paging_temp_id','paging_count_sp','list_count_sp','paging_count','list_count','id'),$value);
    $sql = '
            UPDATE
                container_info
            SET
                group_id=:group_id,
                title=:title,
                container_temp_id=:container_temp_id,
                paging_temp_id=:paging_temp_id,
                paging_count=:paging_count,
                list_count=:list_count,
                paging_count_sp=:paging_count_sp,
                list_count_sp=:list_count_sp
    WHERE id=:id';
    $db->ExecuteRow($sql, $value);
    $db->pdo->commit();
}

function container_info_delete($id) {
    $db = getDB();
    $sql = 'DELETE FROM container_info WHERE id=:id';
    $db->ExecuteRow($sql, array('id' => $id));
}

/** 指定したデータベースがあるか調べます */
function container_info_exist_table($name = null) {
    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'container_info';
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
function container_info_null_member() {
    $db = getDB();
    $param = '@container_info';
    if ($GLOBALS[$param] == null) {
        $db = getDB();
        $obj = $db->getNullMember('container_info');
        $GLOBALS[$param] = $obj;
    }
    return $GLOBALS[$param];
}
?>
