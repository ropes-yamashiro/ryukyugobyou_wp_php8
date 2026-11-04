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
            'host' => 'mysql10010.xserver.jp',
            'port' => '3306',
            'login' => 'xs999786_cms',
            'password' => 'f8rejkm521g1',
            'database' => 'xs999786_cms',
            'encoding' => 'utf8'
    );
}


?>
