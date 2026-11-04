<?php

/**
 * 表示するHTMLを作成する
 * @param array $viewitem
 * @param array $state
 */
function group_detail_view($viewitem,$state = array()){

    #コンテンツ作成--------------------------------------------------------------

    #エラー部分の作成
    $html_err = html_errhash_br($state['err'],'errmes');

    #ソート部分
    $html_sort = group_detail_sort($viewitem['id'], $viewitem['groupid']);

    $html_paging = html_hidden($state['paging']);

    #ステート情報の保存
    unset($state['err']);
    unset($state['paging']);
    $html_state = html_hidden($state,'state');

    #データの置換---------------------------------------------------------------
    $html = userread(switchSpTemplate('group_detail/input.html'));
    $html = rep($html,'html_state',$html_state);
    $html = rep($html,'html_paging',$html_paging);
    $html = rep($html,'html_err',$html_err);
    $html = rep($html,'html_img',$html_img);
    $html = rep($html,'html_sort',$html_sort);
    $html = rep($html,'id',html_hidden(array('id'=>$viewitem['id'])));
    $html = rep($html,'comment',$viewitem['comment']);
    $html = ephash($viewitem, $html);

    return $html;

}

/**
 * ソートボタンを表示する
 * @param int $id
 * @param int $groupid
 * @return string
 */
function group_detail_sort($id,$groupid){
    $ret = $id == null
       ? '-'
       : '<div style="float:left;padding-top:5px;"><span id="sortnum">' . group_edit::getSortNumber($id, $groupid) . '</span>番目　</div>'
         . '<a href="javascript:void(0);" class="btnup1"  onclick="return moveitem(1,1);"><img src="image/btn/up1.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btnup10" onclick="return moveitem(10,1);" ><img src="image/btn/up10.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btnupmax" onclick="return moveitem(0,1);"><img src="image/btn/upmax.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btndn1"  onclick="return moveitem(1,0);"><img src="image/btn/dn1.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btndn10" onclick="return moveitem(10,0);" ><img src="image/btn/dn10.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btndnmax" onclick="return moveitem(0,0);"><img src="image/btn/dnmax.gif" width="26" height="26" /></a>';
    return $ret;
}


?>
