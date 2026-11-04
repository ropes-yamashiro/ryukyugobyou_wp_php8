<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
//ウエダコーポの場合
userload("passwd/passwd_sql.php");

//=====================================================
//  フレームワーク用

/** エラーチェック */
function passwd_check($cmd) {

    $member = passwd_sql_info_by_userid($_POST['userid']);

    if($cmd == 'create' && $member['id'] != null){
        $hasSameid = true;
    }
    elseif($cmd == 'save') {
        $id = $_POST['param'];
        $now = passwd_sql_info_by_id($id);

        #rootがuseridを変えようとしている場合
        if($now['userid'] == 'ann_admin' && $_POST['userid'] != 'ann_admin')
               $err = "ann_adminのアカウント名は変更できません<br />";

        #ユーザーidが変更されていて、データがあり、データが同じでないとき
        if($now['userid'] != $_POST['userid'] && $member['id'] && $member['id'] != $id ){
            $hasSameid= true;
        }
    }
    else $hasSameid = false;

    if($_POST['userid'] == null) $err .= "アカウント名は必ず入力してください<br />";
    if($_POST['username'] == null) $err .= "ユーザー情報は必ず入力してください<br />";
    if(!isEngword($_POST['passwd'])) $err .= "パスワードは半角英数のみ使用できます<br />";
    $err .= $hasSameid ? $_POST['userid'] ."はすでに存在しています<br />" : null;

    return $err ? "<p style='color:red'>$err</p><br>" : null;
}

/** ユーザーを削除します */
function passwd_del() {
    $id     = webparam('param');
    $info   = passwd_sql_info_by_id($id);

    if($info['userid'] == 'ann_admin'){
        return "<p>".html_errtag('ann_adminは削除できません')."</p><br>";
    }
    else{
        passwd_sql_del($id);
    }
}

/** データの保存 */
function passwd_save($cmd) {
    if($cmd == 'create'){
        $id = passwd_sql_add($_POST['userid'],$_POST['passwd'],$_POST['username']);
        $_POST['id'] = $id;
    }
    if($cmd == 'save') {
        passwd_sql_change($_POST['userid'],$_POST['passwd'],$_POST['username'],$_POST['param']);
    }
}

/**
 * NULL値を取り出す
 * @return array
 */
function passwd_nullval(){
    return array("userid"=>null,"username"=>null,"passwd"=>null,'id'=>null);
}

//=====================================================
//  ページ表示用

/**テーブルを表示します */
function passwd_list($err = null) {
    $html_store = new tLoader(userpath(switchSpTemplate("passwd/list.html")));
    $html_page = $html_store->getData('page');
    $html_line = $html_store->getData('line');
    $html_noval = $html_store->getData('noval');
    $html_paging = $html_store->getData('paging');

    $pagecount = isSPhone() ? 1000 : 20;	//スマホは実質ページングなし
    $pageno = webparam('pageno',1);
    $count = passwd_sql_usercount();

    foreach(passwd_sql_infos($pageno,$pagecount) as $row) {
        $tline = ephash($row,$html_line);
        $table .= rep($tline,'color',$i % 2 + 1);
        $i ++;
    }
    if($table == null) $table = $html_noval;

    $html_page = rep($html_page,'pageno',$pageno);
    $html_page = rep($html_page,'table',$table);
    $html_page = rep($html_page,'err',$err);
    $html_page = rep($html_page,'paging',html_paging($count,$pagecount,$pageno,$html_paging));
    return $html_page;
}

/**
 * 詳細画面を表示する
 * @param string $cmd
 * @param string $err
 * @return string
 */
function passwd_view($cmd,$err = null) {
    $id     = webparam('param');
    $pageno = webparam('pageno',1);

    $viewitem = passwd_nullval();
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
        $viewitem = passwd_sql_info_by_id($id);
    }

    $page = userread(switchSpTemplate("passwd/input.html"));
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
function passwd_act() {
    if(!webauth('passwd')) return '<font color="red">不正アクセスエラー：権限がありません</font>';
    $cmd = webparam('cmd','list');

    switch($cmd){
        case 'delline':{ #ユーザー削除
            $err = passwd_del();
            return new plugin_result(passwd_list($err));
        }break;

        case 'detail': case 'new':{ #表示のみ
            return new plugin_result(passwd_view($cmd));
        }break;

        case 'create' : case 'save' :{ #保存
            $err = passwd_check($cmd);
            if($err){
                return new plugin_result(passwd_view($cmd,$err));
            }
            else{
                passwd_save($cmd);
                return new plugin_result(passwd_view($cmd));
            }
        }break;
    }
    return new plugin_result(passwd_list());
}

/**
 * 管理画面メニュー用
 */
function passwd_menu($line,$admin,$title) {
    if(!webauth('passwd')) return;
    $one =  rep($line,'typex',"passwd");
    $ret .= rep($one,'title',"パスワード設定");
    return $ret;
}

/**
 * パスワード権限一覧
 * @return array
 */
function passwd_auth() {
    $auth1 = array('key'=>"passwd",'name'=>"パスワード設定",'info'=>"パスワードの変更が可能になります。");
    $hash["パスワード"] = array($auth1);
    return $hash;
}

/**
 * 一番初めにアクセスできるか
 * @return bool
 */
function passwd_first() {
    return webauth('passwd');
}

//=====================================================
//  モジュール依存

/**
 *  account.php:パスワード情報の取得
 */
function passwd_alldata() {
    $tbl = passwd_sql_all();
    $ret = array();
    foreach($tbl as $row) {
        $ret[$row['userid']] = $row;
    }
    return $ret;
}

/**
 * パスワードを確認する
 * @param string $name
 * @param string $passwd
 * @return array
 */
function passuser_check($name,$passwd) {
    $ret = passwd_sql_info_by_userid($name);
    if($ret['passwd'] == $passwd) {
        unset($ret['passwd']);
        return $ret;
    }
    return false;
}

?>
