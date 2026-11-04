<?php
session_start();
require_once '../../system/init.php';
require_once '../../contents/topicimg/user_topicimg.php';
$config = confpath("topicimg/config.ini");
$conf = new cssConfig($config);
//webparam('gid',1)
$index = $conf->getSelector($_GET['pid']);
$topicimg = $index->getValue('topicimg');
$data = $conf->getSelector($topicimg);
$pid = $data->getValue('topicimgno');
$thumbnail_xmax = $data->getValue('thumbnail_xmax');

$db = getDB();
if ($_SESSION['param'] == '') {
    $thumbsql = 'SELECT thumb.filepath FROM topicimg_thumbnail thumb INNER JOIN topicimg t ON thumb.topicimg_id = t.id WHERE t.visible = 1 AND t.groupid = ' . $pid;
} else {
    $thumbsql = 'SELECT thumb.filepath FROM topicimg_thumbnail thumb INNER JOIN topicimg t ON thumb.topicimg_id = t.id WHERE t.id = ' . $_SESSION['param'];
    unset($_SESSION['param']);
}
$thumbnail = $db->QueryRow($thumbsql);

// リサイズ処理
if(!is_null($thumbnail['0']['filepath'])) {
    //ファイルの拡張子を取得
    $extension = pathinfo('../../contents/topicimg/thumbnail/' . $thumbnail['0']['filepath'],  PATHINFO_EXTENSION);
    //$image_path_thb = '../../contents/topicimg/thumbnail/' . $thumbnail['0']['filepath'] . '_re.' . $extension;
    $image_path_thb = '../../contents/topicimg/thumbnail/' . $thumbnail['0']['filepath'];
    if (!file_exists($image_path_thb)) {
        // リサイズ画像の生成

        copy('../../contents/topicimg/thumbnail/' . $thumbnail['0']['filepath'], $image_path_thb);
        chmod($image_path_thb, 0777);
        //■画像リサイズ■
        resizerThub($image_path_thb, $extension, $thumbnail_xmax);
    }
}


echo '<?xml version="1.0" encoding="utf-8"?>
';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<meta name="Keywords" content="" />
<meta name="Description" content="" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<link href="/css/default.css" rel="stylesheet" type="text/css" />
<link href="/css/layout.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/analytics.js"></script>
<script type="text/javascript" src="/js/smoothscroll.js"></script>
</head>
<body class="image_01" style="background-color: transparent;">
<!-- ラッパー -->

<div id="wrapper" class="clearfix">
<p><img src="<?php echo $image_path_thb; ?>" /></p>
</div>
</body>
</html>