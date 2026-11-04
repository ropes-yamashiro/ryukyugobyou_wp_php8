<?php

#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#           2009/4/02 内部微調整
#==================================
//===================================================
//  ユーザー機能
//#edit

/** イメージを新規でアップロードする */
function file_contents_upload_new($parent, $filekey, $mx, $my) {

    $info = file_contents_upload($filekey, $mx, $my);
    $data = array('realfile' => $info['realfile'], 'savefile' => $info['savefile']
        , 'filekey' => $filekey, 'extension' => $info['extension'], 'parent' => $parent);
    $id = file_contents_insert($data);
    return $id;
}

/** 内部機能 */
function file_contents_upload($filekey, $mx = 800, $my = 600) {
    $upname = $_FILES[$filekey]['name'];
    $size = $_FILES[$filekey]['size'];
    $tmp_name = $_FILES[$filekey]['tmp_name'];
    $tmp_base = basename($tmp_name);
    $pathinfo = pathinfo($upname);

    $ext = strtolower($pathinfo['extension']);
    $savename = date("Y-m-d-H-i-s-") . $filekey . "-" . $tmp_base . "-" . mt_rand(1, 100);
    $filepath = userpath("document/save/") . $savename;
    imagesize($tmp_name, $filepath, $mx, $my, $ext);
    return array("savefile" => $savename, "realfile" => $upname, "extension" => $ext);
}

/** idを持つイメージを表示する */
function file_contents_view($id, $down = false, $cashe = false) {

    $data = file_contents_find($id);
    $realfile = $data['realfile'];
    $savefile = userpath('document/save/') . $data['savefile'];

    if($data['id'] == null){
        $err =  '<span style="color:red;">指定の記事は削除されています</span>';
        pout($err,'utf-8');
        exit();
    }

    if(!file_exists($savefile)){
        $err =  '<span style="color:red;">指定のファイルは削除されています</span>';
        pout($err,'utf-8');
        exit();
    }

    $uagent = $_SERVER['HTTP_USER_AGENT'];

    if (strstr($uagent, 'MSIE') && ! strstr($uagent, 'Opera')) {
        $realfile = mb_convert_encoding($realfile, 'UTF-8', "Shift-JIS");
        $realfile = urlencode($realfile);
    } else {
        $realfile = mb_convert_encoding($realfile, 'UTF-8', "Shift-JIS");
    }

    $type = getMIME($realfile);
    $attach = $down ? " attachment; " : "";


    header('Pragma: public');
    header('Content-Type: ' . $type);
    header('Content-Length: ' . filesize($savefile));
    header('Content-Disposition:' . $attach . 'filename="' . $realfile . '";');

    if ($cashe) {
        header('Cache-Control: max-age=' . 1800);
        if (session_id ()) {
            header('Expires: ' . date('D, d M Y H:i:s', time() + 1800) . ' GMT');
            header('Pragma: cache');
        }
    }
    $files = fopen($savefile, "r");
    fpassthru($files);
    exit();
}

/** idを持つデータのファイルの削除とデータのNULL化 */
function file_contents_clear_picdb($id, $done = null) {
    if ($done == null)
        $done = file_contents_find($id);
    $path = userpath('document/save/') . $done[savefile];
    @unlink($path);
    $done['savefile'] = "";
    $done['realfile'] = "";
    $done['extension'] = "";
    file_contents_update($done);
}

/** idを持つデータのファイルの削除とDBの削除 */
function file_contents_delete_picdb($id, $done = null) {
    if ($done == null) $done = file_contents_find($id);
    $path = userpath('document/save/') . $done['savefile'];
    @unlink($path);
    file_contents_delete($id);
}

/** parentを持つデータを削除します */
function file_contents_delete_parent($parent) {
    $data = file_contents_select_by_parent_id($parent);
    foreach ((array)$data as $done) {
        file_contents_delete_picdb($done['id'], $done);
    }
}

//#end
//===================================================
//  定義機能

/** parent_idでデータを探す */
function file_contents_select_by_parent_id($id, $debug = false) {
    $db = getDB();
    $sql = 'SELECT * FROM file_contents WHERE parent_id =:id ORDER BY id';
    return $db->QueryRow($sql,array('id'=>$id));
}

//===================================================
//  基本機能

/** データベースに接続します */
function file_contents_sql() {
    throw new Exception('この関数をつかうな！！');
}

/** テーブルをリセットします */
function file_contents_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `file_contents`;';
    $db->ExecuteRow($drop);
}

/** データベースを作成します */
function file_contents_create_table() {
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS file_contents (
	id INTEGER  UNSIGNED NOT NULL COMMENT "主キー" AUTO_INCREMENT
	,parent_id INTEGER  UNSIGNED COMMENT "保存元のデータ"
	,extension VARCHAR(16) COMMENT "拡張子名"
	,realfile VARCHAR(128) COMMENT "保存名の名前"
	,filekey VARCHAR(32) COMMENT "INPUTの名前"
	,savefile VARCHAR(128) COMMENT "保存時の名前"
	,PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
       DEFAULT CHARSET=utf8
       COMMENT="ブログのイメージ情報"';
    $db->ExecuteRow($sql);
}

/** id,からデータを探します */
function file_contents_find($id) {
    $db = getDB();
    $sql = 'SELECT * FROM file_contents WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $id));
    return $now;
}

/** データを追加します */
function file_contents_insert($value) {
    $db = getDB();
    $value = shash( array('parent_id','extension','realfile','filekey','savefile'),$value);
    $sql = '
        INSERT INTO file_contents
            (parent_id,extension,realfile,filekey,savefile)
        VALUES
            (:parent_id,:extension,:realfile,:filekey,:savefile)';

     unset($value['id']);
     $id = $db->ExecuteRow($sql, $value, true);
     return $id;
}

/** データを変更します */
function file_contents_update($value) {
    $db = getDB();
    $db->pdo->beginTransaction();
    $sql = 'SELECT * FROM file_contents WHERE id=:id';
    $now = $db->QueryLine($sql, array('id' => $value['id']));
    $value = whash($now, $value);
    $value = shash(array('parent_id','extension','realfile','filekey','savefile','id'),$value);
    $sql = '
            UPDATE
                file_contents
            SET
               parent_id=:parent_id,extension=:extension,realfile=:realfile,
               filekey=:filekey,savefile=:savefile
            WHERE id=:id';
    $db->ExecuteRow($sql, $value);
    $db->pdo->commit();
}

function file_contents_delete($id) {
    $db = getDB();
    $sql = 'DELETE FROM file_contents WHERE id=:id';
    $db->ExecuteRow($sql,array('id'=>$id));
}

/** 指定したデータベースがあるか調べます */
function file_contents_exist_table($name = null) {
    $db = getDB();
    $sql = "SHOW TABLES";
    $res = $db->QueryRow($sql);
    if ($name == null)
        $name = 'file_contents';
    $exist = false;
    foreach ($res as $one) {
        foreach ($one as $val) {
            if ($val == $name) {return true; }
        }
    }
    return false;
}
?>
