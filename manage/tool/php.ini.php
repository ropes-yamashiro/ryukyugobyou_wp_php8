<?

#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

header("Content-type: text/html; charset=utf8");
if(ini_get('mbstring.detect_order') != null && ini_get('mbstring.detect_order') != false) $err1 = true;
if(ini_get('mbstring.encoding_translation')!= null && ini_get('mbstring.encoding_translation')!= '0') $err1 = true;
if(ini_get('mbstring.func_overload') != 0 && ini_get('mbstring.func_overload') != '0') $err1 = true;
if(ini_get('mbstring.http_input') != pass) $err1 = true;
if(ini_get('mbstring.http_output') != pass) $err1 = true;

if(ini_get('mbstring.script_encoding') && ini_get('mbstring.script_encoding') != false) $err1 = true;
if(ini_get('mbstring.strict_detection')!= null && ini_get('mbstring.strict_detection')!= '0') $err1 = true;
if(ini_get('mbstring.substitute_character')!= null) $err1 = true;

$encoding = ini_get('mbstring.internal_encoding');
$language = ini_get('mbstring.language');
$memory = ini_get(memory_limit);
$postsize = ini_get(post_max_size);
$upload_size = ini_get(upload_max_filesize);

if($upload_size != '32M') $err2 = true;
if($postsize != '32M') $err2 = true;
if($memory != '128M') $err2 = true;

echo "verion 2009/0202/15:57<br>";
echo "言語: $language<br>";
echo "PHP用メモリ: $memory<br>";
echo "POST命令サイズ: $postsize<br>";
echo "アップロードファイルサイズ $upload_size<br>";
echo "<hr>";
echo $err1 
	? '文字コード: <span style="color: red">.htaccessもしくはphp.iniが正常に読み込まれていません</span><br>' 
	: '文字コード: 問題なし<br>';

echo $err2
	? 'アップロード: <span style="color: red">.htaccessもしくはphp.iniが正常に読み込まれていません。<br>上記のアップロードファイルサイズまでアップロードできます</span><br>' 
	: 'アップロード: 問題なし<br>';

echo "<hr>";
echo 'mbstring.detect_order=' . var_export(ini_get('mbstring.detect_order'),true) . '<br>';
echo 'mbstring.encoding_translation=' . var_export(ini_get('mbstring.encoding_translation'),true).'<br>';
echo 'mbstring.func_overload=' . var_export(ini_get('mbstring.func_overload'),true) .'<br>';
echo 'mbstring.http_input=' . var_export(ini_get('mbstring.http_input'),true) .'<br>';
echo 'mbstring.http_output=' . var_export(ini_get('mbstring.http_output'),true) .'<br>';

echo 'mbstring.script_encoding=' . var_export(ini_get('mbstring.script_encoding'),true) . '<br>';
echo 'mbstring.strict_detection=' . var_export(ini_get('mbstring.strict_detection'),true) . '<br>';
echo 'mbstring.substitute_character=' . var_export(ini_get('mbstring.substitute_character'),true) . '<br>';

?>
