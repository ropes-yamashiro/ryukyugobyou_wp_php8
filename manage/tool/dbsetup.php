<?php

require_once("../lib/hashstr.php");
ini_set('display_errors', 'On');
error_reporting(E_WARNING);

escape_param();

if($_REQUEST['cmd'] == 'create') {
    $con = check_connect();
    if(count($con)) {
        $message = create_message($con);
    }
    else {
        $page = file_get_contents(dirname(__FILE__) . '/item/dbconf.php');
        $page = phash($_REQUEST, $page);
        file_put_contents(dirname(__FILE__) . '/../system/dbconf.php',$page);

        require_once('../system/init.php');
        $plug = getplugin();
        $data = array();
        foreach($plug->command as $cmd) {
            $func = "{$cmd}_tbl_setup";
            if(function_exists($func)) {
                $func();
            }
            #$data[$cmd] = $cmd . 'テーブルを追加しました';
        }
        $data['exit'] = '<span style="font-weight:bold;">テーブルの追加が完了しました</span><br><br><span style="font-weight:bold;color:red;">'
        . '「/manage/tool/dbsetup.php」を<br>アクセスできないようにするか、削除して下さい</span>';
        $message = create_message($data,false);
        
    }
}

if($_REQUEST['cmd'] == 'connect') {
    $err = check_connect();
    $message = create_message($err);
    if($message == null){
        $message = create_message(array('データベースとの接続に成功しました'));
    }
}

#===============================================================================

/**
 * 文字列エスケープ
 */
function escape_param(){
    foreach(array('dbhost','dbuser','dbpass','dbname') as $name){
        $_REQUEST[$name] = estr($_REQUEST[$name]);
    }
}

/**
 * メッセージ作成
 * @param array $array
 */
function create_message($data,$err = true) {
    if(count($data)) {
        foreach($data as $emes) {
            $mtotal .= $emes . "<br>";
        }
        $style = $err ? 'style="color:red"' : '';
        $message = "<p {$style}>{$mtotal}</p>";
    }
    return $message;
}

/**
 * 接続テスト
 * @return array
 */
function check_connect() {
    $err = array();
    if(!class_exists('PDO')) {
        $err['pdo'] = 'PDOライブラリがありません';

    }
    else {
	$dbhosts = explode(":", $_REQUEST['dbhost']);
	$port = "3306";
	if (count($dbhosts) >= 2)
	{
		$port = $dbhosts[1];
	}

	 $_REQUEST['dbhost'] = $dbhosts[0];
	 $_REQUEST['dbport'] = $port;

        $dsn = "mysql:dbname={$_REQUEST['dbname']};"
                . "host={$dbhosts[0]};port={$port};";

        try {
            $pdo = new PDO($dsn,$_REQUEST['dbuser'],$_REQUEST['dbpass']);
            $pdo->exec("SET NAMES 'utf-8'");
        }
        catch(Exception $e) {
            $mes = $e->getMessage();
            $err['connect'] = '接続に失敗しました: ' . $mes;
        }
    }
    return $err;
}

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <style type="text/css">
            BODY {
                font-family: Verdana, Arial, "MS UI Gothic", Osaka, Helvetica, sans-serif;
                background-color: #EEE;
                margin: 0px;
                padding-left: 20px;padding-right: 20px;
                background-image: none;
                font-size: 12px;
                text-align: left;
            }
        </style>
        <SCRIPT TYPE="text/javascript">
            <!--
            function createdb(){
                if(window.confirm('データベースを作成しますか？')){
                    document.formx.cmd.value = "create";
                    document.formx.submit();
                }
            }

            function connectdb(){
                if(window.confirm('データベースの接続テストを行いますか？')){
                    document.formx.cmd.value = "connect";
                    document.formx.submit();
                }
            }
            // -->
        </SCRIPT>
        <title>メニュー編集</title>
    </head>
    <body>
        <form name="formx" id="formx" method="post" >
            <?php echo $message ?>
            <p>データベース内のテーブルを自動生成します</p>
            <p style="width:300px;border-bottom:solid 1px #000"></p>
            <input type="hidden" name="cmd" />

            <p>データベース名:　<input type="text" name="dbname" value="<?php echo $_REQUEST['dbname'] ?>"></p>
            <p>ユーザー名:　<input type="text" name="dbuser" value="<?php echo $_REQUEST['dbuser'] ?>"></p>
            <p>パスワード名:　<input type="text" name="dbpass" value="<?php echo $_REQUEST['dbpass'] ?>"></p>
            <p>データベースのサーバーホスト:　
                <input type="text" name="dbhost" value="<?php echo $_REQUEST['dbhost'] ? $_REQUEST['dbhost'] : 'localhost'?>"></p>
            <p>
                <input type="button" value="接続テスト" onclick="return connectdb();" >
                <input type="button" value="設定出力" onclick="return createdb();" />
            </p>
        </form>
    </body>
</html>