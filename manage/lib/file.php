<?php

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

/** 一つのページに複数のテンプレートを設定します */
class tLoader {

    var $info = array();

    function __construct($filepath) {
        $switch = false;
        $name = null;
        $var = null;
        if (!file_exists($filepath))
            pout($filepath . "が見つかりません");

        foreach (file($filepath) as $line) {
            $onvalue = "@begin";
            $offvalue = "@end";

            $pos = strpos($line, $onvalue);
            if ($pos !== false) {
                $name = trim(substr($line, strlen($onvalue)));
                $switch = true;
                $var = null;
                continue;
            }
            $pos = strpos($line, $offvalue);
            if ($pos !== false && $switch) {
                $switch = false;
                $this->info[$name] = $var;
                continue;
            }

            if ($switch)
                $var .= $line;
        }
    }

    /** 対応するデータを読込みます $name=nullの場合はすべてのデータ */
    function getData($name =null) {
        return $name ? $this->info[$name] : $this->info;
    }

    /** 特定の部分を選択してデータ読込みます */
    static function nLoad($filepath, $name = null) {
        $loader = new tLoader($filepath);
        return $loader->getData($name);
    }

}

/** css形式のセレクター */
class cssSelector {

    var $hval = array();
    var $name;
    var $double;

    /** コンストラクタ
     * @param $double 属性の重複を許すか? */
    function __construct($name, $double = true) {
        $this->name = $name;
        $this->double = $double;
    }

    /** 属性を追加する */
    function addValue($name, $value) {
        //重複不許可
        if (!$this->double) {
            $allvar = $this->hval;
            for ($i = 0; $i < count($allvar); $i++) {
                if ($allvar[$i]['name'] != $name)
                    continue;
                $this->hval[$i]['value'] = $value;return;
            }
        }
        $var = array('name' => $name, 'value' => $value);
        array_push($this->hval, $var);
    }

    /** 重複属性をすべてとりだす.
     *  @return list:list(value) */
    function getValues($name) {

        $var = $this->hval;
        $ret = array();
        foreach ($var as $one) {
            if ($one['name'] != $name)
                continue;
            $ret[] = $one['value'];
        }
        return $ret;
    }

    /** (重複)属性を一つとりだす
     *  @return list
     * 	@param  $name 属性名
     *  @param  $top  一番初めに定義したもの
     */
    function getValuesOne($name, $top = true) {
        $var = $this->getValues($name);
        $cnt = count($var);
        if ($cnt== 0)
            return null;
        return $top ? $var[0] : $var[$cnt - 1];
    }

    /** (重複)属性を一つとりだす(値がリストでも一つしか返さない)
     *  @return string
     *  @param  $name 属性名
     *  @param  $top   一番初めに定義したもの
     */
    function getValue($name, $top = true) {
        $var = $this->getValuesOne($name, $top);
		if (!is_array($var))
			return null;
        if (count($var) == 0)
            return null;
        return $var[0];
    }

}

/** スタイルシート形式の設定ファイルを読み込みます */
class cssConfig {

    var $selector = array();
    var $filepath;
    var $property_double = true;

    function __construct($path = null) {
        if ($path)
            $this->loadFile($path);
    }

    /** セレクターを取得します */
    function getSelector($name) {
        return $this->selector[$name] ?? null;
    }

    /** ファイルを読込む */
    function loadFile($filepath) {
        $this->filepath = $filepath;
        $this->parseString(file_get_contents($filepath));
    }

    /** 内部関数：データ内のパスを修正します。設定ファイルから見た位置にパスを修正します */
    function getFilepath($path) {
        return dirname($this->filepath) . "/" . trim($path, "\"'");
    }

    /** 内部関数:@import "filename" */
    function css_func_import($filename) {
        $filename = $this->getFilepath($filename);
        if ($filename != false) {
            $this->parseString(file_get_contents($filename));
        }
    }

    /** 内部関数:関数を実行します */
    function actFunc($func) {
        list($fname, $value) = $func;
        $fname = 'css_func_' . $fname;
        if (method_exists($this, $fname)) {
            $this->$fname($value);
        }
    }

    /** 内部関数: ファンクションがあれば、実行。セレクタを取り出す */
    function parseSelectorName($code) {
        $words = tlist(cword($code, ";"));
        foreach ($words as $one) {
            $func = null;
            if ($one == "")
                continue;
            if (substr($one, 0, 1) == "@") {
                $one = substr($one, 1);
                $proc = tlist(cword($one, " "));
                $this->actFunc($proc);
            }
            else
                return $one;
        }
    }

    /** 内部関数: 文字列を解釈します */
    function parseString($str) {

        //$str = ereg_replace("(//[^\n]*\n|/\*(.*)?\*/)", '', $str);
        $str = preg_replace('/(\/\/[^\n]*\n|\/\*([^*]|\*[^\/])*\*\/)/', '', $str);
        $parts = cword($str, "}");
        foreach ($parts as $part) {
            $part = trim($part);


            if (strlen($part) > 0) {
                list($key, $vcode) = array_pad(cword($part, "{"), 2, "");
                $key = $this->parseSelectorName($key);
                $vone = new cssSelector($key, $this->property_double);
                $codes = cword($vcode, ";");
                //dp($codes);
                foreach ($codes as $code) {
                    if (!trim($code)) {continue;                    }
                    list($name, $code) = array_pad(cword($code, ":"), 2, "");
                    $values = tlist(cword($code, ","));
                    $values = twlist($values);
                    $vone->addValue(trim($name), $values);
                }
                $this->selector[$key] = $vone;
            }
        }

       
    }

}

function definefile($path) {
    foreach (readlist($path) as $line) {
        $cut = clist($line);
        if (count($cut) != 2)
            continue;
        define($cut[0], $cut[1]);
    }
}

/** キー,値で一行をなすファイルをハッシュとして読み込む */
function readhash($path) {
    foreach (readlist($path) as $line) {
        if ($line == "")
            continue;
        if (substr($line, 0, 2) == "//")
            continue;
        list($name, $value) = cword($line, ",");
        $ret[$name] = trim($value);
    }
    return $ret;
}

/** アップロード用の情報を取得します */
function upfileInfo($name) {

    $hash["filename"] = $_FILES[$name]["name"]; //ファイル名
    $hash["size"] = $_FILES[$name]["size"];     //サイズ

    $info = pathinfo($_FILES[$name]["name"]);
    $hash["ext"] = strtolower($info["extension"]); //拡張子
    $hash["x"] = 0;$hash["y"] = 0;

    if (in_array($hash['ext'], clist("gif,jpg,png,swf,swc,psd,tiff,bmp,iff,jp2,jpx,jb2,jpc,xbm"))) {
        list($x, $y) = getimagesize($_FILES[$name]["tmp_name"]);//画像の場合のサイズ
        $hash["x"] = $x; $hash["y"] = $y;
    }//イメージサイズの調整の部分 //

    $hash["savetime"] = time(); //取得時間
    if (!$hash["filename"])
        return null;
    $fp = fopen($_FILES[$name]["tmp_name"], "r");//ファイルの中身
    $file = fread($fp, $_FILES[$name]["size"]);
    $hash["file"] = $file;
    return $hash;//*/
}

/** ファイルをダウンロードします */
function fileview($filename, $data, $view = true, $size = null) {

    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (strstr($ua, 'MSIE') && ! strstr($ua, 'Opera')) {
        #$filename = mb_convert_encoding($filename, 'UTF-8', "Shift-JIS");
        $filename = urlencode($filename);
    } else {
        #$filename = mb_convert_encoding($filename, 'UTF-8', "Shift-JIS");
    }

    $doctype = getMIME($filename);
    header('Pragma: public');

    $att = $view ? " inline;" : " attachment;";
    header('Content-Disposition:' . $att . 'filename="' . $filename . '";');
    header('Content-Type: ' . $doctype);
    if ($data) { header("Content-Length: " . strlen($data));    }
    echo $data;exit();
}

/** ファイルを行ごとに区切ったデータを取り出します */
function readlist($path) {
    $file = file($path);
    $ret = array();
    foreach ($file as $line
        )array_push($ret, trim($line));
    return $ret;
}

/** ファイルを出力します
 * @param $path 出力するファイル名
 * @param $hash 出力する値:値の微調整はしない */
function WriteFile($path, $value) {
    $ret = false;
    $exist = file_exists($path);
    if (!$fp = @fopen($path, "w"))
        return false;
    if (flock($fp, LOCK_EX)) {
        //データの出力
        fwrite($fp, $value);
        flock($fp, LOCK_UN);
        $ret = true;
    }
    fclose($fp);
    return $ret;
}

/** CSVファイルを出力します
 * @param $path 出力するファイル名
 * @param $hash 出力する値:値の微調整はしない */
function WriteCSV($path, $hash) {
    $ret = false;
    $exist = file_exists($path);
    if (!$fp = @fopen($path, "a+"))
        return false;
    if (flock($fp, LOCK_EX)) {
        //ヘッダーを出力
        if (!$exist) {
            $header = array_keys($hash);
            fwrite($fp, implode(",", $header) . "\n");
        }

        //データの出力
        fwrite($fp, implode(",", $hash) . "\n");
        flock($fp, LOCK_UN);
        $ret = true;
    }
    fclose($fp);
    return $ret;
}

/** CSVファイルを出力します
 * @param $path 出力するファイル名
 * @param $hash 出力する値
 * @param $chhash  ヘッダーの変換テーブル
 * @param $rephash データ内の変換用コード ,は、として解釈される */
function WriteCSVEx($path, $hash, $chhash = array(), $rephash = null) {

    if ($rephash == null)
        $rephash = array(',' => "、", '"' => "'", "\n" => null, "\r" => null);

    foreach ($hash as $key => $value) {
        $value = rephash($value, $rephash, false);
        $key = (array_key_exists($key, $chhash)) ? $chhash[$key] : $key;
        $ret[$key] = $value;
    }
    WriteCSV($path, $ret);
}
?>