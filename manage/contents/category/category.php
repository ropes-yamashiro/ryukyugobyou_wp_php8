<?php

require_once(dirname(__FILE__) . '/sql/category_info.php');
require_once(dirname(__FILE__) . '/sql/category_master.php');
require_once(dirname(__FILE__) . '/../document/sql/container_info.php');
require_once(dirname(__FILE__) . '/../document/sql/contents_detail.php');
require_once(dirname(__FILE__) . '/../document/sql/contents_info.php');


//===================================================
//  ユーザー機能



/** DB形式を出力形式に変換して出力 */
//AIRでは関係ない 階層→平坦化
function category_value($info){
	$list = category_list();
	
	//テンプレート 
	$text["page"] = file_get_contents(userpath(switchSpTemplate("category/html/input.html")));
	
	//共通値 //コマンドでいける
	$container_title = container_info_find_for_category();	//グループタイトル
    foreach( $container_title as $row ) {
        foreach ( $row as $key => $value ) {
            if ( $key == "id") {
                $container_id = $value;
            }
            if ( $key == "title") {
                $ct_list[$container_id] = $value;
            }
        }
    }

	
	//表示データ作成
	$data["contentsid"]	= array('type'=>"select", 'save'=>true	,'list'=>$ct_list		,'val'=>$info["contentsid"]);
	$data["err"]		= array('type'=>'text'	,'val'=>$info['err']);
	$data["typex"]		= array('type'=>'text'	,'val'=>$list["typex"]	, "hidden"=>true);
	$data["count"]		= array('type'=>'text'	,'val'=>$list["count"]	, "hidden"=>true);
	$data["pageno"]		= array('type'=>'text'	,'val'=>$list["pageno"]	, "hidden"=>true);
	$data["id"]			= array('type'=>'text'	, 'save'=>false	,'val'=>$info["id"]);
	$data["title"]		= array('type'=>'text'  , 'save'=>true	,'val'=>$info["title"]);
	$data["webtitle"]	= array('type'=>'text'  , 'save'=>true	,'val'=>$info["webtitle"]);
	$data["visible"]	= array('type'=>"check" , 'save'=>true	,'list'=>$lyesno	,'val'=>$info["visible"]);

    $page = category_html_view($data,$text);

	return $page;
}

/** データのテキスト置換を行う */
function category_html_view($data,$text){
	$page =  $text["page"];
	$old_data = array();
	$hidden = array();
	foreach($data as $key => $val){
		if($val["type"] == "select") $page = rep($page,$key,html_ophash2($val['list'],$val["val"]));
		if($val["type"] == "text"){
			$tval = ($val["br"]) ? html_br($val["val"]) : $val["val"];
			$page = rep($page,$key,$tval);
		}
		if($val["type"] == "check") { $page = html_check($page,$key,$val['list'],$val["val"]);}
		if($val["type"] == "picture"){
			if($val[dir]){
				$path = $val[dir] . $val["val"];
				$pic  = html_imagetype($val["name"],$val["fname"],$val["no"],$path);
				$page = rep($page,$key,$pic);
			}
			else $page = rep($page,$key,html_imagetype($val["name"],$val["fname"],"",$val["no"],""));
		}
		if($val["type"] == "each"){
			$vtext[page] = $val[text]; $item = "";
			foreach($val["val"] as $vval){ $item .= html_view($vval,$vtext);}
			$page = rep($page,$key,$item);
		}
		if($val["type"] == "table"){
			$vtext["page"] = $val["text"]; $item = $val["label"];
			foreach($val["val"] as $vval){ $item .= html_view($vval,$vtext);}
			$page = rep($page,$key,$item);
		}
		if($val["hidden"] == true) $hidden[$key] = $val["val"];
		if($val["save"] == true)	  $old_data[$key. "_old"] = $val["val"];
	}
	$page = rep($page,"old_data",html_hidden(count($old_data)	? $old_data	: array()));
	$page = rep($page,"hidden"  ,html_hidden(count($hidden)		? $hidden		: array()));

	return $page;
}

/** フォームの値をDB形式に変換します */
function category_form(){

	//formのvalueと整合性が取れている必要がある
	$hash = array("id"=>null,"title"=>null,"webtitle"=>null,"contentsid"=>null,"visible"=>null);
	
	//関数化可能
	foreach($hash as $key => $val){
		$ret[$key] = webparam($key,$val);
	}
	return $ret;
}

/** データベースへの書き込み */
function category_save(){
	
	$form = category_form();

    if(!$form["id"]){
		$id = category_master_insert($form);
		$form[sort] = $id;
		$form[id] = $id;
        category_master_update($form);

	}else{
		category_master_update($form); //データの更新
		$id = $form["id"];
	}
	
	return $id;
}

/** データベースからデータの読み込み */
function category_load($id){
	$info = category_master_find($id);
	return $info;
}






/** リストの値をDB形式に変換します */
function category_list(){
	$hash = array("pid"=>1,"typex"=>"category","count"=>20,"pageno"=>1,"contentsid"=>1);
	foreach($hash as $key => $val){
		$ret[$key] = webparam($key,$val);
	}
	return $ret;
}

/** 表示部分?外部環境に依存しないようにする */
function category_tolist($data=array(),$rule = array(),$text=array()){

	//値の準備====================================================
	//ルール部分
	$rule = whash(category_list(),$rule);

    //テンプレート部分--------------------------------------------

	$temp		= new tLoader(userpath(switchSpTemplate("category/html/list.html")));
	$tbase		= $temp->getData();
	$text		= whash($tbase,$text);			
	
	//データ部分---------------------------------------------------
	$err	= ifrep($errview,"value",$data["err"]);
	
	//使用変数
	$pid = $rule["pid"];$pageno = $rule["pageno"];$count = $rule["count"];

    // コンテンツタイトル取得
	$container_title = container_info_find_for_category();
    foreach( $container_title as $row ) {
        foreach ( $row as $key => $value ) {
            if ( $key == "id") {
                $container_id = $value;
            }
            if ( $key == "title") {
                $ct_list[$container_id] = $value;
            }
        }
    }


	//ページカウント
	$table		= category_master_limit($rule["contentsid"],($pageno - 1) * $count,$count);

	$sel_list = webparam('print',array());


	for($i = 0 ; $i < count($table) ; $i++){
		$table[$i]["pid"] = $pid;	
		$table[$i]["visible"] = $table[$i]["visible"] == 1 ? 'checked="checked"' : "";
		$table[$i]["typex"]= $rule["typex"];
		
        $table[$i]["contentsname"] = $ct_list[$table[$i]["contentsid"]];


        if($data["sel"] == "all") $selv = 'checked="checked"';
		elseif($data["sel"] == "off") $selv = '';
		else{$selv = in_array($table[$i]["id"],$sel_list) ? 'checked="checked"' : ""; } 
		$table[$i]["print"] = $selv ;
	}
	
	//表-テキスト
	foreach($table as $row){
		if(!$i) $i = 0;
		$row["color"] = $i % 2 + 1; $i++;
		$row["class"] = "category".str_pad($row["id"], 2, "0", STR_PAD_LEFT);

		$ttable .= ephash($row,$text["line"]);
	}
	if(count($table) == 0) $ttable = $text["noval"];
	
	//ページング
	$tcount	= category_master_count($rule["contentsid"]);
	$tcount = $tcount . "件";
//	if($tcount) $tcount = $tcount . "件";
	$count_st		= array(10=> "10件",20=>"20件",50=>"50件",100=>"100件");	
	$paging		= html_paging($tcount,$rule["count"],$rule["pageno"],$text["paging"],$text["paging_current"]);

	//ルール部分==================================================
	$out["pid"]		= array('type'=>'text'	,'val'=>$rule["pid"]	, "hidden"=>true);
	$out["typex"]		= array('type'=>'text'	,'val'=>$rule["typex"]	, "hidden"=>true);
	$out["count"] 	= array('type'=>"select",'val'=>$rule["count"] , 'list'=> $count_st );
	$out["contentsid"] 	= array('type'=>"select",'val'=>$rule["contentsid"] , 'list'=> $ct_list );
	$out["pageno"]	= array('type'=>'text'	,'val'=>$rule["pageno"]	, "hidden"=>true);	
	
	//表示部分====================================================
	$out["tcount"]	= array('type'=>'text'	,'val'=>$tcount);			//DB呼び出し？
	$out["err"]		= array('type'=>'text'	,'val'=>$err);			//実行タイミング？
	$out["name"]		= array('type'=>'text'	,'val'=>"カテゴリー設定");		//作成タイミング？
	$out["paging"]	= array('type'=>'text'	,'val'=>$paging);

	//リスト部分==================================================
	$out["table"]		= array('type'=>'text'	,'val'=>$ttable);				//てーぶる部分

    return category_html_view($out,$text);
	
}
//#end

function category_check_err() {
    $p = $_POST;
    $mes = $p['title'] == null ? "カテゴリー名を入力してください。<br />" : "";
    $mes .= $p['webtitle'] == null ? "表示用タイトルを入力してください。<br />" : "";

    if ( !empty( $mes ) ) {
        $mes = "<font color=\"red\">".$mes."</font><br />";
    }

    return $mes;
}

//===================================================
//  定義機能


/** 表示スタイルを変更する */
function category_act_chtype(){
	//#edit,func_chtype
	$info = category_form();
	return category_value($info);
	//#end
}

/** データを新規作成する */
function category_act_newitem(){
	//#edit,func_newitem
	return category_act_detail();
	//#end
}

/** データを編集して、一覧に戻る。 */
function category_act_savelist(){
	//#edit,func_savelist
    $err = category_check_err();
	category_save();
	return category_act_listview();
	//#end
}

/** データを保存する */
function category_act_save(){
	//#edit,func_save
    $err = category_check_err();
    if ( empty( $err ) ) {
        $id = category_save();
        $info = category_load($id);//★★★
    } else {
        $info['id'] = "";
        $info['contentsid'] = $_REQUEST['contentsid'];
        $info['visible'] = $_REQUEST['visible'];
        $info['title'] = $_REQUEST['title'];
        $info['webtitle'] = $_REQUEST['webtitle'];
        $info['err'] = $err;
        
    }
    return category_value($info);
	//return category_value(category_form());
	//#end
}

/** データを編集する */
function category_act_detail(){
	//#edit,func_detail
	$info = category_load(webparam("param"));//★★★

	return category_value($info);
	//#end
}

/** 全選択する */
function category_act_selall(){
	//#edit,func_selall
	$data = array("sel"=>"all");
	return category_tolist($data);
	//#end
}

/** 選択解除 */
function category_act_seloff(){
	//#edit,func_seloff
	$data = array("sel"=>"off");
	return category_tolist($data);
	//#end
}

/** @データを表示する */
function category_act_listview($err=null){
	//#edit,func_listview
	return category_tolist();
	//#end
}

/** @データを上に移動する */
function category_act_up_line(){
	//#edit,func_up_line
	$param = webparam("param");
	$pid   = webparam("contentsid");
	if($param == null || $pid == null) return category_act_listview("存在しないデータを移動しようとしました");
	$table = category_master_limit_by_up_line($pid,$param,0);
	if(count($table) == 1) return category_act_listview();
	$id1 = $table[0]["id"];
	$sort1 = $table[0]["sort"];
	$id2 = $table[1]["id"];
	$sort2 = $table[1]["sort"];
	category_master_update(array("id"=>$id1,"sort"=>$sort2));
	category_master_update(array("id"=>$id2,"sort"=>$sort1));
	return category_act_listview();
	//#end
}

/** @データを下に移動する */
function category_act_down_line(){
	//#edit,func_down_line
	$param = webparam("param");
	$pid   = webparam("contentsid");
	if($param == null || $pid == null) return category_act_listview("存在しないデータを移動しようとしました");
	$table = category_master_limit_by_down_line($pid,$param,0);
	if(count($table) == 1) return category_act_listview();
	$id1 = $table[0]["id"];
	$sort1 = $table[0]["sort"];
	$id2 = $table[1]["id"];
	$sort2 = $table[1]["sort"];
	category_master_update(array("id"=>$id1,"sort"=>$sort2));
	category_master_update(array("id"=>$id2,"sort"=>$sort1));
	return category_act_listview();
	//#end
}

/** @表示を切り替える */
function category_act_ch_bool(){
	//#edit,func_ch_bool
	$param = webparam("param");
	$value = webparam("id" . $param) ? 1 : 0;
	if($param === null)	return category_act_listview("存在しないデータを変更しようとしました");
	category_master_update(array("id"=>$param,"visible"=>$value));
	return category_act_listview();
	//#end
}

/** @データを消去する */
function category_act_del_line(){
	//#edit,func_del_line
	$param = webparam("param");
	if($param == null)	return category_act_listview("存在しないデータを削除しようとしました");
	category_master_delete($param);
	
	return category_act_listview();
	//#end
}

/** データを追加する */
function category_act_setup(){
	//#edit,func_setup
	
	category_db_setup();
	//#end
}




//===================================================
//  フレームワーク機能

function category_act(){
	$cmd = webparam("cmd");
	//#edit,act

    //ここだけあればよさそうな感じ★★★
	if($cmd == null)$cmd = "listview";
	//#end
	switch($cmd){
		case "delfile" :
		//#edit,act_delfile,ファイルを削除する
			return new plugin_result(category_act_delfile());
		//#end
		break; 
		case "chtype" :
		//#edit,act_chtype,表示スタイルを変更する
			return new plugin_result(category_act_chtype());
		//#end
		break; 
		case "newitem" :
		//#edit,act_newitem,データを新規作成する
			return new plugin_result(category_act_newitem());
		//#end
		break; 
		case "savelist" :
		//#edit,act_savelist,データを編集して、一覧に戻る。
			return new plugin_result(category_act_savelist());
		//#end
		break; 
		case "save" :
		//#edit,act_save,データを保存する
			return new plugin_result(category_act_save());
		//#end
		break; 
		case "detail" :
		//#edit,act_detail,データを編集する
			return new plugin_result(category_act_detail());
		//#end
		break; 
		case "selall" :
		//#edit,act_selall,全選択する
			return new plugin_result(category_act_selall());
		//#end
		break; 
		case "seloff" :
		//#edit,act_seloff,選択解除
			return new plugin_result(category_act_seloff());
		//#end
		break; 
		case "listview" :
		//#edit,act_listview,データを表示する
			return new plugin_result(category_act_listview());
		//#end
		break; 
		case "up_line" :
		//#edit,act_up_line,データを上に移動する
			return new plugin_result(category_act_up_line());
		//#end
		break; 
		case "down_line" :
		//#edit,act_down_line,データを下に移動する
			return new plugin_result(category_act_down_line());
		//#end
		break; 
		case "ch_bool" :
		//#edit,act_ch_bool,表示を切り替える
			return new plugin_result(category_act_ch_bool());
		//#end
		break; 
		case "del_line" :
		//#edit,act_del_line,データを消去する
			return new plugin_result(category_act_del_line());
		//#end
		break; 
		case "setup" :
		//#edit,act_setup,データを追加する
			return new plugin_result(category_act_setup());
		//#end
		break; 

	}
}


/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function category_tbl_setup($force = false) {
    if ($force) {
        category_master_reset_table();
        category_info_reset_table();
    }
    category_master_create_table();
    category_info_create_table();
}

/* このプラグインで割り当てられる権限一覧 */
function category_auth(){
/*
	//#edit,auth

	//#end
	$auth1 = array(key=>"category",name=>"設定",info=>"ドキュメントのカテゴリーの設定が可能になります。");
	$hash["カテゴリー設定"] = array($auth1);
	return $hash;
*/
}	

/* 表示するメニュー設定 */
function category_menu($line,$admin,$title){

    //#edit,menu
	if(!category_info_exist_table()){
		category_db_setup();
	}
/*
	if(!webauth(category)) return;
	$one = rep($line,typex,"category");
	$ret .= rep($one,title,"カテゴリー設定");
	return $ret;
*/
}

/* 権限があるかどうか、引数を指定しない場合、アクセスできるIDを返す */
function category_first($pid = null){

	if($pid != null){
		if(webauth(category . $pid)) return $pid;
		return false;
	}
	//#edit,first
	if(!container_info_exist_table()){
		category_db_setup();
	}
	//#end
	$sql = container_info_sql();
	$info = container_info_select_all();	

	foreach($info as $row){
		if(webauth(category . $row["id"])) return $row["id"];
	}
	return false;

}


/** DBのセットアップを行う */
function category_db_setup(){
	if(!category_info_exist_table()){
		category_info_create_table();
        $code = 'category_info_insert_code';
        if (function_exists($code))
            $code();
	}
	if(!category_master_exist_table()){
		category_master_create_table();
        $code = 'category_master_insert_code';
        if (function_exists($code))
            $code();
	}

}



?>
