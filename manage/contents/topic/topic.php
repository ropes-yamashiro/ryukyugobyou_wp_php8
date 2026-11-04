<?php

#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#          2009/4/2 過去日付を１年→２年前に変更
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

/** ファイルをアップロードする: GET/POSTで[pageid],filekey名のtype=fileが必要 */
function topic_file_input($filekey, $errmode) {
    $user = $errmode ? webvalget('username') : null;
    $user = empty($user) ? 'testuser' : $user;
    $filename = $_FILES[$filekey]['name'];
    $size = $_FILES[$filekey]['size'];


    $info = pathinfo($filename);
    $ext = strtolower($info['extension']);
    $filesavename = "save/" . date("Y-m-d-H-i-s-")
            . $filekey . "-" . basename($_FILES[$filekey]['tmp_name']) . "-" . mt_rand(1, 100);
    $file = dirname(__FILE__) . "/" . $filesavename;

    imagesize($_FILES[$filekey]['tmp_name'], $file, 600, 480, $ext);
    if (in_array($ext, clist("gif,jpg,png,swf,swc,psd,tiff,bmp,iff,jp2,jpx,jb2,jpc,xbm"))) {
        list($x, $y) = getimagesize($file);
    }

    $hash = array('filename' => $filename, 'filekey' => $filekey
        , 'user' => $user, 'pid' => webparam('pageid'), 'x' => $x, 'y' => $y, 'file' => $filesavename);

    $db = getDB();
    $db->pdo->beginTransaction();

    $id = $db->Insert('topic_file', $hash);
    $db->pdo->commit();

    return $id;
}

function topic_userreadname($id) {
    $db = getDB();
    $sql = 'SELECT filename FROM topic_file WHERE id=:id';
    $row = $db->QueryLine($sql, array('id' => $id));
    return $row['filename'];
}

/** プレビュー */
function topic_preview() {
    $id = webparam('param');
    $pid = webparam('pageid');
    if (!$id || ! $pid)
        die("不正アクセス1:topic_preview");

    $db = getDB();
    $sql = 'SELECT * FROM topic WHERE groupid=:pid and id=:id';
    $row = $db->QueryLine($sql, array('pid' => $pid, 'id' => $id));

	
    $type = topic_datatype($row['type']);
    if ($type == TOPIC_TYPE_STR_FILE) {
        $_POST['param'] =$row['filename'];
        topic_picture();
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

        $loader = new tLoader(userpath(switchSpTemplate("topic/html/window.html")));
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
	
	if ($type == TOPIC_TYPE_STR_DOCUMENT_LINK) {
		
        $link = "/document.php?pid=" . $row['contents_group']
                . "&param=" . $row['contents_title'] . ($row['anchor'] == 0 ? "&cmd=anchor#id" . $row['contents_title'] : "");
		
        header("Location: " . $link);
        exit();
	}
}

function trep($id, $line) {
    $line = rep($line, 'fid', $id);

    $db = getDB();
    $sql = 'SELECT pid,filename,x,y FROM topic_file WHERE id=:id';
    $row = $db->QueryLine($sql, array('id' => $id));

    if (count($row) == 0)
        return null;
    $line = rep($line, 'name', $row['filename']);
    $line = rep($line, 'x', $row['x']);
    $line = rep($line, 'y', $row['y']);
    $line = rep($line, 'xmax', $row['x'] > 150 ? 150 : $row['x']);
    return rep($line, 'pageid', $row['pid']);
}

/** ファイルの表示 :GET/POSTで[id]と[pageid]の設定が必要 */
function topic_picture($isatt = false) {
    //dp("ファイル");
    $id = webparam('param');
    $pid = webparam('pageid');
    $mes = topic_dtable('notpic');

    if (!$id || ! $pid)
        return $mes;

    $db = getDB();
    $sql = 'SELECT file,filename FROM topic_file WHERE id=:id and pid=:pid';
    $row = $db->QueryLine($sql, array('id' => $id, 'pid' => $pid));

    if (count($row) == 0)
        return $mes;

    $filename = $row['filename'];

    $ua = $_SERVER['HTTP_USER_AGENT'];

    if (strstr($ua, 'MSIE') && ! strstr($ua, 'Opera')) {
        //$filename = mb_convert_encoding( $filename, 'UTF-8', "Shift-JIS" );
        $filename = urlencode($filename);
    }

    $doctype = getMIME($row['filename']);
    header('Pragma: public');

    $att = strpos($doctype, "image") === false ? " Attachment; " : "";
    $att = strpos($doctype, "pdf") !== false ? "" : $att;

    //$att = (strpos($doctype,"text") === false || $isatt == false) ? "" : " attachment; ";
    header('Content-Disposition:' . $att . 'filename="' . $filename . '";');
    #header('Content-Disposition:' . $att .'filename='. $filename . ';');
    header('Content-Type: ' . $doctype);
    header('Content-Length: ' . filesize(dirname(__FILE__) . '/' . $row['file']));

    echo readfile(dirname(__FILE__) . '/' . $row['file']);
    exit();
}

/** 初期アクセスが可能か? */
function topic_first() {
    $pid = topic_okauth();
    return webauth('topic_title' . $pid);
}

function topic_act() {
    $ret = new plugin_result(topic_action());
    $onload = webtopget("deftab");
    $ret->element['onload'] = "deftab($onload)";
    return $ret;
}

function topic_action() {

    define('testuser', 'test');
    $pid = topic_okauth();
    if (!webauth('topic_title' . $pid))
        return topic_dtable('err_access');
    $cmd = webparam("cmd", "list");
    switch ($cmd) {
        case "delfile" :

			$extension = pathinfo(webparam('param'), PATHINFO_EXTENSION);
			if (isset($_FILES['thumbnail']) && $extension == 'gif' || $extension == 'jpeg' || $extension == 'jpg'){
				$db = getDB();
				$delthum = 'DELETE FROM topic_thumbnail WHERE filepath=:filepath';
				$db->ExecuteRow($delthum, array('filepath' => webparam('param')));
				return topic_detail(webparam('dataid'));
			}else{

			    //椎本 修正　サムネイ削除できない物に対応
			    $db = getDB();
			    $delthum = 'DELETE FROM topic_thumbnail WHERE id=:id';

			    $db->ExecuteRow($delthum, array('id' => webparam('param')));
			    //椎本 修正　サムネイ削除できない物に対応　END
			    topic_file_del(webparam('param'), true);

                if (webparam('dataid') == null)
                    return topic_newtopic();
                else
                    return topic_detail(webparam('dataid'));
			}
        //case "reset" : topic_setup() ; return topic_dtable(format_db) ;
        case "delline" : return topic_line_del();
        case "upline" : return topic_line_move(true);
        case "downline" : return topic_line_move(false);
        case "saveinput" :
            topic_check_err();
            $did = topic_save();
            if (is_int($did)) {
                return topic_list();
            }
            else
                return $did;
        case "newtopic" :return topic_newtopic();
        case "detail" : return topic_detail(webparam('param'));
        case "pic" : return topic_picture(false);
        case "chvisible" :return topic_chvisible();
        case "chvisible_sp" :return topic_chvisible('sp');
        case "list" :
            topic_file_del(null, false);
            return topic_list();
        case "del" : return;
        case "saveonly" :
            topic_check_err();
            $did = topic_save();
            if (is_int($did)) {
                return topic_detail($did);
            }
            else

                return $did;
        case "ch_contents" :
            if (webparam(dataid) == null)
                return topic_newtopic();
            else
                return topic_detail(webparam('dataid'));
            ;
        case "preview" : return topic_preview();
        default : return topic_dtable('err_cmd', array('cmd' => $cmd));
    }
}

//======================================================

/** テーブルのHTMLのデータを取得します */
function topic_dtable($key, $hash = array()) {

    $file = userpath(switchSpTemplate("topic/html/table.html"));
    $label = "@topic_dtable";
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
function topic_dinput($key, $hash = array()) {

    $file = userpath(switchSpTemplate("topic/html/input.html"));
    $label = "@topic_dinput";
    $var = webparam($label);
    if (!$var) {
        $var = new tLoader($file);
        $_POST[$label] = $var;
    }
    $value = $var->getData($key);
    return phash($hash, $value);
}

//======================================================





function topic_menu($line, $admin, $title, $title_end = null) {

    #-------------------------------------
    #サイト情報
    $info = site_append('topic','menu');
    if ($info) {
        return $info($line,$admin,$title);
    }
    #------------------------------------

    $top = rep($title, 'title', topic_dtable('menu'));

    $db = getDB();
    $sql = 'SELECT * FROM topic_info';
    $res = $db->QueryRow($sql);

    foreach ($res as $row) {
        if (!webauth('topic_title' . $row['id']))
            continue;
        $item = rep($line, 'typex', "topic&id=" . $row['id']);
        $ret .= rep($item, 'title', $row['name']);
    }
    return $ret ? $top . $ret . $title_end : "";
}

function topic_auth() {
    $db = getDB();
    $sql = 'SELECT * FROM topic_info';
    $res = $db->QueryRow($sql);


    $line = topic_dtable('authinfo');
    $tret = array();
    foreach ($res as $row) {
        array_push($tret, array('key' => 'topic_title' . $row['id'], 'name' => $row['name'],
            "info" => rep($line, 'title', $row['name'])));
    }
    if (count($tret) > 0)
        $hash[topic_dtable('menu')] = $tret;
    return $hash;
}

/** テーブルの行を削除する */
function topic_line_del() {
    //★修正可能性
    $id = webparam('param', -1);
    $pid = webparam('pageid', -1);
    if ($pid == - 1 || $id == - 1)
        return dp("システムエラー");

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql = 'SELECT * FROM topic WHERE groupid=:pid and id=:id';
    $res = $db->QueryRow($sql, array('pid' => $pid, 'id' => $id));

    if (count($res) == 0) {
        $db->pdo->commit();
        return topic_dtable('err_access');
    }

    $sql2 = 'SELECT file FROM topic_file WHERE id=:id';
    $sql3 = 'DELETE FROM topic_file WHERE id=:fid';

    foreach ($res as $row) {

        foreach ($row as $fid) {
            $table = $db->QueryRow($sql2, array('id' => $fid));
            foreach ($table as $row) {
                if (file_exists($row['file']))
                    unlink($row['file']);
            }
            if ($fid)
                $db->ExecuteRow($sql3, array('fid' => $fid));
        }
    }

    $sql4 = 'DELETE FROM topic WHERE groupid=:pid and id=:id';
    $db->ExecuteRow($sql4, array('pid' => $pid, 'id' => $id));

    $db->pdo->commit();
    return topic_list();
}

function topic_chvisible($agent = '') {
    $id = webparam('param');
    $pid = webparam('pageid');
    if ($id == null || $pid == null)
        return topic_dtable('err_access');

    $db = getDB();
    $db->pdo->beginTransaction();

	if (isSPhone()) {
		$sql1 ='SELECT visible FROM topic WHERE groupid=:pid and id=:id';	//スマホ対応により、visibleのみでvisibleおよびvisible_spを更新するよう変更 2019.12.09 SB/K.Ariakta
	}
	else {
		$sql1 = $agent == '' ? 'SELECT visible FROM topic WHERE groupid=:pid and id=:id' : 'SELECT visible_sp AS visible FROM topic WHERE groupid=:pid and id=:id';
	}
    $row = $db->QueryLine($sql1, array('pid' => $pid, 'id' => $id));
    if (count($row) == 0) {
        $db->pdo->commit();
        return topic_list();
    }

	if (isSPhone()) {
		$sql2 = 'UPDATE topic SET visible=:ch, visible_sp=:ch WHERE id=:id and groupid=:pid';	//スマホ対応により、visibleのみでvisibleおよびvisible_spを更新するよう変更 2019.12.09 SB/K.Ariakta
	}
	else {
		$sql2 = $agent == '' ? 'UPDATE topic SET visible=:ch WHERE id=:id and groupid=:pid' : 'UPDATE topic SET visible_sp=:ch WHERE id=:id and groupid=:pid';
	}
    $db->ExecuteRow($sql2, array('pid' => $pid, 'id' => $id, 'ch' => $row['visible'] == "1" ? "0" : "1"));

    $db->pdo->commit();
    return topic_list();
}

function topic_paging() {
    $pid = webparam('pageid', webparam('id', 1));
    $count = webparam('count', 20);
    $cpage = webparam('pageno', 1);

    $db = getDB();
    $sql = 'SELECT count(*) FROM topic WHERE groupid=:pid';
    $total = $db->Count($sql, array('pid' => $pid));
    $line = topic_dtable('page');
    $current = topic_dtable('page_current');
    return html_paging($total, $count, $cpage, $line, $current);
}

/** ファイルの削除
 * ード1: 特定のファイルidのみ削除
 * ード2: 特定のUserファイルを削除 */
function topic_file_del($id = null, $ismode1 = true) {
    //★修正
    $db = getDB();
    if ($ismode1) {
        $sql1 = 'SELECT filekey, topic_id, file FROM topic_file WHERE id=:id';
        $info = $db->QueryLine($sql1, array('id' => $id));
        $file = dirname(__FILE__) . "/" . $info['file'];
        if (file_exists($file))
            unlink($file);

        $sql2 = 'DELETE FROM topic_file WHERE id=:id';
        $db->ExecuteRow($sql2, array('id' => $id));

 		if ($info['topic_id'] != null)
		{
	        $sql3 = 'UPDATE topic SET ' . $info['filekey'] . ' = 0 WHERE id = :id';

	        $db->ExecuteRow($sql3, array('id' => $info['topic_id']));
		}
    }
    else {
        $user = webvalget('username');
        $user = $user ? $user : 'testuser';

        $sql2 = 'SELECT file FROM topic_file WHERE `user`=:user';
        $infos = $db->QueryRow($sql2, array('user' => $user));
        foreach ($infos as $info) {
            $file = dirname(__FILE__) . "/" . $info['file'];
            if (file_exists($file)
                )unlink($file);
        }
        $sql3 = 'DELETE FROM topic_file WHERE user=:user';
        $db->ExecuteRow($sql3, array('user' => $user));
    }
}

function topic_check_err() {
    $p = $_POST;
    $mes .= $p['title'] == null ? topic_dinput('err_title') : "";
    $mes .= ! isDay($p, 'view_year', 'view_month', 'view_day') ? topic_dinput('err_nonday') : "";
    $mes .= ! array_key_exists('type', $p) ? topic_dinput('err_type') : "";

    switch ($p['type']) {

        case TOPIC_TYPE_LINK://リンク
            $mes .= ! array_key_exists('link', $p) ? topic_dinput('err_link') : "";
            $mes .= $p['link'] == null || $p['link'] == "" ? topic_dinput('err_link') : "";
            $mes .= ! array_key_exists('linkto', $p) ? topic_dinput('err_linkto') : "";
            $mes .= $p['linkto'] != 0 && $p['linkto'] != 1 ? topic_dinput('err_linkto2') : "";
            break;
        case TOPIC_TYPE_POP: //ポップアップ
            //★修正
            $mes .= ! array_key_exists('contents', $p) ? topic_dinput('err_contents') : "";
            $mes .= str_replace( array( " ", "　", "&nbsp;" ), "", $p['contents'] ) == "" ? topic_dinput('err_contents') : "";

            /** ファイルのアップロードサイズを調べる
             * =成功 1=単体ファイルサイズエラー 2=ゼロサイズファイルエラー
             * ー@ が　0=成功　1=総合ファイルエラー
             * り値：(キー => エラー番号) の連想配列 */
            $chfile = checkUploadSize();

            $chfmax = ini_get('upload_max_filesize');
            $chtmax = ini_get('post_max_size');

            $mes .= $chfile["@"] == 1 ? topic_dinput('err_file_total', array('size' => $chtmax)) : "";

            for ($i = 1; $i<=5; $i++) {
                if ($_FILES['picname' . $i]['name']) {

                    $mes .= $chfile['picname' . $i] == 2 ? topic_dinput('err_pic', array('num' => $i)) : "";

                    $mes .= $chfile['picname' . $i] == 1 ? topic_dinput('err_file_size', array('size' => $chfmax)) : "";

                    $ext = isImageExt($_FILES['picname' . $i]['name']);
                    if ($ext) {
                        $mes .= topic_dinput('err_ext', array('num' => $i, 'ext' => $ext));
                    }
                }
            }
            break;
        case TOPIC_TYPE_FILE://ファイル
            $chfile = checkUploadSize();

            $chfmax = ini_get('upload_max_filesize');
            $chtmax = ini_get('post_max_size');

            $mes .= $chfile["@"] == 1 ? topic_dinput('err_file_total', array('size' => $chtmax)) : "";

            if (!array_key_exists('filenameid', $p)) {
                if ($_FILES['filename']['name'] == "") {
                    $mes .= topic_dinput('err_file');
                } else {
                    if ($chfile['filename'] == 1)
                        $mes .= topic_dinput('err_file_size', array('size' => $chfmax));
                    if ($chfile['filename'] == 2)
                        $mes .= topic_dinput('err_filesize');
                }
            }
            break;
        case TOPIC_TYPE_TITLE:/* タイトルのみ */ break;
        case TOPIC_TYPE_DOCUMENT_LINK://コンテンツ：日付の確認
            break;
        default : $mes .= topic_dinput('err_type_value');
    }
    if ($mes != "") {
        $mes = topic_dinput('err_container', array('err' => $mes));
    }
    webvalset('topic_err', $mes);
    return $mes;
}

function topic_save() {

//$fname=dirname(__FILE__)."/_debug_topic_topic_save.txt";touch($fname, 0666);$fn=fopen($fname,"a");
//fwrite($fn, "--------------------\n");
    //dp($_POST);
    //★修正
    //エラーの確認
    $err = webvalget('topic_err');

    $haserr = !empty($err);
    $dataid = webparam('dataid');
//fwrite($fn, '$dataid='.print_r($dataid, true)."\n");
//fwrite($fn, '$err='.print_r($err, true)."\n");


    $type = webparam('type');
    $pop = "picname1,picname2,picname3,picname4,picname5";
    $file = "filename";

    if ($type == TOPIC_TYPE_DOCUMENT_LINK) {
        $_POST['linkto'] = $_POST['linktoD'];
    } elseif ($type == TOPIC_TYPE_POP) {
        $upvar = $pop;
        $delvar = $file;
    } //
    elseif ($type == TOPIC_TYPE_FILE) {
        $upvar = $file;
        $delvar = $pop;
    }  //
    else {
        $upvar = $delvar = $pop . "," . $file;
    }

    if (trim($_POST['summary']) == '<br />' || trim($_POST['summary']) == '&nbsp;')
        $_POST['summary'] = null;
    $_POST['summary'] = str_replace('\\"', '"', $_POST['summary']);

    if (trim($_POST['contents']) == '<br />' || trim($_POST['contents']) == '&nbsp;')
        $_POST['contents'] = null;
    $_POST['contents'] = str_replace('\\"', '"', $_POST['contents']);


    //ファイルのアップロード
    foreach (clist($upvar) as $filekey) {
        if (topic_enable_upfile($filekey, $type)) {
            $id = topic_file_input($filekey, $haserr);
            $_POST[$filekey . 'id'] = $id;
        } else {
            unset($_FILES[$filekey]);
        }
    }

    //エラーの場合はフォームの表示
    if ($haserr) {
        $page = $dataid ? topic_detail($dataid, $err) : topic_newtopic($err);
        return $page;
    }

	$file_nm = $_FILES['thumbnail']['name'];
	$extension = pathinfo($file_nm, PATHINFO_EXTENSION);
	if (isset($_FILES['thumbnail']) && $extension == 'gif' || $extension == 'jpeg' || $extension == 'jpg'){
		$filepath = date('Ymdhis') . '.' . $extension;
		if (is_uploaded_file($_FILES["thumbnail"]["tmp_name"])) {					
		//	move_uploaded_file($_FILES["thumbnail"]["tmp_name"], userpath("topic/thumbnail/"). $filepath);
			imagesize($_FILES["thumbnail"]["tmp_name"], userpath("topic/thumbnail/"). $filepath);	
		}
	}

    //DB処理
    $db = getDB();
    $db->pdo->beginTransaction();

    //いらないファイルの削除
    $selectfile_fordel = 'SELECT file FROM topic_file WHERE id=:id';
    $sql_delete = 'DELETE FROM topic_file WHERE id=:id';
    foreach (clist($delvar) as $one) {
        $fid = webparam($one . 'id');
        $sfile = $db->QueryLine($selectfile_fordel, array('id' => $fid));
        foreach ((array) $sfile as $infos) {
            foreach ($infos as $info) {
                $file = $info['file'];
                if (file_exists($file))
                    unlink($file);
            }
        }
        $db->ExecuteRow($sql_delete, array('id' => $fid));
        $_POST[$one] = null;
    }

    $DBpost = array();
    foreach ($_POST as $keypost => $valpost) {
        if (is_string($valpost) || is_int($valpost)) {
            $DBpost[$keypost] = $valpost;
        }
    }
//fwrite($fn, '$DBpost='.print_r($DBpost, true)."\n");

    $list = chash('picname1id,picname2id,picname3id,picname4id,picname5id,'
                    . 'filenameid,visible_sp,visible,contents_group,contents_title,anchor,pageid,link,linkto');
    $DBpost = whash($list, $DBpost);
//fwrite($fn, '$DBpost='.print_r($DBpost, true)."\n");
    $DBpost['pageid'] = intval(webparam('pageid'));
//fwrite($fn, '$DBpost='.print_r($DBpost, true)."\n");
    unset($DBpost['pageno'], $DBpost['count'], $DBpost['cmd'],
            $DBpost['param'], $DBpost['dataid'], $DBpost['sort'], $DBpost['linktoD'], $DBpost["visible_both"]);



    if ($dataid) {
        $update = '
            UPDATE topic
                SET title =:title,type =:type,
                    view_year =:view_year,view_month =:view_month,view_day =:view_day,
                    visible_sp=:visible_sp,visible=:visible,summary=:summary,link =:link,linkto =:linkto,contents =:contents,
                    picname1 =:picname1id,picname2 =:picname2id,picname3 =:picname3id,
                    picname4 =:picname4id,picname5=:picname5id,filename=:filenameid,contents_group=:contents_group,
                    contents_title=:contents_title,anchor=:anchor
                WHERE id=:dataid and groupid=:pageid';

        $DBpost['dataid'] = $dataid;
//fwrite($fn, '$DBpost='.print_r($DBpost, true)."\n");
//fwrite($fn, '$update='.print_r($update, true)."\n");
        $db->ExecuteRow($update, $DBpost);
        $did = $dataid;
    } else {
        $insert = '
            INSERT INTO topic
                (title,type,view_year,view_month,view_day,
                visible,visible_sp,summary,link,linkto,contents,picname1,picname2,picname3,picname4,
                picname5,filename,contents_group,contents_title,anchor,groupid)
            VALUES
                (:title,:type,:view_year,:view_month,:view_day,:visible,:visible_sp,:summary,
                 :link,:linkto,:contents,:picname1id,:picname2id,:picname3id,
                 :picname4id,:picname5id,:filenameid,:contents_group,:contents_title,:anchor,:pageid);';


        $did = $db->ExecuteRow($insert, $DBpost, true);
        $sql = 'UPDATE topic SET `sort`=:did WHERE `id`=:id';
//fwrite($fn, '$DBpost='.print_r($DBpost, true)."\n");
//fwrite($fn, '$insert='.print_r($insert, true)."\n");
        $db->ExecuteRow($sql, array('id' => $did, 'did' => $did));
    }
//fclose($fn);

	$sql_thum = 'SELECT * FROM topic_thumbnail WHERE topic_id=:id';
	$thumdata = $db->QueryLine($sql_thum, array('id' => $did));

	if(empty($thumdata) && !empty($filepath)){
		$sql_thum = 'INSERT INTO topic_thumbnail (topic_id, filepath, created) VALUES (:topic_id, :filepath, :created)';
        $db->ExecuteRow($sql_thum, array('topic_id' => $did, 'filepath' => $filepath, 'created' => date('Y/m/d h:i:s')));
	}

    #親データの適応
    $sql3 = 'UPDATE topic_file SET user="" ,topic_id =:did WHERE id=:id';
    foreach (clist($upvar) as $one) {
        $fid = webparam($one . 'id');
        $db->ExecuteRow($sql3, array('id' => $fid, 'did' => $did));
    }

    $db->pdo->commit();
    return intval($did);
}

function topic_detail($id, $err=null) {
    $pageid = webparam('pageid');
    $page = topic_dinput('input');
    $page = rep($page, 'count', webparam('count', 20));
    $page = rep($page, 'pageno', estr(webparam('pageno', 1)));

    $db = getDB();
    $sql = 'SELECT * FROM topic WHERE id=:id and groupid =:pageid';
    $hash = $db->QueryLine($sql, array('pageid' => $pageid, 'id' => $id));

    $sql = 'SELECT * FROM topic_thumbnail WHERE topic_id=:id';
    $thumbnail = $db->QueryLine($sql, array('id' => $id));

    $date = getdate();
    $y = $date['year'];
    $page = rep($page, "view_year", opdate($y - 2, $y + 3, $hash['view_year']));
    $page = rep($page, "view_month", opdate(1, 12, $hash['view_month']));
    $page = rep($page, "view_day", opdate(1, 31, $hash['view_day']));

    $bin = topic_dinput('file_exist');
    $noin = topic_dinput('file_nonexist');

	$page = rep($page, 'type', topic_dinput('input_type'));
    $var = clist("picname1,picname2,picname3,picname4,picname5,filename");
    foreach ($var as $name) {
        $fid = $hash[$name];
        $filename = topic_filename($name, $fid);
        $in = $filename ? $bin : $noin;
        $in = rep($in, 'filename', $filename);
        $in = rep($in, 'name', $name);
        $in = rep($in, 'fid', $fid);
        $page = rep($page, $name, $in);
    }


    $page = ephash(topic_title(), $page);
    $type = $hash['type'];
    if ($type == 0)
        $typevalue = "リンク";
    if ($type == 1)
        $typevalue = "ポップアップ";
    if ($type == 2)
        $typevalue = "ファイル";
    if ($type == 3)
        $typevalue = "タイトルのみ";
    if ($type == 4)
        $typevalue = "コンテンツ";

    //コピペする
    $list = container_info_select_all();
    $con = array();
    foreach ($list as $lone) {
        $con[$lone['id']] = $lone['title'];
    }
    foreach ($con as $ckey => $cval) {
        if (count(contents_info_select_by_topic($ckey)) == 0)
            unset($con[$ckey]);
    }
    $page = rep($page, 'contents_group', $con ? html_ophash2($con, webparam('contents_group', $hash['contents_group'])) : "");

    $list2 = contents_info_select_by_topic(webparam('contents_group', $hash['contents_group']));
    foreach ($list2 as $lone2) {
        $con2[$lone2['id']] = $lone2['title'];
    }
    $page = rep($page, 'contents_title', $con ? html_ophash2($con2, $hash['contents_title']) : "");
    //ここまで

    $info = site_append('topic','category');

    $category = ($info) ? $info($pageid) :'新着情報';

    $page = rep($page, 'err', $err);
//	$page = rep($page, 'type', topic_dinput('hidden') . $typevalue);
    $page = rep($page,'category',$category);
    $page = html_switch($page, 'type', 0, 4, $type);
    $page = html_switch($page, "linkto", 0, 1, $hash['linkto']);
    $page = html_switch($page, "linktoD", 0, 1, $hash['linkto']);
    $page = html_switch($page, 'anchor', 0, 1, $hash['anchor']);
    $page = html_switch($page, "visible", 0, 1, $hash['visible']);
    $page = html_switch($page, "visible_sp", 0, 1, $hash['visible_sp']);
	
	
	
	$hash['visible_both'] = ($hash['visible']=="1" && $hash['visible_sp']=="1" ? "1" : "0");
    $page = html_switch($page, "visible_both", 0, 1, $hash['visible_both']);

	if(!is_null($thumbnail)){
		$fname = $thumbnail['filepath'];
		$path = 'contents/topic/thumbnail/'.$thumbnail['filepath'];
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

function topic_newtopic($err = null) {
    $page = topic_dinput('input');
    $pageid = webparam('pageid');
    $page = rep($page, 'err', $err ? $err : webvalget('err'));

    $info = site_append('topic','type');
    $inputtype = $info ? $info(webparam('pageid',1)) : topic_dinput('input_type');

    $page = rep($page, 'type', $inputtype);
    $page = rep($page, 'count', webparam('count', 20));
    $page = rep($page, 'pageno', estr(webparam('pageno', 1)));


    $date = getdate();
    $y = $date['year'];
    $page = rep($page, 'view_year', opdate($y - 1, $y + 3, webparam('view_year', $y)));
    $page = rep($page, 'view_month', opdate(1, 12, webparam('view_month', $date['mon'])));
    $page = rep($page, 'view_day', opdate(1, 31, webparam('view_day', $date['mday'])));
    $page = rep($page, 'title', webparam('title'));

    $bin = topic_dinput('file_exist');
    $noin = topic_dinput('file_nonexist');
    $var = clist("picname1,picname2,picname3,picname4,picname5,filename");
    foreach ($var as $name) {
        $fid = webparam($name . 'id');
        $filename = topic_filename($name, $fid);
        $in = $filename ? $bin : $noin;
        $in = rep($in, 'filename', $filename);
        $in = rep($in, 'name', $name);
        $in = rep($in, 'fid', $fid);
        $page = rep($page, $name, $in);
    }

    //コピペする
    $list = container_info_select_all();
    $first;
    $con = array();
    foreach ($list as $lone) {
        $con[$lone['id']] = $lone['title'];
        $first = $first ? $first : $lone['id'];
    }
    foreach ($con as $ckey => $cval) {
        if (count(contents_info_select_by_topic($ckey)) == 0)
            unset($con[$ckey]);
    }
    $page = rep($page, 'contents_group', $con ? html_ophash2($con, webparam('contents_group')) : "");

    $list2 = contents_info_select_by_topic(webparam('contents_group', $first));
    foreach ($list2 as $lone2) {
        $con2[$lone2['id']] = $lone2['title'];
    }
    $page = rep($page, 'contents_title', $con ? html_ophash2($con2, webparam('contents_title')) : "");
    //ここまで

    $page = ephash(topic_title(), $page);


    $type = webparam('type', 0);
    $info = site_append('topic','category');
    $category = ($info) ? $info($pageid) :'新着情報&nbsp;&nbsp;';

    $page = rep($page, 'summary', webparam('summary'));
    $page = rep($page, 'contents', estr(webparam('contents')));
    $page = rep($page, 'link', estr(webparam('link')));
    $page = rep($page, 'id', null);
    $page = rep($page, 'sort', null);
    $page = rep($page, 'category', $category);
    $page = html_switch($page, 'type', 0, 4, $type);
    $page = html_switch($page, 'linkto', 0, 1, webparam('linkto', 0));
    $page = html_switch($page, 'linktoD', 0, 1, webparam('linktoD', 0));
    $page = html_switch($page, 'anchor', 0, 1, webparam('anchor', 0));
    $page = html_switch($page, 'visible', 0, 1, webparam('visible', 0));
    $page = html_switch($page, 'visible_sp', 0, 1, webparam('visible_sp', 0));

	$dspthumb = '<input type=file name="thumbnail" onFocus="this.className=\'on\';" onBlur="this.className=\'off\';" size="37"  />';
	$page = rep($page, 'thumbnail', $dspthumb);

    webtopset('deftab', $type);

    return $page;
}

/** アップロードしたファイルが有効か */
function topic_enable_upfile($filekey, $type) {
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

/** ファイル名を取得します */
function topic_filename($filekey, $id = null) {

    if ($id == null)
        $id = webparam('id');
    if ($id == null && $filekey == null)
        return null;

    //ファイルがアップしていれば、そこから
    if (array_key_exists($filekey, $_FILES)) {
        return $_FILES[$filekey]['name'];
    } else {
        $db = getDB();
        $sql = 'SELECT filename FROM topic_file WHERE id=:id';
        $row = $db->QueryLine($sql, array('id' => $id));
        return empty($row) ? null : $row['filename'];
    }
    return null;
}

//==================================================
//　リスト関連
//=== 完了 ======================================================

/** テーブルの表示順を入れ替えます */
function topic_line_move($isup) {

    $sort = webparam('param');
    $pid = webparam('pageid');

    if ($sort == null || $pid == null)
        return topic_dtable('err_access');

    $db = getDB();
    $db->pdo->beginTransaction();

    $sql = $isup ? 'SELECT id,sort FROM topic WHERE sort >= :sort and groupid=:groupid ORDER BY sort ASC LIMIT 0,2' : 'SELECT id,sort FROM topic WHERE sort <= :sort and groupid=:groupid ORDER BY sort DESC LIMIT 0,2';

    $tbl = $db->QueryRow($sql, array('groupid' => $pid, 'sort' => $sort));
    if (count($tbl) <= 1) {
        $db->pdo->commit();
        return topic_list();
    };


    $param1 = array('pid' => $pid, 'destsort' => $tbl[1]['sort'], 'startid' => $tbl[0]['id']);
    $param2 = array('pid' => $pid, 'sort' => $tbl[0]['sort'], 'destid' => $tbl[1]['id']);
    $db->ExecuteRow('UPDATE topic SET sort=:destsort WHERE id=:startid and groupid=:pid', $param1);
    $db->ExecuteRow('UPDATE topic SET sort=:sort WHERE id=:destid and groupid=:pid', $param2);
    $db->pdo->commit();
    return topic_list();
}

/** 初期値用：アクセス可能な権限を取り出します */
function topic_okauth() {
    $pid = webparam('pageid');
    if (!$pid)
        $pid = webparam('id', 1);
    if (webauth('topic_title' . $pid) && topic_hasmenu($pid)) {
        $_POST['pageid'] = $pid;
        return $pid;
    }

    $db = getDB();
    $row = $db->QueryLine('SELECT max(id) FROM topic_info');
    $count = $row['max(id)'];
    for ($i == 0; $i <= $count; $i++) {
        if (webauth('topic_title' . $i) && topic_hasmenu($i)) {
            $_POST['pageid'] = $i;
            return $i;
        }
    }
    return -1;
}

function topic_list($count = null, $pageno = null) {

    $db = getDB();
    $pid = intval(webparam('pageid', topic_okauth()));

    $menu_row = $db->QueryLine("SELECT * FROM topic_info WHERE `id` =:id", array('id' => $pid));
    if (count($menu_row) == 0)
        return topic_dtable('err_access2');

    if ($count == null)
        $count = webparam('count', 20);
    if ($pageno == null
        )$pageno = webparam('pageno', 1);
    $top = $count * ($pageno - 1);

    $sql = 'SELECT * FROM topic WHERE groupid =:groupid ORDER BY sort DESC';
    $tbl = $db->QueryLimit($sql, $top, $count, array('groupid' => $pid));

    if (count($tbl) == 0) {
        $view = topic_dtable('notdata');
    } else {
        $line = topic_dtable('table_line');

        foreach ($tbl as $row) {
            #細かいデータ--------------------------------------------------------
            $row['visible'] = $row['visible'] == 0 ? "" : 'checked';
            $row['visible_sp'] = $row['visible_sp'] == 0 ? "" : 'checked';
            $row['color'] = intval($i) % 2 + 1; $i++;
            $row['pageid'] = $pid;

            #ラインを設定する----------------------------------------------------
            if ($row['type'] ==TOPIC_TYPE_POP) {
                $tline = rep($line, 'preview', topic_dtable('preview2'));
            }
            if ($row['type'] == TOPIC_TYPE_DOCUMENT_LINK) {
                if ($row['anchor'] == 1)
                    $tline = rep($line, 'preview', topic_dtable('preview3'));
                else {
                    $tline = rep($line, 'preview', topic_dtable('preview4'));
                }
            }
            if ($row['type'] != TOPIC_TYPE_TITLE) {
                $tline = rep($line, 'preview', topic_dtable('preview'));
            } else {
                $tline = rep($line, 'preview', null);
            }

            if ($row['type'] == TOPIC_TYPE_LINK)
                $row['type'] = "リンク";
            if ($row['type'] == TOPIC_TYPE_POP)
                $row['type'] = "ポップアップ";
            if ($row['type'] == TOPIC_TYPE_FILE)
                $row['type'] = "ファイル";
            if ($row['type'] == TOPIC_TYPE_TITLE)
                $row['type'] = "タイトルのみ";
            if ($row['type'] == TOPIC_TYPE_DOCUMENT_LINK)
                $row['type'] = "ブログ";

            $view .= ephash($row, $tline);
        }
    }
    //
    //祖ほのかの必須項目

    $info = site_append('topic','category');
    $category = ($info) ? $info($pid) :'新着情報';

    $html_page = topic_dtable('table');
    $html_page = rep($html_page,'category',$category);
    $html_page = rep($html_page, 'pagename', $menu_row['name']);
    $html_page = rep($html_page, 'view', $view);
    $html_page = rep($html_page, 'paging', topic_paging());
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
function topic_hasmenu($pid) {
    $db = getDB();
    $sql = 'SELECT count(*) FROM topic_info WHERE id=:id';
    $tbl = $db->QueryRow($sql, array('id' => $pid));
    return intval($tbl[0]['count(*)']) > 0;
}

/**
 * ドキュメント番号から値を取り出す
 * @param int $type
 * @return string
 */
function topic_datatype($type) {
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
 * @return hash pid,topicname
 */
function topic_title() {
    $db = getDB();
    $sql = 'SELECT `id` as `pid`,`name` as `topicname` FROM topic_info where `id`=:id';
    $param = array('id' => webparam('pageid', 0));
    $tbl = $db->QueryRow($sql, $param);
    if (count($tbl) == 0)
        return topic_dtable('err_access2');
    return $tbl[0];
}

/**
 * 新着情報のテーブルを作成する
 */
function topic_tbl_setup($force = false) {

    $db = getDB();
    #テーブル削除
    if ($force) {
        $drop = 'DROP TABLE IF EXISTS `topic_info`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `topic`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `topic_file`;';
        $db->ExecuteRow($drop);
        $drop = 'DROP TABLE IF EXISTS `topic_thumbnail`;';
        $db->ExecuteRow($drop);
    }

    #ファイル用
    $create_tbl_file = '
        CREATE TABLE IF NOT EXISTS `topic_file` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT  COMMENT "主キー",
            `user` VARCHAR(64) COMMENT "ファイル使用者",
            `filekey` VARCHAR(32) COMMENT "ファイルキー",
            `filename` VARCHAR(128) COMMENT "保存ファイル名",
            `pid` INTEGER  COMMENT "親ID",
            `topic_id` INTEGER COMMENT "トピックID",
            `file` VARCHAR(128) COMMENT "アップファイル名",
            `x` INTEGER COMMENT "ファイル横幅",
            `y` INTEGER COMMENT "ファイル縦幅",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="新着情報のファイル用"
    ';
    $db->ExecuteRow($create_tbl_file);

    #コンテンツメニュー
    $create_tbl_menu = '
        CREATE TABLE IF NOT EXISTS `topic_info` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT  COMMENT "主キー",
            `name` VARCHAR(20) COMMENT "メニュー名",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="新着情報のメニューを保存"';

    $db->ExecuteRow($create_tbl_menu);

    #テーブル作成 コンテンツ中身
    $create_tbl_contents = '
        CREATE TABLE IF NOT EXISTS `topic` (
            `id` INTEGER NOT NULL  AUTO_INCREMENT COMMENT "主キー" ,
            `groupid` INTEGER NOT NULL COMMENT "メニュー番号",
            `sort` INTEGER COMMENT "ソート番号",
            `visible` TINYINT COMMENT "表示設定(PC)",
            `visible_sp` TINYINT COMMENT "表示設定(SP)",
            `type` INTEGER COMMENT "内容の種類",
            `title` VARCHAR(100) COMMENT "文書タイトル",

            `view_year` SMALLINT COMMENT "表示年",
            `view_month` SMALLINT COMMENT "表示月",
            `view_day` SMALLINT COMMENT "表示日",
            `summary` TEXT  COMMENT "コンテンツ内容",

            `link` VARCHAR(200) COMMENT "リンクアドレス",
            `linkto` TINYINT COMMENT "ターゲット種類",

            `contents` TEXT COMMENT "コンテンツ種類",
            `picname1` INTEGER COMMENT  "イメージ１",
            `picname2` INTEGER COMMENT  "イメージ２",
            `picname3` INTEGER COMMENT  "イメージ３",
            `picname4` INTEGER COMMENT  "イメージ４",
            `picname5` INTEGER COMMENT  "イメージ５",

            `filename` INTEGER COMMENT "ファイル名",
            `contents_group` INTEGER COMMENT "ブログコンテンツのグループID",
            `contents_title` INTEGER COMMENT "ブログコンテンツのデータID",

            `anchor` INT COMMENT "作者ID：現在未使用",
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COMMENT="新着情報コンテンツを保存"';

    $db->ExecuteRow($create_tbl_contents);

    #テーブル作成 サムネイル
    $create_tbl_thumbnail = '
        CREATE TABLE IF NOT EXISTS `topic_thumbnail` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `topic_id` int(11) NOT NULL DEFAULT \'0\',
        `filepath` text NOT NULL,
        `created` datetime NOT NULL,
        `modefied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8
    ';
    $db->ExecuteRow($create_tbl_thumbnail);


}
?>