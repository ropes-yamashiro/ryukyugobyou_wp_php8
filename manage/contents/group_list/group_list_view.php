<?php

/**
 * ページング部分を作成する
 */
function group_list_view($viewitem,$state){


    userload('group_list/group_list_model.php');
    userload('group_edit/group_edit_view.php');

    $count = intval($viewitem['count']) ? intval($viewitem['count']) : 10;
    $cpage = intval($viewitem['pageno']) ? intval($viewitem['pageno']) : 1;

    $html_paging = group_list_paging($count,$cpage);
    $out_list    = group_list_all($count, $cpage);


    $html_group  = group_edit_list($groupid,GROUP_LIST_FOR_TOGROUP);
    $html_group_all = group_edit_list($groupid,GROUP_LIST_FOR_VIEWLIST);
    $html_count  = group_list_page_count($count);
    $html_err    = html_errhash_br($state,null,'color:red;');
    $page   = group_list_getTemp('page');
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
function group_list_paging($count,$cpage){
    $total = group_list::getCountByPaging();
    $line = group_list_getTemp('paging');
    $html =  html_paging($total, $count, $cpage, $line);
    return $html;
}

/**
 * カウント表示を作成する
 * @param int $count
 * @return string
 */
function group_list_page_count($count){
    $clist =array(10,20,50,100);
    foreach($clist as $cone){
        $checked = $count == $cone ? ' selected="selected"' : '';
        $ret .="<option value='{$cone}'{$checked}>{$cone} 件</option>";
    }
    return $ret;
}

/**
 * 一覧を作成する
 * @param int $count
 * @param int $cpage
 * @return string
 */
function group_list_all($count,$cpage){
    $list = group_list::getListByPaging($cpage, $count);
    if(count($list) == 0) return group_list_getTemp('list_none');
    for($i=0;$i<count($list);$i++){
        $ret .= group_list_element($list[$i], $i);
    }
    return $ret;
}

/**
 * テーブルのの行を作成する
 * @param array $row
 * @param int $count
 */
function group_list_element($row,$count){
    $block = $count % 2 == 0 ? 'list_even' : 'list_odd';
    $page = group_list_getTemp($block);

    $row['html_comment'] = mb_strlen(strip_tags($row['comment']),'utf-8') > 40
        ? (mb_substr(strip_tags($row['comment']),0,40,'utf-8') . '...')
        : strip_tags($row['comment']);


    $row['html_title'] = mb_strlen(strip_tags($row['title']),'utf-8') > 20
        ? (mb_substr(strip_tags($row['title']),0,20,'utf-8') . '...')
        : strip_tags($row['title']);

    #ページの変換
    return phash($row, $page);
}



/**
 * テンプレートを読込む
 * @return array
 */
function group_list_getTemp($blockname){
    $param = '@group_list_getTemp';
    $value = $GLOBALS[$param];
    if($value == null){
        $value = group_list_load_temp_action();
        $GLOBALS[$param] = $value;
    }
    return $value[$blockname];
}

/**
 * テンプレートデータを読込む・キャッシュ化する
 * @return array
 */
function group_list_load_temp_action(){
    $cache = userpath('group_list/group_list_manage.cache');
    $input = userpath(switchSpTemplate('group_list/input.html'));
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
