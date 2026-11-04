<?php

define('GALLERY_LIST_AUTH_VIEW','gallery_list_view');

/**
 * プログラムの実行起点(フレームワークから実行される)
 * @param string $cmd
 * @param array $input
 * @param array $state
 * @param bool $render
 */
function gallery_list_act($cmd='view',&$input = array(),&$state = array(),$render = true){

    #必要なクラスを読込む
    userload('gallery_list/gallery_list_model.php');
    userload('gallery_detail/gallery_detail_model.php');

    $cmd = webparam('cmd',$cmd);
    $input = gallery_list::parseValue();
    
    switch($cmd){
        
        #一覧の表示 -------------------------------------------------------------
        case 'view':{
            #viewcount(表示数),paging(ページ番号),viewgroup(表示グループ)
        }break;

        #グループ一覧(Ajax用)----------------------------------------------------
        case 'grouplist':{
            userload('group_edit/group_edit_view.php');
            echo group_edit_list($input['viewgroup'],GROUP_LIST_FOR_TOGROUP);
            exit();
        }break;

        #グループ切換え----------------------------------------------------------
        case "chgrpall":{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;

            $togroup = webparam('chgroup');
            $idlist = gallery_list_getSelected();
            gallery_list::changeGroup($idlist, $togroup);
        }break;

        #グループ一覧------------------------------------------------------------
        case 'list':{
            userload('group_edit/group_edit_view.php');
            echo group_edit_list($input['group_id'],GROUP_LIST_FOR_TOGROUP);
            exit();
        }break;



        #表示切換え--------------------------------------------------------------
        case 'chvisall':{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;
            
            $idlist = gallery_list_getSelected();
            gallery_list::changeVisible($idlist);

        }break;

        #切換え-----------------------------------------------------------------
        case 'removeall':{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;
            
            $idlist = gallery_list_getSelected();
            gallery_list::removeGallery($idlist);

        }break;

        #単体削除
         case 'removeone':{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;
            $idlist = array(webparam('targetid'));
            gallery_list::removeGallery($idlist);

        }break;


        #表示切換え単体 ---------------------------------------------------------
        case 'visible':{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;
            
            gallery_list::changeVisible(array(webparam('targetid')));
        }break;


        #ソート(ローテート) -----------------------------------------------------
        case 'rotate':{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;

            $id = webparam('targetid');
            $groupid = webparam('group_id');
            $size   = webparam('move_size');
            $updown = webparam('move_type');

            gallery_detail::rotateItem($id, $groupid, $updown, $size);
        }
    }
    #表示する場合だけ
    if($render){
        userload('gallery_list/gallery_list_view.php');
        $state['err'] = $errval;
        $html = gallery_list_view($input, $state);
    }
    return new plugin_result($html);
}



function gallery_list_getSelected(){
    $ret = array();
    foreach($_POST as $key => $value){
        if(preg_match('/^id_sel([0-9]+)$/',$key,$match)){
            $ret[] = $match[1];
        }
    }
    return $ret;
}


/**
 * 初期実行が可能か実行できるか(フレームワークから)
 * @return bool
 */
function gallery_list_first() {
    return webauth(GALLERY_LIST_AUTH_VIEW);
}

/**
 * アカウントのメニュー(フレームワークから)
 */
function gallery_list_menu($line,$admin,$title) {
    //一覧ができたら使用しない
}



?>