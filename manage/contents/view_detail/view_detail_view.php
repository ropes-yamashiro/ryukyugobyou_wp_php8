<?php
/**
 * 表示するHTMLを作成する
 * @param array $viewitem
 * @param array $state
 */
function view_detail_view($viewitem,$state = array()){

    #コンテンツ作成--------------------------------------------------------------
    #エラー部分の作成
    $html_err = html_errhash_br($state['err'],'errmes');

    #表示データ
    if($viewitem['id']){
        $row = view_detail::getDetail($viewitem['id']);
        $percount = intval($row['row_count']) * intval($row['column_count']);
        $pertext1 = "(表示するグループの一覧からランダムで{$percount}枚表示)";
        $pertext2 = "(表示するグループの一覧から{$percount} 枚ずつ表示)";
        $pertext3 = "(表示するグループの一覧の1～{$percount} 枚目を表示)";
    }

    #ステート情報の保存
    unset($state['group_edit_id']);
    $html_state = html_hidden($state);

    #データの置換---------------------------------------------------------------

    $html = userread('view_detail/input.html');
    $html = rep($html,'html_state',$html_state);
    $html = rep($html,'html_err',$html_err);
    $html = rep($html,'percount',$percount);
    $html = rep($html,'pertext1',$pertext1);
    $html = rep($html,'pertext2',$pertext2);
    $html = rep($html,'pertext3',$pertext3);

    $html = rep($html,'img_height',$viewitem['img_height']);
    $html = rep($html,'img_width',$viewitem['img_width']);
    $html = rep($html,'pop_height',$viewitem['pop_height']);
    $html = rep($html,'pop_width',$viewitem['pop_width']);
    $html = rep($html,'margin_top',$viewitem['margin_top']);
    $html = rep($html,'margin_right',$viewitem['margin_right']);
    $html = rep($html,'margin_bottom',$viewitem['margin_bottom']);
    $html = rep($html,'margin_left',$viewitem['margin_left']);

    $html = rep($html,'html_group',view_detail_group($viewitem['id']));
    $html = rep($html,'html_reload',view_detail_reload($viewitem['position']));
    $html = rep($html,'html_save',view_detail_save($viewitem['position']));
    $html = rep($html,'html_position',view_detail_position($viewitem['position']));
    $html = rep($html,'html_edit_position',view_detail_position_edit());
    $html = rep($html,'view_url',view_detail_view_url($viewitem['id']));
    
    $html = html_radio($html, 'group_view', array(0,1,2,3), $viewitem['group_view']);
    $html = html_radio($html, 'pop_type', array(0,1,2), $viewitem['pop_type']);
    $html = html_radio($html, 'image_view', array(0,1,2,3), $viewitem['image_view']);
    $html = html_radio($html, 'event_type', array(0,1,2), $viewitem['event_type']);
    $html = view_detail_view_auth($html);
    $html = ephash($viewitem, $html);
    return $html;
}

/**
 * 権限ありなしで詳細データの部分の表示切換えを行う
 * @param string $page
 * @return string 
 */
function view_detail_view_auth($page){

    if(webauth(VIEWDETAIL_AUTH_DETAIL)) return $page;
    $start = strpos($page,'<!-- #AUTH_START -->');
    $end = strpos($page,'<!-- #AUTH_END -->');
    $endsize = strlen('<!-- #AUTH_END -->');
    $remove = substr($page, $start,$end + $endsize - $start);
    $page = str_replace($remove, '', $page);
    return $page;
    
}

/**
 * リロードボタンを表示するか
 * @param string $position
 * @return string
 */
function view_detail_reload($position){
    return $position
        ? '<input type="button" value="再読込" onclick="return reload()" />'
        : null;
}

/**
 * 表示するグループ{html_group}
 */
function view_detail_group($id){
    #必要なクラスを読込む
    userload('view_detail_relation/view_detail_relation_model.php');

    #グループデータの情報を取り出す
    $tbl = view_detail_relation::getInfo($id);
    $html = '<label {class}>'
        .'<input type="checkbox" name="group_edit_id[]" value="{id}" {checked} class="method01" {tagid} {onclick} />{title}</label>';

    $first = true;
    $all = $tbl[GROUP_ID_ALLSELECT]['use'];
    
    foreach($tbl as $row){
    #for($i=0;$i < count($tbl); $i++){
        #$row = $tbl[$i];
        #選択用のチェックボックスを作成する
        $row['checked'] = $row['use'] ? 'checked="checked" ' : '';
        $row['checked'] = $all ? 'checked="checked" ' : $row['checked'];
        $row['tagid'] = $first ? 'id="group_all"' : '';
        $row['onclick'] = $first ? 'onclick="selectall();"' : '';
        $row['class'] = $first ? '' : 'class="grouplist"';
        $ret .= phash($row, $html);
        if($first){
            $first = false;
            $ret .= "<br />";
        }
    }
    return ($ret ? $ret : 'グループが登録されていません');
}

/**
 * 保存ボタン{html_save}
 * @param string $position
 * @return string
 */
function view_detail_save($position){
    if(webauth(VIEWDETAIL_AUTH_EDIT) && $position){
        return '<input type="button" value="保存" onclick="return save()" />';
    }
}

/**
 * 表示位置のリスト{html_position}
 */
function view_detail_position($position){

    #必要なクラスを読込む
    userload('view_edit/view_edit_model.php');

    #テンプレート
    $html = '<option value="{id}" {checked}>{title}</option>';

    #position=null ⇒表示位置なし
    $checked = $position == null ? "selected='selected'" : '';
    $ret = phash(array('id'=>0,'title'=>'▼選択してください','checked'=>$checked), $html);

    #データをとりだす
    $tbl = view_edit::getList();
    foreach($tbl as $row){
        $row['checked'] = $position == $row['id'] ? "selected='selected'" : '';
        $ret .= phash($row, $html);
    }
    return $ret;
}

/**
 * グループ表示ボタンを作成する {html_edit_position}
 * @return string
 */
function view_detail_group_edit(){
    if(webauth(GROUP_AUTH_EDIT)){
        return '<br /><a href="admin.php?typex=group_edit&cmd=inline" rel="facebox" >グループ編集</a>';
    }
}

/**
 * グループ表示ボタンを作成する {html_edit_position}
 * @return string
 */
function view_detail_position_edit(){
    if(webauth(VIEW_AUTH_EDIT)){
        return '　<a href="admin.php?typex=view_edit&cmd=inline" rel="facebox" >ギャラリー編集</a>';
    }
}

function view_detail_view_url($id){
    #/gallery.php?g={id}
    return ($id) ? "<a href='../gallery.php?g={$id}' target='_blank' >gallery.php?g={$id}</a>" : '-';
}

?>
