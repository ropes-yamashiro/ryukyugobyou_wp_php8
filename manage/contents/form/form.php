<?php

#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History: 2009/04/21 プルダウンのラベルに当たる部分の値の削除
#==================================

/** 値をクッキーに保存します */
function form_setcookie($info) {
    $cookies = $info['cookie'];
    if ($cookies == null)
        return;
    foreach ($cookies as $cone) {
        if (substr($cone, 0, 1) == "@")
            continue;
        setcookie($cone, $_POST[$cone], time() + 1800);
    }
}

/** デフォルトのハッシュを作成する */
function form_default($info) {
    $ret = lhash($info['value']);
    foreach (clist("select,check,radio,group_check") as $type) {
        foreach ($info['value_info'][$type] as $dat) {
            $ret[$dat['name']] = $dat['default'];
        }
    }
    return $ret;
}

/** 添付用のデータを取り出す */ 
function form_filedown($fid) {
    $db = getDB();
    $sql = 'SELECT filename,file,fsize FROM form_file_info WHERE id=:id';
    return $db->QueryLine($sql, array('id' => $fid));
}

/** 指定したファイルが存在するか確認する */ 
function form_fileexist($id) {
    $db = getDB();
    $sql = 'SELECT count(*) FROM form_file_info WHERE id=:id';
    return $db->Count($sql, array('id' => $id)) > 0;
}

/** ファイルを表示する */
function form_fileshow($fid) { 
    $row = form_filedown($fid);
    $fp = fopen($row['file'], "r");//ファイルの中身
    $file = fread($fp, $row['fsize']);
    fclose($fp);
    fileview($row['filename'], $file, true);
}

/** ファイルを保存する */
function form_savefile($name) {

    $filename = $_FILES[$name]['name'];
    $temp = $_FILES[$name]['tmp_name'];
    if ($temp == "")
        return;
    $info = pathinfo($filename);
    $ext = strtolower($info['extension']);

    //保存先のファイル名
    $file = dirname(__FILE__) . "/save/" . date("Y-m-d-H-i-s-") . $name . "-" . basename($temp) . "-" . mt_rand(1, 100);

    imagesize($temp, $file, 600, 480, $ext);
    $hash['fsize'] = filesize($file);
    $hash['savetime'] = date('Y/m/d G:i:s');
    $hash['filename'] = $filename;
    $hash['x'] = null;
    $hash['y'] = null;
    $hash['file'] = $file;

    if (in_array($ext, clist("gif,jpg,png,swf,swc,psd,tiff,bmp,iff,jp2,jpx,jb2,jpc,xbm"))) {
        list($x, $y) = getimagesize($file);//画像の場合のサイズ
        $hash['x'] = $x; $hash['y'] = $y;
    }

    $db = getDB();
    return $db->Insert('form_file_info', $hash);
}

function form_reset() {
    form_reset_table();
    form_tbl_setup();
}

/** 送信完了後、ファイルを削除する */
function form_fileexit($info, $hpos) {
    foreach ($info['value_info']['file'] as $dat) {
        $name = $dat['name'];
        if ($hpos[$name]) {
            form_filedel($hpos[$name]);
        }
    }
}

function form_sql() {
    throw new Exception('使うな危険！！');
}

function form_fileinfo($fid = null) {
    $db = getDB();
    if ($fid){
        $sql = 'SELECT id,filename,x,y,savetime,fsize FROM form_file_info WHERE id=:id';
        return $db->QueryLine($sql,array('id' => $fid));
    } 
    else{
        $sql = 'SELECT id,filename,x,y,savetime,fsize FROM form_file_info';
        return $db->QueryRow($sql);
    }
}

function form_filedel($fid = null) {
    $db = getDB();
    $db->pdo->beginTransaction();
    if ($fid) {
        $sql = 'SELECT filename,file,fsize FROM form_file_info WHERE id=:id';
        $row = $db->QueryLine($sql, array('id' => $fid));

        $sql = 'DELETE FROM form_file_info WHERE id=:id';
        $db->ExecuteRow($sql, array('id' => $fid));
        if (file_exists($row['file'])) unlink($row['file']);
    }
    else {
        $sql = 'SELECT id,filename,x,y,savetime,fsize FROM form_file_info';
        $delinfo = 'DELETE FROM form_file_info WHERE id=:id';
        $filedown = 'SELECT filename,file,fsize FROM form_file_info WHERE id=:id';
        $table = $db->QueryRow($sql);
        foreach ($table as $row) {
            if(strtotime($row['savetime']) + (30 * 60) < time()){
                $db->ExecuteRow($delinfo,array('id' => $row['id']));
                $rinfo = $db->QueryLine($filedown,  array('id' => $fid));
                if (file_exists($rinfo['file'])) unlink($rinfo['file']);
            }
        }
    }
    $db->pdo->commit();
}

/** 表示用のデータを作成する */
function form_filetext($fid) {
    $info = form_fileinfo($fid);
    $type = webparam('typex');
    $pid = webparam('pid', 1);
    $info['del'] = "return delfile($fid);";
    $info['view'] = "?cmd=view&id=$fid&pid=$pid";
    return $info;
}

/** $page内に存在するグループ化　チェックボタンの確認用リスト(表示のみ)を作成する
 * param $page 元になるページ
 * param $hpos $_POSTに準ずる値
 * param $info 設定データ */
function form_groupcheck($page, $hpos, $info) {
    foreach ($info['value_info']['group_check'] as $dat) {
        $name = $dat['name'];
        $line = $dat['check_temp'];

        if ($hpos[$name] == null)
            continue;
        $list = toarray($hpos[$name]);

        $value = "";
        for ($i = 1; $i <= count($list); $i++) {
            $temp = rep($line, 'value', $list[$i - 1]);
            $value .= rep($temp, 'nline', ($i - 1) % 2 == 1 ? $dat["even-name"] : $dat["odd-name"]);
        }
        $page = rep($page, $name, $value);
    }
    return $page;
}

function form_fileview($page, $hpos, $info, $type = null) {
    //★ファイル系
    //編集画面
    if ($type == null) {
        foreach ($info['value_info']['file'] as $dat) {
            $name = $dat['name'];
            $fid = array_key_exists($name, $_FILES) ? form_savefile($name) : null;
            
            #エラー出た場合にファイルを消えないようにする
            $fid = $_POST[$name] ? $_POST[$name] : $fid;

            $exist = form_fileexist($fid);

            if ($exist && $fid) {
                $line = $dat['edit'];
                $line = ephash(form_filetext($fid), $line);
                $line .= html_hidden(array($dat['name'] => $fid));
            } else {
                $line = $dat['input'];
                $line = rep($line, 'name', $name);
            }
            $page = rep($page, $name, $line);
        }
        return $page;
    }

    //デフォルト画面
    if ($type == 'default') {
        foreach ($info['value_info']['file'] as $dat) {
            $name = $dat['name'];
            $line = $dat['input'];
            $line = rep($line, 'name', $name);
            $page = rep($page, $name, $line);
        }
        return $page;
    }

    //確認画面
    if ($type == 'check') {
        foreach ($info['value_info']['file'] as $dat) {
            $name = $dat['name'];
            $fid = webparam($name, $fid);
            $fid = array_key_exists($name, $_FILES) ? form_savefile($name) : $fid;
            $exist = form_fileexist($fid);

            if ($exist && $fid) {
                $line = $dat['check'];
                $line = ephash(form_filetext($fid), $line);
                $line .= html_hidden(array($dat['name'] => $fid));
            } else {
                $line = $dat['novalue'];
            }
            $page = rep($page, $dat['name'], $line);
        }
        return $page;
    }
}

/** ファイルを送信します */
function form_mail($formno, $hpos, $info) {


    $cpos = copyhash($hpos);
    foreach ($info['value_info']['group_check'] as $dat) {
        $rline = "";
        $name = $dat['name'];
        if (!$hpos[$name])
            continue;
        $list = toarray($cpos[$name]);

        $line = $dat['mail_temp'];
        foreach ($list as $vone) {
            $rline .= rep($line, 'value', $vone);
        }
        $cpos[$name] = $rline;
    }


    $mconf = new mailconfig();
    $mlist = $mconf->getValue($cpos, $formno, $info['mail']);

    // お問い合わせメール振り分け処理    開始
    userload("form/plus_mail.php");

    $checked_park = mb_convert_encoding($hpos['ここにフォームで使用する変数を入力'],"shift-jis","utf-8");

    if ( $checked_park == mb_convert_encoding("特になし","shift-jis","utf-8") ) {
        $mlist[] = $mlist[9];
        $mlist[11]['to'] = OTHER;
        //$mlist[12]['to'] = KONGO_IKOMA_2;
    } else if ( $checked_park == mb_convert_encoding("御池校","shift-jis","utf-8") ) {
        $mlist[] = $mlist[9];
        $mlist[11]['to'] = OIKE;
    } else if ( $checked_park == mb_convert_encoding("下鴨校","shift-jis","utf-8") ) {
        $mlist[] = $mlist[9];
        $mlist[11]['to'] = SHIMOGAMO;
    } else if ( $checked_park == mb_convert_encoding("一乗寺校","shift-jis","utf-8") ) {
        $mlist[] = $mlist[9];
        $mlist[11]['to'] = ICHIJOJI;
    }
    // お問い合わせメール振り分け処理    終了

    $sender = new mailsender();



    foreach ($info['value_info']['file'] as $dat) {
        $name = $dat['name'];
        if (!$cpos[$name])
            continue;
        $value = form_filedown($cpos[$name]);

        if (file_exists($value['file'])) {
            $fp = fopen($value['file'], "r");//ファイルの中身
            $file = fread($fp, $value['fsize']);
            fclose($fp);
        }

        $sender->attach($value['filename'], $file);
    }

    foreach ($mlist as $mone) { $sender->sendhash($mone);    }
}

//$page : テンプレートデータ
//$info : 設定データ
//$hpos : POSTからの値
function form_pageview($page, $hpos, $info) {


    //★セレクト系
    foreach ($info['value_info']['select'] as $dat) {
        $sel = $hpos[$dat['name']]; //フォーム上のデータを取り出す
        $inval = $dat['value'];  //セレクトに表示するデータを取り出す
        if ($dat['label'])
            array_unshift($inval, $dat['label']); //ラベルがあれば追加する
        $select = html_oplist($inval, $sel, $dat['label'] != null);  //データを作成
        $page = rep($page, $dat['name'], $select);
    }

    //★チェック系
    foreach ($info['value_info']['check'] as $dat) {
        $sel = $hpos[$dat['name']]; //フォーム上のデータを取り出す
        if (!is_array($sel))
            $sel = array($sel);//array化する
        $page = html_check($page, $dat['name'], $dat['value'], $sel); //データを作成
    }

    //★グループチェック系
    foreach ($info['value_info']['group_check'] as $dat) {
        $sel = $hpos[$dat['name']]; //フォーム上のデータを取り出す
        if (!is_array($sel))
            $sel = array($sel);//array化する
        $page = html_check($page, $dat['name'], $dat['value'], $sel); //データを作成
    }

    //★ラジオ系
    foreach ($info['value_info']['radio'] as $dat) {
        $sel = $hpos[$dat['name']];
        $page = html_radio($page, $dat['name'], $dat['value'], $sel);
    }
    //pl(view,strlen($page));
    return $page;
}

/** 内部関数 */
function form_url($file, $name = null) {
    $info = webparam("@form_conf_path");
    $file = $info[0] . $file;
    if (file_exists($file)) {
        return $name ? tLoader::nLoad($file, $name) : file_get_contents($file);
    }
}

/** フォームの設定ファイルを読み込みます */
function form_infoloader($path, $formno) {
    $conf = new cssConfig($path);
    //$sel =new cssSelector("");


    $sel = $conf->getSelector($formno);
    if ($sel->getValue('type') != 'form')
        return array();

    $ret['infofolder'] = $conf->getFilepath("");
    $_POST["@form_conf_path"] = array($ret['infofolder']);
    $ret['input_file'] = $conf->getFilepath($sel->getValue('input_file'));
    $ret['check_file'] = $conf->getFilepath($sel->getValue('check_file'));
    $ret['thanks_file'] = $conf->getFilepath($sel->getValue('thanks_file'));
    $ret['err_info'] = $conf->getFilepath($sel->getValue('err_info'));
    $ret['mail'] = $sel->getValue('mail');
    $ret['mail_no'] = $sel->getValue('mail_no');
    $ret['value'] = $sel->getValuesOne('value'); //list
    $ret['cookie'] = $sel->getValuesOne('cookie'); //list
	
	$ret['previous'] = $sel->getValuesOne('previous');


    $typelist = clist("select,check,file,radio,group_check");
    $ret['value_info'] = array('select' => array(), 'check' => array(), 'file' => array(), 'radio' => array(), 'group_check' => array());
    foreach ($ret['value'] as $vone) {
        $vsel = $conf->getSelector($vone);
        if ($vsel == null)
            continue;
        $vtype = $vsel->getValue('type');
        switch ($vtype) {
            case 'group_check':
                $datatype = $vsel->getValue('datatype');
                $value = ($datatype == 'file') ?
                        readlist($conf->getFilepath($vsel->getValue('value'))) :
                        $vsel->getValuesOne('value'); //$valueはリスト
                $default = $vsel->getValue('default'); //$defaultもリスト
                $odd = $vsel->getValue("odd-name");
                $even = $vsel->getValue("even-name");
                $fmail = ifactFunc($vsel->getValue('mail_temp'), 'form');
                $fcheck = ifactFunc($vsel->getValue('check_temp'), 'form');
                $var = array('value' => $value, 'default' => $default, 'name' => $vone, 'check_temp' => $fcheck, 'mail_temp' => $fmail, "even-name" => $even, "odd-name" => $odd);
                break;
            case 'select':
                $datatype = $vsel->getValue('datatype');
                $label = $vsel->getValue('label');
                $value = ($datatype == 'file') ?
                        readlist($conf->getFilepath($vsel->getValue('value'))) :
                        $vsel->getValuesOne('value');
                $var = array('label' => $label, 'value' => $value, 'name' => $vone);
                break;
            case 'check':
                $on = $vsel->getValue('on');
                $off = $vsel->getValue('off');
                $default = $vsel->getValue('default');
                $var = array('on' => $on, 'off' => $off, 'default' => $default, 'name' => $vone);
                break;
            case 'file':
                $finput = ifactFunc($vsel->getValue('input'), 'form');
                $fedit = ifactFunc($vsel->getValue('edit'), 'form');
                $fcheck = ifactFunc($vsel->getValue('check'), 'form');
                $var = array('name' => $vone, 'input' => $finput, 'edit' => $fedit, 'check' => $fcheck);
                break;
            case 'radio':
                $datatype = $vsel->getValue('datatype');
                $value = ($datatype == 'file') ?
                        readlist($conf->getFilepath($vsel->getValue('value'))) :
                        $vsel->getValuesOne('value');
                $default = $vsel->getValue('default');
                $var = array("value" => $value, 'default' => $default, 'name' => $vone);
                break;
            default:
                continue;
        }
        array_push($ret['value_info'][$vtype], $var);
    }
    return $ret;
}

/** テーブルをリセットします */
function form_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `form_file_info`;';
    $db->ExecuteRow($drop);
}

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function form_tbl_setup($force = false) {

    if($force) form_rest_table();
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS form_file_info (
	id INTEGER  UNSIGNED NOT NULL COMMENT "主キー" AUTO_INCREMENT
        ,filename VARCHAR(256) COMMENT "実際にサーバーに存在する名前ファイル名"
        ,x INTEGER UNSIGNED COMMENT "横幅"
	,y INTEGER UNSIGNED COMMENT "縦幅"
	,savetime DATETIME COMMENT "保存時間"
	,fsize INTEGER  UNSIGNED COMMENT "ファイルサイズ"
	,`file` VARCHAR(256) COMMENT "アップロードした名前"
	,PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
       DEFAULT CHARSET=utf8
       COMMENT="フォーム用のファイル情報"';
    $db->ExecuteRow($sql);

}
?>