<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
//ウエダコーポの場合
userload("hintpasswd/hintpasswd_sql.php");

//=====================================================
//  フレームワーク用

/** エラーチェック */
function hintpasswd_check($cmd) {

    $member = hintpasswd_sql_info_by_userid($_POST['userid']);

    if($cmd == 'create' && $member['id'] != null){
        $hasSameid = true;
    }
    elseif($cmd == 'save') {
        $id = $_POST['param'];
        $now = hintpasswd_sql_info_by_id($id);

        #ユーザーidが変更されていて、データがあり、データが同じでないとき
        if($now['userid'] != $_POST['userid'] && $member['id'] && $member['id'] != $id ){
            $hasSameid= true;
        }
    }
    else $hasSameid = false;

    if($_POST['hintpasswd'] == null) {
        $err .= "パスワードは必ず入力してください<br />";
    }elseif(!isEngword($_POST['hintpasswd'])) {
        $err .= "パスワードは半角英数のみ使用できます<br />";
    }

    return $err ? "<p style='color:red'>$err</p><br>" : null;
}

/** ユーザーを削除します */
function hintpasswd_del() {
    $id     = webparam('param');
    $info   = hintpasswd_sql_info_by_id($id);

    hintpasswd_sql_del($id);
}

/** データの保存 */
function hintpasswd_save($cmd) {
    if($cmd == 'create'){
        $id = hintpasswd_sql_add($_POST['userid'],$_POST['hintpasswd'],$_POST['username']);
        $_POST['id'] = $id;
    }
    if($cmd == 'save') {
        hintpasswd_sql_change($_POST['userid'],$_POST['hintpasswd'],$_POST['username'],$_POST['param']);
    }
}

/**
 * NULL値を取り出す
 * @return array
 */
function hintpasswd_nullval(){
    return array("userid"=>null,"username"=>null,"hintpasswd"=>null,'id'=>null);
}

//=====================================================
//  ページ表示用

/**テーブルを表示します */
function hintpasswd_list($err = null) {
    $html_store = new tLoader(userpath("hintpasswd/list.html"));
    $html_page = $html_store->getData('page');
    $html_line = $html_store->getData('line');
    $html_line = rep($html_line, 'pageno', webparam('id'));
    $html_noval = $html_store->getData('noval');
    $html_paging = $html_store->getData('paging');

    $pagecount = 20;
    $pageno = webparam('pageno',1);
    $count = hintpasswd_sql_usercount();

    foreach(hintpasswd_sql_infos($pageno,$pagecount) as $row) {
        $tline = ephash($row,$html_line);
        $table .= rep($tline,'color',$i % 2 + 1);
        $i ++;
    }
    if($table == null) $table = $html_noval;

    $html_page = rep($html_page,'pageno',$pageno);
    $html_page = rep($html_page,'table',$table);
    $html_page = rep($html_page,'err',$err);
    $html_page = rep($html_page,'paging',html_paging($count,$pagecount,$pageno,$html_paging));

    // データが1件もないとき、新規ボタンを表示させる
//    if ($count == 0) {
        $html_page = rep($html_page,'newbuttn','<input type=submit id=btm value="新規" onclick="return newuser();" />');
//     } else {
//         $html_page = rep($html_page,'newbuttn','');
//     }
    return $html_page;
}

/**
 * 詳細画面を表示する
 * @param string $cmd
 * @param string $err
 * @return string
 */
function hintpasswd_view($cmd,$err = null) {
    $id     = webparam('param');
    $pageno = webparam('pageno',1);

    $viewitem = hintpasswd_nullval();
    #単にコマンドを追加するだけ
    if($cmd == 'new') {
        $cmd = 'create';
    }
    #追加の場合、エラー時　POST値のまま、なし時　コマンドを変更
    elseif($cmd == 'create'){
        if($err == null){
            $cmd ='save';
        }
        $viewitem = array_intersect_key($_POST, $viewitem);
    }
    #変更の場合はPOST値をそのまま
    elseif($cmd == 'save'){
        $viewitem = array_intersect_key($_POST, $viewitem);
    }
    #detailの場合は検索
    elseif($cmd == 'detail') {
        $cmd = 'save';
        $viewitem = hintpasswd_sql_info_by_id($id);
    }

    $page = userread("hintpasswd/input.html");
    $page = rep($page,'id',$id); //情報
    $page = rep($page,'err',$err); //エラーがあれば表示
    $page = rep($page,'cmd',$cmd); //次の行先
    $page = rep($page,'pageno',$pageno);
    $page = ephash($viewitem,$page);  //データ入力
    return $page;
}


//=====================================================
//  フレームワーク用

/**
 * フレームワークからアクセスされる関数
 * @return string
 */
function hintpasswd_act() {
    if(!webauth('hintpasswd' . webparam('id'))) return '<font color="red">不正アクセスエラー：権限がありません</font>';
    $cmd = webparam('cmd','list');

    switch($cmd){
        case 'delline':{ #ユーザー削除
            $err = hintpasswd_del();
            return new plugin_result(hintpasswd_list($err));
        }break;

        case 'detail': case 'new':{ #表示のみ
            return new plugin_result(hintpasswd_view($cmd));
        }break;

        case 'create' : case 'save' :{ #保存
            $err = hintpasswd_check($cmd);
            if($err){
                return new plugin_result(hintpasswd_view($cmd,$err));
            }
            else{
                hintpasswd_save($cmd);
                return new plugin_result(hintpasswd_view($cmd));
            }
        }break;
    }
    return new plugin_result(hintpasswd_list());
}

/**
 * 管理画面メニュー用
 */
function hintpasswd_menu($line,$admin,$title) {
    $res = hintpasswd_sql_all();

    foreach ($res as $row) {
        if (!webauth('hintpasswd' . $row['id'])) {
            continue;
        }
        $item = rep($line, 'typex', "hintpasswd&id=" . $row['id']);
        $ret .= rep($item, 'title', $row['name']);
    }
    //$ret = $ret ? $top . $ret : "";
    $ret = $ret ? "<li class='list_title'>アクセス制限</li>" . $ret : "";

    return $ret;
}

/**
 * パスワード権限一覧
 * @return array
 */
function hintpasswd_auth() {
    $ret = hintpasswd_sql_all();
    $auth1 = array();
    foreach ($ret as $value) {
        array_push($auth1, array('key'=>"hintpasswd" . $value['id'],'name'=>$value['name'],'info'=>$value['name']."の変更が可能になります。"));
    }

    $hash["アクセス制限"] = $auth1;
    return $hash;
}

/**
 * 一番初めにアクセスできるか
 * @return bool
 */
function hintpasswd_first() {
    return webauth('hintpasswd');
}

//=====================================================
//  モジュール依存

/**
 *  account.php:パスワード情報の取得
 */
function hintpasswd_alldata() {
    $tbl = hintpasswd_sql_all();
    $ret = array();
    foreach($tbl as $row) {
        $ret[$row['userid']] = $row;
    }
    return $ret;
}

?>