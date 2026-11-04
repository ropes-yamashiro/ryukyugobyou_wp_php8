<?php


//===================================================
//  基本機能

/** テーブルをリセットします */
function category_info_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `category_info`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function category_info_create_table(){
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS category_info (
	id INTEGER  UNSIGNED NOT NULL COMMENT "主キー" AUTO_INCREMENT
    ,category_id INTEGER UNSIGNED COMMENT "カテゴリーID"
	,contents_info_id INTEGER UNSIGNED COMMENT "コンテンツID"
	,PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
       DEFAULT CHARSET=utf8
       COMMENT="ブログのカテゴリー情報"';
    $db->ExecuteRow($sql);
}

/** contents_info_id,からデータを探します */
function category_info_find_by_category_info_id($contents_info_id){
    $db = getDB();
    $sql = 'SELECT * from category_info Where contents_info_id=:contents_info_id';
    $tbl = $db->QueryRow($sql, array('contents_info_id' => $contents_info_id));
    return $tbl;

}

/** データを追加します */
function category_info_insert($value){
    $db = getDB();
    $value = shash(array('category_id','contents_info_id'),$value);
    $sql = '
        INSERT INTO category_info
            (category_id,contents_info_id)
        VALUES
            (:category_id,:contents_info_id)';
    $null = category_info_null_member();
    $value = whash($null, $value);
    unset($value['id']);

    $id = $db->ExecuteRow($sql, $value, true);
    return $id;
}

function category_info_delete_by_contents_info_id($contents_info_id){
    $db = getDB();
    $sql = 'DELETE from category_info Where contents_info_id=:contents_info_id';
    $db->ExecuteRow($sql, array('contents_info_id' => $contents_info_id));
}

/** 指定したデータベースがあるか調べます */
function category_info_exist_table($name = null){
    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'category_info';
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
function category_info_null_member() {
    $db = getDB();
    $param = '@category_info';
    if ($GLOBALS[$param] == null) {
        $db = getDB();
        $obj = $db->getNullMember('category_info');
        $GLOBALS[$param] = $obj;
    }
    return $GLOBALS[$param];
}


?>
