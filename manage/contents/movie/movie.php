<?php

#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History: 2010/03/26 プレビュー時のイメージを削除できるように修正
#==================================
require_once(dirname(__FILE__) . '/sql/moviebox_info.php');
require_once(dirname(__FILE__) . '/sql/movie_detail.php');
require_once(dirname(__FILE__) . '/sql/movie_info.php');
require_once(dirname(__FILE__) . '/../category/sql/category_info.php');
require_once(dirname(__FILE__) . '/../category/sql/category_master.php');


//===================================================
//  ユーザー機能
//#edit
//★★★テーブルが再帰的に可能・イメージモデル・イメージにはアクションが必要
//AIRでは関係ない

/** データのテキスト置換を行う */
function moviepage_view($data, $text) {
    $page = $text['page'];
    foreach ($data as $key => $val) {
        if ($val['type'] == select)
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
            foreach ($val['val'] as $vval) { $item .= moviepage_view($vval, $vtext);            }
            $page = rep($page, $key, $item);
        }
        if ($val['type'] == 'table') {
            $vtext['page'] = $val['text']; $item = $val['label'];
            foreach ($val['val'] as $vval) { $item .= moviepage_view($vval, $vtext);            }
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
function movie_value($info) {
    $list = movie_list();

    //テンプレート
    $text['page'] = file_get_contents(userpath("movie/html/input.html"));

    //共通値 //コマンドでいける
    $db = moviebox_info_find($list['pid']);         //グループタイトル
    $conf = movie_couf_ini_text_all(confpath("movie/config.ini")); //テキストテンプレート
    foreach ($conf as $cone) { $ltemp[$cone['no']] = $cone['label'];    }

    $size = movie_couf_ini_image(confpath("movie/config.ini"));
    $size = $size[webparam('pid')];

    //追加部分・テンプレート
    $info['content_temp_id'] = $info['content_temp_id'] ? $info['content_temp_id'] : 1;
    $viewtext['page'] = file_get_contents(userpath("movie/html/type" . $info['content_temp_id'] . ".html"));

    //詳細部分の作成 //出来れば避けたい→階層変換モデル //上手にやった動的にできるかも
    $view['viewtype_name'] = array('type' => 'text', 'val' => $ltemp[$info['content_temp_id']]);
    for ($i = 1; $i <= 10; $i++) {
        $item = $info['item'][$i];
        $cid = 'cid' . $i . '_' . $item['data_data_type'];
        foreach ($item as $ione) {
            $cid = 'cid' . $i . '_' . $ione['data_data_type'];
            $view[$cid] = array('type' => 'text', 'val' => $ione['id']);
            switch ($ione['data_data_type']) {
                case 1: {
							$view['text' . $i] = array('type' => 'text', 'hidden' => true, 'val' => $ione['value'],);
							
							if( sbfunc_YouTubeTagCheck($ione['value']) > 0 ){
								//動画タグの場合はURLを抽出
								$url = sbfunc_GetYouTubeWatch($ione['value']);
							}
							$view['text' . $i . 'url'] = array('type' => 'text', 'hidden' => true, 'val' => $url,);
						
						}break;
                case 2: { //ここでデータ化
                        if ($ione['value']) {
                            $dfile = file_movie_find($ione['value']);
                            //dp($dfile[id] . ":" . $ione[value]);
                            $dir = "../";
                            $access = "movie.php?cmd=img&param=" . $ione['value'] . "&pid={pid}";
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

	$pitem = $info['item'][pdf];
	if(!empty($pitem)){
		foreach ($pitem as $ione) {
			$cid = 'cid' . $i . '_' . $ione['data_data_type'];
			$view[$cid] = array('type' => 'text', 'val' => $ione['id']);
			switch ($ione['data_data_type']) {
				case 1: { $view['descpdf'] = array('type' => 'text', 'hidden' => true, 'val' => $ione['value'],);
					}break;
				case 2: { //ここでデータ化
					$dir = "movie/movie/pdf/".$ione['value']; $access = ""; $fname = $ione['value'];
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
    $lyear = numhash(2007, 2020); $lmonth = numhash(1, 12);
    $lday = numhash(1, 31); $lyesno = array(0, 1);

    //★★データ変換 //加工すれば、自動変換可能　★★★どっちつかずのデータ
    $date = $info['view_date']; if ($date == null)
        $date = time();
    $v_year = date("Y", $date); $v_month = date("n", $date); $v_day = date("j", $date);

    //表示データ作成　//完全モデル化可能
    $data['viewtype_input'] = array('type' => 'text', 'save' => false, 'val' => moviepage_view($view, $viewtext));
    $data['pid'] = array('type' => 'text', 'val' => $list['pid'], 'hidden' => true);
    $data['typex'] = array('type' => 'text', 'val' => $list['typex'], 'hidden' => true);
    $data['count'] = array('type' => 'text', 'val' => $list['count'], 'hidden' => true);
    $data['pageno'] = array('type' => 'text', 'val' => $list['pageno'], 'hidden' => true);
    $data['group'] = array('type' => 'text', 'val' => $db['title']);
    $data['err'] = array('type' => 'text', 'val' => $info['err']);
    $data['id'] = array('type' => 'text', 'save' => false, 'val' => $info['id']);
    $data['title'] = array('type' => 'text', 'save' => true, 'val' => $info['title']);
//	$data['viewtype'] = array('type' => 'select', 'save' => true, 'list' => $ltemp, 'val' => $info['content_temp_id']);
//	$data['view_year'] = array('type' => 'select', 'save' => true, 'list' => $lyear, 'val' => $v_year);
//	$data['view_month'] = array('type' => 'select', 'save' => true, 'list' => $lmonth, 'val' => $v_month);
//	$data['view_day'] = array('type' => 'select', 'save' => true, 'list' => $lday, 'val' => $v_day);
	$data['viewtype'] = array('hidden' => true, 'save' => true, 'val' => $info['content_temp_id']);
    $data['view_year'] = array('hidden' => true, 'save' => true, 'val' => $v_year);
    $data['view_month'] = array('hidden' => true, 'save' => true, 'val' => $v_month);
    $data['view_day'] = array('hidden' => true, 'save' => true, 'val' => $v_day);
//	$data['visible'] = array('type' => 'check', 'save' => true, 'list' => $lyesno, 'val' => $info['visible']);
	$data['visible'] = array('hidden' => true, 'save' => true, 'val' => '1');
    $data['summary'] = array('type' => 'text', 'save' => true, 'val' => $info['summary']);

    $page = moviepage_view($data, $text);
	
    return $page;
}

//==============================================================================
// file_movie_dbを使用する


//==============================================================================

/** フォームの値をDB形式に変換します */
function movie_form($preview = false, $errsave = null) {

    //formのvalueと整合性が取れている必要がある
    $hash = array('id' => null, 'title' => null, 'viewtype' => null, 'view_year' => null
        , 'view_month' => null, 'view_day' => null, 'visible' => null, 'summary' => null, 'group_id' => webparam('pid'),category_id=>null);

    //関数化可能
    foreach ($hash as $key => $val) {
        $ret[$key] = webparam($key, $val);
    }

    $size = movie_couf_ini_image(confpath("movie/config.ini"));
    $size = $size[webparam('pid')];

    $ret['content_temp_id'] = $ret['viewtype'];
    $ret['summary'] = str_replace('\\"', '"', $ret['summary']);
    $ret['view_date'] = mktime(0, 0, 0, $ret['view_month'], $ret['view_day'], $ret['view_year']);
    $ret['item'] = array();

    $cacheimglist = array();
    for ($i = 1; $i <= 10; $i++) {
        $item = array();

        $text = str_replace('\\"', '"', webparam('text' . $i));
        if(trim($text) == '<br />' || trim($text) == '&nbsp;') $text = null;

        #テキストデータ
        $item[1] =
                array('movie_info_id' => webparam('id'), 'data_data_type' => 1, 'value_id' => 1
                    , 'id' => webparam('cid' . $i . '_1'), 'temp_id' => $i, 'value' => $text);

        #イメージデータ
        $item[2] =
                array('movie_info_id' => webparam('id'), 'data_data_type' => 2, 'value_id' => 2
                    , id => webparam('cid' . $i . '_2'), 'temp_id' => $i, 'value' => $imgid);

        #位置データ
        $item[3] =
                array('movie_info_id' => webparam('id'), 'data_data_type' => 3, 'value_id' => 3
                    , 'id' => webparam('cid' . $i . '_3'), 'temp_id' => $i, 'value' => webparam('img' . $i . '_pos'));



        $ret['item'][$i] = $item;
    }

    //dp($ret);
    return $ret;
}

function movie_check_err() {
    $p = $_POST;
    $mes = $p['title'] == null ? "タイトルを入力してください。<br />" : "";
    $summary = str_replace( array( " ", "　", "<br/>","\r\n", "&nbsp;" ), "", $p['summary'] );
/*
    $text1 = str_replace( array( " ", "　", "&nbsp;","\r\n", "<br/>" ), "", $p['text1'] );
    if ( empty( $summary ) && empty( $text1 ) ) {
        $mes .= "少なくとも「コンテンツ」または「文章1」のどちらかを入力してください。<br />";
    }
*/
	$movie_count = 0;
    for ($i = 1; $i<=10; $i++) {
		if( $i % 2 == 0 && !empty($p['text'.$i]) ){
			
			if( $res = sbfunc_YouTubeTagCheck($p['text'.$i]) < 0 ){
				$mes .= "動画".( $i / 2 )."の動画タグが不正です。（".$res."）<br />";
			}
			
			$movie_count ++;
		}
    }
	if( $movie_count == 0 ){
		$mes .= "動画タグを入力してください。<br />";
	}		
	

    if ( !empty( $mes ) ) {
        $mes = "<font color=\"red\">".$mes."</font><br />";
    }

    return $mes;
}



/** データベースへの書き込み */
function movie_save() {

    $form = movie_form();

    if (!$form['id']) {
        $id = movie_info_insert($form);
        $form['sort'] = $id;
        $form['id'] = $id;
        movie_info_update($form);
    }//データの追加
    else {
        $old_data = movie_load($form['id']);
        movie_info_update($form); //データの更新
        $id = $form['id'];
    }

    //データを取り出す
    $itemlist = $form['item'];


    for ($i = 1; $i<= 10; $i++) {
        for ($n = 1; $n <= count($itemlist[$i]); $n++) {
            $ione = $itemlist[$i][$n];

            #IDがあれば更新
            if ($ione['movie_info_id']) { //更新
                #echo 'here';
                movie_detail_update($ione);
            } else { //追加
                #echo 'news';
                $ione['movie_info_id'] = $id;
                movie_detail_insert($ione);
            }
        }
    }

    return $id;
}

/** データベースからデータの読み込み */
function movie_load($id) {
    $info = movie_info_find($id);
    $items[0] = array('movie_info_id' => $id, 'data_data_type' => 1, 'value_id' => 1, 'id' => "", 'temp_id' => 0, 'value' => "");
    #デフォルト値の作成
    for ($i = 1; $i <= 10; $i++) {
        $item = array();
        $item[1] = array('movie_info_id' => $id, 'data_data_type' => 1, 'value_id' => 1, 'id' => webparam('cid' . $i . '_1'), 'temp_id' => $i, 'value' => "");
        $item[2] = array('movie_info_id' => $id, 'data_data_type' => 2, 'value_id' => 2, 'id' => webparam('cid' . $i . '_2'), 'temp_id' => $i, 'value' => "");
        $item[3] = array('movie_info_id' => $id, 'data_data_type' => 3, 'value_id' => 3, 'id' => webparam('cid' . $i . '_3'), 'temp_id' => $i, 'value' => 0);
        $items[$i] = $item;
    }

    $itemsl = movie_detail_select_movie_id($id); //★★
    $itemret = array();
    foreach ($itemsl as $ione) {
        $group = $ione['temp_id'];
        $itemret[$group][$ione['value_id']] = $ione;
    }
    $info['item'] = count($itemret) ? $itemret : $items;

    return $info;
}

/** リストの値をDB形式に変換します */
function movie_list() {
    $hash = array('pid' => 1, 'typex' => 'movie', 'count' => 20, 'pageno' => 1);
    foreach ($hash as $key => $val) {
        $ret[$key] = webparam($key, $val);
    }
    return $ret;
}

/** 表示部分?外部環境に依存しないようにする */
function movie_tolist($data=array(), $rule = array(), $text=array()) {

    //値の準備====================================================
    //ルール部分
    $rule = whash(movie_list(), $rule);

    //テンプレート部分--------------------------------------------
    $temp = new tLoader(userpath("movie/html/list.html"));
    $tbase = $temp->getData();
    $text = whash($tbase, $text);

    //データ部分---------------------------------------------------
    $err = ifrep($errview, 'value', $data['err']);

    //使用変数
    $pid = $rule['pid'];$pageno = $rule['pageno'];$count = $rule['count'];

    //項目名
    $item = moviebox_info_find($pid);
    $table = movie_info_limit_by_group_id($pid, ($pageno - 1) * $count, $count);
    $conf = movie_couf_ini_text_all(confpath("movie/config.ini")); //外観

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
    $tcount = movie_info_count_group_id($pid);
    $tcount = $tcount . "件";
    $count_st = array(10 => "10件", 20 => "20件", 50 => "50件", 100 => "100件");
    $paging = html_paging($tcount, $rule['count'], $rule['pageno'], $text['paging']);

    //ルール部分==================================================
    $out['pid'] = array('type' => 'text', 'val' => $rule['pid'], 'hidden' => true);
    $out['typex'] = array('type' => 'text', 'val' => $rule['typex'], 'hidden' => true);
    $out['count'] = array('type' => 'select', 'val' => $rule['count'], 'list' => $count_st);
    $out['pageno'] = array('type' => 'text', 'val' => $rule['pageno'], 'hidden' => true);

    //表示部分====================================================
    $out['tcount'] = array('type' => 'text', 'val' => $tcount);   //DB呼び出し？
    $out['err'] = array('type' => 'text', 'val' => $err);   //実行タイミング？
    $out['title'] = array('type' => 'text', 'val' => $item['title']);  //作成タイミング？
    $out['name'] = array('type' => 'text', 'val' => "ブログ");  //作成タイミング？
    $out['paging'] = array('type' => 'text', 'val' => $paging);

    //リスト部分==================================================
    $out['table'] = array('type' => 'text', 'val' => $ttable);    //てーぶる部分
    return moviepage_view($out, $text);
}

//#end
//===================================================
//  定義機能

/** ファイルを削除する */
function movie_act_delfile() {
    //#edit,func_delfile
    $info = movie_form();
    $fid = webparam('param');

    foreach ($info['item'] as $int => $val) {
        if ($val[2]['value'] == $fid) {
            $info['item'][$int][2]['value'] = null;
        }
    }
    return movie_value($info);
    //#end
}

/** 表示スタイルを変更する */
function movie_act_chtype() {
    //#edit,func_chtype
    $info = movie_form();
    return movie_value($info);
    //#end
}

/** データを新規作成する */
function movie_act_newitem() {
    //#edit,func_newitem
    return movie_act_detail();
    //#end
}

/** データを編集して、一覧に戻る。 */
function movie_act_savelist() {
    //#edit,func_savelist
    $err = movie_check_err();
    if ( empty( $err ) ) {
        movie_save();
        return movie_act_listview();
    } else {
        $info = movie_form(false, 'act_savelist');
        $info['err'] = $err;
        return movie_value($info);
    }
    //#end
}

/** データを保存する */
function movie_act_save() {
    //#edit,func_save

    $err = movie_check_err();

    if ( empty( $err ) ) {
        $id = movie_save();
        $info = movie_load($id);//★★★
    } else {
        $info = movie_form(false, 'act_save');
        $info['err'] = $err;
    }

    return movie_value($info);
    //return movie_value(movie_form());
    //#end
}

/** データを編集する */
function movie_act_detail() {
    //#edit,func_detail
	$pid = webparam('pid');
	$mid = webparam('param');
	if( empty($mid) ){
		$default = movie_info_limit_by_group_id($pid, 0, 1);
		$mid = $default[0]["id"];
	}
    $info = movie_load($mid);//★★★
    return movie_value($info);
    //#end
}

/** 全選択する */
function movie_act_selall() {
    //#edit,func_selall
    $data = array('sel' => 'all');
    return movie_tolist($data);
    //#end
}

/** 選択解除 */
function movie_act_seloff() {
    //#edit,func_seloff
    $data = array('sel' => 'off');
    return movie_tolist($data);
    //#end
}

/** @データを表示する */
function movie_act_listview($err=null) {
    //#edit,func_listview
    return movie_tolist();
    //#end
}

/** @データを上に移動する */
function movie_act_up_line() {
    //#edit,func_up_line
    $param = webparam('param');
    $pid = webparam('pid');
    if ($param == null || $pid == null)
        return movie_act_listview("存在しないデータを移動しようとしました");
    $table = movie_info_limit_by_up_line($pid, $param, 0);
    if (count($table) == 1)
        return movie_act_listview();
    $id1 = $table[0]['id'];
    $sort1 = $table[0]['sort'];
    $id2 = $table[1]['id'];
    $sort2 = $table[1]['sort'];
    movie_info_update(array('id' => $id1, 'sort' => $sort2));
    movie_info_update(array('id' => $id2, 'sort' => $sort1));
    return movie_act_listview();
    //#end
}

/** @データを下に移動する */
function movie_act_down_line() {
    //#edit,func_down_line
    $param = webparam('param');
    $pid = webparam('pid');
    if ($param == null || $pid == null)
        return movie_act_listview("存在しないデータを移動しようとしました");
    $table = movie_info_limit_by_down_line($pid, $param, 0);
    if (count($table) == 1)
        return movie_act_listview();
    $id1 = $table[0]['id'];
    $sort1 = $table[0]['sort'];
    $id2 = $table[1]['id'];
    $sort2 = $table[1]['sort'];
    movie_info_update(array('id' => $id1, 'sort' => $sort2));
    movie_info_update(array('id' => $id2, 'sort' => $sort1));
    return movie_act_listview();
    //#end
}

/** @表示を切り替える */
function movie_act_ch_bool() {
    //#edit,func_ch_bool
    $param = webparam('param');
    $value = webparam('id' . $param) ? 1 : 0;
    if ($param === null)
        return movie_act_listview("存在しないデータを変更しようとしました");
    movie_info_update(array('id' => $param, 'visible' => $value));
    return movie_act_listview();
    //#end
}

/** @データを消去する */
function movie_act_del_line() {
    //#edit,func_del_line
    $param = webparam('param');
    if ($param == null) {
        return movie_act_listview("存在しないデータを削除しようとしました");
    }
    movie_info_delete($param);

    file_movie_delete_parent($param);

    return movie_act_listview();
    //#end
}

/** データを追加する */
function movie_act_setup() {
    //#edit,func_setup

    movie_db_setup();
    //#end
}

//===================================================
//  フレームワーク機能

function movie_act() {
    $cmd = webparam('cmd');
    //#edit,act
    $pid = webparam('pid');
    if ($pid != null) {
        if (!webauth('movie' . $pid))
            return new plugin_result(html_errtag("アクセス権限がありません"));
    }
    else {
        $pid = DEFAULT_ID_MOVIE;
        while (!webauth('movie' . $pid)) {
            $pid++;
            if ($pid == 100) {return new plugin_result(html_errtag("アクセス権限がありません"));            }
        }
        $_POST['pid'] = $pid;
    }

    //ここだけあればよさそうな感じ★★★
    if ($cmd == null
        )$cmd = 'detail';
    //#end
    switch ($cmd) {
        case "delfile" :
            //#edit,act_delfile,ファイルを削除する

			$fid = webparam('param');
			$extension = pathinfo($fid, PATHINFO_EXTENSION);
			if ($extension == 'doc' || $extension == 'xls' || $extension == 'pdf'){
				movie_pdf_delete_filepath($fid);
				$ciid = movie_save();
				$info = movie_load($ciid);
				return new plugin_result(movie_value($info));
	            break;
			}

            return new plugin_result(movie_act_delfile());
            //#end
            break;
        case "chtype" :
            //#edit,act_chtype,表示スタイルを変更する
            return new plugin_result(movie_act_chtype());
            //#end
            break;
        case "newitem" :
            //#edit,act_newitem,データを新規作成する
            return new plugin_result(movie_act_newitem());
            //#end
            break;
        case "savelist" :
            //#edit,act_savelist,データを編集して、一覧に戻る。
            return new plugin_result(movie_act_savelist());
            //#end
            break;
        case "save" :
            //#edit,act_save,データを保存する
            return new plugin_result(movie_act_save());
            //#end
            break;
        case "detail" :
            //#edit,act_detail,データを編集する
            return new plugin_result(movie_act_detail());
            //#end
            break;
        case "selall" :
            //#edit,act_selall,全選択する
            return new plugin_result(movie_act_selall());
            //#end
            break;
        case "seloff" :
            //#edit,act_seloff,選択解除
            return new plugin_result(movie_act_seloff());
            //#end
            break;
        case "listview" :
            //#edit,act_listview,データを表示する
            return new plugin_result(movie_act_listview());
            //#end
            break;
        case "up_line" :
            //#edit,act_up_line,データを上に移動する
            return new plugin_result(movie_act_up_line());
            //#end
            break;
        case "down_line" :
            //#edit,act_down_line,データを下に移動する
            return new plugin_result(movie_act_down_line());
            //#end
            break;
        case "ch_bool" :
            //#edit,act_ch_bool,表示を切り替える
            return new plugin_result(movie_act_ch_bool());
            //#end
            break;
        case "del_line" :
            //#edit,act_del_line,データを消去する
            return new plugin_result(movie_act_del_line());
            //#end
            break;
        case "setup" :
            //#edit,act_setup,データを追加する
            return new plugin_result(movie_act_setup());
            //#end
            break;
    }
}

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function movie_tbl_setup($force = false) {

    if ($force) {
        moviebox_info_reset_table();
        movie_detail_reset_table();
        movie_info_reset_table();
//      file_movie_reset_table();
//      movie_pdf_reset_table();
    }
    moviebox_info_create_table();
    movie_detail_create_table();
    movie_info_create_table();
//  file_movie_create_table();
//  movie_pdf_create_table();
}

/* このプラグインで割り当てられる権限一覧 */

function movie_auth() {
    //#edit,auth
    if (!moviebox_info_exist_table()) {
        movie_db_setup();
    }
    //#end
    //$sql = moviebox_info_sql();
    $info = moviebox_info_select_all();
    $tret = array();

    $tret[] = array(key => "movie_conf", name => "テンプレート設定", info => "動画管理のテンプレートの設定が可能になります。");
//  $tret[] = array(key => "category", name => "カテゴリー設定", info => "動画管理のカテゴリーの設定が可能になります。");
    $tline = "動画管理の{title}が編集が可能になります。";
    foreach ($info as $line) {
        $name = $line["title"];

        array_push($tret,
                array('key' => 'movie' . $line['id']
                    , 'name' => $name
                    , 'info' => rep($tline, 'title', $name)));
    }
    if (count($tret) > 0)
        $hash["動画管理"] = $tret;


    return $hash;

}

/* 表示するメニュー設定 */

function movie_menu($line, $admin, $title) {
    //#edit,menu
    if (!moviebox_info_exist_table()) {
        movie_db_setup();
    }
    //#end
    //$sql = moviebox_info_sql();
    $info = moviebox_info_select_all();
    $top = rep($title, 'title', "動画管理");

    foreach ($info as $row) {
        if (!webauth('movie' . $row['id']))
            continue;
		
		$info = movie_info_select_by_user($row['id']);
		$movie = $info[0];
		
        $item = rep($line, 'typex', "movie&pid=" . $row['id'] . '&cmd=detail&param='.$movie["id"]);
        $ret .= rep($item, 'title', $row['title']);
    }

    $ret = $ret ? $top . $ret : "";
    if(!webauth('movie_conf')) return $ret;
	$one = rep($line,'typex',"movie_conf");
	$ret .= rep($one,'title',"テンプレート設定");

//  if(!webauth('category')) return $ret;
//	$one = rep($line,'typex',"category");
//	$ret .= rep($one,'title',"カテゴリー設定");

    return $ret;
}

/* 権限があるかどうか、引数を指定しない場合、アクセスできるIDを返す */

function movie_first($pid = null) {
    if ($pid != null) {
        if (webauth('movie' . $pid))
            return $pid;
        return false;
    }
    //#edit,first
    if (!moviebox_info_exist_table()) {
        movie_db_setup();
    }
    //#end
    $info = moviebox_info_select_all();

    foreach ($info as $row) {
        if (webauth('movie' . $row['id']))
            return $row['id'];
    }
    return false;
}

/** DBのセットアップを行う */
function movie_db_setup() {
    if (!moviebox_info_exist_table()) {
        moviebox_info_create_table();
        $code = 'moviebox_info_insert_code';
        if (function_exists($code))
            $code();
    }
    if (!movie_detail_exist_table()) {
        movie_detail_create_table();
        $code = 'movie_detail_insert_code';
        if (function_exists($code))
            $code();
    }
    if (!movie_info_exist_table()) {
        movie_info_create_table();
        $code = 'movie_info_insert_code';
        if (function_exists($code))
            $code();
    }
}
?>
