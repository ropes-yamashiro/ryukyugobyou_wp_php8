<?php

define('SESSION_PATH','@session_file');

/**
 * セッションオープン
 * @param string $save_path
 * @param string $session_name
 * @return bool
 */
function session_my_open($save_path, $session_name) {
    $GLOBALS[SESSION_PATH] = $save_path;
    return true;
}

/**
 * セッション閉じ
 * @return bool
 */
function session_my_close() {
    return true;
}

/**
 * セッション読み
 * @param string $id
 * @return array
 */
function session_my_read($id) {
    $sess_file = $GLOBALS[SESSION_PATH] . "/sess_$id";
    wlog('read1','session');
    $ret = (file_exists($sess_file))
        ? unserialize(file_get_contents($sess_file)) : false;
    wlog('read2','session');
    return $ret;
}

/**
 * セッション書き
 * @param string $id
 * @param mixed $sess_data
 */
function session_my_write($id, $sess_data) {
    wlog('write1','session');
    $sess_file = $GLOBALS[SESSION_PATH] . "/sess_$id";
    file_put_contents($sess_file, serialize($sess_data));
    wlog('write2','session');
}

/**
 * セッション破壊
 */
function session_my_destroy($id) {
    $sess_file = $GLOBALS[SESSION_PATH] . "/sess_$id";
    if(file_exists($sess_file)) @unlink($sess_file);
}

/**
 * セッション時間切れ
 * @param int $maxlifetime
 * @return bool
 */
function session_my_gc($maxlifetime) {
    $sess_file = $GLOBALS[SESSION_PATH] . "/sess_*";
    foreach (glob($sess_file) as $filename) {
        if (filemtime($filename) + $maxlifetime < time()) {
            if(file_exists($filename)) @unlink($filename);
        }
    }
    return true;
}




/**
 * セッションを管理する
 */
class session {

    /**
     * セッションがスタートされていなければ開始する
     */
    public static function start() {
        if(!isset($_SESSION)) {
            if(USER_SESSION){
                session_set_save_handler(
                    "session_my_open", "session_my_close",
                    "session_my_read", "session_my_write",
                    "session_my_destroy", "session_my_gc");
            }
            session_start();
        }
    }

    /**
     * セッションをからにする
     */
    public static function destroy() {
        self::start();
        session_destroy();
    }

    /**
     * セッションから値を削除する
     * @param string $name
     */
    public static function remove($name) {
        self::start();
        unset($_SESSION[$name]);
    }

    /**
     * セッションに値を保存する
     * @param string $name
     * @param mixed $value
     */
    public static function set($name,$value) {
        self::start();
        $_SESSION[$name] = serialize($value);
    }
    /**
     * セッションから値を取得する
     */
    public static function get($name) {
        self::start();
        $ret =  unserialize($_SESSION[$name]);
        return $ret;
    }

    /**
     * セッションキーを書き換える
     */
    public static function renew() {
        self::start();
        foreach($_SESSION as $key =>$value) {
            $save[$key] = $value;
        }
        session_destroy();
        foreach($save as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
}

/**
 * セッションを管理する
 */
class cookie {

    /**
     * セッションをからにする
     */
    public static function destroy() {

    }

    public static function checkCookie() {
        if($_COOKIE === null) {

        }
    }

    public static function remove($name) {
        self::checkCookie();
        setcookie($name);
        unset($_COOKIE[$name]);
    }

    /**
     * セッションに値を保存する
     * @param string $name
     * @param mixed $value
     */
    public static function set($name,$value,$time = null) {
        self::checkCookie();
        setcookie($name,serialize($value),$time);
    }
    /**
     * セッションから値を取得する
     */
    public static function get($name) {
        return unserialize($_COOKIE[$name]);
    }
}
?>
