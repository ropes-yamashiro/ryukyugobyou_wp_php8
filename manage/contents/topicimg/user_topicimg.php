<?php

userload('topicimg/topicimg.php');

function topicimg_view($name, $agent = 'pc') {
    $config = confpath("topicimg/config.ini");
    $conf = new cssConfig($config);
    $_POST['conf'] = $conf;
    $type = webparam('type');

    if ($type == 'pop') {topicimgview_popup($conf, $name); exit;    }
    if ($type == 'file') {topicimgview_userfile(); exit;    }
    if ($type == 'image') {topicimgview_userpicture();exit;    }
    if ($type == 'imagepop') {topicimgview_userimagepop($conf, $name);exit;    }
    return topicimgview_create_page($conf, $name, $agent);
}

/** トピック名を取り出す */
function topicimgview_selector() {return basename(__FILE__);}

/** DBクラスを取得します */
function topicimgview_sql() {
}

//============================================
// ここに表示用のコードを書く必要あり

/** 指定したファイルが使用できるか? */
function topicimgview_enableid($filemode = true) {

    $pageid = webparam('pageid');
    $id = webparam('param');

    if (!$pageid || ! $id)
        return false;

    $db = getDB();

    //データがあるか
    $sql = 'SELECT count(*) FROM topicimg WHERE visible=1 and id=:id';
    $count = $db->Count($sql, array('id' => $id));
    return $count > 0;
}

/** イメージ用のポップアップを作成します */
function topicimgview_userimagepop($conf, $name) {
    if (!topicimgview_enableid()){
        return false;
    }

    $data = topicimgview_topicimgloader($conf, webparam('pageid'), $name);
    //データ取得
    $line = $data['windowimagepop'];

    //イメージのデータ
    $fid = webparam('param');
    $pid = webparam('pageid');

    $line = rep($line, 'link', "?type=image&amp;pageid=" . $pid . "&amp;param=" . $fid);
    $line = phash($detail, $line);

    if(TOPIC_VIEW_ENC != TOPIC_SYSTEM_ENC){
        $line = str_replace(TOPIC_SYSTEM_ENC,TOPIC_VIEW_ENC, $line);
        $line = mb_convert_encoding($line, TOPIC_USER_ENC,TOPIC_SYSTEM_ENC);
    }

    pout($line,TOPIC_USER_ENC);
}

function topicimgview_popup($conf, $name) {

    if (!topicimgview_enableid($false))
        return false;

    $data = topicimgview_topicimgloader($conf, webparam('pageid'), $name);

    $db = getDB();
    $sql = 'SELECT * FROM topicimg WHERE visible = 1 and id=:id and groupid =:pid';
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
        $left .= topicimgview_create_link($row, webparam('pageid'), $line, true, $row[$pic], $data['thumbnail_xmax']);
    }

    $page = $nopic ? $data['window-noimage'] : $data['window'];
    $page = rep($page, 'contents', $row['contents']);
    $page = rep($page, 'summary', $row['summary']);
    #$page = rep($page, 'contents', html_br($row['contents']));
    #$page = rep($page, 'summary', html_br($row['summary']));
    $page = phash($row, $page);
    $page = rep($page, 'left', $left);

    if(TOPIC_VIEW_ENC != TOPIC_SYSTEM_ENC){
        $page = str_replace(TOPIC_SYSTEM_ENC, TOPIC_VIEW_ENC ,$page);
        $page = mb_convert_encoding($page, TOPIC_USER_ENC,TOPIC_SYSTEM_ENC);
    }
    pout($page,TOPIC_USER_ENC);
}

function topicimgview_userfile() {
    if (!topicimgview_enableid()) return;
    topicimg_picture();
}

/** ファイルの表示 ユーザー用 */
function topicimgview_userpicture() {
    if (!topicimgview_enableid()) return;
    topicimg_picture();
}

//==========================================
//  データ読込み部分

/** $valueの型変換した値を返す */
function topicimgview_check($value) {
    //pr($value);
    if (existMIME($value))
        return 'ext';
    if (is_numeric($value))
        return 'day';
    return 'text';
}

/** 内部関数 */
function topicimgview_url($file, $name = null) {
    $conf = webparam('conf');
    $file = $conf->getFilepath($file);
    if (file_exists($file)) {
         return $name ? tLoader::nLoad($file, $name) : file_get_contents($file);
    }
}

/** 対応するページのデータを読み込みます */
function topicimgview_topicimgloader($conf, $pageid, $name) {
    $index = $conf->getSelector($name);
    $topicimg = $index->getValuesOne('topicimg');


    foreach ($topicimg as $tone) {
        $var = $conf->getSelector($tone);
        if ($var->getValue('topicimgno') != $pageid)
            continue;
        $data = topicimgview_loader($var); break;
    }
    return $data;
}

/** データを読込みます */
function topicimgview_loader($conf) {

        $ret['topicimgno'] = $conf->getValue('topicimgno');
    $ret['odd'] = $conf->getValue("odd-name");
    $ret['even'] = $conf->getValue("even-name");

    $ret['thumbnail_xmax'] = $conf->getValue('thumbnail_xmax');
    $ret['window-noimage'] = ifactFunc($conf->getValue('window-noimage'), 'topicimgview');
    $ret['window'] = ifactFunc($conf->getValue('window'), 'topicimgview');
    $ret['windowline'] = ifactFunc($conf->getValue('windowline'), 'topicimgview');
    $ret['windowimagepop'] = ifactFunc($conf->getValue('windowimagepop'), 'topicimgview');
    $ret['line'] = ifactFunc($conf->getValue('line'), 'topicimgview');
    $ret['nothing'] = ifactFunc($conf->getValue('nothing'), 'topicimgview');

    $ret['lines'] = array();
    $typevar = clist("fileline,popline,linkline,titleline,contentsline");

    foreach ($typevar as $type) {
        //dp($conf->getValues($type));

        foreach ($conf->getValues($type) as $one) {
            //pr($one);
            $detail = array();
            $detail['type'] = $type;
            foreach ($one as $val) {
                $datatype = topicimgview_check($val);
                $detail[$datatype] = ifactFunc($val, 'topicimgview');
            }
            $ret['lines'][] = $detail;
        }
    }
    return $ret;
}

//=================================================
//  データ出力の部分

/** 適切なデータを取り出します */
function topicimgview_filter($row, $data) {

    $defline = $data['line'];
    $lines = $data['lines'];
    $datatype = topicimg_datatype($row['type']) . 'line';

    reset($lines);

    foreach ($lines as $line) {

        //種類による絞り込み
        if ($datatype != $line['type']){continue;}


        //拡張子絞り込み
        if (($datatype == 'fileline') && $line['ext']) {
            $info = pathinfo(topicimg_userreadname($row['filename']));
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
function topicimgview_create_link($row, $pid, $line, $file = false, $fid =null, $xmax = null) {

    //テーブル用のリンク
    $val = topicimg_datatype($row['type']);

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

    $line = rep($line, 'blank', $row['linkto'] == 1 ? 'target="_blank"' : 'target="_top"');
    $line = rep($line, 'link', $link);
    return $line;
}

/** ユーザーが表示するためのデータを取り出す */
function topicimgview_userlist($pid) {

    $db = getDB();
    $sql = 'SELECT * FROM topicimg WHERE visible = 1 and groupid =:pid ORDER BY sort desc';
    $table = $db->QueryRow($sql,array('pid' => intval($pid)));

    $ret = array();
    foreach ($table as $line) {
        if (!outTimeh($line))continue;
        $ret[] = $line;
    }
    return $ret;
}

/** テーブルを作る */
function topicimgview_create_table($conf, $name, $agent) {
    $data = topicimgview_loader($conf);
    $table = topicimgview_userlist($data['topicimgno']);

    if (count($table) == 0) return $data['nothing'];
    $db = getDB();
    foreach ($table as $row) {

		$thumbsql = 'SELECT * FROM topicimg_thumbnail WHERE topicimg_id=:id';
		$thumbnail = $db->QueryRow($thumbsql, array('id' => $row['id']));

        $txt = topicimgview_filter($row, $data);
        $row['gid'] = $name;
        $row['year'] = $row['view_year'];
        $row['month'] = $row['view_month'];
        $row['day'] = $row['view_day'];

		$dspthumb = '';
		if(!is_null($thumbnail['0']['filepath'])) {
		    $dspthumb = $agent == 'pc' ? './manage/contents/topicimg/thumbnail/'.$thumbnail['0']['filepath'] : '../manage/contents/topicimg/thumbnail/'.$thumbnail['0']['filepath'];
		}
    }
    return $dspthumb;
}

/** ページを作る */
function topicimgview_create_page($conf, $name, $agent) {

    $index = $conf->getSelector($name);
    $func = $agent == 'pc' ? $index->getValue('template') : $index->getValue('template_sp');
    $page = ifactFunc($func, 'topicimgview');

    $topicimg = $index->getValuesOne('topicimg');
    $topicimg = toarray($topicimg);
    foreach ($topicimg as $tone) {
        $in = topicimgview_create_table($conf->getSelector($tone), $name, $agent);
        $page = rep($page, 'thumbnail', $in);
    }
    $page = rep($page, 'pid', $name);

    if(TOPIC_VIEW_ENC != TOPIC_SYSTEM_ENC){
        $page = str_replace(TOPIC_SYSTEM_ENC ,TOPIC_VIEW_ENC, $page);
        $page = mb_convert_encoding($page, TOPIC_USER_ENC,TOPIC_SYSTEM_ENC);
    }
    return $page;
}
?>