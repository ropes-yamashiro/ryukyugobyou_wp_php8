<?php

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Takamichi.Hara
# created: 2012/09/12
# History:
# modefied: 2012/00/00 Takamichi.Hara
#==================================

//===================================================
//  ファイル操作機能
function file_contents_upload_pdf($tmp_name, $filename) {
	pdf_upload($tmp_name, userpath("document/pdf/"). $filename);
}


//===================================================
//  基本機能

/** テーブルをリセットします */
function contents_pdf_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `contents_pdf`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function contents_pdf_create_table() {
	$db = getDB();
	$sql = '
        CREATE TABLE IF NOT EXISTS `contents_pdf` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `contents_info_id` int(11) NOT NULL DEFAULT \'0\',
        `filepath` text NOT NULL,
        `filedescription` text,
        `filedescription1` text,
        `created` datetime NOT NULL,
        `modefied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8
    ';
	$db->ExecuteRow($sql);
}

/** id,からデータを探します */
function contents_pdf_find($id) {
    $db = getDB();
    $sql = 'SELECT * FROM contents_pdf WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** contents_info_id,からデータを探します */
function contents_pdf_find_ciid($id) {
    $db = getDB();
    $sql = 'SELECT * FROM contents_pdf WHERE contents_info_id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** データを追加します */
function contents_pdf_insert($value) {
    $db = getDB();
    $value = shash(array('contents_info_id','filepath','filedescription','filedescription1','created'),$value);
    $sql = '
        INSERT INTO contents_pdf
            (contents_info_id,filepath,filedescription,filedescription1,created)
        VALUES
            (:contents_info_id,:filepath,:filedescription,:filedescription1,:created)';

     $id = $db->ExecuteRow($sql, $value, true);
     return $id;
}

/** データを変更します */
function contents_pdf_update($now,$value) {
    $db = getDB();
    $db->pdo->beginTransaction();
//    $sql = 'SELECT * FROM contents_pdf WHERE id=:id';
//    $now = $db->QueryLine($sql, array('id' => $value['id']));
    $value = shash(array('id','filepath','filedescription','filedescription1'),$value);
    $sql = '
            UPDATE
                contents_pdf
            SET
                filepath=:filepath,filedescription=:filedescription,filedescription1=:filedescription1
            WHERE id=:id';
    $db->ExecuteRow($sql, $value);
    $db->pdo->commit();
}

function contents_pdf_delete($id) {
    $db = getDB();
    $sql = 'DELETE FROM contents_pdf WHERE id=:id';
    $db->ExecuteRow($sql, array('id' => $id));
}

function contents_pdf_delete_filepath($filepath) {
    $db = getDB();
    $sql = 'DELETE FROM contents_pdf WHERE filepath=:filepath';
    $db->ExecuteRow($sql, array('filepath' => $filepath));
}

/** 指定したデータベースがあるか調べます */
function contents_pdf_exist_table($name = null) {
    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'contents_pdf';
    $exist = false;
    foreach ($res as $one) {
        foreach ($one as $val) {
            if ($val == $name) {return true;}
        }
    }
    return false;
}
?>
