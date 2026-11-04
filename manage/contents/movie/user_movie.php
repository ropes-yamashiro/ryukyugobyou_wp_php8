<?php

#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/09
# History: 2010/03/26 プレビュー時のイメージを削除できるように修正
#==================================
userload('movie/movie.php');
userload('movie_conf/movie_conf.php');

/**
 * データを表示する
 * @param int $pid データNo
 * @param int $pno ページングNo
 * @return view ページを出力します
 */
function movie_view($pid = 1, $pno = 1,$cid = 0, $agent = ''){

    $config = confpath("movie/config.ini");
    $pconfig = confpath("movie/print.ini");
    $lconfig = confpath("movie/list.ini");

    //基本データの取得:ブログの取得
//--- START 2013-04-11 K.Arikata --------------
//  $id = webparam('param');  //★dataモデル
    $id = webparam('param', 1);
//--- END  --------------------------------------------------
    $cmd = webparam('cmd');
    $all = ($cmd == 'print' || $cmd == 'list') ? true : false; //印刷モード or 一覧モード

    $config = $all ? ($cmd == 'print' ? $pconfig : $lconfig) : $config;
    $type = $id ? ($cmd == 'anchor' ? 'anchor' : ($cmd=='img' ? 'img' : 'data')) : ($cmd == 'preview' ? 'form' : 'paging');
    $type = $all ? 'paging' : $type;

    //テンプレートの読込み:外観
    $frame_all = movie_couf_ini_frame_all($config);

    $cond = moviebox_info_find($pid);
    $frame = $frame_all[$cond["moviebox_temp_id"]];

    if ($frame === null)
        return html_errtag("「メニュー設定」を済ませていないか、設定データがない可能性があります");
    $page = $agent == '' ? file_get_contents($frame['file']) : file_get_contents($frame['file_sp']);

    $request_url = str_replace("sp/", "", $_SERVER['REQUEST_URI']);
    $page = rep($page, 'href', '..' . $request_url);


    if ($type == 'data') {
        //★data(保存しているデータ)
        $doc = movie_preview(movie_load($id), $config, $pid, $agent); //★★
        $paging = "";
        $count = 1;
        $pcount = 1;
    } elseif ($type == 'form') {
        //★postモデル(プレビュー:単体)
        $doc = movie_preview(movie_form(true), $config, $pid, $agent); //★★
        //$doc = movie_preview(movie_load($_GET['param']), $config, $pid); //★★
        //$doc = movie_preview(movie_form(true)/*movie_load($_GET['param'])*/, $config, $pid); //★★
        $paging = "";
        $count = 1;
        $pcount = 1;
    } elseif ($type == 'img') {
        file_movie_view($id, false, true);
        return;
    } else {
        //★pagingモデル
        $count = movie_info_count_by_user($pid, time(),$cid);

        //データを取得して、表示作成
        $moviebox = moviebox_info_select_moviebox_temp_id($pid);

        $mcount = $cmd == 'print' ? 2000 : ($cmd == 'list' ? (preg_match('/sp/', $_SERVER['REQUEST_URI']) ? $moviebox[0]['list_count_sp'] : $moviebox[0]['list_count']) : (preg_match('/sp/', $_SERVER['REQUEST_URI']) ? $moviebox[0]['paging_count_sp'] : $moviebox[0]['paging_count']));
        if ($mcount == 0) $mcount = 1;

        //現在のページ確認
        if ($type == anchor) {
            $data = movie_info_select_by_user($pid, time());
            $pno = 0;
            for ($ino = 0; $ino < $count; $ino++) {
                if (($ino) % $mcount == 0)
                    $pno += 1;
                if ($data[$ino]['id'] == $id)
                    break;
            }
        }
        else { if ($pno == 0)
                $pno = 1;        }

        if ($cmd == 'print') {
            $pvar = webparam('print', array());
            $plist = array();
            foreach ($pvar as $pone) {
                array_push($plist, movie_info_find($pone));
            }
        }
        if ($cmd == 'list') {
            $plist = movie_info_limit_by_user($pid, time(), $cid, 0, $mcount);
        }

        $doclist = $all
            ? $plist
            : movie_info_limit_by_user($pid, time(), $cid, ($pno - 1) * $mcount, $mcount);

        $doc = movie_viewlist($doclist, $config, $pid, $agent);
        //現在のページ数を示す
        $cval = $count / $mcount;
        $ccval = intval($cval);
        $pcount = ($ccval == $cval) ? $cval : $ccval + 1;

    }

    $data['movie'] = array('type' => 'text', 'val' => $doc);
    $data['paging'] = array('type' => 'text', 'val' => $paging);
    $data['count'] = array('type' => 'text', 'val' => $count);
    $data['pcount'] = array('type' => 'text', 'val' => $pcount);
    $data['pid'] = array('type' => 'text', 'val' => $pid);
    $data['page'] = $page;
    $data[user_category] = $category_data['html'];

    return moviepage_view($data, $data);
}

/** リスト一覧表示 */
function movie_viewlist($doclist, $config, $pid, $agent) {
    foreach ($doclist as $done) {
        $ret .= movie_preview(movie_load($done['id']), $config, $pid, $agent);
    }
    return $ret;
}

/** プレビューモード */
function movie_preview($doc, $config, $pid, $agent) {

    $textconf = $config;

    //★プレビューモードの場合はここで表示
    $text_all = movie_couf_ini_text_all($textconf);

    foreach ($text_all as $tone) {
        if ($tone['no'] == $doc["content_temp_id"]) { $docinfo = $tone; break;        }
    }

    $wk_agent = $agent == '' ? '' : '_sp';
    if ($docinfo === null)
        pout(html_errtag("テンプレートにない番号が指定されました"));
    $pstyle = $docinfo['style' . $wk_agent];
    $temp = new tLoader($docinfo['file' . $wk_agent]);

    $pmain = mb_convert_encoding($temp->getData('main'), 'utf-8','shift-jis');
    #$pmain = $temp->getData('main');

    $pitem  = mb_convert_encoding($temp->getData('item'), 'utf-8','shift-jis');
    #$pitem = $temp->getData('item');

    $pimage = mb_convert_encoding($temp->getData('image'), 'utf-8','shift-jis');
    #$pimage = $temp->getData('image');

    $ppdf  = mb_convert_encoding($temp->getData('pdf'), 'utf-8','shift-jis');
	is_null($doc['item']['pdf']['2']['value']) ? $pmain : $pmain.=$ppdf ;

    $page = movie_create($doc, $pmain, $pitem, $pimage, $docinfo,$ppdf);


    if ( mb_detect_encoding( $page ) == "UTF-8" ) {
        $page = mb_convert_encoding($page,'shift-jis','utf-8');
    }
    return $page;
}

/** ドキュメント単体 */
function movie_create($doc, $templab, $tempitem, $tempimg, $docinfo, $temppdf = null) {

    //AIRに移行してなくなる部分？　完全データ化すればAIRの移行も問題ない
    //日付データの作成
    $date = $doc['view_date'];
    $v_year = date("Y", $date); $v_month = date("n", $date); $v_day = date("j", $date);
    //★★★ちょっと異なるモデル
    $item = array();
    if (count($doc) == 0)
        return null;
    if ($docinfo['style'] == 3) {
        for ($i = 1; $i <= 10; $i++) {
            foreach ($doc['item'][$i] as $ione) {
                if ($ione['data_data_type'] != 2)
                    continue;
                $dfile = file_movie_find($ione['value']);
                $path = userpath("movie/save/") . $dfile['savefile'];
                list($x, $y) = file_exists($path) ? getimagesize($path) : array(0, 0);//★★
                $allx += $x + ($all ? $docinfo['margin-img-right'] : 0);
                $maxy = $y >= $maxy ? $y : $maxy;
            }
        }
        //横幅から縮尺を調べる
        $xview = ($allx == 0 || $docinfo['frame-x'] >= $allx || $docinfo['frame-x'] == 0 ) ? 1 : (float) $docinfo['frame-x'] / (float) $allx;

        $xview = ($docinfo['frame-y'] == 0 || $maxy == 0 || intval($maxy * $xview) <= $docinfo['frame-y']) ? $xview : (float) $docinfo['frame-y'] / (float) $maxy;
    }
    for ($i = 1; $i <= 10; $i++) {
        $view = array();
        foreach ($doc['item'][$i] as $ione) {
            $img = array();
            $view['text'] = array('type' => 'text', 'val' => $ione['value']);
        }

        //テキストブロックのサイズ
        $size = $valimg['val'] ? $docinfo['text-image'] : $docinfo['text-only'];
        $size_sp = $valimg['val'] ? $docinfo['text-image_sp'] : $docinfo['text-only_sp'];
        $view['text-size'] = array('type' => 'text', 'val' => $size);
        $view['text-size_sp'] = array('type' => 'text', 'val' => $size_sp);
        if ($view['image-left'][val] || $view['image-right']['val'] || $view['text']['val'])
            array_push($item, $view);
    }

    //表示
    $label['page'] = $templab;
    $label['self']      = array('type' => 'text', 'val' => "movie.php?pid=" . $doc['group_id'] . "&cmd=anchor&param=" . $doc['id'] . "#id" . $doc['id']);
    $label['frame-x']   = array('type' => 'text', 'val' => $docinfo['frame-x']);
    $label['frame-x_sp']   = array('type' => 'text', 'val' => $docinfo['frame-x_sp']);
    $label['title']     = array('type' => 'text', 'val' => $doc['title']);
    $label['view_year'] = array('type' => 'text', 'val' => $v_year);
    $label['view_month']= array('type' => 'text', 'val' => $v_month);
    $label['view_day']  = array('type' => 'text', 'val' => $v_day);
    $label['summary']   = array('type' => 'text', 'val' => $doc['summary']);
    $label['id']        = array('type' => 'text', 'val' => $doc['id']);
    $label['movie']  = array('type' => 'each', 'val' => $item, 'text' => $tempitem);

	//120928　アローラ椎本修正　
	//doc[pdf]が無いなら、これ！
	if($doc['pdf'] == null){

	//リンク名を入れる。
	is_null($doc['item']['pdf']['1']['value']) || empty($doc['item']['pdf']['1']['value']) ?
		$descpdf = /*'pdf'*/'' : $descpdf = $doc['item']['pdf']['1']['value'];
	//ファイルの説明
	is_null($doc['item']['pdf']['3']['value']) || empty($doc['item']['pdf']['3']['value']) ?
		$descpdf1 = /*'pdfカタログをご覧いただけます。' */'': $descpdf1 = $doc['item']['pdf']['3']['value'];
	$file_name = $doc['item']['pdf']['2']['value'];
	$extension = pathinfo($doc['item']['pdf']['2']['value'], PATHINFO_EXTENSION);
	$extension == 'pdf' ? $img = 'img/img-index/ico_pdf.gif' : $img ='' /*'img/img-index/ico_download.gif'*/;
	if($img != ''){
		$imgtag = '<img src="'.$img.'">';
	}
	}else{
		$descpdf = $doc['pdf']['value']['filedescription'];
		$descpdf1 = $doc['pdf']['value']['filedescription1'];
		$file_name = $doc['pdf']['value']['filepath'];
		$extension = pathinfo($doc['pdf']['value']['filepath'], PATHINFO_EXTENSION);
		$extension == 'pdf' ? $img = 'img/img-index/ico_pdf.gif' : $img = 'img/img-index/ico_download.gif';
	$imgtag = '<img src="'.$img.'">';
	}
    //違うなら、doc[pdf]の中身を使う。


	/*print_r(array(
		'doc[pdf]'=>$doc['pdf'],
		'doc'=>$doc,
		'diescpdf'=>$descpdf,
		'diescpdf1'=>$descpdf1,
		'filedescription'=>$doc['pdf']['value']['filedescription']
	));//die;*/

	//$extension = pathinfo($doc['item']['pdf']['2']['value'], PATHINFO_EXTENSION);


    $label['descpdf']   = array('type' => 'text', 'val' => $descpdf.$imgtag);
    //$label['selpdf']    = array('type' => 'text', 'val' => $doc['item']['pdf']['2']['value']);
    $label['selpdf']    = array('type' => 'text', 'val' => $file_name/*$doc['item']['pdf']['2']['value']*/);
    $label['descpdf1']   = array('type' => 'text', 'val' => $descpdf1);
/*
	if(!is_null($temppdf)){
		$label['pdf']   = array('type' => 'each', 'val' => $pdfitem, 'text' => $temppdf);
	}
*/
    #echo '<pre>';
    #print_r($label);
    #echo '</pre>';

    #exit();
    return movie_view($label, $label);
}

?>
