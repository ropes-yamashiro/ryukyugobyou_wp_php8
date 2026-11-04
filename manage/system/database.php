<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

/**
 * データベースのクラスを取り出す
 * @return MySQLite
 */
function getDB(){
    $param = '@dbobj';
    #シングルトンでいいでしょ。
    #一度のアクセスでいちコネクションで済むように
    if($GLOBALS[$param] == null){
        $db = new MySQLite(new DATABASE_CONFIG());
        if($_REQUEST['ajaxmode']){
            $db->paramout = false;
            $db->sqlout = false;
            $db->retout = false;
        }
        else{
            $db->paramout = DB_PARAM_OUT;
            $db->sqlout   = DB_SQL_OUT;
            $db->retout   = DB_RET_OUT;
        }
        saveDB($db);
    }
    return $GLOBALS[$param];
}

/**
 * グローバルに保存
 * @param MySQLite $db
 */
function saveDB($db){
    $GLOBALS['@dbobj'] = $db;
}

/**
 * DBをAjaxモードにする
 */
function ajaxDBmode(){
    $db = getDB();
    $db->paramout = false;
    $db->sqlout = false;
    $db->retout = false;
    saveDB($db);
    return $db;
}

/**
 * DBオブジェクトのステータスを戻す
 * @return MySQLite
 */
function resetDB(){
    $db = getDB();
    $db->paramout = DB_PARAM_OUT;
    $db->sqlout   = DB_SQL_OUT;
    $db->retout   = DB_RET_OUT;
    saveDB($db);
    return $db;
}


?>