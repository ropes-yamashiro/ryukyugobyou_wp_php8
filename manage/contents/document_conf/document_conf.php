<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================


//===================================================
//  ユーザー機能

//#edit
require_once(dirname(__FILE__) . '/../document/sql/container_info.php');
require_once(dirname(__FILE__) . '/../document/sql/contents_detail.php');
require_once(dirname(__FILE__) . '/../document/sql/contents_info.php');

/** データのテキスト置換を行う */
function document_conf_view($rule,$data,$text){
	$page =  $text["page"];
	foreach($data as $key => $val){
		if($val['type'] == 'select') $page = rep($page,$key,html_ophash2($val['list'],$val['val']));
		if($val['save']) $old_data[$key . '_old'] = $val['val'];
	}
	$page = rep($page,'old_data',html_hidden($old_data));
	return $page;
}

/** DB形式を出力形式に変換して出力 */
function document_conf_value($info){

	//テンプレート
	$text['page'] = file_get_contents(userpath(switchSpTemplate("document_conf/view.html")));

	//一覧の部分
	$db = container_info_select_all();
	foreach($db as $bone){
		if(webauth('document' . $bone['id'])) $linfo[$bone['id']] = $bone['title'];
	}		//DBから

	$conf = document_couf_ini_frame_all(confpath("document/config.ini"));

	foreach($conf as $cone){ $ltemp[$cone['no']] = $cone['label'];}		//設定ファイルから

        $pconf = document_couf_ini_paging_all(confpath("document/config.ini"));

	foreach($pconf as $pone){ $ptemp[$pone['no']] = $pone['label'];}
	$lpaging = $ptemp;
	$lcount  = numhash(1,100);//プログラムから
	$listcount = numhash(1,50);

	//表示データ作成
	$data['list_count']         = array('type'=>'select', 'save'=>true, 'list'=>$listcount  ,'val'=>$info['list_count']);
	$data['list_count_sp']      = array('type'=>'select', 'save'=>true, 'list'=>$listcount  ,'val'=>$info['list_count_sp']);
	$data['id']                 = array('type'=>'select', 'save'=>true, 'list'=>$linfo      ,'val'=>$info['id']);               //コンテンツレベル
	$data['container_temp_id']  = array('type'=>'select', 'save'=>true, 'list'=>$ltemp	,'val'=>$info['container_temp_id']);//コンテナ用 テンプレート
	$data['paging_temp_id']     = array('type'=>'select', 'save'=>true, 'list'=>$lpaging	,'val'=>$info['paging_temp_id']);   //ページングタイプ
	$data['paging_count']       = array('type'=>'select', 'save'=>true, 'list'=>$lcount	,'val'=>$info['paging_count']);     //ページング数(PC)
	$data['paging_count_sp']    = array('type'=>'select', 'save'=>true, 'list'=>$lcount	,'val'=>$info['paging_count_sp']);     //ページング数(SP)
	return document_conf_view($rule,$data,$text);
}

/** フォームの値をDB形式に変換します */
function document_conf_form(){
	$ret["id"] = webparam("id",1);
	$ret["container_temp_id"] = webparam("container_temp_id");
	$ret["paging_temp_id"] = webparam("paging_temp_id");
	$ret["paging_count"] = webparam("paging_count");
	$ret["list_count"] = webparam("list_count");
	$ret["paging_count_sp"] = webparam("paging_count_sp");
	$ret["list_count_sp"] = webparam("list_count_sp");
	return $ret;
}

function document_couf_ini_image($path){
	$conf = new cssConfig($path);
	for($i = 1; $i <= 50 ;$i++){
		$sel = $conf->getSelector("image" .$i);
		if($sel === null) continue;
		$one = array("x"=>$sel->getValue("x"), "y"=>$sel->getValue("y"),"save-path"=>$sel->getValue("save-path"));
		$ret[$i] = $one;
	}
	return $ret;
}

function document_couf_ini_frame_all($path){

	$conf = new cssConfig($path);
	for($i = 1; $i <= 50 ;$i++){
		$sel = $conf->getSelector('frame_temp' .$i);
		if($sel === null) continue;
		$one = array(
			'no'=>$i,
			'file'=>$conf->getFilepath($sel->getValue('file')),
			'file_ajax'=>$conf->getFilepath($sel->getValue('file_ajax')),
			'file_sp'=>$conf->getFilepath($sel->getValue('file_sp')),
			'file_sp_ajax'=>$conf->getFilepath($sel->getValue('file_sp_ajax')),
			'label'=>$sel->getValue('label')
		);
		$ret[$i] = $one;
	}
	return $ret;
}

function document_couf_ini_paging_all($path){
	$conf = new cssConfig($path);
	for($i = 1; $i <= 50 ;$i++){
		$sel = $conf->getSelector("paging_temp" .$i);
		if($sel === null) continue;
		$one = array("no"=>$i,"file"=>$conf->getFilepath($sel->getValue("file"))
				,"label"=>$sel->getValue("label"),"style"=>$sel->getValue("style"));
		$ret[$i] = $one;
	}
	return $ret;
}

function document_couf_ini_text_all($path){
	$conf = new cssConfig($path);
	for($i = 1; $i <= 50 ;$i++){
		$sel = $conf->getSelector('text_temp' .$i);
		if($sel === null) continue;
		$one = array('no'=>$i
		        ,'file'=>$conf->getFilepath($sel->getValue('file'))
				,'label'=>$sel->getValue('label')
				,'style'=>$sel->getValue('style')
				,'max-y'=>$sel->getValue('max-y')
				,'max-x'=>$sel->getValue('max-x')
				,'text-only'=>$sel->getValue('text-only'),'text-image'=>$sel->getValue('text-image')
				,'image-left'=>$sel->getValue('image-left'),'image-right'=>$sel->getValue('image-right')
				,'image-left-text'=>$sel->getValue('image-left-text')
				,'image-right-text'=>$sel->getValue('image-right-text')
				,'image-none-text'=>$sel->getValue('image-none-text')
				,'frame-x'=>$sel->getValue('frame-x')
				,'frame-y'=>$sel->getValue('frame-y')
				,'margin-img-right'=>$sel->getValue('margin-img-right')
		        ,'file_sp'=>$conf->getFilepath($sel->getValue('file_sp'))
				,'label_sp'=>$sel->getValue('label_sp')
				,'style_sp'=>$sel->getValue('style_sp')
				,'max-y_sp'=>$sel->getValue('max-y_sp')
				,'max-x_sp'=>$sel->getValue('max-x_sp')
				,'text-only_sp'=>$sel->getValue('text-only_sp')
				,'text-image_sp'=>$sel->getValue('text-image_sp')
				,'image-left_sp'=>$sel->getValue('image-left_sp')
				,'image-right_sp'=>$sel->getValue('image-right_sp')
				,'image-left-text_sp'=>$sel->getValue('image-left-text_sp')
				,'image-right-text_sp'=>$sel->getValue('image-right-text_sp')
				,'image-none-text_sp'=>$sel->getValue('image-none-text_sp')
				,'frame-x_sp'=>$sel->getValue('frame-x_sp')
				,'frame-y_sp'=>$sel->getValue('frame-y_sp')
				,'margin-img-right_sp'=>$sel->getValue('margin-img-right_sp')
				,'category-tag'=>$sel->getValue('category-tag')
				,'category-link'=>($sel->getValue('category-link')=="true" ? true : false)
				,'summary-length'=>$sel->getValue('summary-length')
				,'image-count' => $sel->getValue('image-count')
		);
		$ret[$i] = $one;
	}
	return $ret;
}
//#end

//===================================================
//  定義機能

/** データを表示する */
function document_conf_act_view(){
	//#edit,func_view
	$db = container_info_select_all();
	//fb($db);
	foreach($db as $done){
		if(webauth('document' . $done['id'])){//fb(here,id);
			$id = $done['id']; break;
		}
	}
	return count($db) ? document_conf_value(container_info_find($id)) : "外枠テンプレートがありません";
	//#end
}

/** データを保存する */
function document_conf_act_save(){
	//#edit,func_save
	$info = document_conf_form();
	container_info_update($info);
	return document_conf_value($info);
	//#end
}

/** コンテナを切り替える */
function document_conf_act_ch_container(){
	//#edit,func_ch_container
	$info = document_conf_form();
	return document_conf_value(container_info_find($info["id"]));
	//#end
}

/** データベースを修正する */
function document_conf_act_updb(){
	//#edit,func_updb
	//container_info_add_list_count();
	return document_conf_act_view();
	//#end
}




//===================================================
//  フレームワーク機能

function document_conf_act(){
	$cmd = webparam("cmd");
	//#edit,act
	if($cmd == null) $cmd = 'view';
	//#end
	switch($cmd){
		case "view" :
		//#edit,act_view,データを表示する
			return new plugin_result(document_conf_act_view());
		//#end
		break;
		case "save" :
		//#edit,act_save,データを保存する
			return new plugin_result(document_conf_act_save());
		//#end
		break;
		case "ch_container" :
		//#edit,act_ch_container,コンテナを切り替える
			return new plugin_result(document_conf_act_ch_container());
		//#end
		break;
		case "updb" :
		//#edit,act_updb,データベースを修正する
			return new plugin_result(document_conf_act_updb());
		//#end
		break;

	}
}

/* このプラグインで割り当てられる権限一覧 */
function document_conf_auth(){
	//#edit,auth

	//#end
}

/* 表示するメニュー設定 */
function document_conf_menu($line,$admin,$title){

}

/* container_info */
function document_conf_first($pid = null){
	//#edit,first

	//#end
	return webauth(document_conf);
}






?>
