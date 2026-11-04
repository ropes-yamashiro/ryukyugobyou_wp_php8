<?php
#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#    2009/03/09: confpathの改良
#==================================
#pear_check(true);


######## Customize Definittion 2013-04-24 K.Arikata ###########
define("DEFAULT_ID_MOVIE",	1);	//movieプラグイン初期pid

// PHP8: 未定義定数のFatal対策
if (!defined("OS_WINDOWS")) define("OS_WINDOWS", strtoupper(substr(PHP_OS, 0, 3)) === "WIN");
if (!defined("CURRENT_CHARACTERSET_PHP")) define("CURRENT_CHARACTERSET_PHP", "UTF-8");


###############################################################

sysload("dbconf.php");
sysload("serverconf.php");
sysload("plugin.php");
sysload("database.php");
sysload("mail.php");
sysload("log.php");

libload('redirect.php');

if (mb_strpos($_SERVER["REQUEST_URI"], "/manage") === FALSE) {
    $redirect = new Redirect();
}

libload('pdolapper.php');
libload("access.php");
libload("database.php");
libload("debug.php");
libload("errors.php");
libload("file.php");
libload("hashstr.php");
libload("htmltools.php");
libload("mail.php");
libload("net.php");
libload("reflection.php");
libload("time.php");
libload("parser.php");
libload("format.php");
libload("image.php");
libload("scape.php");
libload('log.php');
libload("fileuploader.php");
libload('temploader.php');
//--- START 2013-04-11 K.Arikata --------------
libload('sbfunc.php');
libload('sbfunc_html.php');
//--- END  --------------------------------------------------

libload("pdf.php");


#ログインは必須
userload('login/login.php');

#テスト・本番間でセッションを保たないようにする
session_position();


//==============================================================================
//  セッション
/**
 * テスト用と本番用でセッションを保たないようにする
 */
function session_position(){
    $position = session::get('@position') ;
    if($position == __FILE__){
        return ;
    }
    elseif($position == null){
        session::set('@position', __FILE__);
    }
    else{
        session::destroy();
    }
}


//=========================================================
//  PEAR 今のところ使用しない
#function pear_check($force = false) {
#    if(!file_exists_ex("PEAR.php") || $force) {
#        $path = sysroot() . "pear/";
#        set_include_path($path . PATH_SEPARATOR .get_include_path() );
#    }
#}

//=========================================================
//  システムパス関連

/**
 * システムフォルダのPHPを読込む
 * @param string $file
 */
function sysload($file) {
    require_once dirname(__FILE__) . '/' .$file;
}

/** userpageからリネーム ユーザーPHPを実行します */
function userload($file) {
    require_once(userpath($file));
}

/** userreadからリネーム ユーザーファイルの中身を取得します */
function userread($file) {
    return file_get_contents(userpath($file));
};

/** 設定ファイルのパスを取得します */
function confpath($file,$err = true) {
    $folder = realpath(dirname(__FILE__) . "/../config/" );
    $filepath = $folder  ."/" . $file;
    if(!file_exists($filepath) && $err){
        die($folder ."/" . $file . 'が見つかりません');
    }
    return $filepath;
}

/** userのフォルダを取得します。 */
function userpath($file) {
    $folder = realpath(dirname(__FILE__) . "/../contents/" );
    $filepath = $folder  ."/" . $file;
    return $filepath;
}

/** ライブラリフォルダのパスを取得します */
function libpath($file) {
    $folder = realpath(dirname(__FILE__) . "/../lib/" );
    $filepath = $folder  ."/" . $file;
    if(!file_exists($filepath)){
        die($folder ."/" . $file . 'が見つかりません');
    }
    return $filepath;
}

/** ログフォルダのパスを取得します */
function logpath($file) {
    $folder = realpath(dirname(__FILE__) . "/../log/" );
    $filepath = $folder  ."/" . $file;
    return $filepath;
}

/** ライブラリフォルダのPHPを実行します */
function libload($file) {
    require_once(libpath($file));
}

/** システムルートを呼出します */
function sysroot() {
    return realpath(dirname(__FILE__) . "/../") ."/" ;
}



//=========================================================
//  変数関連

/**
 * POSTを優先してデータを取ってくる
 */
function webparam($name,$default = null) {
    $ret = $_POST[$name] ?? ($_GET[$name] ?? null);
    return $ret !== null ? $ret : $default;
}

/** 親ページに渡す値を取得します。$name=nullの場合は、すべての値を取得 */
function webtopget($name = null){
    return $name == null ? $GLOBALS["@ret"] : $GLOBALS["@ret"][$name];
}

/** 親ページに渡す値を設定します */
function webtopset($name,$val,$add = false){
    $GLOBALS["@ret"][$name] = $add ? $GLOBALS["@ret"][$name] . $val : $val ;
}

/** セッションで有効な値を取得します */
function webvalget($name){
    return $_SESSION["@system"][$name];
}

/** セッションで有効な値を設定します */
function webvalset($name,$val,$add = false){
    $_SESSION["@system"][$name] = $add ? $_SESSION["@system"][$name]. $val : $val;
}

/** サイトの設定 */
function siteini($target){
    $confpath = confpath('site/site.ini');
    $info = parse_ini_file($confpath,true);
    return $info[$target];
}

function site_append($target,$action =null){
    $confpath = confpath('site/site.ini');
    $info = parse_ini_file($confpath,true);
    if($info[$target]){
        if(file_exists(userpath($info[$target]['file']))){
            userload($info[$target]['file']);
            if($action){
                if(function_exists($info[$target][$action])){
                    return $info[$target][$action];
                }
                else{
                    return false;
                }
            }
            else{
                $info;
            }
        }
    }
    return false;
}
//=========================================================
//  権限関連

/**
 * 権限をセッションに保存
 */
function websetauth($list) {
    session::set('@account',$list);
}

/**
 * 特定の権限キーを持っているか調べる
 * @param string $authkey 権限キー
 * @return bool
 */
function webauth($authkey) {
    return in_array($authkey,(array)session::get('@account')) ;
}

/**
 *  グローバルユーティリティ関数: include_pathを検索しつつfile_exists()する  *
 *  @param  string  $path               ファイル名
 *  @param  bool    $use_include_path   include_pathをチェックするかどうか
 *  @return bool    true:有り false:無し
 */
function file_exists_ex($path, $use_include_path = true) {
    if ($use_include_path == false) {
        return file_exists($path);
    }
    // check if absolute
    if (is_absolute_path($path)) {
        return file_exists($path);
    }
    $include_path_list = explode(PATH_SEPARATOR, get_include_path());
    if (is_array($include_path_list) == false) {
        return file_exists($path);
    }
    foreach ($include_path_list as $include_path) {
        if (file_exists($include_path . DIRECTORY_SEPARATOR . $path)) {
            return true;
        }
    }
    return false;
}


/**
 *  グローバルユーティリティ関数: 絶対パスかどうかを返す
 *  @param  string  $path               ファイル名
 *  @return bool    true:絶対 false:相対
 */
function is_absolute_path($path) {
    if (OS_WINDOWS) {
        if (preg_match('/^[a-z]:/i', $path) && $path[2] == DIRECTORY_SEPARATOR) {
            return true;
        }
    } else {
        if ($path[0] == DIRECTORY_SEPARATOR) {
            return true;
        }
    }
    return false;
}

/**
 * ファイルの拡張子を取り出す
 */
function extname($filename){
    $finfo = pathinfo($filename);
    return strtolower($finfo['extension']);
}

/**
*  グローバルユーティリティ関数: パスワード認証機能
*  @param  string  なし
*  @return string  エラーメッセージ
*/
function passwd_page_check() {
    require_once realpath(dirname(__FILE__)) ."/" . '../contents/hintpasswd/hintpasswd.php';
    $config = confpath("hintpasswd/config.ini");
    $conf = new cssConfig($config);

    $data = $conf->getSelector($_SERVER['SCRIPT_NAME']);
    $groupid = $data->getValue('pid');
    $cid = $data->getValue('cid');
    $pid = $data->getValue('passpid');

    // 入力されたパスワードのハッシュ値を取得
    $db = getDB();
    $pass = hintpasswd_sql_crypt($_POST['password']);
    $sql = "SELECT id FROM hintpasswd WHERE groupid = " . $pid . " AND hintpasswd = '" . $pass ."'";
    $res = $db->QueryRow($sql);

    // 入力されたパスワードが正なら、受験のヒントページへ遷移
    if (count($res) > 0) {
        $_SESSION['hint'] = 1;

        $jump = $_SERVER['HTTP_HOST'] . $data->getValue('tourl');
        if ($groupid != 0) {
            $jump .= '?pid=' . $groupid;
            if ($cid != 0) $jump .= '&cid=' . $cid;
        } elseif ($cid != 0) {
            $jump .= '?cid=' . $cid;
        }

        header("Location: http://". $jump);
        exit;
    }
    // 入力されたパスワードが誤っている場合、エラーメッセージを表示
    else {
        $err_msg = '<br /><span style="color: red; font-size: 85%;">パスワードが正しくありません。</span>';
        return $err_msg;
    }
}

// ------------------------------------------------------------------
// 画像リサイズ（サムネイル）
// ------------------------------------------------------------------
function resizerThub($filename, $type, $xsize) {

    list($width, $height) = getimagesize($filename);
    switch($type) {
        case    'jpg' :
        case    'jpeg' :
            $src = @imagecreatefromjpeg($filename);
            break;
        case    'png' :
            $src = @imagecreatefrompng($filename);
            break;
        case    'gif' :
            $src = @imagecreatefromgif($filename);
            break;
    }

    if ($width > $xsize) {
        // 元画像のアスペクト比
        $aspect = $width / $height;
        $ysize  = $xsize / $aspect;

        $dst = imagecreatetruecolor($xsize, $ysize);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $xsize, $ysize, $width, $height);
        imagejpeg($dst, $filename);
    }
}

?>