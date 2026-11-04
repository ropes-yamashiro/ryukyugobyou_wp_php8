<?php

userload('topic/topic.php');

function topic_view($name) {
    $config = confpath("topic/config.ini");
    $conf = new cssConfig($config);
    $_POST['conf'] = $conf;
    $type = webparam('type');

    if ($type == 'pop') {topicview_popup($conf, $name); exit;    }
    if ($type == 'file') {topicview_userfile(); exit;    }
    if ($type == 'image') {topicview_userpicture();exit;    }
    if ($type == 'imagepop') {topicview_userimagepop($conf, $name);exit;    }
    return topicview_create_page($conf, $name);
}

/** トピック名を取り出す */
function topicview_selector() {return basename(__FILE__);}

/** DBクラスを取得します */
function topicview_sql() {
}

//============================================
// ここに表示用のコードを書く必要あり

/** 指定したファイルが使用できるか? */
function topicview_enableid($filemode = true) {

    $pageid = webparam('pageid');
    $id = webparam('param');

    if (!$pageid || ! $id)
        return false;

    $db = getDB();

    //ファイルの場合
    if ($filemode)
    {
        $sql = 'SELECT topic_id FROM topic_file WHERE pid=:pid and id=:id';
        $row = $db->QueryLine($sql,array('pid' => $pageid, 'id' => $id));
        $id = $row['topic_id'];
    }

    //データがあるか
    $sql = !strpos($_SERVER['REQUEST_URI'],'sp') ? 'SELECT count(*) FROM topic WHERE visible=1 and id=:id' : 'SELECT count(*) FROM topic WHERE visible_sp=1 and id=:id';
    $count = $db->Count($sql, array('id' => $id));
    return $count > 0;
}

/** イメージ用のポップアップを作成します */
function topicview_userimagepop($conf, $name) {
    if (!topicview_enableid()){
        return false;
    }

    $data = topicview_topicloader($conf, webparam('pageid'), $name);
    //データ取得
    $line = $data['windowimagepop'];

    //イメージのデータ
    $fid = webparam('param');
    $pid = webparam('pageid');

    $db = getDB();
    $sql = 'SELECT filename,x,y FROM topic_file WHERE pid=:pid and id=:id';
    $detail =$db->QueryLine($sql, array('id' => $fid, 'pid' => $pid));


    $line = rep($line, 'link', "?type=image&amp;pageid=" . $pid . "&amp;param=" . $fid);
    $line = phash($detail, $line);

    if(TOPIC_VIEW_ENC != TOPIC_SYSTEM_ENC && !strpos($_SERVER['REQUEST_URI'],'sp')){
        $line = str_replace(TOPIC_SYSTEM_ENC,TOPIC_VIEW_ENC, $line);
        $line = mb_convert_encoding($line, TOPIC_USER_ENC,TOPIC_SYSTEM_ENC) ;
    }

    if (!strpos($_SERVER['REQUEST_URI'],'sp')) {
        pout($page,TOPIC_USER_ENC);
    } else {
        pout($page,TOPIC_SYSTEM_ENC);
    }
}

function topicview_popup($conf, $name) {

    if (!topicview_enableid($false))
        return false;

    $data = topicview_topicloader($conf, webparam('pageid'), $name);

    $db = getDB();
    $sql = !strpos($_SERVER['REQUEST_URI'],'sp') ? 'SELECT * FROM topic WHERE visible = 1 and id=:id and groupid =:pid' : 'SELECT * FROM topic WHERE visible_sp = 1 and id=:id and groupid =:pid';
    $row = $db->QueryLine($sql, array('id' => webparam('param'), 'pid' => webparam('pageid')));

    if (!outTimeh($row)) exit();


    $nopic = true;
    $line = $data['windowline'];
    $row['gid'] = $name;
    $row['year'] = $row['view_year'];
    $row['month'] = $row['view_month'];
    $row['day'] = $row['view_day'];
    foreach (clist("picname1,picname2,picname3,picname4,picname5") as $pic) {
        if(intval($row[$pic]) == null) {continue;}
        $nopic = false;
        $left .= topicview_create_link($row, webparam('pageid'), $line, true, $row[$pic], $data['thumbnail_xmax']);
    }

    $page = $nopic ? $data['window-noimage'] : $data['window'];
    $page = rep($page, 'contents', $row['contents']);
    $page = rep($page, 'summary', $row['summary']);
    #$page = rep($page, 'contents', html_br($row['contents']));
    #$page = rep($page, 'summary', html_br($row['summary']));
    $page = phash($row, $page);
    $page = rep($page, 'left', $left);

    if(TOPIC_VIEW_ENC != TOPIC_SYSTEM_ENC && !strpos($_SERVER['REQUEST_URI'],'sp')){
        $page = str_replace(TOPIC_SYSTEM_ENC, TOPIC_VIEW_ENC ,$page);
        $page = mb_convert_encoding($page, TOPIC_USER_ENC,TOPIC_SYSTEM_ENC) ;
    }
    if (!strpos($_SERVER['REQUEST_URI'],'sp')) {
        pout($page,TOPIC_USER_ENC);
    } else {
        pout($page,TOPIC_SYSTEM_ENC);
    }

}

function topicview_userfile() {
    if (!topicview_enableid()) return;
    topic_picture();
}

/** ファイルの表示 ユーザー用 */
function topicview_userpicture() {
    if (!topicview_enableid()) return;
    topic_picture();
}

//==========================================
//  データ読込み部分

/** $valueの型変換した値を返す */
function topicview_check($value) {
    //pr($value);
    if (existMIME($value))
        return 'ext';
    if (is_numeric($value))
        return 'day';
    return 'text';
}

/** 内部関数 */
function topicview_url($file, $name = null) {
    $conf = webparam('conf');
    $file = $conf->getFilepath($file);
    if (file_exists($file)) {
         return $name ? tLoader::nLoad($file, $name) : file_get_contents($file);
    }
}

/** 対応するページのデータを読み込みます */
function topicview_topicloader($conf, $pageid, $name) {
    $index = $conf->getSelector($name);
    $topic = $index->getValuesOne('topic');

    foreach ($topic as $tone) {
        $var = $conf->getSelector($tone);
        if ($var->getValue('topicno') != $pageid)
            continue;
        $data = topicview_loader($var); break;
    }
    return $data;
}

/** データを読込みます */
function topicview_loader($conf) {

    $ret['topicno'] = $conf->getValue('topicno');
    $ret['odd'] = $conf->getValue("odd-name");
    $ret['even'] = $conf->getValue("even-name");

    $ret['thumbnail_xmax'] = $conf->getValue('thumbnail_xmax');
    $ret['window-noimage'] = ifactFunc($conf->getValue('window-noimage'), 'topicview');
    $ret['window'] = ifactFunc($conf->getValue('window'), 'topicview');
    $ret['windowline'] = ifactFunc($conf->getValue('windowline'), 'topicview');
    $ret['windowimagepop'] = ifactFunc($conf->getValue('windowimagepop'), 'topicview');
    $ret['line'] = ifactFunc($conf->getValue('line'), 'topicview');
    $ret['nothing'] = ifactFunc($conf->getValue('nothing'), 'topicview');

    $ret['lines'] = array();
    $typevar = clist("fileline,popline,linkline,titleline,contentsline");

    foreach ($typevar as $type) {
        //dp($conf->getValues($type));

        foreach ($conf->getValues($type) as $one) {
            //pr($one);
            $detail = array();
            $detail['type'] = $type;
            foreach ($one as $val) {
                $datatype = topicview_check($val);
                $detail[$datatype] = ifactFunc($val, 'topicview');
            }
            $ret['lines'][] = $detail;
        }
    }
    return $ret;
}

//=================================================
//  データ出力の部分

/** 適切なデータを取り出します */
function topicview_filter($row, $data) {

    $defline = $data['line'];
    $lines = $data['lines'];
    $datatype = topic_datatype($row['type']) . 'line';

    reset($lines);

    foreach ($lines as $line) {

        //種類による絞り込み
        if ($datatype != $line['type']){continue;}


        //拡張子絞り込み
        if (($datatype == 'fileline') && $line['ext']) {
            $info = pathinfo(topic_userreadname($row['filename']));
            if ($info['extension'] != $line['ext']){continue;}
        }
        //日付絞り込み
        if ($line['day']) {
            if (!inTimeh($row, $line['day'])){
                continue;
            }
        }
        $ret = $line['text'];
    }
    return $ret ? $ret : $defline;
}

/** リンクを作成する */
function topicview_create_link($row, $pid, $line, $file = false, $fid =null, $xmax = null) {

    //テーブル用のリンク
    $val = topic_datatype($row['type']);

    if ($val == 'title')
        $link = "";
    if ($val == 'link')
        $link = $row['link'];
    if ($val == 'pop')
        $link = "?type=" . ($file ? 'image' : 'pop')
                . "&amp;pageid=$pid&amp;param=" . ($file ? $fid : $row['id'])
                . "&amp;gid=" . $row['gid'];
    if ($val == 'contents')
        $link = "document.php?pid=" . $row['contents_group']
                . "&amp;param=" . $row['contents_title'] . ($row['anchor'] == 0 ? "&amp;cmd=anchor#id" . $row['contents_title'] : "");
    if ($val == 'file') {
        $link = "?type=file&amp;pageid=" . $pid . "&amp;param=" . $row['filename'] . "&amp;gid=" . $row['gid'];
        $fid = $row['file'];
        $file = true;
    }

    if ($file) {
        $db = getDB();
        $sql = 'SELECT filename,x,y FROM topic_file WHERE pid=:pid and id=:id ';
        $detail = $db->QueryLine($sql, array('id' => $fid, 'pid' => $pid));

        $detail['xmax'] = $detail['x'] > $xmax ? $xmax : $detail['x'];

        $detail['x'] += 10;
        $detail['y'] += 50;

        $imagepop = "?type=imagepop&amp;pageid=$pid&amp;param=$fid" . "&amp;gid=" . $row['gid'];
        $line = rep($line, 'imagepop', $imagepop);
        $line = ephash($detail, $line);
    }
    $line = rep($line, 'blank', $row['linkto'] == 1 ? 'target="_blank"' : 'target="_top"');
    $line = rep($line, 'link', $link);
    return $line;
}

/** ユーザーが表示するためのデータを取り出す */
function topicview_userlist($pid) {

$db = getDB();

$nowURL= $_SERVER["REQUEST_URI"];
if (preg_match("/sp_list/", $nowURL)) {
    $sql = !strpos($_SERVER['REQUEST_URI'],'sp') ? 'SELECT * FROM topic WHERE visible = 1 and groupid =:pid ORDER BY sort desc' : "SELECT * FROM topic WHERE visible_sp = 1 and groupid =:pid ORDER BY sort desc";
} else{ 
    $sql = !strpos($_SERVER['REQUEST_URI'],'sp') ? 'SELECT * FROM topic WHERE visible = 1 and groupid =:pid ORDER BY sort desc' : "SELECT * FROM topic WHERE visible_sp = 1 and groupid =:pid ORDER BY sort desc limit 0, 8";
}
    $table = $db->QueryRow($sql,array('pid' => intval($pid)));

    $ret = array();
    foreach ($table as $line) {
        if (!outTimeh($line))continue;
        $ret[] = $line;
    }
    return $ret;
}

/** テーブルを作る */
function topicview_create_table($conf, $name) {
    $data = topicview_loader($conf);
    $table = topicview_userlist($data['topicno']);

    if (count($table) == 0) return $data['nothing'];
    $db = getDB();
    foreach ($table as $row) {

		$thumbsql = 'SELECT * FROM topic_thumbnail WHERE topic_id=:id';
		$thumbnail = $db->QueryRow($thumbsql, array('id' => $row['id']));

        $txt = topicview_filter($row, $data);
        $row['gid'] = $name;
        $row['year'] = $row['view_year'];
        $row['month'] = $row['view_month'];
        $row['day'] = $row['view_day'];

		$dspthumb = '';
		if(!is_null($thumbnail['0']['filepath'])) {
		    // スマートフォンからのアクセスかどうか
		    if (!preg_match("/sp/", $name)) {
		        $dspthumb = '<div  class="thumbnail" style="background:url(manage/contents/topic/thumbnail/'.$thumbnail['0']['filepath'].') no-repeat center center; background-size: cover;"></div>';
		    } else {
		        $dspthumb = '<div  class="thumbnail" style="manage/contents/topic/thumbnail/'.$thumbnail['0']['filepath'].'"></div>';
		    }

		}
        $txt = rep($txt, 'thumbnail', $dspthumb);

        $txt = topicview_create_link($row, $data['topicno'], $txt);
        $row['nline'] = (($i++) % 2) ? $data["even"] : $data["odd"];
        $txt = rep($txt, 'summary', $row['summary']);
        $ret .= phash($row, $txt);
    }
    return $ret;
}

/** ページを作る */
function topicview_create_page($conf, $name) {

    $index = $conf->getSelector($name);

    $func = $index->getValue('template');
    $page = ifactFunc($func, 'topicview');

    $topic = $index->getValuesOne('topic');
    $topic = toarray($topic);
    foreach ($topic as $tone) {
        $in = topicview_create_table($conf->getSelector($tone), $name);
        $page = rep($page, $tone, $in);
        $request_url = str_replace("sp/", "", $_SERVER['REQUEST_URI']);
        $page = rep($page, 'href', '..' . $request_url);
    }

    if(TOPIC_VIEW_ENC != TOPIC_SYSTEM_ENC && !strpos($_SERVER['REQUEST_URI'],'sp')){
        $page = str_replace(TOPIC_SYSTEM_ENC ,TOPIC_VIEW_ENC, $page);
        $page = mb_convert_encoding($page, TOPIC_USER_ENC,TOPIC_SYSTEM_ENC);
    }
    return $page;
}
?>