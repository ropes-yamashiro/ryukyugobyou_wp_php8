<?php

function shokokai_is_homepage($id){
    return $id <= 6;
}

/**
 * 商工会議所 新着情報
 * @param string $line
 * @param string $admin
 * @param string $title
 * @return string
 */
function shokokai_topic_menu($line, $admin, $title) {
    $db = getDB();
    $sql = 'SELECT * FROM topic_info';
    $res = $db->QueryRow($sql);

    $top = rep($title, 'title', 'ホームページ');
    foreach ($res as $row) {
        if (!webauth('topic_title' . $row['id'])) continue;
        if (!shokokai_is_homepage($row['id'])) continue;

        $item = rep($line, 'typex', "topic&id=" . $row['id']);
        $ret1 .= rep($item, 'title', $row['name']);
    }
    $ret1 = $ret1 ? $top . $ret1 : '';

    $top = rep($title, 'title', '新着情報');
    foreach ($res as $row) {
        if (!webauth('topic_title' . $row['id'])) continue;
        if (shokokai_is_homepage($row['id'])) continue;

        $item = rep($line, 'typex', "topic&id=" . $row['id']);
        $ret2 .= rep($item, 'title', $row['name']);
    }
    $ret2 = $ret2 ? $top . $ret2 : '';

    return $ret1 . $ret2;
}

function shokokai_topic_category($gid = 1){
    if(shokokai_is_homepage($gid)){
        return '会員ホームページ&nbsp;&nbsp;';
    }
    else return '';
}

/**
 * 入力タイプ
 * @param <type> $gid
 */
function shokokai_topic_type($gid){
    if(!shokokai_is_homepage($gid)){
        return '
&nbsp;<label><input type=radio name=type onClick="selbox(0,5);" value="0" {type0} />リンク</label>
&nbsp;<label><input type=radio name=type onClick="selbox(1,5);" value="1" {type1} />ポップアップ</label>
&nbsp;<label><input type=radio name=type onClick="selbox(2,5);" value="2" {type2} />ファイル</label>
&nbsp;<label><input type=radio name=type onClick="selbox(3,5);" value="3" {type3} />タイトルのみ</label>
        ';
    }
    else{
        return '
&nbsp;<label><input type=radio name=type onClick="selbox(0,5);" value="0" {type0} />リンク</label>
            ';
    }
}
?>
