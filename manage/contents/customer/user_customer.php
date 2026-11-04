<?php

userload('customer/customer.php');

function customer_view($name, $page_num, $param_id = '', $top, $agent = 'pc') {
    $config = confpath("customer/config.ini");
    $conf = new cssConfig($config);
    $_POST['conf'] = $conf;
    $type = webparam('type');
    $cmd  = webparam('cmd');
    if ($type == 'pop') {customerview_popup($conf, $name); exit;    }
    if ($type == 'file') {customerview_userfile(); exit;    }
    if ($type == 'image') {customerview_userpicture();exit;    }
    if ($type == 'imagepop') {customerview_userimagepop($conf, $name);exit;    }

    if ($param_id == '') {
        return customerview_create_page($conf, $name, $page_num, $top, '', $cmd, $agent);
    } else {
        return customerview_create_page($conf, $name, $page_num, $top, $param_id, $cmd, $agent);
    }

}

/** トピック名を取り出す */
function customerview_selector() {return basename(__FILE__);}

/** DBクラスを取得します */
function customerview_sql() {
}

//============================================
// ここに表示用のコードを書く必要あり

/** 指定したファイルが使用できるか? */
function customerview_enableid($filemode = true) {

    $pageid = webparam('pageid');
    $id = webparam('param');

    if (!$pageid || ! $id)
        return false;

    $db = getDB();

    //データがあるか
    $sql = 'SELECT count(*) FROM customer WHERE visible=1 and id=:id';
    $count = $db->Count($sql, array('id' => $id));
    return $count > 0;
}

/** イメージ用のポップアップを作成します */
function customerview_userimagepop($conf, $name) {
    if (!customerview_enableid()){
        return false;
    }

    $data = customerview_customerloader($conf, webparam('pageid'), $name);
    //データ取得
    $line = $data['windowimagepop'];

    //イメージのデータ
    $fid = webparam('param');
    $pid = webparam('pageid');

    $line = rep($line, 'link', "?type=image&amp;pageid=" . $pid . "&amp;param=" . $fid);
    $line = phash($detail, $line);

    if(CUSTOMER_VIEW_ENC != CUSTOMER_SYSTEM_ENC){
        $line = str_replace(CUSTOMER_SYSTEM_ENC,CUSTOMER_VIEW_ENC, $line);
        $line = mb_convert_encoding($line, CUSTOMER_USER_ENC,CUSTOMER_SYSTEM_ENC);
    }

    pout($line,CUSTOMER_USER_ENC);
}

function customerview_popup($conf, $name) {

    if (!customerview_enableid($false))
        return false;

    $data = customerview_customerloader($conf, webparam('pageid'), $name);

    $db = getDB();
    $sql = 'SELECT * FROM customer WHERE visible = 1 and id=:id and groupid =:pid';
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
        $left .= customerview_create_link($row, webparam('pageid'), $line, true, $row[$pic], $data['thumbnail_xmax']);
    }

    $page = $nopic ? $data['window-noimage'] : $data['window'];
    $page = rep($page, 'contents', $row['contents']);
    $page = rep($page, 'summary', $row['summary']);
    #$page = rep($page, 'contents', html_br($row['contents']));
    #$page = rep($page, 'summary', html_br($row['summary']));
    $page = phash($row, $page);
    $page = rep($page, 'left', $left);

    if(CUSTOMER_VIEW_ENC != CUSTOMER_SYSTEM_ENC){
        $page = str_replace(CUSTOMER_SYSTEM_ENC, CUSTOMER_VIEW_ENC ,$page);
        $page = mb_convert_encoding($page, CUSTOMER_USER_ENC,CUSTOMER_SYSTEM_ENC);
    }
    pout($page,CUSTOMER_USER_ENC);
}

function customerview_userfile() {
    if (!customerview_enableid()) return;
    customer_picture();
}

/** ファイルの表示 ユーザー用 */
function customerview_userpicture() {
    if (!customerview_enableid()) return;
    customer_picture();
}

//==========================================
//  データ読込み部分

/** $valueの型変換した値を返す */
function customerview_check($value) {
    //pr($value);
    if (existMIME($value))
        return 'ext';
    if (is_numeric($value))
        return 'day';
    return 'text';
}

/** 内部関数 */
function customerview_url($file, $name = null) {
    $conf = webparam('conf');
    $file = $conf->getFilepath($file);
    if (file_exists($file)) {
         return $name ? tLoader::nLoad($file, $name) : file_get_contents($file);
    }
}

/** 対応するページのデータを読み込みます */
function customerview_customerloader($conf, $pageid, $name) {
    $index = $conf->getSelector($name);
    $customer = $index->getValuesOne('customer');


    foreach ($customer as $tone) {
        $var = $conf->getSelector($tone);
        if ($var->getValue('customerno') != $pageid)
            continue;
        $data = customerview_loader($var); break;
    }
    return $data;
}

/** データを読込みます */
function customerview_loader($conf) {

    $ret['customerno'] = $conf->getValue('customerno');
    $ret['odd'] = $conf->getValue("odd-name");
    $ret['even'] = $conf->getValue("even-name");

    $ret['thumbnail_xmax'] = $conf->getValue('thumbnail_xmax');
    $ret['img_link'] = $conf->getValue('img_link');
    $ret['block_num'] = $conf->getValue('block_num');
    $ret['window-noimage'] = ifactFunc($conf->getValue('window-noimage'), 'customerview');
    $ret['window'] = ifactFunc($conf->getValue('window'), 'customerview');
    $ret['windowline'] = ifactFunc($conf->getValue('windowline'), 'customerview');
    $ret['windowimagepop'] = ifactFunc($conf->getValue('windowimagepop'), 'customerview');
    $ret['line'] = ifactFunc($conf->getValue('line'), 'customerview');
    $ret['nothing'] = ifactFunc($conf->getValue('nothing'), 'customerview');

    $ret['lines'] = array();
    $typevar = clist("linestart,lineend,fileline,popline,linkline,titleline,contentsline,morebtn");

    foreach ($typevar as $type) {
        //dp($conf->getValues($type));

        foreach ($conf->getValues($type) as $one) {
            //pr($one);
            $detail = array();
            $detail['type'] = $type;
            foreach ($one as $val) {
                $datatype = customerview_check($val);
                $detail[$datatype] = ifactFunc($val, 'customerview');
            }
            $ret['lines'][] = $detail;
        }
    }
    return $ret;
}

//=================================================
//  データ出力の部分

/** 適切なデータを取り出します */
function customerview_filter($row, $data) {

    $defline = $data['line'];
    $lines = $data['lines'];
    $datatype = customer_datatype($row['type']) . 'line';

    reset($lines);
    foreach ($lines as $line) {

        //種類による絞り込み
        if ($datatype != $line['type']){continue;}

        //拡張子絞り込み
        if (($datatype == 'fileline') && $line['ext']) {
            $info = pathinfo(customer_userreadname($row['filename']));
            if ($info['extension'] != $line['ext']){continue;}
        }
        //日付絞り込み
        if ($line['day']) {
            if (!inTimeh($row, $line['day'])){
                continue;
            }
        }
        $ret .= $line['text'];
    }
    // テーブル終了部分がある場合は、末尾に追加
    if (isset($lineend) && isset($ret)) {
        $ret .= $lineend;
    } elseif (isset($lineend)) {
        $defline .= $lineend;
    }
    return $ret ? $ret : $defline;
}

/** リンクを作成する */
function customerview_create_link($row, $pid, $line, $file = false, $fid =null, $xmax = null) {

    //テーブル用のリンク
    $val = customer_datatype($row['type']);

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
function customerview_userlist($pid, $page_begin, $page_cnt, $param_id = '') {

    $db = getDB();

    // ページング
    if ($param_id == '') {
        $sql = "SELECT * FROM customer WHERE visible = 1 and groupid =:pid ORDER BY sort desc LIMIT {$page_begin} , {$page_cnt} ";
        $table = $db->QueryRow($sql,array('pid' => intval($pid)));
    } else {
        $sql = "SELECT * FROM customer WHERE groupid =:pid and id = :id ORDER BY sort desc LIMIT {$page_begin} , {$page_cnt} ";
        $table = $db->QueryRow($sql,array('pid' => intval($pid), 'id' => intval($param_id)));
    }



    $ret = array();
    foreach ($table as $line) {
        if (!outTimeh($line))continue;
        $ret[] = $line;
    }
    return $ret;
}

/** テーブルを作る */
function customerview_create_table($conf, $name, $page_begin, $page_cnt, $param_id = '', $page_cnt_top, $page_num, $count, $top, $cmd) {
    $data = customerview_loader($conf);

    if ($param_id == '' && $cmd != 'preview') {
        $table = $top != '' ? customerview_userlist($data['customerno'], $page_begin, $page_cnt_top) : customerview_userlist($data['customerno'], $page_begin, $page_cnt);
    } else {
        if ($param_id != '') $table = customerview_userlist($data['customerno'], $page_begin, $page_cnt, $param_id);
        if ($cmd == 'preview') {
            // ﾌﾟﾚﾋﾞｭｰ処置
            // 入力値から、配列データを作成する
            $table[0]['title']         = str_replace('<br />', '', webparam('title'));
            $table[0]['summary']       = webparam('summary');
            $table[0]['customer_name'] = str_replace('<br />', '', webparam('customer_name'));
            $table[0]['icon']         = webparam('icon');
        }
    }

    if (count($table) == 0) return $data['nothing'];
    $db = getDB();

    $layout_val = 1;
    $cnt = count($table);

    $sp_path = preg_match('/sp/', $_SERVER['REQUEST_URI']) ? '../' : '';

    foreach ($table as $row) {

        $txt = '';
        if ($cmd != 'preview' || ($cmd == 'preview' && !isset($_FILES['thumbnail']))) {
            $thumbsql = 'SELECT * FROM customer_thumbnail WHERE customer_id=:id';
    		$thumbnail = $db->QueryRow($thumbsql, array('id' => $row['id']));
        } else {
            // プレビュー
            // 画像を仮にアップロードする
            if (isset($_FILES['thumbnail']) && pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION) == 'gif' || pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION) == 'jpeg' || pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION) == 'jpg'){
            	$file_nm = $_FILES['thumbnail']['name'];
            	$extension = pathinfo($file_nm, PATHINFO_EXTENSION);
                $filepath = date('Ymdhis') . '_pre.' . $extension;
                if (is_uploaded_file($_FILES["thumbnail"]["tmp_name"])) {
        			move_uploaded_file($_FILES["thumbnail"]["tmp_name"], userpath("customer/thumbnail/"). $filepath);
        			$thumbnail['0']['filepath'] = $filepath;
        		}
        	}
        }

	    if (($data['block_num'] != 0 && $layout_val % $data['block_num'] == 1) || $data['block_num'] == 1) {
	        $txt .= '<div class="block02">';
	    }

		$txt .= customerview_filter($row, $data);

	    if (($data['block_num'] != 0 && ($layout_val % $data['block_num'] == 0 || $cnt == $layout_val)) || $data['block_num'] == 1) {
	        $txt .= '</div>';
	    }

		$row['gid'] = $name;
        $row['year'] = $row['view_year'];
        $row['month'] = $row['view_month'];
        $row['day'] = $row['view_day'];

		$dspthumb = '';
		if(!is_null($thumbnail['0']['filepath'])) {
            //ファイルの拡張子を取得
            $extension = pathinfo($sp_path . 'manage/contents/customer/thumbnail/' . $thumbnail['0']['filepath'],  PATHINFO_EXTENSION);
		    $image_path_thb = preg_match('/sp/', $_SERVER['REQUEST_URI']) ? $sp_path . 'manage/contents/customer/thumbnail/' . $thumbnail['0']['filepath'] . '_resp.' . $extension : $sp_path . 'manage/contents/customer/thumbnail/' . $thumbnail['0']['filepath'] . '_re.' . $extension;
		    if (!file_exists($image_path_thb)) {
		        // リサイズ画像の生成

                copy($sp_path . 'manage/contents/customer/thumbnail/' . $thumbnail['0']['filepath'], $image_path_thb);
	            chmod($image_path_thb, 0777);
				//■画像リサイズ■
				resizerThub($image_path_thb, $extension, $data['thumbnail_xmax']);

				// ﾌﾟﾚﾋﾞｭｰのとき、元の画像を削除
				if ($cmd == 'preview') {
				    unlink($sp_path . 'manage/contents/customer/thumbnail/' . $thumbnail['0']['filepath']);
				}
		    }
		    if ($data['img_link'] == 1) {
		        $dspthumb = '<a href="' . $sp_path . 'manage/contents/customer/thumbnail/' . $thumbnail['0']['filepath'] . '"  target="_blank"><img src="' . $image_path_thb . '" alt="写真1" /></a>';
		    } else {
		        $dspthumb = '<img src="' . $image_path_thb . '" alt="写真1" />';
		    }

		}
        $txt = rep($txt, 'thumbnail', $dspthumb);

        //icon画像
        $thumbsql = 'SELECT t.* FROM icon_thumbnail t INNER JOIN icon i ON i.id = t.icon_id  WHERE i.id=:id AND i.visible=1';
        $thumbnail = $db->QueryRow($thumbsql, array('id' => $row['icon']));
        $dspthumb = '';
        if(!is_null($thumbnail['0']['filepath'])) {
            //ファイルの拡張子を取得
            $extension = pathinfo($sp_path . 'manage/contents/icon/thumbnail/' . $thumbnail['0']['filepath'],  PATHINFO_EXTENSION);
            $image_path_thb = preg_match('/sp/', $_SERVER['REQUEST_URI']) ? $sp_path . 'manage/contents/icon/thumbnail/' . $thumbnail['0']['filepath'] . '_resp.' . $extension : $sp_path . 'manage/contents/icon/thumbnail/' . $thumbnail['0']['filepath'] . '_resp.' . $extension;
            if (!file_exists($image_path_thb)) {
                // リサイズ画像の生成

                copy($sp_path . 'manage/contents/icon/thumbnail/' . $thumbnail['0']['filepath'], $image_path_thb);
                chmod($image_path_thb, 0777);
                //■画像リサイズ■
                $icon_config = confpath("icon/config.ini");
                $icon_conf = new cssConfig($icon_config);
                $icon_index = $icon_conf->getSelector('icon_info');
                $thumbnail_xmax  = preg_match('/sp/', $_SERVER['REQUEST_URI']) ? $icon_index->getValue('thumbnail_xmax_sp') : $icon_index->getValue('thumbnail_xmax');

                resizerThub($image_path_thb, $extension, $thumbnail_xmax);
            }
            $dspthumb = '<img src="' . $image_path_thb . '" alt="写真1" />';
            $txt = rep($txt, 'icon', $dspthumb);
        } else {
            $txt = rep($txt, 'icon', '');
        }

        $txt = customerview_create_link($row, $data['customerno'], $txt);
        $row['nline'] = (($i++) % 2) ? $data["even"] : $data["odd"];

        $txt = rep($txt, 'summary', $row['summary']);
        $ret .= phash($row, $txt);
        $layout_val += 1;
    }
    // トップページのとき、テーブル開始部分・終了部分をセット
    if ($top != '') {
        $lines = $data['lines'];
        reset($lines);
        foreach ($lines as $line) {
            // テーブル開始部分
            if ($line['type'] == 'linestart') {
                $ret = $line['text'] . $ret;
            }

            // テーブル終了部分
            if ($line['type'] == 'lineend') {
                $end = $line['text'];
            }

            // 「もっと見る」ボタン
            if ($line['type'] == 'morebtn') {
                $more = $line['text'];
            }
        }
        $ret .= $end ? $end : '';

        //トップページ「もっと見る」ボタン
        if ($page_num == 1 && $count > $page_cnt_top && $param_id == '') {
            $ret = rep($ret, 'more', $more);
            if ($page_cnt_top == $page_cnt) {
                $ret = rep($ret, 'page', '2');
            } else {
                $ret = rep($ret, 'page', '1');
            }
            $ret = rep($ret, 'pid', $name);

        } else {
            $ret = rep($ret, 'more', '');
        }
    }

    return $ret;
}

/** ページを作る */
function customerview_create_page($conf, $name, $page_num, $top, $param_id = '', $cmd, $agent) {
    // トップページを表示させる場合
    if ($top != '') {
        $index = $agent == 'pc' ? $conf->getSelector('top' . $top) : $conf->getSelector('top' . $top . 'sp');
        $pid = $index->getValuesOne('gid');
        $func = $index->getValue('template');
    } else {
        $pid[0] = $name;
        $index = $conf->getSelector($name);
        $func = $index->getValue('template');
    }
    $page = ifactFunc($func, 'customerview');

    foreach ($pid as $val) {
        $index = $conf->getSelector($val);

        $db = getDB();

        $customer = $index->getValuesOne('customer');
        $customer = toarray($customer);

        foreach ($customer as $tone) {
            // トップページのIDを退避
            $top_id = $conf->getSelector($tone)->getValue('top');
            // 1ページに表示させるデータ個数(トップページ）
            $page_cnt_top  = $conf->getSelector($tone)->getValue('pagecnt_top');
            // 1ページに表示させるデータ個数(トップページ以外）
            $page_cnt      = $conf->getSelector($tone)->getValue('pagecnt');
            // 1ページに表示させるページ番号数
            $page_disp_cnt = $conf->getSelector($tone)->getValue('page_disp_cnt');
            // 開始ページ位置
            $page_begin = $page_num * $page_cnt - $page_cnt;

            // データ全ての個数をチェック
            if (($page_num > 1 && $type == '') || $page_num == 1) {
                $sql = 'SELECT count(*) FROM customer WHERE visible=1 and groupid=:id';
                $count = $db->Count($sql, array('id' => $conf->getSelector($tone)->getValue('customerno')));
            } else {
                $count = 0;
            }

            if ($param_id == '') {
                $in = customerview_create_table($conf->getSelector($tone), $val, $page_begin, $page_cnt, '', $page_cnt_top, $page_num, $count, $top, $cmd);
            } else {
                $in = customerview_create_table($conf->getSelector($tone), $val, $page_begin, $page_cnt, $param_id, $page_cnt_top, $page_num, $count, $top, $cmd);
            }
            $page = rep($page, $tone, $in);

            // ページナビ
            // 全データ件数
            $page = rep($page, 'count', $count);

            // 全ページ数
            $pcount = ceil($count / $page_cnt);
            $page = rep($page, 'pcount', $pcount);

            // <a href="">前へ</a>　<a href="">1</a>|<strong>2</strong>|<a href="">3</a>|<a href="">4</a>|<a href="">5</a>|<a href="">次へ</a>
            $paging = '';
            if ($page_num > 1) {
                $before = $page_num - 1;
                $paging .= $before == 1 && $page_cnt_top == $page_cnt ? '<a href="./customer.php?top=' . $top_id . '">前へ</a>' : '<a href="./customer.php?page=' . $before . '&pid=' . $name . '">前へ</a>';
            }

//             if ($page_num / 5) {
//                 $i = $page_num - 2;
//             } elseif ($page_num > 1) {
//                 $i = $page_num - 1;
//             } else {
//                 $i = $page_num;
//             }

            $i = ceil($page_num / 5) * 5 - 4;
            $for = $i + 4 > $pcount ? $pcount : $i + 4;
            for (; $i <= $for; $i++) {
                if ($i == $page_num) {
                    $paging .= "<strong>{$i}</strong>|";
                } elseif ($i == $for) {
                    $paging .= '<a href="./customer.php?page=' . $i . '&pid=' . $name . '">' . $i . '</a>|';
                } else {
                    $paging .= $i == 1 && $page_cnt_top == $page_cnt ? '<a href="./customer.php?top=' . $top_id . '">' . $i . '</a>|' : '<a href="./customer.php?page=' . $i . '&pid=' . $name . '">' . $i . '</a>|';
                }
            }

            if ($page_num < $pcount) {
                $next = $page_num + 1;
                $paging .= '<a href="./customer.php?page=' . $next . '&pid=' . $name . '">次へ</a>';
            }

            $page = rep($page, 'paging', $paging);

            $request_url = str_replace("sp/", "", $_SERVER['REQUEST_URI']);
            $page = rep($page, 'href', '..' . $request_url);

        }

    }
    if(CUSTOMER_VIEW_ENC != CUSTOMER_SYSTEM_ENC){
        $page = str_replace(CUSTOMER_SYSTEM_ENC ,CUSTOMER_VIEW_ENC, $page);
        $page = mb_convert_encoding($page, CUSTOMER_USER_ENC,CUSTOMER_SYSTEM_ENC);
    }

    return $page;
}

?>