<?php

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================
#userload("topic/topic.php");
#userload("mail/mail.php");
userload("document/document.php");
userload("movie/movie.php");

/** すべての情報を表示する */
function tools_topic_all() {
    $nline = '<tr><td>{id}</td><td><input type=text name=label{id} value="{name}" /></td><td>{op}</td></tr>';
    $oline1 = '<a href="?" onclick="return selector(\'del\',{id});" >削除</a>';
    $oline2 = '　<a href="?" onclick="return selector(\'ch\',{id});" >変更</a>';
    $new = '<a href="?" onclick="return selector(\'new\',0);">追加</a><br>';


    $nline = rep($nline, op, $oline1 . $oline2);

    $db = getDB();
    $res = $db->QueryRow("SELECT id,name FROM topic_info");
    foreach ($res as $row) {
        $ret .= ephash($row, $nline);
    }
    $var = '<strong>新着情報メニュー編集</strong><br><a href="?type=topic">新着情報メニュー編集</a>　<a href="?type=mail">お問い合わせメニュー編集</a>　<a href="?type=document">ドキュメントメニュー編集</a>　<a href="?type=movie">動画管理メニュー編集</a>　<a href="?type=topicimg">トピック画像メニュー編集</a>　<a href="?type=customer">お客様の声メニュー編集</a>　<a href="?type=icon">アイコン画像メニュー編集</a>　<a href="?type=hintpasswd">アクセス制限メニュー編集</a><hr>
			<br><form action="?" method="POST" name=formx>
			<input type=hidden name="type" value="topic">
			<input type=hidden name="param">
			<input type=hidden name="cmd">
			<input type=text name="newname" >{new}
			<br><table><tr><th>トピック番号</th><th>ラベル名</th><th>操作</th></tr>{table}</table></form>';

    $var = rep($var, 'table', $ret);
    $var = rep($var, "new", $new);
    return $var;
}

/** 特定のメニューを削除する */
function tools_topic_del($id) {

    $db = getDB();

    $sql = 'SELECT file FROM topic_file WHERE pid=:pid';
    $res = $db->QueryRow($sql, array('pid' => $id));
    foreach ($res as $row) {

        $file = $row['file'];
        if (file_exists($file))
            unlink($file);
    }
    $db->ExecuteRow('DELETE FROM topic WHERE groupid=:id', array('id' => $id));
    $db->ExecuteRow('DELETE FROM topic_info WHERE id=:id', array('id' => $id));
}

/** メニューを追加する */
function tools_topic_add($name) {
    $db = getDB();
    for ($i = 1; $i < 10; $i++) {
        $count = $db->Count("SELECT count(*) FROM topic_info WHERE id=:id", array('id' => $i));
        if ($count > 0)
            continue;
        else
            break;
        if ($i == 10)
            die("データの数が多すぎます");
    }

    $db->ExecuteRow("INSERT INTO topic_info (id,name) VALUES(:id,:name)",array('id'=>$i,'name'=>$name));
}

/** ラベル名を編集する */
function tools_topic_rename($id, $name) {
    $db = getDB();
    $db->ExecuteRow('UPDATE topic_info SET name = :name WHERE id=:id', array('id'=>$id,'name'=>$name));
}

////////////////////////////////////////////////////

/** すべての情報を表示する */
function tools_mail_all() {
	$nline = '<tr><td>{id}</td><td><input type=text name=label{id} value="{name}" /></td><td>{op}</td></tr>';
	$oline1 = '<a href="?" onclick="return selector(\'del\',{id});" >削除</a>';
	$oline2 = '　<a href="?" onclick="return selector(\'ch\',{id});" >変更</a>';
	$new = '<a href="?" onclick="return selector(\'new\',0);">追加</a><br>';


	$nline = rep($nline,'op', $oline1 . $oline2);

        $db = getDB();
	$res = $db->QueryRow("SELECT id,AdminTitle as name FROM MailInfo");
	foreach($res as $row){
		$ret .= ephash($row,$nline);
	}
	$var = '<strong>お問い合わせメニュー編集</strong><br><a href="?type=topic">新着情報メニュー編集</a>　<a href="?type=mail">お問い合わせメニュー編集</a>　<a href="?type=document">ドキュメントメニュー編集</a>　<a href="?type=movie">動画管理メニュー編集</a>　<a href="?type=topicimg">トピック画像メニュー編集</a>　<a href="?type=customer">お客様の声メニュー編集</a>　<a href="?type=icon">アイコン画像メニュー編集</a>　<a href="?type=hintpasswd">アクセス制限メニュー編集</a><hr>
			<br><form action="?" method="POST" name=formx>
			<input type=hidden name="type" value="mail">
			<input type=hidden name="param">
			<input type=hidden name="cmd">
			<input type=text name="newname" >{new}
			<br><table><tr><th>トピック番号</th><th>ラベル名</th><th>操作</th></tr>{table}</table></form>';

	$var = rep($var,'table',$ret);
	$var = rep($var,"new",$new);
	return $var;
}

/** 特定のメニューを削除する */
function tools_mail_del($id) {

	//メニューの削除
        $db = getDB();
	$db->ExecuteRow("DELETE FROM MailInfo WHERE id=:id",array('id'=>$id));
}

/** メニューを追加する */
function tools_mail_add($name) {
	$db = getDB();
	$db->ExecuteRow("INSERT INTO MailInfo(AdminTitle) VALUES(:name);",array('name'=>$name));
}

/** ラベル名を編集する */
function tools_mail_rename($id, $name) {
	$db = getDB();
	$res = $db->ExecuteRow("UPDATE MailInfo SET AdminTitle = :name WHERE id=:id;",array('id'=>$id,'name'=>$name));
}

////////////////////////////////////////////////////

/** すべての情報を表示する */
function tools_document_all() {
    $nline = '<tr><td>{id}</td><td><input type=text name=label{id} value="{name}" /></td><td>{op}</td></tr>';
    $oline1 = '<a href="?" onclick="return selector(\'del\',{id});" >削除</a>';
    $oline2 = '　<a href="?" onclick="return selector(\'ch\',{id});" >変更</a>';
    $new = '<a href="?" onclick="return selector(\'new\',0);">追加</a><br>';

    userload("document/document.php");

    $nline = rep($nline, 'op', $oline1 . $oline2);
    if (!container_info_exist_table()) {
        document_db_setup();
    }

    $res = container_info_select_all();
    foreach ($res as $row) {
        $tline = rep($nline, 'id', $row['id']);
        $ret .= rep($tline, 'name', $row['title']);
        //$ret .= ephash($row,$nline);
    }
    $var = '<strong>ドキュメントメニュー編集</strong><br><a href="?type=topic">新着情報メニュー編集</a>　<a href="?type=mail">お問い合わせメニュー編集</a>　<a href="?type=document">ドキュメントメニュー編集</a>　<a href="?type=movie">動画管理メニュー編集</a>　<a href="?type=topicimg">トピック画像メニュー編集</a>　<a href="?type=customer">お客様の声メニュー編集</a>　<a href="?type=icon">アイコン画像メニュー編集</a>　<a href="?type=hintpasswd">アクセス制限メニュー編集</a><hr>
			<br><form action="?" method="POST" name=formx>
			<input type=hidden name="type" value="document">
			<input type=hidden name="param">
			<input type=hidden name="cmd">
			<input type=text name="newname" >{new}
			<br><table><tr><th>トピック番号</th><th>ラベル名</th><th>操作</th></tr>{table}</table></form>';

    $var = rep($var, 'table', $ret);
    $var = rep($var, "new", $new);
    return $var;
}

/** 特定のメニューを削除する */
function tools_document_del($id) {
    container_info_delete($id);
}

/** メニューを追加する */
function tools_document_add($name) {
    container_info_insert(array('title' => $name));
}

/** ラベル名を編集する */
function tools_document_rename($id, $name) {
    container_info_update(array('id' => $id, 'title' => $name));
}


/** すべての情報を表示する */
function tools_movie_all() {
    $nline = '<tr><td>{id}</td><td><input type=text name=label{id} value="{name}" /></td><td>{op}</td></tr>';
    $oline1 = '<a href="?" onclick="return selector(\'del\',{id});" >削除</a>';
    $oline2 = '　<a href="?" onclick="return selector(\'ch\',{id});" >変更</a>';
    $new = '<a href="?" onclick="return selector(\'new\',0);">追加</a><br>';

    userload("movie/movie.php");

    $nline = rep($nline, 'op', $oline1 . $oline2);
    if (!moviebox_info_exist_table()) {
        movie_db_setup();
    }

    $res = moviebox_info_select_all();
    foreach ($res as $row) {
        $tline = rep($nline, 'id', $row['id']);
        $ret .= rep($tline, 'name', $row['title']);
        //$ret .= ephash($row,$nline);
    }
    $var = '<strong>ドキュメントメニュー編集</strong><br><a href="?type=topic">新着情報メニュー編集</a>　<a href="?type=mail">お問い合わせメニュー編集</a>　<a href="?type=document">ドキュメントメニュー編集</a>　<a href="?type=movie">動画管理メニュー編集</a>　<a href="?type=topicimg">トピック画像メニュー編集</a>　<a href="?type=customer">お客様の声メニュー編集</a>　<a href="?type=icon">アイコン画像メニュー編集</a>　<a href="?type=hintpasswd">アクセス制限メニュー編集</a><hr>
			<br><form action="?" method="POST" name=formx>
			<input type=hidden name="type" value="movie">
			<input type=hidden name="param">
			<input type=hidden name="cmd">
			<input type=text name="newname" >{new}
			<br><table><tr><th>動画管理番号</th><th>ラベル名</th><th>操作</th></tr>{table}</table></form>';

    $var = rep($var, 'table', $ret);
    $var = rep($var, "new", $new);
    return $var;
}

/** 特定のメニューを削除する */
function tools_movie_del($id) {
    moviebox_info_delete($id);
}

/** メニューを追加する */
function tools_movie_add($name) {
    moviebox_info_insert(array('title' => $name));
}

/** ラベル名を編集する */
function tools_movie_rename($id, $name) {
    moviebox_info_update(array('id' => $id, 'title' => $name));
}

////////////////////////////////////////////////////

/** すべての情報を表示する */
function tools_topicimg_all() {
    $nline = '<tr><td>{id}</td><td><input type=text name=label{id} value="{name}" /></td><td>{op}</td></tr>';
    $oline1 = '<a href="?" onclick="return selector(\'del\',{id});" >削除</a>';
    $oline2 = '　<a href="?" onclick="return selector(\'ch\',{id});" >変更</a>';
    $new = '<a href="?" onclick="return selector(\'new\',0);">追加</a><br>';


    $nline = rep($nline, op, $oline1 . $oline2);

    $db = getDB();
    $res = $db->QueryRow("SELECT id,name FROM topicimg_info");
    foreach ($res as $row) {
        $ret .= ephash($row, $nline);
    }
    $var = '<strong>トピック画像メニュー編集</strong><br><a href="?type=topic">新着情報メニュー編集</a>　<a href="?type=mail">お問い合わせメニュー編集</a>　<a href="?type=document">ドキュメントメニュー編集</a>　<a href="?type=movie">動画管理メニュー編集</a>　<a href="?type=topicimg">トピック画像メニュー編集</a>　<a href="?type=customer">お客様の声メニュー編集</a>　<a href="?type=icon">アイコン画像メニュー編集</a>　<a href="?type=hintpasswd">アクセス制限メニュー編集</a><hr>
			<br><form action="?" method="POST" name=formx>
			<input type=hidden name="type" value="topicimg">
			<input type=hidden name="param">
			<input type=hidden name="cmd">
			<input type=text name="newname" >{new}
			<br><table><tr><th>トピック番号</th><th>ラベル名</th><th>操作</th></tr>{table}</table></form>';

    $var = rep($var, 'table', $ret);
    $var = rep($var, "new", $new);
    return $var;
}

/** 特定のメニューを削除する */
function tools_topicimg_del($id) {

    $db = getDB();

    $db->ExecuteRow('DELETE FROM topicimg WHERE groupid=:id', array('id' => $id));
    $db->ExecuteRow('DELETE FROM topicimg_info WHERE id=:id', array('id' => $id));
}

/** メニューを追加する */
function tools_topicimg_add($name) {
    $db = getDB();
    $db->ExecuteRow("INSERT INTO topicimg_info (id,name) VALUES(:id,:name)",array('id'=>$i,'name'=>$name));
}

/** ラベル名を編集する */
function tools_topicimg_rename($id, $name) {
    $db = getDB();
    $db->ExecuteRow('UPDATE topicimg_info SET name = :name WHERE id=:id', array('id'=>$id,'name'=>$name));
}

////////////////////////////////////////////////////

/** すべての情報を表示する */
function tools_customer_all() {
    $nline = '<tr><td>{id}</td><td><input type=text name=label{id} value="{name}" /></td><td>{op}</td></tr>';
    $oline1 = '<a href="?" onclick="return selector(\'del\',{id});" >削除</a>';
    $oline2 = '　<a href="?" onclick="return selector(\'ch\',{id});" >変更</a>';
    $new = '<a href="?" onclick="return selector(\'new\',0);">追加</a><br>';


    $nline = rep($nline, op, $oline1 . $oline2);

    $db = getDB();
    $res = $db->QueryRow("SELECT id,name FROM customer_info");
    foreach ($res as $row) {
        $ret .= ephash($row, $nline);
    }
    $var = '<strong>お客様の声メニュー編集</strong><br><a href="?type=topic">新着情報メニュー編集</a>　<a href="?type=mail">お問い合わせメニュー編集</a>　<a href="?type=document">ドキュメントメニュー編集</a>　<a href="?type=movie">動画管理メニュー編集</a>　<a href="?type=topicimg">トピック画像メニュー編集</a>　<a href="?type=customer">お客様の声メニュー編集</a>　<a href="?type=icon">アイコン画像メニュー編集</a>　<a href="?type=hintpasswd">アクセス制限メニュー編集</a><hr>
			<br><form action="?" method="POST" name=formx>
			<input type=hidden name="type" value="customer">
			<input type=hidden name="param">
			<input type=hidden name="cmd">
			<input type=text name="newname" >{new}
			<br><table><tr><th>トピック番号</th><th>ラベル名</th><th>操作</th></tr>{table}</table></form>';

    $var = rep($var, 'table', $ret);
    $var = rep($var, "new", $new);
    return $var;
}

/** 特定のメニューを削除する */
function tools_customer_del($id) {

    $db = getDB();

    $db->ExecuteRow('DELETE FROM customer WHERE groupid=:id', array('id' => $id));
    $db->ExecuteRow('DELETE FROM customer_info WHERE id=:id', array('id' => $id));
}

/** メニューを追加する */
function tools_customer_add($name) {
    $db = getDB();
    $db->ExecuteRow("INSERT INTO customer_info (id,name) VALUES(:id,:name)",array('id'=>$i,'name'=>$name));
}

/** ラベル名を編集する */
function tools_customer_rename($id, $name) {
    $db = getDB();
    $db->ExecuteRow('UPDATE customer_info SET name = :name WHERE id=:id', array('id'=>$id,'name'=>$name));
}

////////////////////////////////////////////////////

/** すべての情報を表示する */
function tools_icon_all() {
    $nline = '<tr><td>{id}</td><td><input type=text name=label{id} value="{name}" /></td><td>{op}</td></tr>';
    $oline1 = '<a href="?" onclick="return selector(\'del\',{id});" >削除</a>';
    $oline2 = '　<a href="?" onclick="return selector(\'ch\',{id});" >変更</a>';
    $new = '<a href="?" onclick="return selector(\'new\',0);">追加</a><br>';


    $nline = rep($nline, op, $oline1 . $oline2);

    $db = getDB();
    $res = $db->QueryRow("SELECT id,name FROM icon_info");
    foreach ($res as $row) {
        $ret .= ephash($row, $nline);
    }
    $var = '<strong>アイコン画像メニュー編集</strong><br><a href="?type=topic">新着情報メニュー編集</a>　<a href="?type=mail">お問い合わせメニュー編集</a>　<a href="?type=document">ドキュメントメニュー編集</a>　<a href="?type=movie">動画管理メニュー編集</a>　<a href="?type=topicimg">トピック画像メニュー編集</a>　<a href="?type=customer">お客様の声メニュー編集</a>　<a href="?type=icon">アイコン画像メニュー編集</a>　<a href="?type=hintpasswd">アクセス制限メニュー編集</a><hr>
			<br><form action="?" method="POST" name=formx>
			<input type=hidden name="type" value="icon">
			<input type=hidden name="param">
			<input type=hidden name="cmd">
			<input type=text name="newname" >{new}
			<br><table><tr><th>トピック番号</th><th>ラベル名</th><th>操作</th></tr>{table}</table></form>';

    $var = rep($var, 'table', $ret);
    $var = rep($var, "new", $new);
    return $var;
}

/** 特定のメニューを削除する */
function tools_icon_del($id) {

    $db = getDB();

    $db->ExecuteRow('DELETE FROM icon WHERE groupid=:id', array('id' => $id));
    $db->ExecuteRow('DELETE FROM icon_info WHERE id=:id', array('id' => $id));
}

/** メニューを追加する */
function tools_icon_add($name) {
    $db = getDB();
    $db->ExecuteRow("INSERT INTO icon_info (id,name) VALUES(:id,:name)",array('id'=>$i,'name'=>$name));
}

/** ラベル名を編集する */
function tools_icon_rename($id, $name) {
    $db = getDB();
    $db->ExecuteRow('UPDATE icon_info SET name = :name WHERE id=:id', array('id'=>$id,'name'=>$name));
}

////////////////////////////////////////////////////

/** すべての情報を表示する */
function tools_hintpasswd_all() {
    $nline = '<tr><td>{id}</td><td><input type=text name=label{id} value="{name}" /></td><td>{op}</td></tr>';
    $oline1 = '<a href="?" onclick="return selector(\'del\',{id});" >削除</a>';
    $oline2 = '　<a href="?" onclick="return selector(\'ch\',{id});" >変更</a>';
    $new = '<a href="?" onclick="return selector(\'new\',0);">追加</a><br>';


    $nline = rep($nline, op, $oline1 . $oline2);

    $db = getDB();
    $res = $db->QueryRow("SELECT id,name FROM hintpasswd_info");
    foreach ($res as $row) {
        $ret .= ephash($row, $nline);
    }
    $var = '<strong>アクセス制限メニュー編集</strong><br><a href="?type=topic">新着情報メニュー編集</a>　<a href="?type=mail">お問い合わせメニュー編集</a>　<a href="?type=document">ドキュメントメニュー編集</a>　<a href="?type=movie">動画管理メニュー編集</a>　<a href="?type=topicimg">トピック画像メニュー編集</a>　<a href="?type=customer">お客様の声メニュー編集</a>　<a href="?type=hintpasswd">アイコン画像メニュー編集</a>　<a href="?type=hintpasswd">アクセス制限メニュー編集</a><hr>
			<br><form action="?" method="POST" name=formx>
			<input type=hidden name="type" value="hintpasswd">
			<input type=hidden name="param">
			<input type=hidden name="cmd">
			<input type=text name="newname" >{new}
			<br><table><tr><th>トピック番号</th><th>ラベル名</th><th>操作</th></tr>{table}</table></form>';

    $var = rep($var, 'table', $ret);
    $var = rep($var, "new", $new);
    return $var;
}

/** 特定のメニューを削除する */
function tools_hintpasswd_del($id) {

    $db = getDB();

    $db->ExecuteRow('DELETE FROM hintpasswd WHERE groupid=:id', array('id' => $id));
    $db->ExecuteRow('DELETE FROM hintpasswd_info WHERE id=:id', array('id' => $id));
}

/** メニューを追加する */
function tools_hintpasswd_add($name) {
    $db = getDB();
    $db->ExecuteRow("INSERT INTO hintpasswd_info (id,name) VALUES(:id,:name)",array('id'=>$i,'name'=>$name));
}

/** ラベル名を編集する */
function tools_hintpasswd_rename($id, $name) {
    $db = getDB();
    $db->ExecuteRow('UPDATE hintpasswd_info SET name = :name WHERE id=:id', array('id'=>$id,'name'=>$name));
}
?>