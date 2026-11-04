<?php



/** id,からデータを探します */
function category_master_find($id){
    $db = getDB();
    $sql = 'SELECT * FROM category_master WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** contentsid,からデータを探します */
function category_master_find_by_contentsid($contentsid){
    $db = getDB();
    $sql = 'SELECT * from category_master Where contentsid=:contentsid';
    $tbl = $db->QueryRow($sql, array('contentsid' => $contentsid));
    return $tbl;
}

/**コンテンツ数を数える*/
function category_master_count($contentsid){
    $db = getDB();
    $sql = 'SELECT count(*) FROM category_master WHERE contentsid=:contentsid';
    return $db->Count($sql,array('contentsid'=>$contentsid));
}



/** データを追加します */
function category_master_insert($value){
    $db = getDB();
    $value = shash(array('contentsid','visible','sort','title','webtitle'),$value);
    $sql = '
        INSERT INTO category_master
            (contentsid,visible,sort,title,webtitle)
        VALUES
            (:contentsid,:visible,:sort,:title,:webtitle)';
     $id = $db->ExecuteRow($sql, $value, true);
     return $id;
}

/** データを変更します */
function category_master_update($value){
    $db = getDB();
    $db->pdo->beginTransaction();
    $sql = 'SELECT * FROM category_master WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $value['id']));
    $value = whash($now, $value);
    $value = shash(array('contentsid','visible','sort','title','webtitle','id'),$value);
    $sql = '
            UPDATE
                category_master
            SET
                contentsid=:contentsid,visible=:visible,
                sort=:sort,title=:title,webtitle=:webtitle
            WHERE id=:id';
    $db->ExecuteRow($sql, $value);
    $db->pdo->commit();
}



//===================================================
//  定義機能

/** ページング */
function category_master_limit($contentsid, $begin, $count = 20) {

    $db = getDB();
    $begin = abs(intval($begin));
    $count = abs(intval($count));
    $sql = 'SELECT * FROM category_master WHERE contentsid =:contentsid ORDER By sort DESC';
    return $db->QueryLimit($sql, $begin, $count, array('contentsid'=>$contentsid));
}

/** 上移動用 */
function category_master_limit_by_up_line($contentsid, $sort, $begin, $count = 2) {
    $db = getDB();
    $begin = abs(intval($begin));
    $count = abs(intval($count));
    $sql = 'SELECT * FROM category_master WHERE contentsid =:contentsid and sort >= :sort ORDER By sort ASC';
    return $db->QueryLimit($sql, $begin, $count, array('contentsid'=>$contentsid,'sort'=>$sort));
}

/** 下移動用 */
function category_master_limit_by_down_line($contentsid, $sort, $begin, $count = 2) {
    $db = getDB();
    $begin = abs(intval($begin));
    $count = abs(intval($count));
    $sql = 'SELECT * FROM category_master WHERE contentsid =:contentsid and sort <= :sort ORDER By sort DESC';
    return $db->QueryLimit($sql, $begin, $count, array('contentsid'=>$contentsid,'sort'=>$sort));
}


//===================================================
//  基本機能

/** テーブルをリセットします */
function category_master_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `category_master`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function category_master_create_table(){
    $db = getDB();
    $sql = '
    CREATE TABLE IF NOT EXISTS category_master (
        id INTEGER  UNSIGNED NOT NULL COMMENT "主キー" AUTO_INCREMENT
        ,contentsid INTEGER UNSIGNED COMMENT "コンテンツ番号"
        ,visible INTEGER UNSIGNED COMMENT "WEB表示"
        ,sort INTEGER  UNSIGNED COMMENT "ソート番号"
        ,title VARCHAR(512) COMMENT "タイトル"
        ,webtitle VARCHAR(512) COMMENT "表示用タイトル"
        ,PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
       DEFAULT CHARSET=utf8
       COMMENT="カテゴリーの設定情報"';
    $db->ExecuteRow($sql);
}




/** visible=trueなデータを探します */
function category_master_find_visible($contentsid){
    $db = getDB();
    $sql = 'SELECT * from category_master Where visible=1 and contentsid=:contentsid order by sort desc';
    $tbl = $db->QueryRow($sql, array('contentsid' => $contentsid));
    return $tbl;
}



/** idから削除する **/
function category_master_delete($id){
    $db = getDB();
    $sql = 'DELETE FROM category_master WHERE id=:id';
    $db->ExecuteRow($sql, array('id' => $id));
}

/** 指定したデータベースがあるか調べます */
function category_master_exist_table($name = null){

    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'category_master';
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
function category_master_null_member() {
    $db = getDB();
    $param = '@category_master';
    if ($GLOBALS[$param] == null) {
        $db = getDB();
        $obj = $db->getNullMember('category_master');
        $GLOBALS[$param] = $obj;
    }
    return $GLOBALS[$param];
}



?>
