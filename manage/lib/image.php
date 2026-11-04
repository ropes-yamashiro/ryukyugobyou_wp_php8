<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Terutaka.Takezaki
# Release: 2009/03/05
# History:
#==================================

/**
 * アップロードしない版
 * @param string $srcfile
 * @param string $newfile
 * @param string $mx
 * @param string $my
 * @param string $ext
 * @return <type>
 */
function imagesize2($srcfile,$newfile,$mx = 800,$my = 600,$ext = null){

        #echo "smx=$mx,smy=$my,";
	//pr(here0);
        if(intval($mx) == null)$mx = 1200;
        if(intval($my) == null)$my = 1200;


	ini_set("memory_limit","100M");
	$ext  = strtolower($ext);
	if($ext == null){
		$finfo = pathinfo($newfile);
		$ext = strtolower($finfo['extension']);
	}

	if(!in_array($ext,array('jpg','png','gif','jpeg'))){ //サイズが変換できる場合
		return copy($srcfile, $newfile);
	}
	list($x,$y) = getimagesize($srcfile);
	switch($ext){
		case 'jpg': case 'jpeg' :
                    $scanbus = imagecreatefromjpeg($srcfile); break;
                
		case 'png': $scanbus = imagecreatefrompng($srcfile);  break;
		case 'gif': $scanbus = imagecreatefromgif($srcfile);  break;
	}

	//サイズが規定の範囲内か
	//if($mx >= $x && $my >= $y){
        //    echo onsize;
	//	//pr(here1);
	//	return copy($srcfile, $newfile);
	//}
	//pr(here2);

	//画像ファイルのサイズ調整
	$dx = ((float) $x) / ((float) $mx);
	$dy = ((float) $y) / ((float) $my);
	$dd = $dx >= $dy ? $dx : $dy;
	$nx = intval(((float)$x) / $dd);
	$ny = intval(((float)$y) / $dd);

        #echo "x=$x,y=$y,nx=$nx,ny=$ny<br>";

	//ファイルの調整
	$ncanbus = imagecreatetruecolor($nx,$ny);
	imagecopyresampled($ncanbus,$scanbus, 0,0,0,0,$nx,$ny,$x,$y);

	//ファイルの保存
	switch($ext){
		case 'jpg': case 'jpeg' : $bool =  imagejpeg($ncanbus,$newfile,100);
		case 'png': $bool = imagepng ($ncanbus,$newfile);
		case 'gif': $bool = imagegif($ncanbus,$newfile);
	}

	imagedestroy($ncanbus);
	imagedestroy($scanbus);

	return $bool;

}

function imagesize($srcfile,$newfile,$mx = 800,$my = 600,$ext = null){
	//pr(here0);

	ini_set("memory_limit","32M");
	$ext  = strtolower($ext);
	if($ext == null){
		$finfo = pathinfo($newfile);
		$ext = strtolower($finfo['extension']);
	}
	//dp($newfile);
	if(!in_array($ext,array('jpg','png','gif','jpeg'))){ //サイズが変換できる場合
		return move_uploaded_file( $srcfile, $newfile );
	}
	list($x,$y) = getimagesize($srcfile);
	switch($ext){
		case 'jpg': case 'jpeg': $scanbus = imagecreatefromjpeg($srcfile); break;
		case 'png': $scanbus = imagecreatefrompng($srcfile);  break;
		case 'gif': $scanbus = imagecreatefromgif($srcfile);  break;
	}
	
	//サイズが規定の範囲内か
	if($mx >= $x && $my >= $y){
		//pr(here1);
		return move_uploaded_file( $srcfile, $newfile );
	}
	//pr(here2);
	
	//画像ファイルのサイズ調整
	$dx = ((float) $x) / ((float) $mx);
	$dy = ((float) $y) / ((float) $my);
	$dd = $dx >= $dy ? $dx : $dy;
	$nx = intval(((float)$x) / $dd);
	$ny = intval(((float)$y) / $dd);
	
	//ファイルの調整
	$ncanbus = imagecreatetruecolor($nx,$ny);
	imagecopyresampled($ncanbus,$scanbus, 0,0,0,0,$nx,$ny,$x,$y);
	
	//ファイルの保存
	switch($ext){
		case 'jpg': case 'jpeg': $bool =  imagejpeg($ncanbus,$newfile,100);
		case 'png': $bool = imagepng ($ncanbus,$newfile);
		case 'gif': $bool = imagegif($ncanbus,$newfile);
	}
	
	imagedestroy($ncanbus);
	imagedestroy($scanbus);
	
	return $bool;
	
}

?>