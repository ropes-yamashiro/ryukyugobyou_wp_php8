<?php

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Jumpei.Mano
# Release: 2012/09/26
# History:
#
#==================================
libload("database.php");
libload("htmltools.php");
libload("hashstr.php");
libload("errors.php");

define('TOPIC_TYPE_LINK', 0);
define('TOPIC_TYPE_POP', 1);
define('TOPIC_TYPE_FILE', 2);
define('TOPIC_TYPE_TITLE', 3);
define('TOPIC_TYPE_DOCUMENT_LINK', 4);

define('TOPIC_TYPE_STR_LINK', 'link');
define('TOPIC_TYPE_STR_POP', 'pop');
define('TOPIC_TYPE_STR_FILE', 'file');
define('TOPIC_TYPE_STR_TITLE', 'title');
define('TOPIC_TYPE_STR_DOCUMENT_LINK', 'contents');

require_once(userpath("document/document.php"));

define('TOPIC_USER_ENC', 'SJIS-win');
define('TOPIC_VIEW_ENC', 'Shift_JIS');
define('TOPIC_SYSTEM_ENC', 'utf-8');

/** プレビュー */
function icon_preview() {
    $id = webparam('param');
    $pid = webparam('pageid');
    if (!$id || ! $pid)
        die("不正アクセス1:icon_preview");

    $db = getDB();
    $sql = 'SELECT * FROM icon WHERE groupid=:pid and id=:id';
    $row = $db->QueryLine($sql, array('pid' => $pid, 'id' => $id));


    $type = icon_datatype($row['type']);
    if ($type == TOPIC_TYPE_STR_FILE) {
        $_POST['param'] =$row['filename'];
        icon_picture();
        exit();
    }
    if ($type == TOPIC_TYPE_STR_LINK) {
        header("Location: " . $row['link']);
        exit();
    }

    if ($type == TOPIC_TYPE_STR_POP) {
        $row['pageid'] = $pid;
        $row['year'] = $row['view_year'];
        $row['month'] = $row['view_month'];
        $row['day'] = $row['view_day'];

        $loader = new tLoader(userpath("icon/html/window.html"));
        $nopic = true;

        for ($i = 1; $i<=5; $i++) {
            if ($row['picname' . $i]) {
                $nopic = false;
                break;
            }
        }
        $page = $loader->getData($nopic ? 'window-noimage' : 'window');
        $line = $loader->getData('line');

        $page = rep($page, 'contents', $row['contents']);
        #$page = rep($page, 'contents', html_br($row['contents']));
        $page = phash($row, $page);

        for ($i = 1; $i<=5; $i++) {
            if ($row['picname' . $i])
                $left .= trep($row['picname' . $i], $line);
        }

        $page = rep($page, 'left', $left);
        pout($page, 'utf-8');
    }
}

/** 初期アクセスが可能か? */
function icon_first() {
    $pid = icon_okauth();
    return webauth('customer_icon_edit' . $pid);
}

function icon_act() {
    $ret = new plugin_result(icon_action());;
    $onload = webtopget("deftab");
    $ret->element['onload'] = "deftab($onload)";
    return $ret;
}

function icon_action() {

    define('testuser', 'test');
    $pid = icon_okauth();
    if (!webauth('customer_icon_edit' . $pid))
        return icon_dtable('err_access');
    $cmd = webparam("cmd", "list");
    switch ($cmd) {
        case "delfile" :

			$extension = pathinfo(webparam('param'), PATHINFO_EXTENSION);
			if (isset($_FILES['thumbnail']) && $extension == 'gif' || $extension == 'jpeg' || $extension == 'jpg'){
				$db = getDB();
				$delthum = 'DELETE FROM icon_thumbnail WHERE filepath=:filepath';
				$db->ExecuteRow($delthum, array('filepath' => webparam('param')));
				return icon_detail(webparam('dataid'));
			}
        case "delline" : return icon_line_del();
        case "upline" : return icon_line_move(true);
        case "downline" : return icon_line_move(false);
        case "saveinput" :
            icon_check_err();
            $did = icon_save();
            if (is_int($did)) {
                return icon_list();
            }
            else
                return $did;
        case "newicon" :return icon_newicon();
        case "detail" : return icon_detail(webparam('param'));
        case "pic" : return icon_picture(false);
        case "chvisible" :return icon_chvisible();
        case "del" : return;
        case "list" : return icon_list();

        case "saveonly" :
            icon_check_err();
            $did = icon_save();
            if (is_int($did)) {
                return icon_detail($did);
            }
            else
                return $did;
        case "ch_contents" :
            if (webparam(dataid) == null)
                return icon_newicon();
            else
                return icon_detail(webparam('dataid'));
            ;
        case "preview" : return icon_preview();
        default : return icon_dtable('err_cmd', array('cmd' => $cmd));
    }
}

//======================================================

/** テーブルのHTMLのデータを取得します */
function icon_dtable($key, $hash = array()) {

    $file = userpath(switchSpTemplate("icon/html/table.html"));
    $label = "@icon_dtable";
    $var = webparam($label);
    if (!$var) {
        $var = new tLoader($file);
        $_POST[$label] = $var;
    }
    //pr($var);
    $value = $var->getData($key);
    return phash($hash, $value);
}

/** 入力のデータを取得します */
function icon_dinput($key, $hash = array()) {

    $file = userpath(switchSpTemplate("icon/html/input.html"));
    $label = "@icon_dinput";
    $var = webparam($label);
    if (!$var) {
        $var = new tLoader($file);
        $_POST[$label] = $var;
    }
    $value = $var->getData($key);
    return phash($hash, $value);
}

//======================================================





function icon_auth() {
    $db = getDB();
    $sql = 'SELECT * FROM icon_info';
    $res = $db->QueryRow($sql);


    $line = icon_dtable('authinfo');
    $tret = array();
    foreach ($res as $row) {
        array_push($tret, array('key' => 'icon_title' . $row['id'], 'name' => $row['name'],
            info => rep($line, 'title', $row['name'])));
    }
    if (count($tret) > 0)
        $hash[icon_dtable('menu')] = $tret;

    $hash = '';
    return $hash;
}

/** テーブルの行を削除する */
function icon_line_del() {
    //★修正可能性
    $id = webparam('param', -1);
    $pid = webparam('pageid', -1);
    if ($pid == - 1 || $id == - 1)
        return dp("システムエラー");

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql = 'SELECT * FROM icon WHERE groupid=:pid and id=:id';
    $res = $db->QueryRow($sql, array('pid' => $pid, 'id' => $id));

    if (count($res) == 0) {
        $db->pdo->commit();
        return icon_dtable('err_access');
    }

    $sql2 = 'SELECT * FROM icon_thumbnail WHERE icon_id=:icon_id';
    $res_2 = $db->QueryRow($sql2, array('icon_id' => $res[0]['id']));

    $delthum = 'DELETE FROM icon_thumbnail WHERE id=:id';
    $db->ExecuteRow($delthum, array('id' => $res_2[0]['id']));
    $path = 'contents/icon/thumbnail/' . $res_2[0]['filepath'];
    unlink($path);

    $sql4 = 'DELETE FROM icon WHERE groupid=:pid and id=:id';
    $db->ExecuteRow($sql4, array('pid' => $pid, 'id' => $id));

    $db->pdo->commit();
    return icon_list();
}

function icon_chvisible() {
    $id = webparam('param');
    $pid = webparam('pageid');
    if ($id == null || $pid == null)
        return icon_dtable('err_access');

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql1 = 'SELECT visible FROM icon WHERE groupid=:pid and id=:id';
    $row = $db->QueryLine($sql1, array('pid' => $pid, 'id' => $id));
    if (count($row) == 0) {
        $db->pdo->commit();
        return icon_list();
    }

    $sql2 = 'UPDATE icon SET visible=:ch WHERE id=:id and groupid=:pid';
    $db->ExecuteRow($sql2, array('pid' => $pid, 'id' => $id, 'ch' => $row['visible'] == "1" ? "0" : "1"));

    $db->pdo->commit();
    return icon_list();
}

function icon_paging() {
    $pid = webparam('pageid', webparam('id', 1));
    $count = webparam('count', 20);
    $cpage = webparam('pageno', 1);

    $db = getDB();
    $sql = 'SELECT count(*) FROM icon WHERE groupid=:pid';
    $total = $db->Count($sql, array('pid' => $pid));
    $line = icon_dtable('page');
    return html_paging($total, $count, $cpage, $line);
}

function icon_check_err() {
    $p = $_POST;
    $mes = $p['title'] == null ? icon_dinput('err_title') : "";
    $mes .= $_FILES['thumbnail']['name'] == null && $p['dataid'] == null ? icon_dinput('err_img') : "";

    if ($mes != "") {
        $mes = icon_dinput('err_container', array('err' => $mes));
    }
    webvalset('icon_err', $mes);
    return $mes;
}

function icon_save() {

    //dp($_POST);
    //★修正
    //エラーの確認
    $err = webvalget('icon_err');

    $haserr = !empty($err);
    $dataid = webparam('dataid');

    $type = webparam('type');

    if (trim($_POST['title']) == '<br />' || trim($_POST['title']) == '&nbsp;')
        $_POST['title'] = null;
    $_POST['title'] = str_replace('\\"', '"', $_POST['title']);

    //エラーの場合はフォームの表示
    if ($haserr) {
        $page = $dataid ? icon_detail($dataid, $err) : icon_newicon($err);
        return $page;
    }

	$file_nm = $_FILES['thumbnail']['name'];
    $extension = pathinfo($file_nm, PATHINFO_EXTENSION);
	if (isset($_FILES['thumbnail']) && $extension == 'gif' || $extension == 'jpeg' || $extension == 'jpg'){
		$filepath = date('Ymdhis') . '.' . $extension;
		if (is_uploaded_file($_FILES["thumbnail"]["tmp_name"])) {
			move_uploaded_file($_FILES["thumbnail"]["tmp_name"], userpath("icon/thumbnail/"). $filepath);
		}
	}

    //DB処理
    $db = getDB();
    $db->pdo->beginTransaction();

    $DBpost = array();
    foreach ($_POST as $keypost => $valpost) {
        if (is_string($valpost) || is_int($valpost)) {
            $DBpost[$keypost] = $valpost;
        }
    }

    $list = chash('anchor,pageid');
    $DBpost = whash($list, $DBpost);
    $DBpost['pageid'] = intval(webparam('pageid'));
    unset($DBpost['pageno'], $DBpost['count'], $DBpost['cmd'],
            $DBpost['param'], $DBpost['dataid'], $DBpost['sort'], $DBpost['linktoD'], $DBpost['contents']);


    if ($dataid) {
        $update = '
            UPDATE icon
                SET title =:title, anchor=:anchor
                WHERE id=:dataid and groupid=:pageid';

        $DBpost['dataid'] = $dataid;
        $db->ExecuteRow($update, $DBpost);
        $did = $dataid;
    } else {
        $insert = '
            INSERT INTO icon
                (title,anchor,groupid)
            VALUES
                (:title,:anchor,:pageid);';
        $did = $db->ExecuteRow($insert, $DBpost, true);
        $sql = 'UPDATE icon SET `sort`=:did WHERE `id`=:id';
        $db->ExecuteRow($sql, array('id' => $did, 'did' => $did));
    }

	$sql_thum = 'SELECT * FROM icon_thumbnail WHERE icon_id=:id';
	$thumdata = $db->QueryLine($sql_thum, array('id' => $did));

	if(empty($thumdata) && !empty($filepath)){
		$sql_thum = 'INSERT INTO icon_thumbnail (icon_id, filepath, created) VALUES (:icon_id, :filepath, :created)';
        $db->ExecuteRow($sql_thum, array('icon_id' => $did, 'filepath' => $filepath, 'created' => date('Y/m/d h:i:s')));
	}
    $db->pdo->commit();
    return intval($did);
}
function icon_detail($id, $err=null) {
    $pageid = webparam('pageid');
    $page = icon_dinput('input');
    $page = rep($page, 'count', webparam('count', 20));
    $page = rep($page, 'pageno', estr(webparam('pageno', 1)));

    $db = getDB();
    $sql = 'SELECT * FROM icon WHERE id=:id and groupid =:pageid';
    $hash = $db->QueryLine($sql, array('pageid' => $pageid, 'id' => $id));

    $sql = 'SELECT * FROM icon_thumbnail WHERE icon_id=:id';
    $thumbnail = $db->QueryLine($sql, array('id' => $id));

    $date = getdate();
    $y = $date['year'];
    $page = rep($page, view_year, opdate($y - 2, $y + 3, $hash['view_year']));
    $page = rep($page, view_month, opdate(1, 12, $hash['view_month']));
    $page = rep($page, view_day, opdate(1, 31, $hash['view_day']));

    $bin = icon_dinput('file_exist');
    $noin = icon_dinput('file_nonexist');

    $page = ephash(icon_title(), $page);

    //コピペする
    $list = container_info_select_all();
    $con = array();
    foreach ($list as $lone) {
        $con[$lone['id']] = $lone['title'];
    }
    foreach ($con as $ckey => $cval) {
        if (count(contents_info_select_by_icon($ckey)) == 0)
            unset($con[$ckey]);
    }
    $page = rep($page, 'contents_group', $con ? html_ophash2($con, webparam('contents_group', $hash['contents_group'])) : "");

    $list2 = contents_info_select_by_icon(webparam('contents_group', $hash['contents_group']));
    foreach ($list2 as $lone2) {
        $con2[$lone2['id']] = $lone2['title'];
    }
    $page = rep($page, 'contents_title', $con ? html_ophash2($con2, $hash['contents_title']) : "");
    //ここまで

    $info = site_append('icon','category');

    $category = ($info) ? $info($pageid) :'アイコン画像';

    $page = rep($page, 'err', $err);
    $page = rep($page, 'type', icon_dinput('hidden') . $typevalue);
    $page = rep($page,'category',$category);
    $page = html_switch($page, 'anchor', 0, 1, $hash['anchor']);
    $page = html_switch($page, "visible", 0, 1, $hash['visible']);
    $page = html_switch($page, "img_pos", 0, 1, $hash['img_pos']);

	if(!is_null($thumbnail)){
		$fname = $thumbnail['filepath'];
		$path = 'contents/icon/thumbnail/'.$thumbnail['filepath'];
		$dspthumb = '
			&nbsp;&nbsp;' . $fname . '&nbsp;&nbsp;<a href="' . $path . '" target="_blank">表示</a>';
	}else{
		$dspthumb = '<input type=file name="thumbnail" onFocus="this.className=\'on\';" onBlur="this.className=\'off\';" size="37"  />	';
	}
	$page = rep($page, 'thumbnail', $dspthumb);

    webtopset('deftab', $type);
    $page = phash($hash, $page);

    return $page;
}

function icon_newicon($err = null) {
    $page = icon_dinput('input');
    $pageid = webparam('pageid');
    $page = rep($page, 'err', $err ? $err : webvalget('err'));

    $info = site_append('icon','type');
    $inputtype = $info ? $info(webparam('pageid',1)) : icon_dinput('input_type');

    $page = rep($page, 'type', $inputtype);
    $page = rep($page, 'count', webparam('count', 20));
    $page = rep($page, 'pageno', estr(webparam('pageno', 1)));


    $date = getdate();
    $y = $date['year'];
    $page = rep($page, 'view_year', opdate($y - 1, $y + 3, webparam('view_year', $y)));
    $page = rep($page, 'view_month', opdate(1, 12, webparam('view_month', $date['mon'])));
    $page = rep($page, 'view_day', opdate(1, 31, webparam('view_day', $date['mday'])));
    $page = rep($page, 'title', webparam('title'));

    $bin = icon_dinput('file_exist');
    $noin = icon_dinput('file_nonexist');
    $var = clist("picname1,picname2,picname3,picname4,picname5");

    //コピペする
    $list = container_info_select_all();
    $first;
    $con = array();
    foreach ($list as $lone) {
        $con[$lone['id']] = $lone['title'];
        $first = $first ? $first : $lone['id'];
    }
    foreach ($con as $ckey => $cval) {
        if (count(contents_info_select_by_icon($ckey)) == 0)
            unset($con[$ckey]);
    }
    $page = rep($page, 'contents_group', $con ? html_ophash2($con, webparam('contents_group')) : "");

    $list2 = contents_info_select_by_icon(webparam('contents_group', $first));
    foreach ($list2 as $lone2) {
        $con2[$lone2['id']] = $lone2['title'];
    }
    $page = rep($page, 'contents_title', $con ? html_ophash2($con2, webparam('contents_title')) : "");
    //ここまで

    $page = ephash(icon_title(), $page);


    $type = webparam('type', 0);
    $info = site_append('icon','category');
    $category = ($info) ? $info($pageid) :'アイコン画像&nbsp;&nbsp;';

    $page = rep($page, 'title', webparam('title'));
    $page = rep($page, 'summary', webparam('summary'));
    $page = rep($page, 'icon_name', webparam('icon_name'));
    $page = rep($page, 'id', null);
    $page = rep($page, 'sort', null);
    $page = rep($page, 'category', $category);
    $page = html_switch($page, 'type', 0, 4, $type);
    $page = html_switch($page, 'anchor', 0, 1, webparam('anchor', 0));
    $page = html_switch($page, 'visible', 0, 1, webparam('visible', 0));
    $page = html_switch($page, 'img_pos', 0, 1, webparam('img_pos', 0));

	$dspthumb = '<input type=file name="thumbnail" onFocus="this.className=\'on\';" onBlur="this.className=\'off\';" size="37"  />';
	$page = rep($page, 'thumbnail', $dspthumb);

    webtopset('deftab', $type);

    return $page;
}

/** アップロードしたファイルが有効か */
function icon_enable_upfile($filekey, $type) {
    if (!array_key_exists($filekey, $_FILES)
        )return false;
    if ($_FILES[$filekey]['size'] == 0
        )return false;
    if ($type == TOPIC_TYPE_POP) {
        if (isImageExt($_FILES[$filekey]['name']) != null)
            return false;
    }
    return true;
}

//==================================================
//　リスト関連
//=== 完了 ======================================================

/** テーブルの表示順を入れ替えます */
function icon_line_move($isup) {

    $sort = webparam('param');
    $pid = webparam('pageid');

    if ($sort == null || $pid == null)
        return icon_dtable('err_access');

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql = $isup ? 'SELECT id,sort FROM icon WHERE sort >= :sort and groupid=:groupid ORDER BY sort ASC LIMIT 0,2' : 'SELECT id,sort FROM icon WHERE sort <= :sort and groupid=:groupid ORDER BY sort DESC LIMIT 0,2';

    $tbl = $db->QueryRow($sql, array('groupid' => $pid, 'sort' => $sort));
    if (count($tbl) <= 1) {
        $db->pdo->commit();
        return icon_list();
    };


    $param1 = array('pid' => $pid, 'destsort' => $tbl[1]['sort'], 'startid' => $tbl[0]['id']);
    $param2 = array('pid' => $pid, 'sort' => $tbl[0]['sort'], 'destid' => $tbl[1]['id']);
    $db->ExecuteRow('UPDATE icon SET sort=:destsort WHERE id=:startid and groupid=:pid', $param1);
    $db->ExecuteRow('UPDATE icon SET sort=:sort WHERE id=:destid and groupid=:pid', $param2);
    $db->pdo->commit();
    return icon_list();
}

/** 初期値用：アクセス可能な権限を取り出します */
function icon_okauth() {
    $pid = webparam('pageid');
    if (!$pid)
        $pid = webparam('id', 1);
    if (webauth('customer_icon_edit' . $pid) && icon_hasmenu($pid)) {
        $_POST['pageid'] = $pid;
        return $pid;
    }

    $db = getDB();
    $row = $db->QueryLine('SELECT max(id) FROM icon_info');
    $count = $row['max(id)'];
    for ($i == 0; $i <= $count; $i++) {
        if (webauth('customer_icon_edit' . $i) && icon_hasmenu($i)) {
            $_POST['pageid'] = $i;
            return $i;
        }
    }
    return -1;
}

function icon_list($count = null, $pageno = null) {

    $db = getDB();
    $pid = intval(webparam('pageid', icon_okauth()));

    $menu_row = $db->QueryLine("SELECT * FROM icon_info WHERE `id` =:id", array('id' => $pid));
    if (count($menu_row) == 0)
        return icon_dtable('err_access2');

    if ($count == null)
        $count = webparam('count', 20);
    if ($pageno == null
        )$pageno = webparam('pageno', 1);
    $top = $count * ($pageno - 1);

    $sql = 'SELECT i.*, t.filepath FROM icon i LEFT OUTER JOIN icon_thumbnail t ON i.id = t.icon_id WHERE i.groupid =:groupid ORDER BY sort DESC';
    $tbl = $db->QueryLimit($sql, $top, $count, array('groupid' => $pid));

    if (count($tbl) == 0) {
        $view = icon_dtable('notdata');
    } else {
        $line = icon_dtable('table_line');

        // エラーのチェック
        if (isset($_SESSION['@system']['icon_err'])) {
            if ($_SESSION['@system']['icon_err'] == 'err') {
                $view .= icon_dtable('visible_err');
                unset($_SESSION['@system']['icon_err']);
            }
        }

        foreach ($tbl as $row) {
            #細かいデータ--------------------------------------------------------
            $row['visible'] = $row['visible'] == 0 ? "" : 'checked';
            $row['pageid'] = $pid;

            #ラインを設定する----------------------------------------------------
            $tline = rep($line, 'preview', icon_dtable('preview'));
            $tline  = rep($tline, 'thumbnail', $row['filepath']);

            $view .= ephash($row, $tline);
        }
    }

    //
    //祖ほのかの必須項目

    $info = site_append('icon','category');
    $category = ($info) ? $info($pid) :'アイコン画像';

    $html_page = icon_dtable('table');
    $html_page = rep($html_page,'category',$category);
    $html_page = rep($html_page, 'pagename', $menu_row['name']);
    $html_page = rep($html_page, 'view', $view);
    $html_page = rep($html_page, 'paging', icon_paging());
    $html_page = rep($html_page, 'pid', $pid);
    $html_page = rep($html_page, 'cmd', webparam('cmd', 'list'));
    $html_page = rep($html_page, 'pageno', $pageno);
    $html_page = rep($html_page, 'count', html_ophash(array("10件" => 10, "20件" => 20, "50件" => 50, "100件" => 100), $count));

    return $html_page;
}

/**
 * メニューがある確認
 * @param int $pid
 * @return bool
 */
function icon_hasmenu($pid) {
    $db = getDB();
    $sql = 'SELECT count(*) FROM icon_info WHERE id=:id';
    $tbl = $db->QueryRow($sql, array('id' => $pid));
    return intval($tbl[0]['count(*)']) > 0;
}

/**
 * ドキュメント番号から値を取り出す
 * @param int $type
 * @return string
 */
function icon_datatype($type) {
    if ($type == TOPIC_TYPE_LINK)
        return TOPIC_TYPE_STR_LINK;
    if ($type == TOPIC_TYPE_POP)
        return TOPIC_TYPE_STR_POP;
    if ($type == TOPIC_TYPE_FILE)
        return TOPIC_TYPE_STR_FILE;
    if ($type == TOPIC_TYPE_TITLE)
        return TOPIC_TYPE_STR_TITLE;
    if ($type == TOPIC_TYPE_DOCUMENT_LINK)
        return TOPIC_TYPE_STR_DOCUMENT_LINK;
}

/**
 * タイトルの名前を取り出す
 * @return hash pid,iconname
 */
function icon_title() {
    $db = getDB();
    $sql = 'SELECT `id` as `pid`,`name` as `iconname` FROM icon_info where `id`=:id';
    $param = array('id' => webparam('pageid', 0));
    $tbl = $db->QueryRow($sql, $param);
    if (count($tbl) == 0)
        return icon_dtable('err_access2');
    return $tbl[0];
}

/**
 * アイコン画像のテーブルを作成する
 */
function icon_tbl_setup($force = false) {

    $db = getDB();
    #テーブル削除
    if ($force) {
        $drop = 'DROP TABLE IF EXISTS `icon_info`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `icon`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `icon_thumbnail`;';
        $db->ExecuteRow($drop);
    }

    #コンテンツメニュー
    $create_tbl_menu = '
        CREATE TABLE IF NOT EXISTS `icon_info` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT  COMMENT "主キー",
            `name` VARCHAR(20) COMMENT "メニュー名",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="アイコン画像のメニューを保存"';

    $db->ExecuteRow($create_tbl_menu);

    #テーブル作成 コンテンツ中身
    $create_tbl_contents = '
        CREATE TABLE IF NOT EXISTS `icon` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT COMMENT "主キー" ,
            `groupid` INTEGER NOT NULL COMMENT "メニュー番号",
            `sort` INTEGER COMMENT "ソート番号",
            `visible` TINYINT COMMENT "表示設定",
            `title` VARCHAR(100) COMMENT "文書タイトル",
            `anchor` INT COMMENT "作者ID：現在未使用",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="アイコン画像コンテンツを保存"';

    $db->ExecuteRow($create_tbl_contents);

    #テーブル作成 サムネイル
    $create_tbl_thumbnail = '
        CREATE TABLE IF NOT EXISTS `icon_thumbnail` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `icon_id` int(11) NOT NULL DEFAULT \'0\',
        `filepath` text NOT NULL,
        `created` datetime NOT NULL,
        `modefied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8
    ';
    $db->ExecuteRow($create_tbl_thumbnail);


}
?>