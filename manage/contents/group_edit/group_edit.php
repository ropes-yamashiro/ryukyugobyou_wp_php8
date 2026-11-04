<?php

define('GROUP_AUTH_EDIT', 'group_edit');
define('GROUP_AUTH_VIEW','group_view');
define('GROUP_LIST_AUTH_VIEW','group_list');



/**
 * プログラムの実行起点(フレームワークから実行される)
 * @param string $cmd
 * @param array $input
 * @param array $state
 * @param bool $render
 */
function group_edit_act($cmd='view', &$input = array(), &$state = array(), $render = true) {

    #必要なクラスを読込む
    userload('group_edit/group_edit_model.php');

    $cmd = webparam('cmd', $cmd);
    $input = group_edit::parseValue();
    

    switch ($cmd) {

        #新規か、保存後どちらか --------------------------------------------------
        case 'view': {
                if (!webauth(GROUP_AUTH_EDIT)) group_edit_non_auth();
                $id = $input['id'];
                $input = ($id) ? group_edit::getDetail($id) : group_edit::getNullMember();
            }break;

        #ポップアップ用 ---------------------------------------------------------
        case 'inline': {
                userload('group_edit/group_edit_view.php');
                echo group_edit_popup();
                exit();
            }

        #削除 ------------------------------------------------------------------
        case 'remove': {
                if (!webauth(GROUP_AUTH_EDIT)) group_edit_non_auth();
                $grouplist = webparam('selgroup', array());
                group_edit::removeGroup($grouplist);
                $input['title'] = null;
                $input['id'] = null;
            }break;



        #保存 ------------------------------------------------------------------
        case 'save': {
                if (!webauth(GROUP_AUTH_EDIT)) group_edit_non_auth();
                $errval = group_edit::checkValue($input);

                #エラーがあるかチェック
                if (count($errval) == 0) {
                    #データを保存する
                    $input = group_edit::saveValue($input);
                    $input['title'] = null;
                    $input['id'] = null;
                    $state['close'] = true;
                }
            }break;

        #ソート(ローテート) -----------------------------------------------------
        case 'rotate': {
                if (!webauth(GROUP_AUTH_EDIT)) group_edit_non_auth();
                $id = $input['id'];
                $size = webparam('move_size');
                $updown = webparam('move_type', group_edit::MOVE_TYPE_UP);
                echo group_edit::rotateItem($id, $updown, $size);
                exit();
            }
    }
    #表示する場合だけ
    if ($render) {
        userload('group_edit/group_edit_view.php');
        $state['err'] = $errval;
        $html = group_edit_view($input, $state);
    }
    echo $html;
    exit();
}

/**
 * 権限がない場合の処理
 */
function group_edit_non_auth() {
    $errval = array('auth' => '編集する権限がありません');
    echo html_errhash_br($errval);
    exit();
}

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function group_edit_tbl_setup($force = false) {
    #必要なクラスを読込む
    userload('group_edit/group_edit_model.php');
    group_edit::createTable($force);
}

/**
 * 初期実行が可能か実行できるか(フレームワークから)
 * @return bool
 */
function group_edit_first() {
    return true;
}
?>
