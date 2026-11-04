<?php
#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History: 2009/07/09 文字列が変換できなかった問題を修正
#==================================
//require_once('DB.php');

function db_hone($sql,$hash=array()){
	$table = db_htable($sql,$hash);
	return count($table) > 0 ? $table[0] : array();	
}

function db_htable($sql,$hash=array()){
	$sql= db_line($sql,$hash);
	return db_ltable($sql);
}

function db_tosjis_table($table){
	$table = toArray($table);
	$ret = array();
	foreach($table as $row){
		$ret[] = db_tosjis_row($row);
	}
	return $ret;
}

function db_tosjis_row($row){
	$row = toArray($row);
	$ret = array();
	foreach($row as $key => $value){
		$ret[$key] = mb_convert_encoding($value,'SJIS-win','UTF-8');
	}	
	return $ret;
}

function db_lone2($sql,$dsnv){
	$db = DB::connect($dsnv);
	$param = db_param(func_get_args(),2);
	$res = db_tosjis_table($db->getAll($sql,$param));
	if (DB::isError($res)){
		dp($res->getMessage() . ":" . $sql);
	}
	$db->disconnect();
	if(count($res) > 0) return $res[0];
	return null;
}

function db_lone($sql){
	$db = DB::connect(dsn);
	$param = db_param(func_get_args());
	$res = db_tosjis_table($db->getAll($sql,$param));
	if (DB::isError($res)){
		dp($res->getMessage() . ":" . $sql);
	}
	$db->disconnect();
	if(count($res) > 0) return $res[0];
	return null;
}

function db_hquery($sql,$dsn1,$hash=array()){
	$db = DB::connect($dsn1);
	$sql = db_line($sql,$hash);
	$param = db_param(func_get_args());
	$res = db_tosjis_table($db->query($sql));
	$db->disconnect();
	return $res;
}

function db_query($dsn1,$sql){
	$db = DB::connect($dsn1);
	$param = db_param(func_get_args());
	$res = db_tosjis_table($db->query($sql));
	if (DB::isError($res)){
		dp($res->getMessage() . ":" . $sql);
	}
	$db->disconnect();
	return $res;
}

function db_ltable3($sql,$dsnv,$param){
	$db = DB::connect($dsnv);
	$res = db_tosjis_table($db->getAll($sql,$param));
	if (DB::isError($res)){
		dp($res->getMessage() . ":" . $sql);
	}
	$db->disconnect();
	return $res;
}

function db_ltable2($sql,$dsnv){
	$db = DB::connect($dsnv);
	$param = db_param(func_get_args(),2);
	$res = db_tosjis_table($db->getAll($sql,$param));
	
	if (DB::isError($res)){
		dp($res->getMessage() . ":" . $sql);
	}
	$db->disconnect();
	return $res;
}

function db_ltable($sql){
	$db = DB::connect(dsn);
	$param = db_param(func_get_args());
	$res = db_tosjis_table($db->getAll($sql,$param));
	if (DB::isError($res)){
		dp($res->getMessage() . ":" . $sql);
	}
	$db->disconnect();
	return $res;
}

function db_hinsert($sql,$hash=array()){
	$sql = db_line($sql,$hash);
	return db_linsert($sql);
}

function db_hinsert_id($sql,$key,$hash=array(),$id= "id"){
	$sql = db_line($sql,$hash);
	return db_linsert_id($sql,$key,$id);
}

function db_linsert_id($sql,$key,$id){
	$db = DB::connect(dsn);
	$param = db_param(func_get_args(),3);
	$number =  $db->nextId($key);
	$sql = db_line($sql,array($id=> $number));
	//$res = $db->query($sql);
	//dp($param);
	$res = db_tosjis_table($db->getAll($sql,$param));
	if (DB::isError($res)){
		//dp($res);
		dp($res->getMessage() . ":" . $sql);
	}
	$db->disconnect();
	return $number;
}


function db_linsert($sql){
	$db = DB::connect(dsn);
	$param = db_param(func_get_args());
	$res = db_tosjis_table($db->getAll($sql,$param));
	
	//リファクタ対象
	$id = mysql_insert_id();
	$db->disconnect();
	return $id;
}

function db_param($arg,$count=1){
	$c = count($arg);
	return 	($c > $count) ? array_slice($arg,$count,$c - $count) : array();
}

function db_line($sql,$hash=array()){
	$code = "SJIS-win";
	$sql = mb_convert_encoding($sql,"UTF-8",$code);
	#file_put_contents(dirname(__FILE__) . '/../sql.txt'  ,"\r\n+++++++++++++++++++++\r\n" .$sql , FILE_APPEND);	

	foreach($hash as $key => $value){ 
		if($value === null) $value = "";
		if(!is_scalar($value)) continue;
		
		$key = mb_convert_encoding("{" . trim($key) . "}","UTF-8",$code);
		$value = mb_convert_encoding($value,"UTF-8",$code);
		$after = sqlite_escape_string($value);
		$sql = str_replace($key,$after,$sql);


		#file_put_contents(dirname(__FILE__) . '/../sql.txt'  , "({$key}){$value} => {$after} \r\n", FILE_APPEND);
	}
	#file_put_contents(dirname(__FILE__) . '/../sql.txt'  , $sql . "\r\n---------------------------\r\n", FILE_APPEND);
	return $sql;	
}

?>