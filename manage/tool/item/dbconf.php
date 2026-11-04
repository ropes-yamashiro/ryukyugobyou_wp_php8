<?php

/**
 * SQL文を表示するか
 */
define('DB_SQL_OUT',false);

/**
 * SQL文を表示するか
 */
define('DB_PARAM_OUT',false);

/**
 * SQLの結果を表示するか
 */
define('DB_RET_OUT',false);


/**
 * データベース設定
 */
class DATABASE_CONFIG {
    public $default = array(
            'driver' => 'mysql',
            'host' => '{dbhost}',
            'port' => '{dbport}',
            'login' => '{dbuser}',
            'password' => '{dbpass}',
            'database' => '{dbname}',
            'encoding' => 'utf8'
    );
}


?>
