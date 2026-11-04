<?php

/**
 * ログを記述する
 * @param string $text
 * @param string $prefix 
 */
function wlog($text,$prefix = null){
    if(LOGWRITE === true){
        $log = getLog();
        $log->write($text, $prefix);
    }
}

/**
 * データベースのクラスを取り出す
 * @return log
 */
function getLog(){
    #一度のアクセスでいちコネクションで済むように
    $param = '@logobj';
    if($GLOBALS[$param] == null){
        $filename =
        $obj = new log(logpath(date('Y-m') . '.log'));
        $GLOBALS[$param] = $obj;
        $obj->write("\n--------------------------------");
    }
    return  $GLOBALS[$param];
}
?>
