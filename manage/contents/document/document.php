<?php

#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History: 2010/03/26 プレビュー時のイメージを削除できるように修正
#==================================
require_once(dirname(__FILE__) . '/sql/container_info.php');
require_once(dirname(__FILE__) . '/sql/contents_detail.php');
require_once(dirname(__FILE__) . '/sql/contents_info.php');
require_once(dirname(__FILE__) . '/sql/file_contents.php');
require_once(dirname(__FILE__) . '/sql/contents_pdf.php');
require_once(dirname(__FILE__) . '/../category/sql/category_info.php');
require_once(dirname(__FILE__) . '/../category/sql/category_master.php');


//===================================================
//  ユーザー機能
//#edit
//★★★テーブルが再帰的に可能・イメージモデル・イメージにはアクションが必要
//AIRでは関係ない

/** データのテキスト置換を行う */
function html_view($data, $text) {
    $page = $text['page'];
	$hidden = array();
	$old_data = array();
    foreach ($data as $key => $val) {
		
		if ($key == "page") { continue; }

		if (!is_array($val)) { continue; }
		
        if ($val['type'] == "select")
            $page = rep($page, $key, html_ophash2($val['list'], $val['val']));
        if ($val['type'] == 'text') {
            $tval = ($val['br']) ? html_br($val['val']) : $val['val'];
            $page = rep($page, $key, $tval);
        }
        if ($val['type'] == 'check') { $page = html_check($page, $key, $val['list'], $val['val']);        }
        if ($val['type'] == 'picture') {
            if ($val['dir']) {
                $path = $val['dir'] . $val['val'];
                $pic = html_imagetype($val['name'], $val['fname'], $val['no'], $path);
                $page = rep($page, $key, $pic);
            }
            else
                $page = rep($page, $key, html_imagetype($val['name'], $val['fname'], "", $val['no'], ""));
        }
        if ($val['type'] == 'pdf') {
            if ($val['dir']) {
                $path = $val['dir'] . $val['val'];
                $pdf = html_pdftype($val['name'], $val['fname'], $val['no'], $path);
                $page = rep($page, $key, $pdf);
            }
            else
                $page = rep($page, $key, html_pdftype($val['name'], $val['fname'], "", $val['no'], ""));
        }
        if ($val['type'] == 'each') {
            $vtext['page'] = $val['text']; $item = "";
            foreach ($val['val'] as $vval) { $item .= html_view($vval, $vtext);            }
            $page = rep($page, $key, $item);
        }
        if ($val['type'] == 'table') {
            $vtext['page'] = $val['text']; $item = $val['label'];
            foreach ($val['val'] as $vval) { $item .= html_view($vval, $vtext);            }
            $page = rep($page, $key, $item);
        }
        if ($val['hidden'] == true)
            $hidden[$key] = $val['val'];
        if ($val['save'] == true)
            $old_data[$key . '_old'] = $val['val'];
    }
    $page = rep($page, 'old_data', html_hidden(count($old_data) ? $old_data : array()));
    $page = rep($page, 'hidden', html_hidden(count($hidden) ? $hidden : array()));
	$page = rep($page, "user_category", mb_convert_encoding( $data["user_category"],'shift-jis','utf-8'));
    return $page;
}

/** DB形式を出力形式に変換して出力 */
//AIRでは関係ない 階層→平坦化
function document_value($info) {
    $list = document_list();

    //テンプレート
    $text['page'] = file_get_contents(userpath(switchSpTemplate("document/html/input.html")));

    //共通値 //コマンドでいける
    $db = container_info_find($list['pid']);         //グループタイトル
    $conf = document_couf_ini_text_all(confpath("document/config.ini")); //テキストテンプレート
    foreach ($conf as $cone) { $ltemp[$cone['no']] = $cone['label'];    }

    $size = document_couf_ini_image(confpath("document/config.ini"));
    $size = $size[webparam('pid')];

    //追加部分・テンプレート
    $info['content_temp_id'] = $info['content_temp_id'] ? $info['content_temp_id'] : 1;
    $viewtext['page'] = file_get_contents(userpath(switchSpTemplate("document/html/type" . $info['content_temp_id'] . ".html")));

    //詳細部分の作成 //出来れば避けたい→階層変換モデル //上手にやった動的にできるかも
    $view['viewtype_name'] = array('type' => 'text', 'val' => $ltemp[$info['content_temp_id']]);
    for ($i = 1; $i <= 10; $i++) {
        $item = $info['item'][$i];
        $cid = 'cid' . $i . '_' . $item['data_data_type'];
        foreach ($item as $ione) {
            $cid = 'cid' . $i . '_' . $ione['data_data_type'];
            $view[$cid] = array('type' => 'text', 'val' => $ione['id']);
            switch ($ione['data_data_type']) {
                case 1: { $view['text' . $i] = array('type' => 'text', 'hidden' => true, 'val' => $ione['value'],);                    }break;
                case 2: { //ここでデータ化
                        if ($ione['value']) {
                            $dfile = file_contents_find($ione['value']);
                            //dp($dfile[id] . ":" . $ione[value]);
                            $dir = "../";
                            $access = "document.php?cmd=img&param=" . $ione['value'] . "&pid={pid}";
                            $fname = $dfile['realfile'];
                        } else {
                            $dir = ""; $access = ""; $fname = "";
                        }
                        $view['img' . $i]
                                = array('type' => 'picture', 'hidden' => true
                            , 'dir' => $dir, 'val' => $access
                            , 'fname' => $fname
                            , 'no' => $ione['value'], 'name' => 'img' . $i);
                    }break;
                case 3: { $view['img' . $i . '_pos'] = array('type' => 'check', 'hidden' => true, 'val' => $ione['value'], 'list' => array(0, 1));                    }break;
            }
            $view[$cid] = array('type' => 'text', 'val' => $ione['id'], 'hidden' => true);
        }
    }

	$pitem = $info['item']["pdf"];
	if(!empty($pitem)){
		foreach ($pitem as $ione) {
			$cid = 'cid' . $i . '_' . $ione['data_data_type'];
			$view[$cid] = array('type' => 'text', 'val' => $ione['id']);
			switch ($ione['data_data_type']) {
				case 1: { $view['descpdf'] = array('type' => 'text', 'hidden' => true, 'val' => $ione['value'],);
					}break;
				case 2: { //ここでデータ化
					$dir = "contents/document/pdf/".$ione['value']; $access = ""; $fname = $ione['value'];
					$view['selpdf']
					        = array('type' => 'pdf', 'hidden' => true
					    , 'dir' => $dir, 'val' => $access
					    , 'fname' => $fname
					    , 'no' => $ione['value'], 'name' => 'img' . $i);
					}break;
				case 3: { $view['descpdf1'] = array('type' => 'text', 'hidden' => true, 'val' => $ione['value'],);
					}break;
			}
		}
	}

    //★★データ作成　//コマンド作れば可能
    $lyear = numhash(2020, 2024); $lmonth = numhash(1, 12);
    $lday = numhash(1, 31); $lyesno = array(0, 1);

    //★★データ変換 //加工すれば、自動変換可能　★★★どっちつかずのデータ
    $date = $info['view_date']; if ($date == null)
        $date = time();
    $v_year = date("Y", $date); $v_month = date("n", $date); $v_day = date("j", $date);

    //表示データ作成　//完全モデル化可能
    $data['viewtype_input'] = array('type' => 'text', 'save' => false, 'val' => html_view($view, $viewtext));
    $data['pid'] = array('type' => 'text', 'val' => $list['pid'], 'hidden' => true);
    $data['typex'] = array('type' => 'text', 'val' => $list['typex'], 'hidden' => true);
    $data['count'] = array('type' => 'text', 'val' => $list['count'], 'hidden' => true);
    $data['pageno'] = array('type' => 'text', 'val' => $list['pageno'], 'hidden' => true);
    $data['group'] = array('type' => 'text', 'val' => $db['title']);
    $data['err'] = array('type' => 'text', 'val' => $info['err']);
    $data['id'] = array('type' => 'text', 'save' => false, 'val' => $info['id']);
    $data['title'] = array('type' => 'text', 'save' => true, 'val' => $info['title']);
    $data['viewtype'] = array('type' => 'select', 'save' => true, 'list' => $ltemp, 'val' => $info['content_temp_id']);
    $data['view_year'] = array('type' => 'select', 'save' => true, 'list' => $lyear, 'val' => $v_year);
    $data['view_month'] = array('type' => 'select', 'save' => true, 'list' => $lmonth, 'val' => $v_month);
    $data['view_day'] = array('type' => 'select', 'save' => true, 'list' => $lday, 'val' => $v_day);
    $data['visible'] = array('type' => 'check', 'save' => true, 'list' => $lyesno, 'val' => $info['visible']);
    $data['summary'] = array('type' => 'text', 'save' => true, 'val' => $info['summary']);

    $page = html_view($data, $text);

    // カテゴリ情報取得
    $info['category'] = view_document_category($info['id']);
    if ( $info['category'] ) {
        $page = rep($page,"category" , $info['category']);
        $page = str_replace( "<!--category", "", $page );
        $page = str_replace( "category-->", "", $page );
    }

    return $page;
}

/**
 * カテゴリ表示
 */
function view_document_category($id){

    //テンプレート部分--------------------------------------------
	$temp		= new tLoader(userpath(switchSpTemplate("document/html/list.html")));
	$tbase		= $temp->getData();
	$text		= whash($tbase,$text);
    $html = $text['category'];

    #カテゴリマスタ情報取得
    $category_master = category_master_find_by_contentsid(webparam("pid"));

    #カテゴリの情報を取り出す
    $category_info = category_info_find_by_category_info_id( $id );

    $overtwo = false;
    $ret = "";
	foreach( $category_master as $master ) {

        $data['value'] = $master['id'];
        $data['title'] = $master['title'];
        $data['webtitle'] = $master['webtitle'];
        $data['checked'] = '';

        foreach( $category_info as $info ) {

            if ( $info['category_id'] == $master['id'] ) {
                $data['checked'] = 'checked="checked" ';
                break;
            }
        }
        if ( !$overtwo ) {
            $overtwo = true;
        } else {
            $ret .= "<br />";
        }

        $ret .= phash($data, $html);

    }

    return $ret;
}

//==============================================================================
// file_contents_dbを使用する

/**
 * キャッシュデータを削除する(古いもの)
 */
function document_img_cache_clear() {
    $file = userpath('document/cache.txt');
    if (!file_exists($file))
        return;

    #キャッシュから値を読み出す
    $list = unserialize(file_get_contents($file));
    $last = array();
    foreach ($list as $lone) {
        if ($lone['time'] + 0 < time()) {
            foreach ($lone['list'] as $imgid) {
                file_contents_delete_picdb($imgid);
            }
        } else {
            $last[] = $lone;
        }
    }
    file_put_contents($file, serialize($last));
}

/**
 * キャッシュにイメージデータを追加する
 * @param int $idlist
 */
function document_img_cache_append($idlist) {
    if (count($idlist) == 0)
        return;
    $file = userpath('document/cache.txt');
    $var = array('time' => time(), 'list' => $idlist);
    if (file_exists($file)) {
        $list = unserialize(file_get_contents($file));
    } else {
        $list = array();
    }
    $list[] = $var;
    file_put_contents($file, serialize($list));
}

/**
 * 任意のIDがキャッシュか確認する
 * @param int $id
 */
function document_img_is_cache($id) {
    $file = userpath('document/cache.txt');
    if (!file_exists($file))
        return false;
    if ($id == null)
        return false;

    #キャッシュから値を読み出す
    $list = unserialize(file_get_contents($file));
    $last = array();
    foreach ($list as $lone) {
        foreach ($lone['list'] as $imgid) {
            if (intval($imgid) == $id)
                return true;
        }
    }
    return false;
}

/**
 * キャッシュから特定のIDを削除する.
 * ファイルは削除しない
 * @param int $id
 */
function document_img_remove_cache($id) {
    $file = userpath('document/cache.txt');
    if (!file_exists($file)) return;

    #キャッシュから値を読み出す
    $list = unserialize(file_get_contents($file));
    $last = array();

    for ($i = 0; $i < count($list); $i++) {
        foreach ($list[$i]['list'] as $key => $imgid) {
            if ($imgid == $id) {
                unset($list[$i]['list'][$key]);
            }
            if(count($list[$i]['list']) == 0) unset($list[$i]);
        }
    }
    file_put_contents($file, serialize($list));
}

//==============================================================================

/** フォームの値をDB形式に変換します */
function document_form($preview = false, $errsave = null) {

    //formのvalueと整合性が取れている必要がある
    $hash = array('id' => null, 'title' => null, 'viewtype' => null, 'view_year' => null
        , 'view_month' => null, 'view_day' => null, 'visible' => null, 'summary' => null, 'group_id' => webparam('pid'),'category_id'=>null);

    //関数化可能
    foreach ($hash as $key => $val) {
        $ret[$key] = webparam($key, $val);
    }

    $size = document_couf_ini_image(confpath("document/config.ini"));
    $size = $size[webparam('pid')];

    $ret['content_temp_id'] = $ret['viewtype'];
    $ret['summary'] = str_replace('\\"', '"', $ret['summary']);
    $ret['view_date'] = mktime(0, 0, 0, $ret['view_month'], $ret['view_day'], $ret['view_year']);
    $ret['item'] = array();

    #キャッシュファイル削除
    document_img_cache_clear();

    $cacheimglist = array();
    for ($i = 1; $i <= 10; $i++) {
        $item = array();


        $img = $_FILES['img' . $i];
        //イメージIDが割り当てられているか？
        if ($img['size']) {
            $imgid = file_contents_upload_new(0, 'img' . $i, $size['x'], $size['y']);
            //pr($imgid,img);
            #ファイルは必ずキャッシュ化
            $cacheimglist[] = $imgid;
        } else {
            $imgid = webparam('img' . $i);
            if (!is_numeric($imgid))
                $imgid = null;
            //pr($imgid,param);
        }


        $text = str_replace('\\"', '"', webparam('text' . $i));
        if(trim($text) == '<br />' || trim($text) == '&nbsp;') $text = null;

        #テキストデータ
        $item[1] =
                array('contents_info_id' => webparam('id'), 'data_data_type' => 1, 'value_id' => 1
                    , 'id' => webparam('cid' . $i . '_1'), 'temp_id' => $i, 'value' => $text);

        #イメージデータ
        $item[2] =
                array('contents_info_id' => webparam('id'), 'data_data_type' => 2, 'value_id' => 2
                    , 'id' => webparam('cid' . $i . '_2'), 'temp_id' => $i, 'value' => $imgid);

        #位置データ
        $item[3] =
                array('contents_info_id' => webparam('id'), 'data_data_type' => 3, 'value_id' => 3
                    , 'id' => webparam('cid' . $i . '_3'), 'temp_id' => $i, 'value' => webparam('img' . $i . '_pos'));



        $ret['item'][$i] = $item;
    }

	$file_nm = $_FILES['selpdf']['name'];
	$extension = pathinfo($file_nm, PATHINFO_EXTENSION);
	if (isset($_FILES['selpdf']) && $extension == 'doc' || $extension == 'xls' || $extension == 'pdf'){
		$now_id = '';
		$wid = webparam('id');
		if(!empty($wid)) $now_id = contents_pdf_find_ciid(webparam('id'));
		if(empty($now_id)){
			 $pdf['call'] = 'insert';
			$filepath = date('Ymdhis');
			$pdf['value'] = array('contents_info_id' => webparam('id'),
				'filepath' => $filepath . '.' .$extension,
				'filedescription' => $_POST['descpdf'],
				'filedescription1' => $_POST['descpdf1'],
				'created' => date("Y/m/d h:i:s")
			);
		}else{
			$pdf['call'] = 'update';
			$pdf['id'] = $now_id['id'];
			$filepath = date('Ymdhis');
			$pdf['value'] = array('id' => $now_id['id'],
				'filepath' => $filepath . '.' .$extension,
				'filedescription' => $_POST['descpdf'],
				'filedescription1' => $_POST['descpdf1']
			);
		}
		file_contents_upload_pdf($_FILES["selpdf"]["tmp_name"], $filepath . '.' .$extension);
	}
	$pdfdesc['value'] = array('id' => '','filepath' => ''
		,'filedescription' => $_POST['descpdf'],'filedescription1' => $_POST['descpdf1']);

	$ret['pdf'] = $pdf;
	$ret['pdfdesc'] = $pdfdesc;

	$ipdf[1] = array('contents_info_id' => webparam('id'), 'data_data_type' => 1, 'value_id' => 1, 'id' => '', 'value' => '');
	$ipdf[2] = array('contents_info_id' => webparam('id'), 'data_data_type' => 2, 'value_id' => 2, 'id' => '', 'value' => '');
	$ipdf[3] = array('contents_info_id' => webparam('id'), 'data_data_type' => 3, 'value_id' => 3, 'id' => '', 'value' => '');
	if(empty($pdf)){
		$pdfval = contents_pdf_find_ciid(webparam('id'));
		$pdfhidden = webparam('img11');
		if(($errsave == 'act_savelist' || $errsave == 'act_save') && empty($pdfhidden)){
			$ipdf[1] = array('contents_info_id' => webparam('id'), 'data_data_type' => 1, 'value_id' => 1, 'id' => '', 'value' => '');
			$ipdf[2] = array('contents_info_id' => webparam('id'), 'data_data_type' => 2, 'value_id' => 2, 'id' => '', 'value' => '');
			$ipdf[3] = array('contents_info_id' => webparam('id'), 'data_data_type' => 3, 'value_id' => 3, 'id' => '', 'value' => '');
		}elseif(!empty($pdfval)){
			$ipdf[1] = array('contents_info_id' => webparam('id'), 'data_data_type' => 1, 'value_id' => 1, 'id' => $pdfid, 'value' => $pdfval['filedescription']);
			$ipdf[2] = array('contents_info_id' => webparam('id'), 'data_data_type' => 2, 'value_id' => 2, 'id' => $pdfid, 'value' => $pdfval['filepath']);
			$ipdf[3] = array('contents_info_id' => webparam('id'), 'data_data_type' => 3, 'value_id' => 3, 'id' => $pdfid, 'value' => $pdfval['filedescription1']);
		}
	}
	if(empty($ret['item']['pdf'])) $ret['item']['pdf'] = $ipdf;

    document_img_cache_append($cacheimglist);

    //dp($ret);
    return $ret;
}

function document_check_err() {
    $p = $_POST;
    $mes = $p['title'] == null ? "タイトルを入力してください。<br />" : "";
    $summary = str_replace( array( " ", "　", "<br/>","\r\n", "&nbsp;" ), "", $p['summary'] );
    $text1 = str_replace( array( " ", "　", "&nbsp;","\r\n", "<br/>" ), "", $p['text1'] );
	if ($p["pid"] != "22") { //「小学校 - 行事予定」は、概要欄が空でもエラーにしない 2019.11.13 SB/Arikata
    if ( empty( $summary ) && empty( $text1 ) ) {
        $mes .= "少なくとも「コンテンツ」または「文章1」のどちらかを入力してください。<br />";
    }
	}

    /** ファイルのアップロードサイズを調べる
     * =成功 1=単体ファイルサイズエラー 2=ゼロサイズファイルエラー
     * ー@ が　0=成功　1=総合ファイルエラー
     * り値：(キー => エラー番号) の連想配列 */
    $chfile = checkUploadSize();

    $chfmax = ini_get('upload_max_filesize');
    $chtmax = ini_get('post_max_size');

    $mes .= $chfile["@"] == 1 ? "一度に合計".$chtmax."以上はアップロードできません。<br />" : "";

    for ($i = 1; $i<=10; $i++) {
        if ($_FILES['img' . $i]['name']) {

            $mes .= $chfile['img' . $i] == 2 ? "写真".$i."は、サイズが0のファイルが選択できません。<br />" : "";

            $mes .= $chfile['img' . $i] == 1 ? $chtmax."以上のファイルはアップロードできません。<br />" : "";

            $ext = isImageExt($_FILES['img' . $i]['name']);
            if ( $ext ) {
                $mes .= "写真".$i."は、.".$ext."のファイルは指定できません。<br />";
            }
        }
    }

    if ( !empty( $mes ) ) {
        $mes = "<font color=\"red\">".$mes."</font><br />";
    }

    return $mes;
}



/** データベースへの書き込み */
function document_save() {

    $form = document_form();

    if (!$form['id']) {
        $id = contents_info_insert($form);
        $form['sort'] = $id;
        $form['id'] = $id;
        contents_info_update($form);
    }//データの追加
    else {
        $old_data = document_load($form['id']);
        contents_info_update($form); //データの更新
        $id = $form['id'];
    }

    // カテゴリー登録
    $dm = category_info_delete_by_contents_info_id($id);
    if ( isset( $form['category_id'] ) ) {
        foreach ( $form['category_id'] as $key => $value  ) {
            $category_data['contents_info_id'] = $id;
            $category_data['category_id'] = $value;
            $dm = category_info_insert($category_data);
        }
    }

    //データを取り出す
    $itemlist = $form['item'];


    for ($i = 1; $i<= 10; $i++) {
        for ($n = 1; $n <= count($itemlist[$i]); $n++) {
            $ione = $itemlist[$i][$n];

            #イメージの有効化
            if ($ione['data_data_type'] == 2) {

                //データがある場合 常に値が入っている
                if ($ione['value']) {

                    #キャッシュで保存していたのものを取り出す
                    $done = file_contents_find($ione['value']);

                    #キャッシュ一覧から削除する
                    document_img_remove_cache($ione['value']);

                    #データを上書きする
                    $done['parent_id'] = $id;
                    file_contents_update($done);
                }



                #古いデータがあれば削除する ```````````````````````````````````````````
                $oldone = $old_data['item'][$i][$n];
			/*
                $file = dirname(__FILE__) . '/' . date('Y-m-d') . '.log';
                file_put_contents($file,  "===========================\n" . date('G:i;s') . "\n", FILE_APPEND);
                file_put_contents($file,var_export($oldone,true) . "\n\n\n\n", FILE_APPEND);
                file_put_contents($file,var_export($ione,true) . "\n\n\n\n", FILE_APPEND);
			*/

                #キャッシュでいらないファイルを削除する
                if ($oldone['value'] != null && $oldone['value'] != $ione['value']) {
                    #file_contents_delete_picdb($oldone['value'], $done);
                }
            }

            #IDがあれば更新
            if ($ione['contents_info_id']) { //更新
                #echo 'here';
                contents_detail_update($ione);
            } else { //追加
                #echo 'news';
                $ione['contents_info_id'] = $id;
                contents_detail_insert($ione);
            }
        }
    }

	if(isset($form['pdf'])){
		if($form['pdf']['call'] == 'insert'){
			$form['pdf']['value']['contents_info_id'] = $id;
			contents_pdf_insert($form['pdf']['value']);
		}elseif($form['pdf']['call'] == 'update'){
			contents_pdf_update($form['pdf']['id'],$form['pdf']['value']);
		}
	}

	if(isset($form['pdfdesc'])){
		$nowpdfid = contents_pdf_find_ciid($id);
		if(!empty($nowpdfid)){
			$form['pdfdesc']['value']['id'] = $nowpdfid['id'];
			$form['pdfdesc']['value']['filepath'] = $nowpdfid['filepath'];
			contents_pdf_update($form['pdfdesc']['id'],$form['pdfdesc']['value']);
		}
	}

    return $id;
}

/** データベースからデータの読み込み */
function document_load($id) {
    $info = contents_info_find($id);
    $size = document_couf_ini_image(confpath("document/config.ini"));
    $size = $size[webparam('pid')];
    $items[0] = array('contents_info_id' => $id, 'data_data_type' => 1, 'value_id' => 1, 'id' => "", 'temp_id' => 0, 'value' => "");
    #デフォルト値の作成
    for ($i = 1; $i <= 10; $i++) {
        $item = array();
        $item[1] = array('contents_info_id' => $id, 'data_data_type' => 1, 'value_id' => 1, 'id' => webparam('cid' . $i . '_1'), 'temp_id' => $i, 'value' => "");
        $item[2] = array('contents_info_id' => $id, 'data_data_type' => 2, 'value_id' => 2, 'id' => webparam('cid' . $i . '_2'), 'temp_id' => $i, 'value' => "");
        $item[3] = array('contents_info_id' => $id, 'data_data_type' => 3, 'value_id' => 3, 'id' => webparam('cid' . $i . '_3'), 'temp_id' => $i, 'value' => 0);
        $items[$i] = $item;
    }

    $itemsl = contents_detail_select_contents_id($id); //★★
    $itemret = array();
    foreach ($itemsl as $ione) {
        $group = $ione['temp_id'];
        $itemret[$group][$ione['value_id']] = $ione;
    }
    $info['item'] = count($itemret) ? $itemret : $items;

	$pdf = array();
	$pdfid = '';
	$pdfdesval = '';
	$pdfdesval1 = '';
	$pdfpatval = '';
	if(!empty($id)){
		$pdata = contents_pdf_find_ciid($id);
		$pdfid = $pdata['id'];
		$pdfdesval = $pdata['filedescription'];
		$pdfdesval1 = $pdata['filedescription1'];
		$pdfpatval = $pdata['filepath'];
	}
	$pdf[1] = array('contents_info_id' => $id, 'data_data_type' => 1, 'value_id' => 1, 'id' => $pdfid, 'value' => $pdfdesval);
	$pdf[2] = array('contents_info_id' => $id, 'data_data_type' => 2, 'value_id' => 2, 'id' => $pdfid, 'value' => $pdfpatval);
	$pdf[3] = array('contents_info_id' => $id, 'data_data_type' => 3, 'value_id' => 3, 'id' => $pdfid, 'value' => $pdfdesval1);
	$info['item']['pdf'] = $pdf;
	
	$category = category_info_find($id);
	$info["item"]["category"] = category_info_find($id);

    return $info;
}

/** リストの値をDB形式に変換します */
function document_list() {
    $hash = array('pid' => 1, 'typex' => 'document', 'count' => 20, 'pageno' => 1);
    foreach ($hash as $key => $val) {
        $ret[$key] = webparam($key, $val);
    }
    return $ret;
}

/** 表示部分?外部環境に依存しないようにする */
function document_tolist($data=array(), $rule = array(), $text=array()) {

    //値の準備====================================================
    //ルール部分
    $rule = whash(document_list(), $rule);

    //テンプレート部分--------------------------------------------
    $temp = new tLoader(userpath(switchSpTemplate("document/html/list.html")));
    $tbase = $temp->getData();
    $text = whash($tbase, $text);
	
    //データ部分---------------------------------------------------
    $err = ifrep($errview, 'value', $data['err']);

    //使用変数
    $pid = $rule['pid'];$pageno = $rule['pageno'];$count = $rule['count'];

    //項目名
    $item = container_info_find($pid);
    $table = contents_info_limit_by_group_id($pid, ($pageno - 1) * $count, $count);
    $conf = document_couf_ini_text_all(confpath("document/config.ini")); //外観

    $sel_list = webparam('print', array());

    for ($i = 0; $i < count($table); $i++) {
        $table[$i]['pid'] = $pid;
        $table[$i]['visible'] = $table[$i]['visible'] == 1 ? 'checked="checked"' : "";
        $date = $table[$i]['view_date'];
        $table[$i]['day'] = $date ? date('j', $date) : "";
        $table[$i]['month'] = $date ? date('n', $date) : "";
        $table[$i]['year'] = $date ? date('Y', $date) : "";
        $table[$i]['type'] = $conf[$table[$i]['content_temp_id']]['label'];
        $table[$i]['typex'] = $rule['typex'];

        //
        if ($data['sel'] == 'all')
            $selv = 'checked="checked"';
        elseif ($data['sel'] == 'off')
            $selv = '';
        else {$selv = in_array($table[$i]['id'], $sel_list) ? 'checked="checked"' : "";        }
        $table[$i]['print'] = $selv;
    }

    //表-テキスト
    foreach ($table as $row) {
        if (!$i)
            $i = 0;
        $row['color'] = $i % 2 + 1; $i++;
        $ttable .= ephash($row, $text['line']);
    }
    if (count($table) == 0)
        $ttable = $text['noval'];

    //ページング
    $tcount = contents_info_count_group_id($pid);
    $tcount = $tcount . "件";
    $count_st = array(10 => "10件", 20 => "20件", 50 => "50件", 100 => "100件");
    $paging = html_paging($tcount, $rule['count'], $rule['pageno'], $text['paging'], $text['paging_current']);

    //ルール部分==================================================
    $out['pid'] = array('type' => 'text', 'val' => $rule['pid'], 'hidden' => true);
    $out['typex'] = array('type' => 'text', 'val' => $rule['typex'], 'hidden' => true);
    $out['count'] = array('type' => 'select', 'val' => $rule['count'], 'list' => $count_st);
    $out['pageno'] = array('type' => 'text', 'val' => $rule['pageno'], 'hidden' => true);

    //表示部分====================================================
    $out['tcount'] = array('type' => 'text', 'val' => $tcount);   //DB呼び出し？
    $out['err'] = array('type' => 'text', 'val' => $err);   //実行タイミング？
    $out['title'] = array('type' => 'text', 'val' => $item['title']);  //作成タイミング
    $out['name'] = array('type' => 'text', 'val' => "ブログ");  //作成タイミング？
    $out['paging'] = array('type' => 'text', 'val' => $paging);
	
	$userinfo = session::get('login_info');
    $out['userid'] = array('type' => 'text', 'val' => $userinfo["userid"], "hidden" => true);

    //リスト部分==================================================
    $out['table'] = array('type' => 'text', 'val' => $ttable);    //てーぶる部分
    return html_view($out, $text);
}

//#end
//===================================================
//  定義機能

/** ファイルを削除する */
function document_act_delfile() {
    //#edit,func_delfile
    $info = document_form();
    $fid = webparam('param');

    if (document_img_is_cache($fid)) {
        document_img_remove_cache($fid);
        file_contents_delete_picdb($fid);
    }
    foreach ($info['item'] as $int => $val) {
        if ($val[2]['value'] == $fid) {
            $info['item'][$int][2]['value'] = null;
        }
    }
    return document_value($info);
    //#end
}

/** 表示スタイルを変更する */
function document_act_chtype() {
    //#edit,func_chtype
    $info = document_form();
    return document_value($info);
    //#end
}

/** データを新規作成する */
function document_act_newitem() {
    //#edit,func_newitem
    return document_act_detail();
    //#end
}

/** データを編集して、一覧に戻る。 */
function document_act_savelist() {
    //#edit,func_savelist
    $err = document_check_err();
    if ( empty( $err ) ) {
        document_save();
        return document_act_listview();
    } else {
        $info = document_form(false, 'act_savelist');
        $info['err'] = $err;
        return document_value($info);
    }
    //#end
}

/** データを保存する */
function document_act_save() {
    //#edit,func_save

    $err = document_check_err();

    if ( empty( $err ) ) {
        $id = document_save();
        $info = document_load($id);//★★★
    } else {
        $info = document_form(false, 'act_save');
        $info['err'] = $err;
    }

    return document_value($info);
    //return document_value(document_form());
    //#end
}

/** データを編集する */
function document_act_detail() {
    //#edit,func_detail
    $info = document_load(webparam('param'));//★★★
    return document_value($info);
    //#end
}

/** 全選択する */
function document_act_selall() {
    //#edit,func_selall
    $data = array('sel' => 'all');
    return document_tolist($data);
    //#end
}

/** 選択解除 */
function document_act_seloff() {
    //#edit,func_seloff
    $data = array('sel' => 'off');
    return document_tolist($data);
    //#end
}

/** @データを表示する */
function document_act_listview($err=null) {
    //#edit,func_listview
    return document_tolist();
    //#end
}

/** @データを上に移動する */
function document_act_up_line() {
    //#edit,func_up_line
    $param = webparam('param');
    $pid = webparam('pid');
    if ($param == null || $pid == null)
        return document_act_listview("存在しないデータを移動しようとしました");
    $table = contents_info_limit_by_up_line($pid, $param, 0);
    if (count($table) == 1)
        return document_act_listview();
    $id1 = $table[0]['id'];
    $sort1 = $table[0]['sort'];
    $id2 = $table[1]['id'];
    $sort2 = $table[1]['sort'];
    contents_info_update(array('id' => $id1, 'sort' => $sort2));
    contents_info_update(array('id' => $id2, 'sort' => $sort1));
    return document_act_listview();
    //#end
}

/** @データを下に移動する */
function document_act_down_line() {
    //#edit,func_down_line
    $param = webparam('param');
    $pid = webparam('pid');
    if ($param == null || $pid == null)
        return document_act_listview("存在しないデータを移動しようとしました");
    $table = contents_info_limit_by_down_line($pid, $param, 0);
    if (count($table) == 1)
        return document_act_listview();
    $id1 = $table[0]['id'];
    $sort1 = $table[0]['sort'];
    $id2 = $table[1]['id'];
    $sort2 = $table[1]['sort'];
    contents_info_update(array('id' => $id1, 'sort' => $sort2));
    contents_info_update(array('id' => $id2, 'sort' => $sort1));
    return document_act_listview();
    //#end
}

/** @表示を切り替える */
function document_act_ch_bool() {
    //#edit,func_ch_bool
    $param = webparam('param');
    $value = webparam('id' . $param) ? 1 : 0;
    if ($param === null)
        return document_act_listview("存在しないデータを変更しようとしました");
    contents_info_update(array('id' => $param, 'visible' => $value));
    return document_act_listview();
    //#end
}

/** @データを消去する */
function document_act_del_line() {
    //#edit,func_del_line
    $param = webparam('param');
    if ($param == null) {
        return document_act_listview("存在しないデータを削除しようとしました");
    }
    contents_info_delete($param);

    file_contents_delete_parent($param);

    return document_act_listview();
    //#end
}

/** データを追加する */
function document_act_setup() {
    //#edit,func_setup

    document_db_setup();
    //#end
}

//===================================================
//  フレームワーク機能

function document_act() {
    $cmd = webparam('cmd');
    //#edit,act
    $pid = webparam('pid');
    if ($pid != null) {
        if (!webauth('document' . $pid))
            return new plugin_result(html_errtag("アクセス権限がありません"));
    }
    else {
        $pid = 1;
        while (!webauth('document' . $pid)) {
            $pid++;
            if ($pid == 100) {return new plugin_result(html_errtag("アクセス権限がありません"));            }
        }
        $_POST['pid'] = $pid;
    }

    //ここだけあればよさそうな感じ★★★
    if ($cmd == null
        )$cmd = 'listview';
    //#end
    switch ($cmd) {
        case "delfile" :
            //#edit,act_delfile,ファイルを削除する

			$fid = webparam('param');
			$extension = pathinfo($fid, PATHINFO_EXTENSION);
			if ($extension == 'doc' || $extension == 'xls' || $extension == 'pdf'){
				contents_pdf_delete_filepath($fid);
				$ciid = document_save();
				$info = document_load($ciid);
				return new plugin_result(document_value($info));
	            break;
			}

            return new plugin_result(document_act_delfile());
            //#end
            break;
        case "chtype" :
            //#edit,act_chtype,表示スタイルを変更する
            return new plugin_result(document_act_chtype());
            //#end
            break;
        case "newitem" :
            //#edit,act_newitem,データを新規作成する
            return new plugin_result(document_act_newitem());
            //#end
            break;
        case "savelist" :
            //#edit,act_savelist,データを編集して、一覧に戻る。
            return new plugin_result(document_act_savelist());
            //#end
            break;
        case "save" :
            //#edit,act_save,データを保存する
            return new plugin_result(document_act_save());
            //#end
            break;
        case "detail" :
            //#edit,act_detail,データを編集する
            return new plugin_result(document_act_detail());
            //#end
            break;
        case "selall" :
            //#edit,act_selall,全選択する
            return new plugin_result(document_act_selall());
            //#end
            break;
        case "seloff" :
            //#edit,act_seloff,選択解除
            return new plugin_result(document_act_seloff());
            //#end
            break;
        case "listview" :
            //#edit,act_listview,データを表示する
            return new plugin_result(document_act_listview());
            //#end
            break;
        case "up_line" :
            //#edit,act_up_line,データを上に移動する
            return new plugin_result(document_act_up_line());
            //#end
            break;
        case "down_line" :
            //#edit,act_down_line,データを下に移動する
            return new plugin_result(document_act_down_line());
            //#end
            break;
        case "ch_bool" :
            //#edit,act_ch_bool,表示を切り替える
            return new plugin_result(document_act_ch_bool());
            //#end
            break;
        case "del_line" :
            //#edit,act_del_line,データを消去する
            return new plugin_result(document_act_del_line());
            //#end
            break;
        case "setup" :
            //#edit,act_setup,データを追加する
            return new plugin_result(document_act_setup());
            //#end
            break;
    }
}

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function document_tbl_setup($force = false) {

    if ($force) {
        container_info_reset_table();
        contents_detail_reset_table();
        contents_info_reset_table();
        file_contents_reset_table();
        contents_pdf_reset_table();
    }
    container_info_create_table();
    contents_detail_create_table();
    contents_info_create_table();
    file_contents_create_table();
    contents_pdf_create_table();
}

/* このプラグインで割り当てられる権限一覧 */

function document_auth() {
    //#edit,auth
    if (!container_info_exist_table()) {
        document_db_setup();
    }
    //#end
    //$sql = container_info_sql();
    $info = container_info_select_all();
    $tret = array();

    $tret[] = array("key" => "document_conf", "name" => "テンプレート設定", "info" => "ブログのテンプレートの設定が可能になります。");
    $tret[] = array("key" => "category", "name" => "カテゴリー設定", "info" => "ブログのカテゴリーの設定が可能になります。");
    $tline = "ブログ管理の{title}が編集が可能になります。";
    foreach ($info as $line) {
        $name = $line["title"];

        array_push($tret,
                array('key' => 'document' . $line['id']
                    , 'name' => $name
                    , 'info' => rep($tline, 'title', $name)));
    }
    if (count($tret) > 0)
        $hash["ブログ管理"] = $tret;


    return $hash;

}

/* 表示するメニュー設定 */

function document_menu($line, $admin, $title, $title_end = null) {
    //#edit,menu
    if (!container_info_exist_table()) {
        document_db_setup();
    }
    //#end
    //$sql = container_info_sql();
    $info = container_info_select_all();
    $top = rep($title, 'title', "コンテンツ");

    foreach ($info as $row) {
        if (!webauth('document' . $row['id']))
            continue;
        $item = rep($line, 'typex', "document&pid=" . $row['id']);
        $ret .= rep($item, 'title', $row['title']);
    }

    $ret = $ret ? $top . $ret : "";
    if(!webauth('document_conf')) return $ret;
	$one = rep($line,'typex',"document_conf");
	$ret .= rep($one,'title',"テンプレート設定");

    if(!webauth('category')) return $ret;
	$one = rep($line,'typex',"category");
	$ret .= rep($one,'title',"カテゴリー設定");
	
	$ret .= $title_end;

    return $ret;
}

/* 権限があるかどうか、引数を指定しない場合、アクセスできるIDを返す */

function document_first($pid = null) {
    if ($pid != null) {
        if (webauth('document' . $pid))
            return $pid;
        return false;
    }
    //#edit,first
    if (!container_info_exist_table()) {
        document_db_setup();
    }
    //#end
    $info = container_info_select_all();

    foreach ($info as $row) {
        if (webauth('document' . $row['id']))
            return $row['id'];
    }
    return false;
}

/** DBのセットアップを行う */
function document_db_setup() {
    if (!container_info_exist_table()) {
        container_info_create_table();
        $code = 'container_info_insert_code';
        if (function_exists($code))
            $code();
    }
    if (!contents_detail_exist_table()) {
        contents_detail_create_table();
        $code = 'contents_detail_insert_code';
        if (function_exists($code))
            $code();
    }
    if (!contents_info_exist_table()) {
        contents_info_create_table();
        $code = 'contents_info_insert_code';
        if (function_exists($code))
            $code();
    }
    if (!file_contents_exist_table()) {
        file_contents_create_table();
        $code = 'file_contents_insert_code';
        if (function_exists($code))
            $code();
    }
}
?>
