<?php

define('GROUP_LIST_FOR_VIEWLIST',0);
define('GROUP_LIST_FOR_AJAXMODE',1);
define('GROUP_LIST_FOR_TOGROUP' ,2);
define('GROUP_LIST_FOR_LIST' ,3);

/**
 * 表示するHTMLを作成する
 * @param array $viewitem
 * @param array $state
 */
function group_edit_view($viewitem,$state = array()){

    #コンテンツ作成--------------------------------------------------------------

    #エラー部分の作成
    $html_err = html_errhash_br($state['err']);

    #ソート部分
    $html_sort = group_edit_sort($viewitem['id'], $viewitem['groupid']);

    #グループの一覧
    $html_groupid = group_edit_group($viewitem);

    #ステート情報の保存
    $html_state = html_hidden($state);

    #保存後閉じるボタン
    $html_close = $state['close'] ? "true" : "false";

    #データの置換---------------------------------------------------------------

    $html = userread(switchSpTemplate('group_edit/input.html'));
    $html = rep($html,'firstclose',$html_close);
    $html = rep($html,'html_state',$html_state);
    $html = rep($html,'html_err',$html_err);
    $html = rep($html,'html_sort',$html_sort);
    $html = rep($html,'html_group',$html_groupid);
    $html = ephash($viewitem, $html);

    return $html;
}

/**
 * セレクトオプション内の中身
 * @param bool $forviewlist 画像一覧内での一覧かどうか
 */
function group_edit_list($groupid,$viewtype){

    #必要なクラスを読込む
    userload('group_edit/group_edit_model.php');

    $first = false;

    $html = '<option value="{id}" {checked}>{title}</option>';

    if($viewtype == GROUP_LIST_VIEWLIST){
        if($groupid === null) $groupid = '-1';
        $checked = $groupid == '-1' ? "selected='selected'" : '';
        $ret = phash(array('id'=>'-1','title'=>'すべてのグループ','checked'=>$checked), $html);

        $checked = $groupid == '0' ? "selected='selected'" : '';
        $ret .= phash(array('id'=>'0','title'=>'未分類','checked'=>$checked), $html);
    }

    if($viewtype == GROUP_LIST_FOR_AJAXMODE){
        $checked = $groupid == null ? "selected='selected'" : '';
        $ret = phash(array('id'=>'0','title'=>'選択しない','checked'=>$checked), $html);
        $first = true;
    }

    if($viewtype == GROUP_LIST_FOR_LIST){
        $checked = $groupid == null ? "selected='selected'" : '';
        $ret = phash(array('id'=>'0','title'=>'選択しない','checked'=>$checked), $html);
    }


    if($viewtype == GROUP_LIST_FOR_TOGROUP){
        $checked = $groupid == null ? "selected='selected'" : '';
        $ret = phash(array('id'=>'0','title'=>'未分類','checked'=>$checked), $html);
    }

    $tbl = group_edit::getListOrdered();
    $checkid = true;
    foreach($tbl as $row){
        if($first){
            $row['checked'] = "selected='selected'";
            $first = false;
            $checkid = false;
        }
        else{

            $row['checked'] = $groupid == $row['id'] && $checkid  ? "selected='selected'" : '';
        }
        
        $ret .= phash($row, $html);
    }
    return $ret;
}

/**
 * グループ一覧作る
 * @param array $viewitem
 * @return string
 */
function group_edit_group($viewitem){

    #画面遷移で戻ることができないかも...
    #一覧を取り出す。sortの順にする
    $tbl = group_edit::getListOrdered();
    $html = '<label class="grouplist">・ {title}</label>';
    foreach($tbl as $row){
        #選択用のチェックボックスを作成する
        $row['sortnum'] =group_edit::getSortNumber($row['id']);
        $ret .= phash($row, $html);
    }
    if($ret == null) $ret = 'グループが登録されていません';
    return $ret;
}

/**
 * ソートボタンの部分を取り出す
 * @param int $id
 * @return string
 */
function group_edit_sort($id){

    #IDがなくてもブロックは作成する必要あり
    $sortnum = $id == null ? '-' : group_edit::getSortNumber($id);
    $visible = $id == null ? 'none' : 'block';

    $ret = '<div id="sortblock" style="display:'. $visible .';">表示順位　<span id="sortnum">' . $sortnum . '</span>位　　'
        . '<input type="button" id="btnsort1" value="<<" onclick="return moveitem(0,1);" />
          <input type="button" id="btnsort2" value="<"  onclick="return moveitem(1,1);"/>
          <input type="button" id="btnsort3" value=">"  onclick="return moveitem(1,0);"/>
          <input type="button" id="btnsort4" value=">>"  onclick="return moveitem(0,0);"/></div>';
    return $ret;
}

/**
 * ポップアップ用のグループを表示する
 */
function group_edit_popup(){
    return '<iframe src="admin.php?typex=group_edit" height="300" width="470" frameborder="0"></iframe>';
}


?>
