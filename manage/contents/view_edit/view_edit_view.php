<?php
/**
 * 表示するHTMLを作成する
 * @param array $viewitem
 * @param array $state
 */
function view_edit_view($viewitem,$state = array()){

    #コンテンツ作成--------------------------------------------------------------

    #エラー部分の作成
    $html_err = html_errhash_br($state['err']);

    #グループの一覧
    $html_groupid = view_edit_group($viewitem);

    #ステート情報の保存
    $html_state = html_hidden($state);

    #データの置換---------------------------------------------------------------
    $html = userread('view_edit/input.html');
    $html = rep($html,'html_state',$html_state);
    $html = rep($html,'html_err',$html_err);
    $html = rep($html,'html_group',$html_groupid);
    $html = ephash($viewitem, $html);

    return $html;
}

/**
 * セレクトオプション内の中身
 */
function view_edit_list($groupid){

    #必要なクラスを読込む
    userload('view_edit/view_edit_model.php');

    $html = '<option value="{id]" {checked}>{title}</option>';
    $checked = $gruopid == null ? "checked='checked'" : '';
    $ret = phash(array('id'=>0,'title'=>'すべての表示位置','checked'=>$checked), $html);

    $tbl = view_edit::getList();
    foreach($tbl as $row){
        $row['checked'] = $gruopid == $row['id'] ? "checked='checked'" : '';
        $ret .= phash($row, $html);
    }
    return $ret;
}

/**
 * 一覧作る
 * @param table $viewitem
 * @return string
 */
function view_edit_group($viewitem){

    #画面遷移で戻ることができないかも...
    #一覧を取り出す。sortの順にする
    $tbl = view_edit::getList();
    $html = '<label class="grouplist">'
        .'<input type="radio" name="selgroup[]" value="{id}" />{title}</label>'
        .'<input type="hidden" id="seltitle_{id}" value="{title}" />';

    foreach($tbl as $row){
        #選択用のチェックボックスを作成する
        $ret .= phash($row, $html);
    }
    return $ret ? $ret : '表示位置が登録されていません';
}

/**
 * ポップアップ用のグループを表示する
 */
function view_edit_popup(){
    return '<iframe src="admin.php?typex=view_edit"  height="300" width="470"  frameborder="0"></iframe>';
}

?>
