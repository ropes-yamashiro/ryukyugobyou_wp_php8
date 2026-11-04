<?php

define('VIEWDETAIL_AUTH_VIEW','viewdetail_view');
define('VIEWDETAIL_AUTH_EDIT','viewdetail_edit');
define('VIEWDETAIL_AUTH_DETAIL','viewdetail_detail');


/**
 * プログラムの実行起点(フレームワークから実行される)
 * @param string $cmd
 * @param array $input
 * @param array $state
 * @param bool $render
 */
function view_detail_act($cmd='view',&$input = array(),&$state = array(),$render = true){
    
    #必要なクラスを読込む
    userload('view_detail/view_detail_model.php');
    
    $cmd = webparam('cmd',$cmd);
    $input = view_detail::parseValue();
    $state['group_edit_id'] = view_detail::filterGroup(webparam('group_edit_id'));

    switch($cmd){
        
        #初期・リロードどちらか --------------------------------------------------
        case 'view': case 'reload':{
            $pos = $input['position'];
            $input = ($pos)
                ? view_detail::getDetailByPosition($pos)
                : view_detail::getNullMember();

            $input['position'] = $pos;
        }break;


       #保存 --------------------------------------------------------------------
        case 'save':{
            if(!webauth(VIEWDETAIL_AUTH_EDIT)){
                $errval = array('auth'=>'編集する権限がありません');
                break;
            }

            $errval = view_detail::checkValue($input,$state);

            #エラーがあるかチェック
            if(!count($errval)){
                $grouplist = $state['group_edit_id'];
                $input = view_detail::saveValue($input,$state);
            }

        }break;


        #リスト一覧--------------------------------------------------------------
        case 'list':{
            userload('view_detail/view_detail_view.php');
            $position =  view_detail_position($input['position']);
            $group = view_detail_group($input['id']);
            echo json_encode(array('position'=>$position,'group'=>$group));
            exit();
        }break;

        #何もしない。
        case 'none':{

        }break;

    }
    #表示する場合だけ
    if($render){
        userload('view_detail/view_detail_view.php');
        $state['err'] = $errval;
        $html = view_detail_view($input, $state);
    }
    return new plugin_result($html);
}

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function view_detail_tbl_setup($force = false){
    #必要なクラスを読込む
    userload('view_detail/view_detail_model.php');
    view_detail::createTable($force);
}

/**
 * 初期実行が可能か実行できるか(フレームワークから)
 * @return bool
 */
function view_detail_first() {
    return webauth(VIEWDETAIL_AUTH_VIEW);
}

/**
 * アカウントのメニュー(フレームワークから)
 */
function view_detail_menu($line,$admin,$title) {
    //一覧ができたら使用しない
}

?>