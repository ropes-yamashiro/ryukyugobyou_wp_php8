<?php

define('GALLERY_LIST_EVENT_NOTHING',0);
define('GALLERY_LIST_EVENT_LINK',1);
define('GALLERY_LIST_EVENT_POPUP',2);

define('GALLERY_DETAIL_TARGET_NOTHING',0);
define('GALLERY_DETAIL_TARGET_BRANK',1);
define('GALLERY_DETAIL_TARGET_PARENT',2);



define('GALLERY_AUTH_VIEW','gallery_view');
define('GALLERY_AUTH_EDIT','gallery_edit');


/**
 * プログラムの実行起点(フレームワークから実行される)
 * @param string $cmd
 * @param array $input
 * @param array $state
 * @param bool $render
 */
function gallery_detail_act($cmd = null,&$input = array(),&$state = array(),$render = true){

    #必要なクラスを読込む
    userload('gallery_detail/gallery_detail_model.php');

    if($cmd == null){
        $cmd = webparam('cmd','view');
    }
    $input = count($input) ? $input : gallery_detail::parseValue();
    $state = count($state) ? $state : gallery_detail_state::parseValue('state');
    $paging = gallery_backlist::parseValue();
    $setstate = true;



    switch($cmd){
        
        #新規か、保存後どちらか --------------------------------------------------
        case 'view':{
            $id = $input['id'];
            $input = ($id)
                ? gallery_detail::getDetail($id)
                : gallery_detail::getNullMember();
        }break;

        #リロード ---------------------------------------------------------------
        case 'reload':{
            $id = $input['id'];
            $input = gallery_detail::getDetail($id);
        }break;

        #グループ一覧------------------------------------------------------------
        case 'list':{
            userload('group_edit/group_edit_view.php');
            echo group_edit_list($input['groupid'],GROUP_LIST_FOR_AJAXMODE);
            exit();
        }break;

        #グループ一覧------------------------------------------------------------
        case 'backlist':{
            redirect('admin.php?typex=gallery_list',$paging);
            exit();
        }break;


        #ソート(ローテート) -----------------------------------------------------
        case 'rotate':{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;
            $id = $input['id'];
            $groupid = $input['groupid'];
            $size   = $state['move_size'];
            $updown = $state['move_type'];
            echo gallery_detail::rotateItem($id, $groupid, $updown, $size);
            exit();
        }break;

       #保存 --------------------------------------------------------------------
        case 'saveback':{
            gallery_detail_act('save', $input, $state, false);
            if(count($state['err']) == 0){
                gallery_detail_act('backlist', $input, $state, false);
            }
            $setstate = false;
        }break;

       #保存 --------------------------------------------------------------------
        case 'save':{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;

            #変数のチェック
            $errval = gallery_detail::checkValue($input,$state);

            #エラーがあるかチェック
            if(count($errval) == 0){
                #エラーがなければ、データを保存する(ここではイメージを保存しない)
                $input = gallery_detail::saveValue($input);
            }

            #画像情報にエラーがなければアップロード(キャッシュ状態へ)
            if(!$errval['saveimgpath']){

                #キャッシュ状態にする
                if($_FILES['imgname']){
                    $upret = gallery_detail::upImageAsTmp($_FILES['imgname']);
                    $state['imgpath'] = $upret['subpath'];
                }

                #エラーがなければ、そのまま保存
                if(count($errval) == 0){
                    #保存が可能な場合、古いファイルがあれば、を削除する
                    if($input['id'] && $state['imgpath']){
                        $data = gallery_detail::getDetail($input['id']);
                        $fname = GALLERY_FIX_FOLDER . $data['saveimgpath'];
                        if($fname){
                            gallery_detail::removeImage($fname,false);
                        }
                        $fixpath = gallery_detail::fixImage($state['imgpath']);
                        $input['saveimgpath'] = $fixpath;
                        gallery_detail::saveImage($input['id'], $fixpath);

                        $state['imgpath'] = null;
                        $state['realpath'] = true;
                    }
                }
                #エラーがある場合、イメージ変更なし
                #イメージデータを取り出す
                elseif($input['id'] && $state['imgpath'] == null){
                    $data = gallery_detail::getDetail($input['id']);
                    $input['saveimgpath'] = $data['saveimgpath'];
                }

            }
        }break;

        #イメージの削除 ---------------------------------------------------------
        case 'imgremove':{
            $errval = gallery_detail::checkAuth();
            if(count($errval)) break;
            
            if(!$input['id']){
                #新規作成している場合は、一発削除する
                #データを復帰させる必要がないため
                gallery_detail::removeImage($state['imgpath'],true);
                $state['imgpath'] = null;
            }
            else{
                #元のデータ以外は一発削除
                $store = gallery_detail::getDetail($input['id']);
                if($store['saveimgpath'] != $state['imgpath']){
                    gallery_detail::removeImage($state['imgpath'],true);
                }
                $state['imgpath'] = null;
                $state['realpath'] = null;
                $state['remove'] = true;
            }
            
        }break;
    }
    if($setstate){
        $state['err'] = $errval;
        $state['paging'] = $paging;
    }

    #表示する場合だけ
    if($render){
        userload('gallery_detail/gallery_detail_view.php');
        $html = gallery_detail_view($input, $state);
    }
    return new plugin_result($html);
}

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function gallery_detail_tbl_setup($force = false){
    #必要なクラスを読込む
    userload('gallery_detail/gallery_detail_model.php');
    gallery_detail::createTable($force);
}

/**
 * 初期実行が可能か実行できるか(フレームワークから)
 * @return bool
 */
function gallery_detail_first() {
    return webauth(GALLERY_AUTH_VIEW);
}

/**
 * システムで使用できる権限の一覧(フレームワークから)
 * @return array
 */
function gallery_detail_auth() {
    $auth1 = array('key'=>GALLERY_LIST_AUTH_VIEW,'name'=>"画像管理：一覧表示",'info'=>"画像一覧を表示できる権限");
    $auth2 = array('key'=>GALLERY_AUTH_VIEW,'name'=>"画像管理：詳細表示",'info'=>"画像の詳細情報を表示できる権限");
    $auth3 = array('key'=>GALLERY_AUTH_EDIT,'name'=>"画像管理：編集",'info'=>"画像の詳細情報を編集できる権限");

    $auth4 = array('key'=>VIEWDETAIL_AUTH_VIEW,'name'=>"ギャラリー名：表示",'info'=>"設定画面を表示するできる権限");
    $auth5 = array('key'=>VIEWDETAIL_AUTH_EDIT,'name'=>"ギャラリー名：編集",'info'=>"ギャラリー名を編集できる権限");
    $auth6 = array('key'=>VIEW_AUTH_EDIT,'name'=>"ギャラリー設定：編集",'info'=>"ギャラリーを編集できる権限");
    $auth7 = array('key'=>VIEWDETAIL_AUTH_DETAIL,'name'=>'ギャラリー設定：詳細設定','info'=>'詳細設定を編集できる権限');
    $auth8 = array('key'=>GROUP_LIST_AUTH_VIEW,'name'=>"グループ管理：一覧表示",'info'=>"グループの一覧を表示できる権限");
    $auth9 = array('key'=>GROUP_AUTH_VIEW,'name'=>"グループ管理：詳細表示",'info'=>"グループの詳細情報を表示できる権限");
    $auth10 = array('key'=>GROUP_AUTH_EDIT,'name'=>"グループ管理：編集",'info'=>"グループの詳細情報を編集できる権限");
    $hash["ギャラリー"] = array($auth1,$auth2,$auth3,$auth4,$auth5,$auth6,$auth7,$auth8,$auth9,$auth10); return $hash;
    
     //機能オミット時はこちらのコメントアウトを外す
	/*$hash["ギャラリー"] = array($auth1,$auth2,$auth3);    return $hash;*/
}


/**
 * アカウントのメニュー(フレームワークから)
 */
function gallery_detail_menu($line,$admin,$title,$title_end = null) {
    #一覧データを読込む
    userload('gallery_list/gallery_list.php');

    #一覧ができたら使用しない
    if(webauth(GALLERY_LIST_AUTH_VIEW)){
        $top = rep($title, 'title', "ギャラリー");
        $ret1 = rep($line,'typex',"gallery_list");
        $ret1 = rep($ret1,'title',"画像管理");
        $ret1 = $top . $ret1;
        $ret = $ret1;
    }

    if(webauth(GROUP_LIST_AUTH_VIEW)){
        $ret2 = rep($line,'typex',"group_list");
        $ret2 = rep($ret2,'title',"グループ管理"); #管理
        $ret .= $ret2;
    }

     //アンで編集時はこちらのコメントアウトを外す
	if(webauth(GALLERY_AUTH_VIEW)){
        $ret3 = rep($line,'typex',"view_detail");
        $ret3 = rep($ret3,'title',"ギャラリー設定");
        $ret .= $ret3;
    }
    $ret .= $title_end;
    return $ret;
}



?>