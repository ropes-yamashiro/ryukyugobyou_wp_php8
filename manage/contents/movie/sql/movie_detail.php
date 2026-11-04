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
function movie_detail_select_movie_id($infoid){
    $db = getDB();
    $sql = 'SELECT * FROM movie_detail WHERE movie_info_id =:infoid ORDER BY temp_id , value_id';
    return $db->QueryRow($sql,array('infoid'=>$infoid));
}

//===================================================
//  基本機能

/** データベースに接続します */
function movie_detail_sql(){
    throw new Exception("この関数使うな！！");
}

/** テーブルをリセットします */
function movie_detail_reset_table(){
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `movie_detail`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function movie_detail_create_table(){
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS `movie_detail` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT "主キー",
  `movie_info_id` int(10) unsigned default NULL COMMENT "動画管理基本情報",
  `data_data_type` int(10) unsigned default NULL COMMENT "データの種類",
  `value_id` int(10) unsigned default NULL COMMENT "値番号",
  `temp_id` int(10) unsigned default NULL COMMENT "テンプ番号",
  `value` text COMMENT "詳細部分 text/imgid/postion",
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT="動画管理の設定情報"';
    $db->ExecuteRow($sql);
}

/** id,からデータを探します */
function movie_detail_find($id){
    $db = getDB();
    $sql = 'SELECT * FROM movie_detail WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** データを追加します */
function movie_detail_insert($value){
    $db = getDB();
    $value = shash(array('movie_info_id','data_data_type','value_id','temp_id','value'),$value);
    $sql = '
        INSERT INTO movie_detail
            (movie_info_id,data_data_type,value_id,temp_id,value)
        VALUES
            (:movie_info_id,:data_data_type,:value_id,:temp_id,:value)';

     $id = $db->ExecuteRow($sql, $value, true);
     return $id;
}

/** データを変更します */
function movie_detail_update($value){
    $db = getDB();
    $db->pdo->beginTransaction();
    $sql = 'SELECT * FROM movie_detail WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $value['id']));
    $value = whash($now, $value);
    $value = shash(array('movie_info_id','data_data_type','value_id','temp_id','value','id'),$value);
    $sql = '
            UPDATE
                movie_detail
            SET
                movie_info_id=:movie_info_id,
                data_data_type=:data_data_type,
                value_id=:value_id,
                temp_id=:temp_id,
                value=:value
            WHERE id=:id';
    $db->ExecuteRow($sql, $value);
    $db->pdo->commit();
}

function movie_detail_delete($id){
    $db = getDB();
    $sql = 'DELETE FROM movie_detail WHERE id=:id';
    $db->ExecuteRow($sql, array('id' => $id));
}

/** 指定したデータベースがあるか調べます */
function movie_detail_exist_table($name = null){
    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'movie_detail';
    $exist = false;
    foreach ($res as $one) {
        foreach ($one as $val) {
            if ($val == $name) {return true;}
        }
    }
    return false;
}

?>
