<?php

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

//メールのロード・保存
function mailload($pid, $temp, $err = null) {
    $detail = new mailconfig();
    $info = $detail->getinfo($pid);
    if ($info == null
        )return;

    $send = $info['ManageSendTo'];
    $info['ManageSendTo'] = "";
    //dp($info);

    if ( empty( $err ) ) {
        $temp = ephash($info, $temp);

        foreach ($send as $one) {
            $num = $one[0];
            $to = $one[1];
            $bool = $one[2];
            $temp = rep($temp, 'ManageSendTo' . $num, $to);
        }

        for ($i = 1; $i <= 10; $i++) {
            $temp = rep($temp, 'ManageSendTo' . $i, "");
        }
    } else {
        $temp = rep($temp, 'AdminTitle', $info['AdminTitle'] );
        $temp = rep($temp, 'UserName', $_REQUEST['UserName'] );
        $temp = rep($temp, 'UserTitle', $_REQUEST['UserTitle'] );
        $temp = rep($temp, 'UserSender', $_REQUEST['UserSender'] );
        $temp = rep($temp, 'UserSenderAddress', $_REQUEST['UserSenderAddress'] );
        $temp = rep($temp, 'ManageName', $_REQUEST['ManageName'] );
        $temp = rep($temp, 'ManageTitle', $_REQUEST['ManageTitle'] );
        $temp = rep($temp, 'ManageSender', $_REQUEST['ManageSender'] );
        $temp = rep($temp, 'ManageSenderAddress', $_REQUEST['ManageSenderAddress'] );
        $temp = rep($temp, 'UserContents', $_REQUEST['UserContents'] );
        $temp = rep($temp, 'ManageContents', $_REQUEST['ManageContents'] );
        for ($i = 1; $i <= 10; $i++) {
            $temp = rep($temp, 'ManageSendTo' . $i, $_REQUEST['ManageSendTo'.$i] );
        }
    }
    $temp = rep($temp, 'pid', $pid);
    if ($err != null
        )$err = $err . "<br>";
    $temp = rep($temp, 'err', $err);
    return $temp;
}


function mailsave($pid, $temp) {
    $detail = new mailconfig();
    $ret = ispostmail();
    if (strlen($ret) == 0) {
        $detail->save($pid, $_POST);
    }
    return mailload($pid, $temp, $ret);
}

function ispostmail() {
    //$ret .= !ismail($_POST[ManageSenderAddress]) && trim($_POST[ManageSenderAddress]) != null ? '<p><font color ="red">' ."管理者用のアドレスにはメールアドレスを入力してください".  "</font></p>\r\n" :	"";
//    $ret .= ! ismail($_POST['UserSenderAddress']) && trim($_POST['UserSenderAddress']) != null ? "" :'<p><font color ="red">' . "返信用のアドレスにはメールアドレスを入力してください" . "</font></p>\r\n" ;
    $ret .= empty( $_POST['UserName'] ) ? "申込者宛 - 宛先を入力してください。<br />" : "";
    $ret .= empty( $_POST['UserTitle'] ) ? "申込者宛 - 件名を入力してください。<br />" : "";
    $ret .= empty( $_POST['UserSender'] ) ? "申込者宛 - メール送信者を入力してください。<br />" : "";
    $ret .= empty( $_POST['UserSenderAddress'] ) ? "申込者宛 - 表示アドレスを入力してください。<br />" : "";
    $ret .= empty( $_POST['ManageName'] ) ? "管理者宛 - 宛先を入力してください。<br />" : "";
    $ret .= empty( $_POST['ManageTitle'] ) ? "管理者宛 - 件名を入力してください。<br />" : "";
    $ret .= empty( $_POST['ManageSender'] ) ? "管理者宛 - メール送信者を入力してください。<br />" : "";
    $ret .= empty( $_POST['ManageSenderAddress'] ) ? "管理者宛 - 表示アドレスを入力してください。<br />" : "";

    $ret .= empty( $_POST['ManageSendTo1'] ) ? "連絡先1を入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo1']) && trim($_POST['ManageSendTo1']) != null ? "連絡先1にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo2']) && trim($_POST['ManageSendTo2']) != null ? "連絡先2にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo3']) && trim($_POST['ManageSendTo3']) != null ? "連絡先3にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo4']) && trim($_POST['ManageSendTo4']) != null ? "連絡先4にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo5']) && trim($_POST['ManageSendTo5']) != null ? "連絡先5にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo6']) && trim($_POST['ManageSendTo6']) != null ? "連絡先6にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo7']) && trim($_POST['ManageSendTo7']) != null ? "連絡先7にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo8']) && trim($_POST['ManageSendTo8']) != null ? "連絡先8にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo9']) && trim($_POST['ManageSendTo9']) != null ? "連絡先9にはメールアドレスを入力してください。<br />" : "";
    $ret .= ! ismail($_POST['ManageSendTo10']) && trim($_POST['ManageSendTo10']) != null ? "連絡先10にはメールアドレスを入力してください。<br />" : "";

    $ret .= empty( $_POST['UserContents'] ) ? "申込者宛 - メール本文を入力してください。<br />" : "";
    $ret .= empty( $_POST['ManageContents'] ) ? "管理者宛 - メール本文を入力してください。<br />" : "";

    if ( !empty( $ret ) ) {
        $ret = "<p><font color =\"red\">".$ret."</font></p>";
    }
    return $ret;
}



function mail_first() {

    $pid = webparam('pid', 1);
    $ok = false;
    if (webauth('mail_title' . $pid) && mail_dataok($pid)) {
        $ok = true;
    }

    for ($i = 1; $i < 10; $i++) {
        if (webauth('mail_title' . $i) && mail_dataok($pid)) {
            $pid = $i;
            $ok = true;
            break;
        }
    }
    return $ok;
}

function mail_act() {
    $pid = webparam('pid', 1);

    $ok = false;
    if (webauth('mail_title' . $pid) && mail_dataok($pid)) {
        $ok = true;
    }

    if ($ok == false) {
        for ($i = 1; $i < 10; $i++) {
            if (webauth('mail_title' . $i) && mail_dataok($pid)) {
                $pid = $i;
                $ok = true;
                break;
            }
        }
    }
    if ($ok == false)
        return new plugin_result('<font color="red">不正アクセスエラー：権限がありません</font>');

    $com = webparam('command', 'load');
    $item = userread(switchSpTemplate("mail/mail.html"));
    if ($com == 'load')
        $item = mailload($pid, $item);
    if ($com == 'save') {
        $item = mailsave($pid, $item);
    }

    $ret = new plugin_result($item);
    $ret->element['onload'] = 'deftab(1);';
    return $ret;
}

function mail_dataok($pid) {
    $db = getDB();
    $count = $db->Count('SELECT count(*) FROM MailInfo WHERE id=:id',  array('id' => $pid));
    return $count > 0;
}

function mail_auth() {
    $detail = new mailconfig();
    $info = $detail->title();

    $tret = array();
    $line = "メール管理の{title}が編集が可能になります。";
    foreach ($info as $value) {
        $name = $value['AdminTitle'];
        array_push($tret,
                array('key' => 'mail_title' . $value['id']
                    , 'name' => $name
                    , 'info' => rep($line, 'title', $name)));
    }
    if (count($tret) > 0)
        $hash["メール管理"] = $tret;
    return $hash;
}

function mail_menu($line, $admin, $title, $title_end) {

    $detail = new mailconfig();
    $info = $detail->title();

    if (count($info) > 0
        )$top = rep($title, 'title', "メール管理");

    foreach ($info as $value) {
        if (!webauth('mail_title' . $value['id']))
            continue;
        $one = rep($line, 'typex', "mail&pid=" . $value['id']);
        $ret .= rep($one, 'title', $value['AdminTitle']);
    }
    return $ret ? $top . $ret . $title_end : "";
}

/** メールの設定ファイル */
class mailconfig {

    function datelist() {
        $v = getdate();
        return array("&year" => $v['year'], "&month" => $v['mon'], "&day" => $v['mday'],
            "&hour" => $v['hours'], "&minute" => $v['minutes'], "&second" => $v['seconds']);
    }

    //データを取得する
    function getValue($values, $formno, $mailvar) {


        $info = $this->getinfo($formno);

        if (!$info)
            return false;

        $values = whash($values, $this->datelist());

        foreach ($info as $key => $val) {
            if ($key == 'ManageSendTo') continue;
            $dat[$key] = phash($values, $info[$key]);
        }



        //ユーザーのデータ
        $user = array('to' => $values[$mailvar], 'from' => $dat['UserSenderAddress'],
            'sub' => $dat['UserTitle'], 'con' => $dat['UserContents'], 'myname' => $dat['UserSender'], 'toname' => $dat['UserName']);
        $ret = array($user);

        //管理者のデータ
        foreach ($info['ManageSendTo'] as $name) {

            if (!$name[2]) continue;
            $manage = array('to' => $name[1], 'from' => $dat['ManageSenderAddress'], 'sub' => $dat['ManageTitle'], 'con' => $dat['ManageContents'],
                'myname' => $dat['ManageSender'], 'toname' => $dat['ManageName']);

            $ret[] = $manage;
        }
        return $ret;
    }

    /** 指定のページ番号のデータを取得する */
    function getinfo($number) {
        $db = getDB();
        $hash = array('id' => $number);
        $sql = 'SELECT * FROM MailInfo WHERE id=:id';
        $row = $db->QueryLine($sql,$hash);
        $row['ManageSendTo'] = array();
        foreach ($row as $key => $value) {
            if (ereg("^ManageSendTo([0-9]{1,2})$", $key, $num)) {
                $row['ManageSendTo'][] = array($num, $value, true);
            }
        }
        return $row;
    }

    /** データを保存する */
    function save($pid, $value) {
        $db = getDB();
        $value['id'] = $pid;
        $sql = '
            UPDATE MailInfo SET 
                UserName=:UserName,UserTitle=:UserTitle,
                UserSender=:UserSender,UserSenderAddress=:UserSenderAddress,
                
                ManageName=:ManageName,
                ManageTitle=:ManageTitle,
                ManageSender=:ManageSender,
                ManageSenderAddress=:ManageSenderAddress,
                
                ManageSendTo1=:ManageSendTo1,ManageSendTo2=:ManageSendTo2,
                ManageSendTo3=:ManageSendTo3,ManageSendTo4=:ManageSendTo4,
                ManageSendTo5=:ManageSendTo5,ManageSendTo6=:ManageSendTo6,
                ManageSendTo7=:ManageSendTo7,ManageSendTo8=:ManageSendTo8,
                ManageSendTo9=:ManageSendTo9,ManageSendTo10=:ManageSendTo10,
                UserContents=:UserContents,ManageContents=:ManageContents
                WHERE id=:id';

        $db->ExecuteRow($sql, $value);
    }

    /** タイトル情報を取得する */
    function title() {
        $db = getDB();
        $sql = 'SELECT id,AdminTitle FROM MailInfo';
        return $db->QueryRow($sql);
    }

    /** DBを作成する */
    function setup($path) {
        $db = getDB();
        $db->pdo->beginTransaction();
        $sql = 'INSERT INTO MailInfo(id,AdminTitle) VALUES(:id,:AdminTitle)';
        foreach (file($path) as $line) {
            $line = trim($line);
            if (!$line)continue;
            $cut = clist($line);
            if (count($cut) != 2)continue;
            $one['id'] = $cut[0];
            $one['AdminTitle'] = $cut[1];
            $db->ExecuteRow($sql, $one);
        }
        $db->pdo->commit();
    }
}

/** DBを作成する */
function mail_setup($path) {
    $db = getDB();
    $db->pdo->beginTransaction();
    $sql = 'INSERT INTO MailInfo(id,AdminTitle) VALUES(:id,:AdminTitle)';
    foreach (file($path) as $line) {
        $line = trim($line);
        if (!$line)continue;
        $cut = clist($line);
        if (count($cut) != 2)continue;
        $one['id'] = $cut[0];
        $one['AdminTitle'] = $cut[1];
        $db->ExecuteRow($sql, $one);
    }
    $db->pdo->commit();
}

/** テーブルをリセットします */
function mail_reset_table() {
    $db = getDB();
    $drop = 'DROP TABLE IF EXISTS `MailInfo`;';
    $db->ExecuteRow($drop);
}

/**
 * データベースを自動作成する(フレームワークから)
 * @param bool $force 強制的にリセットするか
 */
function mail_tbl_setup($force = false) {

    if($force) mail_reset_table();
    $db = getDB();
    $sql = '
        CREATE TABLE IF NOT EXISTS MailInfo (
	id INTEGER  UNSIGNED NOT NULL COMMENT "主キー" AUTO_INCREMENT,
	AdminTitle VARCHAR(64) COMMENT "メニュータイトル",

	UserName VARCHAR(128) COMMENT "ユーザー宛て 送信先名前",
	UserTitle VARCHAR(128) COMMENT "ユーザー宛て タイトル",
	UserSender VARCHAR(128) COMMENT "ユーザー宛て 送信者",
	UserSenderAddress VARCHAR(64) COMMENT "ユーザー宛て 送信者アドレス",

	ManageName VARCHAR(128) COMMENT "管理者宛て　送信先名前",
	ManageTitle VARCHAR(128) COMMENT "管理者宛て　タイトル",
	ManageSender VARCHAR(128) COMMENT "管理者宛て　送信者",
	ManageSenderAddress VARCHAR(64) COMMENT "管理者宛て　送信先アドレス　",

	ManageSendTo1 VARCHAR(64) COMMENT "管理者１",
	ManageSendTo2 VARCHAR(64) COMMENT "管理者２",
	ManageSendTo3 VARCHAR(64) COMMENT "管理者３",
	ManageSendTo4 VARCHAR(64) COMMENT "管理者４",
	ManageSendTo5 VARCHAR(64) COMMENT "管理者５",
	ManageSendTo6 VARCHAR(64) COMMENT "管理者６",
	ManageSendTo7 VARCHAR(64) COMMENT "管理者７",
	ManageSendTo8 VARCHAR(64) COMMENT "管理者８",
	ManageSendTo9 VARCHAR(64) COMMENT "管理者９",
	ManageSendTo10 VARCHAR(64) COMMENT "管理者10",

	UserContents TEXT COMMENT "ユーザーに送信する内容",
	ManageContents TEXT COMMENT "管理者に送信する内容",

	PRIMARY KEY (`id`)
    ) ENGINE=InnoDB
       DEFAULT CHARSET=utf8
       COMMENT="送信メールの情報"';
    $db->ExecuteRow($sql);

}
?>