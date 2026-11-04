<?php
/**
 * テンプレートデータを読込む
 * @param int $viewid
 * @return hash
 */
function read_gallery_thumnail($viewid){
    $confpath = confpath('gallery/config.ini');
    $info = parse_ini_file($confpath,true);

    $iframepath = $info[$viewid]['frame'];
    #設定データがない場合
    if($iframepath == null){
        die('設定ファイル内のデータがありません<br>' .
            "[$viewid]セクション\nframe = ファイル名 <br>");
    }

    $viewid = intval($viewid);
    $cache = userpath("gallery_detail/cache/frame_{$viewid}.cache");
    $input = confpath("gallery/{$iframepath}");
    $block = array('line','list_top','list_normal','list_end','popup');
    return cache_TempLoader($input, $cache, $block);
}

/**
 * テンプレートデータを読込む
 * @param int $viewid
 * @return hash
 */
function read_gallery_popup($viewid){
    $confpath = confpath('gallery/config.ini');
    $info = parse_ini_file($confpath,true);
    $path = $info[$viewid]['popup'];

    #設定データがない場合
    if($path == null){
        die('設定ファイル内のデータがありません<br>' .
            "[$viewid]セクション\npopup = ファイル名 <br>");
    }

    $viewid = intval($viewid);
    $cache = userpath("gallery_detail/cache/popup_{$viewid}.cache");
    $input = confpath("gallery/{$path}");
    $block = array('linkblock');
    return cache_TempLoader($input, $cache, $block);
}

##==============================================================================
#  ポップアップ作成

/**
 *
 * @param table $all
 * @param user_gallery $gallery
 * @param hash $data
 * @param array $groups
 */
function create_popup($all,$gallery,$data,$groups){

    #イメージ情報を取得する img_link,img_width,img_height
    $path = $gallery->createFitSizePopup($data);
    $data['popup_img_link'] = $path;
    $data['detail_id'] = $data['id'];

    #イメージの画像サイズ
    list($width,$height) = getimagesize($path);
    $data['popup_img_width'] = $width;
    $data['popup_img_height'] = $height;

    #テキストタイトル
    $data['title_text'] = trim(strip_tags($data['title']));

    #action情報を作成する
    list($data['backid'],$data['nextid']) = getBackNextID($all, $data['id']);

    $data += $gallery->viewinfo;
    $data['gid'] = $gallery->viewinfo['id'];

    #出力---------------------------------------------------
    #テンプレートの読込み
    $temp = read_gallery_popup($gallery->viewinfo['id']);
    $page = $temp['page'];

    #ページングデータ
    if($data['backid'] != null && $data['nextid'] != null){
        $linkblock = $temp['linkblock'];
        $linkblock = phash($data,$linkblock);

        #foreachでデータの連結
        $all = $gallery->createFitSizeThumbnail($all);

        #次のウインドウのサイズの取得・設定
        list($next_width,$next_height)
            = calcPopupSize($gallery, $all[$data['nextid']]['popup_path']);
        $data += array('popup_next_width'=>$next_width,'popup_next_height'=>$next_height);

        #前のウインドウのサイズの取得・設定
        list($back_width,$back_height)
            = calcPopupSize($gallery, $all[$data['backid']]['popup_path']);
        $data += array('popup_back_width'=>$back_width,'popup_back_height'=>$back_height);
    }
    else{
        $linkblock = null;
    }


    $page = rep($page,'linkblock',$linkblock);
    $page = rep($page,'gfix',join(',',$groups));


    #イメージ用のデータ
    return phash($data, $page);
}

/**
 * イメージからみた前後のデータのIDを取得する
 * @param table $all
 * @param int $id
 */
function getBackNextID($all,$id){
    $fit = 0;

    for($i=0;$i<count($all);$i++){
        if($all[$i]['id'] == $id){
            $fit = $i;
            break;
        }
    }

    $backpos = ($fit > 0) ? $fit - 1 : count($all) -1;
    $nextpos = $fit + 1 < count($all) ? $fit + 1 : 0;

    $backid = $all[$backpos]['id'];
    $nextid = $all[$nextpos]['id'];

    if($id == $backid || $id == $nextid) return array(null,null);
    return array($backid,$nextid);
}


/**
 * ギャラリー一覧のID一覧を作成する
 * @param table $all
 */
function create_popup_state($all){
    foreach((array)$all as $row){
        $ret['gallery_list'][] = $row['id'];
    }
    return html_hidden($ret);
}



##==============================================================================
#  サムネイル作成

/**
 * サムネイルを作成する
 * @param string $finaldate
 * @param table $viewitem
 * @param user_gallery $gallery
 * @param array $groups 表示するグループの一覧
 * @return string
 */
function create_thumbnail($finaldate,$viewitem,$gallery,$groups){

    #今日の日データの作成
    $item_today = array_prefix(getdate_week(),'today_');

    #最終更新日のデータ作成
    $item_final = array_prefix(getdate_week($finaldate),'final_');

    #表示位置の情報の作成
    $item_view = array_prefix(array(
        'id' => $gallery->viewinfo['id'],
        'title' => $gallery->viewinfo['title'],
        'comment' => $gallery->viewinfo['comment'],
        'title_text' => trim(strip_tags($gallery->viewinfo['title']))
    ),'view_');

    #サムネイルの幅情報設定
    $item_thumb = array_prefix(array(
        'width' => $gallery->viewinfo['thumbnail_width'],
        'height' => $gallery->viewinfo['thumbnail_height']
    ),'thumb_');

    #foreachでデータの連結
    $viewlist = $gallery->createFitSizeThumbnail($viewitem);

    foreach((array)$viewlist as $item){
        $minipath = $item['mini_path'];
        list($swidth,$sheight) = getimagesize($minipath);
        $imgone = array_prefix(array(
            'mini_path'=>$item['mini_path'],
            'big_path'=>$item['big_path'],
            'width'=>$swidth,
            'height'=>$sheight,
            'title'=>$item['title'],
            'comment1'=>$item['comment1'],
            'comment2'=>$item['comment2'],
            'link'=>$item['linkurl'],
            'nontag_title'=> trim(strip_tags($item['title']))
        ), 'img_');

        $poppath = $item['popup_path'];

        list($pwidth,$pheight) = calcPopupSize($gallery, $poppath);
        $popone = array_prefix(array(
            'id' => $item['id'],
            'width'=>$pwidth,
            'height'=>$pheight,
            'path'=>$poppath,
            'url'=>USER_GALLERY
                . '?g=' . $gallery->viewinfo['id']
                . '&id=' . $item['id']
        ), 'popup_');

        $retlist[] = array_merge($imgone,$popone);
    }

    $ret = $item_final + $item_thumb + $item_today + $item_view;
    $ret['item'] = $retlist;
    $ret['gfix'] = join(',', $groups);
    return $ret;
}

/**
 * ポップアップウインドウのサイズを計算する
 * @param user_gallery $gallery
 * @param string $imagepath
 * @return array width,height
 */
function calcPopupSize($gallery,$imagepath){
    if($gallery->viewinfo['pop_type'] == VIEWDETAIL_POP_TYPE_FIX){
        return array($gallery->viewinfo['pop_width'],$gallery->viewinfo['pop_height']);
    }
    else{
        list($bwidth,$bheight) = getimagesize($imagepath);
        return array(
            $bwidth + $gallery->viewinfo['margin_left'] + $gallery->viewinfo['margin_right'],
            $bheight + $gallery->viewinfo['margin_top'] + $gallery->viewinfo['margin_bottom']);
    }
}


/**
 * サムネイルの一覧を作成する
 * @param table $data
 * @param user_gallery $gallery
 * @param array $group
 */
function write_thumbnail($data,$gallery,$group){
    #テンプレートの読込み
    $temp = read_gallery_thumnail($gallery->viewinfo['id']);

    #テンプレートの微調整
    if($temp['list_top'] == null)  $temp['list_top'] = $temp['list_normal'];
    if($temp['list_end'] == null)  $temp['list_end'] = $temp['list_normal'];

    #データの微調整
    $i = 0;
    $col = $gallery->viewinfo['column_count'];
    $row = $gallery->viewinfo['row_count'];
    $items = $data['item'];unset($data['item']);

    #行・列の出力
    for($r = 0; $r <  $row ; $r++){
        #-----------------------------------------------------------------------
        $list = '';
        $c = 0;
        for($c; $c < 1 ; $c++ ){
            if($items[$i]){
                $list .= phash($items[$i++],$temp['list_top']);
            }
        }
        for($c; $c < $col - 1  ; $c++){
            if($items[$i]){
                $list .= phash($items[$i++],$temp['list_normal']);
            }
        }
        for($c; $c < $col ; $c++){
            if($items[$i]){
                $list .= phash($items[$i++],$temp['list_end']);
            }
        }
        if($list){
            $num = sprintf('%02d',$r+1);
            $ul .= phash(array('list'=>$list,'line_class'=>'method' . $num) ,$temp['line']);
        }
    }

    $page = rep($temp['page'],'line',$ul);
    $request_url = str_replace("sp/", "", $_SERVER['REQUEST_URI']);
    $page = rep($page, 'href', '..' . $request_url);
    $page = phash($group,$page);
    return phash($data,$page);
}

?>
