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

/* *コンテンツ数を数える */
function movie_info_count_group_id($groupid, $debug = false) {
    $db = getDB();
    $sql = 'SELECT count(*) FROM movie_info WHERE group_id =:groupid';
    return $db->Count($sql,array('groupid'=>$groupid));
}

/** ページング */
function movie_info_limit_by_group_id($groupid, $begin, $count = 20) {
    $db = getDB();
    $begin = abs(intval($begin));
    $count = abs(intval($count));
    $sql = 'SELECT * FROM movie_info WHERE group_id =:groupid ORDER By sort DESC';
    return $db->QueryLimit($sql, $begin, $count, array('groupid'=>$groupid));
}

/** 上移動用 */
function movie_info_limit_by_up_line($groupid, $sort, $begin, $count = 2) {
    $db = getDB();
    $begin = abs(intval($begin));
    $count = abs(intval($count));
    $sql = 'SELECT * FROM movie_info WHERE group_id =:groupid and sort >= :sort ORDER By sort ASC';
    return $db->QueryLimit($sql, $begin, $count, array('groupid'=>$groupid,'sort'=>$sort));
}

/** 下移動用 */
function movie_info_limit_by_down_line($groupid, $sort, $begin, $count = 2) {
    $db = getDB();
    $begin = abs(intval($begin));
    $count = abs(intval($count));
    $sql = 'SELECT * FROM movie_info WHERE group_id =:groupid and sort <= :sort ORDER By sort DESC';
    return $db->QueryLimit($sql, $begin, $count, array('groupid'=>$groupid,'sort'=>$sort));
}

/** ユーザー用表示 */
function movie_info_limit_by_user($groupid, $viewdate, $cid, $begin, $count = 5) {
    $db = getDB();
    $begin = abs(intval($begin));
    $count = abs(intval($count));

    if ( $cid == 0 ) {
        $category_id = "";
        $join = " left outer join category_info ca on co.id = ca.movie_info_id ";
        $param = array('groupid'=>$groupid,'viewdate'=>$viewdate);
    } else {
        $category_id =  "and ca.category_id = :cid and co.id = ca.movie_info_id";
        $join = " , category_info ca ";
        $param = array('groupid'=>$groupid,'viewdate'=>$viewdate,'cid'=>$cid);
    }
    $sql = 'select distinct co.* from movie_info co '.$join.' where co.group_id = :groupid and co.visible = 1 and co.view_date <= :viewdate '.$category_id.'  ORDER By sort DESC';
    return $db->QueryLimit($sql, $begin, $count, $param);
}

/** ユーザー用表示 */
function movie_info_count_by_user($groupid, $viewdate,$cid) {
    $db = getDB();

    if ( $cid == 0 ) {
        $category_id = "";
        $join = " left outer join category_info ca on co.id = ca.movie_info_id ";
        $param = array('groupid'=>$groupid,'viewdate'=>$viewdate);
    } else {
        $category_id =  "and ca.category_id = :cid and co.id = ca.movie_info_id";
        $join = " , category_info ca ";
        $join_id = "";
        $param = array('groupid'=>$groupid,'viewdate'=>$viewdate,'cid'=>$cid);
    }
    $sql = 'select distinct co.* from movie_info co '.$join.' where co.group_id = :groupid and co.visible = 1 and co.view_date <= :viewdate '.$category_id;
    $row = $db->QueryRow($sql, $param);

    return count($row);
}

/** アンカー用 */
function movie_info_select_by_user($groupid, $viewdate = "") {
    $db = getDB();
    $sql = 'SELECT * FROM movie_info WHERE group_id =:groupid ORDER By sort DESC ';
    return $db->QueryRow($sql,array('groupid'=>$groupid));
}

/** トピック用 */
function movie_info_select_by_topic($groupid) {
    $db = getDB();
    $sql = 'SELECT * FROM movie_info WHERE group_id =:id and visible = 1 ORDER BY sort DESC';
    return $db->QueryRow($sql,array('id'=>$groupid));
}

/** トピック画像用 */
function movie_info_select_by_topicimg($groupid) {
    $db = getDB();
    $sql = 'SELECT * FROM movie_info WHERE group_id =:id and visible = 1 ORDER BY sort DESC';
    return $db->QueryRow($sql,array('id'=>$groupid));
}

/** お客様の声用 */
function movie_info_select_by_customer($groupid) {
    $db = getDB();
    $sql = 'SELECT * FROM movie_info WHERE group_id =:id and visible = 1 ORDER BY sort DESC';
    return $db->QueryRow($sql,array('id'=>$groupid));
}

/** アイコン画像用 */
function movie_info_select_by_icon($groupid) {
    $db = getDB();
    $sql = 'SELECT * FROM movie_info WHERE group_id =:id and visible = 1 ORDER BY sort DESC';
    return $db->QueryRow($sql,array('id'=>$groupid));
}

/* *すべてのデータを取り出す */

function movie_info_select_all() {
    $sql = movie_info_sql();
    $db = getDB();
    $sql = 'SELECT * FROM movie_info';
    return $db->QueryRow($sql);
}

//===================================================
//  基本機能

/** データベースに接続します */
function movie_info_sql() {
    throw new Exception('この関数をつかうな！！');
}

/** テーブルをリセットします */
function movie_info_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `movie_info`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function movie_info_create_table() {
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS `movie_info` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT "主キー",
  `group_id` int(10) unsigned default NULL COMMENT "グループ番号",
  `visible` int(10) unsigned default NULL COMMENT "表示・非表示",
  `sort` int(10) unsigned default NULL COMMENT "ソート番号",
  `title` text COMMENT "タイトル",
  `content_temp_id` int(10) unsigned default NULL COMMENT "詳細部分のid",
  `view_date` varchar(20) default NULL COMMENT "表示日付",
  `summary` text COMMENT "サマリー",
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT="動画管理の基本情報部分";';
    $db->ExecuteRow($sql);
}

/** id,からデータを探します */
function movie_info_find($id) {
    $db = getDB();
    $sql = 'SELECT * FROM movie_info WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** データを追加します */
function movie_info_insert($value) {
    $db = getDB();
    $value = shash(array('group_id','visible','sort','title','content_temp_id','view_date','summary'),$value);
    $sql = '
        INSERT INTO movie_info
            (group_id,visible,sort,title,content_temp_id,view_date,summary)
        VALUES
            (:group_id,:visible,:sort,:title,:content_temp_id,:view_date,:summary)';

     $id = $db->ExecuteRow($sql, $value, true);
     return $id;
}

/** データを変更します */
function movie_info_update($value) {
    $db = getDB();
    $db->pdo->beginTransaction();
    $sql = 'SELECT * FROM movie_info WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $value['id']));
    $value = whash($now, $value);
    $value = shash(array('group_id','visible','sort','title','content_temp_id','view_date','summary','id'),$value);
    $sql = '
            UPDATE
                movie_info
            SET
                group_id=:group_id,visible=:visible,
                sort=:sort,title=:title,content_temp_id=:content_temp_id,
                view_date=:view_date,summary=:summary
            WHERE id=:id';
    $db->ExecuteRow($sql, $value);
    $db->pdo->commit();
}

function movie_info_delete($id) {
    $db = getDB();
    $sql = 'DELETE FROM movie_info WHERE id=:id';
    $db->ExecuteRow($sql, array('id' => $id));
}

/** 指定したデータベースがあるか調べます */
function movie_info_exist_table($name = null) {
    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'movie_info';
    $exist = false;
    foreach ($res as $one) {
        foreach ($one as $val) {
            if ($val == $name) {return true;}
        }
    }
    return false;
}
?>
