<?php



/**
 * プログラムの実行起点(フレームワークから実行される)
 * @param string $cmd
 * @param array $input
 * @param array $state
 * @param bool $render
 */
function group_list_act($cmd='view',&$input = array(),&$state = array(),$render = true){

    #必要なクラスを読込む
    userload('group_list/group_list_model.php');
    userload('group_edit/group_edit_model.php');
    $cmd = webparam('cmd',$cmd);
    $input = group_list::parseValue();
    switch($cmd){
        
        #一覧の表示 -------------------------------------------------------------
        case 'view':{
            #viewcount(表示数),paging(ページ番号),viewgroup(表示グループ)
        }break;


        #切換え-----------------------------------------------------------------
        case 'removeall':{
            $errval = group_edit::checkAuth();
            if(count($errval)) break;
            
            $idlist = array(webparam('targetid'));
            group_list::removegroup($idlist);

        }break;


        #ソート(ローテート) -----------------------------------------------------
        case 'rotate':{
            $errval = group_edit::checkAuth();
            if(count($errval)) break;

            $id      = webparam('targetid');
            $size    = webparam('move_size');
            $updown  = webparam('move_type');
            group_edit::rotateItem($id, $updown, $size);
        }
    }
    #表示する場合だけ
    if($render){
        userload('group_list/group_list_view.php');
        $state['err'] = $errval;
        $html = group_list_view($input, $state);
    }

    return new plugin_result($html);
}


/**
 * 初期実行が可能か実行できるか(フレームワークから)
 * @return bool
 */
function group_list_first() {
    return webauth(GROUP_LIST_AUTH_VIEW);
}


?>