<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
//===================================================
//  ユーザー機能

/** データを検索する */
function contents_detail_select_contents_id($infoid){
    $db = getDB();
    $sql = 'SELECT * FROM contents_detail WHERE contents_info_id =:infoid ORDER BY temp_id , value_id';
    return $db->QueryRow($sql,array('infoid'=>$infoid));
}

//===================================================
//  基本機能

/** データベースに接続します */
function contents_detail_sql(){
    throw new Exception("この関数使うな！！");
}

/** テーブルをリセットします */
function contents_detail_reset_table(){
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `contents_detail`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function contents_detail_create_table(){
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS contents_detail (
	id INTEGER  UNSIGNED NOT NULL COMMENT "主キー" AUTO_INCREMENT
	,contents_info_id INTEGER UNSIGNED COMMENT "ブログ基本情報"
	,data_data_type INTEGER  UNSIGNED COMMENT "データの種類"
	,value_id INTEGER  UNSIGNED COMMENT "値番号"
	,temp_id INTEGER  UNSIGNED COMMENT "テンプ番号"
	,value TEXT COMMENT "詳細部分 text/imgid/postion"
	,PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
       DEFAULT CHARSET=utf8
       COMMENT="ブログの設定情報"';
    $db->ExecuteRow($sql);
}

/** id,からデータを探します */
function contents_detail_find($id){
    $db = getDB();
    $sql = 'SELECT * FROM contents_detail WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** データを追加します */
function contents_detail_insert($value){
    $db = getDB();
    $value = shash(array('contents_info_id','data_data_type','value_id','temp_id','value'),$value);
    $sql = '
        INSERT INTO contents_detail
            (contents_info_id,data_data_type,value_id,temp_id,value)
        VALUES
            (:contents_info_id,:data_data_type,:value_id,:temp_id,:value)';

     $id = $db->ExecuteRow($sql, $value, true);
     return $id;
}

/** データを変更します */
function contents_detail_update($value){
    $db = getDB();
    $db->pdo->beginTransaction();
    $sql = 'SELECT * FROM contents_detail WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $value['id']));
    $value = whash($now, $value);
    $value = shash(array('contents_info_id','data_data_type','value_id','temp_id','value','id'),$value);
    $sql = '
            UPDATE
                contents_detail
            SET
                contents_info_id=:contents_info_id,
                data_data_type=:data_data_type,
                value_id=:value_id,
                temp_id=:temp_id,
                value=:value
            WHERE id=:id';
    $db->ExecuteRow($sql, $value);
    $db->pdo->commit();
}

function contents_detail_delete($id){
    $db = getDB();
    $sql = 'DELETE FROM contents_detail WHERE id=:id';
    $db->ExecuteRow($sql, array('id' => $id));
}

/** 指定したデータベースがあるか調べます */
function contents_detail_exist_table($name = null){
    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'contents_detail';
    $exist = false;
    foreach ($res as $one) {
        foreach ($one as $val) {
            if ($val == $name) {return true;}
        }
    }
    return false;
}

?>
