<?php

/**
 * ページング部分を作成する
 */
function gallery_list_view($viewitem,$state){

    #return gallery_list_getTemp('page');

    userload('gallery_list/gallery_list_model.php');
    userload('group_edit/group_edit_view.php');

    $count = intval($viewitem['count']) ? intval($viewitem['count']) : 10;
    $cpage = intval($viewitem['pageno']) ? intval($viewitem['pageno']) : 1;
    $groupid = $viewitem['group_id'] !== null ? $viewitem['group_id'] : '-1';

    $html_paging = gallery_list_paging($groupid,$count,$cpage);
    $out_list    = gallery_list_all($groupid, $count, $cpage);
    $html_group  = group_edit_list($groupid,GROUP_LIST_FOR_TOGROUP);
    $html_group_all = group_edit_list($groupid,GROUP_LIST_FOR_VIEWLIST);
    $html_count  = gallery_list_page_count($count);
    $html_err    = html_errhash_br($state,null,'color:red;');
    
    $page   = gallery_list_getTemp('page');
    $page   = rep($page,'pageno',$cpage);
    $page   = rep($page,'html_paging',$html_paging);
    $page   = rep($page,'out_list',$out_list);
    $page   = rep($page,'html_group',$html_group);
    $page   = rep($page,'html_group_all',$html_group_all);
    $page   = rep($page,'html_count',$html_count);
    $page   = rep($page,'html_err',$html_err);
    
    return $page;
}

/**
 * ページングを作成する
 * @param int $groupid
 * @param int $count
 * @param int $cpage
 * @return string
 */
function gallery_list_paging($groupid,$count,$cpage){
    $total = gallery_list::getCountByPaging($groupid);

    $line = gallery_list_getTemp('paging');
    $html =  html_paging($total, $count, $cpage, $line);
    return $html;
}

/**
 * カウント表示を作成する
 * @param int $count
 * @return string
 */
function gallery_list_page_count($count){
    $clist =array(10,20,50,100);
    foreach($clist as $cone){
        $checked = $count == $cone ? ' selected="selected"' : '';
        $ret .="<option value='{$cone}'{$checked}>{$cone} 件</option>";
    }
    return $ret;
}

/**
 * 一覧を作成する
 * @param int $groupid
 * @param int $count
 * @param int $cpage
 * @return string
 */
function gallery_list_all($groupid,$count,$cpage){
    $list = gallery_list::getListByPaging($groupid, $cpage, $count);
    if(count($list) == 0) return gallery_list_getTemp('list_none');
    for($i=0;$i<count($list);$i++){
        $ret .= gallery_list_element($list[$i], $i);
    }
    return $ret;
}

/**
 * テーブルのの行を作成する
 * @param array $row
 * @param int $count
 */
function gallery_list_element($row,$count){
    $block = $count % 2 == 0 ? 'list_even' : 'list_odd';
    $page = gallery_list_getTemp($block);

    #表示する文字の修正
    $row['html_term'] = ($row['useviewterm'] == 1)
        ? (date('Y/m/d',strtotime($row['fromdate']))
            . ' ～ ' . date('Y/m/d',strtotime($row['finishdate'])))
        : '&nbsp;';

    $row['html_visible'] = $row['isvisible'] == '1'
        ? ' checked="checked" '
        : '';

    $row['html_comment1'] = mb_strlen(strip_tags($row['comment1']),'utf-8') > 20
        ? (mb_substr(strip_tags($row['comment1']),0,20,'utf-8') . '...')
        : strip_tags($row['comment1']);


    $row['html_title'] = mb_strlen(strip_tags($row['title']),'utf-8') > 20
        ? (mb_substr(strip_tags($row['title']),0,20,'utf-8') . '...')
        : strip_tags($row['title']);


    $title_del = mb_substr(strip_tags($row['title']),0,20,'utf-8');
    $title_del = str_replace(array("\n","\r","\t"), '', $title_del);

    $row['html_title_del'] = $title_del;

    $row['html_groupname'] = $row['groupname']
        ? $row['groupname']
        : '未分類';

    #ページの変換
    return phash($row, $page);
}



/**
 * テンプレートを読込む
 * @return array
 */
function gallery_list_getTemp($blockname){
    $param = '@gallery_list_getTemp';
    $value = $GLOBALS[$param];
    if($value == null){
        $value = gallery_list_load_temp_action();
        $GLOBALS[$param] = $value;
    }
    return $value[$blockname];
}

/**
 * テンプレートデータを読込む・キャッシュ化する
 * @return array
 */
function gallery_list_load_temp_action(){
    $cache = userpath('gallery_list/gallery_list_manage.cache');
    $input = userpath(switchSpTemplate('gallery_list/input.html'));
    if(!file_exists($cache)){
        $loader = new TempLoader($input);
        $value = $loader->cookPage(array('list_odd','list_even','list_none','paging'));
        file_put_contents($cache, serialize($value));
    }
    else{
        #if(true){
        if(filemtime($cache) < filemtime($input)){
            $loader = new TempLoader($input);
            $value = $loader->cookPage(array('list_odd','list_even','list_none','paging'));
            file_put_contents($cache, serialize($value));
        }
        else{
            $value = unserialize(file_get_contents($cache));
        }
    }
    return $value;
}




?>
