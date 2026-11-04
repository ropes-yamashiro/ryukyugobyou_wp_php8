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

define('CUSTOMER_TYPE_LINK', 0);
define('CUSTOMER_TYPE_POP', 1);
define('CUSTOMER_TYPE_FILE', 2);
define('CUSTOMER_TYPE_TITLE', 3);
define('CUSTOMER_TYPE_DOCUMENT_LINK', 4);

define('CUSTOMER_TYPE_STR_LINK', 'link');
define('CUSTOMER_TYPE_STR_POP', 'pop');
define('CUSTOMER_TYPE_STR_FILE', 'file');
define('CUSTOMER_TYPE_STR_TITLE', 'title');
define('CUSTOMER_TYPE_STR_DOCUMENT_LINK', 'contents');

require_once(userpath("document/document.php"));

define('CUSTOMER_USER_ENC', 'SJIS-win');
define('CUSTOMER_VIEW_ENC', 'Shift_JIS');
define('CUSTOMER_SYSTEM_ENC', 'utf-8');

/** プレビュー */
function customer_preview() {
    $id = webparam('param');
    $pid = webparam('pageid');
    if (!$id || ! $pid)
        die("不正アクセス1:customer_preview");

    $db = getDB();
    $sql = 'SELECT * FROM customer WHERE groupid=:pid and id=:id';
    $row = $db->QueryLine($sql, array('pid' => $pid, 'id' => $id));


    $type = customer_datatype($row['type']);
    if ($type == CUSTOMER_TYPE_STR_FILE) {
        $_POST['param'] =$row['filename'];
        customer_picture();
        exit();
    }
    if ($type == CUSTOMER_TYPE_STR_LINK) {
        header("Location: " . $row['link']);
        exit();
    }

    if ($type == CUSTOMER_TYPE_STR_POP) {
        $row['pageid'] = $pid;
        $row['year'] = $row['view_year'];
        $row['month'] = $row['view_month'];
        $row['day'] = $row['view_day'];

        $loader = new tLoader(userpath(switchSpTemplate("customer/html/window.html")));
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
function customer_first() {
    $pid = customer_okauth();
    return webauth('customer_title' . $pid);
}

function customer_act() {
    $ret = new plugin_result(customer_action());;
    $onload = webtopget("deftab");
    $ret->element['onload'] = "deftab($onload)";
    return $ret;
}

function customer_action() {

    define('testuser', 'test');
    $pid = customer_okauth();
    if (!webauth('customer_title' . $pid))
        return customer_dtable('err_access');
    $cmd = webparam("cmd", "list");
    switch ($cmd) {
        case "delfile" :

			$extension = pathinfo(webparam('param'), PATHINFO_EXTENSION);
			if (isset($_FILES['thumbnail']) && $extension == 'gif' || $extension == 'jpeg' || $extension == 'jpg'){
				$db = getDB();
				$delthum = 'DELETE FROM customer_thumbnail WHERE filepath=:filepath';
				$db->ExecuteRow($delthum, array('filepath' => webparam('param')));
				return customer_detail(webparam('dataid'));
			}
        case "delline" : return customer_line_del();
        case "upline" : return customer_line_move(true);
        case "downline" : return customer_line_move(false);
        case "saveinput" :
            customer_check_err();
            $did = customer_save();
            if (is_int($did)) {
                return customer_list();
            }
            else
                return $did;
        case "newcustomer" :return customer_newcustomer();
        case "detail" : return customer_detail(webparam('param'));
        case "pic" : return customer_picture(false);
        case "chvisible" :return customer_chvisible();
        case "del" : return;
        case "list" : return customer_list();

        case "saveonly" :
            customer_check_err();
            $did = customer_save();
            if (is_int($did)) {
                return customer_detail($did);
            }
            else
                return $did;
        case "ch_contents" :
            if (webparam(dataid) == null)
                return customer_newcustomer();
            else
                return customer_detail(webparam('dataid'));
            ;
        case "preview" : return customer_preview();
        default : return customer_dtable('err_cmd', array('cmd' => $cmd));
    }
}

//======================================================

/** テーブルのHTMLのデータを取得します */
function customer_dtable($key, $hash = array()) {

    $file = userpath(switchSpTemplate("customer/html/table.html"));
    $label = "@customer_dtable";
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
function customer_dinput($key, $hash = array()) {

    $file = userpath(switchSpTemplate("customer/html/input.html"));
    $label = "@customer_dinput";
    $var = webparam($label);
    if (!$var) {
        $var = new tLoader($file);
        $_POST[$label] = $var;
    }
    $value = $var->getData($key);
    return phash($hash, $value);
}

//======================================================





function customer_menu($line, $admin, $title, $title_end = null) {

    #-------------------------------------
    #サイト情報
    $info = site_append('customer','menu');
    if ($info) {
        return $info($line,$admin,$title);
    }
    #------------------------------------

    $top = rep($title, 'title', customer_dtable('menu'));

    $db = getDB();
    $sql = 'SELECT * FROM customer_info';
    $res = $db->QueryRow($sql);

    foreach ($res as $row) {
        if (!webauth('customer_title' . $row['id']))
            continue;
        $item = rep($line, 'typex', "customer&id=" . $row['id']);
        $ret .= rep($item, 'title', $row['name']);
    }
    return $ret ? $top . $ret . $title_end : "";

    $sql = 'SELECT * FROM icon_info';
    $res = $db->QueryRow($sql);

    foreach ($res as $row) {
        if (!webauth('customer_icon_edit' . $row['id']))
            continue;
        $item = rep($line, 'typex', "icon&id=" . $row['id']);
        $ret .= rep($item, 'title', $row['name']);
    }

    return $ret;
}

function customer_auth() {
    $db = getDB();
    $sql = 'SELECT * FROM customer_info';
    $res = $db->QueryRow($sql);


    $line = customer_dtable('authinfo');
    $tret = array();
    foreach ($res as $row) {
        array_push($tret, array('key' => 'customer_title' . $row['id'], 'name' => $row['name'],
            info => rep($line, 'title', $row['name'])));
    }
    if (count($tret) > 0) {
        $sql = 'SELECT * FROM icon_info';
        $res = $db->QueryRow($sql);
        foreach ($res as $row) {
            array_push($tret, array('key' => 'customer_icon_edit' . $row['id'], 'name' => $row['name'],
            info => rep($line, 'title', $row['name'])));
        }
        $hash[customer_dtable('menu')] = $tret;
    }

    return $hash;
}

/** テーブルの行を削除する */
function customer_line_del() {
    //★修正可能性
    $id = webparam('param', -1);
    $pid = webparam('pageid', -1);
    if ($pid == - 1 || $id == - 1)
        return dp("システムエラー");

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql = 'SELECT * FROM customer WHERE groupid=:pid and id=:id';
    $res = $db->QueryRow($sql, array('pid' => $pid, 'id' => $id));

    if (count($res) == 0) {
        $db->pdo->commit();
        return customer_dtable('err_access');
    }

    $sql2 = 'SELECT * FROM customer_thumbnail WHERE customer_id=:customer_id';
    $res_2 = $db->QueryRow($sql2, array('customer_id' => $res[0]['id']));

    $delthum = 'DELETE FROM customer_thumbnail WHERE id=:id';
    $db->ExecuteRow($delthum, array('id' => $res_2[0]['id']));
    $path = 'contents/customer/thumbnail/' . $res_2[0]['filepath'];
    //unlink($path);
    if (file_exists($path . '_re.jpg')) {
        unlink($path . '_re.jpg');
    }
/*    if (file_exists($path . '_resp.jpg')) {
        unlink($path . '_resp.jpg');
    }
*/
    $sql4 = 'DELETE FROM customer WHERE groupid=:pid and id=:id';
    $db->ExecuteRow($sql4, array('pid' => $pid, 'id' => $id));

    $db->pdo->commit();
    return customer_list();
}

function customer_chvisible() {
    $id = webparam('param');
    $pid = webparam('pageid');
    if ($id == null || $pid == null)
        return customer_dtable('err_access');

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql1 = 'SELECT visible FROM customer WHERE groupid=:pid and id=:id';
    $row = $db->QueryLine($sql1, array('pid' => $pid, 'id' => $id));
    if (count($row) == 0) {
        $db->pdo->commit();
        return customer_list();
    }

    $sql2 = 'UPDATE customer SET visible=:ch WHERE id=:id and groupid=:pid';
    $db->ExecuteRow($sql2, array('pid' => $pid, 'id' => $id, 'ch' => $row['visible'] == "1" ? "0" : "1"));

    $db->pdo->commit();
    return customer_list();
}

function customer_paging() {
    $pid = webparam('pageid', webparam('id', 1));
    $count = webparam('count', 20);
    $cpage = webparam('pageno', 1);

    $db = getDB();
    $sql = 'SELECT count(*) FROM customer WHERE groupid=:pid';
    $total = $db->Count($sql, array('pid' => $pid));
    $line = customer_dtable('page');
    return html_paging($total, $count, $cpage, $line);
}

function customer_check_err() {
    if (trim($_POST['title']) == '<br />' || trim($_POST['title']) == '&nbsp;')
        $_POST['title'] = null;
    $_POST['title'] = str_replace('\\"', '"', $_POST['title']);

    if (trim($_POST['summary']) == '<br />' || trim($_POST['summary']) == '&nbsp;')
        $_POST['summary'] = null;
    $_POST['summary'] = str_replace('\\"', '"', $_POST['summary']);

    if (trim($_POST['customer_name']) == '<br />' || trim($_POST['customer_name']) == '&nbsp;')
        $_POST['customer_name'] = null;
    $_POST['customer_name'] = str_replace('\\"', '"', $_POST['customer_name']);

    $p = $_POST;
    $mes .= $p['title'] == null ? customer_dinput('err_title') : "";
    $mes .= ! isDay($p, 'view_year', 'view_month', 'view_day') ? customer_dinput('err_nonday') : "";
    $mes .= $p['summary'] == null ? customer_dinput('err_summary') : "";

    if ($mes != "") {
        $mes = customer_dinput('err_container', array('err' => $mes));
    }
    webvalset('customer_err', $mes);
    return $mes;
}

function customer_save() {

    //dp($_POST);
    //★修正
    //エラーの確認
    $err = webvalget('customer_err');

    $haserr = !empty($err);
    $dataid = webparam('dataid');

    $type = webparam('type');

    //エラーの場合はフォームの表示
    if ($haserr) {
        $page = $dataid ? customer_detail($dataid, $err) : customer_newcustomer($err);
        return $page;
    }

	$file_nm = $_FILES['thumbnail']['name'];
	$extension = pathinfo($file_nm, PATHINFO_EXTENSION);
	if (isset($_FILES['thumbnail']) && $extension == 'gif' || $extension == 'jpeg' || $extension == 'jpg'){
		$filepath = date('Ymdhis') . '.' . $extension;
		if (is_uploaded_file($_FILES["thumbnail"]["tmp_name"])) {
			move_uploaded_file($_FILES["thumbnail"]["tmp_name"], userpath("customer/thumbnail/"). $filepath);
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

    $list = chash('visible,anchor,pageid');
    $DBpost = whash($list, $DBpost);
    $DBpost['pageid'] = intval(webparam('pageid'));
    if (!isset($DBpost['img_pos'])) $DBpost['img_pos'] = 0;
    unset($DBpost['pageno'], $DBpost['count'], $DBpost['cmd'],
            $DBpost['param'], $DBpost['dataid'], $DBpost['sort'], $DBpost['linktoD'], $DBpost['contents']);


    //お客様名の改行コードを削除
    if (isset($DBpost['customer_name'])) {
        $DBpost['customer_name'] = trim(str_replace('<br />', null, $DBpost['customer_name']));
    }

    //タイトルの改行コードを削除
    if (isset($DBpost['title'])) {
        $DBpost['title'] = trim(str_replace('<br />', null, $DBpost['title']));
    }

    if ($dataid) {
        $update = '
            UPDATE customer
                SET title =:title,view_year =:view_year,view_month =:view_month,view_day =:view_day,
                    visible=:visible,summary=:summary,customer_name=:customer_name,img_pos=:img_pos,anchor=:anchor,icon=:icon
                WHERE id=:dataid and groupid=:pageid';

        $DBpost['dataid'] = $dataid;
        $db->ExecuteRow($update, $DBpost);
        $did = $dataid;
    } else {
        $insert = '
            INSERT INTO customer
                (title,view_year,view_month,view_day,
                visible,summary,customer_name,img_pos,anchor,groupid,icon)
            VALUES
                (:title,:view_year,:view_month,:view_day,:visible,:summary,:customer_name,:img_pos,:anchor,:pageid,:icon);';
        $did = $db->ExecuteRow($insert, $DBpost, true);
        $sql = 'UPDATE customer SET `sort`=:did WHERE `id`=:id';
        $db->ExecuteRow($sql, array('id' => $did, 'did' => $did));
    }

	$sql_thum = 'SELECT * FROM customer_thumbnail WHERE customer_id=:id';
	$thumdata = $db->QueryLine($sql_thum, array('id' => $did));

	if(empty($thumdata) && !empty($filepath)){
		$sql_thum = 'INSERT INTO customer_thumbnail (customer_id, filepath, created) VALUES (:customer_id, :filepath, :created)';
        $db->ExecuteRow($sql_thum, array('customer_id' => $did, 'filepath' => $filepath, 'created' => date('Y/m/d h:i:s')));
	}
    $db->pdo->commit();
    return intval($did);
}
function customer_detail($id, $err=null) {
    $pageid = webparam('pageid');
    $page = customer_dinput('input');
    $page = rep($page, 'count', webparam('count', 20));
    $page = rep($page, 'pageno', estr(webparam('pageno', 1)));

    $db = getDB();
    $sql = 'SELECT * FROM customer WHERE id=:id and groupid =:pageid';
    $hash = $db->QueryLine($sql, array('pageid' => $pageid, 'id' => $id));

    $sql = 'SELECT * FROM customer_thumbnail WHERE customer_id=:id';
    $thumbnail = $db->QueryLine($sql, array('id' => $id));

    $date = getdate();
    $y = $date['year'];
    $page = rep($page, view_year, opdate($y - 2, $y + 3, $hash['view_year']));
    $page = rep($page, view_month, opdate(1, 12, $hash['view_month']));
    $page = rep($page, view_day, opdate(1, 31, $hash['view_day']));

    // アイコン
    $sql = 'SELECT id, title FROM icon';
    $hash_icon = $db->QueryRow($sql);

    $icon_list['--'] = 0;
    if (count($hash_icon) > 0) {
        foreach ($hash_icon as $value) {
            $icon_list[$value['title']] = $value['id'];
        }
    }

    $page = rep($page, icon, html_ophash($icon_list, $hash['icon']));


    $bin = customer_dinput('file_exist');
    $noin = customer_dinput('file_nonexist');

    $page = ephash(customer_title(), $page);

    //コピペする
    $list = container_info_select_all();
    $con = array();
    foreach ($list as $lone) {
        $con[$lone['id']] = $lone['title'];
    }
    foreach ($con as $ckey => $cval) {
        if (count(contents_info_select_by_customer($ckey)) == 0)
            unset($con[$ckey]);
    }
    $page = rep($page, 'contents_group', $con ? html_ophash2($con, webparam('contents_group', $hash['contents_group'])) : "");

    $list2 = contents_info_select_by_customer(webparam('contents_group', $hash['contents_group']));
    foreach ($list2 as $lone2) {
        $con2[$lone2['id']] = $lone2['title'];
    }
    $page = rep($page, 'contents_title', $con ? html_ophash2($con2, $hash['contents_title']) : "");
    //ここまで

    $info = site_append('customer','category');

    $category = ($info) ? $info($pageid) :'お客様の声';

    $page = rep($page, 'err', $err);
    $page = rep($page, 'type', customer_dinput('hidden') . $typevalue);
    $page = rep($page,'category',$category);
    $page = html_switch($page, 'anchor', 0, 1, $hash['anchor']);
    $page = html_switch($page, "visible", 0, 1, $hash['visible']);
    $page = html_switch($page, "img_pos", 0, 1, $hash['img_pos']);

	if(!is_null($thumbnail)){
		$fname = $thumbnail['filepath'];
		$path = 'contents/customer/thumbnail/'.$thumbnail['filepath'];
		$dspthumb = '
			&nbsp;&nbsp;' . $fname . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:delfile(\'' . $fname . '\',\'' . $fname . '\');">削除</a>
			&nbsp;&nbsp;<a href="' . $path . '" target="_blank">表示</a>';
	}else{
		$dspthumb = '<input type=file name="thumbnail" onFocus="this.className=\'on\';" onBlur="this.className=\'off\';" size="37"  />	';
	}
	$page = rep($page, 'thumbnail', $dspthumb);

    webtopset('deftab', $type);
    $page = phash($hash, $page);

    return $page;
}

function customer_newcustomer($err = null) {
    $page = customer_dinput('input');
    $pageid = webparam('pageid');
    $page = rep($page, 'err', $err ? $err : webvalget('err'));

    $info = site_append('customer','type');
    $inputtype = $info ? $info(webparam('pageid',1)) : customer_dinput('input_type');

    $page = rep($page, 'type', $inputtype);
    $page = rep($page, 'count', webparam('count', 20));
    $page = rep($page, 'pageno', estr(webparam('pageno', 1)));


    $date = getdate();
    $y = $date['year'];
    $page = rep($page, 'view_year', opdate($y - 1, $y + 3, webparam('view_year', $y)));
    $page = rep($page, 'view_month', opdate(1, 12, webparam('view_month', $date['mon'])));
    $page = rep($page, 'view_day', opdate(1, 31, webparam('view_day', $date['mday'])));
    $page = rep($page, 'title', webparam('title'));

    // アイコン
    $db = getDB();
    $sql = 'SELECT id, title FROM icon';
    $hash_icon = $db->QueryRow($sql);

    $icon_list['--'] = 0;
    if (count($hash_icon) > 0) {
        foreach ($hash_icon as $value) {
            $icon_list[$value['title']] = $value['id'];
        }
    }

    $page = rep($page, icon, html_ophash($icon_list, $hash['icon']));

    $bin = customer_dinput('file_exist');
    $noin = customer_dinput('file_nonexist');
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
        if (count(contents_info_select_by_customer($ckey)) == 0)
            unset($con[$ckey]);
    }
    $page = rep($page, 'contents_group', $con ? html_ophash2($con, webparam('contents_group')) : "");

    $list2 = contents_info_select_by_customer(webparam('contents_group', $first));
    foreach ($list2 as $lone2) {
        $con2[$lone2['id']] = $lone2['title'];
    }
    $page = rep($page, 'contents_title', $con ? html_ophash2($con2, webparam('contents_title')) : "");
    //ここまで

    $page = ephash(customer_title(), $page);


    $type = webparam('type', 0);
    $info = site_append('customer','category');
    $category = ($info) ? $info($pageid) :'お客様の声&nbsp;&nbsp;';

    $page = rep($page, 'title', webparam('title'));
    $page = rep($page, 'summary', webparam('summary'));
    $page = rep($page, 'customer_name', webparam('customer_name'));
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
function customer_enable_upfile($filekey, $type) {
    if (!array_key_exists($filekey, $_FILES)
        )return false;
    if ($_FILES[$filekey]['size'] == 0
        )return false;
    if ($type == CUSTOMER_TYPE_POP) {
        if (isImageExt($_FILES[$filekey]['name']) != null)
            return false;
    }
    return true;
}

//==================================================
//　リスト関連
//=== 完了 ======================================================

/** テーブルの表示順を入れ替えます */
function customer_line_move($isup) {

    $sort = webparam('param');
    $pid = webparam('pageid');

    if ($sort == null || $pid == null)
        return customer_dtable('err_access');

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql = $isup ? 'SELECT id,sort FROM customer WHERE sort >= :sort and groupid=:groupid ORDER BY sort ASC LIMIT 0,2' : 'SELECT id,sort FROM customer WHERE sort <= :sort and groupid=:groupid ORDER BY sort DESC LIMIT 0,2';

    $tbl = $db->QueryRow($sql, array('groupid' => $pid, 'sort' => $sort));
    if (count($tbl) <= 1) {
        $db->pdo->commit();
        return customer_list();
    };


    $param1 = array('pid' => $pid, 'destsort' => $tbl[1]['sort'], 'startid' => $tbl[0]['id']);
    $param2 = array('pid' => $pid, 'sort' => $tbl[0]['sort'], 'destid' => $tbl[1]['id']);
    $db->ExecuteRow('UPDATE customer SET sort=:destsort WHERE id=:startid and groupid=:pid', $param1);
    $db->ExecuteRow('UPDATE customer SET sort=:sort WHERE id=:destid and groupid=:pid', $param2);
    $db->pdo->commit();
    return customer_list();
}

/** 初期値用：アクセス可能な権限を取り出します */
function customer_okauth() {
    $pid = webparam('pageid');
    if (!$pid)
        $pid = webparam('id', 1);
    if (webauth('customer_title' . $pid) && customer_hasmenu($pid)) {
        $_POST['pageid'] = $pid;
        return $pid;
    }

    $db = getDB();
    $row = $db->QueryLine('SELECT max(id) FROM customer_info');
    $count = $row['max(id)'];
    for ($i == 0; $i <= $count; $i++) {
        if (webauth('customer_title' . $i) && customer_hasmenu($i)) {
            $_POST['pageid'] = $i;
            return $i;
        }
    }
    return -1;
}

function customer_list($count = null, $pageno = null) {

    $db = getDB();
    $pid = intval(webparam('pageid', customer_okauth()));

    $menu_row = $db->QueryLine("SELECT * FROM customer_info WHERE `id` =:id", array('id' => $pid));
    if (count($menu_row) == 0)
        return customer_dtable('err_access2');

    if ($count == null)
        $count = webparam('count', 20);
    if ($pageno == null
        )$pageno = webparam('pageno', 1);
    $top = $count * ($pageno - 1);

    $sql = 'SELECT * FROM customer WHERE groupid =:groupid ORDER BY sort DESC';
    $tbl = $db->QueryLimit($sql, $top, $count, array('groupid' => $pid));

    if (count($tbl) == 0) {
        $view = customer_dtable('notdata');
    } else {
        $line = customer_dtable('table_line');
        foreach ($tbl as $row) {
            // タイトルのhtmlタグを削除
             $row['title'] = preg_replace('/(\<span)(.*?)\"\>/', '', $row['title']);
             $row['title'] = str_replace('</span>', '', $row['title']);
             $row['title'] = str_replace('&nbsp;', ' ', $row['title']);

            #細かいデータ--------------------------------------------------------
            $row['visible'] = $row['visible'] == 0 ? "" : 'checked';
            $row['color'] = intval($i) % 2 + 1; $i++;
            $row['pageid'] = $pid;

            #ラインを設定する----------------------------------------------------
            if ($row['type'] ==CUSTOMER_TYPE_POP) {
                $tline = rep($line, 'preview', customer_dtable('preview2'));
            }
            if ($row['type'] == CUSTOMER_TYPE_DOCUMENT_LINK) {
                if ($row['anchor'] == 1)
                    $tline = rep($line, 'preview', customer_dtable('preview3'));
                else {
                    $tline = rep($line, 'preview', customer_dtable('preview4'));
                }
            }
            if ($row['type'] != CUSTOMER_TYPE_TITLE) {
                $tline = rep($line, 'preview', customer_dtable('preview'));
            } else {
                $tline = rep($line, 'preview', null);
            }

            if ($row['type'] == CUSTOMER_TYPE_LINK)
                $row['type'] = "リンク";
            if ($row['type'] == CUSTOMER_TYPE_POP)
                $row['type'] = "ポップアップ";
            if ($row['type'] == CUSTOMER_TYPE_FILE)
                $row['type'] = "ファイル";
            if ($row['type'] == CUSTOMER_TYPE_TITLE)
                $row['type'] = "タイトルのみ";
            if ($row['type'] == CUSTOMER_TYPE_DOCUMENT_LINK)
                $row['type'] = "ブログ";

            $view .= ephash($row, $tline);
        }
    }
    //
    //祖ほのかの必須項目

    $info = site_append('customer','category');
    $category = ($info) ? $info($pid) :'お客様の声';

    $html_page = customer_dtable('table');
    $html_page = rep($html_page,'category',$category);
    $html_page = rep($html_page, 'pagename', $menu_row['name']);
    $html_page = rep($html_page, 'view', $view);
    $html_page = rep($html_page, 'paging', customer_paging());
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
function customer_hasmenu($pid) {
    $db = getDB();
    $sql = 'SELECT count(*) FROM customer_info WHERE id=:id';
    $tbl = $db->QueryRow($sql, array('id' => $pid));
    return intval($tbl[0]['count(*)']) > 0;
}

/**
 * ドキュメント番号から値を取り出す
 * @param int $type
 * @return string
 */
function customer_datatype($type) {

    if ($type == CUSTOMER_TYPE_LINK)
        return CUSTOMER_TYPE_STR_LINK;
    if ($type == CUSTOMER_TYPE_POP)
        return CUSTOMER_TYPE_STR_POP;
    if ($type == CUSTOMER_TYPE_FILE)
        return CUSTOMER_TYPE_STR_FILE;
    if ($type == CUSTOMER_TYPE_TITLE)
        return CUSTOMER_TYPE_STR_TITLE;
    if ($type == CUSTOMER_TYPE_DOCUMENT_LINK)
        return CUSTOMER_TYPE_STR_DOCUMENT_LINK;
}

/**
 * タイトルの名前を取り出す
 * @return hash pid,customername
 */
function customer_title() {
    $db = getDB();
    $sql = 'SELECT `id` as `pid`,`name` as `customername` FROM customer_info where `id`=:id';
    $param = array('id' => webparam('pageid', 0));
    $tbl = $db->QueryRow($sql, $param);
    if (count($tbl) == 0)
        return customer_dtable('err_access2');
    return $tbl[0];
}

/**
 * お客様の声のテーブルを作成する
 */
function customer_tbl_setup($force = false) {

    $db = getDB();
    #テーブル削除
    if ($force) {
        $drop = 'DROP TABLE IF EXISTS `customer_info`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `customer`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `customer_thumbnail`;';
        $db->ExecuteRow($drop);
    }

    #コンテンツメニュー
    $create_tbl_menu = '
        CREATE TABLE IF NOT EXISTS `customer_info` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT  COMMENT "主キー",
            `name` VARCHAR(20) COMMENT "メニュー名",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="お客様の声のメニューを保存"';

    $db->ExecuteRow($create_tbl_menu);

    #テーブル作成 コンテンツ中身
    $create_tbl_contents = '
        CREATE TABLE IF NOT EXISTS `customer` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT COMMENT "主キー" ,
            `groupid` INTEGER NOT NULL COMMENT "メニュー番号",
            `sort` INTEGER COMMENT "ソート番号",
            `visible` TINYINT COMMENT "表示設定",
            `type` INTEGER COMMENT "内容の種類",
            `view_year` SMALLINT COMMENT "表示年",
            `view_month` SMALLINT COMMENT "表示月",
            `view_day` SMALLINT COMMENT "表示日",
            `title` TEXT COMMENT "文書タイトル",
            `summary` TEXT  COMMENT "コンテンツ内容",
            `customer_name` TEXT COMMENT "お客様名",
            `img_pos` TINYINT NOT NULL DEFAULT "0" COMMENT "画像位置",
            `icon` INT COMMENT "アイコンID",
            `anchor` INT COMMENT "作者ID：現在未使用",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="お客様の声コンテンツを保存"';

    $db->ExecuteRow($create_tbl_contents);

    #テーブル作成 サムネイル
    $create_tbl_thumbnail = '
        CREATE TABLE IF NOT EXISTS `customer_thumbnail` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL DEFAULT \'0\',
        `filepath` text NOT NULL,
        `created` datetime NOT NULL,
        `modefied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8
    ';
    $db->ExecuteRow($create_tbl_thumbnail);


}
?>