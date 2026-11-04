<?php

/**
 * 表示するHTMLを作成する
 * @param array $viewitem
 * @param array $state
 */
function gallery_detail_view($viewitem,$state = array()){

    #コンテンツ作成--------------------------------------------------------------

    #エラー部分の作成
    $html_err = html_errhash_br($state['err'],'errmes');

    #イメージ部分の作成
    if($viewitem['saveimgpath'] || $state['realpath']){
        $data = gallery_detail::getDetail($viewitem['id']);
        $img = GALLERY_FIX_FOLDER . $data['saveimgpath'];
        $state['realpath'] = $data['saveimgpath'] != null;
    }
    elseif($state['imgpath']){
        $img =  $state['imgpath'];
    }
    $html_img  = gallery_detail_image($img);

    #ソート部分
    $html_sort = gallery_detail_sort($viewitem['id'], $viewitem['groupid']);

    #グループの一覧
    userload('group_edit/group_edit_view.php');
    $html_groupid = group_edit_list($viewitem['groupid'],GROUP_LIST_FOR_LIST);
    $html_groupid_all = group_edit_list($viewitem['groupid'],GROUP_LIST_VIEWLIST);
    $html_group_edit = gallery_detail_group_edit();

    $html_paging = html_hidden($state['paging']);

    #ステート情報の保存
    unset($state['err']);
    unset($state['paging']);
    $html_state = html_hidden($state,'state');

    #データの置換---------------------------------------------------------------
    $html = userread(switchSpTemplate('gallery_detail/input.html'));
    $html = rep($html,'html_state',$html_state);
    $html = rep($html,'html_paging',$html_paging);
    $html = rep($html,'html_err',$html_err);
    $html = rep($html,'html_img',$html_img);
    $html = rep($html,'html_sort',$html_sort);
    $html = rep($html,'html_group',$html_groupid);
    $html = rep($html,'html_group_inc',$html_groupid_all);
    $html = rep($html,'html_group_edit',$html_group_edit);
    $html = rep($html,'id',html_hidden(array('id'=>$viewitem['id'])));
    $html = html_check($html, 'isvisible', array(0,1), array($viewitem['isvisible']));
    $html = html_check($html, 'useviewterm', array(0,1), array($viewitem['useviewterm']));
    $html = ephash($viewitem, $html);

    return $html;

}

/**
 * ソートボタンを表示する
 * @param int $id
 * @param int $groupid
 * @return string
 */
function gallery_detail_sort($id,$groupid){


   #        : '<span id="sortnum">' . gallery_detail::getSortNumber($id, $groupid) . '</span>番目　'
   #      . '<input type="button" value="<<" onclick="return moveitem(0,1);" />
   #         <input type="button" value="<10"  onclick="return moveitem(10,1);"/>
   #         <input type="button" value="<"  onclick="return moveitem(1,1);"/>
   #         <input type="button" value=">"  onclick="return moveitem(1,0);"/>
   #         <input type="button" value="10>"  onclick="return moveitem(10,0);"/>
   #         <input type="button" value=">>"  onclick="return moveitem(0,0);"/>';

    $ret = $id == null
       ? '-'
       : '<div style="float:left;padding-top:5px;"><span id="sortnum">' . gallery_detail::getSortNumber($id, $groupid) . '</span>番目　</div>'
         . '<a href="javascript:void(0);" class="btnup1"  onclick="return moveitem(1,1);"><img src="image/btn/up1.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btnup10" onclick="return moveitem(10,1);" ><img src="image/btn/up10.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btnupmax" onclick="return moveitem(0,1);"><img src="image/btn/upmax.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btndn1"  onclick="return moveitem(1,0);"><img src="image/btn/dn1.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btndn10" onclick="return moveitem(10,0);" ><img src="image/btn/dn10.gif" width="26" height="26" /></a>
            <a href="javascript:void(0);" class="btndnmax" onclick="return moveitem(0,0);"><img src="image/btn/dnmax.gif" width="26" height="26" /></a>';
    return $ret;
}

/**
 * グループ表示ボタンを作成する
 * @return string
 */
function gallery_detail_group_edit(){
    if(webauth(GROUP_AUTH_EDIT)){
        return 
        #'<input type="button" value="グループ編集" onclick="return popup_modal();">';
        '<a href="admin.php?typex=group_edit&cmd=inline" rel="facebox" >グループ登録</a>';
        #onclick="return popup_modal();
    }
}


/**
 * イメージコンテンツを作成する
 * @param string $img
 * @return string
 */
function gallery_detail_image($img){
    if($img){
        return "<img src='{$img}' width='300' /><br /><input type='button' value='削除' size='80' onclick='return delimage();'>";
    }
    else{
        return "<input type='file' name='imgname' >";
    }
}


?>
