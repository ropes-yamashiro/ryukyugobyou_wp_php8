<?php

#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/09
# History: 2010/03/26 プレビュー時のイメージを削除できるように修正
#==================================
userload('document/document.php');
userload('document_conf/document_conf.php');



//設定ファイル
$config = confpath("document/config.ini");
$pconfig = confpath("document/print.ini");
$lconfig = confpath("document/list.ini");

define("DCONF", $config);
define("PCONF", $pconfig);
define("LCONF", $lconfig);

/**
 * データを表示する
 * @param int $pid データNo
 * @param int $pno ページングNo
 * @return view ページを出力します
 */
function document_view($pid = 1, $pno = 1, $cid = 0, $agent = '', $ajax = false){

//	$config = confpath("document/config.ini");
//	$pconfig = confpath("document/print.ini");
//	$lconfig = confpath("document/list.ini");
	$config = DCONF;
	$pconfig = PCONF;
	$lconfig = LCONF;

    // 受験のヒントを参照しているとき、ログインしていない場合はエラーとする
    $juken_conf = new cssConfig($config);
    $juken_data = $juken_conf->getSelector('hint_pid');
    $groupid = $juken_data->getValue('hint_pid');

//    if ($pid == $groupid && !isset($_SESSION['hint']) && !isset($_GET['param'])) {
//        $err_msg = '不正なアクセスです。';
//        die(mb_convert_encoding($err_msg, 'Shift-jis', 'UTF-8'));
//    }

    //基本データの取得:ブログの取得
    $id = webparam('param');  //★dataモデル
    $cmd = webparam('cmd');
    $all = ($cmd == 'print' || $cmd == 'list' || $cmd == 'new') ? true : false; //印刷モード or 一覧モード

    $config = $all ? ($cmd == 'print' ? $pconfig : $lconfig) : $config;
    $type = $id ? ($cmd == 'anchor' ? 'anchor' : ($cmd=='img' ? 'img' : 'data')) : ($cmd == 'preview' ? 'form' : 'paging');
    $type = $all ? 'paging' : $type;
	
	$lcount = webparam("lcount");	//表示件数指定
	$vcount = webparam("vcount");	//固定テンプレート使用時　※$lcountが優先

//echo('$_POST=<br>');print_r($_POST);echo('<br>');
//echo('$_GET=<br>');print_r($_GET);echo('<br>');
//echo('webparam("cmd")=<br>');print_r(webparam("cmd"));echo('<br>');
//echo('$id=<br>');print_r($id);echo('<br>');
//echo('$cmd=<br>');print_r($cmd);echo('<br>');
//echo('$all=<br>');print_r($all);echo('<br>');
//echo('$type=<br>');print_r($type);echo('<br>');

    //テンプレートの読込み:外観
    $frame_all = document_couf_ini_frame_all($config);

    // カテゴリー情報取得
    $category_data = get_category_tag($pid, $cid);
    if ( $cid == 0 ) {
        $cid = $category_data['cid'];
    }

    $cond = container_info_find($pid);
    $frame = $frame_all[$cond["container_temp_id"]];

    if ($frame === null)
        return html_errtag("「ブログ設定」を済ませていないか、設定データがない可能性があります");
	
	$ajax_prefix = ( $ajax ? "_ajax" : "" );
//	$page = $agent == '' ? file_get_contents($frame['file']) : file_get_contents($frame['file_sp']);
	$page = $agent == '' ? file_get_contents($frame['file'.$ajax_prefix]) : file_get_contents($frame['file_sp'.$ajax_prefix]);

    //表示中記事のカテゴリ名を取得
    if (isset($_GET['cid'])) {
        $res_category = category_master_find($_GET['cid']);
        $page = rep($page, 'category_title', mb_convert_encoding($res_category['webtitle'], 'Shift-jis', 'UTF-8'));
    } else {
        $page = rep($page, 'category_title', '');
    }
    $request_url = str_replace("sp/", "", $_SERVER['REQUEST_URI']);
    $page = rep($page, 'href', '..' . $request_url);
	
    if ($type == 'data') {
        //★data(保存しているデータ)
        $doc = doc_preview(document_load($id), $config, $pid, $agent); //★★
        $paging = "";
        $count = 1;
        $pcount = 1;
    } elseif ($type == 'form') {
        //★postモデル(プレビュー:単体)
        $doc = doc_preview(document_form(true), $config, $pid, $agent); //★★
        //$doc = doc_preview(document_load($_GET['param']), $config, $pid); //★★
        //$doc = doc_preview(document_form(true)/*document_load($_GET['param'])*/, $config, $pid); //★★
        $paging = "";
        $count = 1;
        $pcount = 1;
    } elseif ($type == 'img') {
        file_contents_view($id, false, true);
        return;
    } else {
        //★pagingモデル
		//
        //データを取得して、表示作成
        $container = container_info_select_container_temp_id($pid);

		if (!empty($lcount) && $lcount > 0) {
			$mcount = $lcount;
		}
		else if (!empty($vcount) && $vcount > 0) {
			$mcount = $vcount;
		}
		else{
			$mcount = 
					$cmd == 'print' ? 2000 
									: (
										($cmd == 'list' || $cmd == 'new')	? (preg_match('/sp/', $_SERVER['REQUEST_URI']) ? $container[0]['list_count_sp'] : $container[0]['list_count']) 
																			: (preg_match('/sp/', $_SERVER['REQUEST_URI']) ? $container[0]['paging_count_sp'] : $container[0]['paging_count'])
									  );

			if ($mcount == 0) $mcount = 1;
		}

		if ($cmd == 'new') {
			$count = $mcount;
		}
		else{
	        $count = contents_info_count_by_user($pid, time(),$cid);
		}
		
        //現在のページ確認
        if ($type == "anchor") {
            $data = contents_info_select_by_user($pid, time());
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
                array_push($plist, contents_info_find($pone));
            }
        }
		
		if ($cmd == 'new') {
			$plist = contents_info_limit_by_user($pid, time(), $cid, 0, $mcount);
		}
		if ($cmd == "list") {
			$plist = contents_info_limit_by_user($pid, time(), $cid, ($pno - 1) * $mcount, $mcount);
		}

        $doclist = $all
            ? $plist
            : contents_info_limit_by_user($pid, time(), $cid, ($pno - 1) * $mcount, $mcount);

        $doc = doc_viewlist($doclist, $config, $pid, $agent, $vcount);
        //現在のページ数を示す
        $cval = $count / $mcount;
        $ccval = intval($cval);
        $pcount = ($ccval == $cval) ? $cval : $ccval + 1;

        //△ぺーじんぐテンプレートを参照してデータを作りだす　→→→
        $paging = doc_paging($cmd, $pid, $pno, $cid, $mcount, $count, $config);
        //△横用のテンプレートと表示
    }

    $data['contents'] = array('type' => 'text', 'val' => $doc);
    $data['paging'] = array('type' => 'text', 'val' => $paging);
    $data['count'] = array('type' => 'text', 'val' => $count);
    $data['pcount'] = array('type' => 'text', 'val' => $pcount);
    $data['pid'] = array('type' => 'text', 'val' => $pid);
    $data['page'] = $page;
    $data["user_category"] = $category_data['html'];
    $data['cid'] = array("type" => "text", "val" => $cid);
    $data['param'] = array("type" => "text", "val" => $id);

    return html_view($data, $data);
}

function get_category_tag($pid,$cid){

    // テンプレート取得
	$template = new tLoader(confpath("document/textframe/category.html"));
    $link_all = $template->getData("link_all");
    $no_link_all = $template->getData("no_link_all");
    $link_category = $template->getData("link_category");
    $no_link_category = $template->getData("no_link_category");

    $category_master = category_master_find_visible($pid);
    $ret = array();
    if ( $cid == 0 ) {
        $ret['html'] = rep($no_link_all,'pid',$pid);
    } else {
        $ret['html'] = rep($link_all,'pid',$pid);
    }
    foreach( $category_master as $data ) {
        if ( $data[id] == $cid ) {
            $category_html = $no_link_category;
        } else {
            $category_html = $link_category;
        }
        $category_html = rep($category_html,'pid',$pid);
        $category_html = rep($category_html,'cid',$data[id]);
        $category_html = rep($category_html,'webtitle',$data[webtitle]);
        $ret['html'] .= $category_html;
    }
    if ( $link_all ) {
        $ret['cid'] = 0;
    } else {
        $ret['cid'] = $category_master[0]['id'];
    }

    return $ret;
}

function doc_paging($cmd, $pid, $cpage, $cid, $count, $total, $config) {
//echo('$cmd=<br>');print_r($cmd);echo('<br>');
//echo('$pid=<br>');print_r($pid);echo('<br>');
//echo('$cpage=<br>');print_r($cpage);echo('<br>');
//echo('$cid=<br>');print_r($cid);echo('<br>');
//echo('$count=<br>');print_r($count);echo('<br>');
//echo('$total=<br>');print_r($total);echo('<br>');
//echo('$config=<br>');print_r($config);echo('<br>');

    $infos = document_couf_ini_paging_all($config);

    $cond = container_info_find($pid);
    $info = new tLoader($infos[$cond["paging_temp_id"]]['file']);

    $pmain = trim($info->getData('main'));
    $pprevi = trim($info->getData('previous'));
    $pnext = trim($info->getData('next'));
    $pmore = trim($info->getData('more'));
    $pselect = trim($info->getData('select'));
    $pnoselect = trim($info->getData('non-select'));


    $mpage = intval($total / $count) + ((($total % $count) > 0) ? 1 : 0); //必要ページ数

    $min = $cpage - ($cpage % 5 ? $cpage % 5 : 5 ) + 1;//区切り最小値
    $max = $min + 4 <= $mpage ? $min + 4 : $mpage; //区切り最大値

    if ($mpage == 1
        )return;

    if ($cpage > 1) {//前の五件があるか
        $prepage = $cpage - 1;
        $val = rep($pprevi, 'path', "document.php?cmd=$cmd&pid=$pid&pno=$prepage&cid=$cid");
    }
    $pmain = rep($pmain, 'previous', $val); $val = "";


    for ($i = $min; $i <= $max; $i++) {//中の五件があるか
        if ($cpage == $i) {$out .= rep($pselect, 'number', $i);        } else {
            $val = rep($pnoselect, 'number', $i);
            $out .= rep($val, 'path', "document.php?cmd=$cmd&pid=$pid&pno=$i&cid=$cid");
        }
    }
    $pmain = rep($pmain, 'paging', $out); $val = "";


    if ($mpage > $cpage) { //次の５件表示があるか？
        $nextpage = $cpage + 1;
        $val = rep($pnext, 'path', "document.php?cmd=$cmd&pid=$pid&pno=$nextpage&cid=$cid");
		
		$val2 = rep($pmore, 'path', "javascript:goPage(".$nextpage.",".$pid.",".$cid.",0)");
    }
    $pmain = rep($pmain, 'next', $val); $val = "";
    $pmain = rep($pmain, 'more', $val2); $val2 = "";
	

    return $pmain;
}

/** リスト一覧表示 */
function doc_viewlist($doclist, $config, $pid, $agent, $vcount = null) {

	if (count($doclist) == 1 && $config == LCONF && $vcount) {
		$doc = document_load($doclist[0]["id"]);
		$doc["content_temp_id"] = "9";
		$ret = doc_preview($doc, $config, $pid, $agent);
	}
	else {
    foreach ($doclist as $done) {
        $ret .= doc_preview(document_load($done['id']), $config, $pid, $agent);
    }
	}
    return $ret;
}

/** プレビューモード */
function doc_preview($doc, $config, $pid, $agent) {

    $textconf = $config;

    //★プレビューモードの場合はここで表示
    $text_all = document_couf_ini_text_all($textconf);

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

    $page = doc_create($doc, $pmain, $pitem, $pimage, $docinfo,$ppdf);


    if ( mb_detect_encoding( $page ) == "UTF-8" ) {
        $page = mb_convert_encoding($page,'shift-jis','utf-8');
    }
    return $page;
}

/** ドキュメント単体 */
function doc_create($doc, $templab, $tempitem, $tempimg, $docinfo, $temppdf = null) {

    //AIRに移行してなくなる部分？　完全データ化すればAIRの移行も問題ない
    //日付データの作成
    $date = $doc['view_date'];
    $v_year = date("Y", $date); $v_month = date("n", $date); $v_day = date("j", $date);
	
	$weekday = date("w", $date);
	switch($weekday) {
		case 0:	$v_weekday = "日";	break;
		case 1:	$v_weekday = "月";	break;
		case 2:	$v_weekday = "火";	break;
		case 3:	$v_weekday = "水";	break;
		case 4:	$v_weekday = "木";	break;
		case 5:	$v_weekday = "金";	break;
		case 6:	$v_weekday = "土";	break;
	}
	
    //★★★ちょっと異なるモデル
    $item = array();
    if (count($doc) == 0)
        return null;
    if ($docinfo['style'] == 3) {
        for ($i = 1; $i <= 10; $i++) {
            foreach ($doc['item'][$i] as $ione) {
                if ($ione['data_data_type'] != 2)
                    continue;
                $dfile = file_contents_find($ione['value']);
                $path = userpath("document/save/") . $dfile['savefile'];
                list($x, $y) = file_exists($path) ? getimagesize($path) : array(0, 0);//★★
                $allx += $x + ($all ? $docinfo['margin-img-right'] : 0);
                $maxy = $y >= $maxy ? $y : $maxy;
            }
        }
        //横幅から縮尺を調べる
        $xview = ($allx == 0 || $docinfo['frame-x'] >= $allx || $docinfo['frame-x'] == 0 ) ? 1 : (float) $docinfo['frame-x'] / (float) $allx;

        $xview = ($docinfo['frame-y'] == 0 || $maxy == 0 || intval($maxy * $xview) <= $docinfo['frame-y']) ? $xview : (float) $docinfo['frame-y'] / (float) $maxy;
    }
	$image_count = 10;
	if (!empty($docinfo["image-count"])) {
		$image_count = $docinfo["image-count"];
	}
	for($i= 1; $i <= $image_count; $i++){
        $view = array();
        foreach ($doc['item'][$i] as $ione) {
            $img = array();
            switch ($ione['data_data_type']) {
                case 1: { $view['text'] = array('type' => 'text', 'val' => $ione['value']);                    }break;
                case 2: {
                        $dfile = file_contents_find($ione['value']);
                        $path = userpath("document/save/") . $dfile['savefile'];
                        list($x, $y) = is_file($path) ? getimagesize($path) : array(0, 0);//★★@@@
                        $imgpath = "document.php?cmd=img&param=" . $ione['value'] . "&pid={pid}";
                        $img['img'] = array('type' => 'text', 'val' => $imgpath);
                        $img['filename'] = array('type' => 'text', 'val' => $dfile['realfile']);
                        $img['x'] = array('type' => 'text', 'val' => $x);
                        $img['y'] = array('type' => 'text', 'val' => $y);
                        $img['page'] = $tempimg;

                    //    if ($docinfo['style'] == 1) {
                        if ($docinfo['style'] == 1 || $docinfo['style'] == 11) {
							if ($docinfo['style'] == 11 && $i > 1) {
								$valimg = array('type' => 'text', 'val' => "");
							}
							else {
								$img['max-y'] = array('type' => 'text', 'val' => $y > $docinfo['max-y'] ? $docinfo['max-y'] : $y);
								$img['max-x'] = array('type' => 'text', 'val' => $x > $docinfo['max-x'] ? $docinfo['max-x'] : $x);
								$img['max-y_sp'] = array('type' => 'text', 'val' => $y > $docinfo['max-y_sp'] ? $docinfo['max-y_sp'] : $y);
								$img['max-x_sp'] = array('type' => 'text', 'val' => $x > $docinfo['max-x_sp'] ? $docinfo['max-x_sp'] : $x);
								$valimg = array('type' => 'text', 'val' => $dfile['id'] ? html_view($img, $img) : "");
							}
                        } elseif ($docinfo['style'] == 2 || $docinfo['style'] == 12) {
							if ($docinfo['style'] == 12 && $i == 1) {
								$valimg = array('type' => 'text', 'val' => "");
							}
							else {
								$yview = ($docinfo['frame-y'] == 0 || $y < $docinfo['frame-y'] ) ? 1 : (float) $docinfo['frame-y'] / (float) $y;
								$yview = ($docinfo['frame-x'] == 0 || intval($x * $yview) <= $docinfo['frame-x']) ? $yview : (float) $docinfo['frame-x'] / (float) $x;
								$yview_sp = ($docinfo['frame-y_sp'] == 0 || $y < $docinfo['frame-y_sp'] ) ? 1 : (float) $docinfo['frame-y_sp'] / (float) $y;
								$yview_sp = ($docinfo['frame-x_sp'] == 0 || intval($x * $yview_sp) <= $docinfo['frame-x_sp']) ? $yview_sp : (float) $docinfo['frame-x_sp'] / (float) $x;

								$img['view-y'] = array('type' => 'text', 'val' => intval($y * $yview));
								$img['view-x'] = array('type' => 'text', 'val' => intval($x * $yview));
								$img['view-y_sp'] = array('type' => 'text', 'val' => intval($y * $yview_sp));
								$img['view-x_sp'] = array('type' => 'text', 'val' => intval($x * $yview_sp));
								$valimg = array('type' => 'text', 'val' => $dfile['id'] ? html_view($img, $img) : "");
							}
                        } elseif ($docinfo['style'] == 3) {
                            $img['margin-img-right'] = array('type' => 'text', 'val' => $docinfo['margin-img-right']);
                            $img['view-x'] = array('type' => 'text', 'val' => intval($x * $xview));
                            $img['margin-img-right_sp'] = array('type' => 'text', 'val' => $docinfo['margin-img-right_sp']);
                            $img['view-x_sp'] = array('type' => 'text', 'val' => intval($x * $xview_sp));
                            $valimg = array('type' => 'text', 'val' => $dfile['id'] ? html_view($img, $img) : "");
						}
						
                    }break;
                case 3: {
                        $pos = ($ione['value'] ? 'right' : 'left');
                }break;
            }
        }
		

        //イメージに入れるデータ
        $view['image-left'] = $pos == 'left' ? $valimg : array('type' => 'text', 'val' => "");
        $view['image-right'] = $pos == 'right' ? $valimg : array('type' => 'text', 'val' => "");
        $view['image-left_sp'] = $pos == 'left' ? $valimg : array('type' => 'text', 'val' => "");
        $view['image-right_sp'] = $pos == 'right' ? $valimg : array('type' => 'text', 'val' => "");

        //イメージの位置
        $vset = ($pos == 'left' ? $docinfo['image-left'] : $docinfo['image-right'] );
        $view['image-pos'] = array('type' => 'text', 'val' => $vset);
        $vset_sp = ($pos == 'left' ? $docinfo['image-left_sp'] : $docinfo['image-right_sp'] );
        $view['image-pos_sp'] = array('type' => 'text', 'val' => $vset_sp);

        //テキストの位置情報
        if ($valimg['val']) {
            $vset = ($pos == 'left' ? $docinfo['image-left-text'] : $docinfo['image-right-text'] );
            $vset_sp = ($pos == 'left' ? $docinfo['image-left-text_sp'] : $docinfo['image-right-text_sp'] );
            $view['text-pos'] = array('type' => 'text', 'val' => $vset);
            $view['text-pos_sp'] = array('type' => 'text', 'val' => $vset_sp);
        }
        else
            $view['text-pos'] = array('type' => 'text', 'val' => $docinfo['image-none-text']);
            $view['text-pos_sp'] = array('type' => 'text', 'val' => $docinfo['image-none-text_sp']);

        //テキストブロックのサイズ
        $size = $valimg['val'] ? $docinfo['text-image'] : $docinfo['text-only'];
        $size_sp = $valimg['val'] ? $docinfo['text-image_sp'] : $docinfo['text-only_sp'];
        $view['text-size'] = array('type' => 'text', 'val' => $size);
        $view['text-size_sp'] = array('type' => 'text', 'val' => $size_sp);
        if ($view['image-left']['val'] || $view['image-right']['val'] || $view['text']['val'])
            array_push($item, $view);
		
		//サムネイル
		
    }

	
	
	
	
	
	
	//  カテゴリ別ブログへのリンクの共通部分
	$link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/document.php?pid=" . $doc['group_id'];

	$category = "";
	if (!empty($doc["item"]["category"])) {
		
		$category_tag	= ( !isset($docinfo["category-tag"])  || empty($docinfo["category-tag"])	? "li"	: $docinfo["category-tag"] );
		$category_link	= ( !isset($docinfo["category-link"]) || empty($docinfo["category-link"])	? false	: $docinfo["category-link"]);
		
		foreach($doc["item"]["category"] as $cat)
		{
			$category		.= '<'.$category_tag.' class="category category'.str_pad($cat["id"], 2, "0", STR_PAD_LEFT).'">';
			if ($category_link) {
				$category	.= '<a href="'.$link.'&cid='.$cat['id'].'">';
			}
			$category		.= $cat["webtitle"];
			if ($category_link) {
				$category	.= '</a>';
			}
			$category		.= '</'.$category_tag.'>';
			
//			$category .= '<span class="category category'.str_pad($cat["id"], 2, "0", STR_PAD_LEFT).'"><a href="'.$link.'&cid='.$cat['id'].'">'.$cat["webtitle"].'</a></span>';
//			$category .= '<span class="category category'.str_pad($cat["id"], 2, "0", STR_PAD_LEFT).'">'.$cat["webtitle"].'</span>';
//			$category .= '<li class="category'.$cat["id"].'"><a href="'.$link.'&cid='.$cat['id'].'">'.$cat["webtitle"].'</a></li>';

		}
	}
	
	
	
	
	
    //表示
	$label['page'] = $templab;
	$label['self']		= array('type' => 'text', 'val' => "document.php?pid=" . $doc['group_id'] . "&cmd=anchor&param=" . $doc['id'] . "#id" . $doc['id']);
	$label['frame-x']	= array('type' => 'text', 'val' => $docinfo['frame-x']);
	$label['frame-x_sp']= array('type' => 'text', 'val' => $docinfo['frame-x_sp']);
	$label['title']		= array('type' => 'text', 'val' => $doc['title']);
	$label['userid']	= array('type' => 'text', 'val' => $doc['userid']);
	$label['username']	= array('type' => 'text', 'val' => $doc['username']);
	$label['link']		= array('type' => 'text', 'val' => "document.php?pid=" . $doc['group_id'] . "&cid=-" . $doc['passwd_info_id']);
	$label['view_year']	= array('type' => 'text', 'val' => $v_year);
	$label['view_month']= array('type' => 'text', 'val' => $v_month);
	$label['view_day']	= array('type' => 'text', 'val' => $v_day);
	$label["view_weekday"]	= array("type" => "text", "val" => $v_weekday);
	$label['id']		= array('type' => 'text', 'val' => $doc['id']);
	$label['contents']	= array('type' => 'each', 'val' => $item, 'text' => $tempitem);
	
	$summary = $doc['summary'];
	if (!empty($docinfo['summary-length'])) {
		$summary = mb_strimwidth(strip_tags($summary), 0, $docinfo['summary-length'], "...", "UTF-8");
	}
	$label['summary']	= array('type' => 'text', 'val' => $summary);
	
	$label['category']	= array('type' => 'text', 'val' => $category);

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
	$extension == 'pdf' ? $img = 'img/img-topic/ico_pdf.gif' : $img ='img/img-topic/ico_download.gif';
	if($img != ''){
		$imgtag = '<img src="'.$img.'">';
	}
	}else{
		$descpdf = $doc['pdf']['value']['filedescription'];
		$descpdf1 = $doc['pdf']['value']['filedescription1'];
		$file_name = $doc['pdf']['value']['filepath'];
		$extension = pathinfo($doc['pdf']['value']['filepath'], PATHINFO_EXTENSION);
		$extension == 'pdf' ? $img = 'img/img-topic/ico_pdf.gif' : $img = 'img/img-topic/ico_download.gif';
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
    return html_view($label, $label);
}
?>
