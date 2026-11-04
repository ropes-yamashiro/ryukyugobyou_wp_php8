<?php

/**
 * プログラムの実行起点(フレームワークから実行される)
 * @param string $cmd
 * @param array $input
 * @param array $state
 * @param bool $render
 */
function group_detail_act($cmd = null,&$input = array(),&$state = array(),$render = true){

    #必要なクラスを読込む
    userload('group_edit/group_edit_model.php');

    if($cmd == null){
        $cmd = webparam('cmd','view');
    }
    $input = count($input) ? $input : group_edit::parseValue();
    $state = count($state) ? $state : group_edit_state::parseValue('state');
    
    $paging = group_backlist::parseValue();
    $setstate = true;


    switch($cmd){
        
        #新規か、保存後どちらか --------------------------------------------------
        case 'view':{
            $id = $input['id'];
            $input = ($id)
                ? group_edit::getDetail($id)
                : group_edit::getNullMember();
        }break;

        #グループ一覧------------------------------------------------------------
        case 'backlist':{
            redirect('admin.php?typex=group_list',$paging);
            exit();
        }break;


        #ソート(ローテート) -----------------------------------------------------
        case 'rotate':{
            $errval = group_edit::checkAuth();
            if(count($errval)) break;
            $id = $input['id'];
            $groupid = $input['groupid'];
            $size   = $state['move_size'];
            $updown = $state['move_type'];
            echo group_edit::rotateItem($id, $updown, $size);
            exit();
        }break;

       #保存 --------------------------------------------------------------------
        case 'saveback':{
            group_detail_act('save', $input, $state, false);
            if(count($state['err']) == 0){
                group_detail_act('backlist', $input, $state, false);
            }
            $setstate = false;
        }break;

       #保存 --------------------------------------------------------------------
        case 'save':{
            $errval = group_edit::checkAuth();
            if(count($errval)) break;

            #変数のチェック
            $errval = group_edit::checkValue($input,$state);

            #エラーがあるかチェック
            if(count($errval) == 0){
                #エラーがなければ、データを保存する(ここではイメージを保存しない)
                $input = group_edit::saveValue($input);
            }
        }break;

    }
    if($setstate){
        $state['err'] = $errval;
        $state['paging'] = $paging;
    }

    #表示する場合だけ
    if($render){
        userload('group_detail/group_detail_view.php');
        $html = group_detail_view($input, $state);
    }
    return new plugin_result($html);
}

/**
 * 初期実行が可能か実行できるか(フレームワークから)
 * @return bool
 */
function group_detail_first() {
    return webauth(GROUP_AUTH_VIEW);
}


?>