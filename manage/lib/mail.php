<?php
/** 日本語環境用のメール */
class mailsender{
	
	var $file = array();
    var $encode = "ISO-2022-JP";

    /** UTF-8-JIS→ISO-2022-JPとBase64エンコードを実行します */
    function encode($utf8, $base = true)
    {
        if ($this->encode == "ISO-2022-JP") {
            $utf8 = mb_convert_encoding($utf8, "ISO-2022-JP", "UTF-8");
        }
        return $base ? base64_encode($utf8) : $utf8;
    }
	

    function rfc822($name)
    {
        if (preg_match("/[\\(\\)\\<\\>\\@\\,\\;\\:\\\\\\.\\[\\]]/", $name)) {
            return $name = "\"" . $name . "\"";
        }
        else
        {
            return $name;
        }
    }

    function japantext($name)
    {
        if ($name == null) return null;
        return "=?{$this->encode}?B?" . $this->encode($name) . "?= ";
    }

    //  データを追加する
    function attach($name, $data)
    {
        array_push($this->file, array('name' => $name, 'value' => $data));
    }

    //  ファイルを追加する
    function attach_file($name, $path)
    {
        if (file_exists($path)) {
            $this->attach($name, file_get_contents($path));
        }
    }

    //  ハッシュを使用して送信します to,from,sub,con,myname,tonameが構成要素
    function sendhash($hash)
    {
        $this->send($hash['to'], $hash['from'], $hash['sub'], $hash['con'], $hash['myname'], $hash['toname']);
    }

    //  メールを送信する
    function send($mailTo, $mailFrom, $subject, $contents, $fromName, $toName)
    {
        if (empty($mailTo)) return;

        //必要な情報
        $toName = $this->japantext($this->rfc822($toName)) . "<$mailTo>"; //送信先の設定
        $fromName = $this->japantext($this->rfc822($fromName)) . "<$mailFrom>"; //送信元の設定
        //$subject = $this->japantext($subject); //タイトルの設定

        //ヘッダー部分
        $header = "From: $fromName\n"; //送信元
        $header .= "Return-path: $mailFrom\n"; //送信できない場合送信先
        $header .= "MIME-Version: 1.0\n";

        //添付用
        $boundary = md5(uniqid(rand()));

        //ファイル用ヘッダー
        if (count($this->file) > 0) {
            $header .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n\n";
            $header .= "This is a multi-part message in MIME format.";
            $msg .= "\n\n--$boundary\n";
            $msg .= 'Content-Type: text/plain; charset="' . $this->encode . '"';
            $msg .= "\nContent-Transfer-Encoding: 7bit\n\n";
        }
        else {
            $header .= 'Content-Type: text/plain; charset="' . $this->encode . '"';
            $header .= "\nContent-Transfer-Encoding: 7bit";
        }
        $contents = str_replace("\r\n", "\n", $contents);
//        $contents = str_replace("\n", "\r\n", $contents);
        $contents = $msg . $this->encode($contents, false);


        foreach ($this->file as $fone) {
            $fname = $this->japantext($fone['name']);
            $ftype = getMIME($fone['name']);
            $fdata = chunk_split(base64_encode($fone['value']));

            $info .= "\n\n--$boundary\n";
            $info .= "Content-Type: " . $ftype . ";\n";
            $info .= "\tname=\"$fname\"\n";
            $info .= "Content-Transfer-Encoding: base64\n";
            $info .= "Content-Disposition: attachment;\n";
            $info .= "\tfilename=\"$fname\"\n\n";
            $info .= "$fdata\n";
        }
        if (count($this->file) > 0) $info .= "\n--$boundary--";
        $contents .= $info;
        //mail($toName, $subject, $contents, $header);
        
		//2014.11.11 CPIサーバーで自動返信メールが2通届く問題修正ため以下コメントアウト
		//$header .= "\nTo: " . $toName;
		
        $defaultEncoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');
        mb_send_mail($mailTo, $subject, $contents, $header);
        mb_internal_encoding($defaultEncoding);
    }
}
?>