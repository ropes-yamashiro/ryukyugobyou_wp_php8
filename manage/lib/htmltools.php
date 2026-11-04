<?php
#==================================
# Hudom CMS
# File version: 1.0.1
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History: 2009/04/21 プルダウンのラベルに当たる部分の値の削除
#==================================
/** イメージ用のテンプレートにする 
 *  @param $path   参照用のパス
 *  @param $picid　イメージID
 *  @param $fname  表示用ファイル名
 *  @param $name   ファイルキー
 */
function html_imagetype($name,$fname,$picid = null,$path,$ex=null,$no=null) {

    if(!$ex) $ex = '
	<input type=hidden name="{name}" value="{picid}" />
		&nbsp;&nbsp;{fname}&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onClick="delpic(\'{picid}\',\'{fname}\'); return false;">削除</a>
		&nbsp;&nbsp;<a href="{path}" target="_blank">表示</a>';
    if(!$no) $no = '<input type=file name="{name}" onFocus="this.className=\'on\';" onBlur="this.className=\'off\';" size="37"  />	';

    if($fname) {
        $ex = rep($ex,'fname',$fname);
        $ex = rep($ex,'name',$name);
        $ex = rep($ex,'path',$path);
        return rep($ex,'picid',$picid);
    }
    else {
        return rep($no,'name',$name);
    }
}

function html_pdftype($name,$fname,$picid = null,$path,$ex=null,$no=null) {
    if(!$ex) $ex = '
	<input type=hidden name="{name}" value="{picid}" />
		&nbsp;&nbsp;{fname}&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onClick="delpic(\'{fname}\',\'{fname}\'); return false;">削除</a>
		&nbsp;&nbsp;<a href="{path}" target="_blank">表示</a>';

	if(!$no) $no = '<input type=file name="selpdf" onFocus="this.className=\'on\';" onBlur="this.className=\'off\';" size="37"  />	';

	if($fname) {
		$ex = rep($ex,'fname',$fname);
		$ex = rep($ex,'name',$name);
		$ex = rep($ex,'path',$path);
		return rep($ex,'picid',$picid);
	}
	else {
		return rep($no,'name',$name);
	}
}

/** ファイルあれば削除、なければ何もしない */
function html_imagedel($name,$imgfolder = "img/") {
    $dir = realpath(sysroot() . "../") . "/" .$imgfolder;
    if(file_exists($dir . $name)) @unlink($dir . $name);
}

/** アップロードするファイルあればそのファイル、なければhiddenを探す */
function html_imagename($name,$imgfolder = "img/",$mx = 800,$my = 600) {
    $img = $_FILES[$name];

    //dp($mx);dp($my);
    //すでに登録済みファイル
    if(!$img['size']) {
        $path = webparam($name);
    }
    else {
        //if(!file_exists($img[tmp_name])) pl($name,$img);
        $dir = realpath(sysroot() . "../") . "/" .$imgfolder;
        $info = pathinfo($img['name']);
        $base = $info['filename'];
        $ext = $info['extension'];
        //ファイル名の決定
        while(!$ok) {
            $i++;
            $path = strtolower( $base . $i . "." . $ext);
            if(!file_exists($dir . $path)) break;
        }
        imagesize($img['tmp_name'],$dir .$path,$mx,$my);
        //move_uploaded_file($img[tmp_name],$dir .$path);
    }
    return $path;
}


//================================================
// オプション関連

/** $hashのキーを表示部分に、値をoptionの値にしたリストを作成する。
 $valueがない場合は$defを使う (参:html_ophash2)
 @param $hash 表示するデータ
 @param $value 選択しておくデータ(値)→設定されていない場合は一番上 */
function html_ophash($hash,$value = null) {
    $ret = "";
    $tline = '<option value="{value}" {sel}>{key}&nbsp;&nbsp;</option>';

    //対象となる値を選択
    if(count(array_keys($hash,$value)) == 0) {
        $sel = $hash[reset($hash)];
    }
    else $sel = $value;

    reset($hash);
    foreach($hash as $key => $invalue) {
        $line = rep($tline,'value',$invalue);
        $line = rep($line,'key',$key);
        $line = rep($line,'sel',$invalue == $sel ? 'selected="selected"' :"");
        $ret .= $line;
    }

    return $ret;
}

/** $hashのキーをoptionの値に、値を表示部分にしたリストを作成する。
 $valueがない場合は$defを使う(参:html_ophash)
 @param $hash 表示するデータ
 @param $value 選択しておくデータ(値)→設定されていない場合は一番上 */
function html_ophash2($hash,$value = null) {
    if($hash == null) return "";
    foreach($hash as $key => $val) {
        $ret[$val] = $key;
    }
    return html_ophash($ret,$value);
}

/** @listの要素のoptionの表示部分と値に設定したリストを作成する。
 選択する値は$value->一番上の値
 @param $list 表示するデータ
 @param $value 選択しておくデータ→設定されていない場合は一番上 */
function html_oplist($list,$value=null,$topislabel = false) {
    foreach($list as $one) {
        if($topislabel) {
            $ret[$one] = '';
            $topislabel = false;
        }
        else $ret[$one] = $one;
    }
    return html_ophash($ret,$value);
}

/** $nowを選択した$fromから$toのoptionリストを作成する */
function html_opnumber($from,$to,$now,$label = null) {
    $date = array();
    if($label !== null)$date[$label] = $label;
    for($i=$from;$i <= $to; $i++)$date[$i] = $i;
    return html_ophash($date,$now);
}

/** $nowを選択した$fromから$toのoptionリストを作成する */
function opdate($from,$to,$now) {
    $date = array();
    for($i=$from;$i <= $to; $i++)$date[$i] = $i;
    return html_ophash($date,$now);
}


//===================================================
//  ラジオ関連

/** $page内に存在する$nameラジオボタンで$valueを選択した状態にする。
 checkedの代わりに{$name_$value}を作成しておく
 @param $page 元になるページ
 @param $name 変数名
 @param $vlist 値の一覧
 @param $value 選択する値 */
function html_radio($page,$name,$vlist,$value = null) {
    $sel = in_array($value,$vlist) ? $value : null;
    foreach($vlist as $one) {
        $page = rep($page, $name . "_" . $one, $one == $sel ? 'checked="checked"' : "");
    }
    return $page;
}

/** $name$from ～ $name$toをひとつのグループにして、$name$valを選択した状態にする
 checkedの変わりに、{$name$number}を作成すること
 チェックボタン・ラジオボタンに使用可能  */
function html_switch($page,$name,$from,$to,$val = null) {
    if($val == null) $val = $from;
    for($i = $from; $i <= $to ;  $i++) {
        $page = rep($page,$name . $i, $val == $i ? 'checked="checked"' : "");
    }
    return $page;
}


//===================================================
//  チェックボックス系

/** $page内に存在する$nameチェックボタンで$valuesを選択した状態にする。
 checkedの代わりに{$name_$value}を作成しておく
 @param $page 元になるページ
 @param $name 変数名
 @param $vlist 値の一覧
 @param $value 選択する値 */
function html_check($page,$name,$vlist,$values = array()) {
    $values = toarray($values);
    foreach($values as $vone) {
        $page = rep($page, $name . "_"  . $vone, 'checked="checked"');
    }
    if(!is_array($vlist)) return $page;
    foreach($vlist as $vone) {
        $page = rep($page, $name . "_"  . $vone, "");
    }
    return $page;
}


//===================================================
//  その他

/** POST値にFile値のKeyを加えたものを返す */
function html_file2post() {
    $epost = copyhash($_POST);
    foreach($_FILES as $key => $value) {
//--- START 1.00b 2015-04-29 SB/K.Arikata --------------
//      $epost[$key] = $_POST[$key];
        $epost[$key] = $value["name"];
//--- START 1.00b 2015-04-29 SB/K.Arikata --------------
    }
    return $epost;
}

/** 確認画面で使用するephash */
function html_ephash($hash,$page) {

    foreach($hash as $key => $value) {
        $page = rep($page,$key,html_br(estr($value)));
    }
    return $page;

}

/** $value内の改行をタグに変換します */
function html_br($value) {
    $value = str_replace("\r\n","<br />",$value);
    $value = str_replace("\r","<br />",$value);
    $value = str_replace("\n","<br />",$value);
    return $value;
}


/** $hashのキーをタグの名前、値をタグの値にした一覧を作成する */
function html_hidden($hash,$prefix = null){
    $ret = "";
    $line = '<input type="hidden" name="{vkey}" value="{vval}" />';
    $preval = $prefix ? $prefix . '_' : '';
    foreach($hash as $vkey => $vval) {
        if(is_array($vval)) {
            foreach($vval as $vone) {
                $one = rep($line,'vkey',$preval . $vkey ."[]");
                $ret .= rep($one,'vval',estr($vone));
            }
        }
        else {
            $one = rep($line,'vkey',$preval . $vkey);
            $ret .= rep($one,'vval',estr($vval));
        }
    }
    return $ret;
}

/**
 * エラーをPタグでくくる
 * @param hash $errhash
 * @return string
 */
function html_errhash_br($errhash,$class = null,$style = null) {
    $errhash = html_errhash($errhash);
    $ret = null;
    foreach((array)$errhash as $key=>$value){
        if($ret != null) $ret .= '<br />';
        $ret .= $value;
    }
    $cls = $class ? " class='{$class}' " : null;
    $sty = $style ? " style='{$style}' " : null;
    return $ret ? ("<p {$cls}{$sty}>" .$ret . '</p>') : '';
}

/** エラーメッセージにタグを付加します */
function html_errhash($errhash) {
    $temp = '<span style="color :red;">{val}</span>';
    $ret = array();
    foreach((array)$errhash as $key => $val) {
        if($val) $ret[$key] = rep($temp,'val',$val);
    }
    return $ret;
}

/** 単純にタグでくくる*/
function html_errtag($message,$style =null) {
    $temp = '<span style="color :red;{style}">{val}</span>';
    $temp = rep($temp,'style',$style);
    return rep($temp,'val',$message);
}

/** ページを作成する */
function html_paging($total,$count,$cpage,$line) {

    $mpage = intval($total / $count) + ((($total % $count) > 0) ? 1 : 0) ; //必要ページ数

    $min = $cpage - ($cpage % 5 ? $cpage % 5 : 5 )+1 ;//区切り最小値
    $max = $min + 4 <= $mpage ? $min + 4 : $mpage; //区切り最大値

    if($mpage == 1)return;

    if($cpage > 1) {//一番初めがあるか？
        $val = rep($line,'view',"≪");
        $out .= rep($val,'no',1);
    }
    if($cpage > 5) {//前の五件があるか
        $val = rep($line,'view', ($min - 5) . "-" . ($min - 1));
        $out .= rep($val ,'no' , $min -1);
    }
    for($i = $min  ; $i <= $max ; $i++) {//中の五件があるか
        if($cpage == $i) {
            $out .= "&nbsp;" . $i;
        }
        else {
            $val = rep($line,'view',$i);
            $out .= rep($val,'no',$i);
        }
    }
    if($mpage > $max) {	//次の５件表示があるか？
        $rmax = $max + 5 <= $mpage ? $max + 5 : $mpage;
        $val  = rep($line, 'view' , $max + 1 . "-" . $rmax);
        $out .= rep($val,'no', $max + 1);
    }
    if($cpage < $mpage) {//最後の１件があるか
        $val = rep($line,'view',"≫");
        $out .= rep($val,'no',$mpage);
    }
    return $out;

}
?>