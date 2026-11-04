<?php

define('VIEW_AUTH_EDIT','view_edit');

/**
 * プログラムの実行起点(フレームワークから実行される)
 * @param string $cmd
 * @param array $input
 * @param array $state
 * @param bool $render
 */
function view_edit_act($cmd='view',&$input = array(),&$state = array(),$render = true){

    #必要なクラスを読込む
    userload('view_edit/view_edit_model.php');

    $cmd = webparam('cmd',$cmd);
    $input = view_edit::parseValue();

    switch($cmd){

        #新規か、保存後どちらか --------------------------------------------------
        case 'view':{
            if(!webauth(VIEW_AUTH_EDIT)){view_edit_non_auth();}
            $id = $input['id'];
            $input = ($id)
                ? view_edit::getDetail($id)
                : view_edit::getNullMember();
        }break;


        #削除 ------------------------------------------------------------------
        case 'remove':{
            if(!webauth(VIEW_AUTH_EDIT)){view_edit_non_auth();}

            $grouplist = webparam('selgroup',array());
            view_edit::removeGroup($grouplist);
            $input['title'] = null;
            $input['id'] = null;
        }break;


        #ポップアップ用 ---------------------------------------------------------
        case 'inline':{
            userload('view_edit/view_edit_view.php');
            echo view_edit_popup();
            exit();
        }


        #保存 ------------------------------------------------------------------
        case 'save':{
            if(!webauth(VIEW_AUTH_EDIT)){view_edit_non_auth();}

            $errval = view_edit::checkValue($input);
            
            #エラーがあるかチェック
            if(count($errval) == 0){
                #データを保存する
                view_edit::saveValue($input);
                $input = view_edit::getNullMember();
            }
        }break;

    }
    #表示する場合だけ
    if($render){
        userload('view_edit/view_edit_view.php');
        $state['err'] = $errval;
        $html = view_edit_view($input, $state);
    }
    echo $html;
    exit();

}

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function view_edit_tbl_setup($force = false){
    #必要なクラスを読込む
    userload('view_edit/view_edit_model.php');
    view_edit::createTable($force);
}

/**
 * 初期実行が可能か実行できるか(フレームワークから)
 * @return bool
 */
function view_edit_first() {
    return true;
}

/**
 * 権限がない場合の処理
 */
function view_edit_non_auth() {
    $errval = array('auth' => '編集する権限がありません');
    echo html_errhash_br($errval);
    exit();
}


?>
