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
function topicimg_preview() {
    $id = webparam('param');
    $pid = webparam('pageid');
    if (!$id || ! $pid)
        die("不正アクセス1:topicimg_preview");

    $db = getDB();
    $sql = 'SELECT * FROM topicimg WHERE groupid=:pid and id=:id';
    $row = $db->QueryLine($sql, array('pid' => $pid, 'id' => $id));


    $type = topicimg_datatype($row['type']);
    if ($type == TOPIC_TYPE_STR_FILE) {
        $_POST['param'] =$row['filename'];
        topicimg_picture();
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

        $loader = new tLoader(userpath(switchSpTemplate("topicimg/html/window.html")));
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
function topicimg_first() {
    $pid = topicimg_okauth();
    return webauth('topicimg_title' . $pid);
}

function topicimg_act() {
    $ret = new plugin_result(topicimg_action());;
    $onload = webtopget("deftab");
    $ret->element['onload'] = "deftab($onload)";
    return $ret;
}

function topicimg_action() {

    define('testuser', 'test');
    $pid = topicimg_okauth();
    if (!webauth('topicimg_title' . $pid))
        return topicimg_dtable('err_access');
    $cmd = webparam("cmd", "list");
    switch ($cmd) {
        case "delfile" :

			$extension = pathinfo(webparam('param'), PATHINFO_EXTENSION);
			if (isset($_FILES['thumbnail']) && $extension == 'gif' || $extension == 'jpeg' || $extension == 'jpg' || $extension == 'png'){
				$db = getDB();
				$delthum = 'DELETE FROM topicimg_thumbnail WHERE filepath=:filepath';
				$db->ExecuteRow($delthum, array('filepath' => webparam('param')));
				return topicimg_detail(webparam('dataid'));
			}
        case "delline" : return topicimg_line_del();
        case "upline" : return topicimg_line_move(true);
        case "downline" : return topicimg_line_move(false);
        case "saveinput" :
            topicimg_check_err();
            $did = topicimg_save();
            if (is_int($did)) {
                return topicimg_list();
            }
            else
                return $did;
        case "newtopicimg" :return topicimg_newtopicimg();
        case "detail" : return topicimg_detail(webparam('param'));
        case "pic" : return topicimg_picture(false);
        case "chvisible" :return topicimg_chvisible();
        case "del" : return;
        case "list" : return topicimg_list();

        case "saveonly" :
            topicimg_check_err();
            $did = topicimg_save();
            if (is_int($did)) {
                return topicimg_detail($did);
            }
            else
                return $did;
        case "ch_contents" :
            if (webparam(dataid) == null)
                return topicimg_newtopicimg();
            else
                return topicimg_detail(webparam('dataid'));
            ;
        case "preview" : return topicimg_preview();
        default : return topicimg_dtable('err_cmd', array('cmd' => $cmd));
    }
}

//======================================================

/** テーブルのHTMLのデータを取得します */
function topicimg_dtable($key, $hash = array()) {

    $file = userpath(switchSpTemplate("topicimg/html/table.html"));
    $label = "@topicimg_dtable";
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
function topicimg_dinput($key, $hash = array()) {

    $file = userpath(switchSpTemplate("topicimg/html/input.html"));
    $label = "@topicimg_dinput";
    $var = webparam($label);
    if (!$var) {
        $var = new tLoader($file);
        $_POST[$label] = $var;
    }
    $value = $var->getData($key);
    return phash($hash, $value);
}

//======================================================





function topicimg_menu($line, $admin, $title, $title_end = null) {

    #-------------------------------------
    #サイト情報
    $info = site_append('topicimg','menu');
    if ($info) {
        return $info($line,$admin,$title);
    }
    #------------------------------------

    $top = rep($title, 'title', topicimg_dtable('menu'));

    $db = getDB();
    $sql = 'SELECT * FROM topicimg_info';
    $res = $db->QueryRow($sql);

    foreach ($res as $row) {
        if (!webauth('topicimg_title' . $row['id']))
            continue;
        $item = rep($line, 'typex', "topicimg&id=" . $row['id']);
        $ret .= rep($item, 'title', $row['name']);
    }
    return $ret ? $top . $ret . $title_end : "";
}

function topicimg_auth() {
    $db = getDB();
    $sql = 'SELECT * FROM topicimg_info';
    $res = $db->QueryRow($sql);


    $line = topicimg_dtable('authinfo');
    $tret = array();
    foreach ($res as $row) {
        array_push($tret, array('key' => 'topicimg_title' . $row['id'], 'name' => $row['name'],
            info => rep($line, 'title', $row['name'])));
    }
    if (count($tret) > 0)
        $hash[topicimg_dtable('menu')] = $tret;
    return $hash;
}

/** テーブルの行を削除する */
function topicimg_line_del() {
    //★修正可能性
    $id = webparam('param', -1);
    $pid = webparam('pageid', -1);
    if ($pid == - 1 || $id == - 1)
        return dp("システムエラー");

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql = 'SELECT * FROM topicimg WHERE groupid=:pid and id=:id';
    $res = $db->QueryRow($sql, array('pid' => $pid, 'id' => $id));

    if (count($res) == 0) {
        $db->pdo->commit();
        return topicimg_dtable('err_access');
    }

    $sql2 = 'SELECT * FROM topicimg_thumbnail WHERE topicimg_id=:topicimg_id';
    $res_2 = $db->QueryRow($sql2, array('topicimg_id' => $res[0]['id']));

    $delthum = 'DELETE FROM topicimg_thumbnail WHERE id=:id';
    $db->ExecuteRow($delthum, array('id' => $res_2[0]['id']));
    $path = 'contents/topicimg/thumbnail/' . $res_2[0]['filepath'];
    unlink($path);

    $sql4 = 'DELETE FROM topicimg WHERE groupid=:pid and id=:id';
    $db->ExecuteRow($sql4, array('pid' => $pid, 'id' => $id));

    $db->pdo->commit();
    return topicimg_list();
}

function topicimg_chvisible() {
    $id = webparam('param');
    $pid = webparam('pageid');
    if ($id == null || $pid == null)
        return topicimg_dtable('err_access');

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql1 = 'SELECT visible FROM topicimg WHERE groupid=:pid and id=:id';
    $row = $db->QueryLine($sql1, array('pid' => $pid, 'id' => $id));
    if (count($row) == 0) {
        $db->pdo->commit();
        return topicimg_list();
    }

    // すでに『表示』としているデータが他にある場合、エラーとする
    if ($row['visible'] == 0) {
        $sql1_1 = 'SELECT count(*) AS ct FROM topicimg WHERE groupid=:pid and visible=1';
        $row = $db->QueryLine($sql1_1, array('pid' => $pid));
        if ($row['ct'] > 0) {
            webvalset('topicimg_err', 'err');
            $db->pdo->commit();
            return topicimg_list();
        }
    }

    $sql2 = 'UPDATE topicimg SET visible=:ch WHERE id=:id and groupid=:pid';
    $db->ExecuteRow($sql2, array('pid' => $pid, 'id' => $id, 'ch' => $row['visible'] == "1" ? "0" : "1"));

    $db->pdo->commit();
    return topicimg_list();
}

function topicimg_paging() {
    $pid = webparam('pageid', webparam('id', 1));
    $count = webparam('count', 20);
    $cpage = webparam('pageno', 1);

    $db = getDB();
    $sql = 'SELECT count(*) FROM topicimg WHERE groupid=:pid';
    $total = $db->Count($sql, array('pid' => $pid));
    $line = topicimg_dtable('page');
    return html_paging($total, $count, $cpage, $line);
}

function topicimg_check_err() {
    $p = $_POST;
    $mes = $p['title'] == null ? topicimg_dinput('err_title') : "";
    $mes .= $_FILES['thumbnail']['name'] == null && $p['dataid'] == null ? topicimg_dinput('err_img') : "";

    if ($mes != "") {
        $mes = topicimg_dinput('err_container', array('err' => $mes));
    }
    webvalset('topicimg_err', $mes);
    return $mes;
}

function topicimg_save() {

    //dp($_POST);
    //★修正
    //エラーの確認
    $err = webvalget('topicimg_err');

    $haserr = !empty($err);
    $dataid = webparam('dataid');

    $type = webparam('type');

    if (trim($_POST['title']) == '<br />' || trim($_POST['title']) == '&nbsp;')
        $_POST['title'] = null;
    $_POST['title'] = str_replace('\\"', '"', $_POST['title']);

    //エラーの場合はフォームの表示
    if ($haserr) {
        $page = $dataid ? topicimg_detail($dataid, $err) : topicimg_newtopicimg($err);
        return $page;
    }

	$file_nm = $_FILES['thumbnail']['name'];
    $extension = pathinfo($file_nm, PATHINFO_EXTENSION);
	if (isset($_FILES['thumbnail']) && $extension == 'gif' || $extension == 'jpeg' || $extension == 'jpg' || $extension == 'png'){
		$filepath = date('Ymdhis') . '.' . $extension;
		if (is_uploaded_file($_FILES["thumbnail"]["tmp_name"])) {
			move_uploaded_file($_FILES["thumbnail"]["tmp_name"], userpath("topicimg/thumbnail/"). $filepath);
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
            UPDATE topicimg
                SET title =:title, anchor=:anchor
                WHERE id=:dataid and groupid=:pageid';

        $DBpost['dataid'] = $dataid;
        $db->ExecuteRow($update, $DBpost);
        $did = $dataid;
    } else {
        $insert = '
            INSERT INTO topicimg
                (title,anchor,groupid)
            VALUES
                (:title,:anchor,:pageid);';
        $did = $db->ExecuteRow($insert, $DBpost, true);
        $sql = 'UPDATE topicimg SET `sort`=:did WHERE `id`=:id';
        $db->ExecuteRow($sql, array('id' => $did, 'did' => $did));
    }

	$sql_thum = 'SELECT * FROM topicimg_thumbnail WHERE topicimg_id=:id';
	$thumdata = $db->QueryLine($sql_thum, array('id' => $did));

	if(empty($thumdata) && !empty($filepath)){
		$sql_thum = 'INSERT INTO topicimg_thumbnail (topicimg_id, filepath, created) VALUES (:topicimg_id, :filepath, :created)';
        $db->ExecuteRow($sql_thum, array('topicimg_id' => $did, 'filepath' => $filepath, 'created' => date('Y/m/d h:i:s')));
	}
    $db->pdo->commit();
    return intval($did);
}
function topicimg_detail($id, $err=null) {
    $pageid = webparam('pageid');
    $page = topicimg_dinput('input');
    $page = rep($page, 'count', webparam('count', 20));
    $page = rep($page, 'pageno', estr(webparam('pageno', 1)));

    $db = getDB();
    $sql = 'SELECT * FROM topicimg WHERE id=:id and groupid =:pageid';
    $hash = $db->QueryLine($sql, array('pageid' => $pageid, 'id' => $id));

    $sql = 'SELECT * FROM topicimg_thumbnail WHERE topicimg_id=:id';
    $thumbnail = $db->QueryLine($sql, array('id' => $id));

    $date = getdate();
    $y = $date['year'];
    $page = rep($page, view_year, opdate($y - 2, $y + 3, $hash['view_year']));
    $page = rep($page, view_month, opdate(1, 12, $hash['view_month']));
    $page = rep($page, view_day, opdate(1, 31, $hash['view_day']));

    $bin = topicimg_dinput('file_exist');
    $noin = topicimg_dinput('file_nonexist');

    $page = ephash(topicimg_title(), $page);

    //コピペする
    $list = container_info_select_all();
    $con = array();
    foreach ($list as $lone) {
        $con[$lone['id']] = $lone['title'];
    }
    foreach ($con as $ckey => $cval) {
        if (count(contents_info_select_by_topicimg($ckey)) == 0)
            unset($con[$ckey]);
    }
    $page = rep($page, 'contents_group', $con ? html_ophash2($con, webparam('contents_group', $hash['contents_group'])) : "");

    $list2 = contents_info_select_by_topicimg(webparam('contents_group', $hash['contents_group']));
    foreach ($list2 as $lone2) {
        $con2[$lone2['id']] = $lone2['title'];
    }
    $page = rep($page, 'contents_title', $con ? html_ophash2($con2, $hash['contents_title']) : "");
    //ここまで

    $info = site_append('topicimg','category');

    $category = ($info) ? $info($pageid) :'トピック画像';

    $page = rep($page, 'err', $err);
    $page = rep($page, 'type', topicimg_dinput('hidden') . $typevalue);
    $page = rep($page,'category',$category);
    $page = html_switch($page, 'anchor', 0, 1, $hash['anchor']);
    $page = html_switch($page, "visible", 0, 1, $hash['visible']);
    $page = html_switch($page, "img_pos", 0, 1, $hash['img_pos']);

	if(!is_null($thumbnail)){
		$fname = $thumbnail['filepath'];
		$path = 'contents/topicimg/thumbnail/'.$thumbnail['filepath'];
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

function topicimg_newtopicimg($err = null) {
    $page = topicimg_dinput('input');
    $pageid = webparam('pageid');
    $page = rep($page, 'err', $err ? $err : webvalget('err'));

    $info = site_append('topicimg','type');
    $inputtype = $info ? $info(webparam('pageid',1)) : topicimg_dinput('input_type');

    $page = rep($page, 'type', $inputtype);
    $page = rep($page, 'count', webparam('count', 20));
    $page = rep($page, 'pageno', estr(webparam('pageno', 1)));


    $date = getdate();
    $y = $date['year'];
    $page = rep($page, 'view_year', opdate($y - 1, $y + 3, webparam('view_year', $y)));
    $page = rep($page, 'view_month', opdate(1, 12, webparam('view_month', $date['mon'])));
    $page = rep($page, 'view_day', opdate(1, 31, webparam('view_day', $date['mday'])));
    $page = rep($page, 'title', webparam('title'));

    $bin = topicimg_dinput('file_exist');
    $noin = topicimg_dinput('file_nonexist');
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
        if (count(contents_info_select_by_topicimg($ckey)) == 0)
            unset($con[$ckey]);
    }
    $page = rep($page, 'contents_group', $con ? html_ophash2($con, webparam('contents_group')) : "");

    $list2 = contents_info_select_by_topicimg(webparam('contents_group', $first));
    foreach ($list2 as $lone2) {
        $con2[$lone2['id']] = $lone2['title'];
    }
    $page = rep($page, 'contents_title', $con ? html_ophash2($con2, webparam('contents_title')) : "");
    //ここまで

    $page = ephash(topicimg_title(), $page);


    $type = webparam('type', 0);
    $info = site_append('topicimg','category');
    $category = ($info) ? $info($pageid) :'トピック画像&nbsp;&nbsp;';

    $page = rep($page, 'title', webparam('title'));
    $page = rep($page, 'summary', webparam('summary'));
    $page = rep($page, 'topicimg_name', webparam('topicimg_name'));
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
function topicimg_enable_upfile($filekey, $type) {
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
function topicimg_line_move($isup) {

    $sort = webparam('param');
    $pid = webparam('pageid');

    if ($sort == null || $pid == null)
        return topicimg_dtable('err_access');

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql = $isup ? 'SELECT id,sort FROM topicimg WHERE sort >= :sort and groupid=:groupid ORDER BY sort ASC LIMIT 0,2' : 'SELECT id,sort FROM topicimg WHERE sort <= :sort and groupid=:groupid ORDER BY sort DESC LIMIT 0,2';

    $tbl = $db->QueryRow($sql, array('groupid' => $pid, 'sort' => $sort));
    if (count($tbl) <= 1) {
        $db->pdo->commit();
        return topicimg_list();
    };


    $param1 = array('pid' => $pid, 'destsort' => $tbl[1]['sort'], 'startid' => $tbl[0]['id']);
    $param2 = array('pid' => $pid, 'sort' => $tbl[0]['sort'], 'destid' => $tbl[1]['id']);
    $db->ExecuteRow('UPDATE topicimg SET sort=:destsort WHERE id=:startid and groupid=:pid', $param1);
    $db->ExecuteRow('UPDATE topicimg SET sort=:sort WHERE id=:destid and groupid=:pid', $param2);
    $db->pdo->commit();
    return topicimg_list();
}

/** 初期値用：アクセス可能な権限を取り出します */
function topicimg_okauth() {
    $pid = webparam('pageid');
    if (!$pid)
        $pid = webparam('id', 1);
    if (webauth('topicimg_title' . $pid) && topicimg_hasmenu($pid)) {
        $_POST['pageid'] = $pid;
        return $pid;
    }

    $db = getDB();
    $row = $db->QueryLine('SELECT max(id) FROM topicimg_info');
    $count = $row['max(id)'];
    for ($i == 0; $i <= $count; $i++) {
        if (webauth('topicimg_title' . $i) && topicimg_hasmenu($i)) {
            $_POST['pageid'] = $i;
            return $i;
        }
    }
    return -1;
}

function topicimg_list($count = null, $pageno = null) {

    $db = getDB();
    $pid = intval(webparam('pageid', topicimg_okauth()));

    $menu_row = $db->QueryLine("SELECT * FROM topicimg_info WHERE `id` =:id", array('id' => $pid));
    if (count($menu_row) == 0)
        return topicimg_dtable('err_access2');

    if ($count == null)
        $count = webparam('count', 20);
    if ($pageno == null
        )$pageno = webparam('pageno', 1);
    $top = $count * ($pageno - 1);

    $sql = 'SELECT * FROM topicimg WHERE groupid =:groupid ORDER BY sort DESC';
    $tbl = $db->QueryLimit($sql, $top, $count, array('groupid' => $pid));

    if (count($tbl) == 0) {
        $view = topicimg_dtable('notdata');
    } else {
        $line = topicimg_dtable('table_line');

        // エラーのチェック
        if (isset($_SESSION['@system']['topicimg_err'])) {
            if ($_SESSION['@system']['topicimg_err'] == 'err') {
                $view .= topicimg_dtable('visible_err');
                unset($_SESSION['@system']['topicimg_err']);
            }
        }

        foreach ($tbl as $row) {
            #細かいデータ--------------------------------------------------------
            $row['visible'] = $row['visible'] == 0 ? "" : 'checked';
            $row['pageid'] = $pid;

            #ラインを設定する----------------------------------------------------
            $tline = rep($line, 'preview', topicimg_dtable('preview'));

            $view .= ephash($row, $tline);
        }
    }

    //
    //祖ほのかの必須項目

    $info = site_append('topicimg','category');
    $category = ($info) ? $info($pid) :'トピック画像';

    $html_page = topicimg_dtable('table');
    $html_page = rep($html_page,'category',$category);
    $html_page = rep($html_page, 'pagename', $menu_row['name']);
    $html_page = rep($html_page, 'view', $view);
    $html_page = rep($html_page, 'paging', topicimg_paging());
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
function topicimg_hasmenu($pid) {
    $db = getDB();
    $sql = 'SELECT count(*) FROM topicimg_info WHERE id=:id';
    $tbl = $db->QueryRow($sql, array('id' => $pid));
    return intval($tbl[0]['count(*)']) > 0;
}

/**
 * ドキュメント番号から値を取り出す
 * @param int $type
 * @return string
 */
function topicimg_datatype($type) {
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
 * @return hash pid,topicimgname
 */
function topicimg_title() {
    $db = getDB();
    $sql = 'SELECT `id` as `pid`,`name` as `topicimgname` FROM topicimg_info where `id`=:id';
    $param = array('id' => webparam('pageid', 0));
    $tbl = $db->QueryRow($sql, $param);
    if (count($tbl) == 0)
        return topicimg_dtable('err_access2');
    return $tbl[0];
}

/**
 * トピック画像のテーブルを作成する
 */
function topicimg_tbl_setup($force = false) {

    $db = getDB();
    #テーブル削除
    if ($force) {
        $drop = 'DROP TABLE IF EXISTS `topicimg_info`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `topicimg`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `topicimg_thumbnail`;';
        $db->ExecuteRow($drop);
    }

    #コンテンツメニュー
    $create_tbl_menu = '
        CREATE TABLE IF NOT EXISTS `topicimg_info` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT  COMMENT "主キー",
            `name` VARCHAR(20) COMMENT "メニュー名",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="トピック画像のメニューを保存"';

    $db->ExecuteRow($create_tbl_menu);

    #テーブル作成 コンテンツ中身
    $create_tbl_contents = '
        CREATE TABLE IF NOT EXISTS `topicimg` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT COMMENT "主キー" ,
            `groupid` INTEGER NOT NULL COMMENT "メニュー番号",
            `sort` INTEGER COMMENT "ソート番号",
            `visible` TINYINT COMMENT "表示設定",
            `title` VARCHAR(100) COMMENT "文書タイトル",
            `anchor` INT COMMENT "作者ID：現在未使用",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="トピック画像コンテンツを保存"';

    $db->ExecuteRow($create_tbl_contents);

    #テーブル作成 サムネイル
    $create_tbl_thumbnail = '
        CREATE TABLE IF NOT EXISTS `topicimg_thumbnail` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `topicimg_id` int(11) NOT NULL DEFAULT \'0\',
        `filepath` text NOT NULL,
        `created` datetime NOT NULL,
        `modefied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8
    ';
    $db->ExecuteRow($create_tbl_thumbnail);


}
?>